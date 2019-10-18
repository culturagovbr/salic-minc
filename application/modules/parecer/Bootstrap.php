<?php
class Parecer_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/parecer/controllers/GenericController.php';
    }

    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                "parecer" => [
                    'analise-inicial-rest',
                    'analise-inicial-conteudo-rest',
                    'analise-inicial-custo-rest',
                    'analise-inicial-consolidacao-rest',
                    'analise-inicial-outros-produtos-rest',
                    'analise-inicial-finalizacao-rest',
                    'analise-inicial-impedimento-rest',
                    'historico-produto-rest',
                    'planilha-produto-rest',
                    'gerenciar-parecer-rest',
                    'gerenciar-distribuir-produto-rest',
                    'gerenciar-distribuir-projeto-rest',
                    'gerenciar-reanalisar-produto-rest',
                    'gerenciar-devolver-produto-secult-rest',
                    'gerenciar-analise-complementar-rest',
                    'gerenciar-avaliacao-rest',
                    'vinculada-rest',
                    'parecerista-rest',
                ]
            ]
        );

        $nomeConjuntoDeRotas = 'restParecer';
        $frontController->getRouter()->addRoute(
            $nomeConjuntoDeRotas,
            $restRoute
        );
    }
}
