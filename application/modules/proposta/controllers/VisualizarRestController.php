<?php

use \Application\Modules\Proposta\Service\Proposta\Visualizar as VisualizarService;

class Proposta_VisualizarRestController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {

        $permissionsPerMethod  = ['*'];

        $this->setValidateUserIsLogged();
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        $idPreProjeto = $this->_request->getParam('idPreProjeto');

        try {
            if (empty($idPreProjeto)) {
                throw new Exception("N&uacute;mero da proposta &eacute; obrigat&oacute;ria");
            }

            $visualizarService= new VisualizarService($this->getRequest(), $this->getResponse());
            $data = $visualizarService->obterSugestaoEnquadramento();

            $this->renderJsonResponse($data, 200);
        } catch (Exception $e) {
            $this->renderJsonResponse($data, 400);
        }
    }

    public function getAction()
    {
        $this->renderJsonResponse(200);
    }

    public function headAction()
    {
        $this->renderJsonResponse(200);
    }

    public function postAction()
    {
        $this->renderJsonResponse(201);

    }

    public function putAction()
    {
        $this->renderJsonResponse(200);
    }

    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(204);
    }
}
