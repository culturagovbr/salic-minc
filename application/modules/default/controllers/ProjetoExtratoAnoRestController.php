<?php

/**
 * Dados do proponente via REST
 *
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright � 2016 - Minist�rio da Cultura - Todos os direitos reservados.
 */
class ProjetoExtratoAnoRestController extends MinC_Controller_Rest_Abstract
{
    public function postAction()
    {
    }
    
    public function indexAction()
    {
        $projeto = $this->_request->getParam('projeto');

        $modelProjetos = new Projetos();
        $listaResult = $modelProjetos->buscarAnoExtratoDeProjeto($projeto);
        $listaAno = $listaResult->toArray();

        # Resposta da autentica��o
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($listaAno));
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
