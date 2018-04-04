<?php 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Area
 *
 * @author Emanuel Melo
 */
class NovoEdital extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbEdital';

    
    public function buscarEdital()
    {
    }
    
    public function alterarEdital()
    {
    }
    
    public function salvarEdital($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
    
    public function salvarFluxoEdital($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
    
    public function salvardadosgerais($dados, $where)
    {
        $update = $this->update($dados, $where);
        return $update;
    }
    
    public function excluirEdital()
    {
    }
    
    public function buscarIdTipoEdital($idEdital)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from('tbEdital', array('idTpEdital'))
                ->where('idEdital = ?', $idEdital);
        return $this->fetchRow($select);
    }
    
    
//    public function  BuscarAreaProjeto($idpronac=false){
//        $select = $this->select();
//        $select->setIntegrityCheck(false);
//        $select->from(
//                        array('a'=>$this->_name),
//                        array('a.Descricao as nomeArea')
//                     );
//        $select->joinInner(
//                            array('pr'=>'Projetos'),
//                            'pr.Area = a.Codigo'
//                          );
//        if($idpronac){
//            $select->where('pr.IdPRONAC = ?', $idpronac);
//        }
//
//        return $this->fetchRow($select);
//    }
//
//
//    public function  BuscarAreas(){
//        $select = $this->select();
//        $select->setIntegrityCheck(false);
//        $select->from(
//                        array('a'=>$this->_name),
//                        array('a.*')
//                     );
//
//        return $this->fetchAll($select);
//    }
}
