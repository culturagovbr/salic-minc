<?php

use Application\Modules\PrestacaoContas\Service\RelatorioCumprimentoObjeto\RelatorioCumprimentoObjeto as RelatorioCumprimentoObjetoService;

class PrestacaoContas_RelatorioCumprimentoObjetoRestController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->setValidateUserIsLogged();

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        try {
            $relatorioCumprimentoObjeto = new RelatorioCumprimentoObjetoService($this->getRequest(), $this->getResponse());
            $resposta = $relatorioCumprimentoObjeto->listarRelatorioCumprimentoObjeto();
            $resposta = \TratarArray::utf8EncodeArray($resposta);

            $this->renderJsonResponse($resposta, 200);

        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 404,
                    'message' => $objException->getMessage()
                ]
            ], 404);

        }

    }

    public function getAction()
    {
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
