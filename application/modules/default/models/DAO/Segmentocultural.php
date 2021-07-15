<?php

class Segmentocultural extends MinC_Db_Table_Abstract
{
    protected $_name = 'Segmento';
    protected $_schema = 'sac';
    protected $_primary = 'Codigo';

    public function buscarSegmento($idArea)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $viewVSegmento = $this->obterViewVSegmento(array(), true);

        $objSelect = $this->select();
        $objSelect->isUseSchema(false);
        $objSelect->setIntegrityCheck(false);

        $objSelect->from(
            array('vSegmento' => new Zend_Db_Expr("({$viewVSegmento})")),
            array(
                'id' => 'Codigo',
                'descricao' => 'Descricao',
                'tp_enquadramento'
            )
        );
        $objSelect->where("Area = ?", $idArea);

        return $db->fetchAll($objSelect);
    }

    /**
     * Este método representa a view SAC.dbo.vSegmento
     */
    public function obterViewVSegmento($where = array(), $isRetornarObjeto = false)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $querySegmentoCultural = $this->select();

        $querySegmentoCultural->setIntegrityCheck(false);
        $querySegmentoCultural->from('Segmento', array(
            new Zend_Db_Expr(
                "CASE WHEN substring(Codigo,1,1)='8' THEN '2'
                 ELSE substring(Codigo,1,1)
                  END as Area"
            ),
            'Codigo',
            'Descricao',
            'idOrgao',
            'tp_enquadramento'
        ), $this->getSchema("sac"));
        $querySegmentoCultural->where("stEstado = ?", 1);
        if (count($where) > 0) {
            foreach ($where as $condicao => $valor) {
                $querySegmentoCultural->where($condicao, $valor);
            }
        }

        if (!$isRetornarObjeto) {
            return $db->fetchAll($querySegmentoCultural);
        }
        return $querySegmentoCultural;
    }

    public function carregarSegmentosArea($idArea)
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            $viewVSegmento = $this->obterViewVSegmento(array(), true);

            $objSelect = $this->select();
            $objSelect->isUseSchema(false);
            $objSelect->setIntegrityCheck(false);

            $objSelect->from(
                array('vSegmento' => new Zend_Db_Expr("({$viewVSegmento})")),
                array(
                    'codigo' => 'Codigo',
                    'descricao' => 'Descricao',
                    'tp_enquadramento'
                )
            );
            if ($idArea) {
                $objSelect->where("Area = ?", $idArea);
            }
            $objSelect->order("order by 2 desc");

            return $db->fetchAll($objSelect);
        } catch (Zend_Exception_Db $e) {
            throw new Exception("Erro ao buscar Segmento Cultural: " . $e->getMessage());
        }
    }
}
