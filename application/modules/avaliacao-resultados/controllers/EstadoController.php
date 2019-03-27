<?php

use Application\Modules\AvaliacaoResultados\Service\Fluxo\Estado as EstadoService;

class AvaliacaoResultados_EstadoController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_PRESTACAO_DE_CONTAS,
        ];

        $permissionsPerMethod  = [];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        $estados = new AvaliacaoResultados_Model_DbTable_Estados();
        $estados = $estados->all();
        $this->customRenderJsonResponse($estados->toArray(), 200);
    }

    public function getAction()
    {
        $id = $this->getRequest()->getParam('id');

        $estado = new  AvaliacaoResultados_Model_DbTable_Estados();

        $estado = $estado->findBy($id);
        $estado['proximo'] = json_decode($estado['proximo']);

        $this->renderJsonResponse($estado, 200);
    }

    public function headAction(){}

    public function postAction()
    {
        $atual = $this->getRequest()->getParam('atual');
        $proximoEstado = $this->getRequest()->getParam('proximo');
        $params = $this->getRequest()->getParams();

        $estado = new EstadoService();
        /* $estado->validar($atual, $proximoEstado); */
        $estado->eventos($atual, $params);

        $this->renderJsonResponse(['post'], 200);
    }

    public function putAction()
    {
    }

    public function deleteAction(){}
}
