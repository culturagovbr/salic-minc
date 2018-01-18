<?php

class PublicacaoDouDAO extends Zend_Db_Table
{
    public static function buscarPortaria()
    {
        $sql = "SELECT distinct PortariaAprovacao
                        FROM SAC.dbo.Aprovacao a
                        WHERE a.DtPortariaAprovacao in
                        (SELECT MAX(DtPortariaAprovacao) FROM SAC.dbo.Aprovacao
                        WHERE YEAR(DtPortariaAprovacao) = '" . date('Y') . "')";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    }

    public static function buscarPortariaPublicacao($PortariaAprovacao, $orgaoSuperior = null, $tipoPublicacao = null)
    {
        if ($orgaoSuperior->Superior == Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $filtroOrgao = 'Pr.Area <> 2';
        } else {
            $filtroOrgao = 'Pr.Area = 2';
        }
        $sql = " SELECT A.IdPRONAC, A.idAprovacao
                 FROM SAC.dbo.Aprovacao AS A
                 INNER JOIN SAC.dbo.Projetos AS Pr ON Pr.IdPRONAC = A.IdPRONAC
                 WHERE A.PortariaAprovacao = '$PortariaAprovacao'
                 AND A.IdPRONAC IS NOT NULL
                 AND $filtroOrgao
                 AND A.TipoAprovacao = $tipoPublicacao
                ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function BuscarProjetosDOU()
    {
        $sql = "SELECT 
                        Pr.IdPRONAC,
                        Pr.AnoProjeto,
                        Pr.Sequencial,
                        Pr.UfProjeto,
                        Pr.Area,
                        Pr.Segmento,
                        Pr.Mecanismo,
                        Pr.NomeProjeto,
                        Pr.Processo,
                        Pr.CgcCpf,
                        Situacao,
                        Pr.DtProtocolo,
                        Pr.DtAnalise,
                        Pr.Modalidade,
                        Pr.OrgaoOrigem,
                        Pr.DtSaida,
                        Pr.DtRetorno,
                        Pr.UnidadeAnalise,
                        Pr.Analista,
                        Pr.DtSituacao,
                        Pr.ResumoProjeto,
                        Pr.ProvidenciaTomada,
                        Pr.Localizacao,
                        Pr.DtInicioExecucao,
                        Pr.DtFimExecucao,
                        Pr.SolicitadoUfir,
                        Pr.SolicitadoReal,
                        Pr.SolicitadoCapitalUfir,
                        Pr.SolicitadoCapitalReal,
                        Pr.Logon,
                        Pr.idProjeto
                        from SAC.dbo.Projetos Pr
                        INNER JOIN SAC.dbo.Aprovacao Ap on Pr.IdPRONAC = Ap.IdPRONAC";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function BuscarIdPronacDOU($idAprovacao)
    {
        $sql = "SELECT 
			IdPRONAC
		from SAC.dbo.Aprovacao
		where idAprovacao = $idAprovacao
		";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function PublicacaoDOU()
    {
        $sql = "SELECT  Ap.idAprovacao,
		Pr.AnoProjeto+Pr.Sequencial as pronac,
		Pr.IdPRONAC,
		Pr.NomeProjeto,
		Ar.Descricao as Area,
		St.Descricao as Situacao,
		CONVERT(CHAR(10),
		Ap.DtInicioCaptacao,103) as DataInicio,
		CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFim,
		Ap.AprovadoReal as VlAprovado,
		Pr.Processo,
		Pr.CgcCpf,
		Pr.UfProjeto,
		Pr.ResumoProjeto,
		CASE
		WHEN N.Descricao IS NULL
		THEN I.Nome
		ELSE N.Descricao
		END AS NomeProponente
		FROM 	SAC.dbo.Projetos Pr
		INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
		INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
		INNER JOIN SAC.dbo.Enquadramento Enq on Enq.IdPRONAC = Pr.IdPRONAC
		INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
		INNER JOIN AGENTES.dbo.Agentes Ag on Ag.CNPJCPF = Pr.CgcCpf
		INNER JOIN SAC.dbo.Interessado I on I.CgcCpf = Pr.CgcCpf
		INNER JOIN AGENTES.dbo.Nomes N ON N.idAgente = Ag.idAgente
		where Ap.DtAprovacao in (select max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = Pr.IdPRONAC)
		and (Pr.Situacao = 'D27' OR Pr.Situacao = 'D28')
		AND (Enq.Enquadramento = '1' OR Enq.Enquadramento = '2')
		AND (Ap.PortariaAprovacao is null or Ap.PortariaAprovacao = '')";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ProjetoPortaria($portaria, $situacao)
    {
        $sql = " SELECT   Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC,  Pr.Processo, 
		Pr.CgcCpf, Pr.UfProjeto, Pr.ResumoProjeto,
		Pr.NomeProjeto, Ar.Descricao as Area, St.Descricao as Situacao,
				CONVERT(CHAR(10),Ap.DtInicioCaptacao,103) as DataInicio, 
		CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFim, 
		CASE WHEN Enq.Enquadramento = 1 THEN 'Artigo 26'
		WHEN Enq.Enquadramento = 2 THEN 'Artigo 18'
		END AS Artigo, 
		
		CASE WHEN En.Municipio IS NULL 
				THEN I.Cidade
			ELSE En.Municipio
				END AS Cidade,
		CASE WHEN N.Descricao IS NULL
				THEN I.Nome
			ELSE N.Descricao
				END AS NomeProponente, Ap.AprovadoReal,
					
			CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) as DataPublicacao,
            CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFimPublicacao,
			CONVERT(CHAR(10),Ap.DtPortariaAprovacao,103) as DataPortaria, Ar.Codigo AS AreaCodigo,
			Ap.PortariaAprovacao
					FROM 	SAC.dbo.Projetos Pr
							INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
							INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
							INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
							INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
							INNER JOIN AGENTES.dbo.Agentes Ag on Ag.CNPJCPF = Pr.CgcCpf
							INNER JOIN AGENTES.dbo.EnderecoNacional En on En.idAgente = Ag.idAgente
							INNER JOIN AGENTES.dbo.Nomes N ON N.idAgente = Ag.idAgente
							INNER JOIN SAC.dbo.Interessado I on I.CgcCpf = Pr.CgcCpf
						WHERE Ap.PortariaAprovacao = '" . $portaria . "' 
						and Ap.DtPortariaAprovacao is not null
						AND Pr.situacao = '$situacao'
                        AND En.Status = 1
                    ORDER BY 18    
        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ProjetoPortariaGerarRTF($portaria, $orgaoSuperior = null)
    {
        $filtroOrgao = 'Pr.Area = 2';
        if ($orgaoSuperior->Superior == Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $filtroOrgao = 'Pr.Area <> 2';
        }

        $sql = " SELECT Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC,  Pr.Processo, 
                    Pr.CgcCpf, Pr.UfProjeto, Pr.ResumoProjeto,
                    Pr.NomeProjeto, Ar.Descricao as Area, St.Descricao as Situacao,
                    CONVERT(CHAR(10),Ap.DtInicioCaptacao,103) as DataInicio, 
                    CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFim, 
                    CASE WHEN Enq.Enquadramento = 1 THEN 'Artigo 26'
                    WHEN Enq.Enquadramento = 2 THEN 'Artigo 18'
                    END AS Artigo, i.Cidade,
                    i.Nome NomeProponente, Ap.AprovadoReal,
                    CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) as DataPublicacao,
                    CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFimPublicacao,
                    CONVERT(CHAR(10),Ap.DtPortariaAprovacao,103) as DataPortaria, Ar.Codigo AS AreaCodigo,
                    Ap.PortariaAprovacao,
                    SAC.dbo.fnValorSolicitado(Pr.AnoProjeto,Pr.Sequencial) AS ValorSolicitado,
                    CASE
                        WHEN Pr.Mecanismo ='2' OR Pr.Mecanismo ='6'
                        THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial)
                        ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial)
                    END AS ValorAprovado,
                    SAC.dbo.fnCustoProjeto (Pr.AnoProjeto,Pr.Sequencial) AS ValorCaptado,
                    (SELECT sum(b2.vlComprovacao) AS vlPagamento
                        FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a2
                        INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b2 ON (a2.idComprovantePagamento = b2.idComprovantePagamento)
                        INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c2 ON (a2.idPlanilhaAprovacao = c2.idPlanilhaAprovacao)
                        WHERE a2.stItemAvaliado = 1 
                        AND c2.stAtivo = 'S' 
                        AND (c2.idPronac = Pr.IdPRONAC) ) as ComprovacaoValidada
                FROM SAC.dbo.Projetos Pr
                    INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
                    INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
                    INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
                    INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
                    INNER JOIN SAC.dbo.Interessado I on I.CgcCpf = Pr.CgcCpf
                WHERE Ap.PortariaAprovacao = '$portaria' 
                    and ( ap.PortariaAprovacao is not null or DtPublicacaoAprovacao is not null or DtPortariaAprovacao is not null) 
                    and $filtroOrgao
               ORDER BY 12,19,7  
        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ProjetoPortariaGerarRTFReadequacoes($portaria, $orgaoSuperior = null)
    {
        $filtroOrgao = 'Pr.Area = 2';
        if ($orgaoSuperior->Superior == Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $filtroOrgao = 'Pr.Area <> 2';
        }

        $sql = " SELECT Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC,  Pr.Processo, 
                    Pr.CgcCpf, Pr.UfProjeto, Pr.ResumoProjeto,
                    Pr.NomeProjeto, Ar.Descricao as Area, St.Descricao as Situacao,
                    CONVERT(CHAR(10),Ap.DtInicioCaptacao,103) as DataInicio, 
                    CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFim, 
                    CASE WHEN Enq.Enquadramento = 1 THEN 'Artigo 26'
                    WHEN Enq.Enquadramento = 2 THEN 'Artigo 18'
                    END AS Artigo, i.Cidade,
                    i.Nome NomeProponente, Ap.AprovadoReal,
                    CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) as DataPublicacao,
                    CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFimPublicacao,
                    CONVERT(CHAR(10),Ap.DtPortariaAprovacao,103) as DataPortaria, Ar.Codigo AS AreaCodigo,
                    Ap.PortariaAprovacao,
                    SAC.dbo.fnValorSolicitado(Pr.AnoProjeto,Pr.Sequencial) AS ValorSolicitado,
                    CASE
                        WHEN Pr.Mecanismo ='2' OR Pr.Mecanismo ='6'
                        THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial)
                        ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial)
                    END AS ValorAprovado,
                    SAC.dbo.fnCustoProjeto (Pr.AnoProjeto,Pr.Sequencial) AS ValorCaptado,
                    (SELECT sum(b2.vlComprovacao) AS vlPagamento
                        FROM BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao AS a2
                        INNER JOIN BDCORPORATIVO.scSAC.tbComprovantePagamento AS b2 ON (a2.idComprovantePagamento = b2.idComprovantePagamento)
                        INNER JOIN SAC.dbo.tbPlanilhaAprovacao AS c2 ON (a2.idPlanilhaAprovacao = c2.idPlanilhaAprovacao)
                        WHERE a2.stItemAvaliado = 1 
                        AND c2.stAtivo = 'S' 
                        AND (c2.idPronac = Pr.IdPRONAC) ) as ComprovacaoValidada, r.idTipoReadequacao, CAST(r.dsSolicitacao AS TEXT) AS dsSolicitacao
                FROM SAC.dbo.tbReadequacao AS r
                    INNER JOIN SAC.dbo.Projetos Pr ON Pr.IdPRONAC = r.idPronac
                    INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
                    INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
                    INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
                    INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC AND Ap.idReadequacao = r.idReadequacao
                    INNER JOIN SAC.dbo.Interessado I on I.CgcCpf = Pr.CgcCpf
                WHERE Ap.PortariaAprovacao = '$portaria' 
                    and ( ap.PortariaAprovacao is not null or DtPublicacaoAprovacao is not null or DtPortariaAprovacao is not null) 
                    and r.siEncaminhamento = 9
                    and $filtroOrgao 
               ORDER BY 25, 2
        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function retornaSeqPortaria($idAprovacao)
    {
        $sql = "select PortariaAprovacao from SAC.dbo.Aprovacao where idAprovacao = $idAprovacao";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sequencialPortaria = $db->fetchAll($sql);
        print($sql);
        die();

        preg_match('#2(.*?)\/#is', $sequencialPortaria[0], $sequencialPortaria);
        $sequencialPortaria = $sequencialPortaria[1];

        return $sequencialPortaria;
    }

    public static function Assinatura($funcao = null)
    {
        $sql = "select pf.pxf_funcao, f.fun_descricao, pid.pid_identificacao
                from Tabelas.dbo.Pessoa_Identificacoes pid
                inner join Tabelas.dbo.PessoasXFuncoes pf on pid.pid_pessoa = pf.pxf_pessoa
                 inner join Tabelas.dbo.Funcoes f on pf.pxf_funcao = f.fun_codigo
                 inner join Tabelas.dbo.Pessoa_Identificacoes pid2 on pf.pxf_entidade = pid2.pid_pessoa
                 where f.fun_status = 1 
                 and pid.pid_meta_dado = 1";

        if (!empty($funcao)) {
            $sql .= " AND pf.pxf_funcao = $funcao";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscaProjetoparaPublicacaoDOU()
    {
        $sql = "SELECT    Ap.idAprovacao, Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC, 
		Pr.NomeProjeto, Ar.Descricao as Area, St.Descricao as Situacao,
		CONVERT(CHAR(10),Ap.DtInicioCaptacao,103) as DataInicio, 
		CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFim, 
		Ap.AprovadoReal as VlAprovado, Pr.Processo, Pr.CgcCpf, Pr.UfProjeto, Pr.ResumoProjeto, 
		CASE 
						WHEN En.Municipio IS NULL
							THEN I.Cidade
						ELSE En.Cidade
					END AS Cidade,
					CASE 
						WHEN N.Descricao IS NULL
							THEN I.Nome
						ELSE N.Descricao
					END AS NomeProponente
					

				FROM 	SAC.dbo.Projetos Pr
						INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
							INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
							INNER JOIN SAC.dbo.Enquadramento Enq on Enq.IdPRONAC = Pr.IdPRONAC
							INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
							INNER JOIN AGENTES.dbo.Agentes Ag on Ag.CNPJCPF = Pr.CgcCpf
							INNER JOIN AGENTES.dbo.EnderecoNacional En on En.idAgente = Ag.idAgente
							INNER JOIN SAC.dbo.Interessado I on I.CgcCpf = Pr.CgcCpf
							INNER JOIN AGENTES.dbo.Nomes N ON N.idAgente = Ag.idAgente 
							 ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ProjetoparaPublicacaoDOU()
    {
        $sql = "SELECT    Ap.idAprovacao, Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC, 
		Pr.NomeProjeto, Ar.Descricao as Area, St.Descricao as Situacao,
		CONVERT(CHAR(10),Ap.DtInicioCaptacao,103) as DataInicio, 
		CONVERT(CHAR(10),Ap.DtFimCaptacao,103) as DataFim, 
		Ap.AprovadoReal as VlAprovado, Pr.Processo, Pr.CgcCpf, Pr.UfProjeto, Pr.ResumoProjeto, Ap.DtPortariaAprovacao, Ap.PortariaAprovacao, Ap.DtPublicacaoAprovacao,
		CASE 
						WHEN En.Municipio IS NULL
							THEN I.Cidade
						ELSE En.Cidade
					END AS Cidade,
					CASE 
						WHEN N.Descricao IS NULL
							THEN I.Nome
						ELSE N.Descricao
					END AS NomeProponente
					

				FROM 	SAC.dbo.Projetos Pr
						INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
							INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
							INNER JOIN SAC.dbo.Enquadramento Enq on Enq.IdPRONAC = Pr.IdPRONAC
							INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
							INNER JOIN AGENTES.dbo.Agentes Ag on Ag.CNPJCPF = Pr.CgcCpf
							INNER JOIN AGENTES.dbo.EnderecoNacional En on En.idAgente = Ag.idAgente
							INNER JOIN SAC.dbo.Interessado I on I.CgcCpf = Pr.CgcCpf
							LEFT JOIN AGENTES.dbo.Nomes N ON N.idAgente = Ag.idAgente 
						WHERE (St.Codigo = 'D27' OR St.Codigo = 'D28')";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ProjetoPublicadoDOU2001()
    {
        $sql = "SELECT   Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC,  
		Pr.NomeProjeto, Ar.Descricao as Area,

		CASE WHEN Enq.Enquadramento = 1 THEN 'Artigo 26'
		WHEN Enq.Enquadramento = 2 THEN 'Artigo 18'
		END AS Artigo, 
	
					
			CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) as DataPublicacao,
			CONVERT(CHAR(10),Ap.DtPortariaAprovacao,103) as DataPortaria,
			Ap.PortariaAprovacao
					FROM 	SAC.dbo.Projetos Pr
							INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
							INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
							INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
							INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
						WHERE Ap.PortariaAprovacao = '2003/10' and Pr.situacao = 'D09'";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ProjetoPublicadoDOU2002()
    {
        $sql = "SELECT   Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC,  
		Pr.NomeProjeto, Ar.Descricao as Area,

		CASE WHEN Enq.Enquadramento = 1 THEN 'Artigo 26'
		WHEN Enq.Enquadramento = 2 THEN 'Artigo 18'
		END AS Artigo, 
	
					
			CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) as DataPublicacao,
			CONVERT(CHAR(10),Ap.DtPortariaAprovacao,103) as DataPortaria,
			Ap.PortariaAprovacao
					FROM 	SAC.dbo.Projetos Pr
							INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
							INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
							INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
							INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
						WHERE Ap.PortariaAprovacao = '2007/10' and Pr.situacao = 'D09'";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarProjetosPublicados($portaria = null)
    {
        if (!empty($portaria)) { // filtra pela portaria
            $sql = "SELECT Pr.AnoProjeto+Pr.Sequencial AS pronac
						,Pr.IdPRONAC
						,Pr.NomeProjeto
						,Ar.Descricao AS Area
						,CASE 
							WHEN Enq.Enquadramento = 1 THEN 'Artigo 26'
							WHEN Enq.Enquadramento = 2 THEN 'Artigo 18'
						END AS Artigo
						,CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) AS DataPublicacao
						,CONVERT(CHAR(10),Ap.DtPortariaAprovacao,103) AS DataPortaria
						,Ap.PortariaAprovacao
						,Ap.idAprovacao

					FROM SAC.dbo.Projetos Pr
						INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
						INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
						INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
						INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC

					WHERE Pr.situacao = 'D09' 
						AND Ap.PortariaAprovacao = '$portaria'";
        } else {
            $sql = "SELECT DISTINCT Ap.PortariaAprovacao,
                                        CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) AS DataPublicacao
					FROM SAC.dbo.Projetos Pr
						INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
						INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
						INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
						INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC

					WHERE Pr.situacao = 'D09' 
						AND Ap.PortariaAprovacao LIKE '2%%%/%%'";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ProjetoPublicadoDOU($PortariaAprovacao)
    {
        $sql = "SELECT   Pr.AnoProjeto+Pr.Sequencial as pronac, Pr.IdPRONAC,  
		Pr.NomeProjeto, Ar.Descricao as Area,

		CASE WHEN Enq.Enquadramento = 1 THEN 'Artigo 26'
		WHEN Enq.Enquadramento = 2 THEN 'Artigo 18'
		END AS Artigo, 
			CONVERT(CHAR(10),Ap.DtPublicacaoAprovacao,103) as DataPublicacao,
			CONVERT(CHAR(10),Ap.DtPortariaAprovacao,103) as DataPortaria,
			Ap.PortariaAprovacao
					FROM 	SAC.dbo.Projetos Pr
							INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
							INNER JOIN SAC.dbo.Area Ar on Ar.Codigo = Pr.Area
							INNER JOIN SAC.dbo.Enquadramento as Enq on Enq.IdPRONAC = Pr.IdPRONAC
							INNER JOIN SAC.dbo.Aprovacao Ap on Ap.IdPRONAC = Pr.IdPRONAC
						WHERE Ap.PortariaAprovacao = '$PortariaAprovacao' and Pr.situacao = 'D09'";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function cadastrarportaria($dados, $id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $where = "idAprovacao = " . (int)$id;

        $alterar = $db->update("SAC.dbo.Aprovacao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }

    public static function gerarportaria($dados, $idAprovacao)
    {
        //		$db= Zend_Db_Table::getDefaultAdapter();
        //		$db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "UPDATE SAC.dbo.Aprovacao SET PortariaAprovacao = '$PortariaAprovacao', DtPortariaAprovacao = '$DtPortariaAprovacao' where idAprovacao = $idAprovacao";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    }

    public static function situcaopublicacaodou($TipoAprovacao, $Portaria, $novaSituacao, $situacaoAtual, $usuarioLogado, $orgaoSuperior)
    {
        try {
            $ProvidenciaTomada = 'Projeto aprovado e publicado no Di&aacute;rio Oficial da Uni&atilde;o.';
            if ($TipoAprovacao == 2) {
                $ProvidenciaTomada = 'Complementa&ccedil;&atilde;o aprovada e publicada no Di&aacute;rio Oficial da Uni&atilde;o.';
            } elseif ($TipoAprovacao == 3) {
                $ProvidenciaTomada = 'Prorroga&ccedil;&atilde;o aprovada e publicada no Di&aacute;rio Oficial da Uni&atilde;o.';
            } elseif ($TipoAprovacao == 4) {
                $ProvidenciaTomada = 'Redu&ccedil;&atilde;o aprovada e publicada no Di&aacute;rio Oficial da Uni&atilde;o.';
            }

            $area = ' p.Area = 2 ';
            if ($orgaoSuperior == Orgaos::ORGAO_SUPERIOR_SEFIC) {
                $area = ' p.Area <> 2 ';
            }

            $sql = "
                UPDATE SAC.dbo.Projetos
                SET Situacao = '$novaSituacao',
                ProvidenciaTomada = '$ProvidenciaTomada',
                DtSituacao = GETDATE(),
                Logon = '$usuarioLogado'
                FROM SAC.dbo.Projetos p 
                INNER JOIN SAC.dbo.Aprovacao a ON (p.AnoProjeto = a.AnoProjeto AND p.Sequencial = a.Sequencial)
                WHERE a.TipoAprovacao = '$TipoAprovacao'
                    AND $area
                    AND p.Situacao = '$situacaoAtual'
                    AND a.PortariaAprovacao = '$Portaria' ";

            if ($TipoAprovacao != 5 && $TipoAprovacao != 6) {
                if ($novaSituacao == 'E12') {
                    $sql .= "AND EXISTS(SELECT TOP 1 * FROM SAC.dbo.Captacao c WHERE c.AnoProjeto = p.AnoProjeto and c.Sequencial = p.Sequencial)";
                } else {
                    $sql .= "AND NOT EXISTS(SELECT TOP 1 * FROM SAC.dbo.Captacao c WHERE c.AnoProjeto = p.AnoProjeto and c.Sequencial = p.Sequencial)";
                }
            }

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $alterar = $db->fetchRow($sql);
        } catch (exception $e) {
        }
    }

    public static function situcaopublicar($dados, $IdPRONAC)
    {
        try {
            $situacao = $dados['Situacao'];
            $ProvidenciaTomada = $dados['ProvidenciaTomada'];
            $DtInicioExecucao = strftime("%Y-%m-%d %H:%M:%S", strtotime("+1 days"));

            $sql = "UPDATE SAC.dbo.Projetos SET Situacao = '$situacao', ProvidenciaTomada = '$ProvidenciaTomada', DtInicioExecucao = '$DtInicioExecucao' WHERE IdPRONAC = '" . $IdPRONAC . "'";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $alterar = $db->fetchRow($sql);
        } catch (exception $e) {
        }
    }

    public static function retirarpublicacao($dados, $IdPRONAC)
    {
        $situacao = $dados['Situacao'];
        $dtSituacao = $dados['dtSituacao'];
        $ProvidenciaTomada = $dados['ProvidenciaTomada'];

        $sql = "UPDATE SAC.dbo.Projetos SET Situacao = '$situacao', ProvidenciaTomada = '$ProvidenciaTomada', dtSituacao = '$dtSituacao' WHERE IdPRONAC = '" . $IdPRONAC . "'";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $alterar = $db->fetchRow($sql);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }

    public static function apagarpublicacao($idAprovacao)
    {
        $sql = "UPDATE SAC.dbo.Aprovacao SET PortariaAprovacao = null, DtPortariaAprovacao = null, DtPublicacaoAprovacao = null, DtInicioCaptacao = null, DtFimCaptacao = null
				WHERE idAprovacao = '" . $idAprovacao . "'";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    }

    public static function alterarsitucaopublicar($idPronac)
    {
        $valida = false;

        $sql = "UPDATE SAC.dbo.Projetos SET Situacao = 'E10' WHERE IdPRONAC = $idPronac";

        $db = Zend_Db_Table::getDefaultAdapter();
        if ($db->setFetchMode(Zend_DB::FETCH_ASSOC)) {
            $valida = true;
        }
        return $valida;
    }

    public static function alterardatapublicacao($dados, $portaria)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = " PortariaAprovacao = '$portaria'";
        $alterar = $db->update("SAC.dbo.aprovacao", $dados, $where);
    }

    public static function buscaCargosPublicacao()
    {
        $sql = "SELECT Descricao FROM Agentes.dbo.Verificacao WHERE idTipo=24 and sistema=21 ORDER BY Descricao";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscaNomesPublicacao()
    {
        $sql = "SELECT Descricao FROM Agentes.dbo.Verificacao WHERE idTipo=26 and sistema=21 ORDER BY Descricao";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
