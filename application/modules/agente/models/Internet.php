<?php

/**
 * Class Agente_Model_Internet
 *
 * @name Agente_Model_Internet
 * @package Modules/Agente
 * @subpackage Models
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 06/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_Internet extends MinC_Db_Model
{
    protected $_idinternet;
    protected $_idagente;
    protected $_tipointernet;
    protected $_descricao;
    protected $_status;
    protected $_divulgar;
    protected $_usuario;

    /**
     * @return mixed
     */
    public function getIdinternet()
    {
        return $this->_idinternet;
    }

    /**
     * @param mixed $idinternet
     */
    public function setIdinternet($idinternet)
    {
        $this->_idinternet = $idinternet;
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
    public function getTipointernet()
    {
        return $this->_tipointernet;
    }

    /**
     * @param mixed $tipointernet
     */
    public function setTipointernet($tipointernet)
    {
        $this->_tipointernet = $tipointernet;
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
    public function getDivulgar()
    {
        return $this->_divulgar;
    }

    /**
     * @param mixed $divulgar
     */
    public function setDivulgar($divulgar)
    {
        $this->_divulgar = $divulgar;
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
}
