<?php

class Readequacao_Model_TbTransferenciaRecursosEntreProjetosMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
    }

    public function obterTransferenciaRecursosEntreProjetos($idPronac, $acao)
    {
        $tbProjetoRecebedorRecurso = new \Readequacao_Model_DbTable_TbProjetoRecebedorRecurso();
        $queryResult = $tbProjetoRecebedorRecurso->obterTransferenciaRecursosEntreProjetos([$acao => $idPronac]);

        $result = array();

        foreach ($queryResult as $params) {
            $tbTranaferenciaRecursoEntreProjetos = new \Readequacao_Model_TbTransferenciaRecursosEntreProjetos($params);
            array_push($result, $tbTranaferenciaRecursoEntreProjetos);
        }

        return $result;
    }
}
