<?php

class AvaliacaoResultados_Model_LaudoFinalMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('AvaliacaoResultados_Model_DbTable_LaudoFinal');
    }

    public function save($model)
    {
        // if ($this->isValid($model)) {
            // var_dump($model); die;
            var_dump( parent::save($model)); die;
        // }
        // return false;
    }

    public function isValid($model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
            'idLaudoFinal',
            'idPronac',
            'dtLaudoFinal',
            'siManifestacao',
            'dsLaudoFinal',
            'idUsuario'
        ];

        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        if (isset($arrData['idPronac'])) {
            $row = $this->getDbTable()->findBy([
                'idPronac' => $arrData['idPronac']
            ]);

        }

        return $booStatus;
    }
}