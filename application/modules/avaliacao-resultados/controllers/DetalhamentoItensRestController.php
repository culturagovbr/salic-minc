<?php
use Application\Modules\AvaliacaoResultados\Service\DetalhamentoItens as DetalhamentoItensService;

class AvaliacaoResultados_DetalhamentoItensRestController extends MinC_Controller_Rest_Abstract
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
        $detalhamentoItensService = new DetalhamentoItensService($this->getRequest(), $this->getResponse());
        $resposta = $detalhamentoItensService->obterDetalhamento();

        $this->renderJsonResponse($resposta, 200);
    }

    public function getAction()
    {

        $this->renderJsonResponse([],200);
    }

    public function headAction()
    {
        $this->renderJsonResponse([],200);
    }

    public function postAction()
    {
        $this->renderJsonResponse([],200);
    }

    public function putAction()
    {
        $this->renderJsonResponse([],200);
    }

    public function deleteAction()
    {
        $this->renderJsonResponse([],204);
    }
}
