<?php

class Proposta_Model_TbPlanilhaPropostaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Proposta_Model_DbTable_TbPlanilhaProposta');
    }

    public function save($model)
    {
        return parent::save($model);
    }
}
