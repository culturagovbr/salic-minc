<?php
class spTipoDeReadequacaoOrcamentaria extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'spTipoDeReadequacaoOrcamentaria';

    /**
     * @param $idPronac
     * @return array
     */
    public function exec($idPronac)
    {
        $sql = "exec ".$this->_banco.".".$this->_name." $idPronac";

        //Foi realizado uma alteracao na Store Procedure pra trazer mais valores - 26/02/2016
        return $this->getAdapter()->fetchAll($sql);
        #return $this->getAdapter()->fetchOne($sql);
    }
}
