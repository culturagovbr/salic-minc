<?php

namespace Application\Modules\Readequacao\Service\TransferenciaRecursos;

class TransferenciaRecursos
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

    public function buscarValoresTransferidos()
    {
        $parametros = $this->request->getParams();
        $acao = $this->identificaColuna($parametros['acao']);
        $idPronac = $parametros['idPronac'];

        $mapper = new \Readequacao_Model_TbTransferenciaRecursosEntreProjetosMapper();
        $result = $mapper->obterTransferenciaRecursosEntreProjetos($idPronac, $acao);

        return $this->utf8Encode($result);
    }

    private function identificaColuna($acao)
    {
        $coluna = '';

        switch ($acao) {
            case 'transferidor':
                $coluna = 'a.idPronacTransferidor = ?';
                break;
            case 'recebedor':
                $coluna = 'a.idPronacRecebedor = ?';
                break;
            default:
                throw new Exception('Parametro acao invalido');
        }

        return $coluna;
    }

    private function utf8Encode($result)
    {
        array_walk($result, function (&$value) {
            $value = array_map('utf8_encode', $value->toArray());
        });

        return $result;
    }
}
