<?php

use Application\Modules\Navegacao\Service\Comunicados as ComunicadosService;

class Navegacao_ProjetoRestController extends MinC_Controller_Rest_Abstract
{
    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array())
    {
        $permissionsPerMethod  = ['*'];

        $this->setValidateUserIsLogged();
        $this->setProtectedMethodsProfilesPermission($permissionsPerMethod);

        parent::__construct($request, $response, $invokeArgs);
    }

    public function init()
    {
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext('get', 'json')
            ->addActionContext('index', 'json')
            ->addActionContext('post', 'json')
            ->addActionContext('put', 'json')
            ->addActionContext('delete', 'json')
            ->initContext('json');
    }

    public function indexAction()
    {
        $pronac = $this->_request->getParam("pronac");

        if (empty($pronac)) {
            var_dump("Informe o Pronac");
            die;
        }

        $tbProjetos = new \Projeto_Model_DbTable_Projetos();
        $where = [];

        $where['AnoProjeto + Sequencial like (?) or NomeProjeto like (?)'] = $pronac.'%';
        $data['projetos'] = $tbProjetos->buscarProjeto($where, null,  10)->toArray();
        $data['count'] = count($data['projetos']);

        $this->customRenderJsonResponse(\TratarArray::utf8EncodeArray($data), 200);
    }

    public function getAction()
    {
        $this->renderJsonResponse(200);
    }

    public function headAction(){}

    public function postAction()
    {
        $this->renderJsonResponse(201);

    }

    public function putAction()
    {
        $this->renderJsonResponse(200);
    }

    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(204);
    }
}
