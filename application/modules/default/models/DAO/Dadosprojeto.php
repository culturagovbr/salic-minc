<?php
class Dadosprojeto extends Zend_Db_Table
{
    protected $_name    = 'SAC.dbo.Projetos';

    public static function buscar($pronac)
    {
        $sql = "SELECT 
            Pr.idPRONAC,
            Tp.Emissor,
            Tp.dtTramitacaoEnvio,
            Tp.Situacao, 
            Tp.Destino, 
            Tp.Receptor, 
            Tp.DtTramitacaoRecebida, 
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
            SAC.dbo.fnOutrasFontes(Pr.idPronac) AS OutrasFontes,
            ISNULL(SAC.dbo.fnValorDaProposta(Pr.idPRONAC),SAC.dbo.fnValorSolicitado(Pr.AnoProjeto,Pr.Sequencial)) as ValorProposta,
            CASE WHEN Pr.Mecanismo IN ('2','6')
            THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial) 
            ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial)
            END AS ValorAprovado,
            CASE WHEN Pr.Mecanismo IN ('2','6')
            THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial)
            ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial) + SAC.dbo.fnOutrasFontes(Pr.idPronac)                
            END AS ValorProjeto,
            SAC.dbo.fnCustoProjeto (Pr.AnoProjeto,Pr.Sequencial) as ValorCaptado
            FROM SAC.dbo.Projetos Pr
            INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
            INNER JOIN SAC.dbo.Area Ar ON  Ar.Codigo = Pr.Area
            INNER JOIN SAC.dbo.Segmento Sg ON Sg.Codigo = Pr.Segmento
            INNER JOIN SAC.dbo.Mecanismo Mc ON Mc.Codigo = Pr.Mecanismo
            INNER JOIN SAC.dbo.Enquadramento En ON En.idPRONAC =  Pr.idPRONAC
            LEFT JOIN AGENTES.dbo.Agentes A ON A.CNPJCPF = Pr.CgcCpf
            LEFT JOIN SAC.dbo.PreProjeto PP ON PP.idPreProjeto = Pr.idProjeto
            LEFT JOIN AGENTES.dbo.Nomes N ON N.idAgente = A.idAgente 
            LEFT JOIN SAC.dbo.vwTramitarProjeto Tp ON Tp.idPronac = Pr.idPRONAC
            LEFT JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf
            WHERE Pr.idPRONAC = ". $pronac ."";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
