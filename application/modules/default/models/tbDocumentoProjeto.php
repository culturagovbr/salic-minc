<?php
class tbDocumentoProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco  = "BDCORPORATIVO";
    protected $_schema = "BDCORPORATIVO.scCorp";
    protected $_name   = "tbDocumentoProjeto";

    public function excluir($where)
    {
        return $this->delete($where);
    }
}
