<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of VotarProjetoCultural
 *
 * @author 01373930160
 */
class VotarProjetoCulturalDAO extends Zend_Db_table
{
    public static function verificaDadosProponente($idpronac = null, $idAgente = null)
    {
        $sql = "SELECT  DISTINCT
                nm.Descricao as nomes,
                nm.idagente,
                pr.IdPRONAC,
                case
                when ag.TipoPessoa = 1 then 'Pessoa Jurídica '
                when ag.TipoPessoa = 0 then 'Pessoa Física' end as tipopessoa,
                pr.CgcCpf,
                endn.Logradouro,
                endn.Bairro,
                endn.Cep,
                mun.Descricao as cidade,
                uf.Descricao as uf,
                nat.Esfera,
                nat.Direito,
                nat.Administracao
                FROM  SAC.dbo.Projetos pr
                left JOIN AGENTES.dbo.Agentes ag ON ag.CNPJCPF = pr.CgcCpf
                left JOIN AGENTES.dbo.Nomes nm ON  nm.idAgente  = ag.idAgente
                left JOIN AGENTES.dbo.EnderecoNacional endn on endn.idAgente = ag.idAgente
                left join AGENTES.dbo.Natureza nat on nat.idAgente = ag.idAgente
                left join AGENTES.dbo.Municipios mun on mun.idMunicipioIBGE = endn.Cidade
                left join AGENTES.dbo.UF uf on uf.idUF = endn.UF";
        
        if (! empty($idpronac)) {
            $sql .= " where pr.idpronac=$idpronac";
        }
        if ($idAgente) {
            $sql .= " where ag.idAgente=$idAgente";
        }
        
        try {
            $db = Zend_Registry::get('db');
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        //        return $sql; die;
        return $db->fetchAll($sql);
    }
    
    public static function buscarTelefones($idpronac)
    {
        $sql = "SELECT
                tel.TipoTelefone,
                uf.Descricao as uf,
                tel.DDD,
                tel.Numero,
                tel.Divulgar
                FROM AGENTES.dbo.Telefones tel
                join AGENTES.dbo.Agentes ag on ag.idAgente = tel.idAgente
                join AGENTES.dbo.UF uf on uf.idUF = tel.UF
                join SAC.dbo.Projetos pr on pr.CgcCpf = ag.CNPJCPF
                where pr.IdPRONAC = " . $idpronac;
        
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        return $db->fetchAll($sql);
    }
    
    public static function buscarEmail($idpronac)
    {
        $sql = "select
                inte.TipoInternet as tpemail,
                inte.Descricao as email
                from AGENTES.dbo.Internet inte
                join AGENTES.dbo.Agentes ag on ag.idAgente = inte.idAgente
                join SAC.dbo.Projetos pr on pr.CgcCpf = ag.CNPJCPF
                WHERE pr.idpronac =" . $idpronac;
        
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    public static function consultaValorAprovado($id = null)
    {
        $sql = "SELECT DISTINCT
                      SAC.dbo.Projetos.IdPRONAC AS Pronac,
                      BDCORPORATIVO.scSAC.tbPauta.idNrReuniao AS NumeroReuniao,
                      Projetos.NomeProjeto AS NomeProjeto,
                      tbReuniao.stEstado As StatusEstado,
                      BDCORPORATIVO.scSAC.tbVotacao.stVoto AS StatusVoto,
                      tbPlanilhaAprovacao.nrOcorrencia AS NumeroOcorrencia,
                      tbPlanilhaAprovacao.vlUnitario AS ValorUnitario,
                      tbPlanilhaAprovacao.qtItem AS QuantidadeItem,
                      Projetos.Area AS CodigoArea,
                      Projetos.Segmento AS CodigoSegmento,
                      SAC.dbo.Area.Descricao AS DescricaoArea
                      ,( tbPlanilhaAprovacao.nrOcorrencia * tbPlanilhaAprovacao.vlUnitario * tbPlanilhaAprovacao.qtItem) AS Total
                FROM
                      SAC.dbo.Projetos INNER JOIN
                      BDCORPORATIVO.scSAC.tbPauta ON Projetos.IdPRONAC = BDCORPORATIVO.scSAC.tbPauta.IdPRONAC INNER JOIN
                      BDCORPORATIVO.scSAC.tbVotacao ON Projetos.IdPRONAC = BDCORPORATIVO.scSAC.tbVotacao.IdPRONAC INNER JOIN
                      SAC.dbo.tbReuniao ON BDCORPORATIVO.scSAC.tbPauta.idNrReuniao = tbReuniao.idNrReuniao INNER JOIN
                      SAC.dbo.tbPlanilhaAprovacao ON Projetos.IdPRONAC = tbPlanilhaAprovacao.IdPRONAC INNER JOIN
                      SAC.dbo.Area ON Projetos.Area = Area.Codigo
                WHERE     tbReuniao.stEstado = 0 ";
        
        if (! empty($id)) {
            $sql .= " AND SAC.dbo.Projetos.IdPRONAC = $id ";
        }
        
        try {
            $db = Zend_Registry::get('db');
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar os Tipos de Documentos: " . $e->getMessage();
        }
        
        return $db->fetchAll($sql);
    }
    
    public static function atualizaVotacao($dados, $idpronac, $idagente, $idnrreuniao)
    {
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        $where = "idpronac = " . $idpronac . " and idagente=" . $idagente . " and idnrreuniao=" . $idnrreuniao;
        $alterar = $db->update("BDCORPORATIVO.scSAC.tbVotacao", $dados, $where);
        
        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function resultadoVotacao($idnrreuniao, $idpronac, $stvoto)
    {
        $sql = "select
                count(stVoto) as qtdvotos
                from BDCORPORATIVO.scSAC.tbVotacao
                where idNrReuniao=" . $idnrreuniao . " and IdPRONAC=" . $idpronac;
        if ($stvoto) {
            $sql .= " and stVoto = '" . $stvoto . "'";
        }
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        return $db->fetchAll($sql);
    }
    
    public static function resultadodescricao($idnrreuniao, $idpronac)
    {
        $sql = "select
                nm.Descricao as nome,
                cast(tv.dsJustificativa AS TEXT) as justificativa
                from BDCORPORATIVO.scSAC.tbVotacao tv
                join Agentes.dbo.Nomes nm on nm.idAgente = tv.idAgente
                where nm.tiponome=18 and tv.dsjustificativa is not null and tv.idNrReuniao=" . $idnrreuniao . " and tv.IdPRONAC=" . $idpronac;
        
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    public static function atualizarreuniao($dados, $idnrreuniao, $idpronac)
    {
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        $where = "IdPRONAC = " . $idpronac . " and IdNrReuniao=" . $idnrreuniao;
        $alterar = $db->update("BDCORPORATIVO.scSAC.tbPauta", $dados, $where);
        
        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }

    public static function inserirConsolidacao($dados)
    {
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $alterar = $db->insert("BDCORPORATIVO.scSAC.tbconsolidacaovotacao", $dados);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function atualizarparaproximareuniao($dados, $idnrreuniao)
    {
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        
        $where = "stAnalise = 'AC' or stAnalise = 'IC' and IdNrReuniao=" . $idnrreuniao;
        $alterar = $db->update("BDCORPORATIVO.scSAC.tbPauta", $dados, $where);
        
        if ($alterar) {
            return true;
        } else {
            return false;
        }
    }
}
