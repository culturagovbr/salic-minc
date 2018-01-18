<?php
class Termo extends Zend_Db_Table
{
    public function buscarReuniao()
    {
        $sql0 = " select MAX(NrReuniao) as NrReuniao
                            from
                            SAC.dbo.tbReuniao as reuniao
                            where
                            reuniao.stEstado = 1 and reuniao.stPlenaria = 'E'";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql0);
    }
        
    public function buscarTermo($NrReuniao)
    {
        $sql = "select  tr.idNrReuniao, 
		tr.NrReuniao, 
		tr.stEstado,
		tr.DtFinal,
        pr.IdPRONAC, 
        pr.NomeProjeto,
        tp.stAnalise, 
        ap.AprovadoReal
		from BDCORPORATIVO.scSAC.tbPauta tp
        inner join SAC.dbo.Projetos pr  on pr.IdPRONAC = tp.IdPRONAC
        inner join SAC.dbo.tbReuniao tr on tp.idNrReuniao = tr.idNrReuniao
        join SAC.dbo.Aprovacao ap on pr.IdPRONAC = ap.IdPRONAC
		where
        tr.NrReuniao = '$NrReuniao'   --/*parametro para a consulta
        and tp.stAnalise in ('AS', 'IS', 'AC', 'IC', 'AR')
        and tr.stEstado = 1 
        and ap.dtaprovacao in(select max(dtaprovacao) from sac.dbo.aprovacao where idpronac=pr.IdPRONAC);";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }


    public function gerarTermo($pronac)
    {
        $sql = "        SELECT
              tr.idNrReuniao,
              pr.AnoProjeto+pr.Sequencial as pronac,
		    tr.NrReuniao,
		    pr.IdPRONAC,
		    pr.NomeProjeto,
		    case 
                     when tp.stAnalise = 'AS' then 'Deferido pela CNIC'
                     when tp.stAnalise = 'IS' then 'Indeferido pela CNIC'
                     when tp.stAnalise = 'AC' then 'Deferido pelo Conselheiro Cultural'
                     when tp.stAnalise = 'IC' then 'Indeferido pelo Conselheiro Cultural'
                     when tp.stAnalise = 'AR' then 'Aprovado por AD-REFERENDUM'
                    end stAnalise
                    ,
		    ap.AprovadoReal,
		    ap.ResumoAprovacao,
              case
		    when ap.TipoAprovacao = 1 then 'Aprova��o Inicial'
		    when ap.TipoAprovacao = 2 then 'Complementa��o'
		    when ap.TipoAprovacao = 3 then 'Prorroga��o de Prazo'
		    when ap.TipoAprovacao = 4 then 'Redu��o'
		    end as tipoaprovacao
      		FROM SAC.dbo.Projetos pr
		    INNER JOIN BDCORPORATIVO.scSAC.tbPauta tp ON pr.IdPRONAC = tp.IdPRONAC
		    INNER JOIN SAC.dbo.tbReuniao tr ON tp.idNrReuniao = tr.idNrReuniao
		    INNER JOIN SAC.dbo.Parecer par on par.idPRONAC = pr.IdPRONAC
		    Inner JOIN SAC.dbo.Aprovacao ap ON pr.IdPRONAC = ap.IdPRONAC
		WHERE
		    pr.IdPRONAC IN ($pronac)
		    AND tp.stAnalise IN ('AS', 'IS', 'AC', 'IC', 'AR')
		    AND tr.stEstado = 1 and tr.stPlenaria = 'E'
                    AND par.stAtivo = 1
		    and ap.dtaprovacao in(select max(dtaprovacao) from sac.dbo.aprovacao where idpronac=pr.IdPRONAC)
		    ;
			" ;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
