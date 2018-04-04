<?php
class ConfigurarPagamentoParecerista extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbConfigurarPagamento';
    protected $_schema = 'SAC';
    protected $_banco = 'SAC';

    public function buscarConfiguracoes($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('c'=> $this->_name),
                        array('c.idConfigurarPagamento',
                                'c.nrDespachoInicial',
                                'c.nrDespachoFinal',
                                new Zend_Db_Expr('CONVERT(VARCHAR(10), c.dtConfiguracaoPagamento ,103) as dtConfiguracaoPagamento'),
                                'c.stEstado',
                                'c.idUsuario')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }



        return $this->fetchAll($select);
    }
}
