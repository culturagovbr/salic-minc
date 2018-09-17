<?php

class AvaliacaoResultados_Model_tbEncaminhamentoPrestacaoContas extends MinC_Db_Model
{
    protected $_idEncPrestContas;
    protected $_idSituacaoEncPrestContas;
    protected $_idPronac;
    protected $_stAtivo;
    protected $_dtInicioEncaminhamento;
    protected $_dtFimEncaminhamento;
    protected $_idAgenteOrigem;
    protected $_idAgenteDestino;
    protected $_idOrgaoDestino;
    protected $_idOrgaoOrigem;
    protected $_cdGruposOrigem;
    protected $_cdGruposDestino;
    protected $_idSituacao;
    protected $_dsJustificativa;

    public function getIdEncPrestContas()
    {
        return $this->_idEncPrestContas;
    }

    public function setIdEncPrestContas($idEncPrestContas)
    {
        $this->_idEncPrestContas = $idEncPrestContas;
        return $this;
    }

    public function getIdSituacaoEncPrestContas()
    {
        return $this->_idSituacaoEncPrestContas;
    }

    public function setIdSituacaoEncPrestContas($idSituacaoEncPrestContas)
    {
        $this->_idSituacaoEncPrestContas = $idSituacaoEncPrestContas;
        return $this;
    }

    public function getIdPronac()
    {
        return $this->_idPronac;
    }

    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
        return $this;
    }

    public function getStAtivo()
    {
        return $this->_stAtivo;
    }

    public function setStAtivo($stAtivo)
    {
        $this->_stAtivo = $stAtivo;
        return $this;
    }

    public function getDtInicioEncaminhamento()
    {
        return $this->_dtInicioEncaminhamento;
    }

    public function setDtInicioEncaminhamento($dtInicioEncaminhamento)
    {
        $this->_dtInicioEncaminhamento = $dtInicioEncaminhamento;
        return $this;
    }

    public function getDtFimEncaminhamento()
    {
        return $this->_dtFimEncaminhamento;
    }

    public function setDtFimEncaminhamento($dtFimEncaminhamento)
    {
        $this->_dtFimEncaminhamento = $dtFimEncaminhamento;
        return $this;
    }

    public function getIdAgenteOrigem()
    {
        return $this->_idAgenteOrigem;
    }

    public function setIdAgenteOrigem($idAgenteOrigem)
    {
        $this->_idAgenteOrigem = $idAgenteOrigem;
        return $this;
    }

    public function getIdAgenteDestino()
    {
        return $this->_idAgenteDestino;
    }

    public function setIdAgenteDestino($idAgenteDestino)
    {
        $this->_idAgenteDestino = $idAgenteDestino;
        return $this;
    }

    public function getIdOrgaoDestino()
    {
        return $this->_idOrgaoDestino;
    }

    public function setIdOrgaoDestino($idOrgao)
    {
        $this->_idOrgaoDestino = $idOrgao;
        return $this;
    }

    public function getIdOrgaoOrigem()
    {
        return $this->_idOrgaoOrigem;
    }

    public function setIdOrgaoOrigem($value)
    {
        $this->_idOrgaoOrigem = $value;
        return $this;
    }

    public function getCdGruposOrigem(){
        return $this->_cdGruposOrigem;
    }

    public function setCdGruposOrigem($value){
        $this->_cdGruposOrigem = $value;
        return $this;
    }

    public function getCdGruposDestino(){
        return $this->_cdGruposDestino;
    }

    public function setCdGruposDestino($value){
        $this->_cdGruposDestino = $value;
        return $this;
    }

    public function getIdSituacao() {
        return $this->_idSituacao;
    }

    public function setIdSituacao($value) {
        $this->_idSituacao = $value;
        return $this;
    }

    public function getDsJustificativa() {
        return $this->_dsJustificativa;
    }

    public function setDsJustificativa($value) {
        $this->_dsJustificativa = $value;
        return $this;
    }
}