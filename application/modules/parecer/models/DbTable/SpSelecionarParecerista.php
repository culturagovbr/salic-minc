<?php

class Parecer_Model_DbTable_SpSelecionarParecerista extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name  = 'spSelecionarParecerista';

    /**
     * exec
     *
     * @param mixed $idOrgao
     * @param mixed $idArea
     * @param mixed $idSegmento
     * @param mixed $vlProduto
     * @access public
     * @return void
     */
    public function exec($idOrgao, $idArea, $idSegmento, $vlProduto, $fechMode = Zend_DB :: FETCH_OBJ)
    {
        if (empty($vlProduto)) {
            $vlProduto = 0;
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "exec ".$this->_schema.".".$this->_name." $idOrgao, '$idArea', '$idSegmento', $vlProduto";
        $db->setFetchMode($fechMode);
        return $db->fetchAll($sql);
    }

    public function obterPareceristas($idOrgao, $idArea, $idSegmento, $valor)
    {
        return $this->exec(
            $idOrgao,
            $idArea,
            $idSegmento,
            $valor,
            \Zend_DB::FETCH_ASSOC
        );
    }
}
