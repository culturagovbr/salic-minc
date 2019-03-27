<?php

class paVerificarAtualizarSituacaoAprovacao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'paVerificarAtualizarSituacaoAprovacao';

    public function expaVerificarAtualizarSituacaoAprovacao($idpronac=null)
    {
        try {
            $rodar = "exec " . $this->_schema .".". $this->_name . ' ' . $idpronac;
            return  $this->getAdapter()->query($rodar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
