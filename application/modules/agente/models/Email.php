<?php

class Agente_Model_Email extends MinC_Db_Table_Abstract
{
    protected $_name = 'internet'; 
    protected $_schema = 'agentes';

    /**
     * @access public
     * @param integer $idAgente
     * @return object $db->fetchAll($sql)
     */
    public function buscarEmailsConselheiro($idAgente)
    {
        $sql = "SELECT * ";
        $sql.= "FROM AGENTES.dbo.Internet ";
        $sql.= "WHERE idAgente = '" . $idAgente . "'";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            throw new Exception ("Erro ao buscar E-mails do Proponente: " . $e->getMessage());
        }

        return $db->fetchAll($sql);
    }



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
            throw new Exception ("Erro ao cadastrar E-mails do Proponente: " . $e->getMessage());
        }
    }

    /**
     * inserir
     *
     * @param mixed $dados
     * @access public
     * @return void
     */
    public function inserir($dados, $dbg = null)
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
            throw new Exception ("Erro ao excluir E-mails do Proponente: " . $e->getMessage());
        }

        return $db->fetchAll($sql);
    } 



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
            throw new Exception ("Erro ao excluir E-mails do Proponente: " . $e->getMessage());
        }
    }
} 
