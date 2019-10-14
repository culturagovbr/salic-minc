<?php

class PlanilhaAprovacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbPlanilhaAprovacao';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    const TIPO_PLANILHA_APROVADA = 3;
    const TIPO_PLANILHA_REMANEJADA = 5;
    const TIPO_PLANILHA_COMPLEMENTACAO_REDUCAO = 6;

    public function somarPlanilhaAprovacao($idpronac, $elaboracao = null, $tpPlanilha = null, $where = array())
    {
        $somar = $this->select();
        $somar->from(
            array('PAP' => $this->_name),
            array(
                'sum(PAP.qtItem*PAP.nrOcorrencia*PAP.vlUnitario) as soma'
            )
        )
            ->joinLeft(
                array('aa' => 'tbAnaliseAprovacao'),
                "aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto and aa.tpAnalise = '{$tpPlanilha}'",
                array()
            )
            ->where('PAP.IdPRONAC = ?', $idpronac)
            ->where('PAP.NrFonteRecurso = ?', '109');
        $somar->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.stAvaliacao ELSE 1 END  = ?', 1);
        if ($elaboracao) {
            $somar->where('PAP.idPlanilhaItem <> ? ', $elaboracao);
        }
        if ($tpPlanilha) {
            $somar->where('PAP.tpPlanilha = ? ', $tpPlanilha);
        }
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $somar->where($coluna, $valor);
        }

        //$somar->where('aa.tpAnalise = ?', $tpPlanilha); //(condigo antigo) retirado pois nao estava atualizando os custos adminitrativos
        return $this->fetchRow($somar);
    }

    public function somarItensPlanilhaAprovacao($where = array())
    {
        $somar = $this->select();
        $somar->from($this->_name, array('sum(qtItem*nrOcorrencia*vlUnitario) as soma'));
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $somar->where($coluna, $valor);
        }

        return $this->fetchRow($somar);
    }

    public function somarItensPlanilhaAprovacaoProdutosFavoraveis($where = array())
    {
        $somar = $this->select();
        $somar->from(array('pa' => $this->_name), array('sum(qtItem*nrOcorrencia*vlUnitario) as soma'));
        $somar->joinLeft(
            array("aa" => "tbAnaliseAprovacao"),
            "aa.idPronac = pa.idPronac and pa.idProduto = aa.idProduto",
            array()
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $somar->where($coluna, $valor);
        }

        return $this->fetchRow($somar);
    }

    public function somarPlanilhaPropostaDivulgacao($idpronac, $fonte = null, $outras = null)
    {
        $somar = $this->select();
        $somar->from(
            $this,
            array(
                'sum(qtItem*nrOcorrencia*vlUnitario) as soma'
            )
        )
            ->where('IdPRONAC = ?', $idpronac)
            ->where('idEtapa = ?', 3);
        if ($fonte) {
            $somar->where('FonteRecurso = ?', $fonte);
        }
        if ($outras) {
            $somar->where('FonteRecurso <> ?', $outras);
        }

        return $this->fetchRow($somar);
    }

    public function InserirPlanilhaAprovacao($data)
    {
        try {
            $inserir = $this->insert($data);
            return $inserir;
        } catch (Zend_Db_Table_Exception $e) {
            die('PlanilhaAprovacao -> InserirPlanilhaAprovacao. Erro:' . $e->getMessage());
        }
    }

    public function CompararPlanilha($idpronac, $tpplanilha)
    {
        $buscar = $this->select();
        $buscar->setIntegrityCheck(false);
        $buscar->from(
            array('tpa' => $this->_name),
            array('(tpa.qtItem*tpa.nrOcorrencia*tpa.vlUnitario) as planilhaaprovacao')
        );
        $buscar->joinInner(
            array('tpp' => 'tbPlanilhaProposta'),
            'tpp.idPlanilhaProposta = tpa.idPlanilhaProposta',
            array('(tpp.Quantidade * tpp.Ocorrencia * tpp.ValorUnitario) as planilhaprojeto')
        );
        $buscar->where('tpa.idpronac = ?', $idpronac);
        $buscar->where('tpa.tpPlanilha = ?', $tpplanilha);

        return $this->fetchAll($buscar);
    }

    public function valoresAgrupados($idpronac, $retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pa' => $this->_name),
            array(
                'Total' => new Zend_Db_Expr('(pa.vlUnitario*pa.qtItem*pa.nrOcorrencia)'),
                'qtTotal' => new Zend_Db_Expr('pa.qtItem'),
                'pa.idPlanilhaItem',
                'pa.idEtapa',
                'pa.idProduto',
                'pa.idUnidade',
                'idPlanilhaAprovacao'
            )
        );
        $select->where('pa.IdPRONAC = ?', $idpronac);
        $select->where('pa.stAtivo = ?', 'S');

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    public function agruparPlanilhaAprovacao($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pa' => $this->_name),
            array(
                'idPlanilhaAprovacaoFilho' => 'pa.idPlanilhaAprovacao'
            )
        );
        $planilhaPai = $this->valoresAgrupados($idpronac, true);
        $select->joinInner(
            array('pa2' => $planilhaPai),
            'pa2.idPlanilhaItem = pa.idPlanilhaItem and pa2.idEtapa = pa.idEtapa and pa2.idProduto = pa.idProduto and pa2.idUnidade = pa.idUnidade',
            array('pa2.idPlanilhaAprovacao')
        );

        $select->where('pa.IdPRONAC = ?', $idpronac);
        $select->where('pa.stAtivo = ?', 'S');
        $select->where('pa.idPlanilhaAprovacao not in (?)', new Zend_Db_Expr('select idPlanilhaAprovacaoFilho  from  SAC.dbo.tbDeParaPlanilhaAprovacao'));

        return $this->fetchAll($select);
    }

    /**
     * @param $idpronac
     * @param null $itemAvaliadoFilter
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarItensPagamento($idpronac, $itemAvaliadoFilter = null)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array(
                'pAprovacao.idPlanilhaAprovacao', 'vlUnitario', 'qtItem', 'nrOcorrencia',
                new Zend_Db_Expr('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) as Total'),
                new Zend_Db_Expr(
                    "(SELECT sum(b1.vlComprovacao) AS vlPagamento
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                    WHERE c1.idPlanilhaItem = pAprovacao.idPlanilhaItem
                        AND c1.nrFonteRecurso = pAprovacao.nrFonteRecurso
                        AND c1.idProduto = pAprovacao.idProduto
                        AND c1.idEtapa = pAprovacao.idEtapa
                        AND c1.idUFDespesa = pAprovacao.idUFDespesa
                        AND c1.idMunicipioDespesa = pAprovacao.idMunicipioDespesa
                        AND c1.idPronac = pAprovacao.idPronac
                    GROUP BY c1.idPlanilhaItem) as vlComprovado"
                ),
                new Zend_Db_Expr(
                    "(SELECT sum(b2.vlComprovacao) AS vlPagamento
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a2
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b2 ON (a2.idComprovantePagamento = b2.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c2 ON (a2.idPlanilhaAprovacao = c2.idPlanilhaAprovacao)
                    WHERE a2.stItemAvaliado = 1 AND c2.stAtivo = 'S' AND c2.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao AND (c2.idPronac = pAprovacao.idPronac)
                    GROUP BY a2.idPlanilhaAprovacao) as ComprovacaoValidada"
                )
            )
        );
        $select->joinInner(
            array('pEtapa' => 'tbPlanilhaEtapa'),
            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
            array('pEtapa.idPlanilhaEtapa', 'pEtapa.tpCusto', 'pEtapa.Descricao as descEtapa'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pItens' => 'tbPlanilhaItens'),
            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
            array('pItens.idPlanilhaItens', 'pItens.Descricao as descItem'),
            'SAC.dbo'
        );
        $select->joinLeft(array('prod' => 'Produto'), 'pAprovacao.idProduto = prod.Codigo', array('prod.Codigo', 'prod.Descricao'), 'SAC.dbo');
        $select->joinInner(array('UFT' => 'UF'), 'pAprovacao.idUFDespesa = UFT.idUF', array('uf' => 'UFT.Sigla'), 'AGENTES.dbo');
        $select->joinInner(
            array('CID' => 'Municipios'),
            'pAprovacao.idMunicipioDespesa = CID.idMunicipioIBGE',
            array('cidade' => 'CID.Descricao', 'idMunicipio' => 'CID.idMunicipioIBGE'),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = cppa.idPlanilhaAprovacao',
            array('stItemAvaliado'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cPagamento' => 'tbComprovantePagamento'),
            'cppa.idComprovantePagamento = cPagamento.idComprovantePagamento',
            array('cPagamento.tpDocumento'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('cpa.idCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxa' => 'tbCotacaoxAgentes'),
            'cpa.idCotacaoxAgentes = cxa.idCotacaoxAgentes ',
            array('cxa.idAgente as idFornecedorCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('dlpa.idDispensaLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('lpa.idLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ctpa' => 'tbContratoxPlanilhaAprovacao'),
            'ctpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('ctpa.idContrato'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('pAprovacao.nrFonteRecurso = ?', 109); //Incentivo Fiscal Federal
        $select->where('pAprovacao.tpAcao IS NULL OR pAprovacao.tpAcao <> ? ', 'E'); //Adicionado para n�o listar as que ja foram excluidas
        $select->where('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) > 0');

        $select->order('prod.Descricao');
        $select->order('pEtapa.idPlanilhaEtapa');
        $select->order('pItens.Descricao');
        $select->order('UFT.Sigla');
        $select->order('CID.Descricao');
        $select->order('pAprovacao.vlUnitario');
        $select->order('pAprovacao.qtItem');
        $select->order('pEtapa.tpCusto');

        /* echo $select;die; */
        if ($itemAvaliadoFilter == 1) {
            $select->where('cppa.stItemAvaliado = ?', 4);
        } elseif ($itemAvaliadoFilter == 2) {
            $select->where('cppa.stItemAvaliado != ?', 4);
        } elseif ($itemAvaliadoFilter == 3) {
            $select->where('cppa.stItemAvaliado = ?', 3);
        }
        return $this->fetchAll($select);
    }

    public function buscarVinculoContrato($idPlanilhaAprovacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array('pAprovacao.idPlanilhaAprovacao')
        );
        $select->joinInner(
            array('cpa' => 'tbContratoxPlanilhaAprovacao'),
            'cpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('cpa.idContrato'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);

        $select->group('pAprovacao.idPlanilhaAprovacao');
        $select->group('cpa.idContrato');

        return $this->fetchAll($select);
    }

    public function buscarVinculo($idPlanilhaAprovacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array('pAprovacao.idPlanilhaAprovacao')
        );
        $select->joinLeft(
            array('cpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('cpa.idCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('dlpa.idDispensaLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('lpa.idLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->Where('(cpa.idCotacao is not null');
        $select->orWhere('dlpa.idDispensaLicitacao is not null');
        $select->orWhere('lpa.idLicitacao is not null)');

        $select->group('pAprovacao.idPlanilhaAprovacao');
        $select->group('cpa.idCotacao');
        $select->group('dlpa.idDispensaLicitacao');
        $select->group('lpa.idLicitacao');

        return $this->fetchAll($select);
    }

    public function descricaoitem($idpronac, $idProduto, $idEtapa, $idPlanilhaItem)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array('pAprovacao.idPlanilhaAprovacao', 'pAprovacao.qtItem', 'pAprovacao.nrOcorrencia', 'pAprovacao.vlUnitario')
        );

        $valores = $this->valoresAgrupados($idpronac, true);
        $select->joinInner(
            array('pa' => $valores),
            'pAprovacao.idPlanilhaItem = pa.idPlanilhaItem and pAprovacao.idEtapa = pa.idEtapa and pAprovacao.idProduto = pa.idProduto and pAprovacao.idUnidade = pa.idUnidade and pa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao ',
            array('pa.Total', 'pa.qtTotal')
        );

        $select->joinLeft(
            array('ic' => 'tbItemCusto'),
            'ic.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('ic.idItemCusto', 'ic.dsFabricante', 'ic.dsItemDeCusto', 'ic.dsMarca', 'ic.dsObservacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where("pAprovacao.stAtivo = 'S'");
        $select->where('pAprovacao.idProduto = ?', $idProduto);
        $select->where('pAprovacao.idEtapa = ?', $idEtapa);

        /* erro 26
        $select->where('pAprovacao.idPlanilhaItem = ?', $idPlanilhaItem);
        */
        $select->where('pAprovacao.idPlanilhaAprovacao = ?', $idPlanilhaItem);

        /* erro 26
        $select->group('pAprovacao.idPlanilhaAprovacao');
        $select->group('pAprovacao.vlUnitario');
        $select->group('pAprovacao.qtItem');
        $select->group('pAprovacao.nrOcorrencia');
        $select->group('ic.idItemCusto');
        $select->group('ic.dsFabricante');
        $select->group('ic.dsItemDeCusto');
        $select->group('ic.dsMarca');
        $select->group('ic.dsObservacao');
         *
         */

        return $this->fetchAll($select);
    }


    public function buscarFornecedor($idpronac, $ckItens)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array()
        );
        $select->joinLeft(
            array('cpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxa' => 'tbCotacaoxAgentes'),
            'cpa.idCotacaoxAgentes = cxa.idCotacaoxAgentes ',
            array('cxa.idAgente as idFornecedorCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dl' => 'tbDispensaLicitacao'),
            'dlpa.idDispensaLicitacao = dl.idDispensaLicitacao',
            array('dl.idAgente as idFornecedorDL'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lxa' => 'tbLicitacaoxAgentes'),
            "lpa.idLicitacao = lxa.idLicitacao and lxa.stVencedor = 'true'",
            array('lxa.idAgente as idFornecedorL'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ctpa' => 'tbContratoxPlanilhaAprovacao'),
            'ctpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ctxa' => 'tbContratoxAgentes'),
            'ctpa.idContrato = ctxa.idContrato',
            array('ctxa.idAgente as idFornecedorContrato'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('pAprovacao.idPlanilhaItem in (' . implode(',', $ckItens) . ')');
        $select->where('(cpa.idCotacao is not null');
        $select->orWhere('dlpa.idDispensaLicitacao is not null');
        $select->orWhere('lpa.idLicitacao is not null');
        $select->orWhere('ctpa.idContrato is not null)');

        $select->group('cxa.idAgente');
        $select->group('dl.idAgente');
        $select->group('lxa.idAgente');
        $select->group('ctxa.idAgente');

        return $this->fetchAll($select);
    }

    public function buscaComprovantePagamento($idpronac, $ckItens)
    {
        $select = $this->select()->setIntegrityCheck(false)
            ->from(array('pAprovacao' => $this->_name), array())
            ->joinLeft(
                array('xp' => 'Produto'),
                'pAprovacao.idProduto = xp.Codigo',
                array("(CASE WHEN xp.Descricao IS NULL THEN 'Administra&ccedil;&atilde;o do Projeto' ELSE xp.Descricao END) as Produto"),
                'SAC.dbo'
            )->joinInner(
                array('xe' => 'tbPlanilhaEtapa'),
                'pAprovacao.idEtapa = xe.idPlanilhaEtapa',
                array('xe.Descricao as Etapa'),
                'SAC.dbo'
            )->joinInner(
                array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
                'cppa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
                array('cppa.idComprovantePagamento', 'cppa.idPlanilhaAprovacao', 'cppa.stItemAvaliado', 'cppa.dsJustificativa', 'CONVERT(CHAR(23), cppa.dtValidacao, 120) AS dtValidacao', 'cppa.vlComprovado'),
                'BDCORPORATIVO.scSAC'
            )->joinInner(
                array('cp' => 'tbComprovantePagamento'),
                'cp.idComprovantePagamento = cppa.idComprovantePagamento',
                array('cp.tpDocumento', 'cp.nrSerie', 'cp.dtEmissao', 'cp.nrComprovante', 'cp.idArquivo'),
                'BDCORPORATIVO.scSAC'
            )->joinInner(array('ag' => 'agentes'), 'ag.idAgente = cp.idFornecedor', array('ag.CNPJCPF'), 'AGENTES.dbo')
            ->joinInner(array('nm' => 'Nomes'), 'nm.idAgente = cp.idFornecedor', array('nm.Descricao'), 'AGENTES.dbo')
            ->joinInner(array('arq' => 'tbArquivo'), 'arq.idArquivo = cp.idArquivo', array('arq.nmArquivo'), 'BDCORPORATIVO.scCorp')
            ->joinInner(
                array('tpi' => 'tbPlanilhaItens'),
                'pAprovacao.idPlanilhaItem = tpi.idPlanilhaItens',
                array('tpi.Descricao AS NomeItem', 'tpi.idPlanilhaItens', 'tpi.idPlanilhaItens'),
                'SAC.dbo'
            )->where('pAprovacao.IdPRONAC = ?', $idpronac)
            ->where('pAprovacao.stAtivo = ?', 'S')
            ->where('pAprovacao.idPlanilhaAprovacao in (?)', $ckItens)
            ->order('xp.Descricao')->order('xe.Descricao')->order('tpi.Descricao')->order('cp.dtEmissao');
        return $this->fetchAll($select);
    }

    public function buscarcomprovantepagamento($idpronac, $idPlanilhaItem)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('pAprovacao' => $this->_name), array());
        $select->joinInner(
            array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'cppa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(
                'cppa.idComprovantePagamento',
                'cppa.stItemAvaliado',
                'CAST(cppa.dsJustificativa AS TEXT) as ocorrencia',
                'dtValidacao' => 'CONVERT(CHAR(23), cppa.dtValidacao, 120)',
                'vlComprovadoPlanilhaAprovacao' => 'vlComprovado',
                'idPlanilhaAprovacao',
            ),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('cp' => 'tbComprovantePagamento'),
            'cp.idComprovantePagamento = cppa.idComprovantePagamento',
            array(
                'cp.tpDocumento',
                'cp.nrSerie',
                'CONVERT(CHAR(23), cp.dtEmissao, 120) as dtEmissao',
                'cp.nrComprovante',
                'cp.idArquivo',
                'cp.tpFormaDePagamento',
                'cp.nrDocumentoDePagamento',
                'CAST(cp.dsJustificativa AS TEXT) as dsJustificativa',
                'cp.vlComprovacao',
            ),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('ag' => 'agentes'),
            'ag.idAgente = cp.idFornecedor',
            array('ag.CNPJCPF'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('nm' => 'Nomes'),
            'nm.idAgente = cp.idFornecedor',
            array('nm.Descricao'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('arq' => 'tbArquivo'),
            'arq.idArquivo = cp.idArquivo',
            array('arq.nmArquivo'),
            'BDCORPORATIVO.scCorp'
        );
        $select->joinInner(
            array('pi' => 'tbPlanilhaItens'),
            'pAprovacao.idPlanilhaItem = pi.idPlanilhaItens',
            array('pi.idPlanilhaItens',
                'pi.Descricao as NomeItem'),
            'SAC.dbo'
        );
        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('pAprovacao.idPlanilhaAprovacao = ?', $idPlanilhaItem);
        /* $select->where('pAprovacao.idPlanilhaItem = ?',$idPlanilhaItem); */
        /* $select->where(' cppa.stItemAvaliado is not null '); */

        return $this->fetchAll($select);
    }

    public function verificarComprovacao($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array(
                'valor' => new Zend_Db_Expr('sum(pAprovacao.vlUnitario*pAprovacao.qtItem*pAprovacao.nrOcorrencia)'), 'pAprovacao.idPlanilhaItem', 'pAprovacao.idEtapa', 'pAprovacao.idProduto'
            )
        );


        $select->joinInner(
            array('pEtapa' => 'tbPlanilhaEtapa'),
            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pItens' => 'tbPlanilhaItens'),
            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
            array(),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('prod' => 'Produto'),
            'pAprovacao.idProduto = prod.Codigo',
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('UFT' => 'UF'),
            'pAprovacao.idUFDespesa = UFT.idUF',
            array(),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('CID' => 'Municipios'),
            'pAprovacao.idMunicipioDespesa = CID.idMunicipioIBGE',
            array(),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = cppa.idPlanilhaAprovacao',
            array('sum(cppa.vlComprovado) as vlComprovado'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cPagamento' => 'tbComprovantePagamento'),
            'cppa.idComprovantePagamento = cPagamento.idComprovantePagamento',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxa' => 'tbCotacaoxAgentes'),
            'cpa.idCotacaoxAgentes = cxa.idCotacaoxAgentes ',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ctpa' => 'tbContratoxPlanilhaAprovacao'),
            'ctpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
//        $select->where("pEtapa.tpCusto = 'P'");
//        $select->where('cppa.vlComprovado is not null');
        $select->where('pAprovacao.stAtivo = ?', 'S');

//        $select->group('pAprovacao.vlUnitario');
//        $select->group('pAprovacao.qtItem');
//        $select->group('pAprovacao.nrOcorrencia');
        $select->group('pAprovacao.idPlanilhaItem');
        $select->group('pAprovacao.idEtapa');
        $select->group('pAprovacao.idProduto');


        $selectFinal = $this->select();
        $selectFinal->setIntegrityCheck(false);
        $selectFinal->from($select, array("vlComprovado", "valor"));
        $selectFinal->where('vlComprovado>valor');

        return $this->fetchAll($selectFinal);
    }


    public function carregarProdutos($idpronac, $idCotacao, $idDispensaLicitacao, $idLicitacao, $idContrato)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array()
        );

        $select->joinLeft(
            array('prod' => 'Produto'),
            'pAprovacao.idProduto = prod.Codigo',
            array(
                'id' => 'prod.Codigo', 'nome' => 'prod.Descricao'
            ),
            'SAC.dbo'
        );

        if ($idCotacao) {
            $select->joinInner(
                array('ctxpa' => 'tbCotacaoxPlanilhaAprovacao'),
                "pAprovacao.idPlanilhaAprovacao = ctxpa.idPlanilhaAprovacao and ctxpa.idCotacao = '$idCotacao' ",
                array(),
                'BDCORPORATIVO.scSAC'
            );
        }
        if ($idDispensaLicitacao) {
            $select->joinInner(
                array('dlxpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
                "pAprovacao.idPlanilhaAprovacao = dlxpa.idPlanilhaAprovacao and dlxpa.idDispensaLicitacao  = '$idDispensaLicitacao' ",
                array(),
                'BDCORPORATIVO.scSAC'
            );
        }
        if ($idLicitacao) {
            $select->joinInner(
                array('lxpa' => 'tbLicitacaoxPlanilhaAprovacao'),
                "pAprovacao.idPlanilhaAprovacao = lxpa.idPlanilhaAprovacao and lxpa.idLicitacao  = '$idLicitacao' ",
                array(),
                'BDCORPORATIVO.scSAC'
            );
        }
        if ($idContrato) {
            $select->joinInner(
                array('cnxpa' => 'tbContratoxPlanilhaAprovacao'),
                "pAprovacao.idPlanilhaAprovacao = cnxpa.idPlanilhaAprovacao and cnxpa.idContrato = '$idContrato' ",
                array(),
                'BDCORPORATIVO.scSAC'
            );
        }


        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);

        $select->order('prod.Descricao');

        $select->group('prod.Codigo');
        $select->group('prod.Descricao');

        return $this->fetchAll($select);
    }

    public function dadosdoitem($idPlanilhaAprovacao, $idpronac)
    {
        $cpxpaDAO = new ComprovantePagamentoxPlanilhaAprovacao();
        $selectAux = $cpxpaDAO->valorComprovadoItem(true);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $select = $db->select();

        /* $select = $this->select(); */
        /* $select->setIntegrityCheck(false); */
        $select->from(
            array('pa' => $this->_name),
            array('pa.idPlanilhaAprovacao', 'vlItem' => 'pa.vlUnitario', 'vlAprovado' => new Zend_Db_Expr('pa.vlUnitario*pa.qtItem*pa.nrOcorrencia')),
            'SAC.dbo'
        );

        $select->join(
            array('paux' => new Zend_Db_Expr('(' . $this->valoresAgrupados($idpronac, true) . ')')),
            'pa.idPlanilhaItem = paux.idPlanilhaItem AND
            pa.idEtapa = paux.idEtapa AND
            pa.idProduto = paux.idProduto AND
            pa.idUnidade = paux.idUnidade AND
            paux.idPlanilhaAprovacao = pa.idPlanilhaAprovacao ',
            array('paux.Total', 'paux.qtTotal')
        );

        $select->joinInner(
            array('pli' => 'tbPlanilhaItens'),
            'pli.idPlanilhaItens = pa.idPlanilhaItem',
            array(
                'idPlanilhaItens' => 'pli.idPlanilhaItens',
                'NomeItem' => 'pli.Descricao'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('eta' => 'tbPlanilhaEtapa'),
            'eta.idPlanilhaEtapa = pa.idEtapa',
            array('Etapa' => 'eta.Descricao'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('pro' => 'Produto'),
            'pro.Codigo = pa.idProduto',
            array(
                'Produto' => new Zend_Db_Expr("CASE WHEN pro.Descricao IS NULL
                    THEN 'Administra&ccedil;&atilde;o do Projeto'
                    ELSE pro.Descricao END")
            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('cpxpa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'cpxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(
                'cpxpa.stItemAvaliado',
                'cpxpa.dsJustificativa',
                'CONVERT(CHAR(23), cpxpa.dtValidacao, 120) AS dtValidacao'
            ),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cp' => 'tbComprovantePagamento'),
            'cp.idComprovantePagamento = cpxpa.idComprovantePagamento',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('di' => $selectAux),
            'di.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array('di.vlComprovado')
        );

        $select->joinLeft(
            array('lxpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );

        $select->joinLeft(
            array('lic' => 'tbLicitacao'),
            'lxpa.idLicitacao = lic.idLicitacao',
            array(
                'lic.nrLicitacao',
                'modalidadeLicitacao' => 'lic.tpModalidade',
                'processoLicitacao' => 'lic.nrProcesso',
                'CONVERT(CHAR(23), lic.dtPublicacaoEdital, 120) AS dtPubliEditalLicitacao',
                'objetoLicitacao' => 'lic.dsObjeto',
                'fundamentoLicitacao' => 'lic.dsFundamentoLegal'
            ),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lxa' => 'tbLicitacaoxAgentes'),
            'lxa.idLicitacao = lic.idLicitacao and lxa.stVencedor = 1',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlxpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlic' => 'tbDispensaLicitacao'),
            'dlxpa.idDispensaLicitacao = dlic.idDispensaLicitacao',
            array('dsDispensa' => 'dlic.dsDispensaLicitacao', 'dtDispensa' => 'dlic.dtContrato'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cota' => 'tbCotacao'),
            'cxpa.idCotacao = cota.idCotacao',
            array('cota.dsCotacao', 'cota.dtCotacao', 'cota.nrCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxa' => 'tbCotacaoxAgentes'),
            'cxa.idCotacao = cota.idCotacao and cxa.idCotacaoxAgentes = cxpa.idCotacaoxAgentes',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ag' => 'Agentes'),
            'ag.idAgente = lxa.idAgente OR ag.idAgente = dlic.idAgente OR ag.idAgente = cxa.idAgente OR cp.idFornecedor = ag.idAgente',
            array('cpfcnpjFornecedor' => 'ag.CNPJCPF'),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('nm' => 'Nomes'),
            'nm.idAgente = ag.idAgente',
            array('nmFornecedor' => 'nm.Descricao'),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('p' => 'Projetos'),
            'pa.IdPRONAC = p.IdPRONAC',
            array(
                'p.IdPRONAC',
                '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                'p.NomeProjeto'
            ),
            'SAC.dbo'
        );

        $select->where('pa.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);
        /* echo $select;die; */
        return $db->fetchAll($select);
    }

    public function buscarProdutosComprovacao($idpronac, $ckItens)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('pAprovacao' => $this->_name), array('id' => 'pAprovacao.idProduto'));
        $select->joinLeft(
            array('prod' => 'Produto'),
            'pAprovacao.idProduto = prod.Codigo',
            array('nome' => new Zend_Db_Expr("CASE WHEN prod.Descricao IS NULL THEN 'Administra&ccedil;&atilde;o do Projeto' ELSE prod.Descricao END")),
            'SAC.dbo'
        );
        $select->order('prod.Descricao');
        $select->group('pAprovacao.idProduto');
        $select->group('prod.Descricao');

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');

        if (is_array($ckItens) and count(is_array($ckItens)) > 0) {
            $select->where('pAprovacao.idPlanilhaAprovacao in (' . implode(',', $ckItens) . ')');
        }

        return $this->fetchAll($select);
    }

    public function buscarProdutosContrato($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array(
                'id' => 'pAprovacao.idProduto'
            )
        );

        $select->joinLeft(
            array('prod' => 'Produto'),
            'pAprovacao.idProduto = prod.Codigo',
            array('nome' => new Zend_Db_Expr("CASE
                                                        WHEN prod.Descricao IS NULL
                                                               THEN 'Administra&ccedil;&atilde;o do Projeto'
                                                        ELSE prod.Descricao
                                                  END")),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('cxpa' => 'tbContratoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = cxpa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('cxpa.idContrato is null');

        $select->order('prod.Descricao');

        $select->group('pAprovacao.idProduto');
        $select->group('prod.Descricao');

        return $this->fetchAll($select);
    }

    public function buscarProdutos($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('pAprovacao' => $this->_name),
            array('id' => 'pAprovacao.idProduto')
        );

        $select->joinLeft(
            array('prod' => 'Produto'),
            'pAprovacao.idProduto = prod.Codigo',
            array('nome' => new Zend_Db_Expr("CASE
                                                        WHEN prod.Descricao IS NULL
                                                               THEN 'Administra&ccedil;&atilde;o do Projeto'
                                                        ELSE prod.Descricao
                                                  END")),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('cxpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = cxpa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlxpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = dlxpa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lxpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = lxpa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        //$select->where('cxpa.idCotacao is null');
        //$select->where('dlxpa.idDispensaLicitacao is null');
        //$select->where('lxpa.idLicitacao is null');

        // $select->order('prod.Descricao');
        $select->order('2');

        $select->group('pAprovacao.idProduto');
        $select->group('prod.Descricao');

        return $this->fetchAll($select);
    }

    /*===========================================================================*/
    /*====================== ABAIXO - METODOS DA CNIC ===========================*/
    /*===========================================================================*/

    public function buscarAnaliseConta($idpronac, $tpplanilha, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pap' => $this->_name),
            array(
                'pap.qtItem as qtdRelator',
                'pap.nrFonteRecurso',
                'pap.qtDias as diasRelator',
                'pap.nrOcorrencia as ocorrenciaRelator',
                'pap.vlUnitario as vlunitarioRelator',
                'pap.idPlanilhaAprovacao',
                'pap.idProduto',
                'pap.idUnidade',
                'pap.idEtapa',
                'pap.dsJustificativa as JSComponente',
            )
        );
        $select->joinInner(
            array('pp' => 'tbPlanilhaProposta'),
            'pp.idPlanilhaProposta = pap.idPlanilhaProposta',
            array(
                'pp.Quantidade as qtdSolicitado',
                'pp.Ocorrencia as ocoSolicitado',
                'pp.ValorUnitario as vlSolicitado',
                'pp.QtdeDias as diasSolicitado'
            )
        );
        $select->joinInner(
            array('ppj' => 'tbPlanilhaProjeto'),
            'ppj.idPlanilhaProjeto = pap.idPlanilhaProjeto',
            array(
                'ppj.Quantidade as qtdParecer',
                'ppj.Ocorrencia as ocoParecer',
                'ppj.ValorUnitario as vlParecer',
                'ppj.Justificativa as JSParecerista',
                'ppj.QtdeDias as diasParecerista'
            )
        );
        $select->joinLeft(
            array('aa' => 'tbAnaliseAprovacao'),
            "aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto and aa.tpAnalise = '{$tpplanilha}'",
            array()
        );

        $select->joinInner(
            array('UNI' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PAP.idUnidade = UNI.idUnidade'),
            array(
                'UNI.Descricao AS Unidade'
            )
        );
        $select->joinInner(
            array('UNIPP' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PP.Unidade = UNIPP.idUnidade'),
            array(
                'UNIPP.Descricao AS UnidadeProposta'
            )
        );
        $select->joinInner(
            array('UNIPJ' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PPJ.idUnidade = UNIPJ.idUnidade'),
            array(
                'UNIPJ.Descricao AS UnidadeProjeto'
            )
        );

        $select->joinInner(
            array('i' => 'tbPlanilhaItens'),
            'pap.idPlanilhaItem  = i.idPlanilhaItens',
            array('i.Descricao as Item')
        );
        $select->joinInner(
            array('e' => 'tbPlanilhaEtapa'),
            'pap.idEtapa  = e.idPlanilhaEtapa',
            array('e.Descricao as Etapa')
        );
        $select->joinLeft(
            array('prod' => 'produto'),
            'pap.idProduto = prod.Codigo',
            array('prod.Descricao as produto')
        );
        $select->where('pap.nrFonteRecurso = ?', 109);
        $select->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.stAvaliacao ELSE 1 END  = ?', 1);
        $select->where('pap.idPronac = ?', $idpronac);
        $select->where('pap.idPlanilhaItem not in (?)', array(206, 1238));
        $select->where('pap.tpPlanilha = ?', $tpplanilha);
        //adiciona outras condicoes enviadas
        foreach ($where as $chave => $valor) {
            $select->where($chave, $valor);
        }
        return $this->fetchAll($select);
    }

    public function buscarAnaliseCustos($idpronac = null, $tpPlanilha = null, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
//        $select->distinct();
        $select->from(
            array('PAP' => $this->_name),
            array(
                'PAP.idProduto',
                'PAP.idUnidade',
                'PAP.nrFonteRecurso',
                'PAP.idPlanilhaAprovacao',
                'PAP.IdPRONAC',
                'PAP.idEtapa',
                'PAP.qtItem as qtitemcomp',
                'PAP.qtDias as qtdiascomp',
                'PAP.nrOcorrencia as nrocorrenciacomp',
                'PAP.vlUnitario as vlunitariocomp',
                'PAP.idPlanilhaAprovacao',
                '(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro',
                //'CAST(PAP.dsJustificativa as TEXT) as dsJustificativaConselheiro',
                //'CONVERT(varchar(8000), PAP.dsJustificativa) as dsJustificativaConselheiro',
                'CAST(PAP.dsJustificativa AS TEXT) AS dsJustificativaConselheiro',
                'PAP.idPlanilhaItem',
            )
        );
        $select->joinInner(
            array('PP' => 'tbPlanilhaProposta'),
            new Zend_Db_Expr('PAP.idPlanilhaProposta = PP.idPlanilhaProposta'),
            array(
                '(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS VlSolicitado',
                //'CAST(PP.dsJustificativa as TEXT) as justificitivaproponente',
                'CONVERT(varchar(8000), PP.dsJustificativa) as justificitivaproponente',
                'PP.Quantidade AS quantidadeprop',
                'PP.Ocorrencia AS ocorrenciaprop',
                'PP.ValorUnitario AS valorUnitarioprop',
                'PP.QtdeDias AS diasprop'
            )
        );
        $select->joinInner(
            array('PPJ' => 'tbPlanilhaProjeto'),
            new Zend_Db_Expr('PPJ.idPlanilhaProjeto= PAP.idPlanilhaProjeto'),
            array(
                '(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) AS VlSugeridoParecerista',
                //'CAST(PPJ.Justificativa as TEXT) as dsJustificativaParecerista',
                'CONVERT(varchar(8000), PPJ.Justificativa) as dsJustificativaParecerista',
                'PPJ.Quantidade AS quantidadeparc',
                'PPJ.Ocorrencia AS ocorrenciaparc',
                'PPJ.ValorUnitario AS valorUnitarioparc',
                'PPJ.QtdeDias AS diasparc'
            )
        );
        $select->joinInner(
            array('I' => 'tbPlanilhaItens'),
            new Zend_Db_Expr('PAP.idPlanilhaItem = I.idPlanilhaItens'),
            array(
                'I.Descricao AS Item'
            )
        );
        $select->joinInner(
            array('E' => 'tbPlanilhaEtapa'),
            new Zend_Db_Expr('PAP.idEtapa = E.idPlanilhaEtapa'),
            array(
                'E.Descricao AS Etapa'
            )
        );
        $select->joinInner(
            array('UNI' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PAP.idUnidade = UNI.idUnidade'),
            array(
                'UNI.Descricao AS Unidade'
            )
        );
        $select->joinInner(
            array('UNIPP' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PP.Unidade = UNIPP.idUnidade'),
            array(
                'UNIPP.Descricao AS UnidadeProposta'
            )
        );
        $select->joinInner(
            array('UNIPJ' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PPJ.idUnidade = UNIPJ.idUnidade'),
            array(
                'UNIPJ.Descricao AS UnidadeProjeto'
            )
        );
        $select->joinInner(
            array('TI' => 'Verificacao'),
            new Zend_Db_Expr('TI.idverificacao = PAP.nrFonteRecurso'),
            array(
                'TI.Descricao as FonteRecurso'
            )
        );
        $select->joinInner(
            array('CID' => 'Municipios'),
            new Zend_Db_Expr('CID.idMunicipioIBGE = PAP.idMunicipioDespesa'),
            array(
                'CID.Descricao as Cidade'
            ),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('FED' => 'UF'),
            new Zend_Db_Expr('PAP.idUFDespesa = FED.idUF'),
            array(
                'FED.Sigla as UF'
            ),
            'Agentes.dbo'
        );
        $select->joinLeft(
            array('aa' => 'tbAnaliseAprovacao'),
            "aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto and aa.tpAnalise = '{$tpPlanilha}'",
            array('aa.stAvaliacao')
        );
        $select->joinLeft(
            array('PD' => 'Produto'),
            new Zend_Db_Expr('PAP.idProduto = PD.Codigo'),
            array(
                'PD.Descricao as Produto'
            )
        );
        $select->where('PAP.idPlanilhaItem not in(?)', array(206, 1238));
        //$select->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.stAvaliacao ELSE 1 END  = ?', 1); //CODIGO QUE RETORNA ITENS APENAS DE PRODUTOS FAVORECIDOS
        if ($idpronac) {
            $select->where('PAP.IdPRONAC = ?', $idpronac);
        }
        if ($tpPlanilha) {
            $select->where('PAP.tpPlanilha = ?', $tpPlanilha);
        }
        $select->order(
            array(
                'PAP.NrFonteRecurso',
                'PD.Descricao',
                'PAP.idEtapa',
                'FED.Sigla',
                'CID.Descricao'
            )
        );
        //adiciona outras condicoes enviadas
        foreach ($where as $chave => $valor) {
            $select->where($chave, $valor);
        }

        return $this->fetchAll($select);
    }

    public function somaDadosPlanilha($dados = array())
    {
        $somar = $this->select();
        $somar->from(
            array('PAP' => $this->_name),
            array(
                'sum(PAP.qtItem*PAP.nrOcorrencia*PAP.vlUnitario) as soma'
            )
        )
            ->joinLeft(
                array('aa' => 'tbAnaliseAprovacao'),
                'aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto',
                array()
            )
            ->where('PAP.NrFonteRecurso = ?', '109');
        $somar->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.stAvaliacao ELSE 1 END  = ?', 1);
        foreach ($dados as $key => $valor) {
            $somar->where($key, $valor);
        }
        return $this->fetchRow($somar);
    }

    //BUSCA ANALISE DE CUSTO
    public function buscarAnaliseCustosPlanilhaAprovacao($idpronac = null, $tpPlanilha = null, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('PAP' => $this->_name),
            array(
                'PAP.tpPlanilha',
                'PAP.idProduto',
                'PAP.idUnidade',
                'PAP.nrFonteRecurso',
                'PAP.idPlanilhaAprovacao',
                'PAP.IdPRONAC',
                'PAP.idEtapa',
                'PAP.qtItem',
                'PAP.qtDias',
                'PAP.nrOcorrencia',
                'PAP.vlUnitario',
                'PAP.idPlanilhaAprovacao',
                '(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS vlTotal',
                'CONVERT(varchar(8000), PAP.dsJustificativa) as dsJustificativa',
                'PAP.idPlanilhaItem',
                'PAP.idPlanilhaAprovacaoPai',
            )
        );

        $select->joinInner(
            array('I' => 'tbPlanilhaItens'),
            new Zend_Db_Expr('PAP.idPlanilhaItem = I.idPlanilhaItens'),
            array(
                'I.Descricao AS Item'
            )
        );
        $select->joinInner(
            array('E' => 'tbPlanilhaEtapa'),
            new Zend_Db_Expr('PAP.idEtapa = E.idPlanilhaEtapa'),
            array(
                'E.Descricao AS Etapa'
            )
        );
        $select->joinInner(
            array('UNI' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PAP.idUnidade = UNI.idUnidade'),
            array(
                'UNI.Descricao AS Unidade'
            )
        );

        $select->joinInner(
            array('TI' => 'Verificacao'),
            new Zend_Db_Expr('TI.idverificacao = PAP.nrFonteRecurso'),
            array(
                'TI.Descricao as FonteRecurso'
            )
        );
        $select->joinInner(
            array('CID' => 'Municipios'),
            new Zend_Db_Expr('CID.idMunicipioIBGE = PAP.idMunicipioDespesa'),
            array(
                'CID.Descricao as Cidade'
            ),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('FED' => 'UF'),
            new Zend_Db_Expr('PAP.idUFDespesa = FED.idUF'),
            array(
                'FED.Sigla as UF'
            ),
            'Agentes.dbo'
        );
        $select->joinLeft(
            array('aa' => 'tbAnaliseAprovacao'),
            "aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto and aa.tpAnalise = '{$tpPlanilha}' AND aa.dtAnalise = (SELECT TOP 1 max(dtAnalise) from SAC..tbAnaliseAprovacao WHERE idPronac = PAP.idPronac and PAP.idProduto = idProduto and tpAnalise = '{$tpPlanilha}')",
            array('aa.stAvaliacao')
        );
        $select->joinLeft(
            array('PD' => 'Produto'),
            new Zend_Db_Expr('PAP.idProduto = PD.Codigo'),
            array(
                'PD.Descricao as Produto'
            )
        );
        $select->where('PAP.idPlanilhaItem not in(?)', array(206, 1238));
        //$select->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.stAvaliacao ELSE 1 END  = ?', 1);
        if ($idpronac) {
            $select->where('PAP.IdPRONAC = ?', $idpronac);
        }
        if ($tpPlanilha) {
            $select->where('PAP.tpPlanilha = ?', $tpPlanilha);
        }
        //adiciona outras condicoes enviadas
        foreach ($where as $chave => $valor) {
            $select->where($chave, $valor);
        }
        $select->order(
            array(
                'PAP.NrFonteRecurso',
                'PD.Descricao',
                'PAP.idEtapa',
                'FED.Sigla',
                'CID.Descricao'
            )
        );

        return $this->fetchAll($select);
    }


    /**
     * M�todo para buscar a planilha de custos no m�dulo de readequa��o
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function buscarCustosReadequacao($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('PAP' => $this->_name),
            array(
                'PAP.tpPlanilha'
            , 'PAP.idProduto'
            , 'PAP.idUnidade'
            , 'PAP.nrFonteRecurso'
            , 'PAP.idPlanilhaAprovacao'
            , 'PAP.IdPRONAC'
            , 'PAP.idEtapa'
            , 'PAP.qtItem'
            , 'PAP.qtDias'
            , 'PAP.nrOcorrencia'
            , 'PAP.vlUnitario'
            , 'PAP.idUFDespesa AS idUF'
            , 'PAP.idMunicipioDespesa AS idMunicipio'
            , '(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS vlTotal'
            , 'CONVERT(varchar(8000), PAP.dsJustificativa) as dsJustificativa'
            , 'PAP.idPlanilhaItem'
            , 'PAP.idPlanilhaProjeto'
            , 'PAP.idPlanilhaProposta'
            , 'PAP.dsItem'
            , 'PAP.tpDespesa'
            , 'PAP.tpPessoa'
            , 'PAP.nrContraPartida'
            , 'PAP.tpAcao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('I' => 'tbPlanilhaItens'),
            new Zend_Db_Expr('PAP.idPlanilhaItem = I.idPlanilhaItens'),
            array(
                'I.Descricao AS Item'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('E' => 'tbPlanilhaEtapa'),
            new Zend_Db_Expr('PAP.idEtapa = E.idPlanilhaEtapa'),
            array(
                'E.Descricao AS Etapa'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('UNI' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PAP.idUnidade = UNI.idUnidade'),
            array(
                'UNI.Descricao AS Unidade'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('TI' => 'Verificacao'),
            new Zend_Db_Expr('TI.idverificacao = PAP.nrFonteRecurso'),
            array(
                'TI.Descricao as FonteRecurso'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('CID' => 'Municipios'),
            new Zend_Db_Expr('CID.idMunicipioIBGE = PAP.idMunicipioDespesa'),
            array(
                'CID.Descricao as Cidade'
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('FED' => 'UF'),
            new Zend_Db_Expr('PAP.idUFDespesa = FED.idUF'),
            array(
                'FED.Sigla as UF'
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('PD' => 'Produto'),
            new Zend_Db_Expr('PAP.idProduto = PD.Codigo'),
            array(
                'PD.Descricao as Produto'
            ),
            'SAC.dbo'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        //x($select->assemble());
        return $this->fetchAll($select);
    } // fecha m�todo buscarCustosReadequacao()


    /**
     * Busca o hist�rico de readequa��o
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function historicoReadequacao($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('h' => $this->_name),
            array(
                'h.tpPlanilha'
            , 'h.idProduto'
            , 'h.idUnidade'
            , 'h.nrFonteRecurso'
            , 'h.idPlanilhaAprovacao'
            , 'h.IdPRONAC'
            , 'h.idEtapa'
            , 'h.qtItem'
            , 'h.qtDias'
            , 'h.nrOcorrencia'
            , 'h.vlUnitario'
            , 'h.idUFDespesa AS idUF'
            , 'h.idMunicipioDespesa AS idMunicipio'
            , '(h.qtItem * h.nrOcorrencia * h.vlUnitario) AS vlTotal'
            , 'CONVERT(varchar(8000), h.dsJustificativa) as dsJustificativa'
            , 'h.idPlanilhaItem'
            , 'h.idPlanilhaProjeto'
            , 'h.idPlanilhaProposta'
            , 'h.dsItem'
            , 'h.tpDespesa'
            , 'h.tpPessoa'
            , 'h.nrContraPartida'
            , 'h.tpAcao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('I' => 'tbPlanilhaItens'),
            new Zend_Db_Expr('h.idPlanilhaItem = I.idPlanilhaItens'),
            array(
                'I.Descricao AS Item'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('E' => 'tbPlanilhaEtapa'),
            new Zend_Db_Expr('h.idEtapa = E.idPlanilhaEtapa'),
            array(
                'E.Descricao AS Etapa'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('UNI' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('h.idUnidade = UNI.idUnidade'),
            array(
                'UNI.Descricao AS Unidade'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('TI' => 'Verificacao'),
            new Zend_Db_Expr('TI.idverificacao = h.nrFonteRecurso'),
            array(
                'TI.Descricao as FonteRecurso'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('CID' => 'Municipios'),
            new Zend_Db_Expr('CID.idMunicipioIBGE = h.idMunicipioDespesa'),
            array(
                'CID.Descricao as Cidade'
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('FED' => 'UF'),
            new Zend_Db_Expr('h.idUFDespesa = FED.idUF'),
            array(
                'FED.Sigla as UF'
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('PD' => 'Produto'),
            new Zend_Db_Expr('h.idProduto = PD.Codigo'),
            array(
                'PD.Descricao as Produto'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('p' => 'tbPedidoAlteracaoProjeto'),
            'p.idPedidoAlteracao = h.idPedidoAlteracao',
            array(
                'p.idPedidoAlteracao'
            , 'p.idSolicitante'
            , 'CONVERT(CHAR(10), p.dtSolicitacao, 103) AS dtSolicitacao'
            , 'CONVERT(CHAR(10), p.dtSolicitacao, 108) AS hrSolicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('j' => 'tbPedidoAlteracaoXTipoAlteracao'),
            'p.idPedidoAlteracao = j.idPedidoAlteracao',
            array(
                'CAST(j.dsJustificativa AS TEXT) AS dsProponente'
            , 'j.tpAlteracaoProjeto'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('a' => 'tbAvaliacaoItemPedidoAlteracao'),
            'p.idPedidoAlteracao = a.idPedidoAlteracao AND j.tpAlteracaoProjeto = a.tpAlteracaoProjeto',
            array(
                'a.idAgenteAvaliador'
            , 'CONVERT(CHAR(10), a.dtInicioAvaliacao, 103) AS dtInicioAvaliacao'
            , 'CONVERT(CHAR(10), a.dtInicioAvaliacao, 108) AS hrInicioAvaliacao'
            , 'CONVERT(CHAR(10), a.dtFimAvaliacao, 103) AS dtFimAvaliacao'
            , 'CONVERT(CHAR(10), a.dtFimAvaliacao, 108) AS hrFimAvaliacao'
            , 'a.stAvaliacaoItemPedidoAlteracao AS stAvaliacao'
            , 'CAST(a.dsAvaliacao AS TEXT) AS dsAvaliacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('ai' => 'tbAvaliacaoSubItemPedidoAlteracao'),
            'ai.idAvaliacaoItemPedidoAlteracao = a.idAvaliacaoItemPedidoAlteracao',
            array(
                'ai.idAvaliacaoSubItemPedidoAlteracao AS idAvaliacaoItem'
            , 'ai.stAvaliacaoSubItemPedidoAlteracao AS stAvaliacaoItem'
            , 'CAST(ai.dsAvaliacaoSubItemPedidoAlteracao AS TEXT) AS dsAvaliacaoItem'),
            'BDCORPORATIVO.scSAC'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()


    //BUSCA CORTES SUGERIDOS
    public function buscarAnaliseContaPlanilhaAprovacao($idpronac, $tpplanilha, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pap' => $this->_name),
            array(
                'pap.qtItem',
                'pap.nrFonteRecurso',
                'pap.qtDias',
                'pap.nrOcorrencia',
                'pap.vlUnitario',
                'pap.idPlanilhaAprovacao',
                'pap.idProduto',
                'pap.idUnidade',
                'pap.idEtapa',
                'pap.dsJustificativa',
            )
        );
        /*
        $select->joinLeft(
                array('aa' => 'tbAnaliseAprovacao'), "aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto and aa.tpAnalise = '{$tpplanilha}'", array()
        );
        */
        $select->joinInner(
            array('UNI' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PAP.idUnidade = UNI.idUnidade'),
            array(
                'UNI.Descricao AS Unidade'
            )
        );

        $select->joinInner(
            array('i' => 'tbPlanilhaItens'),
            'pap.idPlanilhaItem  = i.idPlanilhaItens',
            array('i.Descricao as Item')
        );
        $select->joinInner(
            array('e' => 'tbPlanilhaEtapa'),
            'pap.idEtapa  = e.idPlanilhaEtapa',
            array('e.Descricao as Etapa')
        );
        $select->joinLeft(
            array('prod' => 'produto'),
            'pap.idProduto = prod.Codigo',
            array('prod.Descricao as produto')
        );
        $select->where('pap.nrFonteRecurso = ?', 109);
        //$select->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.stAvaliacao ELSE 1 END  = ?', 1);
        $select->where('pap.idPronac = ?', $idpronac);
        $select->where('pap.idPlanilhaItem not in (?)', array(206, 1238));
        $select->where('pap.tpPlanilha = ?', $tpplanilha);

        //adiciona outras condicoes enviadas
        foreach ($where as $chave => $valor) {
            $select->where($chave, $valor);
        }
        return $this->fetchAll($select);
    }

    //Criado no dia 07/10/2013 - Jefferson Alessandro
    public function buscarDadosAvaliacaoDeItem($idPlanilhaAprovacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr(
                    'a.idPRONAC, a.idPlanilhaAprovacao, a.idPlanilhaProposta, a.idPlanilhaProjeto, a.idProduto, b.Descricao as descProduto, a.idEtapa,
                        c.Descricao as descEtapa, a.idPlanilhaItem, d.Descricao as descItem, a.idUnidade, e.Descricao as descUnidade,
                        a.qtItem as Quantidade, a.nrOcorrencia as Ocorrencia, a.vlUnitario as ValorUnitario, a.qtDias as QtdeDias, CAST(a.dsJustificativa as TEXT) as dsJustificativa'
                )
            )
        );
        $select->joinLeft(
            array('b' => 'Produto'),
            "a.idProduto = b.Codigo",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('c' => 'tbPlanilhaEtapa'),
            "a.idEtapa = c.idPlanilhaEtapa",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('d' => 'tbPlanilhaItens'),
            "a.idPlanilhaItem = d.idPlanilhaItens",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('e' => 'tbPlanilhaUnidade'),
            "a.idUnidade = e.idUnidade",
            array(),
            'SAC.dbo'
        );
        $select->where('a.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);

        return $this->fetchAll($select);
    }

    /**
     * Busca todos os recursos da fonte de projeto pelo id do pronac
     * @param integer $idPronac
     */
    public function buscarRecursosDaFonte($idPronac)
    {
        $select = $this
            ->select()
            ->setIntegrityCheck(false)
            ->from(
                array('planilhaAprovacao' => $this->_name),
                array(
                    'fonteRecurso' => 'verificacao.Descricao',
                    'valorFonte' => new Zend_Db_Expr('SUM (qtItem * nrOcorrencia * vlUnitario)'),
                ),
                $this->_schema
            )
            ->joinInner(
                array('verificacao' => 'Verificacao'),
                'planilhaAprovacao.nrFonteRecurso = verificacao.idVerificacao',
                array()
            )
            ->where('stAtivo = ?', 'S')
            ->where('IdPRONAC = ?', $idPronac)
            ->group('verificacao.Descricao');
        return $this->fetchAll($select);
    }

    public function dadosdoitemArray($idPlanilhaAprovacao, $idpronac)
    {
        $cpxpaDAO = new ComprovantePagamentoxPlanilhaAprovacao();
        $selectAux = $cpxpaDAO->valorComprovadoItem(true);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $select = $db->select();

        $select->from(
            array('pa' => $this->_name),
            array('pa.idPlanilhaAprovacao', 'vlItem' => 'pa.vlUnitario', 'vlAprovado' => new Zend_Db_Expr('pa.vlUnitario*pa.qtItem*pa.nrOcorrencia')),
            'SAC.dbo'
        );
        $select->join(
            array('paux' => new Zend_Db_Expr('(' . $this->valoresAgrupados($idpronac, true) . ')')),
            'pa.idPlanilhaItem = paux.idPlanilhaItem AND
            pa.idEtapa = paux.idEtapa AND
            pa.idProduto = paux.idProduto AND
            pa.idUnidade = paux.idUnidade AND
            paux.idPlanilhaAprovacao = pa.idPlanilhaAprovacao ',
            array('paux.Total', 'paux.qtTotal')
        );
        $select->joinInner(
            array('pli' => 'tbPlanilhaItens'),
            'pli.idPlanilhaItens = pa.idPlanilhaItem',
            array(
                'idPlanilhaItens' => 'pli.idPlanilhaItens',
                'NomeItem' => 'pli.Descricao'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('eta' => 'tbPlanilhaEtapa'),
            'eta.idPlanilhaEtapa = pa.idEtapa',
            array('Etapa' => 'eta.Descricao'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('pro' => 'Produto'),
            'pro.Codigo = pa.idProduto',
            array(
                'Produto' => new Zend_Db_Expr("CASE WHEN pro.Descricao IS NULL
                    THEN 'Administra&ccedil;&atilde;o do Projeto'
                    ELSE pro.Descricao END")
            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('cpxpa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'cpxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(
                'cpxpa.stItemAvaliado',
                'cpxpa.dsJustificativa',
                'CONVERT(CHAR(23), cpxpa.dtValidacao, 120) AS dtValidacao'
            ),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cp' => 'tbComprovantePagamento'),
            'cp.idComprovantePagamento = cpxpa.idComprovantePagamento',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('di' => $selectAux),
            'di.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array('di.vlComprovado')
        );
        $select->joinLeft(
            array('lxpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lic' => 'tbLicitacao'),
            'lxpa.idLicitacao = lic.idLicitacao',
            array(
                'lic.nrLicitacao',
                'modalidadeLicitacao' => 'lic.tpModalidade',
                'processoLicitacao' => 'lic.nrProcesso',
                'CONVERT(CHAR(23), lic.dtPublicacaoEdital, 120) AS dtPubliEditalLicitacao',
                'objetoLicitacao' => 'lic.dsObjeto',
                'fundamentoLicitacao' => 'lic.dsFundamentoLegal'
            ),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lxa' => 'tbLicitacaoxAgentes'),
            'lxa.idLicitacao = lic.idLicitacao and lxa.stVencedor = 1',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlxpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlic' => 'tbDispensaLicitacao'),
            'dlxpa.idDispensaLicitacao = dlic.idDispensaLicitacao',
            array('dsDispensa' => 'dlic.dsDispensaLicitacao', 'dtDispensa' => 'dlic.dtContrato'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cxpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cota' => 'tbCotacao'),
            'cxpa.idCotacao = cota.idCotacao',
            array('cota.dsCotacao', 'cota.dtCotacao', 'cota.nrCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxa' => 'tbCotacaoxAgentes'),
            'cxa.idCotacao = cota.idCotacao and cxa.idCotacaoxAgentes = cxpa.idCotacaoxAgentes',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ag' => 'Agentes'),
            'ag.idAgente = lxa.idAgente OR ag.idAgente = dlic.idAgente OR ag.idAgente = cxa.idAgente OR cp.idFornecedor = ag.idAgente',
            array('cpfcnpjFornecedor' => 'ag.CNPJCPF'),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('nm' => 'Nomes'),
            'nm.idAgente = ag.idAgente',
            array('nmFornecedor' => 'nm.Descricao'),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('p' => 'Projetos'),
            'pa.IdPRONAC = p.IdPRONAC',
            array(
                'p.IdPRONAC',
                '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                'p.NomeProjeto'
            ),
            'SAC.dbo'
        );

        $select->where('pa.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);
        return $db->fetchAll($select);
    }

    /**
     * @param $idpronac
     * @param null $itemAvaliadoFilter
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarItensPagamentoCustoProduto(
        $idpronac,
        $itemAvaliadoFilter = null,
        $uf = null,
        $idPlanilhaEtapa = null,
        $codigoProduto = null,
        $idMunicicpio = null
    )
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array(
                'pAprovacao.idPlanilhaAprovacao', 'vlUnitario', 'qtItem', 'nrOcorrencia',
                new Zend_Db_Expr('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) as Total'),
                new Zend_Db_Expr(
                    "(SELECT sum(b1.vlComprovacao) AS vlPagamento
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                    WHERE c1.idPlanilhaItem = pAprovacao.idPlanilhaItem
                        AND c1.nrFonteRecurso = pAprovacao.nrFonteRecurso
                        AND c1.idProduto = pAprovacao.idProduto
                        AND c1.idEtapa = pAprovacao.idEtapa
                        AND c1.idUFDespesa = pAprovacao.idUFDespesa
                        AND c1.idMunicipioDespesa = pAprovacao.idMunicipioDespesa
                        AND c1.idPronac = pAprovacao.idPronac
                    GROUP BY c1.idPlanilhaItem) as vlComprovado"
                ),
                new Zend_Db_Expr(
                    "(SELECT sum(b2.vlComprovacao) AS vlPagamento
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a2
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b2 ON (a2.idComprovantePagamento = b2.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c2 ON (a2.idPlanilhaAprovacao = c2.idPlanilhaAprovacao)
                    WHERE a2.stItemAvaliado = 1 AND c2.stAtivo = 'S' AND c2.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao AND (c2.idPronac = pAprovacao.idPronac)
                    GROUP BY a2.idPlanilhaAprovacao) as ComprovacaoValidada"
                )
            )
        );
        $select->joinInner(
            array('pEtapa' => 'tbPlanilhaEtapa'),
            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
            array('pEtapa.idPlanilhaEtapa', 'pEtapa.tpCusto', 'pEtapa.Descricao as descEtapa'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pItens' => 'tbPlanilhaItens'),
            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
            array('pItens.idPlanilhaItens', 'pItens.Descricao as descItem'),
            'SAC.dbo'
        );
        $select->joinLeft(array('prod' => 'Produto'), 'pAprovacao.idProduto = prod.Codigo', array('prod.Codigo', 'prod.Descricao'), 'SAC.dbo');
        $select->joinInner(array('UFT' => 'UF'), 'pAprovacao.idUFDespesa = UFT.idUF', array('uf' => 'UFT.Sigla'), 'AGENTES.dbo');
        $select->joinInner(
            array('CID' => 'Municipios'),
            'pAprovacao.idMunicipioDespesa = CID.idMunicipioIBGE',
            array('cidade' => 'CID.Descricao'),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = cppa.idPlanilhaAprovacao',
            array('stItemAvaliado'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cPagamento' => 'tbComprovantePagamento'),
            'cppa.idComprovantePagamento = cPagamento.idComprovantePagamento',
            array('cPagamento.tpDocumento'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('cpa.idCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxa' => 'tbCotacaoxAgentes'),
            'cpa.idCotacaoxAgentes = cxa.idCotacaoxAgentes ',
            array('cxa.idAgente as idFornecedorCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('dlpa.idDispensaLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('lpa.idLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ctpa' => 'tbContratoxPlanilhaAprovacao'),
            'ctpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('ctpa.idContrato'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('pAprovacao.nrFonteRecurso = ?', 109); //Incentivo Fiscal Federal
        $select->where('pAprovacao.tpAcao IS NULL OR pAprovacao.tpAcao <> ? ', 'E'); //Adicionado para n�o listar as que ja foram excluidas
        $select->where('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) > 0');
        $select->where("UFT.Sigla = ?", $uf);
        $select->where("CID.idMunicipioIBGE = ?", $idMunicicpio);
        $select->where("pEtapa.idPlanilhaEtapa = ?", $idPlanilhaEtapa);
        if ($codigoProduto) {
            $select->where("prod.Codigo = ?", $codigoProduto);
        }

        $select->order('prod.Descricao');
        $select->order('pEtapa.idPlanilhaEtapa');
        $select->order('pItens.Descricao');
        $select->order('UFT.Sigla');
        $select->order('CID.Descricao');
        $select->order('pAprovacao.vlUnitario');
        $select->order('pAprovacao.qtItem');
        $select->order('pEtapa.tpCusto');

        if ($itemAvaliadoFilter == 1) {
            $select->where('cppa.stItemAvaliado = ?', 4);
        } elseif ($itemAvaliadoFilter == 2) {
            $select->where('cppa.stItemAvaliado != ?', 4);
        } elseif ($itemAvaliadoFilter == 3) {
            $select->where('cppa.stItemAvaliado = ?', 3);
        }

        return $this->fetchAll($select);
    }

    public function buscarItensPagamentoDados($idpronac, $itemAvaliadoFilter = null)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array(
                'pAprovacao.idPlanilhaAprovacao', 'vlUnitario', 'qtItem', 'nrOcorrencia',
                new Zend_Db_Expr('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) as Total'),
            )
        );
        $select->joinInner(
            array('pEtapa' => 'tbPlanilhaEtapa'),
            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
            array('pEtapa.idPlanilhaEtapa', 'pEtapa.tpCusto', 'pEtapa.Descricao as descEtapa'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pItens' => 'tbPlanilhaItens'),
            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
            array('pItens.idPlanilhaItens', 'pItens.Descricao as descItem'),
            'SAC.dbo'
        );
        $select->joinLeft(array('prod' => 'Produto'), 'pAprovacao.idProduto = prod.Codigo', array('prod.Codigo', 'prod.Descricao'), 'SAC.dbo');
        $select->joinInner(array('UFT' => 'UF'), 'pAprovacao.idUFDespesa = UFT.idUF', array('uf' => 'UFT.Sigla'), 'AGENTES.dbo');
        $select->joinInner(
            array('CID' => 'Municipios'),
            'pAprovacao.idMunicipioDespesa = CID.idMunicipioIBGE',
            array('cidade' => 'CID.Descricao', 'idMunicipio' => 'CID.idMunicipioIBGE'),
            'AGENTES.dbo'
        );
        /*todo*/
        $select->joinLeft(
            array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = cppa.idPlanilhaAprovacao',
            array('stItemAvaliado'),
            'BDCORPORATIVO.scSAC'
        );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('pAprovacao.nrFonteRecurso = ?', 109); //Incentivo Fiscal Federal
        $select->where('pAprovacao.tpAcao IS NULL OR pAprovacao.tpAcao <> ? ', 'E'); //Adicionado para n�o listar as que ja foram excluidas
        $select->where('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) > 0');

        $select->order('prod.Descricao');
        $select->order('pEtapa.idPlanilhaEtapa');
        $select->order('pItens.Descricao');
        $select->order('UFT.Sigla');
        $select->order('CID.Descricao');
        $select->order('pAprovacao.vlUnitario');
        $select->order('pAprovacao.qtItem');
        $select->order('pEtapa.tpCusto');

        if ($itemAvaliadoFilter == 1) {
            $select->where('cppa.stItemAvaliado = ?', 4);
        } elseif ($itemAvaliadoFilter == 2) {
            $select->where('cppa.stItemAvaliado != ?', 4);
        } elseif ($itemAvaliadoFilter == 3) {
            $select->where('cppa.stItemAvaliado = ?', 3);
        }
        /* echo $select;die; */

        return $this->fetchAll($select);
    }

    public function dadosdoitemPorItem($idPlanilhaItem, $idpronac)
    {
        $cpxpaDAO = new ComprovantePagamentoxPlanilhaAprovacao();
        $selectAux = $cpxpaDAO->valorComprovadoItem(true);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $select = $db->select();
        $select->setIntegrityCheck(false);

        $cols = [
            'cppa.idComprovantePagamento',
            'cppa.stItemAvaliado',
            'CAST( cppa.dsJustificativa AS TEXT) AS ocorrencia',
            'CONVERT(
                    CHAR(23),
                    cppa.dtValidacao,
                        120
                        ) AS dtValidacao',
            'cppa.vlComprovado AS vlComprovadoPlanilhaAprovacao',
            'cppa.idPlanilhaAprovacao',
            'cp.tpDocumento',
            'cp.nrSerie',
            'CONVERT( CHAR(23), cp.dtEmissao, 120) AS dtEmissao',
            'cp.nrComprovante',
            'cp.idArquivo',
            'cp.tpFormaDePagamento',
            'cp.nrDocumentoDePagamento',
            'CAST(cp.dsJustificativa AS TEXT) AS dsJustificativa',
            'cp.vlComprovacao',
            'ag.CNPJCPF',
            'nm.Descricao',
            'arq.nmArquivo',
            'pi.idPlanilhaItens',
            'pi.Descricao AS NomeItem',
            'pAprovacao.stAtivo'
        ];

        $select->from(
            array('pAprovacao' => 'tbplanilhaaprovacao'),
            $cols,
            'SAC.dbo'
        );

        $select->join(
            array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'cppa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            [],
            'BDCORPORATIVO.scSAC'
        );

        $select->join(
            array('cp' => 'tbComprovantePagamento'),
            'cp.idComprovantePagamento = cppa.idComprovantePagamento',
            [],
            'BDCORPORATIVO.scSAC'
        );

        $select->join(
            array('ag' => 'Agentes'),
            'ag.idAgente = cp.idFornecedor',
            [],
            'AGENTES.dbo'
        );

        $select->joinLeft(
            array('nm' => 'Nomes'),
            'nm.idAgente = ag.idAgente',
            [],
            'AGENTES.dbo'
        );

        $select->join(
            array('arq' => 'tbArquivo'),
            'arq.idArquivo = cp.idArquivo',
            [],
            'BDCORPORATIVO.scCorp'
        );

        $select->join(
            array('pi' => 'tbPlanilhaItens'),
            'pAprovacao.idPlanilhaItem = pi.idPlanilhaItens',
            [],
            'SAC.dbo'
        );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.idPlanilhaItem = ?', $idPlanilhaItem);
        $select->where('cppa.stItemAvaliado is not null');
        /* echo $select;die; */

        return $db->fetchAll($select);
    }


    public function visualizarPlanilhaEmRemanejamento($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('v' => 'vwVisualizarPlanilhaEmRemanejamento'),
            array(
                'v.idPronac',
                'v.PRONAC',
                'v.NomeProjeto',
                'v.idProduto',
                'v.idPlanilhaAprovacao',
                'v.idPlanilhaAprovacaoPai',
                'v.Produto',
                'v.idEtapa',
                'v.Etapa',
                'v.tpGrupo',
                'v.Item',
                'v.idFonte',
                'v.FonteRecurso',
                'v.Unidade',
                'v.Quantidade',
                'v.Ocorrencia',
                'v.vlUnitario',
                'v.vlAprovado',
                'v.vlComprovado',
                'v.QtdeDias',
                'v.UF',
                'v.Municipio',
                'v.dsJustificativa',
                'v.idAgente',
                'v.stAtivo',
                'v.idReadequacao'
            ),
            $this->_schema
        );

        $select->where("v.idPronac = ?", $idPronac);
        $select->order("v.FonteRecurso");
        $select->order("v.Produto DESC");
        $select->order(new Zend_Db_Expr("convert(varchar(10), v.idEtapa) + ' - ' + v.Etapa"));
        $select->order("v.UF");
        $select->order("v.Municipio");
        $select->order("v.Item");

        $select->setIntegrityCheck(false);

        return $this->fetchAll($select);
    }

    public function buscarItensPagamentoDadosView($idpronac, $itemAvaliadoFilter = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('vwComprovacaoProjetoProdutoEtapaUFCidade'),
            [
                '*', 'Etapa as descEtapa', 'Uf as uf',
                'Cidade as cidade', 'cdEtapa  as idPlanilhaEtapa',
                'cdProduto as Codigo', 'idMunicipio as  idMunicipio'
            ],
            $this->_schema
        );
        $select->where('IdPRONAC = ?', $idpronac);
        /* echo $select; */

        return $this->fetchAll($select);
    }

    public function vwComprovacaoFinanceiraProjeto(
        $idpronac,
        $uf = null,
        $idPlanilhaEtapa = null,
        $codigoProduto = null,
        $idMunicipio = null,
        $tpCusto = null,
        $idPlanilhaItem = null
    )
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('vwComprovacaoFinanceiraProjeto'),
            ['*', 'Pronac as PRONAC', 'Item as NomeItem'],
            $this->_schema
        );

        $select->where('IdPRONAC = ?', $idpronac);

        if ($uf) {
            $select->where('Uf = ?', $uf);
        }

        if ($idMunicipio) {
            $select->where('cdCidade = ?', $idMunicipio);
        }

        if ($idPlanilhaEtapa) {
            $select->where('cdEtapa = ?', $idPlanilhaEtapa);
        }

        if ($tpCusto) {
            $select->where('tpCusto = ?', $tpCusto);
        }

        if ($idPlanilhaItem) {
            $select->where('idPlanilhaItens = ?', $idPlanilhaItem);
        }

        if ($codigoProduto || ($codigoProduto == 0 && !is_null($codigoProduto))) {
            $select->where('cdProduto = ?', $codigoProduto);
        }

        $select->order('tpCusto desc');
        $select->order('Produto');
        $select->order('cdEtapa');
        $select->order('Uf');
        $select->order('Cidade');
        /* echo $select;die; */
        return $this->fetchAll($select);
    }


    public function vwComprovacaoProjetoSemAnalise($idpronac, $uf = null, $idPlanilhaEtapa = null, $codigoProduto = null, $idMunicipio = null, $tpCusto = null)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('vwComprovacaoProjetoSemAnalise'),
            ['*'],
            $this->_schema
        );

        $select->where('IdPRONAC = ?', $idpronac);

        if ($uf) {
            $select->where('Uf = ?', $uf);
        }

        if ($idMunicipio) {
            $select->where('cdCidade = ?', $idMunicipio);
        }

        if ($idPlanilhaEtapa) {
            $select->where('cdEtapa = ?', $idPlanilhaEtapa);
        }
        if ($tpCusto) {
            $select->where('tpCusto = ?', $tpCusto);
        }

        $select->order('tpCusto desc');
        $select->order('Produto');
        $select->order('cdEtapa');
        $select->order('Uf');
        $select->order('Cidade');
        /* echo $select;die; */
        return $this->fetchAll($select);
    }

    public function vwComprovacaoProjetoAvaliada($idpronac, $uf = null, $idPlanilhaEtapa = null, $codigoProduto = null, $idMunicipio = null, $tpCusto = null)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('vwComprovacaoProjetoAvaliada'),
            ['*'],
            $this->_schema
        );

        $select->where('IdPRONAC = ?', $idpronac);

        if ($uf) {
            $select->where('Uf = ?', $uf);
        }

        if ($idMunicipio) {
            $select->where('cdCidade = ?', $idMunicipio);
        }
        if ($tpCusto) {
            $select->where('tpCusto = ?', $tpCusto);
        }

        if ($codigoProduto || $codigoProduto == 0) {
            $select->where('cdProduto = ?', $codigoProduto);
        }

        if ($idPlanilhaEtapa) {
            $select->where('cdEtapa = ?', $idPlanilhaEtapa);
        }

        $select->order('tpCusto desc');
        $select->order('Produto');
        $select->order('cdEtapa');
        $select->order('Uf');
        $select->order('Cidade');

        return $this->fetchAll($select);
    }

    public function vwComprovacaoProjetoRecusada($idpronac, $uf = null, $idPlanilhaEtapa = null, $codigoProduto = null, $idMunicipio = null, $tpCusto = null)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('vwComprovacaoProjetoRecusada'),
            ['*'],
            $this->_schema
        );

        $select->where('IdPRONAC = ?', $idpronac);

        if ($uf) {
            $select->where('Uf = ?', $uf);
        }

        if ($idMunicipio) {
            $select->where('cdCidade = ?', $idMunicipio);
        }
        if ($tpCusto) {
            $select->where('tpCusto = ?', $tpCusto);
        }
        if ($idPlanilhaEtapa) {
            $select->where('cdEtapa = ?', $idPlanilhaEtapa);
        }

        $select->order('tpCusto desc');
        $select->order('Produto');
        $select->order('cdEtapa');
        $select->order('Uf');
        $select->order('Cidade');
        /* echo $select;die; */
        return $this->fetchAll($select);
    }

    public function vwComprovacaoFinanceiraProjetoPorItemOrcamentario
    (
        $idpronac,
        $idPlanilhaItem = null,
        $stItemAvaliado = null,
        $codigoProduto = null,
        $idComprovantePagamento = null,
        $cidade = null,
        $cdUF = null,
        $cdEtapa = null
    )
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('vwComprovacaoFinanceiraProjetoPorItemOrcamentario'),
            [
                '*',
                'nmFornecedor as Descricao',
                'nrCNPJCPF as CNPJCPF',
                'dsJustificativaProponente as dsJustificativa',
                'dsOcorrenciaDoTecnico as ocorrencia',
                'tpDocumento as tipoDocumentoNome',
                'nrcnpjcpf as CNPJCPF'
            ],
            $this->_schema
        );

        if ($stItemAvaliado) {
            $select->where('stItemAvaliado = ?', $stItemAvaliado);
        }

        if ($codigoProduto || ($codigoProduto == 0 && !is_null($codigoProduto))) {
            $select->where('cdProduto = ?', $codigoProduto);
        }

        if ($idComprovantePagamento) {
            $select->where('idComprovantePagamento = ?', $idComprovantePagamento);
        }

        if ($idPlanilhaItem) {
            $select->where('idPlanilhaItem = ?', $idPlanilhaItem);
        }

        if ($cidade) {
            $select->where('cdCidade = ?', $cidade);
        }

        if ($cdUF) {
            $select->where('cdUF = ?', $cdUF);
        }

        if ($cdEtapa) {
            $select->where('cdEtapa = ?', $cdEtapa);
        }

        $select->where('IdPRONAC = ?', $idpronac);
        /* echo $select;die; */

        return $this->fetchAll($select);
    }

    public function countPlanilhaRemanejamento($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array($this->_name),
            array(
                'total' => new Zend_Db_Expr('count(idPlanilhaAprovacao)'),
            ),
            $this->_schema
        );

        $select->where('idPronac = ?', $idPronac);
        $select->where('tpPlanilha = ?', 'RP');
        $select->where('stAtivo = ?', 'N');

        return $this->fetchAll($select);
    }

    public function buscarItensPagamentos($idpronac, $itemAvaliadoFilter = null)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pAprovacao' => $this->_name),
            array(
                'pAprovacao.idPlanilhaAprovacao', 'vlUnitario', 'qtItem', 'nrOcorrencia',
                new Zend_Db_Expr('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) as Total'),
                new Zend_Db_Expr(
                    "(SELECT sum(b1.vlComprovacao) AS vlPagamento
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                    WHERE c1.idPlanilhaItem = pAprovacao.idPlanilhaItem
                        AND c1.nrFonteRecurso = pAprovacao.nrFonteRecurso
                        AND c1.idProduto = pAprovacao.idProduto
                        AND c1.idEtapa = pAprovacao.idEtapa
                        AND c1.idUFDespesa = pAprovacao.idUFDespesa
                        AND c1.idMunicipioDespesa = pAprovacao.idMunicipioDespesa
                        AND c1.idPronac = pAprovacao.idPronac
                    GROUP BY c1.idPlanilhaItem) as vlComprovado"
                ),
                new Zend_Db_Expr(
                    "(SELECT sum(b2.vlComprovacao) AS vlPagamento
                    FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a2
                    INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b2 ON (a2.idComprovantePagamento = b2.idComprovantePagamento)
                    INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c2 ON (a2.idPlanilhaAprovacao = c2.idPlanilhaAprovacao)
                    WHERE a2.stItemAvaliado = 1 AND c2.stAtivo = 'S' AND c2.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao AND (c2.idPronac = pAprovacao.idPronac)
                    GROUP BY a2.idPlanilhaAprovacao) as ComprovacaoValidada"
                )
            )
        );
        $select->joinInner(
            array('pEtapa' => 'tbPlanilhaEtapa'),
            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
            array('pEtapa.idPlanilhaEtapa', 'pEtapa.tpCusto', 'pEtapa.Descricao as etapa'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pItens' => 'tbPlanilhaItens'),
            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
            array('pItens.idPlanilhaItens', 'pItens.Descricao as item'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('prod' => 'Produto'),
            'pAprovacao.idProduto = prod.Codigo',
            array('prod.Codigo', 'prod.Descricao as produto'),
            'SAC.dbo');

        $select->joinInner(array('UFT' => 'UF'), 'pAprovacao.idUFDespesa = UFT.idUF', array('uf' => 'UFT.Sigla'), 'AGENTES.dbo');
        $select->joinInner(
            array('CID' => 'Municipios'),
            'pAprovacao.idMunicipioDespesa = CID.idMunicipioIBGE',
            array('cidade' => 'CID.Descricao', 'idMunicipio' => 'CID.idMunicipioIBGE'),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array('cppa' => 'tbComprovantePagamentoxPlanilhaAprovacao'),
            'pAprovacao.idPlanilhaAprovacao = cppa.idPlanilhaAprovacao',
            array('stItemAvaliado'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cPagamento' => 'tbComprovantePagamento'),
            'cppa.idComprovantePagamento = cPagamento.idComprovantePagamento',
            ['cPagamento.tpDocumento', 'idComprovantePagamento'],
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cpa' => 'tbCotacaoxPlanilhaAprovacao'),
            'cpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('cpa.idCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('cxa' => 'tbCotacaoxAgentes'),
            'cpa.idCotacaoxAgentes = cxa.idCotacaoxAgentes ',
            array('cxa.idAgente as idFornecedorCotacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('dlpa' => 'tbDispensaLicitacaoxPlanilhaAprovacao'),
            'dlpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('dlpa.idDispensaLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('lpa' => 'tbLicitacaoxPlanilhaAprovacao'),
            'lpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('lpa.idLicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('ctpa' => 'tbContratoxPlanilhaAprovacao'),
            'ctpa.idPlanilhaAprovacao = pAprovacao.idPlanilhaAprovacao',
            array('ctpa.idContrato'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('pAprovacao.nrFonteRecurso = ?', 109); //Incentivo Fiscal Federal
        $select->where('pAprovacao.tpAcao IS NULL OR pAprovacao.tpAcao <> ? ', 'E'); //Adicionado para n�o listar as que ja foram excluidas
        $select->where('(pAprovacao.qtItem*pAprovacao.nrOcorrencia*pAprovacao.vlUnitario) > 0');

        $select->order('prod.Descricao');
        $select->order('pEtapa.idPlanilhaEtapa');
        $select->order('pItens.Descricao');
        $select->order('UFT.Sigla');
        $select->order('CID.Descricao');
        $select->order('pAprovacao.vlUnitario');
        $select->order('pAprovacao.qtItem');
        $select->order('pEtapa.tpCusto');

        if ($itemAvaliadoFilter == 1) {
            $select->where('cppa.stItemAvaliado = ?', 4);
        } elseif ($itemAvaliadoFilter == 2) {
            $select->where('cppa.stItemAvaliado != ?', 4);
        } elseif ($itemAvaliadoFilter == 3) {
            $select->where('cppa.stItemAvaliado = ?', 3);
        }
        /* echo $select;die; */
        return $this->fetchAll($select);
    }

    public function planilhaAprovada(
        $idpronac,
        $uf = null,
        $idPlanilhaEtapa = null,
        $codigoProduto = null,
        $idMunicipio = null,
        $tpCusto = null,
        $idPlanilhaItem = null
    )
    {
        $cols = new Zend_Db_Expr("
            a.IdPRONAC,
            a.idPlanilhaAprovacao,
            i.AnoProjeto+i.Sequencial AS Pronac,
            i.NomeProjeto,
            ISNULL(d.Codigo,0) as cdProduto,
            ISNULL(d.Descricao,'Administra&ccedil;&atilde;o do Projeto') AS Produto,
            a.idUFDespesa AS cdUF,
            b.tpCusto,
            b.idPlanilhaEtapa as cdEtapa,
            b.Descricao AS Etapa,
            b.Descricao AS descEtapa,
            e.Sigla AS Uf,
            e.Sigla AS uf,
            a.idMunicipioDespesa as cdCidade,
            f.Descricao AS Cidade,
            f.Descricao AS cidade,
            c.idPlanilhaItens,
            c.Descricao AS Item,
            c.Descricao AS descItem,
            d.Descricao ,
            CONVERT(DECIMAL(38,2), ISNULL((qtItem * nrOcorrencia * vlUnitario),0) ) as vlAprovado,
            CONVERT(DECIMAL(38,2), sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item
                   (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                    a.idMunicipioDespesa,a.idPlanilhaItem)) as vlComprovado,
            CONVERT(DECIMAL(38,2), sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item_Validado
                   (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                    a.idMunicipioDespesa,a.idPlanilhaItem))  as ComprovacaoValidada,
            CONVERT(DECIMAL(38,2), sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item_Validado
                   (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                    a.idMunicipioDespesa,a.idPlanilhaItem)) as Total,
            sac.dbo.fnQtdeComprovado_Fonte_Produto_Etapa_Local_Item(a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa, a.idMunicipioDespesa,a.idPlanilhaItem) as qtComprovado,
            sac.dbo.fnQtdeComprovado_Fonte_Produto_Etapa_Local_Item_Validado(a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa, a.idMunicipioDespesa,a.idPlanilhaItem) as qtComprovadoValidado,
            sac.dbo.fnQtdeComprovado_Fonte_Produto_Etapa_Local_Item_Recusado(a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa, a.idMunicipioDespesa,a.idPlanilhaItem) as qtComprovadoRecusada,
            a.QtItem as quantidade,
            a.nrOcorrencia as numeroOcorrencias,
            a.VlUnitario as valor
        ");

        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);

        $select->from(
            ['a' => 'tbplanilhaaprovacao'],
            $cols,
            'SAC.dbo'
        );

        $select->join(
            ['b' => 'tbPlanilhaEtapa'],
            "(a.idEtapa = b.idPlanilhaEtapa)",
            [],
            'SAC.dbo'
        );

        $select->join(
            ['c' => 'tbPlanilhaItens'],
            "(a.idPlanilhaItem  = c.idPlanilhaItens)",
            [],
            'SAC.dbo'
        );

        $select->joinLeft(
            ['d' => 'produto'],
            "(a.idproduto = d.Codigo)",
            [],
            'SAC.dbo'
        );

        $select->join(
            ['e' => 'UF'],
            "(a.idUFDespesa = e.idUF)",
            [],
            'AGENTES.dbo'
        );

        $select->join(
            ['f' => 'Municipios'],
            "(a.idMunicipioDespesa = f.idMunicipioIBGE)",
            [],
            'AGENTES.dbo'
        );

        $select->joinLeft(
            ['g' => 'tbComprovantePagamentoxPlanilhaAprovacao'],
            "(a.idPlanilhaAprovacao = g.idPlanilhaAprovacao)",
            [],
            'BDCORPORATIVO.scSAC'
        );
//
//        $select->joinLeft(
//                ['h' => 'tbComprovantePagamento'],
//                "(g.idComprovantePagamento = h.idComprovantePagamento)" ,
//               [],
//                'BDCORPORATIVO.scSAC'
//        );

        $select->join(
            ['i' => 'Projetos'],
            "(a.IdPRONAC = i.IdPRONAC)",
            [],
            'sac.dbo'
        );

        /* $select->where('a.tpplanilha = ?', 'SR'); */
        /* $select->where(new Zend_Db_Expr(' */
        /*     sac.dbo.fnVlAprovado_Fonte_Produto_Etapa_Local_Item */
        /*         (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa, */
        /*         a.idMunicipioDespesa,a.idPlanilhaItem) > 0 */
        /*     ')); */
        $select->where("a.tpacao <> 'E' OR a.tpacao is null");
        $select->where('a.IdPRONAC = ?', $idpronac);
        $select->where('a.nrFonteRecurso = 109');
        $select->where('a.stAtivo = ? ', 'S');

        if ($uf) {
            $select->where('sigla = ?', $uf);
        }

        if ($idMunicipio) {
            $select->where('f.idMunicipioIBGE = ?', $idMunicipio);
        }

        if ($idPlanilhaEtapa) {
            $select->where('b.idPlanilhaEtapa = ?', $idPlanilhaEtapa);
        }

        if ($idPlanilhaItem) {
            $select->where('c.idPlanilhaItens = ?', $idPlanilhaItem);
        }

        if ($codigoProduto != 0 && !is_null($codigoProduto)) {
            $select->where('d.codigo = ?', $codigoProduto);
        } else if ($codigoProduto == 0 && !is_null($codigoProduto)) {
            $select->where('d.codigo is null');
        }

        $select->order('c.Descricao');
        return $this->fetchAll($select);
    }

    public function parametrosBuscaComprovantes(
        $idpronac,
        $uf = null,
        $idPlanilhaEtapa = null,
        $codigoProduto = null,
        $idMunicipio = null,
        $idPlanilhaItem = null
    )
    {
        $cols = new Zend_Db_Expr("
                a.IdPRONAC,
                e.Sigla AS Uf,
                ISNULL(d.Codigo,0) as cdProduto,
                a.idMunicipioDespesa as cdCidade,
                c.idPlanilhaItens,
                b.idPlanilhaEtapa as cdEtapa,
                g.stItemAvaliado
        ");

        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);

        $select->from(
            ['a' => 'tbplanilhaaprovacao'],
            $cols,
            'SAC.dbo'
        );

        $select->join(
            ['b' => 'tbPlanilhaEtapa'],
            "(a.idEtapa = b.idPlanilhaEtapa)",
            [],
            'SAC.dbo'
        );

        $select->join(
            ['c' => 'tbPlanilhaItens'],
            "(a.idPlanilhaItem  = c.idPlanilhaItens)",
            [],
            'SAC.dbo'
        );

        $select->joinLeft(
            ['d' => 'produto'],
            "(a.idproduto = d.Codigo)",
            [],
            'SAC.dbo'
        );

        $select->join(
            ['e' => 'UF'],
            "(a.idUFDespesa = e.idUF)",
            [],
            'AGENTES.dbo'
        );

        $select->join(
            ['f' => 'Municipios'],
            "(a.idMunicipioDespesa = f.idMunicipioIBGE)",
            [],
            'AGENTES.dbo'
        );

        $select->joinLeft(
            ['g' => 'tbComprovantePagamentoxPlanilhaAprovacao'],
            "(a.idPlanilhaAprovacao = g.idPlanilhaAprovacao)",
            [],
            'BDCORPORATIVO.scSAC'
        );

        $select->joinLeft(
            ['h' => 'tbComprovantePagamento'],
            "(g.idComprovantePagamento = h.idComprovantePagamento)",
            [],
            'BDCORPORATIVO.scSAC'
        );

        $select->join(
            ['i' => 'Projetos'],
            "(a.IdPRONAC = i.IdPRONAC)",
            [],
            'sac.dbo'
        );

        /* $select->where('a.tpplanilha = ?', 'SR'); */
        $select->where(new Zend_Db_Expr('
            sac.dbo.fnVlAprovado_Fonte_Produto_Etapa_Local_Item
                (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                a.idMunicipioDespesa,a.idPlanilhaItem) > 0
            '));
        $select->where("a.tpacao <> 'E' OR a.tpacao is null");
        $select->where('a.IdPRONAC = ?', $idpronac);
        $select->where('a.nrFonteRecurso = 109');
        $select->where('a.stAtivo = ? ', 'S');

        if ($uf) {
            $select->where('sigla = ?', $uf);
        }

        if ($idMunicipio) {
            $select->where('f.idMunicipioIBGE = ?', $idMunicipio);
        }

        if ($idPlanilhaEtapa) {
            $select->where('b.idPlanilhaEtapa = ?', $idPlanilhaEtapa);
        }

        if ($idPlanilhaItem) {
            $select->where('c.idPlanilhaItens = ?', $idPlanilhaItem);
        }

        if ($codigoProduto != 0 && !is_null($codigoProduto)) {
            $select->where('d.codigo = ?', $codigoProduto);
        } else if ($codigoProduto == 0 && !is_null($codigoProduto)) {
            $select->where('d.codigo is null');
        }

        $select->order('c.idPlanilhaItens');

        return $this->fetchAll($select);
    }

    public function obterItensAprovados($idPronac,
                                        $uf = null,
                                        $idPlanilhaEtapa = null,
                                        $codigoProduto = null,
                                        $idMunicipio = null,
                                        $idPlanilhaItem = null
    )
    {
        $cols = new Zend_Db_Expr("
            a.IdPRONAC,
            i.AnoProjeto+i.Sequencial AS Pronac,
            i.NomeProjeto,
            sac.dbo.fnIdentificarIdPlanilhaAprovacao(
                a.idPronac,
                a.nrFonteRecurso,
                a.idProduto,
                a.idEtapa,
                a.idUFDespesa,
                a.idMunicipioDespesa,
                a.idPlanilhaItem) as idPlanilhaAprovacao,
            ISNULL(d.Codigo,0) as cdProduto,
            ISNULL(d.Descricao,'Administra&ccedil;&atilde;o do Projeto') AS Produto,
            b.tpCusto,
            b.idPlanilhaEtapa as cdEtapa,
            b.Descricao AS Etapa,
            b.nrOrdenacao,
            a.idUFDespesa AS cdUF,
            e.Sigla AS Uf,
            a.idMunicipioDespesa as cdCidade,
            f.Descricao AS Cidade,
            c.idPlanilhaItens,
            c.Descricao AS Item,
            d.Descricao,
            CONVERT(DECIMAL(38,2), sac.dbo.fnVlAprovado_Fonte_Produto_Etapa_Local_Item
                   (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                    a.idMunicipioDespesa,a.idPlanilhaItem)) as vlAprovado,
            CONVERT(DECIMAL(38,2), sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item
                   (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                    a.idMunicipioDespesa,a.idPlanilhaItem)) as vlComprovado,
            CONVERT(DECIMAL(38,2), sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item_Validado
                   (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                    a.idMunicipioDespesa,a.idPlanilhaItem))  as ComprovacaoValidada,
            CONVERT(DECIMAL(38,2), sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item
                   (a.idPronac,a.nrFonteRecurso,a.idProduto,a.idEtapa,a.idUFDespesa,
                    a.idMunicipioDespesa,a.idPlanilhaItem)) as Total
                    "
        );

        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);

        $select->from(
            ['a' => 'tbplanilhaaprovacao'],
            $cols,
            'SAC.dbo'
        );

        $select->join(
            ['b' => 'tbPlanilhaEtapa'],
            "(a . idEtapa = b . idPlanilhaEtapa)",
            [],
            'SAC.dbo'
        );

        $select->join(
            ['c' => 'tbPlanilhaItens'],
            "(a . idPlanilhaItem = c . idPlanilhaItens)",
            [],
            'SAC.dbo'
        );

        $select->joinLeft(
            ['d' => 'produto'],
            "(a . idproduto = d . Codigo)",
            [],
            'SAC.dbo'
        );

        $select->join(
            ['e' => 'UF'],
            "(a . idUFDespesa = e . idUF)",
            [],
            'AGENTES.dbo'
        );

        $select->join(
            ['f' => 'Municipios'],
            "(a . idMunicipioDespesa = f . idMunicipioIBGE)",
            [],
            'AGENTES.dbo'
        );

        $select->join(
            ['i' => 'Projetos'],
            "(a . IdPRONAC = i . IdPRONAC)",
            [],
            'sac.dbo'
        );

        $select->where('a.nrFonteRecurso = 109');
        $select->where('a.stAtivo = ? ', 'S');

        $select->where('a.IdPRONAC = ?', $idPronac);

        if ($uf) {
            $select->where('e.sigla = ?', $uf);
        }

        if ($idMunicipio) {
            $select->where('f.idMunicipioIBGE = ?', $idMunicipio);
        }

        if ($idPlanilhaEtapa) {
            $select->where('b.idPlanilhaEtapa = ?', $idPlanilhaEtapa);
        }

        if ($idPlanilhaItem) {
            $select->where('c.idPlanilhaItens = ?', $idPlanilhaItem);
        }

        if ($codigoProduto != 0 && !is_null($codigoProduto)) {
            $select->where('d.codigo = ?', $codigoProduto);
        } else if ($codigoProduto == 0 && !is_null($codigoProduto)) {
            $select->where('d.codigo is null');
        }

        $select->order(['Produto DESC', 'e.Sigla', 'f.Descricao', 'b.nrOrdenacao', 'c.Descricao']);

        return $this->fetchAll($select);
    }

    public function consolidacaoValoresProjeto($idPronac)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);

        $cols = new Zend_Db_Expr(
            'a.IdPRONAC
                , SUM(convert(DECIMAL(38, 2), (a.QtItem * a.nrOcorrencia * a.VlUnitario))) as valorAprovadoProjeto
                , g.valorComprovado'
        );

        $select->from(
            ['a' => 'tbplanilhaaprovacao'],
            $cols,
            'SAC.dbo'
        );

        $subQuery = new Zend_Db_Expr('
            (SELECT tbpa.IdPRONAC , sum(vlComprovado) as valorComprovado
            FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao cXp
            inner join BDCORPORATIVO.scSAC.tbComprovantePagamento as c ON
            c.idComprovantePagamento = cXp.idComprovantePagamento
            left JOIN SAC.DBO.tbPlanilhaAprovacao as tbpa on
            (cXp.idPlanilhaAprovacao = tbpa.idPlanilhaAprovacao)
            group by tbpa.IdPRONAC)
            '
        );

        $select->joinLeft(
            ['g' => $subQuery],
            "(a.idpronac = g.idpronac)",
            [],
            null
        );

        $select->where('a.nrFonteRecurso = 109');
        $select->where('a.stAtivo = ? ', 'S');
        $select->where("(a.tpacao <> 'E' OR a.tpacao is null)");
        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->group(['a.IdPRONAC', 'g.valorComprovado']);

        return $this->fetchRow($select);
    }
}
