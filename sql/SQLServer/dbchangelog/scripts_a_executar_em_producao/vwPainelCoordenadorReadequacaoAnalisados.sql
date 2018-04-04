DROP VIEW dbo.vwPainelCoordenadorReadequacaoAnalisados
CREATE VIEW dbo.vwPainelCoordenadorReadequacaoAnalisados
AS
SELECT b.idPronac, a.idReadequacao, b.AnoProjeto+b.Sequencial as PRONAC, b.NomeProjeto,d.DtEncaminhamento as dtEncaminhamento, a.dtEnvio
       d.dtEnvioAvaliador as dtDistribuicao,d.dtRetornoAvaliador as dtDevolucao,
	   DATEDIFF(DAY,d.DtEncaminhamento,d.dtEnvioAvaliador) as qtDiasDistribuir,
	   DATEDIFF(DAY,d.dtEnvioAvaliador,d.dtRetornoAvaliador) as qtDiasAvaliar,
	   DATEDIFF(DAY,d.DtEncaminhamento,d.dtRetornoAvaliador) as qtTotalDiasAvaliar,
	   c.dsReadequacao as tpReadequacao,
       d.idAvaliador as idTecnicoParecerista,h.usu_nome as nmTecnicoParecerista,d.idUnidade as idOrgao,i.Sigla as sgUnidade,
	   CASE 
	     WHEN b.Orgao = 179
		   THEN 166
		   ELSE b.Orgao
	   END as idOrgaoOrigem
  FROM sac.dbo.tbReadequacao                 a 
  INNER JOIN sac.dbo.Projetos                b ON (a.idPronac = b.idPronac) 
  INNER JOIN sac.dbo.tbTipoReadequacao       c ON (c.idTipoReadequacao = a.idTipoReadequacao)
  INNER JOIN sac.dbo.tbDistribuirReadequacao d ON (a.idReadequacao = d.idReadequacao)
  LEFT  JOIN tabelas.dbo.Usuarios            h ON (d.idAvaliador = h.usu_codigo)
  INNER JOIN sac.dbo.Orgaos                  i ON (d.idUnidade   = i.Codigo)
  WHERE a.stEstado = 0 
      AND a.siEncaminhamento in (6,10) 
