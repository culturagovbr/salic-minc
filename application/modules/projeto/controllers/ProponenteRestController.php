<?php

use Application\Modules\Projeto\Service\Proponente\Proponente as ProponenteService;

class Projeto_ProponenteRestController extends MinC_Controller_Rest_Abstract
{
    protected $idAgente = 0;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->setValidateUserIsLogged();

        parent::__construct($request, $response, $invokeArgs);
    }

    public function getAction()
    {
        try {
            $ProponenteService = new ProponenteService($this->getRequest(), $this->getResponse());
            $resposta = $ProponenteService->buscarDadosAgenteProponente();

            $this->customRenderJsonResponse(['data' => $resposta], 200);

        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 404,
                    'message' => $objException->getMessage()
                ]
            ], 404);

        }

    }

    public function indexAction()
    {
        $this->renderJsonResponse([], 200);
    }

    public function postAction()
    {
        $this->renderJsonResponse([], 201);
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
