<?php
class tbPedidoAlteracaoProjetoDAO extends Zend_Db_Table
{
    protected $_name = "BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto";

    public static function buscarDadosPedidoAlteracao($idpedidoalteracao = null)
    {
        $sql = "
        select distinct pap.idPRONAC,
        pr.idprojeto,
        pap.idpedidoalteracao,
        pap.tpAlteracaoProjeto,
        pr.NomeProjeto,
        ar.Descricao as area,
        seg.Descricao as segmento,
        pr.dtinicioexecucao,
        pr.dtfimexecucao,
        pap.dtSolicitacao,
	pap.stPedidoAlteracao,
	mun.Descricao as municipio,
        tap.dsAlteracaoProjeto,
        pp.tpProrrogacao
        ";
        if (!empty($idpedidoalteracao)) {
            $sql .= ",
                  pap.idPedidoAlteracao,
                  apa.dtParecerTecnico,
                  apa.dsParecerTecnico,
                  apa.idTecnico,
                  pap.stDeferimentoAvaliacao,
                  apa.dsRetornoCoordenador,
                  apa.dtRetornoCoordenador,
                  apa.idCoordenador,
                  nm.descricao as nomecoordenador,
                  pap.dsJustificativaAvaliacao,
                  pap.dtAvaliacao,
                  pap.idAvaliador,
                  pr.CgcCpf,
                  pp.tpProrrogacao,
                  nm.Descricao as nomeAgente,
                  prep.objetivos";
        }
        $sql .= "
        from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto pap
        join SAC.dbo.Projetos pr on pr.IdPRONAC = pap.idPRONAC
        join SAC.dbo.Area ar on ar.Codigo = pr.Area
        join SAC.dbo.Segmento seg on seg.Codigo = pr.Segmento
        left join SAC.dbo.Abrangencia abrang on abrang.idProjeto = pr.idPronac AND abrang.stAbrangencia = 1 
        left join AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = abrang.idMunicipioIBGE
        left join BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto tap on tap.tpAlteracaoProjeto = pap.tpAlteracaoProjeto
        left join SAC.dbo.PreProjeto prep on prep.idPreProjeto = pr.idProjeto
        left join AGENTES.dbo.Nomes nm on nm.idAgente = prep.idAgente
        left join BDCORPORATIVO.scSAC.tbAvaliacaoPedidoAlteracao apa on apa.idPedidoAlteracao = pap.idPedidoAlteracao
        left join BDCORPORATIVO.scSAC.tbProrrogacaoPrazo pp on pp.idPedidoAlteracao = pap.idPedidoAlteracao
        ";
        if (!empty($idpedidoalteracao)) {
            $sql.=" where pap.idPedidoAlteracao='".$idpedidoalteracao."' ";
            //return $sql;die;
        } else {
            $sql .= " where pap.dtSolicitacao in
            (select max(dtSolicitacao) from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto where idPedidoAlteracao = pap.idPedidoAlteracao)";
        }
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function insertDadosProjeto($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $alterar = $db->insert("BDCORPORATIVO.scSAC.tbAvaliacaoPedidoAlteracao", $dados);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
}
