<?php

class Proposta_Model_DbTable_TbPlanilhaUnidade extends MinC_Db_Table_Abstract
{
    protected $_schema  = 'sac';
    protected $_name = 'tbPlanilhaUnidade';

    public function buscarUnidade()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('idUnidade',
                'Sigla',
                'Descricao'
                ),
            $this->_schema
        );
        $select->order('3');
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($select);
    }
}
