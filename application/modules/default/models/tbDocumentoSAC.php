<?php

class tbDocumentoSAC extends MinC_Db_Table_Abstract
{
    protected $_banco = "sac";
    protected $_schema = 'sac.dbo';
    protected $_name = "tbDocumento";

    public function excluir($where)
    {
        return $this->delete($where);
    }

}
