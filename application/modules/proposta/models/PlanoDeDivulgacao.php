<?php

/**
 * Class Proposta_Model_PlanoDeDivulgacao
 *
 * @name Proposta_Model_PlanoDeDivulgacao
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
class Proposta_Model_PlanoDeDivulgacao extends MinC_Db_Model
{
    protected $_idplanodivulgacao;
    protected $_idprojeto;
    protected $_idpeca;
    protected $_idveiculo;
    protected $_usuario;
    protected $_siplanodedivulgacao;
    protected $_iddocumento;
    protected $_stplanodivulgacao;

    /**
     * @return mixed
     */
    public function getIdplanodivulgacao()
    {
        return $this->_idplanodivulgacao;
    }

    /**
     * @param mixed $idplanodivulgacao
     */
    public function setIdplanodivulgacao($idplanodivulgacao)
    {
        $this->_idplanodivulgacao = $idplanodivulgacao;
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
     */
    public function setIdprojeto($idprojeto)
    {
        $this->_idprojeto = $idprojeto;
    }

    /**
     * @return mixed
     */
    public function getIdpeca()
    {
        return $this->_idpeca;
    }

    /**
     * @param mixed $idpeca
     */
    public function setIdpeca($idpeca)
    {
        $this->_idpeca = $idpeca;
    }

    /**
     * @return mixed
     */
    public function getIdveiculo()
    {
        return $this->_idveiculo;
    }

    /**
     * @param mixed $idveiculo
     */
    public function setIdveiculo($idveiculo)
    {
        $this->_idveiculo = $idveiculo;
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
    public function getSiplanodedivulgacao()
    {
        return $this->_siplanodedivulgacao;
    }

    /**
     * @param mixed $siplanodedivulgacao
     */
    public function setSiplanodedivulgacao($siplanodedivulgacao)
    {
        $this->_siplanodedivulgacao = $siplanodedivulgacao;
    }

    /**
     * @return mixed
     */
    public function getIddocumento()
    {
        return $this->_iddocumento;
    }

    /**
     * @param mixed $iddocumento
     */
    public function setIddocumento($iddocumento)
    {
        $this->_iddocumento = $iddocumento;
    }

    /**
     * @return mixed
     */
    public function getStplanodivulgacao()
    {
        return $this->_stplanodivulgacao;
    }

    /**
     * @param mixed $stplanodivulgacao
     */
    public function setStplanodivulgacao($stplanodivulgacao)
    {
        $this->_stplanodivulgacao = $stplanodivulgacao;
    }
}
