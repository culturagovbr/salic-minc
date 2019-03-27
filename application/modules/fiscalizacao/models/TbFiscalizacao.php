<?php
class Fiscalizacao_Model_TbFiscalizacao extends MinC_Db_Model
{

    protected $_idFiscalizacao;
    protected $_IdPRONAC;
    protected $_dtInicioFiscalizacaoProjeto;
    protected $_dtFimFiscalizacaoProjeto;
    protected $_dtRespostaSolicitada;
    protected $_dsFiscalizacaoProjeto;
    protected $_tpDemandante;
    protected $_stFiscalizacaoProjeto;
    protected $_idAgente;
    protected $_idSolicitante;
    protected $_idUsuarioInterno;

    const ST_FISCALIZACAO_INICIADA = 0;
    const ST_FISCALIZACAO_OFICIALIZADA = 1;
    const ST_FISCALIZACAO_COM_COORDENADOR = 2;
    const ST_FISCALIZACAO_CONCLUIDA = 3;

    /**
     * @return mixed
     */
    public function getIdFiscalizacao()
    {
        return $this->_idFiscalizacao;
    }

    /**
     * @param mixed $idFiscalizacao
     */
    public function setIdFiscalizacao($idFiscalizacao): void
    {
        $this->_idFiscalizacao = $idFiscalizacao;
    }

    /**
     * @return mixed
     */
    public function getIdPRONAC()
    {
        return $this->_IdPRONAC;
    }

    /**
     * @param mixed $IdPRONAC
     */
    public function setIdPRONAC($IdPRONAC): void
    {
        $this->_IdPRONAC = $IdPRONAC;
    }

    /**
     * @return mixed
     */
    public function getDtInicioFiscalizacaoProjeto()
    {
        return $this->_dtInicioFiscalizacaoProjeto;
    }

    /**
     * @param mixed $dtInicioFiscalizacaoProjeto
     */
    public function setDtInicioFiscalizacaoProjeto($dtInicioFiscalizacaoProjeto): void
    {
        $this->_dtInicioFiscalizacaoProjeto = $dtInicioFiscalizacaoProjeto;
    }

    /**
     * @return mixed
     */
    public function getDtFimFiscalizacaoProjeto()
    {
        return $this->_dtFimFiscalizacaoProjeto;
    }

    /**
     * @param mixed $dtFimFiscalizacaoProjeto
     */
    public function setDtFimFiscalizacaoProjeto($dtFimFiscalizacaoProjeto): void
    {
        $this->_dtFimFiscalizacaoProjeto = $dtFimFiscalizacaoProjeto;
    }

    /**
     * @return mixed
     */
    public function getDtRespostaSolicitada()
    {
        return $this->_dtRespostaSolicitada;
    }

    /**
     * @param mixed $dtRespostaSolicitada
     */
    public function setDtRespostaSolicitada($dtRespostaSolicitada): void
    {
        $this->_dtRespostaSolicitada = $dtRespostaSolicitada;
    }

    /**
     * @return mixed
     */
    public function getDsFiscalizacaoProjeto()
    {
        return $this->_dsFiscalizacaoProjeto;
    }

    /**
     * @param mixed $dsFiscalizacaoProjeto
     */
    public function setDsFiscalizacaoProjeto($dsFiscalizacaoProjeto): void
    {
        $this->_dsFiscalizacaoProjeto = $dsFiscalizacaoProjeto;
    }

    /**
     * @return mixed
     */
    public function getTpDemandante()
    {
        return $this->_tpDemandante;
    }

    /**
     * @param mixed $tpDemandante
     */
    public function setTpDemandante($tpDemandante): void
    {
        $this->_tpDemandante = $tpDemandante;
    }

    /**
     * @return mixed
     */
    public function getStFiscalizacaoProjeto()
    {
        return $this->_stFiscalizacaoProjeto;
    }

    /**
     * @param mixed $stFiscalizacaoProjeto
     */
    public function setStFiscalizacaoProjeto($stFiscalizacaoProjeto): void
    {
        $this->_stFiscalizacaoProjeto = $stFiscalizacaoProjeto;
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
    public function setIdAgente($idAgente): void
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
    public function setIdSolicitante($idSolicitante): void
    {
        $this->_idSolicitante = $idSolicitante;
    }

    /**
     * @return mixed
     */
    public function getIdUsuarioInterno()
    {
        return $this->_idUsuarioInterno;
    }

    /**
     * @param mixed $idUsuarioInterno
     */
    public function setIdUsuarioInterno($idUsuarioInterno): void
    {
        $this->_idUsuarioInterno = $idUsuarioInterno;
    }

    public function obterStatusSituacao(): String
    {
        switch ($this->_stFiscalizacaoProjeto) {
            case self::ST_FISCALIZACAO_INICIADA :
            case self::ST_FISCALIZACAO_OFICIALIZADA :
                $status = 'Em andamento';
                break;
            case self::ST_FISCALIZACAO_COM_COORDENADOR :
                $status = 'Em análise pelo Coordenador';
                break;
            case self::ST_FISCALIZACAO_CONCLUIDA :
                $status = 'Conclu&iacute;da';
                break;
            default:
                $status = 'Sem fiscaliza&ccedil;&atilde;o';
                break;
        }

        return $status;
    }


}
