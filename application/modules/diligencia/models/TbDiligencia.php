<?php

class Diligencia_Model_TbDiligencia extends MinC_Db_Model
{
    private $_idDiligencia;
    private $_idPronac;
    private $_idTipoDiligencia;
    private $_DtSolicitacao;
    private $_Solicitacao;
    private $_idSolicitante;
    private $_DtResposta;
    private $_Resposta;
    private $_idProponente;
    private $_stEstado;
    private $_idPlanoDistribuicao;
    private $_idArquivo;
    private $_idCodigoDocumentosExigidos;
    private $_idProduto;
    private $_stProrrogacao;
    private $_stEnviado;

    const DILIGENCIA_NA_ANALISE_TECNICA_INICIAL = 124;
    const DILIGENCIA_NA_CNIC_ANALISE_INICIAL = 126;
    const DILIGENCIA_NA_READEQUACAO_TECNICO = 171;
    const DILIGENCIA_NA_FISCALIZACAO = 172;
    const DILIGENCIA_NA_AVALIACAO_RELATORIO_TRIMESTRAL = 173;
    const DILIGENCIA_NA_PRESTACAO_DE_CONTAS = 174;
    const DILIGENCIA_NA_CNIC_READEQUACAO = 178;
    const DILIGENCIA_NA_READEQUACAO_PARECERISTA = 179;
    const DILIGENCIA_NA_MOVIMENTACAO_BANCARIA = 183;
    const DILIGENCIA_NA_AVALIACAO_RELATORIO_FINAL = 180;
    const DILIGENCIA_DE_CHECKLIST_ANALISE = 181;
    const DILIGENCIA_DE_CHECKLIST_READEQUACAO = 182;
    const DILIGENCIA_NO_RECURSO_PARECERISTA = 563;
    const DILIGENCIA_NO_RECURSO_CNIC = 564;
    const DILIGENCIA_NO_RECURSO_TECNICO = 565;
    const DILIGENCIA_NO_COMPLIANCE_DA_COMPROVACAO_FINANCEIRA = 741;
    const DILIGENCIA_DE_NOTIFICACAO_DE_COBRANCA_PRESTACAO_DE_CONTAS = 627;
    const DILIGENCIA_NA_PRESTACAO_DE_CONTAS_TODOS_OS_ITENS = 645;

    /**
     * @return mixed
     */
    public function getIdDiligencia()
    {
        return $this->_idDiligencia;
    }

    /**
     * @param mixed $idDiligencia
     */
    public function setIdDiligencia($idDiligencia)
    {
        $this->_idDiligencia = $idDiligencia;
    }

    /**
     * @return mixed
     */
    public function getIdPronac()
    {
        return $this->_idPronac;
    }

    /**
     * @param mixed $idPronac
     */
    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
    }

    /**
     * @return mixed
     */
    public function getIdTipoDiligencia()
    {
        return $this->_idTipoDiligencia;
    }

    /**
     * @param mixed $idTipoDiligencia
     */
    public function setIdTipoDiligencia($idTipoDiligencia)
    {
        $this->_idTipoDiligencia = $idTipoDiligencia;
    }

    /**
     * @return mixed
     */
    public function getDtSolicitacao()
    {
        return $this->_DtSolicitacao;
    }

    /**
     * @param mixed $DtSolicitacao
     */
    public function setDtSolicitacao($DtSolicitacao)
    {
        $this->_DtSolicitacao = $DtSolicitacao;
    }

    /**
     * @return mixed
     */
    public function getSolicitacao()
    {
        return $this->_Solicitacao;
    }

    /**
     * @param mixed $Solicitacao
     */
    public function setSolicitacao($Solicitacao)
    {
        $this->_Solicitacao = $Solicitacao;
    }

    /**
     * @return mixed
     */
    public function getIdSolicitante()
    {
        return $this->_idSolicitante;
    }

    /**
     * @param mixed $idSolicitante
     */
    public function setIdSolicitante($idSolicitante)
    {
        $this->_idSolicitante = $idSolicitante;
    }

    /**
     * @return mixed
     */
    public function getDtResposta()
    {
        return $this->_DtResposta;
    }

    /**
     * @param mixed $DtResposta
     */
    public function setDtResposta($DtResposta)
    {
        $this->_DtResposta = $DtResposta;
    }

    /**
     * @return mixed
     */
    public function getResposta()
    {
        return $this->_Resposta;
    }

    /**
     * @param mixed $Resposta
     */
    public function setResposta($Resposta)
    {
        $this->_Resposta = $Resposta;
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
     */
    public function setIdProponente($idProponente)
    {
        $this->_idProponente = $idProponente;
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

    /**
     * @return mixed
     */
    public function getIdPlanoDistribuicao()
    {
        return $this->_idPlanoDistribuicao;
    }

    /**
     * @param mixed $idPlanoDistribuicao
     */
    public function setIdPlanoDistribuicao($idPlanoDistribuicao)
    {
        $this->_idPlanoDistribuicao = $idPlanoDistribuicao;
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
     */
    public function setIdArquivo($idArquivo)
    {
        $this->_idArquivo = $idArquivo;
    }

    /**
     * @return mixed
     */
    public function getIdCodigoDocumentosExigidos()
    {
        return $this->_idCodigoDocumentosExigidos;
    }

    /**
     * @param mixed $idCodigoDocumentosExigidos
     */
    public function setIdCodigoDocumentosExigidos($idCodigoDocumentosExigidos)
    {
        $this->_idCodigoDocumentosExigidos = $idCodigoDocumentosExigidos;
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
    public function setIdProduto($idProduto)
    {
        $this->_idProduto = $idProduto;
    }

    /**
     * @return mixed
     */
    public function getStProrrogacao()
    {
        return $this->_stProrrogacao;
    }

    /**
     * @param mixed $stProrrogacao
     */
    public function setStProrrogacao($stProrrogacao)
    {
        $this->_stProrrogacao = $stProrrogacao;
    }

    /**
     * @return mixed
     */
    public function getStEnviado()
    {
        return $this->_stEnviado;
    }

    /**
     * @param mixed $stEnviado
     */
    public function setStEnviado($stEnviado)
    {
        $this->_stEnviado = $stEnviado;
    }


}
