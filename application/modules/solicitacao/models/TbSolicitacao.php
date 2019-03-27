<?php

class Solicitacao_Model_TbSolicitacao extends MinC_Db_Model
{
    protected $_idSolicitacao;
    protected $_idPronac;
    protected $_idProjeto;
    protected $_idOrgao;
    protected $_idAgente;
    protected $_dtSolicitacao;
    protected $_dsSolicitacao;
    protected $_idTecnico;
    protected $_dtResposta;
    protected $_dsResposta;
    protected $_idDocumento;
    protected $_siEncaminhamento;
    protected $_idSolicitante;
    protected $_stLeitura;
    protected $_stEstado;
    protected $_dtEncaminhamento;
    protected $_idDocumentoResposta;

    const SITUACAO_ENCAMINHAMENTO_CADASTRADA = 12;
    const SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC = 1;
    const SITUACAO_ENCAMINHAMENTO_FINALIZADA_MINC = 15;
    const SITUACAO_ENCAMINHAMENTO_RASCUNHO = 0;
    const ESTADO_INATIVO = 0;
    const ESTADO_ATIVO = 1;

    /**
     * @return mixed
     */
    public function getIdSolicitacao()
    {
        return $this->_idSolicitacao;
    }

    /**
     * @param mixed $idSolicitacao
     */
    public function setIdSolicitacao($idSolicitacao)
    {
        if(!empty($idSolicitacao))
            $this->_idSolicitacao = $idSolicitacao;
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
        if(!empty($idPronac))
            $this->_idPronac = $idPronac;
    }

    /**
     * @return mixed
     */
    public function getIdProjeto()
    {
        return $this->_idProjeto;
    }

    /**
     * @param mixed $idProjeto
     */
    public function setIdProjeto($idProjeto)
    {
        if(!empty($idProjeto))
            $this->_idProjeto = $idProjeto;

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
    public function setIdOrgao($idOrgao)
    {
        $this->_idOrgao = $idOrgao;
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
     */
    public function setIdAgente($idAgente)
    {
        $this->_idAgente = $idAgente;
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
    public function getDtSolicitacao()
    {
        return $this->_dtSolicitacao;
    }

    /**
     * @param mixed $dtSolicitacao
     */
    public function setDtSolicitacao($dtSolicitacao)
    {
        $this->_dtSolicitacao = $dtSolicitacao;
    }

    /**
     * @return mixed
     */
    public function getDsSolicitacao()
    {
        return $this->_dsSolicitacao;
    }

    /**
     * @param mixed $dsSolicitacao
     */
    public function setDsSolicitacao($dsSolicitacao)
    {
        $this->_dsSolicitacao = $dsSolicitacao;
    }

    /**
     * @return mixed
     */
    public function getIdTecnico()
    {
        return $this->_idTecnico;
    }

    /**
     * @param mixed $idTecnico
     */
    public function setIdTecnico($idTecnico)
    {
        $this->_idTecnico = $idTecnico;
    }

    /**
     * @return mixed
     */
    public function getDtResposta()
    {
        return $this->_dtResposta;
    }

    /**
     * @param mixed $dtResposta
     */
    public function setDtResposta($dtResposta)
    {
        $this->_dtResposta = $dtResposta;
    }

    /**
     * @return mixed
     */
    public function getDsResposta()
    {
        return $this->_dsResposta;
    }

    /**
     * @param mixed $dsResposta
     */
    public function setDsResposta($dsResposta)
    {
        $this->_dsResposta = $dsResposta;
    }

    /**
     * @return mixed
     */
    public function getIdDocumento()
    {
        return $this->_idDocumento;
    }

    /**
     * @param mixed $idDocumento
     */
    public function setIdDocumento($idDocumento)
    {
        $this->_idDocumento = $idDocumento;
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
    public function setSiEncaminhamento($siEncaminhamento)
    {
        $this->_siEncaminhamento = $siEncaminhamento;
    }

    /**
     * @return mixed
     */
    public function getStLeitura()
    {
        return $this->_stLeitura;
    }

    /**
     * @param mixed $stLeitura
     */
    public function setStLeitura($stLeitura)
    {
        $this->_stLeitura = $stLeitura;
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
    public function getDtEncaminhamento()
    {
        return $this->_dtEncaminhamento;
    }

    /**
     * @param mixed $dtEncaminhamento
     */
    public function setDtEncaminhamento($dtEncaminhamento)
    {
        $this->_dtEncaminhamento = $dtEncaminhamento;
    }

    /**
     * @return mixed
     */
    public function getIdDocumentoResposta()
    {
        return $this->_idDocumentoResposta;
    }

    /**
     * @param mixed $idDocumentoResposta
     */
    public function setIdDocumentoResposta($idDocumentoResposta)
    {
        $this->_idDocumentoResposta = $idDocumentoResposta;
    }

}
