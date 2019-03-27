<?php

class AvaliacaoResultados_Model_DbTable_vwVisualizarParecerDeAvaliacaoDeResultado extends MinC_Db_Table_Abstract
{
    protected $_name = "vwVisualizarParecerDeAvaliacaoDeResultado";
    protected $_schema = "SAC";
    protected $_primary = "IdPronac";

    public function buscarObjetoParecerAvaliacaoResultado($id)
    {
        $select = $this->select();
        $select->from(
            ['a' => $this->_name],
            [
                new Zend_Db_Expr('a.dsResutaldoAvaliacaoObjeto AS dsManifestacaoObjeto'), 
                'a.dsParecerDeCumprimentoDoObjeto'
            ],
            'sac.dbo'
        )
        ->where('a.IdPronac = ? ', $id);
        
        return $this->fetchRow($select);
    }
}
