<?php
/**
 * Modelo Tipodocumento
 * @author Equipe RUP - Politec
 * @since 30/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Tipodocumento
{
    /**
     * M�todo para buscar os tipos de documentos
     * @access public
     * @param void
     * @return object
     */
    public static function buscar()
    {
        $sql = "SELECT DISTINCT dsTipoDocumento, idTipoDocumento ";
        $sql.= "FROM BDCORPORATIVO.scSAC.tbTipoDocumento ";
        $sql.= "ORDER BY dsTipoDocumento;";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    } // fecha m�todo buscar()
} // fecha class
