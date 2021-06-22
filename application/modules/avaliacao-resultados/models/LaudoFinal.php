<?php

class AvaliacaoResultados_Model_LaudoFinal extends MinC_Db_Model
{
    protected $_idLaudoFinal;
    protected $_idPronac;
    protected $_dtLaudoFinal;
    protected $_siManifestacao;
    protected $_dsLaudoFinal;
    protected $_idUsuario;

    const SI_MANIFESTACAO_APROVADO = 'A';
    const SI_MANIFESTACAO_PARCIALMENTE = 'P';
    const SI_MANIFESTACAO_REPROVADO = 'R';

    public function getIdLaudoFinal(){
        return $this->_idLaudoFinal;
    }

    public function getIdPronac(){
        return $this->_idPronac;
    }

    public function getDtLaudoFinal(){
        return $this->_dtLaudoFinal;
    }

    public function getSiManifestacao(){
        return $this->_siManifestacao;
    }

    public function getDsLaudoFinal(){
        return $this->_dsLaudoFinal;
    }

    public function getIdUsuario(){
        return $this->_idUsuario;
    }
    
    public function setIdLaudoFinal($value){
        $this->_idLaudoFinal = $value;
        return $this;
    }
    
    public function setIdPronac($value){
        $this->_idPronac = $value;
        return $this;
    }

    public function setDtLaudoFinal($value){
        $this->_dtLaudoFinal = $value;
        return $this;
    }

    public function setSiManifestacao($value){
        $this->_siManifestacao = $value;
        return $this;
    }

    public function setDsLaudoFinal($value){
        $this->_dsLaudoFinal = $value;
        return $this;
    }

    public function setIdUsuario($value){
        $this->_idUsuario = $value;
        return $this;
    }
}
