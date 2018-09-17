<?php

class AvaliacaoResultados_Model_FluxosProjeto extends MinC_Db_Model
{
    protected $_id;
    protected $_idPronac;
    protected $_estadoId;

    public function getId(){
        return $this->_id;
    }

    public function getIdPronac(){
        return $this->_idPronac;
    }

    public function getEstadoId(){
        return $this->_estadoId;
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
}