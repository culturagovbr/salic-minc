<?php

class Readequacao_LocalRealizacaoController extends Readequacao_GenericController
{
    private $_siEncaminhamento = null;
    private $_idTipoReadequacao = null;
    private $_existeSolicitacaoEmAnalise = false;

    public function init()
    {
        parent::init();

        $this->_siEncaminhamento = TbTipoEncaminhamento::SOLICITACAO_CADASTRADA_PELO_PROPONENTE;
        $this->_idTipoReadequacao = Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO;

        $tbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
        $this->_existeSolicitacaoEmAnalise = $tbReadequacaoMapper->existeSolicitacaoEmAnalise($this->idPronac, $this->_idTipoReadequacao);
        $this->view->existeSolicitacaoEmAnalise = $this->_existeSolicitacaoEmAnalise;
    }

    public function indexAction()
    {
        $this->view->projeto = $this->projeto;
        $this->view->idTipoReadequacao = $this->_idTipoReadequacao;
        $this->view->urlCallback = '/readequacao/local-realizacao/index/?idPronac=' . $this->idPronacHash;
        $this->view->action = '/readequacao/local-realizacao/salvar-readequacao/?idPronac=' . $this->idPronacHash;

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $this->view->readequacao = $tbReadequacao->obterDadosReadequacao(
            $this->_idTipoReadequacao,
            $this->idPronac
        );
    }

    public function salvarReadequacaoAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        if (empty($this->idPronac)) {
            parent::message("PRONAC &eacute; obrigat&oacute;rio", "principalproponente", "ALERT");
        }


        $urlCallback = $this->_request->getParam('urlCallback');
        if (empty($urlCallback)) {
            $urlCallback = "readequacao/readequacoes/index?idPronac=" . $this->idPronacHash;
        }
        
        if ($this->_existeSolicitacaoEmAnalise) {
            parent::message('Já existe uma solicita&ccedil;ao de readequa&ccedil;&atilde;o em an&aacute;lise!', $urlCallback, "ERROR");
        }


        try {
            $idReadequacao = filter_var($this->_request->getParam("idReadequacao"), FILTER_SANITIZE_NUMBER_INT);
            $idTipoReadequacao = filter_var($this->_request->getParam("tipoReadequacao"), FILTER_SANITIZE_NUMBER_INT);
            $params = $this->getRequest()->getParams();

            $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
            $locaisReadequados = $tbAbrangencia->buscar(
                ['idPronac = ?' => $this->idPronac, 'idReadequacao is null' => '']
            );

            if (count($locaisReadequados) == 0) {
                parent::message('N&atilde;o houve nenhuma altera&ccedil;&atilde;o nos locais de realiza&ccedil;&atilde;o do projeto!', $urlCallback, "ERROR");
            }

            $arrDoc = [];
            $arrDoc['idTipoDocumento'] = Arquivo_Model_TbTipoDocumento::TIPO_DOCUMENTO_SOLICITACAO_READEQUACAO;
            $arrDoc['dsDocumento'] = 'Solicita&ccedil;&atilde;o de Readequa&ccedil;&atilde;o';
            $mapperArquivo = new Arquivo_Model_TbDocumentoMapper();
            $idDocumento = $mapperArquivo->saveCustom($arrDoc, new Zend_File_Transfer());

            if (!empty($idDocumento)) {
                if (!empty($params['idDocumento'])) {
                    $tbDocumento = new Arquivo_Model_DbTable_TbDocumento();
                    $tbDocumento->excluirDocumento($params['idDocumento']);
                }
                $params['idDocumento'] = $idDocumento;
            }

            if (!empty($idReadequacao)) {
                $dados['idReadequacao'] = $idReadequacao;
            }

            $dados['idPronac'] = $this->idPronac;
            $dados['idTipoReadequacao'] = $idTipoReadequacao;
            $dados['dsJustificativa'] = $params['descJustificativa'];
            $dados['dsSolicitacao'] = $params['descSolicitacao'];
            $dados['idDocumento'] = $params['idDocumento'];

            $tbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
            $tbReadequacaoMapper->salvarSolicitacaoReadequacao($dados);

            parent::message("Solicita&ccedil;&atilde;o cadastrada com sucesso!", $urlCallback, "CONFIRM");
        } catch (Exception $e) {
            parent::message($e->getMessage(), $urlCallback, "ERROR");
        }
    }

    public function obterLocaisReadequacaoAjaxAction() {
        $this->_helper->layout->disableLayout();

        $idPronac = $this->_request->getParam('idPronac');

        $arrBusca = array();
        $arrBusca['idprojeto'] = $idPronac;
        $arrBusca['stabrangencia'] = 1;
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $locais = $tblAbrangencia->buscar($arrBusca);

        foreach ($locais as $key => $dado) {
            $locais[$key] = array_map('utf8_encode', $dado);
        }

        $this->_helper->json(array('data' => $locais, 'success' => 'true'));
    }

    public function obterLocaisDeRealizacaoAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        try {
            $idPronac = $this->_request->getParam("idPronac");
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
            $locais = $tbAbrangencia->buscarLocaisParaReadequacao($idPronac, 'tbAbrangencia');
            if (count($locais)==0) {
                $locais = $tbAbrangencia->buscarLocaisParaReadequacao($idPronac, 'Abrangencia');
            }

            $locais = $locais->toArray();
            foreach ($locais as $key => $dado) {
                $locais[$key] = array_map('utf8_encode', $dado);
            }

            $this->_helper->json(['msg' => '', 'data' => $locais, 'success' => 'true']);
        } catch(Exception $e) {
            $this->_helper->json(['msg' => utf8_encode($e->getMessage()), 'data' => $locais, 'success' => 'false']);
        }
    }

    /*
     * Essa função é usada para carregar os dados do locais de realização do projeto.
     */
    public function carregarLocaisDeRealizacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        if (isset($_POST['iduf'])) {
            $iduf = $_POST['iduf'];

            $mun = new Agente_Model_DbTable_Municipios();
            $cidade = $mun->listar($iduf);
            $a = 0;
            $cidadeArray = array();
            foreach ($cidade as $DadosCidade) {
                $cidadeArray[$a]['idMunicipio'] = $DadosCidade->id;
                $cidadeArray[$a]['nomeCidade'] = utf8_encode($DadosCidade->Descricao);
                $a++;
            }
            $this->_helper->json($cidadeArray);
            die;
        }

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
        $locais = $tbAbrangencia->buscarLocaisParaReadequacao($idPronac, 'tbAbrangencia');
        if (count($locais)==0) {
            $locais = $tbAbrangencia->buscarLocaisParaReadequacao($idPronac, 'Abrangencia');
        }

        $tbPais = new Pais();
        $this->view->Paises = $tbPais->buscar(array(), array(3));

//        $buscarEstado = EstadoDAO::buscar();
        $uf = new Agente_Model_DbTable_UF();
        $buscarEstado = $uf->buscar();
        $this->view->UFs = $buscarEstado;

        $get = Zend_Registry::get('get');
        $link = isset($get->link) ? true : false;

        $this->montaTela(
            'local-realizacao/carregar-locais-de-realizacao.phtml',
            array(
                'idPronac' => $idPronac,
                'locaisDeRealizacao' => $locais,
                'link' => $link
            )
        );
    }

    /*
     * Essa função é usada para carregar os dados do locais de realização do projeto.
     */
    public function carregarLocaisDeRealizacaoReadequacoesAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $idPronac = $this->_request->getParam("idPronac");
        $idReadequacao = $this->_request->getParam("idReadequacao");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
        $locais = $tbAbrangencia->buscarLocaisConsolidadoReadequacao($idReadequacao);

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosReadequacao = $tbReadequacao->buscar(array('idReadequacao=?'=>$idReadequacao))->current();
        $siEncaminhamento = $dadosReadequacao->siEncaminhamento;

        $tbPais = new Pais();
        $this->view->Paises = $tbPais->buscar(array(), array(3));


        $uf = new Agente_Model_DbTable_UF();
        $this->view->UFs = $uf->buscar();
        $get = Zend_Registry::get('get');
        $link = isset($get->link) ? true : false;

        $this->montaTela(
            'local-realizacao/carregar-locais-de-realizacao.phtml',
            array(
                'idPronac' => $idPronac,
                'locaisDeRealizacao' => $locais,
                'link' => $link,
                'idReadequacao' => $idReadequacao,
                'siEncaminhamento' => $siEncaminhamento
            )
        );
    }

    public function incluirLocalDeRealizacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        //VERIFICA SE JA POSSUI AS ABRANGENCIAS NA TABELA tbAbrangencia (READEQUACAO), SE NÃO TIVER, COPIA DA ORIGINAL, E DEPOIS INCLUI O ITEM DESEJADO.
        $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
        $readequacaoLR = $tbAbrangencia->buscar(array('idPronac=?'=>$idPronac, 'stAtivo=?'=>'S'));
        $locaisAtivos = $tbAbrangencia->buscarLocaisParaReadequacao($idPronac);

        if (count($readequacaoLR)==0) {
            $locaisCopiados = array();
            foreach ($locaisAtivos as $value) {
                $locaisCopiados['idReadequacao'] = null;
                $locaisCopiados['idPais'] = $value->idPais;
                $locaisCopiados['idUF'] = $value->idUF;
                $locaisCopiados['idMunicipioIBGE'] = $value->idMunicipio;
                $locaisCopiados['tpSolicitacao'] = 'N';
                $locaisCopiados['stAtivo'] = 'S';
                $locaisCopiados['idPronac'] = $idPronac;
                $tbAbrangencia->inserir($locaisCopiados);
            }
        }

        if ($_POST['newPaisLR'] == 31) {
            if (empty($_POST['newUFLR']) && empty($_POST['newMunicipioLR'])) {
                $msg = utf8_encode('Ao escolher o Brasil, os campos de UF e Município se tornam obrigatórios no cadastro!');
                $this->_helper->json(array('resposta'=>false, 'msg'=>$msg));
                $this->_helper->viewRenderer->setNoRender(true);
            }
            $verificaLocalRepetido = $tbAbrangencia->buscar(array('idPronac=?'=>$idPronac, 'stAtivo=?'=>'S', 'idPais=?'=>$_POST['newPaisLR'], 'idUF=?'=>$_POST['newUFLR'], 'idMunicipioIBGE=?'=>$_POST['newMunicipioLR']));
        } else {
            $verificaLocalRepetido = $tbAbrangencia->buscar(array('idPronac=?'=>$idPronac, 'stAtivo=?'=>'S', 'idPais=?'=>$_POST['newPaisLR']));
        }

        if (count($verificaLocalRepetido)==0) {
            /* DADOS DO ITEM PARA INCLUSÃO DA READEQUAÇÃO */
            $dadosInclusao = array();
            $dadosInclusao['idReadequacao'] = null;
            $dadosInclusao['idPais'] = $_POST['newPaisLR'];
            $dadosInclusao['idUF'] = isset($_POST['newUFLR']) ? $_POST['newUFLR'] : 0;
            $dadosInclusao['idMunicipioIBGE'] = isset($_POST['newMunicipioLR']) ? $_POST['newMunicipioLR'] : 0;
            $dadosInclusao['tpSolicitacao'] = 'I';
            $dadosInclusao['stAtivo'] = 'S';
            $dadosInclusao['idPronac'] = $idPronac;
            $insert = $tbAbrangencia->inserir($dadosInclusao);
            if ($insert) {
                //$jsonEncode = json_encode($dadosPlanilha);
                $this->_helper->json(array('resposta'=>true));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }
        } else {
            $msg = utf8_encode('Esse local de realização já foi cadastrado!');
            $this->_helper->json(array('resposta'=>false, 'msg'=>$msg));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /*
     * Esse função é usada pelo proponente para solicitar a exclusão de um local de realização.
     */
    public function excluirLocalDeRealizacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $idAbrangencia = $this->_request->getParam("idAbrangencia");
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
        $readequacaoLR = $tbAbrangencia->buscar(array('idPronac=?'=>$idPronac, 'stAtivo=?'=>'S'));

        //VERIFICA SE JA POSSUI AS ABRANGENCIAS NA TABELA tbAbrangencia (READEQUACAO), SE NÃO TIVER, COPIA DA ORIGINAL, E DEPOIS INCLUI O ITEM DESEJADO.
        $locaisAtivos = $tbAbrangencia->buscarLocaisParaReadequacao($idPronac);
        if (count($readequacaoLR)==0) {
            $locaisCopiados = array();
            foreach ($locaisAtivos as $value) {
                $locaisCopiados['idReadequacao'] = null;
                $locaisCopiados['idPais'] = $value->idPais;
                $locaisCopiados['idUF'] = $value->idUF;
                $locaisCopiados['idMunicipioIBGE'] = $value->idMunicipio;
                $locaisCopiados['tpSolicitacao'] = 'N';
                $locaisCopiados['stAtivo'] = 'S';
                $locaisCopiados['idPronac'] = $idPronac;
                $tbAbrangencia->inserir($locaisCopiados);
            }
        }

        /* DADOS DO ITEM PARA EXCLUSAO LÓGICA DO ITEM DA READEQUACAO */
        $dados = array();
        $dados['tpSolicitacao'] = 'E';

        $itemLR = $tbAbrangencia->buscar(array('idAbrangencia=?'=>$idAbrangencia))->current();
        if ($itemLR) {
            if ($itemLR->tpSolicitacao == 'I') {
                $exclusaoLogica = $tbAbrangencia->delete(array('idAbrangencia = ?'=>$idAbrangencia));
            } else {
                $where = "stAtivo = 'S' AND idAbrangencia = $idAbrangencia";
                $exclusaoLogica = $tbAbrangencia->update($dados, $where);
            }
        } else {
            $Abrangencia = new Proposta_Model_DbTable_Abrangencia();
            $itemLR = $Abrangencia->find(array('idAbrangencia=?'=>$idAbrangencia))->current();
            $dadosArray = array(
                'idPais =?' => $itemLR->idPais,
                'idUF =?' => $itemLR->idUF,
                'idMunicipioIBGE =?' => $itemLR->idMunicipioIBGE,
                'idPronac =?' => $idPronac,
                'stAtivo =?' => 'S',
            );
            $itemLR = $tbAbrangencia->buscar($dadosArray)->current();
            $where = "stAtivo = 'S' AND idAbrangencia = $itemLR->idAbrangencia";
            $exclusaoLogica = $tbAbrangencia->update($dados, $where);
        }

        if ($exclusaoLogica) {
            //$jsonEncode = json_encode($dadosPlanilha);
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

}