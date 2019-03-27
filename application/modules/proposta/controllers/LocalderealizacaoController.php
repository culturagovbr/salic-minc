<?php

/**
 * LocalDeRealizacaoController
 * @author Equipe RUP - Politec
 * @author wouerner <wouerner@gmail.com>
 * @since 15/12/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @copyright ? 2010 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class Proposta_LocalderealizacaoController extends Proposta_GenericController
{

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init();

        //*******************************************
        //VALIDA ITENS DO MENU (Documento pendentes)
        //*******************************************
//        $model = new Proposta_Model_DbTable_DocumentosExigidos();
//        //$this->view->documentosPendentes = $model->buscarDocumentoPendente($get->idPreProjeto);
//        $this->view->documentosPendentes = $model->buscarDocumentoPendente($this->idPreProjeto);
//
//        if (!empty($this->view->documentosPendentes)) {
//            $verificarmenu = 1;
//            $this->view->verificarmenu = $verificarmenu;
//        } else {
//            $verificarmenu = 0;
//            $this->view->verificarmenu = $verificarmenu;
//        }

        //(Enviar Proposta ao MinC , Excluir Proposta)
//        $mov = new Proposta_Model_DbTable_TbMovimentacao();
//        $movBuscar = $mov->buscar(array('idprojeto = ?' => $idPreProjeto), array('idmovimentacao desc'), 1, 0)->current();
//
//        if (isset($movBuscar->Movimentacao) && $movBuscar->Movimentacao != 95) {
//            $enviado = 'true';
//            $this->view->enviado = $enviado;
//        } else {
//            $enviado = 'false';
//            $this->view->enviado = $enviado;
//        }

        $this->verificarPermissaoAcesso(true, false, false);
        $this->validarEdicaoProposta();

        //recupera ID do pre projeto (proposta)
        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;
        } else {
            parent::message("Necess&aacute;rio informar o n&uacute;mero da proposta.", "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
        }
    }

    /**
     * Metodo que monta grid de locais de realizacao
     *
     * @name indexAction
     * @return void
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @author wouerner <wouerner@gmail.com>
     * @since  17/08/2016
     */
    public function indexAction()
    {


        $this->view->deslocamento = $this->getRequest()->getParam('deslocamento');
        $this->view->edital = $this->getRequest()->getParam('edital');


        //RECUPERA OS LOCAIS DE REALIZACAO CADASTRADOS
        $arrBusca = array();
        $arrBusca['idprojeto'] = $this->idPreProjeto;
        $arrBusca['stabrangencia'] = 1;
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);

        $arrDados = array("localizacoes" => $rsAbrangencia,
            "acaoAlterar" => $this->_urlPadrao . "/proposta/localderealizacao/form-local-de-realizacao",
            "acaoExcluir" => $this->_urlPadrao . "/proposta/localderealizacao/excluir");

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY DADOS
        $this->montaTela("localderealizacao/index.phtml", $arrDados);
    }

    /**
     *
     * Metodo que monta o formulario de editar local de realizacao
     * @param void
     * @return void
     */
    public function formLocalDeRealizacaoAction()
    {
        //recupera parametros
        $get = Zend_Registry::get('get');
        $idAbrangencia = $get->cod;

        //RECUPERA OS PAISES
        $table = new Agente_Model_DbTable_Pais();
        $arrPais = $table->fetchPairs('idPais', 'Descricao');

        //RECUPRA OS ESTADOS
        $mapperUf = new Agente_Model_UFMapper();
        $arrUf = $mapperUf->fetchPairs('idUF', 'Sigla');

        //RECUPERA LOCALIZACOES CADASTRADAS
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $arrBusca = array();
        $arrBusca['idProjeto'] = $this->idPreProjeto;
        $arrBusca['stAbrangencia'] = 1;
        if (!empty($idAbrangencia)) {
            $arrBusca['idAbrangencia'] = $idAbrangencia;
        }
        $arrAbrangencia = $tblAbrangencia->buscar($arrBusca);
        $arrCidades = "";
        # RECUPERA AS CIDADES
        if (!empty($arrAbrangencia[0]['idMunicipioIBGE'])) {
            $table = new Agente_Model_DbTable_Municipios();
            $arrCidades = $table->fetchPairs('idMunicipioIBGE', 'Descricao', array('idufibge' => $arrAbrangencia[0]['idUF']));
        }

        $arrDados = array("paises" => $arrPais,
            "estados" => $arrUf,
            'municipios' => $arrCidades,
            "localizacoes" => $arrAbrangencia,
            "idAbrangencia" => $idAbrangencia,
            "acao" => $this->_urlPadrao . "/localderealizacao/salvar-local-realizacao");

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY DADOS
        $this->montaTela("localderealizacao/formlocalderealizacao.phtml", $arrDados);
    }


    /**
     * Metodo responsavel por apagar um local de realiza&ccedil;&atilde;o gravado
     * @param void
     * @return objeto
     */
    public function excluirAction()
    {
        $excluir = false;

        $this->verificarPermissaoAcesso(true, false, false);

        $params = $this->getRequest()->getParams();

        if (!empty($params['cod'])) {
            // buscar municipio e estado desta abrangencia
            $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
            $abrangencia = $tblAbrangencia->findby(array('idabrangencia' => $params['cod']));


            if (!empty($abrangencia)) {
                // exclui itens orcamentarios
                $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $tbPlanilhaProposta->deleteBy([
                    'idProjeto' => $this->idPreProjeto,
                    'UfDespesa' => $abrangencia['idUF'],
                    'MunicipioDespesa' => $abrangencia['idMunicipioIBGE']
                ]);

                // exclui detalhamentos
                $tbDetalhaPlanoDistribuicaoMapper = new Proposta_Model_TbDetalhaPlanoDistribuicaoMapper();
                $tbDetalhaPlanoDistribuicaoMapper->excluirDetalhamentosPorLocalizacao(
                    $this->idPreProjeto,
                    $abrangencia['idUF'],
                    $abrangencia['idMunicipioIBGE']
                );

                // atualiza os custos vinculados do projeto
                $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
                $tbCustosVinculadosMapper->salvarCustosVinculadosDaTbPlanilhaProposta($this->idPreProjeto);
            }

            // exclui registro da tabela abrangencia
            $excluir = $tblAbrangencia->delete(array('idabrangencia = ?' => $params['cod']));
        }

        if ($excluir) {
            parent::message(
                "Exclus&atilde;o realizada com sucesso!",
                "/proposta/localderealizacao/index/idPreProjeto/" . $this->idPreProjeto,
                "CONFIRM"
            );
        }

        parent::message(
            "N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!",
            "/proposta/localderealizacao/index/idPreProjeto/" . $this->idPreProjeto,
            "ERROR"
        );
    }

    /**
     * Metodo que retorna lista de locais de realizacao
     * @param void
     * @return objeto
     */
    public function consultarcomponenteAction()
    {
        //recebe o id via GET
        $get = Zend_Registry::get('get');
        $idProjeto = $get->idPreProjeto;
        $this->_helper->layout->disableLayout(); // desabilita o layout
        if (!empty($idProjeto) || $idProjeto == '0') {
            //RECUPERA OS LOCAIS DE REALIZACAO CADASTRADOS
            $arrBusca = array();
            $arrBusca['idProjeto'] = $idProjeto;
            $arrBusca['stAbrangencia'] = 1;

            $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
            $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);
            $this->view->localizacoes = $rsAbrangencia;
        } else {
            return false;
        }
    }

    /**
     * formInserirAction
     *
     * @access public
     * @return void
     */
    public function formInserirAction()
    {
//        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');
//        $this->view->idPreProjeto = $idPreProjeto;

        # RECUPERA OS PAISES
        $tablePais = new Agente_Model_DbTable_Pais();
        $rsPais = $tablePais->fetchPairs('idPais', 'Descricao');
        $this->view->paises = $rsPais;

        # RECUPERA OS ESTADOS
        $mapperUf = new Agente_Model_UFMapper();
        $rsEstados = $mapperUf->fetchPairs('idUF', 'Descricao');
        $this->view->estados = $rsEstados;
    }

    /**
     * cidadesAction
     *
     * @access public
     * @return void
     */
    public function cidadesAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $post = Zend_Registry::get('post');
        $idEstado = $post->idEstado;

        # RECUPERA AS CIDADES
        $table = new Agente_Model_DbTable_Municipios();
        $arrCidades = $table->fetchPairs('idMunicipioIBGE', 'Descricao', array('idufibge' => $idEstado));
        $html = '';
        foreach ($arrCidades as $key => $cidades) {
            $html .= "<option value=\"{$key}\">{$cidades}</option>";
        }
        echo $html;
    }

    /**
     * Metódo que salva e edita o local de realizacao
     * Neste metodo, quando edita um local de realizacao o mesmo atualiza os itens da planilha orcamentaria.
     *
     * @access public
     * @return void
     */
    public function salvarLocalRealizacaoAction()
    {
        $post = Zend_Registry::get("post");
        $idAbrangencia = $post->cod;

        if (empty($this->idPreProjeto) || empty($post->pais)) {
            parent::message(
                "Dados obrigat&oacute;rios n&atilde;o informados!",
                "/proposta/localderealizacao/index/idPreProjeto/" . $this->idPreProjeto,
                "ALERT"
            );
        }

        $arrBusca = [];
        $arrBusca['idProjeto'] = $this->idPreProjeto;
        $arrBusca['stAbrangencia'] = 1;
        $arrBusca['p.idPais'] = $post->pais;
        if ($post->pais == 31) {
            $arrBusca['u.idUF'] = $post->estados;
            $arrBusca['m.idMunicipioIBGE'] = $post->cidades;
        }

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $abrangenciaJaCadastrada = $tblAbrangencia->buscar($arrBusca);

        if ((count($abrangenciaJaCadastrada) > 0 && empty($idAbrangencia))
            || (count($abrangenciaJaCadastrada) > 0 && $abrangenciaJaCadastrada[0]['idAbrangencia'] != $idAbrangencia)
        ) {
            parent::message(
                "Local de realiza&ccedil;&atilde;o j&aacute; cadastrado!",
                "/proposta/localderealizacao/index/idPreProjeto/" . $this->idPreProjeto,
                "ALERT"
            );
        }

        $dadosAbrangencia = [
            "idprojeto" => $this->idPreProjeto,
            "stabrangencia" => 1,
            "usuario" => $this->idUsuario,
            "idpais" => $post->pais,
            "iduf" => ($post->pais == 31) ? $post->estados : 0,
            "idmunicipioibge" => ($post->pais == 31) ? $post->cidades : 0
        ];


        $msg = "Local de realiza&ccedil;&atilde;o cadastrado com sucesso!";
        if (empty($idAbrangencia)) {
            $tblAbrangencia->insert($dadosAbrangencia);
        } else {
            $abrangenciaAtual = $tblAbrangencia->findby(array('idAbrangencia' => $idAbrangencia));

            $this->atualizarLocaldeRealizacaoDaPlanilha(
                $abrangenciaAtual,
                $dadosAbrangencia["iduf"],
                $dadosAbrangencia["idmunicipioibge"]
            );

            $this->atualizarLocaldeRealizacaoDoDetalhamentoProduto(
                $abrangenciaAtual,
                $dadosAbrangencia["iduf"],
                $dadosAbrangencia["idmunicipioibge"]
            );

            $msg = "Local de realiza&ccedil;&atilde;o alterado com sucesso!";
            $whereAbrangencia['idAbrangencia = ?'] = $idAbrangencia;
            $tblAbrangencia->update($dadosAbrangencia, $whereAbrangencia);
        }

        $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
        $tbCustosVinculadosMapper->salvarCustosVinculadosDaTbPlanilhaProposta($this->idPreProjeto);

        parent::message($msg, "/proposta/localderealizacao/index?idPreProjeto=" . $this->idPreProjeto, "CONFIRM");
    }

    private function atualizarLocaldeRealizacaoDaPlanilha($abrangenciaAtual, $idUf, $idMunicipio)
    {
        if (empty($abrangenciaAtual)) {
            return false;
        }

        $dadosAbrangenciaPlanilha = [
            'UfDespesa' => $idUf,
            'MunicipioDespesa' => $idMunicipio
        ];

        $wherePlanilha = [
            'idProjeto = ?' => $this->idPreProjeto,
            'UfDespesa = ?' => $abrangenciaAtual['idUF'],
            'MunicipioDespesa = ?' => $abrangenciaAtual['idMunicipioIBGE']
        ];

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        return $tbPlanilhaProposta->update($dadosAbrangenciaPlanilha, $wherePlanilha);
    }

    private function atualizarLocaldeRealizacaoDoDetalhamentoProduto($abrangenciaAtual, $idUf, $idMunicipio)
    {
        if (empty($abrangenciaAtual)) {
            return false;
        }

        $tbDetalhaPlanoDistribuicao = new Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao();
        return $tbDetalhaPlanoDistribuicao->updateLocalizacaoDetalhamento(
            $idUf,
            $idMunicipio,
            $this->idPreProjeto,
            $abrangenciaAtual['idUF'],
            $abrangenciaAtual['idMunicipioIBGE']
        );
    }
}
