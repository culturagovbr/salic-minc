<?php
class PedidoAlteracaoDAO extends Zend_Db_Table
{
    public static function buscarAlteracaoNomeProjeto($idpedidoalteracao)
    {
        $sql = "select tpap.idPedidoAlteracao,
                            CAST(pr.ResumoProjeto AS TEXT) AS Objetivos,
                            CAST(tprop.nmProjeto AS TEXT) AS nmProjeto,
                            CAST(tpa.dsJustificativa AS TEXT) AS dsJustificativa
                        from
                            BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpap
                            JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = tpap.IdPRONAC
                            JOIN SAC.dbo.PreProjeto prep on prep.idPreProjeto = pr.idProjeto
                            JOIN SAC.dbo.tbProposta tprop on tprop.idPedidoAlteracao = tpap.idPedidoAlteracao
                            JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = tpap.idPedidoAlteracao
                        where
                            tpap.IdPRONAC = $idpedidoalteracao and tpa.tpAlteracaoProjeto = 5 
                        ORDER BY tpap.idPedidoAlteracao DESC";
   
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }



    public static function buscarAlteracaoRazaoSocial($idPronac)
    {
        $sql = "select tpap.idPedidoAlteracao,
                        CAST(rs.nmProponente AS TEXT) as nmRazaoSocial,
                        rs.nrCNPJCPF as CgcCpf,
                        CAST(tpa.dsJustificativa AS TEXT) AS dsJustificativa
                    from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpap
                        INNER JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = tpap.IdPRONAC
                        LEFT JOIN SAC.dbo.tbProposta tprop on tprop.idPedidoAlteracao = tpap.idPedidoAlteracao
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = tpap.idPedidoAlteracao
                        INNER JOIN BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente rs on rs.idPedidoAlteracao = tpap.idPedidoAlteracao
                    where
                        tpap.IdPRONAC = $idPronac and tpa.tpAlteracaoProjeto = 2";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }


    public static function buscarAlteracaoLocalRealizacao($idPronac, $idPedidoAlteracao = null)
    {
        $sql = "select
                        abran.*
                    from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpap
                        JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = tpap.IdPRONAC
                        JOIN SAC.dbo.tbProposta tprop on tprop.idPedidoAlteracao = tpap.idPedidoAlteracao
                        JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = tpap.idPedidoAlteracao
                        JOIN SAC.dbo.tbAbrangencia abran on abran.idPedidoAlteracao = tpap.idPedidoAlteracao
                    where
                        tpap.IdPRONAC = $idPronac and tpap.idPedidoAlteracao = $idPedidoAlteracao and tpa.tpAlteracaoProjeto = 4";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }


    public static function buscarAlteracaoNomeProponente($idPronac)
    {
        $sql = "select tpap.idPedidoAlteracao,
                        CAST(nom.nmProponente AS TEXT) as proponente,
                        nom.nrCNPJCPF as CgcCpf,
                        CAST(tpa.dsJustificativa AS TEXT) AS dsJustificativa
                    from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpap
                        inner JOIN SAC.dbo.Projetos pr on pr.IdPRONAC = tpap.IdPRONAC
                        left join SAC.dbo.tbProposta tprop on tprop.idPedidoAlteracao = tpap.idPedidoAlteracao
                        inner JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpa on tpa.idPedidoAlteracao = tpap.idPedidoAlteracao
                        inner JOIN BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente nom on nom.idPedidoAlteracao = tpap.idPedidoAlteracao
                    where
                        tpap.IdPRONAC = $idPronac and tpa.tpAlteracaoProjeto = 1";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }


    public static function buscarAlteracaoFichaTecnica($idPronac)
    {
        $sql = "select tpa.idPedidoAlteracao,
                        CAST(pro.dsFichaTecnica AS TEXT) AS dsFichaTecnica,
                        CAST(tpxa.dsJustificativa AS TEXT) AS dsJustificativa
                from
                        SAC.dbo.tbProposta pro
                        inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa on tpa.idPedidoAlteracao = pro.idPedidoAlteracao
                        inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = tpa.idPedidoalteracao
                where
                    tpa.IdPRONAC = {$idPronac} and tpxa.tpAlteracaoProjeto = 3 ORDER BY tpa.idPedidoAlteracao DESC
                ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }

    public static function buscarAlteracaoPropostaPedagogica($idPronac)
    {
        $sql = "select tpa.idPedidoAlteracao 
                from
                        SAC.dbo.tbProposta pro
                        inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa on tpa.idPedidoAlteracao = pro.idPedidoAlteracao
                        inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = tpa.idPedidoalteracao
                where
                    tpa.IdPRONAC = {$idPronac} and tpxa.tpAlteracaoProjeto = 6 ORDER BY tpa.idPedidoAlteracao DESC
                ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }

    public static function buscarAlteracaoFichaTecnicaFinal($idPronac, $idPedidoAlteracao = null)
    {
        $sql = "select
                        CAST(pro.dsFichaTecnica AS TEXT) AS dsFichaTecnica,
                        CAST(tpxa.dsJustificativa AS TEXT) AS dsJustificativa
                from
                        SAC.dbo.tbProposta pro
                        inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa on tpa.idPedidoAlteracao = pro.idPedidoAlteracao
                        inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = tpa.idPedidoalteracao
                where
                    tpa.IdPRONAC = {$idPronac} and tpa.idPedidoAlteracao = $idPedidoAlteracao and tpxa.tpAlteracaoProjeto = 3
                ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public static function buscarAlteracaoPrazoExecucao($idPronac)
    {
        /*$sql ="select
                pro.dtInicioExecucao,
                pro.dtFimExecucao,
                pro.dsJustificativa
            from
                SAC.dbo.tbProposta pro
                inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto pap on pap.idPedidoAlteracao = pro.idPedidoAlteracao
                inner join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = pap.idPedidoAlteracao
            where
                pro.idPedidoAlteracao = {$idpedidoalteracao} and tpxa.tpAlteracaoProjeto = 10";*/

        $sql = "select pap.idPedidoAlteracao,
                        pp.dtInicioNovoPrazo,
                        pp.dtFimNovoPrazo,
                        CAST(tpxa.dsJustificativa AS TEXT) AS dsJustificativa,
                        proj.DtInicioExecucao,
                        proj.DtFimExecucao
                    from
                        BDCORPORATIVO.scSAC.tbProrrogacaoPrazo pp
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto pap on pap.idPedidoAlteracao = pp.idPedidoAlteracao
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = pap.idPedidoAlteracao
                        INNER JOIN BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto tap on tap.tpAlteracaoProjeto = tpxa.tpAlteracaoProjeto
                        INNER JOIN SAC.dbo.Projetos proj on proj.IdPRONAC = pap.IdPRONAC
                    where
                        pap.IdPRONAC = {$idPronac} and pp.tpProrrogacao = 'E' and tap.tpAlteracaoProjeto = 9 
                    ORDER BY pap.idPedidoAlteracao DESC";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }

    public static function buscarAlteracaoPrazoCaptacao($idPronac)
    {
        $sql = "select top 1 pap.idPedidoAlteracao,
                        pp.dtInicioNovoPrazo,
                        pp.dtFimNovoPrazo,
                        CAST(tpxa.dsJustificativa AS TEXT) AS dsJustificativa,
                        proj.DtInicioExecucao,
                        proj.DtFimExecucao,
                        aprov.DtInicioCaptacao,
                        aprov.DtFimCaptacao
                    from
                        BDCORPORATIVO.scSAC.tbProrrogacaoPrazo pp
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto pap on pap.idPedidoAlteracao = pp.idPedidoAlteracao
                        INNER join BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao tpxa on tpxa.idPedidoAlteracao = pap.idPedidoAlteracao
                        INNER JOIN BDCORPORATIVO.scSAC.tbTipoAlteracaoProjeto tap on tap.tpAlteracaoProjeto = tpxa.tpAlteracaoProjeto
                        INNER JOIN SAC.dbo.Projetos proj on proj.IdPRONAC = pap.IdPRONAC
                        INNER JOIN SAC.dbo.Aprovacao aprov on aprov.AnoProjeto+aprov.Sequencial = proj.AnoProjeto+proj.Sequencial
                    where
                        pap.IdPRONAC = $idPronac and pp.tpProrrogacao = 'C' and tap.tpAlteracaoProjeto = 8 and aprov.TipoAprovacao in (1,3)
                    order by pap.idPedidoAlteracao desc";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        $resultado = $db->fetchRow($sql);

        return $resultado;
    }

    public static function salvarComentarioAlteracaoProj($dados)
    {
        $sql = "insert into SAC.dbo.tbDiligencia
                        (
                            idPronac,
                            idTipoDiligencia,
                            DtSolicitacao,
                            Solicitacao,
                            idSolicitante,
                            stEstado
                        )
                    values
                        (
                            {$dados['idPronac']},
                            124,
                            GETDATE(),
                            '{$dados['Solicitacao']}',
                            {$dados['idSolicitante']},
                            0
                        )";

        die($sql);
    }
}
