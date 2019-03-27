<?php

class Fiscalizacao_FiscalizarprojetoculturalController extends MinC_Controller_Action_Abstract
{
    private $codUsuario = 0;
    const URL_ASSINATURA = '/assinatura/index/visualizar-projeto';

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        $Usuario = new UsuarioDAO();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if (!$auth->hasIdentity()) {
            return $this->_helper->redirector->goToRoute(array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'logout'
            ), null, true);
        }

        $PermissoesGrupo = array();

        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_FISCALIZACAO;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::TECNICO_FISCALIZACAO;

        if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
        }

        $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

        $this->view->usuario = $auth->getIdentity();
        $this->view->arrayGrupos = $grupos;
        $this->view->grupoAtivo = $GrupoAtivo->codGrupo;
        $this->view->orgaoAtivo = $GrupoAtivo->codOrgao;
        $this->codUsuario = $auth->getIdentity()->usu_codigo;

        $this->view->isCoordenador = in_array($GrupoAtivo->codGrupo, [
            Autenticacao_Model_Grupos::COORDENADOR_FISCALIZACAO,
            Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO
        ]);

        $this->view->isTecnico = in_array($GrupoAtivo->codGrupo, [
            Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO,
            Autenticacao_Model_Grupos::TECNICO_FISCALIZACAO
        ]);

        parent::init();
    }

    public function painelcontroletecnicofiscalizacaoAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idUsuario = $auth->getIdentity()->usu_codigo;

        $usuarios = new Autenticacao_Model_DbTable_Usuario();
        $agente = $usuarios->getIdUsuario($idUsuario);

        $idAgente = $agente['idAgente'];

        $aprovacaoDao = new Aprovacao();
        $selectAp = $aprovacaoDao->totalAprovadoProjeto(true);
        $abrangenciaDao = new Proposta_Model_DbTable_Abrangencia();
        $selectAb = $abrangenciaDao->abrangenciaProjeto(true);

        $tbFiscalizacao = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
        $resp = $tbFiscalizacao->buscaProjetosFiscalizacao(
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
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['PRONAC'] = "<a target='_blank' href='" . $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'index')) . "?idPronac=" . $val->IdPRONAC . "' >" . $val->AnoProjeto . $val->Sequencial . "</a>";
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

        if (empty($idFiscalizacao)) {
            throw new Exception("Fiscaliza&ccedil;&atilde;o n&atilde;o informada");
        }

        $where = [];
        $where['g.idFiscalizacao = ?'] = $idFiscalizacao;

        $idUsuarioInterno = $this->view->isTecnico ? $this->codUsuario : null;
        if ($idUsuarioInterno) {
            $where['g.idUsuarioInterno = ?'] = $idUsuarioInterno;
            $where['g.stFiscalizacaoProjeto in (?)'] = [
                Fiscalizacao_Model_TbFiscalizacao::ST_FISCALIZACAO_INICIADA,
                Fiscalizacao_Model_TbFiscalizacao::ST_FISCALIZACAO_OFICIALIZADA
            ];
        }

        $tbFiscalizacao = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
        $projeto = $tbFiscalizacao->buscarProjetosFiscalizacao($where);

        if (count($projeto) < 1) {
            parent::message("Dados n&atilde;o localizados", "fiscalizacao/pesquisarprojetofiscalizacao/grid", "ERROR");
        } else {
            $this->view->historicoDevolucao = array();
            $this->view->projeto = $projeto;
            $ArquivoFiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $idFiscalizacao));

            try {
                $relatorios = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();
                $this->view->relatorioFiscalizacao = $relatorios->buscaRelatorioFiscalizacao($idFiscalizacao);
                $HistoricoDevolucaoDAO = new Fiscalizacao_Model_DbTable_TbHistoricoDevolucaoFiscalizacao();
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
        $this->forward('parecerdotecnico', 'fiscalizarprojetocultural');
        $idFiscalizacao = $this->_getParam('idFiscalizacao');

        try {
            if (empty($idFiscalizacao)) {
                throw new Exception("Fiscaliza&ccedil;&atilde;o &eacute; obrigat&oacute;ria");
            }

            $where = [];
            $where['g.idFiscalizacao = ?'] = $idFiscalizacao;

            $tbFiscalizacao = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
            $projeto = $tbFiscalizacao->buscarProjetosFiscalizacao($where);

            if (count($projeto) < 1) {
                throw new Exception("Dados n&atilde;o localizados");
            }

            $servicoDocumentoAssinatura = new \Application\Modules\Fiscalizacao\Service\Assinatura\DocumentoAssinatura(
                $projeto[0]->IdPRONAC,
                Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_FISCALIZACAO,
                $idFiscalizacao
            );

            $assinatura = $servicoDocumentoAssinatura->obterProjetoDisponivelParaAssinatura();
            if (!empty($assinatura)) {
                $url = '%1$s/?idDocumentoAssinatura=%2$s&origin=%3$s';
                $urlAssinatura = sprintf(
                    $url,
                    self::URL_ASSINATURA,
                    $assinatura['idDocumentoAssinatura'],
                    '/fiscalizacao/pesquisarprojetofiscalizacao/grid'
                );

                parent::message("Parecer j&aacute; finalizado! Aguardando assinatura!",
                    $urlAssinatura,
                    "ALERT"
                );
            }

            $this->view->projeto = $projeto;
            $ArquivoFiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $idFiscalizacao));

            try {
                $relatorios = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();
                $this->view->relatorioFiscalizacao = $relatorios->buscaRelatorioFiscalizacao($idFiscalizacao);
            } catch (Exception $e) {
                $this->view->relatorioFiscalizacao = array();
            }
        } catch (Exception $e) {
            parent::message($e->getMessage(), "fiscalizacao/pesquisarprojetofiscalizacao/grid", "ERROR");
        }
    }

    public function cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho)
    {
        if (!empty($arquivoNome) && !empty($arquivoTemp)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
            $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
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

        // pega o id do �ltimo arquivo cadastrado
        $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
        $idUltimoArquivo = (int)$idUltimoArquivo[0]->id;

        // cadastra o bin�rio do arquivo
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
        $ArquivoFiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();

        try {

            if (count($_FILES) > 0) {
                $maxSizeFile = 10485760;

                foreach ($_FILES['arquivo']['name'] as $key => $val) {
                    $arquivoNome = $_FILES['arquivo']['name'][$key];
                    $arquivoTemp = $_FILES['arquivo']['tmp_name'][$key];
                    $arquivoTipo = $_FILES['arquivo']['type'][$key];
                    $arquivoTamanho = $_FILES['arquivo']['size'][$key];

                    if ($arquivoTamanho > $maxSizeFile) {
                        throw new Exception("O arquivo n&atilde;o pode ser maior do que 10MB!");
                    }

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

            $RelatorioFiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();
            $relatorio = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($idFiscalizacao);

            if (count($relatorio)) {
                $RelatorioFiscalizacaoDAO->alteraRelatorio($_POST, array('idFiscalizacao = ?' => $idFiscalizacao));
            } else {
                $RelatorioFiscalizacaoDAO->inserir($_POST);
            }

            if ($_POST['stAvaliacao']) {
                $FiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
                $FiscalizacaoDAO->alteraSituacaoProjeto(2, $idFiscalizacao);

                parent::message("Formul&aacute;rio enviado com sucesso!", "fiscalizacao/pesquisarprojetofiscalizacao/grid", "CONFIRM");
            } else {
                parent::message("Dados salvos com sucesso!", "fiscalizacao/fiscalizarprojetocultural/parecerdotecnico" . '?idFiscalizacao=' . $idFiscalizacao, "CONFIRM");
            }
        } catch (Exception $e) {
            parent::message("Erro ao salvar! " . $e->getMessage(), "fiscalizacao/fiscalizarprojetocultural/parecerdotecnico" . '?idFiscalizacao=' . $idFiscalizacao, "ERROR");
        }
    }

    public function salvarelatoriocoordenadorAction()
    {
        $auth = Zend_Auth::getInstance();
        $dados = $_POST;
        $idFiscalizacao = $dados['idFiscalizacao'];

        if (empty($idFiscalizacao)) {
            throw new Exception("Fiscaliza&ccedil;&atilde;o n&atilde;o informada");
        }

        $anexardocumentos = false;
        $idUsuario = $auth->getIdentity()->usu_codigo;
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

        $AvaliacaoFiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbAvaliacaoFiscalizacao();
        $FiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbFiscalizacao();
        $RelatorioFiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();
        $ArquivoFiscalizacaoDAO = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
        $usuarios = new Autenticacao_Model_DbTable_Usuario();
        $projetosDAO = new Projetos();

//        $Usuario = $usuarios->getIdUsuario($idUsuario);
//        if(!isset($Usuario->idAgente)){
//            parent::message("N�o foi poss�vel realizar a opera��o. Favor entrar em contato com os gestores do sistema!", "fiscalizacao/pesquisarprojetofiscalizacao/grid?tipoFiltro=analisados", "ERROR");
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
        $ArquivoFiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();

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
        if ($dados['stAvaliacao'] == 0) {
            $FiscalizacaoDAO->alteraSituacaoProjeto(1, $idFiscalizacao);
            parent::message("Retornado ao t&eacute;cnico com sucesso!", "fiscalizacao/pesquisarprojetofiscalizacao/grid?tipoFiltro=analisados", "CONFIRM");
        }

        if ($stAprovar) {

            $idDocumentoAssinatura = $this->iniciarFluxoAssinatura($idFiscalizacao);
            if ($idDocumentoAssinatura) {
                parent::message("Fiscaliza&ccedil;&atilde;o aprovada com sucesso! </br>
                 Um documento foi gerado e est&aacute; dispon&iacute;vel para o t&eacute;cnico respons&aacute;vel. </br>
                 Voc&ecirc; dever&aacute; assinar o documento ap&oacute;s o t&eacute;cnico. Acompanhe em 
                 <u><a class='white-text' href='/assinatura/index/gerenciar-assinaturas'>Assinatura</a></u> no menu.",
                    "fiscalizacao/pesquisarprojetofiscalizacao/grid?tipoFiltro=analisados",
                    "CONFIRM"
                );
            }

            parent::message("Fiscaliza&ccedil;&atilde;o aprovada com sucesso! Mas o documento n&atilde;o foi gerado", "fiscalizacao/pesquisarprojetofiscalizacao/grid?tipoFiltro=analisados", "CONFIRM");
        } else {
            parent::message("Dados salvos com sucesso!", "fiscalizacao/fiscalizarprojetocultural/parecerdocoordenador?idFiscalizacao=" . $idFiscalizacao, "CONFIRM");
        }
    }

    final private function iniciarFluxoAssinatura($idFiscalizacao)
    {
        if (empty($idFiscalizacao)) {
            throw new Exception(
                "Identificador da fiscaliza&ccedil;&atilde;o &eacute; necess&aacute;rio para acessar essa funcionalidade."
            );
        }

        $tbRelatorioFiscalizacao = new Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();
        $relatorio = $tbRelatorioFiscalizacao->buscaRelatorioFiscalizacao($idFiscalizacao);

        if (empty($relatorio['dsParecer'])) {
            throw new Exception(
                "&Eacute; necesss&aacute;rio ao menos um parecer para iniciar o fluxo de assinatura."
            );
        }

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $documentoAssinatura = $objDbTableDocumentoAssinatura->obterProjetoDisponivelParaAssinatura(
            $relatorio['IdPRONAC'],
            Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_FISCALIZACAO
        );

        if (count($documentoAssinatura) < 1) {
            $servicoDocumentoAssinatura = new \Application\Modules\Fiscalizacao\Service\Assinatura\DocumentoAssinatura(
                $relatorio['IdPRONAC'],
                Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_FISCALIZACAO,
                $relatorio['idFiscalizacao']
            );
            $idDocumentoAssinatura = $servicoDocumentoAssinatura->iniciarFluxo();
        } else {
            $idDocumentoAssinatura = $documentoAssinatura['idDocumentoAssinatura'];
        }

        return $idDocumentoAssinatura;
    }

    public function gravaHistoricoDevolucao($dsJustificativa, $idRelatorioFiscalizacao)
    {
        $dados = array('idRelatorioFiscalizacao' => $idRelatorioFiscalizacao, 'dsJustificativaDevolucao' => $dsJustificativa, 'dtEnvioDevolucao' => new Zend_Db_Expr('GETDATE()'));
        $where = array('idRelatorioFiscalizacao = ?' => $idRelatorioFiscalizacao);

        $HistoricoDevolucaoDAO = new Fiscalizacao_Model_DbTable_TbHistoricoDevolucaoFiscalizacao();
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
            $arquivofiscalizacaoDao = new Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
            if ($arquivofiscalizacaoDao->delete(array('idArquivoFiscalizacao = ?' => $post->idArquivoFiscalizacao))) {
                $resposta = array('result' => true, 'mensagem' => 'Exclu&iacute;do com sucesso!');
            } else {
                $resposta = array('result' => false, 'mensagem' => utf8_encode('N&atilde;o foi poss&iacute;vel!'));
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

    public function gerenciarAssinaturasAction()
    {
        $origin = $this->_request->getParam('origin');
        if ($origin != '') {
            $this->redirect($origin);
        }
        $this->redirect("/fiscalizacao/pesquisarprojetofiscalizacao/grid");
    }
}
