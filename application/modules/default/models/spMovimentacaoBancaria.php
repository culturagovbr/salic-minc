<?php
class spMovimentacaoBancaria extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_schema  = "sac";
    protected $_name    = "spMovimentacaoBancaria";

    /**
     * M�todo para executar a SP de movimenta��o banc�ria.
     * A mesma verifica se as inconsist�ncias foram corrigidas.
     * @access public
     * @param void
     * @return bool
     */
    public function verificarInconsistencias()
    {
        try {
            $executar = "EXEC {$this->_schema}.{$this->_name}";
            return $this->getAdapter()->query($executar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    } // fecha m�todo verificarInconsistencias()
}
