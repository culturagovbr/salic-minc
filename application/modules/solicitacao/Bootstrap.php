<?php
class Solicitacao_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/solicitacao/controllers/GenericController.php';
    }

    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                'solicitacao' => [
                    'index-rest',
                    'mensagem-rest',
                ]
            ]
        );

        $frontController->getRouter()->addRoute('rest-solicitacao', $restRoute);
    }
}
