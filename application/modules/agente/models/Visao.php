<?php

/**
 * Class Agente_Model_Visao
 *
 * @name Agente_Model_Visao
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
class Agente_Model_Visao extends MinC_Db_Model
{
    protected $_idvisao;
    protected $_idagente;
    protected $_visao;
    protected $_usuario;
    protected $_stativo;

    /**
     * @return mixed
     */
    public function getIdvisao()
    {
        return $this->_idvisao;
    }

    /**
     * @param mixed $idvisao
     */
    public function setIdvisao($idvisao)
    {
        $this->_idvisao = $idvisao;
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
     */
    public function setIdagente($idagente)
    {
        $this->_idagente = $idagente;
    }

    /**
     * @return mixed
     */
    public function getVisao()
    {
        return $this->_visao;
    }

    /**
     * @param mixed $visao
     */
    public function setVisao($visao)
    {
        $this->_visao = $visao;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->_usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario)
    {
        $this->_usuario = $usuario;
    }

    /**
     * @return mixed
     */
    public function getStativo()
    {
        return $this->_stativo;
    }

    /**
     * @param mixed $stativo
     */
    public function setStativo($stativo)
    {
        $this->_stativo = $stativo;
    }
}
