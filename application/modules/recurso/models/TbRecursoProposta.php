<?php

class Recurso_Model_TbRecursoProposta extends MinC_Db_Model
{
    protected $_idRecursoProposta;
    protected $_idPreProjeto;
    protected $_dtRecursoProponente;
    protected $_dsRecursoProponente;
    protected $_idProponente;
    protected $_idAvaliadorTecnico;
    protected $_dtAvaliacaoTecnica;
    protected $_dsAvaliacaoTecnica;
    protected $_tpRecurso;
    protected $_tpSolicitacao;
    protected $_stAtendimento;
    protected $_idArquivo;
    protected $_stEstado;

    /**
     * @return mixed
     */
    public function getIdRecursoProposta()
    {
        return $this->_idRecursoProposta;
    }

    /**
     * @param mixed $idRecursoProposta
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setIdRecursoProposta($idRecursoProposta)
    {
        $this->_idRecursoProposta = $idRecursoProposta;
        return $this;
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
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setIdPreProjeto($idPreProjeto)
    {
        $this->_idPreProjeto = $idPreProjeto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtRecursoProponente()
    {
        return $this->_dtRecursoProponente;
    }

    /**
     * @param mixed $dtRecursoProponente
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setDtRecursoProponente($dtRecursoProponente)
    {
        $this->_dtRecursoProponente = $dtRecursoProponente;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDsRecursoProponente()
    {
        return $this->_dsRecursoProponente;
    }

    /**
     * @param mixed $dsRecursoProponente
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setDsRecursoProponente($dsRecursoProponente)
    {
        $this->_dsRecursoProponente = $dsRecursoProponente;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdProponente()
    {
        return $this->_idProponente;
    }

    /**
     * @param mixed $idProponente
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setIdProponente($idProponente)
    {
        $this->_idProponente = $idProponente;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdAvaliadorTecnico()
    {
        return $this->_idAvaliadorTecnico;
    }

    /**
     * @param mixed $idAvaliadorTecnico
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setIdAvaliadorTecnico($idAvaliadorTecnico)
    {
        $this->_idAvaliadorTecnico = $idAvaliadorTecnico;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtAvaliacaoTecnica()
    {
        return $this->_dtAvaliacaoTecnica;
    }

    /**
     * @param mixed $dtAvaliacaoTecnica
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setDtAvaliacaoTecnica($dtAvaliacaoTecnica)
    {
        $this->_dtAvaliacaoTecnica = $dtAvaliacaoTecnica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDsAvaliacaoTecnica()
    {
        return $this->_dsAvaliacaoTecnica;
    }

    /**
     * @param mixed $dsAvaliacaoTecnica
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setDsAvaliacaoTecnica($dsAvaliacaoTecnica)
    {
        $this->_dsAvaliacaoTecnica = $dsAvaliacaoTecnica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTpRecurso()
    {
        return $this->_tpRecurso;
    }

    /**
     * @param mixed $tpRecurso
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setTpRecurso($tpRecurso)
    {
        $this->_tpRecurso = $tpRecurso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTpSolicitacao()
    {
        return $this->_tpSolicitacao;
    }

    /**
     * @param mixed $tpSolicitacao
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setTpSolicitacao($tpSolicitacao)
    {
        $this->_tpSolicitacao = $tpSolicitacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStAtendimento()
    {
        return $this->_stAtendimento;
    }

    /**
     * @param mixed $stAtendimento
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setStAtendimento($stAtendimento)
    {
        $this->_stAtendimento = $stAtendimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdArquivo()
    {
        return $this->_idArquivo;
    }

    /**
     * @param mixed $idArquivo
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setIdArquivo($idArquivo)
    {
        $this->_idArquivo = $idArquivo;
        return $this;
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
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
        return $this;
    }


}