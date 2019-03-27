<?php

class Projeto_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/projeto/controllers/GenericController.php';
    }

    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                "projeto" => [
                    'proponente-rest',
                    'diligencia-adequacao-rest',
                    'diligencia-projeto-rest',
                    'diligencia-proposta-rest',
                ]
            ]
        );

        $nomeConjuntoDeRotas = 'restProjeto';
        $frontController->getRouter()->addRoute(
            $nomeConjuntoDeRotas,
            $restRoute
        );
    }
}

