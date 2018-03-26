<?php
class tbModeloTermoDecisao extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbModeloTermoDecisao";

    public function buscarTermoDecisao($where = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('ttd' => $this->_name),
                array('ttd.idModeloTermoDecisao',
                      'ttd.idOrgao',
                      'ttd.idVerificacao',
                      'ttd.stModeloTermoDecisao',
                      new Zend_Db_Expr('CAST(ttd.meModeloTermoDecisao AS TEXT) AS meModeloTermoDecisao'),
           )
        );
        $select->joinInner(
            array('o' => 'Orgaos'),
                'ttd.idOrgao = o.Codigo',
                array('o.Codigo', 'o.Sigla')
        );
        if (!empty($where)) {
            foreach ($where as $coluna => $valor) {
                $select->where($coluna, $valor);
            }
        }

        return $this->fetchAll($select);
    }
}
