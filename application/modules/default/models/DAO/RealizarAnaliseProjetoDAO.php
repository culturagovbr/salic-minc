<?php
class RealizarAnaliseProjetoDAO extends Zend_db_table
{
    public static function somarOrcamentoSolicitado($idpronac)
    {
        $sele = "select sum(VlTotal) as somatudo from SAC.dbo.vwOrcamentoSolicitado where idpronac = $idpronac";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sele);
    }

    /**
     * M�todo para Verificar Enquadramento
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function verificaEnquadramento($idpronac=null, $tpAnalise=null, $IN2017=false)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        if ($IN2017) {
            $select = $table->select()
                            ->from(
                                array('e' =>'Enquadramento'),
                            array('stArtigo' => new Zend_Db_Expr(
                                "CASE WHEN Enquadramento = 1 
			                       THEN 'Artigo 26' 
				                 WHEN Enquadramento = 2 
				                   THEN 'Artigo 18' 
                                 ELSE 'N&atilde;o enquadrado'
                                 END"
                            )),
                            'SAC.dbo'
                            )
                            ->where('e.idpronac = ?', $idpronac);
        } else {
            // LEGADO - MANTENDO PARA FINS DE COMPATIBILIDADE
            $select = $table->select()
                            ->from(
                                array('taa' =>'tbAnaliseAprovacao'),
                            array('stArtigo18','stArtigo26'),
                            'SAC.dbo'
                            )
                            ->joinInner(
                                array('pr' => 'projetos'),
                            'pr.idpronac = taa.idpronac',
                            array(''),
                            'SAC.dbo'
                            )
                            ->joinInner(
                                array('pdp' => 'PlanoDistribuicaoProduto'),
                            'pdp.idproduto=taa.idproduto and pdp.idprojeto = pr.idprojeto',
                            array(''),
                            'SAC.dbo'
                            )
                            ->where('taa.idpronac = ?', $idpronac)
                            ->where('pdp.stPrincipal = 1')
                            ->where('taa.tpanalise = ?', $tpAnalise)
                            ->where('pdp.stPlanoDistribuicaoProduto = 1');
        }
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($select);
    }
    /**
    * M�todo para Verificar se o parecer � favoravel ou n�o
    * @access public
    * @static
    * @param integer $idPronac
    * @return object
    */
    public static function verificaParecerFavoravel($idpronac)
    {
        $sql = "select
                    case
                    when sac.dbo.fnParecerFavoravel($idpronac) = 1 then 'N�o'
                    when sac.dbo.fnParecerFavoravel($idpronac) = 2 then 'Sim'
                    end as parecer";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo para Verificar Se existe na tabela Planilha aprovacao item com codigo 206
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function verificaItem($idpronac, $codigoitem)
    {
        $sql = "SELECT  TOP 1 1
                    FROM sac.dbo.tbPlanilhaAprovacao
                    WHERE idPronac = $idpronac
                    and idProduto = 0
                    and idPlanilhaItem=$codigoitem
                    and nrFonteRecurso = 109
                    AND stAtivo = 'S'
                    AND tpPlanilha = 'CO'
                   ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo para Verificar Se existe na tabela Planilha aprovacao item com codigo 206
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function BuscarItem($idpronac, $codigoitem)
    {
        $sql = "SELECT  idPlanilhaProposta,
                            idPronac,
                            idProduto,
                            idEtapa,
                            206,
                            dsItem,
                            idUnidade,
                            qtItem,
                            nrOcorrencia,
                            vlUnitario,
                            qtDias,
                            tpDespesa,
                            tpPessoa,
                            nrContraPartida,
                            nrFonteRecurso,
                            idUFDespesa,
                            idMunicipioDespesa,
                            FROM tbPlanilhaAprovacao
                            WHERE idPronac = $idpronac
                            and idProduto = 0
                            and idPlanilhaItem=$codigoitem
                            and nrFonteRecurso = 109
                            AND stAtivo = 'S'
                            AND tpPlanilha = 'CO'
                   ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    /**
         * M�todo que altera o o altera a planilha aprovacao
         * @access public
         * @param array $dados
         * @param integer $idPronac
         * @param integer $idplanilhaitem
         * @static
         * @return object
         */
    public static function alterarplanilhaaprovacao($dados, $idPronac, $idplanilhaitem)
    {
        $where   = "IdPRONAC = $idPronac
                            and idproduto = 0
                            and idplanilhaitem = $idplanilhaitem
                            and nrfonterecurso = 109
                            and stAtivo='S'
                            and tpplanilha='CO'";
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $alterar = $db->update("SAC.dbo.tbPlanilhaAprovacao", $dados, $where);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * M�todo que insere os dados na tabela tbplanilhaaprovacao
     * @access public
     * @param array $dados
     * @static
     * @return object
     */
    public static function inserirplanilhaaprovacao($dados)
    {
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $cadastrar = $db->insert("BSAC.dbo.tbPlanilhaAprovacao", $dados);
            return true;
        } catch (Exception $e) {
            return false;
        }
    } // fecha m�todo cadastrarSubmeterCNIC()

    /**
    * M�todo para trazer a Elabora��o, Valor do Componente da Comissao
    * @access public
    * @static
    * @param integer $idPronac, $tipoBusca = S: Sugerido pelo componente da comissao, E: Elaboracao e agenciamento
    * @return object
    */
    public static function valoresSugerido($idpronac, $tipo)
    {
        $sql = "SELECT SUM(qtItem * nrOcorrencia * vlUnitario) as valor
                    FROM sac.dbo.tbPlanilhaAprovacao p
                    INNER JOIN sac.dbo.tbPlanilhaItens i on (p.idPlanilhaItem = i.idPlanilhaItens)
                    WHERE idPronac = $idpronac and nrFonteRecurso = 109";
        if ($tipo == 'S') {
            $sql .= " and
                        (idPlanilhaItem <> 206 and idPlanilhaItem <> 1238)
                        AND p.stAtivo = 'S' AND p.tpPlanilha = 'CO'
                        ";
        }
        if ($tipo == 'E') {
            $sql .= " and
                        (idPlanilhaItem = 206 and idPlanilhaItem = 1238)
                        AND p.stAtivo = 'S' AND p.tpPlanilha = 'CO'
                        ";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    /**
    * M�todo para trazer a Elabora��o
    * @access public
    * @static
    * @param integer
    * @return object
    */
    public static function valorElaboracaoAgenciamento($idpronac)
    {
        $sql = "SELECT sac.dbo.fnCalcularValorElaboracaoAgenciamento($idpronac,'CO') /
                         sac.dbo.fnQtElaboracaoAgenciamento($idpronac,'CO') as valor
";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }


    /**
     * M�todo para recuperar os projetos em an�lise. (CONSELHEIRO)
     * S� efetua a busca se as fontes de recursos estiverem de acordo com o C�digo 109 � Incentivo Fiscal Federal,
     * conforme Lei 8.313 de 1991.
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function analiseDeConta($idPronac = null, $tpplanilha = null, $idProduto = null, $idEtapa = null, $idUF = null, $idCidade = null, $buscarPorProduto = null)
    {
        $sql = "SELECT distinct PAP.qtItem AS quantidade_con
                    ,PRO.idpronac
                    ,PRO.AnoProjeto+Sequencial as pronac
					,PAP.qtDias AS dias_con
					,PAP.nrOcorrencia AS ocorrencia_con
					,PAP.vlUnitario AS valorUnitario_con
					,PAP.idPlanilhaAprovacao
					,PAP.idPlanilhaProjeto
					,PAP.idPlanilhaProposta
					,PAP.IdPRONAC
					,PAP.idProduto
					,PAP.idUnidade
					,PRO.NomeProjeto
					,PD.Descricao AS Produto
					,PAP.idEtapa
                                        ,tpe.Descricao as Etapa
					,I.Descricao AS Item
					,(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS VlSolicitado
					,((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario)) AS VlReduzidoParecerista
					,(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) AS VlSugeridoParecerista
					,PPJ.Justificativa as  dsJustificativaParecerista
					,((PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) - (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario)) AS VlReduzidoConselheiro
					,(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro
					,PAP.dsJustificativa as  dsJustificativaConselheiro
					,CASE
						WHEN (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario)  = 0	THEN 'Retirado'
						WHEN (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) < (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) THEN 'Reduzido'
						END AS Situacao

				FROM SAC.dbo.Projetos PRO
					 INNER JOIN SAC.dbo.tbPlanilhaProjeto PPJ on PPJ.idPRONAC = PRO.IdPRONAC
					 INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					 INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
                                         INNER JOIN SAC.dbo.tbPlanilhaEtapa tpe on tpe.idplanilhaetapa = PAP.idEtapa
					 left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)
                                         left join SAC.dbo.tbAnaliseAprovacao ap on ap.idpronac = pro.idpronac
				WHERE
                                        PP.FonteRecurso = 109
					AND PAP.tpPlanilha = '$tpplanilha'
                                        AND ap.stAvaliacao = 1
                                        AND I.idplanilhaitens not in(206, 1238)
                                        ";
        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }
        // busca de acordo com o produto
        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND PAP.idProduto = $idProduto";
        }
        // busca de acordo com a etapa
        if (!empty($idEtapa)) {
            $sql.= " AND PAP.idEtapa = $idEtapa";
        }
        // busca de acordo com a uf
        if (!empty($idUF)) {
            $sql.= " AND PAP.idUFDespesa = $idUF";
        }
        // busca de acordo com a cidade
        if (!empty($idCidade)) {
            $sql.= " AND PAP.idMunicipioDespesa = $idCidade";
        }

        $sql.= " ORDER BY Situacao ASC";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    /**
     * M�todo que busca as informa��es da an�lise do parecer consolidado
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function analiseparecerConsolidado($idPronac)
    {
        $sql = "select
			PRO.IdPRONAC
			,PRO.AnoProjeto+PRO.Sequencial as pronac
			,PRO.AnoProjeto
			,PRO.Sequencial
			,PRO.NomeProjeto
			,par.DtParecer
			,par.SugeridoReal
            ,par.ResumoParecer
            , case
                  when par.ParecerFavoravel = 1 then 'N&atilde;o'
                  when par.ParecerFavoravel = 2 then 'Sim'
            end as parecerfavoravel,
            par.TipoParecer
            ,case
			      when par.TipoParecer = 1 then 'Aprova&ccedil;&atilde;o Inicial'
			      when par.TipoParecer = 2 then 'Complementa&ccedil;&atilde;o'
			      when par.TipoParecer = 3 then 'Prorroga&ccedil;&atilde;o'
			      when par.TipoParecer = 4 then 'Redu&ccedil;&atilde;o'
			end as tipoparecer
            ,case
			when par.idEnquadramento = 1 then 'Artigo 26'
			when par.idEnquadramento = 2 then 'Artigo 18' end as enquadramento
            FROM SAC.dbo.Projetos PRO
			left JOIN SAC.dbo.Parecer par on par.idPRONAC = PRO.IdPRONAC
			left JOIN SAC.dbo.Enquadramento enq on enq.IdPRONAC  = PRO.IdPRONAC
            WHERE PAR.idTipoAgente = 1 and PRO.IdPRONAC=" . $idPronac;
        // busca de acordo com o pronac
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } // fecha o metodo analiseparecerConsolidado


    public static function valoresanaliseparecertecnicoconsolidade($idpronac)
    {
        $sql = " SELECT idPronac,AnoProjeto+Sequencial as PRONAC,
                     isnull(ROUND(sac.dbo.fnValorDaProposta(idProjeto),2),
                     ROUND(sac.dbo.fnValorSolicitado(AnoProjeto,Sequencial),2)) as ValorProposta,
                     ROUND(sac.dbo.fnOutrasFontes(idPronac),2) as OutrasFontes,
                     ROUND(sac.dbo.fnValorSolicitado(AnoProjeto,Sequencial),2) as ValorSolicitado,
                     --CASE
                     --WHEN sac.dbo.fnParecerFavoravel(idPronac) = 1 -- Parecer Desfavor�vel
                     --THEN  0
                     --ELSE
                     --sac.dbo.fnValorElaboracaoAgenciamento(idPronac)
                     --END as Elaboracao,
                     CASE
                       WHEN sac.dbo.fnParecerFavoravel(idPronac) = 1 -- Parecer Desfavor�vel
                         THEN  0
                         ELSE  ROUND(sac.dbo.fnValorSugerido(idPronac),2)
                       END as ValorSugerido,
                     CASE
                       WHEN sac.dbo.fnParecerFavoravel(idPronac) = 1 -- Parecer Desfavor�vel
                         THEN  0
                         ELSE  ROUND(sac.dbo.fnValorSugerido(idPronac) - sac.dbo.fnValorElaboracaoAgenciamento(idPronac),2)
                     END as ValorParecer
                     FROM sac.dbo.Projetos
                         WHERE sac.dbo.fnValorDaProposta(idProjeto) > 0 and IdPRONAC=$idpronac
";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo que busca as informa��es da an�lise do parecer consolidado j� somados os valores
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function somaanaliseparecerConsolidado($idPronac, $outrasfontes = null, $elaboracao=null)
    {
        $sql = "SELECT ";

        if ($outrasfontes == 'S') {
            $sql .= " SUM(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS SOMAVlSolicitado";
        }
        if ($outrasfontes == 'C') {
            $sql .= "SUM(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS SOMAVlSolicitado
                       ,SUM((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario)) AS SOMAVlReduzidoParecerista
		       ,SUM(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) AS VlSugeridoParecerista
		       ,SUM((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario)) AS SOMAVlReduzidoConselheiro
		       ,SUM(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro";
        }
        $sql .=" FROM SAC.dbo.Projetos PRO
		 ,SAC.dbo.tbPlanilhaProjeto PPJ
		 INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
		 INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
		 WHERE PAP.IdPRONAC = PRO.IdPRONAC
		 AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
		 AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)";
        if ($outrasfontes == 'S') {
            $sql .=" AND PP.FonteRecurso <> 109";
        }
        if ($outrasfontes == 'C') {
            $sql .=" AND PP.FonteRecurso = 109";
        }
        if ($elaboracao=='S') {
            $sql .= " AND PP.idPlanilhaItem <> '206' and PP.idPlanilhaItem <> '1238' ";
        }
        if ($elaboracao=='C') {
            $sql .= " AND PP.idPlanilhaItem = '206' and PP.idPlanilhaItem = '1238' ";
        }
        $sql .=" AND PAP.tpPlanilha = 'CO'
                 AND PRO.IdPRONAC= ".$idPronac;
        // busca de acordo com o pronac
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } 

    /**
     * M�todo que busca as informa��es da an�lise de conte�do
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function analiseDeConteudo($idPronac = null, $tpAnalise=null)
    {
        $sql = "SELECT AP.idAnaliseAprovacao
        			,PROJ.AnoProjeto+Sequencial as pronac
					,AP.idAnaliseConteudo
					,AP.IdPRONAC
					,AP.idProduto
                    ,PROD.descricao as produto
					,AP.stLei8313
					,AP.stArtigo3
					,AP.nrIncisoArtigo3
					,AP.dsAlineaArt3
					,AP.stArtigo18
					,AP.dsAlineaArtigo18
					,AP.stArtigo26
					,AP.stLei5761
					,AP.stArtigo27
					,AP.stIncisoArtigo27_I
					,AP.stIncisoArtigo27_II
					,AP.stIncisoArtigo27_III
					,AP.stIncisoArtigo27_IV
					,AP.stAvaliacao
					,AP.dsAvaliacao as dsAvaliacao
					,AP.idAgente
					,AC.TipoParecer
					,AC.idAnaliseDeConteudo
					,AC.idPRONAC AS IdPRONAC_Antigo
					,AC.idProduto AS idProduto_Antigo
					,AC.Lei8313 AS stLei8313_Antigo
					,AC.Artigo3 AS stArtigo3_Antigo
					,AC.IncisoArtigo3 AS nrIncisoArtigo3_Antigo
					,AC.AlineaArtigo3 AS dsAlineaArt3_Antigo
					,AC.Artigo18 AS stArtigo18_Antigo
					,AC.AlineaArtigo18 AS dsAlineaArtigo18_Antigo
					,AC.Artigo26 AS stArtigo26_Antigo
					,AC.Lei5761 AS stLei5761_Antigo
					,AC.Artigo27 AS stArtigo27_Antigo
					,AC.IncisoArtigo27_I AS stIncisoArtigo27_I_Antigo
					,AC.IncisoArtigo27_II AS stIncisoArtigo27_II_Antigo
					,AC.IncisoArtigo27_III AS stIncisoArtigo27_III_Antigo
					,AC.IncisoArtigo27_IV AS stIncisoArtigo27_IV_Antigo
					,AC.ParecerFavoravel AS stAvaliacao_Antigo
					,AC.ParecerDeConteudo AS dsAvaliacao_Antigo
					,AC.idUsuario AS idAgente_Antigo
					,PROJ.NomeProjeto
					,PROD.Descricao AS DescricaoProduto
					,PDP.stPrincipal
				FROM SAC.dbo.tbAnaliseAprovacao AP
				INNER JOIN SAC.dbo.tbAnaliseDeConteudo AC ON AC.idAnaliseDeConteudo = AP.idAnaliseConteudo
				INNER JOIN SAC.dbo.Produto PROD ON PROD.Codigo  = AP.idProduto
				INNER JOIN SAC.dbo.Projetos PROJ ON AC.idPronac = PROJ.IdPRONAC AND PROD.Codigo  = AC.idProduto
				INNER JOIN SAC.dbo.PlanoDistribuicaoProduto PDP on PDP.idProjeto = PROJ.idProjeto  and PDP.idProduto = AP.idProduto AND PDP.stPlanoDistribuicaoProduto = 1 ";

        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " WHERE AP. tpAnalise = '$tpAnalise' and AC.idPronac = $idPronac";
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } 

    /**
     * M�todo que busca os produtos dos projetos da an�lise de custos
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function JustificativaComponente($idpronac)
    {
        $sql = "select dsAnalise from BDCORPORATIVO.scSAC.tbPauta where idpronac = $idpronac ";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);
        return $resultado;
    }

    /**
     * M�todo que busca os produtos dos projetos da an�lise de custos
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */

    public static function analiseDeCustosBuscarProduto($idPronac)
    {
        $sql = "SELECT DISTINCT PD.Descricao,
					CASE
						WHEN PAP.idProduto = 0
							THEN 'Administra��o do Projeto'
							ELSE PD.Descricao
						END AS Produto
					,PAP.IdPRONAC
					,PAP.idProduto

				FROM SAC.dbo.Projetos PRO
					 JOIN SAC.dbo.tbPlanilhaProjeto PPJ on PPJ.idPRONAC = PRO.IdPRONAC
					 JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					 JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					 JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					 JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
					 LEFT JOIN SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)

				WHERE PAP.IdPRONAC = PRO.IdPRONAC
					AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND PP.FonteRecurso = 109
					AND PAP.tpPlanilha = 'CO'
                                        AND I.idplanilhaitens <> 206
                                        AND I.idplanilhaitens <> 1238";

        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }
        $sql.= " ORDER BY PD.Descricao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } 

    /**
     * M�todo que busca as etapas dos projetos da an�lise de custos
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function analiseDeCustosBuscarEtapa($idPronac, $idProduto = null, $buscarPorProduto = null)
    {
        $sql = "SELECT DISTINCT PP.idEtapa AS idEtapa
					,PAP.IdPRONAC
					,PAP.idProduto

				FROM SAC.dbo.Projetos PRO
					,SAC.dbo.tbPlanilhaProjeto PPJ
					 INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					 INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
					 left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)

				WHERE PAP.IdPRONAC = PRO.IdPRONAC
					AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND PP.FonteRecurso = 109
					AND PAP.tpPlanilha = 'CO'
						AND I.idplanilhaitens <> 206
                        AND I.idplanilhaitens <> 1238";

        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }
        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND PAP.idProduto = $idProduto";
        }

        $sql.= " ORDER BY PP.idEtapa";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    /**
     * M�todo que busca os estados dos projetos da an�lise de custos
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function analiseDeCustosBuscarUF($idPronac, $idProduto = null, $idEtapa = null, $buscarPorProduto = null)
    {
        $sql = "SELECT DISTINCT PAP.idUFDespesa AS idUFDespesa
					,PAP.IdPRONAC
					,PAP.idProduto
					,PP.idEtapa
					,PAP.idMunicipioDespesa

				FROM SAC.dbo.Projetos PRO
					,SAC.dbo.tbPlanilhaProjeto PPJ
					 INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					 INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
					 left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)

				WHERE PAP.IdPRONAC = PRO.IdPRONAC
					AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND PP.FonteRecurso = 109
					AND PAP.tpPlanilha = 'CO'

						AND I.idplanilhaitens <> 206
                        AND I.idplanilhaitens <> 1238";

        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }
        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND PAP.idProduto = $idProduto";
        }
        if (!empty($idEtapa)) {
            $sql.= " AND PAP.idEtapa = $idEtapa";
        }

        $sql.= " ORDER BY PAP.idUFDespesa";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } 

    /**
     * M�todo que emite parecer (grava na tabela de aprova��o)
     * @access public
     * @static
     * @param array $dados
     * @return object
     */
    public static function inserirAprovacao($valores)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("SAC.dbo.Aprovacao", $valores);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * M�todo que verifica a tabela aprovacao
     * @access public
     * @static
     * @param $idpronac
     * @return object
     */
    public static function verificaraprovacao($idpronac)
    {
        $sql = "SELECT 1 FROM SAC.dbo.aprovacao WHERE idpronac = $idpronac";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /**
     * M�todo que verifica a tabela aprovacao
     * @access public
     * @static
     * @param $idpronac
     * @return object
     */
    public static function alteraraprovacao($dados, $idpronac)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = " idpronac = ".$idpronac;
        $alterar = $db->update("sac.dbo.aprovacao", $dados, $where);
        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * M�todo que busca a reuni�o aberta
     * @access public
     * @static
     * @return object
     */
    public static function buscarUltimaReuniao()
    {
        $sql = "SELECT * FROM SAC.dbo.tbReuniao WHERE stEstado = 0";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha buscarUltimaReuniao()



    /**
     * M�todo que insere o projeto na pauta
     * @access public
     * @param array $dados
     * @static
     * @return object
     */
    public static function cadastrarSubmeterCNIC($dados)
    {
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbPauta", $dados);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * M�todo que altera o projeto na pauta
     * @access public
     * @param array $dados
     * @param integer $idPronac
     * @pram integer $idReuniao
     * @static
     * @return object
     */
    public static function alterarSubmeterCNIC($dados, $idPronac, $idnrreuniao=null)
    {
        $where   = "IdPRONAC = $idPronac";
        if (!empty($idnrreuniao)) {
            $where .= " and idnrreuniao=".$idnrreuniao;
        }
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $alterar = $db->update("BDCORPORATIVO.scSAC.tbPauta", $dados, $where);
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
            return false;
        }
    }

    /**
     * M�todo que altera o projeto na pauta
     * @access public
     * @param array $dados
     * @static
     * @return object
     */
    public static function submeterCnic($idPronac, $idReuniao, $justificativa)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPauta
				SET dsAnalise = '$justificativa',
					stEnvioPlenario = 'S'
				WHERE IdPRONAC = $idPronac AND idNrReuniao = $idReuniao";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $alterar = $db->fetchAll($sql);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * M�todo que busca os projetos em pauta
     * @access public
     * @param integer $idPronac
     * @param integer $idReuniao
     * @static
     * @return object
     */
    public static function retornaRegistro($idPronac, $idReuniao)
    {
        $sql = "SELECT idPRONAC, idNrReuniao, stEnvioPlenario FROM BDCORPORATIVO.scSAC.tbPauta WHERE idPRONAC = $idPronac AND idNrReuniao = $idReuniao";
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function retornaIndeferimento()
    {
        $sql = "select * from SAC.dbo.Situacao where Codigo in ('A13', 'A14', 'A16', 'A17', 'A20', 'A23', 'A24', 'D14','A41') and StatusProjeto <> 0";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function updateSituacao($idPronac, $situacao)
    {
        $where   = "IdPRONAC = $idPronac";
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $dados = array('situacao'=>$situacao);

            $alterar = $db->update("sac.dbo.projetos", $dados, $where);
            return true;
        } catch (Exception $e) {
            parent::message("Error: ".$e->getMessage(), "realizaranaliseprojeto/emitirparecer?idPronac=".$idPronac, "CONFIRM");
            return false;
        }
    } // fech

    public static function outrasinformacoes($idpronac)
    {
        $sql = " SELECT p.idPronac,
	      				  p.idProjeto,
	      				  p.AnoProjeto+p.Sequencial as Pronac,
	      				  ResumoProjeto,
	      				  Objetivos,
	      				  Justificativa,
	      				  Acessibilidade,
						  DemocratizacaoDeAcesso,
						  EtapaDeTrabalho,
						  FichaTecnica,
						  Sinopse,
	                      EstrategiaDeExecucao,
	                      ImpactoAmbiental,
	                      EspecificacaoTecnica,
	                      NrAtoTombamento,
	                      DtAtoTombamento,
	                      EsferaTombamento
	                FROM  SAC.dbo.PreProjeto pr
	                      INNER JOIN SAC.dbo.Projetos p on (pr.idPreProjeto = p.idProjeto)
	                where p.idPronac =$idpronac";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchRow($sql);
    } 

    public static function localrealizacao($idpronac)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        $select = $table->select()
            ->from(
                'Abrangencia',
                array('idPais'),
                'SAC.dbo'
            )
            ->where('Projetos.IdPRONAC = ? AND Abrangencia.stAbrangencia = \'1\'', $idpronac)
            ->joinInner(
                'PreProjeto',
                'Abrangencia.idProjeto = PreProjeto.idPreProjeto',
                array(new Zend_Db_Expr('
                    CONVERT(CHAR(10), PreProjeto.DtInicioDeExecucao, 103) AS DtInicioDeExecucao,
                    CONVERT(CHAR(10), PreProjeto.DtFinalDeExecucao, 103) AS DtFinalDeExecucao
                ')),
                'SAC.dbo'
            )
            ->joinInner(
                'Projetos',
                'PreProjeto.idPreProjeto = Projetos.idProjeto',
                array(''),
                'SAC.dbo'
            )
            ->joinLeft(
                'Pais',
                'Abrangencia.idPais = Pais.idPais',
                array('Descricao'),
                'agentes.dbo'
            )
            ->joinLeft(
                'Uf',
                'Abrangencia.idUF= Uf.idUF',
                array(new Zend_Db_Expr('
                    Uf.Descricao AS UF
                ')),
                'agentes.dbo'
            )
            ->joinLeft(
                'Municipios',
                'Abrangencia.idMunicipioIBGE = Municipios.idMunicipioIBGE',
                array(new Zend_Db_Expr('
                    Municipios.Descricao AS Cidade
                ')),
                'agentes.dbo'
                );

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            return $db->fetchAll($select);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
    } 

    public static function deslocamento($pronac)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        $select = $table->select()
            ->from(
                'tbDeslocamento',
                array('idDeslocamento','idProjeto','Qtde'),
                'SAC.dbo'
            )
            ->where('Projetos.IdPRONAC = ?', $pronac)
            ->joinInner(
                'Projetos',
                'tbDeslocamento.idProjeto = Projetos.idProjeto',
                array(''),
                'SAC.dbo'
            )
            ->joinInner(
                'Pais',
                'tbDeslocamento.idPaisOrigem = Pais.idPais',
                array(new Zend_Db_Expr('Pais.Descricao AS PaisOrigem')),
                'Agentes.dbo'
            )
            ->joinInner(
                'uf',
                'tbDeslocamento.idUFOrigem = uf.iduf',
                array(new Zend_Db_Expr('uf.Descricao AS UFOrigem')),
                'Agentes.dbo'
            )
            ->joinInner(
                'Municipios',
                'tbDeslocamento.idMunicipioOrigem = Municipios.idMunicipioIBGE',
                array(new Zend_Db_Expr('Municipios.Descricao AS MunicipioOrigem')),
                'Agentes.dbo'
            )
            ->joinInner(
                'Pais',
                'tbDeslocamento.idPaisDestino = Pais_2.idPais',
                array(new Zend_Db_Expr('Pais_2.Descricao AS PaisDestino')),
                'Agentes.dbo'
            )
            ->joinInner(
                'uf',
                'tbDeslocamento.idUFDestino = uf_2.iduf',
                array(new Zend_Db_Expr('uf_2.Descricao AS UFDestino')),
                'Agentes.dbo'
            )
            ->joinInner(
                'Municipios',
                'tbDeslocamento.idMunicipioDestino = Municipios_2.idMunicipioIBGE',
                array(new Zend_Db_Expr('Municipios_2.Descricao AS MunicipioDestino')),
                'Agentes.dbo'
            );


        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            return $db->fetchAll($select);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
    }

    public static function divulgacao($pronac)
    {
        $table = Zend_Db_Table::getDefaultAdapter();
        $select = $table->select()
        ->from(
            'PlanoDeDivulgacao',
            array(''),
            'SAC.dbo'
        )
        ->where('Projetos.IdPRONAC = ? AND PlanoDeDivulgacao.stPlanoDivulgacao = 1 ', $pronac)
        ->joinInner(
            'Projetos',
            'PlanoDeDivulgacao.idProjeto = Projetos.idProjeto',
            array(''),
            'SAC.dbo'
        )
        ->joinInner(
            'Verificacao',
            'PlanoDeDivulgacao.idPeca = Verificacao.idVerificacao',
            array(new Zend_Db_Expr('Verificacao.Descricao AS Peca')),
            'SAC.dbo'
        )
        ->joinInner(
            'Verificacao',
            'PlanoDeDivulgacao.idVeiculo = Verificacao_2.idVerificacao',
            array(new Zend_Db_Expr('Verificacao_2.Descricao AS Veiculo')),
            'SAC.dbo'
        )
         ->order('Peca ASC')
         ->order('Veiculo ASC');

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            return $db->fetchAll($select);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
    }


    public static function divulgacaoProjetos($pronac)
    {
        $sql = "
		    SELECT DISTINCT v.idVerificacao as idVeiculo, v.Descricao as Veiculo
                    FROM sac.dbo.PlanoDeDivulgacao d
                    INNEr JOIN sac.dbo.Projetos p on (d.idProjeto = p.idProjeto)
                    INNER JOIN sac.dbo.Verificacao v on (d.idVeiculo = v.idVerificacao)
                    WHERE p.IdPRONAC ='$pronac' AND d.stPlanoDivulgacao = 1";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function divulgacaoProjetosGeral($pronac)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        $select = $table->select()
        ->from(
            array('d' => 'PlanoDeDivulgacao'),
            array('idPlanoDivulgacao', 'idPeca'),
            'SAC.dbo'
        )
        ->joinInner(
            array('p' => 'Projetos'),
            'd.idProjeto = p.idProjeto',
            array(''),
            'SAC.dbo'
        )
        ->joinInner(
            array('v' => 'Verificacao'),
            'd.idVeiculo = v.idVerificacao',
            array(new Zend_Db_Expr('v.idVerificacao AS idVeiculo'), new Zend_Db_Expr('v.Descricao AS Veiculo')),
            'SAC.dbo'
        )
        ->joinInner(
            array('v2' => 'Verificacao'),
            'd.idPeca = v2.idVerificacao',
            array(new Zend_Db_Expr('v2.Descricao AS Peca')),
            'SAC.dbo'
        )
        ->joinLeft(
            array('logo' => 'tbLogomarca'),
            'logo.idPlanoDivulgacao = d.idPlanoDivulgacao',
            array('dsPosicao'),
            'SAC.dbo'
        )
        ->joinLeft(
            array('doc' => 'tbDocumento'),
            'doc.idDocumento = logo.idDocumento',
            array('idArquivo'),
            'BDCORPORATIVO.scCorp'
        )
        ->joinLeft(
            array('arq' => 'tbArquivo'),
            'arq.idArquivo = doc.idArquivo',
            array('nmArquivo'),
            'BDCORPORATIVO.scCorp'
        )
        ->where('p.IdPRONAC = ?', $pronac)
        ->where('d.stPlanoDivulgacao = 1');


        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($select);
    }

    public static function divulgacaoProjetosGeral2($pronac)
    {
        $sql = "
		    SELECT d.idPlanoDivulgacao, v.idVerificacao as idVeiculo, v.Descricao as Veiculo, d.idPeca, v2.Descricao as Peca
                    FROM sac.dbo.PlanoDeDivulgacao d
                    INNER JOIN sac.dbo.Projetos p on (d.idProjeto = p.idProjeto)
                    INNER JOIN sac.dbo.Verificacao v on (d.idVeiculo = v.idVerificacao)
                    INNER JOIN sac.dbo.Verificacao v2 on (d.idPeca = v2.idVerificacao)
                    WHERE p.IdPRONAC = '$pronac' AND d.stPlanoDivulgacao = 1";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
    }

    public static function divulgacaoProjetosCadastrados($pronac)
    {
        $sql = "
		    select d.idPosicaoDaLogo, g.Descricao as PosicaoLogo, e.*, f.* from SAC.dbo.Projetos as a
                    inner join SAC.dbo.tbRelatorio as b on b.idPRONAC = a.IdPRONAC
                    inner join SAC.dbo.tbRelatorioTrimestral as c on c.idRelatorio = b.idRelatorio
                    inner join SAC.dbo.PlanoDistribuicaoProduto as d on d.idProjeto = a.idProjeto
                    inner join SAC.dbo.tbDistribuicaoProduto as e on e.idPlanoDistribuicao = d.idPlanoDistribuicao
                    inner join SAC.dbo.tbBeneficiario as f on f.idBeneficiario = c.idBeneficiario
                    left join SAC.dbo.Verificacao as g on g.idVerificacao = d.idPosicaoDaLogo
                    where a.idPRONAC = '$pronac' AND d.stPlanoDistribuicaoProduto = 1 ";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
    }


    public static function planodedistribuicao($pronac, $idproduto=null)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        $sql = $table->select()
        ->from(
            array('x' =>'PlanoDistribuicaoProduto'),
            array(new Zend_Db_Expr('idPlanoDistribuicao'),'idProjeto','idProduto','stPrincipal','QtdeProduzida','QtdeProponente','QtdeVendaNormal','QtdePatrocinador','QtdeOutros', 'QtdeVendaPromocional','PrecoUnitarioNormal','PrecoUnitarioPromocional',
                new Zend_Db_Expr('
                 QtdeVendaNormal*PrecoUnitarioNormal as ReceitaNormal,
                 QtdeVendaPromocional*PrecoUnitarioPromocional as ReceitaPro,
                 (QtdeVendaNormal*PrecoUnitarioNormal)
                 +(QtdeVendaPromocional*PrecoUnitarioPromocional) as ReceitaPrevista,
                 a.Codigo as idArea,
                 b.Codigo as idSegmento
                ')),
            'SAC.dbo'
        )
        ->joinInner(
            array('y' => 'Projetos'),
             'x.idProjeto = y.idProjeto',
             array('Localizacao'),
             'SAC.dbo'
        )
        ->joinInner(
            array('p' => 'Produto'),
            'x.idProduto = p.Codigo',
            array(new Zend_Db_Expr('p.Descricao AS Produto')),
            'SAC.dbo'
        )
        ->joinInner(
            array('a' => 'Area'),
            'x.Area = a.Codigo',
            array(new Zend_Db_Expr('a.Descricao AS Area'),new Zend_Db_Expr('b.Descricao AS Segmento')),
            'SAC.dbo'
        )
        ->joinInner(
            array('b' => 'Segmento'),
            'x.Segmento = b.Codigo',
            array(''),
            'SAC.dbo'
        )
        ->joinLeft(
            array('v' => 'Verificacao'),
            'x.idPosicaoDaLogo = v.idVerificacao',
            array(new Zend_Db_Expr('v.Descricao AS PosicaoDaLogo')),
            'SAC.dbo'
        )
        ->where('y.IdPRONAC = ?', $pronac)
        ->where('x.stPlanoDistribuicaoProduto = 1');


        if ($idproduto) {
            $sql .= sprintf(' AND x.idProduto = %d', $idproduto);
        }
        $sql .= " order by x.stPrincipal DESC";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
    }

    public static function documentosanexados($idpronac) // anexado pelo proponente
    {
        $sql = "
					  SELECT CodigoDocumento,
					         Descricao,
					         'Anexado pelo Proponente' as Classificacao,
					         idAgente as Codigo,CONVERT(CHAR(10), Data,103) + ' ' + CONVERT(CHAR(8), Data,108) AS Data,
					         NoArquivo,
					         TaArquivo,
					         idDocumentosAgentes,'tbDocumentosAgentes' as Tabela
					  FROM SAC.dbo.tbDocumentosAgentes d
					  INNER JOIN SAC.dbo.DocumentosExigidos e on (d.CodigoDocumento = e.Codigo)
					  --WHERE d.idPRONAC = $idpronac";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    } // fecha class



    public static function documentosanexadosproponente($idpronac) // anexado pelo proponente
    {
        $sql = "SELECT NoArquivo AS nmArquivo,
						imDocumento AS biArquivo
					  FROM SAC.dbo.tbDocumentosPreProjeto d
					  INNER JOIN SAC.dbo.DocumentosExigidos e on (d.CodigoDocumento = e.Codigo)
					  WHERE idDocumentosPreprojetos =  $idpronac
	      ";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    } // fecha class



    public static function documentosanexadosminc($idpronac) // anexado pelo minc
    {
        $sql = "
					  SELECT d.idTipoDocumento,
					         e.dsTipoDocumento as Descricao,
					         'Anexado no MinC' as Classificacao,
					         idPronac as Codigo,
					         dtDocumento,
					         NoArquivo,
					         TaArquivo,
					         d.idDocumento,
					         'tbDocumento' as Tabela
					  FROM SAC.dbo.tbDocumento d
					  INNER JOIN SAC.dbo.tbTipoDocumento e on (d.idTipoDocumento = e.idTipoDocumento)
					  WHERE idPronac = $idpronac
					 -- ORDER BY Data

	      ";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    } 

    /**
     * Planilha de Or�amento
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function planilhaOrcamento($idPronac = null, $idProduto = null, $Etapa = null, $UF = null, $Cidade = null, $buscarPorProduto = null)
    {
        $sql = "SELECT
			    idPronac,
			    AnoProjeto,
			    Sequencial,
			    NomeProjeto, idPlanilhaProposta,
			    idProduto,
			    Produto,
			    Etapa,
			    Item,
			    Unidade,
			    Quantidade,
			    Ocorrencia,
			    ValorUnitario,
			    (Quantidade * Ocorrencia * ValorUnitario) AS VlSolicitado,
			    VlTotal,
			    QtdeDias,
			    TipoDespesa,
			    TipoPessoa,
			    Contrapartida,
			    FonteRecurso,
			    UF,
			    Municipio,
			    Justificativa

			FROM
			    SAC.dbo.vwOrcamentoSolicitado";


        $sql.= " WHERE idPronac = $idPronac";

        // busca de acordo com o produto
        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND idProduto = $idProduto";
        }
        // busca de acordo com a etapa
        if (!empty($Etapa)) {
            $sql.= " AND Etapa = '$Etapa'";
        }
        // busca de acordo com a uf
        if (!empty($UF)) {
            $sql.= " AND UF = '$UF'";
        }
        // busca de acordo com a cidade
        if (!empty($Cidade)) {
            $sql.= " AND Municipio = '$Cidade'";
        }

        $sql.= " ORDER BY Item ASC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    /**
     * M�todo que busca os produtos da planilha or�amento
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function planilhaOrcamentoBuscarProduto($idPronac)
    {
        $sql = "SELECT DISTINCT Produto AS Produto,
				    idPronac,
				    idProduto

				FROM
				    SAC.dbo.vwOrcamentoSolicitado
				WHERE idPronac = $idPronac
				ORDER BY Produto";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    /**
     * M�todo que busca as etapas da planilhaOrcamento
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function planilhaOrcamentoBuscarEtapa($idPronac, $idProduto = null, $buscarPorProduto = null)
    {
        $sql = "SELECT DISTINCT Etapa AS Etapa,
			    idPronac,
			    idProduto

			FROM
			    SAC.dbo.vwOrcamentoSolicitado";


        $sql.= " WHERE idPronac = $idPronac";

        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND idProduto = $idProduto";
        }

        $sql.= " ORDER BY Etapa";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } 

    /**
     * M�todo que busca os estados da planilhaOrcamento
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function planilhaOrcamentoBuscarUF($idPronac, $idProduto = null, $Etapa = null, $buscarPorProduto = null)
    {
        $sql = "SELECT DISTINCT UF AS UF, Municipio,
			    idPronac,
			    idProduto,
			    Etapa

			FROM
			    SAC.dbo.vwOrcamentoSolicitado";


        $sql.= " WHERE idPronac = $idPronac";

        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND idProduto = $idProduto";
        }
        if (!empty($Etapa)) {
            $sql.= " AND Etapa = '$Etapa'";
        }

        $sql.= " ORDER BY UF";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } 

    /**
     * M�todo que busca o valor total de custo administrativo
     * @access public
     * @static
     * @return object
     */
    public static function ValorTotalAdministrativo($idPronac = null, $tpAprovacao=null)
    {
        $sql = "select sum((qtItem * nrOcorrencia * vlUnitario)) as valorTotaladministrativo from SAC.dbo.tbPlanilhaAprovacao
        where tpPlanilha = '$tpAprovacao'
        and idproduto=0
        and idetapa = 4
        and idplanilhaitem not in (5249, 206, 1238)
        and NrFonteRecurso = 109";

        if ($idPronac) {
            $sql .= " and idpronac = $idPronac";
        }
        //die($sql);
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);
        return $resultado;
    }

    /**
     * M�todo que busca a planilha de an�lise de custos
     * @access public
     * @static
     * @return object
     */
    public static function analiseDeCustos($idPronac = null, $tpPlanilha = null)
    {
        $sql = "SELECT    distinct PAP.idProduto,
					PD.Descricao
					,CASE
						WHEN PAP.idProduto = 0
							THEN 'Administra��o do Projeto'
							ELSE PD.Descricao
						END AS Produto
					,PAP.qtItem AS quantidade_con
                                        ,PRO.idpronac
                                        ,PRO.AnoProjeto+Sequencial as pronac
                                        ,PAP.idPlanilhaAprovacao
					,PAP.qtDias AS dias_con
                                        ,PAP.nrFonteRecurso
					,PAP.nrOcorrencia AS ocorrencia_con
					,PAP.vlUnitario AS valorUnitario_con
					,PAP.idPlanilhaAprovacao
					,PAP.idPlanilhaProjeto
					,PAP.idPlanilhaProposta
					,PAP.IdPRONAC
					,PAP.idProduto
					,PAP.idUnidade
                                        ,PAP.idPlanilhaItem
					,UNI.Descricao AS Unidade
					,PRO.NomeProjeto
					,PAP.idEtapa
                                        ,PP.dsJustificativa as justificitivaproponente
					,E.Descricao AS Etapa
					,I.Descricao AS Item
					,(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS VlSolicitado
					,((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario)) AS VlReduzidoParecerista
					,(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) AS VlSugeridoParecerista
					,CAST(PPJ.Justificativa  as TEXT) as dsJustificativaParecerista
					,((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario)) AS VlReduzidoConselheiro
					,(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro
					,CAST(PAP.dsJustificativa as TEXT) as dsJustificativaConselheiro
					,CASE
						WHEN (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) - (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) = 0
							THEN 'Item Retirado'
							ELSE 'Item Reduzido'
						END AS Situacao
					,UF.idUF
					,UF.Sigla AS UF
					,CID.idMunicipioIBGE AS idCidade
					,CID.Descricao AS Cidade
					,LTRIM(RTRIM(TI.Descricao)) as FonteRecurso
				FROM SAC.dbo.Projetos PRO
					INNER JOIN SAC.dbo.tbPlanilhaProjeto PPJ on PPJ.idPRONAC = PRO.IdPRONAC
					INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
					INNER JOIN AGENTES.dbo.UF UF on (PAP.idUFDespesa = UF.idUF)
					INNER JOIN AGENTES.dbo.Municipios CID on (PAP.idMunicipioDespesa = CID.idMunicipioIBGE)
					INNER JOIN SAC.dbo.tbPlanilhaEtapa E on (PAP.idEtapa = E.idPlanilhaEtapa)
					INNER JOIN SAC.dbo.tbPlanilhaUnidade UNI on (PAP.idUnidade = UNI.idUnidade)
					left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)
					inner join SAC.dbo.Verificacao TI on TI.idverificacao = PAP.nrFonteRecurso and TI.idTipo = 5
                                        inner join SAC.dbo.tbAnaliseAprovacao ap on ap.idpronac = pro.idpronac and tpAnalise='$tpPlanilha'

				WHERE --(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					--AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					--PAP.nrFonteRecurso <> 109
                                          ap.stAvaliacao = 1
					 AND PAP.tpPlanilha = '$tpPlanilha'
                                        AND PAP.idPlanilhaItem not in (206)
 ";

        if (!empty($idPronac)) {
            $sql.= "AND PRO.idpronac = $idPronac ";
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } 
}
