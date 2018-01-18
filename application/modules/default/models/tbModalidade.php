<?php 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tbOpcaoResposta
 *
 * @author Emerson SIlva
 */
class tbModalidade extends MinC_Db_Table_Abstract
{
    protected $_banco   = "SAC";
    protected $_schema  = "dbo";
    protected $_name = 'Modalidade';

    public function buscarModalidade()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array("m" => new Zend_Db_Expr($this->_banco.".".$this->_schema.".".$this->_name)),
                        array(
                                'm.Codigo as id',
                                'm.Descricao as descricao',
                             )
                     );
        $select->order("m.Descricao");
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        return $this->fetchAll($select);
    }
}
