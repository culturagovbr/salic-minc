<?php
/* DAO Planilha Projeto Conselheiro
 * @author Equipe RUP - Politec
 * @since 02/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class PlanilhaProjetoConselheiroDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "SAC.dbo";
    protected $_name    = "tbPlanilhaProjetoConselheiro";
    protected $_primary = "idPlanilhaProjeto";



    /**
     * M�todo para alterar os dados da planilha do conselheiro
     * @access public
     * @static
     * @param array $dados
     * @param integer $idPlanilha
     * @param integer $idPronac
     * @return bool
     */
    public static function alterar($dados, $idPlanilha, $idPronac)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where   = "idPlanilhaProjeto = $idPlanilha AND IdPRONAC = $idPronac";
        $alterar = $db->update("SAC.dbo.tbPlanilhaProjetoConselheiro", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()
} // fecha class
