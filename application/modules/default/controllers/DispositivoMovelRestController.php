<?php

class DispositivoMovelRestController extends Minc_Controller_AbstractRest
{
    public function init()
    {
        $this->setPublicMethod('get');
        $this->setPublicMethod('post');
        $this->setPublicMethod('index');
        parent::init();
    }

    public function postAction()
    {
        # Pegando parametros via POST no formato JSON
        $body = $this->getRequest()->getRawBody();
        $post = Zend_Json::decode($body);
        $registrationId = $post['registrationId'];
        $cpf = isset($post['cpf'])? $post['cpf']: null;
        
        $modelDispositivoMovel = new Dispositivomovel();
        $dispositivo = $modelDispositivoMovel->salvar($registrationId, $cpf);

        # Resposta da autenticação.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($dispositivo));
    }
    
    public function indexAction()
    {
    }
    
    public function getAction()
    {
    }

    public function putAction()
    {
    }

    public function deleteAction()
    {
    }
}
