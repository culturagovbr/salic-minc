<?php
class spValidarDepositoIdentificado extends MinC_Db_Table_Abstract
{
    protected $_schema  = "SAC.dbo";
    protected $_name    = "spValidarDepositoIdentificado";

    /**
     * Método para executar a SP de movimentação bancária.
     * A mesma verifica se as inconsistências foram corrigidas.
     */
    public function verificarInconsistencias($idUsuarioLogado)
    {
        try {
            // executa a sp
            $executar = 'EXEC SAC.dbo.spvalidardepositoidentificado' . ' '.$idUsuarioLogado;
            return $this->getAdapter()->query($executar);
        } catch (Zend_Exception $e) {
            return $e->getMessage();
        }
    }
}
