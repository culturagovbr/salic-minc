<?php
/**
 * DAO Diligencia
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class DiligenciaDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "SAC.dbo.tbDiligencia";
    protected $_primary = "idDiligencia";



    /**
     * M�todo para cadastrar informa��es da diligencia
     * @access public
     * @static
     * @param array $dados
     * @return bool
     */
    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("SAC.dbo.tbDiligencia", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()
} // fecha class DiligenciaDAO
