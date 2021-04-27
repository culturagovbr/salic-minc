<?php
class AvaliacaoResultados_Model_tbAvaliacaoFinanceiraRevisaoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('AvaliacaoResultados_Model_DbTable_tbAvaliacaoFinanceiraRevisao');
    }

    public function save($model)
    {
        if ($this->isValid($model)) {
            return parent::save($model);
        }
        return false;
    }

    public function isValid($model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
            'idAvaliacaoFinanceira',
            'idGrupoAtivo',
            'idAgente',
            'siStatus',
            'dsRevisao'
        ];

        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        if (!isset($arrData['idAvaliacaoFinanceiraRevisao']) && !isset($arrData['idAvaliacaoFinanceira'])) {
            $row = $this->getDbTable()->findBy([
                'idAvaliacaoFinanceira' => $arrData['idAvaliacaoFinanceira']
            ]);

            if (!empty($row)) {
                $this->setMessage('J&aacute; existe uma revis&atilde;o da avalia&ccedil;&atilde;o cadastrada nesse Projeto!', 'idPronac');
                $booStatus = false;
            }
        }
        return $booStatus;
    }
}
