<?php

class Proposta_Model_TbPlanilhaEtapa extends MinC_Db_Model
{
    protected $_idPlanilhaEtapa;
    protected $_Descricao;
    protected $_tpCusto;
    protected $_stEstado;
    protected $_tpGrupo;
    protected $_nrOrdenacao;

    const TIPO_CUSTO_ADMINISTRATIVO = 'A';

    const PRE_PRODUCAO = 1;
    const PRODUCAO = 2;
    const DIVULGACAO_COMERCIALIZACAO = 3;
    const CUSTOS_ADMINISTRATIVOS = 4;
    const RECOLHIMENTOS = 5;
    const ELABORACAO_E_AGENCIAMENTO = 6;
    const POS_PRODUCAO = 7;
    const CUSTOS_VINCULADOS = 8;
    const ASSESSORIA_CONTABIL_E_JURIDICA = 9;
    const CAPTACAO_DE_RECURSOS = 10;

    /**
     * @return mixed
     */
    public function getIdPlanilhaEtapa()
    {
        return $this->_idPlanilhaEtapa;
    }

    /**
     * @param mixed $idPlanilhaEtapa
     */
    public function setIdPlanilhaEtapa($idPlanilhaEtapa)
    {
        $this->_idPlanilhaEtapa = $idPlanilhaEtapa;
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
    public function getTpCusto()
    {
        return $this->_tpCusto;
    }

    /**
     * @param mixed $tpCusto
     */
    public function setTpCusto($tpCusto)
    {
        $this->_tpCusto = $tpCusto;
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
    public function getTpGrupo()
    {
        return $this->_tpGrupo;
    }

    /**
     * @param mixed $tpGrupo
     */
    public function setTpGrupo($tpGrupo)
    {
        $this->_tpGrupo = $tpGrupo;
    }

    /**
     * @return mixed
     */
    public function getNrOrdenacao()
    {
        return $this->_nrOrdenacao;
    }

    /**
     * @param mixed $nrOrdenacao
     */
    public function setNrOrdenacao($nrOrdenacao)
    {
        $this->_nrOrdenacao = $nrOrdenacao;
    }

}
