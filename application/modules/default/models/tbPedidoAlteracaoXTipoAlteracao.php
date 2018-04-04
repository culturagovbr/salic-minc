<?php
class tbPedidoAlteracaoXTipoAlteracao extends MinC_Db_Table_Abstract
{
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scSAC";
    protected $_name    = "tbPedidoAlteracaoXTipoAlteracao";

    /**
     * Busca as justificativas dos pedidos de readequa��o
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function buscarPedido($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('x' => $this->_name),
            array('x.idPedidoAlteracaoXTipoAlteracao AS idTipoAlteracao'
                ,'x.idPedidoAlteracao'
                ,'x.tpAlteracaoProjeto'
                ,new Zend_Db_Expr('CAST(x.dsJustificativa AS TEXT) AS dsJustificativa')
                ,'x.stVerificacao AS stVerificacaoTipo')
        );
        $select->joinInner(
            array('p' => 'tbPedidoAlteracaoProjeto'),
            'p.idPedidoAlteracao = x.idPedidoAlteracao',
            array('p.IdPRONAC'
                ,'p.idSolicitante'
                ,new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 103) AS dtSolicitacao')
                ,new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 108) AS hrSolicitacao')
                ,'p.stPedidoAlteracao'
                ,'p.siVerificacao'),
            'BDCORPORATIVO.scSAC'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    }

    /**
     * Busca os pedidos de readequa��o do checklist
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function buscarPedidoChecklist($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('x' => $this->_name),
            array('x.idPedidoAlteracaoXTipoAlteracao AS idTipoAlteracao'
                ,'x.idPedidoAlteracao'
                ,'x.tpAlteracaoProjeto'
                ,new Zend_Db_Expr('CAST(x.dsJustificativa AS TEXT) AS dsJustificativa')
                ,'x.stVerificacao AS stVerificacaoTipo')
        );
        $select->joinInner(
            array('p' => 'tbPedidoAlteracaoProjeto'),
            'p.idPedidoAlteracao = x.idPedidoAlteracao',
            array('p.IdPRONAC'
                ,'p.idSolicitante'
                ,new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 103) AS dtSolicitacao')
                ,new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 108) AS hrSolicitacao')
                ,'p.stPedidoAlteracao'
                ,'p.siVerificacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('a' => 'tbAvaliacaoItemPedidoAlteracao'),
            'a.idPedidoAlteracao = x.idPedidoAlteracao AND a.tpAlteracaoProjeto = x.tpAlteracaoProjeto',
            array(),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('c' => 'tbAcaoAvaliacaoItemPedidoAlteracao'),
            'c.idAvaliacaoItemPedidoAlteracao = a.idAvaliacaoItemPedidoAlteracao',
            array('c.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao'),
            'BDCORPORATIVO.scSAC'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    }
}
