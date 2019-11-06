<?php

class GerenciarparecerController extends MinC_Controller_Action_Abstract
{
    /**
     * @var integer (vari�vel com o id do usu�rio logado)
     * @access private
     */
    private $getIdUsuario = 0;
    private $intTamPag = 10;

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // t�tulo da p�gina
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 94;
            $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER;
            $PermissoesGrupo[] = 137;
            $PermissoesGrupo[] = Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA;

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o

            if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
                $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
                $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
            }
        } else {
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init();
    }

    public function indexAction()
    {
        return $this->_helper->redirector->goToRoute(array('module' => 'parecer', 'controller' => 'gerenciar-parecer', 'action' => 'index'), null, true);
    }

    /*
     * Deprecated
     *  - movida para parecer/controller/GerenciarParecerController->index()
     */
    public function listaprojetosAction()
    {
        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        if (!$this->_request->getParam("tipoFiltro")) {
            $this->view->tipoFiltro = 'aguardando_distribuicao';
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            $campo = null;
            $order = array('DtEnvioMincVinculada', 'NomeProjeto', 'stPrincipal desc');
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        $where = array();
        $where["idOrgao = ?"] = $codOrgao;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $pronac = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $where["NrProjeto = ?"] = $pronac;
            $this->view->pronacProjeto = $pronac;
        }

        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $tipoFiltro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->tipoFiltro = $tipoFiltro;
        } else {
            $tipoFiltro = 'aguardando_distribuicao';
            $this->view->tipoFiltro = $tipoFiltro;
        }

        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $total = $tbDistribuirParecer->painelAnaliseTecnica($where, $order, null, null, true, $tipoFiltro);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        $busca = $tbDistribuirParecer->painelAnaliseTecnica($where, $order, $tamanho, $inicio, false, $tipoFiltro);
        if ($tipoFiltro == 'validados' || $tipoFiltro == 'em_validacao' || $tipoFiltro == 'devolvida') {
            $checarValidacaoSecundarios = array();
            foreach ($busca as $chave => $item) {
                if ($item->stPrincipal == 1) {
                    $checarValidacaoSecundarios[$item->IdPRONAC] = $tbDistribuirParecer->checarValidacaoProdutosSecundarios($item->IdPRONAC);
                }
            }
            $this->view->checarValidacaoSecundarios = $checarValidacaoSecundarios;
        }

        $paginacao = array(
            "pag" => $pag,
            "qtde" => $this->intTamPag,
            "campo" => $campo,
            "ordem" => $ordem,
            "ordenacao" => $ordenacao,
            "novaOrdem" => $novaOrdem,
            "total" => $total,
            "inicio" => ($inicio + 1),
            "fim" => $fim,
            "totalPag" => $totalPag,
            "Itenspag" => $this->intTamPag,
            "tamanho" => $tamanho
        );

        $this->view->paginacao = $paginacao;
        $this->view->qtdDocumentos = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;
    }

    public function produtosdistribuidosAction()
    {
        $auth = Zend_Auth::getInstance();
        //		$idusuario          = $auth->getIdentity()->usu_codigo;
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;

        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $busca = $tbDistribuirParecer->produtosDistribuidos($codOrgao);

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($busca); // dados a serem paginados
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10);

        $this->view->dadospainel = $paginator;
        $this->view->qtdDocumentos = count($busca); // quantidade
    }

    public function enviarpagamentoAction()
    {
        //** Usuario Logado ************************************************/

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        //		$idusuario          = $auth->getIdentity()->usu_codigo;
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $Grupo = $GrupoAtivo->codGrupo;

        /******************************************************************/
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $busca = $tbDistribuirParecer->pagamentoParecerista($codOrgao, $Grupo);
        /******************************************************************/

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($busca); // dados a serem paginados
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10);

        $this->view->dadospainel = $paginator;
        $this->view->qtdDocumentos = count($busca); // quantidade
        $this->view->busca = $busca;
        $this->view->grupo = $Grupo;

        /******************************************************************/
    }

    public function historicoAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
//        $idUsuario = $auth->getIdentity()->usu_codigo;
        $idusuario = $this->getIdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $idOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $idPronac = $this->_request->getParam("idPronac");
        $idProduto = $this->_request->getParam("idProduto");
        $stPrincipal = $this->_request->getParam("stPrincipal");
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();

        $where['d.idPronac 		= ?'] = $idPronac;
        $where['d.idProduto 	= ?'] = $idProduto;
        $where['d.stPrincipal 	= ?'] = $stPrincipal;

        $resp = $tbDistribuirParecer->buscarHistoricoCoordenador($where);

        $cont = 0;
        $Pareceres = array();

        foreach ($resp as $key => $val) {
            $cont++;

            if ($val->DtSolicitacao && $val->DtResposta == null) {
                $diligencia = 1;
            } elseif ($val->DtSolicitacao && $val->DtResposta != null) {
                $diligencia = 2;
            } elseif ($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) {
                $diligencia = 3;
            } else {
                $diligencia = 0;
            }

            $Pareceres['pareceres'][$cont]['Nome do Produto'] = "$val->dsProduto";
            $Pareceres['pareceres'][$cont]['Unidade Respons&aacute;vel'] = "$val->Unidade";
            $Pareceres['pareceres'][$cont]['Data Distribui&ccedil;&atilde;o'] = date('d/m/Y', strtotime($val->DtDistribuicao));
            $Pareceres['pareceres'][$cont]['Data Devolu&ccedil;&atilde;o'] = ($val->DtDevolucao) ? date('d/m/Y', strtotime($val->DtDevolucao)) : null;
            $Pareceres['pareceres'][$cont]['Observa&ccedil;&otilde;es'] = $val->Observacao;
            $Pareceres['pareceres'][$cont]['Nome do Remetente'] = $val->nmUsuario;
            $Pareceres['pareceres'][$cont]['Nome do Parecerista'] = $val->nmParecerista;
        }

        $this->view->Pareceres = $Pareceres;
    }

    public function distribuirAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
//		$idusuario 	= $auth->getIdentity()->usu_codigo;
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idPronac = $this->_request->getParam("idpronac");
        $idProduto = $this->_request->getParam("idproduto");
        $TipoAnalise = $this->_request->getParam("tipoanalise");
        $tipoFiltro = $this->_request->getParam("tipoFiltro");

        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();

        $dadosWhere["IdPRONAC = ?"] = $idPronac;
        $dadosWhere["idOrgao = ?"] = $codOrgao;
        $dadosWhere["stPrincipal = ?"] = 1;
        $buscaDadosProjeto = $tbDistribuirParecer->painelAnaliseTecnica($dadosWhere, null, null, null, null, $tipoFiltro);

        $pareceristas = array();
        $spSelecionarParecerista = new Parecer_Model_DbTable_SpSelecionarParecerista();
        if (count($buscaDadosProjeto) > 0) {
            $pareceristas = $spSelecionarParecerista->exec($buscaDadosProjeto[0]->idOrgao, $buscaDadosProjeto[0]->idArea, $buscaDadosProjeto[0]->idSegmento, $buscaDadosProjeto[0]->Valor);
        }

        $orgaos = new Orgaos();
        $buscar = $orgaos->buscar(
            array(
                "Codigo <> ?" => $codOrgao,
                "Status = ?" => 0,
                "Vinculo = ?" => 1,
                "stVinculada = ?" => 1,
                "idsecretaria <> ?" => 251,
            ),
            array(2)
        );

        // Apenas o IPHAN principal pode ter acesso as unidades vinculadas
        if ($codOrgao == 91) {
            $buscarIPHAN = $orgaos->buscar(
                array(
                    "Codigo <> ?" => $codOrgao,
                    "Status = ?" => 0,
                    "idSecretaria = ?" => 91 ,
                )
            );

            $buscar = array_merge($buscar->toArray(), $buscarIPHAN->toArray());
        }

        $this->view->idSegmentoProduto = $buscaDadosProjeto[0]->idSegmento;
        $this->view->idAreaProduto = $buscaDadosProjeto[0]->idArea;
        $this->view->orgaos = $buscar;
        $this->view->pareceristas = $pareceristas;
        $this->view->dadosProjeto = $buscaDadosProjeto;
        $this->view->idpronac = $idPronac;
        $this->view->idproduto = $idProduto;
        $this->view->tipoanalise = $TipoAnalise;
        $this->view->tipoFiltro = $tipoFiltro;
    }

    public function distribuiuAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idPronac = $this->_request->getParam("idpronac");
        $observacao = $this->_request->getParam("obs");
        $orgaoDestino = $this->_request->getParam("orgao");
        $idAgenteParecerista = $this->_request->getParam("idAgenteParecerista");
        $tipoescolha = $this->_request->getParam("tipodistribuir");
        $stPrincipal = $this->_request->getParam("stPrincipal");
        $tipoFiltro = $this->_request->getParam("tipoFiltro");

        if (strlen($observacao) < 11) {
            parent::message("Dados obrigat&aacute;rios n&atilde;o informados.", "gerenciarparecer/distribuir/idpronac/" . $idPronac, "ALERT");
        }

        if ((empty($idAgenteParecerista)) && ($tipoescolha == 1)) {    // distribuir
            parent::message(
                "Dados obrigat&aacute;rios n&atilde;o informados.",
                "gerenciarparecer/encaminhar/idproduto/" . $idProduto . "/tipoanalise/" . $TipoAnalise . "/idpronac/" . $idPronac . "/tipoFiltro/" . $tipoFiltro,
                "ALERT"
            );
        }
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();

        $dadosWhere["IdPRONAC = ?"] = $idPronac;
        $dadosWhere["idOrgao = ?"] = $codOrgao;
        $buscaDadosProjeto = $tbDistribuirParecer->painelAnaliseTecnica($dadosWhere, null, null, null, null, $tipoFiltro);

        $error = "";
        $msg = "Distribui&ccedil;&atilde;o realizada com sucesso!";

        $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;

        $objAssinatura = new Assinatura_Model_DbTable_TbAssinatura();
        $assinaturas = $objAssinatura->obterAssinaturas($idPronac, $idTipoDoAtoAdministrativo);

        if (count($assinaturas) > 0) {
            $idDocumentoAssinatura = current($assinaturas)['idDocumentoAssinatura'];

            $objDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $dadosDocumentoAssinatura = array();
            $dadosDocumentoAssinatura["stEstado"] = Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO;
            $whereDocumentoAssinatura = "idDocumentoAssinatura = $idDocumentoAssinatura";
            $objDocumentoAssinatura->update($dadosDocumentoAssinatura, $whereDocumentoAssinatura);
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $projetos = new Projetos();

        try {
            /* $db->beginTransaction(); */

            foreach ($buscaDadosProjeto as $dp) {

                // se forem validados ou em valida��o, zera fecharAnalise
                if ($tipoFiltro == 'devolvida' || $tipoFiltro == 'validados' || $tipoFiltro == 'em_validacao') {
                    $dp->FecharAnalise = 0;
                } else {
                    $dp->FecharAnalise;
                }

                if ($tipoescolha == 2) {    // encaminhar
                    $msg = "Enviado os Produtos/Projeto para a entidade!";

                    // ALTERAR UNIDADE DE AN�LISE ( COORDENADOR DE PARECER )

                    $dadosE = array(
                        'idOrgao' => $orgaoDestino,
                        'DtEnvio' => MinC_Db_Expr::date(),
                        'idAgenteParecerista' => null,
                        'DtDistribuicao' => null,
                        'DtDevolucao' => null,
                        'DtRetorno' => null,
                        'FecharAnalise' => 0,
                        'Observacao' => $observacao,
                        'idUsuario' => $idusuario,
                        'idPRONAC' => $dp->IdPRONAC,
                        'idProduto' => $dp->idProduto,
                        'TipoAnalise' => 3,
                        'stEstado' => 0,
                        'stPrincipal' => $dp->stPrincipal,
                        'stDiligenciado' => null
                    );

                    $where['idDistribuirParecer = ?'] = $dp->idDistribuirParecer;
                    $salvar = $tbDistribuirParecer->alterar(array('stEstado' => 1), $where);

                    $insere = $tbDistribuirParecer->inserir($dadosE);

                    $orgaos = new Orgaos();
                    $orgao = $orgaos->pesquisarNomeOrgao($codOrgao);

                    $projetos->alterarSituacao($dp->IdPRONAC, null, 'B11', 'Encaminhado para <strong>' . $orgao[0]->NomeOrgao . ' para an&aacute;lise e emiss&atilde;o de parecer t&eacute;cnico</strong>.');
                } else {
                    $msg = "Distribui&ccedil;&atilde;o Realizada com sucesso!";

                    $fecharAnalise = ($dp->FecharAnalise == 3) ? '0' : $dp->FecharAnalise;
                    // DISTRIBUIR OU REDISTRIBUIR ( COORDENADOR DE PARECER )
                    $dadosD = array(
                        'idOrgao' => $dp->idOrgao,
                        'DtEnvio' => $dp->DtEnvioMincVinculada,
                        'idAgenteParecerista' => $idAgenteParecerista,
                        'DtDistribuicao' => MinC_Db_Expr::date(),
                        'DtDevolucao' => null,
                        'DtRetorno' => null,
                        'FecharAnalise' => $fecharAnalise,
                        'Observacao' => $observacao,
                        'idUsuario' => $idusuario,
                        'idPRONAC' => $dp->IdPRONAC,
                        'idProduto' => $dp->idProduto,
                        'TipoAnalise' => 3,
                        'stEstado' => 0,
                        'stPrincipal' => $dp->stPrincipal,
                        'stDiligenciado' => null
                    );

                    $where['idDistribuirParecer = ?'] = $dp->idDistribuirParecer;
                    $salvar = $tbDistribuirParecer->alterar(array('stEstado' => 1), $where);

                    $insere = $tbDistribuirParecer->inserir($dadosD);
                    $projetos = new Projetos();
                    $projetos->alterarSituacao($dp->IdPRONAC, null, 'B11', 'Encaminhado para o perito para an&aacute;lise t&eacute;cnica e emiss&atilde;o de parecer.');
                }
            }

            parent::message($msg . ' '.$insere, "parecer/gerenciar-parecer/index?tipoFiltro=" . $tipoFiltro, "CONFIRM");
            /* $db->commit(); */
        } catch (Zend_Exception $ex) {
            /* $db->rollBack(); */
            parent::message("Error" . $ex->getMessage(), "gerenciarparecer/distribuir/idDistribuirParecer/" . $idDistribuirParecer . "/idproduto/" . $idProduto . "/tipoanalise/" . $TipoAnalise . "/idpronac/" . $idPronac . "/tipoFiltro/" . $tipoFiltro, "ERROR");
        }
    }

    public function encaminharAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        //$idusuario 	= $auth->getIdentity()->usu_codigo;
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        /******************************************************************/

        $idPronac = $this->_request->getParam("idpronac");
        $idProduto = $this->_request->getParam("idproduto");
        $tipoFiltro = $this->_request->getParam("tipoFiltro");
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();

        //Produto Principal
        $dadosWhere["IdPRONAC = ?"] = $idPronac;
        $dadosWhere["idOrgao = ?"] = $codOrgao;
        $dadosWhere["stPrincipal = ?"] = 1;
        $dadosWhere["idProduto = ?"] = $idProduto;

        $buscaDadosProjeto = $tbDistribuirParecer->painelAnaliseTecnica($dadosWhere, null, null, null, null, $tipoFiltro);

        //Produto Secundario
        $dadosWhereS["IdPRONAC = ?"] = $idPronac;
        $dadosWhereS["idOrgao = ?"] = $codOrgao;
        $dadosWhereS["stPrincipal = ?"] = 0;
        $dadosWhereS["idProduto = ?"] = $idProduto;
        $buscaDadosProjetoS = $tbDistribuirParecer->painelAnaliseTecnica($dadosWhereS, null, null, null, null, $tipoFiltro);

        if ((count($buscaDadosProjetoS) == 0) && (count($buscaDadosProjeto) == 0)) {
            parent::message("Todos os produtos foram distribuidos!", "parecer/gerenciar-parecer/index?tipoFiltro=" . $tipoFiltro, "ALERT");
        }

        //Produto Secundario
        $dadosWhereSA["IdPRONAC = ?"] = $idPronac;
        $dadosWhereSA["idOrgao = ?"] = $codOrgao;
        $dadosWhereSA["stPrincipal = ?"] = 0;
        $dadosWhereSA["idProduto = ?"] = $idProduto;
        $buscaDadosProjetoSA = $tbDistribuirParecer->painelAnaliseTecnica($dadosWhereSA, null, null, null, null, $tipoFiltro);

        if (count($buscaDadosProjetoSA) > 0 && count($buscaDadosProjetoS) == 0) {
            parent::message("Todos os produtos foram distribuidos SA!", "parecer/gerenciar-parecer/index?tipoFiltro=" . $tipoFiltro, "ALERT");
        }

        if (count($buscaDadosProjetoS) != 0) {
            $this->view->dadosProjeto = $buscaDadosProjetoS;
        } else {
            $this->view->dadosProjeto = $buscaDadosProjeto;
        }

        $pareceristas = array();
        $spSelecionarParecerista = new Parecer_Model_DbTable_SpSelecionarParecerista();
        if (count($buscaDadosProjetoS) > 0) {
            $pareceristas = $spSelecionarParecerista->exec(
                $buscaDadosProjetoS[0]->idOrgao,
                $buscaDadosProjetoS[0]->idArea,
                $buscaDadosProjetoS[0]->idSegmento,
                $buscaDadosProjetoS[0]->Valor
            );
            $this->view->idSegmentoProduto = $buscaDadosProjetoS[0]->idSegmento;
            $this->view->idAreaProduto = $buscaDadosProjetoS[0]->idArea;
        } elseif (count($buscaDadosProjetoSA) > 0) {
            $pareceristas = $spSelecionarParecerista->exec(
                $buscaDadosProjetoSA[0]->idOrgao,
                $buscaDadosProjetoSA[0]->idArea,
                $buscaDadosProjetoSA[0]->idSegmento,
                $buscaDadosProjetoSA[0]->Valor
            );
            $this->view->idSegmentoProduto = $buscaDadosProjetoSA[0]->idSegmento;
            $this->view->idAreaProduto = $buscaDadosProjetoSA[0]->idArea;
        } else {
            $pareceristas = $spSelecionarParecerista->exec(
                $buscaDadosProjeto[0]->idOrgao,
                $buscaDadosProjeto[0]->idArea,
                $buscaDadosProjeto[0]->idSegmento,
                $buscaDadosProjeto[0]->Valor
            );
            $this->view->idSegmentoProduto = $buscaDadosProjeto[0]->idSegmento;
            $this->view->idAreaProduto = $buscaDadosProjeto[0]->idArea;
        }
        $orgaos = new Orgaos();

        $buscar = $orgaos->buscar(
            array(
                "Codigo <> ?" => $codOrgao,
                "Status = ?" => 0,
                "Vinculo = ?" => 1,
                "stvinculada = ?" => 1,
                "idSecretaria <> ?" => 251 ,
            )
        );

        // Apenas o IPHAN principal pode ter acesso as unidades vinculadas
        if ($codOrgao == 91) {
            $buscarIPHAN = $orgaos->buscar(
                array(
                    "Codigo <> ?" => $codOrgao,
                    "Status = ?" => 0,
                    "idSecretaria = ?" => 91 ,
                )
            );

            $buscar = array_merge($buscar->toArray(), $buscarIPHAN->toArray());
        }

        $this->view->orgaos = $buscar;
        $this->view->idpronac = $idPronac;
        $this->view->pareceristas = $pareceristas;
    }

    public function encaminhouAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idDistribuirParecer = $this->_request->getParam("idDistribuirParecer");
        $idPronac = $this->_request->getParam("idpronac");
        $idProduto = $this->_request->getParam("idproduto");

        $observacao = $this->_request->getParam("obs");
        $orgaoDestino = $this->_request->getParam("orgao");
        $idAgenteParecerista = $this->_request->getParam("idAgenteParecerista");
        $tipoescolha = $this->_request->getParam("tipodistribuir");
        $tipoFiltro = $this->_request->getParam("tipoFiltro");

        if (strlen($observacao) < 11) {
            parent::message(
                "O campo observa&ccedil;&atilde;o deve ter no m&iacute;nimo 11 caracteres!",
                "gerenciarparecer/encaminhar/idproduto/" . $idProduto . "/idpronac/" . $idPronac,
                "ALERT"
            );
        }

        if ((empty($idAgenteParecerista)) && ($tipoescolha == 1)) {
            parent::message(
                "Selecione um Parecerista!",
                "gerenciarparecer/encaminhar/idproduto/" . $idProduto . "/idpronac/" . $idPronac . "/tipoFiltro/" . $tipoFiltro,
                "ALERT"
            );
        }

        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();

        $dadosWhere["idDistribuirParecer = ?"] = $idDistribuirParecer;
        $buscaDadosProjeto = $tbDistribuirParecer->painelAnaliseTecnica($dadosWhere, null, null, null, null, $tipoFiltro);


        $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;

//        $objAssinatura = new Assinatura_Model_DbTable_TbAssinatura();
//        $assinaturas = $objAssinatura->obterAssinaturas($idPronac, $idTipoDoAtoAdministrativo);
//        if (count($assinaturas) > 0) {
//            $idDocumentoAssinatura = current($assinaturas)['idDocumentoAssinatura'];
//
//            $objDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
//            $dadosDocumentoAssinatura = array();
//            $dadosDocumentoAssinatura["stEstado"] = 0;
//            $whereDocumentoAssinatura = "idDocumentoAssinatura = $idDocumentoAssinatura";
//
//            $objDocumentoAssinatura->update($dadosDocumentoAssinatura, $whereDocumentoAssinatura);
//        }

        $error = '';
        $projetos = new Projetos();

        try {
            foreach ($buscaDadosProjeto as $dp) {
                // invalida e redistribui

                // se forem validados ou em valida��o, zera fecharAnalise
                if ($tipoFiltro == 'devolvida' || $tipoFiltro == 'validados' || $tipoFiltro == 'em_validacao' || $tipoFiltro == 'impedimento_parecerista') {
                    $dp->FecharAnalise = 0;
                } else {
                    $dp->FecharAnalise;
                }

                // encaminhar
                if ($tipoescolha == 2) {
                    // ALTERAR UNIDADE DE AN�LISE ( COORDENADOR DE PARECER )

                    $dadosE = array(
                        'idOrgao' => $orgaoDestino,
                        'DtEnvio' => MinC_Db_Expr::date(),
                        'idAgenteParecerista' => null,
                        'DtDistribuicao' => null,
                        'DtDevolucao' => null,
                        'DtRetorno' => null,
                        'FecharAnalise' => $dp->FecharAnalise,
                        'Observacao' => $observacao,
                        'idUsuario' => $idusuario,
                        'idPRONAC' => $dp->IdPRONAC,
                        'idProduto' => $dp->idProduto,
                        'TipoAnalise' => 3,
                        'stEstado' => 0,
                        'stPrincipal' => $dp->stPrincipal,
                        'stDiligenciado' => null
                    );

                    $where['idDistribuirParecer = ?'] = $idDistribuirParecer;
                    $salvar = $tbDistribuirParecer->alterar(array('stEstado' => 1), $where);

                    $insere = $tbDistribuirParecer->inserir($dadosE);

                    $orgaos = new Orgaos();
                    $orgao = $orgaos->pesquisarNomeOrgao($codOrgao);

                    $projetos->alterarSituacao($dp->IdPRONAC, null, 'B11', 'Encaminhado para <strong>' . $orgao[0]->NomeOrgao . ' para an&aacute;lise e emiss&atilde;o de parecer t&eacute;cnico</strong>.');

                    parent::message("Enviado os Produtos/Projeto para a entidade!", "parecer/gerenciar-parecer/index?tipoFiltro=" . $tipoFiltro, "CONFIRM");
                } else {
                    // DISTRIBUIR OU REDISTRIBUIR ( COORDENADOR DE PARECER )
                    $dadosD = array(
                        'idOrgao' => $dp->idOrgao,
                        'DtEnvio' => $dp->DtEnvioMincVinculada,
                        'idAgenteParecerista' => $idAgenteParecerista,
                        'DtDistribuicao' => MinC_Db_Expr::date(),
                        'DtDevolucao' => null,
                        'DtRetorno' => null,
                        'FecharAnalise' => $dp->FecharAnalise,
                        'Observacao' => $observacao,
                        'idUsuario' => $idusuario,
                        'idPRONAC' => $dp->IdPRONAC,
                        'idProduto' => $dp->idProduto,
                        'TipoAnalise' => 3,
                        'stEstado' => 0,
                        'stPrincipal' => $dp->stPrincipal,
                        'stDiligenciado' => null
                    );

                    $where['idDistribuirParecer = ?'] = $idDistribuirParecer;
                    $salvar = $tbDistribuirParecer->alterar(array('stEstado' => 1), $where);

                    $insere = $tbDistribuirParecer->inserir($dadosD);
                    $projetos->alterarSituacao($dp->IdPRONAC, null, 'B11', 'Produto <strong>' . $dp->Produto . '</strong> encaminhado ao perito para an&aacute;lise t&aacute;cnica e emiss&atilde;o de parecer.');

                    parent::message("Distribui&ccedil;&atilde;o Realizada com sucesso!  ", "parecer/gerenciar-parecer/index?tipoFiltro=" . $tipoFiltro, "CONFIRM");
                }
            }
        } catch (Zend_Exception $ex) {
            parent::message("Error" . $ex->getMessage(), "gerenciarparecer/encaminhar/idpronac/" . $idPronac . "/tipoFiltro/" . $tipoFiltro . "/idproduto/" . $idProduto, "ERROR");
        }
    }


    /*
     * DEPRECATED - tela removida / funcionalidades no módulo parecer
     */
    public function concluirAction()
    {

        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idDistribuirParecer = $this->_request->getParam("idDistribuirParecer");
        $tipoFiltro = $this->_request->getParam("tipoFiltro");

        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $dadosWhere["t.idDistribuirParecer = ?"] = $idDistribuirParecer;

        $buscaDadosProjeto = $tbDistribuirParecer->dadosParaDistribuir($dadosWhere);

        $this->view->dadosProjeto = $buscaDadosProjeto;
        $this->view->idDistribuirParecer = $idDistribuirParecer;
    }

    /*
     * DEPRECATED - movida para módulo parecer
     */
    public function concluiuAction()
    {
        $this->redirector->goToRoute(array('module' => 'parecer', 'controller' => 'gerenciar-parecer', 'action' => 'index'));
    }

    public function visualizaranalisedeconteudoAction()
    {
        $idPronac = $this->_request->getParam("idpronac");
        $idProduto = $this->_request->getParam("idproduto");
        $projetos = new Projetos();
        $where = array('a.idUsuario IS NOT NULL' => '', 'p.IdPRONAC = ?' => $idPronac, 'a.idProduto = ?' => $idProduto);
        $dados = $projetos->vwAnaliseConteudo($where, $order = array(), $tamanho = -1, $inicio = -1);
        $this->view->dados = $dados;
    }

    public function visualizarplanilhadecustosAction()
    {
        $idPronac = $this->_request->getParam("idpronac");
        $idProduto = $this->_request->getParam("idproduto");
        $this->view->idPronac = $idPronac;
    }

    public function planilhaAction()
    {
    }

    public function pareceristaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);
        $post = Zend_Registry::get('get');
        $NomesDAO = new Nomes();
        $retorno = $NomesDAO->buscarPareceristas($post->idOrgao);

        foreach ($retorno as $value) {
            $pareceristas[] = array('id' => $value->id, 'nome' => utf8_encode($value->Nome));
        }

        $this->_helper->json($pareceristas);
    }

    public function infopareceristaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);

        $idAgente = $this->_request->getParam("idAgente");
        $idpronac = $this->_request->getParam("idpronac");
        $idArea = $this->_request->getParam("idArea");
        $idSegmento = $this->_request->getParam("idSegmento");

        $situacao = '1';
        $situacaoTexto = 'Ativo';

        $dataAtual = date("Y/m/d");
        $mesAnoAtual = date("Y/m");
        $diaAtual = date("d");
        $menos10 = ($diaAtual - 10);

        $dataModificada = $mesAnoAtual . '/' . $menos10;

        $ausencia = new Agente_Model_DbTable_TbAusencia();

        $ferias = $ausencia->BuscarAusenciaAtiva($idAgente, null, 2);
        $atestado = $ausencia->BuscarAusenciaAtiva($idAgente, null, 1);

        if (count($ferias) > 0) {
            $situacao = '0';
            $situacaoTexto = 'F&eacute;rias';
        }

        if (count($atestado) > 0) {
            $situacao = '0';
            $situacaoTexto = 'Atestado';
        }

        if ((count($ferias) > 0) && (count($atestado) > 0)) {
            $situacao = '0';
            $situacaoTexto = 'F&eacute;rias e Atestado';
        }

        // CREDENCIAMENTO

        $projetosDAO = new Projetos();
        $credenciamentoDAO = new Agente_Model_DbTable_TbCredenciamentoParecerista();

        $whereProjeto['IdPRONAC = ?'] = $idpronac;
        //$projeto = $projetosDAO->buscar($whereProjeto);
        // se for produto pegar area e segmento do produto

        $whereCredenciamento['idAgente = ?'] = $idAgente;
        $whereCredenciamento['idCodigoArea = ?'] = $idArea;
        $whereCredenciamento['idCodigoSegmento = ?'] = $idSegmento;
        $credenciamento = $credenciamentoDAO->buscar($whereCredenciamento)->count();

        if ($credenciamento == 0) {
            $situacao = '0';
            $situacaoTexto .= '<br /> Parecerista n&atilde;o credenciado na &aacute;rea e segmento do Produto!';
        }
        //$situacaoTexto .= '<br /> Area: '.$projeto[0]->Area.' Segmento: '.$projeto[0]->Segmento.' idAgente: '.$idAgente;


        // An�lises em eberto
        $whereAnalise['distribuirParecer.idAgenteParecerista = ?'] = $idAgente;
        $analiseEmAberto = $projetosDAO->buscaProjetosProdutosParaAnalise($whereAnalise);
        $situacaoTexto .= '<br /> An&aacute;lise em aberto: ' . count($analiseEmAberto);

        $pareceristas[] = array('situacao' => utf8_encode($situacao), 'situacaoTexto' => utf8_encode($situacaoTexto));

        $this->_helper->json($pareceristas);
    }

    private function tipos($array, $labelCampo, $tp, $infoInicial, $infoFinal = '')
    {
        switch ($tp) {
            case 1:
                $array[$labelCampo . ' = ?'] = $infoInicial;
                break;
            case 2:
                $array[$labelCampo . ' < ?'] = $infoInicial;
                $array[$labelCampo . ' > ?'] = $infoFinal;
                break;
            case 3:
                $array[$labelCampo . ' > ?'] = $infoInicial;
                break;
            case 4:
                $array[$labelCampo . ' >= ?'] = $infoInicial;
                break;
            case 5:
                $array[$labelCampo . ' < ?'] = $infoInicial;
                break;
            case 6:
                $array[$labelCampo . ' <= ?'] = $infoInicial;
                break;
        }

        return $array;
    }

    public function resconsolidacaopareceristaAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
//		$idusuario 	= $auth->getIdentity()->usu_codigo;
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessco com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  orgao ativo na sessco
        /******************************************************************/

        $tela = 'resconsolidacaoparecerista';
        $post = Zend_Registry::get('get');
        $where = array();

        if (!empty($post->parecerista)) {
            $where['dp.idAgenteParecerista = ?'] = $post->parecerista;
        }

        $distribuirParecerDAO = new Parecer_Model_DbTable_TbDistribuirParecer();

        $resp = $distribuirParecerDAO->analisePorPareceristaPagamento($where);
        $retorno = array();

        foreach ($resp as $val) {
            $retorno['nmParecerista'] = $val['nmParecerista'];
            $retorno['stPrincipal'] = $val['stPrincipal'];

            if (!empty($val->dtInicioAusencia)) {
                $dataini = date('d/m/Y', strtotime($val->dtInicioAusencia));
                $datafim = date('d/m/Y', strtotime($val->dtFimAusencia));
                $retorno['ferias'] = $dataini . ' a ' . $datafim;
            } else {
                $retorno['ferias'] = 'N&atilde;o agendada';
                $area = $val->Area;
                $segmento = $val->Segmento;
                $nivel = $val->qtPonto;
                $Principal = $val->stPrincipal;

                $retorno['area_segmento_nivel'][$area . '-' . $segmento . '-' . $nivel] = $area . '-' . $segmento . '-' . $nivel;
                $retorno['projetos'][$val['IdPRONAC']]['pronac'] = $val['pronac'];
                $retorno['projetos'][$val['IdPRONAC']]['nmProjeto'] = $val['NomeProjeto'];
                $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['nmProduto'] = $val['nmProduto'];
                $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['dtDistribuicao'] = date('d/m/Y', strtotime($val['DtDistribuicao']));
                $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['nrDias'] = $val['nrDias'];
            }
        }

        $cProduto = 0;
        $contaProjetos = 0;
        $cDistribuicao = 0;

        foreach ($retorno['projetos'] as $projeto) {
            $contaProjetos++;
            $cProduto += count($projeto['produtos']);

            foreach ($projeto['produtos'] as $produto) {
                $cDistribuicao += count($produto['distribuicao']);
            }
        }

        $retorno['qtAnalise'] = $cDistribuicao;
        $this->view->parecerista = $retorno;
    }

    public function visaopareceristaAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
//		$idusuario 	= $auth->getIdentity()->usu_codigo;
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $tela = 'visaoparecerista';
        $post = Zend_Registry::get('get');
        $where = array();

        if (!empty($post->parecerista)) {
            $where['dp.idAgenteParecerista = ?'] = $post->parecerista;
        }

        $distribuirParecerDAO = new Parecer_Model_DbTable_TbDistribuirParecer();

        $resp = $distribuirParecerDAO->analiseParecerista($where);
        $retorno = array();

        foreach ($resp as $val) {
            $retorno['nmParecerista'] = $val['nmParecerista'];
            $retorno['projetos'][$val['IdPRONAC']]['IdPRONAC'] = $val['IdPRONAC'];
            $retorno['projetos'][$val['IdPRONAC']]['pronac'] = $val['pronac'];
            $retorno['projetos'][$val['IdPRONAC']]['nmProjeto'] = $val['NomeProjeto'];
            $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['nmProduto'] = $val['nmProduto'];
            $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['dtDistribuicao'] = date('d/m/Y', strtotime($val['DtDistribuicao']));
            $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['nrDias'] = $val['nrDias'];
        }

        $cProduto = 0;
        $contaProjetos = 0;
        $cDistribuicao = 0;

        foreach ($retorno['projetos'] as $projeto) {
            $contaProjetos++;
            $cProduto += count($projeto['produtos']);
            foreach ($projeto['produtos'] as $produto) {
                $cDistribuicao += count($produto['distribuicao']);
            }
        }

        $this->view->parecerista = $retorno;
    }

    public function consolidacaopareceristaAction()
    {
        $OrgaosDAO = new Orgaos();
        $NomesDAO = new Nomes();
        $AreaDAO = new Area();
        $SegmentoDAO = new Segmento();

        $this->view->Orgaos = $OrgaosDAO->buscar(array('Status = ?' => 0, 'Vinculo = ?' => 1));
        $this->view->Pareceristas = $NomesDAO->buscarPareceristas();
        $this->view->Areas = $AreaDAO->buscar();
        $this->view->Segmento = $SegmentoDAO->buscar(array('stEstado = ?' => 1));
    }

    private function gerarInfoPaginas($tipo, $where = array(), $paginacao = 0)
    {
        $post = Zend_Registry::get('post');
        $retorno = array();
        $ProjetosDAO = new Projetos();
        $distribuirParecerDAO = new Parecer_Model_DbTable_TbDistribuirParecer();

        switch ($tipo) {
            case 'resaguardandoparecer':

                if ($paginacao > 0) {
                    $total = $distribuirParecerDAO->aguardandoparecerTotal($where)->current()->toArray();
                    $limit = $this->paginacao($total["total"], $paginacao);
                    $resp = $distribuirParecerDAO->aguardandoparecer($where, $limit['tamanho'], $limit['inicio']);
                } else {
                    $resp = $distribuirParecerDAO->aguardandoparecer($where);
                }

                $cDistribuicao = 0;

                foreach ($resp as $val) {
                    $retorno[$val['idOrgao']]['nmOrgao'] = $val['nmOrgao'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['nmParecerista'] = $val['nmParecerista'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['idPronac'] = $val['IdPRONAC'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['pronac'] = $val['pronac'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['nmProjeto'] = $val['NomeProjeto'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['nmProduto'] = $val['nmProduto'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['distribuicao'][$cDistribuicao]['dtEnvio'] = date('d/m/Y', strtotime($val['DtEnvio']));
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['distribuicao'][$cDistribuicao]['dtDistribuicao'] = date('d/m/Y', strtotime($val['DtDistribuicao']));
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['distribuicao'][$cDistribuicao]['qtDias'] = $val['nrDias'];

                    $cDistribuicao++;
                }

                break;
            case 'resumo':
                $resp = $distribuirParecerDAO->aguardandoparecerresumo($where);
                $orgAnt = '';

                foreach ($resp as $val) {
                    if ($orgAnt == '' || $orgAnt != $val['idOrgao']) {
                        $retorno[$val['idOrgao']]['qt'] = 0;
                        $orgAnt = $val['idOrgao'];
                    }

                    $retorno[$val['idOrgao']]['nmOrgao'] = $val['nmOrgao'];
                    $retorno[$val['idOrgao']]['qt'] += $val['qt'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['nmParecerista'] = $val['nmParecerista'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['qt'] = $val['qt'];
                }
                break;

            case 'pareceremitido':
                if ($paginacao > 0) {
                    $total = $ProjetosDAO->listaPareceremitidoTotal()->current()->toArray();
                    $limit = $this->paginacao($total["total"], $paginacao);
                    $resp = $ProjetosDAO->listaPareceremitido($limit['tamanho'], $limit['inicio']);
                } else {
                    $resp = $ProjetosDAO->listaPareceremitido();
                }

                foreach ($resp as $val) {
                    $retorno[$val['IdPRONAC']]['idPronac'] = $val['IdPRONAC'];
                    $retorno[$val['IdPRONAC']]['pronac'] = $val['pronac'];
                    $retorno[$val['IdPRONAC']]['nmProjeto'] = $val['NomeProjeto'];
                    $retorno[$val['IdPRONAC']]['nmOrgao'] = $val['OrgaoOrigem'];
                    $retorno[$val['IdPRONAC']]['dtEnvio'] = date('d/m/Y', strtotime($val['DtEnvio']));
                    $retorno[$val['IdPRONAC']]['dtRetorno'] = date('d/m/Y', strtotime($val['DtRetorno']));
                    $retorno[$val['IdPRONAC']]['qtDias'] = $val['Dias'];
                    $retorno[$val['IdPRONAC']]['stParecer'] = 'Emitido';
                    $resp2 = $distribuirParecerDAO->pareceremitido($val['pronac']);
                    foreach ($resp2 as $val2) {
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['nmOrgao'] = $val2['Sigla'];
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['nmProduto'] = $val2['Produto'];
                        if ($val2['stPrincipal']) {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal'] = 'sim';
                        } else {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal'] = '';
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['dtFechamento'] = date('d/m/Y', strtotime($val2['DtDevolucao']));
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['area'] = $val2['Area'];
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['segmento'] = $val2['Segmento'];
                        }
                    }
                }
                break;

            case 'parecerconsolidado':
                if ($paginacao > 0) {
                    $total = $ProjetosDAO->listaParecerconsolidadoTotal()->current()->toArray();
                    $limit = $this->paginacao($total["total"], $paginacao);
                    $resp = $ProjetosDAO->listaParecerconsolidado($limit['tamanho'], $limit['inicio']);
                } else {
                    $resp = $ProjetosDAO->listaParecerconsolidado();
                }

                foreach ($resp as $val) {
                    $retorno[$val['IdPRONAC']]['idPronac'] = $val['IdPRONAC'];
                    $retorno[$val['IdPRONAC']]['pronac'] = $val['pronac'];
                    $retorno[$val['IdPRONAC']]['nmProjeto'] = $val['NomeProjeto'];
                    $retorno[$val['IdPRONAC']]['nmOrgao'] = $val['OrgaoOrigem'];
                    $retorno[$val['IdPRONAC']]['dtEnvio'] = date('d/m/Y', strtotime($val['DtEnvio']));
                    $retorno[$val['IdPRONAC']]['dtRetorno'] = date('d/m/Y', strtotime($val['DtRetorno']));
                    $retorno[$val['IdPRONAC']]['qtDiasRetorno'] = date('d/m/Y', strtotime($val['DtConsolidacaoParecer']));
                    $retorno[$val['IdPRONAC']]['dtConsolidacao'] = $val['Dias'];
                    $retorno[$val['IdPRONAC']]['qtDiasConsolidado'] = $val['QtdeConsolidar'];
                    $retorno[$val['IdPRONAC']]['stParecer'] = 'Consolidado';
                    $resp2 = $distribuirParecerDAO->parecerconsolidado($val['pronac']);
                    foreach ($resp2 as $val2) {
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['nmOrgao'] = $val2['Sigla'];
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['nmProduto'] = $val2['Produto'];
                        if ($val2['stPrincipal']) {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal'] = 'sim';
                        } else {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal'] = '';
                        }
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['dtFechamento'] = date('d/m/Y', strtotime($val2['DtDevolucao']));
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['area'] = $val2['Area'];
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['segmento'] = $val2['Segmento'];
                    }
                }
                break;

            case 'resgeraldeanalise':
                $this->view->titulo = array('PRONAC',
                    'Nome do Projeto',
                    'Produto',
                    'Dt. Primeiro Envio para Vinculada',
                    'Dt. &Uacute;ltimo Envio para Vinculada',
                    'Dt. Distribui&ccedil;&atilde;o para Parecerista',
                    'Parecerista',
                    'Qtde Dias Para Distribuir',
                    'Qtde Dias na caixa do Parecerista',
                    'Total de dias gastos para An&aacute;lise',
                    'Dt. Devolu&ccedil;&atilde;o do Parecerista para o Coordenador',
                    'Qtde Dias para  Parecerista Analisar',
                    'Qtde Dias Aguardando Avalia&ccedil;&atilde;o do Coordenador',
                    'Status da Dilig&ecirc;ncia',
                    '&Oacute;rg&atilde;o',
                    'Periodo de Execu&ccedil;&atilde;o do Projeto',
                    'Dias Vencidos ou a Vencer para Execu&ccedil;&atilde;o do Projeto'
                );
                if ($paginacao > 0) {
                    $total = $ProjetosDAO->geraldeanaliseTotal($where)->current()->toArray();
                    $limit = $this->paginacao($total["total"], $paginacao);
                    $resp = $ProjetosDAO->geraldeanalise($where, $limit['tamanho'], $limit['inicio']);
                } else {
                    $resp = $ProjetosDAO->geraldeanalise($where);
                }

                foreach ($resp as $key => $val) {
                    $retorno[$key]['pronac'] = $val['PRONAC'];
                    $retorno[$key]['idpronac'] = $val['IdPRONAC'];
                    $retorno[$key]['nmProjeto'] = $val['NomeProjeto'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['nmProduto'] = $val['Produto'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtPriEnvVinc'] = $val['DtPrimeiroEnvio'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtUltEnvVinc'] = $val['DtUltimoEnvio'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtDistPar'] = $val['DtDistribuicao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['nmParecerista'] = $val['Parecerista'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasDist'] = $val['QtdeDiasParaDistribuir'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasCaixaPar'] = $val['QtdeDiasComParecerista'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['tDiasAnal'] = $val['QtdeTotalDiasAnalisar'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtDevParCoo'] = $val['dtDevolucao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasParAnal'] = $val['QtdeDiasPareceristaAnalisar'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasAguarAval'] = $val['QtdeDiasDevolvidosCoordenador'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['stDiligencia'] = $this->estadoDiligencia($val);
                    $retorno[$key]['Produtos'][$val['idProduto']]['nmOrgao'] = $val['nmOrgao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['perExecProj'] = $val['PeriodoExecucao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasVencExecProj'] = $val['QtdeDiasVencido'];
                }
                break;
            case 'resconsolidacaoparecerista':
                $zerado = false;
                $resp = $distribuirParecerDAO->analisePorParecerista($where);
                if ($resp->count() > 0) {
                    foreach ($resp as $val) {
                        if (!empty($post->stAnalise)) {
                            if ($post->stAnalise == 1) {
                                if ($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) {
                                    $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                                }
                            }
                            if ($post->stAnalise == 2) {
                                if ($val->DtSolicitacao && $val->DtResposta == null) {
                                    $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                                }
                            }
                            if ($post->stAnalise == 3) {
                                if (!($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) and !($val->DtSolicitacao && $val->DtResposta == null)) {
                                    $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                                }
                            }
                        } else {
                            $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                        }
                    }

                    if (count($retorno) > 0) {
                        $cProduto = 0;
                        $contaProjetos = 0;
                        $cDistribuicao = 0;
                        foreach ($retorno['projetos'] as $projeto) {
                            $contaProjetos++;
                            $cProduto += count($projeto['produtos']);
                            foreach ($projeto['produtos'] as $produto) {
                                $cDistribuicao += count($produto['distribuicao']);
                            }
                        }
                        $retorno['qtAnalise'] = $cDistribuicao;
                    } else {
                        $zerado = true;
                    }
                } else {
                    $zerado = true;
                }

                if ($zerado) {
                    $agentesDAO = new Agente_Model_DbTable_Agentes();

                    $tela = 'resconsolidacaoparecerista2';
                    $where = $this->filtroGeral($tela);
                    $val = $agentesDAO->dadosParecerista($where);
                    $retorno = $this->dadosParecerista($val, $retorno);
                    $retorno['qtAnalise'] = 0;
                }

                break;
        }
        return $retorno;
    }

    private function dadosParecerista($val, $retorno)
    {
        $retorno['nmParecerista'] = $val['nmParecerista'];
        if (!empty($val->dtInicioAusencia)) {
            $dataini = date('d/m/Y', strtotime($val->dtInicioAusencia));
            $datafim = date('d/m/Y', strtotime($val->dtFimAusencia));
            $retorno['ferias'] = $dataini . ' h? ' . $datafim;
        } else {
            $retorno['ferias'] = 'N&atilde;o agendada';
        }
        $area = $val->Area;
        $segmento = $val->Segmento;
        $nivel = $this->nivelParecerista($val->qtPonto);

        $retorno['area_segmento_nivel'][$area . '-' . $segmento . '-' . $nivel]
            = $area . '-' . $segmento . '-' . $nivel;
        return $retorno;
    }

    private function dadosResconsolidacaoparecerista($val, $retorno)
    {
        $retorno = $this->dadosParecerista($val, $retorno);
        $retorno['projetos'][$val['IdPRONAC']]['idPronac'] = $val['IdPRONAC'];
        $retorno['projetos'][$val['IdPRONAC']]['pronac'] = $val['pronac'];
        $retorno['projetos'][$val['IdPRONAC']]['nmProjeto'] = $val['NomeProjeto'];
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['nmProduto'] = $val['nmProduto'];
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['dtDistribuicao'] = date('d/m/Y', strtotime($val['DtDistribuicao']));
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['nrDias'] = $val['nrDias'];
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['diligencia'] = $this->estadoDiligencia($val);
        return $retorno;
    }

    private function estadoDiligencia($val)
    {
        $post = Zend_Registry::get('post');
        if ($post->tipo == 'pdf' or $post->tipo == 'xls') {
            if ($val->DtSolicitacao && $val->DtResposta == null) {
                $diligencia = "<p style='text-align: center;'>Diligenciado</p>";//1
            } elseif ($val->DtSolicitacao && $val->DtResposta != null) {
                $diligencia = "<p style='text-align: center;'>Dilig&ecirc;ncia respondida</p>";//2
            } elseif ($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) {
                $diligencia = "<p style='text-align: center;'>Dilig&ecirc;ncia n&atilde;o respondida</p>";//3
            } else {
                $diligencia = "<p style='text-align: center;'>A diligenciar</p>";//0
            }
        } else {
            if ($val->DtSolicitacao && $val->DtResposta == null) {
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice.png' width='30px'/></p>";//1
            } elseif ($val->DtSolicitacao && $val->DtResposta != null) {
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice2.png' width='30px'/></p>";//2
            } elseif ($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) {
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice3.png' width='30px'/></p>";//3
            } else {
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice1.png' width='30px'/></p>";//0
            }
        }

        return $diligencia;
    }

    private function nivelParecerista($qtPonto)
    {
        if ($qtPonto >= 7 and $qtPonto <= 14) {
            $nivel = 'I';
        } elseif ($qtPonto >= 15 and $qtPonto <= 19) {
            $nivel = 'II';
        } elseif ($qtPonto >= 20 and $qtPonto <= 25) {
            $nivel = 'III';
        }
    }

    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        return $router->assemble($urlOptions, $name, $reset, $encode);
    }

    public function confirmapagamentopareceristaAction()
    {
        $dataAtual = date("Y-m-d h:i:s");
        $idProduto = $this->_request->getParam('confirmPagamento');
        $valor = $this->_request->getParam('valorPagamento');
        $idAgente = $this->_request->getParam('idAgente');

        try {
            for ($i = 0; $i < sizeof($idProduto); $i++) {
                $arrayComprovante = array('idDocumento' => 0,
                    'nrOrdemPagamento' => 0,
                    'dtPagamento' => null
                );

                $TbComprovantePagamento = new TbComprovantePagamento();

                $buscarComprovante = $TbComprovantePagamento->salvarComprovante($arrayComprovante);

                $buscarComprovante = $TbComprovantePagamento->BuscaUltimoComprovante();

                $arrayPagamento = array(
                    'idAgente' => $idAgente[$i],
                    'idProduto' => $idProduto[$i],
                    'siPagamento' => 0,
                    'vlPagamento' => $valor[$i],
                    'idComprovantePagamento' => $buscarComprovante[0]->idComprovantePagamento
                );

                $TbPagamentoParecerista = new TbPagamentoParecerista();

                $insere = $TbPagamentoParecerista->salvarPagamentoParecerista($arrayPagamento);
            }

            parent::message("Pagamentos enviados para aprova&ccedil;&atilde;o do coordenador PRONAC", "gerenciarparecer/enviarpagamento", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao enviar pagamentos: " . $e->getMessage(), "gerenciarparecer/enviarpagamento", "ERROR");
        }
    }

    public function gerarmemorandoAction()
    {
        $dataAtual = date("Y-m-d h:i:s");
        $idProduto = $this->_request->getParam('confirmPagamento');
        $valor = $this->_request->getParam('valorPagamento');
        $idAgente = $this->_request->getParam('idAgente');
        $idComprovantePagamento = $this->_request->getParam('idComprovantePagamento');
        $idPagamentoParecerista = $this->_request->getParam('idPagamentoParecerista');

        // Dados do memorando!
        $nrMemorando = $this->_request->getParam("nrMemorando");
        $nmCoordenador = $this->_request->getParam("nmCoordenador");
        $cargoCoordenador = $this->_request->getParam("cargoCoordenador");
        $nmSecretario = $this->_request->getParam("nmSecretario");
        $cargoSecretario = $this->_request->getParam("cargoSecretario");

        $this->view->nrMemorando = $nrMemorando;
        $this->view->nmCoordenador = $nmCoordenador;
        $this->view->cargoCoordenador = $cargoCoordenador;
        $this->view->nmSecretario = $nmSecretario;
        $this->view->cargoSecretario = $cargoSecretario;

        if (empty($idAgente)) {
            parent::message(
                "Dados obrigat&aacute;rios n&atilde;o informados.",
                "gerenciarparecer/enviarpagamento",
                "ALERT"
            );
        }


        /*** Validacao data pagamento  ************************************************/
        $diaFixo = 20;
        $diaAtual = date("d");
        $mesAtual = date("m");
        $anoAtual = date("Y");

        if (($diaAtual > 10) and ($mesAtual < 12)) {
            $mesAtual = $mesAtual + 1;
        } elseif (($diaAtual > 10) and ($mesAtual == 12)) {
            $anoAtual = $anoAtual + 1;
            $mesAtual = 01;
        }

        $hora = date("m:i:s");

        $dataCerta = $anoAtual . "/" . $mesAtual . "/20 " . $hora;

        $dataCertaM = "20/" . $mesAtual . "/" . $anoAtual;
        /******************************************************************************/

        /* DADOS DO AGENTE ************************************************************/
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $dadosProduto = $tbDistribuirParecer->pagamentoParecerista(null, 137);

        $agentes = new Agente_Model_DbTable_Agentes();

        $whereAgente = array('a.idAgente = ?' => $idAgente[0]);
        $dadosAgente = $agentes->buscarAgenteENome($whereAgente);

        $nomeParecerista = $dadosAgente[0]->Descricao;
        $cpfParecerista = $dadosAgente[0]->CNPJCPF;
        /******************************************************************************/

        $arrayProdutosProjeto = array();

        try {
            $valorTotal = 0;
            for ($i = 0; $i < sizeof($idProduto); $i++) {
                $valorTotal = $valorTotal + $valor[$i];

                $dadosWhere = array('idDistribuirParecer = ?' => $idProduto[$i]);

                $dadosProjeto = $tbDistribuirParecer->BuscarParaMemorando($dadosWhere)->current();

                $arrayBusca = array(
                    'Item' => $i,
                    'PRONAC' => $dadosProjeto['NrProjeto'],
                    'Objeto' => $dadosProjeto['Produto'],
                    'ValorParecer' => $valor[$i],
                    'DataPagamento' => $dataCerta,
                    'Processo' => $dadosProjeto->Processo
                );

                $arrayProdutosProjeto[] = $arrayBusca;


                $TbPagamentoParecerista = new TbPagamentoParecerista();
                $TbComprovantePagamento = new TbComprovantePagamento();

                $arrayComprovante = array('dtPagamento' => $dataCerta, 'nrOrdemPagamento' => $nrMemorando);
                $buscarComprovante = $TbComprovantePagamento->alterarComprovante($arrayComprovante, $idComprovantePagamento[$i]);

                $arrayPagamento = array('siPagamento' => 1);
                $alterar = $TbPagamentoParecerista->alterarPagamento($arrayPagamento, $idPagamentoParecerista[$i]);
            }

            $arrayParecerista = array('Nome' => $nomeParecerista, 'CPF' => Mascara::addMaskCPF($cpfParecerista), 'ValorTotal' => $valorTotal);

            $this->view->dadosParecerista = $arrayParecerista;
            $this->view->dadosProduto = $arrayProdutosProjeto;
            $this->view->dataMemorando = $dataCertaM;
        } catch (Exception $e) {
            parent::message("Erro ao enviar pagamentos: " . $e->getMessage(), "gerenciarparecer/enviarpagamento", "ERROR");
        }
    }
}
