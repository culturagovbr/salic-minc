<?php

class Projeto_Model_TbHomologacao extends MinC_Db_Model
{
    protected $_idHomologacao;
    protected $_idPronac;
    protected $_tpHomologacao;
    protected $_dtHomologacao;
    protected $_stDecisao;
    protected $_dsHomologacao;
    protected $_idUsuario;

    const ST_DECISAO_INDEFERIDO = 1;
    const ST_DECISAO_DEFERIDO = 2;

    const TP_HOMOLOGACAO_NORMAL = 1;
    const TP_HOMOLOGACAO_AD_REFEREDUM = 2;

    /**
     * @return mixed
     */
    public function getIdHomologacao()
    {
        return $this->_idHomologacao;
    }

    /**
     * @param mixed $idHomologacao
     * @return Admissibilidade_Model_TbHomologacao
     */
    public function setIdHomologacao($idHomologacao)
    {
        $this->_idHomologacao = $idHomologacao;
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
     * @return Admissibilidade_Model_TbHomologacao
     */
    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTpHomologacao()
    {
        return $this->_tpHomologacao;
    }

    /**
     * @param mixed $tpHomologacao
     * @return Admissibilidade_Model_TbHomologacao
     */
    public function setTpHomologacao($tpHomologacao)
    {
        $this->_tpHomologacao = $tpHomologacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtHomologacao()
    {
        return $this->_dtHomologacao;
    }

    /**
     * @param mixed $dtHomologacao
     * @return Admissibilidade_Model_TbHomologacao
     */
    public function setDtHomologacao($dtHomologacao)
    {
        $this->_dtHomologacao = $dtHomologacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStDecisao()
    {
        return $this->_stDecisao;
    }

    /**
     * @param mixed $stDecisao
     * @return Admissibilidade_Model_TbHomologacao
     */
    public function setStDecisao($stDecisao)
    {
        $this->_stDecisao = $stDecisao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDsHomologacao()
    {
        return $this->_dsHomologacao;
    }

    /**
     * @param mixed $dsHomologacao
     * @return Admissibilidade_Model_TbHomologacao
     */
    public function setDsHomologacao($dsHomologacao)
    {
        $this->_dsHomologacao = $dsHomologacao;
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
     * @return Admissibilidade_Model_TbHomologacao
     */
    public function setIdUsuario($idUsuario)
    {
        $this->_idUsuario = $idUsuario;
        return $this;
    }
}
