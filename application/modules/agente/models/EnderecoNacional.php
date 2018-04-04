<?php

/**
 * Class Agente_Model_EnderecoNacional
 *
 * @name Agente_Model_EnderecoNacional
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
class Agente_Model_EnderecoNacional extends MinC_Db_Model
{
    protected $_idendereco;
    protected $_idagente;
    protected $_tipoendereco;
    protected $_tipologradouro;
    protected $_logradouro;
    protected $_numero;
    protected $_bairro;
    protected $_complemento;
    protected $_cidade;
    protected $_uf;
    protected $_cep;
    protected $_municipio;
    protected $_ufdescricao;
    protected $_status;
    protected $_divulgar;
    protected $_usuario;

    /**
     * @return mixed
     */
    public function getIdendereco()
    {
        return $this->_idendereco;
    }

    /**
     * @param mixed $idendereco
     */
    public function setIdendereco($idendereco)
    {
        $this->_idendereco = $idendereco;
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
    public function getTipoendereco()
    {
        return $this->_tipoendereco;
    }

    /**
     * @param mixed $tipoendereco
     */
    public function setTipoendereco($tipoendereco)
    {
        $this->_tipoendereco = $tipoendereco;
    }

    /**
     * @return mixed
     */
    public function getTipologradouro()
    {
        return $this->_tipologradouro;
    }

    /**
     * @param mixed $tipologradouro
     */
    public function setTipologradouro($tipologradouro)
    {
        $this->_tipologradouro = $tipologradouro;
    }

    /**
     * @return mixed
     */
    public function getLogradouro()
    {
        return $this->_logradouro;
    }

    /**
     * @param mixed $logradouro
     */
    public function setLogradouro($logradouro)
    {
        $this->_logradouro = $logradouro;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->_numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero)
    {
        $this->_numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getBairro()
    {
        return $this->_bairro;
    }

    /**
     * @param mixed $bairro
     */
    public function setBairro($bairro)
    {
        $this->_bairro = $bairro;
    }

    /**
     * @return mixed
     */
    public function getComplemento()
    {
        return $this->_complemento;
    }

    /**
     * @param mixed $complemento
     */
    public function setComplemento($complemento)
    {
        $this->_complemento = $complemento;
    }

    /**
     * @return mixed
     */
    public function getCidade()
    {
        return $this->_cidade;
    }

    /**
     * @param mixed $cidade
     */
    public function setCidade($cidade)
    {
        $this->_cidade = $cidade;
    }

    /**
     * @return mixed
     */
    public function getUf()
    {
        return $this->_uf;
    }

    /**
     * @param mixed $uf
     */
    public function setUf($uf)
    {
        $this->_uf = $uf;
    }

    /**
     * @return mixed
     */
    public function getCep()
    {
        return $this->_cep;
    }

    /**
     * @param mixed $cep
     */
    public function setCep($cep)
    {
        $this->_cep = $cep;
    }

    /**
     * @return mixed
     */
    public function getMunicipio()
    {
        return $this->_municipio;
    }

    /**
     * @param mixed $municipio
     */
    public function setMunicipio($municipio)
    {
        $this->_municipio = $municipio;
    }

    /**
     * @return mixed
     */
    public function getUfdescricao()
    {
        return $this->_ufdescricao;
    }

    /**
     * @param mixed $ufdescricao
     */
    public function setUfdescricao($ufdescricao)
    {
        $this->_ufdescricao = $ufdescricao;
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
