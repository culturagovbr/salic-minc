<?php

class PrincipalproponenteController extends MinC_Controller_Action_Abstract
{
    protected $idAgente = null;
    protected $idUsuario;

    public function init()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $GrupoAtivo->codGrupo = 1111;
        $auth = Zend_Auth::getInstance();
        $auth = array_change_key_case((array) $auth->getIdentity());

        parent::perfil(4); // autenticao zend
        parent::init(); // chama o init() do pai GenericControllerNew
        $this->idUsuario = isset($auth['usu_codigo']) ? $auth['usu_codigo'] : $auth['idusuario'];
        $Usuario = new Autenticacao_Model_DbTable_Usuario();
        $Agente = new Agente_Model_DbTable_Agentes();
        $this->idAgente = $auth['idusuario'];
    }

    public function indexAction()
    {
//        xd('AAAAAAAAAAAAAAAAA');
        $a = new Agente_Model_DbTable_Agentes();
        $verificarvinculo = $a->buscarAgenteVinculoResponsavel(array('vr.idAgenteProponente = ?'=>$this->idAgente, 'vprp.siVinculoProposta = ?'=>0))->count();
        $tbComunicados = new tbComunicados();
        $where['stEstado = ?'] = 1;
        $where['stOpcao = ?'] = 1;
        $ordem = array();
        $rs = $tbComunicados->listarComunicados($where, $ordem);

        $this->view->vinculos = ($verificarvinculo > 0)? true : false;
        $this->view->comunicados = $rs;
        $this->view->saudacao = Data::saudacao() . "! " . Data::mostraData() . ".";
    }

    /**
     * M�todo listarComunicados()
     * @access public
     * @param void
     * @return List
     */
    public function listarComunicadosAction()
    {
        $this->_helper->layout->disableLayout();
        $post = Zend_Registry::get('post');
        $this->intTamPag = 1;

        $tbComunicados = new tbComunicados();

        $where = array();

        $periodo1 = $this->_request->getParam("periodo1");
        $periodo2 = $this->_request->getParam("periodo2");
        $stEstado = $this->_request->getParam("stEstado");
        $stOpcao  = $this->_request->getParam("stOpcao");

        if (!empty($periodo1) && !empty($periodo1)) {
            $where['dtiniciovigencia >= ?']  = $periodo1;
            $where['dtterminovigencia <= ?'] = $periodo2;
        }

        if ($stEstado != '') {
            $where['stestado = ?'] = $stEstado;
        }

        if ($stOpcao != '') {
            $where['stopcao = ?'] = $stOpcao;
        }


        $pag = 1;
        //$get = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        if (isset($post->tamPag)) {
            $this->intTamPag = $post->tamPag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim    = $inicio + $this->intTamPag;

        $total = $tbComunicados->listarComunicados($where, array(), null, null, true);


        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) {
            $fim = $total;
        }

        $ordem = array("6 DESC");
        $rs = $tbComunicados->listarComunicados($where, $ordem, $tamanho, $inicio);

        $this->view->registros 		  = $rs;
        $this->view->pag 			  = $pag;
        $this->view->total 			  = $total;
        $this->view->inicio 		  = ($inicio+1);
        $this->view->fim 			  = $fim;
        $this->view->totalPag 		  = $totalPag;
        $this->view->parametrosBusca  = $_POST;
    }
}
