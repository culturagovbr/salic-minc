<?php

/**
 * Class Arquivo_Model_TbArquivoMapper
 */
class Arquivo_Model_TbArquivoMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Arquivo_Model_DbTable_TbArquivo');
    }
}
