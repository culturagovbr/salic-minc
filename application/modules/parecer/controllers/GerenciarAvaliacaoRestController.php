<?php

use Application\Modules\Parecer\Service\GerenciarParecer as GerenciarParecerService;

class Parecer_GerenciarAvaliacaoRestController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,
            Autenticacao_Model_Grupos::SUPERINTENDENTE_DE_VINCULADA,
        ];

        $permissionsPerMethod = [
            '*' => $profiles,
        ];

        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);
        $this->setValidateUserIsLogged();

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
    }

    public function getAction()
    {
    }

    public function postAction()
    {
        try {
            $gerenciarParecerService = new GerenciarParecerService($this->getRequest(), $this->getResponse());
            $this->customRenderJsonResponse([
                'message' => 'Validação realizada com sucesso',
                'data' => $gerenciarParecerService->salvarValidacaoParecer()
            ], 201);
        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 412,
                    'message' => $objException->getMessage()
                ]
            ], 412);
        }
    }

    public function putAction()
    {
        $this->renderJsonResponse([], 200);
    }

    public function deleteAction()
    {
        $this->renderJsonResponse([], 204);
    }

    public function headAction()
    {
        $this->renderJsonResponse([], 200);
    }
}
