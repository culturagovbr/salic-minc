<?php

class Autenticacao_UsuarioController extends MinC_Controller_Rest_Abstract
{
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $permissionsPerMethod  = [];
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        $this->customRenderJsonResponse(['teste index'], 200);
    }

    public function getAction()
    {
        $data=[];

        $auth = Zend_Auth::getInstance();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        $auth = $auth->getIdentity();
        $auth = array_map('trim', (array)$auth);
        $auth = array_map('utf8_encode', $auth);

        $data['usu_codigo'] = $auth['usu_codigo'];
        $data['usu_identificacao'] = $auth['usu_identificacao'];
        $data['usu_nome'] = $auth['usu_nome'];
        $data['usu_pessoa'] = $auth['usu_pessoa'];
        $data['usu_orgao'] = $auth['usu_orgao'];
        $data['usu_org_max_superior'] = $auth['usu_org_max_superior'];

        $data['grupo_ativo'] = $GrupoAtivo->codGrupo;
        $data['orgao_ativo'] = $GrupoAtivo->codOrgao;

        $this->renderJsonResponse($data, 200);
    }

    public function headAction(){}

    public function postAction()
    {
        $this->renderJsonResponse(['post'], 200);
    }

    public function putAction()
    {
    }

    public function deleteAction(){}
}
