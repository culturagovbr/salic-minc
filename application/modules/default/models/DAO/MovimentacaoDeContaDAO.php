<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbrangenciaDAO
 *
 * @author 01129075125
 */
class MovimentacaoDeContaDAO extends Zend_Db_Table
{
    public static function enviarEmail($email, $texto)
    {
        $sql = "EXEC msdb.dbo.sp_send_dbmail    @profile_name = 'PerfilGrupoPRONAC',
                                @recipients = '".$email."',
                                @body = '".$texto."',
                                @body_format = 'HTML',
                                @subject = 'Dilig�ncia na capta��o do Projeto',
                                @exclude_query_output = 1;";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }


    public static function buscarProjeto($agencia, $conta)
    {
        $sql = "SELECT AnoProjeto, Sequencial " .
                "FROM SAC.dbo.ContaBancaria " .
                "WHERE Agencia = '".$agencia."' AND ContaBloqueada = '".$conta."' OR ContaLivre = '".$conta."'";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarProponente($cpf_cnpj)
    {
        $sql = "SELECT A.CNPJCPF, N.Descricao as nome, I.Descricao as email
				FROM AGENTES.dbo.Agentes A, AGENTES.dbo.Nomes N, AGENTES.dbo.Internet I
				WHERE A.CNPJCPF = " .$cpf_cnpj. "
				AND A.idAgente = N.idAgente
				AND A.idAgente = I.idAgente";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarPatrocinador($cpf_cnpj)
    {
        $sql = "SELECT N.descricao nome 
				FROM AGENTES.dbo.Agentes A, AGENTES.dbo.Nomes N
        		WHERE A.idAgente = N.idAgente
        		AND A.CNPJCPF = '".$cpf_cnpj."'";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
    
    public static function buscarEnquadramento($PRONAC)
    {
        $sql = "SELECT Enquadramento FROM SAC.dbo.Enquadramento WHERE AnoProjeto+Sequencial = ".$PRONAC;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarVigenciaExecucao($data, $pronac)
    {
        $sql = "SELECT AnoProjeto+Sequencial as PRONAC, " .
                "CONVERT(CHAR(10), DtInicioExecucao,103), " .
                "CONVERT(CHAR(10), DtFimExecucao,103) " .
                "FROM SAC.dbo.Projetos " .
                "WHERE AnoProjeto+Sequencial = '".$pronac."' " .
                "AND '".$data."' between DtInicioExecucao AND DtFimExecucao";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarVigenciaCaptacao($data, $pronac)
    {
        $sql = "SELECT AnoProjeto+Sequencial as PRONAC, " .
                "CONVERT(CHAR(10), DtInicioCaptacao,103), " .
                "CONVERT(CHAR(10), DtFimCaptacao,103) " .
                "FROM SAC.dbo.Aprovacao " .
                "Where AnoProjeto+Sequencial = '".$pronac."' " .
                "AND '".$data."' between DtInicioCaptacao AND DtFimCaptacao";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }



    public static function salvaCaptacaoOK($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->insert('SAC.dbo.Captacao', $dados);
    }


    public static function salvaCaptacaoErro($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->insert('SAC.dbo.tbTmpCaptacao', $dados);
        return $db->lastInsertId();
    }

    public static function salvaContigencia($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->insert('SAC.dbo.tbTmpInconsistenciaCaptacao', $dados);
    }

    public static function deletaContigencia()
    {
        $sql = "DELETE FROM SAC.dbo.tbTmpInconsistenciaCaptacao ";
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($sql);
    }


    public static function verificacaoDados()
    {
        $sql = "SELECT idTmpCaptacao,
				nrAnoProjeto,
				nrSequencial,
				CONVERT(CHAR(10), dtChegadaRecibo,111) as dtChegadaRecibo,
				nrCpfCnpjProponente,
				nrCpfCnpjIncentivador,
				CONVERT(CHAR(10), dtCredito,111) as dtCredito,
				vlValorCredito,
				cdPatrocinio 
					FROM SAC.dbo.tbTmpCaptacao
					WHERE nrAnoProjeto is not null AND nrAnoProjeto != '' OR nrSequencial is not null AND nrSequencial != ''";
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }
}
