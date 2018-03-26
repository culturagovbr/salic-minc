<?php

class AdmissibilidadeDAO extends Zend_Db_Table
{
    public static function gerenciarAnalistas(stdClass $params)
    {
        $retorno = false;
        $sql = "SELECT
                    usu_codigo as usu_cod,
                    usu_identificacao,
                    usu_nome,
                    usu_orgao,
                    usu_orgaolotacao,
                    usu_telefone,
                    org_superior,
                    uog_orgao,
                    org_siglaautorizado,
                    org_nomeautorizado,
                    sis_codigo,
                    sis_sigla,
                    sis_nome,
                    gru_codigo,
                    gru_nome,
                    uog_status,
                    id_unico
                FROM
                    TABELAS.dbo.vwUsuariosOrgaosGrupos
                WHERE sis_codigo   = 21 and
                      gru_codigo = 92 and
                      (org_superior = {$params->cod_orgao})
                ORDER BY usu_nome";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar projeto: " . $e->getMessage();
        }
        $retorno = $db->fetchAll($sql);
        return $retorno;
    }

    public static function gerenciarAnalista(stdClass $params)
    {
        $sql = " select uog.uog_usuario as usu_cod,
                        u.usu_nome + ' - ' + u.usu_identificacao as usu_nome,
                        uog.uog_orgao   as usu_orgao,
                        Tabelas.dbo.fnEstruturaOrgao(o.org_codigo, 0) as unidade,
                        uog.uog_grupo   as gru_codigo,
                        g.gru_nome,
                        uog.uog_status  as status,
                        u.usu_nome
                   from Tabelas.dbo.UsuariosXOrgaosXGrupos uog
                  inner join TABELAS.dbo.Usuarios u
                     on u.usu_codigo = uog.uog_usuario
                  inner join TABELAS.dbo.Orgaos o
                     on o.org_codigo = uog_orgao
                  inner join TABELAS.dbo.Grupos g
                     on g.gru_codigo = uog.uog_grupo
                  where uog.uog_usuario = {$params->usu_cod}
                    and uog.uog_grupo   = {$params->gru_codigo}
                    and uog.uog_orgao   = {$params->usu_orgao}
                    and gru_sistema     = 21
                    and gru_codigo      <> 97";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar projeto: " . $e->getMessage();
        }
        $retorno = $db->fetchAll($sql);
        if ($retorno) {
            $retorno = $retorno[0];
        }
        return $retorno;
    }

    public static function atualizarAnalista(stdClass $params)
    {
        $sql = "update Tabelas.dbo.UsuariosXOrgaosXGrupos
                   set uog_status  = {$params->uog_status}
                 where uog_usuario = {$params->usu_cod}
                   and uog_grupo   = {$params->gru_codigo}
                   and uog_orgao   = {$params->usu_orgao}";
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar projeto: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function consultarRedistribuirAnalise(stdClass $params)
    {
        $sql = "
            SELECT idProjeto,NomeProposta,stPlanoAnual,CNPJCPF,idAgente,
                idUsuario,Tecnico,idSecretaria,
                DtAdmissibilidade,dias,idAvaliacaoProposta,
                idMovimentacao,stTipoDemanda
            FROM sac.dbo.vwListarPropostas
            WHERE idSecretaria = {$params->usu_orgao}
            ORDER BY Tecnico";
        $retorno = false;

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar dados: " . $e->getMessage();
        }

        $retorno = $db->fetchAll($sql);
        return $retorno;
    }

    public static function consultarRedistribuirAnaliseItem(stdClass $params)
    {
        $retorno = false;
        if ($params->fase != 'Documental') {
            $sql = "select idProjeto,
                           NomeProjeto,
                           SAC.dbo.fnNomeTecnicoMinc(Tecnico) as Tecnico,
                           '{$params->fase}' Tipo
                      from SAC.dbo.vwConformidadeVisualTecnico
                     where idProjeto = {$params->idProjeto}";
        } else {
            $sql = "select idProjeto,
                           NomeProjeto,
                           SAC.dbo.fnNomeTecnicoMinc(idTecnico) as Tecnico,
                           '{$params->fase}' Tipo
                      from SAC.dbo.vwConformidadeDocumentalTecnico
                     where idProjeto = {$params->idProjeto}";
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar dados: " . $e->getMessage();
        }
        $retorno = $db->fetchAll($sql);
        if ($retorno) {
            $retorno = $retorno[0];
        }
        return $retorno;
    }

    public static function consultarRedistribuirAnaliseItemSelect(stdClass $params)
    {
        $sql = "   SELECT DISTINCT usu_codigo as usu_cod, usu_nome,org_superior
                     FROM TABELAS.dbo.vwUsuariosOrgaosGrupos
                    WHERE sis_codigo = 21 AND
                          org_superior = {$params->usu_orgao} AND
                          gru_codigo = 92 AND
                          uog_status = 1 -- AND
                          -- usu_nome <> '{$params->usu_nome}'
                    ORDER BY usu_nome";
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar dados: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function redistribuirAnalise(stdClass $params)
    {
        $sql = "UPDATE SAC.dbo.tbAvaliacaoProposta
                   SET idTecnico = {$params->usu_cod}
                 WHERE idProjeto = {$params->idProjeto}
                   AND stEstado  = 0";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao executar comando sql: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function gerenciamentodepropostas(stdClass $params)
    {
        $sql = "SELECT
                    idProjeto,
                    NomeProposta,
                    idAgente,
                    CNPJCPF,
                    idUsuario,
                    Tecnico,
                    idSecretaria,
                    CONVERT(CHAR(20),DtAdmissibilidade, 120) AS DtAdmissibilidade,
                    DAY(DtAdmissibilidade - GETDATE()) as dias,
                    idAvaliacaoProposta,
                    idMovimentacao,
                    stTipoDemanda
                FROM
                    SAC.dbo.vwGerenciarProposta
                WHERE idSecretaria = {$params->cod_orgao}
                ORDER BY Tecnico, DtAdmissibilidade";
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar dados: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }


    public static function consultarGerenciamentoProposta($where = array(), $order = array())
    {
        $meuWhere = "";
        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $meuWhere .= $coluna . $valor . " AND ";
        }

        $meuOrder = "";
        // adicionando clausulas order
        foreach ($order as $valor) {
            if ($meuOrder != "") {
                $meuOrder .= " , ";
            } else {
                $meuOrder = " ORDER BY ";
            }
            $meuOrder .= $valor;
        }

        $sql = "SELECT p.idPreProjeto AS idProjeto,
                           p.NomeProjeto AS NomeProposta,
                           p.stPlanoAnual,
                           a.CNPJCPF,
                           p.idAgente,
                           x.idTecnico AS idUsuario,
                           SAC.dbo.fnNomeTecnicoMinc(x.idTecnico) AS Tecnico,
                           SAC.dbo.fnIdOrgaoSuperiorAnalista(x.idTecnico) AS idSecretaria,
                           CONVERT(CHAR(20),x.DtAvaliacao, 120) AS DtAdmissibilidade,
                           DATEDIFF(d, x.DtAvaliacao, GETDATE()) as dias,
                           x.idAvaliacaoProposta,
                           m.idMovimentacao,
                           p.stTipoDemanda
                FROM   SAC.dbo.PreProjeto AS p
                INNER JOIN SAC.dbo.tbMovimentacao AS m ON p.idPreProjeto = m.idProjeto AND m.Movimentacao = 127 AND m.stEstado = 0
                INNER JOIN SAC.dbo.tbAvaliacaoProposta AS x ON p.idPreProjeto = x.idProjeto AND x.ConformidadeOK = 1 AND x.stEstado = 0
                INNER JOIN AGENTES.dbo.Agentes AS a ON p.idAgente = a.idAgente
                WHERE {$meuWhere} (p.stEstado = 1) and p.stTipoDemanda = 'NA'
                AND (NOT EXISTS
                        (
                        SELECT TOP (1) IdPRONAC, AnoProjeto, Sequencial, UfProjeto, Area, Segmento, Mecanismo, NomeProjeto, Processo, CgcCpf, Situacao,
                                                   DtProtocolo, DtAnalise, Modalidade, OrgaoOrigem, Orgao, DtSaida, DtRetorno, UnidadeAnalise, Analista, DtSituacao, ResumoProjeto,
                                   ProvidenciaTomada, Localizacao, DtInicioExecucao, DtFimExecucao, SolicitadoUfir, SolicitadoReal, SolicitadoCusteioUfir,
                                   SolicitadoCusteioReal, SolicitadoCapitalUfir, SolicitadoCapitalReal, Logon, idProjeto
                        FROM           SAC.dbo.Projetos AS u
                        WHERE          (p.idPreProjeto = idProjeto)
                        )
                )
                AND (NOT EXISTS
                        (
                        SELECT     TOP (1) Contador, idProjeto, CodigoDocumento, Opcao
                        FROM       SAC.dbo.vwDocumentosPendentes AS z
                        WHERE      (p.idPreProjeto = idProjeto)
                        )
                )
        {$meuOrder} ";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar dados: " . $e->getMessage();
        }
    }


    public static function buscarAnalistas($where = array(), $order = array())
    {
        $meuWhere = "";
        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $meuWhere .= $coluna . $valor . " AND ";
        }

        $meuOrder = "";
        // adicionando clausulas order
        foreach ($order as $valor) {
            if ($meuOrder != "") {
                $meuOrder .= " , ";
            } else {
                $meuOrder = " ORDER BY ";
            }
            $meuOrder .= $valor;
        }

        $sql = " SELECT
                    usu_codigo as usu_cod,
                    usu_identificacao,
                    usu_nome,
                    usu_orgao,
                    usu_orgaolotacao,
                    usu_telefone,
                    org_superior,
                    uog_orgao,
                    org_siglaautorizado,
                    org_nomeautorizado,
                    sis_codigo,
                    sis_sigla,
                    sis_nome,
                    gru_codigo,
                    gru_nome,
                    uog_status,
                    id_unico
                FROM
                    TABELAS.dbo.vwUsuariosOrgaosGrupos
                WHERE {$meuWhere} 1=1
                ORDER BY usu_nome";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }


    public static function consultarProposta($idProjeto)
    {
        $sql = "
            SELECT idProjeto,NomeProposta,stPlanoAnual,CNPJCPF,idAgente,
                idUsuario,Tecnico,idSecretaria,
                DtAdmissibilidade,dias,idAvaliacaoProposta,
                idMovimentacao,stTipoDemanda
            FROM sac.dbo.vwListarPropostas
            WHERE idProjeto = {$idProjeto}
            ORDER BY Tecnico";
        $retorno = false;

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Falha ao buscar dados: " . $e->getMessage();
        }

        $retorno = $db->fetchRow($sql);
        return $retorno;
    }
}
