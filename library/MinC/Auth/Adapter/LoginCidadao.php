<?php

/**
 * Adapter para autentição 'LoginCidadao', utilizado para que o m&eacute;todo 'authenticate' da Classe 'Zend_Auth' consiga receber como parâmetro um objeto que implemente a interface 'Zend_Auth_Adapter_Interface'
 * @author Vin&iacute;cius Feitosa da Silva <viniciusfesil@mail.com>
 * @since 20/10/16 14:32
 */
class MinC_Auth_Adapter_LoginCidadao implements Zend_Auth_Adapter_Interface
{

    public function __construct()
    {

    }

    /**
     * Performs an authentication attempt
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     * @author Vin&iacute;cius Feitosa da Silva <viniciusfesil@mail.com>
     */

    public function authenticate()
    {
        $objSession = new Zend_Session_Namespace("opauth");

        if(!isset($objSession->auth)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $_SESSION['opauth']);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $_SESSION['opauth']);
    }
}
