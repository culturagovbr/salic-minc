<?php

class tbalteracaonomeproponenteDAO extends Zend_Db_Table
{
    protected $_name = "BDCORPORATIVO.scSAC.tbalteracaonomeproponente";

    public static function buscarDadosAltNomProp($idpedidoalteracao)
    {
        $sql = "  select tanp.nrCNPJCPF,
		  tanp.nmproponente,
		  tanp.dsjustificativa,
                  tap.idPRONAC
		  from BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente tanp
		  join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tap on tap.idPedidoAlteracao = tanp.idPedidoAlteracao
                  where tanp.idpedidoalteracao =".$idpedidoalteracao;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarProjPorProp($cpf)
    {
        $sql = "select
                pr.IdPRONAC,
                pr.NomeProjeto,
                pr.Situacao,
                sit.Descricao
                from SAC.dbo.Projetos pr
                join SAC.dbo.Situacao sit on sit.Codigo = pr.Situacao
                where pr.cgccpf = '".$cpf."'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    public static function alterarNomeProponente($dados, $idpronac)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idpronac = ".$idpronac;
        $alterar = $db->update("sac.dbo.projetos", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
}
