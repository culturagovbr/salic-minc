<?php

class Proposta_Model_TbPlanilhaProposta extends MinC_Db_Model
{
    protected $_idPlanilhaProposta;
    protected $_idProjeto;
    protected $_idProduto;
    protected $_idEtapa;
    protected $_idPlanilhaItem;
    protected $_Descricao;
    protected $_Unidade;
    protected $_Quantidade;
    protected $_Ocorrencia;
    protected $_ValorUnitario;
    protected $_QtdeDias;
    protected $_TipoDespesa;
    protected $_TipoPessoa;
    protected $_Contrapartida;
    protected $_FonteRecurso;
    protected $_UfDespesa;
    protected $_MunicipioDespesa;
    protected $_idUsuario;
    protected $_dsJustificativa;
    protected $_stCustoPraticado;

    /**
     * @return mixed
     */
    public function getIdPlanilhaProposta()
    {
        return $this->_idPlanilhaProposta;
    }

    /**
     * @param mixed $idPlanilhaProposta
     */
    public function setIdPlanilhaProposta($idPlanilhaProposta)
    {
        $this->_idPlanilhaProposta = $idPlanilhaProposta;
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
        $this->_idProjeto = $idProjeto;
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
    public function getIdEtapa()
    {
        return $this->_idEtapa;
    }

    /**
     * @param mixed $idEtapa
     */
    public function setIdEtapa($idEtapa)
    {
        $this->_idEtapa = $idEtapa;
    }

    /**
     * @return mixed
     */
    public function getIdPlanilhaItem()
    {
        return $this->_idPlanilhaItem;
    }

    /**
     * @param mixed $idPlanilhaItem
     */
    public function setIdPlanilhaItem($idPlanilhaItem)
    {
        $this->_idPlanilhaItem = $idPlanilhaItem;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->_Descricao;
    }

    /**
     * @param mixed $Descricao
     */
    public function setDescricao($Descricao)
    {
        $this->_Descricao = $Descricao;
    }

    /**
     * @return mixed
     */
    public function getUnidade()
    {
        return $this->_Unidade;
    }

    /**
     * @param mixed $Unidade
     */
    public function setUnidade($Unidade)
    {
        $this->_Unidade = $Unidade;
    }

    /**
     * @return mixed
     */
    public function getQuantidade()
    {
        return $this->_Quantidade;
    }

    /**
     * @param mixed $Quantidade
     */
    public function setQuantidade($Quantidade)
    {
        $this->_Quantidade = $Quantidade;
    }

    /**
     * @return mixed
     */
    public function getOcorrencia()
    {
        return $this->_Ocorrencia;
    }

    /**
     * @param mixed $Ocorrencia
     */
    public function setOcorrencia($Ocorrencia)
    {
        $this->_Ocorrencia = $Ocorrencia;
    }

    /**
     * @return mixed
     */
    public function getValorUnitario()
    {
        return $this->_ValorUnitario;
    }

    /**
     * @param mixed $ValorUnitario
     */
    public function setValorUnitario($ValorUnitario)
    {
        $this->_ValorUnitario = $ValorUnitario;
    }

    /**
     * @return mixed
     */
    public function getQtdeDias()
    {
        return $this->_QtdeDias;
    }

    /**
     * @param mixed $QtdeDias
     */
    public function setQtdeDias($QtdeDias)
    {
        $this->_QtdeDias = $QtdeDias;
    }

    /**
     * @return mixed
     */
    public function getTipoDespesa()
    {
        return $this->_TipoDespesa;
    }

    /**
     * @param mixed $TipoDespesa
     */
    public function setTipoDespesa($TipoDespesa)
    {
        $this->_TipoDespesa = $TipoDespesa;
    }

    /**
     * @return mixed
     */
    public function getTipoPessoa()
    {
        return $this->_TipoPessoa;
    }

    /**
     * @param mixed $TipoPessoa
     */
    public function setTipoPessoa($TipoPessoa)
    {
        $this->_TipoPessoa = $TipoPessoa;
    }

    /**
     * @return mixed
     */
    public function getContrapartida()
    {
        return $this->_Contrapartida;
    }

    /**
     * @param mixed $Contrapartida
     */
    public function setContrapartida($Contrapartida)
    {
        $this->_Contrapartida = $Contrapartida;
    }

    /**
     * @return mixed
     */
    public function getFonteRecurso()
    {
        return $this->_FonteRecurso;
    }

    /**
     * @param mixed $FonteRecurso
     */
    public function setFonteRecurso($FonteRecurso)
    {
        $this->_FonteRecurso = $FonteRecurso;
    }

    /**
     * @return mixed
     */
    public function getUfDespesa()
    {
        return $this->_UfDespesa;
    }

    /**
     * @param mixed $UfDespesa
     */
    public function setUfDespesa($UfDespesa)
    {
        $this->_UfDespesa = $UfDespesa;
    }

    /**
     * @return mixed
     */
    public function getMunicipioDespesa()
    {
        return $this->_MunicipioDespesa;
    }

    /**
     * @param mixed $MunicipioDespesa
     */
    public function setMunicipioDespesa($MunicipioDespesa)
    {
        $this->_MunicipioDespesa = $MunicipioDespesa;
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
    public function setIdUsuario($idUsuario)
    {
        $this->_idUsuario = $idUsuario;
    }

    /**
     * @return mixed
     */
    public function getDsJustificativa()
    {
        return $this->_dsJustificativa;
    }

    /**
     * @param mixed $dsJustificativa
     */
    public function setDsJustificativa($dsJustificativa)
    {
        $this->_dsJustificativa = $dsJustificativa;
    }

    /**
     * @return mixed
     */
    public function getStCustoPraticado()
    {
        return $this->_stCustoPraticado;
    }

    /**
     * @param mixed $stCustoPraticado
     */
    public function setStCustoPraticado($stCustoPraticado)
    {
        $this->_stCustoPraticado = $stCustoPraticado;
    }




}
