<?php

class Navegacao_Model_PerfilMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
    }

    public function buscarPerfisDisponiveis($usu_codigo, $sis_codigo)
    {
        $tbPerfil = new \Navegacao_Model_DbTable_TbPerfil();
        $queryResult = $tbPerfil->buscarPerfisDisponiveis($usu_codigo, $sis_codigo);

        $result = [];

        foreach ($queryResult as $params) {
            $tbPerfil = new \Navegacao_Model_Perfil($params);
            array_push($result, $tbPerfil);
        }

        return $result;
    }
}
