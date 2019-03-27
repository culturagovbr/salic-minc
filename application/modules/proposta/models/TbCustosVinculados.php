<?php

class Proposta_Model_TbCustosVinculados extends MinC_Db_Model
{
    protected $_idCustosVinculados;
    protected $_idProjeto;
    protected $_idPlanilhaItem;
    protected $_dtCadastro;
    protected $_dsObservacao;
    protected $_idUsuario;
    protected $_pcCalculo;

    const ID_CUSTO_ADMINISTRATIVO = 8197;
    const ID_DIREITOS_AUTORAIS = 40;
    const ID_CONTROLE_E_AUDITORIA = 8199;
    const ID_DIVULGACAO = 8198;
    const ID_REMUNERACAO_CAPTACAO = 5249;

    const PERCENTUAL_CUSTO_ADMINISTRATIVO = 15;
//    const LIMITE_CONTROLE_E_AUDITORIA = 100000;
//    const PERCENTUAL_DIREITOS_AUTORAIS = 10;
//    const PERCENTUAL_CONTROLE_E_AUDITORIA = 10;

    const PERCENTUAL_DIVULGACAO_ATE_VALOR_LIMITE = 30;
    const PERCENTUAL_DIVULGACAO_MAIOR_QUE_VALOR_LIMITE = 20;
    const VALOR_LIMITE_DIVULGACAO = 300000;

    # PADRAO
    const PERCENTUAL_PADRAO_REMUNERACAO_CAPTACAO_DE_RECURSOS = 10;
    const LIMITE_PADRAO_CAPTACAO_DE_RECURSOS = 150000;

    # NORTE NORDESTE E CENTRO-OESTE
    const PERCENTUAL_REGIOES_N_NE_CO_REMUNERACAO_CAPTACAO_DE_RECURSOS = 15;
    const LIMITE_REGIOES_N_NE_CO = 172500;

    # SUL E ESTADOS MINAS GERAIS E ESPIRITO SANTO
    const PERCENTUAL_UFS_RS_PR_SC_MG_ES_REMUNERACAO_CAPTACAO_DE_RECURSOS = 12.5;
    const LIMITE_UFS_RS_PR_SC_MG_ES = 168750;

    /**
     * @return mixed
     */
    public function getIdCustosVinculados()
    {
        return $this->_idCustosVinculados;
    }

    /**
     * @param mixed $idCustosVinculados
     */
    public function setIdCustosVinculados($idCustosVinculados)
    {
        $this->_idCustosVinculados = $idCustosVinculados;
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
    public function getDtCadastro()
    {
        return $this->_dtCadastro;
    }

    /**
     * @param mixed $dtCadastro
     */
    public function setDtCadastro($dtCadastro)
    {
        $this->_dtCadastro = $dtCadastro;
    }

    /**
     * @return mixed
     */
    public function getDsObservacao()
    {
        return $this->_dsObservacao;
    }

    /**
     * @param mixed $dsObservacao
     */
    public function setDsObservacao($dsObservacao)
    {
        $this->_dsObservacao = $dsObservacao;
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
    public function getPcCalculo()
    {
        return $this->_pcCalculo;
    }

    /**
     * @param mixed $pcCalculo
     */
    public function setPcCalculo($pcCalculo)
    {
        $this->_pcCalculo = $pcCalculo;
    }
}
