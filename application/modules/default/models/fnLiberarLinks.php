<?php

class fnLiberarLinks extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'fnLiberarLinks';

    /**
     * @todo REMOVER A FUN��O liberarLinks
     * @Deprecated Utililizar met�do links com os mesmos parametros
     */
    public function liberarLinks($tipo, $cpfProponente, $idUsuarioLogado, $idPronac)
    {
        $select = new Zend_Db_Expr("SELECT SAC.dbo.fnLiberarLinksIN($tipo,'$cpfProponente',$idUsuarioLogado,$idPronac) as links");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($select);
    }

    /**
     * FUN��O QUE LIBERA LINKS DO MENU LATERAL DE ACORDO COM A FASE DO PROJETO
     * @author Pedro Philipe Alves de Oliveira <pedrophilipe.ti@gmail.com>
     * @param null $Acao
     * @param null $CNPJCPF_Proponente
     * @param null $idUsuario_Logado
     * @param null $idPronac
     * @return object
     */
    public function links($Acao, $CNPJCPF_Proponente, $idUsuario_Logado, $idPronac)
    {
        $Diligencia = 0;
        $Recursos = 0;
        $Readequacao = 0;
        $PercentualCaptado = 0;
        $ComprovacaoFinanceira = 0;
        $RelatorioTrimestral = 0;
        $RelatorioFinal = 0;
        $Analise = 0;
        $Execucao = 0;
        $PrestacaoDeContas = 0;
        $Readequacao_50 = 0;
        $SolicitarProrrogacao = 0;
        $Marcas = 0;
        $ReadequacaoPlanilha = 0;
        $ReadequacaoTransferenciaRecursos = 0;
        $ReadequacaoSaldoAplicacao = 0;

        # Verificar permiss�o
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $permissao = new Zend_Db_Expr("SELECT SAC.dbo.fnVerificarPermissao ($Acao,$CNPJCPF_Proponente,$idUsuario_Logado,$idPronac) AS dado");
        $Permissao= $db->fetchOne($permissao);

        # Pegar o Pronac
        $dadosProjeto = $db->select()
            ->from(
                'projetos',
                array('AnoProjeto', 'Sequencial', 'Situacao', 'DtSituacao', new Zend_Db_Expr('CONVERT(CHAR(8), DtFimExecucao, 112) AS DtFinalExecucao')),
                $this->_schema
            )
            ->where('idPronac = ? ', $idPronac);
        $dadosProjeto = $db->fetchRow($dadosProjeto);

        # Verifica o numero da reuni�o da CNIC do projeto
        $dadosCnic = $db->select()
            ->from(
                array('tbp' => 'tbPauta'),
                null,
                'BdCorporativo.scSAC'
                )
            ->joinInner(
                array('tbr' => 'tbReuniao'),
                'tbp.idNrReuniao = tbr.idNrReuniao',
                array('DtFinal AS DtReuniao'),
                $this->_schema
                )
            ->where('idPronac = ? ', $idPronac);
        $dadosCnic = $db->fetchRow($dadosCnic);

        # Veririca se a conta foi liberada
        $contaLiberada = $db->select()
           ->from(
               'liberacao',
               array('Permissao'),
               $this->_schema
           )
           ->where('AnoProjeto = ?', $dadosProjeto->AnoProjeto)
           ->where('Sequencial = ?', $dadosProjeto->Sequencial);
        $contaLiberada = $db->fetchRow($contaLiberada);

        $contaLiberada = ($contaLiberada)? 'S': 'N';

        # Verifica envio de relat�rio trimestral
        $relatorioTrimestral = new Zend_Db_Expr("SELECT SAC.dbo.fnQtdeRelatorioTrimestral ($idPronac) AS dado");
        $qtAEnviar = $db->fetchRow($relatorioTrimestral);
        $qtAEnviar = ($qtAEnviar->dado) ? $qtAEnviar->dado : 0;

        $relatorioTrimestral = $db->select()
            ->from(
                'tbComprovanteTrimestral',
                   array(
                       new Zend_Db_Expr("COUNT(*)")
                   ),
                   $this->_schema
                );
        $qtEnviados = $db->fetchRow($relatorioTrimestral);

        # Verificar exist�ncia de portaria
        $NrPortaria = $db->select()
           ->from(
               'Aprovacao',
               array('PortariaAprovacao AS NrPortaria'),
               $this->_schema
           )
           ->where('AnoProjeto = ?', $dadosProjeto->AnoProjeto)
           ->where('Sequencial = ?', $dadosProjeto->Sequencial)
           ->where('TipoAprovacao = ?', 1);
        $dadoPortaria = $db->fetchRow($NrPortaria);

        # Verificar Percentual de capta��o
        $PercentualCaptado = new Zend_Db_Expr("SELECT SAC.dbo.fnPercentualCaptado ('$dadosProjeto->AnoProjeto','$dadosProjeto->Sequencial') AS dado");
        $PercentualCaptado = $db->fetchRow($PercentualCaptado);
        

        $PercentualCaptado = ($PercentualCaptado->dado) ? $PercentualCaptado->dado : 0;
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        
        if ($PercentualCaptado > 20) {
            $fnVlAcomprovar = new Zend_Db_Expr("SELECT sac.dbo.fnVlAComprovarProjeto($idPronac) AS vlAComprovar");
            $vlAComprovar = $db->fetchOne($fnVlAcomprovar);
            
            if ($vlAComprovar > 0) {
                $existeReadequacaoEmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento(
                    $idPronac,
                    Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS
                );
                
                $existeReadequacaoEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmEdicao(
                    $idPronac,
                    Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS
                );
                
                if (!$existeReadequacaoEmAndamento
                    || $existeReadequacaoEmEdicao
                ) {
                    $TbSolicitacaoTransferenciaRecursos = new Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos();
                    $projetosRecebedores = $TbSolicitacaoTransferenciaRecursos->obterProjetosRecebedores(
                        '',
                        $idPronac
                    );
                    if (count($projetosRecebedores) > 0) {
                        $ReadequacaoTransferenciaRecursos = 1;
                    } 
                }
            }            
        }
        // Future Flag
        $ReadequacaoTransferenciaRecursos = 0;
        
        # Verificar se h� dilig�ncia para responder
        $vDiligencia = $db->select()
           ->from(
               'tbDiligencia',
               array('idDiligencia'),
               $this->_schema
           )
           ->where('stEstado = ?', 0)
           ->where(new Zend_Db_Expr('((DtResposta IS NULL AND stEnviado = \'S\') OR (DtResposta IS not NULL AND stEnviado = \'N\'))'))
           ->where('idPronac = ?', $idPronac);
        $vDiligencia = $db->fetchRow($vDiligencia);

        // @TODO VERIFICAR ESSE CARA
        $Diligencia = ($vDiligencia->idDiligencia) ? 1 : 0;

        //Verificar se h� recurso @TODO FAZER ESSA PARTE E DEIXAR PRO FIM
        $data = new Zend_Db_Expr("SELECT DATEDIFF(DAY, '$dadosCnic->DtReuniao', GETDATE()) AS dado");
        $data = $db->fetchOne($data);

//        $situacoesRecurso = Recurso_Model_TbRecurso::obterSituacoesPassiveisDeRecursoFase2();
//
//        $recurso1 = $db->select()
//           ->from(
//               'tbRecurso',
//               array(new Zend_Db_Expr('TOP 1 idRecurso')),
//               $this->_schema
//           )
//           ->where('stEstado = ?', 1)
//           ->where('siFaseProjeto = ?', 2)
//           ->where('siRecurso = ?', 0)
//           ->where('idPronac = ?', $idPronac);
//        $recurso1 = $db->fetchRow($recurso1);
//
//        $recurso2 = $db->select()
//           ->from(
//               'tbRecurso',
//               array(new Zend_Db_Expr('TOP 1 idRecurso')),
//               $this->_schema
//           )
//           ->where('stEstado = ?', 0)
//           ->where('siRecurso <> ?', 0)
//           ->where('idPronac = ?', $idPronac);
//        $recurso2 = $db->fetchRow($recurso2);
//
//        $recurso3 = $db->select()
//            ->from(
//                array('a' => 'tbRecurso'),
//                array('idRecurso'),
//                $this->_schema
//            )
//            ->joinInner(
//                array('b' => 'tbReuniao'),
//                'a.idNrReuniao = b.idNrReuniao',
//                array('DtFinal'),
//                $this->_schema
//            )
//            ->where('a.tpRecurso = ?', 1)
//            ->where('a.siRecurso <> ?', 0)
//            ->where('a.stEstado = ?', 1)
//            ->where('a.idPronac = ?', $idPronac);
//        $recurso3 = $db->fetchRow($recurso3);
//
//        $Recurso4 = new Zend_Db_Expr("SELECT (DATEDIFF(DAY,(
//                                            SELECT dtFinal FROM sac.dbo.TBRecurso a
//                                            INNER JOIN tbReuniao b on (a.idNrReuniao = b.idNrReuniao)
//                                            WHERE a.tpRecurso = 1 AND a.siRecurso <> 0 AND a.stEstado = 1 AND a.idPronac = $idPronac),GETDATE())
//                                ) AS dado");
//        $Recurso4 = $db->fetchRow($Recurso4);
//
//        # recurso finalizado
//        $recursoAdmissibilidade = $db->select()
//           ->from(
//               'tbRecurso',
//               array(new Zend_Db_Expr('idRecurso')),
//               $this->_schema
//           )
//           ->where('siFaseProjeto = ?', 1)
//           ->where('idPronac = ?', $idPronac)->limit(1);
//
//        $recursoAdmissibilidade = $db->fetchRow($recursoAdmissibilidade);
//
//        $recursoIndeferido = $db->select()
//           ->from(
//               'tbRecurso',
//               array(new Zend_Db_Expr('idRecurso')),
//               $this->_schema
//           )
//           ->where('siFaseProjeto = ?', 1)
//           ->where('tpRecurso IN (?)', array(2))
//           ->where('idPronac = ?', $idPronac);
//        $recursoIndeferido = $db->fetchAll($recursoIndeferido);
//
//        $recursoFinalizado = $db->select()
//           ->from(
//               'tbRecurso',
//               array(new Zend_Db_Expr('idRecurso')),
//               $this->_schema
//           )
//           ->where('siFaseProjeto = ?', 1)
//           ->where('siRecurso = ?', 15)
//           ->where('stEstado = ?', 1)
//           //->where('stAtendimento = ?', 'I')
//           ->where('idPronac = ?', $idPronac)->limit(1);
//
//        $recursoFinalizado = $db->fetchRow($recursoFinalizado);
//
//        if (empty($Recurso4->dado)) {
//            $Recurso4->dado = 90;
//        }
//
//        $diasProjeto = new Zend_Db_Expr("SELECT DATEDIFF(DAY,'$dadosProjeto->DtSituacao',GETDATE()) as dias");
//        $diasProjeto = $db->fetchRow($diasProjeto);
//
//        if($diasProjeto->dias <=10 and $dadosProjeto->Situacao == 'D51'){
//            $Recursos = 1;
//        }else if ((($data <= 11 and in_array($dadosProjeto->Situacao, $situacoesRecurso) and !$recurso1->idRecurso and !$recurso2->idRecurso)
//            or
//            !$recurso3->idRecurso and !in_array($dadosProjeto->Situacao, $situacoesRecurso) and $Recurso4->dado <=10)
//            or
//            (
//                $dadosProjeto->Situacao != 'B03' and
//                empty($recursoAdmissibilidade) and
//                ($diasProjeto->dias <= 11 && $dadosProjeto->Situacao == 'B02')
//                or ($dadosProjeto->Situacao != 'B03' and $recursoFinalizado and empty($recursoIndeferido))
//            )
//        ) {
//            $Recursos = 1;
//        }

        $tbRecursoMapper = new Recurso_Model_TbRecursoMapper();
        $Recursos = ($tbRecursoMapper->obterProjetoPassivelDeRecurso($idPronac)) ? 1 : 0;
        //var_dump($dadosProjeto->Situacao, $diasProjeto->dias, $Recursos, $recursoAdmissibilidade, $recursoFinalizado);die;

        /* ===== IDENTIFICAR FRASES DO PROJETO =====  */

        # FASE 2 - DA TRANSFORMA��O DA PROPOSTA EM PROJETO AT� O ENCERRAMENTO DA CNIC
        $validacaoFase2 = $db->select()
            ->from(
                array('a' => 'tbPauta'),
                array('idPRONAC'),
                'BDCorporativo.scsac'
            )
            ->joinInner(
                array('b'=> 'tbReuniao'),
                'a.idNrReuniao = b.idNrReuniao',
                array(''),
                $this->_schema
            )
            ->where('b.stEstado = ?', 0)
            ->where('a.idPronac = ?', $idPronac);
        $validacaoFase2 = $db->fetchRow($validacaoFase2);
        $situacoesFase2 = array('B11', 'B14', 'C10', 'C20', 'C30', 'D20');

        if ((!$dadoPortaria->NrPortaria or $dadoPortaria->NrPortaria == '') and !in_array($dadosProjeto->Situacao, $situacoesFase2) and !$validacaoFase2->idPRONAC) {
            $Fase = 2;
            $Analise = 1;
        } else {
            $Fase = 2;
            $Analise = 0;
        }

        # FASE 3 - DA LIBERA��O DA PUBLICA��O DA PORTARIA DE APROVA��O AT� A LIBERA��O DA CONTA
        if ($dadoPortaria->NrPortaria or $dadoPortaria->NrPortaria != '' and $contaLiberada == 'N') {
            $Fase = 3;
            $Analise = 1;
            $Execucao = 1;
            $PrestacaoDeContas = 0;
            $SolicitarProrrogacao = 1;
            $Marcas = 1;
        }


        # FASE 4 - DA LIBERA��O DA CONTA AT� A DATA FINAL DO PER�ODO DE EXECUCAO
        $sqlDataAtualBanco = new Zend_Db_Expr('SELECT CONVERT( CHAR(8), GETDATE(), 112)');
        $dataAtualBanco = $db->fetchOne($sqlDataAtualBanco);

        if ($contaLiberada == 'S' and $dadosProjeto->DtFinalExecucao >= $dataAtualBanco) {
            $Analise = 1;
            $Execucao = 1;
            $PrestacaoDeContas = 1;
            $ComprovacaoFinanceira = 1;
            $RelatorioTrimestral = 1;
            $Readequacao = 1;
            $SolicitarProrrogacao = 1;
            $Marcas = 1;

            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $existeReadequacaoEmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento($idPronac);

            if ($existeReadequacaoEmAndamento) {
                $readequacaoAndamento = $Readequacao_Model_DbTable_TbReadequacao->obterReadequacaoOrcamentariaEmAndamento($idPronac);
                $existeReadequacaoEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmEdicao($idPronac, $readequacaoAndamento['idTipoReadequacao']);


                if ($existeReadequacaoEmEdicao) {
                    switch ($readequacaoAndamento['idTipoReadequacao']) {
                    case Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL:
                        $Readequacao_50 = 1;
                        break;
                    case Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA:
                        $ReadequacaoPlanilha = 1;
                        break;
                    case Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO:
                        $ReadequacaoSaldoAplicacao = 1;
                        break;
                    }
                } else {
                    $Readequacao_50 = 0;
                    $ReadequacaoPlanilha = 0;
                    $ReadequacaoSaldoAplicacao = 0;
                }
                
            } else {
                $Readequacao_50 = 1;
                $ReadequacaoPlanilha = 1;
                if ($PercentualCaptado >= 100) {
                    $ReadequacaoSaldoAplicacao = 1;
                }
            }
            
            $tbCumprimentoObjeto = new tbCumprimentoObjeto();
            $possuiRelatorioDeCumprimento = $tbCumprimentoObjeto->possuiRelatorioDeCumprimento($idPronac);

            if ($possuiRelatorioDeCumprimento) {
                $Readequacao_50 = 0;
                $Readequacao = 0;
                $ComprovacaoFinanceira = 0;
                $RelatorioTrimestral = 0;
                $RelatorioFinal = 0;
            } else {

                /* ===== CHECAR SE EXISTE RELAT�RIO TRIMESTRAL PARA SER ENVIADO =====*/
                if (($qtAEnviar - $qtEnviados) == 0) {
                    $RelatorioTrimestral = 0;
                    $RelatorioFinal = 1;
                } else {
                    $RelatorioTrimestral = 1;
                    $RelatorioFinal = 0;
                }
            }

            $Fase = 4;
        }

        # FASE 5 - PRESTA��O DE CONTAS DO PROPONENTE - RELAT�RIO DE CUMPRIMENTO DO OBJETO
        if ($contaLiberada == 'S' and $dataAtualBanco > $dadosProjeto->DtFinalExecucao) {
            $Analise = 1;
            $Execucao = 1;
            $PrestacaoDeContas = 1;
            $Marcas = 0;
            $SolicitarProrrogacao = 0;
            $Readequacao = 1;
            $Readequacao_50 = 1;
            $ComprovacaoFinanceira = 1;
            $RelatorioTrimestral = 0;
            $RelatorioFinal = 1;

            /* ===== EXCE��O PARA AJUSTAR PLANILHA PARA PRESTAR CONTAS ===== */

            $situacoesPlanilha = array('E13', 'E15', 'E23', 'E74', 'E75');
            if (in_array($dadosProjeto->Situacao, $situacoesPlanilha)) {
                $Readequacao = 1;
            }

            $tbCumprimentoObjeto = new tbCumprimentoObjeto();
            $possuiRelatorioDeCumprimento = $tbCumprimentoObjeto->possuiRelatorioDeCumprimento($idPronac);

            if ($possuiRelatorioDeCumprimento) {
                $ComprovacaoFinanceira = 0;
                $Readequacao_50 = 0;
                $RelatorioFinal = 0;
                if ($Diligencia) {
                    $ComprovacaoFinanceira = 1;
                }
            } else {
                /* ===== CHECAR SE EXISTE READEQUA�AO DE 50% ===== */

                // VERIFICAR O QUE ESTÁ CHECANDO AQUI. É PRA LIBERAR A DE 50%
                $readequacaoFase5 = $db->select()
                    ->from(
                        array('a' => 'tbReadequacao'),
                        array(new Zend_Db_Expr('TOP 1 a.idTipoReadequacao')),
                        $this->_schema
                    )
                    ->joinInner(
                        array('b' => 'tbTipoReadequacao'),
                        'a.idTipoReadequacao = b.idTipoReadequacao',
                        array(''),
                        $this->_schema
                    )
                    ->where('a.idPronac = ?', $idPronac)
                                  ->where('b.idTipoReadequacao = ?', Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA)
                                  ->where(
                                      'a.siEncaminhamento NOT IN (?)',
                                      array(
                                          Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_INDEFERIDA,
                                          Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_FINALIZADA_SEM_PORTARIA
                                      )
                                  )
                    ->where('a.stEstado = ?', 0);
                $readequacaoFase5 = $db->fetchRow($readequacaoFase5);

                if ($readequacaoFase5->idTipoReadequacao) {
                    $Readequacao_50 = 0;
                } else {
                    $Readequacao_50 = 1;
                }
            }

            if (!$existeReadequacaoEmAndamento && !$existeReadequacaoEmAndamento) {
                $Readequacao_50 = 1;
            } else if ($existeReadequacaoEmAndamento && !$existeReadequacaoPlanilhaEmEdicao)  {
                $Readequacao_50 = 0;
            }

            // caso projeto tenha diligencia de prestacao de contas não mostrar menu readequacão
            if ($dadosProjeto->Situacao == 'E17' || $dadosProjeto->Situacao == 'E30') {
                $Readequacao = 0;
                $Readequacao_50 = 0;
            }

            $Fase = 5;
        }

        //Fases que não pode liberar menu Análise Técnica
        if (in_array($dadosProjeto->Situacao, array('B11', 'B14', 'C10', 'C30', 'C20', 'D50', 'D51'))) {
            $Analise = 0;
        }else{
            $Analise = 1;
        }
        
        $permissao = array('links'=>"$Permissao - $Fase - $Diligencia - $Recursos - $Readequacao - $ComprovacaoFinanceira - $RelatorioTrimestral - $RelatorioFinal - $Analise - $Execucao - $PrestacaoDeContas - $Readequacao_50 - $Marcas - $SolicitarProrrogacao - $ReadequacaoPlanilha - $ReadequacaoTransferenciaRecursos - $ReadequacaoSaldoAplicacao");

        return (object) $permissao;
    }
}
