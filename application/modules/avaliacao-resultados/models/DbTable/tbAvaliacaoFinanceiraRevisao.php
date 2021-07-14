<?php


class AvaliacaoResultados_Model_DbTable_tbAvaliacaoFinanceiraRevisao extends MinC_Db_Table_Abstract
{
    protected $_name   = 'tbAvaliacaoFinanceiraRevisao';
    protected $_schema = 'sac.dbo';
    protected $_primary = 'idAvaliacaoFinanceiraRevisao';

    public function findByAvaliacaoFinanceira($idAvaliacaoFinanceira)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            '*',
            $this->_schema
        );
        $select->where('idAvaliacaoFinanceira = ?',$idAvaliacaoFinanceira);

        return $this->fetchAll($select);
    }

    public function findOneRevisao($idAvaliacaoFinanceiraResumo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            '*',
            $this->_schema
        );
        $select->where('idAvaliacaoFinanceiraRevisao = ?',$idAvaliacaoFinanceiraResumo);

        return $this->fetchAll($select);
    }

    public function aprovacaoRevisao(){

    }
}
