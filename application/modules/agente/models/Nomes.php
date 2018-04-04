<?php

/**
 * Class Agente_Model_Nomes
 *
 * @name Agente_Model_Nomes
 * @package Modules/Agente
 * @subpackage Models
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_Nomes extends MinC_Db_Model
{
    protected $_idnome;
    protected $_idagente;
    protected $_tiponome;
    protected $_descricao;
    protected $_status;
    protected $_usuario;

    /**
     * @return mixed
     */
    public function getIdnome()
    {
        return $this->_idnome;
    }

    /**
     * @param mixed $idnome
     */
    public function setIdnome($idnome)
    {
        $this->_idnome = $idnome;
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
    public function getTiponome()
    {
        return $this->_tiponome;
    }

    /**
     * @param mixed $tiponome
     */
    public function setTiponome($tiponome)
    {
        $this->_tiponome = $tiponome;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->_descricao;
    }

    /**
     * @param mixed $descricao
     */
    public function setDescricao($descricao)
    {
        $this->_descricao = $descricao;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->_status = $status;
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
     *
     * @name toArray
     * @return array
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  01/09/2016
     */
    public function toArray()
    {
        return array(
            'idnome' => self::getIdnome(),
            'idagente' => self::getIdagente(),
            'tiponome' => self::getTiponome(),
            'descricao' => self::getDescricao(),
            'status' => self::getStatus(),
            'usuario' => self::getUsuario()
        );
    }
}
