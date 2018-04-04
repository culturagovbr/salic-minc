<?php

class CaptacaoDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "SAC.dbo.Captacao";
    protected $_nome    = "Captacao";
    protected $_primary = "IdCaptacao";

    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("SAC.dbo.Captacao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()



    /**
     * M�todo para cadastrar os erros
     * @access public
     * @static
     * @param array $dados
     * @return integer
     */
    public static function cadastrarErro($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $db->insert("SAC.dbo.tbTmpCaptacao", $dados);
        return $db->lastInsertId();
    } // fecha m�todo cadastrarErro()



    /**
     * M�todo para cadastrar as insconsist�ncias
     * @access public
     * @static
     * @param array $dados
     * @return bool
     */
    public static function cadastrarInconsistencia($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("SAC.dbo.tbTmpInconsistenciaCaptacao", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrarInconsistencia()



    /**
     * M�todo para excluir as inconsist�ncias
     * @access public
     * @static
     * @param void
     * @return object
     */
    public static function excluirInconsistencia()
    {
        $sql = "DELETE FROM SAC.dbo.tbTmpInconsistenciaCaptacao ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($sql);
    } // fecha m�todo excluirInconsistencia()



    /**
     * M�todo para buscar os erros
     * @access public
     * @static
     * @param void
     * @return object
     */
    public static function buscarErro()
    {
        $sql = "SELECT idTmpCaptacao
					,nrAnoProjeto
					,nrSequencial
					,CONVERT(CHAR(10), dtChegadaRecibo,111) AS dtChegadaRecibo
					,nrCpfCnpjProponente
					,nrCpfCnpjIncentivador
					,CONVERT(CHAR(10), dtCredito,111) AS dtCredito
					,vlValorCredito
					,cdPatrocinio 
				FROM SAC.dbo.tbTmpCaptacao 
				WHERE nrAnoProjeto IS NOT NULL AND nrAnoProjeto != '' OR nrSequencial IS NOT NULL AND nrSequencial != ''";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    } // fecha m�todo buscarErro()

    public static function buscarProjetos($pronac)
    {
        $sql = "select distinct p.AnoProjeto, p.Sequencial, p.AnoProjeto+p.Sequencial as pronac, p.NomeProjeto, p.Processo, p.CgcCpf, 
				p.Area as codArea, p.Segmento as codSegmento, p.Mecanismo as codMecanismo, p.SolicitadoReal, p.UfProjeto, a.AprovadoReal,
				ar.Descricao as Area, s.Descricao as Segmento, m.Descricao as Mecanismo, p.Situacao
				from SAC.dbo.Projetos p
				left join SAC.dbo.Captacao c on p.AnoProjeto+p.Sequencial = c.AnoProjeto+c.Sequencial
				left join SAC.dbo.Aprovacao a on a.IdPRONAC = p.IdPRONAC
				inner join SAC.dbo.Area ar on ar.Codigo = p.Area
				left join SAC.dbo.Segmento s on s.Codigo = p.Segmento
				inner join SAC.dbo.Mecanismo m on m.Codigo = p.Mecanismo
				where (p.Situacao = 'E10' or p.Situacao = 'E12' or p.Situacao = 'E13') and p.AnoProjeto+p.Sequencial = '{$pronac}' and a.AprovadoReal != 0";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    public static function buscarCaptacao($pronac)
    {
        $sql = "select SUM(CaptacaoReal) as captado from SAC.dbo.Captacao 
				where AnoProjeto+Sequencial = '$pronac'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarErro()
    

    /**
     * M�todo para gerar o relat�rio com as capta��es contendo erros
     * @access public
     * @static
     * @param string $pronac
     * @param string $dtChegada
     * @param string $proponente
     * @param string $incentivador
     * @param string $dtCredito
     * @return object
     */
    public static function gerarRelatorioErro($pronac = null, $dtChegada = null, $proponente = null, $incentivador = null, $dtCredito = null)
    {
        $sql = "SELECT c.idTmpCaptacao
					,c.nrAnoProjeto
					,c.nrSequencial
					,c.dtChegadaRecibo
					,c.nrCpfCnpjProponente
					,c.nrCpfCnpjIncentivador
					,c.dtCredito
					,c.vlValorCredito
					,c.cdPatrocinio
					,i.idTipoInconsistencia
					,i.dsTipoInconsistencia

				FROM SAC.dbo.tbTmpCaptacao AS c
					,SAC.dbo.tbTmpInconsistenciaCaptacao AS ic
					,SAC.dbo.tbTipoInconsistencia AS i

				WHERE   c.idTmpCaptacao        = ic.idTmpCaptacao
					AND i.idTipoInconsistencia = ic.idTipoInconsistencia ";

        if (!empty($pronac)) { // busca pelo pronac
            $sql.= "AND c.nrAnoProjeto+c.nrSequencial = '" . $pronac . "' ";
        }

        if (!empty($dtChegada)) { // busca pela data de chegada
            $dtChegadaInicio = $dtChegada[0];
            $dtChegadaFim    = $dtChegada[1];

            if (!empty($dtChegadaInicio) && !empty($dtChegadaFim)) {
                $sql.= "AND c.dtChegadaRecibo BETWEEN '$dtChegadaInicio' AND '$dtChegadaFim' ";
            } else {
                if (!empty($dtChegadaInicio)) {
                    $sql.= "AND c.dtChegadaRecibo >= '$dtChegadaInicio' ";
                }
                if (!empty($dtChegadaFim)) {
                    $sql.= "AND c.dtChegadaRecibo <= '$dtChegadaFim' ";
                }
            }
        }

        if (!empty($proponente)) { // busca pelo cpf/cnpj do proponente
            $sql.= "AND c.nrCpfCnpjProponente = '" . $proponente . "' ";
        }

        if (!empty($incentivador)) { // busca pelo cpf/cnpj do incentivador
            $sql.= "AND c.nrCpfCnpjIncentivador = '" . $incentivador . "' ";
        }

        if (!empty($dtCredito)) { // busca pela data de cr�dito
            $dtCreditoInicio = $dtCredito[0];
            $dtCreditoFim    = $dtCredito[1];

            if (!empty($dtCreditoInicio) && !empty($dtCreditoFim)) {
                $sql.= "AND c.dtCredito BETWEEN '$dtCreditoInicio' AND '$dtCreditoFim' ";
            } else {
                if (!empty($dtCreditoInicio)) {
                    $sql.= "AND c.dtCredito >= '$dtCreditoInicio' ";
                }
                if (!empty($dtChegadaFim)) {
                    $sql.= "AND c.dtCredito <= '$dtCreditoFim' ";
                }
            }
        }

        $sql.= "ORDER BY c.dtChegadaRecibo, c.dtCredito, c.nrAnoProjeto+c.nrSequencial DESC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } 
} 
