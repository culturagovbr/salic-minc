
DROP view dbo.vwPainelAvaliarPropostas;

CREATE VIEW dbo.vwPainelAvaliarPropostas
AS
SELECT a.idPreProjeto AS idProjeto,a.NomeProjeto AS NomeProposta,a.idAgente,CONVERT(CHAR(20),b.DtMovimentacao,120) AS DtMovimentacao,
	   DATEDIFF(d,b.DtMovimentacao,GETDATE()) AS diasDesdeMovimentacao,b.idMovimentacao,b.Movimentacao AS CodSituacao,
	   CONVERT(CHAR(20),c.DtAvaliacao,120) AS DtAdmissibilidade, DATEDIFF(d,c.DtAvaliacao,GETDATE()) AS diasCorridos,
	   c.idTecnico AS idUsuario,c.DtAvaliacao,c.idAvaliacaoProposta,
		 c.ConformidadeOK,
	   (SELECT Usuarios.usu_nome FROM tabelas.dbo.Usuarios WHERE usu_codigo = c.idTecnico) AS Tecnico,
	   CASE
	     WHEN a.AreaAbrangencia = 0 THEN 251
	     WHEN a.AreaAbrangencia = 1 THEN 160
	   END AS idSecretaria,
	   d.CNPJCPF
FROM SAC.dbo.preprojeto                AS a
INNER JOIN SAC.dbo.tbMovimentacao      AS b ON (a.idPreProjeto = b.idProjeto)
INNER JOIN SAC.dbo.tbAvaliacaoProposta AS c ON (a.idPreProjeto = c.idProjeto)
INNER JOIN agentes.dbo.Agentes         AS d ON (a.idAgente     = d.idAgente)
INNER JOIN sac.dbo.Verificacao         AS e ON (b.Movimentacao = e.idVerificacao)
WHERE b.Movimentacao IN(96,97,127,128)
      AND a.stTipoDemanda = 'NA'
      AND a.stEstado = 1
      AND b.stEstado = 0
	  AND c.stEstado = 0
	  AND NOT EXISTS(SELECT * FROM SAC.dbo.Projetos AS f WHERE a.idPreProjeto = f.idProjeto)

GO
