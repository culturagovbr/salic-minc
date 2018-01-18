<?php
/**
 * DAO tbPlanilhaUnidade
 * @author Guilherme Adler - MinC
 * @date 19/10/16
 */

class Proposta_Model_DbTable_PlanilhaUnidade extends MinC_Db_Table_Abstract
{
    protected $_schema  = "sac";
    protected $_name   = "planilhaunidade";

    public function buscarUnidade()
    {

//        $sql = "select idUnidade, Sigla, Descricao
//        FROM SAC.dbo.tbPlanilhaUnidade order by 3";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
           array('u'=> $this->_name),
            array('u.idUnidade', 'u.Sigla', 'u.Descricao'),
            $this->_schema
        );
        $select->order('3');

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);


        return $db->fetchAll($select);
    }
} // fecha class
