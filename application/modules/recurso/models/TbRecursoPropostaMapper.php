<?php

class Recurso_Model_TbRecursoPropostaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Recurso_Model_DbTable_TbRecursoProposta');
    }

    public function save($model)
    {
        return parent::save($model);
    }
}
