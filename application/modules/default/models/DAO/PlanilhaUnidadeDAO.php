<?php
/* DAO Planilha Unidade
 * @author Equipe RUP - Politec
 * @since 02/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class PlanilhaUnidadeDAO extends Zend_Db_Table
{
    protected $_name = 'SAC.dbo.tbPlanilhaUnidade';



    /**
     * Busca a planilha com as unidades
     * @access public
     * @static
     * @param void
     * @return object
     */
    public static function buscar()
    {
        $sql = "SELECT * FROM SAC.dbo.tbPlanilhaUnidade ORDER BY Descricao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscar()
} // fecha class
