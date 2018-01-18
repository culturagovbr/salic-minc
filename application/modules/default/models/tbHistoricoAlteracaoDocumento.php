<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Vinculo
 *
 * @author tisomar
 */
class tbHistoricoAlteracaoDocumento extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_name = "tbHistoricoAlteracaoDocumento";

    /**
    * Grava registro. Se seja passado um ID ele altera um registro existente
    * @param array $dados - array com dados referentes as colunas da tabela no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
    * @return ID do registro inserido/alterado ou FALSE em caso de erro
    */
    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $historico = new tbHistoricoAlteracaoDocumento();
        
        //DECIDINDO SE SERA FEITA UM INSERT OU UPDATE
        //$historico = $historico->createRow();


        $insert = $historico->insert($dados); // cadastra
        return $insert;
    }
}
