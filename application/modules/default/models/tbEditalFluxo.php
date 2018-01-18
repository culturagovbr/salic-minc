<?php 
/*
 * Classe: Edital x Fluxo
 * Modulo: Editais
 * Criado por: Emanuel Melo
 */
class tbEditalFluxo extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbEditalFluxo';

    public function salvarFluxoEdital($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
    
    public function buscarFluxoPorEdital($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('ef' => $this->_name),
                array('idEditalFluxo',
                      'idEdital',
                      'idItemFluxo')
        );
        
        $select->joinInner(
        
            array('f' => 'tbFluxo'),
        
            'ef.idFluxo = f.idFluxo',
                        array('f.idFluxo',
                              'f.dsFluxo'),
        
            'sac.dbo'
        );
        
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        
        return $this->fetchAll($select)->toArray();
    }
}
