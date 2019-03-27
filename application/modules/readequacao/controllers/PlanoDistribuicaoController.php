<?php

class Readequacao_PlanodistribuicaoController extends Readequacao_GenericController
{
    private $_siEncaminhamento = null;
    private $_idTipoReadequacao = null;
    private $_existeSolicitacaoEmAnalise = false;

    public function init()
    {
        parent::init();

        $this->_siEncaminhamento = TbTipoEncaminhamento::SOLICITACAO_CADASTRADA_PELO_PROPONENTE;
        $this->_idTipoReadequacao = Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO;

        $tbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
        $this->_existeSolicitacaoEmAnalise = $tbReadequacaoMapper->existeSolicitacaoEmAnalise($this->idPronac, $this->_idTipoReadequacao);
        $this->view->existeSolicitacaoEmAnalise = $this->_existeSolicitacaoEmAnalise;
    }

    public function indexAction()
    {
        try {
            $this->view->projeto = $this->projeto;
            $this->view->idTipoReadequacao = $this->_idTipoReadequacao;

            if (!$this->in2017) {
                $this->redirect("/readequacao/plano-distribuicao/readequacao-antiga-in/?idPronac=" . $this->idPronacHash);
            }
        } catch (Exception $e) {
            parent::message($e->getMessage(), "/default/consultardadosprojeto/index?idPronac=" . $this->idPronacHash, "ERROR");
        }
    }

    public function readequacaoAntigaInAction()
    {
        $this->view->projeto = $this->projeto;
        $this->view->idTipoReadequacao = $this->_idTipoReadequacao;
        $this->view->urlCallback = '/readequacao/plano-distribuicao/readequacao-antiga-in/?idPronac=' . $this->idPronacHash;
        $this->view->action = '/readequacao/plano-distribuicao/salvar-readequacao-antiga-in/?idPronac=' . $this->idPronacHash;

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $this->view->readequacao = $tbReadequacao->obterDadosReadequacao(
            $this->_idTipoReadequacao,
            $this->idPronac
        );
    }

    public function carregarPlanosDeDistribuicaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $planosDistribuicao = $tbPlanoDistribuicao->buscarPlanosDistribuicaoReadequacao($idPronac, 'tbPlanoDistribuicao');

        if (count($planosDistribuicao) == 0) {
            $planosDistribuicao = $tbPlanoDistribuicao->buscarPlanosDistribuicaoReadequacao($idPronac, 'PlanoDistribuicaoProduto');
        }

        $Verificacao = new Verificacao();
        $posicoesLogomarca = $Verificacao->buscar(array('idTipo=?' => 3), array('Descricao'));

        $get = Zend_Registry::get('get');
        $link = isset($get->link) ? true : false;

        $this->montaTela(
            'plano-distribuicao/carregar-planos-de-distribuicao.phtml',
            array(
                'idPronac' => $idPronac,
                'planosDistribuicao' => $planosDistribuicao,
                'posicoesLogomarca' => $posicoesLogomarca,
                'link' => $link
            )
        );
    }

    public function salvarReadequacaoAntigaInAction()
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
            parent::message('J&aacute; existe uma solicita&ccedil;ao de readequa&ccedil;&atilde;o em an&aacute;lise!', $urlCallback, "ERROR");
        }

        try {
            $idReadequacao = filter_var($this->_request->getParam("idReadequacao"), FILTER_SANITIZE_NUMBER_INT);
            $idTipoReadequacao = filter_var($this->_request->getParam("tipoReadequacao"), FILTER_SANITIZE_NUMBER_INT);
            $params = $this->getRequest()->getParams();

            $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
            $planosReadequados = $tbPlanoDistribuicao->buscar(array(
                'idPronac = ?' => $this->idPronac,
                'tpSolicitacao <> ?' => 'N',
                'stAtivo = ?' => 'S',
                'tpAnaliseTecnica = ?' => 'N',
            ));

            if (count($planosReadequados) == 0) {
                parent::message('N&atilde;o houve nenhuma altera&ccedil;&atilde;o nos planos de distribui&ccedil;&atilde;o do projeto!', $urlCallback, "ERROR");
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
            $idReadequacao = $tbReadequacaoMapper->salvarSolicitacaoReadequacao($dados);

            if ($idReadequacao) {
                $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
                $tbPlanoDistribuicaoMapper->incluirIdReadequacaoNasSolicitacoesAtivas($this->idPronac, $idReadequacao);

                $tbDistribuicaoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
                $tbDistribuicaoMapper->incluirIdReadequacaoNasSolicitacoesAtivas($this->idPronac, $idReadequacao);
            }

            parent::message("Solicita&ccedil;&atilde;o cadastrada com sucesso!", $urlCallback, "CONFIRM");
        } catch (Exception $e) {
            parent::message($e->getMessage(), $urlCallback, "ERROR");
        }
    }

    public function carregarPlanosDeDistribuicaoReadequacoesAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $idPronac = $this->_request->getParam("idPronac");
        $idReadequacao = $this->_request->getParam("idReadequacao");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $planosDistribuicao = $tbPlanoDistribuicao->buscarPlanosDistribuicaoConsolidadoReadequacao($idReadequacao);

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosReadequacao = $tbReadequacao->buscar(array('idReadequacao=?' => $idReadequacao))->current();
        $siEncaminhamento = $dadosReadequacao->siEncaminhamento;

        $Produtos = new Produto();
        $produtos = $Produtos->buscar(array('stEstado=?' => 0), array('Descricao'));

        $Verificacao = new Verificacao();
        $posicoesLogomarca = $Verificacao->buscar(array('idTipo=?' => 3), array('Descricao'));

        $Area = new Area();
        $areas = $Area->buscar(array('Codigo != ?' => 7), array('Descricao'));

        $get = Zend_Registry::get('get');
        $link = isset($get->link) ? true : false;

        $this->montaTela(
            'plano-distribuicao/visualizar-planos-de-distribuicao.phtml',
            array(
                'idPronac' => $idPronac,
                'planosDistribuicao' => $planosDistribuicao,
                'produtos' => $produtos,
                'posicoesLogomarca' => $posicoesLogomarca,
                'areas' => $areas,
                'idReadequacao' => $idReadequacao,
                'link' => $link,
                'siEncaminhamento' => $siEncaminhamento
            )
        );

    }

    public function incluirPlanosDeDistribuicaoAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        //VERIFICA SE JA POSSUI OS PLANOS DE DISTRIBUICAO NA TABELA tbPlanoDistribuicao (READEQUACAO), SE NAO TIVER, COPIA DA ORIGINAL, E DEPOIS INCLUI O ITEM DESEJADO.
        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $readequacaoPDDist = $tbPlanoDistribuicao->buscar(array('idPronac=?' => $idPronac, 'stAtivo=?' => 'S'));

        $planosAtivos = $tbPlanoDistribuicao->buscarPlanosDistribuicaoReadequacao($idPronac);

        if (count($readequacaoPDDist) == 0) {
            $planosCopiados = array();
            foreach ($planosAtivos as $value) {
                $planosCopiados['idReadequacao'] = null;
                $planosCopiados['idPlanoDistribuicaoOriginal'] = $value->idPlanoDistribuicao;
                $planosCopiados['idProduto'] = $value->idProduto;
                $planosCopiados['cdArea'] = $value->idArea;
                $planosCopiados['cdSegmento'] = $value->idSegmento;
                $planosCopiados['idPosicaoLogo'] = $value->idPosicaoDaLogo;
                $planosCopiados['qtProduzida'] = $value->QtdeProduzida;
                $planosCopiados['qtPatrocinador'] = $value->QtdePatrocinador;
                $planosCopiados['qtOutros'] = $value->QtdeOutros;
                $planosCopiados['qtProponente'] = $value->QtdeProponente;
                $planosCopiados['qtVendaNormal'] = $value->QtdeVendaNormal;
                $planosCopiados['qtVendaPromocional'] = $value->QtdeVendaPromocional;
                $planosCopiados['vlUnitarioNormal'] = !empty($value->vlUnitarioNormal) ? $value->vlUnitarioNormal: 0;
                $planosCopiados['PrecoUnitarioNormal'] = $value->PrecoUnitarioNormal;
                $planosCopiados['vlUnitarioPromocional'] = $value->PrecoUnitarioPromocional;
                $planosCopiados['stPrincipal'] = $value->stPrincipal;
                $planosCopiados['tpSolicitacao'] = 'N'; # N - nenhuma, I - inclusao, A - alteracao
                $planosCopiados['stAtivo'] = 'S';
                $planosCopiados['idPronac'] = $idPronac;
                $tbPlanoDistribuicao->inserir($planosCopiados);
            }
        }

        $verificaPlanoRepetido = $tbPlanoDistribuicao->buscar(array('idPronac=?' => $idPronac, 'stAtivo=?' => 'S', 'idProduto=?' => $_POST['newPlanoDistribuicao'], 'idReadequacao IS NULL' => ''));
        if (count($verificaPlanoRepetido) == 1) {
            $QtdeProduzida = $_POST['newQntdNormal'] + $_POST['newQntdPromocional'] + $_POST['newQntdPatrocinador'] + $_POST['newQntdPopulacaoBaixaRenda'] + $_POST['newQntdDivulgacao'];
            $preconormal = str_replace(",", ".", str_replace(".", "", $_POST['newVlNormal']));
            $precopromocional = str_replace(",", ".", str_replace(".", "", $_POST['newVlPromocional']));

            /* DADOS PARA ATUALIZACAO */
            $dadosInclusao = array();
            $dadosInclusao['qtProduzida'] = $QtdeProduzida;
            $dadosInclusao['qtPatrocinador'] = $_POST['newQntdPatrocinador'];
            $dadosInclusao['qtProponente'] = $_POST['newQntdDivulgacao'];
            $dadosInclusao['qtOutros'] = $_POST['newQntdPopulacaoBaixaRenda'];
            $dadosInclusao['qtVendaNormal'] = $_POST['newQntdNormal'];
            $dadosInclusao['qtVendaPromocional'] = $_POST['newQntdPromocional'];
            $dadosInclusao['vlUnitarioNormal'] = $preconormal;
            $dadosInclusao['vlUnitarioPromocional'] = $precopromocional;
            $dadosInclusao['tpSolicitacao'] = 'A'; # N - nenhuma, I - inclusao, A - alteracao
            $dadosInclusao['stAtivo'] = 'S';
            $where = [
                'idPronac = ?' => $idPronac,
                'idProduto = ?' => $_POST['newPlanoDistribuicao'],
                'stAtivo = ?' => 'S'
            ];
            $update = $tbPlanoDistribuicao->update($dadosInclusao, $where);

            if ($update) {
                //$jsonEncode = json_encode($dadosPlanilha);
                $this->_helper->json(array('resposta' => true));
            } else {
                $this->_helper->json(array('resposta' => false, 'msg' => "Erro ao salvar!"));
            }
        } else {
            $msg = utf8_encode('N&atilde;o foi poss&iacute;vel salvar sua solicita&ccedil;&atilde;o, delete a readequa&ccedil;&atilde;o antes de alterar!');
            $this->_helper->json(array('resposta' => false, 'msg' => $msg));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /*
     * @deprecated: 27/04/2018 - nao utilizado
     */
    public function excluirPlanoDeDistribuicaoAction()
    {
        $this->_helper->layout->disableLayout();
        $idPlanoDistribuicao = $this->_request->getParam("idPlanoDistribuicao");
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        //VERIFICA SE JA POSSUI OS PLANOS DE DISTRIBUICAO NA TABELA tbPlanoDistribuicao (READEQUACAO), SE NAO TIVER, COPIA DA ORIGINAL, E DEPOIS INCLUI O ITEM DESEJADO.
        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $readequacaoPDDist = $tbPlanoDistribuicao->buscar(array('idPronac=?' => $idPronac, 'stAtivo=?' => 'S'));
        $planosAtivos = $tbPlanoDistribuicao->buscarPlanosDistribuicaoReadequacao($idPronac);

        if (count($readequacaoPDDist) == 0) {
            $planosCopiados = array();
            foreach ($planosAtivos as $value) {
                $planosCopiados['idReadequacao'] = null;
                $planosCopiados['idProduto'] = $value->idProduto;
                $planosCopiados['cdArea'] = $value->idArea;
                $planosCopiados['cdSegmento'] = $value->idSegmento;
                $planosCopiados['idPosicaoLogo'] = $value->idPosicaoDaLogo;
                $planosCopiados['qtProduzida'] = $value->QtdeProduzida;
                $planosCopiados['qtPatrocinador'] = $value->QtdePatrocinador;
                $planosCopiados['qtOutros'] = $value->QtdeOutros;
                $planosCopiados['qtProponente'] = $value->QtdeProponente;
                $planosCopiados['qtVendaNormal'] = $value->QtdeVendaNormal;
                $planosCopiados['qtVendaPromocional'] = $value->QtdeVendaPromocional;
                $planosCopiados['vlUnitarioNormal'] = $value->PrecoUnitarioNormal;
                $planosCopiados['vlUnitarioPromocional'] = $value->PrecoUnitarioPromocional;
                $planosCopiados['stPrincipal'] = $value->stPrincipal;
                $planosCopiados['tpSolicitacao'] = 'N';
                $planosCopiados['stAtivo'] = 'S';
                $planosCopiados['idPronac'] = $idPronac;
                $tbPlanoDistribuicao->inserir($planosCopiados);
            }
        }

        /* DADOS DO ITEM PARA EXCLUSAO LOGICA DO ITEM DA READEQUACAO */
        $dados = array();
        $dados['tpSolicitacao'] = 'E';

        $itemPDDist = $tbPlanoDistribuicao->buscar(array('idPlanoDistribuicao=?' => $idPlanoDistribuicao))->current();
        if ($itemPDDist) {
            if ($itemPDDist->tpSolicitacao == 'I') {
                $exclusaoLogica = $tbPlanoDistribuicao->delete(array('idPlanoDistribuicao = ?' => $idPlanoDistribuicao));
            } else {
                $where = "stAtivo = 'S' AND idPlanoDistribuicao = $idPlanoDistribuicao";
                $exclusaoLogica = $tbPlanoDistribuicao->update($dados, $where);
            }
        } else {
            $PlanoDistribuicao = new PlanoDistribuicao();
            $itemPDDist = $PlanoDistribuicao->find(array('idPlanoDistribuicao=?' => $idPlanoDistribuicao))->current();
            $dadosArray = array(
                'idProduto =?' => $itemPDDist->idProduto,
                'idPronac =?' => $idPronac,
                'stAtivo =?' => 'S',
            );
            $itemPDDist = $tbPlanoDistribuicao->buscar($dadosArray)->current();
            $where = "stAtivo = 'S' AND idPlanoDistribuicao = $itemPDDist->idPlanoDistribuicao";
            $exclusaoLogica = $tbPlanoDistribuicao->update($dados, $where);
        }

        if ($exclusaoLogica) {
            //$jsonEncode = json_encode($dadosPlanilha);
            $this->_helper->json(array('resposta' => true));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function obterPlanoDistribuicaoDetalhamentosAjaxAction()
    {
        $this->view->idPerfil = $this->idPerfil;

        try {

            if (empty($this->projeto)) {
                throw new Exception("Projeto &eacute; obrigat&oacute;rio obter os planos de distribui&ccedil;&atilde;o");
            }

            $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
            $dados['planodistribuicao'] = $tbPlanoDistribuicaoMapper->obterPlanosDistribuicao($this->projeto);

            $detalhamentoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
            $dados['detalhamentos'] = $detalhamentoMapper->obterDetalhamentosParaReadequacao($this->projeto);


            $this->_helper->json(
                [
                    'data' => TratarArray::utf8EncodeArray($dados),
                    'success' => 'true'
                ]
            );
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(
                [
                    'msg' => "Plano de Distribui&ccedil;&atilde;o - " . $e->getMessage(),
                    'data' => [],
                    'success' => 'false'
                ]
            );
        }
    }

    public function criarReadequacaoAjaxAction()
    {
        try {

            if (empty($this->projeto)) {
                throw new Exception("Projeto &eacute; obrigat&oacute;rio obter os planos de distribui&ccedil;&atilde;o");
            }

            if ($this->idPerfil !== Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Voc&ecirc; n&atilde;o pode criar uma readequa&ccedil;&atilde;o");
            }

            if ($this->_existeSolicitacaoEmAnalise) {
                throw new Exception("Readequa&ccedil;&atilde;o em an&aacute;lise");
            }

            $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper($this->projeto);
            $status = $tbPlanoDistribuicaoMapper->criarReadequacaoPlanoDistribuicao($this->projeto);
            $this->_helper->json(['data' => ['status' => $status], 'success' => 'true']);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(
                [
                    'msg' => "Erro ao criar readequa&ccedil;&atilde;o. " . $e->getMessage(),
                    'data' => [],
                    'success' => 'false'
                ]
            );
        }
    }

    public function salvarDetalhamentoAjaxAction()
    {
        $dados = $this->getRequest()->getPost();

        try {

            if (empty($this->projeto)) {
                throw new Exception("Projeto &eacute; obrigat&oacute;rio");
            }

            if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Acesso negado!");
            }

            $detalhamentoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
            $dados = $detalhamentoMapper->salvarDetalhamento($dados, $this->projeto);

            $this->_helper->json(array('data' => $dados, 'success' => 'true', 'msg' => 'Detalhamento salvo com sucesso!'));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('data' => $dados, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    public function excluirDetalhamentoAjaxAction()
    {
        $dados = $this->getRequest()->getPost();

        try {

            if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Acesso negado!");
            }

            if ($this->_existeSolicitacaoEmAnalise) {
                throw new Exception("Readequa&ccedil;&atilde;o em an&aacute;lise");
            }

            $detalhamentoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
            $dados = $detalhamentoMapper->excluirItemDetalhamento($dados, $this->projeto);

            $this->_helper->json(array('data' => $dados, 'success' => 'true', 'msg' => 'Detalhamento exclu&iacute;do com sucesso!'));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('data' => $dados, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    public function restaurarDetalhamentoAjaxAction()
    {
        $dados = $this->getRequest()->getPost();

        try {

            if (empty($this->projeto)) {
                throw new Exception("Projeto &eacute; obrigat&oacute;rio");
            }

            if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Acesso negado!");
            }

            if ($this->_existeSolicitacaoEmAnalise) {
                throw new Exception("Readequa&ccedil;&atilde;o em an&aacute;lise");
            }

            $mdlDetalhaReadequacao = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacao();
            $detalhamentoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
            $dados = $detalhamentoMapper->alterarSituacaoDetalhamento(
                $dados,
                $mdlDetalhaReadequacao::TP_SOLICITACAO_NAO_ALTERADO,
                $this->projeto
            );

            $this->_helper->json(array('data' => $dados, 'success' => 'true', 'msg' => 'Detalhamento restaurado com sucesso!'));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('data' => $dados, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    public function atualizarReadequacaoAjaxAction()
    {
        try {
            $params = $this->getRequest()->getParams();

            $idReadequacao = $this->salvarReadequacao($params);

            $this->_helper->json(array('data' => $idReadequacao, 'success' => 'true', 'msg' => 'Solicita&ccedil;&atilde;o atualizada com sucesso!'));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('data' => $params, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    private function salvarReadequacao($data)
    {

        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!");
        }

        if (empty($data['idPronac'])) {
            throw new Exception("PRONAC &eacute; obrigat&oacute;rio");
        }

        if ($this->_existeSolicitacaoEmAnalise) {
            throw new Exception("Readequa&ccedil;&atilde;o em an&aacute;lise");
        }

        if (count(trim($data['justificativa'])) == 0) {
            throw new Exception("Readequa&ccedil;&atilde;o em an&aacute;lise");
        }

        $dados = [
            'idReadequacao' => $data['idReadequacao'],
            'idPronac' => $data['idPronac'],
            'idTipoReadequacao' => $data['idTipoReadequacao'],
            'dsJustificativa' => utf8_decode($data['justificativa']),
            'idDocumento' => $data['idDocumento']
        ];

        $TbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
        return $TbReadequacaoMapper->salvarSolicitacaoReadequacao($dados);
    }

    public function excluirReadequacaoPlanoDistribuicaoAjaxAction()
    {
        try {
            if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!");
            }

            if (empty($this->idPronac)) {
                throw new Exception("PRONAC &eacute; obrigat&oacute;rio");
            }

            if ($this->_existeSolicitacaoEmAnalise) {
                throw new Exception("Readequa&ccedil;&atilde;o em an&aacute;lise");
            }

            $tbReadequacaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
            $status = $tbReadequacaoMapper->excluirReadequacaoPlanoDistribuicaoAtiva($this->idPronac);

            $this->_helper->json(array('data' => ['response' => $status], 'success' => 'true', 'msg' => 'Solicita&ccedil;&atilde;o exclu&iacute;da com sucesso'));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('data' => [], 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    public function finalizarReadequacaoPlanoDistribuicaoAjaxAction()
    {
        try {
            $params = $this->getRequest()->getParams();

            if (empty($params['idTipoReadequacao'])) {
                throw new Exception('Tipo da readequa&ccedil;&atilde;o n&atilde;o informada!');
            }

            if (empty($params['idReadequacao'])) {
                throw new Exception('Readequa&ccedil;&atilde;o n&atilde;o encontrada');
            }

            $idReadequacao = $this->salvarReadequacao($params);

            if (empty($idReadequacao)) {
                throw new Exception("Erro ao salvar readequa&ccedil;&atilde;o");
            }

            $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
            $planosReadequados = $tbPlanoDistribuicao->buscar(array(
                'idPronac = ?' => $params['idPronac'],
                'tpSolicitacao <> ?' => 'N',
                'stAtivo = ?' => 'S',
                'idReadequacao = ?' => $params['idReadequacao']
            ));

            if (count($planosReadequados) == 0) {
                throw new Exception('N&atilde;o houve nenhuma altera&ccedil;&atilde;o nos planos de distribui&ccedil;&atilde;o do projeto!');
            }

            $tbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
            $status = $tbReadequacaoMapper->finalizarSolicitacaoReadequacao(
                $this->idPronac, $params['idTipoReadequacao'], $params['idReadequacao']
            );

            if ($status == false) {
                throw new Exception("N&atilde;o foi poss&iacute;vel finalizar a solicita&ccedil;&atilde;o");
            }

            $this->_helper->json(array('data' => $status, 'success' => 'true', 'msg' => 'Readequa&ccedil;&atilde;o finalizada com sucesso!'));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('data' => $params, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }
}
