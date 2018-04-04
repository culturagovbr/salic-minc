<?php

class tbProcuracao extends MinC_Db_Table_Abstract
{
    protected $_banco = "AGENTES";
    protected $_schema = "AGENTES";
    protected $_name = "tbProcuracao";

    public function listarProcuracoesPendentes()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('idProcuracao', 'dtProcuracao', 'dsJustificativa')
        );
        $select->joinInner(
            array('b' => 'Agentes'),
            "a.idAgente = b.idAgente",
            array('idAgente'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('c' => 'Nomes'),
            "b.idAgente = c.idAgente",
            array('Descricao as Procurador'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('d' => 'tbDocumento'),
            "d.idDocumento = a.idDocumento",
            array('idArquivo'),
            'BDCORPORATIVO.scCorp'
        );

        $select->where('a.siProcuracao = ?', 0);
        return $this->fetchAll($select);
    }
}
