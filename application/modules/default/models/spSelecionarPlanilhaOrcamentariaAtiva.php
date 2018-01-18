<?php

/**
 * Description of spSelecionarPlanilhaOrcamentariaAtiva
 * Criado em 26/01/2016 - Fernao Lara
 */
class spSelecionarPlanilhaOrcamentariaAtiva extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'spSelecionarPlanilhaOrcamentariaAtiva';

    public function exec($idPronac)
    {
        $sql = "exec ".$this->_banco.".".$this->_name." $idPronac";
        return $this->getAdapter()->fetchOne($sql);
    }
}
