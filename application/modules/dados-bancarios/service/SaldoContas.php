<?php

namespace Application\Modules\DadosBancarios\Service\SaldoContas;

class SaldoContas
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscarSaldoContas()
    {
        $idPronac = $this->request->idPronac;
        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
            $where = array();
            $where['idPronac = ?'] = $idPronac;

            $dadosSaldoBancario = new \Projetos();

                $busca = $dadosSaldoBancario->extratoDeSaldoBancario($where, ['dtSaldoBancario DESC'], null, null)->toArray();

            return $busca;
    }
}
