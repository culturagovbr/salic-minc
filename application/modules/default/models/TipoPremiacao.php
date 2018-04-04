<?php 
/*
 * Classe: Tipo Premiacao
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class TipoPremiacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbTipoPremiacao';

    /*
     * Metodo: buscarTipoPremiacao
     * Entrada: void
     * Saida: Array de Tipos de Premia��es
    */
    public function buscarTipoPremiacao()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}
