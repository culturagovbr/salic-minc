<?php

class Readequacao_SaldoAplicacaoController extends Readequacao_GenericController 
{
    public function init()
    {
        parent::init();
        
        $idPronac = $this->_request->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->idPronac = $idPronac;
        $this->view->idTipoReadequacao = Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO;
        $this->view->idPronac = $idPronac;
        $this->view->idPerfil = $this->idPerfil;
    }

    public function indexAction()
    {
        $this->view->projeto = $this->projeto;
    }

    public function solicitarUsoSaldoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        $dados = $this->getRequest()->getPost();
        $idPronac = $dados['idPronac'];
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        
        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        
        $idReadequacao = $tbReadequacao->criarReadequacaoPlanilha($idPronac, Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO);

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $readequacao = $tbReadequacao->obterDadosReadequacao(
            Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO,
            $idPronac,
            $idReadequacao
        );
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $verificarPlanilhaReadequadaAtual = $tbPlanilhaAprovacao->buscarPlanilhaReadequadaEmEdicao($idPronac, $idReadequacao);
        
        if (count($verificarPlanilhaReadequadaAtual) == 0) {
            $planilhaAtiva = $tbPlanilhaAprovacao->buscarPlanilhaAtiva($idPronac);
            $criarPlanilha = $tbPlanilhaAprovacao->copiarPlanilhas($idPronac, $idReadequacao);
            
            if ($criarPlanilha) {
                $this->_helper->json(array(
                    'msg' => 'Planilha copiada corretamente',
                    'success' => 'true',
                    'readequacao' => $readequacao
                ));
            } else {
                $this->_helper->json(array(
                    'msg' => 'Houve um erro ao tentar copiar a planilha',
                    'success' => 'false',
                    'readequacao' => $readequacao
                ));
            }
        }
    }

    public function carregarUnidadesAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        try {
            $TbPlanilhaUnidade = new Proposta_Model_DbTable_TbPlanilhaUnidade();
            $unidades = $TbPlanilhaUnidade->buscarUnidade();
            
            $unidadesOut = [];
            foreach ($unidades as $unidade) {
                $unidadeObj = new StdClass();
                $unidadeObj->idUnidade = $unidade->idUnidade;
                $unidadeObj->Sigla = utf8_encode($unidade->Sigla);
                $unidadeObj->Descricao = utf8_encode($unidade->Descricao);
                $unidadesOut[] = $unidadeObj;
            }
            
            $this->_helper->json(array(
                'msg' => 'Tabela de unidades',
                'success' => 'true',
                'unidades' => $unidadesOut
            ));
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json([
                'success' => 'false',
                'msg' => $e->getMessage()
            ]);
        }
    }   
    
    public function carregarValorEntrePlanilhasAction()
    {
        $auth = Zend_Auth::getInstance();
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam("idPronac");
        
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        
        try {
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            
            $valorEntrePlanilhas = $tbReadequacao->carregarValorEntrePlanilhas(
                $idPronac,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO
            );
            $valorEntrePlanilhas['vlDiferencaPlanilhas'] = 'R$ '. number_format(
                ($valorEntrePlanilhas['PlanilhaReadequadaTotal'] - $valorEntrePlanilhas['PlanilhaAtivaTotal']
                ),
                2,
                ',',
                '.'
            );
                                                         
            $this->_helper->json([
                'valorEntrePlanilhas' => $valorEntrePlanilhas,
                'success' => 'true',
                'msg' => 'Readequa&ccedil;&atilde;o salva com sucesso!'
            ]);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json([
                'data' => $valorEntrePlanilhas,
                'success' => 'false',
                'msg' => $e->getMessage()
            ]);
        }
    }
    
    public function salvarReadequacaoAction()
    {
        $dados = $this->getRequest()->getPost();
        
        try {
            
            if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Acesso negado!");
            }
            
            $dados['idTipoReadequacao'] = Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO;
            $dados['stAtendimento'] = 'D';
            $dados['idDocumento'] = null;
            $dados['dsJustificativa'] = $dados['justificativa'];
            
            $readequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
            $id = $readequacaoMapper->salvarSolicitacaoReadequacao($dados);
            
            $this->_helper->json([
                'data' => $dados,
                'success' => 'true',
                'msg' => 'Readequa&ccedil;&atilde;o salva com sucesso!'
            ]);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json(array('data' => $dados, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    public function finalizarReadequacaoAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }
        
        $params = $this->getRequest()->getParams();
        
        try {
            if (empty($params['idReadequacao'])) {
                throw new Exception('Readequa&ccedil;&atilde;o n&atilde;o encontrada');
            }

            if (strlen($params['idPronac']) > 7) {
                $params['idPronac'] = Seguranca::dencrypt($params['idPronac']);
            }

            
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $readequacao = $tbReadequacao->obterDadosReadequacao(
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO,
                $params['idPronac'],
                $params['idReadequacao']
            );

            $tbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
            $status = $tbReadequacaoMapper->finalizarSolicitacaoReadequacao(
                $this->idPronac,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO,
                $params['idReadequacao']
            );
            $this->_helper->json([
                'data' => $status,
                'success' => 'true',
                'msg' => 'Readequa&ccedil;&atilde;o finalizada com sucesso!'
            ]);
        } catch (Exception $e) {
            parent::message($e->getMessage(), "readequacao/readequacoes?idPronac=".Seguranca::encrypt($idPronac), "ERROR");
        }
    }

    public function excluirReadequacaoAction()
    {
        $this->_helper->layout->disableLayout();
        
        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }
        
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        
        $idReadequacao = $this->_request->getParam('idReadequacao');

        try {
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $dados = $tbReadequacao->buscar(['idReadequacao =?'=>$idReadequacao])->current();
            
            if (!empty($dados->idDocumento)) {
                $tbDocumento = new tbDocumento();
                $tbDocumento->excluirDocumento($dados->idDocumento);
            }
            
            $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
            $tbPlanilhaAprovacao->delete([
                'IdPRONAC = ?'=>$idPronac,
                'tpPlanilha = ?'=>'SR',
                'idReadequacao = ?'=>$idReadequacao
            ]);
            
            $exclusao = $tbReadequacao->delete([
                'idPronac =?'=> $idPronac,
                'idReadequacao =?'=>
                $idReadequacao
            ]);
            
            $this->_helper->json([
                'success' => 'true',
                'msg' => 'Readequa&ccedil;&atilde;o exclu&iacute;da com sucesso!'
            ]);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json([
                'data' => $dados,
                'success' => 'false',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function disponivelEdicaoReadequacaoPlanilhaAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        
        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            $this->_helper->json([
                'success' => 'true',
                'disponivelParaEdicaoReadequacaoPlanilha' => false,
                'msg' => 'N&atilde;o dispon&iacute;vel para edi&ccedil;&atilde;o de itens.'
            ]);
        }
        
        try {
            $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $disponivelParaEdicaoReadequacaoPlanilha = $Readequacao_Model_DbTable_TbReadequacao->disponivelParaEdicaoReadequacaoPlanilha($idPronac);
            
            $this->_helper->json([
                'success' => true,
                'disponivelParaEdicaoReadequacaoPlanilha' => $disponivelParaEdicaoReadequacaoPlanilha,
                'msg' => 'Dispon&iacute;vel para edi&ccedil;&atilde;o de itens.'
            ]);
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json([
                'success' => 'false',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /*
     * função copiada de Readequacao_ReadequacoesController->alterarItemSolicitacaoAction()
     * - removendo formatação numérica, prefixo e retornando float
     *
     */
    public function obterItemSolicitacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $idPlanilhaAprovacao = $this->_request->getParam("idPlanilha");
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        
        /* DADOS DO ITEM ATIVO */
        $itemTipoPlanilha = $tbPlanilhaAprovacao->buscar(array(
            'idPlanilhaAprovacao = ?' => $idPlanilhaAprovacao
        ))->current();
        $where = array();
        $where['idPlanilhaAprovacao = ?'] = $idPlanilhaAprovacao;
        $idPlan = $idPlanilhaAprovacao;
        if ($itemTipoPlanilha->tpPlanilha == 'SR') {
            $where['idPlanilhaAprovacao = ?'] = !empty($itemTipoPlanilha->idPlanilhaAprovacaoPai) ? $itemTipoPlanilha->idPlanilhaAprovacaoPai : $itemTipoPlanilha->idPlanilhaAprovacao;
            $idPlan = !empty($itemTipoPlanilha->idPlanilhaAprovacaoPai) ? $itemTipoPlanilha->idPlanilhaAprovacaoPai : $itemTipoPlanilha->idPlanilhaAprovacao;
        }
        $where['stAtivo = ?'] = 'S';
        $planilhaAtiva = $tbPlanilhaAprovacao->buscarDadosAvaliacaoDeItemRemanejamento($where);

        /* DADOS DO ITEM PARA EDICAO DA READEQUACAO */
        $where = array();
        $where['idPlanilhaAprovacao = ?'] = $idPlanilhaAprovacao;
        $where['tpPlanilha = ?'] = 'SR';
        $where['stAtivo = ?'] = 'N';
        $planilhaEditaval = $tbPlanilhaAprovacao->buscarDadosAvaliacaoDeItemRemanejamento($where);

        $dadosPlanilhaAtiva = array();

        /* PROJETO */
        $Projetos = new Projetos();
        $projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $dadosProjeto = array(
            'IdPRONAC' => $projeto->IdPRONAC,
            'PRONAC' => $projeto->AnoProjeto . $projeto->Sequencial,
            'NomeProjeto' => utf8_encode($projeto->NomeProjeto)
        );

        foreach ($planilhaAtiva as $registro) {
            //CALCULAR VALORES MINIMO E MAXIMO PARA VALIDACAO
            $dadosPlanilhaAtiva['idPlanilhaAprovacao'] = $registro['idPlanilhaAprovacao'];
            $dadosPlanilhaAtiva['idProduto'] = $registro['idProduto'];
            $dadosPlanilhaAtiva['descProduto'] = utf8_encode($registro['descProduto']);
            $dadosPlanilhaAtiva['idEtapa'] = $registro['idEtapa'];
            $dadosPlanilhaAtiva['descEtapa'] = utf8_encode($registro['descEtapa']);
            $dadosPlanilhaAtiva['idPlanilhaItem'] = $registro['idPlanilhaItem'];
            $dadosPlanilhaAtiva['descItem'] = utf8_encode($registro['descItem']);
            $dadosPlanilhaAtiva['idUnidade'] = $registro['idUnidade'];
            $dadosPlanilhaAtiva['descUnidade'] = utf8_encode($registro['descUnidade']);
            $dadosPlanilhaAtiva['Quantidade'] = $registro['Quantidade'];
            $dadosPlanilhaAtiva['Ocorrencia'] = $registro['Ocorrencia'];
            $dadosPlanilhaAtiva['ValorUnitario'] = utf8_encode(number_format($registro['ValorUnitario'], 2, '.', ''));
            $dadosPlanilhaAtiva['QtdeDias'] = $registro['QtdeDias'];
            $dadosPlanilhaAtiva['TotalSolicitado'] = utf8_encode(number_format(($registro['Quantidade'] * $registro['Ocorrencia'] * $registro['ValorUnitario']), 2, '.', ''));
            $dadosPlanilhaAtiva['Justificativa'] = utf8_encode($registro['Justificativa']);
        }

        $dadosPlanilhaEditavel = $dadosPlanilhaAtiva;
        if (count($planilhaEditaval) > 0) {
            foreach ($planilhaEditaval as $registroEditavel) {
                $dadosPlanilhaEditavel['idPlanilhaAprovacao'] = $registroEditavel['idPlanilhaAprovacao'];
                $dadosPlanilhaEditavel['idProduto'] = $registroEditavel['idProduto'];
                $dadosPlanilhaEditavel['descProduto'] = utf8_encode($registroEditavel['descProduto']);
                $dadosPlanilhaEditavel['idEtapa'] = $registroEditavel['idEtapa'];
                $dadosPlanilhaEditavel['descEtapa'] = utf8_encode($registroEditavel['descEtapa']);
                $dadosPlanilhaEditavel['idPlanilhaItem'] = $registroEditavel['idPlanilhaItem'];
                $dadosPlanilhaEditavel['descItem'] = utf8_encode($registroEditavel['descItem']);
                $dadosPlanilhaEditavel['idUnidade'] = $registroEditavel['idUnidade'];
                $dadosPlanilhaEditavel['descUnidade'] = utf8_encode($registroEditavel['descUnidade']);
                $dadosPlanilhaEditavel['Quantidade'] = $registroEditavel['Quantidade'];
                $dadosPlanilhaEditavel['Ocorrencia'] = $registroEditavel['Ocorrencia'];
                $dadosPlanilhaEditavel['ValorUnitario'] = utf8_encode(number_format($registroEditavel['ValorUnitario'], 2, '.', ''));
                $dadosPlanilhaEditavel['QtdeDias'] = $registroEditavel['QtdeDias'];
                $dadosPlanilhaEditavel['TotalSolicitado'] = utf8_encode(number_format(($registroEditavel['Quantidade'] * $registroEditavel['Ocorrencia'] * $registroEditavel['ValorUnitario']), 2, '.', ''));
                $dadosPlanilhaEditavel['Justificativa'] = utf8_encode($registroEditavel['Justificativa']);
                $dadosPlanilhaEditavel['idAgente'] = $registroEditavel['idAgente'];
            }
        }
        if (count($planilhaEditaval) > 0 && count($planilhaAtiva) == 0) {
            $dadosPlanilhaAtiva = $dadosPlanilhaEditavel;
        }

        $tbCompPagxPlanAprov = new tbComprovantePagamentoxPlanilhaAprovacao();
        $res = $tbCompPagxPlanAprov->buscarValorComprovadoDoItem($idPlan);
        $valoresDoItem = array(
            'vlComprovadoDoItem' => utf8_encode(number_format($res->vlComprovado, 2, '.', '')),
            'vlComprovadoDoItemValidacao' => utf8_encode(number_format($res->vlComprovado, 2, '.', ''))
        );

        $this->_helper->json(array('resposta' => true, 'dadosPlanilhaAtiva' => $dadosPlanilhaAtiva, 'dadosPlanilhaEditavel' => $dadosPlanilhaEditavel, 'valoresDoItem' => $valoresDoItem, 'dadosProjeto' => $dadosProjeto));
        $this->_helper->viewRenderer->setNoRender(true);        
        
    }    
}
