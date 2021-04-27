<?php

class AvaliacaoResultados_Model_DbTable_vwInformacoesConsolidadasParaAvaliacaoFinanceira extends MinC_Db_Table_Abstract
{
    protected $_name = 'vwInformacoesConsolidadasParaAvaliacaoFinanceira';
    protected $_schema = 'sac';
    protected $_primary = 'IdPRONAC';

    public function informacoes($idPronac) 
    {
        $cols =["*", 
            new Zend_Db_Expr("FORMAT(vlAprovado, 'N', 'pt-br') as vlAprovado"),
            new Zend_Db_Expr("FORMAT(vlComprovado, 'N', 'pt-br') as vlComprovado"),
            new Zend_Db_Expr("FORMAT(vlCaptado, 'N', 'pt-br') as vlCaptado"),
            new Zend_Db_Expr("ISNULL(qtEmpregosGerados, '0') as qtEmpregosGerados"),
            new Zend_Db_Expr("ISNULL(qtEmpregosDiretos, '0') as qtEmpregosDiretos"),
            new Zend_Db_Expr("ISNULL(qtEmpregosIndiretos, '0') as qtEmpregosIndiretos"),
        ];

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->where('IdPRONAC = ?',$idPronac);
        /* echo $select; die; */

        return $this->fetchAll($select);
    }
}
