<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of atualizaReuniao
 *
 * @author 01373930160
 */
class AtualizaReuniaoDAO extends Zend_Db_Table
{
    protected $_name = "SAC.dbo.tbReuniao";

    public static function atualizaReuniao($idReuniao, $valor)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $where = "idNrReuniao = " . $idReuniao;
        $alterar = $db->update("SAC.dbo.tbReuniao", $valor, $where);

        if ($alterar) {
            return true;
        }
        return $alterar;
    }

    public static function verificaPautaConsolidacao($idnrreuniao)
    {
        $sql = " select tp.IdPRONAC,
                 dsAnalise,
                 stAnalise
                 from BDCORPORATIVO.scSAC.tbPauta tp
                 INNER JOIN SAC.dbo.parecer par on par.idpronac = tp.idpronac and par.stAtivo = 1
                 INNER JOIN SAC.dbo.tbReuniao r on r.idNrReuniao = tp.idNrReuniao
                 where
                 tp.idnrreuniao = $idnrreuniao and
                 tp.stAnalise='AC' and tp.stEnvioPlenario='N' and tp.dtEnvioPauta < r.dtFinal " ;
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function verificaReuniao($idAgente, $idReuniao, $null=null, $verificar = null)
    {
        $sql = "SELECT
               tv.idPRONAC,
               pr.anoprojeto+sequencial as pronac,
               pr.nomeprojeto
               FROM BDCORPORATIVO.scSAC.tbVotacao tv
               join BDCORPORATIVO.scSAC.tbPauta tp on tp.IdPRONAC = tv.IdPRONAC and tp.idNrReuniao = tv.idNrReuniao
               join sac.dbo.projetos pr on pr.idpronac = tv.idpronac
               where tv.idNrReuniao = " . $idReuniao;

        if (!empty($verificar)) {
            $sql .= " and tp.stAnalise = 'AC' 
                or tp.stAnalise = 'IC' and
                tv.dtVoto is null AND
                tv.stVoto is null AND
                tv.dsJustificativa is null ";
        }
        if (!empty($null)) {
            $sql .="AND tv.idAgente = " . $idAgente . " AND
                tv.dtVoto is null AND
                tv.stVoto is null AND
                tv.dsJustificativa is null";
        }
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function verificaReuniaoAdministrativo($idReuniao)
    {
        $sql = "SELECT tv.idPRONAC
              , pr.AnoProjeto+pr.Sequencial as pronac
              , tp.stAnalise ,
               pr.NomeProjeto
               FROM BDCORPORATIVO.scSAC.tbVotacao tv
               join BDCORPORATIVO.scSAC.tbPauta tp on tp.IdPRONAC = tv.IdPRONAC and tp.idNrReuniao = tv.idNrReuniao
               join SAC.dbo.Projetos pr on pr.IdPRONAC = tv.IdPRONAC
               WHERE tp.stAnalise='AC' or tp.stAnalise='IC' and  tv.idNrReuniao = " . $idReuniao;
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message =  $e->getMessage();
        }
        return $db->fetchAll($sql);
    }

    public static function inciaVotacao($idPronac, $idAgente, $idReuniao)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $dados = array('IdPRONAC' => $idPronac, 'idAgente' => $idAgente, 'idNrReuniao' => $idReuniao);

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbVotacao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    public static function analisaReuniao($idreuniao=null)
    {
        $sql = "select idnrreuniao, stEstado, stPlenaria, CONVERT(VARCHAR(19), dtFinal, 21) as dtFinal from SAC.dbo.tbReuniao ";
        if ($idreuniao) {
            $sql .="where idnrreuniao=" . $idreuniao;
        } else {
            $sql .="where stEstado = 0";
        }

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
    }

    public static function inserirVotantes($votantes)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("BDCORPORATIVO.scSAC.tbvotante", $votantes);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    public static function selecionarvotantes($idreuniao)
    {
        $sql = "SELECT 
                tbv.idagente,
                nm.descricao
                from BDCORPORATIVO.scSAC.tbvotante tbv
                join agentes.dbo.nomes nm on nm.idagente = tbv.idagente
                where tbv.idreuniao =" . $idreuniao . " and nm.TipoNome=18 order by 2 asc";

//        die($sql);
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }
}
