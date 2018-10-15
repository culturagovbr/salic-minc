<?php

class AvaliacaoResultados_Model_DbTable_LaudoFinal extends MinC_Db_Table_Abstract
{
    protected $_name = "tbLaudoFinal";
    protected $_schema = "SAC";
    protected $_primary = "idLaudoFinal";

    public function all() {
        return $this->fetchAll();
    }

    public function laudoFinal($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(
            array('a' => $this->_name),
            array('*'),
            $this->_schema
        )
        ->where('idPronac = ? ', $id);

        return $db->fetchRow($select);
    }

    public function projetosLaudoFinal()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['p' => 'Projetos'],
            [
                'p.IdPronac', 
                'p.NomeProjeto', 
                'av.siManifestacao',
                new Zend_Db_Expr('p.AnoProjeto+p.Sequencial AS PRONAC') 
            ],
            'sac.dbo'
        )
        ->join(['doc'=>'tbDocumentoAssinatura'], 'p.IdPRONAC=doc.IdPRONAC', null, 'sac.dbo')
        ->join(['av'=>'tbAvaliacaoFinanceira'], 'av.idPronac=p.IdPRONAC', null, 'sac.dbo')
        ->where('doc.idTipoDoAtoAdministrativo = ?', Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_LAUDO_PRESTACAO_CONTAS)
        ->where('doc.cdSituacao = ?', Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA)
        ->where('doc.stEstado = ?', Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);
        return $this->fetchAll($select);
    }
}