<?php

use Application\Modules\Foo\Service\Foo\Bar as BarService;

class Foo_FooRestController extends MinC_Controller_Rest_Abstract
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
        $barService = new BarService($this->getRequest(), $this->getResponse());
        $resposta = $barService->buscarTodos();

        $this->renderJsonResponse($resposta, 200);

    }

    public function getAction()
    {
        $parametros = $this->getRequest()->getParams();
        $barService = new BarService($this->getRequest(), $this->getResponse());
        $resposta = $barService->buscar($this->getParam('id'));

        $this->renderJsonResponse($resposta, 200);
    }

    public function headAction(){}

    public function postAction()
    {
        $barService = new BarService($this->getRequest(), $this->getResponse());
        $resposta = $barService->salvar();

        $this->renderJsonResponse($resposta, 201);

    }

    public function putAction()
    {
        $barService = new BarService($this->getRequest(), $this->getResponse());
        $resposta = $barService->atualizar();

        $this->renderJsonResponse($resposta, 200);
    }

    public function deleteAction()
    {
        $barService = new BarService($this->getRequest(), $this->getResponse());
        $barService->remover();

        $this->getResponse()->setHttpResponseCode(204);
    }
}
