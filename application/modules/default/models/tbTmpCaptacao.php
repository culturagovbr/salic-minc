<?php
/**
 * DAO tbTmpCaptacao
 * @author emanuel.sampaio - Politec
 * @since 17/02/2011
 * @package application
 * @subpackage application.model
 * @link http://www.cultura.gov.br
 */

class tbTmpCaptacao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "tbTmpCaptacao";



    /**
     * M�todo para buscar as inconsist�ncias do extrato de movimenta��o banc�ria
     * @param string $pronac
     * @param array $data_recibo
     * @param string $proponente
     * @param string $incentivador
     * @param array $data_credito
     * @return object
     */
    public function buscarDados($pronac = null, $data_recibo = null, $proponente = null, $incentivador = null, $data_credito = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
//                $select->distinct();
        $select->from(
            array("t" => $this->_name),
            array(new Zend_Db_Expr("(t.nrAnoProjeto+t.nrSequencial) AS pronac")
                ,new Zend_Db_Expr("CONVERT(CHAR(10), t.dtChegadaRecibo, 103) AS dtChegadaRecibo")
                ,"t.nrCpfCnpjProponente"
                ,"t.nrCpfCnpjIncentivador"
                //,"t.NomeIncentivador" SAC.dbo.Interessado
                ,new Zend_Db_Expr("CONVERT(CHAR(10), t.dtCredito, 103) AS dtCredito")
                ,"t.vlValorCredito"
                ,"t.cdPatrocinio"
                ,"p.NomeProjeto"
                ,"c.Banco"
                //,"c.Agencia"
                //,"c.ContaBloqueada"
                ,"bc.Descricao AS nmBanco"
                ,"i.idTipoInconsistencia"
                ,"i.idTmpCaptacao")
        );
        $select->joinInner(
            array("i" => "tbTmpInconsistenciaCaptacao"),
            "t.idTmpCaptacao = i.idTmpCaptacao",
            array()
        );
        $select->joinLeft(
            array("p" => "Projetos"),
            "t.nrAnoProjeto = p.AnoProjeto AND t.nrSequencial = p.Sequencial",
            array()
        );
        $select->joinLeft(
            array("c" => "ContaBancaria"),
            "t.nrAnoProjeto = c.AnoProjeto AND t.nrSequencial = c.Sequencial",
            array('Agencia'=>new Zend_Db_Expr("case
				when
                                    c.Agencia is not null then c.Agencia
				else
                                    t.nrAgenciaProponente end"),
                            'ContaBloqueada'=>new Zend_Db_Expr("case
				when
					c.ContaBloqueada is not null then c.ContaBloqueada
				else t.nrContaProponente end")),
            array()
        );
        $select->joinLeft(
            array("bc" => "bancos"),
            "c.Banco = bc.Codigo",
            array(),
            "AGENTES.dbo"
        );

        // busca pelo pronac
        if (!empty($pronac)) {
            $select->where("(t.nrAnoProjeto+t.nrSequencial) = ?", $pronac);
        }

        // busca pela data do recibo
        if (!empty($data_recibo)) {
            if (!empty($data_recibo[0]) && !empty($data_recibo[1])) {
                $select->where("t.dtChegadaRecibo >= ?", Data::dataAmericana($data_recibo[0]) . " 00:00:00");
                $select->where("t.dtChegadaRecibo <= ?", Data::dataAmericana($data_recibo[1]) . " 23:59:59");
            } else {
                if (!empty($data_recibo[0])) {
                    $select->where("t.dtChegadaRecibo >= ?", Data::dataAmericana($data_recibo[0]) . " 00:00:00");
                }
                if (!empty($data_recibo[1])) {
                    $select->where("t.dtChegadaRecibo <= ?", Data::dataAmericana($data_recibo[1]) . " 23:59:59");
                }
            }
        } // fecha if data do recibo

        // filtra pelo cpf/cnpj do proponente
        if (!empty($proponente)) {
            $select->where("t.nrCpfCnpjProponente = ?", $proponente);
        }

        // filtra pelo cpf/cnpj do incentivador
        if (!empty($incentivador)) {
            $select->where("t.nrCpfCnpjIncentivador = ?", $incentivador);
        }

        // busca pela data do cr�dito
        if (!empty($data_credito)) {
            if (!empty($data_credito[0]) && !empty($data_credito[1])) {
                $select->where("t.dtCredito >= ?", Data::dataAmericana($data_credito[0]) . " 00:00:00");
                $select->where("t.dtCredito <= ?", Data::dataAmericana($data_credito[1]) . " 23:59:59");
            } else {
                if (!empty($data_credito[0])) {
                    $select->where("t.dtCredito >= ?", Data::dataAmericana($data_credito[0]) . " 00:00:00");
                }
                if (!empty($data_credito[1])) {
                    $select->where("t.dtCredito <= ?", Data::dataAmericana($data_credito[1]) . " 23:59:59");
                }
            }
        } // fecha if data do recibo

        //$select->order("t.idTipoInconsistencia");
        $select->order(new Zend_Db_Expr("(t.nrAnoProjeto+t.nrSequencial)"));
        $select->order("t.dtChegadaRecibo");
        $select->order("t.dtCredito");

        return $this->fetchAll($select);
    } // fecha m�todo buscarDados()

    public function listarProjetosInconsistentes($orgao, $idPronac = null, $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from(
                        array("tmpCaptacao" => $this->_name),
                    array(
                            'pronac' => new Zend_Db_Expr('CONVERT(INT, (tmpCaptacao.nrAnoProjeto+tmpCaptacao.nrSequencial))'),
                            'nrCpfCnpjProponente',
                        )
                )
                ->joinLeft(
                        array('projetos' => 'Projetos'),
                        'tmpCaptacao.nrAnoProjeto = projetos.AnoProjeto AND tmpCaptacao.nrSequencial = projetos.Sequencial',
                        array('IdPRONAC', 'NomeProjeto')
                )
                ->joinLeft(
                        array('contaBancaria' => 'ContaBancaria'),
                        'tmpCaptacao.nrAnoProjeto = contaBancaria.AnoProjeto AND tmpCaptacao.nrSequencial = contaBancaria.Sequencial',
                        array('Agencia', 'ContaBloqueada')
                )
                ->where('projetos.Orgao = ?', $orgao)
                ->where('tmpCaptacao.tpValidacao in ?', new Zend_Db_Expr('(2, 3, 4, 5, 6, 7, 8, 9)'))
                ->group(
                    array(
                    new Zend_Db_Expr('(tmpCaptacao.nrAnoProjeto+tmpCaptacao.nrSequencial)'),
                    'tmpCaptacao.nrCpfCnpjProponente',
                    'projetos.IdPRONAC',
                    'projetos.NomeProjeto',
                    'contaBancaria.Agencia',
                    'contaBancaria.ContaBloqueada',
                    )
                 )
                ->order($order);

        if ($idPronac) {
            $select->where(new Zend_Db_Expr('CONVERT(INT, (tmpCaptacao.nrAnoProjeto+tmpCaptacao.nrSequencial)) = ?'), $idPronac);
        } else {
            $select->where(new Zend_Db_Expr('tmpCaptacao.nrAnoProjeto+tmpCaptacao.nrSequencial IS NOT NULL'));
        }

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }


        return $this->fetchAll($select);
    }

    /**
     * M�todo para buscar as inconsist�ncias do extrato de movimenta��o banc�ria
     * @param string $pronac
     * @param array $data_recibo
     * @param string $proponente
     * @param string $incentivador
     * @param array $data_credito
     * @return object
     */
    public function buscarProjetosRelatorioCaptacao($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array("t" => $this->_name),
            array(
                        new Zend_Db_Expr("CONVERT(INT, (t.nrAnoProjeto+t.nrSequencial)) AS pronac"),
                        new Zend_Db_Expr("CONVERT(CHAR(10), t.dtChegadaRecibo, 103) AS dtChegadaRecibo"),
                        'nrCpfCnpjProponente' => new Zend_Db_Expr("CASE
                            WHEN SUBSTRING(nrCpfCnpjProponente,1,3)='000' AND TABELAS.dbo.fnCNPJValido(SUBSTRING(nrCpfCnpjProponente,4,11))=0
                            THEN SUBSTRING(nrCpfCnpjProponente,4,11)
                            ELSE nrCpfCnpjProponente
                            END"),
                        'nrCpfCnpjIncentivador' => new Zend_Db_Expr(
                            "CASE
                                WHEN SUBSTRING(nrCpfCnpjIncentivador,1,3)='000' AND TABELAS.dbo.fnCNPJValido(SUBSTRING(nrCpfCnpjIncentivador,4,11))=0
                                THEN SUBSTRING(nrCpfCnpjIncentivador,4,11)
                                ELSE nrCpfCnpjIncentivador
                                END"
                        ),
                        new Zend_Db_Expr("CONVERT(CHAR(10), t.dtCredito, 103) AS dtCredito"),
                        "t.vlValorCredito",
                        "t.cdPatrocinio",
                        "t.tpValidacao",
                        "t.idTmpCaptacao",
                        "ValorCaptado" => new Zend_Db_Expr("sac.dbo.fnCustoProjeto (p.AnoProjeto,p.Sequencial)")
                )
        );
        $select->joinLeft(
            array("p" => "Projetos"),
            "t.nrAnoProjeto = p.AnoProjeto AND t.nrSequencial = p.Sequencial",
            array("p.NomeProjeto", "p.IdPRONAC")
        );
        $select->joinLeft(
            array("c" => "ContaBancaria"),
            "t.nrAnoProjeto = c.AnoProjeto AND t.nrSequencial = c.Sequencial",
                array(
                    'Agencia' => new Zend_Db_Expr("case when c.Agencia is not null then c.Agencia else t.nrAgenciaProponente end"),
                    'ContaBloqueada' => new Zend_Db_Expr("case when c.ContaBloqueada is not null then c.ContaBloqueada else t.nrContaProponente end"),
                    'c.Banco'
            ),
            array()
        );
        $select->joinLeft(
            array("bc" => "bancos"),
            "c.Banco = bc.Codigo",
            array(
                'bc.Codigo',
                'bc.CNPJ',
                'bc.Sufixo',
                'bc.DV',
                'bc.Descricao AS nmBanco'
            ),
            "AGENTES.dbo"
        );
        $select->joinLeft(array("i" => "Interessado"), "i.CgcCPf = t.nrCpfCnpjIncentivador", array('nomeIncentivador' => 'i.Nome'));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }
        return $this->fetchAll($select);
    }

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
        $where = "idTmpCaptacao = " . $where;
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
        $where = "idTmpCaptacao = " . $where;
        return $this->delete($where);
    } // fecha m�todo excluirDados()


    public function buscarDadosParaRemanejamento($idTmpCaptacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array("a" => $this->_name),
                array(
                    "a.nrAnoProjeto",
                    "a.nrSequencial",
                    new Zend_Db_Expr("(SELECT SUBSTRING(dsInformacao,2,4) FROM SAC.dbo.tbDepositoIdentificadoCaptacao WHERE SUBSTRING(dsInformacao,1,1)='1') as NumeroRecibo"),
                    "a.nrCpfCnpjIncentivador",
                    new Zend_Db_Expr("(SELECT b.Enquadramento FROM sac.dbo.enquadramento AS b WHERE b.AnoProjeto+b.Sequencial =  a.nrAnoProjeto+a.nrSequencial) AS MedidaProvisoria"),
                    "a.dtChegadaRecibo",
                    "a.dtCredito",
                    new Zend_Db_Expr("0 AS CaptacaoUfir"),
                    new Zend_Db_Expr("(SELECT idUsuario FROM SAC.DBO.tbDepositoIdentificadoCaptacao WHERE SUBSTRING(dsInformacao,1,1)='1') AS Logon"),
                    new Zend_Db_Expr("(SELECT idPronac FROM sac.dbo.Projetos AS p WHERE p.AnoProjeto+p.Sequencial = a.nrAnoProjeto + a.nrSequencial) AS idProjeto")
                )
            );
        $select->where('a.idTmpCaptacao = ?', $idTmpCaptacao);

            
        return $this->fetchRow($select);
    } // fecha m�todo buscarDados()
} // fecha class
