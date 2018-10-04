<?php

use Application\Modules\AvaliacaoResultados\Service\ParecerTecnico\RevisaoAvaliacaoFinanceira as RevisaoService;

class AvaliacaoResultados_RevisaoController extends MinC_Controller_Rest_Abstract
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;


    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->request = $request;
        $this->response = $response;

        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod  = [
//            'index' => $profiles,
//            'post' => $profiles
        ];

        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }


    public function indexAction()
    {
        if (!isset($this->_request->idAvaliacaoFinanceira))
        {
            $this->customRenderJsonResponse([], 422);
        }

        $revisaoService = new RevisaoService();
        $resposta = $revisaoService->buscarRevisoes($this->request->getParams()["idAvaliacaoFinanceira"]);
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($resposta[0]), $resposta[1]);
    }

    public function getAction()
    {
        if (!isset($this->_resquest->idAvaliacaoFinanceiraRevisao))
        {
            $this->customRenderJsonResponse([], 422);
        }
        $revisaoService = new RevisaoService();
        $resposta = $revisaoService->buscarRevisao($this->request->getParams()["idAvaliacaoFinanceiraRevisao"]);
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($resposta[0]), $resposta[1]);

    }

    public function headAction(){}

    public function postAction()
    {
        $this->putAction();
    }

    public function putAction()
    {
        $revisaoService = new RevisaoService();
        $response = $revisaoService->salvar($this->request->getParams());

        $this->renderJsonResponse($response[0], $response[1]);
    }

    public function deleteAction(){}

}
