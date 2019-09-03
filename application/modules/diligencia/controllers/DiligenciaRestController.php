<?php

use Application\Modules\Diligencia\Service\Diligencia as Diligencia;
class Diligencia_DiligenciaRestController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::PARECERISTA,
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

            $serviceDiligencia = new Diligencia($this->getRequest(), $this->getResponse());
            $diligencias = $serviceDiligencia->listar();

            $this->customRenderJsonResponse([
                'items' => TratarArray::utf8EncodeArray($diligencias),
            ], 200);

        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 400,
                    'message' => $objException->getMessage()
                ]
            ], 400);

        }
    }

    public function getAction()
    {
        try {

            $serviceDiligencia = new Diligencia($this->getRequest(), $this->getResponse());
            $diligencia = $serviceDiligencia->obter();

            $this->customRenderJsonResponse(TratarArray::utf8EncodeArray($diligencia), 200);

        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 400,
                    'message' => $objException->getMessage()
                ]
            ], 400);

        }
    }

    public function postAction()
    {
        try {
            $serviceDiligencia = new Diligencia($this->getRequest(), $this->getResponse());
            $diligencia = $serviceDiligencia->salvar();

            $this->customRenderJsonResponse(
                [
                    'data' => $diligencia,
                    'message' => html_entity_decode('')
                ], 200);

        } catch (Exception $e) {
            $this->customRenderJsonResponse(
                [
                    'code' => 400,
                    'message' => html_entity_decode($e->getMessage())
                ], 400);

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
