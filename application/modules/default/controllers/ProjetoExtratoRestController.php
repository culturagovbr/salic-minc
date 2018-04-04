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
class ProjetoExtratoRestController extends Minc_Controller_AbstractRest
{
    public function postAction()
    {
    }
    
    public function indexAction()
    {
        # Parametros da Pagina��o.
        $next = $this->_request->getParam('next');
        $offset = $this->_request->getParam('offset');
        $total = $this->_request->getParam('total');
        # Parametros da Consulta.
        $projeto = $this->_request->getParam('projeto');
        $ano = $this->_request->getParam('ano');
        $mes = $this->_request->getParam('mes');

        $modelProjetos = new Projetos();
        $objParam = (object) array(
            'next' => $next,
            'offset' => $offset,
            'idPronac' => $projeto,
            'ano' => $ano,
            'mes' => $mes);
        # Verifica se existe necessidade de buscar o n�mero total de registros da consulta
        if (!$total) {
            $total = $modelProjetos->buscarTotalExtrato($objParam);
        }
        # Busca os dados da lista
        $listaResult = $modelProjetos->buscarExtrato($objParam);
        $listaExtrato = $listaResult->toArray();
        if ($listaExtrato) {
            foreach ($listaExtrato as $identificador => $lancamento) {
                $lancamento['vlLancamento'] = number_format($lancamento['vlLancamento'], 2, ',', '.');
                $listaExtrato[$identificador] = $lancamento;
            }
        }

        # Resposta da autentica��o
        $this->getResponse()->setHttpResponseCode(200)->setBody(json_encode((object) array('list' => $listaExtrato, 'total' => $total)));
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
