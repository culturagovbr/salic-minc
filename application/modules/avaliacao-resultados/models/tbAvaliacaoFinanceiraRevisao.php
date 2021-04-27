<?php

class AvaliacaoResultados_Model_tbAvaliacaoFinanceiraRevisao extends MinC_Db_Model
{

    protected $_idAvaliacaoFinanceiraRevisao;
    protected $_idAvaliacaoFinanceira;
    protected $_idAgente;
    protected $_idGrupoAtivo;
    protected $_dtRevisao;
    protected $_dtAtualizacao;
    protected $_siStatus;
    protected $_dsRevisao;

    /**
     * @return mixed
     */
    public function getIdAvaliacaoFinanceiraRevisao()
    {
        return $this->_idAvaliacaoFinanceiraRevisao;
    }

    /**
     * @param mixed $idAvaliacaoFinanceiraRevisao
     */
    public function setIdAvaliacaoFinanceiraRevisao($idAvaliacaoFinanceiraRevisao)
    {
        $this->_idAvaliacaoFinanceiraRevisao = $idAvaliacaoFinanceiraRevisao;
    }

    /**
     * @return mixed
     */
    public function getIdAvaliacaoFinanceira()
    {
        return $this->_idAvaliacaoFinanceira;
    }

    /**
     * @param mixed $idAvaliacaoFinanceira
     */
    public function setIdAvaliacaoFinanceira($idAvaliacaoFinanceira)
    {
        $this->_idAvaliacaoFinanceira = $idAvaliacaoFinanceira;
    }

    /**
     * @return mixed
     */
    public function getIdAgente()
    {
        return $this->_idAgente;
    }

    /**
     * @param mixed $idAgente
     */
    public function setIdAgente($idAgente)
    {
        $this->_idAgente = $idAgente;
    }

    /**
     * @return mixed
     */
    public function getIdGrupoAtivo()
    {
        return $this->_idGrupoAtivo;
    }

    /**
     * @param mixed $idGrupoAtivo
     */
    public function setIdGrupoAtivo($idGrupoAtivo)
    {
        $this->_idGrupoAtivo = $idGrupoAtivo;
    }

    /**
     * @return mixed
     */
    public function getDtRevisao()
    {
        return $this->_dtRevisao;
    }

    /**
     * @param mixed $dtRevisao
     */
    public function setDtRevisao($dtRevisao)
    {
        $this->_dtRevisao = $dtRevisao;
    }

    /**
     * @return mixed
     */
    public function getSiStatus()
    {
        return $this->_siStatus;
    }

    /**
     * @param mixed $siStatus
     */
    public function setSiStatus($siStatus)
    {
        $this->_siStatus = $siStatus;
    }

    /**
     * @return mixed
     */
    public function getDsRevisao()
    {
        return $this->_dsRevisao;
    }

    /**
     * @param mixed $dsRevisao
     */
    public function setDsRevisao($dsRevisao)
    {
        $this->_dsRevisao = $dsRevisao;
    }

    /**
     * @return mixed
     */
    public function getDtAtualizacao()
    {
        return $this->_dtAtualizacao;
    }

    /**
     * @param mixed $dtAtualizacao
     */
    public function setDtAtualizacao($dtAtualizacao)
    {
        $this->_dtAtualizacao = $dtAtualizacao;
    }
}

