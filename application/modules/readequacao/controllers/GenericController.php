<?php

abstract class Readequacao_GenericController extends MinC_Controller_Action_Abstract
{
    protected $idAgente = 0;
    protected $idUsuario = 0;
    protected $idOrgao = 0;
    protected $idPerfil = 0;

    protected $idPronac;
    protected $idPronacHash;
    protected $projeto;
    protected $in2017;

    public function init()
    {
        parent::init();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->idOrgao = $GrupoAtivo->codOrgao;
        $this->idPerfil = $GrupoAtivo->codGrupo;
        $this->view->usuarioInterno = false;

        // verifica as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::PROPONENTE;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::PRESIDENTE_VINCULADA_SUBSTITUTO;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO;

        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl();
            $this->redirect($url);
        }

        $idPronac = $this->_request->getParam("idPronac");
        $this->idPronacHash = Seguranca::encrypt($idPronac);

        if (strlen($idPronac) > 7) {
            $this->idPronacHash = $idPronac;
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $this->idPronac = $idPronac;
        $this->view->idPronac = $idPronac;
        $this->view->idPronacHash = $this->idPronacHash;

        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->view->usuarioInterno = true;
            $this->idUsuario = $auth->getIdentity()->usu_codigo;

            $Usuario = new Autenticacao_Model_DbTable_Usuario();
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }

            parent::perfil(1, $PermissoesGrupo);
            $this->idAgente = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->idAgente = ($this->idAgente) ? $this->idAgente["idAgente"] : 0;
        } else { // autenticacao scriptcase
            parent::perfil(4, $PermissoesGrupo);
            $this->idUsuario = (isset($_GET["idusuario"])) ? $_GET["idusuario"] : 0;

            if($this->idPronac) {
                $this->verificarPermissaoAcesso(false, true, false);
            }
        }

        $this->view->in2017 = false;
        if ($idPronac) {
            $tbProjetos = new Projeto_Model_DbTable_Projetos();
            $this->projeto = (new Projeto_Model_TbProjetos($tbProjetos->findBy(['idPronac' => $idPronac])));

            $fnIN2017 = new fnVerificarProjetoAprovadoIN2017();

            $this->in2017 = $fnIN2017->verificar($idPronac);
            $this->view->in2017 = $this->in2017;
        }
    }
}
