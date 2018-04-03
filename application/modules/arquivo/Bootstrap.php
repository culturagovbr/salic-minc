<?php
class Arquivo_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/arquivo/controllers/GenericController.php';
    }
}
