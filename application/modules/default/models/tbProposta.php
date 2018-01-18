<?php
/**
 * DAO tbProposta
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 17/04/2012
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2012 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */

class tbProposta extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "tbProposta";



    /**
     * Busca os pedidos de readequa��o
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
            $this->_name,
            array('IdProposta'
                ,'tpProposta'
                ,'CONVERT(CHAR(10), dtProposta, 103) AS dtProposta'
                ,'CONVERT(CHAR(10), dtProposta, 108) AS hrProposta'
                ,'CAST(nmProjeto AS TEXT) AS nmProjeto'
                ,'cdMecanismo'
                ,'nrAgenciaBancaria'
                ,'stAreaAbrangencia'
                ,'CONVERT(CHAR(10), dtInicioExecucao, 103) AS dtInicioExecucao'
                ,'CONVERT(CHAR(10), dtInicioExecucao, 108) AS hrInicioExecucao'
                ,'CONVERT(CHAR(10), dtFimExecucao, 103) AS dtFimExecucao'
                ,'CONVERT(CHAR(10), dtFimExecucao, 108) AS hrFimExecucao'
                ,'nrAtoTombamento'
                ,'CONVERT(CHAR(10), dtAtoTombamento, 103) AS dtAtoTombamento'
                ,'CONVERT(CHAR(10), dtAtoTombamento, 108) AS hrAtoTombamento'
                ,'cdEsferaTombamento'
                ,'CAST(dsResumoProjeto AS TEXT) AS dsResumoProjeto'
                ,'CAST(dsObjetivos AS TEXT) AS dsObjetivos'
                ,'CAST(dsJustificativa AS TEXT) AS dsJustificativa'
                ,'CAST(dsAcessibilidade AS TEXT) AS dsAcessibilidade'
                ,'CAST(dsDemocratizacaoAcesso AS TEXT) AS dsDemocratizacaoAcesso'
                ,'CAST(dsEtapaTrabalho AS TEXT) AS dsEtapaTrabalho'
                ,'CAST(dsFichaTecnica AS TEXT) AS dsFichaTecnica'
                ,'CAST(dsSinopse AS TEXT) AS dsSinopse'
                ,'CAST(dsImpactoAmbiental AS TEXT) AS dsImpactoAmbiental'
                ,'CAST(dsEspecificacaoTecnica AS TEXT) AS dsEspecificacaoTecnica'
                ,'CAST(dsEstrategiaExecucao AS TEXT) AS dsEstrategiaExecucao'
                ,'CONVERT(CHAR(10), dtAceite, 103) AS dtAceite'
                ,'CONVERT(CHAR(10), dtAceite, 108) AS hrAceite'
                ,'CONVERT(CHAR(10), dtArquivamento, 103) AS dtArquivamento'
                ,'CONVERT(CHAR(10), dtArquivamento, 108) AS hrArquivamento'
                ,'stEstado'
                ,'stDataFixa'
                ,'stPlanoAnual'
                ,'stTipoDemanda'
                ,'idPedidoAlteracao'
            )
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
            array('h.IdProposta'
                ,'h.tpProposta'
                ,'CONVERT(CHAR(10), h.dtProposta, 103) AS dtProposta'
                ,'CONVERT(CHAR(10), h.dtProposta, 108) AS hrProposta'
                ,'CAST(h.nmProjeto AS TEXT) AS nmProjeto'
                ,'h.cdMecanismo'
                ,'h.nrAgenciaBancaria'
                ,'h.stAreaAbrangencia'
                ,'CONVERT(CHAR(10), h.dtInicioExecucao, 103) AS dtInicioExecucao'
                ,'CONVERT(CHAR(10), h.dtInicioExecucao, 108) AS hrInicioExecucao'
                ,'CONVERT(CHAR(10), h.dtFimExecucao, 103) AS dtFimExecucao'
                ,'CONVERT(CHAR(10), h.dtFimExecucao, 108) AS hrFimExecucao'
                ,'h.nrAtoTombamento'
                ,'CONVERT(CHAR(10), h.dtAtoTombamento, 103) AS dtAtoTombamento'
                ,'CONVERT(CHAR(10), h.dtAtoTombamento, 108) AS hrAtoTombamento'
                ,'h.cdEsferaTombamento'
                ,'CAST(h.dsResumoProjeto AS TEXT) AS dsResumoProjeto'
                ,'CAST(h.dsObjetivos AS TEXT) AS dsObjetivos'
                ,'CAST(h.dsJustificativa AS TEXT) AS dsJustificativa'
                ,'CAST(h.dsAcessibilidade AS TEXT) AS dsAcessibilidade'
                ,'CAST(h.dsDemocratizacaoAcesso AS TEXT) AS dsDemocratizacaoAcesso'
                ,'CAST(h.dsEtapaTrabalho AS TEXT) AS dsEtapaTrabalho'
                ,'CAST(h.dsFichaTecnica AS TEXT) AS dsFichaTecnica'
                ,'CAST(h.dsSinopse AS TEXT) AS dsSinopse'
                ,'CAST(h.dsImpactoAmbiental AS TEXT) AS dsImpactoAmbiental'
                ,'CAST(h.dsEspecificacaoTecnica AS TEXT) AS dsEspecificacaoTecnica'
                ,'CAST(h.dsEstrategiaExecucao AS TEXT) AS dsEstrategiaExecucao'
                ,'CONVERT(CHAR(10), h.dtAceite, 103) AS dtAceite'
                ,'CONVERT(CHAR(10), h.dtAceite, 108) AS hrAceite'
                ,'CONVERT(CHAR(10), h.dtArquivamento, 103) AS dtArquivamento'
                ,'CONVERT(CHAR(10), h.dtArquivamento, 108) AS hrArquivamento'
                ,'h.stEstado'
                ,'h.stDataFixa'
                ,'h.stPlanoAnual'
                ,'h.stTipoDemanda'
                ,'h.idPedidoAlteracao')
        );
        $select->joinInner(
            array('p' => 'tbPedidoAlteracaoProjeto'),
            'p.idPedidoAlteracao = h.idPedidoAlteracao',
            array(
                'p.idPedidoAlteracao'
                ,'p.idSolicitante'
                ,'CONVERT(CHAR(10), p.dtSolicitacao, 103) AS dtSolicitacao'
                ,'CONVERT(CHAR(10), p.dtSolicitacao, 108) AS hrSolicitacao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('j' => 'tbPedidoAlteracaoXTipoAlteracao'),
            'p.idPedidoAlteracao = j.idPedidoAlteracao',
            array(
                'CAST(j.dsJustificativa AS TEXT) AS dsProponente'
                ,'j.tpAlteracaoProjeto'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('a' => 'tbAvaliacaoItemPedidoAlteracao'),
            'p.idPedidoAlteracao = a.idPedidoAlteracao AND j.tpAlteracaoProjeto = a.tpAlteracaoProjeto',
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



    public function finalizarReadequacaoDeProposta($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('a' => $this->_name),
            array('dsObjetivos AS Solicitacao'
                ,'dsJustificativa AS Justificativa','dsEstrategiaExecucao'
                                ,'dsEspecificacaoTecnica as dsEspecificacaoSolicitacao')
        );
        $select->joinInner(
            array('b' => 'tbPedidoAlteracaoProjeto'),
            'b.idPedidoAlteracao = a.idPedidoAlteracao',
            array('IdPRONAC'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('c' => 'Projetos'),
            'c.IdPRONAC = b.IdPRONAC',
            array('NomeProjeto AS NomeProjeto','CgcCpf AS CNPJCPF'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('d' => 'PreProjeto'),
            'd.idPreProjeto = c.idProjeto',
            array('EstrategiadeExecucao as EstrategiadeExecucao','EspecificacaoTecnica as EspecificacaoTecnica'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('e' => 'Agentes'),
            'e.idAgente = d.idAgente',
            array(),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('f' => 'vProponenteProjetos'),
            'c.CgcCpf = f.CgcCpf',
            array('Nome AS proponente','CgcCpf'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('g' => 'tbPedidoAlteracaoXTipoAlteracao'),
            'g.idPedidoAlteracao = a.idPedidoAlteracao',
            array('dsJustificativa as dsJustificativaSolicitacao'),
            'BDCORPORATIVO.scSAC'
        );

        $select->where('b.IdPRONAC = ?', $idPronac);
        $select->where('g.tpAlteracaoProjeto = ?', 6);
        $select->where('g.stVerificacao = ?', 2);
        xd($select->assemble());

        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()
} // fecha class
