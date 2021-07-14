<?php

class Projeto_Model_vwPlanoDeDistribuicaoProduto extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name   = 'vwPlanoDeDistribuicaoProduto';
    protected $_primary = 'IdPRONAC';

    public function init()
    {
        parent::init();
    }

    public function obterProducaoProjeto(array $arrayWhere = array())
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $objQuery = $db->select();
        //$objQuery = $this->select();

        $objQuery->from(
            array('vwPlanoDeDistribuicaoProduto' => $this->_name),
            '*',
            $this->_schema
        );

        if (count($arrayWhere) > 0) {
            foreach ($arrayWhere as $condicao => $valor) {
                $objQuery->where($condicao, $valor);
            }
        }

        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        return $db->fetchAll($objQuery);
    }
}
