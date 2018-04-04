<?php

class Assinatura_Model_TbAtoAdministrativo extends MinC_Db_Model
{
    protected $_idAtoAdministrativo;
    protected $_idTipoDoAto;
    protected $_idCargoDoAssinante;
    protected $_idOrgaoDoAssinante;
    protected $_idPerfilDoAssinante;
    protected $_idOrdemDaAssinatura;
    protected $_stEstado;
    protected $_idOrgaoSuperiorDoAssinante;

    const ST_ESTADO_ATIVO = 1;
    const ST_ESTADO_INATIVO = 0;

    /**
     * @return mixed
     */
    public function getStEstado()
    {
        return $this->_stEstado;
    }

    /**
     * @param mixed $stEstado
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdOrgaoSuperiorDoAssinante()
    {
        return $this->_idOrgaoSuperiorDoAssinante;
    }

    /**
     * @param mixed $idOrgaoSuperiorDoAssinante
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setIdOrgaoSuperiorDoAssinante($idOrgaoSuperiorDoAssinante)
    {
        $this->_idOrgaoSuperiorDoAssinante = $idOrgaoSuperiorDoAssinante;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdAtoAdministrativo()
    {
        return $this->_idAtoAdministrativo;
    }

    /**
     * @param mixed $idAtoAdministrativo
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setIdAtoAdministrativo($idAtoAdministrativo)
    {
        $this->_idAtoAdministrativo = $idAtoAdministrativo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdTipoDoAto()
    {
        return $this->_idTipoDoAto;
    }

    /**
     * @param mixed $idTipoDoAto
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setIdTipoDoAto($idTipoDoAto)
    {
        $this->_idTipoDoAto = $idTipoDoAto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdCargoDoAssinante()
    {
        return $this->_idCargoDoAssinante;
    }

    /**
     * @param mixed $idCargoDoAssinante
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setIdCargoDoAssinante($idCargoDoAssinante)
    {
        $this->_idCargoDoAssinante = $idCargoDoAssinante;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdOrgaoDoAssinante()
    {
        return $this->_idOrgaoDoAssinante;
    }

    /**
     * @param mixed $idOrgaoDoAssinante
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setIdOrgaoDoAssinante($idOrgaoDoAssinante)
    {
        $this->_idOrgaoDoAssinante = $idOrgaoDoAssinante;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPerfilDoAssinante()
    {
        return $this->_idPerfilDoAssinante;
    }

    /**
     * @param mixed $idPerfilDoAssinante
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setIdPerfilDoAssinante($idPerfilDoAssinante)
    {
        $this->_idPerfilDoAssinante = $idPerfilDoAssinante;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdOrdemDaAssinatura()
    {
        return $this->_idOrdemDaAssinatura;
    }

    /**
     * @param mixed $idOrdemDaAssinatura
     * @return Assinatura_Model_TbAtoAdministrativo
     */
    public function setIdOrdemDaAssinatura($idOrdemDaAssinatura)
    {
        $this->_idOrdemDaAssinatura = $idOrdemDaAssinatura;
        return $this;
    }
}
