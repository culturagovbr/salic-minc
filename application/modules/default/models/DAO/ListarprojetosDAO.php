<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbrangenciaDAO
 *
 * @author 01129075125
 */
class ListarprojetosDAO extends Zend_Db_Table
{
    public static function buscarProjetos($idAgente = null, $dataInicio = null, $dataFim = null, $idAgenteLogado, $tipoProponente = null)
    {
        $sql = "
        	SELECT ag.CNPJCPF as CgcCpf, a.idPreProjeto, a.idAgente, p.AnoProjeto+p.Sequencial as Pronac, a.NomeProjeto, p.IdPRONAC,
			        CONVERT(CHAR(10),a.DtInicioDeExecucao,103) AS DtInicioDeExecucao, 
			        CONVERT(CHAR(10),a.DtFinalDeExecucao,103) AS DtFinalDeExecucao,  
			        ag.CNPJCPF, m.Descricao AS NomeProponente, 
			        a.idEdital, p.Situacao, si.Descricao, a.Mecanismo 
			 FROM SAC.dbo.PreProjeto AS a
				 INNER JOIN AGENTES.dbo.Agentes AS ag ON a.idAgente = ag.idAgente
				 INNER JOIN AGENTES.dbo.Nomes AS m ON a.idAgente = m.idAgente
				 INNER JOIN SAC.dbo.Projetos p on p.idProjeto = a.idPreProjeto
				 INNER JOIN SAC.dbo.Situacao si on si.Codigo = p.Situacao";
        
                
        $where = " WHERE (a.stEstado = 1) AND (a.idEdital is null or a.idEdital = 0) AND (a.idAgente IN (".$idAgenteLogado.")) ";
        if (!empty($tipoProponente)) {
            $sql .= " INNER JOIN AGENTES.dbo.tbVinculo VI ON AG.idAgente = VI.idAgenteProponente
						  INNER JOIN AGENTES.dbo.tbVinculoProposta VP ON VI.idVinculo = VP.idVinculo
						  INNER JOIN AGENTES.dbo.tbProcuracao PRO ON VP.idVinculoProposta = PRO.idVinculoProposta ";
                
            $where =  " WHERE (a.stEstado = 1) AND (a.idEdital is null or a.idEdital = 0) AND (a.idAgente IN (".$idAgente.")) AND PRO.siProcuracao = 1 ";
        }

        $sql .= $where;
            

        // $sql .= " WHERE (a.stEstado = 1) AND (a.idEdital is null or a.idEdital = 0) AND (a.idAgente IN (".$idAgente.")) ";
        
        
        if (!empty($dataInicio)) {
            $sql .= " AND CONVERT(CHAR(10), a.DtInicioExecucao, 103) =  '$dataInicio'";
        }

        if (!empty($dataFim)) {
            $sql .= " AND CONVERT(CHAR(10), a.DtFimExecucao, 103) =  '$dataFim'";
        }

        $sql .= " ORDER BY m.Descricao, a.NomeProjeto ASC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarProjetosEdital($idAgente = null, $dataInicio = null, $dataFim = null, $fundo = null, $classificacao = null, $idAgenteLogado, $tipoProponente = null)
    {
        $sql = "SELECT 
        			p.CgcCpf,
					a.idPreProjeto, a.idAgente, p.AnoProjeto+p.Sequencial as Pronac, a.NomeProjeto, p.IdPRONAC,
			        CONVERT(CHAR(10),a.DtInicioDeExecucao,103) AS DtInicioDeExecucao, 
			        CONVERT(CHAR(10),a.DtFinalDeExecucao,103) AS DtFinalDeExecucao,  
			        ag.CNPJCPF, m.Descricao AS NomeProponente, 
			        a.idEdital, p.Situacao, si.Descricao, 
			        v.Descricao as Fundo, 
			        c.idTipoDocumento,
			        c.idClassificaDocumento,
			        c.dsClassificaDocumento as Classificacao, a.Mecanismo 
						FROM SAC.dbo.PreProjeto AS a
							INNER JOIN AGENTES.dbo.Agentes AS ag ON a.idAgente = ag.idAgente
							INNER JOIN AGENTES.dbo.Nomes AS m ON a.idAgente = m.idAgente
							INNER JOIN SAC.dbo.Projetos p on p.idProjeto = a.idPreProjeto
							INNER JOIN SAC.dbo.Situacao si on si.Codigo = p.Situacao
							INNER JOIN SAC.dbo.Edital ed on ed.idEdital = a.idEdital
							INNER JOIN SAC.dbo.Verificacao v on v.idVerificacao = ed.cdTipoFundo
							INNER JOIN BDCORPORATIVO.scQuiz.tbFormDocumento f ON f.idEdital = a.idEdital
							INNER JOIN BDCORPORATIVO.scSAC.tbClassificaDocumento c on c.idClassificaDocumento = f.idClassificaDocumento";
        
        
        $where = " WHERE (a.stEstado = 1) 
			 		AND (a.idAgente IN (".$idAgenteLogado.")) 
			 		AND a.idEdital is not null and c.idClassificaDocumento != 23 
			 		and c.idClassificaDocumento != 24 
			 		and c.idClassificaDocumento != 25";
         
        if (!empty($tipoProponente)) {
            $sql .= " INNER JOIN AGENTES.dbo.tbVinculo VI ON AG.idAgente = VI.idAgenteProponente
						  INNER JOIN AGENTES.dbo.tbVinculoProposta VP ON VI.idVinculo = VP.idVinculo
						  INNER JOIN AGENTES.dbo.tbProcuracao PRO ON VP.idVinculoProposta = PRO.idVinculoProposta ";
                
            $where =  " WHERE (a.stEstado = 1) 
        					AND a.idEdital is not null 
        					AND c.idClassificaDocumento != 23 
			 				AND c.idClassificaDocumento != 24 
			 				AND c.idClassificaDocumento != 25 
			 				AND (a.idAgente IN (".$idAgente.")) 
			 				AND PRO.siProcuracao = 1 ";
        }

        $sql .= $where;
            
            
        


        
        
        
        
        
        
        if (!empty($dataInicio)) {
            $sql .= " AND CONVERT(CHAR(10), a.DtInicioExecucao, 103) =  '$dataInicio'";
        }

        if (!empty($dataFim)) {
            $sql .= " AND CONVERT(CHAR(10), a.DtFimExecucao, 103) =  '$dataFim'";
        }

        if (!empty($fundo)) {
            $sql .= " AND v.idVerificacao =  $fundo";
        }

        if (!empty($classificacao)) {
            $sql .= " AND c.idClassificaDocumento =  $classificacao";
        }

        $sql .= " ORDER BY m.Descricao, a.NomeProjeto ASC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarTodosProjetos($idAgente = null, $idAgenteLogado = null, $tipoProponente = null, $idResponsavel = null)
    {
        $sql = "
        	SELECT ag.CNPJCPF as CgcCpf, a.idPreProjeto, a.idAgente, p.AnoProjeto+p.Sequencial as Pronac, a.NomeProjeto, p.IdPRONAC,
			        CONVERT(CHAR(10),a.DtInicioDeExecucao,103) AS DtInicioDeExecucao, 
			        CONVERT(CHAR(10),a.DtFinalDeExecucao,103) AS DtFinalDeExecucao,  
			        ag.CNPJCPF, m.Descricao AS NomeProponente, 
			        a.idEdital, p.Situacao, si.Descricao, a.Mecanismo  
			 FROM SAC.dbo.PreProjeto AS a
				 INNER JOIN AGENTES.dbo.Agentes AS ag ON a.idAgente = ag.idAgente
				 INNER JOIN AGENTES.dbo.Nomes AS m ON a.idAgente = m.idAgente
				 INNER JOIN SAC.dbo.Projetos p on p.idProjeto = a.idPreProjeto
				 INNER JOIN SAC.dbo.Situacao si on si.Codigo = p.Situacao";
             
        $where = " WHERE a.idAgente IN (" . $idAgenteLogado . ")";
        if (!empty($tipoProponente)) {
            $sql .= " INNER JOIN AGENTES.dbo.tbVinculo VI ON AG.idAgente = VI.idAgenteProponente
						  INNER JOIN AGENTES.dbo.tbVinculoProposta VP ON VI.idVinculo = VP.idVinculo
						  INNER JOIN AGENTES.dbo.tbProcuracao PRO ON VP.idVinculoProposta = PRO.idVinculoProposta ";
                
            $where =  " WHERE (a.idAgente IN (".$idAgente.")) AND PRO.siProcuracao = 1 ";
        }

        $sql .= $where;

        $sql .= " ORDER BY m.Descricao, a.NomeProjeto ASC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarTodosProjetosResponsavel($idResponsavel, $idProponente, $mecanismo = false)
    {
        $sql = "(SELECT  0 as Ordem,a.CgcCpf as CNPJCPF, b.idAgente, dbo.fnNome(b.idAgente) AS NomeProponente,a.AnoProjeto+a.Sequencial as Pronac,a.NomeProjeto,
                    a.Situacao + ' - ' + d.Descricao as Situacao
                FROM SAC.dbo.Projetos                      a
                INNER JOIN AGENTES.dbo.Agentes             b on (a.CgcCpf   = b.CNPJCPF)
                INNER JOIN CONTROLEDEACESSO.dbo.SGCacesso  c on (a.CgcCpf   = c.Cpf)
                INNER JOIN SAC.dbo.Situacao                d on (a.Situacao = d.Codigo)
                WHERE c.IdUsuario = $idResponsavel)

                UNION ALL
                
                (SELECT  1 as Ordem,a.CgcCpf as CNPJCPF, b.idAgente, dbo.fnNome(b.idAgente) AS NomeProponente,a.AnoProjeto+a.Sequencial as Pronac,a.NomeProjeto,
                    a.Situacao + ' - ' + g.Descricao as Situacao
                FROM SAC.dbo.Projetos                      a
                INNER JOIN AGENTES.dbo.Agentes             b on (a.CgcCpf       = b.CNPJCPF)
                INNER JOIN AGENTES.dbo.tbProcuradorProjeto c on (a.IdPRONAC     = c.idPronac)
                INNER JOIN AGENTES.dbo.tbProcuracao        d on (c.idProcuracao = d.idProcuracao)
                INNER JOIN AGENTES.dbo.Agentes             f on (d.idAgente     = f.idAgente)
                INNER JOIN CONTROLEDEACESSO.dbo.SGCacesso  e on (f.CNPJCPF      = e.Cpf)
                INNER JOIN SAC.dbo.Situacao                g on (a.Situacao     =   g.Codigo)
                WHERE c.siEstado = 2 and e.IdUsuario = $idResponsavel ";

        if ($mecanismo) {
            $sql .= " and a.Mecanismo = $mecanismo";
        }

        $sql .=")
            
                UNION ALL

                (SELECT  1 as Ordem,a.CgcCpf as CNPJCPF, b.idAgente, dbo.fnNome(b.idAgente) AS NomeProponente,a.AnoProjeto+a.Sequencial as Pronac,a.NomeProjeto,
                    a.Situacao + ' - ' + f.Descricao as Situacao
                FROM SAC.dbo.Projetos                      a
                INNER JOIN AGENTES.dbo.Agentes             b on (a.CgcCpf       = b.CNPJCPF)
                INNER JOIN AGENTES.dbo.Vinculacao          c on (b.idAgente     = c.idVinculoPrincipal)
                INNER JOIN AGENTES.dbo.Agentes             d on (c.idAgente     = d.idAgente)
                INNER JOIN CONTROLEDEACESSO.dbo.SGCacesso  e on (d.CNPJCPF      = e.Cpf)
                INNER JOIN SAC.dbo.Situacao                f on (a.Situacao     = f.Codigo)
                WHERE e.IdUsuario = $idResponsavel ";

        if ($mecanismo) {
            $sql .= " and a.Mecanismo = $mecanismo";
        }

        $sql .=" ) ORDER BY 1,6 ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarTodosProjetosExecucao($idAgente = null)
    {
        $sql = "
        	SELECT top 100 p.CgcCpf, a.idPreProjeto, a.idAgente, p.AnoProjeto+p.Sequencial as Pronac, a.NomeProjeto, p.IdPRONAC,
			        CONVERT(CHAR(10),a.DtInicioDeExecucao,103) AS DtInicioDeExecucao, 
			        CONVERT(CHAR(10),a.DtFinalDeExecucao,103) AS DtFinalDeExecucao,  
			        ag.CNPJCPF, m.Descricao AS NomeProponente, 
			        a.idEdital, p.Situacao, si.Descricao, a.Mecanismo
			        FROM SAC.dbo.PreProjeto AS a
			 INNER JOIN AGENTES.dbo.Agentes AS ag ON a.idAgente = ag.idAgente
			 INNER JOIN AGENTES.dbo.Nomes AS m ON a.idAgente = m.idAgente
			 INNER JOIN SAC.dbo.Projetos p on p.idProjeto = a.idPreProjeto
			 INNER JOIN SAC.dbo.Situacao si on si.Codigo = p.Situacao
			 WHERE (a.stEstado = 1) ";
        $sql .="AND (a.idAgente = $idAgente) ";

        $sql .= " ORDER BY a.NomeProjeto ASC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscaProponentes($cpf = null)
    {
        $sql = "SELECT  DISTINCT p.CgcCpf as CNPJCPF,  n.Descricao, a.idAgente
        FROM SAC.dbo.Projetos as p
        INNER JOIN SAC.dbo.Situacao as s on (p.Situacao = s.Codigo)
        INNER JOIN SAC.dbo.PreProjeto pp on (pp.idPreProjeto = p.idProjeto)
        INNER JOIN AGENTES.dbo.Agentes a on (p.CgcCpf = a.CNPJCPF)
        INNER JOIN AGENTES.dbo.Nomes n on (a.idAgente = n.idAgente) ";

        if (!empty($cpf)) {
            $sql .= "WHERE p.CgcCpf =  '$cpf'";
        }
        $sql .= " order by n.Descricao DESC";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscaProponentesVinculados($idResponsavel)
    {
        $sql = "SELECT AG.idAgente, AG.CNPJCPF, NM.Descricao as Nome , VI.idVinculo, VI.siVinculo 
				FROM AGENTES.dbo.Agentes AG
				INNER JOIN AGENTES.dbo.Nomes NM ON AG.idAgente = NM.idAgente
				LEFT JOIN AGENTES.dbo.tbVinculo VI ON AG.idAgente = VI.idAgenteProponente
				WHERE VI.idUsuarioResponsavel = '".$idResponsavel."'
				AND VI.siVinculo IN (0,2)
				ORDER BY NM.Descricao ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscaProponentes2($cpf = null)
    {
        $sql = "SELECT  DISTINCT a.CNPJCPF,  n.Descricao, a.idAgente
        FROM AGENTES.dbo.Agentes as a
        INNER JOIN AGENTES.dbo.Nomes n on (a.idAgente = n.idAgente) ";

        if (!empty($cpf)) {
            $sql .= "WHERE a.CNPJCPF =  '$cpf'";
        }
        $sql .= " order by n.Descricao DESC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
