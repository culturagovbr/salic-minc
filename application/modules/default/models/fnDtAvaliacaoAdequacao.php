<?php

class fnDtAvaliacaoAdequacao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'fnDtAvaliacaoAdequacao';

    public function getDtAvaliacaoAdequacao($idPronac)
    {
        if (is_numeric($idPronac)) {
            $select = new Zend_Db_Expr("SELECT SAC.dbo.fnDtAvaliacaoAdequacao($idPronac)");
            try {
                $db= Zend_Db_Table::getDefaultAdapter();
                $db->setFetchMode(Zend_DB::FETCH_OBJ);
            } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
            }
            return $db->fetchAll($select);
        } else {
            return false;
        }
    }
}
