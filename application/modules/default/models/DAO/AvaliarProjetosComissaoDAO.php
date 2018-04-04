<?php

class AvaliarProjetosComissaoDAO extends Zend_Db_Table
{
    public static function buscaRegiao()
    {
        $sql = "select distinct Regiao from SAC.dbo.Uf ";
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Etapas: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($sql);
    }
    
    public static function buscaUF($regiao = null)
    {
        if ($regiao) {
            $sql = "select * from SAC.dbo.Uf where Regiao = '$regiao'";
        } else {
            $sql = "select * from SAC.dbo.Uf";
        }
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Etapas: " . $e->getMessage();
        }
        //die($sql);
        return $db->fetchAll($sql);
    }
    
    public static function buscaEdital()
    {
        $sql = "select edi.idEdital, edi.NrEdital,fod.nmFormDocumento as NomeEdital
                        from SAC.dbo.Projetos pro
                        inner join SAC.dbo.PreProjeto pp on pp.idPreProjeto = pro.idProjeto
                        inner join SAC.dbo.Edital edi on edi.idEdital = pp.idEdital
                        inner join BDCORPORATIVO.scQuiz.tbFormDocumento fod on fod.idEdital = edi.idEdital and fod.idClassificaDocumento not in (23,24,25)
                        INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoPreProjeto AS app ON app.idPreProjeto = pp.idPreProjeto
                        where pro.Situacao = 'G51'
                        group by edi.idEdital,edi.NrEdital,fod.nmFormDocumento
                        order by edi.NrEdital DESC";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Etapas: " . $e->getMessage();
        }
//
        return $db->fetchAll($sql);
    }
    
    public static function qtdNotas($idPreProjeto)
    {
        $sql = "select count(nrNotaFinal) as qtd from BDCORPORATIVO.scSAC.tbAvaliacaoPreProjeto where idPreProjeto = $idPreProjeto";
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao alterar a Nota: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
    }
    
    public static function alterarNota($nota, $idPreProjeto)
    {
        $sql = "update BDCORPORATIVO.scSAC.tbAvaliacaoPreProjeto set nrNotaFinal = $nota, dtAvaliacao = GETDATE() where idPreProjeto = $idPreProjeto";
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao alterar a Nota: " . $e->getMessage();
        }
      
        return $db->fetchAll($sql);
    }
    
    public static function aprovarProjeto($idPreProjeto, $nrNotalFinal, $justificativa = null, $stAprovacao = null, $aprovacao = null)
    {
        if (isset($aprovacao)) {
            $sql = "update BDCORPORATIVO.scSAC.tbAprovacaoPreProjeto set dtAvaliacao = GETDATE()";
            if (isset($stAprovacao)) {
                $sql .= ", stAprovacao = $stAprovacao";
            }
            if ($nrNotalFinal) {
                $sql .= ", nrNotaFinal = $nrNotalFinal";
            }
            if ($justificativa) {
                $sql .= ", dsJustificativa = '$justificativa'";
            }
            
            $sql .= " where idPreProjeto = $idPreProjeto";
        } else {
            $sql = "insert into 
					BDCORPORATIVO.scSAC.tbAprovacaoPreProjeto 
					(idPreProjeto, nrNotaFinal, dsJustificativa, dtAvaliacao, stAprovacao) 
					values($idPreProjeto, $nrNotalFinal, '$justificativa', GETDATE(), $stAprovacao)";
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao aprovar o projeto: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
    
    public static function buscarAprovacao($idPreProjeto)
    {
        $sql = "select * from BDCORPORATIVO.scSAC.tbAprovacaoPreProjeto where idPreProjeto = $idPreProjeto";
        
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao aprovar o projeto: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}
