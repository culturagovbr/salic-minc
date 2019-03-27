<?php

class paVoltarProjetoFinalizadoComponente extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name  = 'paVoltarProjetoFinalizadoComponente';

    public function execSP($pronac)
    {
        try {
            $rodar = "exec " . $this->_schema .".". $this->_name . ' ' . $pronac;
            return  $this->getAdapter()->query($rodar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
