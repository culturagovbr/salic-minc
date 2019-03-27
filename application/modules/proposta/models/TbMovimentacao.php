<?php

class Proposta_Model_TbMovimentacao extends MinC_Db_Model
{
    protected $_idmovimentacao;
    protected $_idprojeto;
    protected $_movimentacao;
    protected $_dtmovimentacao;
    protected $_stestado;
    protected $_usuario;
    protected $_id_orgao;
    protected $_id_perfil;

    const PROPOSTA_COM_PROPONENTE = 95;
    const PROPOSTA_PARA_ANALISE_INICIAL = 96;
    const PROPOSTA_PARA_ANALISE_FINAL = 128;

    /**
     * @return mixed
     */
    public function getIdOrgao()
    {
        return $this->_id_orgao;
    }

    /**
     * @param mixed $id_orgao
     * @return Proposta_Model_TbMovimentacao
     */
    public function setIdOrgao($id_orgao)
    {
        $this->_id_orgao = $id_orgao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPerfil()
    {
        return $this->_id_perfil;
    }

    /**
     * @param mixed $id_perfil
     * @return Proposta_Model_TbMovimentacao
     */
    public function setIdPerfil($id_perfil)
    {
        $this->_id_perfil = $id_perfil;
        return $this;
    }

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
