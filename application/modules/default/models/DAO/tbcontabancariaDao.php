<?php
class tbcontabancariaDao extends Zend_Db_Table
{
    protected $_name = "SAC.dbo.ContaBancaria";

    public static function buscarDadosContaBancaria($idpronac)
    {
        $sql = "select Banco,
                Agencia,
                anoprojeto,
                sequencial,
                ContaBloqueada,
                dtloteremessacb,
                ContaLivre,
                dtloteremessacl
                from SAC.dbo.ContaBancaria where idPronac=".$idpronac;
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
