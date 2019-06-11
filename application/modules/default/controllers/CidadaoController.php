<?php

/**
    @todo remover essa controller e codigo e enviar para o ver salic
 */


class CidadaoController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
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

    public function indexAction()
    {
        $this->redirect('/cidadao/consultar');
    }

    public function consultarAction()
    {

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
            $raberta = null;  // Fernao: permite n�o filtrar
            $this->view->idNrReuniaoConsulta = null;
        } else {
            $raberta = $reuniao->buscarReuniaoPorId($idNrReuniaoConsulta);//idNrReuniao
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
            $ordenacao = "&campo=".$campo;
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

        /* ================== PAGINACAO ======================*/
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
            $where["y.Descricao LIKE ?"] = "%" . $ProponenteConsulta. "%";
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

        // pagina��o
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
            $urlComplement[] = 'qtde=' . $this->intTamPag;
            $sess->qtde = $this->intTamPag;
        }
        $pag = 1;
        $post  = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $offset = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $objTotal = $Projetos->countProjetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $ordem, false, false);
        $total = $objTotal['total'];
        $fim = $offset + $this->intTamPag;
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $limit = ($fim > $total) ? $total - $offset : $this->intTamPag;

        $paginacao = array(
            "pag"=>$pag,
            "qtde"=>$this->intTamPag,
            "campo"=>$campo,
            "ordem"=>$ordem,
            "ordenacao"=>$ordenacao,
            "novaOrdem"=>$novaOrdem,
            "total"=>$total,
            "inicio"=>($offset+1),
            "fim"=>$fim,
            "totalPag"=>$totalPag,
            "Itenspag"=>$this->intTamPag,
            "tamanho"=>$limit
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
        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->view->novaOrdem = $novaOrdem;
        $this->view->ordem = $ordem;
        $this->view->campo = $campo;
        $this->view->intTamPag     = $this->intTamPag;

        $this->view->intranet = false;
        if (isset($_GET['intranet'])) {
            $this->view->intranet = true;
        }
    }

    public function imprimirListagemAction()
    {

    #Pedro - recuperando a Sessao Salva da pesquisa
        $sess = new Zend_Session_Namespace('Filtro_de_Pesquisa');

        $idNrReuniaoConsulta = $this->_request->getParam("idNrReuniaoConsulta") ? $this->_request->getParam("idNrReuniaoConsulta") : null;
        $reuniao = new Reuniao();

        if (!$idNrReuniaoConsulta) {
            $raberta = null;
            $this->view->idNrReuniaoConsulta = null;
        } else {
            $raberta = $reuniao->buscarReuniaoPorId($idNrReuniaoConsulta);//idNrReuniao
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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
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
            $where["y.Descricao LIKE ?"] = "%" . $ProponenteConsulta. "%";
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

        // pagina��o
        if ($sess->qtde) {
            $this->intTamPag = $sess->qtde;
        }

        $objTotal = $Projetos->countProjetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $ordem, false, false);
        $total = $objTotal['total'];

        $pag = 1;
        $post  = Zend_Registry::get('get');

        if (isset($sess->pag)) {
            $pag = $sess->pag;
        }
        $offset = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $offset + $this->intTamPag;
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
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

    public function xlsListagemAction()
    {
        $idNrReuniaoConsulta = $this->_request->getParam("idNrReuniaoConsulta") ? $this->_request->getParam("idNrReuniaoConsulta") : null;
        $reuniao = new Reuniao();

        if (!$idNrReuniaoConsulta) {
            $raberta = null;
            $this->view->idNrReuniaoConsulta = null;
        } else {
            $raberta = $reuniao->buscarReuniaoPorId($idNrReuniaoConsulta);//idNrReuniao
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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
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
            $where["y.Descricao LIKE ?"] = "%" . $ProponenteConsulta. "%";
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

        // pagina��o
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }

        $objTotal = $Projetos->countProjetosCnicOpinioesPorIdReuniao($idNrReuniao, $where, $ordem, false, false);
        $total = $objTotal['total'];

        $pag = 1;
        $post  = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $offset = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $offset + $this->intTamPag;
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
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
                    <th>&Aacute;rea</th>
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
                            <td>".$d->Pronac."</td>
                            <td>".$d->NomeProjeto."</td>
                            <td>".$d->Proponente."</td>
                            <td>".$d->UF."</td>
                            <td>".$d->Cidade."</td>
                            <td>".$d->descEnquadramento."</td>
                            <td>".$d->dsArea."</td>
                            <td>".$d->dsSegmento."</td>
                            <td>".$d->descAvaliacao."</td>
			    <td>".Data::tratarDataZend($d->DtInicioExecucao, 'Brasileira')."</td>
			    <td>".Data::tratarDataZend($d->DtFimExecucao, 'Brasileira')."</td>
                            <td>".$vl1."</td>
                            <td>".$vl2."</td>
                            <td>".$vl3."</td>
                    </tr>";
            $TotalSolicitado = $TotalSolicitado + $d->vlSolicitado;
            $TotalAprovado = $TotalAprovado + $d->vlAprovado;
            $TotalCaptado = $TotalCaptado + $d->vlCaptado;
        };

        $html .="<tr>
                    <th colspan='11'>TOTAL</th>
                    <th nowrap>". @number_format($TotalSolicitado, 2, ',', '.')."</th>
                    <th nowrap>". @number_format($TotalAprovado, 2, ',', '.')."</th>
                    <th nowrap>". @number_format($TotalCaptado, 2, ',', '.')."</th>
                </tr>
            ";
        $html .="</table>";
        $this->view->html = $html;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    public function cadastrarOpiniaoAction()
    {
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
            parent::message("Projeto n�o encontrado!", "cidadao/index", "ALERT");
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
    }

    public function inserirOpiniaoAction()
    {
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
            parent::message("N�o foi poss�vel cadastrar a sua opini�o!", "cidadao/index", "ERROR");
        }
    }

    public function visualizarOpinioesAction()
    {
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
            parent::message("Projeto n�o encontrado!", "cidadao/index", "ALERT");
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

    public function dadosProjetoAction()
    {
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
            parent::message("Projeto n�o encontrado!", "cidadao/index", "ALERT");
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

    public function parecerConsolidadoAction()
    {
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
            parent::message("Projeto n�o encontrado!", "cidadao/index", "ALERT");
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
}
