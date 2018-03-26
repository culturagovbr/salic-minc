<?php

/**
 * Class Agente_Model_DDD
 *
 * @name Agente_Model_DDD
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
class Agente_Model_DDD extends MinC_Db_Model
{
    protected $_idDDD;
    protected $_idUF;
    protected $_Codigo;

    /**
     * @return mixed
     */
    public function getIdDDD()
    {
        return $this->_idDDD;
    }

    /**
     * @param mixed $idDDD
     */
    public function setIdDDD($idDDD)
    {
        $this->_idDDD = $idDDD;
    }

    /**
     * @return mixed
     */
    public function getIdUF()
    {
        return $this->_idUF;
    }

    /**
     * @param mixed $idUF
     */
    public function setIdUF($idUF)
    {
        $this->_idUF = $idUF;
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->_Codigo;
    }

    /**
     * @param mixed $Codigo
     */
    public function setCodigo($Codigo)
    {
        $this->_Codigo = $Codigo;
    }
}
