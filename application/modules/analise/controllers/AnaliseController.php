<?php

class Analise_AnaliseController extends Analise_GenericController
{
    private $idUsuario = null;
    private $idPreProjeto = null;
    private $idProjeto = null;

    private $codGrupo = null;
    private $codOrgao = null;

    const PERCENTUAL_MINIMO_CAPTACAO_PARA_ANALISE = 10;

    public function init()
    {
        parent::init();

        # define as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_ANALISE;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::TECNICO_ANALISE;

        if (!empty($_REQUEST['idPreProjeto'])) {
            $this->idPreProjeto = $_REQUEST['idPreProjeto'];
        }
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao

        //parent::perfil(1, $PermissoesGrupo);
        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        $this->idUsuario = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        if (isset($auth->getIdentity()->usu_codigo)) {
            // LEMBRAR :
            // $this->grupoAtivo->codOrgao  => Orgão logado   ==== Projetos.Orgao
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }
    }

    public function listarprojetosAction()
    {
        $this->view->usuarioEhCoordenador = true;

        if (Autenticacao_Model_Grupos::TECNICO_ANALISE == $this->codGrupo) {
            $this->view->usuarioEhCoordenador = false;
        }
    }

    public function listarProjetosAjaxAction()
    {
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');

        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : array("DtSituacao DESC");

        $vwPainelAvaliar = new Analise_Model_DbTable_vwProjetosAdequadosRealidadeExecucao();

        if (Autenticacao_Model_Grupos::TECNICO_ANALISE == $this->codGrupo) {
            $where['idTecnico = ?'] = $this->idUsuario;
        }

        $orgao = new Orgaos();
        $orgao = $orgao->codigoOrgaoSuperior($this->codOrgao);
        $orgaoSuperior = $orgao[0]['Codigo'];
        $where['Orgao = ?'] = $orgaoSuperior;

        $projetos = $vwPainelAvaliar->projetos($where, $order, $start, $length, $search);
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $aux = array();
        if (!empty($projetos)) {
            foreach ($projetos as $key => $projetos) {
                $projetos->NomeProjeto = utf8_encode($projetos->NomeProjeto);
                $projetos->Tecnico = utf8_encode($projetos->Tecnico);
                $projetos->Segmento = utf8_encode($projetos->Segmento);
                $projetos->Proponente = utf8_encode($projetos->Proponente);
                $projetos->Enquadramento = utf8_encode($projetos->Enquadramento);
                $projetos->Area = utf8_encode($projetos->Area);
                $projetos->VlSolicitado = number_format(($projetos->VlSolicitado), 2, ",", ".");
                $aux[$key] = $projetos;
            }
            $recordsTotal = $vwPainelAvaliar->projetosTotal($where);
            $recordsFiltered = $vwPainelAvaliar->projetosTotal($where, null, null, null, $search);
        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0));
    }

    public function visualizarprojetoAction()
    {
        $idPronac = $this->getRequest()->getParam('idpronac');

        try {
            if (empty($idPronac)) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            $objTbProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto = $objTbProjetos->findBy(array(
                'IdPRONAC' => $idPronac
            ));
            $this->view->projeto = $projeto;

            $idPreProjeto = $projeto['idProjeto'];
            $dados = Proposta_Model_AnalisarPropostaDAO::buscarGeral($idPreProjeto);
            $this->view->itensGeral = $dados;

            $movimentacao = new Proposta_Model_DbTable_TbMovimentacao();
            $movimentacao = $movimentacao->buscarStatusAtualProposta($idPreProjeto);
            $this->view->movimentacao = $movimentacao['Movimentacao'];

            //========== inicio codigo dirigente ================
            $arrMandatos = array();
            $this->view->mandatos = $arrMandatos;
            $preProjeto = new Proposta_Model_DbTable_PreProjeto();
            $rsDirigentes = array();

            $Empresa = $preProjeto->buscar(array('idPreProjeto = ?' => $idPreProjeto))->current();
            $idEmpresa = $Empresa->idAgente;

            $Projetos = new Projetos();
            $dadosProjeto = $Projetos->buscar(array('idProjeto = ?' => $idPreProjeto))->current();

            // Busca na tabela apoio ExecucaoImediata stproposta
            $tableVerificacao = new Proposta_Model_DbTable_Verificacao();
            if (!empty($this->view->itensGeral[0]->stProposta)) {
                $this->view->ExecucaoImediata = $tableVerificacao->findBy(array('idVerificacao' => $this->view->itensGeral[0]->stProposta));
            }

            $Pronac = null;
            if (count($dadosProjeto) > 0) {
                $Pronac = $dadosProjeto->AnoProjeto . $dadosProjeto->Sequencial;
            }
            $this->view->Pronac = $Pronac;

            if (isset($dados[0]->CNPJCPFdigirente) && $dados[0]->CNPJCPFdigirente != "") {
                $tblAgente = new Agente_Model_DbTable_Agentes();
                $tblNomes = new Nomes();
                foreach ($dados as $v) {
                    $rsAgente = $tblAgente->buscarAgenteENome(array('CNPJCPF=?' => $v->CNPJCPFdigirente))->current();
                    $rsDirigentes[$rsAgente->idAgente]['CNPJCPFDirigente'] = $rsAgente->CNPJCPF;
                    $rsDirigentes[$rsAgente->idAgente]['idAgente'] = $rsAgente->idAgente;
                    $rsDirigentes[$rsAgente->idAgente]['NomeDirigente'] = $rsAgente->Descricao;
                }

                $tbDirigenteMandato = new tbAgentesxVerificacao();
                foreach ($rsDirigentes as $dirigente) {
                    $rsMandato = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idEmpresa, 'idDirigente = ?' => $dirigente['idAgente'], 'stMandato = ?' => 0));
                    $NomeDirigente = $dirigente['NomeDirigente'];
                    $arrMandatos[$NomeDirigente] = $rsMandato;
                }
            }

            $this->view->dirigentes = $rsDirigentes;
            $this->view->mandatos = $arrMandatos;
            //============== fim codigo dirigente ================

            $this->view->itensTelefone = Proposta_Model_AnalisarPropostaDAO::buscarTelefone($this->view->itensGeral[0]->idAgente);
            $this->view->itensPlanosDistribuicao = Proposta_Model_AnalisarPropostaDAO::buscarPlanoDeDistribucaoProduto($idPreProjeto);
            $this->view->itensFonteRecurso = Proposta_Model_AnalisarPropostaDAO::buscarFonteDeRecurso($idPreProjeto);
            $this->view->itensLocalRealiazacao = Proposta_Model_AnalisarPropostaDAO::buscarLocalDeRealizacao($idPreProjeto);
            $this->view->itensDeslocamento = Proposta_Model_AnalisarPropostaDAO::buscarDeslocamento($idPreProjeto);

            # informacoes atuais
            $TPDC = new PlanoDistribuicao();
            $TPP = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $TPA = new Proposta_Model_DbTable_Abrangencia();
            $TPD = new Proposta_Model_DbTable_TbDeslocamento();
            $atual['itensPlanosDistribuicao'] = $TPDC->buscar(array('idProjeto = ?' => $idPreProjeto))->toArray();
            $atual['planilhaProjeto'] = $TPP->buscarPlanilhaCompleta($idPreProjeto);
            $atual['itensLocalRealizacao'] = $TPA->buscar(array('idProjeto' => $idPreProjeto));
            $atual['itensDeslocamento'] = $TPD->buscarDeslocamentosGeral(array('idProjeto' => $idPreProjeto));
            $this->view->atual = $atual;

            # informacoes do historico
            $PPM = new Proposta_Model_DbTable_PreProjetoMeta();
            $historico['planilhaProjeto'] = unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_tbplanilhaproposta'));
            $historico['itensLocalRealizacao'] = unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_abrangencia'));
            $historico['itensDeslocamento'] = unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_deslocamento'));
            $historico['itensPlanosDistribuicao'] = unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_planodistribuicaoproduto'));
            $historico['tbdetalhaplanodistribuicao'] = unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_tbdetalhaplanodistribuicao'));
            $historico['geral'] = unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_identificacaoproposta'));
            $historico['geral'] += unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_responsabilidadesocial'));
            $historico['geral'] += unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_detalhestecnicos'));
            $historico['geral'] += unserialize($PPM->buscarMeta($idPreProjeto, 'alterarprojeto_outrasinformacoes'));

            $this->view->historico = $historico;

            # documentos anexados proposta
            $tbl = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
            $rs = $tbl->buscarDocumentos(array("idProjeto = ?" => $idPreProjeto));
            $this->view->arquivosProposta = $rs;

            # documentos anexados proponente
            $tbA = new Proposta_Model_DbTable_TbDocumentosAgentes();
            $rsA = $tbA->buscarDocumentos(array("idAgente = ?" => $dados[0]->idAgente));
            $this->view->arquivosProponente = $rsA;

            # documentos anexados na diligencia
            $tblAvaliacaoProposta = new AvaliacaoProposta();
            $rsAvaliacaoProposta = $tblAvaliacaoProposta->buscar(array("idProjeto = ?" => $idPreProjeto, "idArquivo ?" => new Zend_Db_Expr("IS NOT NULL")));
            $tbArquivo = new tbArquivo();
            $arrDadosArquivo = array();
            $arrRelacionamentoAvaliacaoDocumentosExigidos = array();
            if (count($rsAvaliacaoProposta) > 0) {
                foreach ($rsAvaliacaoProposta as $avaliacao) {
                    $arrDadosArquivo[$avaliacao->idArquivo] = $tbArquivo->buscar(array("idArquivo = ?" => $avaliacao->idArquivo));
                    $arrRelacionamentoAvaliacaoDocumentosExigidos[$avaliacao->idArquivo] = $avaliacao->idCodigoDocumentosExigidos;
                }
            }
            $this->view->relacionamentoAvaliacaoDocumentosExigidos = $arrRelacionamentoAvaliacaoDocumentosExigidos;
            $this->view->itensDocumentoPreProjeto = $arrDadosArquivo;

            # pegando relacao de documentos exigidos(geral)
            $tblDocumentosExigidos = new DocumentosExigidos();
            $rsDocumentosExigidos = $tblDocumentosExigidos->buscar()->toArray();
            $arrDocumentosExigidos = array();
            foreach ($rsDocumentosExigidos as $documentoExigido) {
                $arrDocumentosExigidos[$documentoExigido["Codigo"]] = $documentoExigido;
            }
            $this->view->documentosExigidos = $arrDocumentosExigidos;
            $this->view->itensHistorico = Proposta_Model_AnalisarPropostaDAO::buscarHistorico($idPreProjeto);
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/analise/listarprojetos", "ERROR");
        }
    }

    public function formavaliaradequacaoAction()
    {
        $this->_helper->layout->disableLayout();

        $idPronac = $this->getRequest()->getParam('idpronac');

        $objTbProjetos = new Projeto_Model_DbTable_Projetos();
        $projeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $idPronac
        ));

        $this->view->projeto = $projeto;
    }

    public function salvaravaliacaadequacaoAction()
    {
        $auth = Zend_Auth::getInstance();
        $Logon = $auth->getIdentity()->usu_codigo;

        $params = $this->getRequest()->getParams();

        $idPronac = $params['idpronac'];
        try {
            if (empty($idPronac)) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            # alterar a situacao do projeto
            $tbProjetos = new Projetos();

            # verificar se o projeto já possui avaliador
            $tbAvaliacao = new Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
            $avaliacao = $tbAvaliacao->buscarUltimaAvaliacao($idPronac);

            if ($params['conformidade'] == 0) {

                # emcaminha e-mail para o proponente com o despacho do avaliador.
                $this->enviarEmail($idPronac, $params['observacao']);

                $situacao = Projeto_Model_Situacao::PROJETO_LIBERADO_PARA_AJUSTES;
                $providenciaTomada = 'Projeto liberado para o proponente adequar &agrave; realidade de execu&ccedil;&atilde;o, n&atilde;o podendo representar aumento de custo e observando as veda&ccedil;&otilde;es do Art. 42, conforme o Art. 72 da Instru&ccedil;&atilde;o Normativa.';

                $tbProjetos->alterarSituacao($params['idpronac'], '', $situacao, $providenciaTomada);

                if (!empty($avaliacao)) {
                    $tbAvaliacao->atualizarAvaliacaoNegativa($idPronac, $avaliacao['idTecnico'], $params['observacao']);
                }

                parent::message($providenciaTomada, "{$this->moduleName}/analise/listarprojetos", "CONFIRM");
            } elseif ($params['conformidade'] == 1) {
                $dadosProjeto = $tbProjetos->buscar(array('idPronac = ?' => $idPronac))->current();
                $idPreProjeto = $dadosProjeto['idProjeto'];

                if (!empty($avaliacao)) {
                    $tbAvaliacao->atualizarAvaliacaoPositiva($idPronac, $avaliacao['idTecnico'], $params['observacao']);

                    $tbPlanoDistribuicao = new PlanoDistribuicao();
                    $idVinculada = $tbPlanoDistribuicao->buscarIdVinculada($idPreProjeto);

                    $tbDistribuirParecer = new tbDistribuirParecer();
                    $jaExiste = $tbDistribuirParecer->buscar(array('idPronac = ?' => $idPronac))->current();
                    if (empty($jaExiste)) {
                        $tbDistribuirParecer->inserirDistribuicaoParaParecer($idPreProjeto, $idPronac, $idVinculada);
                    }

                    $tbAnaliseDeConteudo = new tbAnaliseDeConteudo();
                    $jaExiste = $tbAnaliseDeConteudo->buscar(array('idPronac = ?' => $idPronac))->current();
                    if (empty($jaExiste)) {
                        $tbAnaliseDeConteudo->inserirAnaliseConteudoParaParecerista($idPreProjeto, $idPronac);
                    }

                    $PlanilhaProjeto = new PlanilhaProjeto();
                    $jaExiste = $PlanilhaProjeto->buscar(array('idPronac = ?' => $idPronac))->current();
                    if (empty($jaExiste)) {
                        $PlanilhaProjeto->inserirPlanilhaParaParecerista($idPreProjeto, $idPronac);
                    }

                    # Consultar percentual de valor captado
                    $percentualCaptado = $this->percentualCaptadoByProposta($idPreProjeto, $idPronac);

                    $providenciaTomada = "O Projeto aguardar&aacute; o percentual m&iacute;nimo de capta&ccedil;&atilde;o e depois ser&aacute; encaminhado para unidade vinculada!";

                    $preProjeto = new Proposta_Model_DbTable_PreProjeto();
                    $dadosPreProjeto = $preProjeto->findBy(array('idPreProjeto' => $idPreProjeto));


                    if (($percentualCaptado >= self::PERCENTUAL_MINIMO_CAPTACAO_PARA_ANALISE)
                        or (!empty($dadosPreProjeto['stProposta']) && $dadosPreProjeto['stProposta'] != 610)) {

                        # alterar a situacao do projeto
                        $situacao = Projeto_Model_Situacao::ENCAMINHADO_PARA_ANALISE_TECNICA;
                        $providenciaTomada = 'Projeto encamihado &agrave; unidade vinculada para an&aacute;lise e emiss&atilde;o de parecer t&eacute;cnico';

                        $tbProjetos->alterarSituacao($idPronac, '', $situacao, $providenciaTomada);
                    }

                    $tbProposta = new Proposta_Model_DbTable_PreProjeto();
                    $proposta = $tbProposta->findBy(array('idpreprojeto' => $idPreProjeto));

                    # Faz a soma da planilha da proposta
//                    $TPP = new Proposta_Model_DbTable_TbPlanilhaProposta();
//                    $somaPlanilhaPropostaProdutos = $TPP->somarPlanilhaPropostaProdutos($idPreProjeto, 109);

                    $dados = array(
                        'DtInicioExecucao' => $proposta['DtInicioDeExecucao'],
                        'DtFimExecucao' => $proposta['DtFinalDeExecucao'],
                        'SolicitadoReal' => $tbProposta->valorTotalSolicitadoNaProposta($idPreProjeto),
                        'Logon' => $Logon
                    );

                    if (!empty($idPronac)) {
                        $where = array("IdPRONAC = ?" => $idPronac);
                    }

                    if (!empty($where)) {
                        $tbProjetos->update($dados, $where);
                    }
                }

                parent::message($providenciaTomada, "{$this->moduleName}/analise/listarprojetos", "CONFIRM");
            }
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "{$this->moduleName}/analise/listarprojetos", "ERROR");
        }
    }

    private function enviarEmail($idProjeto, $Mensagem, $pronac = null)
    {
        $auth = Zend_Auth::getInstance();
        $tbTextoEmailDAO = new tbTextoEmail();

        $tbProjetos = new Projetos();
        $dadosProjeto = $tbProjetos->dadosProjetoDiligencia($idProjeto);

        $tbHistoricoEmailDAO = new tbHistoricoEmail();

        foreach ($dadosProjeto as $d) :
            # para Producao comentar linha abaixo e para teste descomente ela
            # $d->Email =   'salicweb@gmail.com';
            $email = trim(strtolower($d->Email));
        $mens = '<b>Pronac: ' . $d->pronac . ' - ' . $d->NomeProjeto . '<br>Proponente: ' . $d->Destinatario . '<br> </b>' . $Mensagem;
        $assunto = 'Pronac:  ' . $d->pronac . ' - Avaliação adequação do projeto';
        $assunto = utf8_decode($assunto);

        $enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens);

        $dados = array(
                'idProjeto' => $idProjeto,
                'idTextoemail' => new Zend_Db_Expr('NULL'),
                'iDAvaliacaoProposta' => new Zend_Db_Expr('NULL'),
                'DtEmail' => new Zend_Db_Expr('getdate()'),
                'stEstado' => 1,
                'idUsuario' => $auth->getIdentity()->usu_codigo,
            );

        $tbHistoricoEmailDAO->inserir($dados);
        endforeach;
    }

    public function redistribuiranaliseitemAction()
    {
        $params = $this->getRequest()->getParams();
        try {
            if (empty($params['idpronac'])) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            if ($this->getRequest()->isPost()) {
                if (empty($params['idNovoTecnico']) || empty($params['tecnicoAtual'])) {
                    throw new Exception("Id do t&eacute;cnico &eacute; necess&aacute;rio para acessar essa funcionalidade.");
                }

                $dados = array(
                    'idTecnico' => $params['idNovoTecnico'],
                    'dtEncaminhamento' => new Zend_Db_Expr('GETDATE()'),
                );

                $where = array('idPronac = ?' => $params['idpronac'], 'idTecnico = ?' => $params['tecnicoAtual'], 'stEstado = ?' => true);

                $tbAvaliacao = new Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
                $tbAvaliacao->update($dados, $where);

                parent::message("An&aacute;lise redistribu&iacute;da com sucesso.", "/{$this->moduleName}/analise/listarprojetos", "CONFIRM");
            } else {
                $vw = new vwUsuariosOrgaosGrupos();

                $vwPainelAvaliar = new Analise_Model_DbTable_vwProjetosAdequadosRealidadeExecucao();

                $where['idpronac = ?'] = $params['idpronac'];

                $projetos = $vwPainelAvaliar->projetos($where, array(), 0, 1);
                $this->view->projeto = $projetos[0];

                $this->view->novosAnalistas = $vw->carregarTecnicosPorUnidadeEGrupo($this->codOrgao, 110);
            }
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/analise/listarprojetos", "ERROR");
        }
    }

    public function calcularPercentualCaptado($valorTotal, $valorCaptado)
    {
        if (empty($valorCaptado) || $valorCaptado <= 0) {
            return 0;
        }

        return number_format(($valorCaptado * 100) / $valorTotal, 2, ",", ".");
    }

    public function percentualCaptadoByProposta($idPreProjeto, $idProjeto)
    {
        if (empty($idProjeto) || empty($idPreProjeto)) {
            return false;
        }

        $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $total = $planilhaproposta->somarPlanilhaProposta($idPreProjeto)->toArray();

        $rsProjeto = ConsultarDadosProjetoDAO::obterDadosProjeto(array('idPronac' => $idProjeto));

        $valorTotal = $total['soma'];
        $valorcaptado = $rsProjeto[0]->ValorCaptado;

        return $this->calcularPercentualCaptado($valorTotal, $valorcaptado);
    }
}
