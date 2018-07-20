<?php

class Foo_Model_Tabela extends MinC_Db_Model
{
    protected $_Codigo;
    protected $_DadoNr;

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->_Codigo;
    }

    /**
     * @param mixed $Codigo
     * @return Foo_Model_Tabela
     */
    public function setCodigo($Codigo)
    {
        $this->_Codigo = $Codigo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDadoNr()
    {
        return $this->_DadoNr;
    }

    /**
     * @param mixed $DadoNr
     * @return Foo_Model_Tabela
     */
    public function setDadoNr($DadoNr)
    {
        $this->_DadoNr = $DadoNr;
        return $this;
    }
}
