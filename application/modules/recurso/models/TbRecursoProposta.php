<?php

class Recurso_Model_TbRecursoProposta extends MinC_Db_Model
{
    protected $_idRecursoProposta;
    protected $_idPreProjeto;
    protected $_dtRecursoProponente;
    /**
     * @var $_dsRecursoProponente Motivo da solicitação do Proponente
     */
    protected $_dsRecursoProponente;
    /**
     * @var $_idProponente Proponente que está solicitando o recurso (idAgente)
     */
    protected $_idProponente;
    /**
     * @var $_idAvaliadorTecnico C&oacute;digo do usuário que está avaliando o recurso (Tabelas.dbo.Usuarios.usu_codigo)
     */
    protected $_idAvaliadorTecnico;
    protected $_dtAvaliacaoTecnica;
    protected $_dsAvaliacaoTecnica;
    /**
     * @var $_tpRecurso
            tpRecurso => 1 - Pedido de reconsideração
            tpRecurso => 2 - Recurso
     */
    protected $_tpRecurso;
    /**
     * @var $_tpSolicitacao
            DR => Desistência do Prazo Recursal
            EN => Enquadramento
     */
    protected $_tpSolicitacao;
    /**
     * @var $_stAtendimento
            'N' => Sem avaliação
            'I' => Quando é indeferido
            'D' => Quando é deferido (movimenta para frente)
     */
    protected $_stAtendimento;
    protected $_idArquivo;
    /**
     * @var $_stAtivo
             0 - Registro Atual
             1 - Registro Inativo
     */
    protected $_stAtivo;

    /**
     * @var $_stRascunho
     *      0 - Enviado
     *      1 - Rascunho Salvo
     */
    protected $_stRascunho;

    /**
     * Quando o proponente o proponente não concordar com o enquadramento
     * ele poderá entrar um pedido de reconsideração.
     */
    const TIPO_RECURSO_PEDIDO_DE_RECONSIDERACAO = 1;
    /**
     * Quando o Avalialiador não concordar com o pedido de reconsideração
     * (faz o indeferimento) o proponente poderá entrar com outro pedido.
     */
    const TIPO_RECURSO_RECURSO = 2;
    const TIPO_SOLICITACAO_DESISTENCIA_DO_PRAZO_RECURSAL = 'DR';
    const TIPO_SOLICITACAO_ENQUADRAMENTO = 'EN';
    const SITUACAO_RECURSO_ATIVO = 0;
    const SITUACAO_RECURSO_INATIVO = 1;
    const SITUACAO_ATENDIMENTO_SEM_AVALIACAO = 'N';
    const SITUACAO_ATENDIMENTO_INDEFERIDO = 'I';
    const SITUACAO_ATENDIMENTO_DEFERIDO = 'D';
    const SITUACAO_RASCUNHO_SALVO = 1;
    const SITUACAO_RASCUNHO_ENVIADO = 0;

    /*
     *  'N' => Sem avaliação
        'I' => Quando é indeferido
        'D' => Quando é deferido (movimenta para frente)
     */
    
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
    public function getStAtivo()
    {
        return $this->_stAtivo;
    }

    /**
     * @param mixed $stEstado
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setStAtivo($stEstado)
    {
        $this->_stAtivo = $stEstado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStRascunho()
    {
        return $this->_stRascunho;
    }

    /**
     * @param mixed $stRascunho
     * @return Recurso_Model_TbRecursoProposta
     */
    public function setStRascunho($stRascunho)
    {
        $this->_stRascunho = $stRascunho;
        return $this;
    }

}