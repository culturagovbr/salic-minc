<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;

class Readequacao_ReverterAlteracaoItemController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::PROPONENTE,
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
        
        $idPronac = $this->getRequest()->getParam('idPronac');
        $idReadequacao = $this->getRequest()->getParam('idReadequacao');
        $idPlanilhaItem = $this->getRequest()->getParam('idPlanilhaItem');
        $idPlanilhaAprovacao = $this->getRequest()->getParam('idPlanilhaAprovacao');
        
        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();
        if (!$permissao) {
            $data['permissao'] = false;
            $data['message'] = 'Você não tem permissão para alterar esta readequação';
            $this->customRenderJsonResponse($data, $code);
        } else {
            try {
                $data = $readequacaoService->reverterAlteracaoItem(
                    $idPronac,
                    $idReadequacao,
                    $idPlanilhaItem,
                    $idPlanilhaAprovacao
                );
                
            } catch (\Exception $objException) {
                $this->customRenderJsonResponse([
                    'error' => [
                        'code' => 412,
                        'message' => $objException->getMessage()
                    ]
                ], 412);
            }
        }
        
        $this->renderJsonResponse($data, $code);
    }

    public function putAction(){}

    public function deleteAction(){}

}
