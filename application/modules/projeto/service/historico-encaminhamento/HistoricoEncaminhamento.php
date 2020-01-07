<?php

namespace Application\Modules\Projeto\Service\HistoricoEncaminhamento;

use Seguranca;

class HistoricoEncaminhamento implements \MinC\Servico\IServicoRestZend
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

    public function buscarHistoricoEncaminhamento()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            //****** Dados do Projeto - Cabecalho *****//

            $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
            $historicoEncaminhamento = $tbDistribuirParecer->buscarHistoricoEncaminhamento(array('a.idPRONAC = ?'=>$idPronac));
        }
        $resultArray = [];

        $Encaminhamentos = $this->montaArrayHistoricoEncaminhamento($historicoEncaminhamento);

        $resultArray['Encaminhamentos'] = $Encaminhamentos;

        $resultArray = \TratarArray::utf8EncodeArray($resultArray);

        return $resultArray;
    }

    private function montaArrayHistoricoEncaminhamento($historicoEncaminhamento) {
        $result = [];

        foreach ($historicoEncaminhamento as $item) {
            $produto = $item['Produto'];
            $unidade = $item['Unidade'];
            $observacao = $item['Observacao'];

            $result[] = [
                'Produto' => $produto,
                'Unidade' => $unidade,
                'Observacao' => $observacao,
                'DtEnvio' => $item['DtEnvio'],
                'DtRetorno' => $item['DtRetorno'],
                'qtDias' => $item['qtDias']
            ];

        }
        return $result;
    }

}
