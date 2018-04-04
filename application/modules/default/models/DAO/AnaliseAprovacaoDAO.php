<?php
/* DAO An�lise Aprovacao
 * @author Equipe RUP - Politec
 * @since 02/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class AnaliseAprovacaoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "SAC.dbo";
    protected $_name    = "tbAnaliseAprovacao";
    protected $_primary = "idAnaliseAprovacao";



    /**
     * M�todo para alterar os dados da an�lise de conte�do na planilha do conselheiro/ministro
     * @access public
     * @static
     * @param array $dados
     * @param integer $idPronac
     * @param integer $idProduto
     * @param integer $idAnaliseAprovacao
     * @return bool
     */
    public static function alterar($dados, $idPronac, $idProduto = null, $idAnaliseAprovacao = null, $tpAnalise = null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "IdPRONAC = $idPronac ";

        // busca pela id do produto
        if (!empty($idProduto)) {
            $where.= "AND idProduto = $idProduto ";
        }

        // busca pela id da tabela
        if (!empty($idAnaliseAprovacao)) {
            $where.= "AND idAnaliseAprovacao = $idAnaliseAprovacao ";
        }
                
        if (!empty($tpAnalise)) {
            $where.= "AND tpAnalise = '$tpAnalise'";
        }

        $alterar = $db->update("SAC.dbo.tbAnaliseAprovacao", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()
} // fecha class
