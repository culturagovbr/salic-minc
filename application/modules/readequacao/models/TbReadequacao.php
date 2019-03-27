<?php

class Readequacao_Model_TbReadequacao extends MinC_Db_Model
{

    protected $_idReadequacao;
    protected $_idPronac;
    protected $_idTipoReadequacao;
    protected $_dtSolicitacao;
    protected $_idSolicitante;
    protected $_dsJustificativa;
    protected $_dsSolicitacao;
    protected $_idDocumento;
    protected $_idAvaliador;
    protected $_dtAvaliador;
    protected $_dsAvaliacao;
    protected $_stAtendimento;
    protected $_siEncaminhamento;
    protected $_stAnalise;
    protected $_idNrReuniao;
    protected $_stEstado;
    protected $_dtEnvio;

    public function setIdReadequacao($idReadequacao)
    {
        $this->_idReadequacao = $idReadequacao;
    }

    public function getIdReadequacao()
    {
        return $this->_idReadequacao;
    }
    
    public function setIdPronac($idPronac)
    {
        $this->_idPronac = $idPronac;
    }
    
    public function getIdPronac()
    {
        return $this->_idPronac;
    }
    
    public function setIdTipoReadequacao($idTipoReadequacao)
    {
        $this->_idTipoReadequacao = $idTipoReadequacao;
    }
    public function getIdTipoReadequacao()
    {
        return $this->_idTipoReadequacao;
    }

    public function setDtSolicitacao($dtSolicitacao)
    {
        $this->_dtSolicitacao = $dtSolicitacao;
    }

    public function getDtSolicitacao()
    {
        return $this->_dtSolicitacao;
    }

    public function setIdSolicitante($idSolicitante)
    {
        $this->_idSolicitante = $idSolicitante;
    }

    public function getIdSolicitante()
    {
        return $this->_idSolicitante;
    }

    public function setDsJustificativa($dsJustificativa)
    {
        $this->_dsJustificativa = $dsJustificativa;
    }

    public function getDsJustificativa()
    {
        return $this->_dsJustificativa;
    }

    public function setDsSolicitacao($dsSolicitacao)
    {
        $this->_dsSolicitacao = $dsSolicitacao;
    }

    public function getDsSolicitacao()
    {
        return $this->_dsSolicitacao;
    }

    public function setIdDocumento($idDocumento)
    {
        $this->_idDocumento = $idDocumento;
    }

    public function getIdDocumento()
    {
        return $this->_idDocumento;
    }

    public function setIdAvaliador($idAvaliador)
    {
        $this->_idAvaliador = $idAvaliador;
    }

    public function getIdAvaliador()
    {
        return $this->_idAvaliador;
    }

    public function setDtAvaliador($dtAvaliador)
    {
        $this->_dtAvaliador = $dtAvaliador;
    }
    
    public function getDtAvaliador()
    {
        return $this->_dtAvaliador;
    }
    
    public function setDsAvaliacao($dsAvaliacao)
    {
        $this->_dsAvaliacao = $dsAvaliacao;
    }
    
    public function getDsAvaliacao()
    {
        return $this->_dsAvaliacao;
    }
    
    public function setStAtendimento($stAtendimento)
    {
        $this->_stAtendimento = $stAtendimento;
    }
    
    public function getStAtendimento()
    {
        return $this->_stAtendimento;
    }
    
    public function setSiEncaminhamento($siEncaminhamento)
    {
        $this->_siEncaminhamento = $siEncaminhamento;
    }
    
    public function getSiEncaminhamento()
    {
        return $this->_siEncaminhamento;
    }
    
    public function setStAnalise($stAnalise)
    {
        $this->_stAnalise = $stAnalise;
    }
    
    public function getStAnalise()
    {
        return $this->_stAnalise;
    }
    
    public function setIdNrReuniao($idNrReuniao)
    {
        $this->_idNrReuniao = $idNrReuniao;
    }
    
    public function getIdNrReuniao()
    {
        return $this->_idNrReuniao;
    }
    
    public function setStEstado($stEstado)
    {
        $this->_stEstado = $stEstado;
    }
    
    public function getStEstado()
    {
        return $this->_stEstado;
    }
    
    public function setDtEnvio($dtEnvio)
    {
        $this->__dtEnvio = $dtEnvio;
    }
    
    public function getDtEnvio()
    {
        return $this->_dtEnvio;
    }   
}