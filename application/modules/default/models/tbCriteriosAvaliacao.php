<?php
/*
 * Classe: EditalComposi��o
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class tbCriteriosAvaliacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbCriteriosAvaliacao';
    protected $_schema  = 'SAC';
    /*
     * Metodo: buscarComposicaoEdital
     * Entrada: void
     * Saida: Array de Composi��es
    */
    public function buscarCriteriosAvaliacao($where=array())
    {
        $select = $this->select();
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }

    public function buscarcriterioporidEdital($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idEdital = ?', $idEdital);
        $select->order('orDesempate');
        return $this->fetchAll($select);
    }

    public function salvarcriterioavaliacao($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
