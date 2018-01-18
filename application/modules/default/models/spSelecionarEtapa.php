<?php

/**
 * Description of spSelecionarEtapa
 *
 */
class spSelecionarEtapa extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'spSelecionarEtapa';

    /**
     * exec
     *
     * @param mixed $idPronac
     * @access public
     * @return void
     * @todo remover metodo e passar para Zend_DB
     */
    public function exec($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "exec ".$this->_banco.".".$this->_name." $idPronac";
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
