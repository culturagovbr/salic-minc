<?php
/**
 * Description of Sgcacesso
 *
 * @author augusto
 */

class Captacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "Captacao";

    public function listaCaptacao($AnoProjeto, $Sequencial)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('c'=>$this->_name),
                array(
                'c.CaptacaoReal'
                )
        );

        $select->where('c.AnoProjeto = ?', $AnoProjeto);
        $select->where('c.Sequencial = ?', $Sequencial);

        return $this->fetchAll($select);
    } // fecha metodo listasituacao()



    /**
     * Metodo para buscar
     * @access public
     * @param void
     * @return object/array
     */
    public function buscarDados()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this);
        $select->order('DtChegadaRecibo');
        $select->order('DtRecibo');
        return $this->fetchAll($select);
    } // fecha metodo buscarDados()



    /**
     * Metodo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha metodo cadastrarDados()



    /**
     * Metodo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "Idcaptacao = " . $where;
        return $this->update($dados, $where);
    } // fecha metodo alterarDados()



    /**
     * Metodo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirDados($where)
    {
        $where = "Idcaptacao = " . $where;
        return $this->delete($where);
    } // fecha metodo excluirDados()

    /**
     * Metodo para buscar
     * @access public
     * @return float TotalCaptadoReal
     */
    public function BuscarTotalCaptadoReal($retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array($this->_name),
                array(
                'AnoProjeto',
                'Sequencial',
                'Mec'=>'isnull(sum(captacaoreal),0)'
                )
        );
        $select->group('AnoProjeto');
        $select->group('Sequencial');

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }
    /**
     * Metodo para buscar
     * @access public
     * @param integer $AnoProjeto,$Sequencial,$selectC,$selectCq,$selectCg,$selectCc
     * @return float totalCaptadoProjeto
     */
    public function BuscarTotalCaptadoProjeto($selectC, $selectCq, $selectCg, $selectCc, $retornaSelect = false, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('Captacao'=>$this->_name),
                array(
                'AnoProjeto',
                'Sequencial',
                'Total'=> new Zend_Db_Expr("isnull(C.Mec,0)+isnull(Cq.Art1,0)+isnull(Cg.Art3,0)+isnull(Cc.Conv,0)")
                )
        );
        $select->joinLeft(
                array('C'=>$selectC),
                "Captacao.AnoProjeto = C.AnoProjeto and Captacao.Sequencial = C.Sequencial",
                array('C.Mec AS Mec')
        );
        $select->joinLeft(
                array('Cq'=>$selectCq),
                "Captacao.AnoProjeto = Cq.AnoProjeto and Captacao.Sequencial = Cq.Sequencial",
                array('Cq.Art1 AS Art1')
        );
        $select->joinLeft(
                array('Cg'=>$selectCg),
                "Captacao.AnoProjeto = Cg.AnoProjeto and Captacao.Sequencial = Cg.Sequencial",
                array('Cg.Art3 AS Art3')
        );
        $select->joinLeft(
                array('Cc'=>$selectCc),
                "Captacao.AnoProjeto = Cc.AnoProjeto and Captacao.Sequencial = Cc.Sequencial",
                array('Cc.Conv AS Conv')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->group('Captacao.AnoProjeto');
        $select->group('Captacao.Sequencial');
        $select->group('Mec');
        $select->group('Art1');
        $select->group('Art3');
        $select->group('Conv');

//        $select->limit(1);
        $select->assemble();
        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    public function CapitacaoTotalMEC($AnoProjeto, $Sequencial)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('c'=>$this->_name),
                array('Mec' => new Zend_Db_Expr("SUM(c.CaptacaoReal)"))
        );

        $select->where('c.AnoProjeto = ?', $AnoProjeto, 'c.Sequencial = ?', $Sequencial);

        return $this->fetchAll($select);
    } // fecha metodo listasituacao()



    public function captacaoPorProjeto($idPronac, $tamanho=-1, $inicio=-1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            //$select->limit($tamanho, $tmpInicio);
        }
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        if ($inicio < 0) {
            $slct->from(
                    array('c' => $this->_name),
                    array(
                            'c.CgcCPfMecena' ,
                            'c.NumeroRecibo',
                            'c.DtChegadaRecibo',
                            'c.DtRecibo',
                            'c.CaptacaoReal',
                            'TipoApoio' =>  new Zend_Db_Expr("case when c.TipoApoio = '2' then 'Patroc&iacute;nio' else 'Doa&ccedil;&atilde;o' end")
                    )
            );
        } else {
            $soma = $tamanho+$tmpInicio;
            $slct->from(
                    array('c' => $this->_name),
                    array(
                            new Zend_Db_Expr("TOP $soma c.CgcCPfMecena") ,
                            'c.NumeroRecibo',
                            'c.DtChegadaRecibo',
                            'c.DtRecibo',
                            'c.CaptacaoReal',
                            'TipoApoio' =>  new Zend_Db_Expr("case when c.TipoApoio = '2' then 'Patroc&iacute;nio' else 'Doa&ccedil;&atilde;o' end")
                    )
            );
        }
        $slct->joinInner(
                array("i"=>"Interessado"),
                "c.CgcCPfMecena = i.CgcCPf",
                array(
                        'i.Nome'
                )
        );
        $slct->joinInner(
                array("p"=>"Projetos"),
                "c.Anoprojeto = p.Anoprojeto and c.Sequencial = p.Sequencial",
                array(
                        'NrProjeto'=>new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'),
                        'p.NomeProjeto'
                )
        );

        $slct->where('p.IdPRONAC = ?', $idPronac);
        $slct->order(
            array(
                'i.Nome'
            )
        );

        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
                $slct,
            array(new Zend_Db_Expr("TOP $tamanho  *"))

        );
        $selectAux->order(array('Nome desc'));

        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order(array('Nome'));

        // paginacao
        if ($inicio <= 0) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchAll($selectAux2);
        }
    }
    public function captacaoPorProjetoTotal($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);


        $slct->from(
                    array('c' => $this->_name),
                    array('total'=>new Zend_Db_Expr('count(*)'))
            );
        $slct->joinInner(
                array("i"=>"Interessado"),
                "c.CgcCPfMecena = i.CgcCPf",
                array()
        );
        $slct->joinInner(
                array("p"=>"Projetos"),
                "c.Anoprojeto = p.Anoprojeto and c.Sequencial = p.Sequencial",
                array()
        );

        $slct->where('p.IdPRONAC = ?', $idPronac);
        


        return $this->fetchAll($slct);
    }

    public function TotalCaptacaoReal($pronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array($this->_name),
                array('Soma' => 'sum(CaptacaoReal)')
        );

        $select->where('AnoProjeto+Sequencial = ?', $pronac);

        return $this->fetchAll($select);
    } // fecha metodo listasituacao()


    public function buscaCompleta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                    array("c"=>$this->_name),
                    array("c.CgcCPfMecena" ,
                          "c.NumeroRecibo",
                          "c.DtTransferenciaRecurso",
                          "c.DtRecibo",
                          "c.CaptacaoReal",
                          "TipoApoio" =>  new Zend_Db_Expr("CASE WHEN c.TipoApoio = '2' THEN 'Patroc&iacute;nio' ELSE 'Doa&ccedil;&atilde;o' END"),
                        ),
            "SAC.dbo"
                    );
        $slct->joinInner(
                        array("p"=>"Projetos"),
                        "c.Anoprojeto = p.Anoprojeto and c.Sequencial = p.Sequencial",
                        array("p.IdPRONAC",
                              "NrProjeto"=>new Zend_Db_Expr("p.AnoProjeto + p.Sequencial"),
                              "p.NomeProjeto",
                            ),
                        "SAC.dbo"
                        );
        $slct->joinInner(
                        array("i"=>"Interessado"),
                        "c.CgcCPfMecena = i.CgcCPf",
                        array("i.Nome"),
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
                         array("c"=>$this->_name),
                         array("total"=>"count(*)",
                               "totalGeralCaptado"=>"sum(CaptacaoReal)"),
                "SAC.dbo"
                        );
            $slctCount->joinInner(
                        array("p"=>"Projetos"),
                        "c.Anoprojeto = p.Anoprojeto and c.Sequencial = p.Sequencial",
                        array(),
                        "SAC.dbo"
                        );
            $slctCount->joinInner(
                        array("i"=>"Interessado"),
                        "c.CgcCPfMecena = i.CgcCPf",
                        array(),
                        "SAC.dbo"
                        );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctCount->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctCount)->current();
            if ($rs) {
                return $rs;
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


    public function buscarDemonstrativoDeCaptacaoCNIC($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $arrVlAutorizado=array(), $blnSomatorioVlAutorizado=false, $blnSomatorioVlCaptado=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(
                        array('ca'=>$this->_name),
                        array('DtRecibo',
                              'CaptacaoReal',
                              'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                              'vlCaptado'=>new Zend_Db_Expr("SAC.dbo.fnTotalCaptadoProjeto(ca.AnoProjeto,ca.Sequencial)")
                             )
                     );
        $slct->joinInner(
                            array('p'=>'Projetos'),
                            'ca.AnoProjeto+ca.Sequencial = p.AnoProjeto+p.Sequencial',
                            array("PRONAC"=>new Zend_Db_Expr("p.AnoProjeto+ p.Sequencial"),
                                  "NomeProjeto"),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('ag'=>'Agentes'),
                            'p.CgcCpf = ag.CNPJCPF',
                            array('CNPJCPFProponente'=>'ag.CNPJCPF'),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('n'=>'Nomes'),
                            'ag.idAgente = n.idAgente',
                            array("Proponente"=>"Descricao"),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('a'=>'Area'),
                            'p.Area = a.Codigo',
                            array("DescArea"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('se'=>'Segmento'),
                            'p.Segmento = se.Codigo',
                            array("DescSegmento"=>"Descricao"),
                            "SAC.dbo"
                          );

        /*$slct->joinInner(
                            array('r'=>'tbReuniao'),
                            't.idNrReuniao = r.idNrReuniao',
                            array(),
                            "SAC.dbo"
                          );*/
        $slct->joinInner(
                            array('uf'=>'UF'),
                            'p.UfProjeto = uf.Sigla',
                            array("Sigla"),
                            "Agentes.dbo"
                          );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //TOTAL DE REGISTRO - PARA PAGINACAO
        if ($count && empty($arrVlAutorizado)) {
            $slctCount = $this->select();
            $slctCount->distinct();
            $slctCount->setIntegrityCheck(false);
            $slctCount->from(
                        array('ca'=>$this->_name),
                        array('total'=>'count(*)')
                     );
            $slctCount->joinInner(
                                array('p'=>'Projetos'),
                                'ca.AnoProjeto+ca.Sequencial = p.AnoProjeto+p.Sequencial',
                            array(),
                                'SAC.dbo'
                              );
            $slctCount->joinInner(
                                array('a'=>'Area'),
                                'p.Area = a.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slctCount->joinInner(
                                array('se'=>'Segmento'),
                                'p.Segmento = se.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slctCount->joinInner(
                                array('ag'=>'Agentes'),
                                'p.CgcCpf = ag.CNPJCPF',
                                array(),
                                "Agentes.dbo"
                              );
            $slctCount->joinInner(
                                array('n'=>'Nomes'),
                                'ag.idAgente = n.idAgente',
                                array(),
                                "Agentes.dbo"
                              );
            /*$slctCount->joinInner(
                                array('r'=>'tbReuniao'),
                                't.idNrReuniao = r.idNrReuniao',
                                array(),
                                "SAC.dbo"
                              );*/
            $slctCount->joinInner(
                                array('uf'=>'UF'),
                                'p.UfProjeto = uf.Sigla',
                                array(),
                                "Agentes.dbo"
                              );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctCount->where($coluna, $valor);
            }

            //x($slctCount->__toString());
            $rs = $this->fetchAll($slctCount)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
        }

        //RETORNA SOMATORIO - VALOR AUTORIZADO
        if ($blnSomatorioVlAutorizado) {
            $slctSA = $this->select();
            $slctSA->setIntegrityCheck(false);
            $slctSA->distinct();
            $slctSA->from(
                            array('ca'=>$this->_name),
                            array('vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                                  'DtRecibo',
                                  'CaptacaoReal',
                                  'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                                  'vlCaptado'=>new Zend_Db_Expr("SAC.dbo.fnTotalCaptadoProjeto(ca.AnoProjeto,ca.Sequencial)")
                                 )
                         );
            $slctSA->joinInner(
                                array('p'=>'Projetos'),
                                'ca.AnoProjeto+ca.Sequencial = p.AnoProjeto+p.Sequencial',
                                array(),
                                'SAC.dbo'
                              );
            $slctSA->joinInner(
                                array('ag'=>'Agentes'),
                                'p.CgcCpf = ag.CNPJCPF',
                                array(),
                                "Agentes.dbo"
                              );
            $slctSA->joinInner(
                                array('n'=>'Nomes'),
                                'ag.idAgente = n.idAgente',
                                array(),
                                "Agentes.dbo"
                              );
            $slctSA->joinInner(
                                array('a'=>'Area'),
                                'p.Area = a.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slctSA->joinInner(
                                array('se'=>'Segmento'),
                                'p.Segmento = se.Codigo',
                                array(),
                                "SAC.dbo"
                              );

            $slctSA->joinInner(
                                array('uf'=>'UF'),
                                'p.UfProjeto = uf.Sigla',
                                array(),
                                "Agentes.dbo"
                              );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctSA->where($coluna, $valor);
            }

            $slctSomatorio = $this->select();
            $slctSomatorio->setIntegrityCheck(false);
            $slctSomatorio->from(
                            array('Master'=>$slctSA),
                            array('somatorioVlAutorizado'=>new Zend_Db_Expr('SUM(vlAutorizado)'))
                         );

            //adicionando linha order ao select
            $slctSomatorio->order($order);

            //x($slctSomatorio->assemble());
            return $this->fetchAll($slctSomatorio);
        }//Fim Somat�rio do Valor Autorizado

        //RETORNA SOMATORIO - VALOR CAPTADO
        if ($blnSomatorioVlCaptado) {
            $slctSC = $this->select();
            $slctSC->setIntegrityCheck(false);
            $slctSC->distinct();
            $slctSC->from(
                            array('ca'=>$this->_name),
                            array('vlCaptado'=>new Zend_Db_Expr("SAC.dbo.fnTotalCaptadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                                     'DtRecibo',
                                  'CaptacaoReal',
                                  'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                                  'vlCaptado'=>new Zend_Db_Expr("SAC.dbo.fnTotalCaptadoProjeto(ca.AnoProjeto,ca.Sequencial)")
                             )
                         );
            $slctSC->joinInner(
                            array('p'=>'Projetos'),
                            'ca.AnoProjeto+ca.Sequencial = p.AnoProjeto+p.Sequencial',
                            array("PRONAC"=>new Zend_Db_Expr("p.AnoProjeto+ p.Sequencial"),
                                  "NomeProjeto"),
                            'SAC.dbo'
                          );
            $slctSC->joinInner(
                            array('ag'=>'Agentes'),
                            'p.CgcCpf = ag.CNPJCPF',
                            array('CNPJCPFProponente'=>'ag.CNPJCPF'),
                            "Agentes.dbo"
                          );
            $slctSC->joinInner(
                            array('n'=>'Nomes'),
                            'ag.idAgente = n.idAgente',
                            array("Proponente"=>"Descricao"),
                            "Agentes.dbo"
                          );
            $slctSC->joinInner(
                            array('a'=>'Area'),
                            'p.Area = a.Codigo',
                            array("DescArea"=>"Descricao"),
                            "SAC.dbo"
                          );
            $slctSC->joinInner(
                            array('se'=>'Segmento'),
                            'p.Segmento = se.Codigo',
                            array("DescSegmento"=>"Descricao"),
                            "SAC.dbo"
                          );

            /*$slct->joinInner(
                             array('r'=>'tbReuniao'),
                             't.idNrReuniao = r.idNrReuniao',
                             array(),
                             "SAC.dbo"
                           );*/
            $slctSC->joinInner(
                            array('uf'=>'UF'),
                            'p.UfProjeto = uf.Sigla',
                            array("Sigla"),
                            "Agentes.dbo"
                          );


            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctSC->where($coluna, $valor);
            }

            $slctSomatorio = $this->select();
            $slctSomatorio->setIntegrityCheck(false);
            $slctSomatorio->from(
                            array('Master'=>$slctSC),
                            array('somatorioVlCaptado'=>new Zend_Db_Expr('SUM(vlCaptado)'))
                         );

            //adicionando linha order ao select
            $slctSomatorio->order($order);

            return $this->fetchAll($slctSomatorio);
        }//Fim Somat�rio do Valor Captado

        if (!empty($arrVlAutorizado)) {
            $slctMaster = $this->select();
            $slctMaster->setIntegrityCheck(false);
            $slctMaster->from(
                            array('Master'=>$slct),
                            array('*')
                         );

            //BUSCA PELO VALOR AUTORIZADO
            foreach ($arrVlAutorizado as $coluna => $valor) {
                $slctMaster->where($coluna, $valor);
            }

            //RETORNA QTDE. DE REGISTRO PARA PAGINACAO
            if ($count) {
                return $this->fetchAll($slctMaster)->count();
            }
            //adicionando linha order ao select
            $slctMaster->order($order);

            // paginacao
            if ($tamanho > -1) {
                $tmpInicio = 0;
                if ($inicio > -1) {
                    $tmpInicio = $inicio;
                }
                $slctMaster->limit($tamanho, $tmpInicio);
            }
            //x($slctMaster->assemble());
            return $this->fetchAll($slctMaster);
        } else {

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

            //x($slct->assemble());
            return $this->fetchAll($slct);
        }
    }

    /*  Busca Demonstativo de Capta��o de Recurso da CNIC  */
    public function buscarDemonstrativoDeCaptacao($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $arrVlAutorizado=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(
                        array('ca'=>$this->_name),
                         array(
                              'CaptacaoReal'=>new Zend_Db_Expr('SUM(CaptacaoReal)'),
                              'p.AnoProjeto',
                              'p.Sequencial',
                               'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)")
                             )
                     );
        $slct->joinInner(
                            array('p'=>'Projetos'),
                            'ca.AnoProjeto+ca.Sequencial = p.AnoProjeto+p.Sequencial',
                            array("PRONAC"=>new Zend_Db_Expr("p.AnoProjeto+ p.Sequencial"),
                                  "NomeProjeto",
                                  "IdPRONAC"),
                            'SAC.dbo'
                          );
        $slct->joinInner(
                            array('ag'=>'Agentes'),
                            'p.CgcCpf = ag.CNPJCPF',
                            array('CNPJCPFProponente'=>'ag.CNPJCPF'),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('n'=>'Nomes'),
                            'ag.idAgente = n.idAgente',
                            array("Proponente"=>"Descricao"),
                            "Agentes.dbo"
                          );
        $slct->joinInner(
                            array('a'=>'Area'),
                            'p.Area = a.Codigo',
                            array("DescArea"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('se'=>'Segmento'),
                            'p.Segmento = se.Codigo',
                            array("DescSegmento"=>"Descricao"),
                            "SAC.dbo"
                          );
        $slct->joinInner(
                            array('uf'=>'UF'),
                            'p.UfProjeto = uf.Sigla',
                            array("Sigla"),
                            "Agentes.dbo"
                          );
        $slct->group(array('p.AnoProjeto','p.Sequencial','p.IdPRONAC','ca.Sequencial','ca.AnoProjeto','p.NomeProjeto','ag.CNPJCPF',
                          'n.Descricao','a.Descricao','se.Descricao','uf.Sigla'));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //TOTAL DE REGISTRO - PARA PAGINACAO
        if ($count && empty($arrVlAutorizado)) {
            $slctCount = $this->select();
            $slctCount->distinct();
            $slctCount->setIntegrityCheck(false);
            /*$slctCount->from(
                        array('ca'=>$this->_name),
                        array('total'=>'count(*)')
                     );*/
            $slctCount->from(
                            array('ca'=>$this->_name),
                             array('total'=>'count(*)',
                                   'CaptacaoReal'=>new Zend_Db_Expr('SUM(CaptacaoReal)'),
                                   'p.AnoProjeto',
                                   'p.Sequencial',
                                   'p.IdPRONAC',
                                   'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)")
                             )
                         );
            $slctCount->joinInner(
                                array('p'=>'Projetos'),
                                'ca.AnoProjeto+ca.Sequencial = p.AnoProjeto+p.Sequencial',
                                array(),
                                'SAC.dbo'
                              );
            $slctCount->joinInner(
                                array('ag'=>'Agentes'),
                                'p.CgcCpf = ag.CNPJCPF',
                                array(),
                                "Agentes.dbo"
                              );
            $slctCount->joinInner(
                                array('n'=>'Nomes'),
                                'ag.idAgente = n.idAgente',
                                array(),
                                "Agentes.dbo"
                              );
            $slctCount->joinInner(
                                array('a'=>'Area'),
                                'p.Area = a.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slctCount->joinInner(
                                array('se'=>'Segmento'),
                                'p.Segmento = se.Codigo',
                                array(),
                                "SAC.dbo"
                              );
            $slctCount->joinInner(
                                array('uf'=>'UF'),
                                'p.UfProjeto = uf.Sigla',
                                array(),
                                "Agentes.dbo"
                              );
            $slctCount->group(array('p.AnoProjeto','p.Sequencial','p.IdPRONAC','ca.Sequencial','ca.AnoProjeto','p.NomeProjeto','ag.CNPJCPF',
                              'n.Descricao','a.Descricao','se.Descricao','uf.Sigla'));

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctCount->where($coluna, $valor);
            }

            //x($slctCount->__toString());

            $rs = $this->fetchAll($slctCount);
            if ($rs) {
                return count($rs);
            } else {
                return 0;
            }
        }

        if (!empty($arrVlAutorizado)) {
            $slctMaster = $this->select();
            $slctMaster->setIntegrityCheck(false);
            $slctMaster->from(
                            array('Master'=>$slct),
                            array('*')
                         );

            //BUSCA PELO VALOR AUTORIZADO
            foreach ($arrVlAutorizado as $coluna => $valor) {
                $slctMaster->where($coluna, $valor);
            }

            //RETORNA QTDE. DE REGISTRO PARA PAGINACAO
            if ($count) {
                return $this->fetchAll($slctMaster)->count();
            }
            //adicionando linha order ao select
            $slctMaster->order($order);

            // paginacao
            if ($tamanho > -1) {
                $tmpInicio = 0;
                if ($inicio > -1) {
                    $tmpInicio = $inicio;
                }
                $slctMaster->limit($tamanho, $tmpInicio);
            }
            //x($slctMaster->assemble());
            return $this->fetchAll($slctMaster);
        } else {
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

            //x($slct->assemble());
            return $this->fetchAll($slct);
        }
    }

    /* Somatorio do valor Captado */
    public function buscarDemonstrativoDeCaptacaoSomatorioValorCaptado($where=array(), $blnSomatorioVlCaptado)
    {
        //RETORNA SOMATORIO - VALOR CAPTADO
        $slctSC = $this->select();
        $slctSC->setIntegrityCheck(false);
        $slctSC->distinct();
        $slctSC->from(
                        array('ca'=>$this->_name),
                        array('vlCaptado'=>new Zend_Db_Expr("SUM(ca.captacaoReal)"),
                              /*'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)")

                              'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                              'vlCaptado'=>new Zend_Db_Expr("SAC.dbo.fnTotalCaptadoProjeto(ca.AnoProjeto,ca.Sequencial)")*/
                             )
                     );
        $slctSC->joinInner(
                            array('p'=>'Projetos'),
            '(ca.AnoProjeto+ca.Sequencial) = (p.AnoProjeto+p.Sequencial)',
                            array(),
            'SAC.dbo'
                          );
        $slctSC->joinInner(
                            array('ag'=>'Agentes'),
                            'p.CgcCpf = ag.CNPJCPF',
                            array(),
                            "Agentes.dbo"
                          );
        $slctSC->joinInner(
                            array('n'=>'Nomes'),
                            'ag.idAgente = n.idAgente',
                            array(),
                            "Agentes.dbo"
                          );
        $slctSC->joinInner(
                            array('a'=>'Area'),
                            'p.Area = a.Codigo',
                            array(),
                            "SAC.dbo"
                          );
        $slctSC->joinInner(
                            array('se'=>'Segmento'),
                            'p.Segmento = se.Codigo',
                            array(),
                            "SAC.dbo"
                          );
        $slctSC->joinInner(
                            array('uf'=>'UF'),
                            'p.UfProjeto = uf.Sigla',
                            array(),
                            "Agentes.dbo"
                          );
        $slctSC->group(array('p.AnoProjeto','p.Sequencial','ca.Sequencial','ca.AnoProjeto','p.NomeProjeto','ag.CNPJCPF',
                          'n.Descricao','a.Descricao','se.Descricao','uf.Sigla'));

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctSC->where($coluna, $valor);
        }

        $slctSomatorio = $this->select();
        $slctSomatorio->setIntegrityCheck(false);
        $slctSomatorio->from(
                            array('Master'=>$slctSC),
                            array('somatorioVlCaptado'=>new Zend_Db_Expr('SUM(vlCaptado)'))
                         );

        //adicionando linha order ao select
        //$slctSomatorio->order($order);

        //x($slctSC->assemble());
        return $this->fetchAll($slctSomatorio);
    }

    /* Somatorio do valor Autorizado */
    public function buscarDemonstrativoDeCaptacaoSomatorioValorAutorizado($where=array(), $blnSomatorioVlCaptado)
    {
        //RETORNA SOMATORIO - VALOR AUTORIZADO
        $slctSA = $this->select();
        $slctSA->setIntegrityCheck(false);
        $slctSA->distinct();
        $slctSA->from(
                        array('ca'=>$this->_name),
                        array('vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                              /*'CaptacaoReal'=>new Zend_Db_Expr("SUM(ca.captacaoReal)")

                              'vlAutorizado'=>new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(ca.AnoProjeto,ca.Sequencial)"),
                              'vlCaptado'=>new Zend_Db_Expr("SAC.dbo.fnTotalCaptadoProjeto(ca.AnoProjeto,ca.Sequencial)")*/
                             )
                     );
        $slctSA->joinInner(
                            array('p'=>'Projetos'),
                            'ca.AnoProjeto+ca.Sequencial = p.AnoProjeto+p.Sequencial',
                            array(),
                            'SAC.dbo'
                          );
        $slctSA->joinInner(
                            array('ag'=>'Agentes'),
                            'p.CgcCpf = ag.CNPJCPF',
                            array(),
                            "Agentes.dbo"
                          );
        $slctSA->joinInner(
                            array('n'=>'Nomes'),
                            'ag.idAgente = n.idAgente',
                            array(),
                            "Agentes.dbo"
                          );
        $slctSA->joinInner(
                            array('a'=>'Area'),
                            'p.Area = a.Codigo',
                            array(),
                            "SAC.dbo"
                          );
        $slctSA->joinInner(
                            array('se'=>'Segmento'),
                            'p.Segmento = se.Codigo',
                            array(),
                            "SAC.dbo"
                          );

        $slctSA->joinInner(
                            array('uf'=>'UF'),
                            'p.UfProjeto = uf.Sigla',
                            array(),
                            "Agentes.dbo"
                          );
        /*$slctSA->group(array('p.AnoProjeto','p.Sequencial','ca.Sequencial','ca.AnoProjeto','p.NomeProjeto','ag.CNPJCPF',
                          'n.Descricao','a.Descricao','se.Descricao','uf.Sigla'));*/

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctSA->where($coluna, $valor);
        }

        $slctSomatorio = $this->select();
        $slctSomatorio->setIntegrityCheck(false);
        $slctSomatorio->from(
                            array('Master'=>$slctSA),
                            array('somatorioVlAutorizado'=>new Zend_Db_Expr('SUM(vlAutorizado)'))
                         );

        //adicionando linha order ao select
        //$slctSomatorio->order($order);

        //x($slctSomatorio->assemble());
        return $this->fetchAll($slctSomatorio);
    }

    /*  Pega o alor total do projeto pelo PRONAC
     *  Captado
     *  Autorizado
     *
     */
    public function valorTotal($tipo, $ano, $sequencial)
    {
        $pronac = $ano.$sequencial;

        if ($tipo == 'captado') {
            $slct = $this->select();
            $slct->setIntegrityCheck(false);
            $slct->distinct();
            $slct->from(
                            array('ca'=>$this->_name),
                            array('vlCaptado'=>new Zend_Db_Expr("SUM(ca.captacaoReal)")
                                 )
                         );
            $slct->where('AnoProjeto+Sequencial = ?', $pronac);
            //$slct = "select SUM(captacaoReal) as VlCaptado from Captacao where AnoProjeto+Sequencial='{$pronac}'";
            $valor = $this->fetchAll($slct);
            foreach ($valor as $val) {
                $total = $val->vlCaptado;
            }
            return $total;
        }

        if ($tipo == 'autorizado') {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $slct = "select SAC.dbo.fnTotalAprovadoProjeto('{$ano}','{$sequencial}')";
            $valor = $db->fetchAll($slct);
            return $valor[0]->computed;
        }
    }

    public function buscaExtratoCaptacao($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                    array("c"=>$this->_name),
                    array('PRONAC' => new Zend_Db_Expr('c.AnoProjeto + c.Sequencial'),
                          "c.CgcCpfMecena",
                          "c.DtChegadaRecibo",
                          "c.DtRecibo",
                          "c.CaptacaoReal",
                          "c.NumeroRecibo",
                          "TipoApoio" => new Zend_Db_Expr("CASE WHEN c.TipoApoio = 1 then 'Patrocínio' WHEN c.TipoApoio = 2 then 'Doa&ccedil;&atilde;o' END "),
                          "Incentivador" =>  new Zend_Db_Expr("SAC.dbo.fnNome(a.idagente)"),
                          "DtLiberacao" =>  new Zend_Db_Expr("(SELECT TOP 1 DtLiberacao FROM SAC.dbo.Liberacao l WHERE c.AnoProjeto+c.Sequencial = l.AnoProjeto+l.Sequencial)"),
                          "Percentual" =>  new Zend_Db_Expr("SAC.dbo.fnPercentualCaptado(c.AnoProjeto, c.Sequencial)"),
                          "c.idCaptacao"
                        ),
            "SAC.dbo"
                    );
        $slct->joinInner(
                        array("a"=>"Agentes"),
                        "c.CgcCpfMecena = a.CNPJCPF",
                        array(),
                        "Agentes.dbo"
                        );
        $slct->joinInner(
                        array("p"=>"Projetos"),
                        "p.AnoProjeto = c.AnoProjeto AND p.Sequencial = c.Sequencial",
                        array('IdPRONAC', 'Situacao'),
                        "SAC.dbo"
                        );
        $slct->joinInner(
                        array("o"=>"Orgaos"),
                        "p.Orgao = o.Codigo",
                        array('idSecretaria'),
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
                         array("c"=>$this->_name),
                         array("total"=>"count(*)"),
                "SAC.dbo"
                        );
            $slctCount->joinInner(
                        array("a"=>"Agentes"),
                        "c.CgcCpfMecena = a.CNPJCPF",
                        array(),
                        "Agentes.dbo"
                        );
            $slctCount->joinInner(
                            array("p"=>"Projetos"),
                            "p.AnoProjeto = c.AnoProjeto AND p.Sequencial = c.Sequencial",
                            array(),
                            "SAC.dbo"
                            );
            $slctCount->joinInner(
                        array("o"=>"Orgaos"),
                        "p.Orgao = o.Codigo",
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


    public function buscaReciboCaptacao($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                    array("c"=>$this->_name),
                    array("c.CgcCpfMecena AS cnpjcpfIncentivador",
                          "c.DtChegadaRecibo AS dtLote",
                          "c.DtRecibo AS dtCaptacao",
                          "c.CaptacaoReal AS vlCaptado",
                          "c.NumeroRecibo AS numLote",
                          "TipoApoio" => new Zend_Db_Expr("CASE WHEN c.TipoApoio = 1 then 'Patroc&iacute;nio'
                                                                WHEN c.TipoApoio = 2 then 'Doa&ccedil;&atilde;o' END "),
                          "Incentivador" =>  new Zend_Db_Expr("SAC.dbo.fnNome(a.idagente)"),
                        ),
            "SAC.dbo"
                    );
        $slct->joinInner(
                        array("p"=>"Projetos"),
                        "c.AnoProjeto+c.Sequencial = p.AnoProjeto+p.Sequencial",
                        array("PRONAC" => new Zend_Db_Expr('p.AnoProjeto + p.Sequencial'),
                              "p.IdPRONAC",
                              "p.NomeProjeto",
                              "p.CgcCpf as CGCCPFProponente")
                        );
        $slct->joinInner(
                        array("a"=>"Agentes"),
                        "c.CgcCpfMecena = a.CNPJCPF",
                        array(),
                        "AGENTES.dbo"
                        );
        $slct->joinInner(
            array("a2" => 'Agentes'),
            'p.CgcCpf = a2.CNPJCPF',
            array(),
            'AGENTES.dbo'
                        );

        $slct->joinLeft(
                        array('n' => 'Nomes'),
                        'n.idAgente = a2.idAgente',
                        array('n.Descricao AS NomeProponente'),
                        'AGENTES.dbo'
                        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($slct)->count();
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

    public function buscaReciboCaptacaoTotalValorGrid($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                    array("c"=>$this->_name),
                    array("vlrCaptado" => new Zend_Db_Expr("SUM(c.CaptacaoReal)")),
            "SAC.dbo"
                    );
        $slct->joinInner(
                        array("p"=>"Projetos"),
                        "c.AnoProjeto+c.Sequencial = p.AnoProjeto+p.Sequencial",
                        array()
                );
        $slct->joinInner(
                        array("a"=>"Agentes"),
                        "c.CgcCpfMecena = a.CNPJCPF",
                        array(),
                        "AGENTES.dbo"
                );
        $slct->joinInner(
            array("a2" => 'Agentes'),
            'p.CgcCpf = a2.CNPJCPF',
            array(),
            'AGENTES.dbo'
                );

        $slct->joinLeft(
                        array('n' => 'Nomes'),
                        'n.idAgente = a2.idAgente',
                        array(),
                        'AGENTES.dbo'
                );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchRow($slct);
    }

    public function fnTotalCaptadoProjeto($anoProjeto, $sequencial)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
//        $select->distinct();
        $select->from(
                array($this->_name),
                array(
                    "totalCaptado" => new Zend_Db_Expr(" SAC.dbo.fnTotalCaptadoProjeto('$anoProjeto', '$sequencial')")
                )
        );

        return $this->fetchRow($select);
    }

    public function painelDadosBancariosCaptacao($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('c' => $this->_name),
                array(
                    new Zend_Db_Expr("c.CgcCPfMecena, c.NumeroRecibo, c.DtTransferenciaRecurso, c.DtRecibo, c.CaptacaoReal"),
                    new Zend_Db_Expr("CASE
                                        WHEN c.TipoApoio = '2'
                                             THEN 'Patroc&iacute;nio'
                                             ELSE 'Doa&ccedil;&atilde;o'
                                        END AS TipoApoio"),
                    new Zend_Db_Expr("p.IdPRONAC, i.Nome"),
                    new Zend_Db_Expr("CASE
                                        WHEN p.Mecanismo ='2' or p.Mecanismo ='6'
                                            THEN sac.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                                            ELSE sac.dbo.fnValorAprovado(p.AnoProjeto,p.Sequencial)
                                        END AS ValorAprovado"),
                    new Zend_Db_Expr("SAC.dbo.fnCustoProjeto(p.AnoProjeto,p.Sequencial) as ValorCaptado"),
                    new Zend_Db_Expr("SAC.dbo.fnOutrasFontes(p.idPronac) as OutrasFontes"),
                    'isBemServico',
                )
        );
        $select->joinInner(
            array('p' => 'Projetos'),
            'c.Anoprojeto = p.Anoprojeto and c.Sequencial = p.Sequencial',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('i' => 'Interessado'),
            'c.CgcCPfMecena = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(array('a' => 'agentes'), 'a.CNPJCPf = c.CgcCPfMecena', array('idAgente'), 'Agentes.dbo');

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
}
