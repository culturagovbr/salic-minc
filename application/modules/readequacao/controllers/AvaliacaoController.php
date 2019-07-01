<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;

class Readequacao_AvaliacaoController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
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

    public function indexAction(){
        $data = [];
        $code = 200;
        
        $idReadequacao = $this->getRequest()->getParam('idReadequacao');
        
        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();

        if (!$permissao) {
            $data['permissao'] = false;
            $code = 203;
            $data['message'] = 'Você não tem permissão para acessar este projeto';
            $this->customRenderJsonResponse($data, $code);
        } else {
            $code = 200;
            $data['message'] = 'Readequação gravada com sucesso.';
            $data = $readequacaoService->buscarAvaliacao($idReadequacao);
        }
        
        $this->renderJsonResponse(\TratarArray::utf8EncodeArray($data), $code);
    }

    public function headAction(){}

    public function postAction(){
        $data = [];
        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();
        if (!$permissao) {
            $data['permissao'] = false;
            $code = 203;
            $data['message'] = 'Você não tem permissão para avaliar esta readequação';
        } else {
            $code = 200;
            try {
                $response = $readequacaoService->salvarAvaliacao();
                if ($response) {
                    $data = $response;
                }
            } catch (\Exception $objException) {
                $this->customRenderJsonResponse([
                    'error' => [
                        'code' => 412,
                        'message' => $objException->getMessage()
                    ]
                ], 412);
            }
            $this->renderJsonResponse($data, $code);
        }
    }

    public function putAction(){}

    public function deleteAction(){}
}
