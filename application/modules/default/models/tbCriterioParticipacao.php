<?php
/*
 * Classe: EditalComposi��o
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class tbCriterioParticipacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbCriterioparticipacao';


    public function buscarCriteriosparticipacao()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }

    public function buscarcriterioporidEdital($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idEdital = ?', $idEdital);
        return $this->fetchAll($select);
    }

    public function buscarCriterioPorIdCategoria($idCategoria)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idCategoria = ?', $idCategoria);
        return $this->fetchAll($select);
    }

    public function salvarcriterioparticipacao($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
