<?php
/**
 * Modelo Areacultural
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Areacultural extends Zend_Db_Table
{
    protected $_name = 'SAC.dbo.Area'; // nome da tabela



    /**
     * M�todo para buscar todas as �reas culturais
     * @access public
     * @param void
     * @return object $db->fetchAll($sql)
     */
    public static function buscar()
    {
        $sql = "SELECT Codigo AS id, Descricao AS descricao ";
        $sql.= "FROM SAC.dbo.Area ";
        $sql.= "ORDER BY Descricao;";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar �rea Cultural: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    } // fecha buscar()
} // fecha class
