<?php
class Votante extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbVotante';
    protected $_schema = 'BDCORPORATIVO.scSAC';

    public function selecionarvotantes($idreuniao)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('tbv' => $this->_name),
            array( 'tbv.idAgente'),
            $this->_schema
        );
        $select->joinInner(
                array('nm' => 'nomes'),
                "nm.idagente = tbv.idagente",
                array('nm.descricao'),
                'agentes.dbo'
        );
        $select->where('tbv.idreuniao = ?', $idreuniao);
        $select->order('nm.descricao asc');

        return $this->fetchAll($select);
    }
}
