<?php

class tbalteracaolocalrealizacaoDAO extends Zend_Db_Table
{
    protected $_name = "BDCORPORATIVO.scSAC.tbalteracaolocalrealizacao";

    public static function buscarDadosAltLocRel($idpedidoalteracao)
    {
        $sql = "
        select
        mun.Descricao as mun,
        uf.Descricao as uf,
        pais.Descricao as pais,
        alr.tpoperacao,
        alr.dsjustificativa
        from BDCORPORATIVO.scSAC.tbAlteracaoLocalRealizacao alr
        left join AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = alr.idMunicipioIBGE
        left join AGENTES.dbo.UF uf on uf.idUF = alr.idUF
        join AGENTES.dbo.Pais pais on pais.idPais = alr.idPais
        where alr.idpedidoalteracao= ".$idpedidoalteracao." order by pais, uf, mun asc";
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
