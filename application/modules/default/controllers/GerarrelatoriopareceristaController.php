<?php
class GerarrelatoriopareceristaController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 100;

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p�gina

        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $PermissoesGrupo = array();

        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usu�rio
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }


        $PermissoesGrupo[] = 148; // Coordenador de Fiscaliza�?o
        $PermissoesGrupo[] = 151; // Coordenador de Fiscaliza�?o
        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        $this->usuarioLogado = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;
        parent::init();
    }

    public function indexAction()
    {
    }
    public function aguardandoparecerAction()
    {
        $produtoDAO = new Produto();
        $this->view->Produtos = $produtoDAO->buscar();
    }
    private function paginacao($total, $qtInformacao = 10)
    {
        $post = Zend_Registry::get('post');
        $this->intTamPag = $qtInformacao;
        //controlando a paginacao
        $pag = 1;
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        if (isset($post->tamPag)) {
            $this->intTamPag = $post->tamPag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) {
            $fim = $total;
        }

        $this->view->pag                = $pag;
        $this->view->total              = $total;
        $this->view->inicio             = ($inicio+1);
        $this->view->fim                = $fim;
        $this->view->totalPag           = $totalPag;
        $this->view->parametrosBusca    = $_POST;

        return array('tamanho'=>$tamanho,'inicio'=>$inicio);
    }
    private function gerarAnexo($tela, $filtro = '')
    {
        $this->view->tela   =   $tela;
        if (empty($filtro)) {
            $this->view->filtro =   $tela;
        } else {
            $this->view->filtro =   $filtro;
        }
        $this->view->post   =   $_POST;
    }
    public function resaguardandoparecerAction()
    {
        $tela = 'resaguardandoparecer';
        $this->gerarAnexo($tela);
        $this->view->tudo   =   $this->gerarInfoPaginas($tela, $this->filtroGeral($tela), 100);
    }
    public function resumoAction()
    {
        $tela   = 'resumo';
        $filtro = 'resaguardandoparecer';
        $this->gerarAnexo($tela, $filtro);
        $this->view->tudo   =   $this->gerarInfoPaginas($tela, $this->filtroGeral($filtro));
    }
    public function graficoresumoAction()
    {
        error_reporting(0);
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $post               = Zend_Registry::get('post');
        $tituloGrafico = "Analise";
        if ($post->idOrgao) {
            $OrgaoDAO   =   new Orgaos();
            $orgao      =   $OrgaoDAO->buscar(array('Codigo = ?'=>$post->idOrgao));
            $tituloGrafico  .=  ' - Orgao = '.$orgao[0]->Sigla;
        }
        $grafico = new Grafico($_POST["cgTipoGrafico"]);
        $grafico->setTituloGrafico($tituloGrafico);
        $grafico->setTituloEixoXY("Parecerista", "Analise");
        $grafico->configurar($_POST);

        $where = $this->filtroGeral('resaguardandoparecer');

        $distribuirParecerDAO   =   new Parecer_Model_DbTable_TbDistribuirParecer();
        $resp                   =   $distribuirParecerDAO->aguardandoparecerresumo($where);
        $titulos    =   array();
        $valores    =   array();
        foreach ($resp as $val) {
            $titulos[] = $val['nmParecerista'];
            $valores[] = $val['qt'];
        }
        if (count($valores)>0) {
            $grafico->addDados($valores);
            $grafico->setTituloItens($titulos);
            $grafico->gerar();
        } else {
            echo "Nenhum dado encontrado gera&ccedil;&atilde;o de Gr�fico.";
        }
    }
    public function pareceremitidoAction()
    {
        $tela   = 'pareceremitido';
        $this->gerarAnexo($tela);
        $this->view->projetos = $this->gerarInfoPaginas($tela, array(), 10);
    }


    public function parecerconsolidadoAction()
    {
        $tela   = 'parecerconsolidado';
        $this->gerarAnexo($tela);
        $this->view->projetos = $this->gerarInfoPaginas($tela, array(), 10);
    }

    public function geraldeanaliseAction()
    {
        $produtoDAO     =   new Produto();
        $OrgaosDAO      =   new Orgaos();
        $NomesDAO       =   new Nomes();
        $AreaDAO        =   new Area();
        $SegmentoDAO    =   new Segmento();
        $this->view->Produtos       =   $produtoDAO->buscar();
        $this->view->Orgaos         =   $OrgaosDAO->buscar(array('Status = ?'=>0,'Vinculo = ?'=>1));
        $this->view->Pareceristas   =   $NomesDAO->buscarPareceristas();
        $this->view->Areas          =   $AreaDAO->buscar();
        $this->view->Segmento       =   $SegmentoDAO->buscar(array('stEstado = ?'=>1));

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;
    }

    public function resgeraldeanaliseAction()
    {
        $this->intTamPag = 20;

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
            $order = array(1); //idPronac
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

        if ((isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where["p.AnoProjeto+p.Sequencial = ?"] = $_GET['pronac'];
            $this->view->pronac = $_GET['pronac'];
        }

        if ((isset($_GET['nmProjeto']) && !empty($_GET['nmProjeto']))) {
            $where["p.NomeProjeto like '%".$_GET['nmProjeto']."%'"] = '';
            $this->view->nmProjeto = $_GET['nmProjeto'];
        }

        if ((isset($_GET['unVinculada']) && !empty($_GET['unVinculada']))) {
            $where["d.idOrgao = ?"] = $_GET['unVinculada'];
            $this->view->unVinculada = $_GET['unVinculada'];
        }

        if ((isset($_GET['qtDiasDistribuir']) && !empty($_GET['qtDiasDistribuir']))) {
            if (!empty($_GET['qtDiasDistribuirVl'])) {
                if ($_GET['qtDiasDistribuir'] == 1) {
                    $where["DATEDIFF(day, d.DtEnvio, isnull(d.DtDistribuicao,GETDATE())) >= ?"] = $_GET['qtDiasDistribuirVl'];
                } else {
                    $where["DATEDIFF(day, d.DtEnvio, isnull(d.DtDistribuicao,GETDATE())) <= ?"] = $_GET['qtDiasDistribuirVl'];
                }
                $this->view->qtDiasDistribuir = $_GET['qtDiasDistribuir'];
                $this->view->qtDiasDistribuirVl = $_GET['qtDiasDistribuirVl'];
            }
        }

        if ((isset($_GET['qtDiasAnalisar']) && !empty($_GET['qtDiasAnalisar']))) {
            if (!empty($_GET['qtDiasAnalisarVl'])) {
                if ($_GET['qtDiasDistribuir'] == 1) {
                    $where["
                        (d.DtDevolucao is null and DATEDIFF(day, d.DtDistribuicao, GETDATE()) >= ".$_GET['qtDiasAnalisarVl'].") or
                        (d.DtDevolucao is not null and DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao) >= ".$_GET['qtDiasAnalisarVl'].") or
                        (Situacao = 'B14' and DATEDIFF(day, d.DtDistribuicao, GETDATE()) - DATEDIFF(day, p.dtSituacao, GETDATE()) >= ".$_GET['qtDiasAnalisarVl'].")
                    "] = '';
                } else {
                    $where["
                        (d.DtDevolucao is null and DATEDIFF(day, d.DtDistribuicao, GETDATE()) <= ".$_GET['qtDiasAnalisarVl'].") or
                        (d.DtDevolucao is not null and DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao) <= ".$_GET['qtDiasAnalisarVl'].") or
                        (Situacao = 'B14' and DATEDIFF(day, d.DtDistribuicao, GETDATE()) - DATEDIFF(day, p.dtSituacao, GETDATE()) <= ".$_GET['qtDiasAnalisarVl'].")
                    "] = '';
                }
                $this->view->qtDiasAnalisar = $_GET['qtDiasAnalisar'];
                $this->view->qtDiasAnalisarVl = $_GET['qtDiasAnalisarVl'];
            }
        }

        if ((isset($_GET['qtDiasCoord']) && !empty($_GET['qtDiasCoord']))) {
            if (!empty($_GET['qtDiasCoordVl'])) {
                if ($_GET['qtDiasCoord'] == 1) {
                    $where["d.DtDevolucao is not null and d.DtRetorno is null AND d.FecharAnalise=0 and DATEDIFF(day, d.DtDevolucao,GETDATE()) >= ?"] = $_GET['qtDiasCoordVl'];
                } else {
                    $where["d.DtDevolucao is not null and d.DtRetorno is null AND d.FecharAnalise=0 and DATEDIFF(day, d.DtDevolucao,GETDATE()) <= ?"] = $_GET['qtDiasCoordVl'];
                }
                $this->view->qtDiasCoord = $_GET['qtDiasCoord'];
                $this->view->qtDiasCoordVl = $_GET['qtDiasCoordVl'];
            }
        }

        if ((isset($_GET['qtDiasIniExec']) && !empty($_GET['qtDiasIniExec']))) {
            if (!empty($_GET['qtDiasIniExecVl'])) {
                if ($_GET['qtDiasIniExec'] == 1) {
                    $where["DATEDIFF(day,GETDATE(), p.DtInicioExecucao) >= ?"] = $_GET['qtDiasIniExecVl'];
                } else {
                    $where["DATEDIFF(day,GETDATE(), p.DtInicioExecucao) <= ?"] = $_GET['qtDiasIniExecVl'];
                }
                $this->view->qtDiasIniExec = $_GET['qtDiasIniExec'];
                $this->view->qtDiasIniExecVl = $_GET['qtDiasIniExecVl'];
            }
        }

        $Projetos = new Projetos();
        $total = $Projetos->painelRelatoriosGeralAnalise($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Projetos->painelRelatoriosGeralAnalise($where, $order, $tamanho, $inicio);
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
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirResgeraldeanaliseAction()
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
            $order = array(1); //idPronac
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

        if ((isset($_POST['pronac']) && !empty($_POST['pronac']))) {
            $where["p.AnoProjeto+p.Sequencial = ?"] = $_POST['pronac'];
            $this->view->pronac = $_POST['pronac'];
        }

        if ((isset($_POST['nmProjeto']) && !empty($_POST['nmProjeto']))) {
            $where["p.NomeProjeto like '%".$_POST['nmProjeto']."%'"] = '';
            $this->view->nmProjeto = $_POST['nmProjeto'];
        }

        if ((isset($_POST['unVinculada']) && !empty($_POST['unVinculada']))) {
            $where["d.idOrgao = ?"] = $_POST['unVinculada'];
            $this->view->unVinculada = $_POST['unVinculada'];
        }

        if ((isset($_POST['qtDiasDistribuir']) && !empty($_POST['qtDiasDistribuir']))) {
            if ($_POST['qtDiasDistribuir'] == 1) {
                $where["DATEDIFF(day, d.DtEnvio, isnull(d.DtDistribuicao,GETDATE())) >= ?"] = $_POST['qtDiasDistribuirVl'];
            } else {
                $where["DATEDIFF(day, d.DtEnvio, isnull(d.DtDistribuicao,GETDATE())) <= ?"] = $_POST['qtDiasDistribuirVl'];
            }
            $this->view->qtDiasDistribuir = $_POST['qtDiasDistribuir'];
            $this->view->qtDiasDistribuirVl = $_POST['qtDiasDistribuirVl'];
        }

        if ((isset($_POST['qtDiasAnalisar']) && !empty($_POST['qtDiasAnalisar']))) {
            if ($_POST['qtDiasDistribuir'] == 1) {
                $where["
                    (d.DtDevolucao is null and DATEDIFF(day, d.DtDistribuicao, GETDATE()) >= ".$_POST['qtDiasAnalisarVl'].") or
                    (d.DtDevolucao is not null and DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao) >= ".$_POST['qtDiasAnalisarVl'].") or
                    (Situacao = 'B14' and DATEDIFF(day, d.DtDistribuicao, GETDATE()) - DATEDIFF(day, p.dtSituacao, GETDATE()) >= ".$_POST['qtDiasAnalisarVl'].")
                "] = '';
            } else {
                $where["
                    (d.DtDevolucao is null and DATEDIFF(day, d.DtDistribuicao, GETDATE()) <= ".$_POST['qtDiasAnalisarVl'].") or
                    (d.DtDevolucao is not null and DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao) <= ".$_POST['qtDiasAnalisarVl'].") or
                    (Situacao = 'B14' and DATEDIFF(day, d.DtDistribuicao, GETDATE()) - DATEDIFF(day, p.dtSituacao, GETDATE()) <= ".$_POST['qtDiasAnalisarVl'].")
                "] = '';
            }
            $this->view->qtDiasAnalisar = $_POST['qtDiasAnalisar'];
            $this->view->qtDiasAnalisarVl = $_POST['qtDiasAnalisarVl'];
        }

        if ((isset($_POST['qtDiasCoord']) && !empty($_POST['qtDiasCoord']))) {
            if ($_POST['qtDiasCoord'] == 1) {
                $where["d.DtDevolucao is not null and d.DtRetorno is null AND d.FecharAnalise=0 and DATEDIFF(day, d.DtDevolucao,GETDATE()) >= ?"] = $_POST['qtDiasCoordVl'];
            } else {
                $where["d.DtDevolucao is not null and d.DtRetorno is null AND d.FecharAnalise=0 and DATEDIFF(day, d.DtDevolucao,GETDATE()) <= ?"] = $_POST['qtDiasCoordVl'];
            }
            $this->view->qtDiasCoord = $_POST['qtDiasCoord'];
            $this->view->qtDiasCoordVl = $_POST['qtDiasCoordVl'];
        }

        if ((isset($_POST['qtDiasIniExec']) && !empty($_POST['qtDiasIniExec']))) {
            if ($_POST['qtDiasIniExec'] == 1) {
                $where["DATEDIFF(day,GETDATE(), p.DtInicioExecucao) >= ?"] = $_POST['qtDiasIniExecVl'];
            } else {
                $where["DATEDIFF(day,GETDATE(), p.DtInicioExecucao) <= ?"] = $_POST['qtDiasIniExecVl'];
            }
            $this->view->qtDiasIniExec = $_POST['qtDiasIniExec'];
            $this->view->qtDiasIniExecVl = $_POST['qtDiasIniExecVl'];
        }

        $Projetos = new Projetos();
        $total = $Projetos->painelRelatoriosGeralAnalise($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Projetos->painelRelatoriosGeralAnalise($where, $order, $tamanho, $inicio);

        if (isset($_POST['xls']) && $_POST['xls']) {
            $html = '';
            $html .= '<table>';
            $html .= '<tr>';
            $html .= '<th>PRONAC</th>';
            $html .= '<th>Nome do Projeto</th>';
            $html .= '<th>Produto</th>';
            $html .= '<th>Dt.Primeiro Envio p/ Vinculada</th>';
            $html .= '<th>Dt.�ltimo Envio p/ Vinculada</th>';
            $html .= '<th>Dt.Distribui��o Parecerista</th>';
            $html .= '<th>Parecerista</th>';
            $html .= '<th>Qtde Dias Para Distribuir</th>';
            $html .= '<th>Qtde Dias Para Parecerista Analisar</th>';
            $html .= '<th>Qtde Dias Devolvidos Para Coordenador</th>';
            $html .= '<th>Status da Dilig�ncia</th>';
            $html .= '<th>Unidade Vinculada</th>';
            $html .= '<th>Dt.In�cio Execu��o</th>';
            $html .= '<th>Dt.Fim Execu��o</th>';
            $html .= '<th>Dias vencidos ou a vencer para execu��o do Projeto</th>';
            $html .= '</tr>';

            foreach ($busca as $v) {
                $html .= '<tr>';
                $html .= '<td>'.$v->Pronac.'</td>';
                $html .= '<td>'.$v->NomeProjeto.'</td>';
                $html .= '<td>'.$v->Produto.'</td>';
                $html .= '<td>'.Data::tratarDataZend($v->DtPrimeiroEnvio, 'Brasileira').'</td>';
                $html .= '<td>'.Data::tratarDataZend($v->DtUltimoEnvio, 'Brasileira').'</td>';
                $html .= '<td>'.Data::tratarDataZend($v->DtDistribuicao, 'Brasileira').'</td>';
                $html .= '<td>'.$v->Parecerista.'</td>';
                $html .= '<td>'.$v->QtdeDiasParaDistribuir.'</td>';
                $html .= '<td>'.$v->QtdeDiasParaParecAnalisar.'</td>';
                $html .= '<td>'.$v->QtdeDiasDevolvidosParaCoord.'</td>';
                $html .= '<td>'.$v->Diligencia.'</td>';
                $html .= '<td>'.$v->Vinculada.'</td>';
                $html .= '<td>'.Data::tratarDataZend($v->DtInicioExecucao, 'Brasileira').'</td>';
                $html .= '<td>'.Data::tratarDataZend($v->DtFimExecucao, 'Brasileira').'</td>';
                $html .= '<td>'.$v->QtdeDiasVencido.'</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=file.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->qtdRegistros = $total;
            $this->view->dados = $busca;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    }


    public function consolidacaopareceristaAction()
    {
        $mapperArea = new Agente_Model_AreaMapper();
        $OrgaosDAO      =   new Orgaos();
        $NomesDAO       =   new Nomes();
        $AreaDAO        =   new Area();
        $SegmentoDAO    =   new Segmento();
        $this->view->Orgaos         =   $OrgaosDAO->buscar(array('Status = ?'=>0,'Vinculo = ?'=>1));
        $this->view->Pareceristas   =   $NomesDAO->buscarPareceristas();
        // O mesmo do Manter Agentes
        $this->view->comboareasculturais = $mapperArea->fetchPairs('codigo', 'descricao');

        $this->view->Areas          =   $AreaDAO->buscar();
        $this->view->Segmento       =   $SegmentoDAO->buscar(array('stEstado = ?'=>1));
    }
    public function resconsolidacaopareceristaAction()
    {
        $tela   = 'resconsolidacaoparecerista';
        $this->gerarAnexo($tela);
        $this->view->parecerista = $this->gerarInfoPaginas($tela, $this->filtroGeral($tela));

        $get  = Zend_Registry::get('get');
        if (! empty($get->parecerista)) {
            $this->view->gerenciar = true;
        } else {
            $this->view->gerenciar = false;
        }
    }

    private function ajustarData($data)
    {
        $data = substr($data, 6, 4).'-'.substr($data, 3, 2).'-'.substr($data, 0, 2);
        return $data;
    }

    private function filtroGeral($tipo)
    {
        $post = Zend_Registry::get('post');
        $get  = Zend_Registry::get('get');
        $where = array();
        switch ($tipo) {
            case    'resaguardandoparecer':
                if (!empty($post->pronac)) {
                    $where['proj.AnoProjeto = ?']           =   substr($post->pronac, 0, 2);
                    $where['proj.Sequencial = ?']           =   substr($post->pronac, 2);
                }
                if (!empty($post->nmprojeto)) {
                    $where["proj.NomeProjeto like ?"]   =   "%".$post->nmprojeto."%";
                }
                if (!empty($post->produto)) {
                    $where['dp.idProduto = ?']  		=   $post->produto;
                }

                $dt_i_envio   =   $this->ajustarData($post->dt_i_envio);
                $dt_f_envio   =   $this->ajustarData($post->dt_f_envio);
                if (!empty($post->dt_i_envio) and $post->dt_i_envio!='00/00/0000') {
                    $where  =   $this->tipos($where, 'dp.DtEnvio', $post->tp_dt_envio, $dt_i_envio, $dt_f_envio);
                }

                $dt_i_distribuicao    =   $this->ajustarData($post->dt_i_distribuicao);
                $dt_f_distribuicao    =   $this->ajustarData($post->dt_f_distribuicao);
                if (!empty($post->dt_i_distribuicao) and $post->dt_i_distribuicao!='00/00/0000') {
                    $where  =   $this->tipos($where, 'dp.DtDistribuicao', $post->tp_dt_distribuicao, $dt_i_distribuicao, $dt_f_distribuicao);
                }

                if (!empty($post->nrdias)) {
                    $where  =   $this->tipos($where, new Zend_Db_Expr('DATEDIFF(day, dp.DtEnvio,dp.DtDistribuicao)'), $post->tpnrdias, $post->nrdias);
                }
                if (!empty($post->idOrgao)) {
                    $where['org.Codigo = ?'] = $post->idOrgao;
                }

            break;
            case    'resgeraldeanalise':
                if (!empty($post->pronac)) {
                    $where['p.AnoProjeto = ?']          =   substr($post->pronac, 0, 2);
                }
                if (!empty($post->pronac)) {
                    $where['p.Sequencial = ?']          =   substr($post->pronac, 2);
                }
                if (!empty($post->nmprojeto)) {
                    $where["p.NomeProjeto like ?"]  	=   "%".$post->nmprojeto."%";
                }
                if (!empty($post->produto)) {
                    $where['d.idProduto = ?']           =   $post->produto;
                }
                if (!empty($post->orgao)) {
                    $where['d.idOrgao = ?']             =   $post->orgao;
                }
                if (!empty($post->parecerista)) {
                    $where['idAgenteParecerista = ?']   =   $post->parecerista;
                }

                if ($post->stAnalise == 1) {
                    $where['d.DtSolicitacao is ?']      =   new Zend_Db_Expr('null');
                    $where['d.DtResposta is ?']        	=   new Zend_Db_Expr('null');
                } elseif ($post->stAnalise == 2) {
                    $where['d.dtDevolucao is not ?']    =   new Zend_Db_Expr('null');
                    $where['d.DtResposta is not ?']    	=   new Zend_Db_Expr('null');
                }
                if (!empty($post->Area)) {
                    $where['p.Area = ?']                =   $post->Area;
                }
                if (!empty($post->Segmento)) {
                    $where['p.Segmento = ?']            =   $post->Segmento;
                }
                if (!empty($post->dt_i_prienvvinc)) {
                    $where  =   $this->tipos($where, 'p.DtProtocolo', $post->tp_dt_prienvvinc, $this->ajustarData($post->dt_i_prienvvinc), $this->ajustarData($post->dt_f_prienvvinc));
                }
                if (!empty($post->dt_i_ultenvvinc)) {
                    $where  =   $this->tipos($where, 'd.DtEnvio', $post->tp_dt_ultenvvinc, $this->ajustarData($post->dt_i_ultenvvinc), $this->ajustarData($post->dt_f_ultenvvinc));
                }
                if (!empty($post->dt_i_dispar)) {
                    $where  =   $this->tipos($where, 'd.DtDistribuicao', $post->tp_dt_dispar, $this->ajustarData($post->dt_i_dispar), $this->ajustarData($post->dt_f_dispar));
                }
                if (!empty($post->dt_i_devparcoo)) {
                    $where  =   $this->tipos($where, 'd.dtDevolucao', $post->tp_dt_devparcoo, $this->ajustarData($post->dt_i_devparcoo), $this->ajustarData($post->dt_f_devparcoo));
                }
                if (!empty($post->dt_i_devparmin)) {
                    $where  =   $this->tipos($where, 'p.DtFimExecucao', $post->tp_dt_devparmin, $this->ajustarData($post->dt_i_devparmin), $this->ajustarData($post->dt_f_devparmin));
                }
            break;
            case 'resconsolidacaoparecerista':
                if (!empty($post->orgao)) {
                    $where['uog.uog_orgao = ?']       =   $post->orgao;
                }
                if (!empty($post->parecerista)) {
                    $where['dp.idAgenteParecerista = ?']    =   $post->parecerista;
                }
                if (!empty($get->parecerista)) {
                    $where['dp.idAgenteParecerista = ?']    =   $get->parecerista;
                }
                if (!empty($post->dt_i_periodo)) {
                    $where                                  =   $this->tipos($where, 'dp.DtDistribuicao', $post->tp_dt_periodo, $this->ajustarData($post->dt_i_periodo), $this->ajustarData($post->dt_f_periodo));
                }
                if (!empty($post->Area)) {
                    $where['proj.Area = ?']                 =   $post->Area;
                }
                if (!empty($post->Seguimento)) {
                    $where['proj.Segmento = ?']             =   $post->Seguimento;
                }
            break;
            case 'resconsolidacaoparecerista2':
                if (!empty($post->parecerista)) {
                    $where['ag.idAgente = ?']               =   $post->parecerista;
                }
                if (!empty($get->parecerista)) {
                    $where['ag.idAgente = ?']               =   $get->parecerista;
                }
            break;
        }
        return $where;
    }

    private function gerarInfoPaginas($tipo, $where = array(), $paginacao = 0)
    {
        $post = Zend_Registry::get('post');
        $retorno = array();
        $ProjetosDAO            =   new Projetos();
        $distribuirParecerDAO   =   new Parecer_Model_DbTable_TbDistribuirParecer();
        switch ($tipo) {
            case 'resaguardandoparecer':

                if ($paginacao > 0) {
                    $total                  =   $distribuirParecerDAO->aguardandoparecerTotal($where)->current()->toArray();
                    $limit                  =   $this->paginacao($total["total"], $paginacao);
                    $resp                   =   $distribuirParecerDAO->aguardandoparecer($where, $limit['tamanho'], $limit['inicio']);
                } else {
                    $resp                   =   $distribuirParecerDAO->aguardandoparecer($where);
                }
                $cDistribuicao = 0;
                foreach ($resp as $val) {
                    $retorno[$val['idOrgao']]['nmOrgao']                                                                                                        								= $val['nmOrgao'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['nmParecerista']                                                                								= $val['nmParecerista'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['idPronac']                                       								= $val['IdPRONAC'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['pronac']                                         								= $val['pronac'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['nmProjeto']                                      								= $val['NomeProjeto'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['nmProduto']       								= $val['nmProduto'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['distribuicao'][$cDistribuicao]['dtEnvio']         = date('d/m/Y', strtotime($val['DtEnvio']));
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['distribuicao'][$cDistribuicao]['dtDistribuicao']  = date('d/m/Y', strtotime($val['DtDistribuicao']));
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['projetos'][$val['IdPRONAC']]['Produtos'][$val['idProduto']]['distribuicao'][$cDistribuicao]['qtDias']          = $val['nrDias'];

                    $cDistribuicao++;
                }
            break;
            case 'resumo':
                $resp                   =   $distribuirParecerDAO->aguardandoparecerresumo($where);
                $orgAnt = '';
                foreach ($resp as $val) {
                    if ($orgAnt == '' || $orgAnt!=$val['idOrgao']) {
                        $retorno[$val['idOrgao']]['qt'] =   0;
                        $orgAnt = $val['idOrgao'];
                    }
                    $retorno[$val['idOrgao']]['nmOrgao']                                            =   $val['nmOrgao'];
                    $retorno[$val['idOrgao']]['qt']                                                 +=  $val['qt'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['nmParecerista']    =   $val['nmParecerista'];
                    $retorno[$val['idOrgao']]['pareceristas'][$val['idAgente']]['qt']               =   $val['qt'];
                }
            break;
            case 'pareceremitido':
                if ($paginacao > 0) {
                    $total                  =   $ProjetosDAO->listaPareceremitidoTotal()->current()->toArray();
                    $limit                  =   $this->paginacao($total["total"], $paginacao);
                    $resp                   =   $ProjetosDAO->listaPareceremitido($limit['tamanho'], $limit['inicio']);
                } else {
                    $resp                   =   $ProjetosDAO->listaPareceremitido();
                }
                foreach ($resp as $val) {
                    $retorno[$val['IdPRONAC']]['idPronac']                                                                  =   $val['IdPRONAC'];
                    $retorno[$val['IdPRONAC']]['pronac']                                                                    =   $val['pronac'];
                    $retorno[$val['IdPRONAC']]['nmProjeto']                                                                 =   $val['NomeProjeto'];
                    $retorno[$val['IdPRONAC']]['nmOrgao']                                                                   =   $val['OrgaoOrigem'];
                    $retorno[$val['IdPRONAC']]['dtEnvio']                                                                   =   date('d/m/Y', strtotime($val['DtEnvio']));
                    $retorno[$val['IdPRONAC']]['dtRetorno']                                                                 =   date('d/m/Y', strtotime($val['DtRetorno']));
                    $retorno[$val['IdPRONAC']]['qtDias']                                                                    =   $val['Dias'];
                    $retorno[$val['IdPRONAC']]['qtDiasConsolidado']                                                         =   $val['QtdeConsolidar'];
                    $retorno[$val['IdPRONAC']]['dtConsolidacao']                                                            =   $val['DtConsolidacaoParecer'];
                    $retorno[$val['IdPRONAC']]['stParecer']                                                                 =   'Emitido';

                    $resp2 = $distribuirParecerDAO->pareceremitido($val['pronac']);

                    foreach ($resp2 as $val2) {
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['nmOrgao']                                           =   $val2['Sigla'];
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['nmProduto']         =   $val2['Produto'];
                        if ($val2['stPrincipal']) {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal'] =   'sim';
                        } else {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal'] =   '';
                        }
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['dtFechamento']      =   date('d/m/Y', strtotime($val2['DtDevolucao']));
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['area']              =   $val2['Area'];
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['segmento']          =   $val2['Segmento'];
                    }
                }
            break;
            case 'parecerconsolidado':
                if ($paginacao > 0) {
                    $total                  =   $ProjetosDAO->listaParecerconsolidadoTotal()->current()->toArray();
                    $limit                  =   $this->paginacao($total["total"], $paginacao);
                    $resp                   =   $ProjetosDAO->listaParecerconsolidado($limit['tamanho'], $limit['inicio']);
                } else {
                    $resp                   =   $ProjetosDAO->listaParecerconsolidado();
                }
                foreach ($resp as $val) {
                    $retorno[$val['IdPRONAC']]['idPronac']                                                                  =   $val['IdPRONAC'];
                    $retorno[$val['IdPRONAC']]['pronac']                                                                    =   $val['pronac'];
                    $retorno[$val['IdPRONAC']]['nmProjeto']                                                                 =   $val['NomeProjeto'];
                    $retorno[$val['IdPRONAC']]['nmOrgao']                                                                   =   $val['OrgaoOrigem'];
                    $retorno[$val['IdPRONAC']]['dtEnvio']                                                                   =   date('d/m/Y', strtotime($val['DtEnvio']));
                    $retorno[$val['IdPRONAC']]['dtRetorno']                                                                 =   date('d/m/Y', strtotime($val['DtRetorno']));
                    $retorno[$val['IdPRONAC']]['qtDiasRetorno']                                                             =   date('d/m/Y', strtotime($val['DtConsolidacaoParecer']));
                    $retorno[$val['IdPRONAC']]['dtConsolidacao']                                                            =   $val['Dias'];
                    $retorno[$val['IdPRONAC']]['qtDiasConsolidado']                                                         =   $val['QtdeConsolidar'];
                    $retorno[$val['IdPRONAC']]['stParecer']                                                                 =   'Consolidado';

                    $resp2 = $distribuirParecerDAO->parecerconsolidado($val['pronac']);

                    foreach ($resp2 as $val2) {
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['nmOrgao']                                        		=   $val2['Sigla'];
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['nmProduto']         	=   $val2['Produto'];
                        if ($val2['stPrincipal']) {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal']   	=   'sim';
                        } else {
                            $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['prodPrincipal']   		=   '';
                        }
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['dtFechamento']     		=   date('d/m/Y', strtotime($val2['DtDevolucao']));
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['area']            		=   $val2['Area'];
                        $retorno[$val['IdPRONAC']]['Orgaos'][$val2['idOrgao']]['Produtos'][$val2['idProduto']]['segmento']        		=   $val2['Segmento'];
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
                    $total                  =   $ProjetosDAO->geraldeanaliseTotal($where)->current()->toArray();
                    $limit                  =   $this->paginacao($total["total"], $paginacao);
                    $resp                   =   $ProjetosDAO->geraldeanalise($where, $limit['tamanho'], $limit['inicio']);
                } else {
                    $resp                   =   $ProjetosDAO->geraldeanalise($where);
                }
                foreach ($resp as $key=>$val) {
                    $retorno[$key]['pronac']                                            =   $val['PRONAC'];
                    $retorno[$key]['idpronac']                                          =   $val['IdPRONAC'];
                    $retorno[$key]['nmProjeto']                                         =   $val['NomeProjeto'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['nmProduto']          =   $val['Produto'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtPriEnvVinc']       =   $val['DtPrimeiroEnvio'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtUltEnvVinc']       =   $val['DtUltimoEnvio'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtDistPar']          =   $val['DtDistribuicao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['nmParecerista']      =   $val['Parecerista'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasDist']         =   $val['QtdeDiasParaDistribuir'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasCaixaPar']     =   $val['QtdeDiasComParecerista'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['tDiasAnal']          =   $val['QtdeTotalDiasAnalisar'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['dtDevParCoo']        =   $val['dtDevolucao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasParAnal']      =   $val['QtdeDiasPareceristaAnalisar'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasAguarAval']    =   $val['QtdeDiasDevolvidosCoordenador'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['stDiligencia']       =   $this->estadoDiligencia($val);
                    $retorno[$key]['Produtos'][$val['idProduto']]['nmOrgao']            =   $val['nmOrgao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['perExecProj']        =   $val['PeriodoExecucao'];
                    $retorno[$key]['Produtos'][$val['idProduto']]['qtDiasVencExecProj'] =   $val['QtdeDiasVencido'];
                }
            break;
            case 'resconsolidacaoparecerista':
                $zerado = false;
                $resp = $distribuirParecerDAO->analisePorParecerista($where);
                if ($resp->count() > 0) {
                    foreach ($resp as $val) {
                        if (!empty($post->stAnalise)) {
                            if ($post->stAnalise==1) {
                                if ($val->DtSolicitacao && $val->DtResposta == null) {
                                    $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                                }
                            }

                            if ($post->stAnalise==2) {
                                if ($val->DtSolicitacao && $val->DtResposta != null) {
                                    $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                                }
                            }

                            if ($post->stAnalise==3) {
                                if ($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) {
                                    $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                                }
                            }

                            if ($post->stAnalise==0) {
                                if (!($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) and !($val->DtSolicitacao && $val->DtResposta == null)) {
                                    $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                                }
                            }
                        } else {
                            $retorno = $this->dadosResconsolidacaoparecerista($val, $retorno);
                        }
                    }

                    if (count($retorno)>0) {
                        $cProduto = 0;
                        $contaProjetos = 0;
                        $cDistribuicao = 0;
                        foreach ($retorno['projetos'] as $projeto) {
                            $contaProjetos ++;
                            $cProduto += count($projeto['produtos']);
                            foreach ($projeto['produtos'] as $produto) {
                                $cDistribuicao += count($produto['distribuicao']);
                            }
                        }
                        $retorno['qtAnalise']  =   $cDistribuicao;
                    } else {
                        $zerado = true;
                    }
                } else {
                    $zerado = true;
                }

                if ($zerado) {
                    $agentesDAO = new Agente_Model_DbTable_Agentes();

                    $tela    = 'resconsolidacaoparecerista2';
                    $where   = $this->filtroGeral($tela);
                    $val     = $agentesDAO->dadosParecerista($where);
                    $retorno = $this->dadosParecerista($val, $retorno);
                    $retorno['qtAnalise']  =   0;
                }

            break;
        }
        return $retorno;
    }
    private function dadosParecerista($val, $retorno)
    {
        $retorno['nmParecerista']   =   $val['nmParecerista'];
        if (!empty($val->dtInicioAusencia)) {
            $dataini    =   date('d/m/Y', strtotime($val->dtInicioAusencia));
            $datafim    =   date('d/m/Y', strtotime($val->dtFimAusencia));
            $retorno['ferias']  =   $dataini.' h? '.$datafim;
        } else {
            $retorno['ferias']  =   'N&atilde;o agendada';
        }
        $area       =   $val->Area;
        $segmento   =   $val->Segmento;
        $nivel      =   $this->nivelParecerista($val->qtPonto);
        $retorno['area_segmento_nivel'][$area.'-'.$segmento.'-'.$nivel]                                                                     =   $area.'-'.$segmento.'-'.$nivel;
        return $retorno;
    }

    private function dadosResconsolidacaoparecerista($val, $retorno)
    {
        $retorno = $this->dadosParecerista($val, $retorno);
        $retorno['projetos'][$val['IdPRONAC']]['idPronac']                                                                                  =   $val['IdPRONAC'];
        $retorno['projetos'][$val['IdPRONAC']]['pronac']                                                                                    =   $val['pronac'];
        $retorno['projetos'][$val['IdPRONAC']]['nmProjeto']                                                                                 =   $val['NomeProjeto'];
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['nmProduto']                                                  =   $val['nmProduto'];
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['dtDistribuicao']  =   date('d/m/Y', strtotime($val['DtDistribuicao']));
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['distribuicao'][$val->idDistribuirParecer]['nrDias']          =   $val['nrDias'];
        $retorno['projetos'][$val['IdPRONAC']]['produtos'][$val['idProduto']]['diligencia']                                                 =   $this->estadoDiligencia($val);
        return $retorno;
    }
    private function estadoDiligencia($val)
    {

//        $diligenciaDAO = new Diligencia();
//
//        $where = array('idPronac = ?'=>$val['IdPRONAC'],'idProduto = ?'=>$val['idProduto']);
//
//        $respDiligencia = $diligenciaDAO->buscar($where);
//        if($respDiligencia->count()>0)


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
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice.png' title='Diligenciado' width='30px'/></p>";//1
            } elseif ($val->DtSolicitacao && $val->DtResposta != null) {
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice3.png' title='Dilig&ecirc;ncia respondida' width='30px'/></p>";//2
            } elseif ($val->DtSolicitacao && round(data::CompararDatas($val->DtDistribuicao)) > $val->tempoFimDiligencia) {
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice2.png' title='Dilig&ecirc;ncia n&atilde;o respondida' width='30px'/></p>";//3
            } else {
                $diligencia = "<p style='text-align: center;'><img src='../public/img/notice1.png' title='A diligenciar' width='30px'/></p>";//0
            }
        }
        return $diligencia;
    }
    private function nivelParecerista($qtPonto)
    {
        if ($qtPonto>=7 and $qtPonto<=14) {
            $nivel = 'I';
        } elseif ($qtPonto>=15 and $qtPonto<=19) {
            $nivel = 'II';
        } elseif ($qtPonto>=20 and $qtPonto<=25) {
            $nivel = 'III';
        }
    }
    public function pareceristaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);
        $post = Zend_Registry::get('post');
        $NomesDAO  =   new Nomes();
        $retorno   =   $NomesDAO->buscarPareceristas($post->idOrgao);

        foreach ($retorno as $value) {
            $pareceristas[] = array('id'=>$value->id,'nome'=>utf8_encode($value->Nome));
        }
        $this->_helper->json($pareceristas);
    }
    private function tipos($array, $labelCampo, $tp, $infoInicial, $infoFinal = '')
    {
        switch ($tp) {
            case 1:
                $array[$labelCampo.' > ?']=$infoInicial . " 00:00:00";
                $array[$labelCampo.' < ?']=$infoInicial . " 23:59:59";
                break;
            case 2:
                $array[$labelCampo.' > ?']=$infoInicial;
                $array[$labelCampo.' < ?']=$infoFinal;
                break;
            case 3:
                $array[$labelCampo.' > ?']=$infoInicial;
                break;
            case 4:
                $array[$labelCampo.' >= ?']=$infoInicial;
                break;
            case 5:
                $array[$labelCampo.' < ?']=$infoInicial;
                break;
            case 6:
                $array[$labelCampo.' <= ?']=$infoInicial;
                break;
        }
        return $array;
    }
    public function geraranexoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $post = Zend_Registry::get('post');
        $conteudo   =   $this->gerarInfoPaginas($post->tela, $this->filtroGeral($post->filtro), 10);
        $html       =   $this->gerarHTML($post->tela, $conteudo);
        if ($post->tipo == 'pdf') {
            $this->gerarPDF($html);
        }
        if ($post->tipo == 'xls') {
            $this->gerarXLS($html);
        }
    }

    public function gerarHTML($tipo, $conteudo)
    {
        $html = '';
        switch ($tipo) {
            case 'resaguardandoparecer':
                $html .= '<table class="tabela">';
                    foreach ($conteudo as $idOrgao => $orgao) {
                        $cParecerista = 1;
                        foreach ($orgao['pareceristas'] as $idParecerista => $pareceristas) {
                            $cProjeto = 1;
                            foreach ($pareceristas['projetos'] as $pronac=>$Projetos) {
                                $cProduto = 1;
                                foreach ($Projetos['Produtos'] as $idProdutos=>$produtos) {
                                    if ($cProduto==1) {
                                        $cDistribuir = 1;
                                        foreach ($produtos['distribuicao'] as $distribuicao) {
                                            if ($cDistribuir==1) {
                                                if ($cProjeto==1) {
                                                    if ($cParecerista == 1) {
                                                        $html .= "
                                                                <tr>
                                                                    <td colspan=\"9\">&Oacute;rg&atilde;o : {$orgao['nmOrgao']}</td>
                                                                </tr>
                                                                ";
                                                    }
                                                    $html .= "
                                                                <tr>
                                                                    <td colspan=\"9\">Parecerista : {$pareceristas['nmParecerista']}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th width=\"50\">PRONAC</th>
                                                                    <th>Nome do Projeto</th>
                                                                    <th>Produto</th>
                                                                    <th width=\"100\">Dt Distribui&ccedil;&atilde;o</th>
                                                                    <th width=\"50\">QTD de Dias Aguardando distribui&ccedil;&atilde;o</th>
                                                                    <th width=\"80\">Dt Envio</th>
                                                                </tr>
                                                             ";
                                                }
                                                $html .= "
                                                            <tr>
                                                                <td>{$Projetos['pronac']}</a></td>
                                                                <td>{$Projetos['nmProjeto']}</td>
                                                                <td>{$produtos['nmProduto']}</td>
                                                                <td>{$distribuicao['dtDistribuicao']}</td>
                                                                <td>{$distribuicao['qtDias']}</td>
                                                                <td>{$distribuicao['dtEnvio']}</td>
                                                            </tr>
                                                            ";
                                                $cDistribuir++;
                                            } else {
                                                $html .= "
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{$distribuicao['dtDistribuicao']}</td>
                                                    <td>{$distribuicao['qtDias']}</td>
                                                    <td>{$distribuicao['dtEnvio']}</td>
                                                </tr>
                                                ";
                                            }
                                        }
                                        $cProduto++;
                                    } else {
                                        $cDistribuir = 1;
                                        foreach ($produtos['distribuicao'] as $distribuicao) {
                                            if ($cDistribuir==1) {
                                                $html .= "
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{$produtos['nmProduto']}</td>
                                                    <td>{$distribuicao['dtDistribuicao']}</td>
                                                    <td>{$distribuicao['qtDias']}</td>
                                                    <td>{$distribuicao['dtEnvio']}</td>
                                                </tr>
                                                ";
                                                $cDistribuir++;
                                            } else {
                                                $html .= "
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{$distribuicao['dtDistribuicao']}</td>
                                                    <td>{$distribuicao['qtDias']}</td>
                                                    <td>{$distribuicao['dtEnvio']}</td>
                                                </tr>
                                                ";
                                            }
                                        }
                                    }
                                }
                                $cProjeto++;
                            }
                            $cParecerista++;
                        }
                    }
                $html .= "</table>";
            break;
            case 'resumo':
                $html .= "<table class=\"tabela apagar\">
                    <tr align=\"center\" bgcolor=\"#ABDA5D\">
                        <th align=\"left\">
                            <font color=\"#3A7300\" size=\"2\" face=\"Trebuchet MS, sans-serif\">
                                <b>Parecerista</b>
                            </font>
                        </th>
                        <th align=\"right\">
                            <font color=\"#3A7300\" size=\"2\" face=\"Trebuchet MS, sans-serif\"><b>An&aacute;lise</b></font>
                        </th>
                    </tr>
                    ";
                    foreach ($conteudo as $idOrgao=>$orgao) {
                        $html .= "
                        <tr align=\"right\" bgcolor=\"#FFFFFF\" valign=\"middle\">
                            <td align=\"left\"><font color=\"#333333\" size=\"2\" face=\"Trebuchet MS, sans-serif\">{$orgao['nmOrgao']}</font></td>

                            <td nowrap=\"nowrap\"><font color=\"#333333\" size=\"2\" face=\"Trebuchet MS, sans-serif\">{$orgao['qt']}
                                </font></td>
                        </tr>
                        ";
                        foreach ($orgao['pareceristas'] as $idPareceristas=>$parecerista) {
                            $html .= "
                            <tr align=\"right\" bgcolor=\"#f3f3f3\" valign=\"middle\">
                                <td align=\"left\" background=\"/scriptcase/appara E_SALICWEB/_lib/img/bg-celulas.gif\"><font color=\"#333333\" size=\"2\" face=\"Trebuchet MS, sans-serif\">&nbsp; &nbsp; &nbsp;{$parecerista['nmParecerista']}</font></td>
                                <td background=\"/scriptcase/appara E_SALICWEB/_lib/img/bg-celulas.gif\" nowrap=\"nowrap\"><font color=\"#333333\" size=\"2\" face=\"Trebuchet MS, sans-serif\">{$parecerista['qt']}</font></td>
                            </tr>
                            ";
                        }
                    }
                $html .= "</table>";
            break;
            case 'pareceremitido':
                $html .= "
                <table class=\"tabela\">
                    <tr>
                        <th>PRONAC</th>
                        <th>Nome do Projeto</th>
                        <th>&Oacute;rg&atilde;o</th>
                        <th width=\"150\">Dt de Envio a vinculada</th>
                        <th width=\"120\">Dt Retorno ao MINC</th>
                        <th>Qtde dias at&eacute; o retorno</th>
                        <th>Status do parecer</th>
                    </tr>
                    ";
                    foreach ($conteudo as $key=>$projeto) {
                        $html .= "
                        <tr>
                            <td align=\"center\">{$projeto['pronac']}</a></td>
                            <td>{$projeto['nmProjeto']}</td>
                            <td align=\"center\">{$projeto['nmOrgao']}</td>
                            <td align=\"center\">{$projeto['dtEnvio']}</td>
                            <td align=\"center\">{$projeto['dtRetorno']}</td>
                            <td align=\"center\">{$projeto['qtDias']}</td>
                            <td>{$projeto['stParecer']}</td>
                        </tr>
                        <tr id=\"produtos_{$key}\">
                            <td></td>
                            <td colspan=\"7\">
                                <table class=\"tabela\">
                                    <tr>
                                        <th width=\"100\">&Oacute;rg&atilde;o				</th>
                                        <th>Produto				</th>
                                        <th>PRODUTO PRINCIPAL</th>
                                        <th width=\"100\">Dt. de fechamento da an�lise t�cnica</th>
                                        <th width=\"150\">&Aacute;rea				</th>
                                        <th width=\"150\">Segmento Cultural	</th>
                                    </tr>
                                    ";
                        foreach ($projeto['Orgaos'] as $Orgao) {
                            $cProduto = 1;
                            foreach ($Orgao['Produtos'] as $produto) {
                                $nmOrg = '';
                                if ($cProduto==1) {
                                    $nmOrg = $Orgao['nmOrgao'];
                                }
                                $pdPrincipal = '';
                                if ($produto['prodPrincipal']=='sim') {
                                    $pdPrincipal = 'Principal';
                                }
                                $html .= "
                                            <tr>
                                                <td>{$nmOrg}</td>
                                                <td>{$produto['nmProduto']}</td>
                                                <td align=\"center\">{$pdPrincipal}</td>
                                                <td>{$produto['dtFechamento']}</td>
                                                <td>{$produto['area']}</td>
                                                <td>{$produto['segmento']}</td>
                                            </tr>
                                            ";
                                $cProduto++;
                            }
                        }

                        $html .= "</table>

                            </td>
                        </tr>";
                    }
                $html .= "</table>";
            break;
            case 'parecerconsolidado':
                $html .= "<table class=\"tabela\">
                    <tr>
                            <th width=\"40\">PRONAC</th>
                            <th>Nome do Projeto</th>
                            <th>&Oacute;rg&atilde;o</th>
                            <th width=\"130\">Dt.Envio a Vinculada</th>
                            <th width=\"120\">Dt.Retorno ao MinC</th>
                            <th width=\"140\">Qtde dias at&eacute; retorno</th>
                            <th width=\"180\">Dt.Consolida&ccedil;&atilde;o do Parecer</th>
                            <th width=\"180\">Qtde dias at&eacute; a consolida&ccedil;&atilde;o</th>
                            <th>Status do parecer</th>
                    </tr>
                    ";
                    foreach ($conteudo as $key=>$projeto) {
                        $html .= "
                        <tr>
                            <td align=\"center\">{$projeto['pronac']}</td>
                            <td>{$projeto['nmProjeto']}</td>
                            <td align=\"center\">{$projeto['nmOrgao']}</td>
                            <td align=\"center\">{$projeto['dtEnvio']}</td>
                            <td align=\"center\">{$projeto['dtRetorno']}</td>
                            <td align=\"center\">{$projeto['qtDiasRetorno']}</td>
                            <td align=\"center\">{$projeto['dtConsolidacao']}</td>
                            <td align=\"center\">{$projeto['qtDiasConsolidado']}</td>
                            <td>{$projeto['stParecer']}</td>
                        </tr>
                        <tr id=\"produtos_{$key}\">
                            <td></td>
                            <td colspan=\"8\">
                                <table class=\"tabela\">
                                    <tr>
                                        <th width=\"100\">&Oacute;rg&atilde;o				</th>
                                        <th>Produto				</th>
                                        <th>PRODUTO PRINCIPAL</th>
                                        <th width=\"100\">Dt. de fechamento da an�lise t�cnica</th>
                                        <th width=\"150\">&Aacute;rea				</th>
                                        <th width=\"150\">Segmento Cultural	</th>
                                    </tr>
                                    ";
                        foreach ($projeto['Orgaos'] as $Orgao) {
                            $cProduto = 1;
                            foreach ($Orgao['Produtos'] as $produto) {
                                $nmOrg = '';
                                if ($cProduto==1) {
                                    $nmOrg = $Orgao['nmOrgao'];
                                }
                                $Principal = '';
                                if ($produto['prodPrincipal']=='sim') {
                                    $Principal = 'Principal';
                                }

                                $html .= "
                                            <tr>
                                                <td>{$nmOrg}</td>
                                                <td>{$produto['nmProduto']}</td>
                                                <td align=\"center\">{$Principal}</td>
                                                <td>{$produto['dtFechamento']}</td>
                                                <td>{$produto['area']}</td>
                                                <td>{$produto['segmento']}</td>
                                            </tr>
                                            ";
                                $cProduto++;
                            }
                        }
                        $html .= "</table>
                            </td>
                        </tr>";
                    }
                $html .= "</table>";
            break;
            case 'resgeraldeanalise':
                $post = Zend_Registry::get('post');
                $html .= "
                <table class=\"tabela\" >
                    <tr>
                        ";
                    foreach ($post->cpconsulta_dest as $t) {
                        $html .= '<th>'.$this->view->titulo[$t-1].'</th>';
                    }
                    $html .= "</tr>";
                    foreach ($conteudo as $projeto) {
                        $cProduto=0;
                        foreach ($projeto['Produtos'] as $produto) {
                            $html .= "<tr>";
                            $linha[0]   =   "<td align='center'>";
                            if ($cProduto==0) {
                                $linha[0]   .=   $projeto['pronac'];
                            }
                            $linha[0]   .=   "</td>";
                            $linha[1]   =   '<td>';
                            if ($cProduto==0) {
                                $linha[1]   .=   $projeto['nmProjeto'];
                            }
                            $linha[1]   .=   '</td>';
                            $linha[2]   =   '<td>'.$produto['nmProduto'].'</td>';
                            $linha[3]   =   '<td>'.$produto['dtPriEnvVinc'].'</td>';
                            $linha[4]   =   '<td>'.$produto['dtUltEnvVinc'].'</td>';
                            $linha[5]   =   '<td>'.$produto['dtDistPar'].'</td>';
                            $linha[6]   =   '<td>'.$produto['nmParecerista'].'</td>';
                            $linha[7]   =   '<td>'.$produto['qtDiasDist'].'</td>';
                            $linha[8]   =   '<td>'.$produto['qtDiasCaixaPar'].'</td>';
                            $linha[9]   =   '<td>'.$produto['tDiasAnal'].'</td>';
                            $linha[10]   =   '<td>'.$produto['dtDevParCoo'].'</td>';
                            $linha[11]   =   '<td>'.$produto['qtDiasParAnal'].'</td>';
                            $linha[12]   =   '<td>'.$produto['qtDiasAguarAval'].'</td>';
                            $linha[13]   =   '<td align="center">'.$produto['stDiligencia'].'</td>';
                            $linha[14]   =   '<td align="center">'.$produto['nmOrgao'].'</td>';
                            $linha[15]   =   '<td align="center">'.$produto['perExecProj'].'</td>';
                            $linha[16]   =   '<td align="center">'.$produto['qtDiasVencExecProj'].'</td>';

                            foreach ($post->cpconsulta_dest as $t) {
                                $html .= $linha[$t-1];
                            }

                            $html .="</tr>";
                            $cProduto++;
                        }
                    }
                    $html .="</table>";
            break;
            case 'resconsolidacaoparecerista':
                $html .="
                <table class=\"tabela\">
                    <tr>
                        <th colspan=\"3\" style=\"font-size: 13px; font-family: sans-serif; text-align: left;\">Parecerista : {$conteudo['nmParecerista']}</th>
                    </tr>
                    <tr>
                        <td>QTD de An�lise: {$conteudo['qtAnalise']}</td>
                        <td>F�rias:{$conteudo['ferias']}</td>
                    </tr>
                    <tr>
                        <td> �reas, Segmentos e N�vel</td>
                        <td>
                            ";
                            foreach ($conteudo['area_segmento_nivel'] as $val) {
                                $html .= $val.'<br />';
                            }
                            $html .="
                        </td>

                    </tr>
                </table>
                ";
                if (isset($conteudo['projetos']) and is_array($conteudo['projetos']) and count($conteudo['projetos'])>0) {
                    $html .="<table class=\"tabela\">
                        <tr>
                            <th width=\"50\">PRONAC</th>
                            <th>Nome do Projeto</th>
                            <th>Produto</th>
                            <th width=\"100\">Dt Distribui&ccedil;&atilde;o</th>
                            <th width=\"50\">Nr Dias</th>
                            <th width=\"80\">Dilig&ecirc;ncia</th>
                            <!--<th>Situa�?o</th>
                            <th>Provid?ncia Tomada</th>-->
                        </tr>
                        ";

                    foreach ($conteudo['projetos'] as $projeto) {
                        $cProduto = 1;
                        foreach ($projeto['produtos'] as $produto) {
                            if ($cProduto == 1) {
                                $cdistribuicao = 1;
                                foreach ($produto['distribuicao'] as $distribuicao) {
                                    if ($cdistribuicao==1) {
                                        $html .="
                                            <tr>
                                                <td>{$projeto['pronac']}</td>
                                                <td>{$projeto['nmProjeto']}</td>
                                                <td>{$produto['nmProduto']}</td>
                                                <td>{$distribuicao['dtDistribuicao']}</td>
                                                <td>{$distribuicao['nrDias']}</td>
                                                <td>{$produto['diligencia']}</td>
                                                <!--<td>B11 - Encaminhado para an�lise t�cnica</td>
                                                <td>Proposta transformada em projeto cultural</td>-->
                                            </tr>
                                            ";
                                    } else {
                                        $html .="
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>{$distribuicao['dtDistribuicao']}</td>
                                                <td>{$distribuicao['nrDias']}</td>
                                                <td></td>
                                                <!--<td>B11 - Encaminhado para an�lise t�cnica</td>
                                                <td>Proposta transformada em projeto cultural</td>-->
                                            </tr>
                                            ";
                                    }
                                    $cdistribuicao++;
                                }
                                $cProduto++;
                            } else {
                                $cdistribuicao = 1;
                                foreach ($produto['distribuicao'] as $distribuicao) {
                                    if ($cdistribuicao==1) {
                                        $html .="
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>{$produto['nmProduto']}</td>
                                                <td>{$distribuicao['dtDistribuicao']}</td>
                                                <td>{$distribuicao['nrDias']}</td>
                                                <td>{$produto['diligencia']}</td>
                                                <!--<td>B11 - Encaminhado para an�lise t�cnica</td>
                                                <td>Proposta transformada em projeto cultural</td>-->
                                            </tr>
                                            ";
                                    } else {
                                        $html .="
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>{$distribuicao['dtDistribuicao']}</td>
                                                <td>{$distribuicao['nrDias']}</td>
                                                <td></td>
                                                <!--<td>B11 - Encaminhado para an�lise t�cnica</td>
                                                <td>Proposta transformada em projeto cultural</td>-->
                                            </tr>
                                            ";
                                    }
                                    $cdistribuicao++;
                                }
                            }
                        }
                    }
                    $html .="</table>";
                } else {
                    $html .='<table class="tabela">
                            <tr>
                                <th>
                                    Este parecerista n&atilde;o tem analises!
                                </th>
                            </tr>
                        </table>';
                }
            break;
        }
        return $html;
    }
    public function gerarXLS($html)
    {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: inline; filename=file.ods;");
        echo $html;
    }
    public function gerarPDF($html)
    {
        ini_set("memory_limit", "2048M");
        set_time_limit(380);
        error_reporting(0);



        $output = '
                            <style>
                                    th{
                                    background:#ABDA5D;
                                    color:#3A7300;
                                    text-transform:uppercase;
                                    font-size:14px;
                                    font-weight: bold;
                                    font-family: sans-serif;
                                    height: 16px;
                                    line-height: 16px;
                            }
                            td{
                                    color:#000;
                                    font-size:14px;
                                    font-family: sans-serif;
                                    height: 14px;
                                    line-height: 14px;
                            }
                            .blue{
                                    color: blue;
                            }
                            .red{
                                    color: red;
                            }
                            .orange{
                                    color: orange;
                            }
                            .green{
                                    color: green;
                            }

                            .direita{
                                    text-align: right;
                            }

                            .centro{
                                    text-align: center;
                            }

                            </style>';



        $output .= $html;

        $patterns = array();
        $patterns[] = '/<table.*?>/is';
        $patterns[] = '/size="3px"/is';
        $patterns[] = '/size="4px"/is';
        $patterns[] = '/size="2px"/is';
        $patterns[] = '/<thead>/is';
        $patterns[] = '/<\/thead>/is';
        $patterns[] = '/<tbody>/is';
        $patterns[] = '/<\/tbody>/is';
        $patterns[] = '/<col.*?>/is';
        $patterns[] = '/<a.*?>/is';
        $patterns[] = '/<img.*?>/is';

        $replaces = array();
        $replaces[] = '<table cellpadding="0" cellspacing="1" border="1" width="90%" align="center">';
        $replaces[] = 'size="14px"';
        $replaces[] = 'size="14px"';
        $replaces[] = 'size="11px"';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $output = preg_replace($patterns, $replaces, utf8_encode($output));
        $pdf=new mPDF('pt', 'A4', 12, '', 8, 8, 5, 14, 9, 9, 'P');
        $pdf->allow_charset_conversion = true;
        $pdf->charset_in='UTF-8';
        $pdf->WriteHTML($output);
        $pdf->Output();
    }
}
