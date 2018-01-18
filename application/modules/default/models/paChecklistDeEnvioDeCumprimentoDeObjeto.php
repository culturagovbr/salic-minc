<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paChecklistDeEnvioDeCumprimentoDeObjeto
 */
class paChecklistDeEnvioDeCumprimentoDeObjeto extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'paChecklistDeEnvioDeCumprimentoDeObjeto';

    public function verificarRelatorio($idPronac)
    {
        $sql = "exec ".$this->_banco.".".$this->_name." $idPronac ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
