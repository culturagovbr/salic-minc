<?php

class Navegacao_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                "navegacao" => [
                    'menu-principal',
                    'perfil-rest',
                    'dados-rest',
                    'comunicados-rest',
                    'projeto-rest'
                ]
            ]
        );

        $nomeConjuntoDeRotas = 'navegacaoRest';
        $frontController->getRouter()->addRoute(
            $nomeConjuntoDeRotas,
            $restRoute
        );
    }
}
