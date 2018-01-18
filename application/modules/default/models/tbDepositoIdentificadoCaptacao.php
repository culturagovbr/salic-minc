<?php
/**
 * DAO tbDepositoIdentificadoCaptacao
 * @author emanuel.sampaio - Politec
 * @since 30/06/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbDepositoIdentificadoCaptacao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "tbDepositoIdentificadoCaptacao";

    /**
     * M�todo para ignorar a aus�ncia da chave prim�ria
     */
    /*public function _setupPrimaryKey()
    {
        $this->_primary = "";
    }*/


    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()

    /**
     * Executa a Procedure spDepositoIdentificadoCaptacao
     * @access public
     * @return null
     */
    public function DepositoIdentificadoCaptacao()
    {
        $sql ="exec SAC.dbo.spDepositoIdentificadoCaptacao";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->query($sql);
    }
}
