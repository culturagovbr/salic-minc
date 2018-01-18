<?php
include_once(APPLICATION_PATH . '/modules/default/models/dominio/TipoMensagem.php');
class MovimentacaodecontaController extends MinC_Controller_Action_Abstract
{
    /**
     * @access private
     * @var integer (idAgente do usuario logado)
     */
    private $getIdUsuario = 0; // codigo do usuorio logado
    private $getIdGrupo   = 0; // codigo do grupo logado
    private $getIdOrgao   = 0; // codigo do orgoo logado
    private $intTamPag    = 10;
    private $modal = "n";

    /**
     * @access private
     * @var object (tabelas utilizadas)
     */
    private $Captacao;
    private $tbTmpCaptacao;
    private $tbTipoInconsistencia;
    private $tbTmpInconsistenciaCaptacao;
    private $ContaBancaria;
    private $Projetos;
    private $Enquadramento;
    private $Agentes;
    private $Internet;
    private $spMovimentacaoBancaria;
    private $tbDepositoIdentificadoCaptacao;
    private $tbTmpDepositoIdentificado;

    /**
     * @access private
     * @var string (diretorio onde se enconta o arquivo .txt)
     */
    private $arquivoTXT = 'DepositoIdentificado';

    public function init()
    {
        $this->view->title = 'Salic - Sistema de Apoio às Leis de Incentivo à Cultura'; // titulo da pagina

        /* ========== INICIO PERFIL ==========*/
        // define os grupos que tem acesso
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121; // Tecnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
        $PermissoesGrupo[] = 129; // Tecnico de Acompanhamento
        $PermissoesGrupo[] = 148;
        $PermissoesGrupo[] = 151;
        parent::perfil(1, $PermissoesGrupo); // perfil novo salic

        // pega o idAgente do usuario logado
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        if (isset($auth->getIdentity()->usu_codigo)) { // autenticacao novo salic
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario['idAgente'] : 0;
        } else { // autenticacao espaco proponente
            $this->getIdUsuario = 0;
        }
        /* ========== FIM PERFIL ==========*/

        /* ========== INICIO ORGAO ========== */
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $this->getIdGrupo = $GrupoAtivo->codGrupo; // id do grupo ativo
        $this->getIdOrgao = $GrupoAtivo->codOrgao; // id do orgao ativo

        if ($this->getIdOrgao != 166 && $this->getIdOrgao != 272 && $this->getIdOrgao != 340&& $this->getIdOrgao != 341) { // aceita somente o �rg�o SEFIC/SACAV && SAV/CAP
            parent::message("Voc&ecirc; nao tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
        }
        /* ========== FIM ORGAO ========== */

        parent::init();

        //verifica se a funcionadade devera abrir em modal
        if ($this->_request->getParam("modal") == "s") {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $this->modal = "s";
            $this->view->modal = "s";
        } else {
            $this->modal = "n";
            $this->view->modal = "n";
        }

        # context
        $context = $this->_helper->getHelper('contextSwitch');
        $context->addActionContext('deposito-equivocado', 'json');
        $context->initContext();
    } // fecha metodo init()



    /**
     * Redireciona para o fluxo inicial
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        // redireciona para o fluxo inicial
        $this->_redirect('movimentacaodeconta/listar-inconsistencias');
    } // fecha metodo indexAction()



    /**
     * Metodo com o formulario para gerar o relatorio de contas rejeitadas
     * @access public
     * @param void
     * @return void
     */
    public function relatorioAction()
    {
    } // fecha metodo relatorioAction()



    /**
     * Metodo com o relatorio de contas rejeitadas.
     * Esse metodo so eh executado quando e feita uma solicitacao via POST.
     * @access public
     * @param void
     * @return void
     */
    public function gerarrelatorioAction()
    {
        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post            = Zend_Registry::get('post');
            $pronac          = $post->nr_pronac;
            $data_recibo     = $post->data_recibo;
            $proponente      = Mascara::delMaskCPFCNPJ($post->proponente);
            $incentivador    = Mascara::delMaskCPFCNPJ($post->incentivador);
            $data_credito    = $post->data_credito;

            try {
                if (!empty($data_recibo[0]) && !Data::validarData($data_recibo[0])) { // valida a data inicial do recibo
                    parent::message('A data inicial do recibo &eacute; inv&aacute;lida!', 'movimentacaodeconta/listar-inconsistencias', 'ERROR');
                } elseif (!empty($data_recibo[1]) && !Data::validarData($data_recibo[1])) { // valida a data final do recibo
                    parent::message('A data final do recibo &eacute; inv&aacute;lida!', 'movimentacaodeconta/listar-inconsistencias', 'ERROR');
                } elseif (!empty($data_credito[0]) && !Data::validarData($data_credito[0])) { // valida a data inicial de credito
                    parent::message('A data inicial de cr&eacute;dito &eacute; inv&aacute;lida!', 'movimentacaodeconta/listar-inconsistencias', 'ERROR');
                } elseif (!empty($data_credito[1]) && !Data::validarData($data_credito[1])) { // valida a data final de credito
                    parent::message('A data final de cr&eacute;dito &eacute; inv&aacute;lida!', 'movimentacaodeconta/listar-inconsistencias', 'ERROR');
                } else {
                    // busca os dados do banco e manda para a visao
                    //$this->tbTmpCaptacao = new tbTmpCaptacao();
                    //$this->view->dados   = $this->tbTmpCaptacao->buscarDados($pronac, $data_recibo, $proponente, $incentivador, $data_credito);

                    $arrBusca = array();
                    $arrBusca['idTipoInconsistencia IN (?)'] = array(2,3,7);
                    $this->tbTipoInconsistencia = new tbTipoInconsistencia();
                    $this->view->inconsistencias = $this->tbTipoInconsistencia->buscar($arrBusca);
                    $this->view->parametrosBusca = $_POST;
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message      = $e->getMessage();
                $this->view->message_type = 'ERROR';
            }
        } // fecha if post
        else {
            parent::message('Por favor, defina um filtro v&aacute;lido para gerar o relat&oacute;rio!', 'movimentacaodeconta/gerarrelatorio', 'ALERT');
        }
    } // fecha metodo gerarrelatorioAction()

    /**
     * t.tpValidacao = 2 // Periodo de Captacao Vencida
     * t.tpValidacao = 3 // Sem Incentivador Cadastrado
     * t.tpValidacao = 4 // Sem Tipo de Deposito
     * t.tpValidacao = 5 // Proponente e Incentivador Iguais
     * t.tpValidacao = 6 // Sem Proponente Cadastrado
     * t.tpValidacao = 7 // Agencia e Conta nao Cadastrada
     * t.tpValidacao = 8 // Sem Enquadramento
     * t.tpValidacao = 9 // Sem saldo para captar
     *
     *  121646 109003 1112073 119157  1012107
     *  	   109003 1112073		  1012107
     */
    public function listarInconsistenciasAction()
    {
        $this->intTamPag = 10;

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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['t.nrAnoProjeto+t.nrSequencial IS NOT NULL'] = '';
        $where['p.Orgao = ?'] = $this->getIdOrgao;
        $where['t.tpValidacao in (?)'] = array('2', '3', '4', '5', '6', '7', '8', '9');

        $pronac = $this->getRequest()->getParam('pronac');

        if (isset($pronac) && !empty($pronac)) {
            $where['t.nrAnoProjeto+t.nrSequencial = ?'] = $pronac;
            $this->view->pronac = $pronac;
        }

        $tbTmpCaptacao = new tbTmpCaptacao();
        $total = $tbTmpCaptacao->listarProjetosInconsistentes($this->getIdOrgao, $pronac, null, null, null)->count();
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbTmpCaptacao->listarProjetosInconsistentes($this->getIdOrgao, $pronac, $order, $tamanho, $inicio);
        $paginacao = array(
                "pag"=>$pag,
                "qtde"=>$this->intTamPag,
                "campo"=>$campo,
                "ordem"=>$ordem,
                "ordenacao"=>$ordenacao,
                "novaOrdem"=>$novaOrdem,
                "total"=>$total,
                "inicio"=>($inicio+1),
                "fim"=>$fim,
                "totalPag"=>$totalPag,
                "Itenspag"=>$this->intTamPag,
                "tamanho"=>$tamanho
             );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirListaInconsistenciasCaptacaoAction()
    {
        $this->intTamPag = 10;

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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $post = Zend_Registry::get('post');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['t.nrAnoProjeto+t.nrSequencial IS NOT NULL'] = '';
        $where['p.Orgao = ?'] = $this->getIdOrgao;
        $where['t.tpValidacao in (?)'] = array('2', '3', '4', '5', '6', '7', '8', '9');

        $pronac = $this->getRequest()->getParam('pronac');

        if (isset($pronac) && !empty($pronac)) {
            $where['t.nrAnoProjeto+t.nrSequencial = ?'] = $pronac;
            $this->view->pronac = $pronac;
        }

        $tbTmpCaptacao = new tbTmpCaptacao();
        $total = $tbTmpCaptacao->listarProjetosInconsistentes($this->getIdOrgao, $pronac, null, null, null)->count();
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbTmpCaptacao->listarProjetosInconsistentes($this->getIdOrgao, $pronac, $order, $tamanho, $inicio);

        if (isset($post->xls) && $post->xls) {
            $html = '';
            $html .= '<table style="border: 1px">';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="6">Relat&oacute;rio de inconsist&ecirc;ncias de conta capta&ccedil;&atilde;o</td></tr>';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="6">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
            $html .='<tr><td colspan="5"></td></tr>';

            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">#</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">PRONAC</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Nome do Projeto</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Proponente</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Ag&ecirc;ncia</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Conta</th>';
            $html .= '</tr>';

            $i=1;
            foreach ($busca as $projeto) {
                if (!empty($projeto->pronac)) {
                    $pr = $projeto->pronac;
                } else {
                    $pr = 'Conta sem PRONAC';
                }

                if (!empty($projeto->pronac)) {
                    $nm = $projeto->NomeProjeto;
                } else {
                    $nm = 'Conta sem Nome de Projeto';
                }

                if (!empty($projeto->Agencia)) {
                    $agencia = $projeto->Agencia;
                } else {
                    $agencia = '<em>N&atilde;o informada</em>';
                }

                if (!empty($projeto->ContaBloqueada)) {
                    $conta = $projeto->ContaBloqueada;
                } else {
                    $conta = '<em>N&atilde;o informada</em>';
                }

                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">'.$i.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$pr.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$nm.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.Validacao::mascaraCPFCNPJ($projeto->nrCpfCnpjProponente).'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$agencia.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$conta.'</td>';
                $html .= '</tr>';
                $i++;
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Relatorio_de_inconsistencias_de_conta_captacao.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->qtdRegistros = $total;
            $this->view->dados = $busca;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    }

    /**
     *
     */
    public function listarInconsistenciaDetalheAction()
    {
        $this->view->addScriptPath("./application/views/scripts/movimentacaodeconta");
        $tbTmpCaptacao = new tbTmpCaptacao();
        $tbTmpInconsistenciaCaptacao = new tbTmpInconsistenciaCaptacao();
        $captacoes = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao(
                array(
                    't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                    'p.Orgao = ?' => $this->getIdOrgao,
                    't.tpValidacao in ?' => new Zend_Db_Expr('(2, 3, 4, 5, 6, 7, 8, 9)'),
                        'p.IdPRONAC = ?' => $this->_request->get('idPronac'),
                ),
                array('1 ASC')
        );

        # redirect para tela de inconsistencia resolvida
        if (!$captacoes->count()) {
            parent::message('Todas as inconsist&ecirc;ncias desse projeto j&aacute; foram resolvidas.', 'movimentacaodeconta/listar-inconsistencias', 'ALERT');
//            $this->_forward('listar-inconsistencia-detalhe-resolvido');
        }

        $this->view->idPronac = $this->_request->get('idPronac');
        $this->view->projeto = $captacoes->current();
        $this->view->captacoes = $captacoes;
        $this->view->inconsistencias = array();
        foreach ($this->view->captacoes as $captacao) {
            $this->view->inconsistencias[$captacao->idTmpCaptacao] = $tbTmpInconsistenciaCaptacao->buscarInconsistenciasPorCaptacao(
                    $captacao->idTmpCaptacao
            );
        }
    }

    /**
     *
     */
    public function listarInconsistenciaDetalheResolvidoAction()
    {
    }

    /**
     *
     */
    public function updateTmpCaptacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $tbTmpCaptacao = new tbTmpCaptacao();
        $result = $tbTmpCaptacao->alterar(
            array('cdPatrocinio' => $this->_request->getParam('cdPatrocinio')),
            array('idTmpCaptacao = ?' => $this->_request->getParam('idTmpCaptacao'))
        );

        if ($result) {
            $this->_helper->json(array('resposta' => true), true);
        }
        $this->_helper->json(array('resposta' => false), true);
    }

    /**
     * Gera um aporte da captacao
     * Remove captacao
     *
     * Devido o fato de que esse caso nao possui um model, a regra esta sendo feita no controller(TA ERRADO)
     * Usando os parametros e chamando os metodos, para encapsular  esse negocio basta transferir para o model adequado
     */
    public function depositoEquivocadoAction()
    {
        try {
            $aporteCaptacao = new tbAporteCaptacao();
            $aporteCaptacao->cadastrarAporteCaptacaoPronac(
                $this->getRequest()->getParam('idPronac'),
                $this->getRequest()->getParam('captacao'),
                Zend_Auth::getInstance()->getIdentity()->usu_codigo
            );
        } catch (Exception $e) {
            dump($e, 1);
            $this->getResponse()->setHttpResponseCode(500);
        }
    }

    public function montaArrBuscaRelatorioInconsistencia($post)
    {
        //recebe os dados via post
        $post            = Zend_Registry::get('post');
        $pronac          = $post->nr_pronac;
        $data_recibo     = $post->data_recibo;
        $proponente      = Mascara::delMaskCPFCNPJ($post->proponente);
        $incentivador    = Mascara::delMaskCPFCNPJ($post->incentivador);
        $data_credito    = $post->data_credito;
        $idTipoInconsistencia = $post->idTpInconsistencia;

        $arrBusca = array();
        // busca pelo pronac
        if (!empty($pronac)) {
            $arrBusca["(t.nrAnoProjeto+t.nrSequencial) = ?"]=$pronac;
        }

        // busca pela data do recibo
        if (!empty($data_recibo)) {
            if (!empty($data_recibo[0]) && !empty($data_recibo[1])) {
                $arrBusca["t.dtChegadaRecibo >= ?"]= Data::dataAmericana($data_recibo[0]) . " 00:00:00";
                $arrBusca["t.dtChegadaRecibo <= ?"]= Data::dataAmericana($data_recibo[1]) . " 23:59:59";
            } else {
                if (!empty($data_recibo[0])) {
                    $arrBusca["t.dtChegadaRecibo >= ?"]= Data::dataAmericana($data_recibo[0]) . " 00:00:00";
                }
                if (!empty($data_recibo[1])) {
                    $arrBusca["t.dtChegadaRecibo <= ?"]= Data::dataAmericana($data_recibo[1]) . " 23:59:59";
                }
            }
        } // fecha if data do recibo

        // filtra pelo cpf/cnpj do proponente
        if (!empty($proponente)) {
            $arrBusca["t.nrCpfCnpjProponente = ?"]= $proponente;
        }

        // filtra pelo cpf/cnpj do incentivador
        if (!empty($incentivador)) {
            $arrBusca["t.nrCpfCnpjIncentivador = ?"]= $incentivador;
        }

        // busca pela data do cr&eacute;dito
        if (!empty($data_credito)) {
            if (!empty($data_credito[0]) && !empty($data_credito[1])) {
                $arrBusca["t.dtCredito >= ?"]= Data::dataAmericana($data_credito[0]) . " 00:00:00";
                $arrBusca["t.dtCredito <= ?"]= Data::dataAmericana($data_credito[1]) . " 23:59:59";
            } else {
                if (!empty($data_credito[0])) {
                    $arrBusca["t.dtCredito >= ?"]= Data::dataAmericana($data_credito[0]) . " 00:00:00";
                }
                if (!empty($data_credito[1])) {
                    $arrBusca["t.dtCredito <= ?"]= Data::dataAmericana($data_credito[1]) . " 23:59:59";
                }
            }
        } // fecha if data do recibo

        //tipo de inconsistencia
        $arrBusca["i.idTipoInconsistencia = ?"]=$idTipoInconsistencia;

        $arrTpInconsistenciaComPronac = array(7);
        if (in_array($idTipoInconsistencia, $arrTpInconsistenciaComPronac)) {
            $arrBusca["t.nrAnoProjeto+t.nrSequencial IS NULL"] = "(?)";
        } else {
            $arrBusca["t.nrAnoProjeto+t.nrSequencial IS NOT NULL"] = "(?)";
        }

        if ($idTipoInconsistencia!="7" || $this->getIdOrgao!='272') { //se a inconsistencia for 'Sem Agencia' nao incluir o orgao para que seja mostrado apenas na SEFIC
                $arrBusca["p.Orgao = ?"] = $this->getIdOrgao; //so busca projetos do orgao do usuario logado
        }

        return $arrBusca;
    }

    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Execucao Nao Vigente
     * @access public
     * @param void
     * @return void
     */
    public function projetosExecucaoNaoVigenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc1)) {
            $ordem[] = "{$post->ordenacaoInc1} {$post->tipoOrdenacaoInc1}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
        $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 1
            );




        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc1 = $_POST;

        $arrBusca = array();
        $arrBusca['idTipoInconsistencia IN (?)'] = array(2,3,7);
        $tbTipoInconsistencia = new tbTipoInconsistencia();
        $this->view->inconsistencias = $tbTipoInconsistencia->buscar($arrBusca);
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Captacao Nao Vigente
     * @access public
     * @param void
     * @return void
     */
    public function projetosCaptacaoNaoVigenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc2)) {
            $ordem[] = "{$post->ordenacaoInc2} {$post->tipoOrdenacaoInc2}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 2
            );

        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc2 = $_POST;
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Sem Incentivador
     * @access public
     * @param void
     * @return void
     */
    public function projetosSemIncentivadorAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc3)) {
            $ordem[] = "{$post->ordenacaoInc3} {$post->tipoOrdenacaoInc3}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 3
            );

        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc3 = $_POST;
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Sem Tipo de Deposito
     * @access public
     * @param void
     * @return void
     */
    public function projetosSemTipoDepositoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc4)) {
            $ordem[] = "{$post->ordenacaoInc4} {$post->tipoOrdenacaoInc4}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 4
            );

        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc4 = $_POST;
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Proponente e incentivador iguais
     * @access public
     * @param void
     * @return void
     */
    public function projetosProponenteIncentivadorIguaisAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc5)) {
            $ordem[] = "{$post->ordenacaoInc5} {$post->tipoOrdenacaoInc5}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 5
            );

        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc5 = $_POST;
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Proponente incompativel
     * @access public
     * @param void
     * @return void
     */
    public function projetosProponenteIncompativelAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc6)) {
            $ordem[] = "{$post->ordenacaoInc6} {$post->tipoOrdenacaoInc6}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 6
            );

        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc6 = $_POST;
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Agencia e Conta nao Cadastrada
     * @access public
     * @param void
     * @return void
     */
    public function projetosAgenciaContaNaoCadastradaAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc7)) {
            $ordem[] = "{$post->ordenacaoInc7} {$post->tipoOrdenacaoInc7}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 't.tpValidacao = ?' => 7
            );

        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc7 = $_POST;
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Sem enquadramento
     * @access public
     * @param void
     * @return void
     */
    public function projetosSemEnquadramentoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc8)) {
            $ordem[] = "{$post->ordenacaoInc8} {$post->tipoOrdenacaoInc8}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 8
            );


        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBuscaInc8 = $_POST;
    }
    /**
     * Metodo para listar os projetos para grid de inconsistencias do tipo Sem saldo para captar
     * @access public
     * @param void
     * @return void
     */
    public function projetosSemSaldoParaCaptarAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');

        $ordem = array();
        if (!empty($post->ordenacaoInc9)) {
            $ordem[] = "{$post->ordenacaoInc9} {$post->tipoOrdenacaoInc9}";
        } else {
            $ordem = array('1 ASC');
        }

        //monta array de busca
//            $arrBusca = $this->montaArrBuscaRelatorioInconsistencia($post);
        $arrBusca = array(
                 't.nrAnoProjeto+t.nrSequencial IS NOT NULL' => '',
                 'p.Orgao = ?' => $this->getIdOrgao,
                 't.tpValidacao = ?' => 9
            );

        //busca os dados do banco e manda para a visao
        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem, 5, 0);

        $idPronac = 0;
        $valorCredito = 0;
        $arrValores   = array();
        foreach ($rs as $projeto) {
            if ($idPronac != $projeto->IdPRONAC) {
                $valorCredito = 0;
                $idPronac     = $projeto->IdPRONAC;
            }

            $valorCredito += $projeto->vlValorCredito;
            $arrValores[$projeto->IdPRONAC] = $valorCredito;
        }

        $this->view->registros = $rs;
        $this->view->arrValores = $arrValores;
        $this->view->parametrosBuscaInc9 = $_POST;
    }
    /**
     * Metodo que monta tela com resumo de captacaoes e saldo para captar do projeto
     * @access public
     * @param void
     * @return void
     */
    public function formSaldoCaptacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $post = Zend_Registry::get('post');
        $idPronac = $post->idPronac;
        $this->view->idPronac  = $idPronac;
        $this->view->idTmpCaptacao  = $post->idTmpCaptacao;
        $vlTotalCaptadoNoLote = $post->vlTotalCaptadoNoLote;

        if (!empty($idPronac)) {
            $tbProjeto = new Projetos();
            $rsProjeto = $tbProjeto->buscar(array('IdPRONAC = ?'=>$idPronac))->current();

            if (!empty($rsProjeto)) {
                $tbAprovacao = new Aprovacao();
                $rsTotalAprovado = $tbAprovacao->fnTotalAprovadoProjeto($rsProjeto->AnoProjeto, $rsProjeto->Sequencial);
                $totalAprovado = $rsTotalAprovado->totalAprovado;
                //$totalAprovado = 150000;

                $tbCaptacao  = new Captacao();
                $rsTotalCaptado = $tbCaptacao->fnTotalCaptadoProjeto($rsProjeto->AnoProjeto, $rsProjeto->Sequencial);
                $totalCaptado = $rsTotalCaptado->totalCaptado;
                //$totalCaptado = 140000;

                //$vlTotalCaptadoNoLote = 150000;

                $vlAutorizadoCaptacao   = (($totalCaptado + $vlTotalCaptadoNoLote) > $totalAprovado) ? $totalAprovado-$totalCaptado : $vlTotalCaptadoNoLote;
                $vlExcedenteCaptado     = (($totalCaptado + $vlTotalCaptadoNoLote) > $totalAprovado) ? $vlTotalCaptadoNoLote-($totalAprovado-$totalCaptado) : 0;

                $this->view->totalAprovado = $totalAprovado;
                $this->view->totalCaptado  = $totalCaptado;
                $this->view->totalCaptadoNoLote     = $vlTotalCaptadoNoLote;
                $this->view->vlAutorizadoCaptacao   = $vlAutorizadoCaptacao;
                $this->view->vlExcedenteCaptado     = $vlExcedenteCaptado;
            }
        }
    }

    /**
     * Remanejamento de valor captado
     * @access public
     * @param void
     * @return void
     */
    public function remanejarValorCaptadoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $auth = Zend_Auth::getInstance();
        $idUsuario = $auth->getIdentity()->usu_codigo;
        $count = 0;

        $idTmpCaptacao  = $this->getRequest()->getParam('idTmpCaptacao');
        $vlrCaptacao    = $this->getRequest()->getParam('vlrCaptacao');
        $vlrDevolucao   = $this->getRequest()->getParam('vlrDevolucao');
        $tpApoio        = $this->getRequest()->getParam('tpApoio');
        $tpDevolucao    = $this->getRequest()->getParam('tpDevolucao');

        $this->view->idTmpCaptacao = $idTmpCaptacao;

        $tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $tbTmpCaptacao->buscarDadosParaRemanejamento($idTmpCaptacao);
        try {
            if (count($rs)>0) {
                if ($vlrCaptacao > 0) {

                        //INSERT NA TABELA SAC.dbo.Captacao
                    $dados = array(
                            'AnoProjeto'        => $rs->nrAnoProjeto,
                            'Sequencial'        => $rs->nrSequencial,
                            'NumeroRecibo'      => $rs->NumeroRecibo == '' ? '9999' : $rs->NumeroRecibo,
                            'CgcCpfMecena'      => $rs->nrCpfCnpjIncentivador,
                            'TipoApoio'         => $tpApoio,
                            'MedidaProvisoria'  => $rs->MedidaProvisoria,
                            'DtChegadaRecibo'   => Data::tratarDataZend($rs->dtChegadaRecibo, 'americano'),
                            'DtRecibo'          => Data::tratarDataZend($rs->dtCredito, 'americano'),
                            'CaptacaoReal'      => $vlrCaptacao,
                            'CaptacaoUfir'      => $rs->CaptacaoUfir,
                            'logon'             => $idUsuario,
                            'IdProjeto'         => $rs->idProjeto,
                        );

                    $Captacao = new Captacao();
                    $Captacao->inserir($dados);
                }

                $ContaBancaria = new ContaBancaria();
                $dadosCB = $ContaBancaria->buscarDados($rs->nrAnoProjeto.$rs->nrSequencial);

                if (count($dadosCB)>0) {
                    $dadosCB = $dadosCB->current();
                    $idCB = $dadosCB->IdContaBancaria;

                    # validacao removida para atender aporte mesmo em caso de valor zerado para a captacao
                    //if($vlrDevolucao > 0){
                    # INSERT NA TABELA SAC.dbo.tbAporteCaptacao
                    $dadosAporte = array(
                                'IdPRONAC'          => $rs->idProjeto,
                                'idVerificacao'     => $tpDevolucao,
                                'CNPJCPF'           => $rs->nrCpfCnpjIncentivador,
                                'idContaBancaria'   => $idCB,
                                'idUsuarioInterno'  => $idUsuario,
                                'dtCredito'         => Data::tratarDataZend($rs->dtCredito, 'americano'),
                                'vlDeposito'        => $vlrDevolucao,
                                'nrLote'            => $rs->NumeroRecibo == '' ? '9999': $rs->NumeroRecibo,
                                'dtLote'            => Data::tratarDataZend($rs->dtChegadaRecibo, 'americano'),
                            );

                    $tbAporteCaptacao = new tbAporteCaptacao();
                    $insertAporteCaptacao = $tbAporteCaptacao->inserir($dadosAporte);

                    if ($insertAporteCaptacao) {
                        $tbTmpInconsistenciaCaptacao = new tbTmpInconsistenciaCaptacao();
                        $tbTmpInconsistenciaCaptacao->delete(array('idTmpCaptacao = ?' => $idTmpCaptacao, 'idTipoInconsistencia = ?' => 9));

                        $tbTmpCaptacao->delete(array('idTmpCaptacao = ?' => $idTmpCaptacao));
                    } else {
                        $count++;
                    }
                    //}
                } else {
                    $count++;
                }
            } else {
                $count++;
            }
        } // fecha try
        catch (Exception $e) {
            $this->_helper->json(array('resposta'=>false, 'messagem'=>$e->getMessage()));
        }

        if ($count == 0) {
            $this->_helper->json(array('resposta'=>true, 'mensagem'=>'Dados atualizados com sucesso!'));
        } else {
            $this->_helper->json(array('resposta'=>false, 'mensagem'=>'Ocorreu um erro no processo de atualiza&ccedil;&atilde;o. Entre em contato com o administrador do sistema!'));
        }
    }

    /**
     * Metodo para listar os projetos para grid do relatorio conforme o tipo de inconsistencia
     * @access public
     * @param void
     * @return void
     */
    public function listarProjetosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout

        // recebe os dados via post
        $post            = Zend_Registry::get('post');
        $pronac          = $post->nr_pronac;
        $data_recibo     = $post->data_recibo;
        $proponente      = Mascara::delMaskCPFCNPJ($post->proponente);
        $incentivador    = Mascara::delMaskCPFCNPJ($post->incentivador);
        $data_credito    = $post->data_credito;
        $idTipoInconsistencia = $post->idTpInconsistencia;

        $arrBusca = array();
        // busca pelo pronac
        if (!empty($pronac)) {
            $arrBusca["(t.nrAnoProjeto+t.nrSequencial) = ?"]=$pronac;
        }

        // busca pela data do recibo
        if (!empty($data_recibo)) {
            if (!empty($data_recibo[0]) && !empty($data_recibo[1])) {
                $arrBusca["t.dtChegadaRecibo >= ?"]= Data::dataAmericana($data_recibo[0]) . " 00:00:00";
                $arrBusca["t.dtChegadaRecibo <= ?"]= Data::dataAmericana($data_recibo[1]) . " 23:59:59";
            } else {
                if (!empty($data_recibo[0])) {
                    $arrBusca["t.dtChegadaRecibo >= ?"]= Data::dataAmericana($data_recibo[0]) . " 00:00:00";
                }
                if (!empty($data_recibo[1])) {
                    $arrBusca["t.dtChegadaRecibo <= ?"]= Data::dataAmericana($data_recibo[1]) . " 23:59:59";
                }
            }
        } // fecha if data do recibo

        // filtra pelo cpf/cnpj do proponente
        if (!empty($proponente)) {
            $arrBusca["t.nrCpfCnpjProponente = ?"]= $proponente;
        }

        // filtra pelo cpf/cnpj do incentivador
        if (!empty($incentivador)) {
            $arrBusca["t.nrCpfCnpjIncentivador = ?"]= $incentivador;
        }

        // busca pela data do credito
        if (!empty($data_credito)) {
            if (!empty($data_credito[0]) && !empty($data_credito[1])) {
                $arrBusca["t.dtCredito >= ?"]= Data::dataAmericana($data_credito[0]) . " 00:00:00";
                $arrBusca["t.dtCredito <= ?"]= Data::dataAmericana($data_credito[1]) . " 23:59:59";
            } else {
                if (!empty($data_credito[0])) {
                    $arrBusca["t.dtCredito >= ?"]= Data::dataAmericana($data_credito[0]) . " 00:00:00";
                }
                if (!empty($data_credito[1])) {
                    $arrBusca["t.dtCredito <= ?"]= Data::dataAmericana($data_credito[1]) . " 23:59:59";
                }
            }
        } // fecha if data do recibo

        //tipo de inconsistencia
        $arrBusca["i.idTipoInconsistencia = ?"]=$idTipoInconsistencia;

        $arrTpInconsistenciaComPronac = array(2,3);
        if (in_array($idTipoInconsistencia, $arrTpInconsistenciaComPronac)) {
            $arrBusca["t.nrAnoProjeto+t.nrSequencial IS NOT NULL"] = "(?)";
        } else {
            $arrBusca["t.nrAnoProjeto+t.nrSequencial IS NULL"] = "(?)";
        }

        if ($idTipoInconsistencia!="7" || $this->getIdOrgao!='272') { //se a inconsistencia for 'Sem Agencia' nao incluir o orgao para que seja mostrado apenas na SEFIC
                $arrBusca["p.Orgao = ?"] = $this->getIdOrgao; //so busca projetos do orgao do usuario logado
        }

        $ordem = array();
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        } else {
            $ordem = array('1 ASC');
        }

        // busca os dados do banco e manda para a visao
        $this->tbTmpCaptacao = new tbTmpCaptacao();
        $rs = $this->tbTmpCaptacao->buscarProjetosRelatorioCaptacao($arrBusca, $ordem);

        $this->view->registros = $rs;
        $this->view->parametrosBusca = $_POST;

        $arrBusca = array();
        $arrBusca['idTipoInconsistencia IN (?)'] = array(2,3,7);
        $this->tbTipoInconsistencia = new tbTipoInconsistencia();
        $this->view->inconsistencias = $this->tbTipoInconsistencia->buscar($arrBusca);
    } // fecha metodo listarProjetosAction()


    /**
     * Metodo para montar o formulario de pesquisa do extrato de captacao
     * @access public
     * @param void
     * @return void
     */
    public function formExtratoDeContaCaptacaoAction()
    {
    } // fecha metodo formExtratoDeContaCaptacaoAction()

    /**
     * Metodo para listar os projetos para grid do relatorio conforme o tipo de inconsistencia
     * @access public
     * @param void
     * @return void
     */
    public function resultadoExtratoDeContaCaptacaoAction()
    {
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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //PRONAC
            $ordenacao = null;
        }

        $pag = 1;
        $post  = Zend_Registry::get('get');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $Usuariosorgaosgrupos = new Usuariosorgaosgrupos();
        $orgaoSuperior = $Usuariosorgaosgrupos->buscarOrgaoSuperiorUnico($this->getIdOrgao);

        $where = array();
        $where['c.siTransferenciaRecurso = ?'] = 0;
        $where['o.idSecretaria = ?'] = $orgaoSuperior->org_superior;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where["c.AnoProjeto+c.Sequencial = ?"] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                    case '': //captou 20%
                        $where['SAC.dbo.fnPercentualCaptado(c.AnoProjeto, c.Sequencial) >= ?'] = 20;
                        break;
                    case 'nc': //nao captou 20%
                        $where['SAC.dbo.fnPercentualCaptado(c.AnoProjeto, c.Sequencial) < ?'] = 20;
                        break;
                }
        } else {
            $where['SAC.dbo.fnPercentualCaptado(c.AnoProjeto, c.Sequencial) >= ?'] = 20;
        }

        $tbCaptacao = new Captacao();
        $total = $tbCaptacao->buscaExtratoCaptacao($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $busca = $tbCaptacao->buscaExtratoCaptacao($where, $order);
        } else {
            $busca = $tbCaptacao->buscaExtratoCaptacao($where, $order, $tamanho, $inicio);
        }

        $paginacao = array(
                    "pag"=>$pag,
                    "qtde"=>$this->intTamPag,
                    "campo"=>$campo,
                    "ordem"=>$ordem,
                    "ordenacao"=>$ordenacao,
                    "novaOrdem"=>$novaOrdem,
                    "total"=>$total,
                    "inicio"=>($inicio+1),
                    "fim"=>$fim,
                    "totalPag"=>$totalPag,
                    "Itenspag"=>$this->intTamPag,
                    "tamanho"=>$tamanho
             );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }


    public function imprimirExtratoDeContaCaptacaoAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $post  = Zend_Registry::get('post');

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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(1); //PRONAC
            $ordenacao = null;
        }

        $pag = 1;
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $Usuariosorgaosgrupos = new Usuariosorgaosgrupos();
        $orgaoSuperior = $Usuariosorgaosgrupos->buscarOrgaoSuperiorUnico($this->getIdOrgao);

        $where = array();
        $where['c.siTransferenciaRecurso = ?'] = 0;
        $where['o.idSecretaria = ?'] = $orgaoSuperior->org_superior;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where["c.AnoProjeto+c.Sequencial = ?"] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                    case '': //captou 20%
                        $where['SAC.dbo.fnPercentualCaptado(c.AnoProjeto, c.Sequencial) >= ?'] = 20;
                        break;
                    case 'nc': //nao captou 20%
                        $where['SAC.dbo.fnPercentualCaptado(c.AnoProjeto, c.Sequencial) < ?'] = 20;
                        break;
                }
        } else {
            $where['SAC.dbo.fnPercentualCaptado(c.AnoProjeto, c.Sequencial) >= ?'] = 20;
        }

        $tbCaptacao = new Captacao();
        $total = $tbCaptacao->buscaExtratoCaptacao($where, $order, null, null, true);

        $fim = $inicio + $this->intTamPag;
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $busca = $tbCaptacao->buscaExtratoCaptacao($where, $order);
        } else {
            $busca = $tbCaptacao->buscaExtratoCaptacao($where, $order, $tamanho, $inicio);
        }

        if (isset($post->xls) && $post->xls) {
            $html = '';
            $html .= '<table style="border: 1px">';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16; font-weight: bold;" colspan="12">Transfer&ecirc;ncia de Recurso</td></tr>';
            $html .='<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10" colspan="12">Data do Arquivo: '. Data::mostraData() .'</td></tr>';
            $html .='<tr><td colspan="12"></td></tr>';

            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">#</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">PRONAC</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Situa&ccedil;&atilde;o</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">CPF/CNPJ</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Incentivador</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">N&ordm; do Lote</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Dt. do Lote</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Dt. Capta&ccedil;&atilde;o</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Tipo de Apoio</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Conta Liberada</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">% Captado</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Vl. Captado</th>';
            $html .= '</tr>';

            $i=1;
            foreach ($busca as $projeto) {
                if (isset($projeto->DtLiberacao) && !empty($projeto->DtLiberacao)) {
                    $DtLiberacao = 'Sim';
                } else {
                    $DtLiberacao = '<span style="color:red; font-weight: bold;">N&atilde;o</span>';
                }

                $CaptacaoReal = 'R$ '.number_format($projeto->CaptacaoReal, '2', ',', '.');

                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">'.$i.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$projeto->PRONAC.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$projeto->Situacao.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.Validacao::mascaraCPFCNPJ($projeto->CgcCpfMecena).'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$projeto->Incentivador.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$projeto->NumeroRecibo.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.date("d/m/Y", strtotime($projeto->DtChegadaRecibo)).'</td>';
                $html .= '<td style="border: 1px dotted black;">'.date("d/m/Y", strtotime($projeto->DtRecibo)).'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$projeto->TipoApoio.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$DtLiberacao.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$projeto->Percentual.'</td>';
                $html .= '<td style="border: 1px dotted black;">'.$CaptacaoReal.'</td>';
                $html .= '</tr>';
                $i++;
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Transferencia_de_recurso.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->dados = $busca;
            $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        }
    } // fecha metodo listarProjetosAction()


    public function transferenciaContaCaptacaoAction()
    {
        if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['liberado'])) {
            parent::message('N&atilde;o foi poss&iacute;vel realizar a transfer&ecirc;ncia.', 'movimentacaodeconta/resultado-extrato-de-conta-captacao', 'ERROR');
        }

        $idCaptacao = $_GET['id'];
        $captacao = new Captacao();
        $dadosCaptacao = $captacao->buscar(array('Idcaptacao = ?' => $idCaptacao));

        if (count($dadosCaptacao)>0) {
            $auth = Zend_Auth::getInstance();
            $idusuario = $auth->getIdentity()->usu_codigo;

            $dados = array('siTransferenciaRecurso' => 1, 'dtTransferenciaRecurso' => new Zend_Db_Expr('GETDATE()'), 'logon' => $idusuario);
            $where = array('Idcaptacao = ?' => $idCaptacao);
            $captacao->update($dados, $where);

            $vlCaptado = $dadosCaptacao[0]->CaptacaoReal;

            $Projetos = new Projetos();
            $dadosProjetos = $Projetos->buscar(array('AnoProjeto = ?'=>$dadosCaptacao[0]->AnoProjeto,'Sequencial = ?'=>$dadosCaptacao[0]->Sequencial));
            $getdate = date('d/m/Y');
            $valorTransferido = @number_format(($vlCaptado), 2, ",", ".");

            $dadosP = array(
                    'ProvidenciaTomada' => 'Transfer&ecirc;ncia de recursos entre conta capta&ccedil;&atilde;o e conta movimento no valor de R$'.$valorTransferido.' em '.$getdate.'.',
                    'Logon' => $idusuario
                );
            $whereP = array('IdPRONAC = ?' => $dadosProjetos[0]->IdPRONAC);
            $Projetos->update($dadosP, $whereP);

            foreach ($dadosProjetos as $dados) {
                $mecanismo = $dados->Mecanismo;
                $AnoProjeto = $dados->AnoProjeto;
                $Sequencial = $dados->Sequencial;
                $cgccpf = $dados->CgcCpf;
            }
            $dados = array(
                    'AnoProjeto' => $AnoProjeto,
                    'Sequencial' => $Sequencial,
                    'Mecanismo' => $mecanismo,
                    'DtLiberacao' => date('Y-m-d H:i:s'),
                    'DtDocumento' => date('Y-m-d H:i:s'),
                    'NumeroDocumento' => '00000',
                    'VlOutrasFontes' => '0,00',
                    'Observacao' => 'Conta Liberada',
                    'CgcCpf' => '',
                    'Permissao' => 'S',
                    'Logon' => $idusuario,
                    'VlLiberado' => $vlCaptado
                );

            $liberar = new Liberacao();
            $buscar = $liberar->buscar(array('AnoProjeto = ?' => $AnoProjeto, 'Sequencial = ?' => $Sequencial))->toArray();

            if (count($buscar)==0) {
                $liberar->inserir($dados);
            }

            # Envia notificacao para o usuario atraves do aplicativo mobile.
            $this->enviarNotificacaoCapitacao((object)array(
                    'cpf' => $dadosProjetos[0]->CgcCpf,
                    'pronac' => $AnoProjeto. $Sequencial,
                    'idPronac' => $dadosProjetos[0]->IdPRONAC,
                    'vlCaptado' => number_format($vlCaptado, 2, ',', '.')
                ));

            parent::message('Transfer&ecirc;ncia executada com sucesso!', 'movimentacaodeconta/resultado-extrato-de-conta-captacao', 'CONFIRM');
        } else {
            parent::message('N&atilde;o foi poss&iacute;vel realizar a transfer&ecirc;ncia.', 'movimentacaodeconta/resultado-extrato-de-conta-captacao', 'ERROR');
        }
    }

    /**
     * Envia notificacao para o usuario atraves do aplicativo mobile.
     *
     * @param stdClass $projeto
     */
    protected function enviarNotificacaoCapitacao(stdClass $projeto)
    {
        $modelDispositivo = new Dispositivomovel();
        $listaDispositivos = $modelDispositivo->listarPorIdPronac($projeto->idPronac);
        $notification = new Minc_Notification_Message();
        $notification
            ->setCpf($projeto->cpf)
            ->setCodePronac($projeto->idPronac)
            ->setListDeviceId($modelDispositivo->listarIdDispositivoMovel($listaDispositivos))
            ->setListResgistrationIds($modelDispositivo->listarIdRegistration($listaDispositivos))
            ->setTipoMensagem(Dominio_TipoMensagem::CAPTACAO)
            ->setTitle('Projeto '. $projeto->pronac)
            ->setText('Recebeu R$'. $projeto->vlCaptado. ' de capta&ccedil;&atilde;o!')
            ->setListParameters(array('projeto' => $projeto->idPronac))
            ->send()
        ;
    }

    public function transferenciaColetivaContaCaptacaoAction()
    {
        if (!is_array($_POST)) {
            parent::message('N&atilde;o foi poss&iacute;vel realizar a transfer&ecirc;ncia.', 'movimentacaodeconta/resultado-extrato-de-conta-captacao', 'ERROR');
        }

        $idsCaptacao = $_POST['listaTransf'];
        $captacao = new Captacao();
        $dadosCaptacao = $captacao->buscar(array('Idcaptacao in (?)' => $idsCaptacao));

        if (count($dadosCaptacao)>0) {
            $auth = Zend_Auth::getInstance();
            $idusuario = $auth->getIdentity()->usu_codigo;
            $vlCaptado = 0;

            foreach ($dadosCaptacao as $d) {
                $vlCaptado = $vlCaptado + $d->CaptacaoReal;
                $dados = array('siTransferenciaRecurso' => 1, 'dtTransferenciaRecurso' => new Zend_Db_Expr('GETDATE()'), 'logon' => $idusuario);
                $where = array('Idcaptacao = ?' => $d->Idcaptacao);
                $captacao->update($dados, $where);
            }

            $Projetos = new Projetos();
            $dadosProjetos = $Projetos->buscar(array('AnoProjeto = ?'=>$dadosCaptacao[0]->AnoProjeto,'Sequencial = ?'=>$dadosCaptacao[0]->Sequencial));
            $getdate = date('d/m/Y');
            $valorTransferido = @number_format(($vlCaptado), 2, ",", ".");

            $dadosP = array(
                    'ProvidenciaTomada' => 'Transfer&ecirc;ncia de recursos entre conta capta&ccedil;&atilde;o e conta movimento no valor de R$'.$valorTransferido.' em '.$getdate.'.',
                    'Logon' => $idusuario
                );
            $whereP = array('IdPRONAC = ?' => $dadosProjetos[0]->IdPRONAC);
            $Projetos->update($dadosP, $whereP);

            foreach ($dadosProjetos as $dados) {
                $mecanismo = $dados->Mecanismo;
                $AnoProjeto = $dados->AnoProjeto;
                $Sequencial = $dados->Sequencial;
                $cgccpf = $dados->CgcCpf;
            }
            $dados = array(
                    'AnoProjeto' => $AnoProjeto,
                    'Sequencial' => $Sequencial,
                    'Mecanismo' => $mecanismo,
                    'DtLiberacao' => date('Y-m-d H:i:s'),
                    'DtDocumento' => date('Y-m-d H:i:s'),
                    'NumeroDocumento' => '00000',
                    'VlOutrasFontes' => '0,00',
                    'Observacao' => 'Conta Liberada',
                    'CgcCpf' => '',
                    'Permissao' => 'S',
                    'Logon' => $idusuario,
                    'VlLiberado' => $vlCaptado
                );
            $liberar = new Liberacao();
            $buscar = $liberar->buscar(array('AnoProjeto = ?' => $AnoProjeto, 'Sequencial = ?' => $Sequencial))->toArray();

            if (count($buscar)==0) {
                $liberar->inserir($dados);
            }
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Metodo para gerar o pdf do extrato
     * @access public
     * @param void
     * @return void
     */
    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
    }

    /**
     * Metodo para enviar o arquivo txt do banco do brasil
     * @access public
     * @param void
     * @return void
     * @todo existe um potencial erro nessa estrutura de codigo. Preciso
     * avalair refatoração.
     */
    public function uploadAction()
    {
        /*if ($this->getIdGrupo != 121 && $this->getIdGrupo != 129) // só Técnico de Acompanhamento que pode acessar
        {
        parent::message('Voce nao tem permissão para acessar essa área do sistema!', 'principal/index', 'ALERT');
        }*/

        // caso o formulário seja enviado via post
        if ($this->getRequest()->isPost()) {
            // configuração o php.ini para 100MB
            @set_time_limit(0);
            @ini_set('mssql.textsize', 10485760000);
            @ini_set('mssql.textlimit', 10485760000);
            @ini_set('mssql.timeout', 10485760000);
            @ini_set('upload_max_filesize', '100M');

            // pega as informações do arquivo
                $arquivoNome    = $_FILES['arquivo']['name']; // nome
                $arquivoTemp    = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
                $arquivoTipo    = $_FILES['arquivo']['type']; // tipo
                $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = strtolower(Upload::getExtensao($arquivoNome)); // extens�o
                }

            // caminho do arquivo txt
                $so               = stripos($_SERVER['SERVER_SOFTWARE'], 'win32') != false ? 'WINDOWS' : 'LINUX'; // sistema operacional
                $bar              = $so == 'WINDOWS' ? '\\' : '/';                                                // configura a barra de acordo com o SO
                $this->arquivoTXT = getcwd() . $bar . 'public' . $bar . 'txt' . $bar . $this->arquivoTXT;         // diretorio interno do arquivo

                $dir = $this->arquivoTXT; // diretorio onde se encontram os arquivos do banco
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    throw new RuntimeException("N&atilde;o foi poss&iacute;vel criar a pasta para salvar o arquivo");
                }
            }

            try {
                // integracao MODELO e VISAO

                if (empty($arquivoTemp)) { // nome do arquivo
                    throw new Exception('Por favor, informe o arquivo!');
                } elseif (($arquivoExtensao != 'ret' && $arquivoExtensao != 'txt') || ($arquivoTipo != 'text/plain' && $arquivoTipo != 'application/octet-stream' && $arquivoTipo != '')) { // extensao do arquivo
                    throw new Exception('A extens&atilde;o do arquivo &eacute; inv&aacute;lida, envie somente arquivos <strong>.txt</strong> ou <strong>.ret</strong>!');
                } elseif ($arquivoTamanho > 14680064) { // tamanho maximo do arquivo: 14MB
                    throw new Exception('O arquivo nao pode ser maior do que <strong>14MB</strong>!');
                } elseif ($arquivoTamanho <= 150) { // tamanho minimo do arquivo: 150 bytes
                    throw new Exception('O layout do arquivo enviado &eacute; inv&aacute;lido!');
                }
                // faz o envio do arquivo
                else {
                    $this->tbDepositoIdentificadoCaptacao = new tbDepositoIdentificadoCaptacao();
                    $this->tbTmpDepositoIdentificado = new tbTmpDepositoIdentificado();
                    // verifica se existe algum dado na tabela
                    $buscar = $this->tbDepositoIdentificadoCaptacao->buscar()->toArray();

                    if (count($buscar) > 0) {
                        throw new Exception('Aguarde um momento, pois, j&aacute; existe um arquivo sendo processado!');
                    }
                    // verifica se j&aacute; existe um arquivo com o mesmo nome
                    elseif (file_exists($dir . '/' . $arquivoNome)) {
                        throw new Exception('O arquivo <strong>' . $arquivoNome . '</strong> j&aacute; existe!');
                    } else {
                        // envia o arquivo
                        if (move_uploaded_file($arquivoTemp, $dir . '/' . $arquivoNome)) {
                            // abre o diretorio
                            if (($abrir = opendir($dir)) === false) {
                                throw new Exception('N&atilde;o foi poss&iacute;vel abrir o diretorio <strong>' . $dir . '</strong>!');
                            }
                            // busca todos os arquivos do diretorio
                            $i = 0;
                            while (($arq = readdir($abrir)) !== false) {
                                // verifica se a extensao do arquivo eh .txt ou .ret
                                if ((substr(strtolower($arq), -4) == '.txt') || (substr(strtolower($arq), -4) == '.ret')) {
                                    // array contendo o caminho/nome completo de cada arquivo
                                    $arquivos[] = $dir . $bar . $arq;

                                    if ($i == 0) {
                                        // abre o arquivo para leitura
                                        $abrir_arquivo_header = fopen($arquivos[0], 'r');

                                        // pega a linha do arquivo
                                        $linha_header = fgets($abrir_arquivo_header, 4096);

                                        // faz a validacao do arquivo de acordo com o layout
                                            $sequencial   = substr($linha_header, 1, 4);  // SEQUENCIAL
                                            $cliente      = substr($linha_header, 5, 5);  // CLIENTE: MINC
                                            $data_geracao = substr($linha_header, 10, 8); // DATA DE GERACAO DO ARQUIVO
                                            $referencia   = substr($linha_header, 18, 6); // MES E ANO DE REFERENCIA DOS DEPOSITOS

                                            // faz a validacao do arquivo pelo header
                                        // verifica pelo header se o arquivo j&aacute; existe
                                        if (substr($linha_header, 0, 1) == 1) :

                                                if (!is_numeric($sequencial) || trim(strtoupper($cliente)) != 'MINC' || !Data::validarData($data_geracao) || !Data::validarData('01'.$referencia)) {
                                                    // fecha o arquivo
                                                    fclose($abrir_arquivo_header);

                                                    // exclui o arquivo
                                                    unlink($arquivos[0]);

                                                    throw new Exception('O layout do arquivo enviado &eacute; inv&aacute;lido!');
                                                }

                                        // busca a data de geracao do arquivo para evitar insercao de registros duplicados
                                        //$dataGeracaoArquivo = Data::dataAmericana(Mascara::addMaskDataBrasileira($data_geracao));

                                        // verifica se o arquivo j&aacute; esta cadastrado no banco de dados
                                        $buscarArquivoCadastrado = $this->tbTmpDepositoIdentificado->buscar(array('dtGeracao = ?' => $data_geracao));

                                        if (count($buscarArquivoCadastrado) > 0) {
                                            // fecha o arquivo
                                            fclose($abrir_arquivo_header);

                                            // exclui o arquivo
                                            unlink($arquivos[0]);

                                            throw new Exception('Esse arquivo j&aacute; foi enviado!');
                                        }

                                        $i++;
                                        endif;

                                        if (!is_numeric($sequencial) || trim(strtoupper($cliente)) != 'MINC' || !Data::validarData($data_geracao) || !Data::validarData('01'.$referencia)) {
                                            // fecha o arquivo
                                            fclose($abrir_arquivo_header);

                                            // exclui o arquivo
                                            unlink($arquivos[0]);

                                            throw new Exception('O layout do arquivo enviado &eacute; inv&aacute;lido!');
                                        }

                                        // fecha o arquivo
                                        fclose($abrir_arquivo_header);

                                        // exclui o arquivo
                                        if (isset($arquivos[$i])) {
                                            unlink($arquivos[$i]);
                                        }
                                    } // fecha if ($i == 0)
                                } // fecha if
                            } // fecha while

                            // caso exista arquivo(s) .txt ou .ret no diretorio:
                            // 	1. Varre o conteudo de cada arquivo
                            // 	2. Grava o conteudo de cada linha no banco
                            // 	3. Deleta o arquivo do diretorio
                            if (isset($arquivos) && count($arquivos) > 0) {
                                // ========== INICIO - VARRE O ARQUIVO DETALHADAMENTE ==========
                                foreach ($arquivos as $arquivoTXT) :

                                        // abre o arquivo para leitura
                                        $abrir_arquivo = fopen($arquivoTXT, 'r');

                                // in�cio while de leitura do arquivo linha por linha
                                $i = 0;
                                $dsInformacao = array();
                                while (!feof($abrir_arquivo)) {
                                    // pega a linha do arquivo
                                    $linha = fgets($abrir_arquivo, 4096);

                                    // caso a linha nao seja vazia e o primeiro caractere for num�rico
                                    if (!empty($linha) && is_numeric(substr($linha, 0, 1))) {
                                        // armazena as linhas do arquivo em um array
                                        $dsInformacao[$i] = trim($linha);
                                    }
                                    $i++;
                                } // fim while de leitura do arquivo linha por linha

                                // fecha o arquivo
                                fclose($abrir_arquivo);

                                // exclui o arquivo
                                unlink($arquivoTXT);

                                // grava linha por linha do arquivo no banco
                                foreach ($dsInformacao as $ds) :
                                            if (!$this->tbDepositoIdentificadoCaptacao->cadastrarDados(array('dsInformacao' => $ds, 'idUsuario' => $this->getIdUsuario))) {
                                                throw new Exception('Erro ao enviar arquivo!');
                                            }
                                endforeach;

                                endforeach;

                                $this->tbDepositoIdentificadoCaptacao->DepositoIdentificadoCaptacao();

                                $where = "SUBSTRING(dsInformacao,1,1)!='1'";
                                $this->tbTmpDepositoIdentificado->deletar($where);

                                // ========== FIM - VARRE O ARQUIVO DETALHADAMENTE ==========
                            } // fecha if

                                parent::message('Arquivo enviado com sucesso!', 'movimentacaodeconta/upload', 'CONFIRM');
                        } // fecha if upload
                        else {
                            parent::message('Erro ao enviar arquivo!', 'movimentacaodeconta/upload', 'ERROR');
                        }
                    } // fecha else
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message       = $e->getMessage();
                $this->view->message_type  = 'ERROR';
            }
        } // fecha if post
    } // fecha metodo uploadAction()



    /**
     * Metodo para executar a sp de movimentcao bancaria
     * @access public
     * @param void
     * @return void
     */
    public function finalizarAction()
    {
        $arrRetorno = array();
        // caso o formulario seja enviado via post
        if ($this->getRequest()->isPost()) {
            // configuracao o php.ini para 100MB
            /* @set_time_limit(0); */
            @ini_set('mssql.textsize', 10485760000);
            @ini_set('mssql.textlimit', 10485760000);
            @ini_set('mssql.timeout', 10485760000);
            /* @ini_set('upload_max_filesize', '100M'); */

            try {
                $auth = Zend_Auth::getInstance();
                $idUsuario = $auth->getIdentity()->usu_codigo;
                // executa a sp
                $this->spValidarDepositoIdentificado = new spValidarDepositoIdentificado();
                $sp = $this->spValidarDepositoIdentificado->verificarInconsistencias($idUsuario);

                if (!is_object($sp)) {
                    throw new Exception($sp);
                }
                if ($this->modal = "s") {
                    $arrRetorno['error'] = false;
                    $arrRetorno['msg']   = 'Rotina executada com sucesso!';
                    $this->_helper->json($arrRetorno);
                } else {
                    parent::message('Rotina executada com sucesso!', 'movimentacaodeconta/listar-inconsistencias', 'CONFIRM');
                }
            } catch (Exception $e) {
                if ($this->modal = "s") {
                    $arrRetorno['error'] = true;
                    $arrRetorno['msg']   = $e->getMessage();
                    $this->_helper->json($arrRetorno);
                } else {
                    parent::message($e->getMessage(), 'movimentacaodeconta/listar-inconsistencias', 'ERROR');
                }
            }
        } else {
            parent::message('Por favor, pressione o bot�o finalizar!', 'movimentacaodeconta/listar-inconsistencias', 'ALERT');
        }
    }

    /**
     * Salva o arquivo do banco do brasil:
     * toda vez que acessar aqui, verificar se o arquivo do BB existe,
     * caso o arquivo exista, grava os dados no banco e exclui o arquivo.
     * ****************************************************************************************************
     * OBS: Por enquanto foi substituido pela TRIGGER/SP, mas, eh bom nao retira-lo para o caso de precisar
     * ****************************************************************************************************
     * @access public
     * @param void
     * @return void
     */
    public function salvararquivobbAction()
    {
        $this->_helper->layout->disableLayout();

        $arquivos = null;

        //$dir = 'D:\\Usuarios\\96569433172\\Desktop\\Politec\\UC 38\\';

        //Diretorio onde se encontra os arquivos
        $dir = getcwd() . $this->arquivoTXT;

        // Esse seria o 'handler' do diretorio
        $dh = opendir($dir);

        // Loop que busca todos os arquivos ate que nao encontre mais nada
        while (false !== ($filename = readdir($dh))) {
            // Verificando se o arquivo � .txt
            if ((substr($filename, -4) == '.TXT') or (substr($filename, -4) == '.txt')) {
                // mostra o nome do arquivo e um link para ele - pode ser mudado para mostrar diretamente a imagem :)
                $txt = $dir.'/'.$filename;
                $arquivos[] = $txt;
            }
        }

        // Se tiver arquivos TXT no diretorio...
        if ($arquivos) {
            foreach ($arquivos as $i) {
                $arquivoTxt = $i;

                /* Abrindo o arquivo */
                $fp = fopen($arquivoTxt, 'r'); // $fp contera o handle do arquivo que abrimos

                /* Count para os dados */
                $countD = 0;
                $countH = 0;
                $countT = 0;

                /* Varrendo o arquivo */
                while (!feof($fp)) {
                    $buffer = fgets($fp, 4096);
                    echo $buffer.'<br />';

                    /* Se o inicio da linha for igual a 1, entao eh Header */
                    if (substr($buffer, 0, 1) == 1) {
                        $movimentacaoH['Sequencial'][$countH]=substr($buffer, 1, 4);
                        $movimentacaoH['cliente'][$countH]=substr($buffer, 5, 5);
                        $movimentacaoH['dataGeracao'][$countH]=substr($buffer, 10, 8);
                        $movimentacaoH['referencia'][$countH]=substr($buffer, 18, 6);
                        $dataChegadaRecibo = $movimentacaoH['dataGeracao'][$countH]=substr($buffer, 10, 8);
                        $countH ++;
                    }

                    /* Se o inicio da linha for igual a 2, entao eh Detalhe */
                    if (substr($buffer, 0, 1) == 2) {
                        $movimentacaoD['cpf_cnpj'][$countD]=substr($buffer, 1, 14);

                        /* Buscando os dados do proponente*/
                        $Proponente = MovimentacaoDeContaDAO::buscarProponente(substr($buffer, 1, 14));

                        if ($Proponente) {
                            foreach ($Proponente as $p) {
                                $movimentacaoD['NomeProponente'][$countD]= $p->nome;
                                $movimentacaoD['EmailProponente'][$countD]= $p->email;
                                $emailProponente = $p->email;
                            }
                        } else {
                            $movimentacaoD['NomeProponente'][$countD]= 'not';
                            $movimentacaoD['EmailProponente'][$countD]= 'not';
                        }

                        $movimentacaoD['agencia'][$countD]=substr($buffer, 15, 4);
                        $movimentacaoD['DvAgencia'][$countD]=substr($buffer, 19, 1);
                        $movimentacaoD['conta'][$countD]=substr($buffer, 21, 11);
                        $movimentacaoD['DvConta'][$countD]=substr($buffer, 32, 1);
                        $movimentacaoD['cpf_cnpjPatrocinador'][$countD]=substr($buffer, 33, 14);


                        // Verificando se o Patrocinador estah cadastrado
                        $Patrocinador = MovimentacaoDeContaDAO::buscarPatrocinador(substr($buffer, 33, 14));
                        if ($Patrocinador) {
                            foreach ($Patrocinador as $p) {
                                $movimentacaoD['Patrocinador'][$countD]= $p->nome;
                            }
                        } else {
                            $movimentacaoD['Patrocinador'][$countD]= 'not';
                        }

                        $agencia = substr($buffer, 15, 4).substr($buffer, 19, 1);
                        $conta = substr($buffer, 21, 11).substr($buffer, 32, 1);

                        /* Buscando o PRONAC pela agencia e conta do projeto */
                        $BuscaPRONAC = MovimentacaoDeContaDAO::buscarProjeto($agencia, $conta);

                        /* Varrendo o array para pegar o PRONAC */
                        foreach ($BuscaPRONAC as $i) {
                            $AnoProjeto 		= $i->AnoProjeto;
                            $SequencialProjeto 	= $i->Sequencial;
                        }

                        /* Se acharo pronac ele da um ok */
                        if ($BuscaPRONAC) {
                            $movimentacaoD['AnoProjeto'][$countD]= $AnoProjeto;
                            $movimentacaoD['SequencialProjeto'][$countD]= $SequencialProjeto;
                        }
                        /* Se nao achar ele manda um email pedindo para cadastrar */
                        else {
                            $movimentacaoD['AnoProjeto'][$countD]= '';
                            $movimentacaoD['SequencialProjeto'][$countD]= '';
                        }

                        // Buscando o enquadramento do Projeto.
                        $Enquadramento = MovimentacaoDeContaDAO::buscarEnquadramento($AnoProjeto.$SequencialProjeto);

                        if ($Enquadramento) {
                            foreach ($Enquadramento as $eq) {
                                $movimentacaoD['Enquadramento'][$countD] = $eq->Enquadramento;
                            }
                        } else {
                            $movimentacaoD['Enquadramento'][$countD] = 'not';
                        }

                        /* Pegando a data para verificacao e transformando para BR */
                        $data = substr($buffer, 47, 8);
                        $dia = substr($data, 0, 2);
                        $mes = substr($data, 2, 2);
                        $ano = substr($data, 4, 4);
                        $dataCredito = $dia.'/'.$mes.'/'.$ano;
                        $dataCreditoC = $ano.'/'.$mes.'/'.$dia;
                        $movimentacaoD['dataCredito'][$countD] = $dataCredito;
                        $movimentacaoD['dataCreditoC'][$countD] = $dataCreditoC;

                        /* Fazer uma busca para ver se o projeto esta com data de execucao vigente */
                        $PExecucao = MovimentacaoDeContaDAO::buscarVigenciaExecucao($dataCreditoC, $AnoProjeto.$SequencialProjeto);
                        if ($PExecucao) {
                            $movimentacaoD['PExecucao'][$countD] = 'ok';
                        } else {
                            $movimentacaoD['PExecucao'][$countD] = 'not';
                        }

                        /* Fazer uma busca para ver se o projeto esta com data de captacao vigente */
                        $PCaptacao = MovimentacaoDeContaDAO::buscarVigenciaCaptacao($dataCreditoC, $AnoProjeto.$SequencialProjeto);
                        if ($PCaptacao) {
                            $movimentacaoD['PCaptacao'][$countD] = 'ok';
                        } else {
                            $movimentacaoD['PCaptacao'][$countD] = 'not';
                        }


                        $movimentacaoD['valorCredito'][$countD]=(float)substr($buffer, 55, 17);

                        /* Se o codigo de patrocinio for em branco transforma para 0 (Nao informado) */
                        if (substr($buffer, 94, 1) == '') {
                            $movimentacaoD['codPatrocinio'][$countD]= 0;
                        } else {
                            $movimentacaoD['codPatrocinio'][$countD]=substr($buffer, 94, 1);
                        }

                        $countD ++;
                    } // fecha if

                    /* Se o inicio da linha for igual a 3, entao eh Trailer */
                    if (substr($buffer, 0, 1) == 3) {
                        $movimentacaoT['quantidadeRegistros'][$countT]=substr($buffer, 1, 8);
                        $countT ++;
                    }
                } // fecha while

                // Colocando as datas nas formatacoes certas
                $dia = substr($dataChegadaRecibo, 0, 2);
                $mes = substr($dataChegadaRecibo, 2, 2);
                $ano = substr($dataChegadaRecibo, 4, 4);
                $dataChegadaReciboM = $dia.'/'.$mes.'/'.$ano;
                $dataChegadaReciboC = $ano.'/'.$mes.'/'.$dia;

                foreach ($movimentacaoD['cpf_cnpj'] as $Key => $i) {
                    $dadosOK = array('AnoProjeto' 		=> $movimentacaoD['AnoProjeto'][$Key],
                                     'Sequencial' 		=> $movimentacaoD['SequencialProjeto'][$Key],
                                     'NumeroRecibo' 	=> '0',
                                     'CgcCpfMecena' 	=> $movimentacaoD['cpf_cnpjPatrocinador'][$Key],
                                     'TipoApoio' 		=> $movimentacaoD['codPatrocinio'][$Key],
                                     'MedidaProvisoria' => $movimentacaoD['Enquadramento'][$Key],
                                     'DtChegadaRecibo' 	=> $dataChegadaReciboC,
                                     'DtRecibo' 		=> $movimentacaoD['dataCreditoC'][$Key],
                                     'CaptacaoReal' 	=> number_format($movimentacaoD['valorCredito'][$Key]/100, 2, '.', ''),
                                     'CaptacaoUfir' 	=> '0',
                                     'logon' 			=> '6764',
                                     //'Idcaptacao' 	=> '',
                                     'idProjeto' 		=> null
                                     );


                    $dadosErro = array('nrAnoProjeto' 			=> $movimentacaoD['AnoProjeto'][$Key],
                                       'nrSequencial' 			=> $movimentacaoD['SequencialProjeto'][$Key],
                                       'dtChegadaRecibo'		=> $dataChegadaReciboC,
                                       'nrCpfCnpjProponente' 	=> $i,
                                       'nrCpfCnpjIncentivador' 	=> $movimentacaoD['cpf_cnpjPatrocinador'][$Key],
                                       'dtCredito' 				=> $movimentacaoD['dataCreditoC'][$Key],
                                       'vlValorCredito' 		=> number_format($movimentacaoD['valorCredito'][$Key]/100, 2, '.', ''),
                                       'cdPatrocinio' 			=> $movimentacaoD['codPatrocinio'][$Key]
                                       );


                    //Zend_Debug::dump($dadosErro);

                    $dadosContigencia = array();

                    $pendencias = 'PEND&Ecirc;NCIAS NA CAPTA&Ccedil;&Atilde;O DO PROJETO PRONAC: '.$movimentacaoD['AnoProjeto'][$Key].
                                                                                $movimentacaoD['SequencialProjeto'][$Key].
                                                                             '<br /><br />';

                    if ($movimentacaoD['PExecucao'][$Key] == 'not') {
                        array_push($dadosContigencia, '1');
                        $pendencias .= 'Periodo de execu&ccedil;&atilde;o n&atilde;o vigente <br />';
                    }
                    if ($movimentacaoD['PCaptacao'][$Key] == 'not') {
                        array_push($dadosContigencia, '2');
                        $pendencias .= 'Periodo de capta&ccedil;&atilde;o n&atilde;o vigente <br />';
                    }
                    if ($movimentacaoD['Patrocinador'][$Key] == 'not') {
                        array_push($dadosContigencia, '3');
                        $pendencias .= 'Deve cadastrar o incentivador com o CPF/CNPJ: '.$movimentacaoD['cpf_cnpjPatrocinador'][$Key].' <br />';
                    }
                    if ($movimentacaoD['codPatrocinio'][$Key] == 0) {
                        array_push($dadosContigencia, '4');
                        $pendencias .= 'Informar o tipo de patroc&iacute;nio <br />';
                    }
                    if ($movimentacaoD['EmailProponente'][$Key] == 'not') {
                        array_push($dadosContigencia, '5');
                    }
                    if ($movimentacaoD['NomeProponente'][$Key] == 'not') {
                        array_push($dadosContigencia, '6');
                    }

                    /* Tipo de contigencias
                        *
                        * 1 - Execucao nao vigente
                        * 2 - Captacao nao vigente
                        * 3 - Incentivador nao cadastrado
                        * 4 - Patrocinio nao informado
                        * 5 - Email do proponente nao cadastrado
                        * 6 - Proponente nao cadastrado
                        */

                    if (sizeof($dadosContigencia) > 0) {
                        $insereCaptacaoErro = MovimentacaoDeContaDAO::salvaCaptacaoErro($dadosErro);

                        for ($i = 0; $i < sizeof($dadosContigencia); $i++) {
                            $dadosContigenciaErro = array('idTipoInconsistencia' => $dadosContigencia[$i], 'idTmpCaptacao' => $insereCaptacaoErro);
                            $insereContigencias = MovimentacaoDeContaDAO::salvaContigencia($dadosContigenciaErro);
                        }

                        /****************************************************************************************************/

                        $email = 'salicweb@gmail.com';

                        // Quando subir para producao deve substituir o email acima por esse abaixo
//					        $email = $emailProponente;

                            //$enviar = MovimentacaoDeContaDAO::enviarEmail($email, $pendencias);

                        /****************************************************************************************************/



                        //MovimentacaoDeContaController::enviarEmail($emailProponente);
                    } else {
                        $insereCaptacaoOK = MovimentacaoDeContaDAO::salvaCaptacaoOK($dadosOK);
                    }
                } // fecha foreach

                /* Fecha o arquivo */
                fclose($fp);

                // Exclui o arquivo
                unlink($arquivoTxt);
            }
        } // Fecha caso tem arquivos

        // Agora vai verificar o que j&aacute; tem cadastrado
        $this->_redirect('movimentacaodeconta/verificacao');
    } // fecha metodo salvararquivobbAction()



    /**
     * Metodo de verificacao
     * ****************************************************************************************************
     * OBS: Por enquanto foi substituido pela TRIGGER/SP, mas, eh bom nao retira-lo para o caso de precisar
     * ****************************************************************************************************
     * @access public
     * @param void
     * @return void
     */
    public function verificacaoAction()
    {
        $this->_helper->layout->disableLayout();

        $deletaContigencias = MovimentacaoDeContaDAO::deletaContigencia();

        $DadosVerificacao = MovimentacaoDeContaDAO::verificacaoDados();

        if ($DadosVerificacao) {
            foreach ($DadosVerificacao as $dv) {
                $idTmpCaptacao 			= $dv->idTmpCaptacao;
                $nrAnoProjeto 			= $dv->nrAnoProjeto;
                $nrSequencial 			= $dv->nrSequencial;
                $dtChegadaRecibo 		= $dv->dtChegadaRecibo;
                $nrCpfCnpjProponente 	= $dv->nrCpfCnpjProponente;
                $nrCpfCnpjIncentivador 	= $dv->nrCpfCnpjIncentivador;
                $dtCredito 				= $dv->dtCredito;
                $vlValorCredito 		= $dv->vlValorCredito;
                $cdPatrocinio 			= $dv->cdPatrocinio;


                $Proponente = MovimentacaoDeContaDAO::buscarProponente($nrCpfCnpjProponente);
                if ($Proponente) {
                    foreach ($Proponente as $p) {
                        $NomeProponente  = $p->nome;
                        $EmailProponente = $p->email;
                    }
                } else {
                    $NomeProponente  = '';
                    $EmailProponente = '';
                }


                $Patrocinador = MovimentacaoDeContaDAO::buscarPatrocinador($nrCpfCnpjIncentivador);
                if ($Patrocinador) {
                    $Incentivador = 'ok';
                } else {
                    $Incentivador = 'not';
                }

                /* Colocar como pedente */
                $Enquadramento = MovimentacaoDeContaDAO::buscarEnquadramento($nrAnoProjeto.$nrSequencial);

                if ($Enquadramento) {
                    foreach ($Enquadramento as $eq) {
                        $Enquadramento = $eq->Enquadramento;
                    }
                }


                /* Fazer uma busca para ver se o projeto esta com data de execucao vigente */
                $PExecucao = MovimentacaoDeContaDAO::buscarVigenciaExecucao($dtCredito, $nrAnoProjeto.$nrSequencial);
                if ($PExecucao) {
                    $PEx = 'ok';
                } else {
                    $PEx = 'not';
                }

                /* Fazer uma busca para ver se o projeto esta com data de captacao vigente */
                $PCaptacao = MovimentacaoDeContaDAO::buscarVigenciaCaptacao($dtCredito, $nrAnoProjeto.$nrSequencial);
                if ($PCaptacao) {
                    $PCap = 'ok';
                } else {
                    $PCap = 'not';
                }


                $dadosOK = array('AnoProjeto' 		=> $nrAnoProjeto,
                                 'Sequencial' 		=> $nrSequencial,
                                 'NumeroRecibo' 	=> '0',
                                 'CgcCpfMecena' 	=> $nrCpfCnpjIncentivador,
                                 'TipoApoio' 		=> $cdPatrocinio,
                                 'MedidaProvisoria' => $Enquadramento,
                                 'DtChegadaRecibo' 	=> $dtChegadaRecibo,
                                 'DtRecibo' 		=> $dtCredito,
                                 'CaptacaoReal' 	=> $vlValorCredito,
                                 'CaptacaoUfir' 	=> '0',
                                 'logon' 			=> '6764',
                                 //'Idcaptacao' 	=> '',
                                 'idProjeto' 		=> null
                                 );


                $dadosErro = array('nrAnoProjeto' 			=> $nrAnoProjeto,
                                   'nrSequencial' 			=> $nrSequencial,
                                   'dtChegadaRecibo'		=> $dtChegadaRecibo,
                                   'nrCpfCnpjProponente' 	=> $nrCpfCnpjProponente,
                                   'nrCpfCnpjIncentivador' 	=> $nrCpfCnpjIncentivador,
                                   'dtCredito' 				=> $dtCredito,
                                   'vlValorCredito' 		=> $vlValorCredito,
                                   'cdPatrocinio' 			=> $cdPatrocinio
                                   );

                $dadosContigencia = array();

                if ($PEx == 'not') {
                    array_push($dadosContigencia, '1');
                }
                if ($PCap == 'not') {
                    array_push($dadosContigencia, '2');
                }
                if ($Incentivador == 'not') {
                    array_push($dadosContigencia, '3');
                }
                if ($cdPatrocinio == 0) {
                    array_push($dadosContigencia, '4');
                }
                if ($EmailProponente == '') {
                    array_push($dadosContigencia, '5');
                }
                if ($NomeProponente == '') {
                    array_push($dadosContigencia, '6');
                }


                Zend_Debug::dump($dadosErro);


                if (sizeof($dadosContigencia) > 0) {
                    foreach ($dadosContigencia as $c) {
                        $dadosContigenciaErro = array('idTipoInconsistencia' => $c, 'idTmpCaptacao' => $idTmpCaptacao);

                        Zend_Debug::dump($dadosContigenciaErro);

                        $insereContigencias = MovimentacaoDeContaDAO::salvaContigencia($dadosContigenciaErro);
                    }
                } else {
                    $insereCaptacaoOK = MovimentacaoDeContaDAO::salvaCaptacaoOK($dadosOK);
                }
            }
        }

        parent::message('Arquivo enviado com sucesso!', 'movimentacaodeconta/upload', 'CONFIRM');
    } // fecha metodo verificacaoAction()


    /**
     *
     */
    public function verificaIncentivadorAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $tbAgente = new Agente_Model_DbTable_Agentes();
        $rsAgente = $tbAgente->buscar(array('CNPJCPF = ?' => $this->_request->getParam('cpfCnpjIncentivador')));

        # verifica se existe o agente
        if ($rsAgente->count()) {
            $agente = $rsAgente->current();
            $visaoTable = new Visao();
            $visaoAgente = $visaoTable->buscar(array(
                'idAgente = ?' => $agente->idAgente,
                'Visao = ?' => 145,
            ));
            # verifica se existe a visao, caso nao cadastra
            if (!$visaoAgente->count()) {
                $visaoTable->insert(array(
                    'Usuario' => Zend_Auth::getInstance()->getIdentity()->usu_codigo,
                    'idAgente' => $agente->idAgente,
                    'Visao' => 145,
                    'stAtivo' => 'S',
                ));
            }
            #
            $this->_helper->json(array('existe' => true), true);
        }
        # agente nao cadastrado
        $this->_helper->json(array('existe' => false), true);
    }

    /**
     *
     */
    public function imprimirExtratoCaptacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $this->view->html = $_POST['html'];
    }

    /**
     *
     */
    public function formRelatorioReciboCaptacaoAction()
    {
        ;
    }

    /**
     * Metodo para listar os projetos para grid do relatorio conforme o tipo de inconsistencia
     * @access public
     * @param void
     * @return void
     */
    public function resultadoRelatorioReciboCaptacaoAction()
    {

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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(9,7,3);
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        if (!empty($get->pronac)) {
            $where["c.AnoProjeto+c.Sequencial = ?"] = $get->pronac;
        }
        if (!empty($get->numLote)) {
            $where["c.NumeroRecibo = ?"] = $get->numLote;
        }
        if (!empty($get->proponente)) {
            $where["p.CgcCpf = ?"] = retiraMascara($get->proponente);
        }
        if (!empty($get->incentivador)) {
            $where["c.cgcCpfMecena = ?"] = retiraMascara($get->incentivador);
        }

        if ($get->tpDtLote != '') {
            if (!empty($get->dtLote)) {
                $d1 = Data::dataAmericana($get->dtLote);
                if ($get->tpDtLote == "igual") {
                    $where["c.DtChegadaRecibo BETWEEN '$d1 00:00:00' AND '$d1 23:59:59.999'"] = '';
                } elseif ($get->tpDtLote == "entre") {
                    $d2 = Data::dataAmericana($get->dtLote_Final);
                    $where["c.DtChegadaRecibo BETWEEN '$d1 00:00:00' AND '$d2 23:59:59.999'"] = '';
                } elseif ($get->tpDtLote == "maior") {
                    $where["c.DtChegadaRecibo >= ?"] = $d1.' 00:00:00';
                } elseif ($get->tpDtLote == "menor") {
                    $where["c.DtChegadaRecibo <= ?"] = $d1.' 23:59:59.999';
                }
            } else {
                if ($get->tpDtLote == "OT") {
                    $d1 = date("Y-m-d", strtotime("-1 day"));
                    $where["c.DtChegadaRecibo BETWEEN '$d1 00:00:00' AND '$d1 23:59:59.999'"] = '';
                } elseif ($get->tpDtLote == "U7") {
                    $d1 = date("Y-m-d", strtotime("-7 day"));
                    $d2 = date("Y-m-d");
                    $where["c.DtChegadaRecibo BETWEEN '$d1 00:00:00' AND '$d2 23:59:59.999'"] = '';
                } elseif ($get->tpDtLote == "MM") {
                    $d1 = date("d");
                    $d2 = date("Y-m-d", strtotime("-$d1 day"));
                    $d3 = date("Y-m-d");
                    $where["c.DtChegadaRecibo BETWEEN '$d2 23:59:59.999' AND '$d3 23:59:59.999'"] = '';
                } elseif ($get->tpDtLote == "UM") {
                    $d1 = date("m", strtotime("-1 month"));
                    $d2 = date("Y", strtotime("-1 month"));
                    $ultimo_dia_do_mes = date("t", mktime(0, 0, 0, $d1, '01', $d2));
                    $where["c.DtChegadaRecibo BETWEEN '$d2-$d1-01 00:00:00.000' AND '$d2-$d1-$ultimo_dia_do_mes 23:59:59.999'"] = '';
                }
            }
        }

        if ($get->tpDtCaptacao != '') {
            if (!empty($get->dtCaptacao)) {
                $d1 = Data::dataAmericana($get->dtCaptacao);
                if ($get->tpDtCaptacao == "igual") {
                    $where["c.DtRecibo BETWEEN '$d1 00:00:00' AND '$d1 23:59:59.999'"] = '';
                } elseif ($get->tpDtCaptacao == "entre") {
                    $d2 = Data::dataAmericana($get->dtCaptacao_Final);
                    $where["c.DtRecibo BETWEEN '$d1 00:00:00' AND '$d2 23:59:59.999'"] = '';
                } elseif ($get->tpDtCaptacao == "maior") {
                    $where["c.DtRecibo >= ?"] = $d1.' 00:00:00';
                } elseif ($get->tpDtCaptacao == "menor") {
                    $where["c.DtRecibo <= ?"] = $d1.' 23:59:59.999';
                }
            } else {
                if ($get->tpDtCaptacao == "OT") {
                    $d1 = date("Y-m-d", strtotime("-1 day"));
                    $where["c.DtRecibo BETWEEN '$d1 00:00:00' AND '$d1 23:59:59.999'"] = '';
                } elseif ($get->tpDtCaptacao == "U7") {
                    $d1 = date("Y-m-d", strtotime("-7 day"));
                    $d2 = date("Y-m-d");
                    $where["c.DtRecibo BETWEEN '$d1 00:00:00' AND '$d2 23:59:59.999'"] = '';
                } elseif ($get->tpDtCaptacao == "MM") {
                    $d1 = date("d");
                    $d2 = date("Y-m-d", strtotime("-$d1 day"));
                    $d3 = date("Y-m-d");
                    $where["c.DtRecibo BETWEEN '$d2 23:59:59.999' AND '$d3 23:59:59.999'"] = '';
                } elseif ($get->tpDtCaptacao == "UM") {
                    $d1 = date("m", strtotime("-1 month"));
                    $d2 = date("Y", strtotime("-1 month"));
                    $ultimo_dia_do_mes = date("t", mktime(0, 0, 0, $d1, '01', $d2));
                    $where["c.DtRecibo BETWEEN '$d2-$d1-01 00:00:00.000' AND '$d2-$d1-$ultimo_dia_do_mes 23:59:59.999'"] = '';
                }
            }
        }

        $tbCaptacao = new Captacao();
        $total = $tbCaptacao->buscaReciboCaptacao($where, $order, null, null, true);

        $this->view->vlrTotalGrid = $tbCaptacao->buscaReciboCaptacaoTotalValorGrid($where);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbCaptacao->buscaReciboCaptacao($where, $order, $tamanho, $inicio);
        $paginacao = array(
                    "pag"=>$pag,
                    "qtde"=>$this->intTamPag,
                    "campo"=>$campo,
                    "ordem"=>$ordem,
                    "ordenacao"=>$ordenacao,
                    "novaOrdem"=>$novaOrdem,
                    "total"=>$total,
                    "inicio"=>($inicio+1),
                    "fim"=>$fim,
                    "totalPag"=>$totalPag,
                    "Itenspag"=>$this->intTamPag,
                    "tamanho"=>$tamanho
             );

        $this->view->paginacao = $paginacao;
        $this->view->qtdDocumentos = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;
        $this->view->pronac = $get->pronac;
        $this->view->proponente = $get->proponente;
        $this->view->incentivador = $get->incentivador;
        $this->view->dtLote = $get->dtLote;
        $this->view->tpDtLote = $get->tpDtLote;
        $this->view->dtLote_Final = $get->dtLote_Final;
        $this->view->dtCaptacao = $get->dtCaptacao;
        $this->view->tpDtCaptacao = $get->tpDtCaptacao;
        $this->view->dtCaptacao_Final = $get->dtCaptacao_Final;
        $this->view->numLote = $get->numLote;
    } // fecha metodo listarProjetosAction()

    public function imprimirRelatorioReciboCaptacaoAction()
    {
        $this->_helper->layout->disableLayout();

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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(9,7,3);
            $ordenacao = null;
        }

        $get = Zend_Registry::get('post');

        /* ================== PAGINACAO ======================*/
        $where = array();
        if (!empty($get->pronac)) {
            $where["c.AnoProjeto+c.Sequencial = ?"] = $get->pronac;
        }
        if (!empty($get->numLote)) {
            $where["c.NumeroRecibo = ?"] = $get->numLote;
        }
        if (!empty($get->proponente)) {
            $where["p.CgcCpf = ?"] = retiraMascara($get->proponente);
        }
        if (!empty($get->incentivador)) {
            $where["c.cgcCpfMecena = ?"] = retiraMascara($get->incentivador);
        }
        if (!empty($get->dtLote) || $get->tpDtLote != '') {
            if ($get->tpDtLote == "igual") {
                $where['DtChegadaRecibo >= ?'] = ConverteData($get->dtLote, 13)." 00:00:00";
                $where['DtChegadaRecibo <= ?'] = ConverteData($get->dtLote, 13)." 23:59:59";
            } elseif ($get->tpDtLote == "maior") {
                $where['DtChegadaRecibo >= ?'] = ConverteData($get->dtLote, 13)." 00:00:00";
            } elseif ($get->tpDtLote == "menor") {
                $where['DtChegadaRecibo <= ?'] = ConverteData($get->dtLote, 13)." 00:00:00";
            } elseif ($get->tpDtLote == "OT") {
                $where['DtChegadaRecibo = ?'] = date("Y-m-").(date("d")-1)." 00:00:00";
            } elseif ($get->tpDtLote == "U7") {
                $where['DtChegadaRecibo > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $where['DtChegadaRecibo < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($get->tpDtLote == "SP") {
                $where['DtChegadaRecibo > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $where['DtChegadaRecibo < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($get->tpDtLote == "MM") {
                $where['DtChegadaRecibo > ?'] = date("Y-m-01")." 00:00:00";
                $where['DtChegadaRecibo < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($get->tpDtLote == "UM") {
                $where['DtChegadaRecibo > ?'] = date("Y-").(date("m")-1)."-01 00:00:00";
                $where['DtChegadaRecibo < ?'] = date("Y-").(date("m")-1)."-31 23:59:59";
            } else {
                $where['DtChegadaRecibo > ?'] = ConverteData($get->dtLote, 13)." 00:00:00";
                if ($get->dtLote_Final != "") {
                    $where['DtChegadaRecibo < ?'] = ConverteData($get->dtLote_Final, 13)." 23:59:59";
                }
            }
        }
        if (!empty($get->dtCaptacao) || $get->tpDtCaptacao != '') {
            if ($get->tpDtCaptacao == "igual") {
                $where['DtRecibo >= ?'] = ConverteData($get->dtCaptacao, 13)." 00:00:00";
                $where['DtRecibo <= ?'] = ConverteData($get->dtCaptacao, 13)." 23:59:59";
            } elseif ($get->tpDtCaptacao == "maior") {
                $where['DtRecibo >= ?'] = ConverteData($get->dtCaptacao, 13)." 00:00:00";
            } elseif ($get->tpDtCaptacao == "menor") {
                $where['DtRecibo <= ?'] = ConverteData($get->dtCaptacao, 13)." 00:00:00";
            } elseif ($get->tpDtCaptacao == "OT") {
                $where['DtRecibo = ?'] = date("Y-m-").(date("d")-1)." 00:00:00";
            } elseif ($get->tpDtCaptacao == "U7") {
                $where['DtRecibo > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $where['DtRecibo < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($get->tpDtCaptacao == "SP") {
                $where['DtRecibo > ?'] = date("Y-m-").(date("d")-7)." 00:00:00";
                $where['DtRecibo < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($get->tpDtCaptacao == "MM") {
                $where['DtRecibo > ?'] = date("Y-m-01")." 00:00:00";
                $where['DtRecibo < ?'] = date("Y-m-d")." 23:59:59";
            } elseif ($get->tpDtCaptacao == "UM") {
                $where['DtRecibo > ?'] = date("Y-").(date("m")-1)."-01 00:00:00";
                $where['DtRecibo < ?'] = date("Y-").(date("m")-1)."-31 23:59:59";
            } else {
                $where['DtRecibo > ?'] = ConverteData($get->dtCaptacao, 13)." 00:00:00";
                if ($get->dtCaptacao_Final != "") {
                    $where['DtRecibo < ?'] = ConverteData($get->dtCaptacao_Final, 13)." 23:59:59";
                }
            }
        }

        $tbCaptacao = new Captacao();
        $busca = $tbCaptacao->buscaReciboCaptacao($where, $order);
        $this->view->dados = $busca;
        $this->view->vlrTotalGrid = $tbCaptacao->buscaReciboCaptacaoTotalValorGrid($where);
    } // fecha metodo listarProjetosAction()


    /**
     * Metodo responsavel pela correcao manual das inconsistencias
     */
    public function corrigirInconsistenciasAction()
    {
    } // fecha metodo corrigirInconsistenciaAction()
} // fecha class
