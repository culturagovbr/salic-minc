<?php

class Readequacao_ReadequacoesController extends Readequacao_GenericController
{
    private $intTamPag = 10;

    public function init()
    {
        parent::init();
    }

    /**
     * Tela de cadastro de readequações - visão do proponente
     * @require idPronac
     */
    public function indexAction()
    {
        $this->view->idPerfil = $this->idPerfil;
        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            parent::message(
                "Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!",
                "principal",
                "ALERT"
            );
        }

        $idUF = $this->_request->getParam('iduf');
        $idEtapa = $this->_request->getParam('idEtapa');
        $idProduto = $this->_request->getParam('idProduto');
        $idMunicipio = $this->_request->getParam('idMunicipio');

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        if ($idUF) {
            $this->carregarListaMunicipios($idUF);
        }

        if ($idEtapa && $idProduto) {
            $this->carregarEtapaProduto($idEtapa, $idProduto, $idPronac, $idMunicipio);
        }


        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();

            $tbVerificao = new Proposta_Model_DbTable_Verificacao();
            $buscarRecurso = $tbVerificao->buscarFonteRecurso();

            $this->view->Recursos = $buscarRecurso;

            $uf = new Agente_Model_DbTable_UF();
            $buscarEstado = $uf->buscar();
            $this->view->UFs = $buscarEstado;

//            $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
//            $this->view->Produtos = $PlanoDistribuicaoProduto->comboProdutosParaInclusaoReadequacao($idPronac);

            $tbPlanilhaEtapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();
            $this->view->Etapas = $tbPlanilhaEtapa->buscar(array('stEstado = ?' => 1));

            $TbPlanilhaUnidade = new Proposta_Model_DbTable_TbPlanilhaUnidade();
            $buscarUnidade = $TbPlanilhaUnidade->buscarUnidade();
            $this->view->Unidade = $buscarUnidade;

            $tbTipoReadequacao = new Readequacao_Model_DbTable_TbTipoReadequacao();
            $this->view->TiposReadequacao = $tbTipoReadequacao->buscarTiposReadequacoesPermitidos($idPronac);

            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $this->view->readequacoesCadastradas = $tbReadequacao->readequacoesCadastradasProponente(
                array(
                    'a.idPronac = ?' => $idPronac,
                    'a.siEncaminhamento IN (?)' => [
                        Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
                        Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_NAO_ENVIA_MINC
                    ],
                    'a.stEstado = ?' => 0,
                    'b.stEstado = ?' => Readequacao_Model_TbTipoReadequacao::TIPO_READEQUACAO_ATIVO
                ),
                array(1)
            );
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "principalproponente", "ERROR");
        }
    }

    public function gerenciarAssinaturasAction()
    {
        $origin = $this->_request->getParam('origin');
        if ($origin != '') {
            $this->redirect($origin);
        } else {
            $this->redirect("/readequacao/readequacoes/painel?tipoFiltro=analisados");
        }
    }
    
    /**
     * Método privado para carregar lista de cidades
     *
     */
    private function carregarListaMunicipios($idUF)
    {
        $this->_helper->layout->disableLayout();

        $mun = new Agente_Model_DbTable_Municipios();
        $cidade = $mun->listar($idUF);
        $a = 0;
        $cidadeArray = array();
        foreach ($cidade as $DadosCidade) {
            $cidadeArray[$a]['idCidade'] = $DadosCidade->id;
            $cidadeArray[$a]['nomeCidade'] = utf8_encode($DadosCidade->Descricao);
            $a++;
        }
        $this->_helper->json($cidadeArray);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function carregarEtapaProduto($idEtapa, $idProduto, $idPronac = null, $idMunicipio = null)
    {
        $this->_helper->layout->disableLayout();

        $tbItensPlanilhaProduto = new tbItensPlanilhaProduto();
        $itens = $tbItensPlanilhaProduto->itensPorItemEEtapaReadequacao($idEtapa, $idProduto);

        $a = 0;
        if ($idPronac && $idMunicipio) {
            $itensAtuais = $tbItensPlanilhaProduto->itensPorProdutoItemEtapaMunicipioReadequacao(
                $idEtapa,
                $idProduto,
                $idMunicipio,
                $idPronac
            );

            $itensArray = array();

            foreach ($itens as $item) {
                $excluir = false;
                foreach ($itensAtuais as $atuais) {
                    if ($item->idPlanilhaItens == $atuais->idPlanilhaItens) {
                        $excluir = true;
                    }
                }

                if (!$excluir) {
                    $itensArray[$a]['idPlanilhaItens'] = $item->idPlanilhaItens;
                    $itensArray[$a]['Item'] = utf8_encode($item->Item);
                    $a++;
                }
            }

        } else {
            foreach ($itens as $item) {
                $itensArray[$a]['idPlanilhaItens'] = $item->idPlanilhaItens;
                $itensArray[$a]['Item'] = utf8_encode($item->Item);
                $a++;
            }
        }

        $this->_helper->json($itensArray);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    
    /**
     * Essa função é acessada para alterar o item da planilha orçamentária.
     */
    public function alterarItemSolicitacaoAction()
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
            $dadosPlanilhaAtiva['nrFonteRecurso'] = $registro['nrFonteRecurso'];
            $dadosPlanilhaAtiva['descProduto'] = utf8_encode($registro['descProduto']);
            $dadosPlanilhaAtiva['idEtapa'] = $registro['idEtapa'];
            $dadosPlanilhaAtiva['descEtapa'] = utf8_encode($registro['descEtapa']);
            $dadosPlanilhaAtiva['idPlanilhaItem'] = $registro['idPlanilhaItem'];
            $dadosPlanilhaAtiva['descItem'] = utf8_encode($registro['descItem']);
            $dadosPlanilhaAtiva['idUnidade'] = $registro['idUnidade'];
            $dadosPlanilhaAtiva['descUnidade'] = utf8_encode($registro['descUnidade']);
            $dadosPlanilhaAtiva['Quantidade'] = $registro['Quantidade'];
            $dadosPlanilhaAtiva['Ocorrencia'] = $registro['Ocorrencia'];
            $dadosPlanilhaAtiva['ValorUnitario'] = utf8_encode('R$ ' . number_format($registro['ValorUnitario'], 2, ',', '.'));
            $dadosPlanilhaAtiva['QtdeDias'] = $registro['QtdeDias'];
            $dadosPlanilhaAtiva['TotalSolicitado'] = utf8_encode('R$ ' . number_format(($registro['Quantidade'] * $registro['Ocorrencia'] * $registro['ValorUnitario']), 2, ',', '.'));
            $dadosPlanilhaAtiva['Justificativa'] = utf8_encode($registro['Justificativa']);
        }

        $dadosPlanilhaEditavel = $dadosPlanilhaAtiva;
        if (count($planilhaEditaval) > 0) {
            foreach ($planilhaEditaval as $registroEditavel) {
                $dadosPlanilhaEditavel['idPlanilhaAprovacao'] = $registroEditavel['idPlanilhaAprovacao'];
                $dadosPlanilhaEditavel['idProduto'] = $registroEditavel['idProduto'];
                $dadosPlanilhaEditavel['nrFonteRecurso'] = $registroEditavel['nrFonteRecurso'];
                $dadosPlanilhaEditavel['descProduto'] = utf8_encode($registroEditavel['descProduto']);
                $dadosPlanilhaEditavel['idEtapa'] = $registroEditavel['idEtapa'];
                $dadosPlanilhaEditavel['descEtapa'] = utf8_encode($registroEditavel['descEtapa']);
                $dadosPlanilhaEditavel['idPlanilhaItem'] = $registroEditavel['idPlanilhaItem'];
                $dadosPlanilhaEditavel['descItem'] = utf8_encode($registroEditavel['descItem']);
                $dadosPlanilhaEditavel['idUnidade'] = $registroEditavel['idUnidade'];
                $dadosPlanilhaEditavel['descUnidade'] = utf8_encode($registroEditavel['descUnidade']);
                $dadosPlanilhaEditavel['Quantidade'] = $registroEditavel['Quantidade'];
                $dadosPlanilhaEditavel['Ocorrencia'] = $registroEditavel['Ocorrencia'];
                $dadosPlanilhaEditavel['ValorUnitario'] = utf8_encode('R$ ' . number_format($registroEditavel['ValorUnitario'], 2, ',', '.'));
                $dadosPlanilhaEditavel['QtdeDias'] = $registroEditavel['QtdeDias'];
                $dadosPlanilhaEditavel['TotalSolicitado'] = utf8_encode('R$ ' . number_format(($registroEditavel['Quantidade'] * $registroEditavel['Ocorrencia'] * $registroEditavel['ValorUnitario']), 2, ',', '.'));
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
            'vlComprovadoDoItem' => utf8_encode('R$ ' . number_format($res->vlComprovado, 2, ',', '.')),
            'vlComprovadoDoItemValidacao' => utf8_encode(number_format($res->vlComprovado, 2, '', ''))
        );

        $this->_helper->json(array('resposta' => true, 'dadosPlanilhaAtiva' => $dadosPlanilhaAtiva, 'dadosPlanilhaEditavel' => $dadosPlanilhaEditavel, 'valoresDoItem' => $valoresDoItem, 'dadosProjeto' => $dadosProjeto));
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    /**
     * Esse função é usada pelo proponente para solicitar a exclusão de um item da planilha orçamentária.
     */
    public function excluirItemSolicitacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $idPlanilhaAprovacao = $this->_request->getParam("idPlanilha");
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        /* DADOS DO ITEM PARA EXCLUSAO LÓGICA DO ITEM DA READEQUACAO */
        $dados = array();
        $dados['tpAcao'] = 'E';

        $whereIdPlanilha = "idPlanilhaAprovacaoPai = $idPlanilhaAprovacao";

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $itemTipoPlanilha = $tbPlanilhaAprovacao->buscar(array('idPlanilhaAprovacao=?' => $idPlanilhaAprovacao))->current();

        if ($itemTipoPlanilha->tpAcao == 'I') {
            $exclusaoLogica = $tbPlanilhaAprovacao->delete(array('idPlanilhaAprovacao = ?' => $idPlanilhaAprovacao));
        } else {
            if ($itemTipoPlanilha->tpPlanilha == 'SR') {
                $whereIdPlanilha = "idPlanilhaAprovacao = $idPlanilhaAprovacao";
            }
            $where = "stAtivo = 'N' AND tpPlanilha = 'SR' AND $whereIdPlanilha";
            $exclusaoLogica = $tbPlanilhaAprovacao->update($dados, $where);
        }

        if ($exclusaoLogica) {
            //$jsonEncode = json_encode($dadosPlanilha);

            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $idReadequacao = $tbReadequacao->buscarIdReadequacaoAtiva(
                $idPronac,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
            );
            
            $atualizarCustosVinculados = $this->atualizarCustosVinculados(
                $idPronac,
                $idReadequacao
            );
            
            if ($atualizarCustosVinculados['erro']) {
                $this->reverterAlteracaoItem(
                    $idPronac,
                    $idReadequacao,
                    $itemTipoPlanilha['idPlanilhaItem']
                );
                
                $this->_helper->json([
                    'resposta' => false,
                    'mensagem' => $atualizarCustosVinculados['mensagem']
                ]);
            }
            
            $this->_helper->json(array('resposta' => true));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /*
     * Criada em 10/03/14
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     */
    public function incluirItemPlanilhaReadequacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $idAgente = 0;
        $auth = Zend_Auth::getInstance(); // pega a autenticação
        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsAgente = $tblAgente->buscar(array('CNPJCPF = ?' => $auth->getIdentity()->Cpf));
        if ($rsAgente->count() > 0) {
            $idAgente = $rsAgente[0]->idAgente;
        }

        $newValorUnitario = str_replace('.', '', $_POST['newValorUnitario']);
        $newValorUnitario = str_replace(',', '.', $newValorUnitario);

        $nrFonteRecurso = $this->_request->getParam("newRecursos");
        $idProduto = $this->_request->getParam("newProduto");
        $idEtapa = $this->_request->getParam("newEtapa");
        $idPlanilhaItem = $this->_request->getParam("newItem");
        $idMunicipioDespesa = $this->_request->getParam("newMunicipio");
        $idReadequacao = $this->_request->getParam("idReadequacao");

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $existeItemSemelhante = $tbPlanilhaAprovacao->itemJaAdicionado(
            $idPronac,
            $nrFonteRecurso,
            $idProduto,
            $idEtapa,
            $idMunicipioDespesa,
            $idPlanilhaItem
        );

        if ($existeItemSemelhante) {
            $this->_helper->json(
                array(
                    'resposta' => false,
                    'msg' => 'ITEM DUPLICADO: Já existe um item igual na mesma fonte / produto / etapa / município.'
                )
            );
        } else {

            /* DADOS DO ITEM PARA INCLUSÃO DA READEQUAÇÃO */
            $dadosInclusao = array();

            $spCalcularMedianaItemOrcamentario = new \Planilha_Model_DbTable_SpCalcularMedianaItemOrcamentario();
            $objValorMediana = $spCalcularMedianaItemOrcamentario->obterMedianaItemOrcamento(
                $editarItem->idProduto,
                $editarItem->idUnidade,
                $editarItem->idPlanilhaItem,
                $editarItem->idUFDespesa,
                $editarItem->idMunicipioDespesa
            );
            $valorMediana = isset($objValorMediana['Mediana']) ? $objValorMediana['Mediana'] : 0;
            $dadosInclusao['stCustoPraticado'] = (!empty($valorMediana) && $valorMediana < $ValorUnitario) ? 1 : 0;
            
            $dadosInclusao['tpPlanilha'] = 'SR';
            $dadosInclusao['dtPlanilha'] = new Zend_Db_Expr('GETDATE()');
            $dadosInclusao['IdPRONAC'] = $idPronac;
            $dadosInclusao['idProduto'] = $idProduto;
            $dadosInclusao['idEtapa'] = $idEtapa;
            $dadosInclusao['idPlanilhaItem'] = $idPlanilhaItem;
            $dadosInclusao['dsItem'] = '';
            $dadosInclusao['idUnidade'] = $this->_request->getParam("newUnidade");
            $dadosInclusao['qtItem'] = $this->_request->getParam("newQuantidade");
            $dadosInclusao['nrOcorrencia'] = $this->_request->getParam("newOcorrencia");
            $dadosInclusao['vlUnitario'] = $newValorUnitario;
            $dadosInclusao['qtDias'] = $this->_request->getParam("newDias");
            $dadosInclusao['tpDespesa'] = 0;
            $dadosInclusao['tpPessoa'] = 0;
            $dadosInclusao['nrContraPartida'] = 0;
            $dadosInclusao['nrFonteRecurso'] = $nrFonteRecurso;
            $dadosInclusao['idUFDespesa'] = $this->_request->getParam("newUF");
            $dadosInclusao['idMunicipioDespesa'] = $idMunicipioDespesa;
            $dadosInclusao['dsJustificativa'] = utf8_decode($this->_request->getParam("newJustificativa"));
            $dadosInclusao['idAgente'] = $idAgente;
            $dadosInclusao['stAtivo'] = 'N';
            $dadosInclusao['tpAcao'] = 'I';
            $dadosInclusao['idReadequacao'] = $idReadequacao;

            $insert = $tbPlanilhaAprovacao->inserir($dadosInclusao);

            if ($insert) {
                $atualizarCustosVinculados = $this->atualizarCustosVinculados(
                    $idPronac,
                    $idReadequacao
                );
                if ($atualizarCustosVinculados['erro']) {
                    $this->reverterAlteracaoItem(
                        $idPronac,
                        $idReadequacao,
                        $dadosInclusao['idPlanilhaItem']
                    );

                    $this->_helper->json([
                        'resposta' => false,
                        'mensagem' => $atualizarCustosVinculados['mensagem']
                    ]);
                }
                
                $this->_helper->json(['resposta' => true]);
            } else {
                $this->_helper->json(['resposta' => false]);
            }
        }
    }

    /*
     * Criada em 06/03/14
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Essa função é usada para alterar os dados do item da planilha orçamentária.
     */
    public function salvarAvaliacaoDoItemAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $auth = Zend_Auth::getInstance(); // pega a autenticação
        $cpf = isset($auth->getIdentity()->Cpf) ? $auth->getIdentity()->Cpf : $auth->getIdentity()->usu_identificacao;

        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsAgente = $tblAgente->buscar(array('CNPJCPF = ?' => $cpf));
        $idAgente = 0;
        if ($rsAgente->count() > 0) {
            $idAgente = $rsAgente[0]->idAgente;
        }

        $idPlanilha = $this->_request->getParam('idPlanilha');

        $ValorUnitario = $this->_request->getParam('ValorUnitario');
        
        $ValorUnitario = str_replace('R$ ', '', $ValorUnitario);
        $ValorUnitario = str_replace('.', '', $ValorUnitario);
        $ValorUnitario = str_replace(',', '.', $ValorUnitario);
        
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $itemTipoPlanilha = $tbPlanilhaAprovacao->buscar(
            array(
                'idPlanilhaAprovacao=?' => $idPlanilha
            )
        )->current();

        if ($itemTipoPlanilha->tpPlanilha == 'SR') {
            $editarItem = $tbPlanilhaAprovacao->buscar(
                array(
                    'IdPRONAC=?' => $idPronac,
                    'tpPlanilha=?' => 'SR',
                    'idPlanilhaAprovacao=?' => $idPlanilha
                )
            )->current();
        } else {
            $editarItem = $tbPlanilhaAprovacao->buscar(
                array(
                    'IdPRONAC=?' => $idPronac,
                    'tpPlanilha=?' => 'SR',
                    'idPlanilhaAprovacaoPai=?' => $idPlanilha
                )
            )->current();
        }

        $spCalcularMedianaItemOrcamentario = new \Planilha_Model_DbTable_SpCalcularMedianaItemOrcamentario();
        $objValorMediana = $spCalcularMedianaItemOrcamentario->obterMedianaItemOrcamento(
            $editarItem->idProduto,
            $editarItem->idUnidade,
            $editarItem->idPlanilhaItem,
            $editarItem->idUFDespesa,
            $editarItem->idMunicipioDespesa
        );
        $valorMediana = isset($objValorMediana['Mediana']) ? $objValorMediana['Mediana'] : 0;
        $editarItem->stCustoPraticado = (!empty($valorMediana) && $valorMediana < $ValorUnitario) ? 1 : 0;
        
        $editarItem->idUnidade = $this->_request->getParam('Unidade');
        $editarItem->qtItem = $this->_request->getParam('Quantidade');
        $editarItem->nrOcorrencia = $this->_request->getParam('Ocorrencia');
        $editarItem->vlUnitario = $ValorUnitario;
        $editarItem->qtDias = $this->_request->getParam('QtdeDias');
        $editarItem->dsJustificativa = utf8_decode($this->_request->getParam('Justificativa'));
        $editarItem->idAgente = $idAgente;
        $editarItem->nrFonteRecurso = $this->_request->getParam('nrFonteRecurso');
        if ($editarItem->tpAcao == 'N') {
            $editarItem->tpAcao = 'A';
        }

        $editarItem->save();

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $idReadequacao = $tbReadequacao->buscarIdReadequacaoAtiva(
            $idPronac,
            Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
        );

        $projetosDbTable = new Projeto_Model_DbTable_Projetos();
        if ($projetosDbTable->possuiCalculoAutomaticoCustosVinculados($idPronac)) {
            $atualizarCustosVinculados = $this->atualizarCustosVinculados(
                $idPronac,
                $idReadequacao
            );
            
            if ($atualizarCustosVinculados['erro']) {
                $this->reverterAlteracaoItem(
                    $idPronac,
                    $idReadequacao,
                    $editarItem->idPlanilhaItem
                );

                $this->_helper->json([
                    'resposta' => false,
                    'mensagem' => $atualizarCustosVinculados['mensagem']
                ]);
            }
        }
        
        $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function atualizarCustosVinculados(
        $idPronac,
        $idReadequacao
    ) {
        $retorno = [
            'mensagem' => 'Custos vinculados atualizados!',
            'erro' => false
        ];
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $tipoReadequacao = $tbPlanilhaAprovacao->calculaSaldoReadequacaoBaseDeCusto($idPronac);
        
        if (in_array($tipoReadequacao, ['COMPLEMENTACAO', 'REDUCAO'])) {
            $propostaTbCustosVinculados = new Proposta_Model_TbCustosVinculadosMapper();
            $custosVinculados = $propostaTbCustosVinculados->obterCustosVinculadosReadequacao($idPronac);
            
            foreach ($custosVinculados as $item) {
                $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                $editarItem = $tbPlanilhaAprovacao->buscar([
                    'idPronac = ?' => $idPronac,
                    'idPlanilhaItem = ?' => $item['idPlanilhaItens'],
                    'idReadequacao = ?' => $idReadequacao
                ])->current();

                if (!$editarItem) {
                    continue;
                }
                
                $comprovantePagamentoxxPlanilhaAprovacao = new PrestacaoContas_Model_ComprovantePagamentoxPlanilhaAprovacao();
                
                $valorComprovado = $comprovantePagamentoxxPlanilhaAprovacao->valorComprovadoPorItem($idPronac, $item['idPlanilhaItens']);
                if ($valorComprovado > $item['valorUnitario']) {
                    
                    $retorno['mensagem'] = "Somente ser&aacute; permitido reduzir ou excluir itens or&ccedil;ament&aacute;rios caso tal a&ccedil;&atilde;o n&atilde;o afete negativamente os custos vinculados abaixo de valores j&aacute; comprovados.";
                    $retorno['erro'] = true;
                    return $retorno;
                }
                
                if ($itemOriginal->vlUnitario != $item['valorUnitario']) {
                    $editarItem->vlUnitario = $item['valorUnitario'];
                    $editarItem->tpAcao = 'A';
                    $editarItem->dsJustificativa = "Rec&aacute;lculo autom&aacute;tico com base no percentual solicitado pelo proponente ao enviar a proposta ao MinC.";

                    $editarItem->save();
                } else {
                    $editarItem->tpAcao = 'N';
                    $editarItem->save();
                }
            }
        } else if ($tipoReadequacao == 'REMANEJAMENTO') {
                $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                
                $itensOriginais = $tbPlanilhaAprovacao->buscar([
                    'idPronac = ?' => $idPronac,
                    'idEtapa IN (?)' => [
                        PlanilhaEtapa::ETAPA_CUSTOS_VINCULADOS,
                        PlanilhaEtapa::ETAPA_CAPTACAO_RECURSOS
                    ],
                    'stAtivo = ?' => 'S'
                ]);
                
                foreach ($itensOriginais as $itemOriginal) {
                    $editarItens = $tbPlanilhaAprovacao->buscar([
                        'idPronac = ?' => $idPronac,
                        'idPlanilhaItem = ?' => $itemOriginal['idPlanilhaItem'],
                        'idReadequacao = ?' => $idReadequacao
                    ]);
                    foreach ($editarItens as $editarItem) {
                        $editarItem->vlUnitario = $itemOriginal['vlUnitario'];
                        $editarItem->tpAcao = 'N';
                        $editarItem->save();
                    }
                }
        } else {
            $retorno['erro'] = true;
        }

        return $retorno;
    }

    /*
     * Criada em 18/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Essa função é usada pelostécnicos, pareceristas e componentes da comissão para alterarem os itens da planilha orçamentária.
     */
    public function alteracoesTecnicasNoItemAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $editarItem = $tbPlanilhaAprovacao->buscar(array('idPlanilhaAprovacao=?' => $_POST['idPlanilha']))->current();

        //$editarItem->idAgente = $idAgente;
        if ($editarItem->tpAcao == 'E') {
            $editarItem->tpAcao = 'N';
        } elseif ($editarItem->tpAcao == 'I') {
            $editarItem->delete();
        } elseif ($editarItem->tpAcao == 'A') {
            $itemAtivo = $tbPlanilhaAprovacao->buscar(array('idPlanilhaAprovacao=?' => $editarItem->idPlanilhaAprovacaoPai))->current();
            $editarItem->idProduto = $itemAtivo->idProduto;
            $editarItem->idEtapa = $itemAtivo->idEtapa;
            $editarItem->idUnidade = $itemAtivo->idUnidade;
            $editarItem->qtItem = $itemAtivo->qtItem;
            $editarItem->nrOcorrencia = $itemAtivo->nrOcorrencia;
            $editarItem->vlUnitario = $itemAtivo->vlUnitario;
            $editarItem->qtDias = $itemAtivo->qtDias;
            $editarItem->qtDias = $itemAtivo->qtDias;
            $editarItem->tpDespesa = $itemAtivo->tpDespesa;
            $editarItem->tpPessoa = $itemAtivo->tpPessoa;
            $editarItem->nrContraPartida = $itemAtivo->nrContraPartida;
            $editarItem->nrFonteRecurso = $itemAtivo->nrFonteRecurso;
            $editarItem->idUFDespesa = $itemAtivo->idUFDespesa;
            $editarItem->idMunicipioDespesa = $itemAtivo->idMunicipioDespesa;
            $editarItem->idMunicipioDespesa = $itemAtivo->idMunicipioDespesa;
            $editarItem->dsJustificativa = $itemAtivo->dsJustificativa;
            $editarItem->tpAcao = 'N';
        }
        $editarItem->save();
        
        $atualizarCustosVinculados = $this->atualizarCustosVinculados(
            $editarItem->IdPRONAC,
            $editarItem->idReadequacao
        );

        if ($atualizarCustosVinculados['erro']) {
            $this->reverterAlteracaoItem(
                $editarItem->IdPRONAC,
                $editarItem->idReadequacao,
                $editarItem->idPlanilhaItem
            );
            $this->_helper->json([
                'resposta' => false,
                'mensagem' => $atualizarCustosVinculados['mensagem']
            ]);
        }
        
        $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function reverterAlteracaoItem(
        $idPronac,
        $idReadequacao,
        $idPlanilhaItem
    ) {
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                
        $itemOriginal = $tbPlanilhaAprovacao->buscar([
            'idPronac = ?' => $idPronac,
            'idPlanilhaItem = ?' => $idPlanilhaItem,
            'stAtivo = ?' => 'S'
        ])->current();
        
        $itemAlterado = $tbPlanilhaAprovacao->buscar([
            'idPronac = ?' => $idPronac,
            'idPlanilhaItem = ?' => $idPlanilhaItem,
            'idReadequacao = ?' => $idReadequacao
        ])->current();                
        
        $itemAlterado->vlUnitario = $itemOriginal->vlUnitario;
        $itemAlterado->qtItem = $itemOriginal->qtItem;
        $itemAlterado->nrOcorrencia = $itemOriginal->nrOcorrencia;
        $itemAlterado->save();
    }

    /*
   * Criada em 18/03/2014
   * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
   * Essa função é usada pelostécnicos, pareceristas e componentes da comissão para alterarem os locais de realização.
   */
    public function alteracoesTecnicasNoLocalDeRealizacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
        $editarItem = $tbAbrangencia->buscar(array('idAbrangencia=?' => $_POST['idAbrangencia']))->current();

        if ($this->idPerfil == Autenticacao_Model_Grupos::PARECERISTA || $this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
            $editarItem->tpAnaliseTecnica = $_POST['tpAcao'];
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {
            $editarItem->tpAnaliseComissao = $_POST['tpAcao'];
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::PROPONENTE) {
            if ($editarItem->tpSolicitacao == 'E') {
                $editarItem->tpSolicitacao = 'N';
            } elseif ($editarItem->tpSolicitacao == 'I') {
                $editarItem->delete();
                $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
        $editarItem->save();

        $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

     /*
     * Criada em 28/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Essa função é usada pelostécnicos, pareceristas e componentes da comissão para alterarem os planos de divulgação.
     */
    public function alteracoesTecnicasNoPlanoDeDivulgacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $tbPlanoDivulgacao = new tbPlanoDivulgacao();
        $editarItem = $tbPlanoDivulgacao->buscar(array('idPlanoDivulgacao=?' => $_POST['idPlanoDivulgacao']))->current();

        if ($this->idPerfil == Autenticacao_Model_Grupos::PARECERISTA || $this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
            $editarItem->tpAnaliseTecnica = $_POST['tpAcao'];
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {
            $editarItem->tpAnaliseComissao = $_POST['tpAcao'];
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::PROPONENTE) {
            if ($editarItem->tpSolicitacao == 'E') {
                $editarItem->tpSolicitacao = 'N';
            } elseif ($editarItem->tpSolicitacao == 'I') {
                $editarItem->delete();
                $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
        $editarItem->save();

        $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /*
     * Criada em 1/04/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Essa função é usada pelostécnicos, pareceristas, proponente e componentes da comissão para alterarem os planos de distribuição.
     */
    public function alteracoesTecnicasNoPlanoDeDistribuicaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
        $editarItem = $tbPlanoDistribuicao->buscar(array('idPlanoDistribuicao=?' => $_POST['idPlanoDistribuicao']))->current();

        if ($this->idPerfil == Autenticacao_Model_Grupos::PARECERISTA || $this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
            $editarItem->tpAnaliseTecnica = $_POST['tpAcao'];
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {
            $editarItem->tpAnaliseComissao = $_POST['tpAcao'];
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::PROPONENTE) {
            if ($editarItem->tpSolicitacao == 'E') {
                $editarItem->tpSolicitacao = 'N';
            } elseif ($editarItem->tpSolicitacao == 'I') {
                $editarItem->delete();
                $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
        $editarItem->save();

        $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function carregarReadequacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idPronac = $this->_request->getParam("idPronac");
        $idTipoReadequacao = $this->_request->getParam("idTipoReadequacao");
        
        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $readequacaoResult = $tbReadequacao->readequacoesCadastradasProponente(
            [
                'a.idPronac = ?' => $idPronac,
                'a.siEncaminhamento = ?' => Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
                'a.stEstado = ?' => Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_EM_ANDAMENTO,
                'b.idTipoReadequacao = ?' => $idTipoReadequacao
            ]
        )->current();

        $readequacao = [];
        
        if (count($readequacaoResult) > 0) {
            $readequacao['idReadequacao'] = $readequacaoResult['idReadequacao'];
            $readequacao['idPronac'] = $readequacaoResult['idPronac'];
            $readequacao['dtSolicitacao'] = $readequacaoResult['dtSolicitacao'];
            $readequacao['stAtendimento'] = $readequacaoResult['stAtendimento'];
            $readequacao['dsAvaliacao'] = utf8_encode($readequacaoResult['dsAvaliacao']);
            $readequacao['dsSolicitacao'] = utf8_encode($readequacaoResult['dsSolicitacao']);
            $readequacao['dsJustificativa'] = utf8_encode($readequacaoResult['dsJustificativa']);
            $readequacao['dsReadequacao'] = utf8_encode($readequacaoResult['dsReadequacao']);
            $readequacao['idArquivo'] = $readequacaoResult['idArquivo'];
            $readequacao['nmArquivo'] = utf8_encode($readequacaoResult['nmArquivo']);
            
        }
        
        $this->_helper->json([
            'success' => true,
            'readequacao' => $readequacao,
            'msg' => 'Dados salvos com sucesso!'
        ]);
    }
    

    public function carregarValorEntrePlanilhasAction()
    {
        $auth = Zend_Auth::getInstance();
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam("idPronac");

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();

        $valorEntrePlanilhas = $tbReadequacao->carregarValorEntrePlanilhas(
            $idPronac,
            Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
        );

        $this->montaTela(
            'readequacoes/carregar-valor-entre-planilhas.phtml',
            array(
            'statusPlanilha' => $valorEntrePlanilhas['statusPlanilha'],
            'vlDiferencaPlanilhas' => 'R$ '.number_format(($valorEntrePlanilhas['PlanilhaReadequadaTotal'] - $valorEntrePlanilhas['PlanilhaAtivaTotal']), 2, ',', '.')
            )
        );
    }

    /*
     * Criada em 28/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Essa função é usada para carregar os dados do planos de divulgação do projeto.
     */
    public function carregarPlanosDeDivulgacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tbPlanoDivulgacao = new tbPlanoDivulgacao();
        $planosDivulgacao = $tbPlanoDivulgacao->buscarPlanosDivulgacaoReadequacao($idPronac, 'tbPlanoDivulgacao');

        if (count($planosDivulgacao) == 0) {
            $planosDivulgacao = $tbPlanoDivulgacao->buscarPlanosDivulgacaoReadequacao($idPronac, 'PlanoDeDivulgacao');
        }

        $Verificacao = new Verificacao();
        $pecas = $Verificacao->buscar(array('idTipo=?' => 1), array('Descricao'));
        $veiculos = $Verificacao->buscar(array('idTipo=?' => 2), array('Descricao'));

        $get = Zend_Registry::get('get');
        $link = isset($get->link) ? true : false;

        $this->montaTela(
            'readequacoes/carregar-planos-de-divulgacao.phtml',
            array(
                'idPronac' => $idPronac,
                'planosDeDivulgacao' => $planosDivulgacao,
                'pecas' => $pecas,
                'veiculos' => $veiculos,
                'link' => $link
            )
        );
    }

    /*
    * Criada em 27/03/2014
    * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
    * Essa função é usada para carregar os dados dos planos de divulgação do projeto.
    */
    public function carregarPlanosDeDivulgacaoReadequacoesAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $idPronac = $this->_request->getParam("idPronac");
        $idReadequacao = $this->_request->getParam("idReadequacao");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbPlanoDivulgacao = new tbPlanoDivulgacao();
        $planosDivulgacao = $tbPlanoDivulgacao->buscarPlanosDivulgacaoConsolidadoReadequacao($idReadequacao);

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosReadequacao = $tbReadequacao->buscar(array('idReadequacao=?' => $idReadequacao))->current();
        $siEncaminhamento = $dadosReadequacao->siEncaminhamento;

        $Verificacao = new Verificacao();
        $pecas = $Verificacao->buscar(array('idTipo=?' => 1), array('Descricao'));
        $veiculos = $Verificacao->buscar(array('idTipo=?' => 2), array('Descricao'));

        $get = Zend_Registry::get('get');
        $link = isset($get->link) ? true : false;

        $this->montaTela(
            'readequacoes/carregar-planos-de-divulgacao.phtml',
            array(
                'idPronac' => $idPronac,
                'planosDeDivulgacao' => $planosDivulgacao,
                'pecas' => $pecas,
                'veiculos' => $veiculos,
                'link' => $link,
                'idReadequacao' => $idReadequacao,
                'siEncaminhamento' => $siEncaminhamento
            )
        );
    }

    /*
     * Criada em 14/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Essa função para incluir uma solicitação de readequação.
     */
    public function incluirSolicitacaoReadequacaoAction()
    {
        $acaoErro = '';

        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idPronac = $this->_request->getParam("idPronac");
        if (empty($idPronac)) {
            parent::message("PRONAC &eacute; obrigat&oacute;rio", "principalproponente", "ALERT");
        }

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        
        $idReadequacao = filter_var($this->_request->getParam("idReadequacao"), FILTER_SANITIZE_NUMBER_INT);
        $idTipoReadequacao = filter_var($this->_request->getParam("tipoReadequacao"), FILTER_SANITIZE_NUMBER_INT);
        
        $urlCallback = $this->_request->getParam('urlCallback');

        if (empty($urlCallback)) {
            switch ($idTipoReadequacao) {
                case Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA:
                    $urlCallback = "readequacao/readequacoes/planilha-orcamentaria/?idPronac=" . Seguranca::encrypt($idPronac);
                    break;
                default:
                    $urlCallback = "readequacao/readequacoes/index?idPronac=" . Seguranca::encrypt($idPronac);
                    break;
            }
        }


        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $busca = array();

        if ($idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
            $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
            $planilhaReadequada = $tbPlanilhaAprovacao->buscar(array('IdPRONAC = ?' => $idPronac, 'tpPlanilha = ?' => 'SR', 'idReadequacao= ?' => $idReadequacao));
            if (count($planilhaReadequada) == 0) {
                parent::message('N&atilde;o houve nenhuma altera&ccedil;&atilde;o na planilha or&ccedil;ament&aacute;ria do projeto!', "readequacao/readequacoes/planilha-orcamentaria?idPronac=" . Seguranca::encrypt($idPronac), "ERROR");
            }
        } elseif ($idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO) {
            $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
            $locaisReadequados = $tbAbrangencia->buscar(array('idPronac = ?' => $idPronac, 'idReadequacao is null' => ''));
            if (count($locaisReadequados) == 0) {
                parent::message('N&atilde;o houve nenhuma altera&ccedil;&atilde;o nos locais de realiza&ccedil;&atilde;o do projeto!', $urlCallback, "ERROR");
            }
        } elseif ($idReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DIVULGACAO) {
            $tbPlanoDivulgacao = new tbPlanoDivulgacao();
            $planosReadequados = $tbPlanoDivulgacao->buscar(array('idPronac = ?' => $idPronac, 'idReadequacao is null' => ''));
            if (count($planosReadequados) == 0) {
                parent::message('N&atilde;o houve nenhuma altera&ccedil;&atilde;o nos planos de divulga&ccedil;&atilde;o do projeto!', $urlCallback, "ERROR");
            }
        }

        try {
            $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $documento = $Readequacao_Model_DbTable_TbReadequacao->inserirDocumento();
            $idDocumento = $documento['idDocumento'];

            $auth = Zend_Auth::getInstance(); // pega a autenticação
            $tblAgente = new Agente_Model_DbTable_Agentes();
            $rsAgente = $tblAgente->buscar(array('CNPJCPF=?' => $auth->getIdentity()->Cpf))->current();

            if ($idReadequacao != '') {
                $dadosReadequacao = [];
                $dadosReadequacao['dsJustificativa'] = $this->_request->getParam('descJustificativa');
                $dadosReadequacao['dsSolicitacao'] = $this->_request->getParam('descSolicitacao');                
                if ($idDocumento) {
                    $dadosReadequacao['idDocumento'] = $idDocumento;
                }

                $readequacaoWhere = "idReadequacao = $idReadequacao";
                $tbReadequacao->update($dadosReadequacao, $readequacaoWhere);

                $mensagemSucesso = "Solicita&ccedil;&atilde;o alterada com sucesso!";
            } else {
                $dados = [];
                $dados['idPronac'] = $idPronac;
                $dados['idTipoReadequacao'] = $idTipoReadequacao;
                $dados['dtSolicitacao'] = new Zend_Db_Expr('GETDATE()');
                $dados['idSolicitante'] = $rsAgente->idAgente;
                $dados['dsJustificativa'] = $this->_request->getParam('descJustificativa');
                $dados['dsSolicitacao'] = $this->_request->getParam('descSolicitacao');
                $dados['idDocumento'] = $idDocumento;
                $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE;
                $dados['stEstado'] = 0;
                $idReadequacao = $tbReadequacao->inserir($dados);

                if (!empty($idReadequacao) && $idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) {
                    $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
                    $tbPlanoDistribuicaoMapper->incluirIdReadequacaoNasSolicitacoesAtivas($idPronac, $idReadequacao);

                    $tbDistribuicaoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
                    $tbDistribuicaoMapper->incluirIdReadequacaoNasSolicitacoesAtivas($idPronac, $idReadequacao);
                }

                $mensagemSucesso = "Solicita&ccedil;&atilde;o cadastrada com sucesso!";
            }

            if ($idReadequacao && $idTipoReadequacao != Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                $acaoErro = 'cadastrar';

                parent::message($mensagemSucesso, $urlCallback, "CONFIRM");
            } elseif ($idReadequacao && $idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                $acaoErro = 'alterar';

                parent::message("Solicita&ccedil;&atilde;o alterada com sucesso!", $urlCallback, "CONFIRM");
            } else {
                throw new Exception("Erro ao $acaoErro a readequação!");
            }
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), $urlCallback, "ERROR");
        }
    }

    public function finalizarSolicitacaoReadequacaoAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }
         
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
         
        $idReadequacao = $this->_request->getParam("idReadequacao");
         
        try {
            $arrayBuscaReadequacao = array(
                'a.idPronac = ?' => $idPronac,
                'a.siEncaminhamento = ?' => Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
                'a.stEstado = ?' => 0,
            );
             
            if ($idReadequacao) {
                $arrayBuscaReadequacao['a.idReadequacao = ?'] = $idReadequacao;
            } else {
                $arrayBuscaReadequacao['b.siReadequacao = ?'] = Readequacao_Model_TbTipoReadequacao::SI_READEQUACAO_DIVERSA;
            }
             
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $readequacoes = $tbReadequacao->readequacoesCadastradasProponente($arrayBuscaReadequacao, array(1), false);
            $dados = array();
            $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_MINC;
            $dados['dtEnvio'] = new Zend_Db_Expr('GETDATE()');
            $where = "idPronac = $idPronac AND siEncaminhamento = " . Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE . " AND stEstado = 0";
             
            if ($idReadequacao) {
                $where .= " AND idReadequacao = $idReadequacao";
            } else {
                $idsTiposReadequacoesDiversas = array_column($readequacoes->toArray(), 'idTipoReadequacao');
                $idsTiposReadequacoesDiversas = implode(',', $idsTiposReadequacoesDiversas);
                $where .= " AND idTipoReadequacao IN ($idsTiposReadequacoesDiversas)";
            }
            $atualizar = $tbReadequacao->update($dados, $where);

            if (!$atualizar) {
                throw new Exception ("Erro ao finalizar as readequa&ccedil;&otilde;es!");
            }

            foreach ($readequacoes as $r) {
                if ($r->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO) {
                    $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
                    $dadosAb = array();
                    $dadosAb['idReadequacao'] = $r->idReadequacao;
                    $whereAb = "idPronac = $idPronac AND stAtivo = 'S' AND idReadequacao is null";
                    $tbAbrangencia->update($dadosAb, $whereAb);
                } elseif ($r->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) {
                    $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
                    $dadosPDDist = array();
                    $dadosPDDist['idReadequacao'] = $r->idReadequacao;
                    $wherePDDist = "idPronac = $idPronac AND stAtivo = 'S' AND idReadequacao is null";
                    $tbPlanoDistribuicao->update($dadosPDDist, $wherePDDist);
                } elseif ($r->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DIVULGACAO) {
                    $tbPlanoDivulgacao = new tbPlanoDivulgacao();
                    $dadosPDD = array();
                    $dadosPDD['idReadequacao'] = $r->idReadequacao;
                    $wherePDD = "idPronac = $idPronac AND stAtivo = 'S' AND idReadequacao is null";
                    $tbPlanoDivulgacao->update($dadosPDD, $wherePDD);
                }
            }
            if ($atualizar) {
                parent::message('Solicita&ccedil;&atilde;o enviada com sucesso!', "default/consultardadosprojeto/index?idPronac=" . Seguranca::encrypt($idPronac), "CONFIRM");
            }
        }
        catch (Exception $e) {
            parent::message($e->getMessage(), "readequacao/readequacoes?idPronac=".Seguranca::encrypt($idPronac), "ERROR");
        }
    }
    
    
    public function painelAction()
    {
        $this->_redirect('index/index/#/readequacao/painel');
    }

    /**
     * salvarAvaliacaoAction Função utilizada pleo Coord. de Acompanhamento para avaliar a readequação e encaminhá-la.
     *
     * @since  Alterada em 11/03/14
     * @author Jefferson Alessandro <jeffersonassilva@gmail.com>
     * @author wouerner <wouerner@gmail.com>
     * @access public
     * @return void
     */
    public function salvarAvaliacaoAction()
    {
        $perfisAcesso = array(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO);

        if (!in_array($this->idPerfil, $perfisAcesso)) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idReadequacao = $this->_request->getParam('idReadequacao');
        $filtro = $this->_request->getParam('filtro');

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $r = $tbReadequacao->find(array('idReadequacao = ?' => $idReadequacao))->current();
        $stValidacaoCoordenador = 0;
        $dataEnvio = null;

        if ($r) {
            $r->stAtendimento = $this->_request->getParam('stAtendimento');
            $r->dsAvaliacao = $this->_request->getParam('dsAvaliacao');
            $r->dtAvaliador = new Zend_Db_Expr('GETDATE()');
            $r->idAvaliador = $this->idUsuario;

            if ($this->_request->getParam('stAtendimento') == 'I') {
                // indeferida
                $r->siEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_INDEFERIDA;
                $r->stEstado = Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_FINALIZADO;
            } elseif ($this->_request->getParam('stAtendimento') == 'DP') {
                // devolvida ao proponente
                $r->siEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE;
                $r->stEstado = 0;
                $r->stAtendimento = 'E';
            } else {
                // deferida
                if ($this->_request->getParam('vinculada') == Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI || $this->_request->getParam('vinculada') == Orgaos::ORGAO_SAV_CAP) {
                    $r->siEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA;
                    $dataEnvio = new Zend_Db_Expr('GETDATE()');
                    $r->idAvaliador = $this->_request->getParam('destinatario');
                } elseif ($this->_request->getParam('vinculada') == 400) {
                    $stValidacaoCoordenador = 1;
                    $r->siEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_CNIC;
                    $r->idAvaliador = $this->_request->getParam('destinatario');
                } else {
                    $r->siEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE;
                }
            }
            $r->save();

            if ($this->_request->getParam('stAtendimento') == 'D') {

                // busca readequacao para ver se existe. Se não existe, cria, senão atualiza
                $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
                $jaDistribuiu = $tbDistribuirReadequacao->buscar(array('idReadequacao = ?' => $r->idReadequacao))->current();

                if (empty($jaDistribuiu)) {
                    $dados = array(
                        'idReadequacao' => $r->idReadequacao,
                        'idUnidade' => $this->_request->getParam('vinculada'),
                        'DtEncaminhamento' => new Zend_Db_Expr('GETDATE()'),
                        'idAvaliador' => (null !== $this->_request->getParam('destinatario')) ? $this->_request->getParam('destinatario') : null,
                        'dtEnvioAvaliador' => !empty($dataEnvio) ? $dataEnvio : null,
                        'stValidacaoCoordenador' => $stValidacaoCoordenador,
                        'dsOrientacao' => $r->dsAvaliacao
                    );

                    $tbDistribuirReadequacao->inserir($dados);
                } else {
                    $dados = array(
                        'idUnidade' => $this->_request->getParam('vinculada'),
                        'DtEncaminhamento' => new Zend_Db_Expr('GETDATE()'),
                        'idAvaliador' => (null !== $this->_request->getParam('destinatario')) ? $this->_request->getParam('destinatario') : null,
                        'dtEnvioAvaliador' => !empty($dataEnvio) ? $dataEnvio : null,
                        'stValidacaoCoordenador' => $stValidacaoCoordenador,
                        'dsOrientacao' => $r->dsAvaliacao
                    );
                    $where = "idReadequacao = " . $r->idReadequacao;

                    $tbDistribuirReadequacao->update($dados, $where);
                }
            } else if ($this->_request->getParam('stAtendimento') == 'DP') {
                $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
                $excluiDistribuicao = $tbDistribuirReadequacao->delete([
                    'idReadequacao = ?' => $r->idReadequacao
                ]);                
            }
            if ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
                parent::message('Dados salvos com sucesso!', "readequacao/readequacoes/painel-readequacoes?tipoFiltro=$filtro", "CONFIRM");
            } else {
                parent::message('Dados salvos com sucesso!', "readequacao/readequacoes/painel?tipoFiltro=$filtro", "CONFIRM");
            }
        } else {
            if ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
                parent::message('Nenhum registro encontrado.', "readequacao/readequacoes/painel-readequacoes?tipoFiltro=$filtro", "ERROR");
            } else {
                parent::message('Nenhum registro encontrado.', "readequacao/readequacoes/painel?tipoFiltro=$filtro", "ERROR");
            }
        }
    }
    
    public function painelReadequacoesAction()
    {
        $this->_redirect('index/index/#/readequacao/painel');
    }

    /**
     * Função acessada pelo Coordenador de Parecer, Coordenador de Acompanhamento, e Coordenador Geral de Acomapanhamento.
     */
    public function visualizarReadequacaoAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }
        $id = $this->_request->getParam('id');
        if (strlen($id) > 7) {
            $id = Seguranca::dencrypt($id);
        }
        
        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $d = $tbReadequacao->visualizarReadequacao(array('a.idReadequacao = ?' => $id))->current();
        $this->view->dados = $d;

        $Projetos = new Projetos();
        $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $d->idPronac))->current();
        $this->view->projeto = $p;

        $fnIN2017 = new fnVerificarProjetoAprovadoIN2017();

        $this->in2017 = $fnIN2017->verificar($d->idPronac);
        $this->view->in2017 = $this->in2017;

        $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $this->view->Parecer = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?' => $id, 'idTipoAgente =?' => 1))->current();
    }

    /**
     * Função acessada pelo Parecerista ou Técnico de acompanhamento para avaliar a readequação.
     */
    public function formAvaliarReadequacaoAction()
    {
        $perfisAcesso = array(Autenticacao_Model_Grupos::PARECERISTA, Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO);

        if (!in_array($this->idPerfil, $perfisAcesso)) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idReadequacao = (strlen($this->_request->getParam('id')) > 7) ? (int)Seguranca::dencrypt($this->_request->getParam('id')) : $this->_request->getParam('id');
        $this->view->idReadequacao = $idReadequacao;

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dados = $tbReadequacao->buscarDadosReadequacoes(array('idReadequacao = ?' => $idReadequacao))->current();
        if (!$dados) {
            parent::message("Readequa&ccedil;&atilde;o n&atilde;o encontrada!", "readequacao/readequacoes/painel-readequacoes", "ERROR");
        }
        $this->view->dados = $dados;
        $this->view->idPronac = $dados->idPronac;
        $this->view->nmPagina = $dados->dsReadequacao;
        $Projetos = new Projetos();

        //DADOS DO PROJETO
        $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $dados->idPronac))->current();
        $this->view->projeto = $p;

        $mensagemCustosVinculados = "";
        if ($this->isProjetoTransicaoIn2017($dados->idPronac)) {
            // fazer a comparacao com a data
            $mensagemCustosVinculados = "<div align='center'><strong>ATEN&Ccedil;&Atilde;O:</strong><br/> Projetos adequados &agrave; realidade de execu&ccedil;&atilde;o at&eacute; a data de <strong>16/10/2018</strong> ter&atilde;o seus custos vinculados e de remunera&ccedil;&atilde;o de capta&ccedil;&atilde;o de recursos recalculados no momento em que for realizada uma readequa&ccedil;&atilde;o.</div>";
        }
        $this->view->mensagemCustosVinculados = $mensagemCustosVinculados;
        
        $TbPlanilhaUnidade = new Proposta_Model_DbTable_TbPlanilhaUnidade();
        $buscarUnidade = $TbPlanilhaUnidade->buscarUnidade();
        $this->view->Unidade = $buscarUnidade;

        $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $this->view->Parecer = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?' => $idReadequacao, 'b.idTipoAgente = ?' => 1))->current();

        $fnIN2017 = new fnVerificarProjetoAprovadoIN2017();

        $this->in2017 = $fnIN2017->verificar($dados->idPronac);
        $this->view->in2017 = $this->in2017;
    }

    /**
     * Função acessada pelo Parecerista ou Técnico de acompanhamento para avaliar a readequação.
     */
    public function salvarParecerTecnicoAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::PARECERISTA
            && $this->idPerfil != Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $params = $this->getRequest()->getParams();

        $idPronac = $params['idPronac'];
        $idReadequacao = $params['idReadequacao'];
        $dsParecer = $params['dsParecer'];
        $parecerProjeto = $params['parecerProjeto'];

        $campoTipoParecer = 8;
        $vlPlanilha = 0;

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosRead = $tbReadequacao->buscar(array('idReadequacao=?' => $idReadequacao))->current();

        //SE FOR READEQUAÇÃO DE PLANILHA ORÇAMENTÁRIA, O CAMPO TipoParecer DA TABELA SAC.dbo.Parecer MUDAR.
        if ($dadosRead->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
            $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

            //BUSCAR VALOR TOTAL DA PLANILHA ATIVA
            $where = array();
            $where['a.IdPRONAC = ?'] = $idPronac;
            $where['a.stAtivo = ?'] = 'S';
            $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //BUSCAR VALOR TOTAL DA PLANILHA DE READEQUADA
            $where = array();
            $where['a.IdPRONAC = ?'] = $idPronac;
            $where['a.tpPlanilha = ?'] = 'SR';
            $where['a.stAtivo = ?'] = 'N';
            $PlanilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            if ($PlanilhaAtiva->Total > $PlanilhaReadequada->Total) {
                $vlPlanilha = $PlanilhaAtiva->Total - $PlanilhaReadequada->Total;
                $campoTipoParecer = 4;
            } else {
                $vlPlanilha = $PlanilhaReadequada->Total - $PlanilhaAtiva->Total;
                $campoTipoParecer = 2;
            }
        }

        if ($dadosRead->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) {
            $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
            $tbPlanoDistribuicaoMapper->atualizarAnaliseTecnica($this->idPronac, $idReadequacao, $this->idPerfil, $parecerProjeto);
        }

        if ($dadosRead->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS) {
            $TbSolicitacaoTransferenciaRecursos = new Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos();
            $tbSolicitacaoTransferenciaRecursosMapper = new Readequacao_Model_TbSolicitacaoTransferenciaRecursosMapper();
            $projetos = new Projetos();

            $projetosRecebedores = $TbSolicitacaoTransferenciaRecursos->obterProjetosRecebedores($dadosRead->idReadequacao);
            $projetoTransferidor = $projetos->buscarProjetoTransferidor($dadosRead->idPronac);

            foreach ($projetosRecebedores as $projetoRecebedor) {
                $arrData = [];
                $arrData['idSolicitacaoTransferenciaRecursos'] = $projetoRecebedor['idSolicitacao'];
                if ($parecerProjeto == 2) {
                    $arrData['siAnaliseTecnica'] = 'D';
                } else if ($parecerProjeto == 1) {
                    $arrData['siAnaliseTecnica'] = 'D';
                } else {
                    $arrData['siAnaliseTecnica'] = 'N';
                }

                $statusSolicitacaoTransferenciaRecursos = $tbSolicitacaoTransferenciaRecursosMapper->salvarParecerTecnico($arrData);

                if (!$statusSolicitacaoTransferenciaRecursos) {
                    throw new Exception("N&atilde;o foi poss&iacute;vel incluir os projetos recebedores da solicita&ccedil;&atilde;o");
                }
            }
        }

        try {
            //ATUALIZA A SITUAÇÃO, ÁREA E SEGMENTO DO PROJETO
            $d = array();
            $d['ProvidenciaTomada'] = 'Readequa&ccedil;&atilde;o em an&aacute;lise pela &aacute;rea t&eacute;cnica.';
            //$d['dtSituacao'] = new Zend_Db_Expr('GETDATE()');
            $where = "IdPRONAC = $idPronac";
            $Projetos = new Projetos();
            $Projetos->update($d, $where);

            $enquadramentoDAO = new Admissibilidade_Model_Enquadramento();
            $buscaEnquadramento = $enquadramentoDAO->buscarDados($idPronac, null, false);

            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac));
            if (count($dadosProjeto) < 1) {
                throw new Exception("Projeto Cultural n&atilde;o encontrado.");
            }

            //CADASTRA OU ATUALIZA O PARECER DO TECNICO
            $parecerDAO = new Parecer_Model_DbTable_Parecer();
            $dadosParecer = array(
                'idPRONAC' => $idPronac,
                'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                'Sequencial' => $dadosProjeto[0]->Sequencial,
                'TipoParecer' => $campoTipoParecer,
                'ParecerFavoravel' => $parecerProjeto,
                'DtParecer' => MinC_Db_Expr::date(),
                'NumeroReuniao' => null,
                'ResumoParecer' => $dsParecer,
                'SugeridoReal' => $vlPlanilha,
                'Atendimento' => 'S',
                'idEnquadramento' => $buscaEnquadramento['IdEnquadramento'],
                'stAtivo' => 1,
                'idTipoAgente' => 1,
                'Logon' => $this->idUsuario
            );

            $parecerAntigo = array(
                'Atendimento' => 'S',
                'stAtivo' => 0
            );

            # @todo isso pode causar inconsistência se existir mais de uma readquação em analise
            $whereUpdateParecer = 'IdPRONAC = ' . $idPronac;
            $alteraParecer = $parecerDAO->alterar($parecerAntigo, $whereUpdateParecer);

            $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
            $buscarParecer = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?' => $idReadequacao))->current();

            if ($buscarParecer) {
                $whereUpdateParecer = 'IdParecer = ' . $buscarParecer->IdParecer;
                $parecerDAO->alterar($dadosParecer, $whereUpdateParecer);
                $idParecer = $buscarParecer->IdParecer;
            } else {
                $idParecer = $parecerDAO->inserir($dadosParecer);
            }

            $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
            $parecerReadequacao = $tbReadequacaoXParecer->buscar(array('idReadequacao = ?' => $idReadequacao, 'idParecer =?' => $idParecer));
            if (count($parecerReadequacao) == 0) {
                $dadosInclusao = array(
                    'idReadequacao' => $idReadequacao,
                    'idParecer' => $idParecer
                );
                $tbReadequacaoXParecer->inserir($dadosInclusao);
            }

            if (isset($params['finalizarAvaliacao']) && $params['finalizarAvaliacao'] == 1) {
                $servicoReadequacaoAssinatura = new \Application\Modules\Readequacao\Service\Assinatura\ReadequacaoAssinatura(
                    $this->grupoAtivo,
                    $this->auth
                );
                $idTipoDoAto = $servicoReadequacaoAssinatura->obterAtoAdministrativoPorTipoReadequacao($dadosRead->idTipoReadequacao);
                
                $servicoDocumentoAssinatura = new \Application\Modules\Readequacao\Service\Assinatura\DocumentoAssinatura(
                    $idPronac,
                    $idTipoDoAto,
                    $idParecer
                );
                $idDocumentoAssinatura = $servicoDocumentoAssinatura->iniciarFluxo();
                
                $origin = "readequacao/readequacoes/painel-readequacoes";

                parent::message(
                    "A avalia&ccedil;&atilde;o da readequa&ccedil;&atilde;o foi finalizada com sucesso! ",
                    "/assinatura/index/visualizar-projeto?idDocumentoAssinatura={$idDocumentoAssinatura}&origin={$origin}",
                    "CONFIRM"
                );
            }

            $idReadequacao = Seguranca::encrypt($idReadequacao);
            parent::message(
                "Dados salvos com sucesso!",
                "readequacao/readequacoes/form-avaliar-readequacao?id=$idReadequacao",
                "CONFIRM"
            );
        } catch (Exception $e) {
            parent::message($e->getMessage(), "readequacao/readequacoes/form-avaliar-readequacao?id=$idReadequacao", "ERROR");
        }
    }

    
    /*
     * Alterada em 14/03/14
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Função acessada pelo componente da comissão para analisar as readequações - painel.
    */
    public function analisarReadequacoesCnicAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            $campo = null;
            $order = array(2); //idReadequacao
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/

        $where = array();
        $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
        $where['a.siEncaminhamento = ?'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_CNIC;
        $where['d.idUnidade = ?'] = 400; // CNIC
        $where['d.idAvaliador = ?'] = $this->idUsuario;

        if ((isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['b.AnoProjeto+b.Sequencial = ?'] = $_GET['pronac'];
            $this->view->pronac = $_GET['pronac'];
        }

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $total = $tbReadequacao->painelReadequacoesComponente($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbReadequacao->painelReadequacoesComponente($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag" => $pag,
            "qtde" => $this->intTamPag,
            "campo" => $campo,
            "ordem" => $ordem,
            "ordenacao" => $ordenacao,
            "novaOrdem" => $novaOrdem,
            "total" => $total,
            "inicio" => ($inicio + 1),
            "fim" => $fim,
            "totalPag" => $totalPag,
            "Itenspag" => $this->intTamPag,
            "tamanho" => $tamanho
        );

        $this->view->paginacao = $paginacao;
        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;
    }

    /*
     * Alterada em 17/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Função acessada pelo componente da comissão para avaliar as readequações.
    */
    public function formAvaliarReadequacaoCnicAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $get = Zend_Registry::get('get');
        $idReadequacao = (int)Seguranca::dencrypt($get->id);

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dados = $tbReadequacao->buscarDadosReadequacoesCnic(array('a.idReadequacao = ?' => $idReadequacao, 'f.idUnidade != ?' => 400))->current();

        if (!$dados) {
            $dados = $tbReadequacao->buscarDadosReadequacoesCnic(array('a.idReadequacao = ?' => $idReadequacao, 'f.idUnidade = ?' => 400))->current();
        }

        $this->view->dados = $dados;
        $this->view->idPronac = $dados->idPronac;
        $this->view->nmPagina = $dados->dsReadequacao;

        //DADOS DO PROJETO
        $Projetos = new Projetos();
        $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $dados->idPronac))->current();
        $this->view->projeto = $p;

        $TbPlanilhaUnidade = new Proposta_Model_DbTable_TbPlanilhaUnidade();
        $buscarUnidade = $TbPlanilhaUnidade->buscarUnidade();
        $this->view->Unidade = $buscarUnidade;

        //DADOS DA AVALIAÇÃO TÉCNICA ou PARECERISTA
        $avaliacaoTecnica = $tbReadequacao->buscarDadosParecerReadequacao(array('a.idReadequacao = ?' => $idReadequacao, 'c.idTipoAgente = ?' => 1))->current();
        $this->view->avaliacaoTecnica = $avaliacaoTecnica;

        //DADOS DA AVALIAÇÃO DO COMPONENTE
        $avaliacaoConselheiro = $tbReadequacao->buscarDadosParecerReadequacao(array('a.idReadequacao = ?' => $idReadequacao, 'c.idTipoAgente = ?' => 6))->current();
        $this->view->avaliacaoConselheiro = $avaliacaoConselheiro;

        $fnIN2017 = new fnVerificarProjetoAprovadoIN2017();

        $this->in2017 = $fnIN2017->verificar($dados->idPronac);
        $this->view->in2017 = $this->in2017;
    }

    /*
     * Alterada em 15/05/2015
     * Função acessada pelo componente da comissão para avaliar as readequações.
    */
    public function componenteComissaoSalvarAvaliacaoAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idPronac = $_POST['idPronac'];
        $idReadequacao = $_POST['idReadequacao'];
        $parecerProjeto = $_POST['parecerProjeto'];
        $dsParecer = $_POST['dsParecer'];
        $campoTipoParecer = 8;
        $vlPlanilha = 0;

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosRead = $tbReadequacao->buscar(array('idReadequacao=?' => $idReadequacao))->current();

        //SE FOR READEQUAÇÃO DE PLANILHA ORÇAMENTÁRIA, O CAMPO TipoParecer DA TABELA SAC.dbo.Parecer MUDAR.
        if ($dadosRead->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
            $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

            //BUSCAR VALOR TOTAL DA PLANILHA ATIVA
            $where = array();
            $where['a.IdPRONAC = ?'] = $idPronac;
            $where['a.stAtivo = ?'] = 'S';
            $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            //BUSCAR VALOR TOTAL DA PLANILHA DE READEQUADA
            $where = array();
            $where['a.IdPRONAC = ?'] = $idPronac;
            $where['a.tpPlanilha = ?'] = 'SR';
            $where['a.stAtivo = ?'] = 'N';
            $PlanilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

            if ($PlanilhaAtiva->Total > $PlanilhaReadequada->Total) {
                $vlPlanilha = $PlanilhaAtiva->Total - $PlanilhaReadequada->Total;
                $campoTipoParecer = 4;
            } else {
                $vlPlanilha = $PlanilhaReadequada->Total - $PlanilhaAtiva->Total;
                $campoTipoParecer = 2;
            }
        }

        if ($dadosRead->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) {
            $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
            $tbPlanoDistribuicaoMapper->atualizarAnaliseTecnica($idPronac, $idReadequacao, $this->idPerfil, $parecerProjeto);
        }

        try {
            $Projetos = new Projetos();
            $enquadramentoDAO = new Admissibilidade_Model_Enquadramento();
            $buscaEnquadramento = $enquadramentoDAO->buscarDados($idPronac, null, false);

            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac));
            if (count($dadosProjeto) > 0) {

                //CADASTRA OU ATUALIZA O PARECER DO TECNICO
                $parecerDAO = new Parecer_Model_DbTable_Parecer();
                $dadosParecer = array(
                    'idPRONAC' => $idPronac,
                    'AnoProjeto' => $dadosProjeto[0]->AnoProjeto,
                    'Sequencial' => $dadosProjeto[0]->Sequencial,
                    'TipoParecer' => $campoTipoParecer,
                    'ParecerFavoravel' => $parecerProjeto,
                    'DtParecer' => MinC_Db_Expr::date(),
                    'NumeroReuniao' => null,
                    'ResumoParecer' => $dsParecer,
                    'SugeridoReal' => $vlPlanilha,
                    'Atendimento' => 'S',
                    'idEnquadramento' => $buscaEnquadramento['IdEnquadramento'],
                    'stAtivo' => 1,
                    'idTipoAgente' => 6, //Componente da Comissão
                    'Logon' => $this->idUsuario
                );

//                foreach ($dadosParecer as $dp) {
                $parecerAntigo = array(
                    'Atendimento' => 'S',
                    'stAtivo' => 0
                );
                $whereUpdateParecer = 'IdPRONAC = ' . $idPronac;
                $alteraParecer = $parecerDAO->alterar($parecerAntigo, $whereUpdateParecer);
//                }

                $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
                $buscarParecer = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?' => $idReadequacao, 'b.idTipoAgente =?' => 6))->current();
                if ($buscarParecer) {
                    $whereUpdateParecer = 'IdParecer = ' . $buscarParecer->IdParecer;
                    $parecerDAO->alterar($dadosParecer, $whereUpdateParecer);
                    $idParecer = $buscarParecer->IdParecer;
                } else {
                    $idParecer = $parecerDAO->inserir($dadosParecer);
                }

                $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
                $parecerReadequacao = $tbReadequacaoXParecer->buscar(array('idReadequacao = ?' => $idReadequacao, 'idParecer =?' => $idParecer));
                if (count($parecerReadequacao) == 0) {
                    $dadosInclusao = array(
                        'idReadequacao' => $idReadequacao,
                        'idParecer' => $idParecer
                    );
                    $tbReadequacaoXParecer->inserir($dadosInclusao);
                }
            }

            if (isset($_POST['finalizarAvaliacao']) && $_POST['finalizarAvaliacao'] == 1) {
                $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
                $dDP = $tbDistribuirReadequacao->buscar(array('idReadequacao = ?' => $idReadequacao, 'idUnidade =?' => 400, 'idAvaliador=?' => $this->idUsuario));

                if (count($dDP) > 0) {
                    //ATUALIZA A TABELA tbDistribuirReadequacao
                    $dadosDP = array();
                    $dadosDP['DtRetornoAvaliador'] = new Zend_Db_Expr('GETDATE()');
                    $whereDP = "idDistribuirReadequacao = " . $dDP[0]->idDistribuirReadequacao;
                    $x = $tbDistribuirReadequacao->update($dadosDP, $whereDP);

                    $reuniao = new Reuniao();
                    $raberta = $reuniao->buscarReuniaoAberta();
                    $idNrReuniao = ($raberta['stPlenaria'] == 'A') ? $raberta['idNrReuniao'] + 1 : $raberta['idNrReuniao'];

                    $read = $tbReadequacao->buscarReadequacao(array('idReadequacao =?' => $idReadequacao))->current();

                    $stEstado = 0;
                    if ($_POST['plenaria']) {
                        $campoSiEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_PLENARIA;
                    } else {
                        $campoSiEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CHECKLIST_PUBLICACAO;
                        if (!in_array(
                            $read->idTipoReadequacao,
                            array(
                                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA,
                                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL,
                                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE,
                                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO,
                                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO
                            )
                        )) {
                            $campoSiEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_FINALIZADA_SEM_PORTARIA;
                            $stEstado = 1;
                        }
                    }

                    //ATUALIZA A TABELA Readequacao_Model_DbTable_TbReadequacao
                    $dados = array();
                    $dados['siEncaminhamento'] = $campoSiEncaminhamento; // Devolvido da análise técnica
                    $dados['idNrReuniao'] = $idNrReuniao;
                    $dados['stEstado'] = $stEstado;

                    $dados['stAnalise'] = 'IC';
                    if ($parecerProjeto == 2) {
                        $dados['stAnalise'] = 'AC';
                    }

                    $where = "idReadequacao = $idReadequacao";
                    $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
                    $tbReadequacao->update($dados, $where);

                    if ($campoSiEncaminhamento != Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_PLENARIA) {

                        if ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) {
                            $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
                            $tbPlanoDistribuicaoMapper->finalizarAnaliseReadequacaoPlanoDistribuicao($idPronac, $idReadequacao, $parecerProjeto);
                        }

                        if ($parecerProjeto == 2) { //Se for parecer favorável, atualiza os dados solicitados na readequação

                            if ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

                                $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                                //BUSCAR VALOR TOTAL DA PLANILHA ATIVA
                                $where = array();
                                $where['a.IdPRONAC = ?'] = $read->idPronac;
                                $where['a.stAtivo = ?'] = 'S';
                                $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

                                //BUSCAR VALOR TOTAL DA PLANILHA DE READEQUADA
                                $where = array();
                                $where['a.IdPRONAC = ?'] = $read->idPronac;
                                $where['a.tpPlanilha = ?'] = 'SR';
                                $where['a.stAtivo = ?'] = 'N';
                                $PlanilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

                                if ($PlanilhaAtiva->Total < $PlanilhaReadequada->Total) {
                                    $TipoAprovacao = Aprovacao::TIPO_APROVACAO_COMPLEMENTACAO;
                                    $dadosPrj->Situacao = 'D28';
                                } else {
                                    $TipoAprovacao = Aprovacao::TIPO_APROVACAO_REDUCAO;
                                    $dadosPrj->Situacao = 'D29';
                                }
                                $dadosPrj->save();

                                $AprovadoReal = number_format(($PlanilhaReadequada->Total - $PlanilhaAtiva->Total), 2, '.', '');
                                $tbAprovacao = new Aprovacao();
                                $dadosAprovacao = array(
                                    'IdPRONAC' => $read->idPronac,
                                    'AnoProjeto' => $dadosPrj->AnoProjeto,
                                    'Sequencial' => $dadosPrj->Sequencial,
                                    'TipoAprovacao' => $TipoAprovacao,
                                    'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                                    'ResumoAprovacao' => 'Parecer favorável para readequação',
                                    'AprovadoReal' => $AprovadoReal,
                                    'Logon' => $this->idUsuario,
                                    'idReadequacao' => $idReadequacao
                                );
                                $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL) { //Se for readequação de alteração de razão social, atualiza os dados na AGENTES.dbo.Nomes.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

                                $tbAprovacao = new Aprovacao();
                                $dadosAprovacao = array(
                                    'IdPRONAC' => $read->idPronac,
                                    'AnoProjeto' => $dadosPrj->AnoProjeto,
                                    'Sequencial' => $dadosPrj->Sequencial,
                                    'TipoAprovacao' => Aprovacao::TIPO_APROVACAO_READEQUACAO,
                                    'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                                    'ResumoAprovacao' => 'Parecer favorável para readequação',
                                    'Logon' => $this->idUsuario,
                                    'idReadequacao' => $idReadequacao
                                );
                                $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_AGENCIA_BANCARIA) { //Se for readequação de agência bancária, atualiza os dados na SAC.dbo.PreProjeto.// READEQUAÇÃO DE ALTERAÇÃO DE RAZÃO SOCIAL
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();
                                $agenciaBancaria = str_replace('-', '', $read->dsSolicitacao);

                                $tblContaBancaria = new ContaBancaria();
                                $arrayDadosBancarios = array(
                                    'Agencia' => $agenciaBancaria,
                                    'ContaBloqueada' => '000000000000',
                                    'DtLoteRemessaCB' => null,
                                    'LoteRemessaCB' => '00000',
                                    'OcorrenciaCB' => '000',
                                    'ContaLivre' => '000000000000',
                                    'DtLoteRemessaCL' => null,
                                    'LoteRemessaCL' => '00000',
                                    'OcorrenciaCL' => '000',
                                    'Logon' => $this->idUsuario,
                                    'idPronac' => $read->idPronac
                                );
                                $whereDadosBancarios['AnoProjeto = ?'] = $dadosPrj->AnoProjeto;
                                $whereDadosBancarios['Sequencial = ?'] = $dadosPrj->Sequencial;
                                $tblContaBancaria->alterar($arrayDadosBancarios, $whereDadosBancarios);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SINOPSE_OBRA) { //Se for readequação de sinopse da obra, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->Sinopse = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_IMPACTO_AMBIENTAL) { //Se for readequação de impacto ambiental, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->ImpactoAmbiental = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESPECIFICACAO_TECNICA) { //Se for readequação de especificação técnica, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->EspecificacaoTecnica = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESTRATEGIA_EXECUCAO) { //Se for readequação de estratégia de execução, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->EstrategiadeExecucao = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                                // READEQUAÇÃO DE LOCAL DE REALIZAÇÃO
                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO) { //Se for readequação de local de realização, atualiza os dados na SAC.dbo.Abrangencia.
                                $Abrangencia = new Proposta_Model_DbTable_Abrangencia();
                                $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
                                $abrangencias = $tbAbrangencia->buscar(array('idReadequacao=?' => $idReadequacao));
                                foreach ($abrangencias as $abg) {
                                    $Projetos = new Projetos();
                                    $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                    //Se não houve avalição do conselheiro, pega a avaliação técnica como referencia.
                                    $avaliacao = $abg->tpAnaliseComissao;
                                    if ($abg->tpAnaliseComissao == 'N') {
                                        $avaliacao = $abg->tpAnaliseTecnica;
                                    }

                                    //Se a avaliação foi deferida, realiza as mudanças necessárias na tabela original.
                                    if ($avaliacao == 'D') {
                                        if ($abg->tpSolicitacao == 'E') { //Se a abrangencia foi excluída, atualiza os status da abrangencia na SAC.dbo.Abrangencia
                                            $Abrangencia->delete(array('idProjeto = ?' => $dadosPrj->idProjeto, 'idPais = ?' => $abg->idPais, 'idUF = ?' => $abg->idUF, 'idMunicipioIBGE = ?' => $abg->idMunicipioIBGE));
                                        } elseif ($abg->tpSolicitacao == 'I') { //Se a abangência foi incluída, cria um novo registro na tabela SAC.dbo.Abrangencia
                                            $novoLocalRead = array();
                                            $novoLocalRead['idProjeto'] = $dadosPrj->idProjeto;
                                            $novoLocalRead['idPais'] = $abg->idPais;
                                            $novoLocalRead['idUF'] = $abg->idUF;
                                            $novoLocalRead['idMunicipioIBGE'] = $abg->idMunicipioIBGE;
                                            $novoLocalRead['Usuario'] = $this->idUsuario;
                                            $novoLocalRead['stAbrangencia'] = 1;
                                            $Abrangencia->salvar($novoLocalRead);
                                        }
                                    }
                                }

                                $dadosAbr = array();
                                $dadosAbr['stAtivo'] = 'N';
                                $whereAbr = "idPronac = $read->idPronac AND idReadequacao = $idReadequacao";
                                $tbAbrangencia->update($dadosAbr, $whereAbr);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE) { //Se for readequação de alteração de proponente, atualiza os dados na SAC.dbo.Projetos.

                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

                                $tbAprovacao = new Aprovacao();
                                $dadosAprovacao = array(
                                    'IdPRONAC' => $read->idPronac,
                                    'AnoProjeto' => $dadosPrj->AnoProjeto,
                                    'Sequencial' => $dadosPrj->Sequencial,
                                    'TipoAprovacao' => Aprovacao::TIPO_APROVACAO_READEQUACAO,
                                    'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                                    'ResumoAprovacao' => 'Parecer favorável para readequação',
                                    'Logon' => $this->idUsuario,
                                    'idReadequacao' => $idReadequacao
                                );
                                $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO) { //Se for readequação de nome do projeto, insere o registo na tela de Checklist de Publicação.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

                                $tbAprovacao = new Aprovacao();
                                $dadosAprovacao = array(
                                    'IdPRONAC' => $read->idPronac,
                                    'AnoProjeto' => $dadosPrj->AnoProjeto,
                                    'Sequencial' => $dadosPrj->Sequencial,
                                    'TipoAprovacao' => Aprovacao::TIPO_APROVACAO_READEQUACAO,
                                    'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                                    'ResumoAprovacao' => 'Parecer favorável para readequação',
                                    'Logon' => $this->idUsuario,
                                    'idReadequacao' => $idReadequacao
                                );
                                $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PERIODO_EXECUCAO) { //Se for readequação de período de execução, atualiza os dados na SAC.dbo.Projetos.
                                $dtFimExecucao = Data::dataAmericana($read->dsSolicitacao);
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();
                                $dadosPrj->DtFimExecucao = $dtFimExecucao;
                                $dadosPrj->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DIVULGACAO) { //Se for readequação de plano de divulgacao, atualiza os dados na SAC.dbo.PlanoDeDivulgacao.
                                $PlanoDeDivulgacao = new PlanoDeDivulgacao();
                                $tbPlanoDivulgacao = new tbPlanoDivulgacao();
                                $planosDivulgacao = $tbPlanoDivulgacao->buscar(array('idReadequacao=?' => $idReadequacao));

                                foreach ($planosDivulgacao as $plano) {
                                    $Projetos = new Projetos();
                                    $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                    //Se não houve avalição do conselheiro, pega a avaliação técnica como referencia.
                                    $avaliacao = $plano->tpAnaliseComissao;
                                    if ($plano->tpAnaliseComissao == 'N') {
                                        $avaliacao = $plano->tpAnaliseTecnica;
                                    }

                                    //Se a avaliação foi deferida, realiza as mudanças necessárias na tabela original.
                                    if ($avaliacao == 'D') {
                                        if ($plano->tpSolicitacao == 'E') { //Se o plano de divulgação foi excluído, atualiza os status do plano na SAC.dbo.PlanoDeDivulgacao
                                            $PlanoDivulgacaoEmQuestao = $PlanoDeDivulgacao->buscar(array('idProjeto = ?' => $dadosPrj->idProjeto, 'idPeca = ?' => $plano->idPeca, 'idVeiculo = ?' => $plano->idVeiculo))->current();
                                            $tbLogomarca = new tbLogomarca();
                                            $dadosLogomarcaDaDivulgacao = $tbLogomarca->buscar(array('idPlanoDivulgacao = ?' => $PlanoDivulgacaoEmQuestao->idPlanoDivulgacao))->current();
                                            if (!empty($dadosLogomarcaDaDivulgacao)) {
                                                $dadosLogomarcaDaDivulgacao->delete();
                                            }
                                            if (!empty($PlanoDivulgacaoEmQuestao)) {
                                                $PlanoDivulgacaoEmQuestao->delete();
                                            }
                                        } elseif ($plano->tpSolicitacao == 'I') { //Se o plano de divulgação foi incluído, cria um novo registro na tabela SAC.dbo.PlanoDeDivulgacao
                                            $novoPlanoDivRead = array();
                                            $novoPlanoDivRead['idProjeto'] = $dadosPrj->idProjeto;
                                            $novoPlanoDivRead['idPeca'] = $plano->idPeca;
                                            $novoPlanoDivRead['idVeiculo'] = $plano->idVeiculo;
                                            $novoPlanoDivRead['Usuario'] = $this->idUsuario;
                                            $novoPlanoDivRead['siPlanoDeDivulgacao'] = 0;
                                            $novoPlanoDivRead['idDocumento'] = null;
                                            $novoPlanoDivRead['stPlanoDivulgacao'] = 1;
                                            $PlanoDeDivulgacao->inserir($novoPlanoDivRead);
                                        }
                                    }
                                }

                                $dadosPDD = array();
                                $dadosPDD['stAtivo'] = 'N';
                                $wherePDD = "idPronac = $read->idPronac AND idReadequacao = $idReadequacao";
                                $tbPlanoDivulgacao->update($dadosPDD, $wherePDD);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO) { //Se for readequação de resumo do projeto, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

                                $tbAprovacao = new Aprovacao();
                                $dadosAprovacao = array(
                                    'IdPRONAC' => $read->idPronac,
                                    'AnoProjeto' => $dadosPrj->AnoProjeto,
                                    'Sequencial' => $dadosPrj->Sequencial,
                                    'TipoAprovacao' => Aprovacao::TIPO_APROVACAO_READEQUACAO,
                                    'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                                    'ResumoAprovacao' => 'Parecer favorável para readequação',
                                    'Logon' => $this->idUsuario,
                                    'idReadequacao' => $idReadequacao
                                );
                                $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_OBJETIVOS) { //Se for readequação de objetivos, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->Objetivos = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_JUSTIFICATIVA) { //Se for readequação de justificativa, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->Justificativa = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ACESSIBILIDADE) { //Se for readequação de acesibilidade, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->Acessibilidade = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_DEMOCRATIZACAO_ACESSO) { //Se for readequação de democratização de acesso, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->DemocratizacaoDeAcesso = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ETAPAS_TRABALHO) { //Se for readequação de etapas de trabalho, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->EtapaDeTrabalho = $read->dsSolicitacao;
                                $dadosPreProjeto->save();

                            } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_FICHA_TECNICA) { //Se for readequação de ficha técnica, atualiza os dados na SAC.dbo.PreProjeto.
                                $Projetos = new Projetos();
                                $dadosPrj = $Projetos->buscar(array('IdPRONAC=?' => $read->idPronac))->current();

                                $PrePropojeto = new Proposta_Model_DbTable_PreProjeto();
                                $dadosPreProjeto = $PrePropojeto->find(array('idPreProjeto=?' => $dadosPrj->idProjeto))->current();
                                $dadosPreProjeto->FichaTecnica = $read->dsSolicitacao;
                                $dadosPreProjeto->save();
                            }
                        }

                        //Atualiza a tabela tbDistribuirReadequacao
                        $dados = array();
                        $dados['stValidacaoCoordenador'] = 1;
                        $dados['DtValidacaoCoordenador'] = new Zend_Db_Expr('GETDATE()');
                        $dados['idCoordenador'] = $this->idUsuario;
                        $where = "idReadequacao = $idReadequacao";
                        $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
                        $tbDistribuirReadequacao->update($dados, $where);
                    }

                    parent::message("A avalia&ccedil;&atilde;o da readequa&ccedil;&atilde;o foi finalizada com sucesso!", "readequacao/readequacoes/analisar-readequacoes-cnic", "CONFIRM");
                } else {
                    parent::message("Erro ao avaliar a readequa&ccedil;&atilde;o!", "readequacao/readequacoes/form-avaliar-readequacao-cnic?id=$idReadequacao", "ERROR");
                }
            }
            $idReadequacao = Seguranca::encrypt($idReadequacao);
            parent::message("Dados salvos com sucesso!", "readequacao/readequacoes/form-avaliar-readequacao-cnic?id=$idReadequacao", "CONFIRM");
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "readequacao/readequacoes/form-avaliar-readequacao-cnic?id=$idReadequacao", "ERROR");
        }
    }

    public function isProjetoTransicaoIn2017($idPronac)
    {
        $fnDtAvaliacaoAdequacao = new fnDtAvaliacaoAdequacao();
        $dtAvaliacaoAdequacao = $fnDtAvaliacaoAdequacao->getDtAvaliacaoAdequacao($idPronac);
        
        if (!empty($dtAvaliacaoAdequacao)) {
            $dataMin = '2017-01-01';
            $dataMax = '2018-17-10';

            preg_match('/(\d{4}\-\d{2}\-\d{2})/', $dtAvaliacaoAdequacao[0]->computed, $date_match);
            $diaAvaliacao = $date_match[0];
            
            if ($diaAvaliacao > $dataMin &&
                $diaAvaliacao < $dataMax) {
            
                return true;
            }
        }
        return false;
    }
    
    /*
     * Página de criação de planilha orçamentária
     * Criada em 02/06/2016
     * @author: Fernão Lopes Ginez de Lara fernao.lara@cultura.gov.br
     * @access public
     * @return void
     */
    public function planilhaOrcamentariaAction()
    {
        $this->view->idPerfil = $this->idPerfil;
        if ($this->idPerfil != Autenticacao_Model_Grupos::PROPONENTE) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        // verificação adicional na pagina se pode incluir nova planilha orcamentaria
        $auth = Zend_Auth::getInstance(); // pega a autenticacao

        $proj = new Projetos();
        $cpf = $proj->buscarProponenteProjeto($idPronac);
        $cpf = $cpf->CgcCpf;
        $idUsuarioLogado = $auth->getIdentity()->IdUsuario;

        $mensagemCustosVinculados = "";
        if ($this->isProjetoTransicaoIn2017($idPronac)) {
            // fazer a comparacao com a data
            $mensagemCustosVinculados = "<div align='center'><strong>ATEN&Ccedil;&Atilde;O:</strong><br/> Projetos adequados &agrave; realidade de execu&ccedil;&atilde;o at&eacute; a data de <strong>26/10/2018</strong> ter&atilde;o seus custos vinculados e de remunera&ccedil;&atilde;o de capta&ccedil;&atilde;o de recursos recalculados no momento em que for realizada uma readequa&ccedil;&atilde;o.</div>";
        }
        $this->view->mensagemCustosVinculados = $mensagemCustosVinculados;

        $links = new fnLiberarLinks();
        $linksXpermissao = $links->links(2, $cpf, $idUsuarioLogado, $idPronac);
        $linksPermissao = str_replace(' ', '', explode('-', $linksXpermissao->links));
        $permiteReadequacaoPlanilha = $linksPermissao[14];

        if (!$permiteReadequacaoPlanilha) {
            parent::message("Voc&ecirc;  n&atilde;o pode realizar uma nova readequa&ccedil;&atilde;o.", "principalproponente", "ERROR");
        }

        $this->view->idPronac = $idPronac;
        if (!empty($idPronac)) {
            $Projetos = new Projetos();
            $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();

            $tbVerificao = new Proposta_Model_DbTable_Verificacao();
            $buscarRecurso = $tbVerificao->buscarFonteRecurso();
            $this->view->Recursos = $buscarRecurso;

            $tbuf = new Agente_Model_DbTable_UF();

            $buscarEstado = $tbuf->buscar();
            $this->view->UFs = $buscarEstado;

            $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
            $this->view->Produtos = $PlanoDistribuicaoProduto->comboProdutosParaInclusaoReadequacao($idPronac);

            $spSelecionarEtapa = new spSelecionarEtapa();
            $etapas = $spSelecionarEtapa->exec($idPronac);
            $etapas = array_slice($etapas, 0, 5);
            $this->view->Etapas = $etapas;
            
            $TbPlanilhaUnidade = new Proposta_Model_DbTable_TbPlanilhaUnidade();
            $buscarUnidade = $TbPlanilhaUnidade->buscarUnidade();
            $this->view->Unidade = $buscarUnidade;

            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $this->view->readequacao = $tbReadequacao->readequacoesCadastradasProponente(array(
                'a.idPronac = ?' => $idPronac,
                'a.siEncaminhamento = ?' => Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
                'a.idTipoReadequacao = ?' => Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA,
                'a.stEstado = ?' => Readequacao_Model_DbTable_TbReadequacao::ST_ESTADO_EM_ANDAMENTO
            ), array(1));
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "principalproponente", "ERROR");
        }
    }


    /**
     * Função para verificar e criar planilha orçamentária. Recebe flag opcional para criar a planilha
     * Criada em 31/05/2016
     * @author: Fernão Lopes Ginez de Lara fernao.lara@cultura.gov.br
     * @access public
     * @return Bool   True se foi possível criar a planilha ou se ela existe
     */
    public function verificarPlanilhaAtivaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $idPronac = $this->_request->getParam('idPronac');
        $idReadequacao = $this->_request->getParam('idReadequacao');

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();

        if (!$idReadequacao || $idReadequacao == 0) {
            $idReadequacao = $tbReadequacao->criarReadequacaoPlanilha($idPronac, Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);

            $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

            $verificarPlanilhaReadequadaAtual = $tbPlanilhaAprovacao->buscarPlanilhaReadequadaEmEdicao($idPronac, $idReadequacao);

            if (count($verificarPlanilhaReadequadaAtual) == 0) {
                $planilhaAtiva = $tbPlanilhaAprovacao->buscarPlanilhaAtiva($idPronac);
                $criarPlanilha = $tbPlanilhaAprovacao->copiarPlanilhas($idPronac, $idReadequacao);

                if ($criarPlanilha) {
                    $this->_helper->json(array(
                        'msg' => 'Planilha copiada corretamente',
                        'idReadequacao' => $idReadequacao
                    ));
                } else {
                    $this->_helper->json(array(
                        'msg' => 'Houve um erro ao tentar copiar a planilha',
                        'idReadequacao' => $idReadequacao
                    ));
                }
            }
        } else {
            $this->_helper->json(array(
                'msg' => 'OK - planilha existe',
                'idReadequacao' => $idReadequacao
            ));
        }
    }

    /*
     * consultarReadequacaoAction Pagina acessada por ajax que retorna dados de readequao
     * @since  13/06/2016
     * @author Fernao Lopes Ginez de Lara fernao.lara@cultura.gov.br
     * @access public
     * @return Mixed Retorna json object com readequao
     */
    public function consultarReadequacaoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $idPronac = $this->_request->getParam("idPronac");
        $idReadequacao = $this->_request->getParam("idReadequacao");
        $idTipoReadequacao = $this->_request->getParam("idTipoReadequacao");

        $where = array();
        $where['a.idPronac = ?'] = $idPronac;

        if ($idReadequacao) {
            $where['a.idReadequacao = ?'] = $idReadequacao;
        }
        if ($idTipoReadequacao) {
            $where['a.idTipoReadequacao = ?'] = $idTipoReadequacao;
        }

        try {
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $readequacoes = $tbReadequacao->buscarDadosReadequacoes($where);

            // prepara sada json
            $output = array();
            foreach ($readequacoes as $readequacao) {
                $output[] = array(
                    'idReadequacao' => $readequacao->idReadequacao,
                    'idPronac' => $readequacao->idPronac,
                    'dtSolicitacao' => $readequacao->dtSolicitacao,
                    'dsSolicitacao' => $readequacao->dsSolicitacao,
                    'dsJustificativa' => $readequacao->dsJustificativa
                );
            }

            $this->_helper->json(array('readequacoes' => $output));
        } catch (Zend_Exception $e) {
            $this->_helper->json(array('msg' => 'Registro no localizado'));
        }
    }

    /*
     * verificarLimitesOrcamentarios Consulta ajax para verficiar limites orçamentários de readequação
     * @since  31/08/2016
     * @author Fernao Lopes Ginez de Lara fernao.lara@cultura.gov.br
     * @access public
     * @return Mixed Retorna json object com mensagem
     */
    public function verificarLimitesOrcamentariosAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $pa = new spChecarLimitesOrcamentarioReadequacao();
        $resultadoCheckList = $pa->exec($idPronac, 3);

        $resultado = array();
        $i = 0;

        $mensagem = array(
            'custo_administrativo' => array(
                'OK' => "O custo administrativo inferior a 15% do valor total do projeto.",
                'PENDENTE' => "O custo administrativo supera 15% do valor total do projeto. Para corrigir, reduza o valor da etapa em R$ ",
            ),
            'remuneracao' => array(
                'OK' => "Remunera&ccedil;&atilde;o para capta&ccedil;&atilde;o de recursos est&aacute; dentro dos par&acirc;metros permitidos.",
                'PENDENTE' => "A remunera&ccedil;&atilde;o para capta&ccedil;&atilde;o de recursos supera 10% do valor do projeto ou R$ 100.000,00. O valor correto &eacute; R$ "
            ),
            'divulgacao' => array(
                'OK' => "Divulga&ccedil;&atilde;o / Comercializa&ccedil;&atilde;o est&aacute; dentro dos par&acirc;metros permitidos.",
                'PENDENTE' => "A divulga&ccedil;&atilde;o ou a comercializa&ccedil;&atilde;o supera 20%. Para corrigir, reduza o valor da etapa em R$ "
            )
        );

        foreach ($resultadoCheckList as $item) {
            $resultado[$i]['idPronac'] = $item->idPronac;
            $resultado[$i]['Descricao'] = utf8_encode($mensagem[$item->Tipo][$item->Observacao]);
            $resultado[$i]['vlDiferenca'] = $item->vlDiferenca;
            $resultado[$i]['Observacao'] = $item->Observacao;
            $i++;
        }
        $this->_helper->json(array('mensagens' => $resultado));
    }


    public function obterDadosReadequacaoAction()
    {
        $this->_helper->layout->disableLayout();

        $httpCode = 200;
        
        $idTipoReadequacao = $this->_request->getParam('idTipoReadequacao');
        $idReadequacao = $this->_request->getParam('idReadequacao');
        $idPronac = $this->_request->getParam('idPronac');
        $siEncaminhamento = $this->_request->getParam('siEncaminhamento');
        
        try {
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $readequacao = $tbReadequacao->obterDadosReadequacao(
                $idTipoReadequacao,
                $idPronac,
                $idReadequacao,
                $siEncaminhamento
            );

            if (empty($readequacao)) {
                $mensagem = 'Nenhuma readequa&ccedil;&atilde;o para o idPronac ' . $this->idPronac;
                $readequacao = [];
            }

            $this->getResponse()->setHttpResponseCode($httpCode);
            $this->_helper->json(
                [
                    'readequacao' => array_map('utf8_encode', $readequacao),
                    'success' => true,
                    'msg' => $mensagem
                ]
            );
        } catch (Exception $objException) {
            $this->getResponse()->setHttpResponseCode(412);

            $this->_helper->json(
                [
                    'readequacao' => [],
                    'sucess' => false,
                    'msg' => 'N&atilde;o foi poss&iacute;vel obter a readequa&ccedil;&atilde;o. Erro: ' . $objException->getMessage()
                ]
            );
        }
    }

    public function excluirDocumentoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $idPronac = $this->_request->getParam('idPronac');
        $idDocumento = $this->_request->getParam('idDocumento');
        $idReadequacao = $this->_request->getParam('idReadequacao');

        try {
            $tbDocumento = new tbDocumento();
            $tbDocumento->excluirDocumento($idDocumento);

            $arrData = [];
            $arrData['idPronac'] = $idPronac;
            $arrData['idDocumento'] = null;
            $arrData['idReadequacao'] = $idReadequacao;

            $TbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
            $idReadequacao = $TbReadequacaoMapper->salvarSolicitacaoReadequacao($arrData);

            $this->_helper->json([
                'mensagem' => utf8_encode('Documento excluído com sucesso.'),
            ]);

        } catch (Exception $objException) {
            $this->getResponse()->setHttpResponseCode(412);
            $this->_helper->json([
                'mensagem' => utf8_encode('Houve um erro ao excluir o documento.'),
                'error' => $objException->getMessage()
            ]);
        }
    }

    public function salvarDocumentoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $idPronac = $this->_request->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        
        $idDocumentoAtual = $this->_request->getParam('idDocumentoAtual');
        $idReadequacao = $this->_request->getParam('idReadequacao');
        $idTipoReadequacao = $this->_request->getParam('idTipoReadequacao');

        $mensagem = '';
        $dados = [];

        try {
            if ($idDocumentoAtual) {
                $tbDocumento = new tbDocumento();
                $tbDocumento->excluirDocumento($idDocumentoAtual);
            }

            $TbReadequacao_DbTable = new Readequacao_Model_DbTable_TbReadequacao();
            $documento = $TbReadequacao_DbTable->inserirDocumento();

            $arrData = [];
            $arrData['idPronac'] = $idPronac;
            $arrData['idTipoReadequacao'] = $idTipoReadequacao;
            $arrData['idDocumento'] = $documento['idDocumento'];

            if ($idReadequacao) {
                $arrData['idReadequacao'] = $idReadequacao;
            } else {
                $arrData['dsJustificativa'] = '';
            }

            $TbReadequacaoMapper = new Readequacao_Model_TbReadequacaoMapper();
            $idReadequacao = $TbReadequacaoMapper->salvarSolicitacaoReadequacao($arrData);

            $this->_helper->json(
                [
                    'documento' => [
                        'idDocumento' => $documento['idDocumento'],
                        'nomeArquivo' => utf8_encode($documento['nomeArquivo'])
                    ],
                    'readequacao' => [
                        'idReadequacao' => $idReadequacao
                    ],
                    'mensagem' => 'Arquivo inserido com sucesso.'
                ]
            );
        } catch (Exception $objException) {
            $this->_helper->json([
                'mensagem' => utf8_encode('Houve um erro ao subir o documento.'),
                'error' => $objException->getMessage()
            ]);
        }
    }

    public function abrirDocumentoReadequacaoAction()
    {
        $idDocumento = $this->getRequest()->getParam('id', null);

        try {
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $readequacao = $tbReadequacao->findBy(
                ['idDocumento = ?' => $idDocumento]
            );

            if (empty($readequacao))
                throw new Exception('Documento n&atilde;o encontrado!');

            $permissao = parent::verificarPermissaoAcesso(false, $readequacao['idPronac'], false, true);

            if ($permissao['status'] === false)
                throw new Exception('Voc&ecirc; n&atilde;o tem permiss&atilde;o para baixar esse arquivo!');

            parent::abrirDocumento($idDocumento);

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function encaminharParaCnicAction()
    {

        if ($this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO
            && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO
            && $this->idPerfil != Autenticacao_Model_Grupos::PRESIDENTE_VINCULADA_SUBSTITUTO
            && $this->idPerfil != Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idReadequacao = Seguranca::dencrypt($this->_request->getParam('id'));
        $cnic = $this->_request->getParam('cnic');

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $readequacao = $tbReadequacao->buscarDadosReadequacoes(array('idReadequacao = ?' => $idReadequacao))->current();

        if ($readequacao->siEncaminhamento <> '10') {
            parent::message("Este projeto n&atilde;o pode ser encaminhado!", "/readequacao/readequacoes/painel", "ERROR");
        }

        if ($readequacao) {
            $Projetos = new Projetos();
            $dadosProjetos = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $readequacao->idPronac))->current();

            $this->view->filtro = $this->_request->getParam('filtro');
            $this->view->readequacao = $readequacao;
            $this->view->projeto = $dadosProjetos;
            $this->view->idPronac = $readequacao->idPronac;
            $this->view->cnic = $cnic;
        }

        $this->view->idReadequacao = $idReadequacao;

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $this->view->conselheiros = $tbTitulacaoConselheiro->buscarConselheirosTitularesTbUsuarios();
    }
    


    public function obterPlanilhaOrcamentariaAction()
    {
        $this->_helper->layout->disableLayout();

        $idPronac = $this->_request->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $tipoPlanilha = $this->_request->getParam('tipoPlanilha');

        $params = [];
        $params['link'] = $this->_request->getParam('link');

        try {
            if (empty($idPronac)) {
                throw new Exception("N&uacute;mero do idPronac &eacute; obrigat&oacute;ria");
            }
            $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
            $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, $tipoPlanilha, $params);
            $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoPlanilha);
            $planilhaPreparada = TratarArray::utf8EncodeArray($planilha);

            $this->_helper->json([
                'planilhaOrcamentaria' => $planilhaPreparada,
                'success' => 'true'
            ]);
        } catch (Exception $e) {
            $this->_helper->json([
                'success' => 'false',
                'msg' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

}
