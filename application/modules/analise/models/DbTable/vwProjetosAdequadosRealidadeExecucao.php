<?php

class Analise_Model_DbTable_vwProjetosAdequadosRealidadeExecucao extends MinC_Db_Table_Abstract
{
    protected $_schema    = 'sac';
    protected $_name      = 'vwProjetosAdequadosRealidadeExecucao';


    public function projetos($where = array(), $order = array(), $start = 0, $limit = 10, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwProjetosAdequadosRealidadeExecucao', '*', $this->_schema)
        ;

        foreach ($where as $coluna=>$valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('Pronac like ? OR NomeProjeto like ? OR Tecnico like ? OR  Proponente like ?', '%'.$search['value'].'%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }

        return $db->fetchAll($sql);
    }

    public function projetosTotal($where = array(), $order = array(), $start = null, $limit = null, $search = null)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $db->select()
            ->from('vwProjetosAdequadosRealidadeExecucao', 'count(*) as total', $this->_schema)
        ;

        foreach ($where as $coluna=>$valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sql->where('Pronac like ? OR NomeProjeto like ? OR Tecnico like ? OR  Proponente like ?', '%'.$search['value'].'%');
        }

        $sql->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }

        return $db->fetchOne($sql);
    }
}
