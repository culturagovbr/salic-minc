<?php

class Foo_JwtController extends Zend_Controller_Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $jwt = new Zend_Session_Namespace('jwt');
        var_dump($jwt->token);die;
    }
}
