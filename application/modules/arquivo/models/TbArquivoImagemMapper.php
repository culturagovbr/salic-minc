<?php

/**
 * Class Arquivo_Model_TbArquivoMapper
 */
class Arquivo_Model_TbArquivoImagemMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Arquivo_Model_DbTable_TbArquivoImagem');
    }

    public function save($model)
    {
        return parent::save($model);
    }

}
