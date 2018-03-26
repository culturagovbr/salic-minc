<?php
class sInformacaoReceitaFederalV3 extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "dbo";
    protected $_name    = "sInformacaoReceitaFederalV3";
    public function gerarDBF($ano)
    {
        try {
            $executar = new Zend_Db_Expr("EXEC " . $this->_banco . "." . $this->_schema . "." . $this->_name . " " . $ano);
            return $this->getAdapter()->query($executar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
