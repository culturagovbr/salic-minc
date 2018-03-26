<?php

/* include_once APPLICATION_PATH.'/../library/Zend/Rest/Client.php'; */

/**
 * Classe para controlar As notificações enviadas para os dispositivos móveis.
 * 
 * @version 1.0
 * @package application
 * @subpackage application.notification
 * @link http://www.cultura.gov.br
 * @copyright © 2016 - Ministério da Cultura - Todos os direitos reservados.
 */
class Minc_Notification_Message {
    
    /**
     * CPF do usuário que receberá a mensagem.
     * 
     * @var string
     */
    protected $cpf;

    /**
     * Código do projeto ou idPronac.
     * 
     * @var integer
     */
    protected $codePronac;
    
    /**
     * Código da diligência ou idDiligencia.
     * 
     * @var integer
     */
    protected $codeDiligencia;

    /**
     * Lista de Id dos dispositivos dos usuários que receberão a notificação.
     * 
     * @var array
     */
    protected $listDeviceId;

    /**
     * Lista de Ids registrations dos dispositivos dos usuários que receberão a notificação.
     * 
     * @var array 
     */
    protected $listResgistrationIds;
    
    /**
     * Tipo de mensagem.
     * 
     * @var integer
     */
    protected $tipoMensagem;

    /**
     * Titulo da mensagem.
     * 
     * @var string
     */
    protected $title;
    
    /**
     * Descrição da mensagem.
     * 
     * @var string
     */
    protected $text;

    /**
     * Parametros para exibir os dados da notificação.
     * 
     * @var array
     */
    protected $listParameters;

    /**
     * Url do serviço GCM para enviar notificações.
     * 
     * @var string
     */
    protected $gcmUrl;
    
    /**
     * Chave da aplicação para acessar o serviço GCM.
     * 
     * @var string
     */
    protected $gcmApiKey;
    
    /**
     * Lista de parametros segundo a documentação do serviço que será consumido para o envio de notificações.
     * 
     * @var array
     */
    protected $listParametersService;

    /**
     * Informações do serviço GCM sobre a execução do envio da notificação.
     * 
     * @var stdClass
     */
    protected $response;
    
    /**
     * Classe realizar requisição e usufruir serviços.
     * 
     * @var Zend_Rest_Client 
     */
    protected $client;
    
    /**
     * Classe que abstrai a tabela de mensagens(SAC.dbo.tbMensagem)
     * 
     * @var Mensagem
     */
    protected $modelMensagem;

    public function getCpf() {
        return $this->cpf;
    }

    public function getCodePronac() {
        return $this->codePronac;
    }

    public function getCodeDiligencia() {
        return $this->codeDiligencia;
    }

    public function getListDeviceId() {
        return $this->listDeviceId;
    }

    public function getListResgistrationIds() {
        return $this->listResgistrationIds;
    }

    public function getTipoMensagem() {
        return $this->tipoMensagem;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getText() {
        return $this->text;
    }

    public function getListParameters() {
        return $this->listParameters;
    }

    public function getGcmUrl() {
        return $this->gcmUrl;
    }

    public function getGcmApiKey() {
        return $this->gcmApiKey;
    }

    public function getListParametersService() {
        return $this->listParametersService;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getClient() {
        return $this->client;
    }

    public function getModelMensagem() {
        return $this->modelMensagem;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
        return $this;
    }

    public function setCodePronac($codePronac) {
        $this->codePronac = $codePronac;
        return $this;
    }

    public function setCodeDiligencia($codeDiligencia) {
        $this->codeDiligencia = $codeDiligencia;
        return $this;
    }

    public function setListDeviceId($listDeviceId) {
        $this->listDeviceId = $listDeviceId;
        return $this;
    }

    public function setListResgistrationIds($listResgistrationIds) {
        $this->listResgistrationIds = $listResgistrationIds;
        return $this;
    }

    public function setTipoMensagem($tipoMensagem) {
        $this->tipoMensagem = $tipoMensagem;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    public function setListParameters($listParameters) {
        $this->listParameters = $listParameters;
        return $this;
    }

    public function setGcmUrl($gcmUrl) {
        $this->gcmUrl = $gcmUrl;
        return $this;
    }

    public function setGcmApiKey($gcmApiKey) {
        $this->gcmApiKey = $gcmApiKey;
        return $this;
    }

    public function setListParametersService($listParametersService) {
        $this->listParametersService = $listParametersService;
        return $this;
    }

    public function setResponse(stdClass $response) {
        $this->response = $response;
        return $this;
    }

    public function setClient(Zend_Rest_Client $client) {
        $this->client = $client;
        return $this;
    }

    public function setModelMensagem(Mensagem $modelMensagem) {
        $this->modelMensagem = $modelMensagem;
        return $this;
    }

    /**
     * Envia notificações para dispositivos móveis.
     * 
     * @param array $listResgistrationIds Lista de Ids dos dispositivos dos usuários que receberão a notificação.
     * @param string $title Titulo da mensagem.
     * @param string $text Descrição da mensagem.
     * @param array $listParameters Parametros para exibir os dados da notificação.
     */
    public function __construct($listResgistrationIds = NULL, $title = NULL, $text = NULL, $listParameters = NULL) {
        $config = new Zend_Config_Ini(APPLICATION_PATH .'/configs/application.ini', APPLICATION_ENV);
        $this->gcmUrl = $config->get('default')->resources->view->service->gcmUrl;
        $this->gcmApiKey = $config->get('default')->resources->view->service->gcmApiKey;
        $this->modelMensagem = new Mensagem();

        $this->listResgistrationIds = $listResgistrationIds;
        $this->title = $title;
        $this->text = $text;
        $this->listParameters = $listParameters;
        $this->loadConfig();
    }

    /**
     * Carrega configurações padrões de envio de notificações.
     * 
     * @return \Minc_Notification_Message
     */
    protected function loadConfig(){
        $this->client = new Zend_Rest_Client($this->gcmUrl);
        $this->loadListParametersService();
        
        return $this;
    }
    
    /**
     * Carrega configurações dos parametros utilizados para configurar o serviço de envio de notificações.
     * 
     * @return \Minc_Notification_Message
     */
    protected function loadListParametersService() {
        $this->listParametersService =  array(
            'priority' => 'high',
            'delay_while_idle' => true,
            'registration_ids' => $this->listResgistrationIds,
            'priority' => 'normal',
            'notification' => array(
                'icon' => 'icon',
                'title' => utf8_encode($this->title),
                'body' => utf8_encode($this->text)
            ),
            'data' => $this->listParameters
        );
        
        return $this;
    }
    
    /**
     * Envia notificação para para dispositivos registrados.
     * 
     * @return \Minc_Notification_Message
     */
    public function send() {
        if($this->listResgistrationIds){
            $this->loadListParametersService();
            
            # Envia notificação se existe configurado url do serviço e o código para consumir o serviço GCM.
            if($this->gcmUrl && $this->gcmApiKey){
                $this->client
                    ->getHttpClient()
                        ->setHeaders(
                            array(
                                'Authorization: key='. $this->gcmApiKey,
                                'Content-Type: application/json'))
                        ->setRawData(json_encode($this->listParametersService))
                        ->setUri($this->gcmUrl);
                $this->response = json_decode($this->client->getHttpClient()->request('POST')->getBody());
            }
            $this->save();
        }
        
        return $this;
    }
    
    /**
     * Salva as mensagens enviadas no banco de dados.
     * 
     * @return \Minc_Notification_Message
     */
    private function save() {
        $messageRow = $this->modelMensagem->createRow();
        $messageRow->nrCPF = $this->cpf;
        $messageRow->idPronac = $this->codePronac;
        $messageRow->idDiligencia = $this->codeDiligencia;
        $messageRow->tpMensagem = $this->tipoMensagem;
        $messageRow->titulo = $this->title;
        $messageRow->descricao = $this->text;
        if($this->response && $this->response->success){
            $messageRow->idSuccess = $this->response->success;
            $messageRow->idMulticast = $this->response->multicast_id;
        }
        $messageRow->save();
        $this->modelMensagem->saveListDevice($messageRow, $this->listDeviceId);
        
        return $this;
    }
    
}