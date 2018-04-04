<?php

/**
 * Description of paIncluirRecusarItem
 */
class Proposta_Model_PaIncluirRecusarItem extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'paIncluirRecusarItem';

    public function incluirRecusarItem($idSolicitarItem, $usuariologado, $stEstado)
    {
        $sql = "exec " . $this->_schema . "." . $this->_name . " $idSolicitarItem, $usuariologado, $stEstado ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
