<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;

class Readequacao_ItemPlanilhaController extends MinC_Controller_Rest_Abstract
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

    public function getAction() {}

    public function indexAction(){}

    public function headAction(){}

    public function postAction(){
        $data = [];
        $code = 200;
        
        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();
        if (!$permissao) {
            $data['permissao'] = false;
            $data['message'] = 'Você não tem permissão para alterar este item de planilha';
            $this->customRenderJsonResponse($data, $code);
        } else {
            $data = $readequacaoService->alterarItemPlanilha();
        }
        
        $this->renderJsonResponse($data, $code);
    }

    public function putAction(){}

    public function deleteAction(){
        // criar delete
    }

}
