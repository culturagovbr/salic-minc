<?php
/* DAO Planilha Proposta
 * @author Equipe RUP - Politec
 * @since 02/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @link http://www.politec.com.br
 * @copyright © 2010 - Politec - Todos os direitos reservados.
 */

class PlanilhaPropostaDAO extends Zend_Db_Table
{
    protected $_schema = 'SAC.dbo';
    protected $_name   = 'tbPlanilhaProposta';



    /**
     * Busca as planilhas com as propostas
     * @access public
     * @static
     * @param integer $idPlanilha
     * @return object
     * @deprecated
     */
    public static function buscar($idPlanilha)
    {
        throw new Exception('Função não está sendo utilizada!');
        
        $sql = "SELECT * 
				FROM SAC.dbo.tbPlanilhaProposta PL
				left join SAC.dbo.tbAnaliseDeConteudo AN on PL.idProjeto = AN.IdPRONAC
				inner join SAC.dbo.Parecer PA on PL.idProjeto = PA.idPRONAC AND PL.idPlanilhaProjeto = " . $idPlanilha;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->query($sql);
    }
    
    public function parecerFavoravel($idpronac, $idproduto = null)
    {
        $sql = "UPDATE SAC.dbo.tbPlanilhaProjeto
				SET 
				Quantidade 		= pro.Quantidade
				,Ocorrencia 	= pro.Ocorrencia
				,ValorUnitario 	= pro.ValorUnitario
				,Justificativa 	= ''
				FROM SAC.dbo.tbPlanilhaProjeto AS PP
				INNER JOIN SAC.dbo.tbPlanilhaProposta AS pro ON pro.idPlanilhaProposta = PP.idPlanilhaProposta
				WHERE PP.idPronac = ".$idpronac;
        
        if (!empty($idproduto)) {
            $sql .= " AND PP.idProduto in (0, ".$idproduto.") ";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->query($sql);
    }
}
