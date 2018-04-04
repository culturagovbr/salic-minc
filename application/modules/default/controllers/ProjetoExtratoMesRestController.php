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
class ProjetoExtratoMesRestController extends Minc_Controller_AbstractRest
{
    public function postAction()
    {
    }
    
    public function indexAction()
    {
        $projeto = $this->_request->getParam('projeto');
        $ano = $this->_request->getParam('ano');
        $listaMes = array();
        $modelProjetos = new Projetos();

        $objListaResult = $modelProjetos->buscarMesExtratoDeProjeto($projeto, $ano);
        $arrListaResult = $objListaResult->toArray();
        if ($arrListaResult) {
            foreach ($arrListaResult as $mes) {
                $mes['descricao'] = utf8_encode($mes['descricao']);
                $listaMes[] = (object)$mes;
            }
        }

        # Resposta da autentica��o
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode($listaMes));
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
