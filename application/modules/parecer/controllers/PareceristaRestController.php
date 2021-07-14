<?php

use Application\Modules\Parecer\Service\Parecerista as PareceristaService;

class Parecer_PareceristaRestController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,
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
        try {
            $gerenciarParecerService = new PareceristaService($this->getRequest(), $this->getResponse());
            $this->customRenderJsonResponse([
                'items' => \TratarArray::utf8EncodeArray($gerenciarParecerService->obterPareceristas()),
            ], 200);

        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 412,
                    'message' => $objException->getMessage()
                ]
            ], 412);

        }
    }

    public function getAction() {}

    public function postAction() {}

    public function putAction() {}

    public function deleteAction() {}

    public function headAction() {}
}
