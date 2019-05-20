<?php

class ProjetosGerenciarController extends MinC_Controller_Action_Abstract
{
    private $bln_readequacao = "false";
    protected $idAgente = 0;

    /*     * ************************************************************************************************************************
     * Funcao que inicia todas as funcionalidades da classe
     * *********************************************************************************************************************** */

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // titulo da pagina
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usuario
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuario esteja autenticado
            // verifica as permissaes
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 103; // Coordenador de Analise
            $PermissoesGrupo[] = 97; // Gestor SALIC
            $PermissoesGrupo[] = 151;
            $PermissoesGrupo[] = 148;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo esta no array de permissaes
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &atilde;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgaos e grupos do usuario (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visao
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuario para a visao
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o argao ativo do usuario para a visao

            // pega o idAgente
            $this->idAgente = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->idAgente = ($this->idAgente) ? $this->idAgente["idAgente"] : 0;
        } // fecha if
        else {
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        // init() de GenericControllerNew
        parent::init();

        /**** CODIGO DE READEQUACAO ****/
        $this->view->bln_readequacao = "false";

        $idpronac = null;
        $idpronac = $this->_request->getParam("idpronac");
        /**** FIM - CODIGO DE READEQUACAO ****/
    }

    public function indexAction()
    {
        $ar = new Area();
        $titulacao = new TitulacaoConselheiro();
        $dpc = new DistribuicaoProjetoComissao();
        $tbRetirarDePauta = new tbRetirarDePauta();

        if (isset($_POST['idpronac'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $pr = new Projetos();
            $idpronac = $_POST['idpronac'];
            $buscarprojetos = $pr->buscar(array('IdPRONAC = ?'=>$idpronac))->current();
            $cdArea = $buscarprojetos->Area;
            //$where['TC.cdArea'] = $cdArea;
            $where['TC.stConselheiro'] = 'A';
            $buscarcomponentes = $titulacao->buscarTitulacaoConselheiro($where, array('ar.Descricao ASC','TC.stTitular desc'));
            $componentes = array();
            $a = 0;
            foreach ($buscarcomponentes as $dadoscomponentes) {
                $componentes[$a]['idAgente'] = $dadoscomponentes->idAgente;
                $componentes[$a]['Area'] = utf8_encode($dadoscomponentes->Area);
                $componentes[$a]['nome'] = utf8_encode($dadoscomponentes->nome);
                $componentes[$a]['stTitular'] = $dadoscomponentes->stTitular == 1 ? 'Titutal' : 'Suplente';
                $a++;
            }
            $this->_helper->json($componentes);
        }
        $buscarArea = $ar->buscar();
        $componentes = array();
        $areaComponente = $titulacao->buscarAreaConselheiro();
        $a = 0;

        foreach ($areaComponente as $dadosComponentes) {
            $componentes[$dadosComponentes->stConselheiro][$a]['idAgente'] = $dadosComponentes->idAgente;
            $componentes[$dadosComponentes->stConselheiro][$a]['Nome'] = $dadosComponentes->Nome;
            $componentes[$dadosComponentes->stConselheiro][$a]['CNPJCPF'] = $dadosComponentes->CNPJCPF;
            $componentes[$dadosComponentes->stConselheiro][$a]['Area'] = $dadosComponentes->Area;
            $componentes[$dadosComponentes->stConselheiro][$a]['cdArea'] = $dadosComponentes->cdArea;
            $where['D.idAgente = ? '] = $dadosComponentes->idAgente;
            $where["D.idPRONAC not in(select IdPRONAC from BDCORPORATIVO.scSAC.tbPauta where IdPRONAC = D.idPRONAC AND stAnalise NOT IN ('AS', 'IS', 'AR'))"] = '?';//incluindo condicao (stAnalise) para contemplar projeto readequados, que um dia ja passaram pela pelanaria e que atualemente encontran-se com (stAnalise) de um projeto ja avaliado
            $where['D.stDistribuicao = ?'] = 'A';
            $where['P.Situacao IN (?)'] = array('C10', 'D01', 'C30');
            $projetosdistribuidos = $dpc->buscarProjetosPorComponente($where);
            $b = 0;
            $componentes[$dadosComponentes->stConselheiro][$a]['QtdProjetos'] = $projetosdistribuidos->count();
            $componentes[$dadosComponentes->stConselheiro][$a]['projetos'] = array();
            $qtdRetiradosPauta = 0; // zera os elementos de retirada de pauta
            foreach ($projetosdistribuidos as $projetos) {

                // conta os elementos de retirada de pauta
                $wherePauta['idPronac = ?'] = $projetos->idPRONAC;
                $wherePauta['tpAcao = ?']   = 1; // retirado de pauta
                $wherePauta['stAtivo = ?']  = 1; // ativo
                $projetosRetirarPauta = $tbRetirarDePauta->buscar($wherePauta);
                $qtdRetiradosPauta += $projetosRetirarPauta->count();

                $componentes[$dadosComponentes->stConselheiro][$a]['projetos'][$b]['Dias'] = $projetos->Dias;
                $componentes[$dadosComponentes->stConselheiro][$a]['projetos'][$b]['dtDistribuicao'] = $projetos->dtDistribuicao;
                $componentes[$dadosComponentes->stConselheiro][$a]['projetos'][$b]['idPRONAC'] = $projetos->idPRONAC;
                $componentes[$dadosComponentes->stConselheiro][$a]['projetos'][$b]['PRONAC'] = $projetos->PRONAC;
                $componentes[$dadosComponentes->stConselheiro][$a]['projetos'][$b]['NomeProjeto'] = $projetos->NomeProjeto;
                $componentes[$dadosComponentes->stConselheiro][$a]['projetos'][$b]['Area'] = $projetos->Area;
                $b++;
            }
            $componentes[$dadosComponentes->stConselheiro][$a]['QtdRetirarPauta'] = $qtdRetiradosPauta; // qtd de elementos de retirada de pauta por componente
            $a++;
        }


        $buscarcomponentedesabilitados = $titulacao->BuscarComponenteDesabilidados();
        $buscarArea = $ar->buscar();

        $this->view->componentesdesabilitados = $buscarcomponentedesabilitados;
        $this->view->componenteshabilitados = $componentes;
        $this->view->area = $buscarArea;
    }

    public function encaminharprojetoAction()
    {
        $dpc = new DistribuicaoProjetoComissao();
        $idPronac = $this->_request->getPost('idPRONAC');
        $justificativa = $this->_request->getPost('justificativa');
        $idAgente = $this->_request->getPost('idAgente');
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idResponsavel = $auth->getIdentity()->usu_codigo;

        $dadosAnteriores = array(
            'stDistribuicao' => 'I'
        );
        $where = 'idPRONAC = ' . $idPronac;
        $dadosAnteriores = $dpc->alterar($dadosAnteriores, $where);

        $dados = array(
            'idPronac' => $idPronac,
            'idAgente' => $idAgente,
            'dtDistribuicao' => new Zend_Db_Expr('GETDATE()'),
            'dsJustificativa' => $justificativa,
            'stDistribuicao' => 'A',
            'idResponsavel' => $idResponsavel
        );

        $dados = $dpc->insert($dados);
        if ($dados) {
            parent::message("O Projeto cultural foi encaminhado com sucesso!", "projetosgerenciar/index", "CONFIRM");
        } else {
            parent::message("Erro ao encaminhar Projeto", "projetosgerenciar/index", "ERROR");
        }
    }

    /*     * ************************************************************************************************************************
     * Funcao que desabilita o componente da comissao para receber projetos
     * e faz o rebalanceamento de todos os projetos do mesmo quando ativos
     * *********************************************************************************************************************** */

    public function desabilitarcomponenteAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idresponsavel = $auth->getIdentity()->usu_codigo;
        //Tela de Dados
        $justificativa = $this->_request->getPost('justificativa');
        $idAgente = $this->_request->getPost('idAgente');

        $titulacaoConselheiro = new TitulacaoConselheiro();
        $distribuicaoProjeto = new DistribuicaoProjetoComissao();
        $buscarArea = $titulacaoConselheiro->buscar(array('idAgente = ?' => $idAgente))->current();
        $dadosUpdateSituacao = array(
                'stConselheiro' => 'I'
        );
        $whereUpdateSituacao = "idAgente =" . $idAgente;
        $UpdateSituacao = $titulacaoConselheiro->alterar($dadosUpdateSituacao, $whereUpdateSituacao);

        // Grava na tabela de historico
        $historicoConselheiro = new HistoricoConselheiro();
        $dadosInserir = array(
            'idConselheiro' => $idAgente,
            'dtHistorico' => date('Y-m-d H:i:s'),
            'dsJustificativa' => $justificativa,
            'stConselheiro' => 'I',
            'idResponsavel' => $idresponsavel
        );
        $historicoConselheiro->inserir($dadosInserir);
        $where['D.idAgente = ? '] = $idAgente;
        $where['D.idPRONAC not in(select IdPRONAC from BDCORPORATIVO.scSAC.tbPauta where IdPRONAC = D.idPRONAC)'] = '';
        $dadosdistribuicaoProjeto = $distribuicaoProjeto->buscarProjetosPorComponente($where);
        foreach ($dadosdistribuicaoProjeto as $resu) {
            $componente = $titulacaoConselheiro->buscarcomponentebalanceamento($buscarArea->cdArea);
            if (count($componente) > 0) {
                $componente = $componente->current();
                $dadosupdate = array('idAgente' => $componente->idAgente, "dtDistribuicao" => new Zend_Db_Expr('GETDATE()'));
                $where = "idAgente =" . $idAgente . " AND idPronac=" . $resu->idPRONAC . " and stDistribuicao = 'A' ";
                $dados = $distribuicaoProjeto->alterar($dadosupdate, $where);
            }
        }
        parent::message("O Componente da Comissao foi desabilitado com sucesso!", "projetosgerenciar/index", "CONFIRM");
    }

    /*     * ************************************************************************************************************************
     * Funcao que habilita o componente da comissao para receber projetos
     * *********************************************************************************************************************** */

    public function habilitarcomponenteAction()
    {
        $justificativa = $this->_request->getPost('justificativa');
        $idAgente = $this->_request->getPost('idAgente');
        $titulacaoConselheiro = new TitulacaoConselheiro();
        $hc = new HistoricoConselheiro();
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idresponsavel = $auth->getIdentity()->usu_codigo;

        $dadosUpdateSituacao = array(
            'stConselheiro' => 'A'
        );
        $whereUpdateSituacao = "idAgente =" . $idAgente;
        $UpdateSituacao = $titulacaoConselheiro->alterar($dadosUpdateSituacao, $whereUpdateSituacao);

        $dadosinserirhistorico = array(
            'idConselheiro' => $idAgente,
            'dtHistorico' => new Zend_Db_Expr('GETDATE()'),
            'dsJustificativa' => $justificativa,
            'stConselheiro' => 'A',
            'idResponsavel' => $idresponsavel
        );
        $dados = $hc->inserir($dadosinserirhistorico);
        if ($dados) {
            parent::message("O Componente da Comiss&atilde;o foi habilitado com sucesso!", "projetosgerenciar/index", "CONFIRM");
        } else {
            parent::message("Erro ao habilitar o Componente da Comiss&atilde;o", "projetosgerenciar/index", "ERROR");
        }
    }

    public function formDevolverParaAnaliseAction()
    {
        $this->_helper->layout->disableLayout();
        $idpronac = $this->_request->getParam("idpronac");

        if ($this->bln_readequacao == "true") {
            echo "<br><br><br><center><font color='red'><b>Este Projeto encontra-se em An�lise de Readequa��o.</b></font><center>";
            $this->_helper->viewRenderer->setNoRender(true);
        }

        $arrBusca = array();
        $arrBusca['p.IdPRONAC =?']=$idpronac;
        $arrBusca['t.stPrincipal =?']=1;
        $arrBusca['t.stEstado =?']=0;
        $tbDistParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $rsProduto = $tbDistParecer->buscarProdutos($arrBusca);
        $this->view->dados = $rsProduto;

        $dados = array('dados'=>$rsProduto, 'idpronac'=>$idpronac);

        $this->montaTela("/projetosgerenciar/formdevolverparaanalise.phtml", $dados);
        return;
        //$this->view->dados = GerenciarPareceresDAO::produtoPrincipal($this->_request->getParam("idpronac") );
    }


    public function devolverProjetoParaAnaliseAction()
    {

        /** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao
        $idusuario = $auth->getIdentity()->usu_codigo;
        /******************************************************************/

        $idpronac   = $this->_request->getParam("idpronac");
        $idorgao    = $this->_request->getParam("idorgao");
        $observacao = $this->_request->getParam("observacao");

        try {
            $arrBusca = array();
            $arrBusca['p.IdPRONAC =?']= $idpronac;
            $arrBusca['t.stEstado =?']=0;

            $tbDistParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $rsProdutos = $tbDistParecer->buscarProdutos($arrBusca);

            //VOLTANDO TODOS OS PRODUTOS
            foreach ($rsProdutos as $produto) {

                //$obs = ($produto->stPrincipal == 1) ? $observacao : NULL;
                $rsDistParecer = $tbDistParecer->find($produto->idDistribuirParecer)->current();

                //codigo antigo
                /*$rsDistParecer->FecharAnalise = 0;
                $rsDistParecer->Observacao    = $observacao;
                $rsDistParecer->idUsuario     = $idusuario;
                $rsDistParecer->idOrgao       = $idorgao;*/

                //ALTERA REGISTROS ANTERIORES PARA SE TORNAR HISTORICO
                $rsDistParecer->FecharAnalise = 0; //informacao inserida por solicitacao do gestor para prever esta acao na Trigger de update da tabela tbDistribuirParecer
                $rsDistParecer->stEstado = 1;
                $rsDistParecer->save();

                //GRAVA NOVA DISTRIBUICAO
                $dados = array( 'idPRONAC' => $idpronac,
                        'idProduto'     => $produto->idProduto,
                        'TipoAnalise'   => $produto->TipoAnalise,//0=AnaliseDeConteudo 1=AnaliseDeCusto 2=AnaliseCustoAdministrativo
                        'idOrgao'       => $produto->idOrgao,
                        'DtEnvio'       => date("Y-m-d H:i:s"),
                        'DtDistribuicao'=> null,
                        'DtDevolucao'   => null,
                        'Observacao'    => $observacao,
                        'stEstado'      => 0,
                        'stPrincipal'   => $produto->stPrincipal,
                        'FecharAnalise' => 2,                    //0=AnaliseAberta 1=AnaliseFechada 2=DevolvidoAoParecerista
                        'DtRetorno'     => null,
                        'idUsuario'     => $idusuario,
                    );
                $tbDistParecer->inserir($dados);
            }

            //============================================================================================//
            //======= APAGA/ALTERA REGISTROS DESSA ANALISE REFERENTE AO COMPONENTE DA COMISSAO ============//
            //============================================================================================//

            //INATIVA DISTRIBUICAO FEITA PARA O COMPONENTE
            $tblDistProjComissao = new tbDistribuicaoProjetoComissao();
            $rsDistProjComissao = $tblDistProjComissao->buscar(array('IdPRONAC =?'=>$idpronac), array('dtDistribuicao DESC'))->current();
            if (!empty($rsDistProjComissao)) {
                //codigo antigo
                /*$where = " idPRONAC           = " . $idpronac .
                         " and idAgente       = " . $rsDistProjComissao->idAgente .
                         " and stDistribuicao = '". $rsDistProjComissao->stDistribuicao."'";

                $tblDistProjComissao->apagar($where);*/
                try {
                    $where = "IdPRONAC = {$idpronac}";
                    $tblDistProjComissao->alterar(array('stDistribuicao'=>'I'), $where);
                } catch (Zend_Exception $ex) {
                    parent::message("Erro ao inativar a distribui&ccedil;&atilde;o do Projeto para o Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                }
            }

            //APAGA PLANILHA APROVACAO CRIADA
            $tblPlanilha = new PlanilhaAprovacao();
            $arrBuscaPlanilha = array();
            $arrBuscaPlanilha["IdPRONAC = ? "]   = $idpronac;
            $arrBuscaPlanilha["tpPlanilha = ? "] = 'CO';
            $arrBuscaPlanilha["stAtivo = ? "]    = 'S';
            $rsPlanilha = $tblPlanilha->buscar($arrBuscaPlanilha);
            $arrIdsPlanilha = array();
            foreach ($rsPlanilha as $planilha) {
                $arrIdsPlanilha[]=$planilha->idPlanilhaAprovacao;
            }
            if (count($arrIdsPlanilha)>0) {
                $where = null;
                $where = " idPRONAC           = " . $idpronac .
                        " and idPlanilhaAprovacao IN (" . implode(",", $arrIdsPlanilha) .")";
                try {
                    $tblPlanilha->apagar($where);
                } catch (Zend_Exception $ex) {
                    parent::message("Erro ao apagar a planilha do Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                }
            }

            //APAGA ANALISE DO COMPONENTE
            $tblAnalise = new AnaliseAprovacao();
            $rsAnalise = $tblAnalise->buscar(array('IdPRONAC =?'=>$idpronac));
            $arrIdsAnalises = array();
            foreach ($rsAnalise as $analise) {
                $arrIdsAnalises[]=$analise->idAnaliseAprovacao;
            }
            if (count($arrIdsAnalises)>0) {
                $where = null;
                $where = " IdPRONAC               = " . $idpronac .
                        " and idAnaliseAprovacao IN (" . implode(",", $arrIdsAnalises) . ")";

                try {
                    $tblAnalise->apagar($where);
                } catch (Zend_Exception $ex) {
                    parent::message("Erro ao apagar a an&aacute;lise  do Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                }
            }

            //APAGA PARECER DO COMPONENTE
            $tblParecer = new Parecer_Model_DbTable_Parecer();
            $rsParecer = $tblParecer->buscar(array('idPRONAC =?'=>$idpronac,'idTipoAgente =?'=>6))->current();
            if (!empty($rsParecer)) {
                $idparecer = isset($rsParecer->IdParecer) ? $rsParecer->IdParecer : $rsParecer->idParecer;
                $where = null;
                $where = " idPRONAC      = " . $idpronac .
                        " and idParecer = " . $idparecer;
                try {
                    $tblParecer->apagar($where);
                } catch (Zend_Exception $ex) {
                    parent::message("Erro ao excluir o parecer do Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                }
            }

            //APAGA PARECER do PARECERISTA
            $rsParecer = array();
            $tblParecer = new Parecer_Model_DbTable_Parecer();
            $rsParecer = $tblParecer->buscar(array('IdPRONAC =?'=>$idpronac,'idTipoAgente =?'=>1))->current();
            if (!empty($rsParecer)) {
                //$idParecer = $rsParecer->stAtivo = 1;
                //$rsParecer->save();
                $idparecer = isset($rsParecer->IdParecer) ? $rsParecer->IdParecer : $rsParecer->idParecer;
                $where = null;
                $where = " idPRONAC      = " . $idpronac .
                        " and idParecer = " . $idparecer;
                try {
                    $tblParecer->apagar($where);
                } catch (Zend_Exception $ex) {
                    parent::message("Erro ao excluir o parecer do Parecerista - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                }
            }

            try {
                //ALTERA SITUACAO DO PROJETO
                $tblProjeto = new Projetos();
                $ProvidenciaTomada = 'Projeto devolvido para an�lise t�cnica por solicita��o do Componente.';
                $tblProjeto->alterarSituacao($idpronac, '', 'B11', $ProvidenciaTomada);
            } catch (Zend_Exception $ex) {
                parent::message("Erro ao alterar a situa&ccedil;&atilde;o do Projeto - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
            }

            parent::message("Devolvido com sucesso!", "projetosgerenciar/index/", "CONFIRM");
        } catch (Zend_Exception $ex) {
            parent::message("Erro ao devolver projeto - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
        }
    }



    /**
     * Metodo para efetuar a retirada de pauta
     */
    public function retirarDePautaAction()
    {
        // recebe os dados via post
        $post = Zend_Registry::get('post');
        $idPronac         = $post->idPronacPauta;
        $idRetirarDePauta = $post->idRetirarDePauta;
        $justificativa    = $post->justificativaCoordenador;
        $tpAcao           = $post->tpAcaoPauta;

        try {
            // altera o status da tabela tbRetirarDePauta
            $tbRetirarDePauta = new tbRetirarDePauta();
            $dados = array(
                    'idAgenteAnalise'         => $this->idAgente
                    ,'dtAnalise'              => new Zend_Db_Expr('GETDATE()')
                    ,'dsJustificativaAnalise' => $justificativa
                    ,'tpAcao'                 => $tpAcao // cancelamento da retirada de pauta pelo coordenador de analise, devolvido para vinculada, outros
                    ,'stAtivo'                => 0);
            $where = array('idRetirarDePauta = ?' => $idRetirarDePauta);

            if ($tbRetirarDePauta->alterar($dados, $where)) {

                // in�cio devolver pra vinculada
                if ($tpAcao == 3) {
                    if ($this->bln_readequacao == "true") {
                        throw new Exception("Este Projeto encontra-se em An�lise de Readequa��o!");
                    }
                    $arrBusca = array();
                    $arrBusca['p.IdPRONAC = ?']    = $idPronac;
                    $arrBusca['t.stPrincipal = ?'] = 1;
                    $arrBusca['t.stEstado = ?']    = 0;
                    $tbDistParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
                    $rsProduto = $tbDistParecer->buscarProdutos($arrBusca);
                    if (count($rsProduto) <= 0) {
                        throw new Exception("Dados n&atilde;o encontrados.");
                    } else {
                        // usuario logado
                        $auth = Zend_Auth::getInstance(); // instancia da autenticacao
                        $idusuario  = $auth->getIdentity()->usu_codigo;
                        $idorgao    = $rsProduto[0]->idOrgao;

                        try {
                            $arrBusca = array();
                            $arrBusca['p.IdPRONAC = ?'] = $idPronac;
                            $arrBusca['t.stEstado = ?'] = 0;

                            $tbDistParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
                            $rsProdutos = $tbDistParecer->buscarProdutos($arrBusca);

                            //VOLTANDO TODOS OS PRODUTOS
                            foreach ($rsProdutos as $produto) {
                                $rsDistParecer = $tbDistParecer->find($produto->idDistribuirParecer)->current();

                                //ALTERA REGISTROS ANTERIORES PARA SE TORNAR HISTORICO
                                $rsDistParecer->FecharAnalise  = 0; //informacao inserida por solicitacao do gestor para prever esta acao na Trigger de update da tabela tbDistribuirParecer
                                $rsDistParecer->stEstado = 1;
                                $rsDistParecer->save();

                                //GRAVA NOVA DISTRIBUICAO
                                $dados = array( 'idPRONAC'      => $idPronac,
                                        'idProduto'     => $produto->idProduto,
                                        'TipoAnalise'   => $produto->TipoAnalise, //0=AnaliseDeConteudo 1=AnaliseDeCusto 2=AnaliseCustoAdministrativo
                                        'idOrgao'       => $produto->idOrgao,
                                        'DtEnvio'       => date("Y-m-d H:i:s"),
                                        'DtDistribuicao'=> null,
                                        'DtDevolucao'   => null,
                                        'Observacao'    => $justificativa,
                                        'stEstado'      => 0,
                                        'stPrincipal'   => $produto->stPrincipal,
                                        'FecharAnalise' => 2, //0=AnaliseAberta 1=AnaliseFechada 2=DevolvidoAoParecerista
                                        'DtRetorno'     => null,
                                        'idUsuario'     => $idusuario);

                                $tbDistParecer->inserir($dados);
                            }

                            //============================================================================================//
                            //======= APAGA/ALTERA REGISTROS DESSA ANALISE REFERENTE AO COMPONENTE DA COMISSAO ============//
                            //============================================================================================//

                            //INATIVA DISTRIBUICAO FEITA PARA O COMPONENTE
                            $tblDistProjComissao = new tbDistribuicaoProjetoComissao();
                            $rsDistProjComissao = $tblDistProjComissao->buscar(array('IdPRONAC =?' => $idPronac), array('dtDistribuicao DESC'))->current();
                            if (!empty($rsDistProjComissao)) {
                                try {
                                    $where = "IdPRONAC = {$idPronac}";
                                    $tblDistProjComissao->alterar(array('stDistribuicao' => 'I'), $where);
                                } catch (Zend_Exception $ex) {
                                    parent::message("Erro ao inativar a distribui&ccedil;&atilde;o do Projeto para o Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                                }
                            }

                            //APAGA PLANILHA APROVACAO CRIADA
                            $tblPlanilha = new PlanilhaAprovacao();
                            $arrBuscaPlanilha = array();
                            $arrBuscaPlanilha["IdPRONAC = ? "]   = $idPronac;
                            $arrBuscaPlanilha["tpPlanilha = ? "] = 'CO';
                            $arrBuscaPlanilha["stAtivo = ? "]    = 'S';
                            $rsPlanilha = $tblPlanilha->buscar($arrBuscaPlanilha);
                            $arrIdsPlanilha = array();
                            foreach ($rsPlanilha as $planilha) {
                                $arrIdsPlanilha[] = $planilha->idPlanilhaAprovacao;
                            }
                            if (count($arrIdsPlanilha)>0) {
                                $where = null;
                                $where = " idPRONAC           = " . $idPronac .
                                        " and idPlanilhaAprovacao IN (" . implode(",", $arrIdsPlanilha) .")";
                                try {
                                    $tblPlanilha->apagar($where);
                                } catch (Zend_Exception $ex) {
                                    parent::message("Erro ao apagar a planilha do Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                                }
                            }

                            //APAGA ANALISE DO COMPONENTE
                            $tblAnalise = new AnaliseAprovacao();
                            $rsAnalise = $tblAnalise->buscar(array('IdPRONAC = ?' => $idPronac));
                            $arrIdsAnalises = array();
                            foreach ($rsAnalise as $analise) {
                                $arrIdsAnalises[] = $analise->idAnaliseAprovacao;
                            }
                            if (count($arrIdsAnalises)>0) {
                                $where = null;
                                $where = " IdPRONAC               = " . $idPronac .
                                        " and idAnaliseAprovacao IN (" . implode(",", $arrIdsAnalises) . ")";

                                try {
                                    $tblAnalise->apagar($where);
                                } catch (Zend_Exception $ex) {
                                    parent::message("Erro ao apagar a an&aacute;lise  do Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                                }
                            }

                            //APAGA PARECER DO COMPONENTE
                            $tblParecer = new Parecer_Model_DbTable_Parecer();
                            $rsParecer = $tblParecer->buscar(array('idPRONAC = ?' => $idPronac, 'idTipoAgente = ?' => 6))->current();
                            if (!empty($rsParecer)) {
                                $idparecer = isset($rsParecer->IdParecer) ? $rsParecer->IdParecer : $rsParecer->idParecer;
                                $where = null;
                                $where = " idPRONAC      = " . $idPronac .
                                        " and idParecer = " . $idparecer;

                                try {
                                    $tblParecer->apagar($where);
                                } catch (Zend_Exception $ex) {
                                    parent::message("Erro ao excluir o parecer do Componente - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                                }
                            }

                            //APAGA PARECER do PARECERISTA
                            $rsParecer = array();
                            $tblParecer = new Parecer_Model_DbTable_Parecer();
                            $rsParecer = $tblParecer->buscar(array('IdPRONAC =?' => $idPronac, 'idTipoAgente = ?' => 1))->current();
                            if (!empty($rsParecer)) {
                                $idparecer = isset($rsParecer->IdParecer) ? $rsParecer->IdParecer : $rsParecer->idParecer;
                                $where = null;
                                $where = " idPRONAC      = " . $idPronac .
                                        " and idParecer = " . $idparecer;

                                try {
                                    $tblParecer->apagar($where);
                                } catch (Zend_Exception $ex) {
                                    parent::message("Erro ao excluir o parecer do Parecerista - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                                }
                            }

                            try {
                                //ALTERA SITUACAO DO PROJETO
                                $tblProjeto = new Projetos();
                                $ProvidenciaTomada = 'Projeto devolvido para an�lise t�cnica por solicita��o do Componente.';
                                $tblProjeto->alterarSituacao($idPronac, '', 'B11', $ProvidenciaTomada);
                            } catch (Zend_Exception $ex) {
                                parent::message("Erro ao alterar a situa&ccedil;&atilde;o do Projeto - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                            }

                            parent::message("Devolvido com sucesso!", "projetosgerenciar/index/", "CONFIRM");
                        } catch (Zend_Exception $ex) {
                            parent::message("Erro ao devolver projeto - ".$ex->getMessage(), "projetosgerenciar/index", "ERROR");
                        }
                    }
                } // fecha if ($tpAcao == 3)
                // fim devolver pra vinculada

                parent::message("Solicita&ccedil;&atilde;o enviada com sucesso!", "projetosgerenciar/index", "CONFIRM");
            } else {
                throw new Exception("Erro ao enviar solicita&ccedil;&atilde;o");
            }
        } catch (Exception $e) {
            parent::message($e->getMessage(), "projetosgerenciar/index", "ERROR");
        }
    } // fecha metodo retirarDePautaAction()
}
