<?php
class Agente_Model_DbTable_Verificacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'agentes';
    protected $_schema = 'agentes';
    protected $_name = 'verificacao';

    const PROPOSTA_PARA_ANALISE_INICIAL = 96;
    const PROPOSTA_EM_CONFORMIDADE_VISUAL_OU_ANÁLISE_DOCUMENTAL = 97;
    const PROPOSTA_EM_ANALISE_FINAL = 128;

    public function combosNatureza($idTipo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name), 
            array('idVerificacao', 'Descricao')
        );
        $select->where('idTipo = ?', $idTipo);
        return $this->fetchAll($select);
    }
}
