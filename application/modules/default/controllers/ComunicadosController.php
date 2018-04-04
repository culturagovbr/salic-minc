<?php

class ComunicadosController extends MinC_Controller_Action_Abstract
{
    /**
     * @var integer (variável com o id do grupo ativo)
     * @access private
     */
    private $GrupoAtivoSalic = 0;
    private $intTamPag = 1;
    
    
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticação

        // define as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Gestor Salic
        
        parent::perfil(1, $PermissoesGrupo);

        // pega o idAgente do usuário logado
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $this->getIdUsuario["idAgente"];
            } else {
                $this->getIdUsuario = 0;
            }
        } else {
            $this->getIdUsuario = $auth->getIdentity()->IdUsuario;
        }

        parent::init();
    }

    /**
     * Método index()
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        $this->_redirect("comunicados/consultar");
    } // fecha método indexAction()

    /**
     * Método novo()
     * @access public
     * @param void
     * @return void
     */
    public function novoAction()
    {
    } // fecha método novoAction()
    
    /**
     * Método consultar()
     * @access public
     * @param void
     * @return void
     */
    public function consultarAction()
    {
    } // fecha método consultarAction()
    
    /**
     * Método ativos()
     * @access public
     * @param void
     * @return List
     */
    public function ativosAction()
    {
    } // fecha método ativosAction()

    /**
     * Método listarComunicados()
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
        $stOpcao  = $this->_request->getParam("stOpcao");

        if (!empty($periodo1) && !empty($periodo1)) {
            $where['dtInicioVigencia >= ?']  = $periodo1;
            $where['dtTerminoVigencia <= ?'] = $periodo2;
        }
        
        if ($stEstado != '') {
            $where['stEstado = ?'] = $stEstado;
        }
        
        if ($stOpcao != '') {
            $where['stOpcao = ?'] = $stOpcao;
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
        
    /**
     * Método desativados()
     * @access public
     * @param void
     * @return List
     */
    public function desativadosAction()
    {
    } // fecha método desativadosAction()
    
    
    /**
     * Método editar()
     * @access public
     * @param void
     * @return List
     */
    public function editarAction()
    {
        $tbComunicados = new tbComunicados();
        
        $idComunicado = $this->_request->getParam("idComunicado");
        
        $where['idComunicado = ?'] = $idComunicado;
        
        $comunicado = $tbComunicados->listarComunicados($where);
        
        $this->view->comunicado = $comunicado;
    } // fecha método editarAction()
    
    /**
     * Método salvar()
     * @access public
     * @param void
     * @return List
     */
    public function salvarAction()
    {
        $tbComunicados = new tbComunicados();
        
        $url 				= $this->_request->getParam("url");
        $idComunicado 		= $this->_request->getParam("idComunicado");
        $dtInicioVigencia 	= $this->_request->getParam("dtInicioVigencia");
        $dtTerminoVigencia 	= $this->_request->getParam("dtTerminoVigencia");
        $stEstado 			= $this->_request->getParam("stEstado");
        $stOpcao 			= $this->_request->getParam("stOpcao");
        //		$comunicado			= strip_tags($this->_request->getParam("comunicado"),'<strike><a><b><i><u><ol><li><ul><strong><blockquote><font>');
        $comunicado			= $this->_request->getParam("comunicado");
        
        if (empty($url)) {
            $url  = "consultar";
        }
        
        
        if (empty($dtInicioVigencia)) {
            $dtInicio  = new Zend_Db_Expr('GETDATE()');
        } else {
            $dtInicio  = Data::dataAmericana($dtInicioVigencia);
        }
        
        if (empty($dtTerminoVigencia)) {
            $dtTermino = null;
        } else {
            $dtTermino = Data::dataAmericana($dtTerminoVigencia);
        }
        
        $dados = array(
                       "Comunicado" 		=> $comunicado,
                       "idSistema" 			=> 21,
                       "stOpcao" 			=> $stOpcao,
                       "stEstado" 			=> $stEstado,
                       "dtInicioVigencia" 	=> $dtInicio,
                       "dtTerminoVigencia" 	=> $dtTermino
        );
        
        try {
            if (!empty($idComunicado)) {
                $where['idComunicado = ?'] = $idComunicado;
                $salvar = $tbComunicados->alterar($dados, $where);
            } else {
                $salvar = $tbComunicados->inserir($dados);
            }
        
            parent::message("Solicita&ccedil;&atilde;o enviada com sucesso!", "comunicados/consultar/".$url, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar: " . $e->getMessage(), "comunicados/editar/idComunicado/".$idComunicado, "ERROR");
        }
    } // fecha método salvarAction()
} // fecha class
