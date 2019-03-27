<?php

namespace Application\Modules\Projeto\Service\Diligencia;

use Seguranca;

class DiligenciaProjeto implements \MinC\Servico\IServicoRestZend
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
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tblProjeto = new \Projetos();
        if (!empty($idPronac)) {
            $projeto = $tblProjeto->buscar(array('IdPRONAC = ?' => $idPronac))->current();
            $proposta = [];
            if (isset($projeto->idProjeto) && !empty($projeto->idProjeto)) {
                $tblPreProjeto = new \Proposta_Model_DbTable_PreProjeto();
                $diligenciasProposta = $tblPreProjeto->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $projeto->idProjeto,'aval.ConformidadeOK = ? '=>0));
                $proposta = $this->montarArrayListaDiligenciaProposta($diligenciasProposta);
            }
        }
        $diligencias = $tblProjeto->listarDiligencias(array('pro.IdPRONAC = ?' => $idPronac, 'dil.stEnviado = ?' => 'S'));
        $projeto = $this->montarArrayListaDiligenciaProjeto($diligencias);

        $tbAvaliarAdequacaoProjeto = new \Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
        $diligenciasAdequacao = $tbAvaliarAdequacaoProjeto->obterAvaliacoesDiligenciadas(['a.idPronac = ?' => $idPronac]);
        $adequacao = $this->montarArrayListaDiligenciaAdequacao($diligenciasAdequacao);

        $result['diligenciaProposta'] = $proposta;
        $result['diligenciaProjeto'] = $projeto;
        $result['diligenciaAdequacao'] = $adequacao;

        $result = \TratarArray::utf8EncodeArray($result);

        return $result;
    }

    private function montarArrayListaDiligenciaProposta($diligenciasProposta)
    {
        $resultArray = [];
        foreach ($diligenciasProposta as $diligencia) {

            $resultArray[] = [
                'idPreprojeto' => $diligencia['pronac'],
                'idAvaliacaoProposta' => $diligencia['idAvaliacaoProposta'],
                'dataSolicitacao' => $diligencia['dataSolicitacao'],
            ];
        }

        return $resultArray;
    }

    private function montarArrayListaDiligenciaProjeto($diligencias)
    {
        $resultArray = [];

        foreach ($diligencias as $diligencia) {
            $tipoDiligencia = $diligencia['tipoDiligencia'];
            $qtdia = 40;
            
            $resultArray[] = [
                'produto' => $diligencia['produto'],
                'tipoDiligencia' => html_entity_decode(utf8_encode($diligencia['tipoDiligencia'])),
                'idDiligencia' => $diligencia['idDiligencia'],
                'tipoDiligencia' => $tipoDiligencia,
                'dataSolicitacao' => $diligencia['dataSolicitacao'],
                'dataResposta' => $diligencia['dataResposta'],
                'prazoResposta' => date('Y-m-d H:i:s',strtotime($diligencia['dataSolicitacao'].' +'.$qtdia.' day')),
            ];
        }

        return $resultArray;
    }

    private function montarArrayListaDiligenciaAdequacao($diligenciasAdequacao)
    {
        $resultArray = [];

        foreach ($diligenciasAdequacao as $diligencia) {

            $resultArray[] = [
                'idAvaliarAdequacaoProjeto' => $diligencia['idAvaliarAdequacaoProjeto'],
                'tipoDiligencia' => 'Dilig&ecirc;ncia na An&aacute;lise da adequa&ccedil;&atilde;o &agrave; realidade do projeto.',
                'dtAvaliacao' => $diligencia['dtAvaliacao'],
            ];
        }

        return $resultArray;
    }

    public function visualizarDiligenciaProjeto()
    {
        $idPronac = $this->request->idPronac;
        $idDiligencia = (int) $this->request->id;

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (empty($idDiligencia)) {
            return [];
        }

        $tblProjeto = new \Projetos();
        $diligencia = $tblProjeto->listarDiligencias([
            'pro.IdPRONAC = ?' => $idPronac,
            'dil.idDiligencia = ?' => $idDiligencia,
            'dil.stEnviado = ?' => 'S']
            )->current();

        $diligenciaProjeto = $this->obterDiligenciaProjeto($diligencia);

        $diligenciaProjeto = \TratarArray::utf8EncodeArray($diligenciaProjeto);

        return $diligenciaProjeto;
    }

    private function obterDiligenciaProjeto($diligencia)
    {
        $Solicitacao = $diligencia['Solicitacao'];
        $Resposta = $diligencia['Resposta'];
        $nomeProjeto = $diligencia['nomeProjeto'];

        $arquivos= $this->obterAnexosDiligencias($diligencia);

        $resultArray = [
            'dataSolicitacao' => $diligencia['dataSolicitacao'],
            'dataResposta' => $diligencia['dataResposta'],
            'Solicitacao' => $Solicitacao,
            'Resposta' => $Resposta,
            'nomeProjeto' => $nomeProjeto,
            'arquivos' => $arquivos
        ];

        return $resultArray;
    }

    private function obterAnexosDiligencias($diligencia)
    {
        $arquivo = new \Arquivo();
        $arquivos = $arquivo->buscarAnexosDiligencias($diligencia['idDiligencia']);
        $arquivoArray = [];
        foreach ($arquivos as $arquivo) {
            $arquivoArray[] = [
                'idArquivo' => $arquivo->idArquivo,
                'nmArquivo' => $arquivo->nmArquivo,
                'dtEnvio' => $arquivo->dtEnvio,
                'idDiligencia' => $arquivo->idDiligencia,
            ];
        }
        return $arquivoArray;
    }
}
