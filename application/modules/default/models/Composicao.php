<?php
class Composicao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbComposicao';

    public function buscarComposicao($where = array(), $order = array(), $dbg = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        $select->order($order);

        if ($dbg) {
            xd($select->assemble());
        }

        return $this->fetchAll($select);
    }
}
