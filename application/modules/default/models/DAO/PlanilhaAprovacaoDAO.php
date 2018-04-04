<?php
/* DAO Planilha Aprovacao
 * @author Equipe RUP - Politec
 * @since 02/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class PlanilhaAprovacaoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "SAC";
    protected $_name    = "tbPlanilhaAprovacao";
    protected $_primary = "idPlanilhaAprovacao";



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

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbPlanilhaAprovacao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()



    /**
     * M�todo para alterar os dados da planilha do conselheiro/ministro
     * @access public
     * @static
     * @param array $dados
     * @param integer $idPronac
     * @param integer $idProduto
     * @param integer $idPlanilhaAprovacao
     * @return bool
     */
    public static function alterar($dados, $idPronac, $idProduto = null, $idPlanilhaAprovacao = null, $tpPlanilha = null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "IdPRONAC = $idPronac ";

        // busca pela id do produto
        if (!empty($idProduto)) {
            $where.= "AND idProduto = $idProduto ";
        }

        // busca pela id da tabela
        if (!empty($idPlanilhaAprovacao)) {
            $where.= "AND idPlanilhaAprovacao = $idPlanilhaAprovacao ";
        }

        if (!empty($tpPlanilha)) {
            $where.= "AND tpPlanilha = '$tpPlanilha'";
        }
        $alterar = $db->update("SAC.dbo.tbPlanilhaAprovacao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()



    /**
     * M�todo para buscar
     * @access public
     * @static
     * @param integer $idPlanilhaAprovacao
         * @param string $tpPlanilha
         * @param integer $idPronac
         * @param integer $idProduto
         * @param integer $idEtapa
         * @param integer $idPlanilhaItem
     * @return object || bool
     */
    public static function buscar($tpPlanilha, $idPlanilhaAprovacao = null, $idPronac = null, $idProduto = null, $idEtapa = null, $idPlanilhaItem = null, $tpAcao = null, $buscarProduto = false)
    {
        $sql = "SELECT * FROM SAC.dbo.tbPlanilhaAprovacao
                        WHERE tpPlanilha = '$tpPlanilha'";

        if (!empty($idPlanilhaAprovacao)) {
            $sql.= " AND idPlanilhaAprovacao = $idPlanilhaAprovacao";
        }
        if (!empty($idPronac)) {
            $sql.= " AND IdPRONAC = $idPronac";
        }
        if (!empty($idProduto) || $buscarProduto == true) {
            $sql.= " AND idProduto = $idProduto";
        }
        if (!empty($idEtapa)) {
            $sql.= " AND idEtapa = $idEtapa";
        }
        if (!empty($idPlanilhaItem)) {
            $sql.= " AND idPlanilhaItem = $idPlanilhaItem";
        }
        if (!empty($tpAcao)) {
            $sql.= " AND tpAcao = '$tpAcao'";
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo verificar()
} // fecha class
