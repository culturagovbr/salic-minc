<?php
class Planilha_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
    }

    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                "planilha" => [
                    'planilha-unidade-rest',
                    'mediana-item-orcamentario-rest',
                ]
            ]
        );

        $nomeConjuntoDeRotas = 'restPlanilha';
        $frontController->getRouter()->addRoute(
            $nomeConjuntoDeRotas,
            $restRoute
        );
    }
}
