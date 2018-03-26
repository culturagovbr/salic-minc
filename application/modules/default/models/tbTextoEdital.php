<?php

class tbTextoEdital extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbTextoEdital';
    protected $_schema  = 'SAC';

    public function buscarTextoEdital($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idEdital = ?', $idEdital);
        $select->order('nrTexto');
        return $this->fetchAll($select);
    }

    public function buscarUltimaOrdem($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idEdital = ?', $idEdital);
        $select->limit(1);
        $select->order('nrTexto DESC');
        return $this->fetchAll($select)->toArray();
    }
}
