<?php

class tbAvaliacaoProposta extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "tbAvaliacaoProposta";
    protected $_primary = "idAvaliacaoProposta";

    public function isPropostaEmConformidade($idPreProjeto)
    {
        $proposta = $this->obterUltimaAvaliacao($idPreProjeto);
        if (count($proposta) > 0 && $proposta[0]->stEstado == 0) {
            return true;
        }
    }

    public function obterUltimaAvaliacao($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $query = $this->select();
        $query->from(
            $this->_name,
            [
                new Zend_Db_Expr('top 1 *')
            ],
            $this->_schema
        );
        $query->where('idProjeto = ?', $idPreProjeto);
        $query->order("DtAvaliacao desc");

        return $db->fetchAll($query);
    }
}
