<?php
class Readequacao_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/readequacao/controllers/GenericController.php';
        require_once APPLICATION_PATH . '/modules/readequacao/controllers/ReadequacaoAssinaturaController.php';

        
        $frontController = Zend_Controller_Front::getInstance();

        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                'readequacao' => [
                    'index',
                    'avaliacao',
                    'campo-atual',
                    'dados-readequacao',
                    'dados-readequacao-documento',
                    'documento',
                    'finalizar',
                    'finalizar-avaliacao',
                    'tipos-disponiveis',
                ]
            ]
        );

        $frontController->getRouter()->addRoute('rest-readequacao', $restRoute);
    }

}
