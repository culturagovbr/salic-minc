<?php
class Diligencia_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        $restRoute = new Zend_Rest_Route($frontController, array(), array('diligencia'));
        $frontController->getRouter()->addRoute('rest', $restRoute);
    }
}
