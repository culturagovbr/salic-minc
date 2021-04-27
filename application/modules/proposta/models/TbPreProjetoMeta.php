<?php
class Proposta_Model_TbPreProjetoMeta extends MinC_Db_Model
{
    protected $_idPreProjetoMeta;
    protected $_idPreProjeto;
    protected $_metaKey;
    protected $_metaValue;
    protected $_dtAlteracao;

    /**
     * @return mixed
     */
    public function getIdPreProjetoMeta()
    {
        return $this->_idPreProjetoMeta;
    }

    /**
     * @param mixed $idPreProjetoMeta
     */
    public function setIdPreProjetoMeta($idPreProjetoMeta)
    {
        $this->_idPreProjetoMeta = $idPreProjetoMeta;
    }

    /**
     * @return mixed
     */
    public function getIdPreProjeto()
    {
        return $this->_idPreProjeto;
    }

    /**
     * @param mixed $idPreProjeto
     */
    public function setIdPreProjeto($idPreProjeto)
    {
        $this->_idPreProjeto = $idPreProjeto;
    }

    /**
     * @return mixed
     */
    public function getMetaKey()
    {
        return $this->_metaKey;
    }

    /**
     * @param mixed $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->_metaKey = $metaKey;
    }

    /**
     * @return mixed
     */
    public function getMetaValue()
    {
        return $this->_metaValue;
    }

    /**
     * @param mixed $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->_metaValue = $metaValue;
    }

    /**
     * @return mixed
     */
    public function getDtAlteracao()
    {
        return $this->_dtAlteracao;
    }

    /**
     * @param mixed $dtAlteracao
     */
    public function setDtAlteracao($dtAlteracao)
    {
        $this->_dtAlteracao = $dtAlteracao;
    }



}
