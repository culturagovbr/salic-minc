<?php

/**
 * Class Proposta_Model_TbDocumentosAgentes
 *
 * @name Proposta_Model_TbDocumentosAgentes
 * @package Modules/Agente
 * @subpackage Models
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 29/09/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_TbDocumentosAgentes extends MinC_Db_Model
{
    protected $_iddocumentosagentes;
    protected $_codigodocumento;
    protected $_idagente;
    protected $_data;
    protected $_noarquivo;
    protected $_taarquivo;
    protected $_imdocumento;

    /**
     * @return mixed
     */
    public function getIddocumentosagentes()
    {
        return $this->_iddocumentosagentes;
    }

    /**
     * @param mixed $iddocumentosagentes
     * @return Proposta_Model_TbDocumentosAgentes
     */
    public function setIddocumentosagentes($iddocumentosagentes)
    {
        $this->_iddocumentosagentes = $iddocumentosagentes;
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
     * @return Proposta_Model_TbDocumentosAgentes
     */
    public function setCodigodocumento($codigodocumento)
    {
        $this->_codigodocumento = $codigodocumento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdagente()
    {
        return $this->_idagente;
    }

    /**
     * @param mixed $idagente
     * @return Proposta_Model_TbDocumentosAgentes
     */
    public function setIdagente($idagente)
    {
        $this->_idagente = $idagente;
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
     * @return Proposta_Model_TbDocumentosAgentes
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
     * @return Proposta_Model_TbDocumentosAgentes
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
     * @return Proposta_Model_TbDocumentosAgentes
     */
    public function setTaarquivo($taarquivo)
    {
        $this->_taarquivo = $taarquivo;
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
     * @return Proposta_Model_TbDocumentosAgentes
     */
    public function setImdocumento($imdocumento)
    {
        $this->_imdocumento = $imdocumento;
        return $this;
    }
}
