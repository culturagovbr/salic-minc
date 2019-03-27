<?php

class Assinatura_Model_TbMotivoDevolucao extends MinC_Db_Model
{
    protected $_idMotivoDevolucao;
    protected $_idDocumentoAssinatura;
    protected $_dtDevolucao;
    protected $_dsMotivoDevolucao;
    protected $_idUsuario;

    /**
     * @return mixed
     */
    public function getIdMotivoDevolucao()
    {
        return $this->_idMotivoDevolucao;
    }

    /**
     * @param mixed $idMotivoDevolucao
     */
    public function setIdMotivoDevolucao($idMotivoDevolucao)
    {
        $this->_idMotivoDevolucao = $idMotivoDevolucao;
    }

    /**
     * @return mixed
     */
    public function getIdDocumentoAssinatura()
    {
        return $this->_idDocumentoAssinatura;
    }

    /**
     * @param mixed $idDocumentoAssinatura
     */
    public function setIdDocumentoAssinatura($idDocumentoAssinatura)
    {
        $this->_idDocumentoAssinatura = $idDocumentoAssinatura;
    }

    /**
     * @return mixed
     */
    public function getDtDevolucao()
    {
        return $this->_dtDevolucao;
    }

    /**
     * @param mixed $dtDevolucao
     */
    public function setDtDevolucao($dtDevolucao)
    {
        $this->_dtDevolucao = $dtDevolucao;
    }

    /**
     * @return mixed
     */
    public function getDsMotivoDevolucao()
    {
        return $this->_dsMotivoDevolucao;
    }

    /**
     * @param mixed $dsMotivoDevolucao
     */
    public function setDsMotivoDevolucao($dsMotivoDevolucao)
    {
        $this->_dsMotivoDevolucao = $dsMotivoDevolucao;
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
