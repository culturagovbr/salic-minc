<?php
/* DAO Plano Distribui��o
 * @author Equipe RUP - Politec
 * @since 02/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
*/

class PlanoDistribuicaoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "SAC.dbo";
    protected $_name    = "tbPlanoDistribuicao";
    protected $_primary = "idPlano";



    /**
     * M�todo para buscar
     * @access public
     * @static
     * @param integer $idPlano
     * @return object || bool
     */
    public static function buscar($idPlano)
    {
        $sql = "SELECT * FROM SAC.dbo.tbPlanoDistribuicao WHERE idPlano = $idPlano ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscar()



    /**
     * M�todo para buscar ultimo registro
     * @access public
     * @static
     * @param void
     * @return object || bool
     */
    public static function buscarUltimo()
    {
        $sql = "SELECT MAX(idPlano) AS id FROM SAC.dbo.tbPlanoDistribuicao ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarUltimo()



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

        $cadastrar = $db->insert("SAC.dbo.tbPlanoDistribuicao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()



    /**
     * M�todo para alterar
     * @access public
     * @static
     * @param array $dados
     * @param integer $idPlano
     * @return bool
     */
    public static function alterar($dados, $idPlano)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idPlano = $idPlano ";

        $alterar = $db->update("SAC.dbo.tbPlanoDistribuicao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()
} // fecha class





class PlanoDistribuicaoProdutoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "SAC.dbo";
    protected $_name    = "PlanoDistribuicaoProduto";
    protected $_primary = "idPlano";



    /**
     * M�todo para buscar
     * @access public
     * @static
     * @param integer $idPlanoDistribuicao
     * @return object || bool
     */
    public static function buscar($idPlanoDistribuicao)
    {
        $sql = "SELECT * FROM SAC.dbo.PlanoDistribuicaoProduto WHERE idPlanoDistribuicao = $idPlanoDistribuicao  AND stPlanoDistribuicaoProduto = 1";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscar()



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

        $cadastrar = $db->insert("SAC.dbo.PlanoDistribuicaoProduto", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()



    /**
     * M�todo para alterar
     * @access public
     * @static
     * @param array $dados
     * @param integer $idPlanoDistribuicao
     * @return bool
     */
    public static function alterar($dados, $idPlanoDistribuicao)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idPlanoDistribuicao = $idPlanoDistribuicao ";

        $alterar = $db->update("SAC.dbo.PlanoDistribuicaoProduto", $dados, $where);

        if ($alterar) {
            return true;
        }
        return false;
    }
}
