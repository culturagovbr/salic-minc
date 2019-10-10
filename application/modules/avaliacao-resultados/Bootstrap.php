<?php
class AvaliacaoResultados_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        $restRoute = new Zend_Rest_Route(
            $frontController,
            [],
            [
                'avaliacao-resultados' => [
                    'index',
                    'assinatura',
                    'avaliacao-comprovante',
                    'diligencia',
                    'emissao-parecer-rest',
                    'encaminhamento-prestacao-contas',
                    'estado',
                    'fluxo',
                    'fluxo-projeto',
                    'historico',
                    'laudo',
                    'tecnicos',
                    'tipo-avaliacao-rest',
                    'projeto',
                    'projeto-assinatura',
                    'projeto-inicio',
                    'projetos-avaliacao-tecnica',
                    'planilha-aprovada',
                    'dashboard',
                    'estatisticas-avaliacao'
                ]
            ]
        );

        $frontController->getRouter()->addRoute('rest-avaliacao-resultados', $restRoute);
    }
}
