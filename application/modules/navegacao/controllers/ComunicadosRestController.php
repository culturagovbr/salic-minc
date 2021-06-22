<?php

use Application\Modules\Navegacao\Service\Comunicados as ComunicadosService;

class Navegacao_ComunicadosRestController extends MinC_Controller_Rest_Abstract
{
    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array())
    {
        $permissionsPerMethod  = ['*'];

        $this->setValidateUserIsLogged();
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('get', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('post', 'json')
            ->addActionContext('put', 'json')
            ->addActionContext('delete', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $comunicadosService = new ComunicadosService($this->getRequest(), $this->getResponse());
        $comunicados = $comunicadosService->obterComunicados();
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($comunicados), 200);
    }

    public function getAction()
    {
        $this->renderJsonResponse(200);
    }

    public function headAction(){}

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
