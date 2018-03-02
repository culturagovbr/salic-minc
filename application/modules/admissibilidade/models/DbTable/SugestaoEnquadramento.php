<?php

class Admissibilidade_Model_DbTable_SugestaoEnquadramento extends MinC_Db_Table_Abstract
{
    protected $_name = "sugestao_enquadramento";
    protected $_schema = "sac";
    protected $_primary = "id_sugestao_enquadramento";

    const ULTIMA_SUGESTAO_ATIVA = 1;
    const ULTIMA_SUGESTAO_INATIVA = 0;

    public function obterHistoricoEnquadramento($id_preprojeto)
    {

        $tableSelect = $this->select();
        $tableSelect->setIntegrityCheck(false);
        $tableSelect->from(
            [$this->_name => $this->_name],
            '*',
            $this->_schema
        );
        $tableSelect->joinInner(
            ['Orgaos' => 'Orgaos'],
            "{$this->_name}.id_orgao = Orgaos.org_codigo",
            [
                'org_sigla'
            ],
            $this->getSchema('tabelas')
        );
        $tableSelect->joinInner(
            ['Grupos' => 'Grupos'],
            "{$this->_name}.id_perfil_usuario = Grupos.gru_codigo",
            [
                'gru_nome'
            ],
            $this->getSchema('tabelas')
        );
        $tableSelect->joinInner(
            ['Usuarios' => 'Usuarios'],
            "{$this->_name}.id_usuario_avaliador = Usuarios.usu_codigo",
            [
                'usu_nome'
            ],
            $this->getSchema('tabelas')
        );
        $tableSelect->joinLeft(
            ['Segmento' => 'Segmento'],
            "{$this->_name}.id_segmento = Segmento.Codigo",
            [
                'segmento' => 'Descricao',
                'tp_enquadramento'
            ],
            $this->_schema
        );
        $tableSelect->joinLeft(
            ['Area' => 'Area'],
            "{$this->_name}.id_area = Area.Codigo",
            [
                'area' => 'Descricao'
            ],
            $this->_schema
        );

        $tableSelect->where('id_preprojeto = ?', $id_preprojeto);
        $tableSelect->order('data_avaliacao desc');

        $resultado = $this->fetchAll($tableSelect);
        if ($resultado) {
            return $resultado->toArray();
        }
    }

    /**
     * @param Admissibilidade_Model_SugestaoEnquadramento $sugestaoEnquadramento
     * @return bool|null
     */
    public function isPropostaEnquadrada(Admissibilidade_Model_SugestaoEnquadramento $sugestaoEnquadramento)
    {
        $arrayPesquisa = [];
        if ($sugestaoEnquadramento->getIdDistribuicaoAvaliacaoProposta()) {
            $arrayPesquisa['id_distribuicao_avaliacao_proposta'] = $sugestaoEnquadramento->getIdDistribuicaoAvaliacaoProposta();
        }
        if ($sugestaoEnquadramento->getIdPreprojeto()
            && $sugestaoEnquadramento->getIdOrgao()
            && $sugestaoEnquadramento->getIdPerfilUsuario()) {
            $arrayPesquisa['id_preprojeto'] = $sugestaoEnquadramento->getIdPreprojeto();
            $arrayPesquisa['id_orgao'] = $sugestaoEnquadramento->getIdOrgao();
            $arrayPesquisa['id_perfil_usuario'] = $sugestaoEnquadramento->getIdPerfilUsuario();
        }

        if (count($arrayPesquisa) > 0) {
            $resultado = $this->findAll($arrayPesquisa);
            if (count($resultado) > 0) {
                return true;
            }
            return false;
        }
    }


    public function inativarSugestoes($id_preprojeto)
    {
        $this->alterar(
            ['ultima_sugestao' => self::ULTIMA_SUGESTAO_INATIVA],
            ['id_preprojeto = ?' => $id_preprojeto]
        );
    }

    public function obterSugestaoAtiva($id_preprojeto)
    {
        return $this->findBy(
            [
                'id_preprojeto' => $id_preprojeto,
                'ultima_sugestao' => self::ULTIMA_SUGESTAO_ATIVA,
            ]
        );
    }

}