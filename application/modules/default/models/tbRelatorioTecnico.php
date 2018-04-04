<?php

class tbRelatorioTecnico extends MinC_Db_Table_Abstract
{
    protected $_name   = 'tbRelatorioTecnico';
    protected $_schema = 'SAC';
    protected $_banco  = 'SAC';

    public function insertParecerTecnico($data)
    {
        $insertparecertecnico = $this->insert($data);
        return $insertparecertecnico;
    }
}
