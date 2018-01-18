<?php

/**
 * Trata as mensagens de erro do sistema
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @copyright c 2010 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class ErrorController extends Zend_Controller_Action
{

    /**
     * Action para exibir mensagem ao usuario nao logado que tentou acessar algo
     * de usuario que necessitam estar autenticados.
     *
     * @name noauthAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  25/08/2016
     */
    public function noauthAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setRender('error');
        $helper = $this->_helper->getHelper('Layout');

        $this->view->message = 'Você precisa efetuar login para visualizar este conteúdo.';//<br /><a href="/">Clique aqui para fazer login ou realizar cadastro</a>
        $this->view->errorType = 'login';
    }

    /**
     * Action para exibir mensagem ao usuario que nao tem permissao para visualizar
     * determinado conteudo.
     *
     * @name notallowedAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  25/08/2016
     */
    public function notallowedAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setRender('error');
        $this->view->message = 'Você não tem permissão para acessar este conteúdo.';
        $this->view->errorType = 'permissao';
    }

    /**
     * Action para exibir mensagem informando que o sistema nao encontrou a
     * pagina solicitada.
     *
     * @name notfoundAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  25/08/2016
     */
    public function notfoundAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setRender('error');
        $this->view->message = 'Página não encontrada.<br /> Você não possui permissão de acesso ou o link desejado não existe mais no sistema.';
        $this->view->errorType = 'pagina';
    }

    /**
     *
     * @name errorPlanetAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  25/08/2016
     */
    public function errorPlanetAction()
    {

//        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setRender('error-planet');

        $this->view
            ->headLink()
            ->appendStylesheet(
                $this->getFrontController()->getBaseUrl() .
                '/public/css/error.css'
            );
        $this->view->baseUrl = $this->getFrontController()->getBaseUrl();


        $errors = $this->_getParam('error_handler');

        if (!$errors) {
            $this->view->message = 'Um erro ocorreu.';
            return;
        }

        if ($errors->type != 'pagina' || $errors->type != 'permissao' || $errors->type != 'login') {
            $this->_helper->layout()->setLayout('error');
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                $this->view->message = 'Página não encontrada. Tem certeza que você digitou o endereço corretamente?';
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                $this->view->message = 'Controller não encontrada';
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                //$this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Action não encontrada';
                break;
            default:
                // application error
                //$this->getResponse()->setHttpResponseCode(500);
//                $this->view->message = $errors;
//                $this->view->errorType = 'aplicacao';
//                Zend_Debug::dump($errors);
//                break;
        }

        // Log exception, if logger available
//        if ($log = $this->getLog()) {
//            $log->crit($this->view->message, $errors->exception);
//        }
//
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;
    }

    /**
     * Retorna o erro gerado pelo bootstrap.
     *
     * @name getLog
     * @return bool
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  25/08/2016
     */
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');

        if (!$bootstrap->hasResource('Log')) {
            return false;
        }

        $log = $bootstrap->getResource('Log');

        return $log;
    }

    /**
     * Trata as excecoes para os usuarios
     * @access public
     * @param void
     * @return void
     */
    public function errorAction()
    {
        if (APPLICATION_ENV === 'development') {
            $this->_helper->viewRenderer->setNoRender();
            $request = clone $this->getRequest();
            // Don't set controller or module; use current values
            $request->setActionName('error-planet');
            return $this->_helper->actionStack($request);
        }

        // limpa o conte�do gerado antes do erro
        $this->getResponse()->clearBody();

        // pega a excecao e manda para o template
        $this->_helper->viewRenderer->setViewSuffix('phtml');
        $error = $this->_getParam('error_handler');
        $this->view->ambiente     = APPLICATION_ENV;
        $this->view->exception    = $error->exception;
        $this->view->request      = $error->request;
        $this->view->message_type = "ERROR";

        switch ($error->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'P&aacute;gina n&atilde;o encontrada!';
                break;

            default:
                $this->view->message = 'Desculpe, ocorreu algum erro no sistema, tente novamente mais tarde!';
                break;
        }
    } // fecha errorAction()
} // fecha class
