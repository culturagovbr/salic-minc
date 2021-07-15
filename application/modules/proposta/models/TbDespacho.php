<?php

/**
 * Class Proposta_Model_TbDeslocamento
 * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
 * @since
 */
class Proposta_Model_TbDespacho extends MinC_Db_Model
{
    protected $_idDespacho;
    protected $_idPronac;
    protected $_idProposta;
    protected $_Tipo;
    protected $_stEncaminhamento;
    protected $_Data;
    protected $_Despacho;
    protected $_stEstado;
    protected $_idUsuario;

    /**
     * @return mixed
     */
    public function getIdDespacho()
    {
        return $this->_idDespacho;
    }

    /**
     * @param mixed $idDespacho
     * @return Proposta_Model_TbDespacho
     */
    public function setIdDespacho($idDespacho)
    {
        $this->_idDespacho = $idDespacho;
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
     * @return Proposta_Model_TbDespacho
     */
    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdProposta()
    {
        return $this->_idProposta;
    }

    /**
     * @param mixed $idProposta
     * @return Proposta_Model_TbDespacho
     */
    public function setIdProposta($idProposta)
    {
        $this->_idProposta = $idProposta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipo()
    {
        return $this->_Tipo;
    }

    /**
     * @param mixed $Tipo
     * @return Proposta_Model_TbDespacho
     */
    public function setTipo($Tipo)
    {
        $this->_Tipo = $Tipo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStEncaminhamento()
    {
        return $this->_stEncaminhamento;
    }

    /**
     * @param mixed $stEncaminhamento
     * @return Proposta_Model_TbDespacho
     */
    public function setStEncaminhamento($stEncaminhamento)
    {
        $this->_stEncaminhamento = $stEncaminhamento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_Data;
    }

    /**
     * @param mixed $Data
     * @return Proposta_Model_TbDespacho
     */
    public function setData($Data)
    {
        $this->_Data = $Data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDespacho()
    {
        return $this->_Despacho;
    }

    /**
     * @param mixed $Despacho
     * @return Proposta_Model_TbDespacho
     */
    public function setDespacho($Despacho)
    {
        $this->_Despacho = $Despacho;
        return $this;
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
     * @return Proposta_Model_TbDespacho
     */
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
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
     * @return Proposta_Model_TbDespacho
     */
    public function setIdUsuario($idUsuario)
    {
        $this->_idUsuario = $idUsuario;
        return $this;
    }
}
