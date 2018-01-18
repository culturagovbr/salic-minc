<?php 
class porcentagemCaptacaoDao extends Zend_Db_Table
{
    protected $_name = "SAC.dbo.fnPercentualCaptado";

    public static function buscarDadosProrrogacaoPrazo($ano, $sequencial)
    {
        $sql = "select SAC.dbo.fnPercentualCaptado(".$ano.",".$sequencial.")";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
