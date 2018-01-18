<?php

/**
 * View para painel de avaliação das propostas e tranformação em projetos.
 *
 * @link http://salic.cultura.gov.br
 */
class Admissibilidade_Model_DbTable_VwPainelAvaliarPropostas extends MinC_Db_Table_Abstract
{
    protected $_schema    = 'sac';
    protected $_name      = 'vwPainelAvaliarPropostas';

    public function propostas($where = array(), $order = array(), $start = 0, $limit = 10, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwPainelAvaliarPropostas', '*', $this->_schema)
            ;

        foreach ($where as $coluna=>$valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('idProjeto like ? OR NomeProposta like ? OR Tecnico like ?', '%'.$search['value'].'%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }

        return $db->fetchAll($sql);
    }

    public function propostasTotal($where = array(), $order = array(), $start = null, $limit = null, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwPainelAvaliarPropostas', 'count(*) as total', $this->_schema)
            ;

        foreach ($where as $coluna=>$valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('idProjeto like ? OR NomeProposta like ? OR Tecnico like ?', '%'.$search['value'].'%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }
        //echo $sql;

        return $db->fetchRow($sql);
    }
}
