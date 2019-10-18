<?php

class Parecer_AnaliseCnicController extends MinC_Controller_Action_Abstract
{
    private $idPronac;
    const ID_TIPO_AGENTE_COMPONENTE_CNIC = 6;

    private function validarPerfis()
    {
        $auth = Zend_Auth::getInstance();

        $PermissoesGrupo = array();
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COMPONENTE_COMISSAO;

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
    }


    public function gerenciarAssinaturasAction()
    {
        $url = Zend_Controller_Front::getInstance()->getRequest()->getServer('HTTP_REFERER');
        $url_get_idpronac = preg_split('/\/([0-9]{6,7})/', $url, -1, PREG_SPLIT_DELIM_CAPTURE);

        if (is_numeric($url_get_idpronac[1])) {
            $idPronac = $url_get_idpronac[1];
        } else {
            $url_get_iddocumentoassinatura = preg_split('/\=([0-9]*)\&/', $url, -1, PREG_SPLIT_DELIM_CAPTURE);
            if (is_numeric($url_get_iddocumentoassinatura[1])) {
                $idDocumentoAssinatura = $url_get_iddocumentoassinatura[1];
                $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();

                $result = $objModelDocumentoAssinatura->find(
                    array(
                        'idDocumentoAssinatura = ?', $idDocumentoAssinatura
                    )
                );
                if (count($result) > 0) {
                    $idPronac = $result[0]['IdPRONAC'];
                    print $idPronac;
                }
            }
        }

        if ($idPronac) {
            $this->redirect("/parecer/analise-cnic/emitirparecer/idpronac/$idPronac");
        } else {
            $this->redirect("/areadetrabalho");
        }
    }

    public function encaminharAssinaturaAction()
    {
        $this->validarPerfis();
        $idPronac = $this->_request->getParam("idpronac");
        $origin = $this->_request->getParam("origin");

        try {
            if (isset($idPronac) && !empty($idPronac)) {
                $parecer = new Parecer_Model_DbTable_Parecer();

                $dadosParecer = $parecer->getIdAtoAdministrativoParecerTecnico($idPronac, self::ID_TIPO_AGENTE_COMPONENTE_CNIC);
                $idAtoAdministrativo = $dadosParecer[0]['idParecer'];

                $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_CNIC;
                $servicoDocumentoAssinatura = new \Application\Modules\Parecer\Service\Assinatura\AnaliseCNIC\DocumentoAssinatura(
                    $idPronac,
                    $idTipoDoAtoAdministrativo,
                    $idAtoAdministrativo
                );
                $idDocumentoAssinatura = $servicoDocumentoAssinatura->iniciarFluxo();

                $this->redirect("/assinatura/index/visualizar-projeto/?idDocumentoAssinatura={$idDocumentoAssinatura}&origin={$origin}");
            }
        } catch (Exception $objException) {
            if($origin) {
                parent::message($objException->getMessage(), $origin);
            } else {
                parent::message($objException->getMessage());
            }
        }
    }

    private function getIdDocumentoAssinatura($idPronac, $idTipoDoAtoAdministrativo)
    {
        $objDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();

        $where = array();
        $where['IdPRONAC = ?'] = $idPronac;
        $where['idTipoDoAtoAdministrativo = ?'] = $idTipoDoAtoAdministrativo;
        $where['stEstado = ?'] = 1;

        $result = $objDocumentoAssinatura->buscar($where);

        return $result[0]['idDocumentoAssinatura'];
    }

    public function indexAction()
    {
        $this->redirect("/areadetrabalho");
    }


    public function emitirparecerAction()
    {
        $idPronac = $this->_request->getParam("idpronac");
        $this->view->idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_CNIC;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->view->idPerfilDoAssinante = $GrupoAtivo->codGrupo;
        $this->view->situacaoAprovar = 'D50';

        $projetos = new Projetos();
        $this->view->IN2017 = $projetos->VerificarIN2017($idPronac);

        $this->view->bln_readequacao = false;

        if (!empty($idPronac)) {
            $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
            $arrBusca = array();
            $arrBusca['pa.idPronac = ?'] = $idPronac;
            $arrBusca['pa.stPedidoAlteracao = ?'] = 'I'; //pedido enviado pelo proponente
            $arrBusca['pa.siVerificacao = ?'] = '1';
            $arrBusca['paxta.tpAlteracaoProjeto = ?'] = '10'; //tipo Readequacao de Itens de Custo
            $rsPedidoAlteraco = $tbPedidoAlteracao->buscarPedidoAlteracaoPorTipoAlteracao($arrBusca, array('dtSolicitacao DESC'))->current();
            if (!empty($rsPedidoAlteraco)) {
                $this->bln_readequacao = "true";
                $this->view->bln_readequacao = "true";
                $this->idPedidoAlteracao = $rsPedidoAlteraco->idPedidoAlteracao;
            }
        }

        if (!$this->view->IN2017) {
            $pa = new paChecarLimitesOrcamentario();
            $resultadoCheckList = $pa->exec($idPronac, 3);
            $i = 0;
            foreach ($resultadoCheckList as $resultado) {
                if ($resultado->Observacao == 'PENDENTE') {
                    $i++;
                }
            }

            $this->view->qtdErrosCheckList = $i;
            $this->view->resultadoCheckList = $resultadoCheckList;
        }

        $tblParecer = new Parecer_Model_DbTable_Parecer();
        $tblPlanilhaAprovacao = new PlanilhaAprovacao();
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $tblProjetos = new Projetos();
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();

        //CASO O COMPONENTE QUEIRA SALVAR O SEU PARECER - FIM
        $ConsultaReuniaoAberta = ReuniaoDAO::buscarReuniaoAberta();
        $numeroReuniao = $ConsultaReuniaoAberta['NrReuniao'];

        //CASO O COMPONENTE QUEIRA APENAS SALVAR O SEU PARECER - INICIO
        if (isset($_POST['usu_codigo'])) {
            $this->salvarParecerComponente($numeroReuniao);
        }

        if (isset($_POST['idpronac'])) {
            $this->fecharAssinatura($idPronac);

            $codSituacao = ($this->bln_readequacao == false) ? 'D50' : 'D02';
            $situacao = $this->_request->getParam("situacao") == null ? $codSituacao : $this->_request->getParam("situacao");
            $ProvidenciaTomada = 'PROJETO APRECIADO PELO COMPONENTE DA COMISS&Atilde;O NA REUNIÃ&Atilde; DA CNIC N&ordm;. ' . $numeroReuniao;
            $tblProjetos->alterarSituacao($idPronac, '', $situacao, $ProvidenciaTomada);

            $this->incluirNaPauta($idPronac, $ConsultaReuniaoAberta);
        }

        //FINALIZAR ANALISE - JUSTIFICATIVA DE PLENARIA - INICIO
        if ($_POST['stEnvioPlenaria'] == 'S') {
            /**** CODIGO DE READEQUACAO ****/
            //SE O PROJETO FOR DE READEQUACAO e a DECISAO FOR DE APROVACAO - INATIVA A ANTIGA PLANILHA 'CO' e ATIVA A 'CO' READEQUADA
            if ($this->bln_readequacao == "true") {
                $this->atualizaReadequacao($idPronac);
                // encerra
            }
            /**** FIM CODIGO DE READEQUACAO ****/
        } // fecha if
        // =================================================================
        // ========= CARREGANDO TELA DE EMISSAO DE PARECER =================
        else {
            $this->carregarEmissaoParecer();
        } // fecha else
    }


    private function atualizaReadequacao($idPronac)
    {
        $post = Zend_Registry::get('post');

        //finaliza readequacao do projeto
        if (!empty($this->idPedidoAlteracao) && $this->idPedidoAlteracao > 0) {
            $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
            $rsPedidoAlteracao = $tbPedidoAlteracao->buscar(array('idPedidoAlteracao = ?' => $this->idPedidoAlteracao))->current();
            $rsPedidoAlteracao->siVerificacao = 2;
            $rsPedidoAlteracao->save();
        }

        //troca planilhas apenas se a decisao do componente for de aprovar a readequacao  //Se a planilha atual � SE significa que voltou da plenaria e nao entra na opcao de desativar a antiga e ativar a nova
        if ($post->decisao == 'AC' && $this->view->tpPlanilha != 'SE') {
            try {
                //ATIVA PLANILHA CO READEQUADA
                $tblPlanilhaAprovacao = new PlanilhaAprovacao();
                $rsPlanilha_Ativa = $tblPlanilhaAprovacao->buscar(array('idPronac = ?' => $idPronac, 'stAtivo = ?' => 'S', 'tpPlanilha=?' => 'CO')); //PLANILHA DA APROVACAO INICIAL

                $arrBuscaPlanilha = array();
                $arrBuscaPlanilha["idPronac = ? "] = $idPronac;
                $arrBuscaPlanilha["tpPlanilha = ? "] = 'CO';
                $arrBuscaPlanilha["stAtivo = ? "] = 'N';
                $arrBuscaPlanilha["idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idPronac}')"] = '(?)';
                $rsPlanilha_Inativa = $tblPlanilhaAprovacao->buscar($arrBuscaPlanilha); //PLANILHA DA READEQUACAO
                //inativa Planilha Aprovacao Inicial
                foreach ($rsPlanilha_Ativa as $planilhaI) {
                    $planilhaI->stAtivo = 'N';
                    $planilhaI->save();
                }
                //ativa Planilha Readequada
                $planilha = null;
                foreach ($rsPlanilha_Inativa as $planilhaR) {
                    $planilhaR->stAtivo = 'S';
                    $planilhaR->save();
                }
            }// fecha try
            catch (Exception $e) {
                parent::message("Erro ao ativar Planilha readequada. " . $e->getMessage(), "parecer/analise-cnic/emitirparecer/idpronac/" . $idPronac, "ERROR");
            }
        }
    }


    private function readequarProjetoAprovadoNaCNIC()
    {
        $idpronac = $_POST['idpronac'];
        $tblSituacao = new Situacao();
        $tblPauta = new Pauta();
        $tblProjetos = new Projetos();

        $buscarnrreuniaoprojeto = $tblPauta->dadosiniciaistermoaprovacao(array($idpronac))->current();
        $dados = array();
        //TRATANDO SITUACAO DO PROJETO QUANDO ESTE FOR DE READEQUACAO
        if ($this->bln_readequacao == false) {
            $dados['Situacao'] = 'D50';
            $buscarsituacao = $tblSituacao->listasituacao(array('D50'))->current();
        } else {
            $dados['Situacao'] = 'D02';
            $buscarsituacao = $tblSituacao->listasituacao(array('D02'))->current();
        }
        $dados['DtSituacao'] = date('Y-m-d H:i:s');
        $dados['ProvidenciaTomada'] = 'PROJETO APRECIADO NA CNIC N&ordm ' . $buscarnrreuniaoprojeto->NrReuniao . ' - ' . $buscarsituacao['Descricao'];
        $dados['Logon'] = $this->auth->getIdentity()->usu_codigo;
        $where = "IdPRONAC = " . $idpronac;
        $tblProjetos->alterar($dados, $where);

        parent::message("Projeto readequado com sucesso!", "areadetrabalho/index", "CONFIRM");
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function incluirNaPauta($idPronac, $ConsultaReuniaoAberta)
    {
        $post = Zend_Registry::get('post');
        $stEnvioPlenaria = $this->_request->getParam("stEnvioPlenaria");
        $justificativa = $this->_request->getParam("justificativaenvioplenaria");
        $TipoAprovacao = $this->_request->getParam("decisao");
        $codSituacao = ($this->bln_readequacao == false) ? 'D50' : 'D02';
        $situacao = $this->_request->getParam("situacao") == null ? $codSituacao : $this->_request->getParam("situacao");
        $dtsituacao = date('Y-m-d H:i:s');

        try {
            // busca a reuniao aberta
            $idReuniao = $ConsultaReuniaoAberta['idNrReuniao'];
            $nrReuniao = $ConsultaReuniaoAberta['NrReuniao'];
            // verifica se ja esta na pauta
            $verificaPauta = RealizarAnaliseProjetoDAO::retornaRegistro($idPronac, $idReuniao);

            if (count($verificaPauta) == 0) {
                $tblPauta = new Pauta();
                $tblProjetos = new Projetos();

                // cadastra o projeto na pauta
                $dados = array(
                    'idNrReuniao' => $idReuniao,
                    'IdPRONAC' => $idPronac,
                    'dtEnvioPauta' => new Zend_Db_Expr('GETDATE()'),
                    'stEnvioPlenario' => $stEnvioPlenaria,
                    'tpPauta' => 1,
                    'stAnalise' => $TipoAprovacao,
                    'dsAnalise' => ' ',
                    'stPlanoAnual' => $this->_request->getParam("stPlanoAnual")
                );

                $tblPauta->inserir($dados);

                parent::message("Projeto cadastrado na Pauta com sucesso!", "areadetrabalho/index", "CONFIRM");
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                // altera o projeto na pauta
                $dados = array(
                    'idNrReuniao' => $idReuniao,
                    'dtEnvioPauta' => new Zend_Db_Expr('GETDATE()'),
                    'stEnvioPlenario' => $stEnvioPlenaria,
                    'tpPauta' => 1,
                    'dsAnalise' => '',
                    'stAnalise' => $TipoAprovacao,
                    'stPlanoAnual' => $this->_request->getParam("stPlanoAnual")
                );

                $dadosprojeto = array(
                    'Situacao' => $situacao,
                    'DtSituacao' => $dtsituacao,
                    'ProvidenciaTomada' => $providencia
                );

                $tbRecurso = new tbRecurso();
                $dadosRecursoAtual = $tbRecurso->buscar(array('IdPRONAC = ?' => $idPronac, 'stAtendimento = ?' => 'N', 'tpSolicitacao =?' => 'EN'));
                if (count($dadosRecursoAtual) > 0) {
                    $auth = Zend_Auth::getInstance(); // pega a autentica��o
                    $this->idUsuario = $auth->getIdentity()->usu_codigo;
                    //ATUALIZA��O DA TABELA RECURSO//
                    $dadosNovos = array(
                        'dtAvaliacao' => new Zend_Db_Expr('GETDATE()'),
                        'dsAvaliacao' => 'Recurso deferido conforme solicita&ccedil;&atilde;o do Proponente.',
                        'idAgenteAvaliador' => $this->idUsuario
                    );
                    $tbRecurso->update($dadosNovos, "idRecurso=" . $dadosRecursoAtual[0]->idRecurso);

                    //ATUALIZA��O DA TABELA Enquadramento//
                    $Enquadramento = new Admissibilidade_Model_Enquadramento();
                    $dadosEnquadramentoAtual = $Enquadramento->buscarDados($idPronac, null);
                    if (count($dadosRecursoAtual) > 0) {
                        $tpEnquadramento = ($dadosEnquadramentoAtual[0]->Enquadramento == 1) ? 2 : 1;
                        $dadosNovosEnquadramento = array(
                            'Enquadramento' => $tpEnquadramento,
                            'dtEnquadramento' => new Zend_Db_Expr('GETDATE()'),
                            'Observacao' => 'Altera&ccedil;&atilde;o de Enquadramento conforme deferimento de recurso.',
                            'Logon' => $this->idUsuario
                        );
                        $Enquadramento->update($dadosNovosEnquadramento, "IdEnquadramento=" . $dadosEnquadramentoAtual[0]->IdEnquadramento);
                    }
                }
                parent::message("Projeto j&aacute; est&aacute; em Pauta, sendo alterado com sucesso!", "areadetrabalho/index", "CONFIRM");
                $this->_helper->viewRenderer->setNoRender(true);
            }
        } // fecha try
        catch (Exception $e) {
            parent::message("Erro ao incluir projeto na Pauta. " . $e->getMessage(), "parecer/analise-cnic/emitirparecer/idpronac/" . $idPronac, "ERROR");
        }
    }


    private function validacao50($idPronac, $projetoAtual)
    {
        $planoDistribuicao = new PlanoDistribuicao();
        $analiseaprovacao = new AnaliseAprovacao();

        //CALCULO DOS 50%
        $buscarPlano = $planoDistribuicao->buscar(array('idProjeto = ?' => $projetoAtual['idProjeto'], 'stPrincipal= ?' => 1))->current()->toArray();
        $buscarAnaliseAp = $analiseaprovacao->buscar(array('IdPRONAC = ?' => $idPronac, 'idProduto = ?' => $buscarPlano['idProduto'], 'tpAnalise = ?' => $this->view->tipoanalise));
        if ($buscarAnaliseAp->count() > 0) {
            $buscarAnaliseAp = $buscarAnaliseAp->current()->toArray();
            if ($buscarAnaliseAp['stAvaliacao'] == 1) {
                $valoraprovacao = $this->view->fontesincentivo * 0.5;
                if ($valoraprovacao >= $this->view->totalsugerido) {
                    $this->view->parecerfavoravel = 'NAO';
                    $this->view->nrparecerfavoravel = 1;
                } else {
                    $this->view->parecerfavoravel = 'SIM';
                    $this->view->nrparecerfavoravel = 2;
                }
            } else {
                $this->view->parecerfavoravel = 'NAO';
                $this->view->nrparecerfavoravel = 1;
            }
        } else {
            $this->view->parecerfavoravel = 'NAO';
            $this->view->nrparecerfavoravel = 1;
        }
    }

    private function validacao1520($idPronac)
    {
        $planilhaAprovacao = new PlanilhaAprovacao();

        //CALCULO DOS 20% - ETAPA DIVULGACAO
        //soma para calculo dos 20% etapada de Divulgacao
        $arrWhereEtapa = array();
        $arrWhereEtapa['pa.idPronac = ?'] = $idPronac;
        $arrWhereEtapa['pa.idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
        $arrWhereEtapa['pa.tpPlanilha = ? '] = $this->view->tipoplanilha;
        $arrWhereEtapa['pa.NrFonteRecurso = ? '] = '109';
        $arrWhereEtapa['pa.idEtapa = ?'] = '3';
        if ($this->bln_readequacao == "true") {
            $arrWhereEtapa["pa.tpAcao <> ('E') OR pa.tpAcao IS NULL "] = '(?)';
            $arrWhereEtapa['pa.stAtivo = ? '] = 'N';
        } else {
            $arrWhereEtapa['pa.stAtivo = ? '] = 'S';
        }
        $arrWhereEtapa['aa.tpAnalise = ?'] = $this->view->tipoplanilha;
        $arrWhereEtapa['aa.stAvaliacao = ?'] = 1; // 1 = parecer favoravel, 0 = parecer nao favoravel

        $valorProjetoDivulgacao = $planilhaAprovacao->somarItensPlanilhaAprovacaoProdutosFavoraveis($arrWhereEtapa);

        //CALCULO DOS 15% - CUSTOS ADMINISTRATIVOS
        $arrWhereCustoAdm = array();
        $arrWhereCustoAdm['idPronac = ?'] = $idPronac;
        $arrWhereCustoAdm['idProduto = ?'] = 0;
        $arrWhereCustoAdm['idEtapa = ?'] = 4; //custos administrativos
        $arrWhereCustoAdm['idPlanilhaItem NOT IN (?)'] = array(5249, 206, 1238);//Remuneracao de captacao de recursos
        $arrWhereCustoAdm['tpPlanilha = ? '] = $this->view->tipoplanilha;
        $arrWhereCustoAdm['NrFonteRecurso = ? '] = '109';
        if ($this->bln_readequacao == "true") {
            $arrWhereCustoAdm["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
            $arrWhereCustoAdm['stAtivo = ? '] = 'N';
        } else {
            $arrWhereCustoAdm['stAtivo = ? '] = 'S';
        }

        $valoracustosadministrativos = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereCustoAdm);

        //CALCULO DOS 10% - REMUNERACAO PARA CAPTACAO DE RECURSO
        $arrWhereItemCaptRecurso = array();
        $arrWhereItemCaptRecurso['idPronac = ?'] = $idPronac;
        $arrWhereItemCaptRecurso['idPlanilhaItem = ?'] = '5249'; //Item de Remuneracao de captacao de recursos
        $arrWhereItemCaptRecurso['tpPlanilha = ? '] = $this->view->tipoplanilha;
        $arrWhereItemCaptRecurso['NrFonteRecurso = ? '] = '109';
        if ($this->bln_readequacao == "true") {
            $arrWhereItemCaptRecurso["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
            $arrWhereItemCaptRecurso['stAtivo = ? '] = 'N';
        } else {
            $arrWhereItemCaptRecurso['stAtivo = ? '] = 'S';
        }
        //$this->view->V2.2 = $valorItemCaptacaoRecurso['soma'];
        $valorItemCaptacaoRecurso = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereItemCaptRecurso);

        //Calcula os 20% do valor total do projeto V3
        $porcentValorProjeto = ($valorProjeto['soma'] * 0.20);

        //Calcula os 15% do valor total do projeto V3.1
        $quinzecentoprojeto = ($valorProjeto['soma'] * 0.15);

        //Calcula os 10% do valor total do projeto V3.2
        $dezPercentValorProjeto = ($valorProjeto['soma'] * 0.10);

        //Calculo do 20% -> V4
        //Subtrai os custos da etapa divulgacao pelos 20% do projeto (V2 - V3)
        $verificacaonegativo20porcento = $valorProjetoDivulgacao->soma - $porcentValorProjeto;

        //Calculo do 15% -> V4.1
        //Subtrai os custos administrativos pelos 15% do projeto (V2.1 - V3.1)
        $verificacaonegativo = $valoracustosadministrativos['soma'] - $quinzecentoprojeto;

        //Calculo do 10% -> V4.2
        //Subtrai o item de captacao de recurso pelos 10% do projeto (V2.2 - V3.2)
        $verificacaonegativo10porcento = $valorItemCaptacaoRecurso['soma'] - $dezPercentValorProjeto;

        //if V4 e V4.1 maior que zero soma os dois V4
        if ($verificacaonegativo20porcento > 0 && $verificacaonegativo > 0 && $verificacaonegativo10porcento > 0) {

            //V1 - (V4 + V4.1 + V4.2) = V5
            /*V5*/
            $novoValorProjeto = /*V1*/
                $valorProjeto['soma'] - (/*V4*/
                    $verificacaonegativo20porcento + /*V4.1*/
                    $verificacaonegativo + /*V4.2*/
                    $verificacaonegativo10porcento);

            /*V6*/
            $vinteporcentovalorretirar = /*V5*/
                $novoValorProjeto * 0.20;
            //V2 - V6
            $valorretirarplanilhaEtapaDivulgacao = $valorProjetoDivulgacao->soma - $vinteporcentovalorretirar; //(correcao V2 - V6)
            //$this->view->verifica15porcento = $valorretirarplanilha;
            $this->view->valorReadequar20porcento = $valorretirarplanilhaEtapaDivulgacao;
            $this->view->totaldivulgacao = "true";

            /*V6.1*/
            $quinzecentovalorretirar = /*V5*/
                $novoValorProjeto * 0.15;
            //V2 - V6
            $valorretirarplanilha = $valoracustosadministrativos['soma'] - $quinzecentovalorretirar; //(correcao V2 - V6)
            $this->view->verifica15porcento = $valorretirarplanilha;

            /*V6.2*/
            $dezcentovalorretirar = /*V5*/
                $novoValorProjeto * 0.10;
            //V2 - V6
            $valorretirarplanilhaItemCaptacaoRecurso = $valorItemCaptacaoRecurso['soma'] - $dezcentovalorretirar; //(correcao V2 - V6)
            $this->view->valorReadequar10porcento = $valorretirarplanilhaItemCaptacaoRecurso;
            $this->view->totalcaptacaorecurso = "true";
        } elseif ($verificacaonegativo20porcento > 0 || $verificacaonegativo > 0 || $verificacaonegativo10porcento > 0) {

            //Calculo dos 20%
            if ($verificacaonegativo20porcento <= 0) {
                $this->view->totaldivulgacao = "false";
                $this->view->valorReadequar20porcento = 0;
            } else {
                //V1 - V4 = V5
                /*V5*/
                $valorretirar20porcento = /*V1*/
                    $valorProjeto['soma'] - /*V4*/
                    $verificacaonegativo20porcento;
                /*V6*/
                $vinteporcentovalorretirar = /*V5*/
                    $valorretirar20porcento * 0.20;
                //V2 - V6
                $valorretirarplanilhaEtapaDivulgacao = $valorProjetoDivulgacao->soma - $vinteporcentovalorretirar; //(correcao V2 - V6)
                $this->view->valorReadequar20porcento = $valorretirarplanilhaEtapaDivulgacao;
                $this->view->totaldivulgacao = "true";
            }

            //Calculo dos 10%
            if ($verificacaonegativo10porcento <= 0) {
                $this->view->totalcaptacaorecurso = "false";
                $this->view->valorReadequar10porcento = 0;
            } else {
                //V1 - V4 = V5
                /*V5*/
                $valorretirar10porcento = /*V1*/
                    $valorProjeto['soma'] - /*V4*/
                    $verificacaonegativo10porcento;
                /*V6*/
                $dezcentovalorretirar = /*V5*/
                    $valorretirar10porcento * 0.10;
                //V2 - V6
                $valorretirarplanilhaItemCaptacaoRecurso = $valorItemCaptacaoRecurso['soma'] - $dezcentovalorretirar; //(correcao V2 - V6)
                $this->view->valorReadequar10porcento = $valorretirarplanilhaItemCaptacaoRecurso;
                $this->view->totalcaptacaorecurso = "true";
            }

            //Calculo dos 10% (complemento)
            $tetoCemMil = (int)'100000.00';
            if ($valorItemCaptacaoRecurso['soma'] > $tetoCemMil) { //verfica se o valor do item de captacao de recurso � maior que R$100.000,00
                $this->view->totalcaptacaorecurso = "true";
                $this->view->valorReadequar10porcento = $valorItemCaptacaoRecurso['soma'] - $tetoCemMil;
            }

            //Calculo dos 15%
            if ($valorProjeto['soma'] > 0 and $valoracustosadministrativos['soma'] < $valorProjeto['soma']) {
                if ($verificacaonegativo <= 0) {
                    $this->view->verifica15porcento = 0;
                } else {
                    //V1 - V4 = V5
                    /*V5*/
                    $valorretirar = /*V1*/
                        $valorProjeto['soma'] - /*V4*/
                        $verificacaonegativo;
                    /*V6*/
                    $quinzecentovalorretirar = /*V5*/
                        $valorretirar * 0.15;
                    //V2 - V6
                    $valorretirarplanilha = $valoracustosadministrativos['soma'] - $quinzecentovalorretirar; //(correcao V2 - V6)
                    $this->view->verifica15porcento = $valorretirarplanilha;
                }
            } else {
                $this->view->verifica15porcento = $valoracustosadministrativos['soma'];
            }
        } else {
            //Calculo dos 20%
            $this->view->totaldivulgacao = "false";
            $this->view->valorReadequar20porcento = 0;

            //Calculo dos 10% (complemento)
            $tetoCemMil = (int)'100000.00';
            if ($valorItemCaptacaoRecurso['soma'] > $tetoCemMil) { //verfica se o valor do item de captacao de recurso � maior que R$100.000,00
                $this->view->totalcaptacaorecurso = "true";
                $this->view->valorReadequar10porcento = $valorItemCaptacaoRecurso['soma'] - $tetoCemMil;
            } else {
                $this->view->totalcaptacaorecurso = "false";
                $this->view->valorReadequar10porcento = 0;
            }
            //Calculo dos 15%
            $this->view->verifica15porcento = 0;
        }
        //FIM - DOS CALCULO DOS 20% e 15%
    }


    private function carregarEmissaoParecer()
    {
        // recebe os dados via get
        $idpronac = $this->_request->getParam("idpronac");
        $projetos = new Projetos();
        $IN2017 = $projetos->VerificarIN2017($idpronac);
        $this->view->idpronac = $idpronac;

        try {
            if (empty($idpronac)) {
                //throw new Exception("Por favor, clique no Pronac Aguardando An&aacute;lise!");
                parent::message("Erro ao realizar opera&ccedil;&atilde;o.", "parecer/analise-cnic/emitirparecer/idpronac/" . $idPronac, "ERROR");
            } else {
                $idpronac = $this->_request->getParam("idpronac");

                $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $planilhaprojeto = new PlanilhaProjeto();
                $planilhaAprovacao = new PlanilhaAprovacao();
                $tbPreProjeto = new Proposta_Model_DbTable_PreProjeto();
                $tblParecer = new Parecer_Model_DbTable_Parecer();

                $rsPlanilhaAtual = $planilhaAprovacao->buscar(array('IdPRONAC = ?' => $idpronac), array('dtPlanilha DESC'))->current();
                $tipoplanilha = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'SE' : 'CO';

                if (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') {
                    $tpPlanilha = "SE";
                    $tpAnalise = "SE";
                    $tpAgente = '10';
                } else {
                    $tpPlanilha = "CO";
                    $tpAnalise = "CO";
                    $tpAgente = '6';
                }

                $projetoAtual = $projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current()->toArray();
                $idprojeto = $projetoAtual['idProjeto'];

                $rsPreprojeto = $tbPreProjeto->buscar(array('idPreProjeto=?' => $idprojeto))->current();
                if (!empty($rsPreprojeto)) {
                    $stPlanoAnual = $rsPreprojeto->stPlanoAnual;
                } else {
                    $stPlanoAnual = '0';
                }
                $this->view->tipoplanilha = $tpPlanilha;
                $this->view->tipoagente = $tpAgente;
                $this->view->stPlanoAnual = $stPlanoAnual;

                /**** CODIGO DE READEQUACAO ****/

                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?'] = $idpronac;

                //TRATANDO SOMA DE PROJETO QUANDO ESTE FOR DE READEQUACAO
                if ($this->bln_readequacao == false) {
                    $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
                    $outrasfontes = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
                } else {
                    $arrWhereFontesIncentivo = $arrWhereSomaPlanilha;
                    $arrWhereFontesIncentivo['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
                    $arrWhereFontesIncentivo['tpPlanilha = ? '] = 'SR';
                    $arrWhereFontesIncentivo['stAtivo = ? '] = 'N';
                    $arrWhereFontesIncentivo['NrFonteRecurso = ? '] = '109';
                    $arrWhereFontesIncentivo["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
                    $fonteincentivo = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereFontesIncentivo);

                    $arrWhereOutrasFontes = $arrWhereSomaPlanilha;
                    $arrWhereOutrasFontes['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
                    $arrWhereOutrasFontes['tpPlanilha = ? '] = 'SR';
                    $arrWhereOutrasFontes['stAtivo = ? '] = 'N';
                    $arrWhereOutrasFontes['NrFonteRecurso <> ? '] = '109';
                    $arrWhereOutrasFontes["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
                    $outrasfontes = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereOutrasFontes);
                }

                $this->view->fontesincentivo = $fonteincentivo['soma'];
                $this->view->outrasfontes = $outrasfontes['soma'];
                $this->view->valorproposta = $fonteincentivo['soma'] + $outrasfontes['soma'];


                $planilhaAprovacao = new PlanilhaAprovacao();

                $arrWhereSomaPlanilha = array();
                $arrWhereSomaPlanilha['idPronac = ?'] = $idpronac;
                $arrWhereSomaPlanilha['idPlanilhaItem <> ? '] = '206'; //elaboracao e agenciamento
                $arrWhereSomaPlanilha['tpPlanilha = ? '] = $this->view->tipoplanilha;
                $arrWhereSomaPlanilha['NrFonteRecurso = ? '] = '109';
                if ($this->bln_readequacao == "true") {
                    $arrWhereSomaPlanilha["tpAcao <> ('E') OR tpAcao IS NULL "] = '(?)';
                    $arrWhereSomaPlanilha['stAtivo = ? '] = 'N';
                } else {
                    $arrWhereSomaPlanilha['stAtivo = ? '] = 'S';
                }
                $valorProjeto = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
                $this->view->totalsugerido = $valorProjeto['soma'] ? $valorProjeto['soma'] : 0; //valor total do projeto (Planilha Aprovacao)

                if (!$IN2017) {
                    $this->validacao1520($idpronac);
                }

                if (!$IN2017) {
                    $this->validacao50($idpronac, $projetoAtual);
                }

                $produtos = RealizarAnaliseProjetoDAO::analiseDeConteudo($idpronac, $tpPlanilha);
                $this->view->ResultRealizarAnaliseProjeto = RealizarAnaliseProjetoDAO::analiseparecerConsolidado($idpronac);
                $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idpronac, $tpPlanilha, $IN2017);
                if ($IN2017) {
                    $this->view->enquadramento = $verificaEnquadramento[0]->stArtigo;
                } elseif (!$IN2017) {
                    if (isset($verificaEnquadramento[0]->stArtigo18) && $verificaEnquadramento[0]->stArtigo18 == true) {
                        $this->view->enquadramento = 'Artigo 18';
                    } elseif (isset($verificaEnquadramento[0]->stArtigo26) && $verificaEnquadramento[0]->stArtigo26 == true) {
                        $this->view->enquadramento = 'Artigo 26';
                    } else {
                        $this->view->enquadramento = 'NAO ENQUADRADO';
                    }
                }
                $this->view->ResultProduto = $produtos;
                $this->view->ResultValoresAnaliseProjeto = $produtos;
                $indeferidos = RealizarAnaliseProjetoDAO::retornaIndeferimento();
                $this->view->indeferidos = $indeferidos;
                $this->view->idpronac = $idpronac;

                /**** CODIGO DE READEQUACAO ****/
                $arrBuscaParecer = array();
                $arrBuscaParecer['idPronac = ?'] = $idpronac;
                $arrBuscaParecer['idTipoAgente = ?'] = $tpAgente;
                $arrBuscaParecer['TipoParecer = ?'] = ($this->bln_readequacao == "true") ? '2' : '1';
                $buscarjustificativa = $tblParecer->buscar($arrBuscaParecer);
                /**** FIM - CODIGO DE READEQUACAO ****/

                if ($buscarjustificativa->count() > 0) {
                    $buscarjustificativa = $buscarjustificativa->current()->toArray();
                    $this->view->valorJustificativa = $buscarjustificativa['ResumoParecer'];
                    $this->view->parecerFavoravel = $buscarjustificativa['ParecerFavoravel'];
                } else {
                    $this->view->valorJustificativa = null;
                }

                $auth = Zend_Auth::getInstance(); // pega a autenticao
                $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usuario
                $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
                $idagente = $idagente['idAgente'];
                //-------------------------------------------------------------------------------------------------------------
                $reuniao = new Reuniao();
                //---------------------------------------------------------------------------------------------------------------
                $ConsultaReuniaoAberta = $reuniao->buscar(array("stEstado = ?" => 0));
                if ($ConsultaReuniaoAberta->count() > 0) {
                    $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
                    $this->view->usu_codigo = $auth->getIdentity()->usu_codigo;
                    $ConsultaReuniaoAberta = $ConsultaReuniaoAberta->current()->toArray();
                    $this->view->plenariaatual = $ConsultaReuniaoAberta['idNrReuniao'];
                    $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
                    //---------------------------------------------------------------------------------------------------------------
                    $votantes = new Votante();
                    $exibirVotantes = $votantes->selecionarvotantes($ConsultaReuniaoAberta['idNrReuniao']);
                    if (count($exibirVotantes) > 0) {
                        foreach ($exibirVotantes as $votantes) {
                            $dadosVotante[] = $votantes->idAgente;
                        }
                        if (count($dadosVotante) > 0) {
                            if (in_array($idagente, $dadosVotante)) {
                                $this->view->votante = true;
                            } else {
                                $this->view->votante = false;
                            }
                        }
                    }
                } else {
                    parent::message("N&atilde;o existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
                }
            } // fecha else
        } // fecha try
        catch (Exception $e) {
            die($e->getMessage());
        }
    }

    private function salvarParecerComponente($numeroReuniao)
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $tblParecer = new Parecer_Model_DbTable_Parecer();

        $tipoAgente = $this->_request->getParam("tipoplanilha");
        $parecer = $this->_request->getParam("parecer");
        $idPronac = $this->_request->getParam("idpronac");
        $justificativa = $this->_request->getParam("justificativa");
        $valorReal = $this->_request->getParam("valorReal");
        $logon = $this->_request->getParam("usu_codigo");

        $buscarParecer = $tblParecer->buscar(array('IdPRONAC = ?' => $idPronac, 'stAtivo = ?' => 1))->current()->toArray();
        if (!empty($buscarParecer)) {

            $dados = array(
                'idPRONAC' => $idPronac,
                'AnoProjeto' => $buscarParecer['AnoProjeto'],
                'idEnquadramento' => $buscarParecer['idEnquadramento'],
                'Sequencial' => $buscarParecer['Sequencial'],
                'TipoParecer' => $buscarParecer['TipoParecer'],
                'ParecerFavoravel' => Seguranca::tratarVarAjaxUFT8($parecer),
                'dtParecer' => date('Y-m-d H:i:s'),
                'NumeroReuniao' => $numeroReuniao,
                'ResumoParecer' => utf8_decode($justificativa),
                'SugeridoUfir' => 0,
                'SugeridoReal' => $valorreal,
                'SugeridoCusteioReal' => 0,
                'SugeridoCapitalReal' => 0,
                'Atendimento' => 'S',
                'Logon' => $logon,
                'stAtivo' => 1,
                'idTipoAgente' => $tipoAgente
            );
            $idparecer = isset($buscarParecer['IdParecer']) ? $buscarParecer['IdParecer'] : $buscarParecer['idParecer'];

            //se parecer ativo nao � o Componente, inativa os outros e grava o do Componente
            if (!$buscarParecer or $buscarParecer['idTipoAgente'] != $tipoAgente) {
                try {
                    $dadosAtualizar = array('stAtivo' => 0);
                    $where = "idparecer = " . $idparecer;

                    $update = $tblParecer->alterar($dadosAtualizar, $where);
                    $inserir = $tblParecer->inserir($dados);
                    $this->_helper->json(array('error' => false));
                } catch (Exception $e) {
                    $this->_helper->json(array('error' => true, 'descricao' => $e->getMessage()));
                }
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                try {
                    $where = "idparecer = " . $idparecer;

                    $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_CNIC;

                    $tbDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
                    $idDocumentoAssinatura = $tbDocumentoAssinatura->getIdDocumentoAssinatura(
                        $idPronac,
                        $idTipoDoAtoAdministrativo
                    );

                    if ($idDocumentoAssinatura) {
                        $this->removerAssinatura($idPronac, $idDocumentoAssinatura);
                        $this->removerDocumentoAssinatura($idPronac, $idDocumentoAssinatura);
                    }

                    $update = $tblParecer->alterar($dados, $where);
                    $this->_helper->json(array('error' => false));
                } catch (Zend_Exception $e) {
                    $this->_helper->json(array('error' => true, 'descricao' => $e->getMessage()));
                }
                $this->_helper->viewRenderer->setNoRender(true);
            }
        } else {
            $this->_helper->json(array('error' => true, 'descricao' => 'N&atilde;o foi encontrado parecer v&aacute;lido da an&aacute;lise t&eacute;cnica.'));
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    private function fecharAssinatura($idPronac)
    {
        $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_CNIC;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $idPerfilDoAssinante = $GrupoAtivo->codGrupo;
        $idOrgaoDoAssinante = $GrupoAtivo->codOrgao;

        try {
            $tbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();

            $objAtoAdministrativo = $tbAtoAdministrativo->obterAtoAdministrativoAtual($idTipoDoAtoAdministrativo, $idPerfilDoAssinante, $idOrgaoDoAssinante);

            if (count($objAtoAdministrativo) > 0) {
                $idAtoAdministrativo = $objAtoAdministrativo['idAtoAdministrativo'];

                $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
                $data = array(
                    'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA
                );
                // TODO: buscar idParecer para ato de gestao
                //$idAtoDeGestao = '';

                $where = array(
                    'IdPRONAC = ?' => $idPronac,
                    'idTipoDoAtoAdministrativo = ?' => $idTipoDoAtoAdministrativo,
                    //'idAtoDeGestao = ?' => $idAtoDeGestao,
                    'cdSituacao = ?' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                    'stEstado = ?' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
                );

                $objModelDocumentoAssinatura->update($data, $where);
            }
        } catch (Zend_Exception $ex) {
            parent::message("Erro ao concluir " . $ex->getMessage(), "parecer/analise-cnic/emitirparecer/$idPronac", "ERROR");
        }
    }

    private function removerAssinatura($idPronac, $idDocumentoAssinatura)
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $idPerfilDoAssinante = $GrupoAtivo->codGrupo;

        $objDocumentoAssinatura = new Assinatura_Model_TbDocumentoAssinaturaMapper();

        if ($objDocumentoAssinatura->IsProjetoJaAssinado(
            $idPronac,
            Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_CNIC,
            $idPerfilDoAssinante)) {
            try {
                $tbAssinatura = new Assinatura_Model_DbTable_TbAssinatura();

                $tbAssinatura->delete(
                    array(
                        'idDocumentoAssinatura = ?' => $idDocumentoAssinatura
                    )
                );
            } catch (Exception $objException) {
                parent::message($objException->getMessage(), $origin);
            }
        }
    }

    private function removerDocumentoAssinatura($idPronac, $idDocumentoAssinatura)
    {
        $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();

        try {
            $tbDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $tbDocumentoAssinatura->delete(
                array(
                    'idDocumentoAssinatura = ?' => $idDocumentoAssinatura
                )
            );
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), $origin);
        }
    }
}
