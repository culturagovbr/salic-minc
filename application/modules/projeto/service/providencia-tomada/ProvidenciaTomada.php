<?php

namespace Application\Modules\Projeto\Service\ProvidenciaTomada;

class ProvidenciaTomada implements \MinC\Servico\IServicoRestZend
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

    public function buscarProvidenciaTomada()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        $pronacArray = [];
        $pronacArray['p.IdPRONAC = ?'] = $idPronac;

        $tblHisSituacao = new \HistoricoSituacao();
        $result = $tblHisSituacao->buscarHistoricosEncaminhamentoIdPronac($pronacArray, ['h.dtSituacao DESC'], null, null, false);

        $providenciaTomada = $this->montaArrayProvidenciaTomada($result);
        $resultArray['providenciaTomada'] = $providenciaTomada;

        $resultArray = \TratarArray::utf8EncodeArray($resultArray);

        return $resultArray;
    }

    private function montaArrayProvidenciaTomada($providenciaTomada)
    {
        $resultArray = [];

        foreach ($providenciaTomada as $providencia) {
            $ProvidenciaTomada = $providencia['ProvidenciaTomada'];
            $usuario = $providencia['usuario'];
            
            $resultArray[] = [
                'DtSituacao' => $providencia['DtSituacao'],
                'Situacao' => $providencia['Situacao'],
                'ProvidenciaTomada' => $ProvidenciaTomada,
                'cnpjcpf' => $providencia['cnpjcpf'],
                'usuario' => $usuario
            ];
        }

        return $resultArray;
    }

}
