<?php

/**
 * spVisualizarPlanilhaOrcamentaria
 *
 * @uses GenericModel
 * @author
 */
class spVisualizarPlanilhaOrcamentaria extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name  = 'spVisualizarPlanilhaOrcamentaria';

    public function exec($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "exec ".$this->_banco.".".$this->_name." $idPronac";
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
