<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of paVoltarProjetoFinalizadoComponente
 *
 * @author 01129075125
 */
class paVoltarProjetoFinalizadoComponente extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'paVoltarProjetoFinalizadoComponente';

    public function execSP($pronac)
    {
        try {
            $rodar = "exec " . $this->_banco .".". $this->_name . ' ' . $pronac;
            return  $this->getAdapter()->query($rodar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
