<?php

class AvaliacaoResultados_Model_DbTable_FluxosProjeto extends MinC_Db_Table_Abstract
{
    protected $_name = "FluxosProjeto";
    protected $_schema = "SAC";
    protected $_primary = "id";

    public function projetos($estadoId)
    {
        /* $db = Zend_Db_Table::getDefaultAdapter(); */
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('e' => $this->_name),
            array('*'),
            $this->_schema
        )
        ->join(
            ['p' => 'projetos'],
            'p.idpronac = e.idpronac',
            '*',
            $this->_schema
        )
        ->where('estadoId = ? ', $estadoId);

        return $this->fetchAll($select);
    }
}