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

class tbTipoDocumentoBDCORPORATIVO extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scCorp";
    protected $_name    = "tbTipoDocumento";

    public function init()
    {
        parent::init();
    }

    /**
     * M�todo para consultar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */

    public function buscar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
            ->from($this->_name, array('idTipoDocumento', 'dsTipoDocumento'), 'BDCORPORATIVO.scCorp');

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            if (!is_null($valor)) {
                $select->where($coluna, $valor);
            }
        }
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $db->fetchAll($select);
    }
}
