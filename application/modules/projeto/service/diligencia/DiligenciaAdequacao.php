<?php

namespace Application\Modules\Projeto\Service\Diligencia;

use Seguranca;

class DiligenciaAdequacao implements \MinC\Servico\IServicoRestZend
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

    public function listaDiligenciaProjeto()
    {

    }

    public function visualizarDiligenciaAdequacaoProjeto()
    {
        $idPronac = $this->request->idPronac;
        $idAvaliarAdequacaoProjeto = (int) $this->request->id;

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbAvaliarAdequacaoProjeto = new \Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
        $diligenciasAdequacao = $tbAvaliarAdequacaoProjeto->obterAvaliacoesDiligenciadas(
            [
                'a.idPronac = ?' => $idPronac,
                'a.idAvaliarAdequacaoProjeto = ?' => $idAvaliarAdequacaoProjeto
            ]
        )->current();

        $adequacao = $this->obterDiligenciaAdequacaoProjeto($diligenciasAdequacao);

        $adequacao = \TratarArray::utf8EncodeArray($adequacao);

        return $adequacao;
    }

    private function obterDiligenciaAdequacaoProjeto($diligencia)
    {
            $dsAvaliacao = $diligencia['dsAvaliacao'];

            $resultArray = [
                'idAvaliarAdequacaoProjeto' => $diligencia['idAvaliarAdequacaoProjeto'],
                'dsAvaliacao' => $dsAvaliacao,
                'dtAvaliacao' => $diligencia['dtAvaliacao'],
            ];

        return $resultArray;
    }
}
