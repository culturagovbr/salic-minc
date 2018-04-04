<?php
/**
 * DAO AvaliacaoSubItemAbrangenciaDAO
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class AvaliacaoSubItemAbrangenciaDAO extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_schema  = "bdcorporativo.scsac";
    protected $_name    = "tbavaliacaosubitemabrangencia";



    /**
     * M�todo para cadastrar
     * @access public
     * @static
     * @param array $dados
     * @return bool
     */
    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbAvaliacaoSubItemAbragencia", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()
} // fecha class AvaliacaoSubItemPlanoDistribuicaoDAO
