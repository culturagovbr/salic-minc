<?php

class Projeto_Model_DbTable_TbHomologacao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbHomologacao';
    protected $_primary = 'idHomologacao';

    public function getBy($where)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(
            array($this->_name),
            array(
                'idHomologacao',
                'idPronac',
                'tpHomologacao',
                'stDecisao',
                new Zend_Db_Expr('CAST(dsHomologacao AS TEXT) AS dsHomologacao'))
        );

        foreach ($where as $campo => $valor) {
            $select->where("{$campo} = ?", $valor);
        }

        $db->query('SET TEXTSIZE 2147483647');
        return $db->fetchRow($select, Zend_DB::FETCH_ASSOC);
    }
}
