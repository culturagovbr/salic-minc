<?php

class Projeto_Model_spVisualizarPlanilhaOrcamentaria extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name  = 'spVisualizarPlanlhaOrcamentaria';

    public function exec($idPronac)
    {
        $sql = "exec ".$this->_schema.".".$this->_name." $idPronac";
        return $this->getAdapter()->fetchAll($sql);
    }
}
