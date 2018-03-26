<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paVerificarAtualizarSituacaoAprovacao
 *
 * @author augusto
 */
class paVerificarAtualizarSituacaoAprovacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'paVerificarAtualizarSituacaoAprovacao';

    public function expaVerificarAtualizarSituacaoAprovacao($idpronac=null)
    {
        try {
            $rodar = "exec " . $this->_banco .".". $this->_name . ' ' . $idpronac;
            return  $this->getAdapter()->query($rodar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
