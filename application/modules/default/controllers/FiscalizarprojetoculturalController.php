<?php
class FiscalizarprojetoculturalController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // título da página
        $auth = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario = new UsuarioDAO(); // objeto usuário
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuário esteja autenticado
            // verifica as permissões
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 135; // tecnico
            $PermissoesGrupo[] = 134; // coordenador
            $PermissoesGrupo[] = 123; //
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo está no array de permissões
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visão
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuário para a visão
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
        } // fecha if
        else { // caso o usuário não esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }
        //recupera ID do pre projeto (proposta)

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    // fecha método init()*/

    public function painelcontroletecnicofiscalizacaoAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autenticação
        $idUsuario = $auth->getIdentity()->usu_codigo;

        $usuarios = new Autenticacao_Model_Usuario();
        $agente = $usuarios->getIdUsuario($idUsuario);

        $idAgente = $agente['idAgente'];

        $aprovacaoDao = new Aprovacao();
        $selectAp = $aprovacaoDao->totalAprovadoProjeto(true);
        $abrangenciaDao = new Proposta_Model_DbTable_Abrangencia();
        $selectAb = $abrangenciaDao->abrangenciaProjeto(true);
        $projetosDao = new Projetos();

        $resp = $projetosDao->buscaProjetosFiscalizacao(
            $selectAb,
            $selectAp,
            false,
            false,
            array(
                    'tbFiscalizacao.idUsuarioInterno = ?' => $idUsuario
                        )
        );

        $this->view->projetosFiscalizacao = array(
            array('nome' => 'FISCALIZA&Ccedil;&Atilde;O EM ANDAMENTO', 'qtd' => 0, 'projetos' => array()),
            array('nome' => 'FISCALIZA&Ccedil;&Atilde;O EM ATRASO', 'qtd' => 0, 'projetos' => array())
        );
        $idFiscalizacaoAnt = null;
        foreach ($resp as $key => $val) {
            if ($idFiscalizacaoAnt != $val->idFiscalizacao) {
                if ($val->stAvaliacao == 0) {
                    $idFiscalizacaoAnt = $val->idFiscalizacao;
                    if (($val->stFiscalizacaoProjeto == 0 && date('Ymd', strtotime($val->dtFimFiscalizacaoProjeto)) >= date('Ymd'))) {
                        $num = 0;
                    } elseif ($val->stFiscalizacaoProjeto == 2) {
                        $num = 2;
                    } elseif (date('Ymd', strtotime($val->dtFimFiscalizacaoProjeto)) < date('Ymd') || $val->stFiscalizacaoProjeto == 1) {
                        $num = 1;
                    }
                    $this->view->projetosFiscalizacao[$num]['qtd']++;
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&nbsp;'] = $this->view->projetosFiscalizacao[$num]['qtd'];
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['PRONAC'] = "<a target='_blank' href='" . $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')) . "?idPronac=" . $val->IdPRONAC . "' >" . $val->AnoProjeto . $val->Sequencial . "</a>";
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Nome do Projeto'] = $val->NomeProjeto;
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Regi&atilde;o'] = $val->Regiao;
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['UF'] = $val->uf;
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Cidade'] = $val->cidade;
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&Aacute;rea'] = $val->dsArea;
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Segmento'] = $val->dsSegmento;
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Valor'] = number_format($val->TotalAprovado, 2, ',', '.');
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Mecanismo'] = $val->dsMecanismo;
                    if ($val->stPlanoAnual == 0) {
                        $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'N&atilde;o';
                    } else {
                        $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'Sim';
                    }

                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Data Inicio'] = date('d/m/Y', strtotime($val->dtInicioFiscalizacaoProjeto));
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Data Fim'] = date('d/m/Y', strtotime($val->dtFimFiscalizacaoProjeto));

                    $fiscalizarHref = $this->url(array('controller' => 'fiscalizarprojetocultural', 'action' => 'parecerdotecnico')) . '?idProjeto=' . $val->idProjeto . '&idFiscalizacao=' . $val->idFiscalizacao;

                    if (($val->stFiscalizacaoProjeto == 1 and date('Ymd', strtotime($val->dtFimFiscalizacaoProjeto)) >= date('Ymd'))) {
                        if ($val->stAvaliacao == 1) {
                            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '';
                        } else {
                            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '   <center><a href="' . $fiscalizarHref . '">
                                                                                                               <img src="../public/img/btn_busca.gif" alt="Fiscalizar"/>
                                                                                                            </a></center>';
                        }
                    } elseif ($val->stFiscalizacaoProjeto != 2 and (date('Ymd', strtotime($val->dtFimFiscalizacaoProjeto)) < date('Ymd'))) {
                        if ($val->stAvaliacao == 1) {
                            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '';
                        } else {
                            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '   <center><a href="' . $fiscalizarHref . '">
                                                                                                               <img src="../public/img/btn_busca.gif" alt="Fiscalizar"/>
                                                                                                            </a></center>';
                        }
                    }
                }
            }
        }
    }

    public function parecerdotecnicoAction()
    {
        $idFiscalizacao = $this->_getParam('idFiscalizacao');
        $ProjetosDAO = new Projetos();
        $Projeto = $ProjetosDAO->buscarProjetosFiscalizacao($idFiscalizacao);

        if (count($Projeto) < 1) {
            parent::message("Dados n&atilde;o localizados", "pesquisarprojetofiscalizacao/grid", "ERROR");
        } else {
            $this->view->historicoDevolucao = array();
            $this->view->projeto = $Projeto;
            $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $idFiscalizacao));

            try {
                $relatorios = new RelatorioFiscalizacao();
                $this->view->relatorioFiscalizacao = $relatorios->buscaRelatorioFiscalizacao($idFiscalizacao);
                $HistoricoDevolucaoDAO = new HistoricoDevolucaoFiscalizacao();
                if (isset($this->view->relatorioFiscalizacao)) {
                    $this->view->historicoDevolucao = $HistoricoDevolucaoDAO->buscaHistoricoDevolucaoFiscalizacao(array('idRelatorioFiscalizacao = ?' => $this->view->relatorioFiscalizacao->idRelatorioFiscalizacao));
                }
            } catch (Exception $e) {
                $this->view->relatorioFiscalizacao = array();
                $this->view->historicoDevolucao = array();
            }
        }
    }

    public function parecerdocoordenadorAction()
    {
        $this->_forward('parecerdotecnico', 'fiscalizarprojetocultural');
        $idFiscalizacao = $this->_getParam('idFiscalizacao');

        $ProjetosDAO = new Projetos();
//        $FiscalizacaoDAO = new Fiscalizacao();
//        $RelatorioFiscalizacaoDAO = new RelatorioFiscalizacao();

//        $aprovacaoDao = new Aprovacao();
//        $selectAp = $aprovacaoDao->totalAprovadoProjeto(true);
//        $abrangenciaDao = new Abrangencia();
//        $selectAb = $abrangenciaDao->abrangenciaProjeto(true);
//        $selectDOU = $aprovacaoDao->buscaDataPublicacaoDOU(true);

//        $Projeto = $ProjetosDAO->buscaProjetosFiscalizacao($selectAb, $selectAp, false, $selectDOU, array('tbFiscalizacao.idFiscalizacao = ?' => $idFiscalizacao));
        $Projeto = $ProjetosDAO->buscarProjetosFiscalizacao($idFiscalizacao);

        if (count($Projeto) < 1) {
            parent::message("Dados n&atilde;o localizados", "pesquisarprojetofiscalizacao/grid", "ERROR");
        } else {
            $this->view->projeto = $Projeto;
            $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $idFiscalizacao));

            try {
                $relatorios = new RelatorioFiscalizacao();
                $this->view->relatorioFiscalizacao = $relatorios->buscaRelatorioFiscalizacao($idFiscalizacao);
            } catch (Exception $e) {
                $this->view->relatorioFiscalizacao = array();
            }
        }
    }

    public function cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho)
    {
        if (!empty($arquivoNome) && !empty($arquivoTemp)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
            $arquivoBinario = Upload::setBinario($arquivoTemp); // binário
            $arquivoHash = Upload::setHash($arquivoTemp); // hash
        }

        // cadastra dados do arquivo
        $dadosArquivo = array(
            'nmArquivo' => $arquivoNome,
            'sgExtensao' => $arquivoExtensao,
            'dsTipoPadronizado' => $arquivoTipo,
            'nrTamanho' => $arquivoTamanho,
            'dtEnvio' => new Zend_Db_Expr('GETDATE()'),
            'dsHash' => $arquivoHash,
            'stAtivo' => 'A');
        $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

        // pega o id do último arquivo cadastrado
        $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
        $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

        // cadastra o binário do arquivo
        $dadosBinario = array(
            'idArquivo' => $idUltimoArquivo,
            'biArquivo' => $arquivoBinario);
        $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);

        return $idUltimoArquivo;
    }

    public function salvarelatoriotecnicoAction()
    {
//        $post = Zend_Registry::get('post');
        $idFiscalizacao = $_POST['idFiscalizacao'];
        $anexardocumentos = true;
        $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();

        if (count($_FILES) > 0) {
            foreach ($_FILES['arquivo']['name'] as $key => $val) {
                $arquivoNome = $_FILES['arquivo']['name'][$key];
                $arquivoTemp = $_FILES['arquivo']['tmp_name'][$key];
                $arquivoTipo = $_FILES['arquivo']['type'][$key];
                $arquivoTamanho = $_FILES['arquivo']['size'][$key];

                if (!empty($arquivoTemp)) {
                    $idArquivo = $this->cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho);
                    $ArquivoFiscalizacaoDao->inserir(array('idArquivo' => $idArquivo, 'idFiscalizacao' => $idFiscalizacao));
                }
            }
        }
        
        unset($_POST['dsJustificativaDevolucao']);
        $_POST['qtEmpregoDireto'] = str_replace('.', '', $_POST['qtEmpregoDireto']);
        $_POST['qtEmpregoIndireto'] = str_replace('.', '', $_POST['qtEmpregoIndireto']);
        //$_POST['dsParecerTecnico'] = $_POST['dsParecerTecnico'];

        
        // A partir da data 15/09/2013, todos estes campos receberao a informacao => Nao se aplica
        // *******************************************
        $_POST['stSiafi'] = 0;
        $_POST['stPrestacaoContas'] = 3;
        $_POST['stCumpridasNormas'] = 3;
        $_POST['stCumpridoPrazo'] = 3;
        $_POST['stPagamentoServidorPublico'] = 3;
        $_POST['stDespesaAdministracao'] = 3;
        $_POST['stTransferenciaRecurso'] = 3;
        $_POST['stDespesasPublicidade'] = 3;
        $_POST['stOcorreuAditamento'] = 3;
        $_POST['stSaldoAposEncerramento'] = 3;
        $_POST['stSaldoVerificacaoFNC'] = 3;
        $_POST['stDocumentacaoCompleta'] = 3;
        $_POST['stDespesaPosterior'] = 3;
        $_POST['stCienciaLegislativo'] = 3;
        $_POST['stFinalidadeEsperada'] = 3;
        $_POST['stPlanoTrabalho'] = 3;
        $_POST['dsConclusaoEquipe'] = ' ';
        // *******************************************
        // *******************************************
        
        
        $RelatorioFiscalizacaoDAO = new RelatorioFiscalizacao();
        $relatorio = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($idFiscalizacao);
        
        if (count($relatorio)) {
            $RelatorioFiscalizacaoDAO->alteraRelatorio($_POST, array('idFiscalizacao = ?' => $idFiscalizacao));
        } else {
            $RelatorioFiscalizacaoDAO->inserir($_POST);
        }

        if ($_POST['stAvaliacao']) {
            $FiscalizacaoDAO = new Fiscalizacao();
            $FiscalizacaoDAO->alteraSituacaoProjeto(2, $idFiscalizacao);

            parent::message("Formulário enviado com sucesso!", "pesquisarprojetofiscalizacao/grid", "CONFIRM");
        } else {
            parent::message("Dados salvos com sucesso!", "fiscalizarprojetocultural/parecerdotecnico" . '?idFiscalizacao=' . $idFiscalizacao, "CONFIRM");
        }
    }

    public function salvarelatoriocoordenadorAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autenticação
        $dados = $_POST;

        $anexardocumentos = false;
        $idUsuario = $auth->getIdentity()->usu_codigo;
        $idFiscalizacao = $dados['idFiscalizacao'];
        $dsParecer = $dados['dsParecer'];
        $stAprovar = $dados['stAprovar'];
        $idPronac = $dados['idPronac'];
        if (isset($dados['dsJustificativaDevolucao'])) {
            $dsJustificativaDevolucao = $dados['dsJustificativaDevolucao'];
            unset($dados['dsJustificativaDevolucao']);
        }
        unset($dados['dsParecer']);
        unset($dados['stAprovar']);
        unset($dados['idPronac']);

        if (isset($dados['qtEmpregoDireto'])) {
            $dados['qtEmpregoDireto'] = str_replace('.', '', $dados['qtEmpregoDireto']);
        }
        if (isset($dados['qtEmpregoIndireto'])) {
            $dados['qtEmpregoIndireto'] = str_replace('.', '', $dados['qtEmpregoIndireto']);
        }

        $AvaliacaoFiscalizacaoDAO = new AvaliacaoFiscalizacao();
        $FiscalizacaoDAO = new Fiscalizacao();
        $RelatorioFiscalizacaoDAO = new RelatorioFiscalizacao();
        $ArquivoFiscalizacaoDAO = new ArquivoFiscalizacao();
        $usuarios = new Autenticacao_Model_Usuario();
        $projetosDAO = new Projetos();

//        $Usuario = $usuarios->getIdUsuario($idUsuario);
//        if(!isset($Usuario->idAgente)){
//            parent::message("Não foi possível realizar a operação. Favor entrar em contato com os gestores do sistema!", "pesquisarprojetofiscalizacao/grid?tipoFiltro=analisados", "ERROR");
//        }
//        $idAvaliador = $Usuario->idAgente;
        
        $idAvaliador = $idUsuario;
        $foiAvaliado = 0;

        $relatorio = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($idFiscalizacao);
        $idRelatorioFiscalizacao = $relatorio['idRelatorioFiscalizacao'];

        $AvaliacaoFisc = false;
        if (!empty($idRelatorioFiscalizacao)) {
            $AvaliacaoFisc = $AvaliacaoFiscalizacaoDAO->buscaAvaliacaoFiscalizacao($idRelatorioFiscalizacao);
        }
        if ($AvaliacaoFisc || is_object($AvaliacaoFisc)) {
            $foiAvaliado = 1;
        }

        if (count($_FILES) > 0) {
            $anexardocumentos = true;
        }
        $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();

        if (count($_FILES) > 0) {
            foreach ($_FILES['arquivo']['name'] as $key => $val) {
                $arquivoNome = $_FILES['arquivo']['name'][$key];
                $arquivoTemp = $_FILES['arquivo']['tmp_name'][$key];
                $arquivoTipo = $_FILES['arquivo']['type'][$key];
                $arquivoTamanho = $_FILES['arquivo']['size'][$key];

                if (!empty($arquivoTemp)) {
                    $idArquivo = $this->cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho);
                    $ArquivoFiscalizacaoDao->inserir(array('idArquivo' => $idArquivo, 'idFiscalizacao' => $idFiscalizacao));
                }
            }
        }
        //este o codigo foi comentado porque quando o coordenador devolvia a fiscalizacao para o tecnico o parecer do tecnico estava sendo apagado
        if (isset($dados['dsParecerTecnico'])) {
            //$dados['dsParecerTecnico'] = Seguranca::tratarVarEditor($dados['dsParecerTecnico']);
        }

        if (count($relatorio)) {
            $RelatorioFiscalizacaoDAO->alteraRelatorio($dados, array('idFiscalizacao = ?' => $idFiscalizacao));
            if (isset($dsJustificativaDevolucao)) {
                $this->gravaHistoricoDevolucao($dsJustificativaDevolucao, $idRelatorioFiscalizacao);
            }
        }
//        else {
//            $idRelatorioFiscalizacao = $RelatorioFiscalizacaoDAO->insereRelatorio($dados);
//            if ($dsJustificativaDevolucao) {
//                $this->gravaHistoricoDevolucao($dsJustificativaDevolucao, $idRelatorioFiscalizacao);
//            }
//        }

        if ($foiAvaliado) {
            if ($stAprovar) {
                $AvaliacaoFiscalizacaoDAO->alteraAvaliacaoFiscalizacao(
                        array('idAvaliador' => $idAvaliador,
                    'dtAvaliacaoFiscalizacao' => date('Y-m-d H:i:s'),
                    'dsParecer' => $dsParecer),
                    array('idRelatorioFiscalizacao = ?' => $idRelatorioFiscalizacao)
                );

                $FiscalizacaoDAO->alteraSituacaoProjeto(3, $idFiscalizacao);
            } else {
                $AvaliacaoFiscalizacaoDAO->alteraAvaliacaoFiscalizacao(
                        array('idAvaliador' => $idAvaliador,
                    'dtAvaliacaoFiscalizacao' => date('Y-m-d H:i:s'),
                    'dsParecer' => $dsParecer),
                    array('idRelatorioFiscalizacao = ?' => $idRelatorioFiscalizacao)
                );
            }
        } else {
            if ($stAprovar) {
                $AvaliacaoFiscalizacaoDAO->insereAvaliacaoFiscalizacao(
                        array(
                            'idRelatorioFiscalizacao' => $idRelatorioFiscalizacao,
                            'idAvaliador' => $idAvaliador,
                            'dtAvaliacaoFiscalizacao' => date('Y-m-d H:i:s'),
                            'dsParecer' => $dsParecer
                )
                );
                $FiscalizacaoDAO->alteraSituacaoProjeto(3, $idFiscalizacao);
            } else {
                if (!empty($dsParecer)) {
                    $AvaliacaoFiscalizacaoDAO->insereAvaliacaoFiscalizacao(
                            array('idRelatorioFiscalizacao' => $idRelatorioFiscalizacao,
                                'idAvaliador' => $idAvaliador,
                                'dtAvaliacaoFiscalizacao' => date('Y-m-d H:i:s'),
                                'dsParecer' => $dsParecer
                    )
                    );
                }
            }
        }
        if ($dados['stAvaliacao']==0) {
            $FiscalizacaoDAO->alteraSituacaoProjeto(1, $idFiscalizacao);
            parent::message("Retornado ao técnico com sucesso!", "pesquisarprojetofiscalizacao/grid?tipoFiltro=analisados", "CONFIRM");
        }

        if ($stAprovar) {
            parent::message("Fiscalizaç&atilde;o aprovada com sucesso!", "pesquisarprojetofiscalizacao/grid?tipoFiltro=analisados", "CONFIRM");
        } else {
            parent::message("Dados salvos com sucesso!", "fiscalizarprojetocultural/parecerdocoordenador?idFiscalizacao=" . $idFiscalizacao, "CONFIRM");
        }
    }

    public function gravaHistoricoDevolucao($dsJustificativa, $idRelatorioFiscalizacao)
    {
        $dados = array('idRelatorioFiscalizacao' => $idRelatorioFiscalizacao, 'dsJustificativaDevolucao' => $dsJustificativa, 'dtEnvioDevolucao' => new Zend_Db_Expr('GETDATE()'));
        $where = array('idRelatorioFiscalizacao = ?' => $idRelatorioFiscalizacao);

        $HistoricoDevolucaoDAO = new HistoricoDevolucaoFiscalizacao();
        $HistoricoDevolucaoDAO->alteraHistoricoDevolucaoFiscalizacao(array('stDevolucao' => 1), $where);
        $HistoricoDevolucaoDAO->insereHistoricoDevolucaoFiscalizacao($dados);
    }

    public function excluirAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);

        $post = Zend_Registry::get('post');
        $resposta = array('result' => false, 'mensagem' => utf8_encode('N?o foi possivel 1!'));

        if ($post->idArquivoFiscalizacao) {
            $arquivofiscalizacaoDao = new ArquivoFiscalizacao();
            if ($arquivofiscalizacaoDao->delete(array('idArquivoFiscalizacao = ?' => $post->idArquivoFiscalizacao))) {
                $resposta = array('result' => true, 'mensagem' => 'Exclu&iacute;do com sucesso!');
            } else {
                $resposta = array('result' => false, 'mensagem' => utf8_encode('N?o foi possivel3!'));
            }
        }
        if ($post->idArquivo) {
            $ArquivoImagemDao = new ArquivoImagem();
            $rs2 = $ArquivoImagemDao->delete(array('idArquivo = ?' => $post->idArquivo));

            $arquivoDao = new Arquivo();
            $rs = $arquivoDao->delete(array('idArquivo = ?' => $post->idArquivo));

            if ($rs && $rs2) {
                $resposta = array('result' => true, 'mensagem' => 'Exclu&iacute;do com sucesso!');
            } else {
                $resposta = array('result' => false, 'mensagem' => utf8_encode('N?o foi possivel4!'));
            }
        }
        $this->_helper->json($resposta);
    }

    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        return $router->assemble($urlOptions, $name, $reset, $encode);
    }
}
