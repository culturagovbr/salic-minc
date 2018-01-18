<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pessoa_Dados
 *
 * @author tisomar
 */
class PessoaDados extends MinC_Db_Table_Abstract
{
    protected $_banco = "TABELAS";
    protected $_name = "Pessoa_Dados";

    

    public function salvarDados($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblPessoasDados = new PessoaDados();

        $tmpTblPessoasDados = $tmpTblPessoasDados->createRow();

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['pdd_pessoa'])) {
            $tmpTblPessoasDados->pdd_pessoa = $dados['pdd_pessoa'];
        }
        if (isset($dados['pdd_meta_dado'])) {
            $tmpTblPessoasDados->pdd_meta_dado = $dados['pdd_meta_dado'];
        }
        if (isset($dados['pdd_sequencia'])) {
            $tmpTblPessoasDados->pdd_sequencia = $dados['pdd_sequencia'];
        }
        if (isset($dados['pdd_dado'])) {
            $tmpTblPessoasDados->pdd_dado = $dados['pdd_dado'];
        }

        $id = $tmpTblPessoasDados->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }
}
