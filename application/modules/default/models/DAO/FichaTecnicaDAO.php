<?php
class FichaTecnicaDAO
{
    public static function buscarFichaTecnica($idPronac, $idPedidoAlteracao = null)
    {
        $sql = "select
                   idPreProjeto, pre.FichaTecnica
                from
                    SAC.dbo.PreProjeto pre
                    inner join SAC.dbo.Projetos pro on pro.idProjeto = pre.idPreProjeto
                    inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa on tpa.IdPRONAC = pro.IdPRONAC
                    inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = tpa.idPedidoAlteracao
                where
                    tpa.IdPRONAC = {$idPronac} and tpa.idPedidoAlteracao = $idPedidoAlteracao and tpxa.tpAlteracaoProjeto = 3";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }

    public static function buscarFichaTecnicaFinal($idPronac, $idPedidoAlteracao = null)
    {
        $sql = "select
                   idPreProjeto, pre.FichaTecnica
                from
                    SAC.dbo.PreProjeto pre
                    inner join SAC.dbo.Projetos pro on pro.idProjeto = pre.idPreProjeto
                    inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa on tpa.IdPRONAC = pro.IdPRONAC
                    inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = tpa.idPedidoAlteracao
                where
                    tpa.IdPRONAC = {$idPronac} and tpa.idPedidoAlteracao = $idPedidoAlteracao and tpxa.tpAlteracaoProjeto = 3";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
