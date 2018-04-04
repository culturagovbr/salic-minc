<?php 
class tbModuloParticipacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbModuloParticipacao';

    public function buscarModulo()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
    
    public function buscarModuloporEdital($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idEdital = ?', $idEdital);
        return $this->fetchAll($select);
    }
    
    public function associarModuloParticipacao($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
