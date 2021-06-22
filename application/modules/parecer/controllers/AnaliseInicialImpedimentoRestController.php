<?php

use Application\Modules\Parecer\Service\AnaliseInicial as AnaliseInicial;
class Parecer_AnaliseInicialImpedimentoRestController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::PARECERISTA,
            Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,
        ];

        $permissionsPerMethod  = [
            '*' => $profiles,
        ];

        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);
        $this->setValidateUserIsLogged();

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        try {

            $this->customRenderJsonResponse([], 200);

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
        $this->renderJsonResponse([], 200);
    }

    public function postAction()
    {
        try {
            $analiseInicialService = new AnaliseInicial($this->getRequest(), $this->getResponse());
            $resposta = $analiseInicialService->devolverProdutoParaCordenador();

            $this->customRenderJsonResponse(
                [
                    'data' => $resposta,
                    'message' => 'Devolução realizada com sucesso!'
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
