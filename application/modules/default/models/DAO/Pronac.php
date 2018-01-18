<?php
/**
 * Modelo Pronac
 * @author Equipe RUP - Politec
 * @since 29/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Pronac extends Zend_Db_Table
{
    protected $_name = 'BDCORPORATIVO.scSAC.Projetos'; // nome da tabela



    /**
     * M�todo para buscar PRONAC
     * @access public
     * @param integer $id
     * @return object $db->fetchAll($sql)
     */
    public static function buscar($id = null)
    {
        $sql = "SELECT AnoProjeto+Sequencial as nrpronac
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
					FROM SAC.dbo.Projetos  ";

        if (!empty($id)) { // busca de acordo com um id
            $sql.= "WHERE IdPRONAC = '" . $id . "' ";
        }

        $sql.= "ORDER BY IdPRONAC;";

//                die('<pre>'.$sql);
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar PRONAC: " . $e->getMessage();
        }
    } // fecha buscar()



    /**
     * M�todo para buscar o PRONAC com a an�lise de conte�do
     * @access public
     * @param integer $id
     * @return object $db->fetchAll($sql)
     */
    public static function buscarPronacAnaliseConteudo($idPRONAC = null, $idPRODUTO = null)
    {
        $sql = "SELECT proj.IdPRONAC 
					,proj.NomeProjeto
					,prod.Descricao AS DescricaoProduto
				FROM SAC.dbo.Produto prod
					,SAC.dbo.tbAnaliseConteudoConselheiro ana
					,BDCORPORATIVO.scSAC.Projetos proj

				WHERE prod.Codigo = ana.idProduto 
					AND ana.IdPRONAC = proj.IdPRONAC ";

        if (!empty($idPRONAC)) { // busca de acordo com um id do pronac
            $sql.= "AND ana.IdPRONAC = '" . $idPRONAC . "' ";
        }
        if (!empty($idPRODUTO)) { // busca de acordo com um id do produto
            $sql.= "AND ana.idProduto = '" . $idPRODUTO . "' ";
        }

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }
    } // fecha buscarPronacAnaliseConteudo()
} // fecha class
