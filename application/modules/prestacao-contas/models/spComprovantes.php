<?php

class PrestacaoContas_Model_spComprovantes extends MinC_Db_Table_Abstract
{
    protected $_name = 'spComprovantesASeremAvaliados';
    protected $_schema = 'sac';
    /* protected $_primary = 'IdPRONAC'; */
    public function exec($idPronac, $perc) 
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "exec sac.dbo.spComprovantesASeremAvalidados ". $idPronac. ',' . $perc;
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
