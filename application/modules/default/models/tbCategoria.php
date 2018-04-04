<?php 
/*
 * Classe: tbCategoria
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class tbCategoria extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbCategoria';
   
    public function inserirCategoria($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
    
    public function atualizaCategoria($dado, $where)
    {
        $update = $this->update($dado, $where);
        return $update;
    }
}
