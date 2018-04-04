<?php
class GerenciarPareceresDAO extends Zend_Db_Table
{
    public static function projetosConsolidados($idPronac =  null, $pronac = null, $nometc = null, $nomeP = null, $dtI = null, $dtF = null, $sutuacaotc = null, $situacao = null, $idSecretaria = null)
    {
        $sql = "SELECT    p.IdPRONAC, p.AnoProjeto + p.Sequencial AS PRONAC, p.NomeProjeto, p.Situacao AS CodSituacao, s.Descricao AS Situacao, o.idSecretaria, 
				          pa.idParecer, CONVERT(CHAR(11),pa.DtParecer,103) + CONVERT(CHAR(20),pa.DtParecer,108) AS DtConsolidacao, m.ValorProposta, m.OutrasFontes, m.ValorSolicitado, m.ValorSugerido, m.Elaboracao, m.ValorParecer,
				          CASE WHEN ValorParecer > 0 THEN (Elaboracao / ValorParecer) * 100 ELSE 0 END AS PERC, 
				          CASE WHEN ValorParecer > 0 THEN CASE WHEN ((Elaboracao / ValorParecer) * 100) > 10 THEN 'Acima de 10%' ELSE '' END END AS Acima,
				          case 
							 when TipoParecer = '1' then 'Aprova&ccedil;&atilde;o' when TipoParecer = '2' then 'Complementa&ccedil;&atilde;o' when TipoParecer = '3' then 'Prorroga&ccedil;&atilde;o'
							 when TipoParecer = '4' then 'Redu&ccedil;&atilde;o'
						   end as TipoParecer,
						   case 
							 when ParecerFavoravel = '1' then 'N&atilde;o' when ParecerFavoravel = '2' then 'Sim' else 'Sim com restri��es' 
						   end as ParecerFavoravel,       
						   case 
							 when Enquadramento = '1' then 'Artigo 26' when Enquadramento = '2' then 'Artigo 18'
						   end as Enquadramento, 
						   CAST(ResumoParecer AS TEXT) AS ResumoParecer, 
						   SugeridoReal
				FROM      SAC.dbo.Projetos AS p 
							INNER JOIN SAC.dbo.Parecer AS pa ON p.IdPRONAC = pa.idPRONAC AND pa.TipoParecer = '1' 
				            LEFT OUTER JOIN SAC.dbo.vwMemoriaDeCalculo AS m ON p.IdPRONAC = m.idPronac 
				            INNER JOIN SAC.dbo.Situacao AS s ON p.Situacao = s.Codigo 
				            INNER JOIN SAC.dbo.Orgaos AS o ON p.Orgao = o.Codigo
				            LEFT JOIN SAC.dbo.Enquadramento e ON (p.idPronac = e.idPronac)
				WHERE     (p.Situacao IN ('C09', 'C20', 'C25')) AND (p.AnoProjeto > '08') AND (p.Mecanismo = '1') ";
        
        if ($idPronac) {
            $sql .= " AND p.IdPRONAC = ".$idPronac;
        }
                
        //Pronac **************************************************************
        if ($pronac) {
            $sql .= " AND (p.AnoProjeto + p.Sequencial) = '".$pronac."' ";
        }
        //*********************************************************************
                
                
        //Nome do Projeto *****************************************************
        if ($nomeP && $nometc == 1) {
            $sql .= " AND p.NomeProjeto like '".$nomeP."%' ";
        }
        if ($nomeP && $nometc == 2) {
            $sql .= " AND p.NomeProjeto like '%".$nomeP."%' ";
        }
        if ($nomeP && $nometc == 3) {
            $sql .= " AND p.NomeProjeto <> '".$nomeP."'";
        }
        //**********************************************************************
                
        //Data de consolida&ccedil;&atilde;o
        if (($dtI) && ($dtF == null)) {
            $sql .= " AND cast(convert(char(8),pa.DtParecer,112)as smalldatetime) = '".$dtI."'";
        }
        if ($dtI && $dtF) {
            $sql .= " AND cast(convert(char(8),pa.DtParecer,112)as smalldatetime) between '".$dtI."' AND '".$dtF."' ";
        }
        //**********************************************************************
                
        /* Situa&ccedil;&atilde;o ************************************************************
        * C09 - Projeto fora da pauta - Proponente Inabilitado
        * C20 - An�lise T�cnica Concluida
        * C25 - Parecer T�cnico desfavor�vel
        */
        if (($situacao) && ($sutuacaotc == 1)) {
            $sql .= " AND p.Situacao = '".$situacao."'";
        }
        if (($situacao) && ($sutuacaotc == 2)) {
            $sql .= " AND p.Situacao <> '".$situacao."'";
        }
        //**********************************************************************
                
                
        $sql .= " ORDER BY pa.DtParecer, PRONAC";
                   
        //die('<pre>'.$sql);
        
                
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    
    public static function projetosConsolidadosParte2($idPronac)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        $select = $table->select()
            ->from(
                array('a' => 'tbAnaliseDeConteudo'),
                array('idPRONAC', new Zend_Db_Expr('
					CASE
							 WHEN Artigo18 = 1
								  THEN \'Artigo 18\'
								  ELSE \'Artigo 26\'
							 END as Enquadramento,
						   CASE
							  WHEN IncisoArtigo27_I = 0
								   THEN \'N&atilde;o\'
								   ELSE \'Sim\'
							  END as IncisoArtigo27_I,
						   CASE
							  WHEN IncisoArtigo27_II = 0
								   THEN \'N&atilde;o\'
								   ELSE \'Sim\'
							  END as IncisoArtigo27_II,
						   CASE
							  WHEN IncisoArtigo27_III = 0
								   THEN \'N&atilde;o\'
								   ELSE \'Sim\'
							  END as IncisoArtigo27_III,
						   CASE
							  WHEN IncisoArtigo27_IV = 0
								   THEN \'N&atilde;o\'
								   ELSE \'Sim\'
							  END as IncisoArtigo27_IV
				')),
                'SAC.dbo'
            )
            ->joinInner(
                array('p' => 'Produto'),
                'a.idProduto = p.Codigo',
                array(new Zend_Db_Expr('p.Descricao as Produto')),
                'SAC.dbo'
            )
            ->where('idPronac = ?', $idPronac);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($select);
    }
    
    
    public static function buscarProdutosParaDevolver($idpronac)
    {
        $sql = "SELECT  t.stPrincipal, t.stEstado,  t.idDistribuirParecer, t.idOrgao, p.IdPRONAC, p.AnoProjeto + p.Sequencial AS PRONAC, p.NomeProjeto, t.idProduto, r.Descricao AS Produto, 
						   x.idSecretaria, a.Descricao as AreaD, s.Descricao as SegmentoD,
						   CASE WHEN TipoAnalise = 0 THEN 'Conte&uacute;do' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo do Projeto'
						   END AS DescricaoAnalise, t.TipoAnalise, CASE WHEN FecharAnalise = 1 THEN 'Conclu�do' ELSE 'Aguardando an�lise' END AS Estado, 
						   TABELAS.dbo.fnEstruturaOrgao(t.idOrgao, 0) AS Orgao
				FROM       SAC.dbo.tbDistribuirParecer AS t 
				INNER JOIN SAC.dbo.Projetos AS p ON t.idPRONAC = p.IdPRONAC 
				INNER JOIN SAC.dbo.Orgaos AS x ON p.Orgao = x.Codigo 
				INNER JOIN SAC.dbo.Produto AS r ON t.idProduto = r.Codigo
				INNER JOIN SAC.dbo.Area a ON A.Codigo = p.Area
				INNER JOIN SAC.dbo.Segmento s ON s.Codigo = p.Segmento 
				WHERE (t.stEstado = 0) AND p.IdPRONAC = ".$idpronac;
                
                
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        return $db->fetchAll($sql);
    }
    
    public static function historicoParecerProduto(
    
        $idPronac    = null,
                                                        $idProduto   = null,
                                                        $tipoanalise = null,
                                                        $produtotc   = null,
                                                        $produto     = null,
                                                        $orgaotc     = null,
                                                        $orgao 	     = null,
                                                        $unidadetc   = null,
                                                        $unidade     = null
    
    ) {
        $sql = "select idPronac,idProduto, Descricao as Produto,
				      	 case TipoAnalise when 0 then 'Conte&uacute;do' when 1 then 'Custo do Produto'
				         else 'Custo Administrativo' end as TipoAnalise
				        ,d.idOrgao,tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) as Unidade, 
                                        CONVERT(CHAR(11),DtEnvio,103) + CONVERT(CHAR(8),DtEnvio,108) AS DtEnvio ,
                                        CONVERT(CHAR(11),DtDevolucao,103) + CONVERT(CHAR(8),DtDevolucao,108) AS DtDevolucao ,
                                        Observacao,
				SAC.dbo.fnNomeUsuario(idUsuario) as Usuario
				from SAC.dbo.tbDistribuirParecer d
				inner join SAC.dbo.Produto p on (d.idProduto = p.Codigo)
				where idPronac <> ''";
                
        // idPronac **********************************************
        if ($idPronac != null) {
            $sql .=" AND idPronac=".$idPronac;
        }
                
        // idProduto *********************************************
        if ($idProduto != null) {
            $sql .= " AND idProduto = ".$idProduto;
        }
                
        // tipoanalise *******************************************
        if ($tipoanalise != null) {
            $sql .= " AND TipoAnalise = ".$tipoanalise;
        }
                
        // Produto ***********************************************
        if ($produto && $produtotc == 1) {
            $sql .= " AND Descricao = '".$produto."' ";
        }
        if ($produto && $produtotc == 2) {
            $sql .= " AND Descricao like '".$produto."%' ";
        }
        if ($produto && $produtotc == 3) {
            $sql .= " AND Descricao like '%".$produto."%' ";
        }
        if ($produto && $produtotc == 4) {
            $sql .= " AND Descricao <> '".$produto."'";
        }
                
                
        // orgao **********************************************
        if (($orgao != null) && ($orgaotc == 1)) {
            $sql .= " AND TipoAnalise = ".$orgao;
        }
        if (($orgao != null) && ($orgaotc == 2)) {
            $sql .= " AND TipoAnalise <> ".$orgao;
        }


        // Unidade ***********************************************
        if ($unidade && $unidadetc == 1) {
            $sql .= " AND tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) = '".$unidade."' ";
        }
        if ($unidade && $unidadetc == 2) {
            $sql .= " AND tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) like '".$unidade."%' ";
        }
        if ($unidade && $unidadetc == 3) {
            $sql .= " AND tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) like '%".$unidade."%' ";
        }
        if ($unidade && $unidadetc == 4) {
            $sql .= " AND tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) <> '".$unidade."' ";
        }
                                
                                
        $sql .=" ORDER BY idDistribuirParecer DESC";
        
        //die('<pre>'.$sql);
                
                
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    
    
    /* Desconsolidar ***********************************************************************************/

    public static function pareceresTecnicos($idpronac)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        $select = $table->select()
            ->from(
                array('p' => 'Projetos'),
                    array('IdPRONAC' , new Zend_Db_Expr('p.AnoProjeto + p.Sequencial AS NrProjeto') , 'NomeProjeto',
                        new Zend_Db_Expr(
                            ' CASE
                                    WHEN Lei8313 = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS Lei8313,
                              CASE
                                    WHEN Artigo3 = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS Artigo3,
                              CASE
                                    WHEN IncisoArtigo3 = 1 THEN \'I\'
                                    WHEN IncisoArtigo3 = 2 THEN \'II\'
                                    WHEN IncisoArtigo3 = 3 THEN \'III\'
                                    WHEN IncisoArtigo3 = 4 THEN \'IV\'
                                    WHEN IncisoArtigo3 = 5 THEN \'V\'
                              END AS IncisoArtigo3, a.AlineaArtigo3, CASE WHEN Artigo18 = 1 THEN \'Sim\' ELSE \'N&atilde;o\' END AS Artigo18, a.AlineaArtigo18,
                              CASE
                                    WHEN Artigo26 = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS Artigo26,
                              CASE
                                    WHEN Lei5761 = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS Lei5761,
                              CASE
                                    WHEN Artigo27 = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS Artigo27,
                              CASE
                                    WHEN IncisoArtigo27_I = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS IncisoArtigo27_I,
                              CASE
                                    WHEN IncisoArtigo27_II = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS IncisoArtigo27_II,
                              CASE
                                    WHEN IncisoArtigo27_III = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\' END AS IncisoArtigo27_III,
                              CASE
                                    WHEN IncisoArtigo27_IV = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                              END AS IncisoArtigo27_IV,
                              CASE WHEN TipoParecer = 1 THEN \'Aprova&ccedil;&atilde;o\'
                                    WHEN TipoParecer = 2 THEN \'Complementa&ccedil;&atilde;o\'
                                    WHEN TipoParecer = 4 THEN \'Redu&ccedil;&atilde;o\'
                              END AS TipoParecer,
                               CASE
                                    WHEN ParecerFavoravel = 1 THEN \'Sim\'
                                    ELSE \'N&atilde;o\'
                               END AS ParecerFavoravel,
                               SAC.dbo.fnNomeParecerista(a.idUsuario) AS Parecerista
                            '
                        )),
                    'SAC.dbo'
            )
            ->joinInner(
                array('i' => 'Interessado'),
                'p.CgcCpf = i.CgcCpf',
                array(new Zend_Db_Expr('i.Nome AS Proponente')),
                'SAC.dbo'
            )
            ->joinInner(
                array('a' => 'tbAnaliseDeConteudo'),
                'p.IdPRONAC = a.idPronac',
                array('a.ParecerDeConteudo','a.idProduto'),
                'SAC.dbo'
            )
            ->joinInner(
                array('pr' => 'Produto'),
                'a.idProduto = pr.Codigo',
                array(new Zend_Db_Expr('pr.Descricao AS Produto')),
                'SAC.dbo'
            )
            ->joinInner(
                array('pd' => 'PlanoDistribuicaoProduto'),
            'p.idProjeto = pd.idProjeto and pd.idProduto = pr.Codigo AND pd.stPlanoDistribuicaoProduto = 1',
            array('pd.stPrincipal'),
            'SAC.dbo'
            )
            ->where('a.idUsuario IS NOT NULL AND p.IdPRONAC = ?', $idpronac)
            ->order('pd.stPrincipal DESC');

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($select);
    }

    
    public static function emPauta($idpronac)
    {
        $sql = "SELECT AnoProjeto, Sequencial, Situacao 
					FROM SAC.dbo.Projetos p
					INNER JOIN SAC.dbo.Situacao s ON s.Codigo = p.Situacao
						WHERE idPronac = ".$idpronac." AND s.AreaAtuacao='R' and s.StatusProjeto='1'";
                
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function projetoAprovado($pronac)
    {
        $sql = "SELECT  TOP 1 * FROM SAC.dbo.Aprovacao WHERE TipoAprovacao = '1' and (AnoProjeto+Sequencial) = '".$pronac."'";
                
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    
    public static function produtoPrincipal($idPronac)
    {
        $sql = "SELECT     t.idDistribuirParecer, t.idOrgao, p.IdPRONAC, p.AnoProjeto + p.Sequencial AS PRONAC, p.NomeProjeto, t.stPrincipal ,t.idProduto, r.Descricao AS Produto, 
						   x.idSecretaria, a.Descricao as AreaD, s.Descricao as SegmentoD,
						   CASE WHEN TipoAnalise = 0 THEN 'Conte&uacute;do' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo do Projeto'
						   END AS DescricaoAnalise, t.TipoAnalise, CASE WHEN FecharAnalise = 1 THEN 'Conclu�do' ELSE 'Aguardando an�lise' END AS Estado, 
						   TABELAS.dbo.fnEstruturaOrgao(t.idOrgao, 0) AS Orgao
				FROM       SAC.dbo.tbDistribuirParecer AS t 
				INNER JOIN SAC.dbo.Projetos AS p ON t.idPRONAC = p.IdPRONAC 
				INNER JOIN SAC.dbo.Orgaos AS x ON p.Orgao = x.Codigo 
				INNER JOIN SAC.dbo.Produto AS r ON t.idProduto = r.Codigo
				INNER JOIN SAC.dbo.Area a ON A.Codigo = p.Area
				INNER JOIN SAC.dbo.Segmento s ON s.Codigo = p.Segmento
				
				WHERE p.IdPRONAC = ".$idPronac." AND t.stPrincipal = 1 AND t.stEstado = 0";
                
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function analiseDeCustos($idpronac)
    {
        $sql = "SELECT		a.IdPRONAC,
			a.AnoProjeto + a.Sequencial AS PRONAC,
			a.NomeProjeto,
			b.idProduto,
			b.idPlanilhaProjeto,
			PAP.dsJustificativa dsJustificativaConselheiro,
			(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro,
			CASE WHEN b.idProduto = 0 THEN 'Administra&ccedil;&atilde;o do Projeto' ELSE c.Descricao END AS Produto, CONVERT(varchar(8),
			d.idPlanilhaEtapa) + ' - ' + d.Descricao AS Etapa,
			d.idPlanilhaEtapa,
			i.Descricao AS Item,
			z.Quantidade * z.Ocorrencia * z.ValorUnitario AS VlSolicitado,
			e.Descricao AS Unidade,
			b.Quantidade,
			b.Ocorrencia,
			b.ValorUnitario,
			b.QtdeDias,
			b.TipoDespesa,
			b.TipoPessoa,
			b.Contrapartida,
			z.dsJustificativa as JustificativaProponente,
			b.FonteRecurso AS idFonte,
			x.Descricao AS FonteRecurso,
			f.UF,
			f.idUF,
			f.Municipio,
			f.idMunicipio,
			ROUND(b.Quantidade * b.Ocorrencia * b.ValorUnitario, 2) AS Sugerido,
			CAST(b.Justificativa AS TEXT) AS Justificativa,
			b.idUsuario
FROM         SAC.dbo.Projetos AS a
					  INNER JOIN SAC.dbo.tbPlanilhaProjeto AS b ON a.IdPRONAC = b.idPRONAC
					  INNER JOIN SAC.dbo.tbPlanilhaProposta AS z ON b.idPlanilhaProposta = z.idPlanilhaProposta
					  left JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = z.idPlanilhaProposta)
					  LEFT OUTER JOIN SAC.dbo.Produto AS c ON b.idProduto = c.Codigo
					  INNER JOIN SAC.dbo.tbPlanilhaEtapa AS d ON b.idEtapa = d.idPlanilhaEtapa
					  INNER JOIN SAC.dbo.tbPlanilhaUnidade AS e ON b.idUnidade = e.idUnidade
					  INNER JOIN SAC.dbo.tbPlanilhaItens AS i ON b.idPlanilhaItem = i.idPlanilhaItens
					  INNER JOIN SAC.dbo.Verificacao AS x ON b.FonteRecurso = x.idVerificacao
					  INNER JOIN AGENTES.dbo.vUFMunicipio AS f ON b.UfDespesa = f.idUF AND b.MunicipioDespesa = f.idMunicipio
					  WHERE a.IdPRONAC = ".$idpronac."
						ORDER BY x.Descricao, Produto, Etapa, UF, Item";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    
    

    public static function buscaDiligencias($idpronac)
    {
        $sql = "SELECT d.idDiligencia, 
					   v.Descricao, 
                                           CONVERT(CHAR(11),d.DtSolicitacao,103) + CONVERT(CHAR(8),d.DtSolicitacao,108) as DtSolicitacao,
					   d.Solicitacao,
                                           CONVERT(CHAR(11),d.DtResposta,103) + CONVERT(CHAR(8),d.DtResposta,108) as DtResposta,
					   d.Resposta, 
					   d.idSolicitante, 
					   d.idProponente
				FROM SAC.dbo.tbDiligencia d
				INNER JOIN SAC.dbo.Verificacao v ON d.idTipoDiligencia = v.idVerificacao
				WHERE idPronac = ".$idpronac;
                
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    
    public static function buscarUnidades($usu_codigo)
    {
        $sql = "SELECT usu_orgao
					,usu_orgaolotacao
					,uog_orgao
					,org_siglaautorizado
					,org_nomeautorizado
					,gru_codigo
					,gru_nome
					,org_superior
					,uog_status

				FROM TABELAS.dbo.vwUsuariosOrgaosGrupos

				WHERE sis_codigo = 21 AND uog_orgao = $usu_codigo ";
    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    

    /** ALTERA��ES *************************************************************************************/
    
    public static function devolverParecer($idpronac, $idproduto, $observacao, $tipoanalise, $idusuario, $idorgao)
    {
        $sql = "UPDATE SAC.dbo.tbDistribuirParecer SET FecharAnalise=0, Observacao = '".$observacao."', idUsuario = ".$idusuario.", idOrgao = ".$idorgao."  
					WHERE idpronac    = ".$idpronac." 
					AND   idproduto   = ".$idproduto." 
					AND   tipoanalise = ".$tipoanalise." 
					AND   stestado    = 0";
                    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }

    //-- Gravar o idParecer nas tabelas tbAnaliseDeConteudo
    public static function updatetbAnaliseDeConteudo($idpronac)
    {
        $sql = "UPDATE SAC.dbo.tbAnaliseDeConteudo SET idParecer = NULL WHERE idPronac = ".$idpronac." and idParecer is not null";
                    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }

    //-- Gravar o idParecer nas tabelas tbPlanilhaProjeto
    public static function updatetbPlanilhaProjeto($idpronac)
    {
        $sql = "UPDATE SAC.dbo.tbPlanilhaProjeto SET idParecer = NULL WHERE idPronac = ".$idpronac." and idParecer is not null";
                    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }
    
    //-- Alterar a situa&ccedil;&atilde;o do projeto
    public static function updateProjetos($idpronac)
    {
        $sql = "UPDATE SAC.dbo.Projetos SET Situacao = 'B11', 
						ProvidenciaTomada = 'Projeto encaminhado para novo parecer t�cnico na unidade de an�lise do MinC.', 
						DtSituacao = getdate() 
      			WHERE idPronac = ".$idpronac;
                    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }
            
    // Excluir o parecer t�cnico do projeto
    public static function delPerecer($idpronac)
    {
        $sql = "DELETE FROM SAC.dbo.Parecer WHERE TipoParecer = '1' and idPronac = ".$idpronac;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }

    // Excluir o enquadramento do projeto
    public static function delEnquadramento($idpronac)
    {
        $sql = "DELETE FROM SAC.dbo.Enquadramento WHERE idPronac = ".$idpronac;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }

    public static function execPareceres()
    {
        $sql = "EXEC SAC.dbo.paConsolidarParecer";
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }
}
