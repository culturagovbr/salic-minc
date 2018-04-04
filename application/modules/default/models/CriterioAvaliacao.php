<?php
class CriteriosAvaliacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbCritetriosAvaliacao';

    public function buscarCriteriosAvaliacao()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}
