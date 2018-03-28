CREATE PROCEDURE dbo.spPlanilhaOrcamentaria (@idPronac int, @TipoPlanilha char(1))
AS  

SET NOCOUNT ON
-- =========================================================================================
-- VERIFICAR SE O PROJETO TEM APROVADO SOBRE A ÉGIDE DA IN 2017
-- =========================================================================================
IF (SELECT sac.dbo.fnVerificar_Projeto_Aprovado_IN2017(@idPronac)) = 1 AND
   NOT EXISTS(SELECT TOP 1 IdPRONAC FROM sac.dbo.tbPlanilhaAprovacao 
	                                     WHERE tpPlanilha = 'CO' AND IdPRONAC = @idPronac)
   BEGIN
	 SET @TipoPlanilha = 1
   END

-- =========================================================================================
-- PLANILHA ORÇAMENTÁRIA DA PROPOSTA
-- =========================================================================================
IF @TipoPlanilha = 0
   BEGIN
      SELECT a.idPreProjeto as idPronac,' ' AS PRONAC,a.NomeProjeto,
            b.idProduto,b.idPlanilhaProposta,
            CASE 
              WHEN idProduto = 0
                   THEN 'Administração do Projeto'
                   ELSE c.Descricao 
              END as Produto,
            b.idEtapa,d.Descricao as Etapa,
            i.Descricao as Item,e.Descricao as Unidade,b.Quantidade,b.Ocorrencia,b.ValorUnitario as vlUnitario,
            ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSolicitado,QtdeDias,x.Descricao as FonteRecurso,b.FonteRecurso as idFonte,
            f.UF,f.Municipio, convert(varchar(max),b.dsJustificativa) as JustProponente
            FROM PreProjeto a
            INNER JOIN tbPlanilhaProposta b on (a.idPreProjeto = b.idProjeto)
            LEFT JOIN Produto c on (b.idProduto = c.Codigo)
            INNER JOIN tbPlanilhaEtapa d on (b.idEtapa = d.idPlanilhaEtapa)
            INNER JOIN tbPlanilhaUnidade e on (b.Unidade = e.idUnidade)
            INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
            INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
            INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
            WHERE a.idPreProjeto = @idPronac
            ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
   END
ELSE
-- =========================================================================================
-- PLANILHA ORÇAMENTÁRIA DO PROPONENTE
-- =========================================================================================
IF @TipoPlanilha = 1
   BEGIN
     SELECT a.idPronac,a.AnoProjeto,a.Sequencial AS PRONAC,a.NomeProjeto,b.idProduto,b.idPlanilhaProposta,'PR',
            CASE 
              WHEN idProduto = 0
                   THEN 'Administração do Projeto'
                   ELSE c.Descricao 
              END as Produto,
            b.idEtapa,d.Descricao as Etapa,b.idPlanilhaItem,i.Descricao as Item,b.UfDespesa as idUF,b.MunicipioDespesa as idMunicipio,
            ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSolicitado,
			CONVERT(VARCHAR(MAX),b.dsJustificativa) as JustProponente,0 as vlSugerido,'' as JustParecerista,
             e.Descricao as Unidade,b.Quantidade as Quantidade,b.Ocorrencia as Ocorrencia,b.ValorUnitario,b.QtdeDias as QtdeDias,
             b.TipoDespesa as TpDespesa,b.TipoPessoa as TpPessoa,b.Contrapartida as nrContrapartida,b.FonteRecurso as idFonte,
             x.Descricao as FonteRecurso,f.UF,f.Municipio,
			ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlAprovado,
			 0 as vlComprovado,CONVERT(VARCHAR(MAX),b.dsJustificativa)  as JustComponente
            FROM Projetos                       a
            INNER JOIN tbPlanilhaProposta       b on (a.idProjeto      = b.idProjeto)
            LEFT JOIN Produto                   c on (b.idProduto      = c.Codigo)
            INNER JOIN tbPlanilhaEtapa  d on (b.idEtapa        = d.idPlanilhaEtapa)
            INNER JOIN tbPlanilhaUnidade        e on (b.Unidade        = e.idUnidade)
            INNER JOIN tbPlanilhaItens          i on (b.idPlanilhaItem = i.idPlanilhaItens)
            INNER JOIN Verificacao              x on (b.FonteRecurso   = x.idVerificacao)
            INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa      = f.idUF and b.MunicipioDespesa = f.idMunicipio)
            WHERE a.idPronac = @idPronac
            ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
   END
ELSE
-- =========================================================================================
-- PLANILHA ORÇAMENTÁRIA DO PARECERISTA
-- =========================================================================================
IF @TipoPlanilha = 2
   BEGIN 
      SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,b.idProduto,b.idPlanilhaProjeto,
             CASE 
               WHEN b.idProduto = 0
                    THEN 'Administração do Projeto'
                    ELSE c.Descricao 
               END as Produto,
             b.idEtapa,d.Descricao as Etapa,b.idPlanilhaItem,i.Descricao as Item,b.UfDespesa as idUF,b.MunicipioDespesa as idMunicipio,
             ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) as vlSolicitado,
             convert(varchar(max),z.dsJustificativa) as JustProponente,
             e.Descricao as Unidade,b.Quantidade,b.Ocorrencia,b.ValorUnitario as vlUnitario,b.QtdeDias,
             b.FonteRecurso as idFonte,x.Descricao as FonteRecurso,f.UF,f.Municipio,
             ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSugerido,convert(varchar(max),b.Justificativa) as JustParecerista,b.idUsuario
             FROM Projetos a
             INNER JOIN tbPlanilhaProjeto b on (a.idPronac = b.idPronac)
             INNER JOIN tbPlanilhaProposta z on (b.idPlanilhaProposta=z.idPlanilhaProposta)
             LEFT JOIN Produto c on (b.idProduto = c.Codigo)
             INNER JOIN tbPlanilhaEtapa d on (b.idEtapa = d.idPlanilhaEtapa)
             INNER JOIN tbPlanilhaUnidade e on (b.idUnidade = e.idUnidade)
             INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
             INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
             INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
             WHERE a.idPronac = @idPronac
             ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
    END
ELSE
-- =========================================================================================
-- PLANILHA ORÇAMENTÁRIA DO APROVADA
-- =========================================================================================
IF @TipoPlanilha = 3
   BEGIN 
      SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.tpPlanilha,
             CASE 
               WHEN k.idProduto = 0
                    THEN 'Administração do Projeto'
                    ELSE c.Descricao 
               END as Produto,
             b.idEtapa,d.Descricao as Etapa,k.idPlanilhaItem,i.Descricao as Item,k.idUfDespesa as idUF,k.idMunicipioDespesa as idMunicipio,
             ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) as vlSolicitado,convert(varchar(max),z.dsJustificativa) as JustProponente,
             ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSugerido,convert(varchar(max),b.Justificativa) as JustParecerista,
             e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,k.QtDias as QtdeDias,
             k.TpDespesa,k.TpPessoa,k.nrContrapartida,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,f.UF,f.Municipio,
             ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
             (SELECT SUM(b1.vlComprovacao) AS vlPagamento 
               FROM       BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
               INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento                   AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
               INNER JOIN SAC.dbo.tbPlanilhaAprovacao                                  AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
               WHERE     c1.nrFonteRecurso     = k.nrFonteRecurso
			         AND c1.idProduto          = k.idProduto
					 AND c1.idEtapa            = k.idEtapa
					 AND c1.idUFDespesa        = k.idUFDespesa
					 AND c1.idMunicipioDespesa = k.idMunicipioDespesa
			         AND c1.idPlanilhaItem     = k.idPlanilhaItem 
			         AND c1.idPronac           = k.idPronac 
               GROUP BY c1.nrFonteRecurso,c1.idProduto,c1.idEtapa,c1.idUFDespesa,c1.idMunicipioDespesa,c1.idPlanilhaItem ) as vlComprovado,
             CONVERT(varchar(max),k.dsJustificativa) as JustComponente
       FROM Projetos a
       INNER JOIN tbPlanilhaProjeto        b on (a.idPronac           = b.idPronac)
       INNER JOIN tbPlanilhaProposta       z on (b.idPlanilhaProposta = z.idPlanilhaProposta)
       INNER JOIN tbPlanilhaAprovacao      k on (b.idPlanilhaProposta = k.idPlanilhaProposta)
       LEFT JOIN Produto                   c on (b.idProduto          = c.Codigo)
       INNER JOIN tbPlanilhaEtapa          d on (k.idEtapa            = d.idPlanilhaEtapa)
       INNER JOIN tbPlanilhaUnidade        e on (b.idUnidade          = e.idUnidade)
       INNER JOIN tbPlanilhaItens          i on (b.idPlanilhaItem     = i.idPlanilhaItens)
       INNER JOIN Verificacao              x on (b.FonteRecurso       = x.idVerificacao)
       INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa          = f.idUF and b.MunicipioDespesa = f.idMunicipio)
       WHERE     k.stAtivo  = 'S' 
	         AND a.idPronac = @idPronac
       ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao

   END
ELSE
-- =========================================================================================
-- CORTES ORÇAMENTÁRIOS APROVADO
-- =========================================================================================
IF @TipoPlanilha = 4
   BEGIN 
      SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,b.idPlanilhaProjeto,
             CASE 
               WHEN k.idProduto = 0
                    THEN 'Administração do Projeto'
                    ELSE c.Descricao 
               END as Produto,
             b.idEtapa,d.Descricao as Etapa,i.Descricao as Item,
             ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) as vlSolicitado,convert(varchar(max),z.dsJustificativa) as JustProponente,
             ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) as vlSugerido,convert(varchar(max),b.Justificativa) as JustParecerista,
             e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,k.QtDias as QtdeDias,
             k.TpDespesa,k.TpPessoa,k.nrContrapartida,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,f.UF,f.Municipio,
             ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
       convert(varchar(max),k.dsJustificativa) as JustComponente
       FROM Projetos a
       INNER JOIN tbPlanilhaProjeto b on (a.idPronac = b.idPronac)
       INNER JOIN tbPlanilhaProposta z on (b.idPlanilhaProposta=z.idPlanilhaProposta)
       INNER JOIN tbPlanilhaAprovacao k on (b.idPlanilhaProposta=k.idPlanilhaProposta)
       LEFT JOIN Produto c on (b.idProduto = c.Codigo)
       INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
       INNER JOIN tbPlanilhaUnidade e on (b.idUnidade = e.idUnidade)
       INNER JOIN tbPlanilhaItens i on (b.idPlanilhaItem=i.idPlanilhaItens)
       INNER JOIN Verificacao x on (b.FonteRecurso = x.idVerificacao)
       INNER JOIN agentes.dbo.vUfMunicipio f on (b.UfDespesa = f.idUF and b.MunicipioDespesa = f.idMunicipio)
       WHERE k.stAtivo = 'S' 
            AND (ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) <> ROUND((b.Quantidade * b.Ocorrencia * b.ValorUnitario),2) OR
                 ROUND((z.Quantidade * z.Ocorrencia * z.ValorUnitario),2) <> ROUND((k.QtItem * k.nrOcorrencia * k.vlUnitario),2))
            AND a.idPronac = @idPronac
       ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
   END
ELSE
-- =========================================================================================
-- REMANEJAMENTO ATÉ 50%
-- =========================================================================================
IF @TipoPlanilha = 5
   BEGIN
      IF NOT EXISTS( SELECT TOP 1 * FROM tbPlanilhaAprovacao WHERE idPronac = @idPronac AND stAtivo = 'S' AND tpPlanilha = 'RP')
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.idPlanilhaAprovacaoPai,
				  CASE 
				    WHEN k.idProduto = 0
					  THEN 'Administração do Projeto'
					  ELSE c.Descricao 
				    END as Produto,
				  k.idEtapa,d.Descricao as Etapa,d.tpGrupo,i.Descricao as Item,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,
				  e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,
				  ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
                  (SELECT SUM(b1.vlComprovacao) AS vlPagamento 
                     FROM       BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                     INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento                   AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                     INNER JOIN SAC.dbo.tbPlanilhaAprovacao                                  AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                     WHERE     c1.nrFonteRecurso     = k.nrFonteRecurso
			               AND c1.idProduto          = k.idProduto
					       AND c1.idEtapa            = k.idEtapa
					       AND c1.idUFDespesa        = k.idUFDespesa
					       AND c1.idMunicipioDespesa = k.idMunicipioDespesa
			               AND c1.idPlanilhaItem     = k.idPlanilhaItem 
			               AND c1.idPronac           = k.idPronac 
                     GROUP BY c1.nrFonteRecurso,c1.idProduto,c1.idEtapa,c1.idUFDespesa,c1.idMunicipioDespesa,c1.idPlanilhaItem ) as vlComprovado,
				     k.QtDias as QtdeDias,f.UF,f.Municipio,k.dsJustificativa,k.idAgente
			    FROM Projetos                       a
			    INNER JOIN tbPlanilhaAprovacao      k on (a.idPronac       = k.idPronac)
			    LEFT JOIN Produto                   c on (k.idProduto      = c.Codigo)
			    INNER JOIN tbPlanilhaEtapa          d on (k.idEtapa        = d.idPlanilhaEtapa)
			    INNER JOIN tbPlanilhaUnidade        e on (k.idUnidade      = e.idUnidade)
			    INNER JOIN tbPlanilhaItens          i on (k.idPlanilhaItem = i.idPlanilhaItens)
			    INNER JOIN Verificacao              x on (k.nrFonteRecurso = x.idVerificacao)
			    INNER JOIN agentes.dbo.vUfMunicipio f on (k.idUfDespesa    = f.idUF and k.idMunicipioDespesa = f.idMunicipio)
			   INNER JOIN tbReadequacao             h on (k.idReadequacao  = h .idReadequacao) 
			    WHERE     k.stAtivo = 'N' 
				   	  AND k.tpPlanilha = 'RP'
					  AND ((ROUND((k.qtItem * k.nrOcorrencia * k.vlUnitario),2) <> 0)
					        OR (k.dsJustificativa IS NOT NULL))
   				      AND h.idTipoReadequacao = 1 
                      AND h.siEncaminhamento  = 11
				      AND h.stAtendimento     = 'D'
		              AND h.stEstado = 0
					  AND a.idPronac = @idPronac
			    ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
	     END
	  ELSE
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.idPlanilhaAprovacaoPai,
				 CASE 
				   WHEN k.idProduto = 0
						THEN 'Administração do Projeto'
						ELSE c.Descricao 
				   END as Produto,
				 k.idEtapa,d.Descricao as Etapa,d.tpGrupo,i.Descricao as Item,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,
				 e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,
				 ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
                  (SELECT SUM(b1.vlComprovacao) AS vlPagamento 
                     FROM       BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                     INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento                   AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                     INNER JOIN SAC.dbo.tbPlanilhaAprovacao                                  AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                     WHERE     c1.nrFonteRecurso     = k.nrFonteRecurso
			               AND c1.idProduto          = k.idProduto
					       AND c1.idEtapa            = k.idEtapa
					       AND c1.idUFDespesa        = k.idUFDespesa
					       AND c1.idMunicipioDespesa = k.idMunicipioDespesa
			               AND c1.idPlanilhaItem     = k.idPlanilhaItem 
			               AND c1.idPronac           = k.idPronac 
                     GROUP BY c1.nrFonteRecurso,c1.idProduto,c1.idEtapa,c1.idUFDespesa,c1.idMunicipioDespesa,c1.idPlanilhaItem ) as vlComprovado,
				 k.QtDias as QtdeDias,f.UF,f.Municipio,k.dsJustificativa,k.idAgente
			   FROM Projetos a
			   INNER JOIN tbPlanilhaAprovacao      k on (a.idPronac = k.idPronac)
			   LEFT JOIN Produto                   c on (k.idProduto = c.Codigo)
			   INNER JOIN tbPlanilhaEtapa          d on (k.idEtapa = d.idPlanilhaEtapa)
			   INNER JOIN tbPlanilhaUnidade        e on (k.idUnidade = e.idUnidade)
			   INNER JOIN tbPlanilhaItens          i on (k.idPlanilhaItem=i.idPlanilhaItens)
			   INNER JOIN Verificacao              x on (k.nrFonteRecurso = x.idVerificacao)
			   INNER JOIN agentes.dbo.vUfMunicipio f on (k.idUfDespesa = f.idUF and k.idMunicipioDespesa = f.idMunicipio)
			   WHERE k.stAtivo = 'S' 
			        AND k.tpPlanilha = 'RP'
					--AND k.tpAcao IN ('N','A','I',NULL)
					AND ((ROUND((k.qtItem * k.nrOcorrencia * k.vlUnitario),2) <> 0)
					     OR (k.dsJustificativa IS NOT NULL))
					AND a.idPronac = @idPronac
			   ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
	   END	  
   END	  
ELSE
-- =========================================================================================
-- REMANEJAMENTO, COMPLEMENTAÇÃO E REDUÇÃO
-- =========================================================================================
IF @TipoPlanilha = 6
   BEGIN
  
      IF EXISTS(SELECT TOP 1 * FROM tbPlanilhaAprovacao a
	                           INNER JOIN tbReadequacao b on (a.idPronac = b.idPronac)
	                           WHERE a.idPronac = @idPronac 
								     AND a.stAtivo = 'N' 
								     AND a.tpPlanilha = 'SR'
									 AND b.idTipoReadequacao = 2 
                                     AND b.siEncaminhamento <> 15
		                             AND b.stEstado = 0)
									  --AND b.siEncaminhamento IN (1,3,4,5,6,7,8,10,12,14))
	
	 -- IF NOT EXISTS(SELECT TOP 1 idPronac FROM tbPlanilhaAprovacao WHERE  stAtivo = 'S' AND tpPlanilha = 'SR' AND IdPRONAC = @idPronac) 
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,b.idProduto,b.idPlanilhaAprovacao,b.idPlanilhaAprovacaoPai,
				 CASE 
				   WHEN b.idProduto = 0
						THEN 'Administração do Projeto'
						ELSE c.Descricao 
				   END as Produto,
				 b.idEtapa,d.Descricao as Etapa,d.tpGrupo,g.Descricao as Item,b.nrFonteRecurso as idFonte,h.Descricao as FonteRecurso,
				 e.Descricao as Unidade,b.QtItem as Quantidade,b.nrOcorrencia as Ocorrencia,b.vlUnitario,
				 ROUND((b.QtItem * b.nrOcorrencia * b.VlUnitario),2) as vlAprovado,
                  (SELECT SUM(b1.vlComprovacao) AS vlPagamento 
                     FROM       BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                     INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento                   AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                     INNER JOIN SAC.dbo.tbPlanilhaAprovacao                                  AS c1 ON (a1.idPlanilhaAprovacao    = c1.idPlanilhaAprovacao)
                     WHERE     c1.nrFonteRecurso     = b.nrFonteRecurso
			               AND c1.idProduto          = b.idProduto
					       AND c1.idEtapa            = b.idEtapa
					       AND c1.idUFDespesa        = b.idUFDespesa
					       AND c1.idMunicipioDespesa = b.idMunicipioDespesa
			               AND c1.idPlanilhaItem     = b.idPlanilhaItem 
			               AND c1.idPronac           = b.idPronac 
                     GROUP BY c1.nrFonteRecurso,c1.idProduto,c1.idEtapa,c1.idUFDespesa,c1.idMunicipioDespesa,c1.idPlanilhaItem ) as vlComprovado,
					 		  b.QtDias as QtdeDias,f.UF,f.Municipio,b.dsJustificativa,b.idAgente,b.tpAcao
			   FROM Projetos                       a
			   INNER JOIN tbPlanilhaAprovacao      b on (a.idPronac       = b.idPronac)
			   LEFT  JOIN Produto                  c on (b.idProduto      = c.Codigo)
			   INNER JOIN tbPlanilhaEtapa          d on (b.idEtapa        = d.idPlanilhaEtapa)
			   INNER JOIN tbPlanilhaUnidade        e on (b.idUnidade      = e.idUnidade)
			   INNER JOIN agentes.dbo.vUfMunicipio f on (b.idUfDespesa    = f.idUF and b.idMunicipioDespesa = f.idMunicipio)
			   INNER JOIN tbPlanilhaItens          g on (b.idPlanilhaItem = g.idPlanilhaItens)
			   INNER JOIN Verificacao              h on (b.nrFonteRecurso = h.idVerificacao)
			   INNER JOIN tbReadequacao            i on (b.idReadequacao  = i .idReadequacao) 
			   WHERE    b.stAtivo = 'N' 
			   		AND b.tpPlanilha = 'SR'
					AND ((ROUND((b.qtItem * b.nrOcorrencia * b.vlUnitario),2) <> 0)
					     OR (b.dsJustificativa IS NOT NULL))
   				    AND i.idTipoReadequacao = 2 
                    AND i.siEncaminhamento <> 15
				    --AND i.stAtendimento     = 'D'
		            AND i.stEstado = 0
					AND a.idPronac = @idPronac
			   ORDER BY h.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,g.Descricao
	     END
	  ELSE
         BEGIN
           SELECT a.idPronac,a.AnoProjeto+a.Sequencial as PRONAC,a.NomeProjeto,k.idProduto,k.idPlanilhaAprovacao,k.idPlanilhaAprovacaoPai,
				 CASE 
				   WHEN k.idProduto = 0
						THEN 'Administração do Projeto'
						ELSE c.Descricao 
				   END as Produto,
				 k.idEtapa,d.Descricao as Etapa,d.tpGrupo,i.Descricao as Item,k.nrFonteRecurso as idFonte,x.Descricao as FonteRecurso,
				 e.Descricao as Unidade,k.QtItem as Quantidade,k.nrOcorrencia as Ocorrencia,k.vlUnitario,
				 ROUND((k.QtItem * k.nrOcorrencia * k.VlUnitario),2) as vlAprovado,
                  (SELECT SUM(b1.vlComprovacao) AS vlPagamento 
                     FROM       BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a1
                     INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento                   AS b1 ON (a1.idComprovantePagamento = b1.idComprovantePagamento)
                     INNER JOIN SAC.dbo.tbPlanilhaAprovacao                                  AS c1 ON (a1.idPlanilhaAprovacao = c1.idPlanilhaAprovacao)
                     WHERE     c1.nrFonteRecurso     = k.nrFonteRecurso
			               AND c1.idProduto          = k.idProduto
					       AND c1.idEtapa            = k.idEtapa
					       AND c1.idUFDespesa        = k.idUFDespesa
					       AND c1.idMunicipioDespesa = k.idMunicipioDespesa
			               AND c1.idPlanilhaItem     = k.idPlanilhaItem 
			               AND c1.idPronac           = k.idPronac 
                     GROUP BY c1.nrFonteRecurso,c1.idProduto,c1.idEtapa,c1.idUFDespesa,c1.idMunicipioDespesa,c1.idPlanilhaItem ) as vlComprovado,
				 k.QtDias as QtdeDias,f.UF,f.Municipio,k.dsJustificativa,k.idAgente,k.tpAcao
			   FROM Projetos a
			   INNER JOIN tbPlanilhaAprovacao k on (a.idPronac = k.idPronac)
			   LEFT JOIN Produto c on (k.idProduto = c.Codigo)
			   INNER JOIN tbPlanilhaEtapa d on (k.idEtapa = d.idPlanilhaEtapa)
			   INNER JOIN tbPlanilhaUnidade e on (k.idUnidade = e.idUnidade)
			   INNER JOIN tbPlanilhaItens i on (k.idPlanilhaItem=i.idPlanilhaItens)
			   INNER JOIN Verificacao x on (k.nrFonteRecurso = x.idVerificacao)
			   INNER JOIN agentes.dbo.vUfMunicipio f on (k.idUfDespesa = f.idUF and k.idMunicipioDespesa = f.idMunicipio)
			   WHERE k.stAtivo = 'S' 
			        AND k.tpPlanilha = 'SR'
					--AND k.tpAcao IN ('N','A','I',NULL)
					AND ((ROUND((k.qtItem * k.nrOcorrencia * k.vlUnitario),2) <> 0)
					     OR (k.dsJustificativa IS NOT NULL))
					AND a.idPronac = @idPronac
			   ORDER BY x.Descricao,c.Descricao DESC,CONVERT(VARCHAR(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao,f.UF,f.Municipio,i.Descricao
	   END	  
   END 

