<?php
class spSelecionarPlanilhaOrcamentariaAtiva extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name  = 'spSelecionarPlanilhaOrcamentariaAtiva';

    public function exec($idPronac)
    {
        $sql = "exec ".$this->_schema.".".$this->_name." $idPronac";
        return $this->getAdapter()->fetchOne($sql);
    }
}
