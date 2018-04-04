<?php
class PaisDao extends Zend_Db_Table
{
    public function buscarPais()
    {
        $sql0 = "select * from Agentes.dbo.Pais";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }
}
