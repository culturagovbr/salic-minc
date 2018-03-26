<?php
/**
 * DAO tbTipoInconsistencia
 * @author emanuel.sampaio - Politec
 * @since 17/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbTipoInconsistencia extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "tbTipoInconsistencia";
    protected $_primary = "idTipoInconsistencia";



    /**
     * M�todo para buscar
     * @access public
     * @param void
     * @return object/array
     */
    public function buscarDados()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);
        $select->order('idTipoInconsistencia');
        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()
} // fecha class
