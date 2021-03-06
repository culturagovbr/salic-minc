<?php

use Application\Modules\Readequacao\Service\Readequacao\Readequacao as ReadequacaoService;

class Readequacao_TiposDisponiveisController extends MinC_Controller_Rest_Abstract
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

    public function indexAction(){
        $data = [];
        $code = 200;

        $idPronac = $this->getRequest()->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $readequacaoService = new ReadequacaoService($this->getRequest(), $this->getResponse());
        $permissao = $readequacaoService->verificarPermissaoNoProjeto();
        if (!$permissao) {
            $data['permissao'] = false;
            $data['message'] = 'Voc&ecirc; não tem permissão para visualizar esta readequação';
            $this->customRenderJsonResponse($data, $code);
        } else {
            $data = $readequacaoService->buscarTiposDisponiveis($idPronac);
        }

        $this->renderJsonResponse($data, $code);
    }

    public function headAction(){}

    public function postAction(){}

    public function putAction(){}

    public function deleteAction(){}

}
