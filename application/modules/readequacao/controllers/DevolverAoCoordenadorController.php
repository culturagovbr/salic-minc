<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;

class Readequacao_DevolverAoCoordenadorController extends MinC_Controller_Rest_Abstract
{

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $profiles = [
            Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,
        ];

        $permissionsPerMethod  = [
            'post' => [
                Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER,
            ]
        ];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        $subRoutes = [];
        $this->registrarSubRoutes($subRoutes);

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
            $data['message'] = 'Voc&ecirc; não tem permissão para devolver esta readequação.';
            $this->customRenderJsonResponse($data, $code);
        } else {
            $encaminhar = $readequacaoService->devolverAoCoordenador();
            if ($encaminhar) {
                $data['message'] = "Readequação devolvida para o coordenador de acompanhamento.";
            }
        }

        $this->renderJsonResponse($data, $code);
    }

    public function putAction(){}

    public function deleteAction(){}

}
