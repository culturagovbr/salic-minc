<?php

class fnTotalCaptadoProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'dbo.fnTotalCaptadoProjeto';

    public function buscaCaptacao($AnoProjeto, $Sequencial)
    {
        $select = new Zend_Db_Expr("SELECT SAC.dbo.fnTotalCaptadoProjeto('$AnoProjeto','$Sequencial') AS vlCaptacao");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($select);
    }
}
