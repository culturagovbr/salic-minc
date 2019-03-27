<?php

namespace Application\Modules\Projeto\Service\PlanoDistribuicaoIn2013;

class PlanoDistribuicaoIn2013 implements \MinC\Servico\IServicoRestZend
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

    public function buscarPlanoDistribuicaoIn2013()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        if (!empty($idPronac)) {
            $buscarDistribuicao = \RealizarAnaliseProjetoDAO::planodedistribuicao($idPronac);
        }

        foreach ($buscarDistribuicao as $item) {

            $resultArray[] = [
                'idPlanoDistribuicao' => $item->idPlanoDistribuicao,
                'idProjeto' => $item->idProjeto,
                'idProduto' => $item->idProduto,
                'stPrincipal' => $item->stPrincipal,
                'QtdeProduzida' => $item->QtdeProduzida,
                'QtdeProponente' => $item->QtdeProponente,
                'QtdeVendaNormal' => $item->QtdeVendaNormal,
                'QtdePatrocinador' => $item->QtdePatrocinador,
                'QtdeOutros' => $item->QtdeOutros,
                'QtdeVendaPromocional' => $item->QtdeVendaPromocional,
                'Produto' => $item->Produto,
                'Area' => $item->Area,
                'Segmento' => $item->Segmento,
                'PosicaoDaLogo' => $item->PosicaoDaLogo,
                'PrecoUnitarioNormal' => $item->PrecoUnitarioNormal,
                'PrecoUnitarioPromocional' => $item->PrecoUnitarioPromocional,
                'ReceitaNormal' => $item->ReceitaNormal,
                'ReceitaPro' => $item->ReceitaPro,
                'ReceitaPrevista' => $item->ReceitaPrevista,
            ];
        }

        $resultArray = \TratarArray::utf8EncodeArray($resultArray);

        return $resultArray;
    }
}
