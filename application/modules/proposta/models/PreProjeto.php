<?php

class Proposta_Model_PreProjeto extends MinC_Db_Model
{
    protected $_idPreProjeto;
    protected $_idAgente;
    protected $_NomeProjeto;
    protected $_Mecanismo;
    protected $_AgenciaBancaria;
    protected $_AreaAbrangencia;
    protected $_DtInicioDeExecucao;
    protected $_DtFinalDeExecucao;
    protected $_Justificativa;
    protected $_NrAtoTombamento;
    protected $_DtAtoTombamento;
    protected $_EsferaTombamento;
    protected $_ResumoDoProjeto;
    protected $_Objetivos;
    protected $_Acessibilidade;
    protected $_DemocratizacaoDeAcesso;
    protected $_EtapaDeTrabalho;
    protected $_FichaTecnica;
    protected $_Sinopse;
    protected $_ImpactoAmbiental;
    protected $_EspecificacaoTecnica;
    protected $_EstrategiadeExecucao;
    protected $_dtAceite;
    protected $_DtArquivamento;
    protected $_stEstado;
    protected $_stDataFixa;
    protected $_stPlanoAnual;
    protected $_idUsuario;
    protected $_stTipoDemanda;
    protected $_idEdital;
    protected $_stProposta;
    protected $_tpProrrogacao;

    /**
     * @return mixed
     */
    public function getIdPreProjeto()
    {
        return $this->_idPreProjeto;
    }

    /**
     * @param mixed $idPreProjeto
     * @return Proposta_Model_PreProjeto
     */
    public function setIdPreProjeto($idPreProjeto)
    {
        $this->_idPreProjeto = $idPreProjeto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdAgente()
    {
        return $this->_idAgente;
    }

    /**
     * @param mixed $idAgente
     * @return Proposta_Model_PreProjeto
     */
    public function setIdAgente($idAgente)
    {
        $this->_idAgente = $idAgente;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNomeProjeto()
    {
        return $this->_NomeProjeto;
    }

    /**
     * @param mixed $NomeProjeto
     * @return Proposta_Model_PreProjeto
     */
    public function setNomeProjeto($NomeProjeto)
    {
        $this->_NomeProjeto = $NomeProjeto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMecanismo()
    {
        return $this->_Mecanismo;
    }

    /**
     * @param mixed $Mecanismo
     * @return Proposta_Model_PreProjeto
     */
    public function setMecanismo($Mecanismo)
    {
        $this->_Mecanismo = $Mecanismo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAgenciaBancaria()
    {
        return $this->_AgenciaBancaria;
    }

    /**
     * @param mixed $AgenciaBancaria
     * @return Proposta_Model_PreProjeto
     */
    public function setAgenciaBancaria($AgenciaBancaria)
    {
        $this->_AgenciaBancaria = $AgenciaBancaria;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAreaAbrangencia()
    {
        return $this->_AreaAbrangencia;
    }

    /**
     * @param mixed $AreaAbrangencia
     * @return Proposta_Model_PreProjeto
     */
    public function setAreaAbrangencia($AreaAbrangencia)
    {
        $this->_AreaAbrangencia = $AreaAbrangencia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtInicioDeExecucao()
    {
        return $this->_DtInicioDeExecucao;
    }

    /**
     * @param mixed $DtInicioDeExecucao
     * @return Proposta_Model_PreProjeto
     */
    public function setDtInicioDeExecucao($DtInicioDeExecucao)
    {
        $this->_DtInicioDeExecucao = $DtInicioDeExecucao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtFinalDeExecucao()
    {
        return $this->_DtFinalDeExecucao;
    }

    /**
     * @param mixed $DtFinalDeExecucao
     * @return Proposta_Model_PreProjeto
     */
    public function setDtFinalDeExecucao($DtFinalDeExecucao)
    {
        $this->_DtFinalDeExecucao = $DtFinalDeExecucao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJustificativa()
    {
        return $this->_Justificativa;
    }

    /**
     * @param mixed $Justificativa
     * @return Proposta_Model_PreProjeto
     */
    public function setJustificativa($Justificativa)
    {
        $this->_Justificativa = $Justificativa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNrAtoTombamento()
    {
        return $this->_NrAtoTombamento;
    }

    /**
     * @param mixed $NrAtoTombamento
     * @return Proposta_Model_PreProjeto
     */
    public function setNrAtoTombamento($NrAtoTombamento)
    {
        $this->_NrAtoTombamento = $NrAtoTombamento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtAtoTombamento()
    {
        return $this->_DtAtoTombamento;
    }

    /**
     * @param mixed $DtAtoTombamento
     * @return Proposta_Model_PreProjeto
     */
    public function setDtAtoTombamento($DtAtoTombamento)
    {
        $this->_DtAtoTombamento = $DtAtoTombamento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEsferaTombamento()
    {
        return $this->_EsferaTombamento;
    }

    /**
     * @param mixed $EsferaTombamento
     * @return Proposta_Model_PreProjeto
     */
    public function setEsferaTombamento($EsferaTombamento)
    {
        $this->_EsferaTombamento = $EsferaTombamento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResumoDoProjeto()
    {
        return $this->_ResumoDoProjeto;
    }

    /**
     * @param mixed $ResumoDoProjeto
     * @return Proposta_Model_PreProjeto
     */
    public function setResumoDoProjeto($ResumoDoProjeto)
    {
        $this->_ResumoDoProjeto = $ResumoDoProjeto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjetivos()
    {
        return $this->_Objetivos;
    }

    /**
     * @param mixed $Objetivos
     * @return Proposta_Model_PreProjeto
     */
    public function setObjetivos($Objetivos)
    {
        $this->_Objetivos = $Objetivos;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcessibilidade()
    {
        return $this->_Acessibilidade;
    }

    /**
     * @param mixed $Acessibilidade
     * @return Proposta_Model_PreProjeto
     */
    public function setAcessibilidade($Acessibilidade)
    {
        $this->_Acessibilidade = $Acessibilidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDemocratizacaoDeAcesso()
    {
        return $this->_DemocratizacaoDeAcesso;
    }

    /**
     * @param mixed $DemocratizacaoDeAcesso
     * @return Proposta_Model_PreProjeto
     */
    public function setDemocratizacaoDeAcesso($DemocratizacaoDeAcesso)
    {
        $this->_DemocratizacaoDeAcesso = $DemocratizacaoDeAcesso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEtapaDeTrabalho()
    {
        return $this->_EtapaDeTrabalho;
    }

    /**
     * @param mixed $EtapaDeTrabalho
     * @return Proposta_Model_PreProjeto
     */
    public function setEtapaDeTrabalho($EtapaDeTrabalho)
    {
        $this->_EtapaDeTrabalho = $EtapaDeTrabalho;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFichaTecnica()
    {
        return $this->_FichaTecnica;
    }

    /**
     * @param mixed $FichaTecnica
     * @return Proposta_Model_PreProjeto
     */
    public function setFichaTecnica($FichaTecnica)
    {
        $this->_FichaTecnica = $FichaTecnica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSinopse()
    {
        return $this->_Sinopse;
    }

    /**
     * @param mixed $Sinopse
     * @return Proposta_Model_PreProjeto
     */
    public function setSinopse($Sinopse)
    {
        $this->_Sinopse = $Sinopse;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpactoAmbiental()
    {
        return $this->_ImpactoAmbiental;
    }

    /**
     * @param mixed $ImpactoAmbiental
     * @return Proposta_Model_PreProjeto
     */
    public function setImpactoAmbiental($ImpactoAmbiental)
    {
        $this->_ImpactoAmbiental = $ImpactoAmbiental;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEspecificacaoTecnica()
    {
        return $this->_EspecificacaoTecnica;
    }

    /**
     * @param mixed $EspecificacaoTecnica
     * @return Proposta_Model_PreProjeto
     */
    public function setEspecificacaoTecnica($EspecificacaoTecnica)
    {
        $this->_EspecificacaoTecnica = $EspecificacaoTecnica;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEstrategiadeExecucao()
    {
        return $this->_EstrategiadeExecucao;
    }

    /**
     * @param mixed $EstrategiadeExecucao
     * @return Proposta_Model_PreProjeto
     */
    public function setEstrategiadeExecucao($EstrategiadeExecucao)
    {
        $this->_EstrategiadeExecucao = $EstrategiadeExecucao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtAceite()
    {
        return $this->_dtAceite;
    }

    /**
     * @param mixed $dtAceite
     * @return Proposta_Model_PreProjeto
     */
    public function setDtAceite($dtAceite)
    {
        $this->_dtAceite = $dtAceite;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtArquivamento()
    {
        return $this->_DtArquivamento;
    }

    /**
     * @param mixed $DtArquivamento
     * @return Proposta_Model_PreProjeto
     */
    public function setDtArquivamento($DtArquivamento)
    {
        $this->_DtArquivamento = $DtArquivamento;
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
     * @return Proposta_Model_PreProjeto
     */
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStDataFixa()
    {
        return $this->_stDataFixa;
    }

    /**
     * @param mixed $stDataFixa
     * @return Proposta_Model_PreProjeto
     */
    public function setStDataFixa($stDataFixa)
    {
        $this->_stDataFixa = $stDataFixa;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStPlanoAnual()
    {
        return $this->_stPlanoAnual;
    }

    /**
     * @param mixed $stPlanoAnual
     * @return Proposta_Model_PreProjeto
     */
    public function setStPlanoAnual($stPlanoAnual)
    {
        $this->_stPlanoAnual = $stPlanoAnual;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdUsuario()
    {
        return $this->_idUsuario;
    }

    /**
     * @param mixed $idUsuario
     * @return Proposta_Model_PreProjeto
     */
    public function setIdUsuario($idUsuario)
    {
        $this->_idUsuario = $idUsuario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStTipoDemanda()
    {
        return $this->_stTipoDemanda;
    }

    /**
     * @param mixed $stTipoDemanda
     * @return Proposta_Model_PreProjeto
     */
    public function setStTipoDemanda($stTipoDemanda)
    {
        $this->_stTipoDemanda = $stTipoDemanda;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdEdital()
    {
        return $this->_idEdital;
    }

    /**
     * @param mixed $idEdital
     * @return Proposta_Model_PreProjeto
     */
    public function setIdEdital($idEdital)
    {
        $this->_idEdital = $idEdital;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStProposta()
    {
        return $this->_stProposta;
    }

    /**
     * @param mixed $stProposta
     * @return Proposta_Model_PreProjeto
     */
    public function setStProposta($stProposta)
    {
        $this->_stProposta = $stProposta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTpProrrogacao()
    {
        return $this->_tpProrrogacao;
    }

    /**
     * @param mixed $tpProrrogacao
     * @return Proposta_Model_PreProjeto
     */
    public function setTpProrrogacao($tpProrrogacao)
    {
        $this->_tpProrrogacao = $tpProrrogacao;
        return $this;
    }
}