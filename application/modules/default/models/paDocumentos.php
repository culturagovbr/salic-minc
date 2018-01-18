<?php
/**
 * Description of paDocumentos
 */
class paDocumentos extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name   = 'paDocumentos';

    public function marcasAnexadas($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db = $db->query("exec $this->_schema.$this->_name $idPronac");

        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        return $db->fetchAll();
    }
}
