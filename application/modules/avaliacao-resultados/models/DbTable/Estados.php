<?php

class AvaliacaoResultados_Model_DbTable_Estados extends MinC_Db_Table_Abstract
{
    protected $_name = "Estados";
    protected $_schema = "SAC";
    protected $_primary = "id";

    public function all() {
        return $this->fetchAll();
    }

    public function findBy($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        /* $select->setIntegrityCheck(false); */
        $select->from(
            array('e' => $this->_name),
            array('*'),
            $this->_schema
        )
        ->where('id = ? ', $id);

        return $db->fetchRow($select);
    }
}