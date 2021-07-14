<?php

use Application\Modules\AvaliacaoResultados\Service\Fluxo\Estado as EstadoService;

class AvaliacaoResultados_FluxoProjetoController extends MinC_Controller_Rest_Abstract
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
        $estadoId = (int)$this->getRequest()->getParam('estadoid');
        $idAgente = (int)$this->getRequest()->getParam('idAgente');
        $idSecretaria = $this->getRequest()->getParam('idSecretaria');

        $fluxo = new AvaliacaoResultados_Model_DbTable_FluxosProjeto();
        $projetos = $fluxo->projetos($estadoId, $idSecretaria, $idAgente);
        $aux = [];

        foreach($projetos as $projeto){
            $aux[] =  array_map('utf8_encode', $projeto->toArray());
        }
        $this->renderJsonResponse($aux, 200);
    }

    public function getAction()
    {
        $id = $this->getRequest()->getParam('id');

        $fluxo = new AvaliacaoResultados_Model_DbTable_FluxosProjeto();
        $projeto = $fluxo->projeto($id);

        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($projeto), 200);
    }

    public function headAction(){}

    public function postAction()
    {
        $atual = $this->getRequest()->getParam('atual');
        $proximoEstado = $this->getRequest()->getParam('proximo');
        $params = $this->getRequest()->getParams();

        $estado = new EstadoService();
        $estado->validar($atual, $proximoEstado);
        $estado->eventos($atual, $params);

        $this->renderJsonResponse(['post'], 200);
    }

    public function putAction()
    {
    }

    public function deleteAction(){}
}
