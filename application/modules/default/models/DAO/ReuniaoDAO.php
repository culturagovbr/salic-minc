<?php
/**
 * Modelo Estado
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class ReuniaoDAO extends Zend_Db_Table
{
    protected $_name = 'sac.dbo.tbreuniao'; // nome da tabela



    /**
     * M�todo para buscar a reuni�o em aberto
     * @access public
     * @param void
     * @return object $db->fetchAll($sql)
     */
    public static function buscarReuniaoAberta()
    {
        $sql = "select idNrReuniao, NrReuniao, stPlenaria from sac..tbreuniao where stEstado = 0";
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao Reuniao Aberta: " . $e->getMessage();
        }

        return $db->fetchRow($sql);
    } // fecha buscar()
} // fecha class
