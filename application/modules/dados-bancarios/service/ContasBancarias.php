<?php

namespace Application\Modules\DadosBancarios\Service\ContasBancarias;

class ContasBancarias
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

    public function buscarContaBancaria()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {

            $tblContaBancaria = new \ContaBancaria();
            $rsContaBancaria = $tblContaBancaria->contaPorProjeto($idPronac);
            $rsContaBancaria = $rsContaBancaria ? $rsContaBancaria->toArray() : [];

            return $rsContaBancaria;
        }
    }
}

