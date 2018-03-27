<?php
class tbPlanilhaAprovacao extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "tbPlanilhaAprovacao";
    protected $_primary = "idPlanilhaAprovacao";


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
    ) {
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
     * @param integer $idPlanilhaItem
     * @return boolean
     */
    public function valorTotalPlanilhaAtiva($idPronac) {
        
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
                
        return $this->fetchAll($select);
    }

    /**
     * Método para retornar o valor total da planilha ativa
     * @access public
     * @param integer $idPronac
     * @param integer $idPlanilhaItem
     * @return boolean
     */
    public function valorTotalPlanilhaAtivaNaoExcluidosPorEtapa($idPronac, $idEtapa) {
        
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
     * @return boolean
     */
    public function valorTotalPlanilhaReadequada($idPronac, $idReadequacao) {
        
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
        $vlTotalItem = $item['qtItem']*$item['nrOcorrencia']*$item['vlUnitario'];
        
        //CALCULAR VALORES MINIMO E MAXIMO PARA VALIDACAO
        $vlAtual = @number_format(
            (
                $item['qtItem'] * $item['nrOcorrencia'] * $item['vlUnitario']
            ),
            2,
            '',
            ''
        );
        $vlAtualPerc = $vlAtual* Readequacao_Model_tbReadequacao::PERCENTUAL_REMANEJAMENTO/100;
        
        //VALOR MINIMO E MAXIMO DO ITEM ORIGINAL
        //SE TIVER VALOR COMPROVADO, DEVE SUBTRAIR O VALOR DO ITEM COMPROVADO DO VALOR UNITARIO
        
        $vlAtualMin = (
            number_format(
                $valorComprovado,
                2,
                '',
                ''
            ) > round($vlAtual-$vlAtualPerc)
        )
                    ? number_format(
                        $valorComprovado,
                        2,
                        '',
                        ''
                    )
                    : round($vlAtual-$vlAtualPerc);

        $vlAtualMax = round($vlAtual+$vlAtualPerc);
        
        $valoresItem = [];
        
        $valoresItem['vlTotalItem'] = $vlTotalItem;
        $valoresItem['vlAtual'] = $vlAtual;
        $valoresItem['vlAtualPerc'] = $vlAtualPerc;
        $valoresItem['vlAtualMin'] = $vlAtualMin;
        $valoresItem['vlAtualMax'] = $vlAtualMax;
        
        return $valoresItem;
    }
}
