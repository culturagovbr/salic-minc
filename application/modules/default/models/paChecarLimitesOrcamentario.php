<?php

/**
 * Description of paChecarLimitesOrcamentario
 *
 */
class paChecarLimitesOrcamentario extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'paChecarLimitesOrcamentario';

    public function exec($idPronac, $fase)
    {
        $sql = "exec ".$this->_banco.".".$this->_name." $idPronac, $fase ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
