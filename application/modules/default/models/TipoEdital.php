<?php 
/*
 * Classe: Tipo Edital
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class TipoEdital extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbTipoEdital';

    /*
     * Metodo: buscarTipoEdital
     * Entrada: void
     * Saida: Array de Tipos de Editais
    */
    public function buscarTipoEdital()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
}
