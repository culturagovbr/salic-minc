<?php

class Admissibilidade_Model_SugestaoEnquadramentoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('Admissibilidade_Model_DbTable_SugestaoEnquadramento');
    }

    public function fetchAll()
    {
        $resultSet = $this->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Admissibilidade_Model_SugestaoEnquadramento($row->toArray());
            $entries[] = $entry->toArray();
        }
        return $entries;
    }
}