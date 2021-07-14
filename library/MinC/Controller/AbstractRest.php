<?php

/**
 * Classe para controlar API de Servi�os.
 *
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2016 - Ministério da Cultura - Todos os direitos reservados.
 */
abstract class Minc_Controller_AbstractRest extends Zend_Rest_Controller{

    /**
     * Chave secreta para criptografar e descriptografar os dados.
     *
     * @var string
     */
    protected $encryptHash;

    /**
     * Chave p�blica usada por aplicativos para consumir os servi�os.
     * @todo transformar em lista quando houver v�rias aplica��es consumindo dados.
     *
     * @var string
     */
    protected $publicKey;

    /**
     * Chave de acesso utilizada pelo usu�rio logado no sistema.
     *
     * @var string
     */
    protected $authorization;

    /**
     * Código �nico do dispositivo conectado fornecido pelo servi�o GCM.
     *
     * @var string
     */
    protected $registrationId;

    /**
     * Dados do usu�rio que est� consumindo o servi�o.
     *
     * @var Sgcacesso
     */
    protected $usuario;

    /**
     * M�todos que podem ser acessados sem autentica��o.
     *
     * @var array
     */
    protected $arrPublicMethod = array();

    public function init(){
        $this->publicKey = Zend_Registry::get('config')->resources->view->service->salicMobileHash;
        $this->encryptHash = Zend_Registry::get('config')->resources->view->service->encryptHash;
        $this->registrationId = $this->getRequest()->getHeader('registrationId');

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $this->authorization = $this->getRequest()->getHeader('Authorization');
        if(!$this->getRequest()->getHeader('ApplicationKey') || $this->publicKey != $this->getRequest()->getHeader('ApplicationKey')) {
            $this->_forward('error-forbiden');
        } else if(!$this->authorization && !in_array($this->getRequest()->getActionName(), $this->getPublicMethod())) {
            $this->_forward('error-forbiden');
        }
        if($this->authorization){
            $this->carregarUsuario();
        }
        $this->salvarUltimoAcesso();
    }

    /**
     * Salva data e hora do �ltimo acesso do dispositivo.
     *
     * @return VOID
     */
    protected function salvarUltimoAcesso() {
        if($this->registrationId){
            $modelDispositivo = new Dispositivomovel();
            $dispositivoRow = $modelDispositivo->fetchRow("idRegistration = '{$this->registrationId}'");
            if($dispositivoRow){
                $dispositivoRow->dtAcesso = new Zend_Db_Expr('GETDATE()');
                $dispositivoRow->save();
            }
        }
    }

    /**
     * Carrega o objeto usu�rio com os dados do usu�rio que est� consumindo os dados atrav�s da token.
     *
     * @param integer $id
     * @return Zend_Db_Table_Rowset_Abstract
     */
    protected function carregarUsuario() {
        $keyCpf = Seguranca::dencrypt($this->authorization, $this->encryptHash);
        $cpf = str_replace($this->publicKey, '', $keyCpf);
        $modelSgcAcesso = new Autenticacao_Model_Sgcacesso();
        $this->usuario = $modelSgcAcesso->fetchRow("Cpf = '{$cpf}'");
        # Valida se o usu�rio � v�lido.
        if(!$this->usuario){
            $this->_forward('error-forbiden');
        }

        return $this->usuario;
    }

    /**
     * Atribui Código de erro a situa��o da resposta da requisi��o e informa o erro de acesso negado.
     *
     * @return JSON
     */
    protected function errorForbidenAction(){
        $this->getResponse()
            ->setHttpResponseCode(403)
            ->setBody(json_encode(array('error' => '403 Forbidden', 'message' => 'Acesso negado'))
        );
    }

    /**
     * Responde requisi��o de op��es de met�dos suportados ao servi�o.
     *
     * @return JSON
     */
    public function optionsAction(){
        $this->getResponse()->setHttpResponseCode(200);
        $this->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, DELETE, HEAD, PUT');
        $this->getResponse()->setHeader('Access-Control-Allow-Headers', 'x-requested-with, Content-Type, origin, authorization, accept, client-security-token');
    }

    public function getPublicMethod() {
        return $this->arrPublicMethod;
    }

    public function setPublicMethod($publicMethod) {
        if (is_array($publicMethod)){
            $this->arrPublicMethod = array_merge ($this->arrPublicMethod, $publicMethod);
        }
        else if (is_string($publicMethod)){
            $this->arrPublicMethod[] = $publicMethod;
        }
        $this;
    }

    public function headAction() {

    }

}
