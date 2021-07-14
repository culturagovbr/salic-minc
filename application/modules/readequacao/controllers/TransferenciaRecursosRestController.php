<?php

use Application\Modules\Readequacao\Service\TransferenciaRecursos\TransferenciaRecursos as TransferenciaRecursosService;

class Readequacao_TransferenciaRecursosRestController extends Zend_Rest_Controller
{
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
        $valorTransferidoService = new TransferenciaRecursosService($this->getRequest(), $this->getResponse());
        $resposta = $valorTransferidoService->buscarValoresTransferidos();
        $this->view->assign('data', $resposta);

        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction()
    {
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function headAction()
    {
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction()
    {
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction()
    {
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(204);
    }
}
