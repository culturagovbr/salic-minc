<?php

class Arquivo_Model_TbArquivo extends MinC_Db_Model
{
    protected $_idArquivo;
    protected $_nmArquivo;
    protected $_sgExtensao;
    protected $_nrTamanho;
    protected $_dtEnvio;
    protected $_dsHash;
    protected $_stAtivo;
    protected $_dsTipoPadronizado;
    protected $_idUsuario;

    /**
     * @return mixed
     */
    public function getIdArquivo()
    {
        return $this->_idArquivo;
    }

    /**
     * @param mixed $idArquivo
     */
    public function setIdArquivo($idArquivo)
    {
        $this->_idArquivo = $idArquivo;
    }

    /**
     * @return mixed
     */
    public function getNmArquivo()
    {
        return $this->_nmArquivo;
    }

    /**
     * @param mixed $nmArquivo
     */
    public function setNmArquivo($nmArquivo)
    {
        $this->_nmArquivo = $nmArquivo;
    }

    /**
     * @return mixed
     */
    public function getSgExtensao()
    {
        return $this->_sgExtensao;
    }

    /**
     * @param mixed $sgExtensao
     */
    public function setSgExtensao($sgExtensao)
    {
        $this->_sgExtensao = $sgExtensao;
    }

    /**
     * @return mixed
     */
    public function getNrTamanho()
    {
        return $this->_nrTamanho;
    }

    /**
     * @param mixed $nrTamanho
     */
    public function setNrTamanho($nrTamanho)
    {
        $this->_nrTamanho = $nrTamanho;
    }

    /**
     * @return mixed
     */
    public function getDtEnvio()
    {
        return $this->_dtEnvio;
    }

    /**
     * @param mixed $dtEnvio
     */
    public function setDtEnvio($dtEnvio)
    {
        $this->_dtEnvio = $dtEnvio;
    }

    /**
     * @return mixed
     */
    public function getDsHash()
    {
        return $this->_dsHash;
    }

    /**
     * @param mixed $dsHash
     */
    public function setDsHash($dsHash)
    {
        $this->_dsHash = $dsHash;
    }

    /**
     * @return mixed
     */
    public function getStAtivo()
    {
        return $this->_stAtivo;
    }

    /**
     * @param mixed $stAtivo
     */
    public function setStAtivo($stAtivo)
    {
        $this->_stAtivo = $stAtivo;
    }

    /**
     * @return mixed
     */
    public function getDsTipoPadronizado()
    {
        return $this->_dsTipoPadronizado;
    }

    /**
     * @param mixed $dsTipoPadronizado
     */
    public function setDsTipoPadronizado($dsTipoPadronizado)
    {
        $this->_dsTipoPadronizado = $dsTipoPadronizado;
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
     */
    public function setIdUsuario($idUsuario)
    {
        $this->_idUsuario = $idUsuario;
    }


}
