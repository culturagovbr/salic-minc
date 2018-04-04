DROP VIEW dbo.vwPainelCoordenadorReadequacaoEmAnalise;

CREATE VIEW dbo.vwPainelCoordenadorReadequacaoEmAnalise
AS
SELECT b.idPronac, a.idReadequacao, b.AnoProjeto+b.Sequencial as PRONAC, b.NomeProjeto,a.dtSolicitacao,
       d.dtEncaminhamento,DATEDIFF(DAY,a.dtEnvio,d.dtEncaminhamento) as qtDiasEncaminhar,c.dsReadequacao as tpReadequacao, 
	   a.siEncaminhamento,e.dsEncaminhamento,d.dtEnvioAvaliador as dtDistribuicao,
	   DATEDIFF(DAY,d.dtEnvioAvaliador,GETDATE()) as qtDiasEmAnalise,
	   d.idAvaliador as idTecnicoParecerista,f.usu_nome as nmReceptor,h.usu_nome as nmTecnicoParecerista,d.idUnidade as idOrgao,i.Sigla as sgUnidade,b.Orgao as idOrgaoOrigem, a.dtEnvio
  FROM sac.dbo.tbReadequacao                 a 
  INNER JOIN sac.dbo.Projetos                b ON (a.idPronac = b.idPronac) 
  INNER JOIN sac.dbo.tbTipoReadequacao       c ON (c.idTipoReadequacao = a.idTipoReadequacao)
  INNER JOIN sac.dbo.tbDistribuirReadequacao d ON (a.idReadequacao = d.idReadequacao)
  INNER JOIN sac.dbo.tbTipoEncaminhamento    e ON (a.siEncaminhamento = e.idTipoEncaminhamento)
  LEFT  JOIN tabelas.dbo.Usuarios            f ON (a.idAvaliador = f.usu_codigo)
  LEFT  JOIN tabelas.dbo.Usuarios            h ON (d.idAvaliador = h.usu_codigo)
  INNER JOIN sac.dbo.Orgaos                  i ON (d.idUnidade   = i.Codigo)
  WHERE a.stEstado = 0 
        AND a.siEncaminhamento in (3, 4, 5, 7,8,14) 
