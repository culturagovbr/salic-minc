<?php
/**
 * @name Agente_Model_DbTable_TbVinculo
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Equipe UFABC
 * @since 18/08/2016 14:29
 *
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_TbVinculoProposta extends MinC_Db_Table_Abstract
{
    protected $_banco = 'agentes';
    protected $_name = 'tbvinculoproposta';
    protected $_schema = 'agentes';
    protected $_primary = 'idVinculoProposta';

    /**
     * @param array $where
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarResponsaveisProponentes($where = array())
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);

        $slct->from(
            array('VP' => $this->_name),
            array('*')
        );

        $slct->joinInner(
            array('VI' => 'tbVinculo'),
            'VI.idVinculo = VP.idVinculo',
            array('*')
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }
}
