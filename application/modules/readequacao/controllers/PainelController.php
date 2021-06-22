<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;

class Readequacao_PainelController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO,
            Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA,
        ];
        
        $permissionsPerMethod  = [];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        $subRoutes = [];
        $this->registrarSubRoutes($subRoutes);
        
        parent::__construct($request, $response, $invokeArgs);
    }

    public function getAction() {}

    public function indexAction(){
        $data = [];
        $code = 200;

        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $data = $readequacaoService->buscarReadequacoesPainel();
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($data), $code);
    }

    public function headAction(){}

    public function postAction(){}

    public function putAction(){}

    public function deleteAction(){}

}
