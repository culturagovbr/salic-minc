<?php

class ErrorController extends Zend_Controller_Action
{
    private $ravenClient;

    public function init()
    {
        $config = Zend_Registry::get("config")->toArray();
        if ($config['errorHandler'] && $config['errorHandler']['sentryURL']) {
            $this->instanciarRaven($config['errorHandler']['sentryURL']);
        }
    }

    private function instanciarRaven($url)
    {
        $this->ravenClient = new Raven_Client($url);

        $auth = Zend_Auth::getInstance();
        $auth = $auth->getIdentity();
        $auth->usu_seguranca = '********';
        $auth->usu_controle = '********';
        $auth->usu_validacao = '********';
        $auth->Senha =  '********';

        $this->ravenClient->user_context(array(
            'auth' => $auth
        ));

        $error_handler = new Raven_ErrorHandler($this->ravenClient);
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();
        $this->ravenClient->install();
    }

    /**
     * Action para exibir mensagem ao usuario nao logado que tentou acessar algo
     * de usuario que necessitam estar autenticados.
     */
    public function noauthAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setRender('error');
        $helper = $this->_helper->getHelper('Layout');

        $this->view->message = 'Você precisa efetuar login para visualizar este conteúdo.';
        $this->view->errorType = 'login';
    }

    /**
     * Action para exibir mensagem ao usuario que nao tem permissao para visualizar
     * determinado conteudo.
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
     */
    public function notfoundAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setRender('error');
        $this->view->message = 'Página não encontrada.<br /> Você não possui permissão de acesso ou o link desejado não existe mais no sistema.';
        $this->view->errorType = 'pagina';
    }

    public function errorPlanetAction()
    {

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
                $this->view->message = 'Action não encontrada';
                break;
            default:
        }

        // Log exception, if logger available
        $log = $this->getLog();
        if ($log) {
            $log->crit($this->view->message, $errors->exception);
        }
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    /**
     * Retorna o erro gerado pelo bootstrap.
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
     */
    public function errorAction()
    {
        $error = $this->_getParam('error_handler');
        if($this->ravenClient) {
            $this->ravenClient->captureException($error->exception);
        }

        if (APPLICATION_ENV === 'development') {
            $this->_helper->viewRenderer->setNoRender();
            $request = clone $this->getRequest();
            // Don't set controller or module; use current values
            $request->setActionName('error-planet');
            return $this->_helper->actionStack($request);
        }

        $this->getResponse()->clearBody();
        // pega a excecao e manda para o template
        $this->_helper->viewRenderer->setViewSuffix('phtml');

        $this->view->ambiente = APPLICATION_ENV;
        $this->view->exception = $error->exception;
        $this->view->request = $error->request;
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
    }
}
