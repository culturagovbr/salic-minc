<?php
//
///**
// * VisaoDAO
// * @author Equipe RUP - Politec
// * @since 2010
// * @version 1.0
// * @package application
// * @subpackage application.model.DAO
// * @link http://www.politec.com.br
// * @copyright c 2010 - Politec - Todos os direitos reservados.
// *
// * @todo RETIRAR futuramente
// */
//class VisaoDAO extends Zend_Db_Table
//{
//    /**
//     * Nome da tabela do banco
//     */
//    protected $_name = 'AGENTES.dbo.Visao';
//
//
//    /**
//     * Metodo para buscar as visoes do agente
//     * @access public
//     * @static
//     * @param integer $idAgente
//     * @param integer $visao
//     * @param boolean $todasVisoes
//     * @return object
//     */
//    public static function buscarVisao($idAgente = null, $visao = null, $todasVisoes = false)
//    {
//        // busca todas as vis�es existentes no banco
//        if ($todasVisoes) {
//            $sql = "select distinct idverificacao, descricao from  " . GenericModel::getStaticTableName('agentes', 'verificacao') . "  where idtipo = 16 and sistema = 21 ";
//        } // busca todas as vis�es do usu�rio
//        else {
//            $sql = "select
//                                distinct vis.idvisao ,
//                                ver.descricao,
//                                ver.idverificacao,
//                                vis.idagente ,
//                                vis.visao ,
//                                vis.usuario ,
//                                vis.stativo ,
//                                ar.descricao as area
//                                from " . GenericModel::getStaticTableName('agentes', 'visao') . " vis
//                                inner join " . GenericModel::getStaticTableName('agentes', 'verificacao') . " ver on ver.idverificacao = vis.visao
//                                left join " . GenericModel::getStaticTableName('agentes', 'tbtitulacaoconselheiro') . " ttc on ttc.idagente =  vis.idagente
//                                left join " . GenericModel::getStaticTableName('sac', 'area') . " ar on ttc.cdArea = ar.Codigo ";
//
//            $sql .= " where ver.idverificacao = vis.visao
//				and ver.idtipo = 16 and sistema = 21";
//
//            if (!empty($idAgente)) // busca pelo id do agente
//            {
//                $sql .= " and vis.idagente = " . $idAgente;
//            }
//            if (!empty($visao)) // busca pela vis�o
//            {
//                $sql .= " and vis.visao = " . $visao;
//            }
//        }
//        $sql .= " order by 2";
//        $db = Zend_Db_Table::getDefaultAdapter();
//        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//
//        $dados = $db->fetchAll($sql);
//        return $dados;
//    } // fecha m�todo buscarVisao()
//
//
//    /**
//     * M�todo para cadastrar a vis�o de um agente
//     * @access public
//     * @static
//     * @param array $dados
//     * @return boolean
//     */
//    public static function cadastrarVisao($dados)
//    {
//        $db= Zend_Db_Table::getDefaultAdapter();
//        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//
//        $insert = $db->insert('AGENTES.dbo.Visao', $dados); // cadastra
//
//        return $insert ? true : false;
//    } // fecha m�todo cadastrarVisao()
//
//    /**
//     * M�todo para alterar a vis�o de um agente
//     * @access public
//     * @static
//     * @param integer $idAgente
//     * @param array $dados
//     * @return boolean
//     */
//    public static function alterarVisao($idAgente, $dados)
//    {
//        $db= Zend_Db_Table::getDefaultAdapter();
//        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//
//        $where = "idAgente = " . $idAgente; // condi��o para altera��o
//
//        $update = $db->update('AGENTES.dbo.Visao', $dados, $where); // altera
//
//        if ($update) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//
//    /**
//     * Metodo para excluir a visao de um agente
//     * @access public
//     * @static
//     * @param integer $idAgente
//     * @return boolean
//     */
//    public static function excluirVisao($idAgente)
//    {
//        $db= Zend_Db_Table::getDefaultAdapter();
//        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//
//        $where = "idAgente = " . $idAgente; // condi��o para exclus�o
//
//        $delete = $db->delete('AGENTES.dbo.Visao', $where); // exclui
//
//        if ($delete) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//}
