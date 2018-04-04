<?php
/**
 * DAO Projeto
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class ProjetoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "BDCORPORATIVO.scSAC.Projetos";
    protected $_primary = "IdPRONAC";



    /**
     * M�todo para buscar os projetos (PRONAC)
     * Permite buscar pelo Ano/Sequencial do projeto
     * @access public
     * @static
     * @param string $pronac
     * @return object || bool
     */
    public static function buscar($pronac = null)
    {
        $sql = "SELECT AnoProjeto+Sequencial as pronac
					,IdPRONAC
					,AnoProjeto
					,Sequencial
					,UfProjeto
					,Area
					,Segmento
					,Mecanismo
					,NomeProjeto
					,Processo
					,CgcCpf
					,Situacao
					,DtProtocolo
					,DtAnalise
					,Modalidade
					,Orgao
					,OrgaoOrigem
					,DtSaida
					,DtRetorno
					,UnidadeAnalise
					,Analista
					,DtSituacao
					,ResumoProjeto
					,ProvidenciaTomada
					,Localizacao
					,DtInicioExecucao
					,DtFimExecucao
					,SolicitadoUfir
					,SolicitadoReal
					,SolicitadoCusteioUfir
					,SolicitadoCusteioReal
					,SolicitadoCapitalUfir
					,SolicitadoCapitalReal
					,Logon
					,idProjeto
				FROM SAC.dbo.Projetos ";

        if (!empty($pronac)) { // busca de acordo com o pronac
            $sql.= "WHERE AnoProjeto+Sequencial = '$pronac' ";
        }

        $sql.= "ORDER BY AnoProjeto+Sequencial";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha buscar()

    public static function buscarDadosProjeto($pronac)
    {
        $sql = "select distinct p.AnoProjeto, p.Sequencial, p.AnoProjeto+p.Sequencial as pronac, p.NomeProjeto, p.Processo, p.CgcCpf, 
				p.Area as codArea, p.Segmento as codSegmento, p.Mecanismo as codMecanismo, p.SolicitadoReal, p.UfProjeto,
				ar.Descricao as Area, s.Descricao as Segmento, m.Codigo as Mecanismo, m.Descricao as Descricao, p.Situacao
				from SAC.dbo.Projetos p
				inner join SAC.dbo.Area ar on ar.Codigo = p.Area
				inner join SAC.dbo.Segmento s on s.Codigo = p.Segmento
				inner join SAC.dbo.Mecanismo m on m.Codigo = p.Mecanismo
				where p.AnoProjeto+p.Sequencial = '$pronac'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    } // fecha buscarIdPronac()

    /**
     * M�todo para retornar o idPronac
     * @access public
     * @static
     * @param string $pronac
     * @return object || bool
     */
    public static function buscarIdPronac($pronac)
    {
        $sql = "SELECT TOP 1 IdPRONAC FROM SAC.dbo.Projetos WHERE AnoProjeto+Sequencial = '$pronac'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    } // fecha buscarIdPronac()



    /**
     * M�todo para retornar o pronac
     * @access public
     * @static
     * @param integer $idPronac
     * @return object || bool
     */
    public static function buscarPronac($idPronac)
    {
        $sql = "SELECT TOP 1 AnoProjeto+Sequencial AS pronac, idProjeto FROM SAC.dbo.Projetos WHERE IdPRONAC = $idPronac";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    } // fecha buscarPronac()



    /**
     * M�todo para alterar a situa��o de um projeto
     * @access public
     * @static
     * @param integer $idPronac
     * @param string $situacao
     * @return object || bool
     */
    public static function alterarSituacao($idPronac, $situacao)
    {
        $sql = "UPDATE SAC.dbo.Projetos SET Situacao = '$situacao' WHERE IdPRONAC = $idPronac";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    } // fecha alterarSituacao()



    /**
     * M�todo para buscar o hist�rico de situa��es do projeto
     * @access public
     * @static
     * @param string $pronac
     * @return object || bool
     */
    public static function buscarSituacoesProjeto($pronac)
    {
        $sql = "SELECT * FROM SAC.dbo.HistoricoSituacao WHERE AnoProjeto+Sequencial='$pronac' ORDER BY DtSituacao DESC;";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha buscarSituacoesProjeto()



    /**
     * M�todo para buscar o periodo de execu��o do projeto
     * @access public
     * @static
     * @param integer $idPronac
     * @param string $pronac
     * @return object || bool
     */
    public static function buscarPeriodoExecucao($idPronac = null, $pronac = null)
    {
        $sql = "SELECT DtInicioExecucao
					,DtFimExecucao
					,DATEDIFF(DAY, DtInicioExecucao, DtFimExecucao) AS dias
				FROM SAC.dbo.Projetos ";

        // busca pelo id pronac
        if (!empty($idPronac)) {
            $sql.= "WHERE IdPRONAC = " . $idPronac;
        }

        // busca pelo pronac
        if (!empty($pronac)) {
            $sql.= "WHERE AnoProjeto+Sequencial = '" . $pronac . "'";
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    } // fecha buscarPeriodoExecucao()
} // fecha class
