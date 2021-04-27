<?php

class Admissibilidade_Model_SugestaoEnquadramento extends MinC_Db_Model
{
    protected $_id_sugestao_enquadramento;
    protected $_id_preprojeto;
    protected $_id_orgao;
    protected $_id_orgao_superior;
    protected $_id_perfil_usuario;
    protected $_id_usuario_avaliador;
    protected $_id_area;
    protected $_id_segmento;
    protected $_descricao_motivacao;
    protected $_data_avaliacao;
    protected $_ultima_sugestao;
    protected $_id_distribuicao_avaliacao_proposta;

    /**
     * @return mixed
     */
    public function getIdDistribuicaoAvaliacaoProposta()
    {
        return $this->_id_distribuicao_avaliacao_proposta;
    }

    /**
     * @param mixed $id_distribuicao_avaliacao_proposta
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdDistribuicaoAvaliacaoProposta($id_distribuicao_avaliacao_proposta)
    {
        $this->_id_distribuicao_avaliacao_proposta = $id_distribuicao_avaliacao_proposta;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUltimaSugestao()
    {
        return $this->_ultima_sugestao;
    }

    /**
     * @param mixed $ultima_sugestao
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setUltimaSugestao($ultima_sugestao)
    {
        $this->_ultima_sugestao = $ultima_sugestao;
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
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdOrgaoSuperior($id_orgao_superior)
    {
        $this->_id_orgao_superior = $id_orgao_superior;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdSugestaoEnquadramento()
    {
        return $this->_id_sugestao_enquadramento;
    }

    /**
     * @param mixed $id_sugestao_enquadramento
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdSugestaoEnquadramento($id_sugestao_enquadramento)
    {
        $this->_id_sugestao_enquadramento = $id_sugestao_enquadramento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdOrgao()
    {
        return $this->_id_orgao;
    }

    /**
     * @param mixed $id_orgao
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdOrgao($id_orgao)
    {
        $this->_id_orgao = $id_orgao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdPerfilUsuario()
    {
        return $this->_id_perfil_usuario;
    }

    /**
     * @param mixed $id_perfil_usuario
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdPerfilUsuario($id_perfil_usuario)
    {
        $this->_id_perfil_usuario = $id_perfil_usuario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdUsuarioAvaliador()
    {
        return $this->_id_usuario_avaliador;
    }

    /**
     * @param mixed $id_usuario_avaliador
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdUsuarioAvaliador($id_usuario_avaliador)
    {
        $this->_id_usuario_avaliador = $id_usuario_avaliador;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescricaoMotivacao()
    {
        return $this->_descricao_motivacao;
    }

    /**
     * @param mixed $descricao_motivacao
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setDescricaoMotivacao($descricao_motivacao)
    {
        $this->_descricao_motivacao = $descricao_motivacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataAvaliacao()
    {
        return $this->_data_avaliacao;
    }

    /**
     * @param mixed $data_avaliacao
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setDataAvaliacao($data_avaliacao)
    {
        $this->_data_avaliacao = $data_avaliacao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdArea()
    {
        return $this->_id_area;
    }

    /**
     * @param mixed $id_area
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdArea($id_area)
    {
        $this->_id_area = $id_area;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdSegmento()
    {
        return $this->_id_segmento;
    }

    /**
     * @param mixed $id_segmento
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdSegmento($id_segmento)
    {
        $this->_id_segmento = $id_segmento;
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
     * @return Admissibilidade_Model_SugestaoEnquadramento
     */
    public function setIdPreprojeto($id_preprojeto)
    {
        $this->_id_preprojeto = $id_preprojeto;
        return $this;
    }

    public function isPermitidoSugerirEnquadramento()
    {

        if(!empty($this->getIdPerfilUsuario()) && !is_null($this->getIdPerfilUsuario())) {
            return (Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE == $this->getIdPerfilUsuario()
                || Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE == $this->getIdPerfilUsuario()
                || Autenticacao_Model_Grupos::COMPONENTE_COMISSAO == $this->getIdPerfilUsuario()
                || Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE == $this->getIdPerfilUsuario());
        }
    }
}