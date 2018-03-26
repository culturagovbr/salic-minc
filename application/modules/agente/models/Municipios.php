<?php

/**
 * Class Agente_Model_Municipios
 *
 * @name Agente_Model_Municipios
 * @package Modules/Agente
 * @subpackage Models
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 01/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_Municipios extends MinC_Db_Model
{
    protected $_idMunicipioIBGE;
    protected $_idUFIBGE;
    protected $_IdMeso;
    protected $_idMicro;
    protected $_Descricao;

    /**
     * @return mixed
     */
    public function getIdMunicipioIBGE()
    {
        return $this->_idMunicipioIBGE;
    }

    /**
     * @param mixed $idMunicipioIBGE
     */
    public function setIdMunicipioIBGE($idMunicipioIBGE)
    {
        $this->_idMunicipioIBGE = $idMunicipioIBGE;
    }

    /**
     * @return mixed
     */
    public function getIdUFIBGE()
    {
        return $this->_idUFIBGE;
    }

    /**
     * @param mixed $idUFIBGE
     */
    public function setIdUFIBGE($idUFIBGE)
    {
        $this->_idUFIBGE = $idUFIBGE;
    }

    /**
     * @return mixed
     */
    public function getIdMeso()
    {
        return $this->_IdMeso;
    }

    /**
     * @param mixed $IdMeso
     */
    public function setIdMeso($IdMeso)
    {
        $this->_IdMeso = $IdMeso;
    }

    /**
     * @return mixed
     */
    public function getIdMicro()
    {
        return $this->_idMicro;
    }

    /**
     * @param mixed $idMicro
     */
    public function setIdMicro($idMicro)
    {
        $this->_idMicro = $idMicro;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->_Descricao;
    }

    /**
     * @param mixed $Descricao
     */
    public function setDescricao($Descricao)
    {
        $this->_Descricao = $Descricao;
    }
}
