<?php

/**
 * Class Proposta_Model_DbTable_TbCustosVinculados
 *
 * @name Proposta_Model_DbTable_TbCustosVinculados
 * @package Modules/proposta
 * @subpackage Models/DbTable
 * @version $Id$
 *
 */
class Proposta_Model_DbTable_TbCustosVinculados extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbCustosVinculados';
    protected $_primary = 'idCustosVinculados';

    public function buscarCustosVinculados($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => $this->_name), '*', $this->_schema)
            ->joinInner(
                array('b' => 'tbPlanilhaItens'), 'a.idPlanilhaItem = b.idPlanilhaItens',
                array('Descricao as item'),
                $this->_schema
                )
        ;

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($order) {
            $select->order($order);
        }
        return $this->fetchAll($select);
    }

}
