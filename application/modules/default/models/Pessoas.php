<?php

/**
 * Description of Pessoa
 *
 * @author tisomar
 */
class Pessoas extends MinC_Db_Table_Abstract
{
    protected $_banco = "TABELAS";
    protected $_schema = "TABELAS";
    protected $_name = "Pessoas";

    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblPessoas = new Pessoas();


        //DECIDINDO SE SERA FEITA UM INSERT OU UPDATE
        if (isset($dados['pes_codigo'])) {
            $tmpTblPessoas = $tmpTblPessoas->buscar(array("pes_codigo = ?" => $dados['pes_codigo']))->current();
        } else {
            $tmpTblPessoas = $tmpTblPessoas->createRow();
        }

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['pes_tipo'])) {
            $tmpTblPessoas->pes_tipo = $dados['pes_tipo'];
        }
        if (isset($dados['pes_esfera'])) {
            $tmpTblPessoas->pes_esfera = $dados['pes_esfera'];
        }
        if (isset($dados['pes_administracao'])) {
            $tmpTblPessoas->pes_administracao = $dados['pes_administracao'];
        }
        if (isset($dados['pes_utilidade_publica'])) {
            $tmpTblPessoas->pes_utilidade_publica = $dados['pes_utilidade_publica'];
        }
        if (isset($dados['pes_superior'])) {
            $tmpTblPessoas->pes_superior = $dados['pes_superior'];
        }
        if (isset($dados['pes_validade'])) {
            $tmpTblPessoas->pes_validade = $dados['pes_validade'];
        }
        if (isset($dados['pes_orgao_cadastrador'])) {
            $tmpTblPessoas->pes_orgao_cadastrador = $dados['pes_orgao_cadastrador'];
        }
        if (isset($dados['pes_usuario_cadastrador'])) {
            $tmpTblPessoas->pes_usuario_cadastrador = $dados['pes_usuario_cadastrador'];
        }
        if (isset($dados['pes_data_cadastramento'])) {
            $tmpTblPessoas->pes_data_cadastramento = $dados['pes_data_cadastramento'];
        }
        if (isset($dados['pes_orgao_atualizador'])) {
            $tmpTblPessoas->pes_orgao_atualizador = $dados['pes_orgao_atualizador'];
        }
        if (isset($dados['pes_usuario_atualizador'])) {
            $tmpTblPessoas->pes_usuario_atualizador = $dados['pes_usuario_atualizador'];
        }
        if (isset($dados['pes_data_atualizacao'])) {
            $tmpTblPessoas->pes_data_atualizacao = $dados['pes_data_atualizacao'];
        }
        if (isset($dados['pes_controle'])) {
            $tmpTblPessoas->pes_controle = $dados['pes_controle'];
        }

        $id = $tmpTblPessoas->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    public function salvarDados($dados)
    {

        
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblPessoas = new Pessoas();

        $tmpTblPessoas = $tmpTblPessoas->createRow();

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['pes_codigo'])) {
            $tmpTblPessoas->pes_codigo = $dados['pes_codigo'];
        }
        if (isset($dados['pes_categoria'])) {
            $tmpTblPessoas->pes_categoria = $dados['pes_categoria'];
        }
        if (isset($dados['pes_tipo'])) {
            $tmpTblPessoas->pes_tipo = $dados['pes_tipo'];
        }
        if (isset($dados['pes_esfera'])) {
            $tmpTblPessoas->pes_esfera = $dados['pes_esfera'];
        }
        if (isset($dados['pes_administracao'])) {
            $tmpTblPessoas->pes_administracao = $dados['pes_administracao'];
        }
        if (isset($dados['pes_utilidade_publica'])) {
            $tmpTblPessoas->pes_utilidade_publica = $dados['pes_utilidade_publica'];
        }
        if (isset($dados['pes_superior'])) {
            $tmpTblPessoas->pes_superior = $dados['pes_superior'];
        }
        if (isset($dados['pes_validade'])) {
            $tmpTblPessoas->pes_validade = $dados['pes_validade'];
        }
        if (isset($dados['pes_orgao_cadastrador'])) {
            $tmpTblPessoas->pes_orgao_cadastrador = $dados['pes_orgao_cadastrador'];
        }
        if (isset($dados['pes_usuario_cadastrador'])) {
            $tmpTblPessoas->pes_usuario_cadastrador = $dados['pes_usuario_cadastrador'];
        }
        if (isset($dados['pes_data_cadastramento'])) {
            $tmpTblPessoas->pes_data_cadastramento = $dados['pes_data_cadastramento'];
        }
        if (isset($dados['pes_orgao_atualizador'])) {
            $tmpTblPessoas->pes_orgao_atualizador = $dados['pes_orgao_atualizador'];
        }
        if (isset($dados['pes_usuario_atualizador'])) {
            $tmpTblPessoas->pes_usuario_atualizador = $dados['pes_usuario_atualizador'];
        }
        if (isset($dados['pes_data_atualizacao'])) {
            $tmpTblPessoas->pes_data_atualizacao = $dados['pes_data_atualizacao'];
        }
        if (isset($dados['pes_controle'])) {
            $tmpTblPessoas->pes_controle = $dados['pes_controle'];
        }

        $id = $tmpTblPessoas->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }
}
