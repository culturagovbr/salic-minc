<?php
/**
 * DAO tbMovimentacaoBancaria
 * @author emanuel.sampaio - Politec
 * @since 17/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbMovimentacaoBancaria extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "dbo";
    protected $_name    = "tbMovimentacaoBancaria";



    /**
     * M�todo para buscar
     * @access public
     * @param string $pronac
     * @param boolean $conta_rejeitada
     * @param array $periodo
     * @param array $operacao
     * @return object
     */
    public function buscarDados($pronac = null, $conta_rejeitada = null, $periodo = null, $operacao = null, $tamanho=-1, $inicio=-1, $count = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
                
        if (isset($count)) {
            $select->from(
            array("mi" => "tbMovimentacaoBancariaItem"),
                        array("total" => "count(*)")
                    );
        } else {
            $select->from(
                            array("mi" => "tbMovimentacaoBancariaItem"),
                        array("m.nrBanco"
                                    ,"CONVERT(CHAR(10), m.dtInicioMovimento, 103) AS dtInicioMovimento"
                                    ,"CONVERT(CHAR(10), m.dtFimMovimento, 103) AS dtFimMovimento"
                                    ,"mi.idMovimentacaoBancaria"
                                    ,"mi.tpRegistro"
                                    ,"mi.nrAgencia"
                                    ,"mi.nrDigitoConta"
                                    ,"mi.nmTituloRazao"
                                    ,"mi.nmAbreviado"
                                    ,"CONVERT(CHAR(10), mi.dtAberturaConta, 103) AS dtAberturaConta"
                                    ,"mi.nrCNPJCPF"
                                    ,"n.Descricao AS Proponente"
                                    ,"mi.vlSaldoInicial"
                                    ,"mi.tpSaldoInicial"
                                    ,"mi.vlSaldoFinal"
                                    ,"mi.tpSaldoFinal"
                                    ,"CONVERT(CHAR(10), mi.dtMovimento, 103) AS dtMovimento"
                                    ,"mi.cdHistorico"
                                    ,"mi.dsHistorico"
                                    ,"mi.nrDocumento"
                                    ,"mi.vlMovimento"
                                    ,"mi.cdMovimento"
                                    ,"mi.idMovimentacaoBancariaItem"
                                    ,"ti.idTipoInconsistencia"
                                    ,"ti.dsTipoInconsistencia"
                                    ,"(p.AnoProjeto+p.Sequencial) AS pronac"
                                    ,"p.NomeProjeto"
                                    ,"bc.Descricao AS nmBanco")
                    );
        }
                
        $select->joinInner(
            array("m" => $this->_name),
                
            "m.idMovimentacaoBancaria = mi.idMovimentacaoBancaria",
                
            array()
        );
        if (!empty($conta_rejeitada) && $conta_rejeitada) {
            $select->joinInner(
            array("mx" => "tbMovimentacaoBancariaItemxTipoInconsistencia"),
                    
                        "mi.idMovimentacaoBancariaItem = mx.idMovimentacaoBancariaItem",
                    
                        array()
                    );
                    
            $select->joinInner(
            array("ti" => "tbTipoInconsistencia"),
                    
                        "ti.idTipoInconsistencia = mx.idTipoInconsistencia",
                    
                        array()
                    );
        } else {
            $select->joinLeft(
            array("mx" => "tbMovimentacaoBancariaItemxTipoInconsistencia"),
                        "mi.idMovimentacaoBancariaItem = mx.idMovimentacaoBancariaItem",
                        array()
                    );
                    
            $select->joinLeft(
            array("ti" => "tbTipoInconsistencia"),
                    
                        "ti.idTipoInconsistencia = mx.idTipoInconsistencia",
                    
                        array()
                    );
        }
        
        $select->joinLeft(
            array("c" => "ContaBancaria"),
        
            "mi.nrAgencia = c.Agencia AND (mi.nrDigitoConta = c.ContaBloqueada OR mi.nrDigitoConta = c.ContaLivre)",
        
            array()
        );
        $select->joinLeft(
            array("p" => "Projetos"),
            "c.AnoProjeto = p.AnoProjeto AND c.Sequencial = p.Sequencial",
            array()
        );
        $select->joinLeft(
            array("bc" => "bancos"),
            "m.nrBanco = bc.Codigo",
            array(),
            "AGENTES.dbo"
        );
        $select->joinLeft(
            array("a" => "Agentes"),
            "mi.nrCNPJCPF = a.CNPJCPF",
            array(),
            "AGENTES.dbo"
        );
        $select->joinLeft(
            array("n" => "Nomes"),
            "a.idAgente = n.idAgente",
            array(),
            "AGENTES.dbo"
        );
                
        // $select->where("mi.vlSaldoInicial > 0.00");
        //$select->where("mi.vlSaldoFinal > 0.00");

        // busca pelo pronac
        if (!empty($pronac)) {
            $select->where("(c.AnoProjeto+c.Sequencial) = ?", $pronac);
        }

        // filtra por contas rejeitadas
        if (!empty($conta_rejeitada) && $conta_rejeitada) {
            $select->where("mx.idMovimentacaoBancariaItem IS NOT NULL");
            $select->where("mx.idTipoInconsistencia IS NOT NULL");
        } else {
            $select->where("mx.idMovimentacaoBancariaItem IS NULL");
            $select->where("mx.idTipoInconsistencia IS NULL");
        }

        // busca pelo per�odo
        if (!empty($periodo)) {
            if ($periodo[0] == "A") { // Hoje
                $select->where("CONVERT(DATE, m.dtInicioMovimento) = CONVERT(DATE, GETDATE()) 
					OR CONVERT(DATE, m.dtFimMovimento) = CONVERT(DATE, GETDATE()) 
					OR CONVERT(DATE, mi.dtMovimento) = CONVERT(DATE, GETDATE())");
            }
            if ($periodo[0] == "B") { // Ontem
                $select->where("CONVERT(DATE, m.dtInicioMovimento) = DATEADD(DAY, -1, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, m.dtFimMovimento) = DATEADD(DAY, -1, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, mi.dtMovimento) = DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))");
            }
            if ($periodo[0] == "C") { // �ltimos 7 dias
                $select->where("CONVERT(DATE, m.dtInicioMovimento) > DATEADD(DAY, -7, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, m.dtFimMovimento) > DATEADD(DAY, -7, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, mi.dtMovimento) > DATEADD(DAY, -7, CONVERT(DATE, GETDATE()))");
            }
            if ($periodo[0] == "D") { // Semana passada (seg-dom)
                $select->where("(CONVERT(DATE, m.dtInicioMovimento) >= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, -7, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -8, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -9, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -10, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -11, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -12, CONVERT(DATE, GETDATE()))
					  END 
					  AND CONVERT(DATE, m.dtInicioMovimento) <= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -3, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -4, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -5, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					  END) 
					OR (CONVERT(DATE, m.dtFimMovimento) >= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, -7, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -8, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -9, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -10, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -11, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -12, CONVERT(DATE, GETDATE()))
					  END 
					  AND CONVERT(DATE, m.dtFimMovimento) <= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -3, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -4, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -5, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					  END) 
					OR (CONVERT(DATE, mi.dtMovimento) >= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, -7, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -8, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -9, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -10, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -11, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -12, CONVERT(DATE, GETDATE()))
					  END 
					  AND CONVERT(DATE, mi.dtMovimento) <= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -3, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -4, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -5, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					  END)");
            }
            if ($periodo[0] == "E") { // �ltima semana (seg-sex)
                $select->where("(CONVERT(DATE, m.dtInicioMovimento) >= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -3, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -4, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -5, CONVERT(DATE, GETDATE()))
					  END 
					  AND CONVERT(DATE, m.dtInicioMovimento) <= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					  END) 
					OR (CONVERT(DATE, m.dtFimMovimento) >= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -3, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -4, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -5, CONVERT(DATE, GETDATE()))
					  END 
					  AND CONVERT(DATE, m.dtFimMovimento) <= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					  END) 
					OR (CONVERT(DATE, mi.dtMovimento) >= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -6, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, -3, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, -4, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -5, CONVERT(DATE, GETDATE()))
					  END 
					  AND CONVERT(DATE, mi.dtMovimento) <= CASE DATEPART(DW, GETDATE())
					     WHEN 1 THEN DATEADD(DAY, -2, CONVERT(DATE, GETDATE()))
					     WHEN 2 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 3 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 4 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 5 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 6 THEN DATEADD(DAY, 0, CONVERT(DATE, GETDATE()))
					     WHEN 7 THEN DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))
					  END)");
            }
            if ($periodo[0] == "F") { // Este m�s
                $select->where("DATEPART(MONTH, m.dtInicioMovimento) + DATEPART(YEAR, m.dtInicioMovimento) = DATEPART(MONTH, GETDATE()) + DATEPART(YEAR, GETDATE()) 
					OR DATEPART(MONTH, m.dtFimMovimento) + DATEPART(YEAR, m.dtFimMovimento) = DATEPART(MONTH, GETDATE()) + DATEPART(YEAR, GETDATE()) 
					OR DATEPART(MONTH, mi.dtMovimento) + DATEPART(YEAR, mi.dtMovimento) = DATEPART(MONTH, GETDATE()) + DATEPART(YEAR, GETDATE())");
            }
            if ($periodo[0] == "G") { // Ano passado
                $select->where("DATEPART(YEAR, m.dtInicioMovimento) = (DATEPART(YEAR, GETDATE()) - 1) 
					OR DATEPART(YEAR, m.dtFimMovimento) = (DATEPART(YEAR, GETDATE()) - 1) 
					OR DATEPART(YEAR, mi.dtMovimento) = (DATEPART(YEAR, GETDATE()) - 1)");
            }
            if ($periodo[0] == "H") { // �ltimos 12 meses
                $select->where("CONVERT(DATE, m.dtInicioMovimento) >= DATEADD(MONTH , -12, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, m.dtFimMovimento) >= DATEADD(MONTH , -12, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, mi.dtMovimento) >= DATEADD(MONTH , -12, CONVERT(DATE, GETDATE()))");
            }
            if ($periodo[0] == "I") { // �ltimos 6 meses
                $select->where("CONVERT(DATE, m.dtInicioMovimento) >= DATEADD(MONTH , -6, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, m.dtFimMovimento) >= DATEADD(MONTH , -6, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, mi.dtMovimento) >= DATEADD(MONTH , -6, CONVERT(DATE, GETDATE()))");
            }
            if ($periodo[0] == "J") { // �ltimos 3 meses
                $select->where("CONVERT(DATE, m.dtInicioMovimento) >= DATEADD(MONTH , -3, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, m.dtFimMovimento) >= DATEADD(MONTH , -3, CONVERT(DATE, GETDATE())) 
					OR CONVERT(DATE, mi.dtMovimento) >= DATEADD(MONTH , -3, CONVERT(DATE, GETDATE()))");
            }
            if ($periodo[0] == "K") { // filtra conforme uma data inicial e uma data final
                if (!empty($periodo[1]) && !empty($periodo[2])) {
                    $select->where("m.dtInicioMovimento >= ?", Data::dataAmericana($periodo[1]) . " 00:00:00");
                    $select->where("m.dtFimMovimento <= ?", Data::dataAmericana($periodo[2]) . " 23:59:59");
                } else {
                    if (!empty($periodo[1])) {
                        $select->where("m.dtInicioMovimento >= ?", Data::dataAmericana($periodo[1]) . " 00:00:00");
                    }
                    if (!empty($periodo[2])) {
                        $select->where("m.dtFimMovimento <= ?", Data::dataAmericana($periodo[2]) . " 23:59:59");
                    }
                }
            }
        } // fecha if periodo

        // filtra pelo tipo de opera��o
        if (!empty($operacao)) {
            $select->where("mi.tpSaldoInicial = ? OR mi.tpSaldoInicial IS NULL", $operacao);
            $select->where("mi.tpSaldoFinal = ? OR mi.tpSaldoFinal IS NULL", $operacao);
            $select->where("mi.cdMovimento = ? OR mi.cdMovimento IS NULL", $operacao);
        }
                
        /*if(is_null($count)){
            /*$select->order("mi.tpRegistro");
            $select->order("(p.AnoProjeto+p.Sequencial)");
            $select->order("m.dtInicioMovimento");
            $select->order("m.dtFimMovimento");
            $select->order("mi.dtMovimento");
            $select->order(array(5,26,2,3,17));
        }*/
                
        //paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }
        //x($select->assemble());
        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()



    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()



    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idMovimentacaoBancaria = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()



    /**
     * M�todo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirDados($where)
    {
        $where = "idMovimentacaoBancaria = " . $where;
        return $this->delete($where);
    } // fecha m�todo excluirDados()
} // fecha class
