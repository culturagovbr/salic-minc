<?php

class Readequacao_Model_TbProjetoRecebedorRecurso extends MinC_Db_Model
{
    protected $_idProjetoRecebedorRecurso;
    protected $_idSolicitacaoTransferenciaRecursos;
    protected $_idPronacTransferidor;
    protected $_idPronacRecebedor;
    protected $_tpTransferencia;
    protected $_dtRecebimento;
    protected $_vlRecebido;

    function setIdProjetoRecebedorRecurso($idProjetoRecebedorRecurso) {
        $this->_idProjetoRecebedorRecurso = $idProjetoRecebedorRecurso;
    }

    function getIdProjetoRecebedorRecurso() {
        return $this->_idProjetoRecebedorRecurso;
    }
    
    function setIdSolicitacaoTransferenciaRecursos($idSolicitacaoTransferenciaRecursos) {
        $this->_idSolicitacaoTransferenciaRecursos = $idSolicitacaoTransferenciaRecursos;
    }
    
    function getIdSolicitacaoTransferenciaRecursos() {
        return $this->_idSolicitacaoTransferenciaRecursos;
    }
    
    function setIdPronacTransferidor($idPronacTransferidor) {
        $this->_idPronacTransferidor = $idPronacTransferidor;
    }
    
    function getIdPronacTransferidor() {
        return $this->_idPronacTransferidor;
    }
    
    function setIdPronacRecebedor($idPronacRecebedor) {
        $this->_idPronacRecebedor = $idPronacRecebedor;
    }
    
    function getIdPronacRecebedor() {
        return $this->_idPronacRecebedor;
    }
    
    function setTpTransferencia($tpTransferencia) {
        $this->_tpTransferencia = $tpTransferencia;
    }
    
    function getTpTransferencia() {
        return $this->_tpTransferencia;
    }
    
    function setDtRecebimento($dtRecebimento) {
        $this->_dtRecebimento = $dtRecebimento;
    }
    
    function getDtRecebimento() {
        return $this->_dtRecebimento;
    }
    
    function setVlRecebido($vlRecebido) {
        $this->_vlRecebido = $vlRecebido;
    }
    
    function getVlRecebido() {
        return $this->_vlRecebido;
    }
}
