<?php

class AvaliacaoResultados_Model_FluxosProjeto extends MinC_Db_Model
{
    protected $_id;
    protected $_idPronac;
    protected $_estadoId;
    protected $_orgao;
    protected $_grupo;
    protected $_idAgente;

    public function getId(){
        return $this->_id;
    }

    public function getIdPronac(){
        return $this->_idPronac;
    }

    public function getEstadoId(){
        return $this->_estadoId;
    }

    public function getOrgao(){
        return $this->_orgao;
    }

    public function getGrupo(){
        return $this->_grupo;
    }

    public function getIdAgente(){
        return $this->_idAgente;
    }

    public function setEstadoId($value){
        $this->_estadoId = $value;
        return $this;
    }

    public function setId($value){
        $this->_id = $value;
        return $this;
    }
    public function setIdPronac($value){
        $this->_idPronac = $value;
        return $this;
    }

    public function setOrgao($value){
        $this->_orgao = $value;
        return $this;
    }

    public function setGrupo($value){
        $this->_grupo = $value;
        return $this;
    }

    public function setIdAgente($value){
        $this->_idAgente = $value;
        return $this;
    }
}