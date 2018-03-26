<?php
/**
 * DAO tbAlteracaoNomeProponente
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 11/04/2012
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2012 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */

class tbAlteracaoNomeProponente extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scSAC";
    protected $_name    = "tbAlteracaoNomeProponente";



    /**
     * Busca os pedidos de altera��o de Proponente (tpAlteracao igual a 1 e 2)
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
            array($this->_name),
            array(
                'nrCNPJCPF AS CNPJCPF'
                ,'nmProponente AS NomeProponente')
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    } // fecha m�todo buscarPedido()



    /**
     * Busca o hist�rico de readequa��o
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function historicoReadequacao($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('h' => $this->_name),
            array(
                'h.nrCNPJCPF AS CNPJCPF'
                ,'h.nmProponente AS NomeProponente')
        );
        $select->joinInner(
            array('p' => 'tbPedidoAlteracaoProjeto'),
            'p.idPedidoAlteracao = h.idPedidoAlteracao',
            array(
                'p.idPedidoAlteracao'
                ,'p.idSolicitante'
                ,new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 103) AS dtSolicitacao')
                ,new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 108) AS hrSolicitacao')),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('j' => 'tbPedidoAlteracaoXTipoAlteracao'),
            'p.idPedidoAlteracao = j.idPedidoAlteracao',
            array(
                new Zend_Db_Expr('CAST(j.dsJustificativa AS TEXT) AS dsProponente')
                ,'j.tpAlteracaoProjeto'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('a' => 'tbAvaliacaoItemPedidoAlteracao'),
            'p.idPedidoAlteracao = a.idPedidoAlteracao AND j.tpAlteracaoProjeto = a.tpAlteracaoProjeto ',
            array(
                'a.idAgenteAvaliador'
                ,'CONVERT(CHAR(10), a.dtInicioAvaliacao, 103) AS dtInicioAvaliacao'
                ,'CONVERT(CHAR(10), a.dtInicioAvaliacao, 108) AS hrInicioAvaliacao'
                ,'CONVERT(CHAR(10), a.dtFimAvaliacao, 103) AS dtFimAvaliacao'
                ,'CONVERT(CHAR(10), a.dtFimAvaliacao, 108) AS hrFimAvaliacao'
                ,'a.stAvaliacaoItemPedidoAlteracao AS stAvaliacao'
                ,'CAST(a.dsAvaliacao AS TEXT) AS dsAvaliacao'),
            'BDCORPORATIVO.scSAC'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()
} // fecha class
