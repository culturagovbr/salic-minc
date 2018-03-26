<?php

/**
 * @name Admissibilidade_Model_TbMensagemProjeto
 * @package Modules/Admissibilidade
 * @subpackage Models
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 20/03/2018
 *
 * @link http://salic.cultura.gov.br
 */
class Projeto_Model_TbProjetos extends MinC_Db_Model
{
    // protected $_idPronac;
    protected $_idPRONAC;
    protected $_anoProjeto;
    protected $_sequencial;
    protected $_ufProjeto;
    protected $_area;
    protected $_segmento;
    protected $_mecanismo;
    protected $_nomeProjeto;
    protected $_processo;
    protected $_cgcCpf;
    protected $_situacao;
    protected $_dtProtocolo;
    protected $_dtAnalise;
    protected $_modalidade;
    protected $_orgaoOrigem;
    protected $_orgao;
    protected $_dtSaida;
    protected $_dtRetorno;
    protected $_unidadeAnalise;
    protected $_analista;
    protected $_dtSituacao;
    protected $_resumoProjeto;
    protected $_providenciaTomada;
    protected $_localizacao;
    protected $_dtInicioExecucao;
    protected $_dtFimExecucao;
    protected $_solicitadoUfir;
    protected $_solicitadoReal;
    protected $_solicitadoCusteioUfir;
    protected $_solicitadoCusteioReal;
    protected $_solicitadoCapitalUfir;
    protected $_solicitadoCapitalReal;
    protected $_logon;
    protected $_idProjeto;

    // public function getIdPronac() {
    //   return $this->_idPronac;
    // }
    public function getIdPRONAC() {
      return $this->_idPRONAC;
    }
    public function getAnoProjeto() {
      return $this->_anoProjeto;
    }
    public function getSequencial() {
      return $this->_sequencial;
    }
    public function getUfProjeto() {
      return $this->_ufProjeto;
    }
    public function getArea() {
      return $this->_area;
    }
    public function getSegmento() {
      return $this->_segmento;
    }
    public function getMecanismo() {
      return $this->_mecanismo;
    }
    public function getNomeProjeto() {
      return $this->_nomeProjeto;
    }
    public function getProcesso() {
      return $this->_processo;
    }
    public function getCgcCpf() {
      return $this->_cgcCpf;
    }
    public function getSituacao() {
      return $this->_situacao;
    }
    public function getDtProtocolo() {
      return $this->_dtProtocolo;
    }
    public function getDtAnalise() {
      return $this->_dtAnalise;
    }
    public function getModalidade() {
      return $this->_modalidade;
    }
    public function getOrgaoOrigem() {
      return $this->_orgaoOrigem;
    }
    public function getOrgao() {
      return $this->_orgao;
    }
    public function getDtSaida() {
      return $this->_dtSaida;
    }
    public function getDtRetorno() {
      return $this->_dtRetorno;
    }
    public function getUnidadeAnalise() {
      return $this->_unidadeAnalise;
    }
    public function getAnalista() {
      return $this->_analista;
    }
    public function getDtSituacao() {
      return $this->_dtSituacao;
    }
    public function getResumoProjeto() {
      return $this->_resumoProjeto;
    }
    public function getProvidenciaTomada() {
      return $this->_providenciaTomada;
    }
    public function getLocalizacao() {
      return $this->_localizacao;
    }
    public function getDtInicioExecucao() {
      return $this->_dtInicioExecucao;
    }
    public function getDtFimExecucao() {
      return $this->_dtFimExecucao;
    }
    public function getSolicitadoUfir() {
      return $this->_solicitadoUfir;
    }
    public function getSolicitadoReal() {
      return $this->_solicitadoReal;
    }
    public function getSolicitadoCusteioUfir() {
      return $this->_solicitadoCusteioUfir;
    }
    public function getSolicitadoCusteioReal() {
      return $this->_solicitadoCusteioReal;
    }
    public function getSolicitadoCapitalUfir() {
      return $this->_solicitadoCapitalUfir;
    }
    public function getSolicitadoCapitalReal() {
      return $this->_solicitadoCapitalReal;
    }
    public function getLogon() {
      return $this->_logon;
    }
    public function getidProjeto() {
      return $this->_idProjeto;
    }


    /**
     * @name set
     **/
    // public function setidPronac($param)
    // {
    //   $this->_idPronac  = $param;
    //   return $this;
    // }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setIdPRONAC($param)
    {
      $this->_idPRONAC = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setAnoProjeto($param)
    {
      $this->_anoProjeto = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSequencial($param)
    {
      $this->_sequencial = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setUfProjeto($param)
    {
      $this->_ufProjeto = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setArea($param)
    {
      $this->_area = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSegmento($param)
    {
      $this->_segmento = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setMecanismo($param)
    {
      $this->_mecanismo = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setNomeProjeto($param)
    {
      $this->_nomeProjeto = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setProcesso($param)
    {
      $this->_processo = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setCgcCpf($param)
    {
      $this->_cgcCpf = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSituacao($param)
    {
      $this->_situacao = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setDtProtocolo($param)
    {
      $this->_dtProtocolo = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setDtAnalise($param)
    {
      $this->_dtAnalise = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setModalidade($param)
    {
      $this->_modalidade = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setOrgaoOrigem($param)
    {
      $this->_orgaoOrigem = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setOrgao($param)
    {
      $this->_orgao = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setDtSaida($param)
    {
      $this->_dtSaida = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setDtRetorno($param)
    {
      $this->_dtRetorno = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setUnidadeAnalise($param)
    {
      $this->_unidadeAnalise = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setAnalista($param)
    {
      $this->_analista = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setDtSituacao($param)
    {
      $this->_dtSituacao = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setResumoProjeto($param)
    {
      $this->_resumoProjeto = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setProvidenciaTomada($param)
    {
      $this->_providenciaTomada = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setLocalizacao($param)
    {
      $this->_localizacao = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setDtInicioExecucao($param)
    {
      $this->_dtInicioExecucao = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setDtFimExecucao($param)
    {
      $this->_dtFimExecucao = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSolicitadoUfir($param)
    {
      $this->_solicitadoUfir = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSolicitadoReal($param)
    {
      $this->_solicitadoReal = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSolicitadoCusteioUfir($param)
    {
      $this->_solicitadoCusteioUfir = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSolicitadoCusteioReal($param)
    {
      $this->_solicitadoCusteioReal = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSolicitadoCapitalUfir($param)
    {
      $this->_solicitadoCapitalUfir = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setSolicitadoCapitalReal($param)
    {
      $this->_solicitadoCapitalReal = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setLogon($param)
    {
      $this->_logon = $param;
      return $this;
    }

    /**
     * @name set
     * @return Projeto_Model_TbProjetos
     **/
    public function setidProjeto($param)
    {
      $this->_idProjeto = $param;
      return $this;
    }

    // /**
    //  * @return mixed
    //  */
    // public function getIdPronac()
    // {
    //     return $this->_idPronac;
    // }

    // /**
    //  * @param mixed $idPronac
    //  * @return Projeto_Model_TbProjetos
    //  */
    // public function setIdPronac($idPronac)
    // {
    //     $this->_idPronac = $idPronac;
    //     return $this;
    // }
}