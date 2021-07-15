<?php

class Planilha_MedianaItemOrcamentarioRestController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->setValidateUserIsLogged();

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction() {}

    public function getAction()
    {
        try {
            $params = $idPreProjeto = $this->getRequest()->getParams();

            if (empty($params['idProduto'])
                || empty($params['idUnidade'])
                || empty($params['idPlanilhaItem'])
                || empty($params['idUfDespesa'])
                || empty($params['idMunicipioDespesa'])
            ) {
                throw new Exception("Dado obrigatório não informado!");
            }

            $spCalcularMedianaItemOrcamentario = new Planilha_Model_DbTable_SpCalcularMedianaItemOrcamentario();
            $mediana = $spCalcularMedianaItemOrcamentario->obterMedianaItemOrcamento(
                $params['idProduto'],
                $params['idUnidade'],
                $params['idPlanilhaItem'],
                $params['idUfDespesa'],
                $params['idMunicipioDespesa']
            );
            $mediana = \TratarArray::utf8EncodeArray($mediana);

            $this->customRenderJsonResponse([
                'data' => $mediana,
            ], 200);

        } catch (Exception $objException) {
            $this->customRenderJsonResponse([
                'error' => [
                    'code' => 404,
                    'message' => $objException->getMessage()
                ]
            ], 404);

        }
    }

    public function postAction() {}

    public function putAction() {}

    public function deleteAction() {}

    public function headAction() {}
}
