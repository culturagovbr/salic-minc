<?php
class DadosprojetoDAO extends Zend_Db_Table
{
    protected $_name    = 'SAC.dbo.Projetos';

    public static function buscar($pronac)
    {
        $sql = "SELECT
			Pr.AnoProjeto+Pr.Sequencial as pronac ,
			Pr.idPRONAC,
			Tp.Emissor, 
			CONVERT(CHAR(10),Tp.dtTramitacaoEnvio,103) as dtTramitacaoEnvio, 
			Tp.Situacao, 
			Tp.Destino, 
			Tp.Receptor, 
			CONVERT(CHAR(10),Tp.DtTramitacaoRecebida,103) as DtTramitacaoRecebida, 
			Pr.NomeProjeto,
			Pr.ResumoProjeto, 
			Tp.meDespacho,
			St.descricao dsSituacao,
			Mc.descricao dsMecanismo,
			Sg.descricao dsSegmento,
			Ar.descricao dsArea,
			PP.idPreProjeto,
			CASE WHEN N.Descricao IS NULL
			THEN I.Nome
			ELSE N.Descricao
			END AS nmProponente,
			Pr.UfProjeto, 
			Pr.Processo, 
			Pr.CgcCpf, 
			CONVERT(CHAR(10),Pr.DtSituacao,103) as DtSituacao, 
			Pr.ProvidenciaTomada, 
			Pr.Localizacao,
			CASE En.Enquadramento when 1 then 'Artigo 26' when 2 then 'Artigo 18' else 'N�o enquadrado' end as Enquadramento,
			Pr.SolicitadoReal,
			--SAC.dbo.fnOutrasFontes(Pr.idPronac) AS OutrasFontes,
			CASE WHEN SAC.dbo.fnOutrasFontes(Pr.idPronac) is null
			Then SAC.dbo.fnValorDaProposta(PP.idPreProjeto)
			else SAC.dbo.fnValorSolicitado(Pr.AnoProjeto,Pr.Sequencial)
			end as ValorProposta,
			--CASE WHEN Pr.Mecanismo IN ('2','6')
			--THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial)
			--ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial)
			--END AS ValorAprovado,
			CASE WHEN Pr.Mecanismo IN ('2','6')
			--THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial)
			--ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial) + SAC.dbo.fnOutrasFontes(Pr.idPronac)
			--END AS ValorProjeto,
			--SAC.dbo.fnCustoProjeto (Pr.AnoProjeto,Pr.Sequencial) as ValorCaptado
			FROM SAC.dbo.Projetos Pr
			JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
			JOIN SAC.dbo.Area Ar ON  Ar.Codigo = Pr.Area
			JOIN SAC.dbo.Segmento Sg ON Sg.Codigo = Pr.Segmento
			JOIN SAC.dbo.Mecanismo Mc ON Mc.Codigo = Pr.Mecanismo
			LEFT JOIN SAC.dbo.Enquadramento En ON En.idPRONAC =  Pr.idPRONAC
			JOIN AGENTES.dbo.Agentes A ON A.CNPJCPF = Pr.CgcCpf
			JOIN SAC.dbo.PreProjeto PP ON PP.idPreProjeto = Pr.idProjeto
			JOIN AGENTES.dbo.Nomes N ON N.idAgente = A.idAgente 
			LEFT JOIN SAC.dbo.vwTramitarProjeto Tp ON Tp.idPronac = Pr.idPRONAC
			JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf
			WHERE Pr.idPRONAC = ". $pronac ."" ;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
