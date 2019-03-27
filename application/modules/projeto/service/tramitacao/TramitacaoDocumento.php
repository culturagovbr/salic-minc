<?php

namespace Application\Modules\Projeto\Service\Tramitacao;

use Seguranca;

class TramitacaoDocumento implements \MinC\Servico\IServicoRestZend
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

    public function buscarTramitacaoDocumentoProjeto()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tblHistDoc = new \tbHistoricoDocumento();
        $total = $tblHistDoc->buscarHistoricoTramitacaoDocumento(array("p.IdPronac =?"=>$idPronac), null, null, null, false);

        $tramitacoes = $this->obterTramitacaoDocumento($total);

        $tramitacoes = \TratarArray::utf8EncodeArray($tramitacoes);

        return $tramitacoes;
    }

    private function obterTramitacaoDocumento($tramitacoes)
    {
        $resultArray = [];
        foreach ($tramitacoes as $tramitacao) {
            $dsTipoDocumento = $tramitacao['dsTipoDocumento'];
            $noArquivo = $tramitacao['noArquivo'];

            $resultArray[] = [
                'idDocumento' => $tramitacao['idDocumento'],
                'dsTipoDocumento' => $dsTipoDocumento,
                'dtDocumento' => $tramitacao['dtDocumento'],
                'noArquivo' => $noArquivo,
                'dtAnexacao' => $tramitacao['dtJuntada'],
                'Usuario' => $tramitacao['Usuario'],
                'idLote' => $tramitacao['idLote'],
                'Situacao' => $tramitacao['Situacao'],
            ];
        }

        return $resultArray;
    }

}
