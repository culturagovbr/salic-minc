<?php 


class tbEditalAreaSegmento extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbEditalAreaSegmento';
    protected $_primary = array('idEdital','idArea','idSegmento');

    
    public function buscarAreasSegmento($idEdital, $dbg = null)
    {
        $select = $this->select();
          
        $select->setIntegrityCheck(false);
      
        $select->from(
      
            array('eas' => 'tbEditalAreaSegmento'),
                        array('idEditalAreaSegmento',
                              'idEdital',
                              'idArea',
                              'idSegmento')
        );
          
        $select->where('idEdital = ?', $idEdital);
          
        if ($dbg) {
            xd($select->assemble());
        }
        
        return $this->fetchAll($select)->toArray();
    }
}
