<?php

class AvaliacaoResultados_Model_DbTable_tbPlanilhaAprovacao extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "tbPlanilhaAprovacao";
    protected $_primary = "idPlanilhaAprovacao";

    const FILTRO_ANALISE_FINANCEIRA_VIRTUAL_AGUARDANDO_ANALISE = 1;
    const FILTRO_ANALISE_FINANCEIRA_VIRTUAL_EM_ANALISE = 2;
    const FILTRO_ANALISE_FINANCEIRA_VIRTUAL_ANALISADOS = 3;

    public function init()
    {
        parent::init();
    }

    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    public function alterarDados($dados, $where)
    {
        $where = "idPlanilhaAprovacao = " . $where;
        return $this->update($dados, $where);
    }


    public function buscarItensOrcamentarios($where, $order = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
            array('a' => $this->_name),
            array('idPlanilhaItem')
        );

        $slct->joinLeft(
            array('b' => 'tbPlanilhaItens'),
            "a.idPlanilhaItem = b.idPlanilhaItens",
            array('Descricao'),
            'SAC.dbo'
        );

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->group('a.idPlanilhaItem');
        $slct->group('b.Descricao');

        $slct->order($order);


        return $this->fetchAll($slct);
    }

    /**
     * Função para buscar a planilha ativa
     * @param integer $idPronac
     * @return mixed
     */
    public function buscarPlanilhaAtiva($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('a' => $this->_name),
            '*'
        );

        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->where('a.StAtivo = ?', 'S');

        return $this->fetchAll($select);
    }

    /**
     * Função para buscar a planilha ativa sem excluidos ou zerados
     * @param integer $idPronac
     * @return mixed
     */
    public function buscarPlanilhaAtivaNaoExcluidos($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('a' => $this->_name),
            '*'
        );

        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->where('a.StAtivo = ?', 'S');
        $select->where(new Zend_Db_Expr('a.tpAcao <> ? OR a.tpAcao IS NULL'), 'E');
        $select->where(new Zend_Db_Expr('(a.qtItem * a.nrOcorrencia * a.vlUnitario) > ?'), '0');

        return $this->fetchAll($select);
    }

    /**
     * Função para buscar a planilha ativa
     * @param integer $idPronac
     * @param integer $idReadequacao
     * @return mixed
     */
    public function buscarPlanilhaReadequadaEmEdicao($idPronac, $idReadequacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('a' => $this->_name),
            '*'
        );

        $select->joinInner(
            array('r' => 'tbReadequacao'),
            'a.idReadequacao = r.idReadequacao',
            array(''),
            'SAC.dbo'
        );

        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->where('a.StAtivo = ?', 'S');
        $select->where('r.stEstado = ?', 0);

        return $this->fetchAll($select);
    }


    public function copiandoPlanilhaRecurso($idPronac)
    {
        $sql = "INSERT INTO SAC.dbo.tbPlanilhaAprovacao
                    (tpPlanilha,dtPlanilha,idPlanilhaProjeto,idPlanilhaProposta,idPronac,idProduto,idEtapa,idPlanilhaItem,dsItem,idUnidade,
                    qtItem,nrOcorrencia,vlUnitario,qtDias,tpDespesa,tpPessoa,nrContraPartida,nrFonteRecurso,idUFDespesa,idMunicipioDespesa,
                    dsJustificativa,idAgente,StAtivo)
              SELECT 'CO',GETDATE(),idPlanilhaProjeto,idPlanilhaProposta,'$idPronac',idProduto,idEtapa,idPlanilhaItem,Descricao,idUnidade,
                        Quantidade,Ocorrencia,ValorUnitario,QtdeDias,TipoDespesa,TipoPessoa,Contrapartida,FonteRecurso,UFDespesa,
                        MunicipioDespesa,Justificativa,idUsuario,'S'
                        FROM SAC.dbo.tbPlanilhaProjeto
                        WHERE idPronac = '$idPronac'
        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function copiandoPlanilhaRemanejamento($idPronac)
    {
        $sql = "INSERT INTO SAC.dbo.tbPlanilhaAprovacao
                        (tpPlanilha,dtPlanilha,idPlanilhaProjeto,idPlanilhaProposta,idPronac,idProduto,idEtapa,idPlanilhaItem,dsItem,idUnidade,
                        qtItem,nrOcorrencia,vlUnitario,qtDias,tpDespesa,tpPessoa,nrContraPartida,nrFonteRecurso,idUFDespesa,idMunicipioDespesa,
                        dsJustificativa,idAgente,StAtivo)
               SELECT 'RP',GETDATE(),idPlanilhaProjeto,idPlanilhaProposta,'$idPronac',idProduto,idEtapa,idPlanilhaItem,dsItem,idUnidade,
                        qtItem,nrOcorrencia,vlUnitario,qtDias,tpDespesa,tpPessoa,nrContraPartida,nrFonteRecurso,idUFDespesa,idMunicipioDespesa,
                        dsJustificativa,idAgente,'N'
                        FROM SAC.dbo.tbPlanilhaAprovacao
                        WHERE idPronac = '$idPronac'
        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function buscarDadosAvaliacaoDeItemRemanejamento($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr(
                    'a.idPRONAC,
                    a.idPlanilhaAprovacao,
                    a.idUFDespesa,
                    a.idMunicipioDespesa,
                    a.idProduto,
                    a.nrFonteRecurso,
                    b.Descricao as descProduto,
                    a.idEtapa,
                    c.Descricao as descEtapa,
                    a.idPlanilhaItem,
                    d.Descricao as descItem,
                    a.idUnidade,
                    e.Descricao as descUnidade,
                    a.qtItem as Quantidade,
                    a.nrOcorrencia as Ocorrencia,
                    a.vlUnitario as ValorUnitario,
                    a.qtDias as QtdeDias,
                    CAST(a.dsJustificativa as TEXT) as Justificativa,
                    a.idAgente'
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

        foreach ($where as $key => $valor) {
            $select->where($key, $valor);
        }

        return $this->fetchAll($select);
    }

    public function valorTotalPlanilha($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('ROUND(SUM(a.qtItem*a.nrOcorrencia*a.vlUnitario), 2) AS Total')
            )
        );

        foreach ($where as $key => $valor) {
            $select->where($key, $valor);
        }


        return $this->fetchAll($select);
    }


    public function getInfoIdPlanilhaPai($idPlanilhaAprovacao, $tpPlanilha = null)
    {
        $idPlanilhaAprovacaoPai = array();

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('a.idPlanilhaAprovacao, a.idPlanilhaAprovacaoPai, a.tpAcao, a.tpPlanilha')
            )
        );

        $select->where(new Zend_Db_Expr('(idPlanilhaAprovacao = ?) OR (idPlanilhaAprovacaoPai = ?)', $idPlanilhaAprovacao));
        if ($tpPlanilha) {
            $select->where('tpPlanilha = ?', $tpPlanilha);
        }

        return $this->fetchAll($select);
    }

    /**
     * Método para verificar se existe algum item já cadastrado na mesma fonte, produto, etapa e município
     * @access public
     * @param integer $idPronac
     * @param integer $nrFonteRecurso
     * @param integer $idEtapa
     * @param integer $idMunicipioDespesa
     * @param integer $idPlanilhaItem
     * @return boolean
     */
    public function itemJaAdicionado(
        $idPronac,
        $nrFonteRecurso,
        $idProduto,
        $idEtapa,
        $idMunicipioDespesa,
        $idPlanilhaItem
    )
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            'a.idPlanilhaAprovacao');
        $select->joinInner(
            array('r' => 'tbReadequacao'),
            "a.idReadequacao = r.idReadequacao",
            array(),
            $this->_schema
        );

        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->where('a.nrFonteRecurso = ?', $nrFonteRecurso);
        $select->where('a.idProduto = ?', $idProduto);
        $select->where('a.idEtapa = ?', $idEtapa);
        $select->where('a.idMunicipioDespesa = ?', $idMunicipioDespesa);
        $select->where('a.idPlanilhaItem = ?', $idPlanilhaItem);

        $result = $this->fetchAll($select);

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Método para retornar o valor total da planilha ativa
     * @access public
     * @param integer $idPronac
     * @param array $nrFonteRecurso
     * @return boolean
     */
    public function valorTotalPlanilhaAtiva($idPronac, $nrFonteRecurso = [])
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('ROUND(SUM(a.qtItem*a.nrOcorrencia*a.vlUnitario), 2) AS Total')
            )
        );
        $select->where('a.idPronac = ?', $idPronac);
        $select->where('a.stAtivo = ?', 'S');

        if (!empty($nrFonteRecurso)) {
            $select->where('a.nrFonteRecurso IN(?)', $nrFonteRecurso);
        }

        return $this->fetchAll($select);
    }

    /**
     * Método para retornar o valor total da planilha ativa
     * @access public
     * @param integer $idPronac
     * @param integer $idPlanilhaItem
     * @return boolean
     */
    public function valorTotalPlanilhaAtivaNaoExcluidosPorEtapa($idPronac, $idEtapa)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('ROUND(SUM(a.qtItem*a.nrOcorrencia*a.vlUnitario), 2) AS Total')
            )
        );
        $select->where('a.idPronac = ?', $idPronac);
        $select->where('a.stAtivo = ?', 'S');
        $select->where(new Zend_Db_Expr('a.tpAcao <> ? OR a.tpAcao IS NULL'), 'E');
        $select->where(new Zend_Db_Expr('(a.qtItem * a.nrOcorrencia * a.vlUnitario) > ?'), '0');
        $select->where('a.idEtapa IN (?)', $idEtapa);

        return $this->fetchAll($select);
    }

    /**
     * Método para retornar o valor total da planilha readequada
     * @access public
     * @param integer $idPronac
     * @param integer $idReadequacao
     * @param array $nrFonteRecurso
     * @return boolean
     */
    public function valorTotalPlanilhaReadequada($idPronac, $idReadequacao, $nrFonteRecurso = [])
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('ROUND(SUM(a.qtItem*a.nrOcorrencia*a.vlUnitario), 2) AS Total')
            )
        );

        $select->where('a.tpPlanilha = ?', 'SR');
        $select->where('a.stAtivo = ?', 'N');
        $select->where('a.tpAcao != ?', 'E');
        $select->where('a.idReadequacao = ?', $idReadequacao);

        if (!empty($nrFonteRecurso)) {
            $select->where('a.nrFonteRecurso IN(?)', $nrFonteRecurso);
        }

        return $this->fetchAll($select);
    }

    /**
     * Função para buscar item de planilha original
     * @param integer $idPlanilhaAprovacao
     * @return mixed
     */
    public function buscarItemPlanilhaOriginal($idPlanilhaAprovacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('a' => $this->_name),
            '*'
        );
        $select->joinInner(
            array('b' => $this->_name),
            'a.idPlanilhaAprovacao = b.idPlanilhaAprovacaoPai',
            array(''),
            $this->_schema
        );

        $select->where('b.idPlanilhaAprovacao = ?', $idPlanilhaAprovacao);

        return $this->fetchAll($select);
    }

    /**
     * Busca item ativo por idPlanilhaAprovacao
     *
     * @param integer $idPlanilhaAprovacao
     * @return mixed
     */
    public function buscarItemAtivoId($idPlanilhaAprovacao)
    {
        $where = [];
        $where['idPlanilhaAprovacao = ?'] = $idPlanilhaAprovacao;

        $planilhaAtiva = $this->buscarDadosAvaliacaoDeItemRemanejamento($where);

        if (count($planilhaAtiva) > 0) {
            return $planilhaAtiva[0];
        } else {
            return false;
        }
    }


    /**
     * função para buscar valor comprovado do item
     *
     * @param mixed $planilhaAtiva
     * @return mixed $resComprovado
     */
    public function buscarItemValorComprovado($planilhaAtiva)
    {
        $whereItemValorComprovado = [];
        $whereItemValorComprovado['b.IdPRONAC = ?'] = $planilhaAtiva['idPRONAC'];
        $whereItemValorComprovado['b.idPlanilhaItem = ?'] = $planilhaAtiva['idPlanilhaItem'];
        $whereItemValorComprovado['b.idEtapa = ?'] = $planilhaAtiva['idEtapa'];
        $whereItemValorComprovado['b.idProduto = ?'] = $planilhaAtiva['idProduto'];
        $whereItemValorComprovado['b.idUFDespesa = ?'] = $planilhaAtiva['idUFDespesa'];
        $whereItemValorComprovado['b.idMunicipioDespesa = ?'] = $planilhaAtiva['idMunicipioDespesa'];
        $whereItemValorComprovado['b.nrFonteRecurso = ?'] = $planilhaAtiva['nrFonteRecurso'];

        $tbCompPagxPlanAprov = new tbComprovantePagamentoxPlanilhaAprovacao();
        $resComprovado = $tbCompPagxPlanAprov->buscarValorComprovadoPorFonteProdutoEtapaLocalItem($whereItemValorComprovado);

        if (count($resComprovado) > 0) {
            return $resComprovado;
        } else {
            return false;
        }
    }


    /**
     * retorna item original da planilha
     *
     * @param mixed $planilhaAtiva
     * @return mixed
     */
    public function buscarRemanejamentoPlanilhaOriginal($planilhaAtiva)
    {
        $whereItemPlanilhaOriginal = [];
        $whereItemPlanilhaOriginal['tpPlanilha = ?'] = 'CO'; # CO - planilha do componente da comissao (original aprovada)
        $whereItemPlanilhaOriginal['IdPRONAC = ?'] = $planilhaAtiva['idPRONAC'];
        $whereItemPlanilhaOriginal['idPlanilhaItem = ?'] = $planilhaAtiva['idPlanilhaItem'];
        $whereItemPlanilhaOriginal['idEtapa = ?'] = $planilhaAtiva['idEtapa'];
        $whereItemPlanilhaOriginal['idProduto = ?'] = $planilhaAtiva['idProduto'];
        $whereItemPlanilhaOriginal['idUFDespesa = ?'] = $planilhaAtiva['idUFDespesa'];
        $whereItemPlanilhaOriginal['idMunicipioDespesa = ?'] = $planilhaAtiva['idMunicipioDespesa'];
        $whereItemPlanilhaOriginal['nrFonteRecurso = ?'] = $planilhaAtiva['nrFonteRecurso'];

        $planilhaOriginal = $this->buscar($whereItemPlanilhaOriginal);

        if (count($planilhaOriginal) > 0) {
            return $planilhaOriginal[0];
        } else {
            return false;
        }
    }


    /**
     * retorna valores do item
     */
    public function buscarValoresItem($item, $valorComprovado)
    {
        $vlTotalItem = $item['qtItem'] * $item['nrOcorrencia'] * $item['vlUnitario'];

        //CALCULAR VALORES MINIMO E MAXIMO PARA VALIDACAO
        $vlAtual = @number_format(
            (
                $item['qtItem'] * $item['nrOcorrencia'] * $item['vlUnitario']
            ),
            2,
            '',
            ''
        );
        $vlAtualPerc = $vlAtual * Readequacao_Model_DbTable_TbReadequacao::PERCENTUAL_REMANEJAMENTO / 100;

        //VALOR MINIMO E MAXIMO DO ITEM ORIGINAL
        //SE TIVER VALOR COMPROVADO, DEVE SUBTRAIR O VALOR DO ITEM COMPROVADO DO VALOR UNITARIO

        $vlAtualMin = (
            number_format(
                $valorComprovado,
                2,
                '',
                ''
            ) > round($vlAtual - $vlAtualPerc)
        )
            ? number_format(
                $valorComprovado,
                2,
                '',
                ''
            )
            : round($vlAtual - $vlAtualPerc);

        $vlAtualMax = round($vlAtual + $vlAtualPerc);

        $valoresItem = [];

        $valoresItem['vlTotalItem'] = $vlTotalItem;
        $valoresItem['vlAtual'] = $vlAtual;
        $valoresItem['vlAtualPerc'] = $vlAtualPerc;
        $valoresItem['vlAtualMin'] = $vlAtualMin;
        $valoresItem['vlAtualMax'] = $vlAtualMax;

        return $valoresItem;
    }

    public function obterProjetosAnaliseFinanceiraVirtual(
        $codGrupo,
        $situacaoEncaminhamentoPrestacao,
        $order = null,
        $start = 0,
        $limit = 20,
        $search = null)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();


        switch ($situacaoEncaminhamentoPrestacao) {
            case tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_AGUARDANDO_ANALISE :
                $colunasOrdenadas = [
                    'd.AnoProjeto+d.Sequencial AS Pronac',
                    'd.NomeProjeto',
                    'd.Situacao as cdSituacao',
                    'a.IdPRONAC',
                ];
                $select->where("d.Situacao = ?", 'E68');
                $select->where(
                    "CASE
                        WHEN J.idSituacaoEncPrestContas IS NULL THEN 1
                        ELSE J.idSituacaoEncPrestContas
                    END = ?",
                    tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_AGUARDANDO_ANALISE
                );
                break;
            case tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_EM_ANALISE :
                $colunasOrdenadas = [
                    'd.AnoProjeto+d.Sequencial AS Pronac',
                    'd.NomeProjeto',
                    'd.Situacao as cdSituacao',
                    'k.usu_nome as nmTecnico',
                    'J.dtFimEncaminhamento',
                    'DATEDIFF(DAY,J.dtInicioEncaminhamento, J.dtFimEncaminhamento) as qtDiasEmAnalise',
                    'a.IdPRONAC',
                    'J.dtInicioEncaminhamento'
                ];
                $select->where("d.Situacao IN ('E27', 'E17', 'E20', 'E30')");
                $select->where(
                    "CASE
                        WHEN J.idSituacaoEncPrestContas IS NULL THEN 1
                        ELSE J.idSituacaoEncPrestContas
                    END = ?",
                    tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_EM_ANALISE
                );
                break;
            case tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_ANALISADOS :
                $colunasOrdenadas = [
                    'd.AnoProjeto+d.Sequencial AS Pronac',
                    'd.NomeProjeto',
                    'd.Situacao as cdSituacao',
                    'g.Descricao as dsSegmento',
                    'f.Descricao as dsArea',
                    'a.IdPRONAC'
                ];
                $select->where("d.Situacao IN ('E20','E30')");
                $select->where(
                    "CASE
                        WHEN J.idSituacaoEncPrestContas IS NULL THEN 1
                        ELSE J.idSituacaoEncPrestContas
                    END = ?",
                    tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_ANALISADOS
                );
                break;
        }
        $colunasOrdenadas[] = '
            (select
                count(Contador)
            from
                sac.dbo.parecercontrole
            where
                AnoProjeto+Sequencial = d.AnoProjeto+d.Sequencial) as Prioridade';


        $colunasOrdenadas = implode(", ", $colunasOrdenadas);
        $colunasOrdenadas = new Zend_Db_Expr($colunasOrdenadas);

        $select->from(
            ['a' => $this->_name],
            [$colunasOrdenadas],
            'SAC.dbo'
        );
        $select->joinInner(
            ['b' => 'tbComprovantePagamentoxPlanilhaAprovacao'],
            'a.idPlanilhaAprovacao    = b.idPlanilhaAprovacao',
            [''],
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            ['c' => 'tbComprovantePagamento'],
            'b.idComprovantePagamento = c.idComprovantePagamento',
            [''],
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            ['d' => 'Projetos'],
            'a.IdPRONAC = d.IdPRONAC',
            [''],
            'SAC.dbo'
        );
        $select->joinInner(
            ['e' => 'tbCumprimentoObjeto'],
            'd.IdPRONAC = e.idPronac',
            [''],
            'SAC.dbo'
        );
        $select->joinInner(
            ['f' => 'Area'],
            'd.Area = f.Codigo',
            [''],
            'SAC.dbo'
        );

        $select->joinInner(
            ['g' => 'Segmento'],
            'd.Segmento = g.Codigo',
            [''],
            'SAC.dbo'
        );
        $select->joinInner(
            ['i' => 'Situacao'],
            'd.Situacao = i.Codigo',
            [''],
            'SAC.dbo'
        );

        $select->joinLeft(
            ['j' => 'tbEncaminhamentoPrestacaoContas'],
            'd.IdPRONAC = J.idPronac AND J.stAtivo = 1',
            [''],
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            ['k' => 'Usuarios'],
            'j.idAgenteDestino = k.usu_codigo',
            [''],
            'Tabelas.dbo'
        );
        $select->joinLeft(
            ['l' => 'tbSituacaoEncaminhamentoPrestacaoContas'],
            'j.idSituacaoEncPrestContas = l.idSituacaoEncPrestContas',
            [''],
            'BDCORPORATIVO.scSAC'
        );

        $select->where('a.nrFonteRecurso = 109');
        $select->where("d.Mecanismo = '1'");
        $select->where("d.Orgao = ?", $codGrupo);

        if (!empty($search['value'])) {
            $select->where('d.AnoProjeto+d.Sequencial like ? OR d.NomeProjeto like ?', '%' . $search['value'] . '%');
        }

        if (!empty($order)) {
            $select->order($order);
        }

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $select->limit($limit, $start);
        }

        return $this->fetchAll($select);
    }

    /**
     * Método que copia planilha associando a um idReadequacao
     * @access private
     * @param integer $idPronac
     * @param integer $idReadequacao
     * @return Bool
     */
    public function copiarPlanilhas($idPronac, $idReadequacao)
    {
        $planilhaSR = array();

        $planilhaAtiva = $this->buscarPlanilhaAtivaNaoExcluidos($idPronac);

        foreach ($planilhaAtiva as $value) {
            $planilhaSR['tpPlanilha'] = 'SR';
            $planilhaSR['dtPlanilha'] = new Zend_Db_Expr('GETDATE()');
            $planilhaSR['idPlanilhaProjeto'] = $value['idPlanilhaProjeto'];
            $planilhaSR['idPlanilhaProposta'] = $value['idPlanilhaProposta'];
            $planilhaSR['IdPRONAC'] = $value['IdPRONAC'];
            $planilhaSR['idProduto'] = $value['idProduto'];
            $planilhaSR['idEtapa'] = $value['idEtapa'];
            $planilhaSR['idPlanilhaItem'] = $value['idPlanilhaItem'];
            $planilhaSR['dsItem'] = $value['dsItem'];
            $planilhaSR['idUnidade'] = $value['idUnidade'];
            $planilhaSR['qtItem'] = $value['qtItem'];
            $planilhaSR['nrOcorrencia'] = $value['nrOcorrencia'];
            $planilhaSR['vlUnitario'] = $value['vlUnitario'];
            $planilhaSR['qtDias'] = $value['qtDias'];
            $planilhaSR['tpDespesa'] = $value['tpDespesa'];
            $planilhaSR['tpPessoa'] = $value['tpPessoa'];
            $planilhaSR['nrContraPartida'] = $value['nrContraPartida'];
            $planilhaSR['nrFonteRecurso'] = $value['nrFonteRecurso'];
            $planilhaSR['idUFDespesa'] = $value['idUFDespesa'];
            $planilhaSR['idMunicipioDespesa'] = $value['idMunicipioDespesa'];
            $planilhaSR['dsJustificativa'] = null;
            $planilhaSR['idAgente'] = 0;
            $planilhaSR['idPlanilhaAprovacaoPai'] = $value['idPlanilhaAprovacao'];
            $planilhaSR['idReadequacao'] = $idReadequacao;
            $planilhaSR['tpAcao'] = 'N';
            $planilhaSR['idRecursoDecisao'] = $value['idRecursoDecisao'];
            $planilhaSR['stAtivo'] = 'N';

            $idPlanilhaAprovacao = $this->inserir($planilhaSR);

            if (!$idPlanilhaAprovacao) {
                throw new Exception("Houve um erro na c&oacute;pia das planilhas");
            }
        }
        return true;
    }

    public function obterAnaliseFinanceiraVirtual(
        $codGrupo,
        $situacaoEncaminhamentoPrestacao,
        $order = null,
        $start = 0,
        $limit = 20,
        $search = null)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();

        switch ($situacaoEncaminhamentoPrestacao) {
            case tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_AGUARDANDO_ANALISE :
                $colunasOrdenadas = [
                    'd.AnoProjeto+d.Sequencial AS Pronac',
                    'd.AnoProjeto+d.Sequencial AS PRONAC',
                    'd.NomeProjeto',
                    'd.Situacao as cdSituacao',
                    'd.Situacao as Situacao',
                    'd.UfProjeto',
                    'a.IdPRONAC',
                ];
                $select->where("d.Situacao in (?)", ['E68', 'E30']);
                /* $select->where( */
                /*     "CASE */
                /*         WHEN J.idSituacaoEncPrestContas IS NULL THEN 1 */
                /*         ELSE J.idSituacaoEncPrestContas */
                /*     END = ?", */
                /*     tbPlanilhaAprovacao::FILTRO_ANALISE_FINANCEIRA_VIRTUAL_AGUARDANDO_ANALISE */
                /* ); */
                break;
        }
        $colunasOrdenadas[] = '
            (select
                count(Contador)
            from
                sac.dbo.parecercontrole
            where
                AnoProjeto+Sequencial = d.AnoProjeto+d.Sequencial) as Prioridade';


        $colunasOrdenadas = implode(", ", $colunasOrdenadas);
        $colunasOrdenadas = new Zend_Db_Expr($colunasOrdenadas);

        $select->from(
            ['a' => $this->_name],
            [$colunasOrdenadas],
            'SAC.dbo'
        );
        $select->joinInner(
            ['b' => 'tbComprovantePagamentoxPlanilhaAprovacao'],
            'a.idPlanilhaAprovacao    = b.idPlanilhaAprovacao',
            [''],
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            ['c' => 'tbComprovantePagamento'],
            'b.idComprovantePagamento = c.idComprovantePagamento',
            [''],
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            ['d' => 'Projetos'],
            'a.IdPRONAC = d.IdPRONAC',
            ['a.IdPRONAC as idPronac'],
            'SAC.dbo'
        );
        $select->joinInner(
            ['e' => 'tbCumprimentoObjeto'],
            'd.IdPRONAC = e.idPronac',
            [''],
            'SAC.dbo'
        );
        $select->joinInner(
            ['f' => 'Area'],
            'd.Area = f.Codigo',
            [''],
            'SAC.dbo'
        );

        $select->joinInner(
            ['g' => 'Segmento'],
            'd.Segmento = g.Codigo',
            [''],
            'SAC.dbo'
        );
        $select->joinInner(
            ['i' => 'Situacao'],
            'd.Situacao = i.Codigo',
            [''],
            'SAC.dbo'
        );

        $select->joinLeft(
            ['j' => 'tbEncaminhamentoPrestacaoContas'],
            'd.IdPRONAC = J.idPronac AND J.stAtivo = 1',
            [''],
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            ['k' => 'Usuarios'],
            'j.idAgenteDestino = k.usu_codigo',
            [''],
            'Tabelas.dbo'
        );
        $select->joinLeft(
            ['l' => 'tbSituacaoEncaminhamentoPrestacaoContas'],
            'j.idSituacaoEncPrestContas = l.idSituacaoEncPrestContas',
            [''],
            'BDCORPORATIVO.scSAC'
        );

        $select->where('a.nrFonteRecurso = 109');
        $select->where("d.Mecanismo = '1'");
        $select->where("d.Orgao = ?", $codGrupo);
        $select->where("d.Situacao = ?", "E68");

        if (!empty($search['value'])) {
            $select->where('d.AnoProjeto+d.Sequencial like ? OR d.NomeProjeto like ?', '%' . $search['value'] . '%');
        }

        /* echo $select;die; */

        return $this->fetchAll($select);
    }

    public function projetoContemEtapasCustosDivulgacao($idPronac)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);

        $objQuery->from(
            array(
                'tbPlanilhaAprovacao' => $this->_name
            ),
            'idPronac',
            $this->_schema
        );

        $objQuery->where('tbPlanilhaAprovacao.tpPlanilha = ?', 'CO');
        $objQuery->where('tbPlanilhaAprovacao.idEtapa IN(?)', [
            PlanilhaEtapa::ETAPA_CUSTOS_ADMINISTRATIVOS,
            PlanilhaEtapa::ETAPA_DIVULGACAO_COMERCIALIZACAO
        ]);
        $objQuery->where('tbPlanilhaAprovacao.IdPRONAC = ?', $idPronac);

        $result = $this->fetchAll($objQuery);

        if (count($result > 0)) {
            return true;
        }
        return false;
    }

    public function validarTodosComprovantes($idPronac)
    {
        if (!is_numeric($idPronac)) {
            return false;
        }

        $sql = "
          UPDATE
              BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao
          SET
              stItemAvaliado = 1
          FROM
              SAC.dbo.tbplanilhaaprovacao AS a
          INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS b ON
              (a.idPlanilhaAprovacao = b.idPlanilhaAprovacao)
          INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS c ON
              (b.idComprovantePagamento = c.idComprovantePagamento)
          INNER JOIN sac.dbo.Projetos AS d ON (a.IdPRONAC = d.IdPRONAC)
          WHERE
              d.IdPRONAC = $idPronac
              AND b.stItemAvaliado = 4";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
