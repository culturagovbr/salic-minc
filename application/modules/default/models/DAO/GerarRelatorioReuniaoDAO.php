<?php
class GerarRelatorioReuniaoDAO extends Zend_Db_Table
{
    public static function geraRelatorioReuniao()
    {
        $sql = "SELECT
                    tbReuniao.idNrReuniao,
                    BDCORPORATIVO.scSAC.tbPauta.IdPRONAC,
                    tbReuniao.NrReuniao,
                    tbReuniao.DtInicio,
                    tbReuniao.DtFechamento,
                    tbReuniao.stEstado,
                    BDCORPORATIVO.scSAC.tbPauta.stEnvioPlenario
                FROM         
                   BDCORPORATIVO.scSAC.tbPauta
                INNER JOIN
                   SAC.dbo.tbReuniao as tbReuniao
                   ON BDCORPORATIVO.scSAC.tbPauta.idNrReuniao = tbReuniao.idNrReuniao
                WHERE     (tbReuniao.stEstado = 0)";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function consultaAreaCultural()
    {
        $sql = "SELECT * FROM SAC.dbo.Area ";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function consultaProjetosPautaReuniao($idReuniao = null)
    {
        $sql = "SELECT     pr.IdPRONAC AS idPronac, pr.AnoProjeto + pr.Sequencial AS pronac, pr.NomeProjeto, seg.Descricao AS DescricaoSegmento,
                      par.Atendimento AS StatusAtendimento, tp.dtEnvioPauta AS DataEnvioPauta, tp.stEnvioPlenario AS StatusEnvioPlenario, tp.stAnalise AS StatusAnalise,
                      tp.dsAnalise AS dsatusAnalise, seg.Codigo, ar.Descricao AS DescricaoArea, tr.idNrReuniao AS NumeroReuniao, tr.NrReuniao, tr.stEstado,
                      ar.Codigo AS CodigoArea
FROM         SAC.dbo.Projetos AS pr INNER JOIN
                      SAC.dbo.Segmento AS seg ON pr.Segmento = seg.Codigo INNER JOIN
                      SAC.dbo.Parecer AS par ON pr.IdPRONAC = par.idPRONAC INNER JOIN
                      BDCORPORATIVO.scSAC.tbPauta AS tp ON pr.IdPRONAC = tp.IdPRONAC INNER JOIN
                      SAC.dbo.Area AS ar ON pr.Area = ar.Codigo INNER JOIN
                      SAC.dbo.tbReuniao AS tr ON par.idPRONAC = pr.IdPRONAC where stEnvioPlenario = 'S'
and     (tr.stEstado = 0)";
        if ($idReuniao != 0) {
            $sql .=" and tr.NrReuniao = $idReuniao";
        }

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }

    public static function consultaValorAprovado($idPronac = null)
    {
        $sql = "SELECT DISTINCT
                      SAC.dbo.Projetos.IdPRONAC AS Pronac,
                      BDCORPORATIVO.scSAC.tbPauta.idNrReuniao AS NumeroReuniao,
                      SAC.dbo.Projetos.NomeProjeto,
                      SAC.dbo.tbReuniao.stEstado AS StatusEstado,
                      SAC.dbo.tbPlanilhaAprovacao.nrOcorrencia AS NumeroOcorrencia,
                      SAC.dbo.tbPlanilhaAprovacao.vlUnitario AS ValorUnitario,
                      SAC.dbo.tbPlanilhaAprovacao.qtItem AS QuantidadeItem,
                      SAC.dbo.Projetos.Area AS CodigoArea,
                      SAC.dbo.Projetos.Segmento AS CodigoSegmento,
                      SAC.dbo.Area.Descricao AS DescricaoArea,
                      SAC.dbo.tbPlanilhaAprovacao.nrOcorrencia * SAC.dbo.tbPlanilhaAprovacao.vlUnitario * SAC.dbo.tbPlanilhaAprovacao.qtItem AS Total
                FROM
                      SAC.dbo.Projetos INNER JOIN
                      BDCORPORATIVO.scSAC.tbPauta
                      ON SAC.dbo.Projetos.IdPRONAC = BDCORPORATIVO.scSAC.tbPauta.IdPRONAC
                INNER JOIN
                      SAC.dbo.tbReuniao
                      ON BDCORPORATIVO.scSAC.tbPauta.idNrReuniao = SAC.dbo.tbReuniao.idNrReuniao
                INNER JOIN
                      SAC.dbo.tbPlanilhaAprovacao ON  SAC.dbo.Projetos.IdPRONAC = SAC.dbo.tbPlanilhaAprovacao.IdPRONAC
                INNER JOIN
                      SAC.dbo.Area ON  SAC.dbo.Projetos.Area =  SAC.dbo.Area.Codigo
                WHERE     SAC.dbo.tbReuniao.stEstado = 0 ";

        if (!empty($idPronac)) {
            $sql.= " AND SAC.dbo.Projetos.IdPRONAC = $idPronac ";
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}
