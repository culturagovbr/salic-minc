<?php
class Autenticacao_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                'autenticacao' => [
                    'usuario',
                ]
            ]
        );

        $frontController->getRouter()->addRoute('rest-autenticacao', $restRoute);
    }
}
