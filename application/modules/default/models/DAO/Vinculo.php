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
class Vinculo extends MinC_Db_Table_Abstract
{
    protected $_banco = "AGENTES";
    protected $_name = "tbVinculo";

    /**
    * Grava registro. Se seja passado um ID ele altera um registro existente
    * @param array $dados - array com dados referentes as colunas da tabela no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
    * @return ID do registro inserido/alterado ou FALSE em caso de erro
    */
    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tmpTblVinculo = new Vinculo();

        //DECIDINDO SE SERA FEITA UM INSERT OU UPDATE
        if (isset($dados['idVinculo'])) {
            $tmpRsVinculo = $tmpTblVinculo->find($dados['idVinculo'])->current();
        } else {
            $tmpRsVinculo = $tmpTblVinculo->createRow();
        }
        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        if (isset($dados['idAgenteResponsavel'])) {
            $tmpRsVinculo->idAgenteResponsavel = $dados['idAgenteResponsavel'];
        }
        if (isset($dados['idAgenteProponente'])) {
            $tmpRsVinculo->idAgenteProponente = $dados['idAgenteProponente'];
        }
        if (isset($dados['dtVinculo'])) {
            $tmpRsVinculo->dtVinculo = $dados['dtVinculo'];
        }
        if (isset($dados['dsEmailVinculo'])) {
            $tmpRsVinculo->dsEmailVinculo = $dados['dsEmailVinculo'];
        }
        if (isset($dados['stVinculo'])) {
            $tmpRsVinculo->stVinculo = $dados['stVinculo'];
        }
        if (isset($dados['tpVinculo'])) {
            $tmpRsVinculo->tpVinculo = $dados['tpVinculo'];
        }

        //echo "<pre>";
        //print_r($tmpRsVinculo);
        //SALVANDO O OBJETO CRIADO
        $id = $tmpRsVinculo->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }
}
