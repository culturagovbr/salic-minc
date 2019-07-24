<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;

class Readequacao_SolicitarSaldoController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::PROPONENTE,
            Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO,
        ];

        $permissionsPerMethod  = [
            'post' => [
                Autenticacao_Model_Grupos::PROPONENTE
            ]
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);
        
        parent::__construct($request, $response, $invokeArgs);
    }

    public function getAction() {
        $data = [];
        $code = 200;
        
        $idPronac = $this->getRequest()->getParam('id');
        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto($idPronac);
        if (!$permissao) {
            $data['permissao'] = false;
            $data['message'] = utf8_encode('Você não tem permissão para solicitar o uso do saldo de aplicação');
            $this->customRenderJsonResponse($data, $code);
        } else {
            $data = $readequacaoService->solicitarSaldo($idPronac);
        }
        
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($data), $code);
    }

    public function indexAction(){}

    public function headAction(){}

    public function postAction(){}

    public function putAction(){}

    public function deleteAction(){}
}
