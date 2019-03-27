<?php

class Parecer_IndexController extends MinC_Controller_Action_Abstract
{
    private $idPronac;

    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
    }

    public function indexAction()
    {
        switch ($this->grupoAtivo->codGrupo) {
        case Autenticacao_Model_Grupos::PARECERISTA:
            $this->redirect("/{$this->moduleName}/analise-inicial");
            break;
        case Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER:
            $this->redirect("/{$this->moduleName}/gerenciar-parecer/index");
            break;
        }
    }

    public function gerenciarAssinaturasAction()
    {
        switch ($this->grupoAtivo->codGrupo) {
        case Autenticacao_Model_Grupos::PARECERISTA:
            $this->redirect("/{$this->moduleName}/analise-inicial");
            break;
        case Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER:
            $this->redirect("/{$this->moduleName}/gerenciar-parecer/index");
            break;
        }
    }


    public function encaminharAssinaturaAction()
    {
    }
}
