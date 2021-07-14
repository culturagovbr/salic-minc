<?php

class PrestacaoContas_Model_spComprovacaoFinanceiraProjeto extends MinC_Db_Table_Abstract
{
    protected $_name = 'spComprovacaoFinanceiraProjeto';
    protected $_schema = 'sac';

    public function exec($idPronac, $perc)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = 'exec sac.dbo.spComprovacaoFinanceiraProjeto '. $idPronac. ',' . $perc;
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
