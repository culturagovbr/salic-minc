<?php
class Planilha_Model_DbTable_TbPlanilhaProjeto extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'tbPlanilhaProjeto';

    public function restaurarPlanilha($idPronac, $idsProdutos = [], $idPlanilhaProjeto = null)
    {
        $sql = "UPDATE SAC.dbo.tbPlanilhaProjeto
				SET 
				Quantidade = pro.Quantidade,
				Ocorrencia = pro.Ocorrencia,
				QtdeDias = pro.QtdeDias,
				ValorUnitario = pro.ValorUnitario,
				idUnidade = pro.Unidade,
				Justificativa = '',
				stCustoPraticado = pro.stCustoPraticado
				FROM SAC.dbo.tbPlanilhaProjeto AS PP
				INNER JOIN SAC.dbo.tbPlanilhaProposta AS pro ON pro.idPlanilhaProposta = PP.idPlanilhaProposta
				WHERE PP.idPronac = " . (int) $idPronac;

        if (!empty($idsProdutos)) {
            $idsProdutos = implode(",", $idsProdutos);
            $sql .= " AND PP.idProduto in ({$idsProdutos}) ";
        }

        if (!empty($idPlanilhaProjeto)) {
            $sql .= " AND PP.idPlanilhaProjeto = {$idPlanilhaProjeto} ";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->query($sql);
    }
}
