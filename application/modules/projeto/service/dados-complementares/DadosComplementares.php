<?php

namespace Application\Modules\Projeto\Service\DadosComplementares;

use Seguranca;

class DadosComplementares implements \MinC\Servico\IServicoRestZend
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

    public function buscarDadosComplementares()
    {
        $idPronac = $this->request->idPronac;

        $resultArray = [];
        $resultArray['CustosVinculados'] = [];
        $resultArray['Proposta'] = '';

        if (empty($idPronac)) {
            return $resultArray;
        }
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tblProjeto = new \Projetos();
        $rsProjeto = $tblProjeto->buscar(array('IdPronac=?'=>$idPronac,'idProjeto IS NOT NULL'=>'?'))->current();

        $rsProposta = [];
        if (!empty($rsProjeto)) {
            $tblProposta = new \Proposta_Model_DbTable_PreProjeto();
            $rsProposta = $tblProposta->buscar(['idPreProjeto=?'=> $rsProjeto->idProjeto])->current();
        }

        if (empty($rsProposta)) {;
            return $resultArray;
        }

        $tbCustosVinculadosMapper = new \Proposta_Model_DbTable_TbCustosVinculados();
        $itensCustosVinculados = $tbCustosVinculadosMapper->buscarCustosVinculados(['idProjeto = ?' => $rsProjeto->idProjeto])->toArray();

        $CustosVinculadosArray = $this->montaArrayCustosVinculados($itensCustosVinculados);
        $PropostaArray = $this->montaArrayProposta($rsProposta);

        $resultArray['CustosVinculados'] = $CustosVinculadosArray;
        $resultArray['Proposta'] = $PropostaArray;

        $resultArray = \TratarArray::utf8EncodeArray($resultArray);

        return $resultArray;
    }

    private function montaArrayCustosVinculados($itensCustosVinculados) {
        $custosVinculadosArray = [];
        foreach ($itensCustosVinculados as $item) {

            $custosVinculadosArray[] = [
                'Descricao' => $item['item'],
                'Percentual' => $item['pcCalculo'],
                'dtCadastro' => $item['dtCadastro']
            ];
        }
        return $custosVinculadosArray;
    }

    private function montaArrayProposta($rsProposta) {
        $PropostaArray = [
            'Objetivos' => $rsProposta['Objetivos'],
            'Justificativa' => $rsProposta['Justificativa'],
            'Acessibilidade' => $rsProposta['Acessibilidade'],
            'DemocratizacaoDeAcesso' => $rsProposta['DemocratizacaoDeAcesso'],
            'EtapaDeTrabalho' => $rsProposta['EtapaDeTrabalho'],
            'FichaTecnica' => $rsProposta['FichaTecnica'],
            'ImpactoAmbiental' => $rsProposta['ImpactoAmbiental'],
            'EspecificacaoTecnica' => $rsProposta['EspecificacaoTecnica'],
            'OutrasInformacoes' => $rsProposta['EstrategiadeExecucao'],
            'Sinopse' => $rsProposta['Sinopse'],
            'DescricaoAtividade' => $rsProposta['DescricaoAtividade'],
            'DescricaoTipicidade' => $rsProposta['DescricaoTipicidade'],
            'DescricaoTipologia' => $rsProposta['DescricaoTipologia'],
        ];

        $PropostaArray = array_map('trim', $PropostaArray);

        return $PropostaArray;
    }
}
