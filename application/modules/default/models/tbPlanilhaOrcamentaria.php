<?php

class tbPlanilhaOrcamentaria extends MinC_Db_Table_Abstract
{
    protected $_banco   = "SAC";
    protected $_schema  = "dbo";
    protected $_name    = 'tbPlanilhaOrcamentariaEdital';

    
    public function salvarPlanilhaOrcamentaria($dadosPlanilhaOrcamentaria)
    {
        $insert = $this->insert($dadosPlanilhaOrcamentaria);
        return $insert;
    }
}
