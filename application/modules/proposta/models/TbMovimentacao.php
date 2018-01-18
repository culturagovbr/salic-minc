<?php

/**
 * Class Proposta_Model_TbMovimentacao
 *
 * @name Proposta_Model_TbMovimentacao
 * @package Modules/Agente
 * @subpackage Models
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 20/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_TbMovimentacao extends MinC_Db_Model
{
    protected $_idmovimentacao;
    protected $_idprojeto;
    protected $_movimentacao;
    protected $_dtmovimentacao;
    protected $_stestado;
    protected $_usuario;

    /**
     * @return mixed
     */
    public function getIdmovimentacao()
    {
        return $this->_idmovimentacao;
    }

    /**
     * @param mixed $idmovimentacao
     * @return Proposta_Model_TbMovimentacao
     */
    public function setIdmovimentacao($idmovimentacao)
    {
        $this->_idmovimentacao = $idmovimentacao;
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
     * @return Proposta_Model_TbMovimentacao
     */
    public function setIdprojeto($idprojeto)
    {
        $this->_idprojeto = $idprojeto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMovimentacao()
    {
        return $this->_movimentacao;
    }

    /**
     * @param mixed $movimentacao
     * @return Proposta_Model_TbMovimentacao
     */
    public function setMovimentacao($movimentacao)
    {
        $this->_movimentacao = $movimentacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtmovimentacao()
    {
        return $this->_dtmovimentacao;
    }

    /**
     * @param mixed $dtmovimentacao
     * @return Proposta_Model_TbMovimentacao
     */
    public function setDtmovimentacao($dtmovimentacao)
    {
        $this->_dtmovimentacao = $dtmovimentacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStestado()
    {
        return $this->_stestado;
    }

    /**
     * @param mixed $stestado
     * @return Proposta_Model_TbMovimentacao
     */
    public function setStestado($stestado)
    {
        $this->_stestado = $stestado;
        return $this;
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
     * @return Proposta_Model_TbMovimentacao
     */
    public function setUsuario($usuario)
    {
        $this->_usuario = $usuario;
        return $this;
    }
}
