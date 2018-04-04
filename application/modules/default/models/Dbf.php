<?php
/**
 * DAO Dbf
 * @author emanuel.sampaio - Politec
 * @since 29/07/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Dbf extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "dbo";
    protected $_name    = "Dbf";



    /**
     * M�todo para ignorar a aus�ncia da chave prim�ria
     */
    public function _setupPrimaryKey()
    {
        $this->_primary = "";
    }



    /**
     * M�todo para buscar as informa��es do arquivo DBF
     * @access public
     * @param void
     * @return object
     */
    public function buscarInformacoes()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array($this->_name), array("Informacao"));
        $select->order("Informacao ASC");
        return $this->fetchAll($select);
    } // fecha m�todo buscarInformacoes()
} // fecha class
