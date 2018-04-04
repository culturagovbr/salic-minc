<?php
require_once "library/MinC/Currency/Currency.php";

class PareceristaController extends MinC_Controller_Action_Abstract
{

    /**
     * @var integer (variável com o id do usuário logado)
     * @access private
     */
    private $getIdUsuario = 0;
    private $intTamPag = 10;


    public function init()
    {
        $this->view->title  = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // título da página
        $auth               = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario            = new UsuarioDAO(); // objeto usuário
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuário esteja autenticado
            // verifica as permissões
            $PermissoesGrupo    = array();
            $PermissoesGrupo[]  = 94;
            $PermissoesGrupo[]  = 93;
            $PermissoesGrupo[]  = 137;

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo está no array de permissões
                parent::message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visão
            $this->view->usuario        = $auth->getIdentity(); // manda os dados do usuário para a visão
            $this->view->arrayGrupos    = $grupos; // manda todos os grupos do usuário para a visão
            $this->view->grupoAtivo     = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
            $this->view->orgaoAtivo     = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão

            if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
                $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
                $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
            }
        } // fecha if
        else { // caso o usuário não esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    /**
     * Método index()
     * Consulta Coordenador
     * @access public
     * @param void
     * @return list
     */
    public function indexAction()
    {
        $pronacPesquisa     = $this->_request->getParam('pronacPesquisa');
        $situacaoPesquisa   = $this->_request->getParam('situacaoPesquisa', 1);
        $nrCPFPesquisa      = $this->_request->getParam('nrCPFPesquisa');
        $dtInicioPesquisa   = $this->_request->getParam('dtInicioPesquisa');
        $dtFimPesquisa      = $this->_request->getParam('dtFimPesquisa');

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }

        $order = array();

        // Parametro de ordenacao
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

        // campo de ordenacao
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");

            $cn = $campo;

            if ($campo == 'nome') {
                $campo = 'sac.dbo.fnNome(pp.idParecerista)';
                $cn = 'nome';
            }

            if ($campo == 'pronac') {
                $campo = '(pro.AnoProjeto + pro.Sequencial)';
            }

            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo      = null;
            $cn         = null;
            $order      = array(2);
            $ordenacao  = null;
        }

        $pag = 1;

        $get = Zend_Registry::get('get');

        if (isset($get->pag)) {
            $pag = $get->pag;
        }

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $where = array();

        if (!empty($pronacPesquisa)) {
            $where['pro.AnoProjeto + pro.Sequencial = ?'] = $pronacPesquisa;
            $this->view->pronacPesquisa = $pronacPesquisa;
        }

        if (!empty($situacaoPesquisa)) {
            $where['gpp.siPagamento = ?'] = $situacaoPesquisa;
            $this->view->situacaoPesquisa = $situacaoPesquisa;
        } else {
            $where['gpp.siPagamento = ?'] = 3;
            $this->view->situacaoPesquisa = 3;
        }

        if (!empty($nrCPFPesquisa)) {
            $where['a.CNPJCPF = ?'] = Mascara::delMaskCPF($nrCPFPesquisa);
            $this->view->nrCPFPesquisa = $nrCPFPesquisa;
        }

        if (!empty($dtInicioPesquisa)) {
            $where['dtGeracaoPagamento >= ?'] = date('Y-d-m', strtotime($dtInicioPesquisa)) ;
            $this->view->dtInicioPesquisa = $dtInicioPesquisa;
        }

        if (!empty($dtFimPesquisa)) {
            $where['dtGeracaoPagamento <= ?'] = date('Y-d-m', strtotime($dtFimPesquisa)) ;
            $this->view->dtFimPesquisa = $dtFimPesquisa;
        }

        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $total = $modelGerarPagamentoParecerista->buscarProjetosFinalizados($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;


        $busca = $modelGerarPagamentoParecerista->buscarProjetosFinalizados($where, $order, $tamanho, $inicio);

        $paginacao = array(
                "pag"       => $pag,
                "qtde"      => $this->intTamPag,
                "campo"     => $cn,
                "ordem"     => $ordem,
                "ordenacao" => $ordenacao,
                "novaOrdem" => $novaOrdem,
                "total"     => $total,
                "inicio"    => ($inicio+1),
                "fim"       => $fim,
                "totalPag"  => $totalPag,
                "Itenspag"  => $this->intTamPag,
                "tamanho"   => $tamanho
         );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdDocumentos = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    /**
     *
     */
    public function indexImprimirAction()
    {
        $this->indexAction();
        $this->_helper->layout->disableLayout();
    }

    /**
     * Método consultar()
     * Consulta Parecerista
     * @access public
     * @param void
     * @return list
     */
    public function consultarAction()
    {
        $pronacPesquisa     = $this->_request->getParam('pronacPesquisa');
        $situacaoPesquisa   = $this->_request->getParam('situacaoPesquisa');
        $nrCPFPesquisa      = $this->_request->getParam('nrCPFPesquisa');
        $dtInicioPesquisa   = $this->_request->getParam('dtInicioPesquisa');
        $dtFimPesquisa      = $this->_request->getParam('dtFimPesquisa');

        $auth = Zend_Auth::getInstance();
        $idAgente = 0;

        if (isset($auth->getIdentity()->usu_codigo)) {
            $Usuario      = new Autenticacao_Model_Usuario(); // objeto usuário
            $Agente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
            $idAgente = $Agente['idagente'];
            $this->view->idAgente    = $idAgente;
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }

        $order = array();

        // Parametro de ordenacao
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

        // campo de ordenacao
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");

            $cn = $campo;

            if ($campo == 'nome') {
                $campo = 'sac.dbo.fnNome(pp.idParecerista)';
                $cn = 'nome';
            }

            if ($campo == 'pronac') {
                $campo = '(pro.AnoProjeto + pro.Sequencial)';
            }

            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo      = null;
            $cn         = null;
            $order      = '';
            $ordenacao  = null;
        }

        $pag = 1;

        $get = Zend_Registry::get('get');

        if (isset($get->pag)) {
            $pag = $get->pag;
        }

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $where = array();

        if (!empty($pronacPesquisa)) {
            $where['pro.AnoProjeto + pro.Sequencial = ?'] = $pronacPesquisa;
            $this->view->pronacPesquisa = $pronacPesquisa;
        }

        if (!empty($situacaoPesquisa)) {
            $where['gpp.siPagamento = ?'] = $situacaoPesquisa;
            $this->view->situacaoPesquisa = $situacaoPesquisa;
        } else {
            $where['gpp.siPagamento = ?'] = 3;
            $this->view->situacaoPesquisa = 3;
        }

        if (!empty($nrCPFPesquisa)) {
            $where['a.CNPJCPF = ?'] = Mascara::delMaskCPF($nrCPFPesquisa);
            $this->view->nrCPFPesquisa = $nrCPFPesquisa;
        }

        if (!empty($dtInicioPesquisa)) {
            $where['dtGeracaoPagamento >= ?'] = date('Y-d-m', strtotime($dtInicioPesquisa)) ;
            $this->view->dtInicioPesquisa = $dtInicioPesquisa;
        }

        if (!empty($dtFimPesquisa)) {
            $where['dtGeracaoPagamento <= ?'] = date('Y-d-m', strtotime($dtFimPesquisa)) ;
            $this->view->dtFimPesquisa = $dtFimPesquisa;
        }

        $where['pp.idParecerista = ?'] = $idAgente;

        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $total = $modelGerarPagamentoParecerista->buscarProjetosFinalizados($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $modelGerarPagamentoParecerista->buscarProjetosFinalizados($where, $order, $tamanho, $inicio);

        $paginacao = array(
                "pag"       => $pag,
                "qtde"      => $this->intTamPag,
                "campo"     => $cn,
                "ordem"     => $ordem,
                "ordenacao" => $ordenacao,
                "novaOrdem" => $novaOrdem,
                "total"     => $total,
                "inicio"    => ($inicio+1),
                "fim"       => $fim,
                "totalPag"  => $totalPag,
                "Itenspag"  => $this->intTamPag,
                "tamanho"   => $tamanho
         );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdDocumentos = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirConsultaAction()
    {
        $pronacPesquisa     = $this->_request->getParam('pronacPesquisa');
        $situacaoPesquisa   = $this->_request->getParam('situacaoPesquisa');
        $dtInicioPesquisa   = $this->_request->getParam('dtInicioPesquisa');
        $dtFimPesquisa      = $this->_request->getParam('dtFimPesquisa');

        $this->view->situacaoPesquisa = $situacaoPesquisa;

        $auth = Zend_Auth::getInstance();
        $idAgente = 0;

        if (isset($auth->getIdentity()->usu_codigo)) {
            $Usuario      = new Autenticacao_Model_Usuario(); // objeto usuário
            $Agente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
            $idAgente = $Agente['idAgente'];
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }

        $order = array();

        // Parametro de ordenacao
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

        // campo de ordenacao
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $cn = $campo;
            if ($campo == 'nome') {
                $campo = 'sac.dbo.fnNome(pp.idParecerista)';
                $cn = 'nome';
            }

            if ($campo == 'pronac') {
                $campo = '(pro.AnoProjeto + pro.Sequencial)';
            }

            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo      = null;
            $cn         = null;
            $order      = '';
            $ordenacao  = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('post');

        if (isset($get->pag)) {
            $pag = $get->pag;
        }

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $where = array();

        if (!empty($pronacPesquisa)) {
            $where['pro.AnoProjeto + pro.Sequencial = ?'] = $pronacPesquisa;
        }

        if (!empty($situacaoPesquisa)) {
            $where['gpp.siPagamento = ?'] = $situacaoPesquisa;
        } else {
            $where['gpp.siPagamento = ?'] = 3;
        }

        if (!empty($nrCPFPesquisa)) {
            $where['a.CNPJCPF = ?'] = Mascara::delMaskCPF($nrCPFPesquisa);
        }

        if (!empty($dtInicioPesquisa)) {
            $where['dtGeracaoPagamento >= ?'] = date('Y-d-m', strtotime($dtInicioPesquisa)) ;
        }

        if (!empty($dtFimPesquisa)) {
            $where['dtGeracaoPagamento <= ?'] = date('Y-d-m', strtotime($dtFimPesquisa)) ;
        }

//        $where['pp.idParecerista = ?'] = 30013;
        $where['pp.idParecerista = ?'] = $idAgente;

        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $total = $modelGerarPagamentoParecerista->buscarProjetosFinalizados($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $modelGerarPagamentoParecerista->buscarProjetosFinalizados($where, $order, $tamanho, $inicio);

        $this->view->qtdDocumentos = $total;
        $this->view->dados         = $busca;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    /**
     * Método addAssinantes()
     * Adicionar assinante
     * @access public
     * @param void
     * @return void
     */
    public function addAssinantesAction()
    {
        try {
            $parametros = explode('-', $this->_request->getParam('assinanteCargo'));
            $assinante      = $parametros[0];
            $configuracao   = $parametros[1];

            $modeltbConfigurarPagamentoXtbAssinantes = new tbConfigurarPagamentoXtbAssinantes();
            $counter = $modeltbConfigurarPagamentoXtbAssinantes->buscar(array('idConfigurarPagamento = ?' => $configuracao))->count();

            $modeltbConfigurarPagamentoXtbAssinantes->inserir(
                array('idAssinantes' => $assinante, 'idConfigurarPagamento' => $configuracao, 'nrOrdenacao' => ++$counter,),
                false
            );
            parent::message('Assinatura configurada com sucesso!', 'parecerista/configurar-pagamento-parecerista', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao configurar a assinatura.', 'parecerista/configurar-pagamento-parecerista', 'ERROR');
        }
    }

    /**
     * Método removeAssinantes()
     * Remover assinante
     * @access public
     * @param void
     * @return void
     */
    public function removeAssinantesAction()
    {
        $modeltbConfigurarPagamentoXtbAssinantes = new tbConfigurarPagamentoXtbAssinantes();

        try {
            $parametros = explode('-', $this->_request->getParam('assinanteCargoRemove'));

            $assinante      = $parametros[0];
            $configuracao   = $parametros[1];

            $where['idAssinantes = ?'] = $assinante;
            $where['idConfigurarPagamento = ?'] = $configuracao;

            $modeltbConfigurarPagamentoXtbAssinantes->delete($where);

            parent::message('Assinatura removida com sucesso!', 'parecerista/configurar-pagamento-parecerista', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao remover a assinatura.', 'parecerista/configurar-pagamento-parecerista', 'ERROR');
        }
    }

    /**
     * Método configurarPagamentoParecerista()
     * Configurar Pagamentos de Pareceristas
     * @access public
     * @param void
     * @return void
     */
    public function configurarPagamentoPareceristaAction()
    {
        $modelPagarParecerista    = new PagarParecerista();
        $modelConfigurarPagamento = new ConfigurarPagamentoParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();
        $modelAssinantes = new tbAssinantes();
        $modeltbConfigurarPagamentoXtbAssinantes = new tbConfigurarPagamentoXtbAssinantes();

        // Buscar o Último despacho gerado
        $ultimoDespachoDoAno = $modelGerarPagamentoParecerista->ultimoDespachoDoAno();
        $this->view->assign('ultimoDespachoDoAno', $ultimoDespachoDoAno['UltimoDespachoDoAno']);

        // Pega a autenticação
        $auth = Zend_Auth::getInstance();

        // Dados da configuração de pagamento
        $configAtivo = $modelConfigurarPagamento->buscarConfiguracoes(array('stEstado = ?' => '1'));

        if (count($configAtivo) == 0) {
            $dados = array('nrDespachoInicial' => 0,
                            'nrDespachoFinal' => 0,
                            'dtConfiguracaoPagamento' => new Zend_Db_Expr('GETDATE()'),
                            'stEstado' => 1,
                            'idUsuario' => $auth->getIdentity()->usu_codigo
            );

            $modelConfigurarPagamento->inserir($dados);
            $configAtivo = $modelConfigurarPagamento->buscarConfiguracoes(array('stEstado = ?' => '1'));
        }

        $this->view->assign('configuracaoAtiva', $configAtivo);

        // Envia todos os assinantes selecionados
        $assinantesConfigurados = $modeltbConfigurarPagamentoXtbAssinantes->assinantesConfigurados(
            array(
                'a.idConfigurarPagamento = ?' => $configAtivo[0]->idConfigurarPagamento
            )
        );

        $this->view->assign('assinantesConfigurados', $assinantesConfigurados);

        $idAssinantes = array();
        foreach ($assinantesConfigurados as $ac) {
            array_push($idAssinantes, $ac->idAssinantes);
        }

        // Envia todos os assinantes que ainda não foram configurados
        $assinantes = $modelAssinantes->listarAssinantes(array('stEstado = ?' => 1));
        $listaAssinantes = array();
        $i = 0;
        foreach ($assinantes as $ass) {
            if (!in_array($ass->idAssinantes, $idAssinantes)) {
                $listaAssinantes[$i]['idAssinantes'] = $ass->idAssinantes;
                $listaAssinantes[$i]['Nome'] = $ass->Nome;
                $listaAssinantes[$i]['Cargo'] = $ass->Cargo;
            }

            $i++;
        }

        $this->view->assign('assinantes', $listaAssinantes);

        // Dados dos pareceristas e projetos analisados
        $listaPareceristas = $modelPagarParecerista->buscarPareceristas(array('pp.idGerarPagamentoParecerista IS NULL' => null));

        $parecerista = array();

        $pa = 0;
        foreach ($listaPareceristas as $p) {
            $parecerista[$pa]['idParecerista'] = $p->idParecerista;
            $parecerista[$pa]['nmParecerista'] = $p->nmParecerista;

            $listaDePagamentos = $modelPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista IS NULL' => null, 'idParecerista = ?' => $p->idParecerista));

            $dados = array();
            $pr = 0;
            foreach ($listaDePagamentos as $pag) {
                $dados[$pr]['idPagarParecerista']   = $pag->idPagarParecerista;
                $dados[$pr]['idPronac']             = $pag->idpronac;
                $dados[$pr]['pronac']               = $pag->pronac;
                $dados[$pr]['NomeProjeto']          = $pag->NomeProjeto;
                $dados[$pr]['UnidadeAnalise']       = $pag->Vinculada;
                $dados[$pr]['Produto']              = $pag->Produto;
                $dados[$pr]['vlPagamento']          = $pag->vlPagamento;
                $pr++;
            }

            $parecerista[$pa]['Projetos'] = $dados;

            $pa++;
        }

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator          = Zend_Paginator::factory($parecerista); // dados a serem paginados
        $currentPage        = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10);

        $this->view->listaDePagamentos      = $paginator;
        $this->view->qtdDePagamentos      = $pa; // quantidade
    }

    /**
     * Método configurouPagamentoParecerista()
     * Confirmação da configuração de pagamentos de pareceristas
     * @access public
     * @param void
     * @return void
     */
    public function configurouPagamentoPareceristaAction()
    {
        $modelPagarParecerista    = new PagarParecerista();
        $modelConfigurarPagamento = new ConfigurarPagamentoParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();
        $auth = Zend_Auth::getInstance();

        $idConfigurarPagamento  = $this->_request->getParam('idConfigurarPagamento');
        $nrDespachoInicial      = $this->_request->getParam('nrDespachoInicial');
        $nrDespachoFinal        = $this->_request->getParam('nrDespachoFinal');

        try {

            // Dados da configuração de pagamento
            $dados = array('nrDespachoInicial'       => $nrDespachoInicial,
                           'nrDespachoFinal'         => $nrDespachoFinal,
                           'dtConfiguracaoPagamento' => new Zend_Db_Expr('getDate()'),
                           'stEstado'                => 0,
                           'idUsuario'               => $auth->getIdentity()->usu_codigo
            );

            $modelConfigurarPagamento->update($dados, array('idConfigurarPagamento = ?' => $idConfigurarPagamento));

            // Dados dos pareceristas e projetos analisados
            $listaPareceristas = $modelPagarParecerista->buscarPareceristas(array('pp.idGerarPagamentoParecerista IS NULL' => null));

            $di = $nrDespachoInicial;
            foreach ($listaPareceristas as $p) {
                $vlTotalPagamento = $modelPagarParecerista->vlTotalPagamento(array('pp.idGerarPagamentoParecerista IS NULL' => null, 'idParecerista = ?' => $p->idParecerista));

                // Gerar Pagamento Parecerista
                $dados = array('nrDespacho'             => $di,
                               'vlTotalPagamento'       => $vlTotalPagamento[0]->vlTotalPagamento,
                               'idConfigurarPagamento'  => $idConfigurarPagamento,
                               'siPagamento'            => 1,
                               'dtGeracaoPagamento'     => new Zend_Db_Expr('GETDATE()'),
                               'idUsuario'              => $auth->getIdentity()->usu_codigo
                );

                $id = $modelGerarPagamentoParecerista->inserir($dados, false);

                // Atualiza Pagar Parecerista
                $listaDePagamentos = $modelPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista IS NULL' => null, 'idParecerista = ?' => $p->idParecerista));

                foreach ($listaDePagamentos as $pag) {
                    $modelPagarParecerista->update(array('idGerarPagamentoParecerista' => $id), array('idPagarParecerista = ?' => $pag->idPagarParecerista));
                }

                $di++;
            }

            parent::message('Despachos gerados com sucesso!', 'parecerista/solicitar-pagamento-parecerista', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao gerar os despachos.', 'parecerista/gerar-pagamento-parecerista', 'ERROR');
        }
    }

    /**
     * Método cancelarPagamentoParecerista()
     * Cancelar pagamentos de pareceristas
     * @access public
     * @param void
     * @return void
     */
    public function cancelarPagamentoPareceristaAction()
    {
        $modelPagarParecerista          = new PagarParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();
        $idGerarPagamentoParecerista    = $this->_request->getParam('idGerarPagamentoParecerista');

        try {

            // Limpar na tabela [tbPagarParecerista]
            $modelPagarParecerista->update(array('idGerarPagamentoParecerista' => null), array('idGerarPagamentoParecerista = ?' => $idGerarPagamentoParecerista));

            // Limpar na tabela [tbConfigurarPagamento]
            $modelGerarPagamentoParecerista->delete(array('idGerarPagamentoParecerista = ?' => $idGerarPagamentoParecerista));

            parent::message('Despacho cancelado com sucesso!', 'parecerista/solicitar-pagamento-parecerista', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao cancelar o despacho.', 'parecerista/solicitar-pagamento-parecerista', 'ERROR');
        }
    }

    public function excluirRegistroPagamentoAction()
    {
        $idPagamento = $this->_request->getParam('idPagamentoCancelar');
        $modelPagarParecerista = new PagarParecerista();

        try {
            // Exclui o registro na tabela [tbPagarParecerista]
            $modelPagarParecerista->delete(array('idPagarParecerista = ?' => $idPagamento));
            parent::message('Exclusão realiazada com sucesso!', 'parecerista/configurar-pagamento-parecerista', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao excluir o registto.', 'parecerista/configurar-pagamento-parecerista', 'ERROR');
        }
    }

    /**
     * Método solicitarPagamentoParecerista()
     * Solicitação de pagamentos de pareceristas configurados
     * @access public
     * @param void
     * @return void
     */
    public function solicitarPagamentoPareceristaAction()
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
            $order = array(7); //Coluna NrDespacho
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/

        $DadosGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $total = $DadosGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 1), $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $DadosGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 1), $order, $tamanho, $inicio);

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

        $dadosPagarParecerista = new PagarParecerista();

        $despachos = array();

        $d = 0;

        foreach ($busca as $de) {
            $despachos[$d]['idGerarPagamentoParecerista']   = $de->idGerarPagamentoParecerista;
            $despachos[$d]['idConfigurarPagamento']         = $de->idConfigurarPagamento;
            $despachos[$d]['dtGeracaoPagamento']            = $de->dtGeracaoPagamento;
            $despachos[$d]['anoGeracaoPagamento']           = substr($de->dtGeracaoPagamento, -4);
            $despachos[$d]['dtEfetivacaoPagamento']         = $de->dtEfetivacaoPagamento;
            $despachos[$d]['dtOrdemBancaria']               = $de->dtOrdemBancaria;
            $despachos[$d]['nrOrdemBancaria']               = $de->nrOrdemBancaria;
            $despachos[$d]['nrDespacho']                    = $de->nrDespacho;
            $despachos[$d]['vlTotalPagamento']              = $de->vlTotalPagamento;
            $despachos[$d]['siPagamento']                   = $de->siPagamento;
            $despachos[$d]['nmParecerista']                 = $de->nmParecerista;

            $listaDePagamentos = $dadosPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

            $despachos[$d]['idParecerista'] = $listaDePagamentos[0]->idParecerista;

            $dados = array();
            $pr = 0;
            $valorTotal = 0;
            $pronac = $listaDePagamentos[0]->pronac;

            foreach ($listaDePagamentos as $pagmt) {
                $valorTotal = $pagmt->vlPagamento + $valorTotal;

                if ($pronac != $pagmt->pronac) {
                    $pr++;
                    $valorTotal = $pagmt->vlPagamento;
                }

                $dados[$pr]['idPronac']             = $pagmt->idpronac;
                $dados[$pr]['pronac']               = $pagmt->pronac;
                $dados[$pr]['NomeProjeto']          = $pagmt->NomeProjeto;
                $dados[$pr]['UnidadeAnalise']       = $pagmt->Vinculada;
                $dados[$pr]['vlPagamento']          = $valorTotal;

                $pronac = $pagmt->pronac;
            }

            $despachos[$d]['Projetos'] = $dados;

            $d++;
        }

        $this->view->paginacao         = $paginacao;
        $this->view->qtd               = $total;
        $this->view->dados             = $despachos;
        $this->view->intTamPag         = $this->intTamPag;
    }

    /**
     * Método confirmarPagamentoParecerista()
     * Confirmar pagamento de parecerista
     * @access public
     * @param void
     * @return void
     */
    public function confirmarPagamentoPareceristaAction()
    {
        $auth = Zend_Auth::getInstance();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();
        $idGerarPagamentoParecerista = $this->_request->getParam('idGerarPagamentoParecerista');

        try {

            // Mudar a situação do pagamento para 3 = Registrar ordem bancária
            $dados = array('siPagamento'            => 3,
                           'idUsuario'              => $auth->getIdentity()->usu_codigo);

            $modelGerarPagamentoParecerista->update($dados, array('idGerarPagamentoParecerista = ?' => $idGerarPagamentoParecerista));

            parent::message('Pagamento confirmado com sucesso!', 'parecerista/solicitar-pagamento-parecerista', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao confirmar o pagamento! '.$exc->getMessage(), 'parecerista/solicitar-pagamento-parecerista', 'ERROR');
        }
    }

    public function cancelarRegistroOrdemBancariaAction()
    {
        $auth = Zend_Auth::getInstance();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $idGerarPagamentoParecerista = $this->_request->getParam('idGerarPagamentoParecerista');

        try {

            // Mudar a situação do pagamento para 1
            $dados = array('siPagamento'            => 1,
                           'idUsuario'              => $auth->getIdentity()->usu_codigo);

            $modelGerarPagamentoParecerista->update($dados, array('idGerarPagamentoParecerista = ?' => $idGerarPagamentoParecerista));

            parent::message('Cancelamento de ordem bancária realizado com sucesso!', 'parecerista/registrar-ordem-bancaria', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao cancelar a ordem bancária! '.$exc->getMessage(), 'parecerista/registrar-ordem-bancaria', 'ERROR');
        }
    }

    /**
     * Método registrarOrdemBancaria()
     * Registrar ordem bancária
     * @access public
     * @param void
     * @return void
     */
    public function registrarOrdemBancariaAction()
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
            $order = array(7); //Coluna NrDespacho
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/

        $DadosGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $total = $DadosGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 3), $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $DadosGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 3), $order, $tamanho, $inicio);

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

        $dadosPagarParecerista = new PagarParecerista();

        $despachos = array();

        $d = 0;

        foreach ($busca as $de) {

            $despachos[$d]['idGerarPagamentoParecerista']   = $de->idGerarPagamentoParecerista;
            $despachos[$d]['idConfigurarPagamento']         = $de->idConfigurarPagamento;
            $despachos[$d]['dtGeracaoPagamento']            = $de->dtGeracaoPagamento;
            $despachos[$d]['anoGeracaoPagamento']           = substr($de->dtGeracaoPagamento, -4);
            $despachos[$d]['dtEfetivacaoPagamento']         = $de->dtEfetivacaoPagamento;
            $despachos[$d]['dtOrdemBancaria']               = $de->dtOrdemBancaria;
            $despachos[$d]['nrOrdemBancaria']               = $de->nrOrdemBancaria;
            $despachos[$d]['nrDespacho']                    = $de->nrDespacho;
            $despachos[$d]['vlTotalPagamento']              = $de->vlTotalPagamento;
            $despachos[$d]['siPagamento']                   = $de->siPagamento;
            $despachos[$d]['nmParecerista']                 = $de->nmParecerista;

            $listaDePagamentos = $dadosPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

            $despachos[$d]['idParecerista'] = $listaDePagamentos[0]->idParecerista;

            $dados = array();
            $pr = 0;
            $valorTotal = 0;
            $pronac = $listaDePagamentos[0]->pronac;

            foreach ($listaDePagamentos as $pagmt) {
                $valorTotal = $pagmt->vlPagamento + $valorTotal;

                if ($pronac != $pagmt->pronac) {
                    $pr++;
                    $valorTotal = $pagmt->vlPagamento;
                }

                $dados[$pr]['idPronac']             = $pagmt->idpronac;
                $dados[$pr]['pronac']               = $pagmt->pronac;
                $dados[$pr]['NomeProjeto']          = $pagmt->NomeProjeto;
                $dados[$pr]['UnidadeAnalise']       = $pagmt->Vinculada;
                $dados[$pr]['vlPagamento']          = $valorTotal;

                $pronac = $pagmt->pronac;
            }

            $despachos[$d]['Projetos'] = $dados;

            $d++;
        }

        $this->view->paginacao         = $paginacao;
        $this->view->qtd               = $total;
        $this->view->dados             = $despachos;
        $this->view->intTamPag         = $this->intTamPag;
    }

    /**
     * Método finalizarPagamentoParecerista()
     * Finalizar os pagamentos dos Pareceristas
     * @access public
     * @param void
     * @return void
     */
    public function finalizarPagamentoPareceristaAction()
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
            $order = array(7); //Coluna NrDespacho
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/

        $DadosGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $total = $DadosGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 5), $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $DadosGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 5), $order, $tamanho, $inicio);

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

        $dadosPagarParecerista          = new PagarParecerista();
        $arquivoPagamentoParecerista    = new ArquivoPagamentoParecerista();

        $despachos = array();

        $d = 0;

        foreach ($busca as $de) {
            $despachos[$d]['idGerarPagamentoParecerista']   = $de->idGerarPagamentoParecerista;
            $despachos[$d]['idConfigurarPagamento']         = $de->idConfigurarPagamento;
            $despachos[$d]['dtGeracaoPagamento']            = $de->dtGeracaoPagamento;
            $despachos[$d]['dtEfetivacaoPagamento']         = $de->dtEfetivacaoPagamento;
            $despachos[$d]['dtOrdemBancaria']               = $de->dtOrdemBancaria;
            $despachos[$d]['nrOrdemBancaria']               = $de->nrOrdemBancaria;
            $despachos[$d]['nrDespacho']                    = $de->nrDespacho;
            $despachos[$d]['vlTotalPagamento']              = $de->vlTotalPagamento;
            $despachos[$d]['siPagamento']                   = $de->siPagamento;
            $despachos[$d]['nmParecerista']                 = $de->nmParecerista;

            $listaDePagamentos = $dadosPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

            $despachos[$d]['idParecerista'] = $listaDePagamentos[0]->idParecerista;

            $dados = array();
            $pr = 0;
            $valorTotal = 0;
            $pronac = $listaDePagamentos[0]->pronac;

            foreach ($listaDePagamentos as $pagmt) {
                $valorTotal = $pagmt->vlPagamento + $valorTotal;

                if ($pronac != $pagmt->pronac) {
                    $pr++;
                    $valorTotal = $pagmt->vlPagamento;
                }

                $dados[$pr]['idPronac']             = $pagmt->idpronac;
                $dados[$pr]['pronac']               = $pagmt->pronac;
                $dados[$pr]['NomeProjeto']          = $pagmt->NomeProjeto;
                $dados[$pr]['UnidadeAnalise']       = $pagmt->Vinculada;
                $dados[$pr]['vlPagamento']          = $valorTotal;

                $pronac = $pagmt->pronac;
            }

            $despachos[$d]['Projetos'] = $dados;

            $arquivos = $arquivoPagamentoParecerista->buscarArquivo(array('arqpa.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

            $despachos[$d]['Arquivos'] = $arquivos;

            $d++;
        }

        $this->view->paginacao         = $paginacao;
        $this->view->qtd               = $total;
        $this->view->dados             = $despachos;
        $this->view->intTamPag         = $this->intTamPag;
    }

    /**
     * Método gerarDespachoPagamentoParecerista()
     * Gerar despacho de pagamento de Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function gerarDespachoPagamentoPareceristaAction()
    {
        $auth = Zend_Auth::getInstance();
        $modelPagarParecerista = new PagarParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $idParecerista  = $this->_request->getParam('idParecerista');
        $vlTotal        = $this->_request->getParam('vlTotal');
        $nrDespacho     = $this->_request->getParam('nrDespacho');

        try {

            // Gerar Pagamento Parecerista
            $dados = array('nrDespacho'         => $nrDespacho,
                           'vlTotalPagamento'   => $vlTotal,
                           'siPagamento'        => 1,
                           'dtGeracaoPagamento' => new Zend_Db_Expr('GETDATE()'),
                           'idUsuario'          => $auth->getIdentity()->usu_codigo
            );

            $id = $modelGerarPagamentoParecerista->inserir($dados);

            // Atualiza Pagar Parecerista
            $listaDePagamentos = $modelPagarParecerista->buscarPagamentos(array('idGerarPagamentoParecerista IS NULL' => null, 'idParecerista = ?' => $idParecerista));

            foreach ($listaDePagamentos as $pag) {
                $modelPagarParecerista->update(array('idGerarPagamentoParecerista' => $id), array('idPagarParecerista = ?' => $pag->idPagarParecerista));
            }
        } catch (Exception $exc) {
            parent::message('Error: '.$exc->getMessage(), 'parecerista/gerar-pagamento-parecerista', 'ERROR');
        }

        parent::message('Despacho gerado com sucesso!', 'parecerista/despacho-pagamento-parecerista/despacho/'.$id, 'CONFIRM');
    }

    /**
     * Método despachoPagamentoParecerista()
     * Buscar despacho de pagamento de Parecerista
     * @access public
     * @param void
     * @return list
     */
    public function despachoPagamentoPareceristaAction()
    {
        $nrDespacho = $this->_request->getParam('despacho');
        $modelPagarParecerista          = new PagarParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $listaDespachos = $modelGerarPagamentoParecerista->buscarDespachos(array('gpp.idGerarPagamentoParecerista = ?' => $nrDespacho));

        $despachos = array();

        $d = 0;
        foreach ($listaDespachos as $de) {
            $despachos[$d]['idGerarPagamentoParecerista']   = $de->idGerarPagamentoParecerista;
            $despachos[$d]['dtGeracaoPagamento']            = $de->dtGeracaoPagamento;
            $despachos[$d]['dtEfetivacaoPagamento']         = $de->dtEfetivacaoPagamento;
            $despachos[$d]['dtOrdemBancaria']               = $de->dtOrdemBancaria;
            $despachos[$d]['nrOrdemBancaria']               = $de->nrOrdemBancaria;
            $despachos[$d]['nrDespacho']                    = $de->nrDespacho;
            $despachos[$d]['vlTotalPagamento']              = $de->vlTotalPagamento;
            $despachos[$d]['siPagamento']                   = $de->siPagamento;

            $listaDePagamentos = $modelPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

            $despachos[$d]['idParecerista'] = $listaDePagamentos[0]->idParecerista;
            $despachos[$d]['nmParecerista'] = $listaDePagamentos[0]->nmParecerista;
            $despachos[$d]['cpfParecerista'] = $listaDePagamentos[0]->CNPJCPF;

            $dados = array();
            $pr = 0;
            $valorTotal = 0;
            $pronac = $listaDePagamentos[0]->pronac;
            foreach ($listaDePagamentos as $pag) {
                $valorTotal = $pag->vlPagamento + $valorTotal;

                if ($pronac != $pag->pronac) {
                    $pr++;
                    $valorTotal = $pag->vlPagamento;
                }

                $dados[$pr]['idPronac']             = $pag->idpronac;
                $dados[$pr]['pronac']               = $pag->pronac;
                $dados[$pr]['NomeProjeto']          = $pag->NomeProjeto;
                $dados[$pr]['UnidadeAnalise']       = $pag->Vinculada;
                $dados[$pr]['vlPagamento']          = $valorTotal;

                $pronac = $pag->pronac;
            }

            $despachos[$d]['Projetos'] = $dados;
            $d++;
        }

        $this->view->assign('listaDePagamentos', $despachos);
    }

    /**
     * Método imprimirDespacho()
     * Imprimir despacho de pagamento de Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function imprimirDespachoAction()
    {
        $this->_helper->layout->disableLayout();

        $nrDespacho = $this->_request->getParam('despacho');
        $modelPagarParecerista          = new PagarParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();
        $modeltbConfigurarPagamentoXtbAssinantes = new tbConfigurarPagamentoXtbAssinantes();

        $listaDespachos = $modelGerarPagamentoParecerista->buscarDespachos(array('gpp.idGerarPagamentoParecerista = ?' => $nrDespacho));

        // Envia todos os assinantes selecionados
        $assinantesConfigurados = $modeltbConfigurarPagamentoXtbAssinantes->assinantesConfigurados(
            array(
                'a.idConfigurarPagamento = ?' => $listaDespachos[0]->idConfigurarPagamento
            )
        );

        $this->view->assign('assinantesConfigurados', $assinantesConfigurados);

        $despachos = array();

        $d = 0;
        foreach ($listaDespachos as $de) {

            $despachos[$d]['idGerarPagamentoParecerista']   = $de->idGerarPagamentoParecerista;
            $despachos[$d]['dtGeracaoPagamento']            = $de->dtGeracaoPagamento;
            $despachos[$d]['anoGeracaoPagamento']           = substr($de->dtGeracaoPagamento, -4);
            $despachos[$d]['dtEfetivacaoPagamento']         = $de->dtEfetivacaoPagamento;
            $despachos[$d]['dtOrdemBancaria']               = $de->dtOrdemBancaria;
            $despachos[$d]['nrOrdemBancaria']               = $de->nrOrdemBancaria;
            $despachos[$d]['nrDespacho']                    = $de->nrDespacho;
            $despachos[$d]['vlTotalPagamento']              = $de->vlTotalPagamento;
            $despachos[$d]['siPagamento']                   = $de->siPagamento;

            $listaDePagamentos = $modelPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

            $despachos[$d]['idParecerista'] = $listaDePagamentos[0]->idParecerista;
            $despachos[$d]['nmParecerista'] = $listaDePagamentos[0]->nmParecerista;
            $despachos[$d]['cpfParecerista'] = $listaDePagamentos[0]->CNPJCPF;
            if ($listaDePagamentos[0]->nrIdentificadorProcessual) {
                $despachos[$d]['nrIdentificadorProcessual'] = Mascara::addMaskProcesso($listaDePagamentos[0]->nrIdentificadorProcessual);
            } else {
                $despachos[$d]['nrIdentificadorProcessual'] = '';
            }

            $dados = array();
            $pr = 0;
            $valorTotal = 0;
            $pronac = $listaDePagamentos[0]->pronac;
            foreach ($listaDePagamentos as $pag) {
                $valorTotal = $pag->vlPagamento + $valorTotal;

                if ($pronac != $pag->pronac) {
                    $pr++;
                    $valorTotal = $pag->vlPagamento;
                }

                $dados[$pr]['idPronac']             = $pag->idpronac;
                $dados[$pr]['pronac']               = $pag->pronac;
                $dados[$pr]['NomeProjeto']          = $pag->NomeProjeto;
                $dados[$pr]['UnidadeAnalise']       = $pag->Vinculada;
                $dados[$pr]['vlPagamento']          = $valorTotal;

                $pronac = $pag->pronac;
            }

            $despachos[$d]['Projetos'] = $dados;
            $d++;
        }

        $this->view->assign('listaDePagamentos', $despachos);
        $this->view->processo = $despachos[0]['nrIdentificadorProcessual'];
        $this->view->pareceristaNome = $despachos[0]['nmParecerista'];
        $this->view->pareceristaCpf = $despachos[0]['cpfParecerista'];
        $this->view->projetoValor = round($despachos[0]['vlTotalPagamento'], 1);
        $this->view->projetoValorExtenso = Currency::numberToWord(round($despachos[0]['vlTotalPagamento'], 1));
        $this->view->dataCompetencia = new DateTime(date('m/d/Y', mktime(
             null,
            null,
            null,
             substr($despachos[0]['dtGeracaoPagamento'], 3, 2),
             substr($despachos[0]['dtGeracaoPagamento'], 0, 2),
             substr($despachos[0]['dtGeracaoPagamento'], 6, 4)
        )));
    }

    /**
     * Método efetivarPagamentoParecerista()
     * Efetivar Pagamentos de Pareceristas
     * @access public
     * @param void
     * @return void
     */
    public function efetivarPagamentoPareceristaAction()
    {
        $modelPagarParecerista          = new PagarParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $listaDespachos = $modelGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 1));

        $despachos = array();

        $d = 0;
        foreach ($listaDespachos as $de) {
            $despachos[$d]['idGerarPagamentoParecerista']   = $de->idGerarPagamentoParecerista;
            $despachos[$d]['dtGeracaoPagamento']            = $de->dtGeracaoPagamento;
            $despachos[$d]['dtEfetivacaoPagamento']         = $de->dtEfetivacaoPagamento;
            $despachos[$d]['dtOrdemBancaria']               = $de->dtOrdemBancaria;
            $despachos[$d]['nrOrdemBancaria']               = $de->nrOrdemBancaria;
            $despachos[$d]['nrDespacho']                    = $de->nrDespacho;
            $despachos[$d]['vlTotalPagamento']              = $de->vlTotalPagamento;
            $despachos[$d]['siPagamento']                   = $de->siPagamento;

            $listaDePagamentos = $modelPagarParecerista->buscarPagamentos(array('pp.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

            $despachos[$d]['idParecerista'] = $listaDePagamentos[0]->idParecerista;
            $despachos[$d]['nmParecerista'] = $listaDePagamentos[0]->nmParecerista;

            $dados = array();
            $pr = 0;
            $valorTotal = 0;
            $pronac = $listaDePagamentos[0]->pronac;
            foreach ($listaDePagamentos as $pag) {
                $valorTotal = $pag->vlPagamento + $valorTotal;

                if ($pronac != $pag->pronac) {
                    $pr++;
                    $valorTotal = $pag->vlPagamento;
                }

                $dados[$pr]['idPronac']             = $pag->idpronac;
                $dados[$pr]['pronac']               = $pag->pronac;
                $dados[$pr]['NomeProjeto']          = $pag->NomeProjeto;
                $dados[$pr]['UnidadeAnalise']       = $pag->Vinculada;
                $dados[$pr]['vlPagamento']          = $valorTotal;

                $pronac = $pag->pronac;
            }

            $despachos[$d]['Projetos'] = $dados;

            $d++;
        }

        $this->view->assign('listaDePagamentos', $despachos);
    }

    /**
     * Método efetivouDespachoPagamentoParecerista()
     * Efetivou Despacho Pagamento de Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function efetivouDespachoPagamentoPareceristaAction()
    {
        $auth = Zend_Auth::getInstance();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $idGerarPagamentoParecerista    = $this->_request->getParam('idGerarPagamentoParecerista');
        $idAgente                       = $this->_request->getParam('idAgente');
        $nrOrdemBancaria                = $this->_request->getParam('nrOrdemBancaria');
        $dtOrdemBancariaValidar         = $this->_request->getParam('dtOrdemBancaria');
        $dtOrdemBancaria                = implode("-", array_reverse(explode("/", $this->_request->getParam('dtOrdemBancaria'))));

        try {
            if (!empty($_FILES['arquivo']['tmp_name'])) {
                $arquivoNome     = $_FILES['arquivo']['name']; // nome
                $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome temporário
                $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho

                if (!empty($arquivoNome)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
                }

                if (!Validacao::validarData($dtOrdemBancariaValidar)) {
                    parent::message("A data informada não e válida!", "parecerista/registrar-ordem-bancaria", "ALERT");
                }

                if (!isset($_FILES['arquivo'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "parecerista/registrar-ordem-bancaria", "ALERT");
                }

                if (empty($_FILES['arquivo']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo RPA.", "parecerista/registrar-ordem-bancaria", "ALERT");
                }

                $tipos = array('jpeg','jpg','png','pdf');

                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Marca no formato JPEG, JPG, PNG ou PDF!", "parecerista/registrar-ordem-bancaria", "ALERT");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // cadastra dados do arquivo
                $dadosArquivo = array('nmArquivo'               => $arquivoNome,
                                      'sgExtensao'              => $arquivoExtensao,
                                      'nrTamanho'               => $arquivoTamanho,
                                      'stAtivo'                 => 'A',
                                      'biArquivo'               => $data,
                                      'idDocumento'             => null,
                                      'idTipoDocumento'         => 36,
                                      'dsDocumento'             => 'RPA Enviado pelo Coordenador de Pronac',
                                      'idAgente'                => $idAgente,
                                      'stAtivoDocumentoAgente'  => 1
                );

                $vwAnexarDocumentoAgente = new vwAnexarDocumentoAgente();

                $vwAnexarDocumentoAgente->inserirUploads($dadosArquivo);

                $ultimoArquivo = ArquivoDAO::buscarIdArquivo();
                $idUltimoArquivo = (int) $ultimoArquivo[0]->id;

                $arquivoParecerista = new ArquivoPagamentoParecerista();

                $arquivoParecerista->inserirArquivodePagamento($idGerarPagamentoParecerista, $idUltimoArquivo, 1);

                // Mudar a situação do pagamento para 2 = pagamento efetivado
                $dados = array('dtEfetivacaoPagamento'  => new Zend_Db_Expr('getDate()'),
                               'dtOrdemBancaria'        => $dtOrdemBancaria,
                               'nrOrdemBancaria'        => $nrOrdemBancaria,
                               'siPagamento'            => 4,
                               'idUsuario'              => $auth->getIdentity()->usu_codigo);

                $modelGerarPagamentoParecerista->update($dados, array('idGerarPagamentoParecerista = ?' => $idGerarPagamentoParecerista));

                parent::message('Ordem bancária registrada com sucesso!', 'parecerista/registrar-ordem-bancaria', 'CONFIRM');
            } else {
                parent::message('Arquivo não anexado.', 'parecerista/registrar-ordem-bancaria', 'ALERT');
            }
        } catch (Exception $exc) {
            parent::message('Erro ao registrar ordem bancária! '.$exc->getMessage(), 'parecerista/registrar-ordem-bancaria', 'ERROR');
        }
    }

    /**
     * Método finalizarDespachoPagamentoParecerista()
     * Finalizou Despacho Pagamento de Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function finalizarDespachoPagamentoPareceristaAction()
    {
        $auth = Zend_Auth::getInstance();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $idGerarPagamentoParecerista = $this->_request->getParam('idGerarPagamentoParecerista');

        try {

            // Mudar a situação do pagamento para 7 = pagamento finalizado
            $dados = array('siPagamento'            => 7,
                           'idUsuario'              => $auth->getIdentity()->usu_codigo);

            $modelGerarPagamentoParecerista->update($dados, array('idGerarPagamentoParecerista = ?' => $idGerarPagamentoParecerista));

            parent::message('Pagamento finalizado com sucesso!', 'parecerista/finalizar-pagamento-parecerista', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao finalizar o pagamento! '.$exc->getMessage(), 'parecerista/finalizar-pagamento-parecerista', 'ERROR');
        }
    }

    /**
     * Método assinouRpaParecerista()
     * Assinou RPA do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function assinouRpaPareceristaAction()
    {
        $auth = Zend_Auth::getInstance();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();

        $idGerarPagamentoParecerista = $this->_request->getParam('idGerarPagamentoParecerista');
        $idAgente                    = $this->_request->getParam('idAgente');

        try {
            if (!empty($_FILES['arquivo']['tmp_name'])) {
                $arquivoNome     = $_FILES['arquivo']['name']; // nome
                $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome temporário
                $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho

                if (!empty($arquivoNome)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
                }

                if (!isset($_FILES['arquivo'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "parecerista/confirmacao-pagamento-parecerista", "ALERT");
                }

                if (empty($_FILES['arquivo']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo RPA.", "parecerista/confirmacao-pagamento-parecerista", "ALERT");
                }

                $tipos = array('jpeg','jpg','png','pdf');

                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Marca no formato JPEG, JPG, PNG ou PDF!", "parecerista/confirmacao-pagamento-parecerista", "ALERT");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // cadastra dados do arquivo
                $dadosArquivo = array('nmArquivo'               => $arquivoNome,
                                      'sgExtensao'              => $arquivoExtensao,
                                      'nrTamanho'               => $arquivoTamanho,
                                      'stAtivo'                 => 'A',
                                      'biArquivo'               => $data,
                                      'idDocumento'             => null,
                                      'idTipoDocumento'         => 36,
                                      'dsDocumento'             => 'RPA Assinado Enviado pelo Parecerista',
                                      'idAgente'                => $idAgente,
                                      'stAtivoDocumentoAgente'  => 1
                );

                $vwAnexarDocumentoAgente = new vwAnexarDocumentoAgente();

                $vwAnexarDocumentoAgente->inserirUploads($dadosArquivo);

                $ultimoArquivo = ArquivoDAO::buscarIdArquivo();
                $idUltimoArquivo = (int) $ultimoArquivo[0]->id;

                $arquivoParecerista = new ArquivoPagamentoParecerista();

                $arquivoParecerista->inserirArquivodePagamento($idGerarPagamentoParecerista, $idUltimoArquivo, 2);

                // Mudar a situação do pagamento para 5 = pagamento confirmado pelo parecerista
                $dados = array('siPagamento'            => 5,
                               'idUsuario'              => $auth->getIdentity()->usu_codigo);

                $modelGerarPagamentoParecerista->update($dados, array('idGerarPagamentoParecerista = ?' => $idGerarPagamentoParecerista));

                parent::message('Pagamento confirmado com sucesso!', 'parecerista/confirmacao-pagamento-parecerista', 'CONFIRM');
            } else {
                parent::message('É preciso fazer o Upload do RPA assinado!', 'parecerista/confirmacao-pagamento-parecerista', 'ALERT');
            }
        } catch (Exception $exc) {
            parent::message('Erro ao confirmar o pagamento! '.$exc->getMessage(), 'parecerista/confirmacao-pagamento-parecerista', 'ERROR');
        }
    }

    /**
     * Método confirmacaoPagamentoParecerista()
     * Confirmacao de pagamento do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function confirmacaoPagamentoPareceristaAction()
    {
        $modelPagarParecerista          = new PagarParecerista();
        $modelGerarPagamentoParecerista = new GerarPagamentoParecerista();
        $arquivoPagamentoParecerista    = new ArquivoPagamentoParecerista();
        $auth = Zend_Auth::getInstance();
        $idAgente = 0;

        if (isset($auth->getIdentity()->usu_codigo)) {
            $Usuario      = new Autenticacao_Model_Usuario(); // objeto usuário
            $Agente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
            $idAgente = $Agente['idagente'];
            $this->view->idAgente    = $idAgente;
        }

        $listaDespachos = $modelGerarPagamentoParecerista->buscarDespachos(array('gpp.siPagamento = ?' => 4));

        $despachos = array();
        $arquivos  = array();

        $d = 0;
        foreach ($listaDespachos as $de) {
            $where = array('pp.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista,
                            'pp.idParecerista = ?' => $idAgente);

            $listaDePagamentos = $modelPagarParecerista->buscarPagamentos($where);

            if (count($listaDePagamentos) > 0) {
                $despachos[$d]['idParecerista'] = $listaDePagamentos[0]->idParecerista;
                $despachos[$d]['nmParecerista'] = $listaDePagamentos[0]->nmParecerista;

                $despachos[$d]['idGerarPagamentoParecerista']   = $de->idGerarPagamentoParecerista;
                $despachos[$d]['dtGeracaoPagamento']            = $de->dtGeracaoPagamento;
                $despachos[$d]['dtEfetivacaoPagamento']         = $de->dtEfetivacaoPagamento;
                $despachos[$d]['dtOrdemBancaria']               = $de->dtOrdemBancaria;
                $despachos[$d]['nrOrdemBancaria']               = $de->nrOrdemBancaria;
                $despachos[$d]['nrDespacho']                    = $de->nrDespacho;
                $despachos[$d]['vlTotalPagamento']              = $de->vlTotalPagamento;
                $despachos[$d]['siPagamento']                   = $de->siPagamento;


                $dados = array();
                $pr = 0;
                $valorTotal = 0;
                $pronac = $listaDePagamentos[0]->pronac;
                foreach ($listaDePagamentos as $pag) {
                    $valorTotal = $pag->vlPagamento + $valorTotal;

                    if ($pronac != $pag->pronac) {
                        $pr++;
                        $valorTotal = $pag->vlPagamento;
                    }

                    $dados[$pr]['idPronac']             = $pag->idpronac;
                    $dados[$pr]['pronac']               = $pag->pronac;
                    $dados[$pr]['NomeProjeto']          = $pag->NomeProjeto;
                    $dados[$pr]['UnidadeAnalise']       = $pag->Vinculada;
                    $dados[$pr]['vlPagamento']          = $valorTotal;

                    $pronac = $pag->pronac;
                }

                $despachos[$d]['Projetos'] = $dados;

                $arquivos = $arquivoPagamentoParecerista->buscarArquivo(array('arqpa.idGerarPagamentoParecerista = ?' => $de->idGerarPagamentoParecerista));

                $despachos[$d]['Arquivos'] = $arquivos;

                $d++;
            }
        }

        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator          = Zend_Paginator::factory($despachos); // dados a serem paginados
        $currentPage        = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10);

        $this->view->listaDePagamentos      = $paginator;
        $this->view->qtdDePagamentos        = count($despachos); // quantidade
    }

    /**
     * Método abrirarquivo()
     * Abrir arquivo em binário
     * @access public
     * @param void
     * @return void
     */
    public function abrirArquivoAction()
    {
        $id = $this->_request->getParam("id");

        // Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        // busca o arquivo
        $arquivoPagamentoParecerista    = new ArquivoPagamentoParecerista();
        $resultado = $arquivoPagamentoParecerista->buscarArquivo(array('arq.idArquivo = ?' => $id));

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->view->message = 'Não foi possível abrir o arquivo!';
            $this->view->message_type = 'ERROR';
            die("N&atilde;o existe o arquivo especificado");
        } else {
            // lê os cabeçalhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $this->getResponse()
                    ->setHeader('Content-Type', $r->dsTipoPadronizado)
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                    ->setHeader("Connection", "close")
                    ->setHeader("Content-transfer-encoding", "binary")
                    ->setHeader("Cache-control", "private")
                    ->setBody($r->biArquivo);
            }
        }
    }

    /**
     * Método gerenciarAssinantes()
     * Gerenciar Assinantes
     * @access public
     * @param void
     * @return list
     */
    public function gerenciarAssinantesAction()
    {
        $modelAssinantes = new tbAssinantes();
        $assinantes = $modelAssinantes->listarAssinantes(array('stEstado = ?' => 1));
        $this->view->assign('listaAssinantes', $assinantes);
    }

    /**
     * Método novoAssinante()
     * Adicionar Assinante
     * @access public
     * @param void
     * @return void
     */
    public function novoAssinanteAction()
    {
        $modelAssinantes = new tbAssinantes();

        $listaNaoAssinantes = $modelAssinantes->listarNaoAssinantes();
        $this->view->assign('listaNaoAssinantes', $listaNaoAssinantes);

        $listaCargos        = $modelAssinantes->listarCargos();
        $this->view->assign('listaCargos', $listaCargos);
    }

    /**
     * Método addNovoAssinante()
     * Adicionou novo Assinantes
     * @access public
     * @param void
     * @return void
     */
    public function addNovoAssinanteAction()
    {
        $modelAssinantes = new tbAssinantes();

        $assinante  = $this->_request->getParam('assinante');
        $cargo      = $this->_request->getParam('cargo');

        try {
            $dados = array('idOrgao'    => 251,
                           'idAgente'   => $assinante,
                           'idCargo'    => $cargo
            );

            // Verificar se já existe o mesmo cadastrado com o mesmo cargo!
            $verificacao = $modelAssinantes->buscar(array('idAgente = ?'=> $assinante, 'idCargo = ?' => $cargo));

            if (count($verificacao) > 0 && $verificacao[0]->stEstado == 1) {
                parent::message('Assinante já cadastrado com esse cargo! ', 'parecerista/novo-assinante', 'ALERT');
            } elseif (count($verificacao) > 0 && $verificacao[0]->stEstado == 0) {
                $modelAssinantes->update(array('stEstado' => 1), array('idAssinantes = ?'=> $verificacao[0]->idAssinantes));
            } else {
                $modelAssinantes->inserir($dados, false);
            }


            parent::message('Assinante cadastrado com sucesso!', 'parecerista/novo-assinante', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao cadastrar o assinante.! '.$exc->getMessage(), 'parecerista/novo-assinante', 'ERROR');
        }
    }

    /**
     * Método removeAssinante()
     * Remover assinante
     * @access public
     * @param void
     * @return void
     */
    public function removeAssinanteAction()
    {
        $modelAssinantes = new tbAssinantes();

        $idAssinantes  = $this->_request->getParam('idAssinantes');

        try {
            $modelAssinantes->update(array('stEstado' => 0), array('idAssinantes = ?'=> $idAssinantes));

            parent::message('Assinante desabilitado com sucesso!', 'parecerista/gerenciar-assinantes', 'CONFIRM');
        } catch (Exception $exc) {
            parent::message('Erro ao desabilitar o assinante.! '.$exc->getMessage(), 'parecerista/gerenciar-assinantes', 'ERROR');
        }
    }

    /**
     * Método atualizaListaAssinantes()
     * atualiza a lista de assinantes
     * @access public
     * @param void
     * @return list
     */
    public function atualizaListaAssinantesAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $this->_helper->viewRenderer->setNoRender();

        $modelAssinantes = new tbConfigurarPagamentoXtbAssinantes();
        $idConfigurarPagamento = $this->_request->getParam('idConfigurarPagamento');

        $stringUsuarioId   = str_replace("&", "", $this->_request->getParam('usuarioId'));
        $arrayUsuarioId     = array_slice(explode("usuarioId[]=", $stringUsuarioId), 1);
        $count = 1;
        foreach ($arrayUsuarioId as $usuarioId) {
            $where = array('idConfigurarPagamento = ?' => $idConfigurarPagamento, 'idAssinantes = ?' => $usuarioId);

            $modelAssinantes->update(array('nrOrdenacao' => $count), $where);

            $count++;
        }

        echo '&nbsp;';
    }
}
