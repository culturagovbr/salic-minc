<?php

class Foo_Model_TabelaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('Foo_Model_DbTable_Tabela');
    }

    public function fetchAll()
    {
        $resultSet = $this->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Foo_Model_Tabela($row->toArray());
            $entries[] = $entry->toArray();
        }
        return $entries;
    }
}
