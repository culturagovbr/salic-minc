<?php

/**
 * Modelo Telefone
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class AprovacaoDAO extends Zend_Db_Table
{
    protected $_name = 'BDCORPORATIVO.scSAC.tbPauta'; // nome da tabela

    #--verifica se tem algum componente na area e segmento selecionado--

    public static function buscarPedidosProjetosAprovados($idpronac=null, $situacao = null, $tipoaprovacao=null, $orgao=null)
    {
        $sql = "select
                pr.AnoProjeto+pr.Sequencial as pronac,
                pr.NomeProjeto,
                pr.CgcCpf,
                pr.idpronac,
                pr.Area as cdarea,
                pr.Segmento as cdseg,
                ar.Descricao as area,
                seg.Descricao as segmento,
                pr.ResumoProjeto,
                pr.UfProjeto,
                case when en.Enquadramento = 1 then '26'
                when en.Enquadramento = 2 then '18'
                end as enquadramento,
                en.Enquadramento as nrenq,
                en.Observacao,
                ap.DtInicioCaptacao,
                ap.DtFimCaptacao,
                pr.DtInicioExecucao,
                pr.DtFimExecucao,
                ap.AprovadoReal,
                ap.idAprovacao,
                tr.NrReuniao,
                nm.Descricao as nome
                from SAC.dbo.Projetos pr
                INNER JOIN SAC.dbo.Area ar on ar.Codigo = pr.Area
                INNER JOIN SAC.dbo.Segmento seg on seg.Codigo = pr.Segmento
                left JOIN SAC..Enquadramento en on en.IdPRONAC = pr.IdPRONAC
                INNER JOIN SAC..Aprovacao ap on ap.IdPRONAC = pr.IdPRONAC and ap.DtAprovacao in (select max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = pr.IdPRONAC)
                INNER JOIN BDCORPORATIVO.scSAC.tbPauta tp on tp.IdPRONAC = pr.IdPRONAC
                INNER JOIN SAC..tbReuniao tr on tr.idNrReuniao = tp.idNrReuniao
                INNER JOIN AGENTES..Agentes ag on ag.CNPJCPF = pr.CgcCpf
                INNER JOIN AGENTES..Nomes nm on nm.idAgente = ag.idAgente ";

        if ($situacao) {
            $sql .= " where pr.Situacao in ($situacao) ";
        }

        if ($tipoaprovacao) {
            $sql .= " and ap.TipoAprovacao in ($tipoaprovacao) ";
        }

//        if ($orgao)
//        {
//            $sql .= " and pr.orgao = $orgao" ;
//        }
        //die('<pre>'.$sql);
        if ($idpronac) {
            $sql .= " where pr.idpronac = $idpronac ";
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarPedidosReadProjetosAprovados()
    {
        $sql = "select
                pr.AnoProjeto+pr.Sequencial as pronac,
                pr.NomeProjeto,
                pr.CgcCpf,
                pr.idpronac,
                pr.Area as cdarea,
                pr.Segmento as cdseg,
                ar.Descricao as area,
                seg.Descricao as segmento,
                pr.ResumoProjeto,
                pr.UfProjeto,
                case when en.Enquadramento = 1 then '26'
                when en.Enquadramento = 2 then '18'
                end as enquadramento,
                en.Enquadramento as nrenq,
                en.Observacao,
                ap.DtInicioCaptacao,
                ap.DtFimCaptacao,
                pr.DtInicioExecucao,
                pr.DtFimExecucao,
                ap.AprovadoReal,
                tr.NrReuniao,
                nm.Descricao as nome
                from SAC.dbo.Projetos pr
                INNER JOIN SAC.dbo.Area ar on ar.Codigo = pr.Area
                INNER JOIN SAC.dbo.Segmento seg on seg.Codigo = pr.Segmento
                left JOIN SAC..Enquadramento en on en.IdPRONAC = pr.IdPRONAC
                INNER JOIN SAC..Aprovacao ap on ap.IdPRONAC = pr.IdPRONAC and ap.DtAprovacao in (select max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = pr.IdPRONAC)
                INNER JOIN BDCORPORATIVO.scSAC.tbPauta tp on tp.IdPRONAC = pr.IdPRONAC
                INNER JOIN SAC..tbReuniao tr on tr.idNrReuniao = tp.idNrReuniao
                INNER JOIN AGENTES..Agentes ag on ag.CNPJCPF = pr.CgcCpf
                INNER JOIN AGENTES..Nomes nm on nm.idAgente = ag.idAgente
                where (pr.Situacao = 'D40' and ap.TipoAprovacao= 2) or (pr.Situacao='D40' and ap.TipoAprovacao=4) or (ap.TipoAprovacao = 5)";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarCaptacaoRead($idpronac=null)
    {
        $sql = "select dtiniciocaptacao, dtfimcaptacao, PortariaAprovacao from sac.dbo.aprovacao where idpronac = $idpronac  --and tipoaprovacao=9";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function SomarAprovacao($idpronac=null)
    {
        $sql = "select SUM(AprovadoReal) as soma 
                from SAC..Aprovacao
                where idpronac = $idpronac
                and portariaaprovacao is not null";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_ASSOC);
        return $db->fetchRow($sql);
    }
    
    public static function SomarReadeqComplementacao($idpronac=null, $tipoaprovacao=null)
    {
        $sql = "select SUM(AprovadoReal) as soma
                from SAC..Aprovacao
                where idpronac = $idpronac
                and tipoaprovacao = $tipoaprovacao
                and portariaaprovacao is null";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_ASSOC);
        return $db->fetchRow($sql);
    }

    public static function alterarDadosProjetoAprovado($dados, $idpronac)
    {
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $where = "idpronac = $idpronac and dtAprovacao in (select max(dtAprovacao) from sac.dbo.aprovacao where idpronac = $idpronac)";
            $alterar = $db->update("SAC.dbo.Aprovacao", $dados, $where);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }
}
