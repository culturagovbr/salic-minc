<?php

/**
 * Description of spChecarLimitesOrcamentarioReadequacao
 *
 */
class spChecarLimitesOrcamentarioReadequacao extends GenericModel
{
    protected $_banco = 'SAC';
    protected $_name  = 'spChecarLimitesOrcamentarioReadequacao';

    public function exec($idPronac, $fase)
    {
        $sql = "exec ".$this->_banco.".".$this->_name." $idPronac";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
