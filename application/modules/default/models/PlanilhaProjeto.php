<?php
class PlanilhaProjeto extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'tbPlanilhaProjeto';
    
    public function alterar($dados, $where, $dbg=false)
    {
        if ($dbg) {
            x($this->dbg($dados, $where));
        }
        $update = $this->update($dados, $where);
        return $update;
    }
    
    public function outrasFontes($idpronac)
    {
        $buscar = $this->select();
        $buscar->setIntegrityCheck(false);

        $buscar->from(
            $this,
                array('sum(Quantidade*Ocorrencia*ValorUnitario) as valor')
        );
        $buscar->joinInner(
                array('tbPlanilhaItens'),
                'idPlanilhaItem = idPlanilhaItens',
                array(),
                'SAC.dbo'
        );

        $buscar->where('idPRONAC = ?', $idpronac);

        return $this->fetchAll($buscar);
    }

    public function valorTotalDoProjeto($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array('pp' => $this->_name),
                array('SUM(pp.ValorUnitario) as valorTotal')
        );
        
        $slct->where('pp.idPRONAC = ? ', $idPronac);
        
        return $this->fetchRow($slct);
    }
    
    
    public function somarPlanilhaProjeto($idpronac, $fonte=null, $outras=null, $where=array())
    {
        $somar = $this->select();
        $somar->from(
            $this,
            array(
                    'sum(Quantidade*Ocorrencia*ValorUnitario) as soma'
                        )
                )
                ->where('IdPRONAC = ?', $idpronac)
                ->where('idProduto <> ?', '206');
        if ($fonte) {
            $somar->where('FonteRecurso = ?', $fonte);
        }
        if ($outras) {
            $somar->where('FonteRecurso <> ?', $outras);
        }
        
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $somar->where($coluna, $valor);
        }

        return $this->fetchRow($somar);
    }
    
    public function somarPlanilhaProjetoTotal($idpronac, $fonte=null, $outras=null)
    {
        $somar = $this->select();
        $somar->from(
            $this,
            array(
                    'sum(Quantidade*Ocorrencia*ValorUnitario) as soma'
                        )
                );

        $somar->where('IdPRONAC = ?', $idpronac);
        
        if ($fonte) {
            $somar->where('FonteRecurso = ?', $fonte);
        }
        if ($outras) {
            $somar->where('FonteRecurso <> ?', $outras);
        }
        return $this->fetchRow($somar);
    }
    
    public function somarPlanilhaProjetoDivulgacao($idpronac, $fonte=null, $outras=null, $idproduto=null)
    {
        $somar = $this->select();
        $somar->from(
            $this,
            array(
                    'sum(Quantidade*Ocorrencia*ValorUnitario) as soma'
                        )
                )
                ->where('IdPRONAC = ?', $idpronac);
        if ($fonte) {
            $somar->where('FonteRecurso = ?', $fonte);
        }
        if ($outras) {
            $somar->where('FonteRecurso <> ?', $outras);
        }
        if (!empty($idproduto)) {
            $somar->where('idProduto = ?', $idproduto);
        }
        $somar->where('idEtapa = ?', 3);
        
        
        return $this->fetchRow($somar);
    }

    /** M�todo para valida��o dos 15% dos cortes do Parecerista ***/
    
    public function somarPlanilhaProjetoParecerista($idpronac, $elaboracao=null, $tpPlanilha=null)
    {
        $somar = $this->select();
        
        $somar->from(
        
            array('PAP' => $this->_name),
                     array('sum(PAP.Quantidade*PAP.Ocorrencia*PAP.ValorUnitario) as soma')
        
        )
        ->joinLeft(
                     array('aa' => 'tbAnaliseAprovacao'),
            "aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto and aa.tpAnalise = '{$tpPlanilha}'",
            array()
                  )
                ->where('PAP.IdPRONAC = ?', $idpronac)
                ->where('PAP.FonteRecurso = ?', '109');
                
        $somar->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.stAvaliacao ELSE 1 END  = ?', 1);
        
        if ($elaboracao) {
            $somar->where('PAP.idPlanilhaItem <> ? ', $elaboracao);
        }
        if ($tpPlanilha) {
            $somar->where('PAP.tpPlanilha = ? ', $tpPlanilha);
        }
        
        
        
        return $this->fetchRow($somar);
    }
    
    
    // M�todo que retorna os  CUSTOS ADMINISTRATIVOS DO PROJETO
    public function somaDadosPlanilha($dados=array())
    {
        $somar = $this->select();
        $somar->from(
            array('PAP' => $this->_name),
                     array('sum(PAP.Quantidade*PAP.Ocorrencia*PAP.ValorUnitario) as soma')
        )
                     
        ->joinLeft(array('aa' => 'tbAnaliseDeConteudo'), 'aa.idPronac = PAP.idPronac and PAP.idProduto = aa.idProduto', array())
        ->where('PAP.FonteRecurso = ?', '109');
        
        $somar->where('CASE WHEN PAP.idPRODUTO <> 0 THEN aa.ParecerFavoravel ELSE 1 END  = ?', 1);
        
        foreach ($dados as $key => $valor) {
            $somar->where($key, $valor);
        }
        
        
        
        return $this->fetchRow($somar);
    }
    
    
    public function dadosPlanilhaProjeto($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                $this,
            array(
            'idPlanilhaProjeto',
            'idPlanilhaProposta',
            'idProduto',
            'idEtapa',
            'idPlanilhaItem',
            'idUnidade',
            'Quantidade',
            'Ocorrencia',
            'ValorUnitario',
            'QtdeDias',
            'TipoDespesa',
            'TipoPessoa',
            'Contrapartida',
            'FonteRecurso',
            'UfDespesa',
            'MunicipioDespesa',
            'Cast(Justificativa as TEXT) as Justificativa'
                )
        );
        $select->where('idPRONAC = ?', $idpronac);
        return $this->fetchAll($select);
    }

    public function parecerFavoravel($idpronac)
    {
        $sql = "UPDATE SAC.dbo.tbPlanilhaProjeto
				SET 
				Quantidade 		= pro.Quantidade
				,Ocorrencia 		= pro.Ocorrencia
				,ValorUnitario 	= pro.ValorUnitario
				FROM SAC.dbo.tbPlanilhaProjeto AS PP
				INNER JOIN SAC.dbo.tbPlanilhaProposta AS pro ON pro.idPlanilhaProposta = PP.idPlanilhaProposta
				WHERE PP.idPronac = ".$idpronac;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->query($sql);
    }

    public function buscarAnaliseCustos($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('PPJ' => $this->_name),
            array(
            'PPJ.idPlanilhaProjeto',
            'PPJ.IdPRONAC',
            'PPJ.idProduto',
            'PPJ.FonteRecurso as FR',
            'PPJ.idUnidade',
            'PPJ.idEtapa',
            '(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) AS VlSugeridoParecerista',
            'CAST(PPJ.Justificativa as TEXT) as dsJustificativaParecerista',
            'PPJ.Quantidade AS quantidadeparc',
            'PPJ.Ocorrencia AS ocorrenciaparc',
            'PPJ.ValorUnitario AS valorUnitarioparc',
            'PPJ.QtdeDias AS diasparc',
            'PPJ.stCustoPraticado AS custopraticado',
                )
        );
        $select->joinInner(
                array('PP' => 'tbPlanilhaProposta'),
            new Zend_Db_Expr('PPJ.idPlanilhaProposta = PP.idPlanilhaProposta'),
            array(
            '(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS VlSolicitado',
            'CAST(PP.dsJustificativa as TEXT) as justificitivaproponente',
            'PP.Quantidade AS quantidadeprop',
            'PP.Ocorrencia AS ocorrenciaprop',
            'PP.ValorUnitario AS valorUnitarioprop',
            'PP.QtdeDias AS diasprop',
            'PP.idPlanilhaProposta'
                )
        );
        $select->joinInner(
                array('I' => 'tbPlanilhaItens'),
            new Zend_Db_Expr('PPJ.idPlanilhaItem = I.idPlanilhaItens'),
            array(
            'I.Descricao AS Item'
                )
        );
        $select->joinLeft(
                array('E' => 'tbPlanilhaEtapa'),
            new Zend_Db_Expr('PPJ.idEtapa = E.idPlanilhaEtapa'),
            array(
            'E.Descricao AS Etapa'
                )
        );
        $select->joinLeft(
                array('UNIPP' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PP.Unidade = UNIPP.idUnidade'),
            array(
            'UNIPP.Descricao AS UnidadeProposta'
                )
        );
        $select->joinLeft(
                array('UNIPJ' => 'tbPlanilhaUnidade'),
            new Zend_Db_Expr('PPJ.idUnidade = UNIPJ.idUnidade'),
            array(
            'UNIPJ.Descricao AS UnidadeProjeto'
                )
        );
        $select->joinLeft(
                array('TI' => 'Verificacao'),
            new Zend_Db_Expr('TI.idverificacao = PPJ.FonteRecurso'),
            array(
            'TI.Descricao as FonteRecurso',
            'TI.idVerificacao as NrFonteRecurso'
                )
        );
        $select->joinLeft(
                array('CID' => 'Municipios'),
            new Zend_Db_Expr('CID.idMunicipioIBGE = PPJ.MunicipioDespesa'),
            array(
            'CID.Descricao as Cidade'
                ),
            'Agentes.dbo'
        );
        $select->joinLeft(
                array('FED' => 'UF'),
            new Zend_Db_Expr('PPJ.UFDespesa = FED.idUF'),
            array(
            'FED.Sigla as UF'
                ),
            'Agentes.dbo'
        );
        $select->joinLeft(
                array('PD' => 'Produto'),
            new Zend_Db_Expr('PPJ.idProduto = PD.Codigo'),
            array(
            'PD.Descricao as Produto'
                )
        );

        foreach ($where as $key => $value) {
            if ($value == null) {
                $select->where($key);
            } else {
                $select->where($key, $value);
            }
        }

        $select->order(
                array(
                    'PPJ.FonteRecurso',
                    'PD.Descricao DESC',
                    'E.nrOrdenacao',
                    'FED.Sigla',
                    'CID.Descricao'
                )
        );
        /* echo $select;die; */
        
        return $this->fetchAll($select);
    }
    
    public function buscarDadosAvaliacaoDeItem($idPlanilhaProjeto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
                array(
                    new Zend_Db_Expr(
                        'a.idPRONAC, a.idPlanilhaProjeto, a.idPlanilhaProposta, a.idProduto, b.Descricao as descProduto, a.idEtapa,
                        c.Descricao as descEtapa, a.idPlanilhaItem, d.Descricao as descItem,
                        a.idUnidade, e.Descricao as descUnidade, a.Quantidade, a.Ocorrencia, a.ValorUnitario, a.QtdeDias, CAST(a.Justificativa as TEXT) as Justificativa'
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
        $select->where('a.idPlanilhaProjeto = ?', $idPlanilhaProjeto);
        
        return $this->fetchAll($select);
    }
    
    public function inserirPlanilhaParaParecerista($idPreProjeto, $idPronac)
    {
        $sqlParecerista = "INSERT INTO SAC.dbo.tbPlanilhaProjeto
                                     (idPlanilhaProposta,idPronac,idProduto,idEtapa,idPlanilhaItem,Descricao,idUnidade,Quantidade,Ocorrencia,ValorUnitario,QtdeDias,
                                     TipoDespesa,TipoPessoa,Contrapartida,FonteRecurso,UFDespesa,    MunicipioDespesa,idUsuario)
                                   SELECT idPlanilhaProposta, {$idPronac},idProduto,idEtapa,idPlanilhaItem,Descricao,Unidade,
                                        Quantidade, Ocorrencia,ValorUnitario,QtdeDias,TipoDespesa,TipoPessoa,Contrapartida,FonteRecurso,UFDespesa,
                                        MunicipioDespesa, 0
                                        FROM SAC.dbo.tbPlanilhaProposta
                                        WHERE idProjeto = {$idPreProjeto}";

        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query($sqlParecerista);
    }
}
