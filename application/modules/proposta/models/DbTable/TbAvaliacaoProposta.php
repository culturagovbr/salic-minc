<?php

class Proposta_Model_DbTable_TbAvaliacaoProposta extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "tbAvaliacaoProposta";
    protected $_primary = "idAvaliacaoProposta";

    public function contarDiligenciasAbertas($idPreProjeto)
    {

        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from(
            ['avaliacao' => $this->_name],
            [ 'avaliacao.idProjeto' ],
            $this->_schema
        );

        $query->joinInner(
            ['movimentacao' => 'tbMovimentacao'],
            'avaliacao.idProjeto = movimentacao.idProjeto',
            [],
            $this->_schema
        );

        $query->where('movimentacao.Movimentacao = ?', Proposta_Model_TbMovimentacao::PROPOSTA_COM_PROPONENTE);
        $query->where('movimentacao.stEstado = ?', 0);
        $query->where('avaliacao.stEstado = ?', 0);
        $query->where('avaliacao.idProjeto = ?', $idPreProjeto);

        return count($this->fetchAll($query));
    }

}
