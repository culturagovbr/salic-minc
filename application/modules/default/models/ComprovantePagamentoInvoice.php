<?php
/**
 * @author Caio Lucena <caioflucena@gmail.com>
 */
class ComprovantePagamentoInvoice extends ComprovantePagamento
{
    protected $nif;

    /**
     * Zend Table
     */
    protected $_banco = 'bdcorporativo';
    protected $_schema = 'bdcorporativo.scSAC';
    protected $_name = 'tbComprovantePagamento';

    public function __construct(
            $id,
            $fornecedor,
            $item,
            $nif,
            $serie,
            $dataEmissao,
            $arquivo,
            $comprovanteData,
            $comprovanteValor,
            $justificativa
            ) {
        parent::__construct();
        $this->comprovantePagamento = $id;
        $this->fornecedor = $fornecedor;
        $this->tipo = $serie; // Tipo Invoice
        $this->item = $item;
        $this->nif = $nif;
        $this->serie = $serie;
        $this->dataEmissao = $dataEmissao;
        $this->arquivo = $arquivo;
        $this->comprovanteData = $comprovanteData;
        $this->comprovanteValor = $comprovanteValor;
        $this->comprovanteJustificativa = $justificativa;
    }

    public function getNif()
    {
        return $this->nif;
    }

    public function setNif($nif)
    {
        $this->nif = $nif;
    }

    /**
     *
     */
    private function validarCadastrar()
    {
        if (!$this->getFornecedor()) {
            throw new Exception('Fornecedor inv�lido.');
        }
        if (!$this->getItem()) {
            throw new Exception('Item inv�lido.');
        }
        if (!$this->getTipoDocumento()) {
            throw new Exception('Comprovante inv�lido.');
        }
        if (!$this->getNif()) {
            throw new Exception('NIF inv�lido.');
        }

        # validar periodo
        $itemModel = new PlanilhaAprovacao();
        $projetoModel = new Projetos();
        $projeto = $projetoModel->find($itemModel->find($this->item)->current()->IdPRONAC)->current();
        $dtInicioExecucao = new DateTime($projeto->DtInicioExecucao);
        $dtFimExecucao = new DateTime($projeto->DtFimExecucao);

        if (!$this->dataEmissao || ($this->dataEmissao < $dtInicioExecucao) || ($this->dataEmissao > $dtFimExecucao)) {
            throw new Exception('A data do documento deve estar dentro do per�odo de execu��o do projeto.');
        }

        if (!$this->comprovanteValor) {
            throw new Exception('Valor do item inv�lido.');
        }
    }

    /**
     * @todo usar objeto de agente quando disponivel (pesquisa)
     * @todo usar objeto de arquivo quando disponivel (cadastro)
     * @todo usar objeto de planilha comprovante quando disponivel (cadastro)
     */
    public function cadastrar()
    {
        $this->validarCadastrar();
        try {
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
            $this->getFornecedor()->setId(
                    $this->getFornecedor()->insert(
                            array(
                                'dsNome' => $this->getFornecedor()->getNome(),
                                'dsEndereco' => $this->getFornecedor()->getEndereco(),
                                'dsPais' => $this->getFornecedor()->getPais(),
                            )
                    )
            );
            $this->setComprovantePagamento($this->insert(
                array(
                    'tpDocumento' => $this->getTipoDocumento(),
                    'nrComprovante' => $this->getNif(),
                    'idFornecedorExterior' => $this->getFornecedor()->getId(),
                    'nrSerie' => $this->getSerie(),
                    'dtEmissao' => $this->getDataEmissao()->format('Y-m-d h:i:s'),
                    'idArquivo' => $this->getArquivo(),
                    'vlComprovacao' => $this->getComprovanteValor(),
                    'dtPagamento' => $this->getComprovanteData()->format('Y-m-d h:i:s'),
                    'dsJustificativa' => $this->getComprovanteJustificativa(),
                )
            ));
            $this->comprovarPlanilhaCadastrar();
            Zend_Db_Table::getDefaultAdapter()->commit();
        } catch (Exception $exception) {
            try {
                Zend_Db_Table::getDefaultAdapter()->rollBack();
            } catch (PDOException $ex) {
                /*
                 * Ao fazer o rollback o driver PDO do linux lan�a um
                 * PDOException, aparentemente sem motivo, j� que o mesmo
                 * consegue por vias de fato realizar o rollback. Neste ponto
                 * fa�o esse tratamento vazio para que n�o suba a exce��o para
                 * o Controller e que este por sua vez mostre ao usu�rio na tela
                 */
            }
        }
    }

    public function atualizar($status = 4, $atualizarArquivo = false)
    {
        $this->validarCadastrar(true);
        // somente mexer no arquivo se houver um arquivo
        if ($atualizarArquivo) {
            $arquivoModel = new ArquivoModel();
            $arquivoModel->deletar($this->arquivo);
            $arquivoModel->cadastrar('arquivoInternacional');
            $arquivoId = $arquivoModel->getId();
        } else {
            $arquivoId = $this->arquivo;
        }

        $this->update(
            array(
                'idFornecedorExterior' => $this->fornecedor,
                'tpDocumento' => $this->tipo,
                'nrComprovante' => $this->nif,
                'nrSerie' => $this->serie,
                'dtEmissao' => $this->dataEmissao->format('Y-m-d h:i:s'),
                'idArquivo' => $arquivoId,
                'vlComprovacao' => $this->comprovanteValor,
                'dtPagamento' => $this->comprovanteData->format('Y-m-d h:i:s'),
                'dsJustificativa' => $this->comprovanteJustificativa,
                'tpFormaDePagamento' => $this->comprovanteTipo,
                'nrDocumentoDePagamento' => $this->comprovanteNumero,
            ),
            array('idComprovantePagamento = ?' => $this->comprovantePagamento)
        );
        $this->comprovarPlanilhaAtualizarStatus($status, $this->comprovanteValor, $this->comprovantePagamento);
    }
}
