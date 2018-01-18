<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GerenciarPautaReuniao
 *
 * @author 01373930160
 */
class ConsultaProjetosPautaReuniaoDAO extends Zend_Db_Table
{
    public static function consultaqtdprojeto($idnrreuniao, $condicao=null)
    {
        $sql = " select COUNT(1) as qtd 
                 from BDCORPORATIVO.scSAC.tbPauta
                 where idNrReuniao=$idnrreuniao";

        if ($condicao and $condicao == 'SP') {
            $sql .= " and stEnvioPlenario = 'S'";
        }
        if ($condicao and $condicao == 'AC') {
            $sql .= " and (stAnalise = 'AC' or stAnalise = 'IC') and stEnvioPlenario = 'S'";
        }
        if ($condicao and $condicao == 'AS') {
            $sql .= " and (stAnalise = 'AS' or stAnalise = 'IS') and stEnvioPlenario = 'S'";
        }
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function consultaProjetosPautaReuniao($stEnvioPlenaria=null, $stAnalise=null, $order=null)
    {
        $sql = "SELECT
                      pr.IdPRONAC AS idPronac,
                      pr.anoprojeto+pr.sequencial as pronac,
                      pr.NomeProjeto,
                      seg.Descricao AS DescricaoSegmento,
                      par.Atendimento AS StatusAtendimento,
                      tp.dtEnvioPauta AS DataEnvioPauta,
                      tp.stEnvioPlenario AS StatusEnvioPlenario,
                      tp.stAnalise AS StatusAnalise,
                      tp.dsAnalise AS dsatusAnalise,
                      seg.Codigo,
                      ar.Codigo AS CodigoArea,
                      ar.Descricao AS DescricaoArea,
                      tr.idNrReuniao AS NumeroReuniao,
                      tr.NrReuniao,
                      tr.stEstado,
                      tr.dtfechamento,
                      tr.stPlenaria,
                      tp.stAnalise
		      FROM  SAC.dbo.Projetos pr
		      JOIN SAC.dbo.Segmento seg ON pr.Segmento = seg.Codigo
                      left JOIN SAC.dbo.Parecer par ON pr.IdPRONAC = par.idPRONAC
                      JOIN BDCORPORATIVO.scSAC.tbPauta tp ON pr.IdPRONAC = tp.IdPRONAC
                      JOIN SAC.dbo.Area ar ON pr.Area = ar.Codigo
                      JOIN SAC.dbo.tbReuniao tr ON tr.idNrReuniao = tp.idNrReuniao
                      where tr.stEstado = 0 and par.stAtivo=1 and tp.stAnalise <> 'AR'
                      ";
        if (isset($stEnvioPlenaria)) {
            $sql .=" and tp.stEnvioPlenario = '" . $stEnvioPlenaria . "'";
        }
        if (!empty($stAnalise)) {
            $sql .=" and not exists(select * from BDCORPORATIVO.scSAC.tbVotacao where idPRONAC = idPronac and idNrReuniao=NumeroReuniao )  ";
        }
        if (!empty($order)) {
            $sql .=" order by 6 $order";
        } else {
            $sql .=" order by 6 desc";
        }
//        die('<pre>'.$sql);
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
    }

    public static function evolucaoReuniao($idReuniao = null, $codigoArea = null, $stAnalise = null)
    {
        $sql = "	SELECT
               count(Pauta.idNrReuniao) as qtd
            FROM
                SAC.dbo.Projetos AS Projetos INNER JOIN
                SAC.dbo.Area AS Area ON Projetos.Area = Area.Codigo INNER JOIN
               BDCORPORATIVO.scSAC.tbPauta AS Pauta ON Projetos.IdPRONAC = Pauta.IdPRONAC INNER JOIN
               SAC.dbo.tbReuniao ON Pauta.idNrReuniao = SAC.dbo.tbReuniao.idNrReuniao
            WHERE     (SAC.dbo.tbReuniao.stEstado = 0 ) ";

        if (!empty($idReuniao)) {
            $sql .= " and Pauta.idNrReuniao=$idReuniao";
        }
        if (!empty($stAnalise)) {
            $sql .= " and Pauta.stAnalise='$stAnalise'";
        }
        if (!empty($idReuniao)) {
            $sql .= " and Area.Codigo=$codigoArea";
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
