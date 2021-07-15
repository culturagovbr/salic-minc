<?php

class PrincipalController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio ás Leis de Incentivo é Cultura"; // tetulo da pegina
        $auth = Zend_Auth::getInstance(); // pega a autenticaeeo
        $usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usuerio
        $grupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sesseo com o grupo ativo
        parent::perfil();

        parent::init();
    }

    /**
     * Pegina inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        $this->view->saudacao = Data::saudacao() . "! " . Data::mostraData() . ".";

        $tbComunicados = new tbComunicados();

        $where['stEstado = ?'] = 1;
        $where['stOpcao = ?'] = 0;
        $ordem = array();

        $rs = $tbComunicados->listarComunicados($where, $ordem);

        $this->view->comunicados = $rs;
    }

    public function abasAction()
    {
    } // fecha metodo abasAction()

    public function textoAction()
    {
    } // fecha metodo textoAction()

    public function gridAction()
    {
    } // fecha metodo gridAction()

    public function caixadetextoAction()
    {
    } // fecha metodo caixadetextoAction()

    public function modalAction()
    {
    } // fecha metodo modalAction()

    public function botoesAction()
    {
    } // fecha metodo botoesAction()

    public function exemplosAction()
    {
    } // fecha metodo exemplosAction()

    /**
     * Metodo listarComunicados()
     * @access public
     * @param void
     * @return List
     */
    public function listarComunicadosAction()
    {
        //header("Content-Type: text/html; charset=ISO-8859-1");
        $this->_helper->layout->disableLayout();
        $post = Zend_Registry::get('post');
        $this->intTamPag = 1;

        $tbComunicados = new tbComunicados();

        $where = array();

        $periodo1 = $this->_request->getParam("periodo1");
        $periodo2 = $this->_request->getParam("periodo2");
        $stEstado = $this->_request->getParam("stEstado");
        $stOpcao = $this->_request->getParam("stOpcao");

        if (!empty($periodo1) && !empty($periodo1)) {
            $where['dtInicioVigencia >= ?'] = $periodo1;
            $where['dtTerminoVigencia <= ?'] = $periodo2;
        }

        if ($stEstado != '') {
            $where['stEstado = ?'] = $stEstado;
        }

        if ($stOpcao != '') {
            $where['stOpcao = ?'] = $stOpcao;
        }


        $pag = 1;
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        if (isset($post->tamPag)) {
            $this->intTamPag = $post->tamPag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $total = $tbComunicados->listarComunicados($where, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim > $total) {
            $fim = $total;
        }

        $ordem = array("6 DESC");
        $rs = $tbComunicados->listarComunicados($where, $ordem, $tamanho, $inicio);

        $this->view->registros = $rs;
        $this->view->pag = $pag;
        $this->view->total = $total;
        $this->view->inicio = ($inicio + 1);
        $this->view->fim = $fim;
        $this->view->totalPag = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }

    /**
     * Metodo buscarProjeto()
     * @access public
     * @param void
     * @return void
     */
    public function buscarprojetoAction()
    {
        $Pronac = $this->_request->getParam("Pronac");

        if (empty($Pronac)) {
            parent::message("Informe o Pronac.", "principal/index", "ERROR");
        }

        $tbProjetos = new Projetos();
        $where = [];
        $where['AnoProjeto + Sequencial'] = $Pronac;
        $projeto = $tbProjetos->findBy($where);

        if (empty($projeto)) {
            parent::message("Nenhum projeto encontrado com o n&uacute;mero de Pronac informado.", "principal/index", "ERROR");
        }

        $this->redirect('/projeto/#/' . $projeto['IdPRONAC']);
    }
}
