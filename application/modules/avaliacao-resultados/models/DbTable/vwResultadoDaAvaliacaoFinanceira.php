<?php

class AvaliacaoResultados_Model_DbTable_vwResultadoDaAvaliacaoFinanceira extends MinC_Db_Table_Abstract
{
    protected $_name = "vwResultadoDaAvaliacaoFinanceira";
    protected $_schema = "SAC";
    protected $_primary = "IdPronac";

    public function buscarConsolidacaoComprovantes($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        //@TODO REFATORAR AS FUNÇÕES
        $select->from(
            ['a' => 'Projetos'],
            [
                new Zend_Db_Expr('sac.dbo.fnQuantidadeComprovantesDePagamento( a.idPronac ) as qtTotalComprovante'),
                new Zend_Db_Expr('sac.dbo.fnQuantidadeComprovantesDePagamentoValidados( a.IdPronac ) as qtComprovantesValidadosProjeto'),
                new Zend_Db_Expr('sac.dbo.fnQuantidadeComprovantesDePagamentoRecusados( a.idPronac ) as qtComprovantesRecusadosProjeto'),
                new Zend_Db_Expr('sac.dbo.fnQuantidadeComprovantesDePagamentoNaoAvalidados( a.idPronac ) as qtComprovantesNaoAvaliados'),
                new Zend_Db_Expr('sac.dbo.fnVlComprovadoProjeto( a.IdPRONAC ) as vlComprovadoProjeto'),
                new Zend_Db_Expr('sac.dbo.fnVlComprovadoValidadoProjeto( a.IdPRONAC ) as vlComprovadoValidado'),
                new Zend_Db_Expr('sac.dbo.fnVlComprovadoRecusadoProjeto( a.IdPRONAC ) as vlComprovadoRecusado'),
                new Zend_Db_Expr('sac.dbo.fnTotalCaptadoProjeto( a.AnoProjeto,a.Sequencial ) - sac.dbo.fnVlComprovadoProjeto( a.IdPRONAC ) as vlNaoComprovado'),
            ], $this->_schema);

        $select->where('a.IdPronac = ?', $idPronac);

        return $this->fetchRow($select);
    }


}