<?php
/**
 * DAO ControlarMovimentacaoBancariaDAO
 * @author Equipe RUP - Politec
 * @since 22/01/2011
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class ControlarMovimentacaoBancariaDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "";
    protected $_primary = "";



    /**
     * M�todo para buscar o extrato de movimentacao bancaria
     * @access public
     * @static
     * @param string $pronac
     * @param array $periodo
     * @param array $operacao
     * @return object
     */
    public static function buscar($pronac, $periodo, $operacao)
    {
        /* $sql = "SELECT *
                FROM MovimentacaoBancaria
                WHERE id > 0 ";

        // busca pelo pronac
        if (!empty($pronac))
        {
            $sql.= " AnoProjeto+Sequencial = '" . $pronac . "' ";
        }

        // busca pelo per�odo
        if (!empty($periodo))
        {
            // filtra conforme uma data inicial e uma data final
            if (count($periodo) == 2)
            {
                if (!empty($periodo[0]) && !empty($periodo[1]))
                {
                    $sql.= " AND dtInicioMovimentacao >= '$periodo[0]' ";
                    $sql.= " AND dtFimMovimentacao    <= '$periodo[1]' ";
                }
                else
                {
                    if (!empty($periodo[0]))
                    {
                        $sql.= " AND dtInicioMovimentacao >= '$periodo[0]' ";
                    }
                    if (!empty($periodo[1]))
                    {
                        $sql.= " AND dtFimMovimentacao    <= '$periodo[1]' ";
                    }
                }
            }
        }

        // busca pelo tipo de opera��o: cr�dito ou d�bito
        if (!empty($operacao))
        {
            $sql.= " stMovimento = '" . $operacao . "' ";
        }

        try
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            return $db->fetchAll($sql);
        }
        catch (Zend_Exception_Db $e)
        {
            $this->view->message = "Erro ao buscar extrato de movimenta��o banc�ria: " . $e->getMessage();
        } */
        // dados do header
        $dados_header[0]['header']              = ""; // HEADER DO ARQUIVO - CONSTANTE 1
        $dados_header[0]['nr_banco']            = ""; // N�MERO DO BANCO
        $dados_header[0]['arquivo']             = ""; // NOME DO ARQUIVO
        $dados_header[0]['dt_arquivo']          = ""; // DATA DA GERA��O DO ARQUIVO - FORMATO (DDMMAAAA)
        $dados_header[0]['dt_inicio_movimento'] = ""; // DATA DO INICIO DA MOVIMENTACAO BANCARIA - FORMATO (DDMMAAAA)
        $dados_header[0]['dt_fim_movimento']    = ""; // DATA FINAL DA MOVIMENTACAO BANCARIA - FORMATO (DDMMAAAA)



        // dados da movimenta��o
        $dados[0]['dt_movimentacao'] = "24/12/2010";
        $dados[0]['deposito_origem'] = "";
        $dados[0]['historico']       = "Saldo Anterior";
        $dados[0]['documento']       = "0";
        $dados[0]['op_valor']        = "C";
        $dados[0]['valor']           = "59.487,23";
        $dados[0]['op_saldo']        = "C";
        $dados[0]['saldo']           = "59.487,23";
        $dados[0]['idPronac']        = 57016;
        $dados[0]['pronac']          = "066044";
        $dados[0]['NomeProjeto']     = "Elabora��o de Projetos Executivos para a Restaura��o dos Monumentos da Floresta da Tijuca";

        $dados[1]['dt_movimentacao'] = "03/01/2011";
        $dados[1]['deposito_origem'] = "";
        $dados[1]['historico']       = "Dep�sito COMPE<br />033 0815 890474192 MARIA DA SILVA";
        $dados[1]['documento']       = "7.178";
        $dados[1]['op_valor']        = "C";
        $dados[1]['valor']           = "1.000,00";
        $dados[1]['op_saldo']        = "";
        $dados[1]['saldo']           = "";
        $dados[1]['idPronac']        = 57016;
        $dados[1]['pronac']          = "066044";
        $dados[1]['NomeProjeto']     = "Elabora��o de Projetos Executivos para a Restaura��o dos Monumentos da Floresta da Tijuca";

        $dados[2]['dt_movimentacao'] = "03/01/2011";
        $dados[2]['deposito_origem'] = "";
        $dados[2]['historico']       = "Cobran�a de I.O.F.";
        $dados[2]['documento']       = "391.100.701";
        $dados[2]['op_valor']        = "D";
        $dados[2]['valor']           = "0,13";
        $dados[2]['op_saldo']        = "C";
        $dados[2]['saldo']           = "60.487,10";
        $dados[2]['idPronac']        = 57016;
        $dados[2]['pronac']          = "066044";
        $dados[2]['NomeProjeto']     = "Elabora��o de Projetos Executivos para a Restaura��o dos Monumentos da Floresta da Tijuca";

        $dados[3]['dt_movimentacao'] = "09/01/2011";
        $dados[3]['deposito_origem'] = "1236-X";
        $dados[3]['historico']       = "Compra com Cart�o<br />04/01 12:25 MADE IN BRAZIL";
        $dados[3]['documento']       = "144.736";
        $dados[3]['op_valor']        = "D";
        $dados[3]['valor']           = "300,00";
        $dados[3]['op_saldo']        = "C";
        $dados[3]['saldo']           = "60.187,10";
        $dados[3]['idPronac']        = 57016;
        $dados[3]['pronac']          = "066044";
        $dados[3]['NomeProjeto']     = "Elabora��o de Projetos Executivos para a Restaura��o dos Monumentos da Floresta da Tijuca";

        $dados[4]['dt_movimentacao'] = "17/01/2011";
        $dados[4]['deposito_origem'] = "1236-X";
        $dados[4]['historico']       = "Cheque Compensado";
        $dados[4]['documento']       = "850.020";
        $dados[4]['op_valor']        = "D";
        $dados[4]['valor']           = "2.000,00";
        $dados[4]['op_saldo']        = "C";
        $dados[4]['saldo']           = "58.187,10";
        $dados[4]['idPronac']        = 57016;
        $dados[4]['pronac']          = "066044";
        $dados[4]['NomeProjeto']     = "Elabora��o de Projetos Executivos para a Restaura��o dos Monumentos da Floresta da Tijuca";

        $dados[5]['dt_movimentacao'] = "09/01/2011";
        $dados[5]['deposito_origem'] = "1236-X";
        $dados[5]['historico']       = "Compra com Cart�o<br />04/01 12:25 MADE IN BRAZIL";
        $dados[5]['documento']       = "144.736";
        $dados[5]['op_valor']        = "D";
        $dados[5]['valor']           = "300,00";
        $dados[5]['op_saldo']        = "C";
        $dados[5]['saldo']           = "60.187,10";
        $dados[5]['idPronac']        = 119358;
        $dados[5]['pronac']          = "098064";
        $dados[5]['NomeProjeto']     = "Circuito Sinf�nico 2010";

        $dados[6]['dt_movimentacao'] = "17/01/2011";
        $dados[6]['deposito_origem'] = "1236-X";
        $dados[6]['historico']       = "Cheque Compensado";
        $dados[6]['documento']       = "850.020";
        $dados[6]['op_valor']        = "D";
        $dados[6]['valor']           = "2.000,00";
        $dados[6]['op_saldo']        = "C";
        $dados[6]['saldo']           = "58.187,10";
        $dados[6]['idPronac']        = 119358;
        $dados[6]['pronac']          = "098064";
        $dados[6]['NomeProjeto']     = "Circuito Sinf�nico 2010";

        return $dados;
    } // fecha m�todo buscar()



    /**
     * M�todo para cadastrar informa��es dos arquivos vindos do banco do brasil
     * @access public
     * @static
     * @param array $dados
     * @return bool
     */
    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo cadastrar()



    /**
     * M�todo para alterar informa��es dos arquivos vindos do banco do brasil
     * @access public
     * @static
     * @param array $dados
     * @param integer $id
     * @return bool
     */
    public static function alterar($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where   = "id = $id";
        $alterar = $db->update("", $dados, $where);

        if ($alterar) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo alterar()



    /**
     * M�todo para excluir informa��es dos arquivos vindos do banco do brasil
     * @access public
     * @static
     * @param array $dados
     * @param integer $id
     * @return bool
     */
    public static function excluir($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where   = "id = $id";
        $excluir = $db->delete("", $dados, $where);

        if ($excluir) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo excluir()



    /**
     * M�todo para buscar o id da �ltima informa��o do arquivo vindo do banco do brasil
     * @access public
     * @static
     * @param void
     * @return object || integer
     */
    public static function buscarId()
    {
        $sql = "SELECT MAX(id) AS id FROM ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarId()
} // fecha class ControlarMovimentacaoBancariaDAO
