<?php
class Area extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'Area';

    public function BuscarAreaProjeto($idpronac=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array('a.Descricao as nomeArea')
                     );
        $select->joinInner(
                            array('pr'=>'Projetos'),
                            'pr.Area = a.Codigo'
                          );
        if ($idpronac) {
            $select->where('pr.IdPRONAC = ?', $idpronac);
        }

        return $this->fetchRow($select);
    }


    public function BuscarAreas()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array('a.*')
                     );

        return $this->fetchAll($select);
    }
}
