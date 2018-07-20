<?php

use Application\Modules\Foo\Service\Foo\Bar as BarService;

class Foo_FooRestController extends Zend_Rest_Controller
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
//        $fooModel = new Foo_Model_Foo();
//        $this->view->foos = $fooModel->listar();
//
//        $tooModel = new Foo_Model_Too();
//        $this->view->toos = $tooModel->listar();
//        $dataAux = $_GET;
//        $this->view->assign('data', $dataAux);
//        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction()
    {
        $parametros = $this->getRequest()->getParams();
        $barService = new BarService($this->getRequest(), $this->getResponse());
        $resposta = $barService->buscar($parametros['id']);

        $this->view->assign('data', $resposta);

        $this->getResponse()->setHttpResponseCode(200);
    }

    public function headAction()
    {
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction()
    {
        $barService = new BarService($this->getRequest(), $this->getResponse());
        $resposta = $barService->salvarRegistro();

        $this->view->assign('data', $resposta);
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction()
    {

        $this->view->assign('data', 'asda');
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function deleteAction()
    {
        $this->getResponse()->setHttpResponseCode(204);
    }
}
