<?php
class tbprorrogacaoprazoDao extends Zend_Db_Table
{
    protected $_name = "bdcorporativo.scsac.tbprorrogracaoprazo";

    public static function buscarDadosProrrogacaoPrazo($idpedidoalteracao)
    {
        $sql = "
            select
            ppraz.dtinicioprazo,
            ppraz.dtfimprazo,
            ppraz.dsjustificativa,
            tap.idPRONAC
            from BDCORPORATIVO.scSAC.tbProrrogacaoPrazo ppraz
            join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tap on tap.idPedidoAlteracao = ppraz.idPedidoAlteracao
            where ppraz.idpedidoalteracao = ".$idpedidoalteracao;
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function alterarProrrogracaoPrazoCap($dados, $idpronac)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idpronac = ".$idpronac;
        $alterar = $db->update("sac.dbo.aprovacao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function alterarProrrogracaoPrazoExec($dados, $idpronac)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idpronac = ".$idpronac;
        $alterar = $db->update("sac.dbo.projetos", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
}
