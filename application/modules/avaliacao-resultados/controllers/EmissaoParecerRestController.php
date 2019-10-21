<?php

use Application\Modules\AvaliacaoResultados\Service\ParecerTecnico\AvaliacaoFinanceira as AvaliacaoFinanceiraService;

class AvaliacaoResultados_EmissaoParecerRestController extends MinC_Controller_Rest_Abstract
{
    private $_profiles;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->_profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod = [
//            'index' => $profiles,
//            'post' => $profiles
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }


    public function indexAction()
    {
        $this->customRenderJsonResponse([], 204);
    }

    public function getAction()
    {
        if (!isset($this->_request->idPronac)) {
            $this->customRenderJsonResponse([], 422);
        }

        $avaliacaoFinanceiraService = new AvaliacaoFinanceiraService($this->getRequest(), $this->getResponse());
        $resposta = $avaliacaoFinanceiraService->buscarDadosProjeto();
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($resposta), 200);
    }

    private function isPermitidoAcesso()
    {
        if (!in_array($this->_codGrupo, $this->_profiles)) {
            $fluxosProjetoDbTable = new \AvaliacaoResultados_Model_DbTable_FluxosProjeto();
            $fluxoProjeto = $fluxosProjetoDbTable->findBy(['idPronac = ?' => $this->_request->idPronac]);

            if ($fluxoProjeto['estadoId'] != 12) {
                return false;
            }
        }
        return true;
    }

    public function headAction()
    {
    }

    public function postAction()
    {
        $this->putAction();
    }

    public function putAction()
    {
        $avaliacaoFinanceiraService = new AvaliacaoFinanceiraService($this->getRequest(), $this->getResponse());
        $response = $avaliacaoFinanceiraService->salvar();
        $this->customRenderJsonResponse($response, 200);
    }

    public function deleteAction()
    {
    }

}
