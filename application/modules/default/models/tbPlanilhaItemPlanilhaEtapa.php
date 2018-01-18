<?php

class tbPlanilhaItemPlanilhaEtapa extends MinC_Db_Table_Abstract
{
    protected $_banco   = "SAC";
    protected $_schema  = "dbo";
    protected $_name    = 'tbPlanilhaItemPlanilhaEtapa';

    
    public function buscarEtapaItensPorPlanilhaOrc($idPlanOrcamentaria)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->where('idPlanOrcEdital = ?', $idPlanOrcamentaria);
        return $this->fetchAll($slct);
    }
    
    public function salvarPlanilhaEtapaPlanilhaItem($dadosPlanilhaItemPlanilhaEtapa)
    {
        $this->insert($dadosPlanilhaItemPlanilhaEtapa);
        return $insert;
    }
}
