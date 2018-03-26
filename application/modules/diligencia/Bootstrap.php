<?php
class Diligencia_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        $route = new Zend_Controller_Router_Route(
            'diligencia/gerenciar/responder/*',
            [
                'module' => 'diligencia',
                'controller' => 'gerenciar',
                'action'     => 'responder'
            ]
        );

        $frontController->getRouter()->addRoute('diligencia', $route);

        $restRoute = new Zend_Rest_Route($frontController, array(), array('diligencia'));
        $frontController->getRouter()->addRoute('rest', $restRoute);


    }
}
