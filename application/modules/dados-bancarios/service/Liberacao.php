<?php

namespace Application\Modules\DadosBancarios\Service\Liberacao;

class Liberacao
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

    public function buscarLiberacao()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
            $tbLiberacao = new \Liberacao();
            $rsLiberacao = $tbLiberacao->liberacaoPorProjeto($idPronac);
            $rsLiberacao = $rsLiberacao ? $rsLiberacao->toArray() : [];

            return $rsLiberacao;
        }
    }
}

