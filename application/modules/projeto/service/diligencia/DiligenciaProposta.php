<?php

namespace Application\Modules\Projeto\Service\Diligencia;

class DiligenciaProposta implements \MinC\Servico\IServicoRestZend
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

    public function visualizarDiligenciaProposta()
    {
        $idPreProjeto = $this->request->idPreProjeto;
        $idAvaliacaoProposta = (int) $this->request->id;

        if (empty($idAvaliacaoProposta) || empty($idPreProjeto)) {
            return [];
        }

        $tblPreProjeto = new \Proposta_Model_DbTable_PreProjeto();
        $diligenciasProposta = $tblPreProjeto->listarDiligenciasPreProjeto(
            [
                'pre.idPreProjeto = ?' => $idPreProjeto,
                'aval.idAvaliacaoProposta = ?' => $idAvaliacaoProposta,
                'aval.ConformidadeOK = ? '=> 0
            ]
        )->current();

        $proposta = $this->obterDiligenciaProposta($diligenciasProposta);

        $proposta = \TratarArray::utf8EncodeArray($proposta);

        return $proposta;
    }

    private function obterDiligenciaProposta($diligencia)
    {
        $Solicitacao = $diligencia['Solicitacao'];
        $Resposta = $diligencia['Resposta'];
        $nomeProjeto = $diligencia['nomeProjeto'];

        $resultArray = [
            'idPreprojeto' => $diligencia['pronac'],
            'dataSolicitacao' => $diligencia['dataSolicitacao'],
            'dataResposta' => $diligencia['dataResposta'],
            'nomeProjeto' => $nomeProjeto,
            'Solicitacao' => $Solicitacao,
            'Resposta' => $Resposta,
        ];

        return $resultArray;
    }
}
