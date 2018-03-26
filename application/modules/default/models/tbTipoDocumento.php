<?php
/**
 * DAO tbTipoInconsistencia
 * @author emanuel.sampaio - Politec
 * @since 17/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbTipoDocumento extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "tbTipoDocumento";


    /**
     * M�todo para consultar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function consultarDados($idTipoDocumento = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('T' => $this->_name)
        );
        if ($idTipoDocumento) {
            $select->where('T.idTipoDocumento = ?', $idTipoDocumento);
        }
        $select->where('T.stEstado = ?', 1);
        $select->order('T.dsTipoDocumento');

        return $this->fetchAll($select);
    }
} // fecha class
