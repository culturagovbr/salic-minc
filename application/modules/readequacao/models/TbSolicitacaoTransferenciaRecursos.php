<?php

class Readequacao_Model_TbSolicitacaoTransferenciaRecursos extends MinC_Db_Model
{
    protected $_idSolicitacaoTransferenciaRecursos;
    protected $_idReadequacao;
    protected $_tpTransferencia;
    protected $_idPronacRecebedor;
    protected $_vlRecebido;
    protected $_siAnaliseTecnica;
    protected $_siAnaliseComissao;
    protected $_stEstado;

    function setIdSolicitacaoTransferenciaRecursos($idSolicitacaoTransferenciaRecursos) {
        $this->_idSolicitacaoTransferenciaRecursos = $idSolicitacaoTransferenciaRecursos;
    }

    function getIdSolicitacaoTransferenciaRecursos() {
        return $this->_idSolicitacaoTransferenciaRecursos;
    }

    function setIdReadequacao($idReadequacao) {
        $this->_idReadequacao = $idReadequacao;
    }

    function getIdReadequacao() {
        return $this->_idReadequacao;
    }

    function setTpTransferencia($tpTransferencia) {
        $this->_tpTransferencia = $tpTransferencia;
    }

    function getTpTransferencia() {
        return $this->_tpTransferencia;
    }
    
    function setIdPronacRecebedor($idPronacRecebedor) {
        $this->_idPronacRecebedor = $idPronacRecebedor;
    }

    function getIdPronacRecebedor() {
        return $this->_idPronacRecebedor;
    }

    function setVlRecebido($vlRecebido) {
        $this->_vlRecebido = $vlRecebido;
    }

    function getVlRecebido() {
        return $this->_vlRecebido;
    }

    function setSiAnaliseTecnica($siAnaliseTecnica) {
        $this->_siAnaliseTecnica = $siAnaliseTecnica;
    }

    function getSiAnaliseTecnica() {
        return $this->_siAnaliseTecnica;
    }
    
    function setSiAnaliseComissao($siAnaliseComissao) {
        $this->_siAnaliseComissao = $siAnaliseComissao;
    }
    
    function getSiAnaliseComissao() {
        return $this->_siAnaliseComissao;
    }
    
    function setStEstado($stEstado) {
        $this->_stEstado = $stEstado;
    }
    
    function getStEstado() {
        return $this->_stEstado;
    }
}