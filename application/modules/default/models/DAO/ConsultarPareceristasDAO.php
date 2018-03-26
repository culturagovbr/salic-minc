<?php
class ConsultarPareceristasDAO extends Zend_Db_Table
{
    public static function buscarPronacs($idPronac, $area = null, $segmento = null)
    {
        $sql = "select distinct
					p.IdPRONAC, 
					p.AnoProjeto+Sequencial as Pronac, 
					p.Area, 
					p.Segmento, 
					p.NomeProjeto as NomeProjeto, 
					p.Situacao, 
					p.DtAnalise, 
					p.UnidadeAnalise, 
					p.Analista, 
					p.idProjeto 
				from SAC.dbo.Projetos p
				where IdPRONAC = $idPronac";
        
        if (!empty($area)) {
            $sql .= " AND p.Area = $area";
        }
        
        if (!empty($segmento)) {
            $sql .= " AND p.Segmento = $segmento";
        }
        
        $sql .= " order by p.IdPRONAC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
    
    public static function buscarOrgaos($idAgente, $orgao, $idPronac)
    {
        $sql = "select distinct
					a.idAgente, 
					u.uog_orgao,
					dp.idPRONAC as idPronac, 
					dp.idProduto as idProduto,
					dp.stEstado as TipoParecer 
				From AGENTES.dbo.Agentes a
				inner join SAC.dbo.tbDistribuirParecer dp on dp.idAgenteParecerista = a.idAgente
				inner join TABELAS.dbo.vwUsuariosOrgaosGrupos u on u.usu_identificacao = a.CNPJCPF
				INNER JOIN Agentes.dbo.Nomes AS n ON a.idAgente = n.idAgente
				INNER JOIN Agentes.dbo.Visao AS v ON n.idAgente = v.idAgente 
				where a.idAgente = $idAgente and idPRONAC = $idPronac
				and (v.Visao = 209) AND (n.TipoNome = 18) AND (u.sis_codigo = 21) AND (u.gru_codigo = 94) 
				and u.uog_orgao = $orgao";
        

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
    
    public static function buscarProdutos($idAgente, $stPrincipal = null, $idOrgao = null, $idArea = null, $idSegmento = null, $dias = null)
    {
        $sql = "SELECT p.IdPRONAC, t.idDistribuirParecer, t.idOrgao, t.idAgenteParecerista, t.idProduto, 
				t.DtDevolucao, t.TipoAnalise, t.DtEnvio, t.stPrincipal, 
				CONVERT(CHAR(10),t.DtDistribuicao,103) AS DtDistribuicaoPT, 
				CONVERT(CHAR(10),t.DtEnvio,103) AS DtEnvioPT, 
				DATEDIFF(day, t.DtEnvio,t.DtDistribuicao) AS nrDias, 
				agentes.dbo.fnNome(t.idAgenteParecerista) AS nomeParecerista, 
				CASE 
				WHEN TipoAnalise = 0 THEN 'Cont�udo' 
				WHEN TipoAnalise = 1 THEN 'Custo do Produto' 
				ELSE 'Custo Administrativo' 
				END AS DescricaoAnalise, 
				SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs, 
				pp.idPagamentoParecerista, pp.idComprovantePagamento, p.IdPRONAC, (p.AnoProjeto + p.Sequencial) AS NrProjeto, 
				p.NomeProjeto, r.Descricao AS Produto, a.Descricao AS Area, s.Descricao AS Segmento, ac.ParecerFavoravel AS Status,
				pp.idComprovantePagamento as memorando, pp.siPagamento, pp.vlPagamento
				FROM SAC.dbo.tbDistribuirParecer AS t
				 LEFT JOIN AGENTES.dbo.tbPagamentoParecerista AS pp ON pp.idProduto = t.idDistribuirParecer
				 INNER JOIN SAC.dbo.Projetos AS p ON t.idPRONAC = p.IdPRONAC
				 INNER JOIN SAC.dbo.Produto AS r ON t.idProduto = r.Codigo
				 INNER JOIN SAC.dbo.Area AS a ON p.Area = a.Codigo
				 INNER JOIN SAC.dbo.Segmento AS s ON p.Segmento = s.Codigo
				 LEFT JOIN SAC.dbo.tbAnaliseDeConteudo AS ac ON ac.IdPRONAC = t.IdPRONAC AND ac.idProduto = t.idProduto 
				 WHERE (t.stEstado = 0)  AND (t.TipoAnalise <> 2) AND (p.Situacao IN ('B11','B14')) 
				 AND (pp.idProduto is not null ) AND (t.idAgenteParecerista = '$idAgente')";
        
        if (!empty($idOrgao)) {
            $sql .= " and t.idOrgao = $idOrgao";
        }
        
        if (!empty($stPrincipal)) {
            $sql .= " and t.stPrincipal = $stPrincipal";
        }
        
        if (!empty($idArea)) {
            $sql .= " and a.Codigo = $idArea";
        }
        
        if (!empty($idSegmento)) {
            $sql .= " and s.Codigo = $idSegmento";
        }
        
        if (!empty($dias)) {
            $sql .= " and DATEDIFF(day, t.DtEnvio,t.DtDistribuicao) = $dias";
        }
        
        $sql .= " ORDER BY p.IdPRONAC";
 
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
    
    public static function buscarProdutosPareceristas(
    
        $idAgente,
    
        $stPrincipal = null,
    
        $dataInicio = null,
    
        $dataFim = null,
                                $parecer = null,
    
        $idpronac = null,
    
        $tipo_pagamento = null,
    
        $pronac = null
    
    ) {
        $sql = "select 
					a.idAgente, 
					pro.AnoProjeto + pro.Sequencial as pronac,
					dp.idPRONAC as idPronac, 
					dp.idProduto as idProduto,
					p.Descricao, 
					pro.NomeProjeto AS NomeProjeto, 
					dp.stPrincipal,
					cp.idDocumento,
					pp.vlPagamento as vlPagamento,
					pp.siPagamento,
					CASE
					WHEN pp.siPagamento = 1 THEN 'Efetuado'
					WHEN pp.siPagamento = 0 THEN 'Pendentes'
					END AS siPagamento,
					cp.idDocumento as memorando,
					CONVERT(CHAR(10), cp.dtPagamento,103) as dtPagamento,
					cp.nrOrdemPagamento as OrdemPagamento,
					dp.TipoAnalise,
					Area.Descricao as Area,
					Seg.Descricao as Segmento,
					pp.siPagamento as TipoParecer 
				From AGENTES.dbo.Agentes a
				inner join SAC.dbo.tbDistribuirParecer dp on dp.idAgenteParecerista = a.idAgente
				INNER JOIN SAC.dbo.Projetos AS pro ON dp.idPRONAC = pro.IdPRONAC
				inner join SAC.dbo.Produto p on p.Codigo = dp.idProduto
				inner join SAC.dbo.Area Area ON Area.Codigo = pro.Area
				inner join SAC.dbo.Segmento Seg ON Seg.Codigo = pro.Segmento 
				inner join AGENTES.dbo.tbPagamentoParecerista pp on pp.idProduto = dp.idDistribuirParecer
				inner join AGENTES.dbo.tbComprovantePagamento cp on cp.idComprovantePagamento = pp.idComprovantePagamento
				where a.idAgente = ".$idAgente;
        
        if (!empty($stPrincipal)) {
            if ($stPrincipal == 2) {
                $sql .= " and stPrincipal = 0";
            } else {
                $sql .= " and stPrincipal = $stPrincipal";
            }
        }
        
        if (!empty($dataInicio) && empty($dataFim)) {
            $sql .= " and CONVERT(char(10), cp.dtPagamento, 103) >= CONVERT(char(10), $dataInicio, 103)";
        }
        
        if (empty($dataInicio) && !empty($dataFim)) {
            $sql .= " and CONVERT(char(10), cp.dtPagamento, 103) <= CONVERT(char(10), $dataFim, 103)";
        }
        
        if (!empty($dataInicio) && !empty($dataFim)) {
            $sql .= " and CONVERT(char(10), cp.dtPagamento, 103) >= CONVERT(char(10), $dataInicio, 103) and CONVERT(char(10), cp.dtPagamento, 103) <= CONVERT(char(10), $dataFim, 103)";
        }
        
        if (!empty($parecer)) {
            if ($parecer == 4) {
                $sql .= " and pp.siPagamento = $parecer";
            } elseif ($parecer == 1) {
                $sql .= " and pp.siPagamento != 4";
            }
        }
        
        if (!empty($idpronac)) {
            $sql .= " and dp.idPRONAC = '$idpronac'";
        }
        
        if (isset($tipo_pagamento)) {
            if ($tipo_pagamento == 2) {
            } else {
                $sql .= " and pp.siPagamento = $tipo_pagamento";
            }
        }
        
        if ($pronac) {
            $sql .= " and pro.AnoProjeto + pro.Sequencial = $pronac";
        }
        
        $sql .= " order by dp.idPRONAC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
    
    public static function buscarAusencias($tpAusencia = null, $x = null, $idAgente, $dataInicio, $dataFim)
    {
        $sql = "SELECT  
					au.idAusencia, 
					au.idTipoAusencia, 
					au.idAgente, 
					CONVERT(CHAR(10), au.dtInicioAusencia,103) as dtInicio,
					CONVERT(CHAR(10), au.dtFimAusencia ,103) as dtFim
				FROM AGENTES.dbo.tbAusencia au
				INNER JOIN AGENTES.dbo.tbTipoAusencia tp on tp.idTipoAusencia = au.idTipoAusencia
				WHERE au.idAgente = $idAgente";
    
        if ($tpAusencia == 2 and $x == 1) {
            $sql .= " AND tp.idTipoAusencia = 2 AND CONVERT(date, au.dtInicioAusencia, 105) < CONVERT(date, GETDATE(), 105)";
        } elseif ($tpAusencia == 2 and $x == 2) {
            $sql .= " AND tp.idTipoAusencia = 2 AND CONVERT(date, au.dtInicioAusencia, 105) > CONVERT(date, GETDATE(), 105)";
        } elseif ($tpAusencia == 1 and $x == 3) {
            $sql .= " AND tp.idTipoAusencia = 1";
        }
        
        if (!empty($dataInicio) and empty($dataFim)) {
            $sql .= " AND CONVERT(char(10), au.dtInicioAusencia, 103) >= '$dataInicio'";
        }
        if (empty($dataInicio) and !empty($dataFim)) {
            $sql .= " AND CONVERT(char(10), au.dtFimAusencia, 103) = '$dataFim'";
        }
        if (!empty($dataInicio) and !empty($dataFim)) {
            $sql .= " AND CONVERT(char(10), au.dtInicioAusencia, 103) >= '$dataInicio' and CONVERT(char(10), au.dtFimAusencia, 103) <= '$dataFim'";
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
