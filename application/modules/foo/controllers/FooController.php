<?php

class Foo_FooController extends Zend_Controller_Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $fooModel = new Foo_Model_Foo();
        $this->view->foos = $fooModel->listar();
        $tooModel = new Foo_Model_Too();
        $this->view->toos = $tooModel->listar();
    }
}
