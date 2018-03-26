<?php
/**
 * DAO TipoDocumento
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class TipoDocumentoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "BDCORPORATIVO.scSAC.tbTipoDocumento";
    protected $_primary = "idTipoDocumento";



    /**
     * M�todo para buscar os tipos de documentos
     * @access public
     * @static
     * @param void
     * @return object || bool
     */
    public static function buscar()
    {
        $sql = "SELECT * FROM SAC.dbo.tbTipoDocumento ORDER BY dsTipoDocumento";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscar()
} // fecha class
