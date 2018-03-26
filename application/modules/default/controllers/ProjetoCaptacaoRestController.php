<?php

/**
 * Dados do proponente via REST
 *
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright © 2016 - Ministério da Cultura - Todos os direitos reservados.
 */
class ProjetoCaptacaoRestController extends MinC_Controller_Rest_Abstract
{
    public function postAction()
    {
    }
    
    public function indexAction()
    {
    }
    
    public function getAction()
    {
        $projeto = $this->_request->getParam('id');
        
        $ProjetoCaptacao = new stdClass();
        $ProjetoCaptacao->incentivador = utf8_encode('Sucoritrico Cutrale Ltda');
        $ProjetoCaptacao->numeroRecibo = 1499;
        $ProjetoCaptacao->tipoApoio = utf8_encode('Patrocínio');
        $ProjetoCaptacao->dataCaptacao = '29/12/2015';
        $ProjetoCaptacao->dataTransferencia = '31/12/2015';
        $ProjetoCaptacao->porcentagemCapitado = 419022.40;
        $ProjetoCaptacao->valorCapitado = number_format(419022.40, 2, ',', '.');
        $ProjetoCaptacao->bemServico = utf8_encode('Não');
        $ProjetoCaptacao->valorTotalCapitado = number_format(419022.40, 2, ',', '.');
        $ProjetoCaptacao->porcentagemTotalCapitado = 99.99;

        # Resposta da autenticação.
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($ProjetoCaptacao));
    }

    public function putAction()
    {
    }

    public function deleteAction()
    {
    }
}
