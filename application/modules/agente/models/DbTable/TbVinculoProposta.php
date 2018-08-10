<?php

class Agente_Model_DbTable_TbVinculoProposta extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbvinculoproposta';
    protected $_schema = 'AGENTES';
    protected $_primary = 'idVinculoProposta';

    /**
     * @param array $where
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarResponsaveisProponentes($where = array())
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);

        $slct->from(
            array('VP' => $this->_name),
            array('*')
        );

        $slct->joinInner(
            array('VI' => 'tbVinculo'),
            'VI.idVinculo = VP.idVinculo',
            array('*')
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }
}
