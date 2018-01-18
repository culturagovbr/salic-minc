<?php

/**
 * Description of spSelecionarParecerista
 *
 */
class spSelecionarParecerista extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
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
     * @todo remover metodo e passar para Zend_DB
     */
    public function exec($idOrgao, $idArea, $idSegmento, $vlProduto)
    {
        if (empty($vlProduto)) {
            $vlProduto = 0;
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "exec ".$this->_banco.".".$this->_name." $idOrgao, '$idArea', '$idSegmento', $vlProduto";
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
