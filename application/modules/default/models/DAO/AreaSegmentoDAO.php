<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AreaSegmentoDAO
 *
 * @author 01129075125
 */
class AreaSegmentoDAO extends Zend_Db_Table
{
    public static function consultaAreaCultural()
    {
        $sql = "SELECT * FROM SAC.dbo.Area ";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}
