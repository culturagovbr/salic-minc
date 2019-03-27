<?php
class PrestacaoContas_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                'prestacao-contas' => [
                    'comprovante-pagamento',
                    'planilha-aprovacao'
                ]
            ]
        );

        $frontController->getRouter()->addRoute('rest-prestacao-contas', $restRoute);
    }
}
