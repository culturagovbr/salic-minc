<?php

class Assinatura_Model_TbDocumentoAssinatura extends MinC_Db_Model
{
    protected $_idDocumentoAssinatura;
    protected $_IdPRONAC;
    protected $_idTipoDoAtoAdministrativo;
    protected $_conteudo;
    protected $_dt_criacao;
    protected $_idCriadorDocumento;
    protected $_cdSituacao;
    protected $_idAtoDeGestao;
    protected $_stEstado;

    const CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA = 1;
    const CD_SITUACAO_FECHADO_PARA_ASSINATURA = 2;

    const ST_ESTADO_DOCUMENTO_ATIVO = 1;
    const ST_ESTADO_DOCUMENTO_INATIVO = 0;

    /**
     * @return mixed
     */
    public function getStEstado()
    {
        return $this->_stEstado;
    }

    /**
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
        return $this;
    }

    public function getIdAtoDeGestao()
    {
        return $this->_idAtoDeGestao;
    }

    /**
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdAtoDeGestao($idAtoDeGestao)
    {
        $this->_idAtoDeGestao = $idAtoDeGestao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCdSituacao()
    {
        return $this->_cdSituacao;
    }

    /**
     * @param mixed $cdSituacao
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setCdSituacao($cdSituacao)
    {
        $this->_cdSituacao = $cdSituacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdDocumentoAssinatura()
    {
        return $this->_idDocumentoAssinatura;
    }

    /**
     * @param mixed $_idDocumentoAssinatura
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdDocumentoAssinatura($_idDocumentoAssinatura)
    {
        $this->_idDocumentoAssinatura = $_idDocumentoAssinatura;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPRONAC()
    {
        return $this->_IdPRONAC;
    }

    /**
     * @param mixed $IdPRONAC
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdPRONAC($IdPRONAC)
    {
        $this->_IdPRONAC = $IdPRONAC;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdTipoDoAtoAdministrativo()
    {
        return $this->_idTipoDoAtoAdministrativo;
    }

    /**
     * @param mixed $idTipoDoAtoAdministrativo
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdTipoDoAtoAdministrativo($idTipoDoAtoAdministrativo)
    {
        $this->_idTipoDoAtoAdministrativo = $idTipoDoAtoAdministrativo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConteudo()
    {
        return $this->_conteudo;
    }

    /**
     * @param mixed $conteudo
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setConteudo($conteudo)
    {
        $this->_conteudo = $conteudo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtCriacao()
    {
        return $this->_dt_criacao;
    }

    /**
     * @param mixed $dt_criacao
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setDtCriacao($dt_criacao)
    {
        $this->_dt_criacao = $dt_criacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdCriadorDocumento()
    {
        return $this->_idCriadorDocumento;
    }

    /**
     * @param mixed $idCriadorDocumento
     * @return Assinatura_Model_TbDocumentoAssinatura
     */
    public function setIdCriadorDocumento($idCriadorDocumento)
    {
        $this->_idCriadorDocumento = $idCriadorDocumento;
        return $this;
    }
}
