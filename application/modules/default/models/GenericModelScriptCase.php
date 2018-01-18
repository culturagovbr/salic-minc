<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GenericModel
 *
 * @author augusto
 */
class GenericModelScriptCase extends Zend_Db_Table_Abstract
{
    public function _setupPrimaryKey()
    {
        $this->_cols = array();
        $this->_name = '';
    }
}
