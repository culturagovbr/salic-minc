<?php 
/*
 * Classe: tbEditalModulo
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class tbEditalModulo extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbEditalModulo';

    /*
     * Metodo: buscarModulos
     * Entrada: void
     * Saida: Array de Composi��es
    */
    public function buscarModulo()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
    
    public function buscarModuloporEdital($idEdital)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idEdital = ?', $idEdital);
        return $this->fetchAll($select);
    }
    
    public function associarModuloEdital($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
