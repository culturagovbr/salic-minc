<?php

class AvaliacaoResultados_Model_FluxosProjetoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('AvaliacaoResultados_Model_DbTable_FluxosProjeto');
    }

    public function save($model)
    {
        /* $table = new AvaliacaoResultados_Model_DbTable_FluxosProjeto(); */

        /* var_dump($table->insert((array)$model)); */
        /* var_dump($model);die; */
        /* if ($this->isValid($model)) { */
            return parent::save($model);
        /* } */
        /* return false; */
    }

    public function find($where)
    {
        $table = new AvaliacaoResultados_Model_DbTable_FluxosProjeto();
        return $table->findBy($where);
    }

    /* public function isValid( $model) */
    /* { */
    /*     return true; */
    /*     $booStatus = true; */
    /*     $arrData = $model->toArray(); */
    /*     $arrRequired = [ */
    /*         'idPronac', */
    /*         'dtAvaliacaoFinanceira', */
    /*         'tpAvaliacaoFinanceira', */
    /*         'siManifestacao', */
    /*         'dsParecer', */
    /*         'idUsuario' */
    /*     ]; */

    /*     foreach ($arrRequired as $strValue) { */
    /*         if (!isset($arrData[$strValue]) || empty($arrData[$strValue])) { */
    /*             $this->setMessage('Campo obrigat&oacute;rio!', $strValue); */
    /*             $booStatus = false; */
    /*         } */
    /*     } */

    /*     if (!isset($arrData['idAvaliacaoFinanceira']) && isset($arrData['idPronac'])) { */
    /*         $row = $this->getDbTable()->findBy([ */
    /*             'idPronac' => $arrData['idPronac'] */
    /*         ]); */

    /*         if(!empty($row)){ */
    /*             $this->setMessage('J&aacute; existe uma avalia&ccedil;&atilde;o cadastrada nesse Projeto!', 'idPronac'); */
    /*             $booStatus = false; */
    /*         } */
    /*     } */

    /*     return $booStatus; */
    /* } */
}