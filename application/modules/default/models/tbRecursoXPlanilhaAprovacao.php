<?php

class tbRecursoXPlanilhaAprovacao extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbRecursoXPlanilhaAprovacao";

    public function buscarDados()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);

        return $this->fetchAll($select);
    }

    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    /**
     * @param integer $idPlanilha (excluir todos os recursos vinculados a planilha de aprovacao)
     * @param integer $idRecurso (excluir um determinado recurso)
     * @return integer (quantidade de registros excluidos)
     */
    public function excluirDados($idPlanilha = null, $idRecurso = null)
    {
        // exclui todos os recursos vinculados a planilha de aprova��o
        if (!empty($idPlanilha)) {
            $where = "idPlanilhaAprovacao = " . $idPlanilha;
        } elseif (!empty($idRecurso)) {
            $where = "idRecurso = " . $idRecurso;
        }

        return $this->delete($where);
    }
}
