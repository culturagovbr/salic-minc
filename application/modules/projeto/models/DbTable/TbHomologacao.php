<?php

class Projeto_Model_DbTable_TbHomologacao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbHomologacao';
    protected $_primary = 'idHomologacao';

    public function getBy($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('idHomologacao', 'idPronac', 'tpHomologacao', 'stDecisao', 'CAST(dsHomologacao AS TEXT) AS dsHomologacao')
        );
        parent::setWhere($select, $where);
        $objResult = $this->fetchRow($select);
        return ($objResult)? $objResult->toArray() : $objResult;
    }
}
