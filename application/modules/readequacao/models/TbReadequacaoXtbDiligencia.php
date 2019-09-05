<?php

class Readequacao_Model_TbReadequacaoXtbDiligencia extends MinC_Db_Model
{
    protected $_idReadequacao;
    protected $_idDiligencia;
    
    function setIdReadequacao($idReadequacao) {
        $this->_idReadequacao = $idReadequacao;
    }

    function getIdReadequacao() {
        return $this->_idReadequacao;
    }

    function setIdDiligencia($idDiligencia) {
        $this->_idDiligencia = $idDiligencia;
    }

    function getIdDiligencia() {
        return $this->_idDiligencia;
    }
}
