<?php
/**
 * DAO tbAvaliacaoSubItemPlanoDistribuicao
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 11/04/2012
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2012 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */

class tbAvaliacaoSubItemPlanoDistribuicao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "BDCORPORATIVO";
    protected $_schema  = "BDCORPORATIVO.scSAC";
    protected $_name    = "tbAvaliacaoSubItemPlanoDistribuicao";


    /**
     * Busca as avali��es dos itens
     * @access public
     */
    public function buscarAvaliacao($idPlano, $idAvaliacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_schema .'.'. $this->_name),
            array()
        );
        $select->joinInner(
            array('b' => 'tbAvaliacaoSubItemPedidoAlteracao'),
            'a.idAvaliacaoSubItemPedidoAlteracao = b.idAvaliacaoSubItemPedidoAlteracao',
            array('b.idAvaliacaoSubItemPedidoAlteracao as idAvaliacaoSubItem', 'b.stAvaliacaoSubItemPedidoAlteracao as avaliacao', 'b.dsAvaliacaoSubItemPedidoAlteracao as descricao'),
            'BDCORPORATIVO.scSAC'
        );
        $select->where('a.idPlano = ?', $idPlano);
        $select->where('b.idAvaliacaoItemPedidoAlteracao = ?', $idAvaliacao);

        return $this->fetchRow($select);
    } // fecha m�todo buscarLocaisAprovados()
} // fecha class
