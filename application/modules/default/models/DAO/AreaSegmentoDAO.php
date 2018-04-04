<?php

class AreaSegmentoDAO extends Zend_Db_Table
{
    public static function consultaAreaCultural()
    {
        $sql = "SELECT * FROM SAC.dbo.Area ";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            xd($e->getMessage());
        }

        return $db->fetchAll($sql);
    }
}
