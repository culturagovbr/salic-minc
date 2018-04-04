<?php
/**
 * DAO AvaliacaoSubItemCusto
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class AvaliacaoSubItemCustoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "BDCORPORATIVO.scSAC.tbAvaliacaoSubItemCusto";
    protected $_primary = "idAvaliacaoSubItemCusto";



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

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbAvaliacaoSubItemCusto", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()



    /**
     * M�todo para verificar se o registro existe
     * @access public
     * @static
     * @param integer $idPlanilhaAprovacao
     * @return object || bool
     */
    public static function verificar($idPlanilhaAprovacao)
    {
        $sql = "SELECT * FROM BDCORPORATIVO.scSAC.tbAvaliacaoSubItemCusto WHERE idPlanilhaAprovacao = $idPlanilhaAprovacao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo verificar()
} // fecha class AvaliacaoSubItemCustoDAO
