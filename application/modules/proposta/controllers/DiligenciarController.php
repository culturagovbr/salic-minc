<?php

class Proposta_DiligenciarController extends Proposta_GenericController
{
    private $idPronac = null;
    private $idProduto = null;
    private $situacao = null;
    private $tpDiligencia = null;
    private $idDiligencia = null;
    private $idAvaliacaoProposta = null;
    private $btnVoltar = null;// ajusta o link de voltar de acordo com o tipo de dilignecia

    public function init()
    {
        parent::init();

        $this->idPronac = $this->getRequest()->getParam('idPronac');
        $this->idDiligencia = $this->getRequest()->getParam('idDiligencia');
        $this->idAvaliacaoProposta = $this->getRequest()->getParam('idAvaliacaoProposta');
        $this->situacao = $this->getRequest()->getParam('situacao');
        $this->tpDiligencia = $this->getRequest()->getParam('tpDiligencia');
        $this->idProduto = $this->getRequest()->getParam('idProduto');

        if ($this->tpDiligencia) {
            $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
            $urlArray = array('controller' => 'verificarreadequacaodeprojeto');

            // ajusta o link de voltar de acordo com o tipo de dilignecia
            if ($GrupoAtivo->codGrupo == 122) { // diligencia na readequacao - coordenador acompanhamento
                $this->btnVoltar = $this->view->url(array_merge($urlArray, array('action' => 'verificarreadequacaodeprojetocoordacompanhamento')));
            } elseif ($this->tpDiligencia == 171) { // diligencia na readequacao - tecnico acompanhamento
                $this->btnVoltar = $this->view->url(array_merge($urlArray, array('action' => 'verificarreadequacaodeprojetotecnico')));
            } elseif ($this->tpDiligencia == 179) { // diligencia na readequacao - parecerista
                $this->btnVoltar = $this->view->url(array_merge($urlArray, array('action' => 'verificarreadequacaodeprojetoparecerista')));
            } else {
                $this->btnVoltar = 'javascript:voltar()';
            }
        }

        $this->view->btnVoltar = $this->btnVoltar; // botao voltar dinamico

        if ($this->idPronac) {
            $this->view->urlMenu = [
                'module' => 'projeto',
                'controller' => 'menu',
                'action' => 'obter-menu-ajax',
                'idPronac' => $this->idPronac
            ];
        }
    }

    public function indexAction()
    {
        $this->forward("listardiligenciaproponente");
    }

    public function cadastrardiligenciaAction()
    {
        $verificacaodao = new Verificacao();
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();
        $diligenciaDAO = new Diligencia();
        $post = Zend_Registry::get('post');

        $auth = Zend_Auth::getInstance(); // instancia da autenticacao
        $Usuario = new Autenticacao_Model_DbTable_Usuario();
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $usu_identificacao = trim($idagente['usu_identificacao']);
        $idagente = $idagente['idAgente'];

        $utl = $diligenciaDAO->buscarUltDiligencia(array('idPronac = ?' => $this->idPronac, 'stEnviado = ?' => 'N', 'stEstado  = ?' => 0, 'idSolicitante = ?' => new Zend_Db_Expr("isnull((SELECT usu_codigo FROM tabelas..usuarios WHERE usu_identificacao='" . $usu_identificacao . "'), (SELECT idAgente FROM Agentes.dbo.Agentes WHERE CNPJCPF='" . $usu_identificacao . "'))")))->current();
        if (count($utl) > 0) {
            $this->view->ultimo = $utl;
        }
        $this->view->idPronac = $this->idPronac;
        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->situacao = $this->situacao;
        $this->view->idProduto = $this->idProduto;
        $this->view->tpDiligencia = $this->tpDiligencia;

        if ($this->view->idPronac) {
            $resp = $Projetosdao->dadosProjeto(array('pro.IdPRONAC = ?' => $this->view->idPronac));
            $this->view->nmCodigo = 'PRONAC';
            $this->view->nmTipo = 'DO PROJETO';
        }
        if ($this->view->idPreProjeto) {
            $resp = $PreProjetodao->dadosPreProjeto(array('pre.idPreProjeto = ?' => $this->view->idPreProjeto));
            $this->view->nmCodigo = 'NR PROPOSTA';
            $this->view->nmTipo = 'DA PROPOSTA';
        }
        $tipoDiligencia = $verificacaodao->tipoDiligencia(array('idVerificacao = ?' => $this->tpDiligencia));

        if (isset($resp) && is_object($resp) && count($resp) > 0) {
            $this->view->pronac = $resp[0]->pronac;
            $this->view->nomeProjeto = $resp[0]->nomeProjeto;
        } else {
            $this->view->pronac = '';
            $this->view->nomeProjeto = '';
        }
        if (is_object($tipoDiligencia) && count($tipoDiligencia) > 0) {
            $this->view->tipoDiligencia = $tipoDiligencia[0]->Descricao;
        } else {
            $this->view->tipoDiligencia = '';
        }

        $this->view->dataSolicitacao = date('d/m/Y H:i');
    }

    /**
     *
     */
    public function visualizardiligenciaAction()
    {
        $verificacaodao = new Verificacao();
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();
        $DocumentosExigidosDao = new DocumentosExigidos();

        $post = Zend_Registry::get('post');
        //$this->view->dadosDiligencia    = $post;
        $this->view->idPronac = $this->idPronac;
        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->idProduto = $this->idProduto;

        $this->view->idDiligencia = $this->getRequest()->getParam('idDiligencia');
        $this->view->idAvaliacaoProposta = $this->getRequest()->getParam('idAvaliacaoProposta');

        if ($this->view->idDiligencia) {
            $resp = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->view->idPronac, 'dil.idDiligencia = ?' => $this->view->idDiligencia));
            $this->view->nmCodigo = 'PRONAC';
            $this->view->nmTipo = 'DO PROJETO';
            $this->view->tipoDiligencia = $resp[0]->tipoDiligencia;
        }
        if ($this->view->idAvaliacaoProposta) {
            if ($this->idPronac) {
                $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $this->idPronac));
                $idPreProjeto = $projeto[0]->idProjeto;
            }

            if ($this->idPreProjeto) {
                $idPreProjeto = $this->idPreProjeto;
            }
            $resp = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $idPreProjeto, ' aval.idAvaliacaoProposta = ?' => $this->view->idAvaliacaoProposta));

            $this->view->nmCodigo = 'Nr PROPOSTA';
            $this->view->nmTipo = 'DA PROPOSTA';
            $this->view->Descricao = $resp[0]->Descricao;
        }

        $this->view->stEnviado = $resp[0]->stEnviado;
        $this->view->pronac = $resp[0]->pronac;
        $this->view->nomeProjeto = $resp[0]->nomeProjeto;
        //$this->view->Proponente = $rd[0]->Proponente;
        $this->view->dataSolicitacao = date('d/m/Y H:i', strtotime($resp[0]->dataSolicitacao));
        if ($resp[0]->dataResposta != '') {
            $this->view->dataResposta = date('d/m/Y H:i', strtotime($resp[0]->dataResposta));
        }
        $this->view->solicitacao = $resp[0]->Solicitacao;
        $this->view->resposta = $resp[0]->Resposta;
        if ($resp[0]->idCodigoDocumentosExigidos) {
            $documento = $DocumentosExigidosDao->listarDocumentosExigido($resp[0]->idCodigoDocumentosExigidos);
            $this->view->DocumentosExigido = $documento[0]->Descricao;
            $this->view->Opcao = $documento[0]->Opcao;
        }

        if ($this->view->idDiligencia) {
            $arquivo = new Arquivo();
            $arquivos = $arquivo->buscarAnexosDiligencias($this->view->idDiligencia);
            $this->view->arquivos = $arquivos;
        }
    }

    /**
     * imprimirdiligenciaAction
     *
     * @access public
     * @return void
     */
    public function imprimirdiligenciaAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $verificacaodao = new Verificacao();
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();
        $DocumentosExigidosDao = new DocumentosExigidos();

        $post = Zend_Registry::get('get');
        $this->view->idPronac = $this->idPronac;
        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->idProduto = $this->idProduto;

        $this->view->idDiligencia = $post->idDiligencia;
        $this->view->idAvaliacaoProposta = $post->idAvaliacaoProposta;

        if ($this->view->idDiligencia) {
            $resp = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->view->idPronac, 'dil.idDiligencia = ?' => $this->view->idDiligencia));
            $this->view->nmCodigo = 'PRONAC';
            $this->view->nmTipo = 'DO PROJETO';
            $this->view->tipoDiligencia = $resp[0]->tipoDiligencia;
        }
        if ($this->view->idAvaliacaoProposta) {
            if ($this->idPronac) {
                $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $this->idPronac));
                $idPreProjeto = $projeto[0]->idProjeto;
            }

            if ($this->idPreProjeto) {
                $idPreProjeto = $this->idPreProjeto;
            }
            $resp = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $idPreProjeto, ' aval.idAvaliacaoProposta = ?' => $this->view->idAvaliacaoProposta));

            $this->view->nmCodigo = 'Nr PROPOSTA';
            $this->view->nmTipo = 'DA PROPOSTA';
            $this->view->Descricao = $resp[0]->Descricao;
        }
        $this->view->stEnviado = $resp[0]->stEnviado;
        $this->view->pronac = $resp[0]->pronac;
        $this->view->nomeProjeto = $resp[0]->nomeProjeto;
        $this->view->dataSolicitacao = date('d/m/Y H:i', strtotime($resp[0]->dataSolicitacao));
        if ($resp[0]->dataResposta != '') {
            $this->view->dataResposta = date('d/m/Y H:i', strtotime($resp[0]->dataResposta));
        }
        $this->view->solicitacao = $resp[0]->Solicitacao;
        $this->view->resposta = $resp[0]->Resposta;
        if ($resp[0]->idCodigoDocumentosExigidos) {
            $documento = $DocumentosExigidosDao->listarDocumentosExigido($resp[0]->idCodigoDocumentosExigidos);
            $this->view->DocumentosExigido = $documento[0]->Descricao;
            $this->view->Opcao = $documento[0]->Opcao;
        }

        if ($this->view->idDiligencia) {
            $arquivo = new Arquivo();
            $arquivos = $arquivo->buscarAnexosDiligencias($post->idDiligencia);
            $this->view->arquivos = $arquivos;
        }
    }

    /**
     * cadastrarrespostadiligenciaAction
     *
     * @access public
     * @return void
     */
    public function cadastrarrespostadiligenciaAction()
    {
        $post = Zend_Registry::get('post');
        $idArquivo = '';
        $Mensagem = '';
        if (!empty($_FILES) && is_file($_FILES['arquivo']['tmp_name'])) {
            $arquivoNome = $_FILES['arquivo']['name']; // nome
            $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
            $arquivoTipo = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash = Upload::setHash($arquivoTemp); // hash
            }

            $tipos = array('pdf');
            if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                parent::message("Favor selecionar o arquivo no formato PDF!", "/proposta/diligenciar/listardiligenciaproponente?idPronac=$this->idPronac", "ALERT");
            }

            if (!empty($this->idPronac) && $this->idPronac != "0") {
                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x" . $arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                    'nmArquivo' => $arquivoNome,
                    'sgExtensao' => $arquivoExtensao,
                    'biArquivo' => $data,
                    'dsDocumento' => 'Resposta de Dilig&ecirc;ncia',
                    'idPronac' => $this->idPronac,
                    'idTipoDocumento' => 3,
                    'idDiligencia' => $post->idDiligencia
                );
                $vw = new vwAnexarDocumentoDiligencia();
                $vw->inserirUploads($dadosArquivo);
            }

            $dados = array(
                'DtResposta' => new Zend_Db_Expr('GETDATE()'),
                'Resposta' => $_POST['dsResposta'],
                'idProponente' => $this->idUsuario,
                'stEnviado' => 'N'
            );
            $where = "idDiligencia = $post->idDiligencia";
            $diligenciaDAO = new Diligencia();
            $resp = $diligenciaDAO->update($dados, $where);
        }

        $verificacaodao = new Verificacao();
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();
        $DocumentosExigidosDao = new DocumentosExigidos();

        $this->view->idPronac = $this->idPronac;
        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->idProduto = $this->idProduto;
        $this->view->idDiligencia = $this->idDiligencia;
        $this->view->idAvaliacaoProposta = $this->idAvaliacaoProposta;
        $this->view->idUsuario = Zend_Auth::getInstance()->getIdentity()->IdUsuario;

        if ($this->view->idDiligencia) {
            $resp = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->view->idPronac, 'dil.idDiligencia = ?' => $this->view->idDiligencia));
            $this->view->nmCodigo = 'PRONAC';
            $this->view->nmTipo = 'DO PROJETO';
            $this->view->tipoDiligencia = $resp[0]->tipoDiligencia;
        }
        if ($this->view->idAvaliacaoProposta) {
            if ($this->idPronac) {
                $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $this->idPronac));
                $idPreProjeto = $projeto[0]->idProjeto;
            }
            if ($this->idPreProjeto) {
                $idPreProjeto = $this->idPreProjeto;
            }
            $resp = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $idPreProjeto, ' aval.idAvaliacaoProposta = ?' => $this->view->idAvaliacaoProposta));
            $this->view->nmCodigo = 'Nr PROPOSTA';
            $this->view->nmTipo = 'DA PROPOSTA';
        }

        $arquivo = new Arquivo();
        $arquivos = $arquivo->buscarAnexosDiligencias($this->idDiligencia);
        $this->view->arquivos = $arquivos;

        $this->view->arquivos = $arquivos;
        $this->view->pronac = $resp[0]->pronac;
        $this->view->nomeProjeto = $resp[0]->nomeProjeto;
        $this->view->dataSolicitacao = date('d/m/Y H:i', strtotime($resp[0]->dataSolicitacao));
        if ($resp[0]->dataResposta != '') {
            $this->view->dataResposta = date('d/m/Y H:i', strtotime($resp[0]->dataResposta));
        }
        $this->view->solicitacao = $resp[0]->Solicitacao;
        $this->view->resposta = $resp[0]->Resposta;

        if ($resp[0]->idCodigoDocumentosExigidos) {
            $documento = $DocumentosExigidosDao->listarDocumentosExigido($resp[0]->idCodigoDocumentosExigidos);
            $this->view->DocumentosExigido = $documento[0]->Descricao;
            $this->view->Opcao = $documento[0]->Opcao;
        }
        if (!empty($resp[0]->stEnviado) && !empty($documento)) {
            $this->view->retorna = "true";
        }
        $this->view->DocumentoExigido = $DocumentosExigidosDao->listarDocumentosExigido();

        # comprovantes recusados
        $comprovantePagamentoModel = new ComprovantePagamento();
        $this->view->comprovantesDePagamento = $comprovantePagamentoModel->pesquisarComprovanteRecusado($this->idPronac);
    }

    /**
     * excluirarquivoAction
     *
     * @access public
     * @return void
     */
    public function excluirarquivoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $Arquivo = new vwAnexarDocumentoDiligencia();
        $result = $Arquivo->excluirArquivo($_POST['arquivo'], $_POST['diligencia']);

        if (count($result) > 0) {
            $this->_helper->json(array('resposta' => true));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        die();
    }

    public function listardiligenciaproponenteAction()
    {

        //$this->operacoesDiligencia();
        //$post                     = Zend_Registry::get('post');
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();
        //$dao                      = new DiligenciarDao();
        //$this->view->idPronac     = 118389;

        if (strlen($this->idPronac) > 7) {
            $this->idPronac = Seguranca::dencrypt($this->idPronac);
        }

        $this->view->idPronac = $this->idPronac;
        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->idProduto = $this->idProduto;

        if ($this->view->idPronac) {
            if ($this->view->idProduto) {
                $this->view->diligencias = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->view->idPronac, 'dil.idProduto = ?' => $this->view->idProduto));
            } else {
                $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $this->idPronac));
                $_idProjeto = isset($projeto[0]->idProjeto) && !empty($projeto[0]->idProjeto) ? $projeto[0]->idProjeto : 0;
                $this->view->diligenciasProposta = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $_idProjeto, 'aval.ConformidadeOK = ? ' => 0));
                $this->view->diligencias = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->view->idPronac));
            }
        }
        if ($this->view->idPreProjeto) {

            $projeto = $Projetosdao->buscar(array('idProjeto = ?' => $this->view->idPreProjeto))->current();

            if ($projeto) {
                $tbAvaliarAdequacaoProjeto = new Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
                $this->view->diligenciasAdequacao = $tbAvaliarAdequacaoProjeto->obterAvaliacoesDiligenciadas(['a.idPronac = ?' => $projeto->IdPRONAC]);
            }

            $this->view->diligenciasProposta = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $this->view->idPreProjeto, 'aval.ConformidadeOK <> ? ' => 9));
            //$this->view->diligenciasProposta = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $this->view->idPreProjeto));
        }
    }

    /**
     *
     */
    public function listardiligenciaanalistaAction()
    {
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();

        $this->view->idPronac = $this->idPronac;
        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->situacao = $this->situacao;
        $this->view->idProduto = $this->idProduto;
        $this->view->tpDiligencia = $this->tpDiligencia;

        if ($this->view->idPronac) {
            if ($this->idProduto) {
                $this->view->diligencias = $Projetosdao->listarDiligencias(
                    array(
                        'pro.IdPRONAC = ?' => $this->idPronac,
                        'dil.idProduto = ?' => $this->idProduto,
                        'dil.stEnviado = ?' => 'S'
                    )
                );
            } else {
                $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $this->idPronac));
                $_idProjeto = isset($projeto[0]->idProjeto) && !empty($projeto[0]->idProjeto) ? $projeto[0]->idProjeto : 0;
                $this->view->diligenciasProposta = $PreProjetodao->listarDiligenciasPreProjeto(
                    array(
                        'pre.idPreProjeto = ?' => $_idProjeto,
                        'aval.ConformidadeOK = ?' => 0
                    )
                );
                $this->view->diligencias = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->idPronac));
            }
        } else {
            if ($this->view->idPreProjeto) {
                $this->view->diligenciasProposta = $dao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $this->idPreProjeto, 'aval.ConformidadeOK = ? ' => 0));
            }
        }
    }

    private $situacaoProjetoResposta = array('C30' => 'anterior', 'E62' => 'anterior',
        'E61' => 'anterior', 'E60' => 'anterior', 'E59' => 'anterior', 'E17' => 'E30', 'G18' => 'G54',
        'D25' => 'anterior', 'D33' => 'anterior',
        'B14' => 'anterior', 'E12' => 'anterior', 'E13' => 'anterior', 'E50' => 'anterior');
    private $situacaoProjetoNaoResposta = array('C30' => 'anterior', 'E62' => 'E66', 'E61' => 'E71',
        'E60' => 'E69', 'E59' => 'anterior', 'E17' => 'E20',
        'G18' => 'G20', 'D25' => 'mantem', 'B14' => 'anterior', 'E12' => 'anterior',
        'E13' => 'anterior', 'E50' => 'anterior');

    /**
     * situacaoProjeto
     *
     * @param mixed $situacoes
     * @param mixed $idPronac
     * @param string $texto
     * @access private
     * @return void
     */
    private function situacaoProjeto($situacoes, $idPronac, $texto = '')
    {
        $diligencia = new Diligencia();
        $diligencia = $diligencia->aberta($idPronac);

        $ProjetoDAO = new Projetos();
        $SituacaoDAO = new Situacao();
        $HistoricoSituacaoDAO = new HistoricoSituacao();
        $projeto = $ProjetoDAO->buscar(array(' IdPRONAC = ?' => $idPronac));
        $situacao = false;
        /* var_dump($situacoes[$projeto[0]->Situacao]);die; */
        if (array_key_exists($projeto[0]->Situacao, $situacoes)) {
            switch ($situacoes[$projeto[0]->Situacao]) {
                case 'anterior':

                    $historico = $HistoricoSituacaoDAO->buscar(array('AnoProjeto = ?' => $projeto[0]->AnoProjeto, 'Sequencial = ?' => $projeto[0]->Sequencial), array('Contador Desc'));

                    if ($historico) {
                        $situacao = $historico[0]->Situacao;
                    } else {
                        $situacao = false;
                    }
                    break;
                case 'mantem':
                    $situacao = false;
                    break;
                default:
                    $situacao = $situacoes[$projeto[0]->Situacao];
                    break;
            }
            if ($situacao) {
                $situacaoDesc = $SituacaoDAO->buscar(array('Codigo = ?' => $situacao));
                if ($texto == '') {
                    $texto = $situacaoDesc[0]->Descricao;
                }
                $data = array(
                    'Situacao' => $situacaoDesc[0]->Codigo,
                    'DtSituacao' => new Zend_Db_Expr('GETDATE()'),
                    'ProvidenciaTomada' => $texto
                );

                if ($diligencia->idTipoDiligencia == 645) {
                    $projeto = new Projetos();
                    $projeto->alterarSituacao($idPronac, null, 'E30', 'An&aacute;lise de resposta de dilig&ecirc;ncia');
                }
            }
        }
    }

    public function updatediligenciaAction()
    {
        $post = Zend_Registry::get('post');
        $diligenciaDAO = new Diligencia();
        $AvaliacaoPropostaDAO = new AvaliacaoProposta();
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $tblProjeto = new Projetos();

        $idArquivo = '';
        $Mensagem = '';
        if (is_file($_FILES['arquivo']['tmp_name'])) {
            $arquivoNome = $_FILES['arquivo']['name']; // nome
            $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome temporario
            $arquivoTipo = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
                $arquivoBinario = Upload::setBinario($arquivoTemp); // binario
                $arquivoHash = Upload::setHash($arquivoTemp); // hash
            }

            $tipos = array('pdf');
            if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                parent::message("Favor selecionar o arquivo no formato PDF!", "/proposta/diligenciar/listardiligenciaproponente?idPronac=$this->idPronac", "ALERT");
            }

            if (!empty($this->idPronac) && $this->idPronac != "0") {
                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x" . $arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                    'nmArquivo' => $arquivoNome,
                    'sgExtensao' => $arquivoExtensao,
                    'biArquivo' => $data,
                    'dsDocumento' => 'Resposta de Dilig&ecirc;ncia',
                    'idPronac' => $this->idPronac,
                    'idTipoDocumento' => 3,
                    'idDiligencia' => $post->idDiligencia
                );
                $vw = new vwAnexarDocumentoDiligencia();
                $vw->inserirUploads($dadosArquivo);
            }
        }

        if (!empty($retorno['Mensagem'])) {
            $post->verificaEnviado = 'N';
        }

        if ($post->idDiligencia) {
            $rsProjeto = $tblProjeto->buscar(array("IdPRONAC = ?" => $this->idPronac))->current();
            if (isset($rsProjeto->idProjeto) && !empty($rsProjeto->idProjeto)) {
                $rsPreProjeto = $tblPreProjeto->buscar(array("idPreProjeto = ?" => $rsProjeto->idProjeto))->current();
                $paramEdital = "";
                if ($rsPreProjeto->idEdital && $rsPreProjeto->idEdital != 0) {
                    $paramEdital = "&edital=sim";
                }
            }

            if ($post->idCodigoDocumentosExigidos) {
                $idCodigoDocumentosExigidos = $post->idCodigoDocumentosExigidos;
            } else {
                $idCodigoDocumentosExigidos = new Zend_Db_Expr('null');
            }

            $dados = array(
                'DtResposta' => new Zend_Db_Expr('GETDATE()'),
                'Resposta' => $_POST['resposta'],
                'idProponente' => $this->idUsuario,
                'stEnviado' => $post->verificaEnviado
            );
            $where = "idDiligencia = $post->idDiligencia";
            $resp = $diligenciaDAO->update($dados, $where);

            if ($post->verificaEnviado == 'S') {
                $this->situacaoProjeto(
                    $this->situacaoProjetoResposta,
                    $this->idPronac,
                    'Dilig&ecirc;ncia respondida pelo proponente, esperando decis&atilde;o.'
                );
            }
            $aux = "?idPronac={$this->idPronac}&idDiligencia={$this->idDiligencia}&idProduto={$this->idProduto}{$paramEdital}";
        }

        if ($post->idAvaliacaoProposta) {
            $rsPreProjeto = $tblPreProjeto->buscar(array("idPreProjeto = ?" => $this->idPreProjeto))->current();
            $paramEdital = "";
            if ($rsPreProjeto->idEdital && $rsPreProjeto->idEdital != 0) {
                $paramEdital = "&edital=sim";
            }

            if (!empty($idArquivo)) {
                $dados = array(
                    'dtResposta' => new Zend_Db_Expr('GETDATE()'),
                    'dsResposta' => $post->resposta,
                    'idArquivo' => $idArquivo,
                    'idCodigoDocumentosExigidos' => $post->idCodigoDocumentosExigidos,
                    'stEnviado' => $post->verificaEnviado
                );
            } else {
                $dados = array(
                    'dtResposta' => new Zend_Db_Expr('GETDATE()'),
                    'dsResposta' => $post->resposta,
                    'idCodigoDocumentosExigidos' => $post->idCodigoDocumentosExigidos,
                    'stEnviado' => $post->verificaEnviado
                );
            }
            $where = array('idAvaliacaoProposta = ?' => $post->idAvaliacaoProposta);
            $resp = $AvaliacaoPropostaDAO->update($dados, $where);

            $aux = "?idPreProjeto={$this->idPreProjeto}&idAvaliacaoProposta={$this->idAvaliacaoProposta}{$paramEdital}";
        }

        if (empty($retorno['Mensagem'])) {
            if ($resp) {
                if ($post->verificaEnviado == 'S') {
                    parent::message("Mensagem enviada com sucesso!", "/proposta/diligenciar/listardiligenciaproponente{$aux}", "CONFIRM");
                } else {
                    parent::message("Mensagem salva com sucesso!", "/proposta/diligenciar/listardiligenciaproponente{$aux}", "CONFIRM");
                }
            } else {
                parent::message('N&atildeo foi possivel realizar a opera&ccedil;&atildeo solicitada!', "/proposta/diligenciar/listardiligenciaproponente{$aux}", "ERROR");
            }
        } else {
            parent::message($retorno['Mensagem'], "/proposta/diligenciar/cadastrarrespostadiligencia{$aux}", "ERROR");
        }
    }

    /**
     * inserirdiligenciaAction
     *
     * @access public
     * @return void
     */
    public function inserirdiligenciaAction()
    {
        $diligenciaDAO = new Diligencia();
        $auth = Zend_Auth::getInstance();

        if (!$this->idPronac) {
            $this->_helper->redirector->goToRoute(
                array(
                    'controller' => 'diligenciar',
                    'action' => 'cadastrardiligencia',
                    'tipoAnalise' => 'inicial',
                    'idPronac' => $this->getRequest()->getParam('idPronac'),
                    'situacao' => $this->getRequest()->getParam('situacao'),
                    'tpDiligencia' => $this->getRequest()->getParam('tpDiligencia'),
                )
            );
        }

        // caso ja tenha diligencia para o pronac
        if (isset($this->idPronac) && !empty($this->idPronac)) {
            $buscarDiligenciaResp = $diligenciaDAO->buscar(array('idPronac = ?' => $this->idPronac, 'DtResposta ?' => array(new Zend_Db_Expr('IS NULL')), 'stEnviado = ?' => 'S'), array('idDiligencia DESC'), 0, 0, $this->getRequest()->getParam('idProduto'));
            if (count($buscarDiligenciaResp) > 0) {
                $queryString = '?idPronac=' . $this->idPronac . '&situacao=' . $this->getRequest()->getParam('situacao') . '&tpDiligencia=' . $this->getRequest()->getParam('tpDiligencia');
                parent::message('Existe dilig&ecirc;ncia aguardando resposta!', '/proposta/diligenciar/cadastrardiligencia' . $queryString, 'ALERT');
            }
        }

        $idagente = $auth->getIdentity()->usu_codigo;
        $idProduto = $this->getRequest()->getParam('idProduto') ? $this->getRequest()->getParam('idProduto') : new Zend_Db_Expr('null');

        $dados = array(
            'idPronac' => $this->getRequest()->getParam('idPronac'),
            'DtSolicitacao' => null,
            'Solicitacao' => $this->getRequest()->getParam('solicitacao'),
            'idSolicitante' => null,
            'idTipoDiligencia' => $this->getRequest()->getParam('tpDiligencia'),
            'idProduto' => $idProduto,
            'stEstado' => 0,
            'stEnviado' => 'N'
        );

        if ($this->getRequest()->getParam('btnEnvio') == 1) {
            $dados['DtSolicitacao'] = new Zend_Db_Expr('GETDATE()');
            $dados['idSolicitante'] = $idagente;
            $dados['stEnviado'] = 'S';
        }
        $rowDiligencia = $diligenciaDAO->inserir($dados);

        # Envia notificacao para o usuario atraves do aplicativo mobile.
        $modelProjeto = new Projetos();
        $projeto = $modelProjeto->buscarPorPronac((int)$this->getRequest()->getParam('idPronac'));
        $this->enviarNotificacao((object)array(
            'cpf' => $projeto->CNPJCPF,
            'pronac' => $projeto->Pronac,
            'idPronac' => $projeto->IdPRONAC,
            'idDiligencia' => $rowDiligencia->idDiligencia
        ));

        parent::message(
            "Dilig&ecirc;ncia enviada com sucesso!",
            "/proposta/diligenciar/listardiligenciaanalista/idPronac/{$this->getRequest()->getParam('idPronac')}/situacao/{$this->getRequest()->getParam('situacao')}/tpDiligencia/{$this->getRequest()->getParam('tpDiligencia')}",
            "CONFIRM"
        );
    }

    /**
     * salvardiligenciaAction
     *
     * @access public
     * @return void
     */
    protected function enviarNotificacao(stdClass $projeto)
    {
        $modelDispositivo = new Dispositivomovel();
        $listaDispositivos = $modelDispositivo->listarPorIdPronac($projeto->idPronac);
        $notification = new Minc_Notification_Message();
        $notification
            ->setCpf($projeto->cpf)
            ->setCodeDiligencia($projeto->idDiligencia)
            ->setCodePronac($projeto->idPronac)
            ->setListDeviceId($modelDispositivo->listarIdDispositivoMovel($listaDispositivos))
            ->setListResgistrationIds($modelDispositivo->listarIdRegistration($listaDispositivos))
            ->setTitle('Projeto ' . $projeto->pronac)
            ->setText('Recebeu nova dilig&ecirc;ncia!')
            ->setListParameters(array('projeto' => $projeto->idPronac))
            ->send();
    }

    public function salvardiligenciaAction()
    {
        $post = Zend_Registry::get('post');
        $diligenciaDAO = new Diligencia();
        $ProjetoDAO = new Projetos();
        $AvaliacaoPropostaDAO = new AvaliacaoProposta();
        $PreProjetoDAO = new Proposta_Model_DbTable_PreProjeto();
        $verificacaodao = new Verificacao();
        $auth = Zend_Auth::getInstance();

        if ($this->idPronac) {
            $agente = $ProjetoDAO->buscarAgenteProjeto(array(' pro.IdPRONAC = ?' => $this->idPronac))->current();
            if ($post->idProduto) {
                $idProduto = $post->idProduto;
            } else {
                $idProduto = new Zend_Db_Expr('null');
            }

            $Usuario = new Autenticacao_Model_DbTable_Usuario();
            $idagente = $auth->getIdentity()->usu_codigo;

            $stEnviado = 'N';
            if ($_POST['btnEnvio'] == 1) {
                $stEnviado = 'S';
            }

            $dados = array(
                'idPronac' => $post->idPronac,
                'DtSolicitacao' => new Zend_Db_Expr('GETDATE()'),
                'Solicitacao' => $_POST['solicitacao'],
                'idSolicitante' => $idagente,
                'idTipoDiligencia' => $post->tpDiligencia,
                'idProduto' => $idProduto,
                'stEstado' => 0,
                'stEnviado' => $stEnviado
            );

            $ult = $diligenciaDAO->alterar($dados, array('idDiligencia = ?' => $_POST['idDiligencia']));
            $tipoDiligencia = $verificacaodao->tipoDiligencia(array('idVerificacao = ?' => $post->tpDiligencia));

            $data = array(
                'Situacao' => $this->situacao,
                'DtSituacao' => date("Y-m-d"),
                'ProvidenciaTomada' => 'Projeto Diligenciado na Fase ' . $tipoDiligencia[0]->Descricao
            );
        }

        if ($_POST['btnEnvio'] == 1) {
            $msgAlert = 'Enviado com sucesso!';
        } else {
            $msgAlert = 'Salvo com sucesso!';
        }

        $aux = "?idPronac={$this->idPronac}&situacao={$this->situacao}&tpDiligencia={$post->tpDiligencia}";
        parent::message("$msgAlert", "/proposta/diligenciar/listardiligenciaanalista{$aux}", "CONFIRM");
    }

    private function eviarEmail($idPronac, $tpDiligencia)
    {
        $auth = Zend_Auth::getInstance();
        $tbTextoEmailDAO = new tbTextoEmail();
        $projetosDAO = new Projetos();

        $where = array(
            'idTextoEmail = ?' => 14
        );
        $textoEmail = $tbTextoEmailDAO->buscar($where)->current();

        $dadosProjeto = $projetosDAO->dadosProjetoDiligencia($idPronac)->current();

        $email = 'bruno.alexandre@cultura.gov.br';

        $mens = '<b>Projeto: ' . $dadosProjeto->pronac . ' - ' . $dadosProjeto->NomeProjeto . '<br> Proponente: ' .
            $dadosProjeto->Destinatario . '<br> </b>' . $textoEmail->dsTexto;


        $assunto = 'Diligencia na fase de ';
        switch ($tpDiligencia) {
            case 124:
                $assunto .= ' Analise Tecnica Inicial';
                break;
            case 126:
                $assunto .= ' Comissao CNIC ';
                break;
            case 171:
                $assunto .= ' Readequacao ';
                break;
            case 172:
                $assunto .= ' Fiscalizacao ';
                break;
            case 173:
                $assunto .= ' Relatorio Trimestral ';
                break;
            case 174:
                $assunto .= ' Prestacao de Contas ';
                break;
            case 178:
                $assunto .= ' Readequacao CNIC ';
                break;
            case 179:
                $assunto .= ' Readequacao Parecerista ';
                break;
            case 180:
                $assunto .= ' Relatorio Final';
                break;
            case 181:
                $assunto .= ' Checklist Analise Inicial ';
                break;
            case 182:
                $assunto .= ' Checklist Readequacao ';
                break;
        }
        $perfil = "SALICWEB";

        $enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens, $perfil);

        $tbHistoricoEmailDAO = new tbHistoricoEmail();

        $dados = array(
            'idPronac' => $idPronac,
            'idTextoemail' => 14,
            'iDAvaliacaoProposta' => new Zend_Db_Expr('NULL'),
            'DtEmail' => new Zend_Db_Expr('getdate()'),
            'stEstado' => 1,
            'idUsuario' => $auth->getIdentity()->usu_codigo,
        );

        $tbHistoricoEmailDAO->inserir($dados);
    }

    public function imprimirAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $verificacaodao = new Verificacao();
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();
        $DocumentosExigidosDao = new DocumentosExigidos();

        $post = Zend_Registry::get('post');
        $this->view->idPronac = $this->idPronac;
        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->idProduto = $this->idProduto;

        $this->view->idDiligencia = $post->idDiligencia;
        $this->view->idAvaliacaoProposta = $post->idAvaliacaoProposta;

        if ($this->view->idDiligencia) {
            $resp = $Projetosdao->listarDiligencias(array('pro.IdPRONAC = ?' => $this->view->idPronac, 'dil.idDiligencia in (?)' => $this->view->idDiligencia));
            $this->view->nmCodigo = 'PRONAC';
            $this->view->nmTipo = 'DO PROJETO';
            $this->view->dadosDiligencia = $resp;
        }

        if ($this->view->idAvaliacaoProposta) {
            if ($this->idPronac) {
                $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $this->idPronac));
                $idPreProjeto = $projeto[0]->idProjeto;
            }

            if ($projeto[0]->idProjeto) {
                $idPreProjeto = $projeto[0]->idProjeto;
            }

            $resp = $PreProjetodao->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $idPreProjeto, ' aval.idAvaliacaoProposta in (?)' => $this->view->idAvaliacaoProposta));
            $this->view->nmCodigo = 'Nr PROPOSTA';
            $this->view->nmTipo = 'DA PROPOSTA';
            $this->view->dadosDiligencia = $resp;
        }

        if ($resp[0]->idCodigoDocumentosExigidos) {
            $documento = $DocumentosExigidosDao->listarDocumentosExigido($resp[0]->idCodigoDocumentosExigidos);
            $this->view->DocumentosExigido = $documento[0]->Descricao;
            $this->view->Opcao = $documento[0]->Opcao;
        }

        $arquivos = array();
        if ($this->view->idDiligencia) {
            $arquivo = new Arquivo();
            foreach ($post->idDiligencia as $ids) {
                $arquivos[$ids] = $arquivo->buscarAnexosDiligencias($ids);
            }
        }
        $this->view->arquivos = $arquivos;
    }

    public function prorrogarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $post = Zend_Registry::get('post');
        $Diligenciadao = new Diligencia();
        $AvaliacaoPropostadao = new AvaliacaoProposta();
        $Projetosdao = new Projetos();
        $dados = array(
            'stProrrogacao' => "S"
        );
        if ($post->idPronac != '') {
            if ($post->idDiligencia) {
                $where = array(
                    'idPronac = ?' => $post->idPronac,
                    'idDiligencia = ?' => $post->idDiligencia
                );
                $prorrogado = $Diligenciadao->update($dados, $where);
            }
            if ($post->idAvaliacaoProposta) {
                $projeto = $Projetosdao->buscar(array('IdPRONAC = ?' => $this->idPronac));
                $where = array(
                    'idProjeto = ?' => $projeto[0]->idProjeto,
                    'idAvaliacaoProposta = ?' => $post->idAvaliacaoProposta
                );
                $prorrogado = $AvaliacaoPropostadao->update($dados, $where);
            }
        } else {
            $AvaliacaoPropostadao = new AvaliacaoProposta();
            $where = array(
                'idProjeto = ?' => $post->idPreProjeto,
                'idAvaliacaoProposta = ?' => $post->idAvaliacaoProposta
            );
            $prorrogado = $AvaliacaoPropostadao->update($dados, $where);
        }
        if ($prorrogado) {
            $this->_helper->json(array('result' => true, 'mensagem' => 'Prorrogado com sucesso.'));
        } else {
            $this->_helper->json(array('result' => false, 'mensagem' => 'N&atilde;o foi possivel!'));
        }
    }

    /**
     * checarprazorespostaAction
     * Descri�?o: checar os prazos para resposta de dilig?ncia.
     * O cursor � utilizado por a trigger que altera a situa�?o do projeto, quando o
     * propoenente responde a dilig?ncia, atua atrav�s do atribuito idPronac.
     *
     * @access public
     * @return void
     */
    public function checarprazorespostaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $diligenciaDao = new Diligencia();
        $AvaliacaoPropostaDao = new AvaliacaoProposta();
        //atualizar situa�?o do projeto para diligencias n?o respondidas
        $diligenciaProjeto = $diligenciaDao->diligenciasNaoRespondidas();
        foreach ($diligenciaProjeto as $value) {
            $this->situacaoProjeto($this->situacaoProjetoNaoResposta, $value->idPronac);
        }
        //resposta da diligencia Projeto
        $diligenciaProjeto = $diligenciaDao->diligenciasNaoRespondidas(true);
        $data = array(
            'DtResposta' => new Zend_Db_Expr('GETDATE()'),
            'stEnviado' => 'S',
            'RESPOSTA' => 'O PROPONENTE NÃO RESPONDEU A DILIGÊNCIA NO PRAZO DETERMINADO PELA IN 3 DE 30 DE DEZEMBRO DE 2010; ESPERANDO DECISÃO.'
        );
        $where = array('idPronac in (?)' => $diligenciaProjeto);
        $diligenciaDao->update($data, $where);

        //resposta da diligencia Proposta
        $diligenciaProposta = $AvaliacaoPropostaDao->diligenciasNaoRespondidas(true);
        $data = array(
            'dtResposta' => new Zend_Db_Expr('GETDATE()'),
            'stEnviado' => 'S',
            'dsResposta' => 'O PROPONENTE NÃO RESPONDEU A DILIGÊNCIA NO PRAZO DETERMINADO PELA IN 3 DE 30 DE DEZEMBRO DE 2010; ESPERANDO DECISÃO.'
        );
        $where = array('idPronac in (?)' => $diligenciaProposta);
        $AvaliacaoPropostaDao->update($data, $where);

    }

    public function listardiligenciaadmissibilidadeAction()
    {
        $Projetosdao = new Projetos();
        $PreProjetodao = new Proposta_Model_DbTable_PreProjeto();

        ini_set('memory_limit', '-1');

        $this->view->diligencias = $Projetosdao->listarDiligencias(array('dil.idTipoDiligencia = ?' => 124, 'dil.stEnviado = ?' => 'S', 'dil.stEstado = ?' => '0', 'dil.DtResposta ?' => new Zend_Db_Expr('IS NOT NULL'), 'dil.idProponente ?' => new Zend_Db_Expr('IS NOT NULL')));
        $this->view->diligenciasProposta = $PreProjetodao->listarDiligenciasPreProjeto(array('aval.dtResposta ?' => new Zend_Db_Expr('IS NOT NULL'), 'aval.stEnviado = ?' => 'S'));
    }
}
