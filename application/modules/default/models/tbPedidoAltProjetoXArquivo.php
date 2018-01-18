<?php
/**
 * DAO tbPedidoAltProjetoXArquivo
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 12/04/2012
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2012 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */

class tbPedidoAltProjetoXArquivo extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scSAC";
    protected $_name    = "tbPedidoAltProjetoXArquivo";



    /**
     * Busca os arquivos da solicita��o de readequa��o
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function buscarArquivos($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('x' => $this->_name),
            array('x.idPedidoAlteracao')
        );
        $select->joinInner(
            array('a' => 'tbArquivo'),
            'x.idArquivo = a.idArquivo',
            array('a.idArquivo'
                ,'a.nmArquivo'),
            'BDCORPORATIVO.scCorp'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    } // fecha m�todo buscarArquivos()
} // fecha class
