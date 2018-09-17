<?php

use \Application\Modules\Solicitacao\Service\Solicitacao\Mensagem as MensagemService;

class Solicitacao_MensagemRestController extends MinC_Controller_Rest_Abstract
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
        try {
            $mensagemService = new MensagemService($this->getRequest(), $this->getResponse());
            $solicitacoes = $mensagemService->historicoSolicitacoes();

            $this->renderJsonResponse($solicitacoes, 200);
        } catch (Exception $e) {
            $this->renderJsonResponse($solicitacoes, 400);
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
