<?php

class Parecer_GerenciarParecerController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 10;

    const ID_TIPO_AGENTE_PARCERISTA = 1;

    private function validarPerfis()
    {
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::SUPERINTENDENTE_DE_VINCULADA;

        isset($this->auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;

        $this->validarPerfis();
    }

    public function gerenciarAssinaturasAction()
    {
        switch ($this->grupoAtivo->codGrupo) {
        case Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA:
            $this->redirect("/{$this->moduleName}/gerenciar-parecer/finalizar-parecer");
            break;
        case Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER:
            $this->redirect("/{$this->moduleName}/gerenciar-parecer/index?tipoFiltro=validados");
            break;
        }
    }

    public function encaminharAssinaturaAction()
    {
    }

    public function indexAction()
    {
        $idusuario = $this->auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $this->view->quantidadeMinimaAssinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
            $this->idTipoDoAtoAdministrativo,
            $this->auth->getIdentity()->usu_org_max_superior
        );
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;
        $this->view->idPerfilDoAssinante = $GrupoAtivo->codGrupo;

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }

        $ordem = "ASC";
        $novaOrdem = "ASC";
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            }
        }

        $campo = null;
        $order = array('DtEnvioMincVinculada', 'NomeProjeto', 'stPrincipal desc');
        $ordenacao = null;
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
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

        if (!$this->_request->getParam("tipoFiltro")) {
            $tipoFiltro = 'aguardando_distribuicao';
        } else {
            $tipoFiltro = $this->_request->getParam("tipoFiltro");
            if (strpos($tipoFiltro, '/') > -1) {
                $tipoFiltro = explode('/', $tipoFiltro)[0];
            }
        }

        if ($tipoFiltro == 'analisado_superintendencia') {
            $order = array('NomeProjeto', 'stPrincipal desc');
        }

        $this->view->tipoFiltro = $tipoFiltro;

        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();

        $total = $tbDistribuirParecer->painelAnaliseTecnica($where, $order, null, null, true, $tipoFiltro);
        $fim = $inicio + $this->intTamPag;
        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        $busca = $tbDistribuirParecer->painelAnaliseTecnica($where, $order, $tamanho, $inicio, false, $tipoFiltro);

        $checarValidacaoSecundarios = array();
        foreach ($busca as $chave => $item) {
            if ($item->stPrincipal == 1) {
                $checarValidacaoSecundarios[$item->IdPRONAC] = $tbDistribuirParecer->checarValidacaoProdutosSecundarios($item->IdPRONAC);
            }
        }
        $this->view->checarValidacaoSecundarios = $checarValidacaoSecundarios;
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;

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


    public function concluiuAction()
    {
        $idUsuario = $this->auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;
        $codGrupo = $GrupoAtivo->codGrupo;

        $idDistribuirParecer = $this->_request->getParam("idDistribuirParecer");
        $idPronac = $this->_request->getParam("idpronac");
        $observacao = $this->_request->getParam("obs");
        $tipoFiltro = $this->_request->getParam("tipoFiltro");

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $projetos = new Projetos();
        $orgaos = new Orgaos();

        try {
            $db->beginTransaction();

            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $dadosWhere["t.idDistribuirParecer = ?"] = $idDistribuirParecer;

            $buscaDadosProjeto = $tbDistribuirParecer->dadosParaDistribuir($dadosWhere);

            foreach ($buscaDadosProjeto as $dp) {
                $idOrgao = $dp->idOrgao;

                if ($tipoFiltro == 'em_validacao') {
                    //colocar IN2017 fecharanalise = 1 , caso caontrario 3
                    $fecharAnalise = 1;
                    //colocar IN2017 fecharanalise = 1 , caso caontrario 3
                    if ($projetos->verificarIN2017($idPronac)) {
                        $fecharAnalise = 1;
                    } else {
                        $fecharAnalise = 3;
                    }

                    if ($orgaos->isVinculadaIphan($dp->idOrgao)) {
                        if ($codGrupo == Autenticacao_Model_Grupos::SUPERINTENDENTE_DE_VINCULADA) {
                            $idOrgao = Orgaos::ORGAO_IPHAN_PRONAC;
                        }
                        $fecharAnalise = 3;
                    }
                } elseif ($tipoFiltro == 'devolvida') {
                    $fecharAnalise = 0;
                } else {
                    $fecharAnalise = $dp->FecharAnalise;
                }

                //colocar IN2017 fecharanalise = 1 , caso caontrario 3
                if ($tipoFiltro == 'validados') {
                    if (!$projetos->verificarIN2017($idPronac) && !$orgaos->isVinculadaIphan($dp->idOrgao)) {
                        $fecharAnalise    = 1;
                    } else {
                        $fecharAnalise    = 3;
                    }
                }

                $dados = array(
                    'DtEnvio' => $dp->DtEnvio,
                    'idAgenteParecerista' => $dp->idAgenteParecerista,
                    'DtDistribuicao' => $dp->DtDistribuicao,
                    'DtDevolucao' => $dp->DtDevolucao,
                    'DtRetorno' => MinC_Db_Expr::date(),
                    'Observacao' => $observacao,
                    'idUsuario' => $idUsuario,
                    'FecharAnalise' => $fecharAnalise,
                    'idOrgao' => $idOrgao,
                    'idPRONAC' => $dp->IdPRONAC,
                    'idProduto' => $dp->idProduto,
                    'TipoAnalise' => $dp->TipoAnalise,
                    'stEstado' => 0,
                    'stPrincipal' => $dp->stPrincipal,
                    'stDiligenciado' => null
                );

                $whereD['idDistribuirParecer = ?'] = $idDistribuirParecer;
                $salvar = $tbDistribuirParecer->alterar(array('stEstado' => 1), $whereD);
                $insere = $tbDistribuirParecer->inserir($dados);
            }

            /** Grava o Parecer nas Tabelas tbPlanilhaProjeto e Parecer e altera a situa��o do Projeto para  ***************/
            $projeto = new Projetos();
            $wherePro['IdPRONAC = ?'] = $idPronac;
            $buscaDadosdoProjeto = $projeto->buscar($wherePro);

            // se for produto principal
            if ($buscaDadosProjeto[0]->stPrincipal == 1) {

                //fluxo in2013
                if (!$projetos->verificarIN2017($idPronac)) {
                    $inabilitadoDAO = new Inabilitado();
                    $buscaInabilitado = $inabilitadoDAO->BuscarInabilitado($buscaDadosdoProjeto[0]->CgcCpf, $buscaDadosdoProjeto[0]->AnoProjeto, $buscaDadosdoProjeto[0]->Sequencial);

                    // nao est inabilitado
                    if (count($buscaInabilitado == 0)) {
                        // dentro das unidades abaixo
                        if (in_array($dp->idOrgao, array(91,92,93,94,95,160,171,335))) {
                            if ($tipoFiltro == 'validados' || $tipoFiltro == 'devolvida') {
                                $projeto->alterarSituacao($idPronac, null, 'C20', 'Anlise t&eacute;cnica conclu&iacute;da');
                            } elseif ($tipoFiltro == 'em_validacao') {
                                $projeto->alterarSituacao($idPronac, null, 'B11', 'Aguardando valida&ccedil;&atilde;o do parecer t&eacute;cnico');
                            }
                        } else {
                            // fora das unidades acima
                            $projeto->alterarSituacao($idPronac, null, 'B11', 'Aguardando valida&ccedil;&atilde;o do parecer t&eacute;cnico');
                        }
                    } else {
                        // inabilitado
                        $projeto->alterarSituacao($idPronac, null, 'C09', 'Projeto fora da pauta de reuni&atilde;o da CNIC porque o proponente est&aacute; inabilitado no Minist&eacute;rio da Cidadania.');
                    }
                }
                //fluxo in2013

                /****************************************************************************************************************/
                $parecerDAO = new Parecer_Model_DbTable_Parecer();
                $whereParecer['idPRONAC = ?'] = $idPronac;
                $buscarParecer = $parecerDAO->buscar($whereParecer);

                $analiseDeConteudoDAO = new Parecer_Model_DbTable_TbAnaliseDeConteudo();
                $whereADC['idPRONAC = ?'] = $idPronac;
                $dadosADC = array('idParecer' => $buscarParecer[0]->IdParecer);
                $alteraADC = $analiseDeConteudoDAO->alterar($dadosADC, $whereADC);

                $planilhaProjetoDAO = new PlanilhaProjeto();
                $wherePP['idPRONAC = ?'] = $idPronac;
                $dadosPP = array('idParecer' => $buscarParecer[0]->IdParecer);
                $alteraPP = $planilhaProjetoDAO->alterar($dadosPP, $wherePP);
                /****************************************************************************************************************/
            }
            $db->commit();
            parent::message("Conclu&iacute;do com sucesso!", "parecer/gerenciar-parecer?tipoFiltro=" . $tipoFiltro, "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao concluir " . $ex->getMessage(), "gerenciarparecer/concluir/idDistribuirParecer/" . $idDistribuirParecer . "/tipoFiltro/" . $tipoFiltro, "ERROR");
        }
    }

    public function finalizarParecerAction()
    {
        $idusuario = $this->auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $this->view->quantidadeMinimaAssinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
            $this->idTipoDoAtoAdministrativo,
            $this->auth->getIdentity()->usu_org_max_superior
        );
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;
        $this->view->idPerfilDoAssinante = $GrupoAtivo->codGrupo;

        $order = array();
        $where = array();

        if (Orgaos::isVinculadaIphan($codOrgao)) {
            $tipoFiltro  = 'superintendente_vinculadas';
        } else {
            $tipoFiltro  = 'presidente_vinculadas';
        }
        $where["idOrgao = ?"] = $codOrgao;

        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        $busca = $tbDistribuirParecer->painelAnaliseTecnica($where, $order, null, null, false, $tipoFiltro);

        $checarValidacaoSecundarios = array();
        foreach ($busca as $chave => $item) {
            if ($item->stPrincipal == 1) {
                $checarValidacaoSecundarios[$item->IdPRONAC] = $tbDistribuirParecer->checarValidacaoProdutosSecundarios($item->IdPRONAC);
            }
        }
        $this->view->checarValidacaoSecundarios = $checarValidacaoSecundarios;
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;

        $this->view->qtdDocumentos = count($busca);
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;
    }


    /*
     * Finalização de presidente de vinculada
     */
    public function finalizouParecerAction()
    {
        $idDistribuirParecer = $this->_request->getParam("idDistribuirParecer");
        $idPronac = $this->_request->getParam("idpronac");
        $idUsuario = $this->auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $projeto = new Projetos();
        $orgaos = new Orgaos();

        try {
            $db->beginTransaction();

            if (!$orgaos->isVinculadaIphan($codOrgao)) {
                $this->fecharAssinatura($idPronac);
            }

            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $dadosWhere["t.idDistribuirParecer = ?"] = $idDistribuirParecer;

            $buscaDadosProjeto = $tbDistribuirParecer->dadosParaDistribuir($dadosWhere);

            foreach ($buscaDadosProjeto as $dp) {
                if ($orgaos->isVinculadaIphan($dp->idOrgao)) {
                    $idOrgao = Orgaos::ORGAO_IPHAN_PRONAC;
                } else {
                    $idOrgao = $dp->idOrgao;
                }
                $fecharAnalise = 1;

                $dados = array(
                    'DtEnvio' => $dp->DtEnvio,
                    'idAgenteParecerista' => $dp->idAgenteParecerista,
                    'DtDistribuicao' => $dp->DtDistribuicao,
                    'DtDevolucao' => $dp->DtDevolucao,
                    'DtRetorno' => MinC_Db_Expr::date(),
                    'Observacao' => "",
                    'idUsuario' => $idUsuario,
                    'FecharAnalise' => $fecharAnalise,
                    'idOrgao' => $idOrgao,
                    'idPRONAC' => $dp->IdPRONAC,
                    'idProduto' => $dp->idProduto,
                    'TipoAnalise' => $dp->TipoAnalise,
                    'stEstado' => 0,
                    'stPrincipal' => $dp->stPrincipal,
                    'stDiligenciado' => null
                );

                $whereD['idDistribuirParecer = ?'] = $idDistribuirParecer;
                $salvar = $tbDistribuirParecer->alterar(array('stEstado' => 1), $whereD);
                $insere = $tbDistribuirParecer->inserir($dados);
            }

            $wherePro['IdPRONAC = ?'] = $idPronac;
            $buscaDadosdoProjeto = $projeto->buscar($wherePro);

            /// ALTERAR SITUACAO
            $inabilitadoDAO = new Inabilitado();
            $buscaInabilitado = $inabilitadoDAO->BuscarInabilitado($buscaDadosdoProjeto[0]->CgcCpf, $buscaDadosdoProjeto[0]->AnoProjeto, $buscaDadosdoProjeto[0]->Sequencial);

            if (count($buscaInabilitado == 0)) {
                if (!$orgaos->isVinculadaIphan($dp->idOrgao)) {
                    // somente presidente
                    $projeto->alterarSituacao($idPronac, null, 'C20', 'An&aacute;lise t&eacute;cnica conclu&iacute;da');
                } else {
                    $projeto->alterarSituacao($idPronac, null, 'B11', 'Aguardando valida&ccedil;&atilde;o do parecer t&eacute;cnico');
                }
            } else {
                // inabilitado
                $projeto->alterarSituacao($idPronac, null, 'C09', 'Projeto fora da pauta de reuni&atilde;o da CNIC porque o proponente est&aacute; inabilitado no Minist&eacute;rio da Cidadania.');
            }

            $db->commit();
            parent::message("Conclu&iacute;do com sucesso!", "parecer/gerenciar-parecer/finalizar-parecer", "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao concluir " . $ex->getMessage(), "parecer/gerenciar-parecer/finalizar-parecer", "ERROR");
        }
    }

    private function fecharAssinatura($idPronac)
    {
        try {
            $parecer = new Parecer_Model_DbTable_Parecer();
            $idAtoAdministrativo = $parecer->getIdAtoAdministrativoParecerTecnico($idPronac, self::ID_TIPO_AGENTE_PARCERISTA)->current()['idParecer'];

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $data = array(
                'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA
            );
            $where = array(
                'IdPRONAC = ?' => $idPronac,
                'idTipoDoAtoAdministrativo = ?' => $this->idTipoDoAtoAdministrativo,
                'idAtoDeGestao = ?' => $idAtoAdministrativo,
                'cdSituacao = ?' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                'stEstado = ?' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
            );
            $objModelDocumentoAssinatura->update($data, $where);
        } catch (Zend_Exception $ex) {
            parent::message("Erro ao concluir " . $ex->getMessage(), "parecer/gerenciar-parecer/finalizar-parecer", "ERROR");
        }
    }
}
