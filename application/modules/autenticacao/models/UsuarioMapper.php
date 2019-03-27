<?php
class Autenticacao_Model_UsuarioMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('Autenticacao_Model_DbTable_Usuario');
    }
}
