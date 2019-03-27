<?php

use Application\Modules\Navegacao\Service\Footer as FooterService;
use Application\Modules\Solicitacao\Service\Solicitacao\Solicitacao as SolicitacaoService;

class Navegacao_DadosRestController extends MinC_Controller_Rest_Abstract
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
        $dados = [];
        $footerService = new FooterService($this->getRequest(), $this->getResponse());
        $dados['versao'] = $footerService->buscarVersao();

        $solicitacaoService = new SolicitacaoService($this->getRequest(), $this->getResponse());
        $dados['quantidadeSolicitacoes'] = $solicitacaoService->contarSolicitacoes();

        $this->renderJsonResponse($dados, 200);
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
