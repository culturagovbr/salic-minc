<?php

final class PrestacaoContas_Model_ComprovantePagamento extends MinC_Db_Table_Abstract
{
    protected $_schema = 'bdcorporativo.scSAC';
    protected $_name = 'tbComprovantePagamento';

    public function __construct()
    {
        parent::__construct();
    }

    protected function setDataEmissao($data, $format)
    {
        $d = DateTime::createFromFormat($format, $data);
        if (!$d) {
            throw new Exception('Sem data de emissão');
        }

        $this->dataEmissao = $d;
    }

    protected function setDataPagamento($data, $format)
    {
        $d = DateTime::createFromFormat($format, $data);
        if (!$d) {
            throw new Exception('Sem data de pagamento');
        }

        $this->dataPagamento = $d;
    }

    protected function setTipoDocumento($data)
    {
        if (!$data || !is_numeric($data)) {
            throw new Exception('Sem tipo de documento');
        }

        $this->tipoDocumento = $data;
    }

    public function preencher($request)
    {
        $obj = (json_decode($request));

        $this->eInternacional = $obj->fornecedor->eInternacional;
        $this->item = $obj->item;

        if ($obj->fornecedor->eInternacional) {
            $this->fornecedorInternacional($obj);
        } else {
            $this->fornecedorNacional($obj);
        }
    }

    protected function fornecedorNacional($obj)
    {
        $this->setTipoDocumento($obj->tipo);
        $this->nrComprovante = $obj->numero;
        $this->serie = $obj->serie;
        $this->dsJustificativa = $obj->justificativa;
        $this->vlComprovacao = $obj->valor; // not null
        $this->setDataEmissao($obj->dataEmissao, 'd/m/Y');
        $this->setDataPagamento($obj->dataPagamento, 'd/m/Y');
        $this->idFornecedor = $obj->fornecedor->idAgente;
        $this->tpFormaDePagamento = $obj->forma;
        $this->nrDocumentoDePagamento = $obj->numeroDocumento;

        if ($obj->id) {
            $this->idComprovantePagamento = $obj->id;
        }
    }

    protected function fornecedorInternacional($obj)
    {
        $this->fornecedor['nome'] = $obj->fornecedor->nome;
        $this->fornecedor['endereco'] = $obj->fornecedor->endereco;
        $this->fornecedor['pais'] = $obj->fornecedor->nacionalidade;
        $this->nrDocumentoDePagamento = $obj->numeroDocumento;
        $this->setTipoDocumento($obj->tipo);
        $this->setDataEmissao($obj->dataEmissao, 'd/m/Y');
        $this->setDataPagamento($obj->dataPagamento, 'd/m/Y');
        $this->vlComprovacao = $obj->valor; // not null
        $this->dsJustificativa = $obj->justificativa;
        $this->serie = $obj->serie;
    }

    public function cadastrar()
    {
        /* $this->validarCadastrar(); */

        $arquivoId = $this->upload();
        if (!$arquivoId) {
            throw new Exception('Não existe arquivo.');
        }

        if ($this->eInternacional) {
            $fornecedorInternacional = new PrestacaoContas_Model_FornecedorInternacional();

            $fornecedorInternacional->nome = $this->fornecedor['nome'];
            $fornecedorInternacional->endereco = $this->fornecedor['endereco'];
            $fornecedorInternacional->pais = $this->fornecedor['pais'];

            $this->idFornecedorExterior = $fornecedorInternacional->save();
        }

        $dados = [
            'tpDocumento' => $this->tipoDocumento,
            'dtEmissao' => $this->dataEmissao->format('Y-m-d h:i:s'),
            'idArquivo' => $arquivoId,
            'vlComprovacao' => $this->vlComprovacao,
            'dtPagamento' => $this->dataPagamento->format('Y-m-d h:i:s'),
            'dsJustificativa' => $this->dsJustificativa,
            'nrDocumentoDePagamento' => $this->nrDocumentoDePagamento,
            'nrSerie' => $this->serie,
        ];

        if (!$this->eInternacional) {
            $dados += [
                'nrComprovante' => $this->nrComprovante,
                'tpFormaDePagamento' => $this->tpFormaDePagamento,
                'idFornecedor' => $this->idFornecedor,
            ];
        } else {
            $dados += [
                'idFornecedorExterior' => $this->idFornecedorExterior,
            ];
        }

        $this->idComprovantePagamento = $this->insert($dados);
        $this->comprovarPlanilhaCadastrar();
        return $this->idComprovantePagamento;
    }

    public function atualizar()
    {
        /* $this->validarCadastrar(); */

        $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

        if ($arquivoTamanho) {
            $arquivoId = $this->upload();
        }

        if ($this->eInternacional) {
            $fornecedorInternacional = new PrestacaoContas_Model_FornecedorInternacional();

            $fornecedorInternacional->nome = $this->fornecedor['nome'];
            $fornecedorInternacional->endereco = $this->fornecedor['endereco'];
            $fornecedorInternacional->pais = $this->fornecedor['pais'];

            $this->idFornecedorExterior = $fornecedorInternacional->save();
        }

        $dados = [
            'tpDocumento' => $this->tipoDocumento,
            'dtEmissao' => $this->dataEmissao->format('Y-m-d h:i:s'),
            'vlComprovacao' => $this->vlComprovacao,
            'dtPagamento' => $this->dataPagamento->format('Y-m-d h:i:s'),
            'dsJustificativa' => $this->dsJustificativa,
            'nrDocumentoDePagamento' => $this->nrDocumentoDePagamento,
            'nrSerie' => $this->serie,
        ];

        if ($arquivoTamanho) {
            $dados['idArquivo'] = $arquivoId;
        }

        if (!$this->eInternacional) {
            $dados += [
                'nrComprovante' => $this->nrComprovante,
                'tpFormaDePagamento' => $this->tpFormaDePagamento,
                'idFornecedor' => $this->idFornecedor,
            ];
        } else {
            $dados += [
                'idFornecedorExterior' => $this->idFornecedorExterior,
            ];
        }

        $this->comprovarPlanilhaCadastrar();
        $result = $this->update(
            $dados,
            ['idComprovantePagamento = ?' => $this->idComprovantePagamento]
        );
        return $result;
    }

    public function upload()
    {
        $arquivoModel = new ArquivoModel();
        $arquivoModel->cadastrar('arquivo');
        $idArquivo = $arquivoModel->getId();
        if (empty($idArquivo)) {
            throw new Exception('O arquivo deve ser PDF.');
        }
        return $idArquivo;
    }

    public function excluir()
    {
        if (!$this->idComprovantePagamento) {
            throw new Exception('Comprovante nao informado.');
        }
        $tbComprovantePagamentoxPlanilhaAprovacao = new ComprovantePagamentoxPlanilhaAprovacao();
        $tbComprovantePagamentoxPlanilhaAprovacao->delete(array('idComprovantePagamento = ?' => $this->idComprovantePagamento));
        $vwAnexarComprovantes = new vwAnexarComprovantes();
        $vwAnexarComprovantes->excluirArquivo($this->idComprovantePagamento);

        $tbComprovantePagamento = new ComprovantePagamento();
        $comprovantePagamentoRow = $tbComprovantePagamento->fetchRow(array('idComprovantePagamento = ?' => $this->idComprovantePagamento));

        if ($comprovantePagamentoRow && $comprovantePagamentoRow->idFornecedorExterior) {
            $idfornecedorInvoice = $comprovantePagamentoRow->idFornecedorExterior;
        }
        $comprovantePagamentoRow->delete();
        if (isset($idfornecedorInvoice)) {
            $fornecedorInvoiceTable = new FornecedorInvoice();
            $fornecedorInvoiceTable->getAdapter()->getProfiler()->setEnabled(true);
            $fornecedorInvoiceTable->delete(array('idFornecedorExterior = ?' => $idfornecedorInvoice));
        }
    }

    public function inserirComprovantePagamento($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarComprovantePagamento($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarComprovantePagamento($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    private function validarCadastrar($exterior = false)
    {
        if (!$this->fornecedor) {
            throw new Exception('Fornecedor inv&aacute;lido.');
        }
        if (!$this->item) {
            throw new Exception('Item inv&aacute;lido.');
        }
        if (!$this->tipo) {
            throw new Exception('Comprovante inv&aacute;lido.');
        }
        if (!$this->numero) {
            throw new Exception('N&uacute;mero inv&aacute;lido.');
        }

        # validar periodo
        $itemModel = new PlanilhaAprovacao();
        $projetoModel = new Projetos();
        $projeto = $projetoModel->find($itemModel->find($this->item)->current()->IdPRONAC)->current();
        $dtInicioExecucao = new DateTime($projeto->DtInicioExecucao);
        $dtFimExecucao = new DateTime($projeto->DtFimExecucao);
        if (!$this->dataEmissao || ($this->dataEmissao < $dtInicioExecucao) || ($this->dataEmissao > $dtFimExecucao)) {
            throw new Exception('A data do documento deve estar dentro do per&iacute;odo de execu&ccedil;&atilde;o do projeto.');
        }

        // caso seja comprova��o de pagamento a empresa do exterior, n�o precisa comprovar
        if (!$exterior) {
            if (!$this->comprovanteTipo) {
                throw new Exception('Forma de pagamento inv&aacute;lida.');
            }
        }
        if (!$this->comprovanteNumero) {
            throw new Exception('N&uacute;mero do comprovante inv&aacute;lido.');
        }
        if (!$this->comprovanteValor) {
            throw new Exception('Valor do item inv&aacute;lido.');
        }
    }

    /* public function atualizar($status = 4, $atualizarArquivo = false) */
    /* { */
    /*     $this->validarCadastrar(); */
    /*     // somente mexer no arquivo se houver um arquivo */
    /*     if ($atualizarArquivo) { */
    /*         $arquivoModel = new ArquivoModel(); */
    /*         $arquivoModel->deletar($this->arquivo); */
    /*         $arquivoModel->cadastrar('arquivo'); */
    /*         $arquivoId = $arquivoModel->getId(); */
    /*     } else { */
    /*         $arquivoId = $this->arquivo; */
    /*     } */

    /*     $this->update( */
    /*         array( */
    /*             'idFornecedor' => $this->fornecedor, */
    /*             'tpDocumento' => $this->tipo, */
    /*             'nrComprovante' => $this->numero, */
    /*             'nrSerie' => $this->serie, */
    /*             'dtEmissao' => $this->dataEmissao->format('Y-m-d h:i:s'), */
    /*             'idArquivo' => is_object($arquivoModel) ? $arquivoModel->getId() : $this->arquivo, */
    /*             'vlComprovacao' => $this->comprovanteValor, */
    /*             'dtPagamento' => $this->comprovanteData->format('Y-m-d h:i:s'), */
    /*             'dsJustificativa' => $this->comprovanteJustificativa, */
    /*             'tpFormaDePagamento' => $this->comprovanteTipo, */
    /*             'nrDocumentoDePagamento' => $this->comprovanteNumero, */
    /*         ), */
    /*         array('idComprovantePagamento = ?' => $this->comprovantePagamento) */
    /*     ); */

    /*     $this->comprovarPlanilhaAtualizarStatus($status, $this->comprovanteValor, $this->comprovantePagamento); */
    /* } */

    /**
     *
     */
    public function deletar()
    {
        if (!$this->comprovantePagamento) {
            throw new Exception('Comprovante nao informado.');
        }
        $tbComprovantePagamentoxPlanilhaAprovacao = new ComprovantePagamentoxPlanilhaAprovacao();
        $tbComprovantePagamentoxPlanilhaAprovacao->delete(array('idComprovantePagamento = ?' => $this->comprovantePagamento));
        $vwAnexarComprovantes = new vwAnexarComprovantes();
        $vwAnexarComprovantes->excluirArquivo($this->comprovantePagamento);

        $tbComprovantePagamento = new ComprovantePagamento();
        $comprovantePagamentoRow = $tbComprovantePagamento->fetchRow(array('idComprovantePagamento = ?' => $this->comprovantePagamento));

        if ($comprovantePagamentoRow && $comprovantePagamentoRow->idFornecedorExterior) {
            $idfornecedorInvoice = $comprovantePagamentoRow->idFornecedorExterior;
        }
        $comprovantePagamentoRow->delete();
        if (isset($idfornecedorInvoice)) {
            $fornecedorInvoiceTable = new FornecedorInvoice();
            $fornecedorInvoiceTable->getAdapter()->getProfiler()->setEnabled(true);
            $fornecedorInvoiceTable->delete(array('idFornecedorExterior = ?' => $idfornecedorInvoice));
        }
    }

    public function pesquisarComprovante($idComprovante, $fetchMode = Zend_DB::FETCH_ASSOC)
    {
        $select = "SELECT
                    comp.tpDocumento,
                    comp.nrComprovante,
                    comp.nrSerie,
                    comp.idComprovantePagamento,
                    CAST(comp.dsJustificativa AS TEXT) AS dsJustificativa,
                    comp.vlComprovacao,
                    comp.dtEmissao,
                    comp.dtPagamento,
                    comp.idFornecedor,
                    comp.idFornecedorExterior,
                    comp.idArquivo,
                    comp.dsOutrasFontes,
                    comp.tpFormaDePagamento,
                    comp.nrDocumentoDePagamento,
                    comp.tbComprovantePagamento,
                    arq.nmArquivo,
                    convert(char(10), comp.dtEmissao, 103) as dtEmissao,
                    convert(char(10), comp.dtPagamento, 103) as dtPagamento,
                    (
                        CASE pa.idProduto
                            WHEN 0 THEN ('Administra&ccedil;&atilde;o do projeto')
                            ELSE prod.Descricao
                        END
                    ) as produtonome,
                    pEtapa.Descricao as etapanome,
                    pit.Descricao as itemnome,
                    (
                        CASE tpFormaDePagamento
                            WHEN 1 THEN ('Cheque')
                            WHEN 2 THEN ('Transfer&ecirc;ncia Banc&aacute;ria')
                            WHEN 3 THEN ('Saque/Dinheiro')
                        END
                    ) as tipoFormaPagamentoNome,
                    (
                        CASE tpDocumento
                            WHEN 1 THEN ('Boleto Banc&aacute;rio')
                            WHEN 2 THEN ('Cupom Fiscal')
                            WHEN 3 THEN ('Nota Fiscal/Fatura')
                            WHEN 4 THEN ('Recibo de Pagamento')
                            WHEN 5 THEN ('Aut&ocirc;nomo')
                        END
                    ) as tipoDocumentoNome,
                    ROUND((pa.QtItem * pa.nrOcorrencia * pa.VlUnitario),2) AS valorAprovado,
                    (
                        SELECT sum(b1.vlComprovacao) AS vlPagamento
                        FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                        INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                        INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                        WHERE c1.stAtivo = 'S' AND c1.idPlanilhaAprovacao = pa.idPlanilhaAprovacao
                        GROUP BY a1.idPlanilhaAprovacao
                    ) AS valorComprovado, CAST(cpxpa.dsJustificativa AS TEXT) as JustificativaTecnico

                FROM bdcorporativo.scSAC.tbComprovantePagamento AS comp
                    INNER JOIN bdcorporativo.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS cpxpa ON cpxpa.idComprovantePagamento = comp.idComprovantePagamento
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS pa ON pa.idPlanilhaAprovacao = cpxpa.idPlanilhaAprovacao
                    INNER JOIN SAC.dbo.tbPlanilhaItens AS pit ON pit.idPlanilhaItens = pa.idPlanilhaItem
                    INNER JOIN SAC.dbo.tbPlanilhaEtapa AS pEtapa ON pa.idEtapa = pEtapa.idPlanilhaEtapa
                    INNER JOIN BDCORPORATIVO.scCorp.tbArquivo as arq ON arq.idArquivo = comp.idArquivo
                    LEFT JOIN SAC.dbo.Produto AS prod ON pa.idProduto = prod.Codigo
                WHERE
                    cpxpa.idComprovantePagamento = ?";
        $db = $this->getAdapter();
        $db->setFetchMode($fetchMode);
        $statement = $this->getAdapter()->query($select, array($idComprovante));
        return $statement->fetchAll();
    }

    /**
     * Author: Alysson Vicu�a de Oliveira
     * Descri��o: Altera��o realizada por pedido da �rea Finalistica em 16/02/2016 as 11:33
     * @param $item
     * @return array
     */
    public function pesquisarComprovantePorItem($item, $idPronac = false, $idEtapa = false, $idProduto = false, $idUFDespesa = false, $idMunicipioDespesa = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('comp' => $this->_name),
            array('*',
                'tpDocumento',
                new Zend_Db_Expr('convert(char(10), comp.dtEmissao, 103) as dtEmissao'),
                new Zend_Db_Expr('(
                                        CASE pa.idProduto
                                            WHEN 0 THEN (\'Administra&ccedil;&atilde;o do projeto\')
                                            ELSE prod.Descricao
                                        END
                                     ) AS produtonome'),
                new Zend_Db_Expr('pEtapa.Descricao AS etapanome'),
                new Zend_Db_Expr('pit.Descricao AS itemnome'),
                new Zend_Db_Expr('(
                                            CASE tpFormaDePagamento
                                                WHEN 1 THEN (\'Cheque\')
                                                WHEN 2 THEN (\'Transfer&ecirc;ncia Banc&aacute;ria\')
                                                WHEN 3 THEN (\'Saque/Dinheiro\')
                                            END
                                         ) AS tipoFormaPagamentoNome'),
                new Zend_Db_Expr('(
                                            CASE tpDocumento
                                                WHEN 1 THEN (\'Cupom Fiscal\')
                                                WHEN 2 THEN (\'Guia de Recolhimento\')
                                                WHEN 3 THEN (\'Nota Fiscal/Fatura\')
                                                WHEN 4 THEN (\'Recibo de Pagamento\')
                                                WHEN 5 THEN (\'RPA\')
                                            END
                                         ) AS tipoDocumentoNome'),
                new Zend_Db_Expr('ROUND((pa.QtItem * pa.nrOcorrencia * pa.VlUnitario),2) AS valorAprovado'),
                new Zend_Db_Expr('(
                                            SELECT sum(b1.vlComprovacao) AS vlPagamento
                                            FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                                            INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                                            INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                                            WHERE c1.stAtivo = \'S\' AND c1.idPlanilhaAprovacao = pa.idPlanilhaAprovacao
                                            GROUP BY a1.idPlanilhaAprovacao
                                   ) AS valorComprovado')
            ),
            'bdcorporativo.scSAC'
        )
            ->joinInner(
                array('cpxpa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
                'cpxpa.idComprovantePagamento = comp.idComprovantePagamento',
                array('cpxpa.stItemAvaliado'),
                'bdcorporativo.scSAC'
            )
            ->joinInner(
                array('pa' => 'tbPlanilhaAprovacao'),
                'pa.idPlanilhaAprovacao = cpxpa.idPlanilhaAprovacao',
                array(''),
                'SAC.dbo'
            )
            ->joinInner(
                array('pit' => 'tbPlanilhaItens'),
                'pit.idPlanilhaItens = pa.idPlanilhaItem',
                array(''),
                'SAC.dbo'
            )
            ->joinInner(
                array('pEtapa' => 'tbPlanilhaEtapa'),
                'pa.idEtapa = pEtapa.idPlanilhaEtapa',
                array(''),
                'SAC.dbo'
            )
            ->joinInner(
                array('arq' => 'tbArquivo'),
                'arq.idArquivo = comp.idArquivo',
                array('nmArquivo'),
                'BDCORPORATIVO.scCorp'
            )
            ->joinLeft(
                array('prod' => 'Produto'),
                'pa.idProduto = prod.Codigo',
                array(''),
                'SAC.dbo'
            )
            ->where('pa.idPlanilhaItem = ?', $item)
            ->where('pa.nrFonteRecurso = 109'); //BATIZADO: Incentivo Fiscal Federal

        if ($idPronac) {
            $select->where('pa.idPronac = ?', $idPronac);
        }

        if ($idEtapa) {
            $select->where('pa.idEtapa = ?', $idEtapa);
        }

        if ($idProduto) {
            $select->where('pa.idProduto = ?', $idProduto);
        }

        if ($idUFDespesa) {
            $select->where('pa.idUFDespesa = ?', $idUFDespesa);
        }

        if ($idMunicipioDespesa) {
            $select->where('pa.idMunicipioDespesa = ?', $idMunicipioDespesa);
        }

        $select->order('prod.Descricao ASC');

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($select);
    }

    /**
     *
     */
    public function pesquisarComprovanteRecusado($idPronac)
    {
        $select = "SELECT top 50 a.*,
                    (
                        CASE c.idProduto
                            WHEN 0 THEN ('Administra&ccedil;&atilde;o do projeto')
                            ELSE f.Descricao
                        END
                    ) as produto, b.*, d.Descricao as etapa, e.Descricao as item
            FROM bdcorporativo.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a
            INNER JOIN bdcorporativo.scSAC.tbComprovantePagamento AS b ON b.idComprovantePagamento = a.idComprovantePagamento
            INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c ON c.idPlanilhaAprovacao = a.idPlanilhaAprovacao
            INNER JOIN SAC.dbo.tbPlanilhaEtapa AS d ON d.idPlanilhaEtapa = c.idEtapa
            INNER JOIN SAC.dbo.tbPlanilhaItens AS e ON e.idPlanilhaItens = c.idPlanilhaItem
            LEFT JOIN SAC.dbo.Produto AS f ON f.Codigo = c.idProduto
            WHERE
                stItemAvaliado = 3
                AND IdPRONAC = ?
            ORDER BY 1 DESC";
        $statement = $this->getAdapter()->query($select, array($idPronac));
        return $statement->fetchAll();
    }

    /**
     *
     */
    public function toStdclass()
    {
        return (object)array(
            'comprovantePagamento' => $this->comprovantePagamento,
        );
    }

    protected function comprovarPlanilhaCadastrar()
    {
        $comprovantePlanilha = new ComprovantePagamentoxPlanilhaAprovacao();
        $dados =
            [
                'idComprovantePagamento' => $this->idComprovantePagamento,
                'idPlanilhaAprovacao' => $this->item,
                'vlComprovado' => $this->vlComprovacao,
            ];
        $comprovantePlanilha->insert($dados);
    }

    /**
     * @todo remover esse metodo apos implementacao ideal planilha comprovacao
     */
    public function comprovarPlanilhaAtualizarStatus($status, $vlComprovado, $idComprovantePagamento)
    {
        $comprovantePlanilha = new ComprovantePagamentoxPlanilhaAprovacao();
        $comprovantePlanilha->update(
            array(
                'stItemAvaliado' => $status,  //aguardando analise
                'vlComprovado' => $vlComprovado
            ),
            array('idComprovantePagamento = ?' => $idComprovantePagamento)
        );
    }
}
