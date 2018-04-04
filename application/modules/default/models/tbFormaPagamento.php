<?php 
/*
 * Classe: EditalComposi��o
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class tbFormaPagamento extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbFormaPagamento';

  
    public function buscarFormaPagamento()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        return $this->fetchAll($select);
    }
  
    public function buscarFormaPagamentoPorId($idFormaPagamento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idFormaPagamento = ?', $idFormaPagamento);
        return $this->fetchAll($select);
    }
    
    public function buscarFormaPagamentoPorIdCategoria($idCategoria)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where('idCategoria = ?', $idCategoria);
        $select->order('idFormaPagamento');
        return $this->fetchAll($select);
    }
    
//    public function buscarFormaPagamentoPorIdEdital($idEdital){
//        $select = $this->select();
//        $select->setIntegrityCheck(false);
//        $select->where('idCategoria = ?', $idCategoria);
//        return $this->fetchAll($select);
//    }
    
    public function salvarFormaPagamento($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
