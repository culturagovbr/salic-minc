<?php

class AvaliacaoResultados_Model_tbEncaminhamentoPrestacaoContasMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('AvaliacaoResultados_Model_DbTable_tbEncaminhamentoPrestacaoContas');
    }

    public function save($model)
    {
        /* if ($this->isValid($model)) { */
            return parent::save($model);
        /* } */
        /* return false; */
    }

    public function update($data, $where)
    {
        $table = new AvaliacaoResultados_Model_DbTable_tbEncaminhamentoPrestacaoContas();
        return $table->update($data, $where);
    }


    public function isValid( $model)
    {
        $booStatus = true;
        $arrData = $model->toArray();
        $arrRequired = [
            'idPronac',
        ];

        foreach ($arrRequired as $strValue) {
            if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) {
                $this->setMessage('Campo obrigat&oacute;rio!', $strValue);
                $booStatus = false;
            }
        }

        return $booStatus;
    }
}