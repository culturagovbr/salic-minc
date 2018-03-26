<?php
/**
 * DAO AvaliacaoSubItemPlanoDistribuicao
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class AvaliacaoSubItemPlanoDistribuicaoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao";
    protected $_primary = "idAvaliacaoSubItemPedidoAlteracao";



    /**
     * M�todo para cadastrar
     * @access public
     * @static
     * @param array $dados
     * @return bool
     */
    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()



    /**
     * M�todo para alterar
     * @access public
     * @static
     * @param array $dados
     * @param integer $id
         * @param integer $idPlano
     * @return bool
     */
    public static function alterar($dados, $id, $idPlano)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where   = "idAvaliacaoSubItemPedidoAlteracao = $id";
        $where  .= "idPlano = $idPlano";
        $alterar = $db->update("BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()
} // fecha class AvaliacaoSubItemPlanoDistribuicaoDAO
