<?php

class Parecer_AnaliseInicialController extends MinC_Controller_Action_Abstract
{
    private $idPronac;
    protected $idUsuario = 0;

    private function validarPerfis()
    {
        $auth = Zend_Auth::getInstance();

        $PermissoesGrupo = array();
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::PARECERISTA;

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->idUsuario = $this->auth->getIdentity()->usu_codigo;
    }

    public function gerenciarAssinaturasAction()
    {
        $this->redirect("/{$this->moduleName}/index");
    }

    public function encaminharAssinaturaAction()
    {
        try {
            $get = $this->getRequest()->getParams();
            $post = $this->getRequest()->getPost();

            $servicoDocumentoAssinatura = new Parecer_AnaliseInicialDocumentoAssinaturaController($this->getRequest()->getPost());

            if (isset($get['IdPRONAC']) && !empty($get['IdPRONAC']) && $get['encaminhar'] == 'true') {
                $servicoDocumentoAssinatura->idPronac = $get['IdPRONAC'];
                $servicoDocumentoAssinatura->encaminharProjetoParaAssinatura();

                $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;
                $idDocumentoAssinatura = $this->getIdDocumentoAssinatura($get['IdPRONAC'], $idTipoDoAtoAdministrativo);

                $this->redirect("/assinatura/index/visualizar-projeto/?idDocumentoAssinatura=" . $idDocumentoAssinatura . "&origin=" . $get['origin']);
            } elseif (isset($post['IdPRONAC']) && is_array($post['IdPRONAC']) && count($post['IdPRONAC']) > 0) {
                // ainda nao implementado o encaminhamento de vários para pareceres
            }
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/analise-inicial/index");
        }
    }

    private function getIdDocumentoAssinatura($idPronac, $idTipoDoAtoAdministrativo)
    {
        $objDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();

        $where = array();
        $where['IdPRONAC = ?'] = $idPronac;
        $where['idTipoDoAtoAdministrativo = ?'] = $idTipoDoAtoAdministrativo;
        $where['stEstado = ?'] = 1;

        $result = $objDocumentoAssinatura->buscar($where);

        return $result[0]['idDocumentoAssinatura'];
    }

    public function indexAction()
    {
        $this->validarPerfis();

        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;
        $this->view->idUsuario = $idusuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $idOrgao = $GrupoAtivo->codOrgao; //  ¿rg¿o ativo na sess¿o

        $UsuarioDAO = new Autenticacao_Model_DbTable_Usuario();
        $agente = $UsuarioDAO->getIdUsuario($idusuario);
        $idAgenteParecerista = $agente['idagente'];
        $this->view->idAgenteParecerista = $idAgenteParecerista;

        $situacao = $this->_request->getParam('situacao');

        if(empty($idAgenteParecerista)) {
            parent::message("Agente n&atilde;o cadastrado", "/default/principal/index", 'ERROR');
        }

        $projeto = new Projetos();
        $resp = $projeto->buscaProjetosProdutosParaAnalise(
            array(
                'distribuirParecer.idAgenteParecerista = ?' => $idAgenteParecerista,
                'distribuirParecer.idOrgao = ?' => $idOrgao,
            )
        );

        $this->idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;
        $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $this->view->quantidadeMinimaAssinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
            $this->idTipoDoAtoAdministrativo,
            $this->auth->getIdentity()->usu_org_max_superior
        );
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;
        $this->view->idPerfilDoAssinante = $GrupoAtivo->codGrupo;

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($resp); // dados a serem paginados
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10); // 10 por p¿gina

        $this->view->qtdRegistro = count($resp);
        $this->view->situacao = $situacao;
        $this->view->buscar = $paginator;
//        $this->view->buscar = $resp;
    }


    public function fecharParecerAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica¿¿o
        $idusuario = $auth->getIdentity()->usu_codigo;
        $dtAtual = Date("Y/m/d h:i:s");

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess¿o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  ¿rg¿o ativo na sess¿o

        $idPronac = $this->_request->getParam("idPronac");
        $idProduto = $this->_request->getParam("idProduto");
        $idDistribuirParecer = $this->_request->getParam("idD");
        $stPrincipal = $this->_request->getParam("stPrincipal");
        $this->view->totaldivulgacao = "true";

        $projetos = new Projetos();
        $orgaos = new Orgaos();

        if (!$projetos->verificarIN2017($idPronac)) {
            $this->validacaoAnteriorIN2017($idPronac);
        }

        if ($_POST || $this->_request->getParam("concluir") == 1) {
            $justificativa = ($this->_request->getParam("concluir") == 1) ? "" : trim(strip_tags($this->_request->getParam("justificativa")));
            $tbDistribuirParecer = new tbDistribuirParecer();
            $dadosWhere["t.idDistribuirParecer = ?"] = $idDistribuirParecer;
            $buscaDadosProjeto = $tbDistribuirParecer->dadosParaDistribuir($dadosWhere);

            try {
                $tbDistribuirParecer->getAdapter()->beginTransaction();
                foreach ($buscaDadosProjeto as $dp):

                $fecharAnalise = 0;

                $dados = array(
                        'idOrgao' => $dp->idOrgao,
                        'DtEnvio' => $dp->DtEnvio,
                        'idAgenteParecerista' => $dp->idAgenteParecerista,
                        'DtDistribuicao' => $dp->DtDistribuicao,
                        'DtDevolucao' => MinC_Db_Expr::date(),
                        'DtRetorno' => null,
                        'FecharAnalise' => $fecharAnalise,
                        'Observacao' => $justificativa,
                        'idUsuario' => $idusuario,
                        'idPRONAC' => $dp->IdPRONAC,
                        'idProduto' => $dp->idProduto,
                        'TipoAnalise' => $dp->TipoAnalise,
                        'stEstado' => 0,
                        'stPrincipal' => $dp->stPrincipal,
                        'stDiligenciado' => null
                    );

                $where['idDistribuirParecer = ?'] = $idDistribuirParecer;

                $salvar = $tbDistribuirParecer->alterar(array('stEstado' => 1), $where);

                $insere = $tbDistribuirParecer->inserir($dados);

                endforeach;

                $tbDistribuirParecer->getAdapter()->commit();

                parent::message("An&aacute;lise conclu&iacute;da com sucesso !", "parecer/analise-inicial", "CONFIRM");
            } catch (Zend_Db_Exception $e) {
                $tbDistribuirParecer->getAdapter()->rollBack();
                parent::message("Error" . $e->getMessage(), "parecer/analise-inicial", "ERROR");
            }
        } else {
            $idPronac = $this->_request->getParam("idPronac");
            $idProduto = $this->_request->getParam("idProduto");
        }

        $projetos = new Projetos();
        /* $dadosProjetoProduto = $projetos->dadosFechar($this->getIdUsuario, $idPronac, $idDistribuirParecer); */
        $dadosProjetoProduto = $projetos->dadosFechar($this->idUsuario, $idPronac, $idDistribuirParecer);
        $this->view->dados = $dadosProjetoProduto;

        $this->view->IN2017 = $projetos->verificarIN2017($idPronac);

        $this->view->idpronac = $idPronac;
    }

    private function validacaoAnteriorIN2017($idPronac)
    {
        // Validacao do 20%
        //valor total do projeto V1

        $planilhaProjeto = new PlanilhaProjeto();
        $valorProjeto = $planilhaProjeto->somarPlanilhaProjeto($idPronac, 109);
        //Validacao dos 20%
        if ($valorProjeto['soma'] > 0 && $stPrincipal == "1") {
            $this->view->totaldivulgacao = $this->validaRegra20Porcento($idPronac);
        }

        // Validacao do 15%
        if ($stPrincipal == "1") { //avaliacao da regra dos 15% so deve ser feita quando a analise for do produto principal
            $Situacao = false;

            $V1 = '';
            $V2 = '';
            $V3 = '';
            $V4 = '';
            $V5 = '';
            $V6 = '';

            $tpPlanilha = 'CO'; // O que eh isso?
            $planilhaProjeto = new PlanilhaProjeto();

            /* V1 */

            $whereTotalV1['PAP.IdPRONAC = ?'] = $idPronac;
            $whereTotalV1['PAP.FonteRecurso = ?'] = 109;
            $whereTotalV1['PAP.idPlanilhaItem <> ? '] = 206;

            $valorProjeto15 = $planilhaProjeto->somaDadosPlanilha($whereTotalV1);

            $V1 = $valorProjeto15['soma'];

            /* V2 */
            $whereTotalV2['PAP.IdPRONAC = ?'] = $idPronac;
            $whereTotalV2['PAP.FonteRecurso = ?'] = 109;
            $whereTotalV2['PAP.idEtapa = ? '] = 4;
            $whereTotalV2['PAP.idProduto = ?'] = 0;
            $whereTotalV2['PAP.idPlanilhaItem not in (?)'] = array(5249, 206, 1238);

            $valoracustosadministrativos = $planilhaProjeto->somaDadosPlanilha($whereTotalV2);
            $V2 = $valoracustosadministrativos['soma'];

            /* 15% */
            if ($V1 > 0 and $valoracustosadministrativos['soma'] < $valorProjeto['soma']) {
                //Calcula os 15% do valor total do projeto V3
                $quinzecentoprojeto = $V1 * 0.15;

                //Subtrai os custos administrativos pelos 15% do projeto (V2 - V3)
                $verificacaonegativo = $valoracustosadministrativos['soma'] - $quinzecentoprojeto;
                //V4
                if ($verificacaonegativo < 0) {
                    //x(0);
                    $this->view->verifica15porcento = 0;
                } else {
                    //V1 - V4 = V5
                    /*V5*/
                    $valorretirar = /*V1*/
                        $V1 - /*V4*/
                        $verificacaonegativo;
                    /*V6*/
                    $quinzecentovalorretirar = /*V5*/
                        $valorretirar * 0.15;
                    //V2 - V6
                    $valorretirarplanilha = $valoracustosadministrativos['soma'] - $quinzecentovalorretirar; //(correcao V2 - V6)
                    $this->view->verifica15porcento = $valorretirarplanilha;
                }
            } else {
                $this->view->verifica15porcento = $valoracustosadministrativos['soma'];
            }
        }
    }
}
