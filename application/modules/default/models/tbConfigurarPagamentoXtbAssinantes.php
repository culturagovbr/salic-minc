<?php
/**
 * Description of tbConfigurarPagamentoXtbAssinantes
 *
 * @author Tarcisio Angelo
 */

class tbConfigurarPagamentoXtbAssinantes extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbConfigurarPagamentoXtbAssinantes';
    protected $_primary = 'idAssinantes';

    public function assinantesConfigurados($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
                        array('a.nrOrdenacao')
        );

        $select->joinInner(
            array('ass'=>'tbAssinantes'),
            "a.idAssinantes = ass.idAssinantes",
                            array('ass.idAssinantes'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('ag'=>'Agentes'),
            "ass.idAgente = ag.idAgente",
                            array('ag.idAgente'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('nm'=>'Nomes'),
            "ag.idAgente = nm.idAgente",
                            array('nm.Descricao as Nome'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('v'=>'Verificacao'),
            "ass.idCargo = v.idVerificacao",
                            array('v.idVerificacao as idCargo','v.Descricao as Cargo'),
            'AGENTES.dbo'
        );


        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('a.nrOrdenacao');



        return $this->fetchAll($select);
    }
}
