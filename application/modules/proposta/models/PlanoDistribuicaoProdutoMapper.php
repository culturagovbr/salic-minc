<?php

class Proposta_Model_PlanoDistribuicaoProdutoMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Proposta_Model_DbTable_PlanoDistribuicaoProduto');
    }

    public function fetchAll()
    {
        $resultSet = $this->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Proposta_Model_PlanoDistribuicaoProduto($row->toArray());
            $entries[] = $entry->toArray();
        }
        return $entries;
    }

    public function save( $model)
    {
        return parent::save($model);
    }
}
