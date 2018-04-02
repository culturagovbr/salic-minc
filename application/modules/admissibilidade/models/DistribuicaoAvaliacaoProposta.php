<?php

class Admissibilidade_Model_DistribuicaoAvaliacaoProposta extends MinC_Db_Model
{

    protected $_id_distribuicao_avaliacao_proposta;
    protected $_id_preprojeto;
    protected $_id_orgao_superior;
    protected $_id_perfil;
    protected $_data_distribuicao;
    protected $_avaliacao_atual;

    const AVALIACAO_ATUAL_INATIVA = 0;
    const AVALIACAO_ATUAL_ATIVA = 1;

    /**
     * @return mixed
     */
    public function getAvaliacaoAtual()
    {
        return $this->_avaliacao_atual;
    }

    /**
     * @param mixed $avaliacao_atual
     * @return Admissibilidade_Model_DistribuicaoAvaliacaoProposta
     */
    public function setAvaliacaoAtual($avaliacao_atual)
    {
        $this->_avaliacao_atual = $avaliacao_atual;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdDistribuicaoAvaliacaoProposta()
    {
        return $this->_id_distribuicao_avaliacao_proposta;
    }

    /**
     * @param mixed $id_distribuicao_avaliacao_proposta
     * @return Admissibilidade_Model_DistribuicaoAvaliacaoProposta
     */
    public function setIdDistribuicaoAvaliacaoProposta($id_distribuicao_avaliacao_proposta)
    {
        $this->_id_distribuicao_avaliacao_proposta = $id_distribuicao_avaliacao_proposta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPreprojeto()
    {
        return $this->_id_preprojeto;
    }

    /**
     * @param mixed $id_preprojeto
     * @return Admissibilidade_Model_DistribuicaoAvaliacaoProposta
     */
    public function setIdPreprojeto($id_preprojeto)
    {
        $this->_id_preprojeto = $id_preprojeto;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdOrgaoSuperior()
    {
        return $this->_id_orgao_superior;
    }

    /**
     * @param mixed $id_orgao_superior
     * @return Admissibilidade_Model_DistribuicaoAvaliacaoProposta
     */
    public function setIdOrgaoSuperior($id_orgao_superior)
    {
        $this->_id_orgao_superior = $id_orgao_superior;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPerfil()
    {
        return $this->_id_perfil;
    }

    /**
     * @param mixed $id_perfil
     * @return Admissibilidade_Model_DistribuicaoAvaliacaoProposta
     */
    public function setIdPerfil($id_perfil)
    {
        $this->_id_perfil = $id_perfil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataDistribuicao()
    {
        return $this->_data_distribuicao;
    }

    /**
     * @param mixed $data_distribuicao
     * @return Admissibilidade_Model_DistribuicaoAvaliacaoProposta
     */
    public function setDataDistribuicao($data_distribuicao)
    {
        $this->_data_distribuicao = $data_distribuicao;
        return $this;
    }

    public function isPermitidoAvaliarProposta()
    {
        if (!empty($this->getIdPerfil()) && !is_null($this->getIdPerfil())) {
            return (Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE == $this->getIdPerfil()
                || Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE == $this->getIdPerfil()
                || Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE == $this->getIdPerfil()
            );
        }
    }
}