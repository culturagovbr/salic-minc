<?php

class AvaliacaoResultados_Model_FluxosMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('AvaliacaoResultados_Model_DbTable_Fluxos');
    }

    public function save($model)
    {
        if ($this->isValid($model)) {
            return parent::save($model);
        }
        return false;
    }

    public function isValid( $model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
            'idPronac',
            'dtAvaliacaoFinanceira',
            'tpAvaliacaoFinanceira',
            'siManifestacao',
            'dsParecer',
            'idUsuario'
        ];

        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        if (!isset($arrData['idAvaliacaoFinanceira']) && isset($arrData['idPronac'])) {
            $row = $this->getDbTable()->findBy([
                'idPronac' => $arrData['idPronac']
            ]);

            if(!empty($row)){
                $this->setMessage('J&aacute; existe uma avalia&ccedil;&atilde;o cadastrada nesse Projeto!', 'idPronac');
                $booStatus = false;
            }
        }

        return $booStatus;
    }
}