<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of consultaAreaCultural
 *
 * @author 01373930160
 */
class ConsultaAreaCulturalDAO extends Zend_Db_Table
{
    public static function consultaAreaCultural()
    {
        $sql = "SELECT codigo, descricao as area FROM SAC.dbo.Area";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}
