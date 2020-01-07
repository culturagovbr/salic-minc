<?php


class AvaliacaoResultados_AvaliacaoGeralComprovantesController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod = [
//            '*' => $profiles,
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);


        parent::__construct($request, $response, $invokeArgs);
    }

    public function postAction()
    {

        if (
            !$this->getRequest()->getParam('idPronac') ||
            !isset($this->getRequest()->idPronac)) {

            $this->customRenderJsonResponse([], 422);
            return;
        }

        $tbPlanilhaAplicacao = new \AvaliacaoResultados_Model_DbTable_tbPlanilhaAprovacao();
        $tbPlanilhaAplicacao->validarTodosComprovantes($this->getRequest()->getParam('idPronac'));

        $this->renderJsonResponse([
            'data' => []
        ], 200);
    }

    public function getAction()
    {
    }

    public function indexAction()
    {
    }

    public function headAction()
    {
    }

    public function putAction()
    {
    }

    public function deleteAction()
    {
    }

}
