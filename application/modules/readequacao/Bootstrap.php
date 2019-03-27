<?php
class Readequacao_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/readequacao/controllers/GenericController.php';
        require_once APPLICATION_PATH . '/modules/readequacao/controllers/ReadequacaoAssinaturaController.php';
    }
}
