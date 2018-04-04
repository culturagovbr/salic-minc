<?php

/**
 * Foo_FooController Controller de exemplo para arquitetura.
 *
 * @uses GenericControllerNew
 * @package Controller
 * @version 0.1
 * @author  wouerner <wouerner@gmail.com>
 */
class Foo_FooController extends Zend_Controller_Action
{

    /**
     * init Metodo de inicializacao da classe
     *
     * @access public
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * indexAction Metodo padrao para execucao da controller
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        $fooModel = new Foo_Model_Foo();

        $this->view->foos = $fooModel->listar();

        $tooModel = new Foo_Model_Too();

        $this->view->toos = $tooModel->listar();
    }
}
