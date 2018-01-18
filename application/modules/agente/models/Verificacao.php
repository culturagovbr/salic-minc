<?php

class Agente_Model_Verificacao extends MinC_Db_Model
{
    protected $_idverificacao;
    protected $_idtipo;
    protected $_descricao;
    protected $_sistema;

    public function getIdverificacao()
    {
        return $this->_idverificacao;
    }

    public function setIdverificacao($idverificacao)
    {
        $this->_idverificacao = $idverificacao;
    }

    public function getIdtipo()
    {
        return $this->_idtipo;
    }

    public function setIdtipo($idtipo)
    {
        $this->_idtipo = $idtipo;
    }

    public function getDescricao()
    {
        return $this->_descricao;
    }

    public function setDescricao($descricao)
    {
        $this->_descricao = $descricao;
    }

    public function getSistema()
    {
        return $this->_sistema;
    }

    public function setSistema($sistema)
    {
        $this->_sistema = $sistema;
    }
}
