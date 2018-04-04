<?php

class GerarRelatoriosDAO extends Zend_Db_Table
{
    public static function relatorio1($idEdital = null, $idUf = null, $idMunicipio = null, $idFundo = null, $idClassificacao = null)
    {
        $sql = "select
                        uf.Sigla as c1,
                        mun.Descricao as c2,
                        ppr.idPreProjeto as c3,
                        ppr.NomeProjeto as c4,
                        Agentes.dbo.fnnome(ppr.idAgente) as c5,
                        e.nrEdital as c6,
                        fd.nmFormDocumento as c7,
                        CONVERT(VARCHAR(10), envio.DtEnvio, 103) AS c8,
                        CONVERT(VARCHAR(10), ultimaAvaliacao.DtAvaliacao, 103) AS c9
                from SAC.dbo.PreProjeto ppr
                    inner join SAC.dbo.Edital e on (ppr.idEdital = e.idEdital)
                    inner join CONTROLEDEACESSO..SGCacesso s on (ppr.idUsuario = s.IdUsuario)
                    inner join BDCORPORATIVO.scquiz.tbformdocumentoProjeto b on (ppr.idPreProjeto = b.idProjeto)
                    left join SAC.dbo.tbAvaliacaoProposta envio on (envio.idProjeto = ppr.idPreProjeto and envio.ConformidadeOK = 9)
                    left join SAC.dbo.tbAvaliacaoProposta ultimaAvaliacao on (ultimaAvaliacao.idProjeto = ppr.idPreProjeto and ultimaAvaliacao.stEstado = 0)
                    left join AGENTES..EnderecoNacional en on (en.idAgente = ppr.idAgente and Status = 1)
                    left join AGENTES..UF uf on (uf.idUF = en.UF)
                    left join AGENTES..Municipios mun on (mun.idMunicipioIBGE = en.Cidade)
                    left join BDCORPORATIVO.scQuiz.tbFormDocumento fd on fd.idEdital = ppr.idEdital and idClassificaDocumento not in (23,24,25) ";

        if ($idEdital) {
            $sql .= " where e.idEdital = '$idEdital' ";
        }

        if ($idUf) {
            if ($idEdital) {
                $sql .= " AND en.UF = '$idUf' ";
            } else {
                $sql .= " where en.UF = '$idUf' ";
            }
        }

        if ($idMunicipio) {
            $sql .= " AND en.Cidade = '$idMunicipio' ";
        }
        if ($idFundo) {
            if ($idEdital || $idUf) {
                $sql .= " AND e.cdTipoFundo = $idFundo";
            } else {
                $sql .= " where e.cdTipoFundo = $idFundo";
            }
        }

        if ($idClassificacao) {
            if ($idEdital || $idUf || $idFundo) {
                $sql .= " AND fd.idClassificaDocumento = $idClassificacao";
            } else {
                $sql .= " where fd.idClassificaDocumento = $idClassificacao";
            }
        }

        $sql .= " group by ppr.idPreProjeto,e.idEdital,uf.Sigla,en.Cidade,mun.Descricao,ppr.NomeProjeto, Agentes.dbo.fnnome(ppr.idAgente),e.NrEdital,
                     fd.nmFormDocumento, envio.DtEnvio,ultimaAvaliacao.DtAvaliacao ";
        $sql .= " order by ppr.NomeProjeto, uf.Sigla, Cidade, e.idEdital asc ";


        //die('<pre>'.$sql);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function relatorio2($idEdital = null, $idUf = null, $idMunicipio = null, $idFundo = null, $idClassificacao = null)
    {
        $sql = "select
                        uf.Sigla as c1,
                        mun.Descricao as c2,
                        ppr.idPreProjeto as c3,
                        ppr.NomeProjeto as c4,
                        Agentes.dbo.fnnome(ppr.idAgente) as c5,
                        e.nrEdital as c6,
                        fd.nmFormDocumento as c7,
                        CONVERT(VARCHAR(10), envio.DtEnvio, 103) AS c8,
                    CONVERT(VARCHAR(10), ultimaAvaliacao.DtAvaliacao, 103) AS c9
                from SAC.dbo.PreProjeto ppr
                    inner join SAC.dbo.Edital e on (ppr.idEdital = e.idEdital)
                    inner join CONTROLEDEACESSO..SGCacesso s on (ppr.idUsuario = s.IdUsuario)
                    inner join BDCORPORATIVO.scquiz.tbformdocumentoProjeto b on (ppr.idPreProjeto = b.idProjeto)
                    left join SAC.dbo.tbAvaliacaoProposta envio on (envio.idProjeto = ppr.idPreProjeto and envio.ConformidadeOK = 9)
                        inner join SAC.dbo.tbAvaliacaoProposta ultimaAvaliacao on (ultimaAvaliacao.idProjeto = ppr.idPreProjeto and ultimaAvaliacao.ConformidadeOK = 1 and ultimaAvaliacao.stEstado = 0)
                    left join AGENTES..EnderecoNacional en on (en.idAgente = ppr.idAgente and Status = 1)
                    left join AGENTES..UF uf on (uf.idUF = en.UF)
                    left join AGENTES..Municipios mun on (mun.idMunicipioIBGE = en.Cidade)
                    left join BDCORPORATIVO.scQuiz.tbFormDocumento fd on fd.idEdital = ppr.idEdital and idClassificaDocumento not in (23,24,25)
                    left join SAC.dbo.Projetos pro on  pro.idProjeto = ppr.idPreProjeto
                    where pro.idProjeto is null ";

        if ($idEdital) {
            $sql .= " AND e.idEdital = '$idEdital' ";
        }
        if ($idUf) {
            $sql .= " and en.UF = '$idUf' ";
        }
        if ($idMunicipio) {
            $sql .= " and en.Cidade = '$idMunicipio' ";
        }
        if ($idFundo) {
            $sql .= " AND e.cdTipoFundo = $idFundo";
        }
        if ($idClassificacao) {
            $sql .= " AND fd.idClassificaDocumento = $idClassificacao";
        }

        $sql .= "group by ppr.idPreProjeto,e.idEdital,uf.Sigla,en.Cidade,mun.Descricao,ppr.NomeProjeto, Agentes.dbo.fnnome(ppr.idAgente),e.NrEdital,
                    fd.nmFormDocumento, envio.DtEnvio,ultimaAvaliacao.DtAvaliacao ";
        $sql .= "order by ppr.NomeProjeto, uf.Sigla, Cidade, e.idEdital asc ";

        //die('<pre>'.$sql);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function relatorio3($idEdital = null, $idUf = null, $idMunicipio = null, $idFundo = null, $idClassificacao = null)
    {
        $sql = "select
                        uf.Sigla as c1,
                        mun.Descricao as c2,
                        pro.AnoProjeto+pro.Sequencial as c3,
                        ppr.NomeProjeto as c4,
                        Agentes.dbo.fnnome(ppr.idAgente) as c5,
                    e.nrEdital as c6,
                    fd.nmFormDocumento as c7,
                    CONVERT(VARCHAR(10), envio.DtEnvio, 103) AS c8,
                    CONVERT(VARCHAR(10), ultimaAvaliacao.DtAvaliacao, 103) AS c9,
                    pro.IdPRONAC as c10
                from SAC.dbo.PreProjeto ppr
                    inner join SAC.dbo.Edital e on (ppr.idEdital = e.idEdital)
                    inner join CONTROLEDEACESSO..SGCacesso s on (ppr.idUsuario = s.IdUsuario)
                    left join BDCORPORATIVO.scQuiz.tbFormDocumento fd on fd.idEdital = ppr.idEdital and idClassificaDocumento not in (23,24,25)
                    inner join BDCORPORATIVO.scquiz.tbformdocumentoProjeto b on (ppr.idPreProjeto = b.idProjeto)
                    inner join SAC.dbo.Projetos pro on (pro.idProjeto = ppr.idPreProjeto)
                    left join SAC.dbo.tbAvaliacaoProposta envio on (envio.idProjeto = ppr.idPreProjeto and envio.ConformidadeOK = 9)
                        left join SAC.dbo.tbAvaliacaoProposta ultimaAvaliacao on (ultimaAvaliacao.idProjeto = ppr.idPreProjeto and ultimaAvaliacao.stEstado = 0)
                    left join AGENTES..EnderecoNacional en on (en.idAgente = ppr.idAgente and Status = 1)
                    left join AGENTES..UF uf on (uf.idUF = en.UF)
                    left join AGENTES..Municipios mun on (mun.idMunicipioIBGE = en.Cidade) ";


        if ($idEdital) {
            $sql .= " where e.idEdital = '$idEdital' ";
        }

        if ($idUf) {
            if ($idEdital) {
                $sql .= " and en.UF = '$idUf' ";
            } else {
                $sql .= " where en.UF = '$idUf' ";
            }
        }
        if ($idMunicipio) {
            $sql .= " and en.Cidade = '$idMunicipio' ";
        }
        if ($idFundo) {
            if ($idEdital || $idUf) {
                $sql .= " and e.cdTipoFundo = $idFundo";
            } else {
                $sql .= " where e.cdTipoFundo = $idFundo";
            }
        }
        if ($idClassificacao) {
            if ($idEdital || $idUf || $idFundo) {
                $sql .= " and fd.idClassificaDocumento = $idClassificacao";
            } else {
                $sql .= " where fd.idClassificaDocumento = $idClassificacao";
            }
        }

        $sql .= " group by pro.AnoProjeto+Sequencial,e.idEdital,uf.Sigla,en.Cidade,mun.Descricao,ppr.NomeProjeto, Agentes.dbo.fnnome(ppr.idAgente),e.NrEdital,
                     fd.nmFormDocumento, envio.DtEnvio,ultimaAvaliacao.DtAvaliacao,pro.IdPRONAC ";
        $sql .= " order by ppr.NomeProjeto, uf.Sigla, Cidade, e.idEdital asc ";
        //die('<pre>'.$sql);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        return $db->fetchAll($sql);
    }

    public static function relatorio4($idEdital = null, $idUf = null, $idMunicipio = null, $idFundo = null, $idClassificacao = null)
    {
        $sql = "select
                        uf.Sigla as c1,
                        mun.Descricao as c2,
                        ppr.idPreProjeto as c3,
                        ppr.NomeProjeto as c4,
                        Agentes.dbo.fnnome(ppr.idAgente) as c5,
                        e.nrEdital as c6,
                        fd.nmFormDocumento as c7,
                        CONVERT(VARCHAR(10), envio.DtEnvio, 103) AS c8,
                        CONVERT(VARCHAR(10), ultimaAvaliacao.DtAvaliacao, 103) AS c9
                from SAC.dbo.PreProjeto ppr
                    inner join SAC.dbo.Edital e on (ppr.idEdital = e.idEdital)
                    inner join CONTROLEDEACESSO..SGCacesso s on (ppr.idUsuario = s.IdUsuario)
                    inner join BDCORPORATIVO.scquiz.tbformdocumentoProjeto b on (ppr.idPreProjeto = b.idProjeto)
                    left join SAC.dbo.Projetos pro on  pro.idProjeto = ppr.idPreProjeto
                    left join SAC.dbo.tbAvaliacaoProposta envio on (envio.idProjeto = ppr.idPreProjeto and envio.ConformidadeOK = 9)
                        inner join SAC.dbo.tbAvaliacaoProposta ultimaAvaliacao on (ultimaAvaliacao.idProjeto = ppr.idPreProjeto and ultimaAvaliacao.ConformidadeOK = 1 and ultimaAvaliacao.stEstado = 0)
                    left join AGENTES..EnderecoNacional en on (en.idAgente = ppr.idAgente and Status = 1)
                    left join AGENTES..UF uf on (uf.idUF = en.UF)
                    left join AGENTES..Municipios mun on (mun.idMunicipioIBGE = en.Cidade)
                    left join BDCORPORATIVO.scQuiz.tbFormDocumento fd on fd.idEdital = ppr.idEdital and idClassificaDocumento not in (23,24,25)
                where  pro.idProjeto is null ";


        if ($idEdital) {
            $sql .= " AND e.idEdital = '$idEdital' ";
        }
        if ($idUf) {
            $sql .= " and en.UF = '$idUf' ";
        }
        if ($idMunicipio) {
            $sql .= " and en.Cidade = '$idMunicipio' ";
        }
        if ($idFundo) {
            $sql .= " AND e.cdTipoFundo = $idFundo";
        }
        if ($idClassificacao) {
            $sql .= " AND fd.idClassificaDocumento = $idClassificacao";
        }

        $sql .= " group by ppr.idPreProjeto,e.idEdital,uf.Sigla,en.Cidade,mun.Descricao,ppr.NomeProjeto, Agentes.dbo.fnnome(ppr.idAgente),e.NrEdital,
                     fd.nmFormDocumento, envio.DtEnvio,ultimaAvaliacao.DtAvaliacao ";
        $sql .= " order by ppr.NomeProjeto, uf.Sigla, Cidade, e.idEdital asc ";

        //die('<pre>'.$sql);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function consultaFundos()
    {
        $sql = "select idVerificacao as idFundo, Descricao as nmFundo
                    from SAC..Verificacao
                    where idTipo = 15 order by nmFundo";


        //die('<pre>'.$sql);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function consultaClassificacoes()
    {
        $sql = "select cd.idClassificaDocumento as idClassificacao,cd.dsClassificaDocumento as nmClassificacao from BDCORPORATIVO.scSAC.tbClassificaDocumento cd
                    inner join BDCORPORATIVO.scQuiz.tbFormDocumento fd on fd.idClassificaDocumento = cd.idClassificaDocumento
                    where
                    fd.stModalidadeDocumento is not null
                    group by cd.idClassificaDocumento,cd.dsClassificaDocumento
                    order by cd.dsClassificaDocumento";


        //die('<pre>'.$sql);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function consultaEditais($idFundo = null, $idClassificacao = null)
    {
        $sql = "select e.idEdital as id, fd.nmFormDocumento as descricao, e.nrEdital
                from SAC.dbo.Edital e
                    inner join BDCORPORATIVO.scQuiz.tbFormDocumento fd on e.idEdital = fd.idEdital and fd.idClassificaDocumento not in (23,24,25)
                    inner join SAC.dbo.PreProjeto ppr on ppr.idEdital = e.idEdital ";


        if ($idFundo) {
            $sql .= "where e.cdTipoFundo = $idFundo ";
        }

        if ($idClassificacao) {
            if ($idFundo) {
                $sql .= "and fd.idClassificaDocumento = $idClassificacao ";
            } else {
                $sql .= "where fd.idClassificaDocumento = $idClassificacao ";
            }
        }

        $sql .= "group by e.idEdital, e.NrEdital,fd.nmFormDocumento order by 2";

        //die('<pre>'.$sql);


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
