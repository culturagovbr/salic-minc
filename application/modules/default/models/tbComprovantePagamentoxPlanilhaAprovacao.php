<?php

class tbComprovantePagamentoxPlanilhaAprovacao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "bdcorporativo.scsac";
    protected $_name    = "tbComprovantePagamentoxPlanilhaAprovacao";


    /**
     * M�todo para buscar as inconsist�ncias do extrato de movimenta��o banc�ria
     * @param string $pronac
     * @param array $data_recibo
     * @param string $proponente
     * @param string $incentivador
     * @param array $data_credito
     * @return object
     */
    public function buscarDadosItens($idPronac, $idPlanilhaAprovacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
//                $select->distinct();
        $select->from(
            array("a" => $this->_name),
            array('vlComprovado as vlComprovacao'),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinInner(
            array("b" => "tbComprovantePagamento"),
            "a.idComprovantePagamento = b.idComprovantePagamento",
            array('DtPagamento','tpDocumento','nrComprovante','dtEmissao','idArquivo','tpFormaDePagamento'),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinLeft(
            array("c" => "tbPlanilhaAprovacao"),
            "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
            array(),
            "SAC.dbo"
        );
        $select->joinLeft(
            array("d" => "tbPlanilhaItens"),
            "c.idPlanilhaItem = d.idPlanilhaItens",
            array('Descricao as Item'),
            "SAC.dbo"
        );
        $select->joinLeft(
            array("e" => "Nomes"),
            "b.idFornecedor = e.idAgente",
            array('Descricao as Fornecedor'),
            "Agentes.dbo"
        );
        $select->joinLeft(
            array("f" => "tbArquivo"),
            "b.idArquivo = f.idArquivo",
            array('nmArquivo'),
            "BDCORPORATIVO.scCorp"
        );

        $select->where("c.stAtivo = ?", 'S');
        $select->where("c.idPronac = ?", $idPronac);
        $select->where("a.idPlanilhaAprovacao = ?", $idPlanilhaAprovacao);

        //$select->order("t.dtCredito");

        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()


    public function buscarRelacaoPagamentos($idPronac, $idPlanilhaAprovacao = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array("a" => $this->_name),
                    array(
                        new Zend_Db_Expr(
                            "
                            c.idPronac,
                            d.Descricao as Item,
                            b.idComprovantePagamento,
                            a.idPlanilhaAprovacao,
                            g.CNPJCPF,
                            e.Descricao as Fornecedor,
                            b.DtPagamento as DtComprovacao,
                            b.DtPagamento,
                            CASE tpDocumento
                                WHEN 1 THEN ('Cupom Fiscal')
                                WHEN 2 THEN ('Guia de Recolhimento')
                                WHEN 3 THEN ('Nota Fiscal/Fatura')
                                WHEN 4 THEN ('Recibo de Pagamento')
                                WHEN 5 THEN ('RPA')
                                ELSE ''
                            END as tbDocumento,
                            b.nrComprovante,
                            b.dtEmissao as DtEmissao,
                            CASE
                              WHEN b.tpFormaDePagamento = '1'
                                 THEN 'Cheque'
                              WHEN b.tpFormaDePagamento = '2'
                                 THEN 'Transferencia Banc&aacute;ria'
                              WHEN b.tpFormaDePagamento = '3'
                                 THEN 'Saque/Dinheiro'
                                 ELSE ''
                            END as tpFormaDePagamento,
                            b.nrComprovante,
                            b.nrDocumentoDePagamento,
                            a.vlComprovado as vlPagamento,
                            b.idArquivo,
                            b.dsJustificativa,
                            f.nmArquivo"
                        )
                    ),
                "BDCORPORATIVO.scSAC"
            );

        $select->joinInner(
                array("b" => "tbComprovantePagamento"),
                "a.idComprovantePagamento = b.idComprovantePagamento",
                array(),
                "BDCORPORATIVO.scSAC"
            );
        $select->joinLeft(
                array("c" => "tbPlanilhaAprovacao"),
                "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
                array(),
                "SAC.dbo"
            );
        $select->joinLeft(
                array("d" => "tbPlanilhaItens"),
                "c.idPlanilhaItem = d.idPlanilhaItens",
                array(),
                "SAC.dbo"
            );
        $select->joinLeft(
                array("e" => "Nomes"),
                "b.idFornecedor = e.idAgente",
                array(),
                "Agentes.dbo"
            );
        $select->joinLeft(
                array("f" => "tbArquivo"),
                "b.idArquivo = f.idArquivo",
                array('nmArquivo'),
                "BDCORPORATIVO.scCorp"
            );
        $select->joinLeft(
                array("g" => "Agentes"),
                "b.idFornecedor = g.idAgente",
                array(),
                "Agentes.dbo"
            );

        $select->where("c.idPronac = ?", $idPronac);
        if ($idPlanilhaAprovacao) {
            $select->where("a.idPlanilhaAprovacao = ?", $idPlanilhaAprovacao);
        }

        $select->order(["d.Descricao" ,"b.DtPagamento DESC"]);
        $select->order("e.Descricao");

        return $this->fetchAll($select);
    }

    public function pagamentosPorUFMunicipio($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("a" => $this->_name),
            array(
                new Zend_Db_Expr("
                    c.idPlanilhaAprovacao,
                    Agentes.dbo.fnUFAgente(idFornecedor) AS UFFornecedor,
                    Agentes.dbo.fnMunicipioAgente(idFornecedor) AS MunicipioFornecedor,
                    c.idPronac,
                    d.Descricao AS Item,
                    g.CNPJCPF,
                    e.Descricao AS Fornecedor,
                    b.DtPagamento AS DtComprovacao,
                    b.vlComprovacao AS vlPagamento,
                    b.idArquivo,
                    f.nmArquivo
                ")
            ),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinInner(
            array("b" => "tbComprovantePagamento"),
            "a.idComprovantePagamento = b.idComprovantePagamento",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinLeft(
            array("c" => "tbPlanilhaAprovacao"),
            "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
            array(),
            "SAC.dbo"
        );
        $select->joinLeft(
            array("d" => "tbPlanilhaItens"),
            "c.idPlanilhaItem = d.idPlanilhaItens",
            array(),
            "SAC.dbo"
        );
        $select->joinLeft(
            array("e" => "Nomes"),
            "b.idFornecedor = e.idAgente",
            array(),
            "Agentes.dbo"
        );
        $select->joinLeft(
            array("f" => "tbArquivo"),
            "b.idArquivo = f.idArquivo",
            array(),
            "BDCORPORATIVO.scCorp"
        );
        $select->joinLeft(
            array("g" => "Agentes"),
            "b.idFornecedor = g.idAgente",
            array(),
            "Agentes.dbo"
        );

        //Altera��o realizada no dia 25/02/2016 a pedido da area demandante.
        #$select->where("c.stAtivo = ?", 'S');
        $select->where("c.idPronac = ?", $idPronac);

        $select->order("d.Descricao");
        $select->order("e.Descricao");



        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()


    public function pagamentosConsolidadosPorUfMunicipio($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("a" => $this->_name),
            array(
                new Zend_Db_Expr("
                    Agentes.dbo.fnUFAgente(idFornecedor) AS UFFornecedor,
                    Agentes.dbo.fnMunicipioAgente(idFornecedor) AS MunicipioFornecedor,
                    SUM(b.vlComprovacao) as vlPagamento
                ")
            ),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinInner(
            array("b" => "tbComprovantePagamento"),
            "a.idComprovantePagamento = b.idComprovantePagamento",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinLeft(
            array("c" => "tbPlanilhaAprovacao"),
            "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
            array(),
            "SAC.dbo"
        );
        $select->joinLeft(
            array("d" => "tbPlanilhaItens"),
            "c.idPlanilhaItem = d.idPlanilhaItens",
            array(),
            "SAC.dbo"
        );
        $select->joinLeft(
            array("g" => "Agentes"),
            "b.idFornecedor = g.idAgente",
            array(),
            "Agentes.dbo"
        );

        $select->where("c.idPronac = ?", $idPronac);

        $select->order(new Zend_Db_Expr("Agentes.dbo.fnUFAgente(idFornecedor)"));
        $select->order(new Zend_Db_Expr("Agentes.dbo.fnMunicipioAgente(idFornecedor)"));
        $select->group(new Zend_Db_Expr("Agentes.dbo.fnUFAgente(idFornecedor), Agentes.dbo.fnMunicipioAgente(idFornecedor)"));

        return $this->fetchAll($select);
    }


    public function buscarRelatorioBensDeCapital($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("a" => $this->_name),
            array(
                new Zend_Db_Expr("
                    CASE
                        WHEN tpDocumento = 1 THEN 'Boleto Banc�rio'
                        WHEN tpDocumento = 2 THEN 'Cupom Fiscal'
                        WHEN tpDocumento = 3 THEN 'Nota Fiscal / Fatura'
                        WHEN tpDocumento = 4 THEN 'Recibo de Pagamento'
                        WHEN tpDocumento = 5 THEN 'RPA'
                    END as Titulo,
                    b.nrComprovante,
                    d.Descricao as Item,
                    DtEmissao as dtPagamento,
                    dsItemDeCusto Especificacao,
                    dsMarca as Marca,
                    dsFabricante as Fabricante,
                    (c.qtItem*nrOcorrencia) as Qtde,
                    c.vlUnitario,
                    (c.qtItem*nrOcorrencia*c.vlUnitario) as vlTotal
                ")
            ),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinInner(
            array("b" => "tbComprovantePagamento"),
            "a.idComprovantePagamento = b.idComprovantePagamento",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinInner(
            array("c" => "tbPlanilhaAprovacao"),
            "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
            array(),
            "SAC.dbo"
        );
        $select->joinInner(
            array("d" => "tbPlanilhaItens"),
            "c.idPlanilhaItem = d.idPlanilhaItens",
            array(),
            "SAC.dbo"
        );
        $select->joinInner(
            array("e" => "tbItemCusto"),
            "e.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
            array(),
            "BDCORPORATIVO.scSAC"
        );

        //Linha retirada para corrigir problema na Visualiza��o dos Projetos (24/02/2016)
        #$select->where("c.stAtivo = ?", 'S');

        $select->where("c.idPronac = ?", $idPronac);
        $select->order(3);

        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()


    public function buscarRelatorioFisico($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("a" => $this->_name),
            array(
                new Zend_Db_Expr("
                    f.idPlanilhaEtapa,
                    f.Descricao AS Etapa,
                    d.Descricao AS Item,
                    g.Descricao AS Unidade,
                    (c.qtItem*nrOcorrencia) AS qteProgramada,
                    (c.qtItem*nrOcorrencia*c.vlUnitario) AS vlProgramado,
                    ((sum(b.vlComprovacao) / (c.qtItem*nrOcorrencia*c.vlUnitario)) * 100) AS PercExecutado,
                    (sum(b.vlComprovacao)) AS vlExecutado,
                    (100 - (sum(b.vlComprovacao) / (c.qtItem*nrOcorrencia*c.vlUnitario)) * 100) AS PercAExecutar
                ")
            ),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinInner(
            array("b" => "tbComprovantePagamento"),
            "a.idComprovantePagamento = b.idComprovantePagamento",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $select->joinInner(
            array("c" => "tbPlanilhaAprovacao"),
            "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
            array(),
            "SAC.dbo"
        );
        $select->joinInner(
            array("d" => "tbPlanilhaItens"),
            "c.idPlanilhaItem = d.idPlanilhaItens",
            array(),
            "SAC.dbo"
        );
        $select->joinInner(
            array("f" => "tbPlanilhaEtapa"),
            "c.idEtapa = f.idPlanilhaEtapa",
            array(),
            "SAC.dbo"
        );
        $select->joinInner(
            array("g" => "tbPlanilhaUnidade"),
            "c.idUnidade= g.idUnidade",
            array(),
            "SAC.dbo"
        );

        //Linha retirada para corrigir problema na Visualiza��o dos Projetos (24/02/2016)
        //$select->where("c.stAtivo = ?", 'S');

        $select->where("c.idPronac = ?", $idPronac);
        $select->group(array('c.idPronac','f.Descricao','d.Descricao','g.Descricao','c.qtItem','nrOcorrencia','c.vlUnitario','f.idPlanilhaEtapa'));
        $select->order(array(1,2));

        return $this->fetchAll($select);
    }

    public function buscarRelatorioExecucaoReceita($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
                array('a' => 'Captacao'),
                array( new Zend_Db_Expr("'RECEITA' AS tipo, a.CgcCpfMecena, c.Descricao AS Nome, sum(CaptacaoReal) AS vlIncentivado") ),
            'SAC.dbo'
        );
        $a->joinInner(
                array('b' => 'Agentes'),
            "a.CgcCpfMecena = b.CNPJCpf",
                array(),
            'AGENTES.dbo'
        );
        $a->joinInner(
                array('c' => 'Nomes'),
            "b.idAgente = c.idAgente",
                array(),
            'AGENTES.dbo'
        );

        $a->where(new Zend_Db_Expr('a.AnoProjeto+a.Sequencial = (SELECT x.Anoprojeto+x.Sequencial FROM SAC.dbo.Projetos x WHERE x.idPronac = ?)'), $idPronac);

        $a->group(array('a.CgcCpfMecena','c.Descricao'));
        $a->order(array('2','3'));

        return $this->fetchAll($a);
    }

    public function buscarRelatorioExecucaoDespesa($idPronac)
    {
        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->from(
                array('a' => $this->_name),
                array( new Zend_Db_Expr("'DESPESA' AS tipo, f.Descricao as Etapa, d.Descricao AS Item, sum(b.vlComprovacao) AS vlPagamento") ),
            'BDCORPORATIVO.scSAC'
        );
        $b->joinInner(
                array('b' => 'tbComprovantePagamento'),
            "a.idComprovantePagamento = b.idComprovantePagamento",
                array(),
            'BDCORPORATIVO.scSAC'
        );
        $b->joinInner(
                array('c' => 'tbPlanilhaAprovacao'),
            "a.idPlanilhaAprovacao = c.idPlanilhaAprovacao",
                array(),
            'SAC.dbo'
        );
        $b->joinInner(
                array('d' => 'tbPlanilhaItens'),
            "c.idPlanilhaItem = d.idPlanilhaItens",
                array(),
            'SAC.dbo'
        );
        $b->joinInner(
                array('f' => 'tbPlanilhaEtapa'),
            "c.idEtapa = f.idPlanilhaEtapa",
                array(),
            'SAC.dbo'
        );
        //Linha retirada para corrigir problema na Visualiza��o dos Projetos (24/02/2016)
        #$b->where('c.stAtivo = ?', 'S');

        $b->where('c.idPronac = ?', $idPronac);
        $b->group(array('c.idPronac','f.Descricao','d.Descricao'));
        $b->order(array('2','3'));
        return $this->fetchAll($b);
    } // fecha m�todo buscarDados()


    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()



    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idTmpCaptacao = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()



    /**
     * M�todo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirDados($where)
    {
        $where = "idTmpCaptacao = " . $where;
        return $this->delete($where);
    } // fecha m�todo excluirDados()


    public function buscarDadosParaRemanejamento($idTmpCaptacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("a" => $this->_name),
            array(
                "a.nrAnoProjeto",
                "a.nrSequencial",
                new Zend_Db_Expr("(SELECT SUBSTRING(dsInformacao,2,4) FROM SAC.dbo.tbDepositoIdentificadoCaptacao WHERE SUBSTRING(dsInformacao,1,1)='1') as NumeroRecibo"),
                "a.nrCpfCnpjIncentivador",
                new Zend_Db_Expr("(SELECT b.Enquadramento FROM sac.dbo.enquadramento AS b WHERE b.AnoProjeto+b.Sequencial =  a.nrAnoProjeto+a.nrSequencial) AS MedidaProvisoria"),
                "a.dtChegadaRecibo",
                "a.dtCredito",
                new Zend_Db_Expr("0 AS CaptacaoUfir"),
                new Zend_Db_Expr("(SELECT idUsuario FROM SAC.DBO.tbDepositoIdentificadoCaptacao WHERE SUBSTRING(dsInformacao,1,1)='1') AS Logon"),
                new Zend_Db_Expr("(SELECT idPronac FROM sac.dbo.Projetos AS p WHERE p.AnoProjeto+p.Sequencial = a.nrAnoProjeto + a.nrSequencial) AS idProjeto")
            )
        );
        $select->where('a.idTmpCaptacao = ?', $idTmpCaptacao);


        return $this->fetchRow($select);
    } // fecha m�todo buscarDados()

    public function buscarValorComprovadoDoItem($idPlanilhaAprovacao, $where = [])
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
                array( new Zend_Db_Expr("SUM(b.vlComprovacao) AS vlComprovado")),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
                array('b' => 'tbComprovantePagamento'),
            "a.idComprovantePagamento = b.idComprovantePagamento",
                array(),
            'BDCORPORATIVO.scSAC'
        );
        if (is_array($idPlanilhaAprovacao)) {
            $select->where(new Zend_Db_Expr('a.idPlanilhaAprovacao IN (?)'), $idPlanilhaAprovacao);
        } else {
            $select->where('a.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);
        }

        foreach ($where as $coluna => $valor) {
            if (!is_null($valor)) {
                $select->where($coluna, $valor);
            }
        }

        return $this->fetchRow($select);
    }

    public function buscarValorComprovadoPorFonteProdutoEtapaLocalItem($where)
    {
        try {

            if (empty($where)) {
               throw new Exception("Par&acirc;metros n&atilde;o podem ficar vazios");
            }

            $select = $this->select();
            $select->setIntegrityCheck(false);
            $select->from(
                ['a' => $this->_name],
                [ new Zend_Db_Expr("ISNULL(SUM(c.vlComprovacao), 0) AS vlComprovado")],
                'BDCORPORATIVO.scSAC'
            );
            $select->joinInner(
                ['b' => 'tbPlanilhaAprovacao'],
                "a.idPlanilhaAprovacao = b.idPlanilhaAprovacao",
                [],
                'SAC.dbo'
            );
            $select->joinInner(
                ['c' => 'tbComprovantePagamento'],
                'a.idComprovantePagamento = c.idComprovantePagamento',
                [],
                'BDCORPORATIVO.scSAC'
            );

            foreach ($where as $coluna => $valor) {
                if (!is_null($valor)) {
                    $select->where($coluna, $valor);
                }
            }
            return $this->fetchRow($select);

        } catch(Exception $e) {
            throw new $e;
        }

    }

    // TODO: usar dbo.fnVlComprovadoItem para valor comprovado
} // fecha class
