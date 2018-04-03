<?php

class Analise_Model_TbAvaliarAdequacaoProjeto extends MinC_Db_Model
{
    protected $_idAvaliarAdequacaoProjeto;
    protected $_idPronac;
    protected $_dtEncaminhamento;
    protected $_idTecnico;
    protected $_dtAvaliacao;
    protected $_dsAvaliacao;
    protected $_siEncaminhamento;
    protected $_stAvaliacao;
    protected $_stEstado;

    /**
     * @return mixed
     */
    public function getIdAvaliarAdequacaoProjeto()
    {
        return $this->_idAvaliarAdequacaoProjeto;
    }

    /**
     * @param mixed $idAvaliarAdequacaoProjeto
     */
    public function setIdAvaliarAdequacaoProjeto($idAvaliarAdequacaoProjeto)
    {
        $this->_idAvaliarAdequacaoProjeto = $idAvaliarAdequacaoProjeto;
    }

    /**
     * @return mixed
     */
    public function getIdPronac()
    {
        return $this->_idPronac;
    }

    /**
     * @param mixed $idPronac
     */
    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
    }

    /**
     * @return mixed
     */
    public function getDtEncaminhamento()
    {
        return $this->_dtEncaminhamento;
    }

    /**
     * @param mixed $dtEncaminhamento
     */
    public function setDtEncaminhamento($dtEncaminhamento)
    {
        $this->_dtEncaminhamento = $dtEncaminhamento;
    }

    /**
     * @return mixed
     */
    public function getIdTecnico()
    {
        return $this->_idTecnico;
    }

    /**
     * @param mixed $idTecnico
     */
    public function setIdTecnico($idTecnico)
    {
        $this->_idTecnico = $idTecnico;
    }

    /**
     * @return mixed
     */
    public function getDtAvaliacao()
    {
        return $this->_dtAvaliacao;
    }

    /**
     * @param mixed $dtAvaliacao
     */
    public function setDtAvaliacao($dtAvaliacao)
    {
        $this->_dtAvaliacao = $dtAvaliacao;
    }

    /**
     * @return mixed
     */
    public function getDsAvaliacao()
    {
        return $this->_dsAvaliacao;
    }

    /**
     * @param mixed $dsAvaliacao
     */
    public function setDsAvaliacao($dsAvaliacao)
    {
        $this->_dsAvaliacao = $dsAvaliacao;
    }

    /**
     * @return mixed
     */
    public function getSiEncaminhamento()
    {
        return $this->_siEncaminhamento;
    }

    /**
     * @param mixed $siEncaminhamento
     */
    public function setSiEncaminhamento($siEncaminhamento)
    {
        $this->_siEncaminhamento = $siEncaminhamento;
    }

    /**
     * @return mixed
     */
    public function getStAvaliacao()
    {
        return $this->_stAvaliacao;
    }

    /**
     * @param mixed $stAvaliacao
     */
    public function setStAvaliacao($stAvaliacao)
    {
        $this->_stAvaliacao = $stAvaliacao;
    }

    /**
     * @return mixed
     */
    public function getStEstado()
    {
        return $this->_stEstado;
    }

    /**
     * @param mixed $stEstado
     */
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
    }
}
