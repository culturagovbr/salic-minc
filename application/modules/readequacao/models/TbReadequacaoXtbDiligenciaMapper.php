<?php

class Readequacao_Model_TbReadequacaoXtbDiligenciaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbReadequacaoXtbDiligencia');
    }

    public function save($model)
    {
        return parent::save($model);
    }
}
