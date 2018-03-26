<?php
class tbAbrangenciaDAO extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'abrangencia';

    public static function buscarDadosAbrangencia($idprojeto=null, $idpedidoalteracao=null)
    {
        $sql = "select
                mun.Descricao as mun,
                uf.Descricao as uf,
                pais.Descricao as pais
                from SAC.dbo.Abrangencia ab
                join AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = ab.idMunicipioIBGE
                join AGENTES.dbo.UF uf on uf.idUF = ab.idUF
                join AGENTES.dbo.Pais pais on pais.idPais = ab.idPais";
        if ($idprojeto) {
            $sql .= " where ab.idProjeto =".$idprojeto." and ab.stAbrangencia = 1 order by pais, uf , mun asc";
        }
        
        if ($idpedidoalteracao) {
            $sql .=" join SAC.dbo.Projetos pr on pr.idProjeto = ab.idProjeto and ab.stAbrangencia = 1
                     join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto pap on pap.idPRONAC = pr.IdPRONAC
                     where pap.idPedidoAlteracao = ".$idpedidoalteracao;
        }
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarDadosTbAbrangencia($idprojeto=null, $idpedidoalteracao=null)
    {
        $sql = "SELECT c.idPais, c.idUF, c.idMunicipioIBGE, c.idAbrangenciaAntiga, c.tpAcao
                    FROM BDCORPORATIVO.scSAC.tbAvaliacaoSubItemAbragencia               AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao    AS b ON a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracao AND a.idAvaliacaoSubItemPedidoAlteracao = b.idAvaliacaoSubItemPedidoAlteracao
                    INNER JOIN SAC.dbo.tbAbrangencia                                    AS c ON c.idAbrangencia = a.idAbrangencia
                WHERE a.idAvaliacaoItemPedidoAlteracao = $idpedidoalteracao and b.stAvaliacaoSubItemPedidoAlteracao = 'D'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
