<?php

class tbProcuradorProjeto extends MinC_Db_Table_Abstract
{
    protected $_banco = "AGENTES";
    protected $_schema = "AGENTES";
    protected $_name = "tbProcuradorProjeto";

    public function buscarProcuradorDoProjeto($idPronac)
    {
        $select = $this->select();
        $select->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(''),
            $this->_schema
        );

        $select->joinInner(
            array('b' => 'tbProcuracao'),
            'a.idProcuracao = b.idProcuracao',
            array(''),
            $this->_schema
        );

        $select->joinInner(
            array('c' => 'Agentes'),
            'b.idAgente = c.idAgente',
            array('c.CNPJCPF'),
            $this->_schema
        );

        $select->joinInner(
            array('d' => 'Nomes'),
            'c.idAgente = d.idAgente',
            array('d.Descricao as nome'),
            $this->_schema
        );

        $select->where("a.siEstado = ?", 2);
        $select->where("b.siProcuracao = ?", 1);
        $select->where("a.idPronac = ?", $idPronac);

        return $this->fetchAll($select);
    }

}


