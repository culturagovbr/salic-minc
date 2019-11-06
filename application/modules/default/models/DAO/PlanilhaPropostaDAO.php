<?php

class PlanilhaPropostaDAO extends Zend_Db_Table
{
    protected $_schema = 'SAC.dbo';
    protected $_name   = 'tbPlanilhaProposta';

    public function parecerFavoravel($idpronac, $idproduto = null)
    {
        $sql = "UPDATE SAC.dbo.tbPlanilhaProjeto
				SET 
				Quantidade 		= pro.Quantidade
				,Ocorrencia 	= pro.Ocorrencia
				,ValorUnitario 	= pro.ValorUnitario
				,Justificativa 	= ''
				,stCustoPraticado = pro.stCustoPraticado
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
