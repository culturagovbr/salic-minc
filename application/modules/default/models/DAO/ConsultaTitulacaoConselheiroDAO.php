<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of titulacaoConselheiro
 *
 * @author 01373930160
 */
class ConsultaTitulacaoConselheiroDAO extends Zend_Db_Table
{
    public static function exibeVotantes($area, $sttilular)
    {
        $sql = "SELECT ttc.cdArea AS CodigoArea,
                ttc.cdSegmento AS CodigoSegmento,
                nm.Descricao AS Nome,
                ttc.idAgente,
                FROM AGENTES.dbo.tbTitulacaoConselheiro ttc
                JOIN AGENTES.dbo.Nomes nm ON ttc.idAgente = nm.idAgente
                where ttc.cdarea = $area and ttc.stTitular = $sttilular ORDER BY ttc.cdArea, ttc.stTitular DESC";
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function excluirComponente($idnrreuniao)
    {
        $sql = "delete from bdcorporativo.scsac.tbvotante where idreuniao=$idnrreuniao";
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function ConsultaCoordenadorParecerista()
    {
        $sql = "select ag.idAgente,
                nm.Descricao as Nome
                from AGENTES.dbo.Agentes ag
                JOIN AGENTES.dbo.Nomes nm on nm.idAgente = ag.idAgente
                JOIN AGENTES.dbo.Visao vis on vis.idAgente = ag.idAgente
                where vis.visao = 212 and nm.TipoNome=18
                ";
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function consultaComponente($idPronac = null)
    {
        $sql = "SELECT
                pr.IdPRONAC,
                nm.Descricao,
                tdpc.stDistribuicao
                FROM
                SAC.dbo.Projetos pr
                JOIN BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao tdpc ON pr.IdPRONAC = tdpc.idPRONAC
                JOIN AGENTES.dbo.tbTitulacaoConselheiro ttc ON tdpc.idAgente = ttc.idAgente
                JOIN AGENTES.dbo.Agentes ag ON tdpc.idAgente = ag.idAgente
                JOIN AGENTES.dbo.Nomes nm ON tdpc.idAgente = nm.idAgente
                WHERE tdpc.stDistribuicao = 'A'";

        if (!empty($idPronac)) {
            $sql.= " AND pr.IdPRONAC = $idPronac ";
        }

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }
}
