<?php

/**
  @todo remover essa controller e codigo e enviar para o ver salic
 */
class CidadaoController extends MinC_Controller_Action_Abstract {

    private $blnProponente = false;
    private $blnProcurador = false;
    private $intFaseProjeto = 0;
    private $intTamPag = 10;
    protected $cpfLogado = 0;
    protected $idResponsavel = 0;
    protected $idAgente = 0;
    private $bln_readequacao = "false";
    private $idPreProjeto = 0;

    public function init() {
        parent::init(); // chama o init() do pai GenericControllerNew
        $this->intTamPag = 10;
        $this->usuarioInterno = false;
        $this->view->usuarioInterno = false;

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        if (isset($auth->getIdentity()->usu_codigo)) {

            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usu�rio
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }

        if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
            parent::perfil(1, $PermissoesGrupo);
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
            $this->usuarioInterno = true;
            $this->view->usuarioInterno = true;
        }
        parent::init();
    }

    public function indexAction() {
        $this->redirect('/cidadao/consultar');
    }

    public function consultarAction() {

        #Pedro - Criando a variavel de Sessao para Usar na impressao PDF
        $sess = new Zend_Session_Namespace('Filtro_de_Pesquisa');

        if (!$this->usuarioInterno) {
            //Zend_Layout::startMvc(array('layout' => 'layout_login'));
            Zend_Layout::startMvc(array('layout' => 'open'));
        }

        $idNrReuniaoConsulta = $this->_request->getParam("idNrReuniaoConsulta") ? $this->_request->getParam("idNrReuniaoConsulta") : null;
        $reuniao = new Reuniao();
        //Alysson - Na Primeira Consulta exibe dados da ultima reuniao aberta
        if (!$idNrReuniaoConsulta) {
            $raberta = null;  // Fernao: permite Não filtrar
            $this->view->idNrReuniaoConsulta = null;
        } else {
            $raberta = $reuniao->buscarReuniaoPorId($idNrReuniaoConsulta); //idNrReuniao
            $this->view->idNrReuniaoConsulta = $raberta->idNrReuniao;
        }
        $this->view->reuniao = $raberta;

        $order_reuniao = array("NrReuniao DESC");
        $this->view->listaReunioes = $reuniao->buscarTodasReunioes($order_reuniao);
        $order = array();

        $urlComplement = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "DESC") {
                $novaOrdem = "ASC";
            } else {
                $novaOrdem = "DESC";
            }
        } else {
            $ordem = "DESC";
            $novaOrdem = "DESC";
        }

        $sess->novaOrdem = $novaOrdem;
        $sess->ordem = $ordem;

        if ($this->_request->getParam("pag")) {
            $pag = $this->_request->getParam("pag");
            $urlComplement[] = "pag=" . $pag;
            $sess->pag = $pag;
        }
        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $ordenacao = "&campo=" . $campo;
            $urlComplement[] = "campo=$campo";
            $urlComplement[] = "ordem=$ordem";
            $sess->campo = $campo;
        } else {
            $campo = 2;
            $ordem = 'DESC';
            $ordenacao = null;
            $urlComplement[] = "ordem=" . $ordem;
            $urlComplement[] = "campo=" . $campo;
            $sess->campo = $campo;
            $sess->ordem = $ordem;
        }

        $order = array("$campo $ordem");

        /* ================== PAGINACAO ====================== */
        $where = array();
        $where["stAtivo = ?"] = 1;

        // Fernao: adicionando filtros
        if ($this->_request->getParam("NrPronacConsulta")) {
            $nrPronac = $this->_request->getParam("NrPronacConsulta");
            $sess->nrPronac = $nrPronac;
            $where["p.AnoProjeto+p.Sequencial = ?"] = $nrPronac;
            $this->view->nrPronac = $nrPronac;
            $urlComplement[] = "NrPronac=$nrPronac";
        }
        if ($this->_request->getParam("CnpjCpfConsulta")) {
            $CnpjCpf = $this->_request->getParam("CnpjCpfConsulta");
            $CnpjCpf = str_replace(array('.', '/', '-'), '', $CnpjCpf);
            $where["x.CNPJCPF = ?"] = $CnpjCpf;
            $this->view->cnpjCpf = $CnpjCpf;
            $urlComplement[] = "CNPJCPF=$CnpjCpf";
            $sess->CnpjCpf = $CnpjCpf;
        }
        if ($this->_request->getParam("ProponenteConsulta")) {
            $ProponenteConsulta = $this->_request->getParam("ProponenteConsulta");
            $where["y.Descricao LIKE ?"] = "%" . $ProponenteConsulta . "%";
            $this->view->proponente = $ProponenteConsulta;
            $urlComplement[] = "ProponenteConsulta=$ProponenteConsulta";
            $sess->ProponenteConsulta = $ProponenteConsulta;
        }
        if ($this->_request->getParam("NomeProjetoConsulta")) {
            $NomeProjetoConsulta = $this->_request->getParam("NomeProjetoConsulta");
            $where["p.NomeProjeto LIKE ?"] = "%" . $NomeProjetoConsulta . "%";
            $this->view->nomeProjeto = $NomeProjetoConsulta;
            $urlComplement[] = "NomeProjetoConsulta=$NomeProjetoConsulta";
            $sess->NomeProjetoConsulta = $NomeProjetoConsulta;
        }

        $Projetos = new Projetos();

        if (!$idNrReuniaoConsulta) {
            $idNrReuniao = null;
        } else {
            $idNrReuniao = $raberta->idNrReuniao;
            $urlComplement[] = "idNrReuniaoConsulta=" . $idNrReuniao;
        }

        // PAGINAÇÂO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
            $urlComplement[] = 'qtde=' . $this->intTamPag;
            $sess->qtde = $this->intTamPag;
        }
        $pag = 1;
        $post = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $offset = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;
        $objTotal = $Projetos->countProjetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $ordem, false, false);
        $total = $objTotal['total'];
        $fim = $offset + $this->intTamPag;
        $totalPag = (int) (($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $limit = ($fim > $total) ? $total - $offset : $this->intTamPag;

        $paginacao = array(
            "pag" => $pag,
            "qtde" => $this->intTamPag,
            "campo" => $campo,
            "ordem" => $ordem,
            "ordenacao" => $ordenacao,
            "novaOrdem" => $novaOrdem,
            "total" => $total,
            "inicio" => ($offset + 1),
            "fim" => $fim,
            "totalPag" => $totalPag,
            "Itenspag" => $this->intTamPag,
            "tamanho" => $limit
        );

        $busca = $Projetos->projetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $order, $limit, $offset);

        // gera url completa com paginacao e vari�veis
        $strUrlComplement = '';   // campo texto vazio
        // se houver complementos
        if (!empty($urlComplement)) {
            $first = true;
            foreach ($urlComplement as $complement) {
                if ($first) {
                    $strUrlComplement .= '?' . $complement;
                    $first = false;
                } else {
                    $strUrlComplement .= '&' . $complement;
                }
            }
        }

        $this->view->urlComplement = $strUrlComplement;
        $this->view->paginacao = $paginacao;
        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->view->novaOrdem = $novaOrdem;
        $this->view->ordem = $ordem;
        $this->view->campo = $campo;
        $this->view->intTamPag = $this->intTamPag;

        $this->view->intranet = false;
        if (isset($_GET['intranet'])) {
            $this->view->intranet = true;
        }
    }

    public function imprimirListagemAction() {

        #Pedro - recuperando a Sessao Salva da pesquisa
        $sess = new Zend_Session_Namespace('Filtro_de_Pesquisa');

        $idNrReuniaoConsulta = $this->_request->getParam("idNrReuniaoConsulta") ? $this->_request->getParam("idNrReuniaoConsulta") : null;
        $reuniao = new Reuniao();

        if (!$idNrReuniaoConsulta) {
            $raberta = null;
            $this->view->idNrReuniaoConsulta = null;
        } else {
            $raberta = $reuniao->buscarReuniaoPorId($idNrReuniaoConsulta); //idNrReuniao
            $this->view->idNrReuniaoConsulta = $raberta->idNrReuniao;
        }
        $this->view->reuniao = $raberta;
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($sess->ordem) {
            $ordem = $sess->ordem;
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
        if ($sess->campo) {
            $campo = $sess->campo;
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            $campo = null;
            $order = array('2 DESC');
            $ordenacao = null;
        }

        $where["stAtivo = ?"] = 1;

        // Fernao: adicionando filtros
        if ($sess->nrPronac) {
            #$nrPronac = $this->_request->getParam("NrPronacConsulta");
            $nrPronac = $sess->nrPronac; #Pedro
            $where["p.AnoProjeto+p.Sequencial = ?"] = $nrPronac;
            $this->view->nrPronac = $nrPronac;
        }
        if ($sess->CnpjCpf) {
            $CnpjCpf = $sess->CnpjCpf;
            $CnpjCpf = str_replace(array('.', '/', '-'), '', $CnpjCpf);
            $where["x.CNPJCPF = ?"] = $CnpjCpf;
            $this->view->cnpjCpf = $CnpjCpf;
        }
        if ($sess->ProponenteConsulta) {
            $ProponenteConsulta = $sess->ProponenteConsulta;
            $where["y.Descricao LIKE ?"] = "%" . $ProponenteConsulta . "%";
            $this->view->proponente = $ProponenteConsulta;
        }
        if ($sess->NomeProjetoConsulta) {
            $NomeProjetoConsulta = $sess->NomeProjetoConsulta;
            $where["p.NomeProjeto LIKE ?"] = "%" . $NomeProjetoConsulta . "%";
            $this->view->nomeProjeto = $NomeProjetoConsulta;
        }

        $Projetos = new Projetos();

        if (!$idNrReuniaoConsulta) {
            $idNrReuniao = null;
        } else {
            $idNrReuniao = $raberta->idNrReuniao;
        }

        // PAGINAÇÂO
        if ($sess->qtde) {
            $this->intTamPag = $sess->qtde;
        }

        $objTotal = $Projetos->countProjetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $ordem, false, false);
        $total = $objTotal['total'];

        $pag = 1;
        $post = Zend_Registry::get('get');

        if (isset($sess->pag)) {
            $pag = $sess->pag;
        }
        $offset = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;
        $fim = $offset + $this->intTamPag;
        $totalPag = (int) (($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $limit = ($fim > $total) ? $total - $offset : $this->intTamPag;

        $busca = $Projetos->projetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $order, $limit, $offset);

        $this->view->dados = $busca;
        $this->view->novaOrdem = $sess->novaOrdem;
        $this->view->ordem = $sess->ordem;
        $this->view->campo = $sess->campo;

        $this->view->intranet = false;
        if (isset($_GET['intranet'])) {
            $this->view->intranet = true;
        }

        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function xlsListagemAction() {
        $idNrReuniaoConsulta = $this->_request->getParam("idNrReuniaoConsulta") ? $this->_request->getParam("idNrReuniaoConsulta") : null;
        $reuniao = new Reuniao();

        if (!$idNrReuniaoConsulta) {
            $raberta = null;
            $this->view->idNrReuniaoConsulta = null;
        } else {
            $raberta = $reuniao->buscarReuniaoPorId($idNrReuniaoConsulta); //idNrReuniao
            $this->view->idNrReuniaoConsulta = $raberta->idNrReuniao;
        }
        $this->view->reuniao = $raberta;
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
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            $campo = null;
            $order = array('2 DESC'); //Vl.Sugerido
            $ordenacao = null;
        }

        $where["stAtivo = ?"] = 1;

        // Fernao: adicionando filtros
        if ($this->_request->getParam("NrPronacConsulta")) {
            $nrPronac = $this->_request->getParam("NrPronacConsulta");
            $where["p.AnoProjeto+p.Sequencial = ?"] = $nrPronac;
        }
        if ($this->_request->getParam("CnpjCpfConsulta")) {
            $CnpjCpf = $this->_request->getParam("CnpjCpfConsulta");
            $CnpjCpf = str_replace(array('.', '/', '-'), '', $CnpjCpf);
            $where["x.CNPJCPF = ?"] = $CnpjCpf;
        }
        if ($this->_request->getParam("ProponenteConsulta")) {
            $ProponenteConsulta = $this->_request->getParam("ProponenteConsulta");
            $where["y.Descricao LIKE ?"] = "%" . $ProponenteConsulta . "%";
        }
        if ($this->_request->getParam("NomeProjetoConsulta")) {
            $NomeProjetoConsulta = $this->_request->getParam("NomeProjetoConsulta");
            $where["p.NomeProjeto LIKE ?"] = "%" . $NomeProjetoConsulta . "%";
        }

        $Projetos = new Projetos();

        if (!$idNrReuniaoConsulta) {
            $idNrReuniao = null;
        } else {
            $idNrReuniao = $raberta->idNrReuniao;
        }

        // PAGINAÇÂO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }

        $objTotal = $Projetos->countProjetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $ordem, false, false);
        $total = $objTotal['total'];

        $pag = 1;
        $post = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $offset = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;
        $fim = $offset + $this->intTamPag;
        $totalPag = (int) (($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $limit = ($fim > $total) ? $total - $offset : $this->intTamPag;

        $busca = $Projetos->projetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $order, $limit, $offset);

        $html = "<table cellspacing='0' cellpadding='2' border='1' align='center' width='99%'>
                <tr>
                    <th align='left'>PRONAC</th>
                    <th>Nome do Projeto</th>
                    <th>Proponente</th>
                    <th>UF</th>
                    <th>Munic&iacute;pio</th>
                    <th>Enquadramento</th>
                    <th>Área</th>
                    <th>Segmento</th>
                    <th>Avalia&ccedil;&atilde;o</th>
		    <th>Dt. In&iacute;cio Execu&ccedil;&atilde;o</th>
		    <th>Dt. T&eacute;rmino Execu&ccedil;&atilde;o</th>
		    <th>Vl.Solicitado</th>
                    <th>Vl.Aprovado</th>
                    <th>Vl.Captado</th>
                </tr>";

        $TotalSolicitado = 0;
        $TotalAprovado = 0;
        $TotalCaptado = 0;

        foreach ($busca as $d) {
            if (!empty($d->vlSolicitado)) {
                $vl1 = @number_format($d->vlSolicitado, 2, ",", ".");
            } else {
                $vl1 = '';
            }

            if (!empty($d->vlAprovado)) {
                $vl2 = @number_format($d->vlAprovado, 2, ",", ".");
            } else {
                $vl2 = '';
            }

            if (!empty($d->vlCaptado)) {
                $vl3 = @number_format($d->vlCaptado, 2, ",", ".");
            } else {
                $vl3 = '';
            }


            $html .= "  <tr>
                            <td>" . $d->Pronac . "</td>
                            <td>" . $d->NomeProjeto . "</td>
                            <td>" . $d->Proponente . "</td>
                            <td>" . $d->UF . "</td>
                            <td>" . $d->Cidade . "</td>
                            <td>" . $d->descEnquadramento . "</td>
                            <td>" . $d->dsArea . "</td>
                            <td>" . $d->dsSegmento . "</td>
                            <td>" . $d->descAvaliacao . "</td>
			    <td>" . Data::tratarDataZend($d->DtInicioExecucao, 'Brasileira') . "</td>
			    <td>" . Data::tratarDataZend($d->DtFimExecucao, 'Brasileira') . "</td>
                            <td>" . $vl1 . "</td>
                            <td>" . $vl2 . "</td>
                            <td>" . $vl3 . "</td>
                    </tr>";
            $TotalSolicitado = $TotalSolicitado + $d->vlSolicitado;
            $TotalAprovado = $TotalAprovado + $d->vlAprovado;
            $TotalCaptado = $TotalCaptado + $d->vlCaptado;
        };

        $html .= "<tr>
                    <th colspan='11'>TOTAL</th>
                    <th nowrap>" . @number_format($TotalSolicitado, 2, ',', '.') . "</th>
                    <th nowrap>" . @number_format($TotalAprovado, 2, ',', '.') . "</th>
                    <th nowrap>" . @number_format($TotalCaptado, 2, ',', '.') . "</th>
                </tr>
            ";
        $html .= "</table>";
        $this->view->html = $html;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function cadastrarOpiniaoAction() {
        if (!$this->usuarioInterno) {
            Zend_Layout::startMvc(array('layout' => 'layout_login'));
        }

        if (isset($_GET['idPronac']) && !empty($_GET['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $this->view->idPronac = $idPronac;
        } else {
            parent::message("Projeto Não encontrado!", "cidadao/index", "ALERT");
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
    }

    public function inserirOpiniaoAction() {
        //INSERT NA TABELA SAC.dbo.tbOpinarProjeto
        $dados = array(
            'idPronac' => $_POST['idPronac'],
            'idVisao' => 197,
            'siFaseProjeto' => 2,
            'dtOpiniao' => new Zend_Db_Expr('GETDATE()'),
            'stQuestionamento_1' => isset($_POST['qst1']) ? $_POST['qst1'] : 0,
            'stQuestionamento_2' => isset($_POST['qst2']) ? $_POST['qst2'] : 0,
            'stQuestionamento_3' => isset($_POST['qst3']) ? $_POST['qst3'] : 0,
            'dsComentario' => $_POST['comentario'],
            'dsEmail' => $_POST['email']
        );
        $tbOpinarProjeto = new tbOpinarProjeto();
        $insert = $tbOpinarProjeto->inserir($dados);

        if ($insert) {
            parent::message("Sua opini�o foi cadastrada com sucesso!", "cidadao/index", "CONFIRM");
        } else {
            parent::message("Não foi poss�vel cadastrar a sua opini�o!", "cidadao/index", "ERROR");
        }
    }

    public function visualizarOpinioesAction() {
        if (!$this->usuarioInterno) {
            Zend_Layout::startMvc(array('layout' => 'layout_login'));
        }

        if (isset($_GET['idPronac']) && !empty($_GET['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $this->view->idPronac = $idPronac;
        } else {
            parent::message("Projeto Não encontrado!", "cidadao/index", "ALERT");
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbOpinarProjeto = new tbOpinarProjeto();
        $opinioes = $tbOpinarProjeto->buscar(array('idPronac = ?' => $idPronac), array('dtOpiniao Desc'));
        $this->view->dados = $opinioes;

        //Quantidade de resposta sim/nao da primeira questao
        $qst1s = $tbOpinarProjeto->buscar(array('idPronac = ?' => $idPronac, 'stQuestionamento_1 = ?' => 1));
        $this->view->qst1s = $qst1s;
        $qst1n = $tbOpinarProjeto->buscar(array('idPronac = ?' => $idPronac, 'stQuestionamento_1 = ?' => 2));
        $this->view->qst1n = $qst1n;

        //Quantidade de resposta sim/nao da segunda questao
        $qst2s = $tbOpinarProjeto->buscar(array('idPronac = ?' => $idPronac, 'stQuestionamento_2 = ?' => 1));
        $this->view->qst2s = $qst2s;
        $qst2n = $tbOpinarProjeto->buscar(array('idPronac = ?' => $idPronac, 'stQuestionamento_2 = ?' => 2));
        $this->view->qst2n = $qst2n;

        //Quantidade de resposta sim/nao da terceira questao
        $qst3s = $tbOpinarProjeto->buscar(array('idPronac = ?' => $idPronac, 'stQuestionamento_3 = ?' => 1));
        $this->view->qst3s = $qst3s;
        $qst3n = $tbOpinarProjeto->buscar(array('idPronac = ?' => $idPronac, 'stQuestionamento_3 = ?' => 2));
        $this->view->qst3n = $qst3n;
    }

    public function dadosProjetoAction() {
        if (!$this->usuarioInterno) {
            Zend_Layout::startMvc(array('layout' => 'layout_login'));
        }

        if (isset($_GET['idPronac']) && !empty($_GET['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $this->view->idPronac = $idPronac;
        } else {
            parent::message("Projeto Não encontrado!", "cidadao/index", "ALERT");
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->cidadaoDadosProjeto(array('p.IdPRONAC = ?' => $idPronac))->current();
        $this->view->dados = $DadosProjeto;

        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
        $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, 2);
        $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, 2);
        $this->view->planilha = $planilha;
        $this->view->tipoPlanilha = 2;
    }

    public function parecerConsolidadoAction() {
        if (!$this->usuarioInterno) {
            Zend_Layout::startMvc(array('layout' => 'layout_login'));
        }

        if (isset($_GET['idPronac']) && !empty($_GET['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $this->view->idPronac = $idPronac;
        } else {
            parent::message("Projeto Não encontrado!", "cidadao/index", "ALERT");
        }

        $Parecer = new Parecer_Model_DbTable_Parecer();
        $this->view->identificacaoParecerConsolidado = $Parecer->cidadoPareceConsolidado($idPronac);

        $vwMemoriaDeCalculo = new vwMemoriaDeCalculo();
        $this->view->memoriaDeCalculo = $vwMemoriaDeCalculo->busca($idPronac);

        $tbAnaliseDeConteudo = new Parecer_Model_DbTable_TbAnaliseDeConteudo();
        $this->view->outrasInformacoesParecer = $tbAnaliseDeConteudo->cidadoBuscarOutrasInformacoes($idPronac);

        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
        $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, 2);
        $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, 2);
        $this->view->planilha = $planilha;
        $this->view->tipoPlanilha = 2;
    }

//    private function consultapronacAction()
    public function consultapronacAction() {
        Zend_Layout::startMvc(array('layout' => 'layout'));

        if (isset($_REQUEST['idPronac'])) {
            $idPronac = $_GET['idPronac'];
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }


            $dados = array();
            $dados['idPronac'] = (int) $idPronac;
            if (is_numeric($dados['idPronac'])) {
                if (isset($dados['idPronac'])) {
                    $idPronac = $dados['idPronac'];
                    //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
                    $this->view->idPronac = $idPronac;
                    $this->view->menumsg = 'true';
                }

                $tblProjetos = new Projetos();
                $rst = $tblProjetos->buscarDadosUC75($idPronac);
//                $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);
                //DEFINIE LINK PARA PLANILHA DE VALOR APROVADO
                $pp = new PlanilhaProjeto();
                $pa = new PlanilhaAprovacao();
                $buscarsomaprojeto = $pp->somarPlanilhaProjeto($idPronac);
                $buscarsomaaprovacaoC = $pa->somarPlanilhaAprovacao($idPronac, 206, "CO");
                $buscarsomaaprovacaoP = $pa->somarPlanilhaAprovacao($idPronac, 206, "SE");

                if (isset($buscarsomaaprovacaoP['soma']) && $buscarsomaaprovacaoP['soma'] > 0) {
                    $this->view->linkplanilha = "plenaria";
                } elseif (isset($buscarsomaaprovacaoC['soma']) && $buscarsomaaprovacaoC['soma'] > 0) {
                    $this->view->linkplanilha = "cnic";
                } else {
                    $this->view->linkplanilha = "inicial";
                }

                if (count($rst) > 0) {
                    $this->view->projeto = $rst[0];
                    $this->view->idpronac = $idPronac;
                    $this->view->idprojeto = $rst[0]->idProjeto;
                    if ($rst[0]->codSituacao == 'E12' || $rst[0]->codSituacao == 'E13' || $rst[0]->codSituacao == 'E15' || $rst[0]->codSituacao == 'E50' || $rst[0]->codSituacao == 'E59' || $rst[0]->codSituacao == 'E61' || $rst[0]->codSituacao == 'E62') {
                        $this->view->menuCompExec = 'true';
                    }
                    $this->view->situacaoProjeto = $rst[0]->codSituacao;

                    $geral = new ProponenteDAO();

                    $arrBusca['IdPronac = ?'] = $idPronac;
                    $rsProjeto = $tblProjetos->buscar($arrBusca)->current();
                    $idPreProjeto = 0;

                    if (!empty($rsProjeto->idProjeto)) {
                        $idPreProjeto = $rsProjeto->idProjeto;
                    }

                    $pronac = $rsProjeto->AnoProjeto . $rsProjeto->Sequencial;
                    $dadosProjeto = $geral->execPaProponente($idPronac);
                    $this->view->dados = $dadosProjeto;
                    $this->view->dadosProjeto = $rsProjeto;


                    //VERIFICA SE O PROJETO EST� NA CNIC //
                    $Parecer = new Parecer_Model_DbTable_Parecer();
                    $dadosCNIC = $Parecer->verificaProjSituacaoCNIC($pronac);

                    $msgCNIC = 0;
                    if (count($dadosCNIC)) {
                        $msgCNIC = 1;
                    }
                    $this->view->msgCNIC = $msgCNIC;
                    // FIM - VERIFICA SE O PROJETO EST� NA CNIC //
                    //VERIFICA OS DADOS DE ARQUIVAMENTO, CASO EXISTA //
                    $ArquivamentoProjeto = array();
                    $tbArquivamento = new tbArquivamento();
                    $dadosArquivamentoProjeto = $tbArquivamento->conferirArquivamentoProjeto($pronac);
                    if (count($dadosArquivamentoProjeto)) {
                        $ArquivamentoProjeto = $dadosArquivamentoProjeto;
                    }
                    $this->view->dadosArquivamentoProjeto = $ArquivamentoProjeto;
                    // FIM - VERIFICA OS DADOS DE ARQUIVAMENTO, CASO EXISTA //


                    $verificarHabilitado = $geral->verificarHabilitado($rst[0]->CgcCPf);
                    if (count($verificarHabilitado) > 0) {
                        $this->view->ProponenteInabilitado = 1;
                    }

                    //VALORES DO PROJETO
                    $planilhaproposta = new Proposta_Model_DbTable_PlanilhaProposta();
                    $planilhaprojeto = new PlanilhaProjeto();
                    $planilhaAprovacao = new PlanilhaAprovacao();

                    $rsPlanilhaAtual = $planilhaAprovacao->buscar(array('IdPRONAC = ?' => $idPronac), array('dtPlanilha DESC'))->current();
                    $tpPlanilha = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'SE' : 'CO';

                    $arrWhereSomaPlanilha = array();
                    $arrWhereSomaPlanilha['idPronac = ?'] = $idPronac;
                    if ($this->bln_readequacao == "false") {
                        $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idPreProjeto, 109);
                        $outrasfontes = $planilhaproposta->somarPlanilhaProposta($idPreProjeto, false, 109);
                        $parecerista = $planilhaprojeto->somarPlanilhaProjeto($idPreProjeto, 109);
                    } else {
                        $arrWhereFontesIncentivo = $arrWhereSomaPlanilha;
                        $arrWhereFontesIncentivo['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
                        $arrWhereFontesIncentivo['tpPlanilha = ? '] = 'SR';
                        $arrWhereFontesIncentivo['stAtivo = ? '] = 'N';
                        $arrWhereFontesIncentivo['NrFonteRecurso = ? '] = '109';
                        $arrWhereFontesIncentivo["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                        $arrWhereFontesIncentivo["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
                        $fonteincentivo = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereFontesIncentivo);

                        $arrWhereOutrasFontes = $arrWhereSomaPlanilha;
                        $arrWhereOutrasFontes['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
                        $arrWhereOutrasFontes['tpPlanilha = ? '] = 'SR';
                        $arrWhereOutrasFontes['stAtivo = ? '] = 'N';
                        $arrWhereOutrasFontes['NrFonteRecurso <> ? '] = '109';
                        $arrWhereOutrasFontes["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                        $arrWhereOutrasFontes["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
                        $outrasfontes = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereOutrasFontes);

                        $arrWherePlanilhaPA = $arrWhereSomaPlanilha;
                        $arrWherePlanilhaPA['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
                        $arrWherePlanilhaPA['tpPlanilha = ? '] = 'PA';
                        $arrWherePlanilhaPA['stAtivo = ? '] = 'N';
                        $arrWherePlanilhaPA['NrFonteRecurso = ? '] = '109';
                        $arrWherePlanilhaPA["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')");
                        $arrWherePlanilhaPA["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
                        $parecerista = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWherePlanilhaPA);
                    }
                    //valor do componetne
                    $arrWhereSomaPlanilha = array();
                    $arrWhereSomaPlanilha['idPronac = ?'] = $idPronac;
                    $arrWhereSomaPlanilha['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
                    $arrWhereSomaPlanilha['tpPlanilha = ? '] = $tpPlanilha;
                    $arrWhereSomaPlanilha['NrFonteRecurso = ? '] = '109';
                    $arrWhereSomaPlanilha['stAtivo = ? '] = 'S';
                    $componente = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

                    $valoresProjeto = new ArrayObject();
                    $valoresProjeto['fontesincentivo'] = $fonteincentivo['soma'];
                    $valoresProjeto['outrasfontes'] = $outrasfontes['soma'];
                    $valoresProjeto['valorproposta'] = $fonteincentivo['soma'] + $outrasfontes['soma'];
                    $valoresProjeto['valorparecerista'] = $parecerista['soma'];
                    $valoresProjeto['valorcomponente'] = $componente['soma'];
                    $this->view->valoresDoProjeto = $valoresProjeto;

                    $tblCaptacao = new Captacao();
                    $rsCount = $tblCaptacao->buscaCompleta(array('idPronac = ?' => $idPronac), array(), null, null, true);
                    $this->view->totalGeralCaptado = $rsCount->totalGeralCaptado;
                    /*                     * *************** FIM  - MODO NOVO ******************* */

                    /*                     * * Valida��o do Proponente Inabilitado *********************************** */

                    $cpfLogado = $this->cpfLogado;
                    $cpfProponente = !empty($dadosProjeto[0]->CNPJCPF) ? $dadosProjeto[0]->CNPJCPF : '';
                    $respProponente = 'R';
                    $inabilitado = 'N';


                    // Verificando se o Proponente est� inabilitado
                    $inabilitadoDAO = new Inabilitado();
                    $where['CgcCpf 		= ?'] = $cpfProponente;
                    $where['Habilitado 	= ?'] = 'N';
                    $busca = $inabilitadoDAO->Localizar($where)->count();

                    if ($busca > 0) {
                        $inabilitado = 'S';
                    }

                    if (!empty($idPreProjeto)) {

                        // Se for Respons�vel verificar se tem Procura��o
                        $procuracaoDAO = new Procuracao();
                        $procuracaoValida = 'N';

                        $wherePro['vprp.idPreProjeto = ?'] = $idPreProjeto;
                        $wherePro['v.idUsuarioResponsavel = ?'] = $this->idResponsavel;
                        $wherePro['p.siProcuracao = ?'] = 1;
                        $buscaProcuracao = $procuracaoDAO->buscarProcuracaoProjeto($wherePro)->count();
                        if ($buscaProcuracao > 0) {
                            $procuracaoValida = 'S';
                        }
                    } else {
                        $procuracaoValida = 'S';
                    }

                    $this->view->procuracaoValida = $procuracaoValida;
                    $this->view->respProponente = $respProponente;
                    $this->view->inabilitado = $inabilitado;

                    /*                     * ************************************************************************* */

                    $tbemail = $geral->buscarEmail($idPronac);
                    $this->view->email = $tbemail;

                    $tbtelefone = $geral->buscarTelefone($idPronac);
                    $this->view->telefone = $tbtelefone;

                    $tblAgente = new Agente_Model_DbTable_Agentes();
                    if (isset($dadosProjeto[0]->CNPJCPF) && !empty($dadosProjeto[0]->CNPJCPF)) {
                        $rsAgente = $tblAgente->buscar(array('CNPJCPF=?' => $dadosProjeto[0]->CNPJCPF))->current();
                        $this->view->CgcCpf = $dadosProjeto[0]->CNPJCPF;
                    }

                    $rsIdAgente = (isset($rsAgente->idAgente) && !empty($rsAgente->idAgente)) ? $rsAgente->idAgente : 0;

                    $rsDirigentes = $tblAgente->buscarDirigentes(array('v.idVinculoPrincipal =?' => $rsIdAgente, 'n.Status =?' => 0), array('n.Descricao ASC'));
//                    $tbDirigentes = $geral->buscarDirigentes($idPronac);
                    $this->view->dirigentes = $rsDirigentes;

                    //========== inicio codigo mandato dirigente ================
                    /* ================================================== */
                    $arrMandatos = array();

                    if (!empty($this->idPreProjeto)) {
                        $preProjeto = new Proposta_Model_DbTable_PreProjeto();
                        $Empresa = $preProjeto->buscar(array('idPreProjeto = ?' => $this->idPreProjeto))->current();
                        $idEmpresa = $Empresa->idAgente;

                        $tbDirigenteMandato = new tbAgentesxVerificacao();
                        foreach ($rsDirigentes as $dirigente) {
                            $rsMandato = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idEmpresa, 'idDirigente = ?' => $dirigente->idAgente, 'stMandato = ?' => 0));
                            $arrMandatos[$dirigente->NomeDirigente] = $rsMandato;
                        }
                    }
                    $this->view->mandatos = $arrMandatos;

                    //============== fim codigo dirigente ================
                    /* ================================================== */

                    if (!empty($idPreProjeto)) {
                        //OUTROS DADOS PROPONENTE
                        $this->view->itensGeral = Proposta_Model_AnalisarPropostaDAO::buscarGeral($idPreProjeto);
                    }
                } else {
                    parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "listarprojetos/listarprojetos", "ERROR");
                }
            } else {
                parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
            }
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "listarprojetos/listarprojetos", "ERROR");
        }
    }

    public function planilhaOrcamentariaAction() {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
            $this->view->tipoPlanilha = 1;
        }
    }

    public function planilhaOrcamentariaAprovadaAction() {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();

            $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
            $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, 6);

            if (count($planilhaOrcamentaria) > 0) {
                $this->view->tipoPlanilha = 6;
            } else {
                $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, 3);
                if (count($planilhaOrcamentaria) > 0) {
                    $this->view->tipoPlanilha = 3;
                } else {
                    $this->view->tipoPlanilha = 2;
                }
            }
        }
    }

    public function dadosBancariosCaptacaoAction() {
        Zend_Layout::startMvc(array('layout' => 'layout'));


        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();

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
                $order = array($campo . " " . $ordem);
                $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
            } else {
                $campo = null;
                $order = array(8, 4); //NomeProjeto, Dt.Recibo
                $ordenacao = null;
            }

            $pag = 1;
            $get = Zend_Registry::get('get');
            if (isset($get->pag)) {
                $pag = $get->pag;
            }
            $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

            /* ================== PAGINACAO ====================== */
            $where = array();
            $where['p.idPronac = ?'] = $idPronac;

            if (isset($_GET['dtReciboInicio']) && !empty($_GET['dtReciboInicio']) && isset($_GET['dtReciboFim']) && empty($_GET['dtReciboFim'])) {
                $di = ConverteData($_GET['dtReciboInicio'], 13) . " 00:00:00";
                $df = ConverteData($_GET['dtReciboInicio'], 13) . " 23:59:59";
                $where["c.DtRecibo BETWEEN '$di' AND '$df'"] = '';
                $this->view->dtReciboInicio = $_GET['dtReciboInicio'];
                $this->view->dtReciboFim = $_GET['dtReciboFim'];
            }

            if (isset($_GET['dtReciboInicio']) && !empty($_GET['dtReciboInicio']) && isset($_GET['dtReciboFim']) && !empty($_GET['dtReciboFim'])) {
                $di = ConverteData($_GET['dtReciboInicio'], 13) . " 00:00:00";
                $df = ConverteData($_GET['dtReciboFim'], 13) . " 23:59:59";
                $where["c.DtRecibo BETWEEN '$di' AND '$df'"] = '';
                $this->view->dtReciboInicio = $_GET['dtReciboInicio'];
                $this->view->dtReciboFim = $_GET['dtReciboFim'];
            }

            $Captacao = new Captacao();
            $total = $Captacao->painelDadosBancariosCaptacao($where, $order, null, null, true);
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int) (($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

            $busca = $Captacao->painelDadosBancariosCaptacao($where, $order, $tamanho, $inicio);

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
            $this->view->qtd = $total;
            $this->view->dados = $busca;
            $this->view->intTamPag = $this->intTamPag;
        }
    }

    public function dadosRelacaoPagamentosAction() {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;

            $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
            $this->view->relacaoPagamentos = $tbComprovante->buscarRelacaoPagamentos($idPronac);
        }
    }

    public function historicoEncaminhamentoAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();
            $this->view->idPronac = $idPronac;
            $this->view->DadosProjeto = $DadosProjeto;

            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $this->view->dados = $tbDistribuirParecer->buscarHistoricoEncaminhamento(array('a.idPRONAC = ?'=>$idPronac));
        }
    }

     public function dadosBancariosAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

            $tblContaBancaria = new ContaBancaria();
            $rsContaBancaria = $tblContaBancaria->contaPorProjeto($idPronac);
            $this->view->dadosContaBancaria = $rsContaBancaria;

            $tbLiberacao = new Liberacao();
            $rsLiberacao = $tbLiberacao->liberacaoPorProjeto($idPronac);
            $this->view->dadosLiberacao = $rsLiberacao;
        }
    }

}
