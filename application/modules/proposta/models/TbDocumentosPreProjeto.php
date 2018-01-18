<?php

/**
 * Class Proposta_Model_TbDocumentosPreProjeto
 *
 * @name Proposta_Model_TbDocumentosPreProjeto
 * @package Modules/Proposta
 * @subpackage Models
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 02/10/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_TbDocumentosPreProjeto extends MinC_Db_Model
{
    protected $_iddocumentospreprojetos;
    protected $_codigodocumento;
    protected $_idprojeto;
    protected $_idpronac;
    protected $_data;
    protected $_noarquivo;
    protected $_taarquivo;
    protected $_bidocumento;
    protected $_dsdocumento;
    protected $_imdocumento;

    /**
     * @return mixed
     */
    public function getIddocumentospreprojetos()
    {
        return $this->_iddocumentospreprojetos;
    }

    /**
     * @param mixed $iddocumentospreprojetos
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setIddocumentospreprojetos($iddocumentospreprojetos)
    {
        $this->_iddocumentospreprojetos = $iddocumentospreprojetos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigodocumento()
    {
        return $this->_codigodocumento;
    }

    /**
     * @param mixed $codigodocumento
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setCodigodocumento($codigodocumento)
    {
        $this->_codigodocumento = $codigodocumento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdprojeto()
    {
        return $this->_idprojeto;
    }

    /**
     * @param mixed $idprojeto
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setIdprojeto($idprojeto)
    {
        $this->_idprojeto = $idprojeto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdpronac()
    {
        return $this->_idpronac;
    }

    /**
     * @param mixed $idpronac
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setIdpronac($idpronac)
    {
        $this->_idpronac = $idpronac;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param mixed $data
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNoarquivo()
    {
        return $this->_noarquivo;
    }

    /**
     * @param mixed $noarquivo
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setNoarquivo($noarquivo)
    {
        $this->_noarquivo = $noarquivo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaarquivo()
    {
        return $this->_taarquivo;
    }

    /**
     * @param mixed $taarquivo
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setTaarquivo($taarquivo)
    {
        $this->_taarquivo = $taarquivo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBidocumento()
    {
        return $this->_bidocumento;
    }

    /**
     * @param mixed $bidocumento
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setBidocumento($bidocumento)
    {
        $this->_bidocumento = $bidocumento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDsdocumento()
    {
        return $this->_dsdocumento;
    }

    /**
     * @param mixed $dsdocumento
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setDsdocumento($dsdocumento)
    {
        $this->_dsdocumento = $dsdocumento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImdocumento()
    {
        return $this->_imdocumento;
    }

    /**
     * @param mixed $imdocumento
     * @return Proposta_Model_TbDocumentosPreProjeto
     */
    public function setImdocumento($imdocumento)
    {
        $this->_imdocumento = $imdocumento;
        return $this;
    }
}
