<?php
/**
 * DAO HistoricoInsert
 * @author emanuel.sampaio - Politec
 * @since 18/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @link http://www.cultura.gov.br
 */

class HistoricoInsert extends MinC_Db_Table_Abstract
{
    protected $_schema  = "SAC";
    protected $_name    = "sysobjects";
    protected $_primary = "Id";

    /**
     * Metodo para verificar se a trigger HISTORICO_INSERT esta habilitada
     * @access public
     * @param void
     * @return integer (0 = Habilitado e 1 = desabilitado)
     */
    public function statusHISTORICO_INSERT()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $objQuery = $db->select();

        $objQuery->from(
            array( 'sysobjects' => $this->_name),
            array(
                'Habilitado' => new Zend_Db_Expr(
                    "ObjectProperty(Object_id(name), 'ExecIsTriggerDisabled')"
                )
            ),
            $this->_schema
        );

        $objQuery->where('name = ?', 'HISTORICO_INSERT');
        $resultado = $db->fetchRow($objQuery->assemble());
        return $resultado->Habilitado;
    }
}
