<?php

class LocalizacaoFisicaController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $this->permissoesGrupo = array();
        $this->permissoesOrgao = array();

        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                if (!in_array($grupo->gru_codigo, $this->permissoesGrupo)) {
                    $this->permissoesGrupo[] = $grupo->gru_codigo;
                }
                if (!in_array($grupo->uog_orgao, $this->permissoesOrgao)) {
                    $this->permissoesOrgao[] = $grupo->uog_orgao;
                }
            }
        }
                        
        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $this->permissoesGrupo) : parent::perfil(4, $this->permissoesGrupo);

        $this->usuarioLogado = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;
        parent::init();

        /* =============================================================================== */
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        /* =============================================================================== */
        $this->verificarPermissaoAcesso(true, false, false);
        
        # Paginator
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacaoMinc.phtml');
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->view->filtro = 'pronac';
            
        if (!empty($_GET) || !empty($_POST)) {
            $filtro = $this->_getParam('filtro');
            $pronac = $this->_getParam('pronac');
            if (isset($filtro) && 'pronac' == $filtro && empty($pronac)) {
                parent::message("Digite o n�mero do Pronac!", "localizacao-fisica/index", "ALERT");
            }
                
            $this->intTamPag = 10;

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
                $order = array(1); //idManterPortaria
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
            $where['Orgao in (?)'] = $this->permissoesOrgao;

            if (isset($_POST['filtro']) || isset($_GET['filtro'])) {
                $filtro = isset($_POST['filtro']) ? $_POST['filtro'] : $_GET['filtro'];
                $this->view->filtro = $filtro;
            }
                
            if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
                $where['p.AnoProjeto+p.Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
                $this->view->pronac = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
                unset($where['Orgao in (?)']);
            }

            if ((isset($_POST['tecnico']) && !empty($_POST['tecnico'])) || (isset($_GET['tecnico']) && !empty($_GET['tecnico']))) {
                $where['p.Logon = ?'] = isset($_POST['tecnico']) ? $_POST['tecnico'] : $_GET['tecnico'];
                $this->view->tecnico = isset($_POST['tecnico']) ? $_POST['tecnico'] : $_GET['tecnico'];
            }

            if ((isset($_POST['vinculada']) && !empty($_POST['vinculada'])) || (isset($_GET['vinculada']) && !empty($_GET['vinculada']))) {
                $where['p.Orgao = ?'] = isset($_POST['vinculada']) ? $_POST['vinculada'] : $_GET['vinculada'];
                $this->view->vinculada = isset($_POST['vinculada']) ? $_POST['vinculada'] : $_GET['vinculada'];
            }

            $LocalizacaoFisicaModel = new LocalizacaoFisicaModel();
            $total = $LocalizacaoFisicaModel->localizarProjetos($where, $order, null, null, true);
            $fim = $inicio + $this->intTamPag;

            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

            $busca = $LocalizacaoFisicaModel->localizarProjetos($where, $order, $tamanho, $inicio);
                
            if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
                if (!in_array($busca[0]->Codigo, $this->permissoesOrgao)) {
                    parent::message("Usu�rio sem autoriza��o no �rg�o do projeto! Este projeto se encontra em {$busca[0]->Sigla}.", "localizacao-fisica/index", "ALERT");
                }
            }
                
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
        
        $localizacaoFisicaModel = new LocalizacaoFisicaModel();
        $this->view->tecnicos = $localizacaoFisicaModel->getTecnicos(array(Zend_Auth::getInstance()->getIdentity()->usu_orgao));
        $this->view->vinculadas = $localizacaoFisicaModel->getVinculadas($this->permissoesOrgao);
    }
        
    public function imprimirLocalizacaoFisicaProjetoAction()
    {
        $this->intTamPag = 10;
            
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
            $order = array(1); //idManterPortaria
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
        $where['Orgao in (?)'] = $this->permissoesOrgao;

        if (isset($_POST['filtro']) || isset($_GET['filtro'])) {
            $filtro = isset($_POST['filtro']) ? $_POST['filtro'] : $_GET['filtro'];
            $this->view->filtro = $filtro;
        }

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['p.AnoProjeto+p.Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronac = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            unset($where['Orgao in (?)']);
        }

        if ((isset($_POST['tecnico']) && !empty($_POST['tecnico'])) || (isset($_GET['tecnico']) && !empty($_GET['tecnico']))) {
            $where['p.Logon = ?'] = isset($_POST['tecnico']) ? $_POST['tecnico'] : $_GET['tecnico'];
            $this->view->tecnico = isset($_POST['tecnico']) ? $_POST['tecnico'] : $_GET['tecnico'];
        }

        if ((isset($_POST['vinculada']) && !empty($_POST['vinculada'])) || (isset($_GET['vinculada']) && !empty($_GET['vinculada']))) {
            $where['p.Orgao = ?'] = isset($_POST['vinculada']) ? $_POST['vinculada'] : $_GET['vinculada'];
            $this->view->vinculada = isset($_POST['vinculada']) ? $_POST['vinculada'] : $_GET['vinculada'];
        }

        $LocalizacaoFisicaModel = new LocalizacaoFisicaModel();
        $total = $LocalizacaoFisicaModel->localizarProjetos($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $LocalizacaoFisicaModel->localizarProjetos($where, $order, $tamanho, $inicio);

        if (isset($get->xls) && $get->xls) {
            $html = '';
            $html .= '<table style="border: 1px">';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="8">Localizar Projeto</td></tr>';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="8">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
            $html .='<tr><td colspan="8"></td></tr>';

            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">#</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">PRONAC</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Nome do Projeto</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">CPF / CNPJ</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Proponente</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Localiza��o</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">T�cnico</th>';
            $html .= '</tr>';

            $x=1;
            foreach ($busca as $d) {
                $cpfcnpj = Validacao::mascaraCPFCNPJ($d->CgcCpf);
                            
                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">'.$x.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$d->Pronac.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$d->NomeProjeto.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$cpfcnpj.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$d->NomeProponente.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$d->Localizacao.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$d->NomeTecnico.'</td>';
                $html .= '</tr>';
                $x++;
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Localizar_Projeto.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->qtdRegistros = $total;
            $this->view->dados = $busca;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    }

    /**
     *
     */
    public function cadastrarAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        # projeto
        $projetoModel = new Projetos();
        $this->view->projeto = $projetoModel->fetchRow(array('AnoProjeto + Sequencial = ?' => $this->getRequest()->getParam('pronac')));
        $this->view->projeto->NomeProjeto = utf8_encode($this->view->projeto->NomeProjeto);
        # tecnicos
        $localizacaoFisicaModel = new LocalizacaoFisicaModel();
        $this->view->tecnicos = $localizacaoFisicaModel->getTecnicos(array(Zend_Auth::getInstance()->getIdentity()->usu_orgao));
        foreach ($this->view->tecnicos as $tecnico) {
            $tecnico->usu_nome = utf8_encode($tecnico->usu_nome);
        }
        # persistencia
        if ($this->getRequest()->isPost()) {
            $redirectUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->view->url(array('controller' => 'localizacao-fisica', 'action' => 'index'), null, true);
            if (!$this->getRequest()->getParam('localizacao')) {
                $this->_helper->flashMessenger->addMessage("Erro ao salvar localiza&ccedil;&atilde;o f&iacute;sica do projeto. Preencha o campo obrigat&oacute;rio.");
                $this->_helper->flashMessengerType->addMessage("ERROR");
                $this->_redirect($_SERVER['HTTP_REFERER']);
            }
            $projetoModel->update(
                array('Logon' => $this->getRequest()->getParam('tecnico'),),
                array('AnoProjeto + Sequencial = ?' => $this->getRequest()->getParam('pronac'))
            );
            $localizacaoFisicaModel->insert(
                array(
                    'IdPronac' => $this->getRequest()->getParam('idPronac'),
                    'Pronac' => $this->getRequest()->getParam('pronac'),
                    'TecnicoAntigo' => $this->getRequest()->getParam('logon'),
                    'TecnicoAtual' => $this->getRequest()->getParam('tecnico'),
                    'Localizacao' => $this->getRequest()->getParam('localizacao'),
                )
            );
            if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                $redirectUrl = $_SERVER['HTTP_REFERER'];
            }
            $this->_redirect($redirectUrl);
        }
    }

    /**
     *
     */
    public function historicoAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        # projeto
        $projetoModel = new Projetos();
        $this->view->projeto = $projetoModel->fetchRow(array('AnoProjeto + Sequencial = ?' => $this->getRequest()->getParam('pronac')));
        $this->view->projeto->NomeProjeto = utf8_encode($this->view->projeto->NomeProjeto);
        # historico
        $localizacaoFisicaModel = new LocalizacaoFisicaModel();
        $this->view->localizacoes = $localizacaoFisicaModel->pesquisar($this->getRequest()->getParam('pronac'));
        foreach ($this->view->localizacoes as $localizacao) {
            $localizacao->TecnicoAntigoNome = utf8_encode($localizacao->TecnicoAntigoNome);
            $localizacao->TecnicoAtualNome = utf8_encode($localizacao->TecnicoAtualNome);
            $localizacao->Localizacao = utf8_encode($localizacao->Localizacao);
        }
    }
        
    /**
     *
     */
    public function imprimirHistoricoAction()
    {
        Zend_Layout::getMvcInstance()->disableLayout();
        # projeto
        $projetoModel = new Projetos();
        $this->view->projeto = $projetoModel->fetchRow(array('AnoProjeto + Sequencial = ?' => $this->getRequest()->getParam('pronac')));
        $this->view->projeto->NomeProjeto = utf8_encode($this->view->projeto->NomeProjeto);
        # historico
        $localizacaoFisicaModel = new LocalizacaoFisicaModel();
        $this->view->localizacoes = $localizacaoFisicaModel->pesquisar($this->getRequest()->getParam('pronac'));
        foreach ($this->view->localizacoes as $localizacao) {
            $localizacao->TecnicoAntigoNome = utf8_encode($localizacao->TecnicoAntigoNome);
            $localizacao->TecnicoAtualNome = utf8_encode($localizacao->TecnicoAtualNome);
            $localizacao->Localizacao = utf8_encode($localizacao->Localizacao);
        }
    }
}
