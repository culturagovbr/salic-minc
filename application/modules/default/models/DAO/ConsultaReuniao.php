<?php
class ConsultaReuniao
{
    public static function listaReuniao()
    {
        $sql = "SELECT
                 SAC.dbo.tbReuniao.idNrReuniao,
                 BDCORPORATIVO.scSAC.tbPauta.IdPRONAC,
                 SAC.dbo.tbReuniao.NrReuniao, tbReuniao.DtInicio,
                 SAC.dbo.tbReuniao.DtFechamento,
                 SAC.dbo.tbReuniao.stEstado
            FROM
               BDCORPORATIVO.scSAC.tbPauta
            INNER JOIN
                      SAC.dbo.tbReuniao ON
                      BDCORPORATIVO.scSAC.tbPauta.idNrReuniao = SAC.dbo.tbReuniao.idNrReuniao
            WHERE SAC.dbo.tbReuniao.stEstado = 0";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}

