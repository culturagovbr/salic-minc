<?php
class paCoordenadorDoPerfil extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'paCoordenadorDoPerfil';

    public function buscarUsuarios($codPerfil, $codOrgao)
    {
        $sql = new Zend_Db_Expr("exec ".$this->_banco.".".$this->_name." $codPerfil, $codOrgao ");
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
