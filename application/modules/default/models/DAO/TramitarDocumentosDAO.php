<?php

class TramitarDocumentosDAO extends Zend_Db_Table
{

    public static function buscarDocumentos($idUsuario, $idOrgao, $idDestino)
    {
        $sql = "SELECT
                    top 100 pro.Orgao as idOrgao,
                    pro.IdPRONAC,
                    pro.NomeProjeto,
                    substring(pro.Processo,1,5) + '.' +substring(pro.Processo,6,6)+ '/' +substring(pro.Processo,12,4)+ '-' +substring(pro.Processo,16,2) as Processo,
                    --SAC.dbo.fnFormataProcesso(pro.IdPRONAC) as Processo,
                    (pro.AnoProjeto+pro.Sequencial) AS Pronac,
                    --TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) localizacao,
                    doc.dtDocumento,CONVERT(CHAR(10),dtDocumento,103) AS dtDocumentoBR  ,
                    doc.NoArquivo,
                    hd.stEstado,
                    hd.idDocumento,
                    hd.idUnidade,
                    hd.dtTramitacaoEnvio,
                    hd.dtTramitacaoRecebida,
                    hd.idUsuarioEmissor,
                    hd.idLote,
                    hd.idUsuarioReceptor,
                    CONVERT(CHAR(10),hd.dtTramitacaoEnvio, 103) AS dtTramitacaoEnvioBR,
                    CONVERT(CHAR(10),hd.dtTramitacaoRecebida , 103) AS dtTramitacaoRecebidaBR ,
                    CASE
                        WHEN hd.Acao = 1 THEN 'Cadastrado'
                        WHEN hd.Acao = 2 THEN 'Enviado'
                        WHEN hd.Acao = 3 THEN 'Recebido'
                        WHEN hd.Acao = 4 THEN 'Recusado'
                        WHEN hd.Acao = 6 THEN 'Anexado'
                    END AS Situacao,
                    td.dsTipoDocumento,
                    hd.idOrigem as idOrigem,
                    --TABELAS.dbo.fnEstruturaOrgao(hd.idOrigem,0) AS Origem,
                    hd.idUnidade as idDestino,
                    --TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) AS Destino,
                    hd.idUsuarioEmissor AS UsuarioEmissor,
                    hd.idUsuarioReceptor AS UsuarioReceptor
                FROM
                    SAC.dbo.tbHistoricoDocumento hd
                    INNER JOIN SAC.dbo.tbDocumento doc ON hd.idDocumento = doc.idDocumento
                    INNER JOIN SAC.dbo.Projetos pro ON doc.idPronac = pro.IdPRONAC
                    INNER JOIN SAC.dbo.tbTipoDocumento td ON doc.idTipoDocumento = td.idTipoDocumento
                WHERE
                    hd.Acao = 1 AND
                    hd.stEstado = 1  AND
                    doc.idUnidadeCadastro=".$idOrgao." AND
                    hd.idUsuarioEmissor = ".$idUsuario." AND
                    hd.idUnidade = '".$idDestino."'
                ORDER BY hd.idHistorico ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 104857600");
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarDocumentosEnviados($idUsuario = null, $idLote = null, $estado = null, $acao = null, $orgao = null)
    {
        $filtros = array();
        $sql = "SELECT pro.Orgao as idOrgao,
                    pro.NomeProjeto,
                    --pro.Processo,
                    --SAC.dbo.fnFormataProcesso(pro.IdPRONAC) AS Processo,
                    substring(pro.Processo,1,5) + '.' +substring(pro.Processo,6,6)+ '/' +substring(pro.Processo,12,4)+ '-' +substring(pro.Processo,16,2) as Processo,
                    (pro.AnoProjeto+pro.Sequencial) AS Pronac,
                    --TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) localizacao,
                    doc.dtDocumento,CONVERT(CHAR(10),dtDocumento,103) AS dtDocumentoBR  ,
                    doc.NoArquivo,
                    hd.idDocumento,
                    hd.idUnidade,
                    hd.dtTramitacaoEnvio,
                    hd.dtTramitacaoRecebida,
                    hd.idUsuarioEmissor,
                    hd.idLote,
                    hd.idUsuarioReceptor,
                    CONVERT(CHAR(10),hd.dtTramitacaoEnvio, 103) AS dtTramitacaoEnvioBR,
                    CONVERT(CHAR(10),hd.dtTramitacaoRecebida, 103) AS dtTramitacaoRecebidaBR,
                    td.dsTipoDocumento,
                    hd.idOrigem as idOrigem,
                    TABELAS.dbo.fnEstruturaOrgao(hd.idOrigem,0) AS Origem,
                    hd.idUnidade as idDestino,
                    TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) AS Destino,
                    hd.idUsuarioEmissor AS UsuarioEmissor,
                    hd.idUsuarioReceptor AS UsuarioReceptor,
                    hd.idPronac,
                    hd.idUnidade,
                    hd.idDocumento
				FROM SAC.dbo.tbHistoricoDocumento hd
                    INNER JOIN SAC.dbo.tbDocumento doc		ON hd.idDocumento = doc.idDocumento
                    INNER JOIN SAC.dbo.Projetos pro			ON doc.idPronac = pro.IdPRONAC
                    INNER JOIN SAC.dbo.tbTipoDocumento td	ON doc.idTipoDocumento = td.idTipoDocumento
				WHERE	";


        if ($idUsuario) {
            $filtros[] = "hd.idUsuarioEmissor = " . $idUsuario;
        }

        if ($idLote) {
            $filtros[] = "hd.idLote = " . $idLote;
        }

        if ($estado) {
            $filtros[] = "hd.stEstado = " . $estado;
        }

        if ($orgao) {
            $filtros[] = "hd.idOrigem = " . $orgao;
        }
        if ($acao) {
            $filtros[] = "hd.Acao = " . $acao;
        }

        $sql .= implode(" AND ", $filtros);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 10485760");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarDocumentosRecebidosDestino($idUsuario, $idLote = null, $estado = null, $acao = null, $orgao = null)
    {
        $sql = "SELECT	top 100 pro.Orgao as idOrgao,pro.NomeProjeto, pro.Processo, pro.IdPRONAC, (pro.AnoProjeto+pro.Sequencial) AS Pronac,
						TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) localizacao,
						doc.dtDocumento,CONVERT(CHAR(10),dtDocumento,103) AS dtDocumentoBR  , doc.NoArquivo,
						hd.idDocumento, hd.idUnidade, hd.dtTramitacaoEnvio, hd.dtTramitacaoRecebida, hd.idUsuarioEmissor, hd.idLote, hd.idUsuarioReceptor,
						CONVERT(CHAR(10),hd.dtTramitacaoEnvio, 103) AS dtTramitacaoEnvioBR,
						CONVERT(CHAR(10),hd.dtTramitacaoRecebida, 103) AS dtTramitacaoRecebidaBR,
						td.dsTipoDocumento,
						hd.idOrigem as idOrigem,
						TABELAS.dbo.fnEstruturaOrgao(hd.idOrigem,0) AS Origem,
						hd.idUnidade as idDestino,
						TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) AS Destino,
						hd.idUsuarioEmissor AS UsuarioEmissor,
						hd.idUsuarioReceptor AS UsuarioReceptor, hd.idPronac, hd.idUnidade, hd.idDocumento
				FROM	SAC.dbo.tbHistoricoDocumento hd
						INNER JOIN SAC.dbo.tbDocumento doc		ON hd.idDocumento = doc.idDocumento
						INNER JOIN SAC.dbo.Projetos pro			ON doc.idPronac = pro.IdPRONAC
						INNER JOIN SAC.dbo.tbTipoDocumento td	ON doc.idTipoDocumento = td.idTipoDocumento
				WHERE	hd.idLote=".$idLote." AND hd.idUsuarioEmissor = ".$idUsuario;



        if ($estado) {
            $sql .=" AND hd.stEstado = ".$estado;
        }

        if ($orgao) {
            $sql .=" AND hd.idUnidade = ".$orgao;
        }
        if ($acao) {
            $sql .=" AND hd.Acao = ".$acao;
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 10485760");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    /**
     * @name buscarDocumentosRecebidosPaginator
     * @param integer $acao
     * @param integer $estado
     * @param integer $orgao
     * @param integer $idLote
     * @return Zend_Paginator
     */
    public static function buscarDocumentosRecebidosPaginator($acao, $estado, $orgao = null, $lote = null, $pronac = null)
    {
        $table = new tbHistoricoDocumento();
        $select = $table->select()->setIntegrityCheck(false)
            ->from(
                array('hd' => 'tbHistoricoDocumento'),
                array(
                    'idDocumento',
                    'idUnidade',
                    'dtTramitacaoEnvio',
                    'dtTramitacaoRecebida',
                    'idUsuarioEmissor',
                    'idLote',
                    'idUsuarioReceptor',
                    'idOrigem',
                    'UsuarioEmissor' => 'idUsuarioEmissor',
                    'UsuarioReceptor' => 'idUsuarioReceptor',
                    new Zend_Db_Expr('TABELAS.dbo.fnEstruturaOrgao(pro.OrgaoOrigem,0) AS Origem'),
                    new Zend_Db_Expr('TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) AS Destino'),
                )
            )
            ->join(
                array('doc' => 'tbDocumento'),
                'hd.idDocumento = doc.idDocumento',
               [
                    'dtDocumentoBR' => new Zend_Db_Expr('CONVERT(CHAR(10),dtDocumento,103)'),
                    'NoArquivo'
               ]
            )
            ->join(
                array('pro' => 'Projetos'),
                'doc.idPronac = pro.IdPRONAC',
                array(
                    'IdPRONAC',
                    'idOrgao' => 'Orgao',
                    'pro.NomeProjeto',
                    'pro.Processo',
                    new Zend_Db_Expr('pro.AnoProjeto+pro.Sequencial as Pronac'),
                )
            )
            ->join(
                array('td' => 'tbTipoDocumento'),
                'doc.idTipoDocumento = td.idTipoDocumento',
                array('dsTipoDocumento',)
            )
            ->where('hd.idDocumento <> ?', 0)
            ->where('hd.Acao = ?', $acao)
            ->where('hd.stEstado = ?', $estado)
            ->order('hd.idLote')
        ;
        if ($orgao) {
            $select->where('hd.idUnidade = ?', $orgao);
        }
        if ($lote) {
            $select->where('hd.idLote = ?', $lote);
        }
        if ($pronac) {
            $select->where('pro.AnoProjeto+pro.Sequencial = ?', $pronac);
        }
        return new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
    }

    public static function buscarDocumentosRecebidos($idUsuario, $idLote)
    {
        $sql = "SELECT	top 100 pro.IdPRONAC, pro.Orgao as idOrgao, pro.NomeProjeto, pro.Processo, (pro.AnoProjeto+pro.Sequencial) AS Pronac,
						TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) localizacao,
						doc.dtDocumento,CONVERT(CHAR(10),dtDocumento,103) AS dtDocumentoBR  , doc.NoArquivo,
						hd.idDocumento, hd.idUnidade, hd.dtTramitacaoEnvio, hd.dtTramitacaoRecebida, hd.idUsuarioEmissor, hd.idLote, hd.idUsuarioReceptor, hd.idOrigem,
						CONVERT(CHAR(20),hd.dtTramitacaoEnvio, 120) AS dtTramitacaoEnvioUS,
						CONVERT(CHAR(10),hd.dtTramitacaoEnvio, 103) AS dtTramitacaoEnvioBR,
						CONVERT(CHAR(10),hd.dtTramitacaoRecebida, 103) AS dtTramitacaoRecebidaBR,
						td.dsTipoDocumento,
						TABELAS.dbo.fnEstruturaOrgao(pro.OrgaoOrigem,0) AS Origem,
						TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) AS Destino,
						hd.idUsuarioEmissor AS UsuarioEmissor,
						hd.idUsuarioReceptor AS UsuarioReceptor
				FROM	SAC.dbo.tbHistoricoDocumento hd
						INNER JOIN SAC.dbo.tbDocumento doc		ON hd.idDocumento = doc.idDocumento
						INNER JOIN SAC.dbo.Projetos pro			ON doc.idPronac = pro.IdPRONAC
						INNER JOIN SAC.dbo.tbTipoDocumento td	ON doc.idTipoDocumento = td.idTipoDocumento
				WHERE	hd.Acao = 3 AND hd.idLote=".$idLote." /*AND hd.idUsuarioEmissor = ".$idUsuario."*/ AND hd.stEstado = 1";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarDocumentosAnexados($idUsuario, $idLote = null, $idDocumento = null)
    {
        $sql = "SELECT pro.IdPRONAC, pro.Orgao as idOrgao, pro.NomeProjeto,
                                                substring(pro.Processo,1,5) + '.' +substring(pro.Processo,6,6)+ '/' +substring(pro.Processo,12,4)+ '-' +substring(pro.Processo,16,2) as Processo,
                                                (pro.AnoProjeto+pro.Sequencial) AS Pronac,
						--TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) localizacao,
						doc.dtDocumento,CONVERT(CHAR(10),dtDocumento,103) AS dtDocumentoBR  , doc.NoArquivo,
						hd.idDocumento, hd.idUnidade, hd.dtTramitacaoEnvio, hd.dtTramitacaoRecebida, hd.idUsuarioEmissor, hd.idLote, hd.idUsuarioReceptor,
						CONVERT(CHAR(20),hd.dtTramitacaoEnvio, 120) AS dtTramitacaoEnvioUS,
						CONVERT(CHAR(10),hd.dtTramitacaoEnvio, 103) AS dtTramitacaoEnvioBR,
						CONVERT(CHAR(10),hd.dtTramitacaoRecebida, 103) AS dtTramitacaoRecebidaBR,
						td.dsTipoDocumento,
						hd.idOrigem as idOrigem,
						--TABELAS.dbo.fnEstruturaOrgao(hd.idOrigem,0) AS Origem,
						hd.idUnidade as idDestino,
						--TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) AS Destino,
						hd.idUsuarioEmissor AS UsuarioEmissor,
						hd.idUsuarioReceptor AS UsuarioReceptor
				FROM	SAC.dbo.tbHistoricoDocumento hd
						INNER JOIN SAC.dbo.tbDocumento doc		ON hd.idDocumento = doc.idDocumento
						INNER JOIN SAC.dbo.Projetos pro			ON doc.idPronac = pro.IdPRONAC
						INNER JOIN SAC.dbo.tbTipoDocumento td	ON doc.idTipoDocumento = td.idTipoDocumento
				WHERE	hd.Acao = 6 AND hd.idUsuarioEmissor = ".$idUsuario." AND hd.stEstado = 1";

        if ($idLote) {
            $sql .= " AND hd.idLote=".$idLote."";
        }

        if ($idDocumento) {
            $sql .= " AND hd.idDocumento=".$idDocumento."";
        }


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 10485760");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarDocumentoUnico($idDocumento, $acao)
    {
        $sql = "SELECT pro.IdPRONAC, pro.Orgao as idOrgao, pro.NomeProjeto,
                                                substring(pro.Processo,1,5) + '.' +substring(pro.Processo,6,6)+ '/' +substring(pro.Processo,12,4)+ '-' +substring(pro.Processo,16,2) as Processo,
                                                (pro.AnoProjeto+pro.Sequencial) AS Pronac,
						--TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) localizacao,
						doc.dtDocumento,CONVERT(CHAR(10),dtDocumento,103) AS dtDocumentoBR  , doc.NoArquivo,
						hd.idDocumento, hd.idUnidade, hd.dtTramitacaoEnvio, hd.dtTramitacaoRecebida, hd.idUsuarioEmissor, hd.idLote, hd.idUsuarioReceptor,
						CONVERT(CHAR(20),hd.dtTramitacaoEnvio, 120) AS dtTramitacaoEnvioUS,
						CONVERT(CHAR(10),hd.dtTramitacaoEnvio, 103) AS dtTramitacaoEnvioBR,
						CONVERT(CHAR(10),hd.dtTramitacaoRecebida, 103) AS dtTramitacaoRecebidaBR,
						td.dsTipoDocumento,
						hd.idOrigem as idOrigem,
						--TABELAS.dbo.fnEstruturaOrgao(hd.idOrigem,0) AS Origem,
						hd.idUnidade as idDestino,
						--TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) AS Destino,
						hd.idUsuarioEmissor AS UsuarioEmissor,
						hd.idUsuarioReceptor AS UsuarioReceptor
				FROM	SAC.dbo.tbHistoricoDocumento hd
						INNER JOIN SAC.dbo.tbDocumento doc		ON hd.idDocumento = doc.idDocumento
						INNER JOIN SAC.dbo.Projetos pro			ON doc.idPronac = pro.IdPRONAC
						INNER JOIN SAC.dbo.tbTipoDocumento td	ON doc.idTipoDocumento = td.idTipoDocumento
						WHERE hd.Acao in ($acao) AND hd.stEstado = 1 AND hd.idDocumento <> 0 AND hd.idDocumento = ".$idDocumento;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarDocumentosEnviadosOrgao($codOrgao)
    {
        $sql = "SELECT distinct TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) localizacao
                FROM SAC.dbo.tbHistoricoDocumento hd
					INNER JOIN SAC.dbo.tbDocumento doc ON hd.idDocumento = doc.idDocumento
					INNER JOIN SAC.dbo.Projetos pro ON hd.idPronac = pro.IdPRONAC
						WHERE Acao = 2 AND hd.stEstado = 1 AND hd.idDocumento <> 0 AND doc.idUnidadeCadastro=".$codOrgao;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarDocumentosCadastradosOrgao($codOrgao)
    {
        $sql = "SELECT distinct pro.Orgao as idDestino, TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) as Destino
                FROM SAC.dbo.tbHistoricoDocumento hd
					INNER JOIN SAC.dbo.tbDocumento doc ON hd.idDocumento = doc.idDocumento
					INNER JOIN SAC.dbo.Projetos pro ON hd.idPronac = pro.IdPRONAC
						WHERE Acao = 1 AND hd.stEstado = 1 AND hd.idDocumento <> 0 AND doc.idUnidadeCadastro=".$codOrgao."
							UNION
				SELECT distinct pro.Orgao as idDestino, TABELAS.dbo.fnEstruturaOrgao(pro.Orgao,0) as Destino
                FROM SAC.dbo.tbHistoricoDocumento hd
					INNER JOIN SAC.dbo.tbDocumento doc ON hd.idDocumento = doc.idDocumento
					INNER JOIN SAC.dbo.Projetos pro ON hd.idPronac = pro.IdPRONAC
						WHERE Acao = 4 AND hd.stEstado = 1 AND hd.idDocumento <> 0 AND doc.idUnidadeCadastro=".$codOrgao;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarDocumentosGeral($Orgao, $idUsuarioEmissor, $lote = null)
    {
        $sql = "SELECT p.AnoProjeto+p.Sequencial as Pronac, p.NomeProjeto, t.dsTipoDocumento, CONVERT(CHAR(10), d.dtDocumento, 103) AS dtDocumentoBR,
					   p.Processo, CONVERT(CHAR(10), h.dtTramitacaoEnvio, 103) AS dtTramitacaoEnvioBR,
					   TABELAS.dbo.fnEstruturaOrgao(p.Orgao,0)AS Destino, h.idUsuarioReceptor AS Receptor, d.NoArquivo, h.idLote,
					   CASE WHEN h.Acao = 1 THEN 'Cadastrado' WHEN h.Acao = 2 THEN 'Enviado' WHEN h.Acao = 3 THEN 'Recebido' WHEN h.Acao = 4 THEN 'Recusado' WHEN
				       h.Acao = 6 THEN 'Anexado' END AS Situacao, d.idDocumento
							FROM        SAC.dbo.tbHistoricoDocumento AS h
							INNER JOIN  SAC.dbo.tbDocumento AS d ON h.idDocumento = d.idDocumento
							INNER JOIN  SAC.dbo.Projetos AS p ON d.idPronac = p.IdPRONAC
							INNER JOIN  SAC.dbo.tbTipoDocumento AS t ON d.idTipoDocumento = t.idTipoDocumento
								WHERE  (h.idDocumento <> 0) AND (h.idDocumento is not null) AND h.stEstado = 1
								 AND h.idUsuarioEmissor = ".$idUsuarioEmissor." AND p.Orgao = $Orgao";
        if ($lote) {
            $sql .= " and h.idLote = $lote";
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

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
				where Acao in(2,4) and stEstado = 1 and h.idDocumento != 0";

        if ($codOrgao) {
            $sql .= " AND idOrigem = $codOrgao";
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
           xd("Erro ao salvar Projeto: " . $e->getMessage());
        }
        return $db->fetchAll($sql);
    }

    public static function buscarCancelOrgao($codOrgao = null)
    {
        $sql = "select distinct idUnidade as idDestino, Sigla as Destino, idLote
				from SAC.dbo.tbHistoricoDocumento h
				inner join SAC.dbo.Orgaos org on org.Codigo = h.idUnidade
				where Acao in (2,4) and stEstado = 1 and h.idDocumento != 0";

        if ($codOrgao) {
            $sql .= " AND idOrigem = $codOrgao";
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
           xd("Erro ao salvar Projeto: " . $e->getMessage());
        }
        return $db->fetchAll($sql);
    }

    public static function listaOrgaos()
    {
        $sql = "SELECT * FROM SAC.dbo.Orgaos WHERE Status = 0 ORDER BY Sigla";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarHistorico($idDocumento = null, $acao, $acao2 = null, $idHistorico = null)
    {
        $sql = "SELECT top 100 idHistorico, idPronac, idDocumento, idUnidade, dtTramitacaoEnvio, CONVERT(CHAR(20), dtTramitacaoEnvio, 120) AS dtTramitacaoEnvioBR,
                           idUsuarioEmissor, meDespacho, idLote, dtTramitacaoRecebida,
                           idUsuarioReceptor, Acao, stEstado, dsJustificativa, idOrigem,  TABELAS.dbo.fnEstruturaOrgao(idOrigem,0) AS Origem
                        FROM SAC.dbo.tbHistoricoDocumento
                        WHERE stEstado = 1 AND Acao = ".$acao;

        if ($acao2) {
            $sql .=" OR Acao = ".$acao2;
        }

        if ($idDocumento) {
            $sql .=" AND idDocumento = ".$idDocumento."";
        }

        if ($idHistorico) {
            $sql .=" AND idHistorico = $idHistorico";
        }

//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarLotes($codOrgao, $acao, $idusuario = null)
    {
        $sql = "SELECT distinct hd.idLote, hd.idOrigem as idOrigem, TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) localizacao,
                        TABELAS.dbo.fnEstruturaOrgao(hd.idOrigem,0) Origem
                        FROM SAC.dbo.tbHistoricoDocumento hd
                        INNER JOIN SAC.dbo.tbDocumento doc ON hd.idDocumento = doc.idDocumento
                        INNER JOIN SAC.dbo.Projetos pro ON hd.idPronac = pro.IdPRONAC
                        WHERE Acao = $acao AND hd.stEstado = 1 AND hd.idDocumento <> 0 AND hd.idUnidade = $codOrgao";

        //and hd.idUsuarioEmissor = $idusuario

//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarOrgaos($idusuario, $acao, $orgao = null)
    {
        $sql = "SELECT distinct  hd.idLote, pro.Orgao,
				TABELAS.dbo.fnEstruturaOrgao(doc.idUnidadeCadastro,0) Origem,
				TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) localizacao
                FROM SAC.dbo.tbHistoricoDocumento hd
				INNER JOIN SAC.dbo.tbDocumento doc ON hd.idDocumento = doc.idDocumento
				INNER JOIN SAC.dbo.Projetos pro ON hd.idPronac = pro.IdPRONAC
				WHERE Acao = $acao AND hd.stEstado = 1 AND hd.idDocumento <> 0 AND hd.idUsuarioEmissor = $idusuario";

        if ($orgao) {
            $sql .= " and hd.idOrigem = $orgao";
        }

//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarOrgaosDestino($idusuario, $acao, $orgao = null)
    {
        $sql = "SELECT distinct  hd.idLote, pro.Orgao,
				TABELAS.dbo.fnEstruturaOrgao(doc.idUnidadeCadastro,0) Origem,
				TABELAS.dbo.fnEstruturaOrgao(hd.idUnidade,0) localizacao
                FROM SAC.dbo.tbHistoricoDocumento hd
				INNER JOIN SAC.dbo.tbDocumento doc ON hd.idDocumento = doc.idDocumento
				INNER JOIN SAC.dbo.Projetos pro ON hd.idPronac = pro.IdPRONAC
				WHERE Acao = $acao AND hd.stEstado = 1 AND hd.idDocumento <> 0 AND hd.idUsuarioEmissor = $idusuario";

        if ($orgao) {
            $sql .= " and hd.idUnidade = $orgao";
        }

//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarLotesTramitados(

        $formaOrigem 		= null,
                                                  $origem 			= null,
                                                  $DtDocI 			= null,
                                                  $DtDocF 			= null,
                                                  $DtEnvioI 		= null,
                                                  $DtEnvioF 		= null,
                                                  $DtRecebidoI 		= null,
                                                  $DtRecebidoF 		= null,
                                                  $lote 			= null,
                                                  $formadestino 	= null,
                                                  $destino 			= null,
                                                  $formasituacao 	= null,
                                                  $situacao 		= null,
                                                  $correio 			= null

    ) {
        $sql = "select distinct h.idLote, h.idOrigem, TABELAS.dbo.fnEstruturaOrgao(h.idOrigem,0)AS Origem
				from SAC.dbo.tbHistoricoDocumento h
				INNER JOIN  SAC.dbo.tbDocumento AS d ON h.idDocumento = d.idDocumento
				INNER JOIN  SAC.dbo.Projetos AS p ON d.idPronac = p.IdPRONAC
				INNER JOIN  SAC.dbo.tbTipoDocumento AS t ON d.idTipoDocumento = t.idTipoDocumento
				WHERE  (h.idDocumento <> 0) AND (h.idDocumento is not null) AND h.stEstado = 1";

        if ($origem) {
            $sql .= " AND h.idOrigem ".$formaOrigem." ".$origem;
        }

        if (($DtDocI) && ($DtDocF == null)) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) = '".$DtDocI."'";
        }
        if ($DtDocI && $DtDocF) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) between '".$DtDocI."' AND '".$DtDocF."' ";
        }

        if (($DtEnvioI) && ($DtEnvioF == null)) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoEnvio,112)as smalldatetime) = '".$DtEnvioI."'";
        }
        if ($DtEnvioI && $DtEnvioF) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoEnvio,112)as smalldatetime) between '".$DtEnvioI."' AND '".$DtEnvioF."' ";
        }
        if (($DtRecebidoI) && ($DtRecebidoF == null)) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoRecebida,112)as smalldatetime) = '".$DtRecebidoI."'";
        }
        if ($DtRecebidoI && $DtRecebidoF) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoRecebida,112)as smalldatetime) between '".$DtRecebidoI."' AND '".$DtRecebidoF."' ";
        }
        if ($lote) {
            $sql .= " AND h.idLote = ".$lote;
        }
        if ($destino) {
            $sql .= " AND h.idUnidade ".$formadestino." ".$destino;
        }
        if ($situacao) {
            $sql .= " AND h.Acao ".$formasituacao." ".$situacao." AND h.stEstado = 1";
        }
        if ($correio) {
            $sql .= " AND d.CodigoCorreio = "."'".$correio."'";
        }

        $sql .= " order by h.idOrigem";
//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);

        //x($sql);
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    public static function buscarOrgaosTramitados(
        $formaOrigem 		= null,
                                                  $origem 			= null,
                                                  $DtDocI 			= null,
                                                  $DtDocF 			= null,
                                                  $DtEnvioI 		= null,
                                                  $DtEnvioF 		= null,
                                                  $DtRecebidoI 		= null,
                                                  $DtRecebidoF 		= null,
                                                  $lote 			= null,
                                                  $formadestino 	= null,
                                                  $destino 			= null,
                                                  $formasituacao 	= null,
                                                  $situacao 		= null,
                                                  $correio 			= null
    ) {
        $sql = "select distinct idOrigem as idOrigem,
				CASE
					WHEN h.idOrigem IS NOT NULL AND h.idOrigem <> 0
						THEN TABELAS.dbo.fnEstruturaOrgao(h.idOrigem, 0)
					WHEN h.idOrigem IS NULL OR h.idOrigem = 0
						THEN TABELAS.dbo.fnEstruturaOrgao(d.idUnidadeCadastro,0)
					END AS Origem
				 from SAC.dbo.tbHistoricoDocumento h
				 INNER JOIN  SAC.dbo.tbDocumento AS d ON h.idDocumento = d.idDocumento
				INNER JOIN  SAC.dbo.Projetos AS p ON d.idPronac = p.IdPRONAC
				INNER JOIN  SAC.dbo.tbTipoDocumento AS t ON d.idTipoDocumento = t.idTipoDocumento
				WHERE  (h.idDocumento <> 0) AND (h.idDocumento is not null) AND h.stEstado = 1";

        if ($origem) {
            $sql .= " AND h.idOrigem ".$formaOrigem." ".$origem;
        }

        if (($DtDocI) && ($DtDocF == null)) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) = '".$DtDocI."'";
        }
        if ($DtDocI && $DtDocF) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) between '".$DtDocI."' AND '".$DtDocF."' ";
        }

        if (($DtEnvioI) && ($DtEnvioF == null)) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoEnvio,112)as smalldatetime) = '".$DtEnvioI."'";
        }
        if ($DtEnvioI && $DtEnvioF) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoEnvio,112)as smalldatetime) between '".$DtEnvioI."' AND '".$DtEnvioF."' ";
        }
        if (($DtRecebidoI) && ($DtRecebidoF == null)) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoRecebida,112)as smalldatetime) = '".$DtRecebidoI."'";
        }
        if ($DtRecebidoI && $DtRecebidoF) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoRecebida,112)as smalldatetime) between '".$DtRecebidoI."' AND '".$DtRecebidoF."' ";
        }
        if ($lote) {
            $sql .= " AND h.idLote = ".$lote;
        }
        if ($destino) {
            $sql .= " AND h.idUnidade ".$formadestino." ".$destino;
        }
        if ($situacao) {
            $sql .= " AND h.Acao ".$formasituacao." ".$situacao." AND h.stEstado = 1";
        }
        if ($correio) {
            $sql .= " AND d.CodigoCorreio = "."'".$correio."'";
        }

        $sql .= " order by h.idOrigem";
//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);

        //x($sql);
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarDoc($id)
    {
        $sql = "SELECT * FROM SAC.dbo.tbDocumento WHERE idDocumento = ".$id;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 10485760");
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function listaTipoDocumentos()
    {
        $sql = "SELECT * FROM SAC.dbo.tbTipoDocumento";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscaProjeto($pronac)
    {
        $sql = "select Processo, nomeprojeto , TABELAS.dbo.fnEstruturaOrgao(orgao,0) localizacao, Orgao, IdPRONAC
				from sac.dbo.projetos p
				where anoprojeto+sequencial = '$pronac'";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaGuiasTramitacao($idOrigem, $pronac = null, $lote = null, $destino = null, $dtDocI = null, $dtDocF = null, $dtEnvioI = null, $dtEnvioF = null)
    {
        $sql = "SELECT     distinct h.idLote, CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) AS dtEnvio, TABELAS.dbo.fnEstruturaOrgao(p.Orgao,0)AS Destino
				FROM        SAC.dbo.tbHistoricoDocumento AS h
				INNER JOIN  SAC.dbo.tbDocumento AS d ON h.idDocumento = d.idDocumento
				INNER JOIN  SAC.dbo.Projetos AS p ON d.idPronac = p.IdPRONAC
				INNER JOIN  SAC.dbo.tbTipoDocumento AS t ON d.idTipoDocumento = t.idTipoDocumento
					WHERE  (h.idDocumento <> 0) AND (h.idDocumento is not null) AND (h.idLote is not null) AND h.Acao = 2 AND d.idUnidadeCadastro = ".$idOrigem."";

        if ($pronac) {
            $sql .= " AND p.AnoProjeto+p.Sequencial = '".$pronac."'";
        }
        if ($lote) {
            $sql .= " AND h.idLote =  ".$lote;
        }
        if ($destino) {
            $sql .= " AND p.Orgao = ".$destino;
        }
        if (($dtDocI) && ($dtDocF == null)) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) = '".$dtDocI."'";
        }
        if ($dtDocI && $dtDocF) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) between '".$dtDocI."' AND '".$dtDocF."' ";
        }
        if (($dtEnvioI) && ($dtEnvioF == null)) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoEnvio,112) as smalldatetime) = '".$dtEnvioI."'";
        }
        if ($dtEnvioI && $dtEnvioF) {
            $sql .= "AND cast(convert(char(8),h.dtTramitacaoEnvio,112)as smalldatetime) between '".$dtEnvioI."' AND '".$dtEnvioF."'";
        }


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }



    public static function ConsultaDocumentoOrgao(



        $formaOrigem 		= null,
                                                  $origem 			= null,
                                                  $DtDocI 			= null,
                                                  $DtDocF 			= null,
                                                  $DtEnvioI 		= null,
                                                  $DtEnvioF 		= null,
                                                  $DtRecebidoI 		= null,
                                                  $DtRecebidoF 		= null,
                                                  $lote 			= null,
                                                  $formadestino 	= null,
                                                  $destino 			= null,
                                                  $formasituacao 	= null,
                                                  $situacao 		= null,
                                                  $correio 			= null



    ) {
        $sql = "SELECT distinct top 100 p.Orgao, p.IdPRONAC, p.AnoProjeto+p.Sequencial as Pronac, p.NomeProjeto, d.idDocumento,
				t.dsTipoDocumento, p.Processo, h.idUsuarioReceptor as Receptor, d.NoArquivo, h.idLote,
				CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) as dtEnvio,
				CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) as dtRecebida,
				CONVERT(CHAR(10), d.dtDocumento,103) as dtDocumento,
				CASE
				WHEN h.Acao = 1 THEN 'Cadastrado'
				WHEN h.Acao = 2 THEN 'Enviado'
				WHEN h.Acao = 3 THEN 'Recebido'
				WHEN h.Acao = 4 THEN 'Recusado'
				WHEN h.Acao = 6 THEN 'Anexado' END AS Situacao,
				h.idUsuarioEmissor AS Emissor, h.idUsuarioEmissor,
				h.idUnidade as idDestino,
				TABELAS.dbo.fnEstruturaOrgao(h.idUnidade,0)AS Destino, h.idLote,
				p.OrgaoOrigem,
				h.idOrigem as idOrigem,
				TABELAS.dbo.fnEstruturaOrgao(h.idOrigem,0)AS Origem,
				h.idLote, p.Orgao, h.Acao, d.CodigoCorreio
				FROM        SAC.dbo.tbHistoricoDocumento AS h
				INNER JOIN  SAC.dbo.tbDocumento AS d ON h.idDocumento = d.idDocumento
				INNER JOIN  SAC.dbo.Projetos AS p ON d.idPronac = p.IdPRONAC
				INNER JOIN  SAC.dbo.tbTipoDocumento AS t ON d.idTipoDocumento = t.idTipoDocumento
					WHERE  (h.idDocumento <> 0) AND (h.idDocumento is not null) AND h.stEstado = 1";

        if ($origem) {
            $sql .= " AND h.idOrigem ".$formaOrigem." ".$origem;
        }

        if (($DtDocI) && ($DtDocF == null)) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) = '".$DtDocI."'";
        }
        if ($DtDocI && $DtDocF) {
            $sql .= " AND cast(convert(char(8),d.dtDocumento,112)as smalldatetime) between '".$DtDocI."' AND '".$DtDocF."' ";
        }

        if (($DtEnvioI) && ($DtEnvioF == null)) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoEnvio,112)as smalldatetime) = '".$DtEnvioI."'";
        }
        if ($DtEnvioI && $DtEnvioF) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoEnvio,112)as smalldatetime) between '".$DtEnvioI."' AND '".$DtEnvioF."' ";
        }
        if (($DtRecebidoI) && ($DtRecebidoF == null)) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoRecebida,112)as smalldatetime) = '".$DtRecebidoI."'";
        }
        if ($DtRecebidoI && $DtRecebidoF) {
            $sql .= " AND cast(convert(char(8),h.dtTramitacaoRecebida,112)as smalldatetime) between '".$DtRecebidoI."' AND '".$DtRecebidoF."' ";
        }
        if ($lote) {
            $sql .= " AND h.idLote = ".$lote;
        }
        if ($destino) {
            $sql .= " AND h.idUnidade ".$formadestino." ".$destino;
        }
        if ($situacao) {
            $sql .= " AND h.Acao ".$formasituacao." ".$situacao." AND h.stEstado = 1";
        }
        if ($correio) {
            $sql .= " AND d.CodigoCorreio = "."'".$correio."'";
        }

        $sql .= " order by h.idOrigem";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function ConsultaDocumentoEmissor($orgao)
    {
        $sql = "SELECT  distinct top 10 h.idUsuarioEmissor, h.idUsuarioEmissor AS Emissor
							FROM        SAC.dbo.tbHistoricoDocumento AS h
							INNER JOIN  SAC.dbo.tbDocumento AS d ON h.idDocumento = d.idDocumento
							INNER JOIN  SAC.dbo.Projetos AS p ON d.idPronac = p.IdPRONAC
							INNER JOIN  SAC.dbo.tbTipoDocumento AS t ON d.idTipoDocumento = t.idTipoDocumento
								WHERE  (h.idDocumento <> 0) AND (h.idDocumento is not null) AND p.Orgao = ".$orgao;



        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function cadDocumento($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($dados);
        return $db->lastInsertId();
    }

    public static function cadHistorico($base, $dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->insert($base, $dados);
        return $db->lastInsertId();
    }

    public static function NovoLote()
    {
        $sql = "INSERT INTO SAC.dbo.tbLote (dtLote)values(GETDATE())";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($sql);

        return $db->lastInsertId();
    }

    public static function GravarHistorico($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->insert('SAC.dbo.tbHistoricoDocumento', $dados);
    }

    public static function MudaEstado($idDocumento)
    {
        $sql = "UPDATE SAC.dbo.tbHistoricoDocumento SET stEstado = 0 WHERE stEstado = 1 and idDocumento = ".$idDocumento;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function ExcluirDoc($idDocumento, $tabela)
    {
        $sql  = "DELETE FROM " .$tabela. " WHERE  idDocumento = ".$idDocumento;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }
}
