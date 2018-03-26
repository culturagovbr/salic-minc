<?php

/**
 * Description of ComprovantePagamentoService
 *
 * @author mikhail
 */
class ComprovantePagamentoService
{
    /**
     *
     */
    const TIPO_DOCUMENTO_BOLETO_BANCARIO = 1;
    const TIPO_DOCUMENTO_CUPOM_FISCAL = 2;
    const TIPO_DOCUMENTO_NOTA_FISCAL = 3;
    const TIPO_DOCUMENTO_FATURA = 3;
    const TIPO_DOCUMENTO_RECIBO_PAGAMENTO = 4;
    const TIPO_DOCUMENTO_AUTONOMO = 5;

    const TIPO_PAGAMENTO_CHEQUE = 1;
    const TIPO_PAGAMENTO_TRANSFERENCIA_BANCARIA = 2;
    const TIPO_PAGAMENTO_SAQUE = 3;
    const TIPO_PAGAMENTO_DINHEIRO = 3;

    /**
     * Metodo que est� disponivel para ser consumido como soap.
     * Responsavel por cadastrar um comprovante de pagamento
     *
     * @param string $fornecedorCpfCnpj
     * @param integer $itemId
     * @param string $tipo
     * @param integer $numero
     * @param string $dataEmissao
     * @param string $arquivoNome
     * @param string $arquivoConteudo
     * @param string $comprovanteTipo
     * @param string $comprovanteData
     * @param string $comprovanteJustificativa
     * @param double $comprovanteValor
     * @param string $serie
     * @param string $comprovanteJustificativa
     * @throws Exception
     * @return string Description
     */
    public function cadastrar(
        $fornecedorCpfCnpj,
        $itemId,
        $tipo,
        $numero,
        $dataEmissao,
        $arquivoNome,
        $arquivoConteudo,
        $comprovanteTipo,
        $comprovanteData,
        $comprovanteValor,
        $comprovanteNumero,
        $serie = null,
        $comprovanteJustificativa = null
    ) {
        try {
            /**
             *  @todo mover este trecho de buscar fornecedor para um model
             */
            $cnpjcpf = preg_replace('/\.|-|\//', '', $fornecedorCpfCnpj);
            $agentesDao = new Agente_Model_DbTable_Agentes();
            $fornecedores = $agentesDao->buscarFornecedor(array(' A.CNPJCPF = ? ' => $cnpjcpf));
            if (!$fornecedores->count()) {
                throw new Exception('Fornecedor n�o encontrado');
            }
            $fornecedor = $fornecedores->current();

            /**
             * @todo mover o trecho de arquivo para uma classe de model
             */
            $arquivo = base64_decode($arquivoConteudo);
            $filePath = tempnam("/tmp", "ComprovantePagamentoService_");
            file_put_contents($filePath, $arquivo);
            $_FILES['arquivo']['name'] = $arquivoNome; // nome
            $_FILES['arquivo']['tmp_name'] = $filePath; // nome tempor�rio
            $_FILES['arquivo']['type'] = mime_content_type($filePath); // tipo
            $_FILES['arquivo']['size'] = filesize($filePath); // tamanho

            $arquivoModel = new ArquivoModel();
            $arquivoModel->cadastrar('arquivo');

            $comprovantePagamentoModel = new ComprovantePagamento(
                    null,
                    $fornecedor->idAgente,
                    $itemId,
                    constant("self::{$tipo}"),
                    $numero,
                    $serie,
                    new DateTime($dataEmissao),
                    $arquivoModel->getId(),
                    constant("self::{$comprovanteTipo}"),
                    new DateTime($comprovanteData),
                    $comprovanteValor,
                    $comprovanteNumero,
                    $comprovanteJustificativa
            );

            $comprovantePagamentoModel->cadastrar();
        } catch (Exception $exception) {
            $mensagem = $exception->getMessage();
            if (false !== strpos($mensagem, 'DateTime::__construct()')) {
                $matches = array();
                preg_match('#string \((.+)\) at#', $mensagem, $matches);
                $mensagem = "A data \"{$matches[1]}\" est� fora do formato aceito: \"yyyy-mm-dd\".";
            }
            new Exception($mensagem, null, $exception);
        }
    }
}
