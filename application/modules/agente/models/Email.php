<?php
/**
 * Modelo Email
 * @author wouerner <wouerner@gmail.com>
 * @since 29/03/2010
 * @version 1.0
 * @todo modificar o nome da tabela no banco de dados.
 */

class Agente_Model_Email extends MinC_Db_Table_Abstract
{
    /**
     * @var nome da tabela
     */
    protected $_name = 'internet'; // nome da tabela
    protected $_schema = 'agentes'; // nome da tabela

    /**
     * M�todo para buscar todos os e-mails de um conselheiro
     * @access public
     * @param integer $idAgente
     * @return object $db->fetchAll($sql)
     */
    public function buscar($idAgente)
    {
        $sql = "SELECT * ";
        $sql.= "FROM AGENTES.dbo.Internet ";
        $sql.= "WHERE idAgente = '" . $idAgente . "'";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar E-mails do Proponente: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    } // fecha buscar()



    /**
     * M�todo para cadastrar todos os e-mails de um conselheiro
     * @access public
     * @param array $dados
     * @return boolean
     */
    public static function cadastrar($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        try {
            $inserir = $db->insert('AGENTES.dbo.Internet', $dados);
            $db->closeConnection();
            return true;
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao cadastrar E-mails do Proponente: " . $e->getMessage();
            return false;
        }
    }

    /**
     * inserir
     *
     * @param mixed $dados
     * @access public
     * @return void
     */
    public function inserir($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        try {
            $schema = $this->getSchema($this->_schema) .'.'. $this->_name;
            $inserir = $db->insert($schema, $dados);
            return true;
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao cadastrar E-mails do Proponente: " . $e->getMessage();
            return false;
        }
    }



    /**
     * M�todo para excluir e-mail de um conselheiro
     * @access public
     * @param integer $id
     * @return object $db->fetchAll($sql)
     */
    public static function excluir($id)
    {
        try {
            $sql = "DELETE FROM AGENTES.dbo.Internet WHERE idInternet = '$id'";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao excluir E-mail do Proponente: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    } // fecha m�todo excluir()



    /**
     * M�todo para excluir todos os emails de um conselheiro
     * @access public
     * @param integer $id
     * @return object $db->fetchAll($sql)
     */
    public static function excluirTodos($idAgente)
    {
        try {
            $sql = "DELETE FROM AGENTES.dbo.Internet WHERE idAgente =".$idAgente;

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $i = $db->query($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao excluir E-mail do Proponente: " . $e->getMessage();
        }
    } // fecha m�todo excluirTodos()
} // fecha class
