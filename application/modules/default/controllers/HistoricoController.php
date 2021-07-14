<?php

class HistoricoController extends MinC_Controller_Action_Abstract
{
    public function historicoAction()
    {
    }

    public function init()
    {
        // Visualiza��o do t�tulo da p&aacute;gina
        $this->view->title = "Visualiza��o dos Hist�ricos dos Projetos";

        parent::init();
    }

    public function indexAction()
    {
        $get = Zend_Registry::get('get');
        $pronac = $get->pronac;

        $mens = new HistoricoDAO();

        $tbprojeto = $mens->buscaProjeto($pronac);
        $this->view->projeto = $tbprojeto;

        $tbhistorico = $mens->buscaHistorico($pronac);
        $this->view->historico = $tbhistorico;
    }
}
