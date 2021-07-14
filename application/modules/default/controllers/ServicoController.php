<?php

/**
 * Description of Servico
 * Classe abstrata que trata dos servi�os publicados como SOAP
 * sendo necess�rio apenas criar um controller que herde desta classe, a action
 * deve apenas e usar a fun��o setServiceClass() passando uma classe que deve conter
 * os metodos que ser&aacute;o publicados pelo soap, o  com o phpdoc dos m�todos desta classe
 * devem estar devidamente anotados, pois a class Zend_Soap_AutoDiscover faz uso
 * dos mesmos.
 * @author mikhail
 */
abstract class ServicoController extends Zend_Controller_Action
{
    private $serviceClass = null;

    /**
     * Desabilita o layout para retornar apenas o xml SOAP
     */
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        Zend_Layout::getMvcInstance()->disableLayout();
    }

    /**
     * M�todo executado ap�s qualquer action.
     * Respons�vel por instanciar a classe de soap correta e configurala
     * disponibilizando assim o servi�o para ser consumido.
     */
    public function postDispatch()
    {
        $request = $this->getRequest();

        $params = $request->getParams();
        $router = Zend_Controller_Front::getInstance()->getRouter();

        if ($this->hasParam('wsdl')) {
            $server = new Zend_Soap_AutoDiscover();
        } else {
            $server = new Zend_Soap_Server();
            $url = "{$request->getScheme()}://{$request->getHttpHost()}{$router->assemble($params)}";
            $server->setUri($url);
            $server->setEncoding('ISO-8859-1');
        }

        $server->setClass($this->serviceClass);
        $server->handle();
    }

    /**
     * Seta a classe de servi�o que ser&aacute; estudada (autodiscover) pelo phpdoc
     * e disponibilizada como servi�o para ser consumida
     * @param string $class
     */
    public function setServiceClass($class)
    {
        $this->serviceClass = $class;
    }
}
