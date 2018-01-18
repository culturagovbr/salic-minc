<?php

/**
 * Description of aprovacao
 *
 * @author augusto
 */
class Aprovacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'Aprovacao';
    protected $_schema = 'SAC';

    public function inserirAprovacao($dados)
    {
        try {
            $inserir = $this->insert($dados);
            return $inserir;
        } catch (Zend_Db_Table_Exception $e) {
            return 'Class:Aprovacao Method: inserirAprovacao -> Erro: ' . $e->__toString();
        }
    }

    public function totalAprovadoProjeto($retornaSelect = false)
    {
        $selectAprovacao = $this->select();
        $selectAprovacao->setIntegrityCheck(false);
        $selectAprovacao->from(
                array($this->_name),
                array(
                'somatorio'=> new Zend_Db_Expr("
                                      SUM(CASE Tipoaprovacao WHEN '1' THEN AprovadoReal           ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '2' THEN AprovadoReal          ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '1' THEN AutorizadoReal        ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '2' THEN AutorizadoReal        ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '1' THEN ConcedidoCapitalReal  ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '2' THEN ConcedidoCapitalReal  ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '1' THEN ContrapartidaReal     ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '2' THEN ContrapartidaReal     ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '1' THEN ConcedidoCusteioReal  ELSE 0 END)
                                      +SUM(CASE Tipoaprovacao WHEN '2' THEN ConcedidoCusteioReal  ELSE 0 END)
                                      -SUM(CASE Tipoaprovacao WHEN '4' THEN AprovadoReal          ELSE 0 END)
                                      -SUM(CASE Tipoaprovacao WHEN '4' THEN AutorizadoReal        ELSE 0 END)
                                      -SUM(CASE Tipoaprovacao WHEN '4' THEN ConcedidoCapitalReal  ELSE 0 END)
                                      -SUM(CASE Tipoaprovacao WHEN '4' THEN ContrapartidaReal     ELSE 0 END)
                                      -SUM(CASE Tipoaprovacao WHEN '4' THEN ConcedidoCusteioReal  ELSE 0 END) "),
                'AnoProjeto',
                'Sequencial'
                )
        );
        $selectAprovacao->group('AnoProjeto');
        $selectAprovacao->group('Sequencial');

        if ($retornaSelect) {
            return $selectAprovacao;
        } else {
            return $this->fetchAll($selectAprovacao);
        }
    }

    public function buscaDataPublicacaoDOU($retornaSelect = false)
    {
        $selectAprovacao = $this->select();
        $selectAprovacao->setIntegrityCheck(false);
        $selectAprovacao->from(
                array($this->_name),
                array(
                'AnoProjeto',
                'Sequencial',
                'DtPublicacaoAprovacao' => new Zend_Db_Expr(" max(DtPublicacaoAprovacao)")
                )
        );
        $selectAprovacao->group('AnoProjeto');
        $selectAprovacao->group('Sequencial');

        if ($retornaSelect) {
            return $selectAprovacao;
        } else {
            return $this->fetchAll($selectAprovacao);
        }
    }


    public function buscaCompleta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                    array("a"=>$this->_name),
                    array("DtAprovacao"=>"CONVERT(CHAR(20),a.DtAprovacao, 120)",
                          "a.ResumoAprovacao",
                          "a.PortariaAprovacao",
                          "DtPortariaAprovacao"=>"CONVERT(CHAR(20),a.DtPortariaAprovacao, 120)",
                          "DtPublicacaoAprovacao"=>"CONVERT(CHAR(20),a.DtPublicacaoAprovacao, 120)",
                          "DtInicioCaptacao"=>"CONVERT(CHAR(20),a.DtInicioCaptacao, 120)",
                          "DtFimCaptacao"=>"CONVERT(CHAR(20),a.DtFimCaptacao, 120)",
                          "a.AprovadoReal",
                          "a.ConcedidoCusteioReal",
                          "a.ConcedidoCapitalReal",
                          "a.ContrapartidaReal",
                          "CodTipoAprovacao"=>"a.TipoAprovacao",
                          "Concedido"=>new Zend_Db_Expr("(ConcedidoCusteioReal+ConcedidoCapitalReal) - ContrapartidaReal"),
                          "TipoAprovacao" => new Zend_Db_Expr("CASE WHEN a.TipoAprovacao = 1 THEN 'Inicial'
                                                               WHEN a.TipoAprovacao = 2 THEN 'Complementa&ccedil;&atilde;o'
                                                               WHEN a.TipoAprovacao = 3 THEN 'Prorroga&ccedil;&atilde;o'
                                                               WHEN a.TipoAprovacao = 4 THEN 'Redu&ccedil;&atilde;o'
                                                               WHEN a.TipoAprovacao = 8 THEN 'Readequa&ccedil;&atilde;o' END"),
                        "a.idReadequacao"
                        ),
            "SAC.dbo"
                    );
        $slct->joinInner(
                        array("p"=>"Projetos"),
                        "a.AnoProjeto = p.AnoProjeto and a.Sequencial = p.Sequencial",
                        array("p.IdPRONAC",
                              "Pronac"=>new Zend_Db_Expr("p.AnoProjeto + p.Sequencial"),
                              "p.NomeProjeto",
                            ),
                        "SAC.dbo"
                        );
        $slct->joinLeft(
                        array("tbr"=>"tbReadequacao"),
                        "p.IdPRONAC = tbr.idPronac AND a.idReadequacao = tbr.idReadequacao",
                        array(),
            "SAC.dbo"
                        );
        $slct->joinLeft(
                        array("tbtpr"=>"tbTipoReadequacao"),
                        "tbr.idTipoReadequacao = tbtpr.idTipoReadequacao",
                        array('tbtpr.dsReadequacao'),
            "SAC.dbo"
                        );
        $slct->joinInner(
                        array("m"=>"Mecanismo"),
                        "p.Mecanismo = m.Codigo",
                        array("Mecanismo"=>"m.Descricao"),
                        "SAC.dbo"
                        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slctCount = $this->select();
            $slctCount->setIntegrityCheck(false);
            $slctCount->from(
                         array("a"=>$this->_name),
                         array('total'=>"count(*)"),
                "SAC.dbo"
                        );
            $slctCount->joinInner(
                            array("p"=>"Projetos"),
                            "a.AnoProjeto = p.AnoProjeto and a.Sequencial = p.Sequencial",
                            array(),
                            "SAC.dbo"
                            );
            $slct->joinLeft(
                        array("tbr"=>"tbReadequacao"),
                        "p.IdPRONAC = tbr.idPronac AND a.idReadequacao = tbr.idReadequacao",
                        array(),
                "SAC.dbo"
                        );
            $slct->joinLeft(
                        array("tbtpr"=>"tbTipoReadequacao"),
                        "tbr.idTipoReadequacao = tbtpr.idTipoReadequacao",
                        array('tbtpr.dsReadequacao'),
                "SAC.dbo"
                        );
            $slct->joinInner(
                        array("m"=>"Mecanismo"),
                        "p.Mecanismo = m.Codigo",
                        array(),
                        "SAC.dbo"
                        );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctCount->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctCount)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }


        return $this->fetchAll($slct);
    }

    /*===========================================================================*/
    /*====================== ABAIXO - METODOS DA CNIC ===========================*/
    /*===========================================================================*/

    public function buscarportaria($where=array(), $order=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
                array('ap' => $this->_name),
                array(
                    'ap.PortariaAprovacao',
                    'ap.DtPublicacaoAprovacao',
                    'ap.dtPortariaAprovacao',
                    //'pr.IdPRONAC'
                )
        );
        $select->joinInner(
                array('pr' => 'Projetos'),
                'pr.AnoProjeto = ap.AnoProjeto AND pr.Sequencial = ap.Sequencial',
                array('')
        );
        foreach ($where as $comando => $valores) {
            $select->where($comando, $valores);
        }
        foreach ($order as $valores) {
            $select->order($valores);
        }
        
        return $this->fetchAll($select);
    }

    public function consultaPortaria($where=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => 'Projetos'),
                array(
                    'a.IdPRONAC',
                    'a.AnoProjeto',
                    'a.Sequencial',
                    'a.NomeProjeto',
                    'a.DtInicioExecucao',
                    'a.DtFimExecucao',
                    new Zend_Db_Expr('sac.dbo.fnInicioCaptacao(a.AnoProjeto,a.Sequencial) as dtInicioCaptacao'),
                    new Zend_Db_Expr('sac.dbo.fnFimCaptacao(a.AnoProjeto,a.Sequencial) as dtFimCaptacao')
                ),
            'SAC.dbo'
        );
        $select->joinInner(
                array('b' => 'Aprovacao'),
                'a.AnoProjeto = b.AnoProjeto AND a.Sequencial = b.Sequencial',
                array('b.PortariaAprovacao'),
            'SAC.dbo'
        );

        foreach ($where as $comando => $valores) {
            $select->where($comando, $valores);
        }

        return $this->fetchAll($select);
    }

    public function consultaPortariaReadequacoes($where=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('r' => 'tbReadequacao'),
                array('idReadequacao', 'CAST(r.dsSolicitacao AS TEXT) AS dsSolicitacao'),
            'SAC.dbo'
        );
        $select->joinInner(
                array('a' => 'Projetos'),
            'r.idPronac = a.IdPRONAC',
                array(
                    'a.IdPRONAC', 'a.AnoProjeto', 'a.Sequencial', 'a.NomeProjeto', 'a.DtInicioExecucao', 'a.DtFimExecucao',
                    new Zend_Db_Expr('sac.dbo.fnInicioCaptacao(a.AnoProjeto,a.Sequencial) as dtInicioCaptacao'),
                    new Zend_Db_Expr('sac.dbo.fnFimCaptacao(a.AnoProjeto,a.Sequencial) as dtFimCaptacao')
                ),
            'SAC.dbo'
        );
        $select->joinInner(
                array('b' => 'Aprovacao'),
            'a.AnoProjeto = b.AnoProjeto AND a.Sequencial = b.Sequencial AND b.idReadequacao = r.idReadequacao',
                array('b.PortariaAprovacao'),
            'SAC.dbo'
        );
        $select->joinInner(
                array('tpr' => 'tbTipoReadequacao'),
            'tpr.idTipoReadequacao = r.idTipoReadequacao',
                array('tpr.idTipoReadequacao','tpr.dsReadequacao'),
            'SAC.dbo'
        );

        foreach ($where as $comando => $valores) {
            $select->where($comando, $valores);
        }

        
        return $this->fetchAll($select);
    }

    public function consultaPortariaImpressao($where=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => 'Projetos'),
                array(
                    'a.IdPRONAC',
                    'a.AnoProjeto',
                    'a.Sequencial',
                    'a.NomeProjeto',
                    'a.CgcCpf',
                    'c.Descricao as Area',
                    'd.Descricao as Segmento',
                    new Zend_Db_Expr("
                        CASE
                            WHEN a.Mecanismo ='2' OR a.Mecanismo ='6'
                            THEN SAC.dbo.fnValorAprovadoConvenio(a.AnoProjeto,a.Sequencial)
                            ELSE SAC.dbo.fnValorAprovado(a.AnoProjeto,a.Sequencial)
                            END as ValorAprovado
                        "),
                    'b.PortariaAprovacao',
                    'b.DtPublicacaoAprovacao',
                    'b.DtPortariaAprovacao'
                ),
            'SAC.dbo'
        );
        $select->joinInner(
                array('b' => 'Aprovacao'),
                'a.AnoProjeto = b.AnoProjeto AND a.Sequencial = b.Sequencial',
                array('b.PortariaAprovacao'),
            'SAC.dbo'
        );
        $select->joinInner(
                array('c' => 'Area'),
            'c.Codigo = a.Area',
                array(''),
            'SAC.dbo'
        );
        $select->joinInner(
                array('d' => 'Segmento'),
            'd.Codigo = a.Segmento',
                array(''),
            'SAC.dbo'
        );

        foreach ($where as $comando => $valores) {
            $select->where($comando, $valores);
        }

        
        return $this->fetchAll($select);
    }


    public function buscarAprovados($mecanismo, $QntdPorPagina=null, $PaginaAtual=null)
    {
        $TotalReg = $PaginaAtual*$QntdPorPagina;
        $select =  new Zend_Db_Expr("select * from (select top ". $QntdPorPagina ." * from (SELECT TOP ". $TotalReg ."
                ap.*, (pr.AnoProjeto+pr.Sequencial) AS pronac, pr.NomeProjeto FROM SAC.dbo.Aprovacao AS ap
            INNER JOIN SAC.dbo.Projetos AS pr ON pr.IdPRONAC = ap.idPRONAC WHERE pr.Mecanismo = '".$mecanismo."'  order by ap.idAprovacao)
            as tabela order by idAprovacao desc) as tabela order by idAprovacao");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
            
        return $db->fetchAll($select);
    }

    public function buscarMecanismo($mecanismo)
    {
        $select =  new Zend_Db_Expr("SELECT ap.*, (pr.AnoProjeto+pr.Sequencial) AS pronac, pr.NomeProjeto FROM SAC.dbo.Aprovacao AS ap
            INNER JOIN SAC.dbo.Projetos AS pr ON pr.IdPRONAC = ap.idPRONAC WHERE pr.Mecanismo = $mecanismo  order by ap.idAprovacao");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
            
        return $db->fetchAll($select);
    }

    public static function buscaTotalAprovadoProjeto($anoProjeto, $sequencial)
    {
        $select =  new Zend_Db_Expr("SELECT SAC.dbo.fnTotalAprovadoProjeto('$anoProjeto', '$sequencial') as total");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
            
        return $db->fetchAll($select);
    }

    public function buscarAprovacao($idpronac = null, $tipoParecer = null, $usu_codigo = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('ap' => $this->_name),
                array(
                    "CONVERT(CHAR(10), ap.DtAprovacao, 103) AS DtAprovacao",
                    'ap.ResumoAprovacao as ResumoAprovacao',
                    'ap.TipoAprovacao'
                )
        );
        if (!empty($tipoParecer)) {
            $select->where('ap.TipoAprovacao in(?)', $tipoParecer);
        }
        if (!empty($usu_codigo)) {
            $select->where('ap.Logon in(?)', $usu_codigo);
        }

        $select->where('ap.IdPRONAC = ?', $idpronac);

        $result = $this->fetchAll($select);
        return $result;
    }

    public static function alterarAprovacao($dados, $idpronac)
    {
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $where = "idpronac = $idpronac";
            $alterar = $db->update("SAC.dbo.Aprovacao", $dados, $where);
        } catch (Exception $e) {
            die("ERRO: AlterarAprovacao-Aprovacao. ".$e->getMessage());
        }
    }

    public function fnTotalAprovadoProjeto($anoProjeto, $sequencial)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
//        $select->distinct();
        $select->from(
                array($this->_name),
                array(
                    'totalAprovado' => new Zend_Db_Expr(" SAC.dbo.fnTotalAprovadoProjeto('$anoProjeto', '$sequencial')")
                )
        );

        return $this->fetchRow($select);
    }

    public function buscarDatasCaptacao($pronac, $idProrrogacao)
    {
        $select =  new Zend_Db_Expr("
            SELECT b.DtInicio,b.DtFinal FROM SAC.dbo.Aprovacao a
            INNER JOIN SAC.dbo.Prorrogacao b on (a.AnoProjeto = b.AnoProjeto and a.Sequencial = b.Sequencial and  a.idProrrogacao =  b.idProrrogacao)
            WHERE a.TipoAprovacao ='3' and a.AnoProjeto+a.Sequencial='$pronac' and b.idProrrogacao = $idProrrogacao
        ");

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        
        return $db->fetchAll($select);
    }
}
