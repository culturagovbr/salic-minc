<?php
/* DAO Planilha Etapa
 * @author Equipe RUP - Politec
 * @since 02/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class PlanilhaEtapaDAO extends Zend_Db_Table
{
    protected $_name = 'SAC.dbo.tbPlanilhaEtapa';



    /**
     * Busca a planilha com as etapas
     * @access public
     * @static
     * @param void
     * @return object
     */
    public static function buscar()
    {
        $sql = "SELECT * FROM SAC.dbo.tbPlanilhaEtapa ORDER BY Descricao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscar()
} // fecha class
