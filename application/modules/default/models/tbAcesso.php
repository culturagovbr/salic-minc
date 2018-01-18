<?php

/**
 * Description of tbAcesso
 *
 * @author 01129075125
 */
class tbAcesso extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbAcesso';


    /**
     * M�todo para consultar se existe algum registro para o idRelatorio
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function buscarDadosAcesso($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a' => $this->_name)
            );
        $select->joinInner(
                    array('b' => 'tbRelatorioTrimestral'),
                    'b.idRelatorioTrimestral = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->joinInner(
                    array('c' => 'tbRelatorio'),
                    'c.idRelatorio = b.idRelatorio',
                    array(''),
                    'SAC.dbo'
                    );

        $select->where('c.IdPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }
}
