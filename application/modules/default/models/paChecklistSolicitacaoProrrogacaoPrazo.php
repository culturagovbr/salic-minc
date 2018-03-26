<?php
class paChecklistSolicitacaoProrrogacaoPrazo extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name  = 'paChecklistSolicitacaoProrrogacaoPrazo';

    public function checkSolicitacao($idPronac, $dtInicio, $dtFinal, $acao)
    {
        $sql = new Zend_Db_Expr("exec ".$this->_schema.'.'.$this->_name." $idPronac, '$dtInicio', '$dtFinal', '$acao' ");
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
