<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Desciption of TramitarprojetosDAO
 *
 * @author tisomar
 */
class TramitarprojetosDAO extends Zend_Db_Table
{
    public static function buscaOrgao($idOrigem = null)
    {
        $sql = "select Sigla from SAC.dbo.Orgaos 
		where Codigo = $idOrigem
		";

        /*
          $sql = "select Sigla from SAC.dbo.Orgaos o
          INNER JOIN  SAC.dbo.tbHistoricoDocumento th ON  th.Acao  != 6 AND th.idPronac != '' AND th.idDocumento is NUll
          where o.Codigo = $idOrigem
          ";


         */

        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function atualizaProjeto($idPronac, $idDestino)
    {
        $sql = "UPDATE 
					SAC.dbo.Projetos SET Orgao = $idDestino where IdPRONAC = $idPronac";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaProjeto($pronac)
    {
        $sql = "SELECT org.Sigla, p.*, p.IdPRONAC as idPronac
				FROM SAC.dbo.Projetos p, SAC.dbo.Orgaos org
				WHERE p.Orgao = org.Codigo AND (AnoProjeto+Sequencial) = '" . $pronac . "'";
                    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscaProjetoPDF($pronac)
    {
        $sql = "SELECT org.Sigla, p.*, p.IdPRONAC as idPronac, p.AnoProjeto+Sequencial as pronacp
				FROM SAC.dbo.Projetos p, SAC.dbo.Orgaos org
				WHERE p.Orgao = org.Codigo AND p.IdPRONAC = '" . $pronac . "'";
                    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscaProjetoUnidade($idPronac)
    {
        $sql = " SELECT AnoProjeto+Sequencial as pronac, NomeProjeto, p.IdPRONAC, Situacao, ProvidenciaTomada, Orgao, ar.stAcao, stEstado, ar.idArquivamento
				 FROM SAC.dbo.Projetos p
				 INNER JOIN SAC.dbo.tbArquivamento ar on ar.idPronac = p.IdPRONAC
				 WHERE p.Orgao = 290 and ar.stEstado = 1 and p.IdPRONAC = $idPronac";

        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscaProjetoExistente($idPronac)
    {
        $sql = " SELECT AnoProjeto+Sequencial as pronac, NomeProjeto, p.IdPRONAC, Situacao, ProvidenciaTomada, Orgao
				 FROM SAC.dbo.Projetos p
				 WHERE p.IdPRONAC = $idPronac";

        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function alterarSituacao($situacao, $providenciaTomada, $idPronac)
    {
        $sql = "UPDATE SAC.dbo.Projetos SET Situacao = '$situacao', ProvidenciaTomada = '$providenciaTomada' 
				WHERE idPronac =  $idPronac";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao Arquivar: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function alterarStatusArquivamento($idPronac)
    {
        $sql = "UPDATE SAC.dbo.tbArquivamento 
					SET stEstado = 0 
				WHERE idPronac =  $idPronac and stEstado = 1";

        //print_r($sql);die;
        
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao Arquivar: " . $e->getMessage();
        }
        
        return $db->fetchAll($sql);
    }

    public static function arquivarProjeto($idPronac = null, $stAcao, $cxInicio = null, $cxFinal = null, $idusuario, $idArquivamento = null, $x = null)
    {
        if ($x) {
            $sql = "INSERT INTO 
						sac.dbo.tbArquivamento (idPronac, Data, Edificio, CaixaInicio, CaixaFinal, stAcao, stEstado, idUsuario) 
						VALUES ($idPronac, getdate(), 0, $cxInicio, $cxFinal, $stAcao, 1, $idusuario)";
        } else {
            $sql = "INSERT INTO 
							sac.dbo.tbArquivamento (idPronac, Data, Edificio, stAcao, stEstado, idUsuario, CaixaInicio, CaixaFinal) 
						SELECT $idPronac, data = getdate(), Edificio, stAcao = $stAcao, stEstado = 1, idUsuario = $idusuario,";

            if (($cxInicio) && ($cxFinal)) {
                $sql.= " CaixaInicio = $cxInicio, CaixaFinal = $cxFinal ";
            } else {
                $sql.= " CaixaInicio, CaixaFinal ";
            }
            if ($idArquivamento) {
                $sql.= " FROM sac.dbo.tbArquivamento where idArquivamento = $idArquivamento";
            } else {
                $sql.= " FROM sac.dbo.tbArquivamento where idPronac = $idPronac";
            }
        }

        //print_r($sql);die;
        
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao Arquivar: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function projetosArquivados($idusuario, $pronac = null, $tipo_nome = null, $nome = null, $tipo_processo = null, $processo = null, $tipo_dtArquivo = null, $dtArquivI = null, $dtArquivInull = null, $dtArquivF = null, $tipo_cxInicio = null, $cxInicio = null, $tipo_cxFinal = null, $cxFinal = null)
    {
        $sql = " SELECT top 1000
					ar.idArquivamento,
					--p.Processo,
                                        SAC.dbo.fnFormataProcesso(p.IdPRONAC) AS Processo,
					AnoProjeto+Sequencial as pronac, 
					NomeProjeto, 
					p.IdPRONAC as idPronac, 
					Situacao, 
					ProvidenciaTomada, 
					Orgao, 
					ar.CaixaInicio, 
					ar.CaixaFinal, 
					ar.stAcao, 
					stEstado, 
					convert(varchar,Data,103)+' '+convert(varchar,Data,108) as Data
				FROM SAC.dbo.Projetos p
				INNER JOIN SAC.dbo.tbArquivamento ar on ar.idPronac = p.IdPRONAC
				WHERE p.Orgao = 290 and stAcao = 0 and stEstado = 1";

        if ($pronac) {
            $sql.= " AND AnoProjeto+Sequencial =  '$pronac'";
        }

        if ($nome) {
            if ($tipo_nome == 1) {
                $sql .= " AND NomeProjeto like '$nome%'";
            }

            if ($tipo_nome == 2) {
                $sql .= " AND NomeProjeto like '%$nome%'";
            }

            if ($tipo_nome == 3) {
                $sql .= " AND NomeProjeto = '$nome'";
            }
        }

        if ($processo) {
            if ($tipo_processo == 1) {
                $sql .= " AND Processo like '%$processo%'";
            }

            if ($tipo_processo == 2) {
                $sql .= " AND Processo = '$processo'";
            }
        }

        if (($dtArquivI <> '// 00:00:00.000' and $dtArquivI <> '// 23:59:59.999' and !empty($dtArquivI)) or ($dtArquivF <> '// 00:00:00.000' and $dtArquivF <> '// 23:59:59.999' and !empty($dtArquivF))) {
            if ($tipo_dtArquivo == 1) {
                $sql .= " AND Data >= '$dtArquivI' AND Data <= '$dtArquivInull'";
            } elseif ($tipo_dtArquivo == 2) {
                $sql .= " AND Data >= '$dtArquivI' AND Data <= '$dtArquivF'";
            }
        }

        if ($cxInicio) {
            if ($tipo_cxInicio == 1) {
                $sql .= " AND CaixaInicio like '$cxInicio%'";
            }

            if ($tipo_cxInicio == 2) {
                $sql .= " AND CaixaInicio like '%$cxInicio%'";
            }

            if ($tipo_cxInicio == 3) {
                $sql .= " AND CaixaInicio = '$cxInicio'";
            }
        }

        if ($cxFinal) {
            if ($tipo_cxFinal == 1) {
                $sql .= " AND CaixaFinal like '$cxFinal%'";
            }

            if ($tipo_cxFinal == 2) {
                $sql .= " AND CaixaFinal like '%$cxFinal%'";
            }

            if ($tipo_cxFinal == 3) {
                $sql .= " AND CaixaFinal = '$cxFinal'";
            }
        }

        $sql .= " Order By NomeProjeto";
        
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function pesquisarTodosDestinos()
    {
        $sql = "Select distinct 
				Codigo, 
				Sigla, 
				org.org_nomeautorizado 
				FROM SAC.dbo.Orgaos o 
				INNER JOIN TABELAS.dbo.vwUsuariosOrgaosGrupos org on org.uog_orgao = o.Codigo 
				WHERE org.sis_codigo = 21 AND org.uog_status = 1
				ORDER BY Sigla";

        //$sql .= "ORDER BY org_siglaautorizado";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function pesquisarDestinos($situacao)
    {
        $sql = "SELECT distinct 
					h.idUnidade as idDestino,
					org.org_siglaautorizado as siglaDestino,
					org.org_nomeautorizado as nomeDestino,
					h.idLote as lote
					FROM
					SAC.dbo.tbHistoricoDocumento AS h
					INNER JOIN
					TABELAS.dbo.vwUsuariosOrgaosGrupos as org ON org.uog_orgao = h.idUnidade
					WHERE (h.Acao = $situacao or h.Acao = 4) and h.stEstado = 1";

        $sql .= "ORDER BY siglaDestino";
        //print_r($sql);die();
        
        $db= Zend_Db_Table::getDefaultAdapter();

        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaEmissor($situacao1, $situacao2)
    {
        $sql = "SELECT distinct 
					
					h.idUsuarioEmissor as idEmissor,
					u.usu_nome as Emissor
					FROM
					SAC.dbo.tbHistoricoDocumento AS h
					INNER JOIN SAC.dbo.Projetos AS p ON h.idPronac = p.IdPRONAC
					INNER JOIN TABELAS.dbo.Usuarios as u on h.idUsuarioEmissor = u.usu_codigo
					WHERE (h.idDocumento is NULL or h.idDocumento = 0) and h.stEstado = 1";

        if ($situacao1 && !$situacao2) {
            $sql .= " AND h.Acao = $situacao1";
        } elseif ($situacao1 && $situacao2) {
            $sql .= " AND(h.Acao = $situacao1 OR h.Acao = $situacao2)";
        }

        //print_r($sql);die();
        //die($sql);
        $db= Zend_Db_Table::getDefaultAdapter();

        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        return $db->fetchAll($sql);
    }

    public static function projetosDespachados($situacao1 = null, $situacao2 = null, $idUsuario = null, $idLote = null)
    {
        $sql = "SELECT distinct 
					p.IdPRONAC as idPronac,
					p.AnoProjeto + p.Sequencial AS Pronac,
					p.NomeProjeto, p.Orgao AS idOrigem,
					TABELAS.dbo.fnEstruturaOrgao(p.Orgao, 0) AS Origem,
					SAC.dbo.fnFormataProcesso(p.IdPRONAC) AS Processo,
					h.meDespacho as despacho,
					(select top 1 thd.idHistorico from SAC.dbo.tbHistoricoDocumento thd order by thd.idhistorico desc) as idHistorico,
					--(select top 1 CONVERT(CHAR(10), thd.dtTramitacaoEnvio,103) + ' ' + CONVERT(CHAR(8), thd.dtTramitacaoEnvio,108) AS dtTramitacaoEnvio from SAC.dbo.tbHistoricoDocumento thd order by thd.dtTramitacaoEnvio desc) as dtEnvio,
					(select top 1 CONVERT(CHAR(10), p.DtSituacao,103) + ' ' + CONVERT(CHAR(8), p.DtSituacao,108) AS dtSituacao from SAC.dbo.Projetos p order by p.DtSituacao desc) as dtSituacao, 
					(select top 1 h.idLote from SAC.dbo.tbHistoricoDocumento h order by h.idLote desc) as idLote,
					h.idUnidade AS idDestino,
					CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) + ' ' + CONVERT(CHAR(10), h.dtTramitacaoEnvio,108) AS dtEnvio,
					CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) + ' ' + CONVERT(CHAR(10), h.dtTramitacaoRecebida,108) AS dtRecebimento,
					TABELAS.dbo.fnEstruturaOrgao(h.idUnidade, 0) AS Destino,
					SAC.dbo.fnNomeUsuario(h.idUsuarioEmissor) AS Emissor,
					h.idUsuarioReceptor,
					SAC.dbo.fnNomeUsuario(h.idUsuarioReceptor) AS Receptor,
					h.Acao as Acao,
					CASE
					WHEN h.Acao = 1 THEN 'Cadastrado'
					WHEN h.Acao = 2 THEN 'Enviado'
					WHEN h.Acao = 3 THEN 'Recebido'
					WHEN h.Acao = 4 THEN 'Recusado'
					WHEN h.Acao = 6 THEN 'Anexado'
					END AS Situacao,
					h.stEstado
					FROM
					SAC.dbo.tbHistoricoDocumento AS h
					INNER JOIN
					SAC.dbo.Projetos AS p ON h.idPronac = p.IdPRONAC
					WHERE (h.idDocumento is NULL or h.idDocumento = 0) and h.stEstado = 1";

        //die($sql);
        if ($situacao1 && !$situacao2) {
            $sql .= " AND h.Acao = $situacao1";
        } elseif ($situacao1 && $situacao2) {
            $sql .= " AND(h.Acao = $situacao1 OR h.Acao = $situacao2)";
        }

        if ($idUsuario) {
            $sql .= " AND(h.idUsuarioEmissor = $idUsuario)";
        }

        if ($idLote) {
            $sql .= " AND(h.idLote = $idLote)";
        }

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        
        return $db->fetchAll($sql);
    }

    public static function setProjeto($pronac, $acao)
    {
        $sql = " SELECT DISTINCT	top 1 MAX(idHistorico) as idHistorico,
					AnoProjeto+Sequencial as pronac, 
					p.IdPRONAC, 
					h.idUnidade as idDestino,
					h.meDespacho as despacho
					FROM SAC.dbo.Projetos p
					INNER JOIN SAC.dbo.tbHistoricoDocumento h on h.idPronac = p.IdPRONAC
					WHERE AnoProjeto+Sequencial = '$pronac' and h.Acao = $acao
					group by AnoProjeto, Sequencial, p.IdPRONAC, h.idUnidade, h.meDespacho
					order by idHistorico desc";

        
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        
        return $db->fetchAll($sql);
    }

    public static function tramitarProjeto($idPronac, $idDestino, $idLote, $acaoA, $acaoB, $despacho)
    {
        $sql = "INSERT INTO 
					SAC.dbo.tbHistoricoDocumento (idPronac, idUnidade, dtTramitacaoEnvio, idUsuarioEmissor, idUsuarioReceptor, idLote, Acao, stEstado, meDespacho)
					SELECT IdPRONAC, idUnidade = $idDestino, dtTramitacaoEnvio = GETDATE(), idUsuarioEmissor, idUsuarioReceptor, idLote = $idLote, Acao = $acaoB, stEstado = 1, meDespacho = '$despacho'
				";

        $sql .= " FROM SAC.dbo.tbHistoricoDocumento 
					WHERE Acao = $acaoA and idUnidade = $idDestino and IdPRONAC = $idPronac";

        //die($sql);
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($sql);
    }

    public static function buscaUltimoLote()
    {
        $sql = "select max(idLote) as idLote from SAC.dbo.tbLote ";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }
    
    public static function verificaTramitacoesRepetidas()
    {
        $sql = "SELECT COUNT(idPronac) AS contador, idPronac
                FROM SAC.dbo.tbHistoricoDocumento
                WHERE stEstado = 1 AND idDocumento = 0
                GROUP BY idPronac
                HAVING COUNT(idPronac) > 1
                ORDER BY 2 DESC ";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function verificaHistoricoDocumento($idPronac, $acao)
    {
        $sql = "select top 1 * from SAC.dbo.tbHistoricoDocumento where idDocumento = 0 and idPronac = '$idPronac' order by Acao desc";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function insereLote()
    {
        $sql = "INSERT INTO SAC.dbo.tbLote (dtLote) Values (GETDATE()) ";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function buscarDadosPronac($pronac)
    {
        $sql = "select 
					 p.IdPRONAC,
					 p.Processo,
					 p.AnoProjeto+p.Sequencial as pronac,
					 p.NomeProjeto,
					 p.Logon,
					 p.Analista as usuario,
					 p.DtSituacao,
					 p.OrgaoOrigem
					 FROM SAC.dbo.Projetos p 
					 WHERE p.AnoProjeto+p.Sequencial = '$pronac'";
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        
        return $db->fetchAll($sql);
    }

    public static function mudaStatus($idPronac, $destino, $pronac, $processo, $usuarios, $DtSituacao, $despacho, $origem = null)
    {
        if ($origem) {
            $sql = "insert into SAC.dbo.tbHistoricoDocumento 
				(IdPRONAC, idOrigem, idUnidade, idUsuarioEmissor, Acao, stEstado, meDespacho, dtTramitacaoEnvio) 
				values 
				($idPronac, $origem, $destino, $usuarios, 1, 1, '$despacho', GETDATE()) ";
        } else {
            $sql = "insert into SAC.dbo.tbHistoricoDocumento 
				(IdPRONAC, idUnidade, idUsuarioEmissor, Acao, stEstado, meDespacho, dtTramitacaoEnvio) 
				values 
				($idPronac, $destino, $usuarios, 1, 1, '$despacho', GETDATE()) ";
        }


        
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function atualizaStatus($idPronac, $destino, $despacho)
    {
        $sql = "UPDATE SAC.dbo.tbHistoricoDocumento 
				SET idUnidade = $destino, meDespacho = '$despacho', Acao = 1
				WHERE idPronac = $idPronac";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }
        //die($sql);
        $db->fetchAll($sql);
        return true;
    }

    public static function atualizaDados($idPronac)
    {
        $sql = "UPDATE SAC.dbo.tbHistoricoDocumento SET stEstado = 0 WHERE IdPRONAC =  $idPronac and (idDocumento is NULL or idDocumento = 0)";

        //print_r($sql);die;
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($sql);
    }

    public static function atualizaEstado($idPronac)
    {
        $sql = "UPDATE SAC.dbo.tbHistoricoDocumento SET stEstado = 0 WHERE idPronac =  $idPronac and (idDocumento is NULL or idDocumento = 0)";

        //print_r($sql);die;
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($sql);
    }

    public static function atualizaEstadoRecusa($idPronac)
    {
        $sql = "UPDATE SAC.dbo.tbHistoricoDocumento SET stEstado = 0 WHERE idPronac =  $idPronac";

        //print_r($sql);die;
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($sql);
    }

    public static function recusarProjeto($idPronac, $acao, $codOrgao, $despacho = null)
    {
        $sql = "INSERT INTO 
					SAC.dbo.tbHistoricoDocumento (idPronac, idUnidade, dtTramitacaoEnvio, idLote, idUsuarioEmissor, Acao, stEstado, meDespacho)
					SELECT IdPRONAC, idUnidade = $codOrgao, dtTramitacaoEnvio = GETDATE(), idLote, idUsuarioEmissor, Acao = $acao, stEstado = 1, meDespacho = '$despacho' 
					FROM SAC.dbo.tbHistoricoDocumento 
					WHERE (Acao = 2 and idPronac = $idPronac) ";

        //print_r($sql);die;
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($sql);
    }

    public static function consultarProjetos($idusuario, $tipo_origem, $origem, $tipo_dtEnvio, $dtEnvioI, $dtEnvioF, $tipo_dtRecebida, $dtRecebidoI, $dtRecebidoF, $lote, $tipo_destino, $destino, $tipo_situacao, $situacao)
    {
        $sql = "SELECT distinct top 1000
					h.idOrigem AS idOrigem,
					h.idPronac,
					TABELAS.dbo.fnEstruturaOrgao(h.idOrigem, 0) AS Origem,
					p.AnoProjeto + p.Sequencial AS Pronac,
					p.NomeProjeto as NomeProjeto,
					SAC.dbo.fnFormataProcesso(p.IdPRONAC) AS Processo,
					h.meDespacho as despacho,
					SAC.dbo.fnNomeUsuario(h.idUsuarioEmissor) AS Emissor,
					CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) as dtEnvio,
					h.idUnidade AS idDestino,
					TABELAS.dbo.fnEstruturaOrgao(h.idUnidade, 0) AS Destino,
					CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) as dtRecebida,
					h.idUsuarioReceptor,
					SAC.dbo.fnNomeUsuario(h.idUsuarioReceptor) AS Receptor,
					h.idLote as idLote,
					CASE
					WHEN h.Acao = 1 THEN 'Cadastrado'
					WHEN h.Acao = 2 THEN 'Enviado'
					WHEN h.Acao = 3 THEN 'Recebido'
					WHEN h.Acao = 4 THEN 'Recusado'
					WHEN h.Acao = 6 THEN 'Anexado'
					END AS Situacao,
					h.stEstado
					FROM
					SAC.dbo.tbHistoricoDocumento AS h
					INNER JOIN
					SAC.dbo.Projetos AS p ON h.idPronac = p.IdPRONAC
					where h.stEstado = 1 and ((h.idDocumento is NULL) OR (h.idDocumento = 0))";


        if ($origem) {
            if ($tipo_origem == 1) {
                $sql .= " AND h.idOrigem = " . $origem;
            } elseif ($tipo_origem == 2) {
                $sql .= " AND h.idOrigem <> " . $origem;
            }
        }
        if ($dtEnvioI && $dtEnvioI <> "//") {
            if ($tipo_dtEnvio == 1) {
                $sql .= " AND CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) = '$dtEnvioI'";
            } elseif ($tipo_dtEnvio == 2) {
                $sql .= " AND CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) >= '$dtEnvioI' AND CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) <= '$dtEnvioF'";
            }
        }

        if ($dtRecebidoI && $dtRecebidoI <> "//") {
            if ($tipo_dtRecebida == 1) {
                $sql .= " AND CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) = '$dtRecebidoI'";
            } elseif ($tipo_dtRecebida == 2) {
                $sql .= " AND CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) >= '$dtRecebidoI' AND CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) <= '$dtRecebidoF'";
            }
        }
        //die($sql);
        if ($lote) {
            $sql .= "AND h.idLote = '$lote'";
        }
        if ($destino) {
            if ($tipo_destino == 1) {
                $sql .= " AND h.idUnidade = " . $destino;
            } elseif ($tipo_destino == 2) {
                $sql .= " AND h.idUnidade <> " . $destino;
            }
        }

        if ($situacao) {
            if ($tipo_situacao == 1) {
                $sql .= " AND h.Acao = " . $situacao;
            } elseif ($tipo_situacao == 2) {
                $sql .= " AND h.Acao <> " . $situacao;
            }
        }
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function inserirSolicitacaoArquivamento($idPronac, $justificativa, $idusuario, $cxInicio, $cxFinal, $acao, $stEstado)
    {
        $sql = "insert into SAC.dbo.tbArquivamento 
				(idPronac, Data, Edificio, CaixaInicio, CaixaFinal, stAcao, stEstado, idUsuario, dsJustificativa) 
				values 
				($idPronac, GETDATE(), 0, $cxInicio, $cxFinal, $acao, $stEstado, $idusuario, '$justificativa') ";

        
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function buscarCancelamento($codOrgao = null)
    {
        $sql = "select h.*, p.Processo, p.AnoProjeto+p.Sequencial as pronac, p.NomeProjeto as NomeProjeto,
						CASE
				          WHEN h.Acao = 0 THEN 'Bloqueado'
				          WHEN h.Acao = 1 THEN 'Cadastrado'
				          WHEN h.Acao = 2 THEN 'Enviado'
				          WHEN h.Acao = 3 THEN 'Recebido'
				          WHEN h.Acao = 4 THEN 'Recusado'
				          WHEN h.Acao = 6 THEN 'Anexado'
				        END AS Situacao,
				         usu.usu_nome AS Emissor, h.dsJustificativa, h.idDocumento, h.idHistorico
				from SAC.dbo.tbHistoricoDocumento h
				inner join SAC.dbo.Projetos p on p.IdPRONAC = h.idPronac
				LEFT JOIN Tabelas.dbo.Usuarios AS usu ON usu.usu_codigo = h.idUsuarioEmissor
				where Acao = 0 and stEstado = 1 and (h.idDocumento is NULL or h.idDocumento = 0) and h.dsJustificativa is not NULL";

        if ($codOrgao) {
            $sql .= " AND idUnidade = $codOrgao";
        }

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }
        
        return $db->fetchAll($sql);
    }

    public static function buscarDesarquivar()
    {
        $sql = "  select top 100 ar.* , p.AnoProjeto+p.Sequencial as pronac, p.NomeProjeto, p.Processo, ar.dsJustificativa
					 from SAC.dbo.tbArquivamento ar
					 inner join SAC.dbo.Projetos p on p.IdPRONAC = ar.idPronac
					 where stAcao = 0 and stEstado = 1 and dsJustificativa is not NULL
					 order by p.NomeProjeto";

        
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function buscarCancelOrgao($codOrgao = null)
    {
        $sql = "select distinct idUnidade as idDestino, Sigla as Destino, idLote 
				from SAC.dbo.tbHistoricoDocumento h
				inner join SAC.dbo.Orgaos org on org.Codigo = h.idUnidade
				where Acao = 0 and stEstado = 1 and (h.idDocumento is NULL or h.idDocumento = 0)";

        if ($codOrgao) {
            $sql .= " AND idUnidade = $codOrgao";
        }

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao salvar Projeto: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }
    
    public static function projetosImprimirGuia($idUsuario, $idLote)
    {
        $sql = "SELECT 
					p.AnoProjeto + p.Sequencial AS Pronac,
					p.NomeProjeto,
					TABELAS.dbo.fnEstruturaOrgao(p.Orgao, 0) AS Origem,
					SAC.dbo.fnFormataProcesso(p.IdPRONAC) AS Processo,
					CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) + ' ' + CONVERT(CHAR(10), h.dtTramitacaoEnvio,108) AS dtEnvio,
					TABELAS.dbo.fnEstruturaOrgao(h.idUnidade, 0) AS Destino,
					SAC.dbo.fnNomeUsuario(h.idUsuarioEmissor) AS Emissor,
                    h.idLote
                FROM SAC.dbo.tbHistoricoDocumento AS h
                INNER JOIN SAC.dbo.Projetos AS p ON h.idPronac = p.IdPRONAC
                WHERE (h.idDocumento is NULL or h.idDocumento = 0) and h.stEstado = 1
                    AND(h.idUsuarioEmissor = $idUsuario)
                    AND(h.idLote = $idLote)
                ORDER BY h.idHistorico ";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
        
        return $db->fetchAll($sql);
    }
}
