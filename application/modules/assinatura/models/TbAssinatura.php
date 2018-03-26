<?php

class Assinatura_Model_TbAssinatura extends MinC_Db_Model
{
    protected $_idAssintura;
    protected $_idPronac;
    protected $_idDocumento;
    protected $_dtAssintura;
    protected $_idAssinante;
    protected $_idOrgao;
    protected $_IdCargo;

    /**
     * @var int $_idAtoAdministrativo
     */
    protected $_idAtoAdministrativo;

    /**
     * @return int
     */
    public function getIdAtoAdministrativo()
    {
        return $this->_idAtoAdministrativo;
    }

    /**
     * @param int $idAtoAdministrativo
     * @return Assinatura_Model_TbAssinatura
     */
    public function setIdAtoAdministrativo($idAtoAdministrativo)
    {
        $this->_idAtoAdministrativo = $idAtoAdministrativo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdAssintura()
    {
        return $this->_idAssintura;
    }

    /**
     * @param mixed $idAssintura
     */
    public function setIdAssintura($idAssintura)
    {
        $this->_idAssintura = $idAssintura;
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
    public function getIdDocumento()
    {
        return $this->_idDocumento;
    }

    /**
     * @param mixed $idDocumento
     */
    public function setIdDocumento($idDocumento)
    {
        $this->_idDocumento = $idDocumento;
    }

    /**
     * @return mixed
     */
    public function getDtAssintura()
    {
        return $this->_dtAssintura;
    }

    /**
     * @param mixed $dtAssintura
     */
    public function setDtAssintura($dtAssintura)
    {
        $this->_dtAssintura = $dtAssintura;
    }

    /**
     * @return mixed
     */
    public function getIdAssinante()
    {
        return $this->_idAssinante;
    }

    /**
     * @param mixed $idAssinante
     */
    public function setIdAssinante($idAssinante)
    {
        $this->_idAssinante = $idAssinante;
    }

    /**
     * @return mixed
     */
    public function getIdOrgao()
    {
        return $this->_idOrgao;
    }

    /**
     * @param mixed $idOrgao
     */
    public function setIdOrgao($idOrgao)
    {
        $this->_idOrgao = $idOrgao;
    }

    /**
     * @return mixed
     */
    public function getIdCargo()
    {
        return $this->_IdCargo;
    }

    /**
     * @param mixed $IdCargo
     */
    public function setIdCargo($IdCargo)
    {
        $this->_IdCargo = $IdCargo;
    }
}
