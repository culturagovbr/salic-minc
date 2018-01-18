<?php
/*
 * Classe: CriterioAvaliacao
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class CriteriosAvaliacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbCritetriosAvaliacao';

    /*
     * Metodo: buscarComposicao
     * Entrada: void
     * Saida: Array de Criterios de Avaliacao
    */
    public function buscarCriteriosAvaliacao()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}
