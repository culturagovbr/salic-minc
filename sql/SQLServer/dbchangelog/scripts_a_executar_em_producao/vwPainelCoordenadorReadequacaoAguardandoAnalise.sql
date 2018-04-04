DROP VIEW dbo.vwPainelCoordenadorReadequacaoAguardandoAnalise;

CREATE VIEW dbo.vwPainelCoordenadorReadequacaoAguardandoAnalise
AS
SELECT b.idPronac, a.idReadequacao, b.AnoProjeto+b.Sequencial as PRONAC, b.NomeProjeto, b.Orgao as idOrgao, a.dtSolicitacao, 
       c.dsReadequacao as tpReadequacao,
	   DATEDIFF(DAY,a.dtEnvio,GETDATE()) as qtAguardandoDistribuicao, a.dtEnvio
  FROM tbReadequacao AS a 
  INNER JOIN SAC.dbo.Projetos AS b ON a.idPronac = b.idPronac 
  INNER JOIN SAC.dbo.tbTipoReadequacao AS c ON c.idTipoReadequacao = a.idTipoReadequacao 
  WHERE a.stEstado = 0 
        AND a.siEncaminhamento = 1
