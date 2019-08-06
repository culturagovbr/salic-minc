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
                    'calcular-resumo-planilha',
                    'campo-atual',
                    'dados-readequacao',
                    'dados-readequacao-documento',
                    'destinatarios-distribuicao',
                    'devolver-ao-coordenador',
                    'devolver-readequacao',
                    'distribuir-readequacao',
                    'documento',
                    'finalizar',
                    'finalizar-avaliacao',
                    'item-planilha',
                    'painel',
                    'planilha-obter-unidades',
                    'redistribuir-readequacao',
                    'reverter-alteracao-item',
                    'solicitar-saldo',
                    'tipos-disponiveis',
                ]
            ]
        );

        $frontController->getRouter()->addRoute('rest-readequacao', $restRoute);
    }
}
