<?php
class Planilha_Model_DbTable_SpCalcularMedianaItemOrcamentario extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'spCalcularMedianaItemOrcamentario';

    public function obterMedianaItemOrcamento($idProduto, $idUnidade, $idPlanilhaItem, $idUFDespesa, $idMunicipioDespesa)
    {
        if (empty($idPlanilhaItem) or empty($idUnidade)) {
            return false;
        }

        $exec = new Zend_Db_Expr("EXEC {$this->_schema}.{$this->_name} {$idProduto}, {$idPlanilhaItem}, {$idUFDespesa}, {$idMunicipioDespesa}, {$idUnidade}");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }

        $resultado = $db->fetchRow($exec);

        return $resultado ? $resultado : [];
    }
}
