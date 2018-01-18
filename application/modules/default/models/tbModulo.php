<?php 
/*
 * Classe: tbModulo
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class tbModulo extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbModulo';

    /*
     * Metodo: buscarComposicaoEdital
     * Entrada: void
     * Saida: Array de Composi��es
    */
    //  public function buscarModulo(){
//        $select = $this->select();
//        $select->setIntegrityCheck(false);
//        return $this->fetchAll($select);
//    }
    
    public function buscarModulo($idEdital)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from('tbModulo', array('idModulo','dsModulo'))
                ->join('tbEditalModulo', 'tbEditalModulo.idModulo = tbModulo.idModulo', array(), 'sac.dbo')
                ->where('idEdital = ?', $idEdital);
        
        return $this->fetchAll($select);
    }
    
    
    public function buscarModuloPorEdital($where)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($this->_name, array('idModulo','dsModulo'))
                ->join('tbEditalModulo', 'tbEditalModulo.idModulo = tbModulo.idModulo', array(), 'sac.dbo');
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }
    
    public function salvarModulo($nomeModulo)
    {
        $dados = array(
           'dsModulo' => $nomeModulo
         );
         
        $insert = $this->insert($dados);
        return $insert;
    }
    
    
    public function atualizaModulo($dado, $where)
    {
        $update = $this->update($dado, $where);
        
        return $update;
    }
}
