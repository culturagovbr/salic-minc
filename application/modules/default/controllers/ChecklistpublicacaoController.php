<?php
class ChecklistPublicacaoController extends MinC_Controller_Action_Abstract
{
    private $getIdUsuario = 0;
    private $getIdAgenteLogado = 0;
    private $codGrupo = null;
    private $codOrgao = null;
    private $tipoAnalise = null;
    private $blnCoordenador = "false";
    private $intTamPag = 10;
    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // titulo da pagina
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario = new UsuarioDAO(); // objeto usuario
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuario esteja autenticado
            // verifica as permissoes
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 103;
            $PermissoesGrupo[] = 127;
            $PermissoesGrupo[] = 121;
            $PermissoesGrupo[] = 122;
            $PermissoesGrupo[] = 123;
            $PermissoesGrupo[] = 110;
            $PermissoesGrupo[] = 142;
            $PermissoesGrupo[] = 151;
            $PermissoesGrupo[] = 148;

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo esta no array de permissoes
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            if ($GrupoAtivo->codGrupo == 103 || $GrupoAtivo->codGrupo == 122 || $GrupoAtivo->codGrupo == 127  || $GrupoAtivo->codGrupo == 123) {
                $this->view->coordenador = "true";
            }

            // pega as unidades autorizadas, orgaos e grupos do usuario (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuario para a visao
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o orgao ativo do usuario para a visao

            $this->getIdUsuario = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;

            $tblAgente = new Agente_Model_DbTable_Agentes();
            $rsAgente = $tblAgente->buscar(array('CNPJCPF = ?'=>$auth->getIdentity()->usu_identificacao))->current();
            if (!empty($rsAgente)) {
                $this->getIdAgenteLogado = $rsAgente->idAgente;
            }
        } // fecha if
        else { // caso o usu�rio n�o esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew

        $this->codGrupo       = $_SESSION['GrupoAtivo']['codGrupo'];
        $this->view->codGrupo = $_SESSION['GrupoAtivo']['codGrupo'];
        $this->codOrgao       = $_SESSION['GrupoAtivo']['codOrgao'];
        $this->view->codOrgao = $_SESSION['GrupoAtivo']['codOrgao'];

        if ($this->codGrupo == 103 || $this->codGrupo == 110  || $this->codGrupo == 127) { //103=Coord. de Analise  110=Tecnico de Analise   127=Coord. Geral de Analise
            $this->view->tipoAnalise = "inicial";
            $this->tipoAnalise = "inicial";
            if ($this->codGrupo == 103 || $this->codGrupo == 127) {
                $this->blnCoordenador = "true";
                $this->view->blnCoordenador = "true";
            }
        } elseif ($this->codGrupo == 122 || $this->codGrupo == 121  || $this->codGrupo == 123) { //121=Cood. de Acompanhamento  121=Tecnico Acompanhamento  123=Cood. Geral de Acompanhamento
            $this->view->tipoAnalise = "readequados";
            $this->tipoAnalise = "readequados";
            if ($this->codGrupo == 121 || $this->codGrupo == 123) {
                $this->view->blnCoordenador = "true";
                $this->view->blnCoordenador = "true";
            }
        }

        $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior))?$auth->getIdentity()->usu_org_max_superior:$auth->getIdentity()->usu_orgao;
    }

    public function indexAction()
    {
    }

    public function listasAction()
    {

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
            $order = array(1); //NomeProjeto
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
        $where['pr.Orgao = ?'] = $this->codOrgao;

        if ($this->blnCoordenador == 'false') {
            $where['vp.idUsuario = ?'] = $this->getIdUsuario;
        }

        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    $where['pr.Situacao = ?'] = 'D03';
                    $where['NOT EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
                    break;
                case 'desistencias':
                    $where['NOT EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
                    $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbRecurso WHERE stEstado = 1 and siFaseProjeto = 2 and siRecurso = 0 AND idPronac = pr.IdPRONAC)'] = '';
                    break;
                case 'diligenciados':
                    $this->view->nmPagina = 'Proponentes Diligenciados';
                    $where['pr.Situacao = ?'] = 'D25';
                    $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
                    break;
                case 'irregulares':
                    $this->view->nmPagina = 'Proponentes Irregulares';
                    $where['pr.Situacao = ?'] = 'D11';
                    break;
                case 'respondidos':
                    $this->view->nmPagina = 'Diligencias Respondidas';
                    $where['pr.Situacao = ?'] = 'D03';
                    $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NOT NULL AND stEstado = 0)'] = '';
                    break;
                case 'finalizados':
                    $this->view->nmPagina = 'Projetos Finalizados';
                    $where['pr.Situacao = ?'] = 'D03';
                    break;
                case 'recursos':
                    $this->view->nmPagina = 'Projetos Recursos';
                    $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbRecurso WHERE idPronac = pr.idPronac AND siRecurso = ?)'] = 9;
                    $where['pr.Situacao = ?'] = 'D20';
                    break;
                case 'readequacoes':
                    $this->view->nmPagina = 'Projetos Readequa&ccedil;&otilde;es';
                    $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbReadequacao WHERE idPronac = pr.idPronac AND siEncaminhamento = ?)'] = 9;
//                    $where['pr.Situacao = ?'] = 'D20';
                    break;
            }
        } else {
            $where['pr.Situacao = ?'] = 'D03';
            $where['NOT EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
        }

        if ($this->view->filtro == 'finalizados') {
            $where['vp.stAnaliseProjeto = ?'] = 3;
        } else {
            $where['vp.stAnaliseProjeto != ?'] = 3;
        }

        if ((isset($_POST['pronacPesquisa']) && !empty($_POST['pronacPesquisa'])) || (isset($_GET['pronacPesquisa']) && !empty($_GET['pronacPesquisa']))) {
            $where['pr.AnoProjeto+pr.Sequencial = ?'] = isset($_POST['pronacPesquisa']) ? $_POST['pronacPesquisa'] : $_GET['pronacPesquisa'];
            $this->view->pronacProjeto = isset($_POST['pronacPesquisa']) ? $_POST['pronacPesquisa'] : $_GET['pronacPesquisa'];
        }

        if ((isset($_POST['nrReuniaoPesquisa']) && !empty($_POST['nrReuniaoPesquisa'])) || (isset($_GET['nrReuniaoPesquisa']) && !empty($_GET['nrReuniaoPesquisa']))) {
            $where['tr.NrReuniao = ?'] = isset($_POST['nrReuniaoPesquisa']) ? $_POST['nrReuniaoPesquisa'] : $_GET['nrReuniaoPesquisa'];
            $this->view->nrReuniaoPesquisa = isset($_POST['nrReuniaoPesquisa']) ? $_POST['nrReuniaoPesquisa'] : $_GET['nrReuniaoPesquisa'];
        }

        $projetos = new Projetos();
        $total = $projetos->painelAguardandoAnaliseDocumental($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $projetos->painelAguardandoAnaliseDocumental($where, $order, $tamanho, $inicio);

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

        $this->view->paginacao     = $paginacao;
        $this->view->qtdDocumentos = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirChecklistPublicacaoAction()
    {

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
            $order = array(1); //NomeProjeto
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('post');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['pr.Situacao = ?'] = 'D03';
        $where['pr.Orgao = ?'] = $this->codOrgao;

        if ($this->blnCoordenador == 'false') {
            $where['vp.idUsuario = ?'] = $this->getIdUsuario;
        }

        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    $where['NOT EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
                    break;
                case 'diligenciados':
                    $this->view->nmPagina = 'Proponentes Diligenciados';
                    $where['pr.Situacao = ?'] = 'D25';
                    $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
                    break;
                case 'irregulares':
                    $this->view->nmPagina = 'Proponentes Irregulares';
                    $where['pr.Situacao = ?'] = 'D11';
                    break;
                case 'respondidos':
                    $this->view->nmPagina = 'Diligencias Respondidas';
                    $where['pr.Situacao = ?'] = 'D03';
                    $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NOT NULL AND stEstado = 0)'] = '';
                    break;
                case 'finalizados':
                    $this->view->nmPagina = 'Projetos Finalizados';
                    $where['pr.Situacao = ?'] = 'D03';
                    break;
            }
        }

        if ($this->view->filtro == 'finalizados') {
            $where['vp.stAnaliseProjeto = ?'] = 3;
        } else {
            $where['vp.stAnaliseProjeto != ?'] = 3;
        }

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['pr.AnoProjeto+pr.Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        $projetos = new Projetos();
        $busca = $projetos->painelAguardandoAnaliseDocumental($where, $order);

        $this->view->dados = $busca;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function localizarAction()
    {
    }

    public function aguardandoAnaliseDocumentaloldAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $tipoAnalise = $this->_request->getParam("tipoAnalise");
        $post = Zend_Registry::get('post');

        $projetos = new Projetos();
        $diligencia = new tbDiligencia();

        /* ============== AGUARDANDO ANALISE DOCUMENTAL - D03 =============================*/
        if ($tipoAnalise == "inicial") {
            $arrBusca = array();
            $arrBusca['pr.Situacao = ?'] = 'D03';
            $arrBusca['pr.Orgao = ?'] = $this->codOrgao;
            $arrBusca['ap.TipoAprovacao = ?'] = 1;

            if ($this->codGrupo == 110) { // 110=Tecnico de Analise  - Inclui projetos que estao direcionados ao tecnico
                $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
                $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
            }
            $this->view->codDiligencia = 181;
        }

        /* ============== AGUARDANDO ANALISE DOCUMENTAL - D02 =============================*/
        if ($tipoAnalise == "readequados") {
            $arrBusca = array();
            $arrBusca['pr.Situacao = ?'] = 'D02';
            $arrBusca['pr.Orgao = ?']    = $this->codOrgao;
            $arrBusca['ap.TipoAprovacao = ?'] = 1;

            if ($this->codGrupo == 121) { //121=Tecnico Acompanhamento  - Inclui projetos que estao direcionados ao tecnico
                $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
                $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
            }
            $this->view->codDiligencia = 182;
        }

//        if(!empty($post->ordenacaoAG)){ $ordem[] = "{$post->ordenacaoAG} {$post->tipoOrdenacaoAG}"; }else{$ordem = array('1 ASC');}
        $rsAguardandoAnalise = $projetos->ProjetosCheckList($arrBusca);
        $this->view->aguardandoAnalise = $rsAguardandoAnalise;
        $this->view->parametrosBuscaAG = $_POST;
    }

    //ESTE METODO DEIXOU DE SER UTILIZADO - MANTIDO AQUI COMO REFERENCIA ATE QUE A DEMANDA SEJA HOMOLOGADA
    public function coordenadoracompanhamentoAction()
    {
        $diligencia = new tbDiligencia();
        $projetos = new Projetos();

        /* ================================================================================*/
        /* ============== AGUARDANDO ANALISE DOCUMENTAL - D02 =============================*/
        /* ================================================================================*/

        $arrBusca = array();
        $arrBusca['pr.Situacao = ?'] = 'D02';
        $arrBusca['pr.Orgao = ?']    = $this->codOrgao;

        if ($this->codGrupo == 121) { //121=Tecnico Acompanhamento  - Inclui projetos que estao direcionados ao tecnico
            $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
            $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
        }

        $projetosReadequados = $projetos->buscarProjetosCheckList($arrBusca);
        $arrProjetosReadequados = $projetosReadequados->toArray();
        $this->view->BuscarAprovadosRegularesReadequados = $arrProjetosReadequados;

        /* ================================================================================*/
        /* ============== PROPONENTE DILIGENCIADO - D33 ===================================*/
        /* ================================================================================*/

        $arrBusca = array();
        $arrBusca['pr.Situacao = ?'] = 'D33';
        $arrBusca['pr.Orgao = ?']    = $this->codOrgao;

        if ($this->codGrupo == 121) { //121=Tecnico Acompanhamento  - Inclui projetos que estao direcionados ao tecnico
            $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
            $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
        }

        $projetosDiligenciadosReadequados = $projetos->buscarProjetosCheckList($arrBusca);
        $arrProjetosDiligenciadosReadequados = $projetosDiligenciadosReadequados->toArray();
        $this->view->BuscarDiligenciadosReadequados = $arrProjetosDiligenciadosReadequados;

        /* ================================================================================*/
        /* ============== PROPONENTE IRREGULAR - D11 ======================================*/
        /* ================================================================================*/

        $arrBusca = array();
        $arrBusca['pr.Situacao = ?'] = 'D11';
        $arrBusca['pr.Orgao = ?']    = $this->codOrgao;

        if ($this->codGrupo == 121) { //121=Tecnico Acompanhamento  - Inclui projetos que estao direcionados ao tecnico
            $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
            $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
        }

        $salicReadequados = $projetos->buscarProjetosCheckList($arrBusca);
        $arrSalicReadequados = $salicReadequados->toArray();
        $this->view->BuscarIrregularSalicReadequados = $arrSalicReadequados;

        //busca areas culturais
        $areaCultura = AreaSegmentoDAO::consultaAreaCultural();
        $this->view->BuscarAreaCultura = $areaCultura;
    }

    //ESTE METODO DEIXOU DE SER UTILIZADO - MANTIDO AQUI COMO REFERENCIA ATE QUE A DEMANDA SEJA HOMOLOGADA
    public function coordenadoranaliseAction()
    {
        $diligencia = new tbDiligencia();
        $projetos = new Projetos();

        /* ================================================================================*/
        /* ============== AGUARDANDO ANALISE DOCUMENTAL - D03 =============================*/
        /* ================================================================================*/

        $arrBusca = array();
        $arrBusca['pr.Situacao = ?'] = 'D03';
        $arrBusca['pr.Orgao = ?']    = $this->codOrgao;
        /*$arrBusca['ap.TipoAprovacao = ?']               = '1';
        $arrBusca['ap.DtPublicacaoAprovacao IS NULL']   = '?';
        $arrBusca['ap.PortariaAprovacao IS NULL']       = '?';*/

        if ($this->codGrupo == 110) { // 110=Tecnico de Analise  - Inclui projetos que estao direcionados ao tecnico
            $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
            $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
        }
        
        $projetosAprovadosInic = $projetos->buscarProjetosCheckList($arrBusca);
        $arrProjetosAprovadosInic = $projetosAprovadosInic->toArray();
        $this->view->BuscarAprovadosRegularesAprovadosInic = $arrProjetosAprovadosInic;

        /* ================================================================================*/
        /* ============== PROPONENTE DILIGENCIADO - D25  ==================================*/
        /* ================================================================================*/

        $arrBusca = array();
        $arrBusca['pr.Situacao = ?'] = 'D25';
        $arrBusca['pr.Orgao = ?']    = $this->codOrgao;

        if ($this->codGrupo == 110) { // 110=Tecnico de Analise  - Inclui projetos que estao direcionados ao tecnico
            $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
            $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
        }

        $projetosDiligenciadosAprovadosInic = $projetos->buscarProjetosCheckList($arrBusca);
        $arrProjetosDiligenciadosAprovadosInic = $projetosDiligenciadosAprovadosInic->toArray();
        $this->view->BuscarDiligenciadosAprovadosInic = $arrProjetosDiligenciadosAprovadosInic;

        /* ================================================================================*/
        /* ============== PROPONENTE IRREGULAR - D11  =====================================*/
        /* ================================================================================*/
        $arrBusca = array();
        $arrBusca['pr.Situacao = ?'] = 'D11';
        $arrBusca['pr.Orgao = ?']    = $this->codOrgao;
        $arrBusca['ap.TipoAprovacao = ?']               = '1';
        $arrBusca['ap.DtPublicacaoAprovacao IS NULL']   = '?';
        $arrBusca['ap.PortariaAprovacao IS NULL']       = '?';

        if ($this->codGrupo == 110) { // 110=Tecnico de Analise  - Inclui projetos que estao direcionados ao tecnico
            $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
            $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
        }

        $salicAprovadosInic = $projetos->buscarProjetosCheckList($arrBusca);
        $arrSalicAprovadosInic = $salicAprovadosInic->toArray();
        $this->view->BuscarIrregularSalicAprovadosInic = $arrSalicAprovadosInic;

        //busca areas culturais
        $areaCultura = AreaSegmentoDAO::consultaAreaCultural();
        $this->view->BuscarAreaCultura = $areaCultura;
    }

    public function proponenteDiligenciadoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $tipoAnalise = $this->_request->getParam("tipoAnalise");
        $post = Zend_Registry::get('post');

        $projetos = new Projetos();
        $diligencia = new tbDiligencia();

        /* ============== PROPONENTE DILIGENCIADO - D25  ==================================*/
        if ($tipoAnalise == "inicial") {
            $arrBusca = array();
            $arrBusca['pr.Situacao = ?'] = 'D25';
            $arrBusca['pr.Orgao = ?']    = $this->codOrgao;
            $arrBusca["EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL)"] = '(?)';

            if ($this->codGrupo == 110) { // 110=Tecnico de Analise  - Inclui projetos que estao direcionados ao tecnico
                $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
                $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
            }

            //calcula o total de registros
            $totalProponenteDiligenciado = $projetos->buscarProjetosCheckList($arrBusca, null, null, null, true);
            $this->view->totalProponenteDiligenciado = $totalProponenteDiligenciado;
            $this->view->codDiligencia = 181;
        }

        /* ============== PROPONENTE DILIGENCIADO - D33 ===================================*/
        if ($tipoAnalise == "readequados") {
            $arrBusca = array();
            $arrBusca['pr.Situacao = ?'] = 'D33';
            $arrBusca['pr.Orgao = ?']    = $this->codOrgao;
            $arrBusca["EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = pr.idPronac AND idTipoDiligencia = 182 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL)"] = '(?)';

            if ($this->codGrupo == 121) { //121=Tecnico Acompanhamento  - Inclui projetos que estao direcionados ao tecnico
                $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
                $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
            }
            $this->view->codDiligencia = 182;
        }

        if (!empty($post->ordenacaoPD)) {
            $ordem[] = "{$post->ordenacaoPD} {$post->tipoOrdenacaoPD}";
        } else {
            $ordem = array('1 ASC');
        }
        $rsDiligenciados = $projetos->buscarProjetosCheckList($arrBusca, $ordem);
        $arrDiligenciados = $rsDiligenciados->toArray();
        $this->view->proponenteDiligenciado = $arrDiligenciados;
        $this->view->parametrosBuscaPD = $_POST;
    }

    public function proponenteIrregularAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $tipoAnalise = $this->_request->getParam("tipoAnalise");
        $post = Zend_Registry::get('post');

        $projetos = new Projetos();
        $diligencia = new tbDiligencia();

        /* ============== PROPONENTE IRREGULAR - D11 - ANALISE INICIAL ====================*/
        if ($tipoAnalise == "inicial") {
            $arrBusca = array();
            $arrBusca['pr.Situacao = ?'] = 'D11';
            $arrBusca['pr.Orgao = ?']    = $this->codOrgao;
            $arrBusca['ap.TipoAprovacao = ?']               = '1';
            $arrBusca['ap.DtPublicacaoAprovacao IS NULL']   = '?';
            $arrBusca['ap.PortariaAprovacao IS NULL']       = '?';

            if ($this->codGrupo == 110) { // 110=Tecnico de Analise  - Inclui projetos que estao direcionados ao tecnico
                $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
                $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
            }

            //calcula o total e retorna para a tela original
            $totalProponenteIrregular = $projetos->buscarProjetosCheckList($arrBusca, null, null, null, true);
            $this->view->totalProponenteIrregular = $totalProponenteIrregular;
            $this->view->codDiligencia = 181;
        }
        /* ============== PROPONENTE IRREGULAR - D11 - READEQUACAO ========================*/
        if ($tipoAnalise == "readequados") {
            $arrBusca = array();
            $arrBusca['pr.Situacao = ?'] = 'D11';
            $arrBusca['pr.Orgao = ?']    = $this->codOrgao;

            if ($this->codGrupo == 121) { //121=Tecnico Acompanhamento  - Inclui projetos que estao direcionados ao tecnico
                $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
                $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
            }
            $this->view->codDiligencia = 182;
        }

        if (!empty($post->ordenacaoPI)) {
            $ordem[] = "{$post->ordenacaoPI} {$post->tipoOrdenacaoPI}";
        } else {
            $ordem = array('1 ASC');
        }
        $rsProponenteIrregular = $projetos->buscarProjetosCheckList($arrBusca);
        $arrProponenteIrregular = $rsProponenteIrregular->toArray();
        $this->view->proponenteIrregular = $arrProponenteIrregular;
        $this->view->parametrosBuscaPI = $_POST;
    }

    /**
     * Metodo com a grid de readequacao
     */
    public function solicitacaoReadequacaoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $tipoAnalise = $this->_request->getParam("tipoAnalise");
        $post = Zend_Registry::get('post');

        $projetos = new tbPedidoAlteracaoProjeto();

        if ($tipoAnalise == "readequados") :
                    $arrBusca = array();
        $arrBusca['re.siVerificacao IN (?)'] = array('1');
        $arrBusca['rex.tpAlteracaoProjeto IN (?)'] = array('1', '2', '5', '7', '8', '9', '10');
        $arrBusca['pr.Situacao NOT IN (?)'] = array('D27', 'D28');
        if ($this->codGrupo == 121) : // 121 = Tecnico Acompanhamento - Inclui projetos que estao direcionados ao tecnico
                            $arrBusca['vp.idUsuario = ?'] = $this->getIdUsuario;
        $arrBusca['vp.stAnaliseProjeto NOT IN (?)'] = array('3','4'); //Analise Finalizada e Encaminhado para portaria
        endif;
        endif;
        if (!empty($post->ordenacaoR)) {
            $ordem[] = "{$post->ordenacaoR} {$post->tipoOrdenacaoR}";
        } else {
            $ordem = array('1 ASC');
        }
        $rsProponente  = $projetos->buscarProjetosCheckList($arrBusca, array('rex.tpAlteracaoProjeto'));
        $arrProponente = $rsProponente->toArray();
        $this->view->proponenteR      = $arrProponente;
        $this->view->parametrosBuscaR = $_POST;
    } // fecha metodo solicitacaoReadequacaoAction()


    public function gravarEncaminhamentoPortariaAction()
    {
        $idpronac = $this->_request->getParam("idpronac");
        $post = Zend_Registry::get('post');
        $idAprovacao = $post->idAprovacao;
        if (!empty($idpronac)) {
            try {
                if ($this->codGrupo == 103 || $this->codGrupo == 127) { //103=Coord. de Analise   103=Coord. Geral de Analise
                    $situacao = "D27";
                } elseif ($this->codGrupo == 122 || $this->codGrupo == 123) { //121=Cood. de Acompanhamento    123=Cood. Geral de Acompanhamento
                    $situacao = "D28";
                }

                $providencia = '';
                $tblSituacao = new Situacao();
                $rsSituacao = $tblSituacao->buscar(array('Codigo=?'=>$situacao))->current();
                if (!empty($rsSituacao)) {
                    $providencia = $rsSituacao->Descricao;
                }
                // altera a situacao do projeto
                $tblProjeto = new Projetos();
                $rsProjeto = $tblProjeto->buscar(array('IdPRONAC=?'=>$idpronac))->current();
                $nrPronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;
                $tblProjeto->alterarSituacao($idpronac, '', $situacao, $providencia);

                //STATUS AVALIACAO
                $tblVerificaProjeto = new tbVerificaProjeto();
                $rsVP = $tblVerificaProjeto->buscar(array('idPronac=?'=>$idpronac))->current();
                $dadosVP = array();
                if (empty($rsVP)) {
                    $dadosVP['idPronac']    = $idpronac;
                    $dadosVP['idOrgao']     = $this->codOrgao;
                    $dadosVP['idAprovacao'] = $idAprovacao;
                    $dadosVP['idUsuario']    = $this->getIdUsuario;
                    $dadosVP['stAnaliseProjeto']=4;
                    $dadosVP['dtFinalizado']= date('Y-m-d H:i:s');
                    $dadosVP['dtPortaria']  = date('Y-m-d H:i:s');
                    $dadosVP['stAtivo']     = 1;
                    $tblVerificaProjeto->inserir($dadosVP);
                } else {
                    $rsVP->stAnaliseProjeto = '4';
                    $rsVP->dtPortaria       = date('Y-m-d H:i:s');
                    $rsVP->save();
                }
                parent::message("Projeto encaminhando com sucesso!", "checklistpublicacao/listas?tipoFiltro=finalizados", "CONFIRM");
            } catch (Exception $e) {
                parent::message("O Projeto {$nrPronac} n&atilde;o pode ser encaminhado para Portaria, pois o proponente est&aacute; irregular.", "checklistpublicacao/listas?tipoFiltro=finalizados", "ERROR");
            }
        }
        parent::message("O Projeto {$nrPronac} n&atilde;o pode ser encaminhado para Portaria, pois o proponente est&aacute; irregular.", "checklistpublicacao/listas?tipoFiltro=finalizados", "ERROR");
    }

    public function assinaturasImprimirChecklistAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $nomeUsuario = $auth->getIdentity()->usu_nome;
        $this->view->nomeUsuario = $nomeUsuario;
        $this->view->idPronac = $this->_request->getParam("idPronac");
    }

    public function imprimirChecklistAction()
    {
        $Projetos = new Projetos();
        $dadosProjeto = $Projetos->buscarDadosUC75($_POST['idPronac'])->current();

        $this->view->dados = $_POST;
        $this->view->dadosProjeto = $dadosProjeto;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function gravarFinalizacaoAnaliseAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $usuario = $auth->getIdentity()->usu_codigo;

        $idpronac = $this->_request->getParam("idpronac");
        $post        = Zend_Registry::get('post');
        $idAprovacao = $post->idAprovacao;
        $grid        = $post->grid;
        $dtIniCaptacao = Data::dataAmericana($post->dataCaptacaoIni);
        $dtFimCaptacao = Data::dataAmericana($post->dataCaptacaoFim);

        if (!empty($idpronac)) {
            try {
                $tblProjeto = new Projetos();
                $rsProjeto = $tblProjeto->buscarProjetosCheckList(array('pr.IdPRONAC=?'=>$idpronac))->current();

                //DADOS APROVACAO
                $tblAprovacao = new Aprovacao();
                $rsAprovacao = $tblAprovacao->buscar(array('idAprovacao=?'=>$idAprovacao))->current();
                //ATUALIZANDO PERIODO DE CAPTACAO
                //$rsAprovacao->DtInicioCaptacao = (!empty($dtIniCaptacao)) ? $dtIniCaptacao : $rsProjeto->DtInicioCaptacao;
                $rsAprovacao->DtInicioCaptacao = $rsProjeto->DtInicioCaptacao;
                $rsAprovacao->DtFimCaptacao    = $rsProjeto->DtFimCaptacao;
                $rsAprovacao->Logon            = $usuario;
                $rsAprovacao->save();

                //STATUS AVALIACAO
                $tblVerificaProjeto = new tbVerificaProjeto();
                $rsVP = $tblVerificaProjeto->buscar(array('idPronac=?'=>$idpronac))->current();
                $dadosVP = array();
                if (empty($rsVP)) {
                    $dadosVP['idPronac'] = $idpronac;
                    $dadosVP['idOrgao'] = $this->codOrgao;
                    $dadosVP['idAprovacao'] = $idAprovacao;
                    $dadosVP['idUsuario'] = $this->getIdUsuario;
                    $dadosVP['stAnaliseProjeto'] = 3;
                    $dadosVP['dtFinalizado'] = new Zend_Db_Expr('GETDATE()');
                    $dadosVP['stAtivo'] = 1;
                    $tblVerificaProjeto->inserir($dadosVP);
                } else {
                    $rsVP->stAnaliseProjeto = '3';
                    $rsVP->dtFinalizado = new Zend_Db_Expr('GETDATE()');
                    $rsVP->save();
                }
                parent::message("An&aacute;lise finalizada com sucesso!", "checklistpublicacao/listas", "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao atualizar informa&ccedil;&otilde;es! Opera&ccedil;&atilde;o n&atilde;o realizada.", "checklistpublicacao/listas", "ERROR");
            }
        }
        parent::message("Erro ao atualizar informa&ccedil;&otilde;es! Pronac n&atilde;o encontrado.", "checklistpublicacao/listas", "ERROR");
    }

    public function gravarAlteracaoProjetoAction()
    {
        $this->_helper->layout->disableLayout();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $auth = Zend_Auth::getInstance(); // pega a autenticacao

        $post = Zend_Registry::get('post');
        $idpronac = $post->idpronac;
        $areacultural = $post->areacultural;
        $segmento = $post->segmento;
        $enquadramento = $post->enquadramento;
        $dsjustificativaEnquadramento = Seguranca::tratarVarAjaxUFT8($post->dsjustificativa);
        $dtinicioexec = Data::dataAmericana($post->dtinicioexecucao);
        $dtfimexecucao = Data::dataAmericana($post->dtfimexecucao);
        $dtinicaptacao = Data::dataAmericana($post->dataCaptacaoIni);
        $dtfimcaptacao = Data::dataAmericana($post->dataCaptacaoFim);
        $idAprovacao = $post->idAprovacao;
        $nomeProjeto = Seguranca::tratarVarAjaxUFT8($post->nomeProjeto);
        $resumoProjeto = Seguranca::tratarVarAjaxUFT8($post->resumoProjeto);
        $usuario = $auth->getIdentity()->usu_codigo;
        //$urlRetorno = "index.php";
        // Alterar projetos
        try {
            //DADOS DO PROJETO
            $dadosProjeto = array(
                "Area"              => $areacultural,
                "Segmento"          => $segmento,
                "DtInicioExecucao"  => $dtinicioexec,
                "DtFimExecucao"     => $dtfimexecucao,
                "Logon"             => $usuario,
                "NomeProjeto"       => $nomeProjeto,
                "ResumoProjeto"     => $resumoProjeto
            );
            $alterarTabelaProjetos = ProjetosDAO::alterarDadosProjeto($dadosProjeto, $idpronac);

            //DADOS ENQUADRAMENTO
            $dadosEnquadramento = array(
                "Enquadramento" => $enquadramento,
                "Observacao" => $dsjustificativaEnquadramento,
                "DtEnquadramento" => new Zend_Db_Expr('GETDATE()'),
                "Logon" => $usuario
            );
            $alterarEnquadramento = EnquadramentoDAO::AlterarEnquadramento($dadosEnquadramento, $idpronac);

            //DADOS APROVACAO
            $tblAprovacao = new Aprovacao();
            $rsAprovacao = $tblAprovacao->buscar(array('idAprovacao=?'=>$idAprovacao))->current();
            //ATUALIZANDO PERIODO DE CAPTACAO
            $rsAprovacao->DtInicioCaptacao = $dtinicaptacao;
            $rsAprovacao->DtFimCaptacao    = $dtfimcaptacao;
            $rsAprovacao->Logon            = $usuario;
            $rsAprovacao->save();

            //STATUS AVALIACAO
            $tblVerificaProjeto = new tbVerificaProjeto();
            $rsVP = $tblVerificaProjeto->buscar(array('idPronac=?'=>$idpronac))->current();
            $dadosVP = array();
            if (empty($rsVP)) {
                $dadosVP['idPronac']    = $idpronac;
                $dadosVP['idOrgao']     = $this->codOrgao;
                $dadosVP['idAprovacao'] = $idAprovacao;
                $dadosVP['idUsuario']    = $usuario;
                $dadosVP['stAnaliseProjeto']=2;
                $dadosVP['dtRecebido']  = new Zend_Db_Expr('GETDATE()');
                //$dadosVP['dtFinalizado']= $idpronac;
                //$dadosVP['dtPortaria']  = $idpronac;
                $dadosVP['stAtivo']     = 1;
                $tblVerificaProjeto->inserir($dadosVP);
            } else {
                $rsVP->stAnaliseProjeto = '2';
                $rsVP->save();
            }
            $this->_helper->json(array('error' => false, "msg" => "Projeto alterado com sucesso!"));
            $this->_helper->viewRenderer->setNoRender(true);
            //parent::message("Projeto alterado com sucesso!!", "checklistpublicacao/", "CONFIRM");
        } catch (Exception $e) {
            $this->_helper->json(array('error' => true, "msg" => "Erro ao atualizar informa&ccedil;&otilde;es! As altera&ccedil;&otilde;es n&atilde;o foram salvas. ".$e->getMessage()));
            $this->_helper->viewRenderer->setNoRender(true);
            //parent::message("Erro ao atualizar informacoes! Operacao nao realizada. ".$e->getMessage(), "checklistpublicacao/", "ERROR");
        }
    }

    public function alterarProjetoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $post = Zend_Registry::get('post');
        $idpronac = $post->idpronac;
        $dadosDoProjeto = array();
        $this->view->idpronac = $idpronac;

        //$projetos = New Projetos();
        //$busca = $projetos->buscar(array('IdPRONAC = ?' => $idpronac));

        $arrBusca = array();
        $arrBusca['pr.IdPRONAC = ?'] = $idpronac;

        $tblProjeto = new Projetos();
        $rsProjeto = $tblProjeto->ProjetosCheckList($arrBusca)->current();
        $this->view->projetos = $rsProjeto;

        /*$anoProjeto = $busca[0]['AnoProjeto'];
        $sequencial = $busca[0]['Sequencial'];
        $buscaTotalAprovadoProjeto = Aprovacao::buscaTotalAprovadoProjeto($anoProjeto, $sequencial);

        $totalAprovadoProjeto =  $buscaTotalAprovadoProjeto[0]->total;
        $dadosDoProjeto['totalAprovadoProjeto'] = $totalAprovadoProjeto;

        $dadosProjeto = AprovacaoDAO::buscarPedidosProjetosAprovados($idpronac);

        foreach ($dadosProjeto as $projeto)
        {
            $dadosDoProjeto['pronac'] = $projeto->pronac;
            $dadosDoProjeto['nomeprojeto'] = ($projeto->NomeProjeto);
            $dadosDoProjeto['cdarea'] = $projeto->cdarea;
            $dadosDoProjeto['cdsegmento'] = $projeto->cdseg;
            $dadosDoProjeto['resumo'] = ($projeto->ResumoProjeto);
            $dadosDoProjeto['enquadramento'] = $projeto->nrenq;
            $dadosDoProjeto['justEnquadramento'] = $projeto->Observacao;
            $valoresdata = AprovacaoDAO::buscarCaptacaoRead($idpronac);
            $a = 0;
            foreach ($valoresdata as $valor)
            {
                $dadosDoProjeto['captacao'][$a]['iniciocaptacao'] = Data::tratarDataZend($valor->dtiniciocaptacao, "brasileiro");
                $dadosDoProjeto['captacao'][$a]['fimcaptacao'] = Data::tratarDataZend($valor->dtfimcaptacao, "brasileiro");
                if ($valor->PortariaAprovacao != NULL)
                {
                    $dadosDoProjeto['captacao'][$a]['portaria'] = ($valor->PortariaAprovacao);
                }
                else
                {
                    $dadosDoProjeto['captacao'][$a]['portaria'] = ' - ';
                }
            }
            $datafimexecucao = strtotime($projeto->DtFimExecucao);
            $dataCaptacaoFim = strtotime(date('Y-12-31'));

            $dadosDoProjeto['dtiniciocaptacao'] = Data::somarData(date('Y-m-d'), 1);

            if ($datafimexecucao <= $dataCaptacaoFim)
            {
                $dadosDoProjeto['dtfimcaptacao'] = Data::tratarDataZend($projeto->DtFimExecucao, 'Brasileiro'); // Data::tratarDataZend($projeto->DtFimCaptacao, 'Brasileiro');
            }
            else
            {
                $dadosDoProjeto['dtfimcaptacao'] = date('31/12/Y');
            }

            $dadosDoProjeto['dtinicioexecucao'] = Data::tratarDataZend($projeto->DtInicioExecucao, 'brasileiro');
            $dadosDoProjeto['dtfimexecucao'] = Data::tratarDataZend($projeto->DtFimExecucao, 'brasileiro');
            $dadosDoProjeto['proponente'] = ($projeto->nome);
            $dadosDoProjeto['cnpj'] = Validacao::mascaraCPFCNPJ($projeto->CgcCpf);
            /*$aprovadoReal = AprovacaoDAO::SomarAprovacao($idpronac);
            $dadosDoProjeto['AprovadoReal'] = number_format($aprovadoReal['soma'], '2', ',', '.');*/

        $tipoaprovacaoComplementacao = AprovacaoDAO::SomarReadeqComplementacao($idpronac, 2);
        $tipoaprovacaoReadequacao    = AprovacaoDAO::SomarReadeqComplementacao($idpronac, 4);

        if (count($tipoaprovacaoComplementacao) > 0) {
            $dadosReadequacao['Tipo'] = ('Valor Complementa&ccedil;&atilde;o (R$):');
            $dadosReadequacao['ReadCompl'] = number_format($tipoaprovacaoComplementacao['soma'], '2', ',', '.');
        } else {
            if (count($tipoaprovacaoReadequacao) > 0) {
                $dadosReadequacao['Tipo'] = ('Valor Readequa&ccedil;&atilde;o (R$):');
                $dadosReadequacao['ReadCompl'] = number_format($tipoaprovacaoReadequacao['soma'], '2', ',', '.');
            }
        }
        $this->view->dadosReadequacao = $dadosReadequacao;

        //$dadosDoProjeto['idAprovacao'] = $projeto->idAprovacao;

        /*}*/

        //busca areas culturais
        $areaCultura = new Area();
        $this->view->BuscarAreaCultura = $areaCultura->buscar(array('Codigo != ?'=>7));
    }

    public function alterarProjetoReadequacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $post = Zend_Registry::get('post');
        $idpronac = $post->idpronac;
        $dadosDoProjeto = array();
        $this->view->idpronac = $idpronac;

        $arrBusca = array();
        $arrBusca['pr.IdPRONAC = ?'] = $idpronac;

        $tblProjeto = new tbPedidoAlteracaoProjeto();
        $rsProjeto = $tblProjeto->buscarProjetosCheckList($arrBusca)->current();
        $this->view->projetos = $rsProjeto;

        $tipoaprovacaoComplementacao = AprovacaoDAO::SomarReadeqComplementacao($idpronac, 2);
        $tipoaprovacaoReadequacao    = AprovacaoDAO::SomarReadeqComplementacao($idpronac, 4);

        if (count($tipoaprovacaoComplementacao) > 0) {
            $dadosReadequacao['Tipo'] = ('Valor Complementa&ccedil;&atilde;o (R$):');
            $dadosReadequacao['ReadCompl'] = number_format($tipoaprovacaoComplementacao['soma'], '2', ',', '.');
        } else {
            if (count($tipoaprovacaoReadequacao) > 0) {
                $dadosReadequacao['Tipo'] = ('Valor Readequa&ccedil;&atilde;o (R$):');
                $dadosReadequacao['ReadCompl'] = number_format($tipoaprovacaoReadequacao['soma'], '2', ',', '.');
            }
        }
        $this->view->dadosReadequacao = $dadosReadequacao;

        //busca areas culturais
        $areaCultura = AreaSegmentoDAO::consultaAreaCultural();
        $this->view->BuscarAreaCultura = $areaCultura;
    }

    public function recuperaAreaCulturalAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $cdarea = $this->_request->getParam("area");
        //$cdarea = $post->area;
        $dadosSegmento = array();
        $vSegmento = array();
        $objSegmentocultural = new Segmentocultural();
        $dadosSegmento = $objSegmentocultural->buscarSegmento($cdarea);
        $i = 0;
        if (count($dadosSegmento)>0) {
            foreach ($dadosSegmento as $segmento) {
                $vSegmento[$i]['cdsegmento'] = $segmento->id;
                $vSegmento[$i]['descsegmento'] = utf8_encode($segmento->descricao);
                $i++;
            }
        }
        $jsonSegmento = json_encode($vSegmento);
        echo $jsonSegmento;
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function formRedistribuirProjetoAction()
    {
        $arrBusca = array();
        if ($this->tipoAnalise == "inicial") {
            $arrBusca['g.gru_codigo = ?'] = 110;
            //$arrBusca['g.gru_codigo = ?'] = $this->codOrgao;
        } else {
            $arrBusca['g.gru_codigo = ?'] = 121;
        }
        //$arrBusca[" TABELAS.dbo.fnCodigoOrgaoEstrutura(o.org_codigo, 1) = ? "] = $this->codOrgaoSuperior;
        $arrBusca['ug.uog_orgao = ? '] = $this->codOrgao;
        $arrBusca['ug.uog_status = ?'] = 1; //usuarios ativos

        $tblTecnicos = new Usuariosorgaosgrupos();
        $rsTecnicos = $tblTecnicos->buscarUsuariosOrgaosGrupos($arrBusca, array('u.usu_nome ASC', 'g.gru_nome ASC'));
        $this->view->tecnicos = $rsTecnicos;
    }

    public function buscarTecnicosRedistribuicaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $post = Zend_Registry::get('post');
        $idTecnico = $post->tecnico;
        $pronac    = $post->pronac;
        $nrReuniao = $post->reuniao;

        $arrBusca = array();
        if (!empty($idTecnico)) {
            $arrBusca['vp.idUsuario = ?'] = $idTecnico;
        }
        if (!empty($pronac)) {
            $arrBusca['(pr.AnoProjeto + pr.Sequencial) = ?'] = $pronac;
        }
        if (!empty($nrReuniao)) {
            $arrBusca['tr.NrReuniao = ?'] = $nrReuniao;
        }
        $arrBusca['vp.stAnaliseProjeto IN (?)'] = array('1','2','3');
        $arrBusca['vp.stAnaliseProjeto in (SELECT TOP 1 max(stAnaliseProjeto) from SAC..tbVerificaProjeto where IdPRONAC = pr.IdPRONAC)'] = '?';

        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array('32 ASC');
        }

        $tblProjeto = new Projetos();
        $rsProjetos = $tblProjeto->buscarProjetosCheckList($arrBusca, $ordem);
        $this->view->projetos = $rsProjetos;
        $this->view->parametrosBusca = $_POST;

        //BUSCAR TECNICOS PARA DISTRIBUIR
        $arrBusca = array();
        if ($this->tipoAnalise == "inicial") {
            $arrBusca['g.gru_codigo = ?'] = 110;
            //$arrBusca['g.gru_codigo = ?'] = $this->codOrgao;
        } else {
            $arrBusca['g.gru_codigo = ?'] = 121;
        }
        //$arrBusca[" TABELAS.dbo.fnCodigoOrgaoEstrutura(o.org_codigo, 1) = ? "] = $this->codOrgaoSuperior;
        $arrBusca['ug.uog_orgao = ? '] = $this->codOrgao;
        $arrBusca['ug.uog_status = ?'] = 1; //usuarios ativos
        if (!empty($idTecnico)) {
            $arrBusca['u.usu_codigo <> ?'] = $idTecnico; //tecnico atual
        }

        $tblTecnicos = new Usuariosorgaosgrupos();
        $rsTecnicos = $tblTecnicos->buscarUsuariosOrgaosGrupos($arrBusca, array('u.usu_nome ASC', 'g.gru_nome ASC'));
        $this->view->tecnicos = $rsTecnicos;
    }

    public function redistribuirProjetoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $post = Zend_Registry::get('post');
        $arrPronacs = $post->idpronac;
        $idTecnico = $post->novoTecnico;
        

        $tblVerificaProjeto = new tbVerificaProjeto();
        try {
            foreach ($arrPronacs as $idPronac) {
                $rsVerificaProjeto = $tblVerificaProjeto->buscar(array('idPronac = ?'=>$idPronac))->current();
                if (!empty($rsVerificaProjeto)) {
                    $rsVerificaProjeto->idUsuario = $idTecnico;
                    $rsVerificaProjeto->stAnaliseProjeto = 1; //aguardando analise
                    $rsVerificaProjeto->save();
                }
            }
            parent::message("Projeto(s) redistribu&iacute;do(s) com sucesso!", "checklistpublicacao/listas", "CONFIRM");
            return;
        } // fecha try
        catch (Exception $e) {
            parent::message("Erro ao redistribuir projeto(s). ".$e->getMessage(), "checklistpublicacao/listas", "ERROR");
            return;
        }
    }
}
