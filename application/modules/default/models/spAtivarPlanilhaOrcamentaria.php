<?php
class spAtivarPlanilhaOrcamentaria extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name  = 'spAtivarPlanilhaOrcamentaria';

    public function exec($idPronac)
    {
        $sql = "exec ".$this->_schema.".".$this->_name." $idPronac";
        return $this->getAdapter()->query($sql);
    }
}
