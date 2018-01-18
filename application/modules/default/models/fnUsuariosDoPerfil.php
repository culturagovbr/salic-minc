<?php

class fnUsuariosDoPerfil extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'dbo.fnUsuariosDoPerfil';

    public function usuariosDoPerfil($perfilLogado, $idUsuarioLogado)
    {
        $select = new Zend_Db_Expr("SELECT * FROM SAC.dbo.fnUsuariosDoPerfil($perfilLogado,$idUsuarioLogado) order by 2");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($select);
    }
}
