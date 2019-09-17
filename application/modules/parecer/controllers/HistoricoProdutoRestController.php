<?php

class Parecer_HistoricoProdutoRestController extends MinC_Controller_Rest_Abstract
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
        try {
            $idPronac = $this->_request->getParam("idPronac");
            $idProduto = $this->_request->getParam("idProduto");
            $stPrincipal = $this->_request->getParam("stPrincipal");

            if (empty($idPronac) || empty($idProduto)) {
                throw new Exception("Parâmetros obrigatórios não informados");
            }

            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $resposta = $tbDistribuirParecer->buscarHistorico(
                [
                    "d.idPronac = ?" => $idPronac,
                    "d.idProduto = ?" => $idProduto,
                    "d.stPrincipal = ?" => $stPrincipal
                ]
            );

            if (!empty($resposta)) {
                $resposta = $resposta->toArray();
            }
            $this->customRenderJsonResponse(['items' => \TratarArray::utf8EncodeArray($resposta)], 200);

        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 404,
                    'message' => $objException->getMessage()
                ]
            ], 404);

        }
    }

    public function postAction()
    {
        $this->renderJsonResponse([], 200);
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
