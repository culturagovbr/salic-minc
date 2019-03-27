<?php
class AnalisarexecucaofisicatecnicoController extends MinC_Controller_Action_Abstract
{
    private $getIdAgente  = 0;
    private $getIdGrupo = 0;
    private $getIdOrgao = 0;
    private $intTamPag = 10;
    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 129;
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);

        $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usu�rio
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->getIdAgente = $idagente['idAgente'];
        $this->getIdGrupo = $GrupoAtivo->codGrupo;
        $this->getIdOrgao = $GrupoAtivo->codOrgao;
        
        parent::init();
    }

    public function indexAction()
    {
        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $codPerfil          = $GrupoAtivo->codGrupo; //  �rg�o ativo na sess�o
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /******************************************************************/

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
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


        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array('a.NomeProjeto','a.nrComprovanteTrimestral');
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;


        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['Pronac = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $total = $vw->listaRelatorios($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $vw->listaRelatorios($where, $order, $tamanho, $inicio);

        $paginacao = array(
                "pag"=>$pag,
                "qtde"=>$this->intTamPag,
                "campo"=>$campo,
                "ordem"=>$ordem,
                "ordenacao"=>$ordenacao,
                "novaOrdem"=>$novaOrdem,
                "total"=>$total,
                "inicio"=>($inicio+1),
                "fim"=>$fim,
                "totalPag"=>$totalPag,
                "Itenspag"=>$this->intTamPag,
                "tamanho"=>$tamanho
         );

        if (!$campo) {
            $campo = 2; //Se o campo para ordenar for vazio ele ordena pelo campo 2 (pronac)
        }


        $this->view->campo = $campo;
        $this->view->pag = $pag;
        $this->view->novaOrdem = $novaOrdem;
        $this->view->paginacao     = $paginacao;
        $this->view->qtdRelatorios = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function parecerTecnicoAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idPronac = ?'] = $idpronac;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        $this->view->DadosRelatorio = $DadosRelatorio;
        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbComprovante = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $dadosParecer = $tbComprovante->buscarComprovantes(array('IdPRONAC=?'=>$idpronac,'nrComprovanteTrimestral=?'=>$idrelatorio,'idTecnicoAvaliador=?'=>$idusuario));
        $this->view->DadosParecer = $dadosParecer;
    }

    public function etapasDeTrabalhoAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRel = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac=?'=>$idpronac,'nrComprovanteTrimestral=?'=>$idrelatorio,'siComprovanteTrimestral in (?)'=>array(3,4)));
        $this->view->DadosRelatorio = $DadosRel;
        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
    }

    public function localDeRealizacaoAction()
    {
        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
        
        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;
    }

    public function planoDeDivulgacaoAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;
    }

    public function planoDeDistribuicaoAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;
    }

    public function metasComprovadasAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        //****** Dados da Comprova��o de Metas *****//
        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;
    }

    public function itensComprovadosAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;
    }

    public function comprovantesDeExecucaoAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $idrelatorio;

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $this->view->idPronac = $idpronac;
        $this->view->idRelatorio = $idrelatorio;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
    }

    public function avaliarRelatorioTrimestralAction()
    {

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        /******************************************************************/

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;
        $where['a.siComprovanteTrimestral in (?)'] = array(3,4);
        $where['a.nrComprovanteTrimestral = ?'] = $_POST['nrRelatorio'];

        $vw = new vwPainelTecnicoAvaliacaoTrimestral();
        $DadosRelatorio = $vw->listaRelatorios($where, array(), null, null, false);

        if (count($DadosRelatorio)==0) {
            parent::message('Relat&oacute;rio n&atilde;o encontrado!', "analisarexecucaofisicatecnico", "ALERT");
        }

        $tbComprovante = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $dadosRel = $tbComprovante->buscarComprovantes(array('IdPRONAC=?'=>$idpronac,'nrComprovanteTrimestral=?'=>$DadosRelatorio[0]->nrComprovanteTrimestral,'idTecnicoAvaliador=?'=>$idusuario));
        
        $siComprovante = 4;
        $msg = 'Relat&oacute;rio salvo com sucesso!';
        $controller = "analisarexecucaofisicatecnico/parecer-tecnico?idpronac=".$idpronac."&relatorio=".$DadosRelatorio[0]->nrComprovanteTrimestral;
        if (isset($_POST['finalizar']) && !empty($_POST['finalizar'])) {
            $siComprovante = 5;
            $msg = 'Relat&oacute;rio finalizado com sucesso!';
            $controller = 'analisarexecucaofisicatecnico/';
        }

        $dados = array(
            'dsParecerTecnico' => $_POST['parecerTecnico'],
            'dsRecomendacao' => $_POST['recomendacoes'],
            'siComprovanteTrimestral' => $siComprovante
        );
        $whereFinal = 'idComprovanteTrimestral = '.$dadosRel->idComprovanteTrimestral;
        $resultado = $tbComprovante->alterar($dados, $whereFinal);

        if ($resultado) {
            parent::message($msg, $controller, "CONFIRM");
        } else {
            parent::message('N&atilde;o foi poss&iacute;vel salvar o relat&oacute;rio!', "analisarexecucaofisicatecnico", "ERROR");
        }
    }

    public function devolverRelatorioAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        /******************************************************************/

        $post = Zend_Registry::get('post');
        $idPronac = (int) $post->pronac;
        $nrRelatorio = (int) $post->nr;

        $dados = array();
        $dados['idTecnicoAvaliador'] = null;
        $dados['siComprovanteTrimestral'] = 2;
        $where = "IdPRONAC = $idPronac AND nrComprovanteTrimestral = $nrRelatorio AND idTecnicoAvaliador = $idusuario";

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $return = $tbComprovanteTrimestral->update($dados, $where);

        if ($return) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
