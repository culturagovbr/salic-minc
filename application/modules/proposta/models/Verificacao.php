<?php

class Proposta_Model_Verificacao extends MinC_Db_Model
{
    protected $_idVerificacao;
    protected $_idTipo;
    protected $_Descricao;
    protected $_stEstado;

    const PROPOSTA_APROVADA_EM_EDITAIS = 618;
    const PROPOSTA_COM_CONTRATOS_DE_PATROCINIOS = 619;

    /**
     * @return mixed
     */
    public function getIdVerificacao()
    {
        return $this->_idVerificacao;
    }

    /**
     * @param mixed $idVerificacao
     */
    public function setIdVerificacao($idVerificacao)
    {
        $this->_idVerificacao = $idVerificacao;
    }

    /**
     * @return mixed
     */
    public function getIdTipo()
    {
        return $this->_idTipo;
    }

    /**
     * @param mixed $idTipo
     */
    public function setIdTipo($idTipo)
    {
        $this->_idTipo = $idTipo;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->_Descricao;
    }

    /**
     * @param mixed $Descricao
     */
    public function setDescricao($Descricao)
    {
        $this->_Descricao = $Descricao;
    }

    /**
     * @return mixed
     */
    public function getStEstado()
    {
        return $this->_stEstado;
    }

    /**
     * @param mixed $stEstado
     */
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
    }


}