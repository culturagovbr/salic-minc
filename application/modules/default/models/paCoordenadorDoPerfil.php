<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paCoordenadorDoPerfil
 *
 */
class paCoordenadorDoPerfil extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'paCoordenadorDoPerfil';

    public function buscarUsuarios($codPerfil, $codOrgao)
    {
        $sql = "exec ".$this->_banco.".".$this->_name." $codPerfil, $codOrgao ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
