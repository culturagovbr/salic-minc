<?php

namespace Application\Modules\Projeto\Service\DocumentosAssinados;

class DocumentosAssinados implements \MinC\Servico\IServicoRestZend
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

    public function buscarDocumentosAssinados()
    {
        $idPronac = $this->request->idPronac;

        $idPronac = isset($idPronac) ? $idPronac : null;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        $documentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $dados = $documentoAssinatura->obterDocumentosAssinadosPorProjeto($idPronac);

        $itemArray = [];
        foreach ($dados as $dado) {
            $itemArray[] = [
                'pronac' => $dado['pronac'],
                'nomeProjeto' => $dado['nomeProjeto'],
                'dsAtoAdministrativo' => $dado['dsAtoAdministrativo'],
                'dt_criacao' => $dado['dt_criacao'],
                'idDocumentoAssinatura' => $dado['idDocumentoAssinatura'],
                'IdPRONAC' => $dado['IdPRONAC'],
            ];
        }

        $itemArray = \TratarArray::utf8EncodeArray($itemArray);

        return $itemArray;
    }
}
