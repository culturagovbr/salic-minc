<?php

namespace Application\Modules\Projeto\Service\CertidoesNegativas;

class CertidoesNegativas implements \MinC\Servico\IServicoRestZend
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

    public function buscarCertidoesNegativas()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        $Projetos = new \Projetos();
        $rs = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        $sv = new \certidaoNegativa();
        $resultado = $sv->buscarCertidaoNegativa($rs->CgcCpf);

        $certidoes = [];
        $resultArray = [];

        foreach ($resultado as $item) {
            $dsCertidao = $item['dsCertidao'];
            $situacao = $item['Situacao'];
            $objDateTimeDtEmissao = $item['DtEmissao'];
            $objDateTimeDtValidade = $item['DtValidade'];

            if ($item['DtValidade'] == '1900-01-01 00:00:00') {
                $objDateTimeDtValidade = null;
            }

            $itemArray = [
                'dsCertidao' => $dsCertidao,
                'CodigoCertidao' => $item['CodigoCertidao'],
                'DtEmissao' => $objDateTimeDtEmissao,
                'DtValidade' => $objDateTimeDtValidade,
                'Pronac' => $item['Pronac'],
                'Situacao' => $situacao,
            ];

            $certidoes[] = $itemArray;
        }


        $resultArray['certidoes'] = $certidoes;

        $resultArray = \TratarArray::utf8EncodeArray($resultArray);

        return $resultArray;
    }
}
