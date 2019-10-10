<?php
use Application\Modules\AvaliacaoResultados\Service\ParecerTecnico\AvaliacaoFinanceira as AvaliacaoFinanceiraService;


class AvaliacaoResultados_EstatisticasAvaliacaoController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod  = [
//            'index' => $profiles,
//            'post' => $profiles
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);


        parent::__construct($request, $response, $invokeArgs);
    }

    public function getAction()
    {
        if (
            !$this->getRequest()->getParam('idPronac') ||
            !isset($this->getRequest()->idPronac))
        {
            $this->customRenderJsonResponse([], 404);
            return;
        }

        $comprovantesService = new AvaliacaoFinanceiraService($this->getRequest(), $this->getResponse());
        $dados = $comprovantesService->buscarDadosAnaliseFinanceira();

        $this->customRenderJsonResponse(['data' => $dados], 200);
    }

    public function postAction(){}

    public function indexAction(){}

    public function headAction(){}

    public function putAction(){}

    public function deleteAction(){}

}
