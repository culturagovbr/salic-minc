<?php

/**
 * Class Proposta_Model_TbDeslocamento
 *
 * @name Proposta_Model_TbDeslocamento
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
class Proposta_Model_TbDeslocamento extends MinC_Db_Model
{
    protected $_iddeslocamento;
    protected $_idprojeto;
    protected $_idpaisorigem;
    protected $_iduforigem;
    protected $_idmunicipioorigem;
    protected $_idpaisdestino;
    protected $_idufdestino;
    protected $_idmunicipiodestino;
    protected $_qtde;
    protected $_idusuario;

    /**
     * @return mixed
     */
    public function getIddeslocamento()
    {
        return $this->_iddeslocamento;
    }

    /**
     * @param mixed $iddeslocamento
     */
    public function setIddeslocamento($iddeslocamento)
    {
        $this->_iddeslocamento = $iddeslocamento;
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
    public function getIdpaisorigem()
    {
        return $this->_idpaisorigem;
    }

    /**
     * @param mixed $idpaisorigem
     */
    public function setIdpaisorigem($idpaisorigem)
    {
        $this->_idpaisorigem = $idpaisorigem;
    }

    /**
     * @return mixed
     */
    public function getIduforigem()
    {
        return $this->_iduforigem;
    }

    /**
     * @param mixed $iduforigem
     */
    public function setIduforigem($iduforigem)
    {
        $this->_iduforigem = $iduforigem;
    }

    /**
     * @return mixed
     */
    public function getIdmunicipioorigem()
    {
        return $this->_idmunicipioorigem;
    }

    /**
     * @param mixed $idmunicipioorigem
     */
    public function setIdmunicipioorigem($idmunicipioorigem)
    {
        $this->_idmunicipioorigem = $idmunicipioorigem;
    }

    /**
     * @return mixed
     */
    public function getIdpaisdestino()
    {
        return $this->_idpaisdestino;
    }

    /**
     * @param mixed $idpaisdestino
     */
    public function setIdpaisdestino($idpaisdestino)
    {
        $this->_idpaisdestino = $idpaisdestino;
    }

    /**
     * @return mixed
     */
    public function getIdufdestino()
    {
        return $this->_idufdestino;
    }

    /**
     * @param mixed $idufdestino
     */
    public function setIdufdestino($idufdestino)
    {
        $this->_idufdestino = $idufdestino;
    }

    /**
     * @return mixed
     */
    public function getIdmunicipiodestino()
    {
        return $this->_idmunicipiodestino;
    }

    /**
     * @param mixed $idmunicipiodestino
     */
    public function setIdmunicipiodestino($idmunicipiodestino)
    {
        $this->_idmunicipiodestino = $idmunicipiodestino;
    }

    /**
     * @return mixed
     */
    public function getQtde()
    {
        return $this->_qtde;
    }

    /**
     * @param mixed $qtde
     */
    public function setQtde($qtde)
    {
        $this->_qtde = $qtde;
    }

    /**
     * @return mixed
     */
    public function getIdusuario()
    {
        return $this->_idusuario;
    }

    /**
     * @param mixed $idusuario
     */
    public function setIdusuario($idusuario)
    {
        $this->_idusuario = $idusuario;
    }
}
