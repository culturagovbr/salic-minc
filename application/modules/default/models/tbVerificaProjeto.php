<?php

class tbVerificaProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbVerificaProjeto';
    protected $_schema = 'SAC';

    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblVerificaProjeto = new tbVerificaProjeto();

        if (isset($dados['idVerificaProjeto'])) {
            $tmpRsVerificaProjeto = $tmpTblVerificaProjeto->find($dados['idVerificaProjeto'])->current();
        } else {
            $tmpRsVerificaProjeto = $tmpTblVerificaProjeto->createRow();
        }

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['IdPRONAC'])) {
            $tmpRsVerificaProjeto->IdPRONAC = $dados['IdPRONAC'];
        }
        $id = $tmpRsVerificaProjeto->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }
}
