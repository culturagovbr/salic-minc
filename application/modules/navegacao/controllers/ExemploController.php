<?php

class Navegacao_ExemploController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $gitTag = '?v=' . $this->view->gitTag();

        $this->view->headScript()->offsetSetFile(99, '/public/dist/js/manifest.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(100, '/public/dist/js/vendor.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(101, '/public/dist/js/foo.js'. $gitTag, 'text/javascript', array('charset' => 'utf-8'));
    }
}
