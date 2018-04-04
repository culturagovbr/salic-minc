<?php
class GenericModelScriptCase extends Zend_Db_Table_Abstract
{
    public function _setupPrimaryKey()
    {
        $this->_cols = array();
        $this->_name = '';
    }
}
