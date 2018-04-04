<?php 
class tbModuloCategoria extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbModuloCategoria';
    
    public function inserirCategoria($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
