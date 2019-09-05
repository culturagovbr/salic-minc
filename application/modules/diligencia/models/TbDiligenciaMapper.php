<?php

class Diligencia_Model_TbDiligenciaMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Diligencia_Model_DbTable_TbDiligencia');
    }

    public function save($model)
    {
        return parent::save($model);
    }
}
