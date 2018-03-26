<?php
/**
 * DAO tbMovimentacaoBancariaItem
 * @author emanuel.sampaio - Politec
 * @since 17/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbMovimentacaoBancariaItem extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "dbo";
    protected $_name    = "tbMovimentacaoBancariaItem";



    /**
     * M�todo para buscar
     * @access public
     * @param void
     * @return object/array
     */
    public function buscarDados()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);
        $select->order('dtAberturaConta');
        $select->order('dtMovimento');
        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()



    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()



    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idMovimentacaoBancariaItem = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()



    /**
     * M�todo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirDados($where)
    {
        $where = "idMovimentacaoBancariaItem = " . $where;
        return $this->delete($where);
    } // fecha m�todo excluirDados()
} // fecha class
