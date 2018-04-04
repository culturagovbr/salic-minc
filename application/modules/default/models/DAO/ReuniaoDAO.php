<?php
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
    }
}
