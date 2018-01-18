<?php
/**
 * Modelo Regiao
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Regiao extends Zend_Db_Table
{
    protected $_name = 'AGENTES.dbo.UF.Regiao'; // nome da tabela



    /**
     * M�todo para buscar as regi�es
     * @access public
     * @param void
     * @return object $db->fetchAll($sql)
     */
    public static function buscar()
    {
        $sql = "SELECT DISTINCT Regiao AS Regiao FROM AGENTES.dbo.UF ORDER BY Regiao";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Regi�es: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    } // fecha buscar()
} // fecha class
