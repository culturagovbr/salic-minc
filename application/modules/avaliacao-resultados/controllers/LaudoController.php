<?php

use Application\Modules\AvaliacaoResultados\Service\LaudoFinal\Laudo as LaudoService;
use Application\Modules\AvaliacaoResultados\Service\Fluxo\Estado as EstadoService;


class AvaliacaoResultados_LaudoController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod = [];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function headAction()
    {
        $this->renderJsonResponse(["Nenhum conte&uacute;do"], 204);
    }

    public function indexAction()
    {
        $estadoId = $this->getRequest()->getParam('estadoId');
        $service = new LaudoService();
        $projetos = $service->obterProjetos($estadoId);

        $this->renderJsonResponse(
            \TratarArray::utf8EncodeArray($projetos),
            200
        );
    }

    public function getAction()
    {
        $idPronac = $this->getRequest()->getParam('idPronac');
        $service = new LaudoService();
        $data = $service->obterLaudo($idPronac) ? $service->obterLaudo($idPronac) : null;

        $this->customRenderJsonResponse([
            'data' =>
            [
                'code' => 200,
                'items' => \TratarArray::utf8EncodeArray($data),
            ]
        ], 200);

    }

    public function postAction()
    {
        $idLaudoFinal = $this->getRequest()->getParam('idLaudoFinal');
        $idPronac = $this->getRequest()->getParam('idPronac');
        $siManifestacao = $this->getRequest()->getParam('siManifestacao');
        $dsLaudoFinal = $this->getRequest()->getParam('dsLaudoFinal');
        $finalizar = $this->getRequest()->getParam('finalizar');
        $atual = $this->getRequest()->getParam('atual');

        $service = new LaudoService();
        $data = $service->salvarLaudo($idLaudoFinal, $idPronac, $siManifestacao, $dsLaudoFinal);

        if ($finalizar && $atual) {
            $params = $this->getRequest()->getParams();

            $estado = new EstadoService();
            $estado->eventos($atual, $params);
        }

        $this->renderJsonResponse([$data], 200);
    }

    public function putAction()
    {
        // TODO: Implement putAction() method.
    }

    public function deleteAction()
    {
//        403 Proibido
        $this->customRenderJsonResponse(["M&eacute;todo não permitido"], 405);
    }

}
