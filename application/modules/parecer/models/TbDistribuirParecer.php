<?php

class Parecer_Model_TbDistribuirParecer  extends MinC_Db_Model
{
    private $_idDistribuirParecer;
    private $_idPRONAC;
    private $_idProduto;
    private $_TipoAnalise;
    private $_idOrgao;
    private $_DtEnvio;
    private $_idAgenteParecerista;
    private $_DtDistribuicao;
    private $_DtDevolucao;
    private $_Observacao;
    private $_stEstado;
    private $_stPrincipal;
    private $_FecharAnalise;
    private $_DtRetorno;
    private $_idUsuario;
    private $_stDiligenciado;
    private $_siEncaminhamento;
    private $_siAnalise;
    private $_idOrgaoOrigem;

    const FECHAR_ANALISE_ABERTA = 0;
    const FECHAR_ANALISE_FECHADA = 1;
    const FECHAR_ANALISE_DEVOLVIDA_PARA_REANALISE = 2;
    const FECHAR_ANALISE_EM_VALIDACAO = 3;
    const FECHAR_ANALISE_ASSINATURA = 4;

    const ST_ESTADO_ATIVO = 0;
    const ST_ESTADO_INATIVO = 1;

    const SI_ANALISE_AGUARDANDO_ANALISE = 0;
    const SI_ANALISE_EM_ANALISE = 1;
    const SI_ANALISE_ANALISADO = 2;
    const SI_ANALISE_FINALIZADO = 3;
    const SI_ANALISE_EM_VALIDACAO = 4;
    const SI_ANALISE_VALIDADO = 5;

    /**
     * @return mixed
     */
    public function getIdDistribuirParecer()
    {
        return $this->_idDistribuirParecer;
    }

    /**
     * @param mixed $idDistribuirParecer
     */
    public function setIdDistribuirParecer($idDistribuirParecer): void
    {
        $this->_idDistribuirParecer = $idDistribuirParecer;
    }

    /**
     * @return mixed
     */
    public function getIdPRONAC()
    {
        return $this->_idPRONAC;
    }

    /**
     * @param mixed $idPRONAC
     */
    public function setIdPRONAC($idPRONAC): void
    {
        $this->_idPRONAC = $idPRONAC;
    }

    /**
     * @return mixed
     */
    public function getIdProduto()
    {
        return $this->_idProduto;
    }

    /**
     * @param mixed $idProduto
     */
    public function setIdProduto($idProduto): void
    {
        $this->_idProduto = $idProduto;
    }

    /**
     * @return mixed
     */
    public function getTipoAnalise()
    {
        return $this->_TipoAnalise;
    }

    /**
     * @param mixed $TipoAnalise
     */
    public function setTipoAnalise($TipoAnalise): void
    {
        $this->_TipoAnalise = $TipoAnalise;
    }

    /**
     * @return mixed
     */
    public function getIdOrgao()
    {
        return $this->_idOrgao;
    }

    /**
     * @param mixed $idOrgao
     */
    public function setIdOrgao($idOrgao): void
    {
        $this->_idOrgao = $idOrgao;
    }

    /**
     * @return mixed
     */
    public function getDtEnvio()
    {
        return $this->_DtEnvio;
    }

    /**
     * @param mixed $DtEnvio
     */
    public function setDtEnvio($DtEnvio): void
    {
        $this->_DtEnvio = $DtEnvio;
    }

    /**
     * @return mixed
     */
    public function getIdAgenteParecerista()
    {
        return $this->_idAgenteParecerista;
    }

    /**
     * @param mixed $idAgenteParecerista
     */
    public function setIdAgenteParecerista($idAgenteParecerista): void
    {
        $this->_idAgenteParecerista = $idAgenteParecerista;
    }

    /**
     * @return mixed
     */
    public function getDtDistribuicao()
    {
        return $this->_DtDistribuicao;
    }

    /**
     * @param mixed $DtDistribuicao
     */
    public function setDtDistribuicao($DtDistribuicao): void
    {
        $this->_DtDistribuicao = $DtDistribuicao;
    }

    /**
     * @return mixed
     */
    public function getDtDevolucao()
    {
        return $this->_DtDevolucao;
    }

    /**
     * @param mixed $DtDevolucao
     */
    public function setDtDevolucao($DtDevolucao): void
    {
        $this->_DtDevolucao = $DtDevolucao;
    }

    /**
     * @return mixed
     */
    public function getObservacao()
    {
        return $this->_Observacao;
    }

    /**
     * @param mixed $Observacao
     */
    public function setObservacao($Observacao): void
    {
        $this->_Observacao = $Observacao;
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
    public function setStEstado($stEstado): void
    {
        $this->_stEstado = $stEstado;
    }

    /**
     * @return mixed
     */
    public function getStPrincipal()
    {
        return $this->_stPrincipal;
    }

    /**
     * @param mixed $stPrincipal
     */
    public function setStPrincipal($stPrincipal): void
    {
        $this->_stPrincipal = $stPrincipal;
    }

    /**
     * @return mixed
     */
    public function getFecharAnalise()
    {
        return $this->_FecharAnalise;
    }

    /**
     * @param mixed $FecharAnalise
     */
    public function setFecharAnalise($FecharAnalise): void
    {
        $this->_FecharAnalise = $FecharAnalise;
    }

    /**
     * @return mixed
     */
    public function getDtRetorno()
    {
        return $this->_DtRetorno;
    }

    /**
     * @param mixed $DtRetorno
     */
    public function setDtRetorno($DtRetorno): void
    {
        $this->_DtRetorno = $DtRetorno;
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
     */
    public function setIdUsuario($idUsuario): void
    {
        $this->_idUsuario = $idUsuario;
    }

    /**
     * @return mixed
     */
    public function getStDiligenciado()
    {
        return $this->_stDiligenciado;
    }

    /**
     * @param mixed $stDiligenciado
     */
    public function setStDiligenciado($stDiligenciado): void
    {
        $this->_stDiligenciado = $stDiligenciado;
    }

    /**
     * @return mixed
     */
    public function getSiEncaminhamento()
    {
        return $this->_siEncaminhamento;
    }

    /**
     * @param mixed $siEncaminhamento
     */
    public function setSiEncaminhamento($siEncaminhamento): void
    {
        $this->_siEncaminhamento = $siEncaminhamento;
    }

    /**
     * @return mixed
     */
    public function getSiAnalise()
    {
        return $this->_siAnalise;
    }

    /**
     * @param mixed $siAnalise
     */
    public function setSiAnalise($siAnalise): void
    {
        $this->_siAnalise = $siAnalise;
    }

    /**
     * @return mixed
     */
    public function getIdOrgaoOrigem()
    {
        return $this->_idOrgaoOrigem;
    }

    /**
     * @param mixed $idOrgaoOrigem
     */
    public function setIdOrgaoOrigem($idOrgaoOrigem): void
    {
        $this->_idOrgaoOrigem = $idOrgaoOrigem;
    }
}
