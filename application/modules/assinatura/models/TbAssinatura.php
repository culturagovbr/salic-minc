<?php

class Assinatura_Model_TbAssinatura extends MinC_Db_Model
{
    protected $_idAssintura;
    protected $_idPronac;
    protected $_idDocumentoAssinatura;
    protected $_dtAssinatura;
    protected $_idAssinante;
    protected $_idAtoAdministrativo;
    protected $_dsManifestacao;

    /**
     * @return mixed
     */
    public function getDsManifestacao()
    {
        return $this->_dsManifestacao;
    }

    /**
     * @param mixed $dsManifestacao
     * @return Assinatura_Model_TbAssinatura
     */
    public function setDsManifestacao($dsManifestacao)
    {
        $this->_dsManifestacao = $dsManifestacao;
        return $this;
    }

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
    public function getDtAssinatura()
    {
        return $this->_dtAssinatura;
    }

    /**
     * @param mixed $dtAssinatura
     */
    public function setDtAssinatura($dtAssinatura)
    {
        $this->_dtAssinatura = $dtAssinatura;
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

}
