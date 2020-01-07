<?php

namespace Application\Modules\AvaliacaoResultados\Service\ParecerTecnico;


class AvaliacaoComprovante
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;


    function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscarDadosAvaliacaoComprovante()
    {
        $planilhaAprovacaoModel = new \AvaliacaoResultados_Model_DbTable_Item();

        $projeto = $planilhaAprovacaoModel
            ->buscarDadosDoItem(
                $this->request->idPronac,
                $this->request->uf,
                $this->request->produto,
                $this->request->idmunicipio,
                $this->request->idPlanilhaItem,
                $this->request->etapa
            )->current()->toArray();

        $response = \TratarArray::utf8EncodeArray($projeto);

        return $response;
    }

    public function buscarComprovantes()
    {
        $idPronac = $this->request->getParam("idPronac");
        $codigoProduto = $this->request->getParam('produto');
        $tipo = $this->request->getParam('tipo');
        $stItemAvaliado = $this->request->getParam('stItemAvaliado');
        $idPlanilhaItem = $this->request->getParam("idPlanilhaItem");
        $etapa = $this->request->getParam('etapa');

        $vwComprovacoes = new \PrestacaoContas_Model_vwComprovacaoFinanceiraProjetoPorItemOrcamentario();

        if ($tipo == 'internacional') {
            $comprovantes = $vwComprovacoes->comprovacoesInternacionais(
                $idPronac,
                $idPlanilhaItem,
                $stItemAvaliado,
                $codigoProduto,
                null,
                $etapa
            );
        } else {
            $comprovantes = $vwComprovacoes->comprovacoesNacionais(
                $idPronac,
                $idPlanilhaItem,
                $stItemAvaliado,
                $codigoProduto,
                null,
                $etapa
            );
        }

        $comprovantes = \TratarArray::utf8EncodeArray($comprovantes->toArray());

        $dataAux = [];
        foreach ($comprovantes as $chave => $comprovante) {

            $dataAux[$chave] = $comprovante;
            $dataAux[$chave]['tipo'] = $comprovante['tipo'];
            $dataAux[$chave]['numero'] = $comprovante['numero'];
            $dataAux[$chave]['serie'] = $comprovante['serie'];
            $dataAux[$chave]['forma'] = $comprovante['forma'];
            $dataAux[$chave]['valor'] = $comprovante['vlComprovacao'];
            $dataAux[$chave]['justificativa'] = \TratarString::tratarTextoRico($comprovante['dsJustificativaProponente']);
            $dataAux[$chave]['dataPagamento'] = $comprovante['dtPagamento'];
            $dataAux[$chave]['dataEmissao'] = $comprovante['dataEmissao'];
            $dataAux[$chave]['numeroDocumento'] = $comprovante['numeroDocumento'];
            $dataAux[$chave]['nrDocumentoDePagamento'] = $comprovante['nrDocumentoDePagamento'];
            $dataAux[$chave]['fornecedor']['CNPJCPF'] = $comprovante['CNPJCPF'];
            $dataAux[$chave]['fornecedor']['nome'] = $comprovante['nmFornecedor'];
            $dataAux[$chave]['fornecedor']['endereco'] = $comprovante['endereco'];
            $dataAux[$chave]['fornecedor']['id'] = $comprovante['id'];
            $dataAux[$chave]['fornecedor']['pais'] = $comprovante['pais'];
            $dataAux[$chave]['fornecedor']['nacionalidade'] = $comprovante['pais'];
            $dataAux[$chave]['arquivo']['nome'] = $comprovante['nmArquivo'];
            $dataAux[$chave]['arquivo']['id'] = $comprovante['idArquivo'];
        }

        return $dataAux;
    }
}

