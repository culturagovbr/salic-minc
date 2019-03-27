<?php

class Readequacao_Model_TbTransferenciaRecursosEntreProjetos extends MinC_Db_Model
{
    protected $idPronacTransferidor;
    protected $PronacTransferidor;
    protected $NomeProjetoTranferidor;
    protected $idPronacRecebedor;
    protected $PronacRecebedor;
    protected $NomeProjetoRecedor;
    protected $dtRecebimento;
    protected $vlRecebido;

    public function __construct($params)
    {
        $this->idPronacTransferidor = $params['idPronacTransferidor'];
        $this->PronacTransferidor = $params['PronacTransferidor'];
        $this->NomeProjetoTranferidor = $params['NomeProjetoTranferidor'];
        $this->idPronacRecebedor = $params['idPronacRecebedor'];
        $this->PronacRecebedor = $params['PronacRecebedor'];
        $this->NomeProjetoRecedor = $params['NomeProjetoRecedor'];
        $this->dtRecebimento = Data::tratarDataZend($params['dtRecebimento'], 'brasileiro');
        $this->vlRecebido = $params['vlRecebido'];
    }

    /**
     * @return int
     */
    public function getIdPronacTransferidor()
    {
        return $this->idPronacTransferidor;
    }

    /**
     * @param int $idPronacTransferidor
     */
    public function setIdPronacTransferidor($idPronacTransferidor)
    {
        $this->idPronacTransferidor = $idPronacTransferidor;
    }

    /**
     * @return int
     */
    public function getPronacTransferidor()
    {
        return $this->PronacTransferidor;
    }

    /**
     * @param int $PronacTransferidor
     */
    public function setPronacTransferidor($PronacTransferidor)
    {
        $this->PronacTransferidor = $PronacTransferidor;
    }

    /**
     * @return string
     */
    public function getNomeProjetoTranferidor()
    {
        return $this->NomeProjetoTranferidor;
    }

    /**
     * @param string $NomeProjetoTranferidor
     */
    public function setNomeProjetoTranferidor($NomeProjetoTranferidor)
    {
        $this->NomeProjetoTranferidor = $NomeProjetoTranferidor;
    }

    /**
     * @return int
     */
    public function getIdPronacRecebedor()
    {
        return $this->idPronacRecebedor;
    }

    /**
     * @param int $idPronacRecebedor
     */
    public function setIdPronacRecebedor($idPronacRecebedor)
    {
        $this->idPronacRecebedor = $idPronacRecebedor;
    }

    /**
     * @return int
     */
    public function getPronacRecebedor()
    {
        return $this->PronacRecebedor;
    }

    /**
     * @param int $PronacRecebedor
     */
    public function setPronacRecebedor($PronacRecebedor)
    {
        $this->PronacRecebedor = $PronacRecebedor;
    }

    /**
     * @return string
     */
    public function getNomeProjetoRecedor()
    {
        return $this->NomeProjetoRecedor;
    }

    /**
     * @param string $NomeProjetoRecedor
     */
    public function setNomeProjetoRecedor($NomeProjetoRecedor)
    {
        $this->NomeProjetoRecedor = $NomeProjetoRecedor;
    }

    /**
     * @return DateTime
     */
    public function getDtRecebimento()
    {
        return $this->dtRecebimento;
    }

    /**
     * @param DateTime $dtRecebimento
     */
    public function setDtRecebimento($dtRecebimento)
    {
        $this->dtRecebimento = $dtRecebimento;
    }

    /**
     * @return float
     */
    public function getVlRecebido()
    {
        return $this->vlRecebido;
    }

    /**
     * @param float $vlRecebido
     */
    public function setVlRecebido($vlRecebido)
    {
        $this->vlRecebido = $vlRecebido;
    }
}
