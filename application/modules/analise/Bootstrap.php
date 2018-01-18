<?php
class Analise_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/analise/controllers/GenericController.php';
    }
}
