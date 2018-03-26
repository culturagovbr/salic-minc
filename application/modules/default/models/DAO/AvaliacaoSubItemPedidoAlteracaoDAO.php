<?php
/**
 * DAO AvaliacaoSubItemPedidoAlteracao
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class AvaliacaoSubItemPedidoAlteracaoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao";
    protected $_primary = "idAvaliacaoSubItemPedidoAlteracao";



    /**
     * M�todo para buscar
     * @access public
     * @static
     * @param void
     * @return object || bool
     */
    public static function buscar($idAvaliacaoItemPedidoAlteracao = null)
    {
        $sql = "SELECT idAvaliacaoItemPedidoAlteracao AS id FROM BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao";
        if (!empty($idAvaliacaoItemPedidoAlteracao)) {
            $sql.= " WHERE idAvaliacaoItemPedidoAlteracao = " . $idAvaliacaoItemPedidoAlteracao;
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscar()



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

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao", $dados);

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
     * @return bool
     */
    public static function alterar($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where   = "idAvaliacaoSubItemPedidoAlteracao = $id";
        $alterar = $db->update("BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()



    /**
     * M�todo para buscar ultimo registro
     * @access public
     * @static
     * @param void
     * @return object || bool
     */
    public static function buscarUltimo()
    {
        $sql = "SELECT MAX(idAvaliacaoSubItemPedidoAlteracao) AS id FROM BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarUltimo()
} // fecha class AvaliacaoSubItemPedidoAlteracaoDAO
