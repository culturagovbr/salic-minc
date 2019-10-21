<?php
use Application\Modules\AvaliacaoResultados\Service\ParecerTecnico\AvaliacaoComprovante as AvaliacaoComprovantesService;


class AvaliacaoResultados_AvaliacaoComprovanteController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
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

    public function getAction()
    {
        if (
            !$this->getRequest()->getParam('idPronac') ||
            !isset($this->getRequest()->idPronac))
        {
            $this->customRenderJsonResponse([], 404);
            return;
        }

        $comprovantesService = new AvaliacaoComprovantesService($this->getRequest(), $this->getResponse());
        $comprovantes = $comprovantesService->buscarComprovantes();
//        $dadosItem = $comprovantesService->buscarDadosAvaliacaoComprovante();

        $this->renderJsonResponse([
//            'dadosItem' => $dadosItem,
            'comprovantes' => $comprovantes
        ], 200);
    }

    public function postAction(){
        $dsJustificativa = utf8_decode($this->getRequest()->getParam('dsJustificativa', ''));
        $stItemAvaliado = $this->getRequest()->getParam('stItemAvaliado');
        $idComprovantePagamento = $this->getRequest()->getParam('idComprovantePagamento');
        try {

            $dsJustificativa = $dsJustificativa == 'null' ? '' : $dsJustificativa;

            if (!$idComprovantePagamento) {
                throw new Exception('Falta idComprovantePagamento');
            }

            if ($stItemAvaliado == '3' && strlen(trim($dsJustificativa)) == 0) {
                throw new Exception('Falta Parecer da avalia&ccedil;&atilde;o');
            }

            if (!$stItemAvaliado) {
                throw new Exception('Falta Avalia&ccedil;&atilde;o do item');
            }

            $tblComprovantePag = new ComprovantePagamentoxPlanilhaAprovacao();
            $rsComprovantePag = $tblComprovantePag
                ->buscar( [ 'idComprovantePagamento = ?' => $idComprovantePagamento] )
                ->current();

            $rsComprovantePag->dtValidacao = date('Y/m/d H:i:s');
            $rsComprovantePag->dsJustificativa = $dsJustificativa;
            $rsComprovantePag->stItemAvaliado = $stItemAvaliado;

            $rsComprovantePag->save();
            $this->customRenderJsonResponse(
                [
                    'code' => 200,
                    'message' => html_entity_decode('Avalia&ccedil;&atilde;o realizada com sucesso')
                ], 200);

        } catch (Exception $e) {
            $this->customRenderJsonResponse(
                [
                    'code' => 500,
                    'message' => html_entity_decode($e->getMessage())
                ], 500);

        }
    }

    public function indexAction(){}

    public function headAction(){}

    public function putAction(){}

    public function deleteAction(){}

}
