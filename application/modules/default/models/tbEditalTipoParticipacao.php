<?php 

 
class tbEditalTipoParticipacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbEditalTipoParticipacao';

    
    public function buscarParticipacaoPorEdital($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pa' => $this->_name),
                array('pa.idEditalTipoParticipacao',
                      'pa.idEdital',
                      'pa.qtParticipacao')
        );
        
        $select->joinInner(
        
            array('tp' => 'tbTipoParticipacao'),
        
            'pa.idTpParticipacao = tp.idTpParticipacao',
                        array('tp.idTpParticipacao',
                              'tp.dsTpParticipacao'),
        
            'sac.dbo'
        );
        
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        
        return $this->fetchAll($select)->toArray();
    }
    
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
    
    public function associarEditalTipoParticipacao($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
