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
                    'a.siEncaminhamento = ?' => Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE,
                    'a.stEstado = ?' => 0,
                    'b.stEstado = ?' => Readequacao_Model_TbTipoReadequacao::TIPO_READEQUACAO_ATIVO,
                    'b.siReadequacao = ?' => Readequacao_Model_TbTipoReadequacao::SI_READEQUACAO_DIVERSA
                ),
                array(1)
            );
        } else {
            parent::message("N&uacute;mero Pronac inv&aacute;lido!", "principalproponente", "ERROR");
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
            $dadosInclusao['tpPlanilha'] = 'SR';
            $dadosInclusao['dtPlanilha'] = new Zend_Db_Expr('GETDATE()');
            $dadosInclusao['IdPRONAC'] = $idPronac;
            $dadosInclusao['idProduto'] = $idProduto;
            $dadosInclusao['idEtapa'] = $idEtapa;
            $dadosInclusao['idPlanilhaItem'] = $idPlanilhaItem;
            $dadosInclusao['dsItem'] = '';
            $dadosInclusao['idUnidade'] = $this->_request->getParam("newUnidade");
            $dadosInclusao['qtItem'] = $this->_request->getParam("newQuantidade");
            $_POST['newQuantidade'];
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
            $dadosInclusao['idReadequacao'] = $this->_request->getParam("idReadequacao");

            $insert = $tbPlanilhaAprovacao->inserir($dadosInclusao);

            if ($insert) {
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

        $editarItem->idUnidade = $this->_request->getParam('Unidade');
        $editarItem->qtItem = $this->_request->getParam('Quantidade');
        $editarItem->nrOcorrencia = $this->_request->getParam('Ocorrencia');
        $editarItem->vlUnitario = $ValorUnitario;
        $editarItem->qtDias = $this->_request->getParam('QtdeDias');
        $editarItem->dsJustificativa = utf8_decode($this->_request->getParam('Justificativa'));
        $editarItem->idAgente = $idAgente;
        if ($editarItem->tpAcao == 'N') {
            $editarItem->tpAcao = 'A';
        }

//        $editarItem->idAgente = $auth->getIdentity()->IdUsuario;
        $editarItem->save();

        $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
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
            $editarItem->tpAcao = 'N';
        }
        $editarItem->save();

        $this->_helper->json(array('resposta' => true, 'msg' => 'Dados salvos com sucesso!'));
        $this->_helper->viewRenderer->setNoRender(true);
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

    public function criarCampoTipoReadequacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $get = Zend_Registry::get('get');
        $tipoReadequacao = $get->tpReadequacao;
        $valorPreCarregado = null;

        if ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscarDadosUC75($get->idPronac)->current();

            if ($dadosProjeto) {
                $valorPreCarregado = $dadosProjeto->Proponente;
            }
        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_AGENCIA_BANCARIA) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $ContaBancaria = new ContaBancaria();
                $dadosBancarios = $ContaBancaria->contaPorProjeto($get->idPronac);
                if (count($dadosBancarios) > 0) {
                    $valorPreCarregado = $dadosBancarios->Agencia;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SINOPSE_OBRA) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->Sinopse;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_IMPACTO_AMBIENTAL) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->ImpactoAmbiental;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESPECIFICACAO_TECNICA) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->EspecificacaoTecnica;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ESTRATEGIA_EXECUCAO) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->EstrategiadeExecucao;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $valorPreCarregado = $dadosProjeto->CgcCpf;
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $valorPreCarregado = $dadosProjeto->NomeProjeto;
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PERIODO_EXECUCAO) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();
            $DtFimExecucao = Data::tratarDataZend($dadosProjeto->DtFimExecucao, 'brasileira');

            if ($dadosProjeto) {
                $valorPreCarregado = $DtFimExecucao;
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $valorPreCarregado = $dadosProjeto->ResumoProjeto;
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_OBJETIVOS) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->Objetivos;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_JUSTIFICATIVA) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->Justificativa;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ACESSIBILIDADE) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->Acessibilidade;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_DEMOCRATIZACAO_ACESSO) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->DemocratizacaoDeAcesso;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ETAPAS_TRABALHO) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->EtapaDeTrabalho;
                }
            }

        } elseif ($tipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_FICHA_TECNICA) {
            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $get->idPronac))->current();

            if ($dadosProjeto) {
                $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $PreProjeto->buscar(array('idPreProjeto = ?' => $dadosProjeto->idProjeto))->current();
                if ($dadosPreProjeto) {
                    $valorPreCarregado = $dadosPreProjeto->FichaTecnica;
                }
            }
        }

        $this->montaTela(
            'readequacoes/criar-campo-tipo-readequacao.phtml',
            array(
                'tipoReadequacao' => $tipoReadequacao,
                'valorPreCarregado' => $valorPreCarregado
            )
        );
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

        $urlCallback = $this->_request->getParam('urlCallback');

        if (empty($urlCallback)) {
            $urlCallback = "readequacao/readequacoes/index?idPronac=" . Seguranca::encrypt($idPronac);
        }

        $idReadequacao = filter_var($this->_request->getParam("idReadequacao"), FILTER_SANITIZE_NUMBER_INT);
        $idTipoReadequacao = filter_var($this->_request->getParam("tipoReadequacao"), FILTER_SANITIZE_NUMBER_INT);

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

            if ($idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                $descJustificativa = $this->_request->getParam('descJustificativa');

                // se for tipo planilha orçamentaria, somente atualiza Readequacao_Model_DbTable_TbReadequacao
                $dadosReadequacao = array();
                $dadosReadequacao['dsJustificativa'] = $descJustificativa;

                if ($idDocumento) {
                    $dadosReadequacao['idDocumento'] = $idDocumento;
                }

                $readequacaoWhere = "idReadequacao = $idReadequacao";
                $tbReadequacao->update($dadosReadequacao, $readequacaoWhere);
            } else {
                // outros casos: insere Readequacao_Model_DbTable_TbReadequacao e altera tbPlanilhaAprovacao
                $dados = array();
                $dados['idPronac'] = $idPronac;
                $dados['idTipoReadequacao'] = $idTipoReadequacao;
                $dados['dtSolicitacao'] = new Zend_Db_Expr('GETDATE()');
                $dados['idSolicitante'] = $rsAgente->idAgente;
                $dados['dsJustificativa'] = $_POST['descJustificativa'];
                $dados['dsSolicitacao'] = $_POST['descSolicitacao'];
                $dados['idDocumento'] = $idDocumento;
                $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE;
                $dados['stEstado'] = 0;
                $idReadequacao = $tbReadequacao->inserir($dados);

//                $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
//                $dadosPlanilha = array();
//                $dadosPlanilha['idReadequacao'] = $idReadequacao;
//                $wherePlanilha = "IdPRONAC = $idPronac AND tpPlanilha = 'SR' AND idReadequacao is null";
//                $tbPlanilhaAprovacao->update($dadosPlanilha, $wherePlanilha);

                if (!empty($idReadequacao) && $idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) {
                    $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
                    $tbPlanoDistribuicaoMapper->incluirIdReadequacaoNasSolicitacoesAtivas($idPronac, $idReadequacao);

                    $tbDistribuicaoMapper = new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper();
                    $tbDistribuicaoMapper->incluirIdReadequacaoNasSolicitacoesAtivas($idPronac, $idReadequacao);
                }
            }

            if ($idReadequacao && $idTipoReadequacao != Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                $acaoErro = 'cadastrar';

                parent::message("Solicita&ccedil;&atilde;o cadastrada com sucesso!", $urlCallback, "CONFIRM");
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

    /*
     * Criada em 14/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Essa função para excluir uma solicitação de readequação.
     */
    public function excluirSolicitacaoReadequacaoAction()
    {
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
            $dados = $tbReadequacao->buscar(array('idReadequacao =?' => $idReadequacao))->current();

            if (!empty($dados->idDocumento)) {
                $tbDocumento = new tbDocumento();
                $tbDocumento->excluirDocumento($dados->idDocumento);
            }

            if ($dados->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

                $tbPlanilhaAprovacao->delete(array('IdPRONAC = ?' => $idPronac, 'tpPlanilha = ?' => 'SR', 'idReadequacao = ?' => $idReadequacao));
            }

            if ($dados->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_LOCAL_REALIZACAO) {
                $tbAbrangencia = new Readequacao_Model_DbTable_TbAbrangencia();
                $tbAbrangencia->delete(array('idPronac = ?' => $idPronac, 'stAtivo = ?' => 'S'));
            }

            if ($dados->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) {
                $tbPlanoDistribuicao = new Readequacao_Model_DbTable_TbPlanoDistribuicao();
                $tbPlanoDistribuicao->delete(array('idPronac = ?' => $idPronac, 'stAtivo = ?' => 'S'));

                $tbDetalhaPlanoDistribuicao = new Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao();
                $tbDetalhaPlanoDistribuicao->delete(array('idPronac = ?' => $idPronac, 'stAtivo = ?' => 'S'));
            }

            if ($dados->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DIVULGACAO) {
                $tbPlanoDivulgacao = new tbPlanoDivulgacao();
                $tbPlanoDivulgacao->delete(array('idPronac = ?' => $idPronac, 'stAtivo = ?' => 'S'));
            }

            $exclusao = $tbReadequacao->delete(array('idPronac =?' => $idPronac, 'idReadequacao =?' => $idReadequacao));

            if ($exclusao) {
                if ($dados->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                    parent::message('Tipo de readequa&ccedil;&atilde;o exclu&iacute;da com sucesso!', "readequacao/readequacoes/planilha-orcamentaria?idPronac=" . Seguranca::encrypt($idPronac), "CONFIRM");
                } else {
                    parent::message('Tipo de readequa&ccedil;&atilde;o exclu&iacute;da com sucesso!', "readequacao/readequacoes/index?idPronac=" . Seguranca::encrypt($idPronac), "CONFIRM");
                }
            } else {
                throw new Exception("Erro ao excluir o tipo de readequa&ccedil;&atilde;o!");
            }
        } // fecha try
        catch (Exception $e) {
            parent::message($e->getMessage(), "readequacao/readequacoes?idPronac=".Seguranca::encrypt($idPronac), "ERROR");
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

    /**
     * painelAction - Lista dos Projetos em nas situações: Aguardando Análise, Em
     * Analise, Analisados.
     *
     * @since Alterada em 30/06/16
     * @author wouerner <wouerner@gmail.com>
     * @author fernao <fernao.lara@cultura.gov.br>
     * @access public
     * @return void
     */
    public function painelAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO && $this->idPerfil != Autenticacao_Model_Grupos::PRESIDENTE_VINCULADA_SUBSTITUTO && $this->idPerfil != Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO) {
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
            if ($ordem == "DESC") {
                $novaOrdem = "ASC";
            } else {
                $novaOrdem = "DESC";
            }
        } else {
            $campo = 8;  //qtDiasAguardandoDistribuicao default
            $ordem = "DESC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            // ordenação padrão
            $order = array($campo . " " . $ordem);
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

        $filtro = null;
        if ($this->_request->getParam('tipoFiltro')) {
            $filtro = $this->_request->getParam('tipoFiltro');
        } else {
            $filtro = 'aguardando_distribuicao';
        }

        $this->view->filtro = $filtro;
        
        if ($filtro == 'aguardando_distribuicao') {
            $where['idOrgao = ?'] = $this->idOrgao;
        } else {
            $where['idOrgaoOrigem = ?'] = $this->idOrgao;
        }

        if ($this->_request->getParam('pronac')) {
            $where['a.PRONAC = ?'] = $this->_request->getParam('pronac');
            $this->view->pronac = $this->_request->getParam('pronac');
        }
        
        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        
        $total = $tbReadequacao->painelReadequacoesCoordenadorAcompanhamentoCount($where, $filtro);
        
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        if ($filtro == 'analisados') {
            unset($where['idOrgaoOrigem = ?']);
            $where['CASE 
	                    WHEN projetos.Orgao in (160,179,682)
		            THEN 166
                        WHEN projetos.Orgao in (251,341)
                    THEN 272
                    ELSE projetos.Orgao
                    END = ?'] = $this->idOrgao;

            if ($this->_request->getParam('pronac')) {
                unset($where['a.PRONAC = ?']);
                $where[new Zend_Db_Expr('projetos.anoprojeto + projetos.sequencial') . ' = ?'] = $this->_request->getParam('pronac');
            }           
        }
        $busca = $tbReadequacao->painelReadequacoesCoordenadorAcompanhamento($where, $order, $tamanho, $inicio, false, $filtro);

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

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $this->view->conselheiros = $tbTitulacaoConselheiro->buscarConselheirosTitularesTbUsuarios();

        $this->view->paginacao = $paginacao;
        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;
    }

    /*
     * Alterada em 15/05/15
     */
    public function imprimirReadequacoesAction()
    {

        if ($this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO) {
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
            $order = array(5); //Dt. Solicitação
            $ordenacao = null;
        }

        $pag = 1;
        if (isset($get->pag)) {
            $pag = $this->_request->getParam('pag');
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/

        $where = array();
        if ($this->_request->getParam('pronac')) {
            $where['a.PRONAC = ?'] = $this->_request->getParam('pronac');
            $this->view->pronac = $this->_request->getParam('pronac');
        }

        $filtro = null;
        if ($this->_request->getParam('tipoFiltro')) {
            $filtro = $this->_request->getParam('tipoFiltro');
        } else {
            $filtro = 'aguardando_distribuicao';
        }

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();

        $total = $tbReadequacao->painelReadequacoesCoordenadorAcompanhamentoCount($where, $filtro);

        $fim = $inicio + $this->intTamPag;
        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbReadequacao->painelReadequacoesCoordenadorAcompanhamento($where, $order, $tamanho, $inicio, null, $filtro);

        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }

    /*
     * Alterada em 19/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Função usada para o coordenador de acompanhamento deferir ou não a solicitação de readequação.
     */
    public function avaliarReadequacaoAction()
    {
        $perfisAcesso = array(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO);

        if (!in_array($this->idPerfil, $perfisAcesso)) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idReadequacao = Seguranca::dencrypt($this->_request->getParam('id'));

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $r = $tbReadequacao->buscarDadosReadequacoes(array('idReadequacao = ?' => $idReadequacao))->current();

        if ($r) {
            $Projetos = new Projetos();
            $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $r->idPronac))->current();

            $this->view->filtro = $this->_request->getParam('filtro');
            $this->view->readequacao = $r;
            $this->view->projeto = $p;
            $this->view->idPronac = $r->idPronac;
        } else {
            parent::message('Nenhum registro encontrado.', "readequacao/readequacoes/painel", "ERROR");
        }
    }

    /*
     * Alterada em 06/03/14
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     */
    public function buscarDestinatariosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $vinculada = $this->_request->getParam('vinculada');
        $idPronac = $this->_request->getParam('idPronac');

        $a = 0;
        $dadosUsuarios = array();

        if ($vinculada == 166 || $vinculada == 262) {
            $dados = array();
            $dados['sis_codigo = ?'] = 21;
            $dados['uog_status = ?'] = 1;
            $dados['gru_codigo = ?'] = Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO; //Tecnico de Acompanhamento
            if ($vinculada == 166) {
                $dados['org_superior = ?'] = 160;
            } else {
                $dados['org_superior = ?'] = 251;
            }

            $vw = new vwUsuariosOrgaosGrupos();
            $result = $vw->buscar($dados, array('usu_nome'));

            if (count($result) > 0) {
                foreach ($result as $registro) {
                    $dadosUsuarios[$a]['id'] = $registro['usu_codigo'];
                    $dadosUsuarios[$a]['nome'] = utf8_encode($registro['usu_nome']);
                    $a++;
                }
                $jsonEncode = json_encode($dadosUsuarios);
                $this->_helper->json(array('resposta' => true, 'conteudo' => $dadosUsuarios));
            } else {
                $this->_helper->json(array('resposta' => false));
            }
        } else { //CNIC
            $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
            $result = $tbTitulacaoConselheiro->buscarConselheirosTitularesTbUsuarios();

            if (count($result) > 0) {
                foreach ($result as $registro) {
                    $dadosUsuarios[$a]['id'] = $registro['id'];
                    $dadosUsuarios[$a]['nome'] = utf8_encode($registro['nome']);
                    $a++;
                }
                $jsonEncode = json_encode($dadosUsuarios);
                $this->_helper->json(array('resposta' => true, 'conteudo' => $dadosUsuarios));
            } else {
                $this->_helper->json(array('resposta' => false));
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
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

    /*
     * FUNÇÃO ACESSADA PELO TECNICO DE ACOMPANHAMENTO
    */
    public function painelReadequacoesAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER && $this->idPerfil != Autenticacao_Model_Grupos::PARECERISTA && $this->idPerfil != Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }

        $ordem = "ASC";
        $novaOrdem = "ASC";
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            }
        }

        $campo = null;
        $order = array(1); //idDistribuirReadequacao
        $ordenacao = null;
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();

        $filtro = 'painel_do_tecnico';
        if ($this->_request->getParam('tipoFiltro') !== null) {
            $filtro = $this->_request->getParam('tipoFiltro');
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER) {
            $filtro = 'aguardando_distribuicao';
        }
        $this->view->filtro = $filtro;

        if ($this->_request->getParam('pronac')) {
            $where['c.AnoProjeto+c.Sequencial = ?'] = $this->_request->getParam('pronac');
            $this->view->pronac = $this->_request->getParam('pronac');
        }

        $where['idUnidade = ?'] = $this->idOrgao;
        if ($this->idOrgao == 272) {
            $where['idUnidade = ?'] = 262;
        }

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();

        if ($this->idPerfil == Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER) {

            switch ($filtro) {
                case 'aguardando_distribuicao':
                    $total = count($tbDistribuirReadequacao->buscarReadequacaoCoordenadorParecerAguardandoAnalise($where));
                    break;
                case 'em_analise':
                    $total = count($tbReadequacao->buscarReadequacaoCoordenadorParecerEmAnalise($where));
                    break;
                case 'analisados':
                    $total = count($tbDistribuirReadequacao->buscarReadequacaoCoordenadorParecerAnalisados($where));
                    break;
            }
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO || $this->idPerfil == Autenticacao_Model_Grupos::PARECERISTA) {
            $auth = Zend_Auth::getInstance(); // pega a autenticação
            $where['d.idAvaliador = ?'] = $auth->getIdentity()->usu_codigo;

            $total = count($tbReadequacao->painelReadequacoesTecnicoAcompanhamento($where));

            $this->filtro = '';
        }

        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        if ($this->idPerfil == Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER) {
            switch ($filtro) {
                case 'aguardando_distribuicao':
                    $busca = $tbDistribuirReadequacao->buscarReadequacaoCoordenadorParecerAguardandoAnalise($where, $order, $tamanho, $inicio, false);
                    break;
                case 'em_analise':
                    $busca = $tbReadequacao->buscarReadequacaoCoordenadorParecerEmAnalise($where, $order, $tamanho, $inicio, false);
                    break;
                case 'analisados':
                    $busca = $tbDistribuirReadequacao->buscarReadequacaoCoordenadorParecerAnalisados($where, $order, $tamanho, $inicio, false);
                    break;
            }
        } elseif ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO || $this->idPerfil == Autenticacao_Model_Grupos::PARECERISTA) {
            $busca = $tbReadequacao->painelReadequacoesTecnicoAcompanhamento($where, $order, $tamanho, $inicio, false);
        }
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
        $this->view->idPerfil = $this->idPerfil;
        $this->view->idOrgao = $this->idOrgao;
    }

    /**
     * Função acessada pelo Coordenador de Parecer, Coordenador de Acompanhamento, e Coordenador Geral de Acomapanhamento.
     */
    public function visualizarReadequacaoAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $id = Seguranca::dencrypt($this->_request->getParam('id'));

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
     * Função acessada pelo Coordenador de Parecer para encaminhar a readequação para o Parecerista.
     */
    public function encaminharReadequacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $vinculada = $this->idOrgao;

        $idDistRead = $this->_request->getParam('idDistRead');
        $idReadequacao = $this->_request->getParam('idReadequacao');

        //Atualiza a tabela tbDistribuirReadequacao
        if ($vinculada != Orgaos::ORGAO_IPHAN_PRONAC) { // todos os casos exceto IPHAN
            $idAvaliador = $this->_request->getParam('parecerista');

            $dados = array();
            $dados['idAvaliador'] = $idAvaliador;
            $dados['DtEnvioAvaliador'] = new Zend_Db_Expr('GETDATE()');
            $where["idDistribuirReadequacao = ? "] = $idDistRead;
            $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
            $return = $tbDistribuirReadequacao->update($dados, $where);

            //Atualiza a tabelaReadequacao_Model_DbTable_TbReadequacao
            $dados = array();
            $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA;
            $where = array();
            $where['idReadequacao = ?'] = $idReadequacao;
            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $return2 = $tbReadequacao->update($dados, $where);

            if ($return && $return2) {
                $this->_helper->json(array('resposta' => true));
            } else {
                $this->_helper->json(array('resposta' => false));
            }
        } else {
            // IPHAN

            $idVinculada = $this->_request->getParam('parecerista');

            $dados = array();
            $dados['idUnidade'] = $idVinculada;
            $where["idDistribuirReadequacao = ? "] = $idDistRead;
            $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
            $return = $tbDistribuirReadequacao->update($dados, $where);

            if ($return) {
                $this->_helper->json(array('resposta' => true));
            } else {
                $this->_helper->json(array('resposta' => false));
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
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

//        $d = array();
//        $d['ProvidenciaTomada'] = 'Readequa&ccedil;&atilde;o enviada para avalia&ccedil;&atilde;o t&eacute;cnica.';
//        $d['dtSituacao'] = new Zend_Db_Expr('GETDATE()');
//        $where = "IdPRONAC = $dados->idPronac";
//        $Projetos->update($d, $where);

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
            $parecerDAO = new Parecer();
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
                $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
                $dDP = $tbDistribuirReadequacao->buscar(array('idReadequacao = ?' => $idReadequacao));

                if (count($dDP) > 0) {
                    //ATUALIZA A TABELA tbDistribuirReadequacao
                    $dadosDP = array();
                    $dadosDP['DtRetornoAvaliador'] = new Zend_Db_Expr('GETDATE()');
                    $whereDP = "idDistribuirReadequacao = " . $dDP[0]->idDistribuirReadequacao;
                    $x = $tbDistribuirReadequacao->update($dadosDP, $whereDP);

                    $siEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA;
                    if ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
                        $siEncaminhamento = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO;
                    }
                    //ATUALIZA A TABELA Readequacao_Model_DbTable_TbReadequacao
                    $dados = array();
                    $dados['siEncaminhamento'] = $siEncaminhamento;
                    $where = "idReadequacao = $idReadequacao";
                    $tbReadequacao->update($dados, $where);
                }
                
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
                
                $origin = "readequacao/readequacao-assinatura";

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

    public function coordParecerFinalizarReadequacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idReadequacao = (int)$post->idReadequacao;
        $idPronac = (int)$post->idPronac;

        //Atualiza a tabela Readequacao_Model_DbTable_TbReadequacao
        $dados = array();
        $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC;
        $where = "idReadequacao = $idReadequacao";
        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $return = $tbReadequacao->update($dados, $where);

        if ($return) {

            $idTipoDoAtoAdministrativo = Readequacao_ReadequacaoAssinaturaController::obterIdTipoAtoAdministativoPorOrgaoSuperior($this->idOrgao);
            $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $idDocumentoAssinatura = $objDbTableDocumentoAssinatura->getIdDocumentoAssinatura(
                $idPronac,
                $idTipoDoAtoAdministrativo
            );

            $linkAssinatura = '';
            if ($idDocumentoAssinatura) {
                $origin = "readequacao/readequacao-assinatura";
                $linkAssinatura = "/assinatura/index/visualizar-projeto?idDocumentoAssinatura={$idDocumentoAssinatura}&origin={$origin}";
            }

            $this->_helper->json(array('resposta' => true, $linkAssinatura));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Função acessada pelos coordenadores para devolver para análise técnica.
     */
    public function devolverReadequacaoAction()
    {
        $dados = array();
        $get = Zend_Registry::get('get');
        $idReadequacao = (int)$get->id;

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosReadequacao = $tbReadequacao->find(array('idReadequacao=?' => $idReadequacao))->current();

        $siEncaminhamento = $dadosReadequacao->siEncaminhamento;

        if ($siEncaminhamento == Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC) {

            $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE;
            $where = "idReadequacao = $idReadequacao";
        } else {
            $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA;
            $where = "idReadequacao = $idReadequacao";
        }
        $tbReadequacao->update($dados, $where);

        parent::message("Readequa&ccedil;&atilde;o devolvida com sucesso!", "readequacao/readequacoes/painel?tipoFiltro=analisados", "CONFIRM");
    }

    /*
     * Função acessada pelo coordenador de análise para finalizar a readequação e encaminhar para o componente da comissão.
    */
    public function coordAnaliseFinalizarReadequacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        //$vinculada = $this->idOrgao;

        $post = Zend_Registry::get('post');
        $idComponente = (int)$post->componente;
        $idReadequacao = (int)$post->idReadequacao;

        if (count($idReadequacao) > 7) {
            $idReadequacao = (int)Seguranca::dencrypt($idReadequacao);
        }

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosReadequacao = $tbReadequacao->buscar(array('idReadequacao = ?' => $idReadequacao))->current();
        $idPronac = $dadosReadequacao->idPronac;

        $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
        $dadosDistRead = $tbDistribuirReadequacao->buscar(array('idReadequacao=?' => $idReadequacao));
        if (count($dadosDistRead) > 0) {
            //Atualiza a tabela tbDistribuirReadequacao
            $dadosDP = array();
            $dadosDP['stValidacaoCoordenador'] = 1;
            $dadosDP['DtValidacaoCoordenador'] = new Zend_Db_Expr('GETDATE()');
            $dadosDP['idCoordenador'] = $this->idUsuario;

            // atualiza com dados para enviar para cnic
            $dadosDP['idUnidade'] = 400;
            $dadosDP['DtEncaminhamento'] = new Zend_Db_Expr('GETDATE()');
            $dadosDP['idAvaliador'] = $idComponente;
            $dadosDP['dtEnvioAvaliador'] = new Zend_Db_Expr('GETDATE()');

            $whereDP = "idDistribuirReadequacao = " . $dadosDistRead[0]->idDistribuirReadequacao;
            $distribuicao = $tbDistribuirReadequacao->update($dadosDP, $whereDP);
        }

        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();
        $idNrReuniao = ($raberta['stPlenaria'] == 'A') ? $raberta['idNrReuniao'] + 1 : $raberta['idNrReuniao'];

        //Atualiza a tabela Readequacao_Model_DbTable_TbReadequacao
        $dados = array();
        $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_CNIC;
        $dados['idNrReuniao'] = $idNrReuniao;
        $where = "idReadequacao = $idReadequacao";
        $return = $tbReadequacao->update($dados, $where);

        if ($return && $distribuicao) {
            $this->_helper->json(array('resposta' => true));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
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
                $parecerDAO = new Parecer();
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

    /*
     * Alterada em 15/05/2015
     * Função criada para finalizar ou encaminhar a readequação para checklist de publicação.
    */
    public function encaminharReadequacaoChecklistAction()
    {
        if ($this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER
            && $this->idPerfil != Autenticacao_Model_Grupos::PARECERISTA
            && $this->idPerfil != Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO
            && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO
            && $this->idPerfil != Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $auth = Zend_Auth::getInstance();
        // TODO: quando finalizar, mantem filtro de pronac caso estiver marca
        $pronacSelecionado = $this->_request->getParam("pronac");
        $urlComplemento = ($pronacSelecionado) ? "&pronac=$pronacSelecionado" : "";

        $get = Zend_Registry::get('get');
        $idReadequacao = (int)$get->id;

        $retorno = $this->encaminharOuFinalizarReadequacaoChecklist(
            $idReadequacao
        );

        if (!$retorno) {
            parent::message("N&atilde;o foi poss&iacute;vel encaminhar a readequa&ccedil;&atilde;o para o Checklist de Publica&ccedil;&atilde;o", "readequacao/readequacoes/painel?tipoFiltro=analisados" . $urlComplemento, "ERROR");
        }
        parent::message("Readequa&ccedil;&atilde;o finalizada com sucesso!", "readequacao/readequacoes/painel?tipoFiltro=analisados" . $urlComplemento, "CONFIRM");
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
            $this->view->Etapas = $spSelecionarEtapa->exec($idPronac);

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
     * Encaminhar para análise técnica
     * Criada em 13/06/2016
     * @author: Fernão Lopes Ginez de Lara fernao.lara@cultura.gov.br
     * @access public
     * @return void
     */
    public function encaminharAnaliseTecnicaAction()
    {
        $perfisAcesso = array(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO);

        if (!in_array($this->idPerfil, $perfisAcesso)) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idReadequacao = Seguranca::dencrypt($this->_request->getParam('id'));
        $cnic = $this->_request->getParam('cnic');

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $r = $tbReadequacao->buscarDadosReadequacoes(array('idReadequacao = ?' => $idReadequacao))->current();

        if ($r) {
            $Projetos = new Projetos();
            $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $r->idPronac))->current();

            $this->view->filtro = $this->_request->getParam('filtro');
            $this->view->readequacao = $r;
            $this->view->projeto = $p;
            $this->view->idPronac = $r->idPronac;
            $this->view->cnic = $cnic;
        } else {
            parent::message('Nenhum registro encontrado.', "readequacao/readequacoes/painel", "ERROR");
        }

//        $this->renderScript('readequacao/encaminhar-analise-tecnica.phtml');
    }


    /**
     * formEncaminharAnaliseTecnicaAction Função utilizada pleo Coord. de Acompanhamento para encaminhar readequacao
     *
     * @since  Criada em 13/06/2016
     * @author Fernão Lopes Ginez de Lara <fernao.lara@cultura.gov.br>
     * @access public
     * @return void
     */
    public function formEncaminharAnaliseTecnicaAction()
    {
        $perfisAcesso = array(Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO);

        if (!in_array($this->idPerfil, $perfisAcesso)) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal", "ALERT");
        }

        $idReadequacao = $this->_request->getParam('idReadequacao');
        $filtro = $this->_request->getParam('filtro');
        $dsOrientacao = $this->_request->getParam('dsAvaliacao');
        $stValidacaoCoordenador = 0;
        $dataEnvio = null;
        $destinatario = (null !== $this->_request->getParam('destinatario')) ? $this->_request->getParam('destinatario') : null;
        $idUnidade = $this->_request->getParam('vinculada');

        try {
            if (in_array($idUnidade, array(Orgaos::ORGAO_SAV_CAP, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI))) {
                // MUDANÇA DE TECNICO
                $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
                $dados = array(
                    'idAvaliador' => $destinatario,
                    'DtEnvioAvaliador' => new Zend_Db_Expr('GETDATE()'),
                    'dsOrientacao' => $dsOrientacao,
                    'idUnidade' => $idUnidade
                );
                $where = array();
                $where['idReadequacao = ?'] = $idReadequacao;

                $u = $tbDistribuirReadequacao->update($dados, $where);

                // atualizar Readequacao_Model_DbTable_TbReadequacao
                $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
                $dados = array(
                    'siEncaminhamento' => Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA
                );
                $where = array();
                $where['idReadequacao = ?'] = $idReadequacao;
                $tbReadequacao->update($dados, $where);

                if ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
                    parent::message('Dados salvos com sucesso!', "readequacao/readequacoes/painel-readequacoes?tipoFiltro=$filtro", "CONFIRM");
                } else {
                    parent::message('Dados salvos com sucesso!', "readequacao/readequacoes/painel?tipoFiltro=$filtro", "CONFIRM");
                }
            } else {
                // MUDANÇA DE VINCULADA

                // MUDANÇA DE TECNICO
                $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
                $dados = array(
                    'idUnidade' => $idUnidade,
                    'DtEncaminhamento' => new Zend_Db_Expr('GETDATE()'),
                    'idAvaliador' => null,
                    'DtEnvioAvaliador' => null,
                    'dsOrientacao' => $dsOrientacao
                );
                $where = array();
                $where['idReadequacao = ?'] = $idReadequacao;

                $tbDistribuirReadequacao->update($dados, $where);

                $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
                $dadosReadequacao = array(
                    'siEncaminhamento' => Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE
                );
                $u = $tbReadequacao->update($dadosReadequacao, $where);

                if ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
                    parent::message('Dados salvos com sucesso!', "readequacao/readequacoes/painel-readequacoes?tipoFiltro=$filtro", "CONFIRM");
                } else {
                    parent::message('Dados salvos com sucesso!', "readequacao/readequacoes/painel?tipoFiltro=$filtro", "CONFIRM");
                }
            }
        } catch (Exception $e) {
            if ($this->idPerfil == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO) {
                parent::message('Erro ao encaminhar readequa&ccedil;&atilde;o!', "readequacao/readequacoes/painel-readequacoes?tipoFiltro=$filtro", "ERROR");
            } else {
                parent::message('Erro ao encaminhar readequa&ccedil;&atilde;o!', "readequacao/readequacoes/painel?tipoFiltro=$filtro", "ERROR");
            }
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

            $this->_helper->json(
                [
                    'readequacao' => array_map('utf8_encode', $readequacao),
                    'msg' => $mensagem
                ]
            );
        } catch (Exception $objException) {
            $this->getResponse()->setHttpResponseCode(412);

            $this->_helper->json(
                [
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
                'mensagem' => utf8_encode('Houve um erro ao excluir o documento.'),
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

    public function encaminharOuFinalizarReadequacaoChecklist($idReadequacao)
    {
        try {

            $auth = Zend_Auth::getInstance();
            $idUsuarioLogado = $auth->getIdentity()->usu_codigo;
            $reuniao = new Reuniao();
            $raberta = $reuniao->buscarReuniaoAberta();
            $idNrReuniao = ($raberta['stPlenaria'] == 'A') ? $raberta['idNrReuniao'] + 1 : $raberta['idNrReuniao'];

            $tbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
            $dadosParecer = $tbReadequacaoXParecer->buscar([
                'idReadequacao=?' => $idReadequacao
            ]);
            foreach ($dadosParecer as $key => $dp) {
                $pareceres = array();
                $pareceres[$key] = $dp->idParecer;
            }

            $Parecer = new Parecer();
            $parecerTecnico = $Parecer->buscar(
                ['IdParecer = (?)' => $pareceres],
                ['IdParecer']
            )->current();

            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
            $read = $tbReadequacao->buscarReadequacao(array('idReadequacao =?' => $idReadequacao))->current();

            if ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANO_DISTRIBUICAO) { //Se for readequação de plano de distribuição, atualiza os dados na SAC.dbo.PlanoDistribuicaoProduto.
                $tbPlanoDistribuicaoMapper = new Readequacao_Model_TbPlanoDistribuicaoMapper();
                $tbPlanoDistribuicaoMapper->finalizarAnaliseReadequacaoPlanoDistribuicao($read->idPronac, $idReadequacao, $parecerTecnico->ParecerFavoravel);
            }

            if ($parecerTecnico->ParecerFavoravel == 2) { //Se for parecer favorável, atualiza os dados solicitados na readequação
                if ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA) {
                    $Projetos = new Projetos();
                    $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

                    $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                    $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilhaAtiva($read->idPronac);

                    //BUSCAR VALOR TOTAL DA PLANILHA DE READEQUADA
                    $PlanilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilhaReadequada(
                        $read->idPronac,
                        $read->idReadequacao
                    );

                    // chama SP que verifica o tipo do remanejamento
                    $spTipoDeReadequacaoOrcamentaria = new spTipoDeReadequacaoOrcamentaria();
                    $TipoDeReadequacao = $spTipoDeReadequacaoOrcamentaria->exec($read->idPronac);

                    // complementacao
                    if ($TipoDeReadequacao[0]['TipoDeReadequacao'] == 'CO') {
                        $TipoAprovacao = 2;
                        $dadosPrj->Situacao = 'D28';
                        $dadosPrj->ProvidenciaTomada = 'Aguardando portaria de complementação';
                        $dadosPrj->Logon = $auth->getIdentity()->usu_codigo;
                    } elseif ($TipoDeReadequacao[0]['TipoDeReadequacao'] == 'RE') {
                        // reducao
                        $TipoAprovacao = 4;
                        $dadosPrj->Situacao = 'D29';
                        $dadosPrj->ProvidenciaTomada = 'Aguardando portaria de redução';
                        $dadosPrj->Logon = $auth->getIdentity()->usu_codigo;
                    }

                    // insere somente em reducao ou complementacao
                    if ($TipoDeReadequacao[0]['TipoDeReadequacao'] == 'CO' || $TipoDeReadequacao[0]['TipoDeReadequacao'] == 'RE') {
                        $dadosPrj->save();
                        $tbAprovacao = new Aprovacao();
                        $dadosAprovacao = array(
                            'IdPRONAC' => $read->idPronac,
                            'AnoProjeto' => $dadosPrj->AnoProjeto,
                            'Sequencial' => $dadosPrj->Sequencial,
                            'TipoAprovacao' => $TipoAprovacao,
                            'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                            'ResumoAprovacao' => 'Parecer favorável para readequação',
                            #'AprovadoReal' => $AprovadoReal,
                            'AprovadoReal' => $TipoDeReadequacao[0]['vlReadequado'], //Alterado pelo valor retornado pela Store
                            'Logon' => $auth->getIdentity()->usu_codigo,
                            'idReadequacao' => $idReadequacao
                        );

                        $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);
                    }

                } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL) {
                    $Projetos = new Projetos();
                    $dadosPrj = $Projetos->find(array('IdPRONAC=?' => $read->idPronac))->current();

                    $tbAprovacao = new Aprovacao();
                    $dadosAprovacao = array(
                        'IdPRONAC' => $read->idPronac,
                        'AnoProjeto' => $dadosPrj->AnoProjeto,
                        'Sequencial' => $dadosPrj->Sequencial,
                        'TipoAprovacao' => 8,
                        'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                        'ResumoAprovacao' => 'Parecer favorável para readequação',
                        'Logon' => $auth->getIdentity()->usu_codigo,
                        'idReadequacao' => $idReadequacao
                    );
                    $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

                } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_AGENCIA_BANCARIA) {
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
                        'Logon' => $auth->getIdentity()->usu_codigo,
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
                                $novoLocalRead['Usuario'] = $idUsuarioLogado;
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
                        'TipoAprovacao' => 8,
                        'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                        'ResumoAprovacao' => 'Parecer favorável para readequação',
                        'Logon' => $auth->getIdentity()->usu_codigo,
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
                        'TipoAprovacao' => 8,
                        'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                        'ResumoAprovacao' => 'Parecer favorável para readequação',
                        'Logon' => $auth->getIdentity()->usu_codigo,
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
                                $PlanoDivulgacaoEmQuestao->delete();
                            } elseif ($plano->tpSolicitacao == 'I') { //Se o plano de divulgação foi incluído, cria um novo registro na tabela SAC.dbo.PlanoDeDivulgacao
                                $novoPlanoDivRead = array();
                                $novoPlanoDivRead['idProjeto'] = $dadosPrj->idProjeto;
                                $novoPlanoDivRead['idPeca'] = $plano->idPeca;
                                $novoPlanoDivRead['idVeiculo'] = $plano->idVeiculo;
                                $novoPlanoDivRead['Usuario'] = $auth->getIdentity()->usu_codigo;
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
                        'TipoAprovacao' => 8,
                        'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                        'ResumoAprovacao' => 'Parecer favorável para readequação',
                        'Logon' => $auth->getIdentity()->usu_codigo,
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
                } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_TRANSFERENCIA_RECURSOS) {

                    $TbSolicitacaoTransferenciaRecursos = new Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos();
                    $tbProjetoRecebedorRecursoMapper = new Readequacao_Model_TbProjetoRecebedorRecursoMapper();
                    $tbSolicitacaoTransferenciaRecursosMapper = new Readequacao_Model_TbSolicitacaoTransferenciaRecursosMapper();
                    $projetos = new Projetos();

                    $projetosRecebedores = $TbSolicitacaoTransferenciaRecursos->obterProjetosRecebedores($idReadequacao);
                    $projetoTransferidor = $projetos->buscarProjetoTransferidor($read->idPronac);

                    foreach ($projetosRecebedores as $projetoRecebedor) {

                        $arrData = [];
                        $arrData['idSolicitacaoTransferenciaRecursos'] = $projetoRecebedor['idSolicitacao'];
                        $arrData['idPronacTransferidor'] = $projetoTransferidor['idPronac'];
                        $arrData['idPronacRecebedor'] = $projetoRecebedor['idPronacRecebedor'];
                        $arrData['tpTransferencia'] = $projetoRecebedor['tpTransferencia'];
                        $arrData['dtRecebimento'] = new Zend_Db_Expr('GETDATE()');
                        $arrData['vlRecebido'] = $projetoRecebedor['vlRecebido'];

                        $statusProjetoRecebedorRecurso = $tbProjetoRecebedorRecursoMapper->finalizarSolicitacaoReadequacao($arrData);

                        $arrData = [];
                        $arrData['stEstado'] = 1;
                        $arrData['idSolicitacaoTransferenciaRecursos'] = $projetoRecebedor['idSolicitacao'];
                        // TODO: dando pau aqui
                        $statusSolicitacaoTransferenciaRecursos = $tbSolicitacaoTransferenciaRecursosMapper->save($arrData);

                        if ($statusProjetoRecebedorRecurso == false
                            || $statusSolicitacaoTransferenciaRecursos == false
                        ) {
                            throw new Exception("N&atilde;o foi poss&iacute;vel incluir os projetos recebedores da solicita&ccedil;&atilde;o");
                        }
                    }
                } elseif ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_SALDO_APLICACAO) {
                    $Projetos = new Projetos();
                    $dadosPrj = $Projetos->find(array('IdPRONAC=?'=>$read->idPronac))->current();

                    $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                    $planilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilhaReadequada(
                        $read->idPronac,
                        $read->idReadequacao,
                        [Proposta_Model_Verificacao::INCENTIVO_FISCAL_FEDERAL]
                    )->current();

                    $where = array();
                    $where['a.IdPRONAC = ?'] = $read->idPronac;
                    $where['a.stAtivo = ?'] = 'S';
                    $where['a.nrFonteRecurso = ?'] = Proposta_Model_Verificacao::INCENTIVO_FISCAL_FEDERAL;
                    $PlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilha($where)->current();

                    $valorDaReadequacao = $planilhaReadequada->Total - $PlanilhaAtiva->Total;

                    $tbAprovacao = new Aprovacao();
                    $dadosAprovacao = array(
                        'IdPRONAC' => $read->idPronac,
                        'AnoProjeto' => $dadosPrj->AnoProjeto,
                        'Sequencial' => $dadosPrj->Sequencial,
                        'TipoAprovacao' => Aprovacao::TIPO_APROVACAO_COMPLEMENTACAO,
                        'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                        'AprovadoReal' => $valorDaReadequacao,
                        'ResumoAprovacao' => $parecerTecnico->ResumoParecer,
                        'idParecer' => $parecerTecnico->IdParecer,
                        'Logon' => $auth->getIdentity()->usu_codigo,
                        'idReadequacao' => $idReadequacao
                    );
                    $idAprovacao = $tbAprovacao->inserir($dadosAprovacao);

                    $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
                    $dadosReadequacaoAnterior = ['stAtivo' => 'N'];
                    $whereReadequacaoAnterior = [
                        'IdPRONAC = ?' => $read->idPronac,
                        'stAtivo = ?' => 'S'
                    ];
                    $update = $tbPlanilhaAprovacao->update($dadosReadequacaoAnterior, $whereReadequacaoAnterior);

                    $dadosReadequacaoNova = ['stAtivo' => 'S'];
                    $whereReadequacaoNova = [
                        'IdPRONAC = ?' => $read->idPronac,
                        'stAtivo = ?' => 'N',
                        'idReadequacao=?' => $idReadequacao
                    ];
                    $tbPlanilhaAprovacao->update($dadosReadequacaoNova, $whereReadequacaoNova);
                }
            }

            //Atualiza a tabela Readequacao_Model_DbTable_TbReadequacao
            $dados = [];
            $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_FINALIZADA_SEM_PORTARIA;
            $dados['stEstado'] = 1;

            $tiposParaChecklist = [
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RAZAO_SOCIAL,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_ALTERACAO_PROPONENTE,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_NOME_PROJETO,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_RESUMO_PROJETO
            ];

            if (in_array($read->idTipoReadequacao, $tiposParaChecklist)) {
                if ($read->idTipoReadequacao != Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA && $TipoDeReadequacao[0]['TipoDeReadequacao'] != 'RM') {
                    if ($parecerTecnico->ParecerFavoravel !== '1') { // desfavoravel
                        $dados['stEstado'] = 0;
                        $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CHECKLIST_PUBLICACAO;
                    }
                }
            }

            // atualiza registro de tbReadequacao
            $dados['idNrReuniao'] = $idNrReuniao;
            $where = [];
            $where["idReadequacao = ?"] = $idReadequacao;

            $retorno = true;
            $atualizacaoReadequacao = $tbReadequacao->update($dados, $where);
            if (!$atualizacaoReadequacao) {
                $retorno = false;
            }

            if ($read->idTipoReadequacao == Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA && $TipoDeReadequacao[0]['TipoDeReadequacao'] == 'RM') {
                // remanejamento: chama sp para trocar planilha ativa (desativa atual e ativa remanejada)
                $spAtivarPlanilhaOrcamentaria = new spAtivarPlanilhaOrcamentaria();
                $ativarPlanilhaOrcamentaria = $spAtivarPlanilhaOrcamentaria->exec($read->idPronac);
            }

            //Atualiza a tabela tbDistribuirReadequacao
            $dados = array();
            $dados['stValidacaoCoordenador'] = 1;
            $dados['DtValidacaoCoordenador'] = new Zend_Db_Expr('GETDATE()');
            $dados['idCoordenador'] = $idUsuarioLogado;
            $where = "idReadequacao = $idReadequacao";
            $tbDistribuirReadequacao = new Readequacao_Model_tbDistribuirReadequacao();
            $atualizacaoDistribuicaoReadequacao = $tbDistribuirReadequacao->update($dados, $where);
            if (!$atualizacaoDistribuicaoReadequacao) {
                $retorno = false;
            }
            return $retorno;
        } catch (Exception $objExcetion) {
            xd($objExcetion->getMessage());
            throw $objExcetion;
        }
    }
}
