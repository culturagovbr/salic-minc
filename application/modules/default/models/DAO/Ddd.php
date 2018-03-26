<?php
/**
 * Modelo Ddd
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 *
 * @todo alterar para o formato com as models, dbtable e mapper.
 */
class Ddd extends MinC_Db_Table_Abstract
{
    protected $_name = 'AGENTES.dbo.DDD'; // nome da tabela


    /**
     * Metodo para buscar os ddds de um determinado estado
     * @access public
     * @param integer $idUF
     * @return object $db->fetchAll($sql)
     */
    public function buscar($idUF)
    {
        $sql = "SELECT AGENTES.dbo.DDD.idDDD AS id, AGENTES.dbo.DDD.Codigo AS descricao ";
        $sql.= "FROM AGENTES.dbo.DDD ";
        $sql.= "WHERE AGENTES.dbo.DDD.idUF = " . $idUF . " ";
        $sql.= "ORDER BY AGENTES.dbo.DDD.Codigo;";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar DDDs: " . $e->getMessage();
        }

        
        return $db->fetchAll($sql);
    } // fecha buscar()
} // fecha class
