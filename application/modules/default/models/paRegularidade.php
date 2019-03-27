<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paRegularidade
 */

class paRegularidade extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name  = 'paRegularidade';

    public function exec($CNPJCPF)
    {
        $sql = "exec ".$this->_schema.".".$this->_name." '$CNPJCPF' ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
