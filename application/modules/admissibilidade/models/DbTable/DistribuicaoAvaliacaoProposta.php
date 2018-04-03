<?php

class Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta extends MinC_Db_Table_Abstract
{
    protected $_name = "distribuicao_avaliacao_proposta";
    protected $_schema = "sac";
    protected $_primary = "id_distribuicao_avaliacao_proposta";
    /**
     * @var $distribuicaoAvaliacaoProposta Admissibilidade_Model_DistribuicaoAvaliacaoProposta
     */
    private $_distribuicaoAvaliacaoProposta;

    /**
     * @param array $distribuicaoAvaliacaoProposta
     * @return Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta
     */
    public function setDistribuicaoAvaliacaoProposta(array $params)
    {
        if(count($params) > 0) {
            $this->_distribuicaoAvaliacaoProposta = new Admissibilidade_Model_DistribuicaoAvaliacaoProposta($params);
        }

        return $this;
    }

    public function propostaPossuiAvaliacao(
        $id_preprojeto,
        $id_perfil,
        $id_orgao_superior
    )
    {
        $filtroBusca = [
            'id_preprojeto' => $id_preprojeto,
            'id_perfil' => $id_perfil,
            'id_orgao_superior' => $id_orgao_superior,
        ];

        $resultado = $this->findAll($filtroBusca);
        if (count($resultado) > 0) {
            return true;
        }
        return false;
    }

    public function inativarAvaliacoesProposta($id_preprojeto)
    {

        $this->alterar(
            ['avaliacao_atual' => Admissibilidade_Model_DistribuicaoAvaliacaoProposta::AVALIACAO_ATUAL_INATIVA],
            ['id_preprojeto = ?' => $id_preprojeto]
        );
    }

    public function obterAvaliacoesVencidas($prazoVencimentoEmDias = 5)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwPainelAvaliarPropostas',
            [],
            $this->_schema);

        $select->joinInner(
            ['distribuicao_avaliacao_proposta']
            , "distribuicao_avaliacao_proposta.id_preprojeto = vwPainelAvaliarPropostas.idProjeto"
            , [
                'dias_corridos_distribuicao' => new Zend_Db_Expr('DATEDIFF(d, distribuicao_avaliacao_proposta.data_distribuicao, GETDATE())')
                , '*'
            ]
            , $this->getSchema('sac')
        );

        if ($this->_distribuicaoAvaliacaoProposta->getIdPerfil()) {
            $select->where("distribuicao_avaliacao_proposta.id_perfil = {$this->_distribuicaoAvaliacaoProposta->getIdPerfil()}");
        }
        $select->where(new Zend_Db_Expr("DATEDIFF(d, distribuicao_avaliacao_proposta.data_distribuicao, GETDATE()) > ?"), $prazoVencimentoEmDias);
        $select->where("avaliacao_atual = ?", Admissibilidade_Model_DistribuicaoAvaliacaoProposta::AVALIACAO_ATUAL_ATIVA);

        return $db->fetchAll($select);
    }

    public function obterDistribuicaoAtiva() {
        if(!$this->_distribuicaoAvaliacaoProposta->getIdPreprojeto()) {
            throw new Exception("Identificador da Proposta não Informada.");
        }
        $tableSelect = $this->select();
        $tableSelect->from(
            [$this->_name],
            '*',
            $this->getSchema('sac')
        );
        $tableSelect->where('id_preprojeto = ?', $this->_distribuicaoAvaliacaoProposta->getIdPreprojeto());
        $tableSelect->where('avaliacao_atual = ?', Admissibilidade_Model_DistribuicaoAvaliacaoProposta::AVALIACAO_ATUAL_ATIVA);
        $resultado = $this->fetchRow($tableSelect);
        if($resultado) {
            return $resultado->toArray();
        }
    }

}