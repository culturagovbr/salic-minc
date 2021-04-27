<?php

class AvaliacaoResultados_Model_tbAvaliacaoFinanceira extends MinC_Db_Model
{
    protected $_idAvaliacaoFinanceira;
    protected $_idPronac;
    protected $_dtAvaliacaoFinanceira;
    protected $_tpAvaliacaoFinanceira;
    protected $_siManifestacao;
    protected $_dsParecer;
    protected $_idUsuario;

    /**
     * @return mixed
     */
    public function getIdAvaliacaoFinanceira()
    {
        return $this->_idAvaliacaoFinanceira;
    }

    /**
     * @param mixed $idAvaliacaoFinanceira
     * @return tbAvaliacaoFinanceira
     */
    public function setIdAvaliacaoFinanceira($idAvaliacaoFinanceira)
    {
        $this->_idAvaliacaoFinanceira = $idAvaliacaoFinanceira;
        return $this;
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
     * @return tbAvaliacaoFinanceira
     */
    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtAvaliacaoFinanceira()
    {
        return $this->_dtAvaliacaoFinanceira;
    }

    /**
     * @param mixed $dtAvaliacaoFinanceira
     * @return tbAvaliacaoFinanceira
     */
    public function setDtAvaliacaoFinanceira($dtAvaliacaoFinanceira)
    {
        $this->_dtAvaliacaoFinanceira = $dtAvaliacaoFinanceira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTpAvaliacaoFinanceira()
    {
        return $this->_tpAvaliacaoFinanceira;
    }

    /**
     * @param mixed $tpAvaliacaoFinanceira
     * @return tbAvaliacaoFinanceira
     */
    public function setTpAvaliacaoFinanceira($tpAvaliacaoFinanceira)
    {
        $this->_tpAvaliacaoFinanceira = $tpAvaliacaoFinanceira;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiManifestacao()
    {
        return $this->_siManifestacao;
    }

    /**
     * @param mixed $siManifestacao
     * @return tbAvaliacaoFinanceira
     */
    public function setSiManifestacao($siManifestacao)
    {
        $this->_siManifestacao = $siManifestacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDsParecer()
    {
        return $this->_dsParecer;
    }

    /**
     * @param mixed $dsParecer
     * @return tbAvaliacaoFinanceira
     */
    public function setDsParecer($dsParecer)
    {
        $this->_dsParecer = utf8_decode($dsParecer);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdUsuario()
    {
        return $this->_idUsuario;
    }

    /**
     * @param mixed $idUsuario
     * @return tbAvaliacaoFinanceira
     */
    public function setIdUsuario($idUsuario)
    {
        $this->_idUsuario = $idUsuario;
        return $this;
    }

}