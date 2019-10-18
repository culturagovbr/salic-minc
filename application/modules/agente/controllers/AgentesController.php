<?php
class Agente_AgentesController extends MinC_Controller_Action_Abstract
{
    /**
     * @var integer (variavel com o id do usuario logado)
     * @access private
     */
    private $getIdUsuario = 0;

    /**
     * @var integer (variavel para Parecerista)
     * @access private
     */
    private $getParecerista = 'N';

    /**
     * @var integer (variavel com o id do grupo ativo)
     * @access private
     */
    private $GrupoAtivoSalic = 0;

    /**
     * combovisoes
     *
     * @var bool
     * @access private
     */
    private $combovisoes = array();

    /**
     * modal
     *
     * @var string
     * @access private
     */
    private $modal = "n";

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $mapperArea = new Agente_Model_AreaMapper;
        $mapperVerificacao = new Agente_Model_VerificacaoMapper();
        $mapperUF = new Agente_Model_UFMapper();
        $this->view->comboestados = $mapperUF->fetchPairs('idUF', 'Sigla');
        $this->view->combotiposenderecos = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 2));
        $this->view->combotiposlogradouros = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 13));
        $this->view->comboareasculturais = $mapperArea->fetchPairs('Codigo', 'Descricao');
        $this->view->combotipostelefones = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 3));
        $this->view->combotiposemails = $mapperVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idtipo' => 4, 'idverificacao' => array(28, 29)));

        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $authIdentity = array_change_key_case((array) $auth->getIdentity());

        if (isset($authIdentity['cpf'])) {
            $this->view->combovisoes = self::visoes(null, true);
            $this->view->ehProponente = true;
        } else {
            $this->view->combovisoes = self::visoes(null, false);
            $this->view->ehProponente = false;
        }

        //verifica se a funcionadade devera abrir em modal
        if ($this->_request->getParam("modal") == "s") {
            $this->_helper->layout->disableLayout();
            $this->modal = "s";
            $this->view->modal = "s";
            $this->view->exibirTelefone = 'n';
            $this->view->exibirEmail = 'n';
        } else {
            $this->modal = "n";
            $this->view->modal = "n";
            $this->view->exibirTelefone = 's';
            $this->view->exibirEmail = 's';
        }

        $this->view->autoCarregarDadosCPF = false;

        parent::init();
    }

    /**
     * Funcao para tratar data
     * @access private
     * @param Date
     * @return string
     */
    private function formatadata($data)
    {
        if ($data) {
            $dia = substr($data, 0, 2);
            $mes = substr($data, 3, 2);
            $ano = substr($data, 6, 4);
            $dataformatada = $ano . "/" . $mes . "/" . $dia;
        } else {
            $dataformatada = null;
        }

        return $dataformatada;
    }

    /**
     * autenticacao
     *
     * @access private
     * @return void
     */
    private function autenticacao()
    {
        $arrAuth = array_change_key_case((array) Zend_Auth::getInstance()->getIdentity()); // pega a autenticacao
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        // define as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 118; // Componente da Comissao
        $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
        $PermissoesGrupo[] = 121; // Tecnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        $PermissoesGrupo[] = 137; // Coordenador de PRONAC
        $PermissoesGrupo[] = 144; // Proponente
        //Perfis incluidos para cadastro de Agentes no ato do cadastro do projeto FNC.
        $PermissoesGrupo[] = 103; // Coordenador de Analise
        $PermissoesGrupo[] = 142; // Coordenador de Convenios
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE;

        $params = $this->getRequest()->getParams();
        $params = array_change_key_case($params);

        if (isset($arrAuth['cpf']) &&
                !empty($arrAuth['cpf']) &&
                isset($params['acao']) && $params['acao'] == 'cc' &&
                isset($params['cpf']) &&
                !empty($params['cpf'])) { // pega do readequacao
            parent::perfil(2); // scriptcase
        }

        if (isset($arrAuth['cpf']) &&
                !empty($arrAuth['cpf']) &&
                !isset($params['acao']) &&
                !isset($params['cpf']) &&
                empty($params['cpf'])) { // pega do readequacao
            parent::perfil(4, $PermissoesGrupo); // migracao e novo salic
        } elseif (isset($arrAuth['usu_codigo']) && !empty($arrAuth['usu_codigo'])) {
            parent::perfil(1, $PermissoesGrupo); // migracao e novo salic
        } else {
            parent::perfil(4, $PermissoesGrupo); // migracao e novo salic
        }

        if (isset($arrAuth['usu_codigo'])) { // autenticacao novo salic
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($arrAuth['usu_codigo']);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
        } else { // autenticacao scriptcase
            $this->getIdUsuario = (isset($arrAuth['idusuario'])) ? $arrAuth['idusuario'] : 0;
        }

        $Cpflogado = $this->getIdUsuario;

        $this->view->cpfLogado = $Cpflogado;
        $this->view->grupoativo = $GrupoAtivo->codGrupo;

        $this->GrupoAtivoSalic = $GrupoAtivo->codGrupo; // Grava o Id do Grupo Ativo

        $menuLateral = $this->_request->getParam("menuLateral");

        if (($menuLateral == 'true') || ($menuLateral == '')) {
            $this->view->menuLateral = 'true';
        } else {
            $this->view->menuLateral = 'false';
        }

        /* Monta os dados do Agente */
        $idAgente = $this->_request->getParam("id");

        if (($GrupoAtivo->codGrupo == Autenticacao_Model_Grupos::PARECERISTA) || ($GrupoAtivo->codGrupo == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO)) {
            $idAgente = $this->getIdUsuario;
        }

        $qtdDirigentes = '';
        if (isset($idAgente)) {
            $dados = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgente);

            if (!$dados) {
                parent::message("Agente não encontrado!", "agente/agentes/buscaragente", "ALERT");
            }

            $this->view->telefones = Agente_Model_ManterAgentesDAO::buscarFones($idAgente);
            $this->view->emails = Agente_Model_ManterAgentesDAO::buscarEmails($idAgente);
            $visaoTable = new Agente_Model_DbTable_Visao();
            $visoes = $visaoTable->buscarVisao($idAgente);
            $this->view->visoes = $visoes;

            foreach ($visoes as $v) {
                if ($v->Visao == '209') {
                    $this->getParecerista = 'sim';
                }
            }

            if(empty($dados[0]->idResponsavel)) {

                if($dados[0]->tipopessoa == 1) {
                    $sgcAcesso = new Autenticacao_Model_Sgcacesso();
                    $this->view->responsaveis = $sgcAcesso->buscarUsuario(['Cpf = ?' => $dados[0]->cnpjcpf]);
                }
            }

            if ($dados[0]->tipopessoa == 1) {
                $dirigentes = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, null, null, $idAgente);
                $qtdDirigentes = count($dirigentes);
                $this->view->dirigentes = $dirigentes;
                $this->view->responsaveis = $dirigentes;
            }



            $this->view->dados = $dados;
            $this->view->qtdDirigentes = $qtdDirigentes;
            $this->view->parecerista = $this->getParecerista;

            $this->view->id = $idAgente;
        }
    }

    /**
     * incluir
     *
     * @access private
     * @return void
     */
    private function incluir()
    {
        $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($this->_request->getParam("cpf")));

        $cpfMask = '';
        $tipoCpf = '';

        if (strlen($cpf) == 11) {
            $tipoCpf = 'cpf';
            $cpfMask = Mascara::addMaskCPF($this->_request->getParam("cpf"));
        }
        if (strlen($cpf) == 14) {
            $tipoCpf = 'cnpj';
            $cpfMask = Mascara::addMaskCNPJ($this->_request->getParam("cpf"));
        }

        $this->view->cpf = $cpfMask;
        $this->view->tipocpf = $tipoCpf;
        $this->view->idpronac = $this->_request->getParam('idpronac');
    }

    private function visoes($visao = null, $ehProponente = false)
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $GrupoAtivo = $GrupoAtivo->codGrupo;

        $visaoTable = new Agente_Model_DbTable_Visao();
        $visoes = $visaoTable->buscarVisoes($visao);

        $visoesNew = null;

        foreach ($visoes as $key => $visaoGrupo) {
            if ($GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER
                and ($visaoGrupo->idVerificacao == VisaoModel::PARECERISTA_DE_PROJETO_CULTURAL
                    or $visaoGrupo->idVerificacao == VisaoModel::TECNICO)
            ) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if ($GrupoAtivo == Autenticacao_Model_Grupos::PARECERISTA
                and $visaoGrupo['idVerificacao'] == VisaoModel::PARECERISTA_DE_PROJETO_CULTURAL
            ) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if ($GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_DO_PRONAC
                and $visaoGrupo['idVerificacao'] == VisaoModel::PARECERISTA_DE_PROJETO_CULTURAL
            ) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if ($GrupoAtivo == Autenticacao_Model_Grupos::GESTOR_SALIC) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if ($GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_CNIC
                and $visaoGrupo['idVerificacao'] == VisaoModel::COMPONENTE_DA_COMISSAO) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if ($GrupoAtivo == Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO
                and $visaoGrupo['idVerificacao'] == VisaoModel::INCENTIVADOR) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if ($GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO
                and $visaoGrupo['idVerificacao'] == VisaoModel::INCENTIVADOR) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if ($GrupoAtivo == Autenticacao_Model_Grupos::COMPONENTE_COMISSAO
                and $visaoGrupo['idVerificacao'] == VisaoModel::COMPONENTE_DA_COMISSAO) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if (($GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO
                    or $GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO)
                and $visaoGrupo['idVerificacao'] == VisaoModel::INCENTIVADOR) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if (($GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_ANALISE
                    or $GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_DE_CONVENIO)
                and $visaoGrupo['idVerificacao'] == VisaoModel::PROPONENTE) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }

            if (($GrupoAtivo == Autenticacao_Model_Grupos::GESTOR_SALIC
                    or $GrupoAtivo == Autenticacao_Model_Grupos::COORDENADOR_CNIC)
                and $visaoGrupo['idVerificacao'] == VisaoModel::VOTANTES_DA_CNIC) {
                $visoesNew[$key]['idVerificacao'] = $visaoGrupo['idVerificacao'];
                $visoesNew[$key]['Descricao'] = $visaoGrupo['Descricao'];
            }
        }

        if ($ehProponente) {
            $visoesNew[0]['idVerificacao'] = VisaoModel::PROPONENTE;
            $visoesNew[0]['Descricao'] = 'Proponente';
        }

        if (!empty($visao)) {
            $visoesNew = $visoes;
        }

        return $visoesNew;
    }

    /**
     * vincular Metodo responsavel por vincular o Responsavel logado a seu proprio perfil de Proponente
     *
     * @param bool $cpfCadastrado
     * @param bool $idAgenteCadastrado
     * @access public
     * @return void
     */
    public function vincular($cpfCadastrado = null, $idAgenteCadastrado = null)
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao

        /**
         * O metodo so e executado quando o usuario logado for Responsavel.
         * So entra aqui quando o cpf do Proponente que esta sendo cadastrado for igual ao do Responsavel logado.
         */
        if (isset($auth->getIdentity()->Cpf) && !empty($cpfCadastrado) && !empty($idAgenteCadastrado) && ($auth->getIdentity()->Cpf == Mascara::delMaskCPFCNPJ($cpfCadastrado))) :

            /**
             * ==============================================================
             * INICIO DO VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE)
             * ==============================================================
             */
            /* ========== BUSCA O ID DO RESPONSAVEL ========== */
            $Sgcacesso = new Autenticacao_Model_Sgcacesso();
        $buscarResponsavel = $Sgcacesso->buscar(array('Cpf = ?' => $cpfCadastrado));

        /* ========== VINCULA O RESPONSAVEL A SEU PROPRIO PERFIL DE PROPONENTE ========== */
        if (count($buscarResponsavel) > 0) :
                $tbVinculo = new Agente_Model_DbTable_TbVinculo();

        $dadosVinculo = array(
                    'idAgenteProponente' => $idAgenteCadastrado
                    , 'dtVinculo' => new Zend_Db_Expr('GETDATE()')
                    , 'siVinculo' => 2
                    , 'idUsuarioResponsavel' => $buscarResponsavel[0]->IdUsuario);
        $tbVinculo->inserir($dadosVinculo);
        endif;

        endif;
    }

    /**
     * buscaPessoaAction
     *
     * @access public
     * @return void
     */
    public function buscaPessoaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        #Instancia a Classe de Servico do WebService da Receita Federal
        $wsServico = new ServicosReceitaFederal();

        $retorno        = array();
        $cpf            = str_replace('.', '', str_replace('-', '', $this->getRequest()->getParam('cpf')));
        $teste          = $this->getRequest()->getParam('teste');
        $tipoPessoa     = $this->getRequest()->getParam('tipoPessoa');
        $erro           = 0;

        try {
            if (11 == strlen($cpf)) {
                if (!validaCPF($cpf)) {
                    $retorno['error'] = 'CPF inv&aacute;lido';
                    $erro = 1;
                } else {
                    $arrResultado = $wsServico->consultarPessoaFisicaReceitaFederal($cpf);
                    if (empty($arrResultado)) {
                        $retorno['error'] = 'Pessoa n&atilde;o encontrada!';
                        $erro = 1;
                    }
                    if ($erro == 0 && count($arrResultado) > 0) {
                        $retorno['dados']['idPessoa'] = $arrResultado['idPessoaFisica'];
                        $retorno['dados']['nome'] = $arrResultado['nmPessoaFisica'];
                        $retorno['dados']['cep'] = isset($arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep']) && $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] ? $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] : '';
                        $retorno['error'] = '';
                    } else {
                        $retorno['error'] = 'Pessoa n&atilde;o encontrada!!';
                    }
                }
            } elseif (15 == strlen($cpf)) {
                if (!isCnpjValid($cpf)) {
                    $retorno['error'] = 'CNPJ inv&aacute;lido';
                    $erro = 1;
                } else {
                    $arrResultado = $wsServico->consultarPessoaJuridicaReceitaFederal($cpf);
                    if (empty($arrResultado)) {
                        $retorno['error'] = 'Pessoa n&atilde;o encontrada!!';
                        $erro = 1;
                    }
                    if ($erro == 0 && count($arrResultado) > 0) {
                        $retorno['dados']['idPessoa'] = $arrResultado['idPessoaJuridica'];
                        $retorno['dados']['nome'] = $arrResultado['nmRazaoSocial'];
                        $retorno['dados']['cep'] = isset($arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep']) && $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] ? $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] : '';
                        $retorno['error'] = '';
                    } else {
                        $retorno['error'] = 'Pessoa n&atilde;o encontrada!';
                    }
                }
            }
        } catch (InvalidArgumentException $exc) {
            $retorno['error'] = 'Pessoa n&atilde;o encontrada!';
        } catch (Exception $exc) {
            $retorno['error'] = 'Pessoa n&atilde;o encontrada!';
        }

        $this->_helper->json($retorno);
        die;
    }

    /**
     * Metodo incluiragente()
     * @access public
     * @param void
     * @return void
     */
    public function incluiragenteAction()
    {
        $this->autenticacao();

        $modal  = $this->_request->getParam("modal");
        $modulo = $this->_request->getParam("modulo");
        $this->view->modulo = $modulo;

        if (!empty($modal)) {
            $this->_helper->layout->disableLayout(); // desabilita 0o Zend_Layout
            $this->view->modal = "s";
            $this->view->cpf = $this->_request->getParam("cpf");
            $this->view->caminho = $this->_request->getParam("caminho");
        } else {
            $this->view->modal = "n";
            $this->view->caminho = "";
            $this->view->exibirTelefone = 's';
            $this->view->exibirEmail = 's';
        }
        $this->incluir();
    }

    public function incluirIncentivadorModalAction()
    {
        $this->autenticacao();

        $this->_helper->layout->disableLayout();

        $modulo = $this->_request->getParam("modulo");
        $this->view->modulo = $modulo;

        $this->view->modal = "s";
        $this->view->cpf = $this->_request->getParam("cpf");
        $this->view->caminho = $this->_request->getParam("caminho");

        $this->incluir();
    }

    /**
     * Metodo incluirfonecedor()
     * @access public
     * @param void
     * @return void
     */
    public function incluirfornecedorAction()
    {
        $this->autenticacao();
        $modal  = $this->_request->getParam("modal");
        $this->_helper->layout->disableLayout();
        $this->view->modal = "s";
        $this->view->cpf = $this->_request->getParam("cpfCnpj");
        $this->view->caminho = $this->_request->getParam("caminho");
        $this->view->acao = $this->_request->getParam("acao");

        $this->view->autoCarregarDadosCPF = true;
        $this->view->combovisoes = self::visoes(VisaoModel::FORNECEDOR);
        $this->incluir();
    }

    /**
     * Metodo incluirprocurador()
     * @access public
     * @param void
     * @return void
     */
    public function incluirprocuradorAction()
    {
        $this->autenticacao();

        $modal  = $this->_request->getParam("modal");

        $this->_helper->layout->disableLayout();
        $this->view->modal = "s";
        $this->view->cpf = $this->_request->getParam("cpfCnpj");
        $this->view->caminho = $this->_request->getParam("caminho");
        $this->view->acao = $this->_request->getParam("acao");

        $this->incluir();
    }

    /**
     * Metodo incluirbeneficiario()
     * @access public
     * @param void
     * @return void
     */
    public function incluirbeneficiarioAction()
    {
        $this->autenticacao();

        $this->view->cpf = $this->_request->getParam("cpfCnpj");
        $this->view->caminho = $this->_request->getParam("caminho");
        $this->view->acao = $this->_request->getParam("acao");

        $this->view->autoCarregarDadosCPF = true;
        $this->view->combovisoes = self::visoes(VisaoModel::BENEFICIARIO_DE_PRODUTOS);

        $this->incluir();
    }

    /**
     * incluiragenteexternoAction
     *
     * @access public
     * @return void
     * @deprecated action nao utilizada (27/03/2018)
     */
//    public function incluiragenteexternoAction()
//    {
//        Zend_Layout::startMvc(array('layout' => 'open'));
//        $this->incluir();
//    }

    /**
     * Metodo para visualizacao dos dados do agente
     * @access public
     * @param void
     */
    public function agentesAction()
    {
        $this->autenticacao();

        if (($this->GrupoAtivoSalic != 1111) &&
                ($this->GrupoAtivoSalic != 118) &&
                ($this->GrupoAtivoSalic != 144) &&
                ($this->GrupoAtivoSalic != 137) &&
                ($this->GrupoAtivoSalic != 121) &&
                ($this->GrupoAtivoSalic != 122) &&
                ($this->GrupoAtivoSalic != 123) &&
                ($this->GrupoAtivoSalic != 97) &&
                ($this->GrupoAtivoSalic != 93) &&
                ($this->GrupoAtivoSalic != 120) &&
                ($this->GrupoAtivoSalic != 94)) {
            parent::message("Você n&atilde;o tem permiss&atilde;o para essa funcionalidade!", "agente/agentes/sempermissao", "ALERT");
        }

        $idAgente = (int)$this->_request->getParam("id");

        if (($this->GrupoAtivoSalic == 94) || ($this->GrupoAtivoSalic == 118)) {
            $idAgente = $this->getIdUsuario;
        }

        if ($this->GrupoAtivoSalic == 93 && empty($idAgente)) {
            $this->redirect('agente/agentes/buscaragente');
        }

        if (empty($idAgente)) {
            $this->redirect('agente/agentes/incluiragente');
        }

        $tbInfo = new Agente_Model_DbTable_TbInformacaoProfissional();
        $this->view->formacoes = $tbInfo->BuscarInfo($idAgente, null);

        $ano = date('Y');
        $mes = date('m');

        $tbAusencia = new Agente_Model_DbTable_TbAusencia();
        $dados = $tbAusencia->carregarAusencia($idAgente, $ano, 2, $mes);
        $totalDias = 0;

        foreach ($dados as $d) {
            if (($d['siAusencia'] == 0) or ($d['siAusencia'] == 1)) {
                $totalDias = $totalDias + $d['qtddias'];
            }
        }

        $tbCredenciamentoParecerista = new Agente_Model_DbTable_TbCredenciamentoParecerista();
        $credenciados = $tbCredenciamentoParecerista->carregar($idAgente);

        $this->view->credenciados = $credenciados;
        $this->view->totalDias = $totalDias;
        $this->view->dadosferias = $dados;
        $this->view->id = $idAgente;
    }

    /**
     * Metodo dirigentes()
     * @access public
     * @param void
     * @return void
     */
    public function dirigentesAction()
    {
        $this->autenticacao();
    }

    /**
     * Metodo visualizadirigente()
     * @access public
     * @param void
     * @return void
     */
    public function visualizadirigenteAction()
    {
        $this->autenticacao();

        $idAgente       = $this->_request->getParam("id");
        $idDirigente    = $this->_request->getParam("idDirigente");

        if (isset($idAgente)) {
            $dadosDirigenteD = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, $idDirigente, null, $idAgente);
            $dados = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idDirigente);
            $this->view->dadosD = $dados;

            if (!$dados) {
                parent::message("Agente n&atilde;o encontrado!", "agente/agentes/buscaragente", "ALERT");
            }

            $this->view->telefonesD = Agente_Model_ManterAgentesDAO::buscarFones($idDirigente);
            $this->view->emailsD = Agente_Model_ManterAgentesDAO::buscarEmails($idDirigente);
            $visaoTable = new Agente_Model_DbTable_Visao();
            $this->view->visoesD = $visaoTable->buscarVisao($idDirigente);
            $this->view->Instituicao = "sim";
            $this->view->id = $this->_request->getParam("id");
            $this->view->idDirigente = $this->_request->getParam("idDirigente");

            if ($dadosDirigenteD) {
                $this->view->vinculado = "sim";
            }
            $tbTipodeDocumento = new VerificacaoAGENTES();
            $whereLista['idTipo = ?'] = 5;
            $rsTipodeDocumento = $tbTipodeDocumento->buscar($whereLista);
            $this->view->tipoDocumento = $rsTipodeDocumento;

            $tbDirigenteMandato = new tbAgentesxVerificacao();
            $buscarMandato = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idAgente, 'idDirigente = ?' => $idDirigente, 'stMandato = ?' => 0));
            $this->view->mandatos = $buscarMandato;
            $mandatoAtual = $tbDirigenteMandato->listarMandato(array('idEmpresa = ?' => $idAgente,'idDirigente = ?' => $idDirigente, 'stMandato = ?' => 0), array('dtFimMandato DESC'))->current();
            $this->view->mandatosAtual = $mandatoAtual;
        }
    }

    /**
     * Metodo mandato()
     * @access public
     * @param void
     * @return void
     */
    public function mandatoAction()
    {
        $this->autenticacao();

        if (!empty($_POST)) {
            $idAgente = $this->_request->getParam("id");
            $idDirigente = $this->_request->getParam("idDirigente");

            $tbDirigenteMandato = new tbAgentesxVerificacao();

            $idVerificacao          = $this->_request->getParam("idVerificacao");
            $dsNumeroDocumento      = $this->_request->getParam("dsNumeroDocumento");
            $idDirigente            = $this->_request->getParam("idDirigente");
            $dtInicioVigencia       = $this->_request->getParam("dtInicioVigencia");
            $dtInicioVigencia       = Data::dataAmericana($dtInicioVigencia);
            $dtTerminoVigencia      = $this->_request->getParam("dtTerminoVigencia");
            $dtTerminoVigencia      = Data::dataAmericana($dtTerminoVigencia);
            $stMandato              = 0;
            $idArquivo              = $this->_request->getParam("idArquivo");

            //validacao data do mandato
            $buscarMandato = $tbDirigenteMandato->mandatoRepetido($idAgente, $dtInicioVigencia, $dtTerminoVigencia);

            if (count($buscarMandato) > 0) {
                parent::message("N&atilde;o poder&aacute; inserir um novo mandato, pois j&aacute; existe um mandato em vigor para esse dirigente!mandatos", "agente/agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
            }

            if (count($_FILES) > 0) {
                $ERROR = ''; //Criado para corrigir erro.
                foreach ($_FILES['arquivo']['name'] as $key=>$val) {
                    $arquivoNome     = $_FILES['arquivo']['name'][$key];
                    $arquivoTemp     = $_FILES['arquivo']['tmp_name'][$key];
                    $arquivoTipo     = $_FILES['arquivo']['type'][$key];
                    $arquivoTamanho  = $_FILES['arquivo']['size'][$key];

                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao

                    if ($arquivoExtensao != "doc" && $arquivoExtensao != "docx") {
                        if (!empty($arquivoTemp)) {
                            $idArquivo = $this->cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho);

                            $dados = array(
                             'idArquivo'        => $idArquivo,
                             'idTipoDocumento'  => 0,
                        );

                            $tabela      = new tbDocumento();
                            $idDocumento = $tabela->inserir($dados);
                            if ($idDocumento) {
                                $idDocumento = $tabela->ultimodocumento(array('idArquivo = ? '=>$idArquivo));
                                $idDocumento = $idDocumento->idDocumento;
                            } else {
                                $ERROR .= "Erro no anexo";
                                $idDocumento = 0;
                                $erro = true;
                            }
                        }
                    } else {
                        parent::message("N&atilde;o s&atilde;o permitidos documentos de texto doc/docx!", "agente/agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
                    }
                }
            }
            try {
                $arrayMandato = array(
                    'idVerificacao'         => $idVerificacao,
                    'dsNumeroDocumento'     => $dsNumeroDocumento,
                    'dtInicioMandato'       => $dtInicioVigencia,
                    'dtFimMandato'          => $dtTerminoVigencia,
                    'stMandato'             => $stMandato,
                    'idEmpresa'             => $idAgente,
                    'idDirigente'           => $idDirigente
                );

                if ($idArquivo > 0) {
                    $arrayMandato['idArquivo'] = $idArquivo;
                }

                $salvarMandato = $tbDirigenteMandato->inserir($arrayMandato);

                $buscarMandato = $tbDirigenteMandato->buscar(array('idAgentexVerificacao = ?' => $salvarMandato));

                if (!empty($buscarMandato)) {
                    $dadosBuscar['idVerificacao']       = $buscarMandato[0]->idVerificacao;
                    $dadosBuscar['dsNumeroDocumento']   = $buscarMandato[0]->dsNumeroDocumento;
                    $dadosBuscar['dtInicioMandato']     = $buscarMandato[0]->dtInicioMandato;
                    $dadosBuscar['dtFimMandato']        = $buscarMandato[0]->dtFimMandato;
                    $dadosBuscar['stMandato']           = $buscarMandato[0]->stMandato;
                    $dadosBuscar['idEmpresa']           = $buscarMandato[0]->idEmpresa;
                    $dadosBuscar['idDirigente']         = $buscarMandato[0]->idDirigente;
                    $dadosBuscar['idArquivo']           = $buscarMandato[0]->idArquivo;
                }

                parent::message("Cadastro realizado com sucesso!", "agente/agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao salvar o mandato:" . $e->getMessage(), "agente/agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
            }
        }
    }

    /**
     * Metodo excluirmandato()
     * @access public
     * @param void
     * @return void
     */
    public function excluirmandatoAction()
    {
        $this->autenticacao();

        $tbDirigenteMandato = new tbAgentesxVerificacao();

        $idAgente = $this->_request->getParam("id");
        $idDirigente = $this->_request->getParam("idDirigente");
        $idMandato = $this->_request->getParam("idAgentexVerificacao");

        try {
            $arrayMandato = array('stMandato' => 1);

            $whereMandato['idAgentexVerificacao = ?'] = $idMandato;
            $tbDirigenteMandato->alterar($arrayMandato, $whereMandato);

            parent::message("Exclus&atilde;o realizada com sucesso!", "agente/agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao excluir o mandato:" . $e->getMessage(), "agente/agentes/visualizadirigente/id/" . $idAgente . "/idDirigente/" . $idDirigente, "ERROR");
        }
    }

    /**
     * cadastraranexo
     *
     * @param mixed $arquivoNome
     * @param mixed $arquivoTemp
     * @param mixed $arquivoTipo
     * @param mixed $arquivoTamanho
     * @access public
     * @return void
     */
    public function cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho)
    {
        if (!empty($arquivoNome) && !empty($arquivoTemp)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
            $arquivoBinario  = Upload::setBinario($arquivoTemp); // binario
            $arquivoHash     = Upload::setHash($arquivoTemp); // hash
        }

        // cadastra dados do arquivo
        $dadosArquivo = array(
                'nmArquivo'         => $arquivoNome,
                'sgExtensao'        => $arquivoExtensao,
                'dsTipoPadronizado' => $arquivoTipo,
                'nrTamanho'         => $arquivoTamanho,
                'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                'dsHash'            => $arquivoHash,
                'stAtivo'           => 'A');
        $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

        // pega o id do ultimo arquivo cadastrado
        $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
        $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

        // cadastra o binario do arquivo
        $dadosBinario = array(
                'idArquivo' => $idUltimoArquivo,
                'biArquivo' => $arquivoBinario);
        $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);

        return $idUltimoArquivo;
    }

    /**
     * Metodo enderecos()
     * Visualiza, exclui e altera os enderecos do agente
     * @access public
     * @param void
     * @return void
     */
    public function enderecosAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");

        $tblEndereco = new Agente_Model_DbTable_EnderecoNacional();

        $lista = $tblEndereco->buscarEnderecos($idAgente);

        $this->view->endereco = $lista;
        $this->view->qtdEndereco = count($lista);
    }

    /**
     * Metodo salvaendereco()
     * Salva o endereco do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvaenderecoAction()
    {
        $this->autenticacao();

        $Usuario = $this->getIdUsuario; // id do usuario logado
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $this->_request->getParam("id");

        $cepEndereco = $this->_request->getParam("cep");
        $tipoEndereco = $this->_request->getParam("tipoEndereco");
        $ufEndereco = $this->_request->getParam("uf");
        $CidadeEndereco = $this->_request->getParam("cidade");
        $Endereco = $this->_request->getParam("logradouro");
        $divulgarEndereco = $this->_request->getParam("divulgarEndereco");
        $tipoLogradouro = $this->_request->getParam("tipoLogradouro");
        $numero = $this->_request->getParam("numero");
        $complemento = $this->_request->getParam("complemento");
        $bairro = $this->_request->getParam("bairro");
        $enderecoCorrespodencia = $this->_request->getParam("enderecoCorrespodencia");

        try {
            $arrayEnderecos = array(
                'idAgente' => $idAgente,
                'Cep' => str_replace(".", "", str_replace("-", "", $cepEndereco)),
                'TipoEndereco' => $tipoEndereco,
                'UF' => $ufEndereco,
                'Cidade' => $CidadeEndereco,
                'Logradouro' => $Endereco,
                'Divulgar' => $divulgarEndereco,
                'TipoLogradouro' => $tipoLogradouro,
                'Numero' => $numero,
                'Complemento' => $complemento,
                'Bairro' => $bairro,
                'Status' => $enderecoCorrespodencia,
                'Usuario' => $Usuario
            );

            $tblEndereco = new Agente_Model_DbTable_EnderecoNacional();

            if ($enderecoCorrespodencia == "1") {
                $tblEndereco->mudaCorrespondencia($idAgente);
            }

            $tblEndereco->insert($arrayEnderecos);

            parent::message("Cadastro realizado com sucesso!", "agente/agentes/enderecos/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar o endere&ccedil;o: " . $e->getMessage(), "agente/agentes/enderecos/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo excluiendereco()
     * Exclui o endereco do agente
     * @access public
     * @param void
     * @return void
     */
    public function excluienderecoAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idEndereco = $this->_request->getParam("idEndereco");
        $qtdEndereco = $this->_request->getParam("qtdEndereco");

        $enderecoCorrespondencia = $this->_request->getParam("enderecoCorrespondencia");

        if ($qtdEndereco <= 1) {
            parent::message("Voc&ecirc; tem que ter pelo menos um endere&ccedil;o cadastrado!", "agente/agentes/enderecos/id/" . $idAgente, "ALERT");
        }

        try {
            $tblEndereco = new Agente_Model_DbTable_EnderecoNacional();

            $tblEndereco->delete($idEndereco);

            if ($enderecoCorrespondencia == "1") {
                $tblEndereco->novaCorrespondencia($idAgente);
            }

            parent::message("Exclus&atilde;o realizada com sucesso!", "agente/agentes/enderecos/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao excluir o endere&ccedil;o: " . $e->getMessage(), "agente/agentes/enderecos/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo telefones()
     * @access public
     * @param void
     * @return void
     */
    public function telefonesAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");

        $mdlTelefones = new Agente_Model_DbTable_Telefones();
        $lista = $mdlTelefones->buscarFones($idAgente);
        $this->view->telefones = $lista;
        $this->view->qtdTel = count($lista);
    }

    /**
     * Metodo salvatelefone()
     * Salva o tefefone do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvatelefoneAction()
    {
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usuario logado

        $idAgente = $this->_request->getParam("id");
        $tipoFone = $this->_request->getParam("tipoFone");
        $ufFone = $this->_request->getParam("ufFone");
        $dddFone = $this->_request->getParam("dddFone");
        $Fone = $this->_request->getParam("fone");
        $divulgarFone = $this->_request->getParam("divulgarFone");

        try {
            $arrayTelefones = array(
                'idagente' => $idAgente,
                'tipotelefone' => $tipoFone,
                'uf' => $ufFone,
                'ddd' => $dddFone,
                'numero' => $Fone,
                'divulgar' => $divulgarFone,
                'usuario' => $Usuario
            );

            # Salvando telefone.
            $mapperTelefones = new Agente_Model_TelefonesMapper();
            $modelTelefones = new Agente_Model_Telefones($arrayTelefones);

            $mapperTelefones->beginTransaction();
            $mapperTelefones->save($modelTelefones);

            $mapperTelefones->commit();
            parent::message("Cadastro realizado com sucesso!", "agente/agentes/telefones/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $mapperTelefones->rollBack();
            parent::message("Erro ao salvar o Telefone: " . $e->getMessage(), "agente/agentes/telefones/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo excluitelefone()
     * Exclui o tefefone do agente
     * @access public
     * @param void
     * @return void
     */
    public function excluitelefoneAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idTelefone = $this->_request->getParam("idTelefone");
        $qtdTel = $this->_request->getParam("qtdTel");

        if ($qtdTel <= 1) {
            parent::message("Você tem que ter pelo menos um telefone cadastrado!", "agente/agentes/telefones/id/" . $idAgente, "ALERT");
        } else {
            try {
                $mapperTelefones = new Agente_Model_TelefonesMapper();
                $mapperTelefones->deleteBy(array("idTelefone" => $idTelefone));

                parent::message("Exclus&atilde;o realizada com sucesso!", "agente/agentes/telefones/id/" . $idAgente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao excluir o telefone: " . $e->getMessage(), "agente/agentes/telefones/id/" . $idAgente, "ERROR");
            }
        }
    }

    /**
     * Metodo emails()
     * @access public
     * @param void
     * @return void
     */
    public function emailsAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");

        $modelInternet = new Agente_Model_DbTable_Internet();
        $lista = $modelInternet->buscarEmails($idAgente);

        $this->view->emails = $lista;
        $this->view->qtdEmail = count($lista);
    }

    /**
     * Metodo salvaemail()
     * Salva o email do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvaemailAction()
    {
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usuario logado

        $idAgente = $this->_request->getParam("id");
        $tipoEmail = $this->_request->getParam("tipoEmail");
        $Email = $this->_request->getParam("email");
        $divulgarEmail = $this->_request->getParam("divulgarEmail");
        $enviarEmail = $this->_request->getParam("enviarEmail");

        try {
            $arrayEmail = array(
                'idagente' => $idAgente,
                'tipointernet' => $tipoEmail,
                'descricao' => $Email,
                'status' => $enviarEmail,
                'divulgar' => $divulgarEmail,
                'usuario' => $Usuario
            );

            $modelInternet = new Agente_Model_Internet($arrayEmail);
            $mapperInternet = new Agente_Model_InternetMapper();

            $mapperInternet->beginTransaction();
            $mapperInternet->save($modelInternet);

            $mapperInternet->commit();

            parent::message("Cadastro realizado com sucesso!", "agente/agentes/emails/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $mapperInternet->rollBack();
            parent::message("Erro ao salvar o e-mail: " . $e->getMessage(), "agente/agentes/emails/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo excluiemail()
     * Exclui o email do agente
     * @access public
     * @param void
     * @return void
     */
    public function excluiemailAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idInternet = $this->_request->getParam("idInternet");
        $qtdEmail = $this->_request->getParam("qtdEmail");

        if ($qtdEmail <= 1) {
            parent::message("Voc&eacirc; tem que ter pelo menos um email cadastrado!", "agente/agentes/emails/id/" . $idAgente, "ALERT");
        } else {
            try {
                $mapperTelefones = new Agente_Model_InternetMapper();
                $mapperTelefones->deleteBy(array("idInternet" => $idInternet));

                parent::message("Exclus&atilde;o realizada com sucesso!", "agente/agentes/emails/id/" . $idAgente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao excluir o e-mail: " . $e->getMessage(), "agente/agentes/emails/id/" . $idAgente, "ERROR");
            }
        }
    }

    /**
     * Metodo escolaridade()
     * @access public
     * @param void
     * @return void
     */
    public function escolaridadeAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");

        $escolaridade = new TbEscolaridade();

        $escolaridades = $escolaridade->BuscarEscolaridades($idAgente);
        $tipoEscolaridade = $escolaridade->BuscarTipoEscolaridade();
        $pais = $escolaridade->BuscarPais();

        $this->view->tipoEscolaridade = $tipoEscolaridade;
        $this->view->escolaridades = $escolaridades;
        $this->view->pais = $pais;
        $this->view->id = $this->_request->getParam("id");
    }

    /**
     * Metodo salvaescolaridade()
     * Salva a escolaridade do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaescolaridadeAction()
    {
        $this->autenticacao();
        $dtAtual = date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $idDocumento = null;

        $tipoEscolaridade = $this->_request->getParam('tipoEscolaridade');
        $instituicao = $this->_request->getParam('instituicao');
        $curso = $this->_request->getParam('curso');
        $dtEntrada = $this->formatadata($this->_request->getParam('dtentrada'));
        $dtSaida = $this->formatadata($this->_request->getParam('dtsaida'));
        $pais = $this->_request->getParam('pais');
        $tipoDocumento = $this->_request->getParam('tipoDocumento');

        $tbEscolaridade = new TbEscolaridade();

        if (!empty($_FILES['arquivo'])):
            $tbDocumento = new tbDocumento();
        $tbArquivo = new tbArquivo();
        $tbArquivoImagem = new tbArquivoImagem();

        // pega as informacoes do arquivo
            $arquivoNome = $_FILES['arquivo']['name']; // nome
            $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome temporario
            $arquivoTipo = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

            if (!empty($arquivoNome)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
            }
        if (!empty($arquivoTemp)) {
            $arquivoBinario = Upload::setBinario($arquivoTemp); // binario
                $arquivoHash = Upload::setHash($arquivoTemp); // hash
        }
        endif;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            if (!empty($_FILES['arquivo'])):
                $dadosArquivo = array('nmArquivo' => $arquivoNome,
                    'sgExtensao' => $arquivoExtensao,
                    'stAtivo' => 'A',
                    'dsHash' => $arquivoHash,
                    'dtEnvio' => $dtAtual
                );

            $salvarArquivo = $tbArquivo->cadastrarDados($dadosArquivo);
            $idArquivo = $tbArquivo->buscarUltimo();

            $dadosArquivoImagem = array('idArquivo' => $idArquivo['idArquivo'],
                    'biArquivo' => $arquivoBinario
                );

            $dadosAI = "Insert into BDCORPORATIVO.scCorp.tbArquivoImagem
				  (idArquivo, biArquivo) values (" . $idArquivo['idArquivo'] . ", " . $arquivoBinario . ") ";

            $salvarArquivoImagem = $tbArquivoImagem->salvarDados($dadosAI);

            $dadosDocumento = array('idTipoDocumento' => 0,
                    'idArquivo' => $idArquivo['idArquivo']
                );

            $salvarDocumento = $tbDocumento->cadastrarDados($dadosDocumento);
            $ultimoDocumento = $tbDocumento->ultimodocumento();

            $idDocumento = $ultimoDocumento['idDocumento'];
            endif;


            $arrayDados = array('idAgente' => $idAgente,
                'idTipoEscolaridade' => $tipoEscolaridade,
                'nmCurso' => $curso,
                'nmInstituicao' => $instituicao,
                'dtInicioCurso' => $dtEntrada,
                'dtFimCurso' => $dtSaida,
                'idDocumento' => $idDocumento,
                'idPais' => $pais
            );

            $salvarInfo = $tbEscolaridade->inserirEscolaridade($arrayDados);

            $db->commit();
            parent::message("Cadastrado realizado com sucesso!", "agente/agentes/escolaridade/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $db->rollBack();
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agente/agentes/escolaridade/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo formacao()
     * @access public
     * @param void
     * @return void
     */
    public function formacaoAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");

        $escolaridade = new TbEscolaridade();

        $tipoEscolaridade = $escolaridade->BuscarTipoEscolaridade();
        $this->view->tipoEscolaridade = $tipoEscolaridade;

        $pais = $escolaridade->BuscarPais();
        $this->view->pais = $pais;

        $tipoDocumento = $escolaridade->BuscarTipoDocumento();
        $this->view->tipoDocumento = $tipoDocumento;

        $tbInfo = new Agente_Model_DbTable_TbInformacaoProfissional();

        $this->view->formacoes = $tbInfo->BuscarInfo($idAgente, null);

        $this->view->id = $this->_request->getParam("id");
        $visaoTable = new Agente_Model_DbTable_Visao();
        $this->view->visoes = $visaoTable->buscarVisao($idAgente);
    }

    /**
     * Metodo salvaformacao()
     * Salva a formacao do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaformacaoAction()
    {
        $this->autenticacao();
        $dtAtual = date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $profissao = $this->_request->getParam('profissao');
        $cargo = $this->_request->getParam('cargo');
        $endereco = $this->_request->getParam('endereco');
        $dtEntrada = $this->formatadata($this->_request->getParam('dtentrada'));
        $dtSaida = $this->formatadata($this->_request->getParam('dtsaida'));
        $tipoDocumento = $this->_request->getParam('tipoDocumento');

        $tbInfoPro = new Agente_Model_DbTable_TbInformacaoProfissional();
        $tbDocumento = new tbDocumento();
        $tbArquivo = new tbArquivo();
        $tbArquivoImagem = new tbArquivoImagem();


        // pega as informacoes do arquivo
        $arquivoNome = $_FILES['arquivo']['name']; // nome
        $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome temporario
        $arquivoTipo = $_FILES['arquivo']['type']; // tipo
        $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

        if (!empty($arquivoNome)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
        }
        if (!empty($arquivoTemp)) {
            $arquivoBinario = Upload::setBinario($arquivoTemp); // binario
            $arquivoHash = Upload::setHash($arquivoTemp); // hash
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $dadosArquivo = array('nmArquivo' => $arquivoNome,
                'sgExtensao' => $arquivoExtensao,
                'stAtivo' => 'A',
                'dsHash' => $arquivoHash,
                'dtEnvio' => $dtAtual
            );

            $salvarArquivo = $tbArquivo->cadastrarDados($dadosArquivo);
            $idArquivo = $tbArquivo->buscarUltimo();

            $dadosArquivoImagem = array('idArquivo' => $idArquivo['idArquivo'],
                'biArquivo' => $arquivoBinario
            );

            $dadosAI = "Insert into BDCORPORATIVO.scCorp.tbArquivoImagem
				  (idArquivo, biArquivo) values (" . $idArquivo['idArquivo'] . ", " . $arquivoBinario . ") ";

            $salvarArquivoImagem = $tbArquivoImagem->salvarDados($dadosAI);

            $dadosDocumento = array('idTipoDocumento' => 0,
                'idArquivo' => $idArquivo['idArquivo']
            );

            $salvarDocumento = $tbDocumento->cadastrarDados($dadosDocumento);
            $ultimoDocumento = $tbDocumento->ultimodocumento();

            $arrayDados = array('idAgente' => $idAgente,
                'nmProfissao' => $profissao,
                'nmCargo' => $cargo,
                'dsEndereco' => $endereco,
                'dtInicioVinculo' => $dtEntrada,
                'dtFimVinculo' => $dtSaida,
                'idDocumento' => $ultimoDocumento['idDocumento'],
                'siInformacao' => 0
            );

            $salvarInfo = $tbInfoPro->inserirInfo($arrayDados);

            $db->commit();
            parent::message("Cadastrado realizado com sucesso!", "agente/agentes/formacao/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $db->rollBack();
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agente/agentes/formacao/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo ferias()
     * @access public
     * @param void
     * @return void
     */
    public function feriasAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $ano = date('Y');

        $tbAusencia = new Agente_Model_DbTable_TbAusencia();

        $dados = $tbAusencia->carregarAusencia($idAgente, $ano, 2, null);

        $totalDias = 0;

        foreach ($dados as $d) {
            if (($d->siAusencia == 0) or ($d->siAusencia == 1)) {
                $totalDias = $totalDias + $d->qtddias;
            }
        }

        $this->view->id = $this->_request->getParam("id");
        $this->view->totalDias = $totalDias;
        $this->view->dadosferias = $dados;
    }

    /**
     * Metodo salvaferias()
     * Salva as ferias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaferiasAction()
    {
        $this->autenticacao();

        $dtAtual = Date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $dtInicio = $this->formatadata($this->_request->getParam("dtinicio"));
        $dtFim = $this->formatadata($this->_request->getParam("dtfim"));

        $tbAusencia = new Agente_Model_DbTable_TbAusencia();

        $repetida = $tbAusencia->BuscarAusenciaRepetida($idAgente, $dtInicio, $dtFim);

        if (count($repetida) > 0) {
            parent::message("J&aacute; existe agendamento de f&eacute;rias para o per&iacute;odo solicitado!", "agente/agentes/ferias/id/" . $idAgente . "?dtInicio=" . $this->_request->getParam("dtinicio") . "&dtFim=" . $this->_request->getParam("dtfim"), "ALERT");
        }

        try {
            $dados = array('idTipoAusencia' => 2,
                'idAgente' => $idAgente,
                'dtInicioAusencia' => $dtInicio,
                'dtFimAusencia' => $dtFim,
                'stImpacto' => 0,
                'siAusencia' => 0,
                'dsJustificativaAusencia' => '',
                'dtCadastroAusencia' => $dtAtual
            );

            // salva o novo registro
            $salvar = $tbAusencia->inserir($dados);

            // Busca o id do registro cadastrado
            $ultimoRegistro = $tbAusencia->UltimoRegistro();

            // Monta o array com o id do ultimo registro cadastrado
            $dados = array('idAlteracao' => $ultimoRegistro[0]->id);

            // Alterar o ultimo registro cadastrado colocando o seu próprio id no campo idalteracao
            $altera = $tbAusencia->alteraAusencia($dados, $ultimoRegistro[0]->id);

            parent::message("Suas f&eacute;rias foram agendas para " . $dtInicio . " &agrave; " . $dtFim . ". Aguarde Aprova&ccedil;&atilde;o do Coordenador.
							<br /> Caso n&atilde;o tenha resposta favor entre em contato com o mesmo!", "agente/agentes/ferias/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Error! " . $e->getMessage(), "agente/agentes/ferias/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo alterarferias()
     * Altera as ferias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function alterarferiasAction()
    {
        $this->autenticacao();
        $dtAtual = Date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $dtInicio = $this->formatadata($this->_request->getParam("dtinicioalteracao"));
        $dtFim = $this->formatadata($this->_request->getParam("dtfimalteracao"));
        $justificativa = trim(strip_tags($this->_request->getParam("justificativa")));
        $tipoAlteracao = $this->_request->getParam("tipoalteracao");
        $idferias = $this->_request->getParam("idferias");
        $stAusencia = 0;

        if ($tipoAlteracao == 0) {
            $stAusencia = 2;
        } else {
            $stAusencia = 3;
        }

        try {
            $tbAusencia = new Agente_Model_DbTable_TbAusencia();

            if ($tipoAlteracao == 1) {
                $dados = array('dsJustificativaAusencia' => $justificativa,
                    'siAusencia' => 3
                );

                $altera = $tbAusencia->alteraAusencia($dados, $idferias);
            } else {
                $repetida = $tbAusencia->BuscarAusenciaRepetida($idAgente, $dtInicio, $dtFim);

                if (count($repetida) > 0) {
                    parent::message("J&aacute; existe f&eacute;rias marcada dentro desse per&iacute;odo!", "agente/agentes/ferias/id/" . $idAgente, "ALERT");
                }


                $dados = array('dsJustificativaAusencia' => $justificativa,
                    'siAusencia' => 2
                );

                $altera = $tbAusencia->alteraAusencia($dados, $idferias);

                $dados = array('idTipoAusencia' => 2,
                    'idAgente' => $idAgente,
                    'dtInicioAusencia' => $dtInicio,
                    'dtFimAusencia' => $dtFim,
                    'stImpacto' => 0,
                    'siAusencia' => 0,
                    'dsJustificativaAusencia' => '',
                    'idAlteracao' => $idferias,
                    'dtCadastroAusencia' => $dtAtual
                );

                $insere = $tbAusencia->inserirAusencia($dados);
            }

            parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "agente/agentes/ferias/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agente/agentes/ferias/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo cancelaferias()
     * Cancela as ferias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function cancelaferiasAction()
    {
        $this->autenticacao();
        $dtAtual = Date("Y/m/d");
        $idAgente = $this->_request->getParam("id");
        $justificativa = trim(strip_tags($this->_request->getParam("justificativa")));
        $idferias = $this->_request->getParam("idferias");

        try {
            $tbAusencia = new Agente_Model_DbTable_TbAusencia();

            $dados = array('dsJustificativaAusencia' => $justificativa,
                'siAusencia' => 3
            );

            $altera = $tbAusencia->alteraAusencia($dados, $idferias);
            parent::message("Exclus&atilde;o realizada com sucesso!", "agente/agentes/painelferias", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agente/agentes/painelferias", "ERROR");
        }
    }

    /**
     * Metodo confirmaferias()
     * Confirma as ferias do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function confirmaferiasAction()
    {
        $this->autenticacao();
        $idferias = $this->_request->getParam("idferias");

        try {
            $tbAusencia = new Agente_Model_DbTable_TbAusencia();

            $dados = array('siAusencia' => 1);

            $altera = $tbAusencia->alteraAusencia($dados, $idferias);
            parent::message("Aprovado com sucesso!", "agente/agentes/painelferias", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agente/agentes/painelferias", "ERROR");
        }
    }

    /**
     * Metodo atestados()
     * @access public
     * @param void
     * @return void
     */
    public function atestadosAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $ano = date('Y');

        $tbAusencia = new Agente_Model_DbTable_TbAusencia();
        $atestados = $tbAusencia->carregarAusencia($idAgente, $ano, 1, null);

        $this->view->atestados = $atestados;
    }

    /**
     * Metodo salvaatestado()
     * Salva o atestado do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvaatestadoAction()
    {
        $this->autenticacao();
        $dtAtual = Date("Y/m/d h:i:s");

        $idAgente = $this->_request->getParam("id");
        $dtInicio = $this->formatadata($this->_request->getParam("dtinicio"));
        $dtFim = $this->formatadata($this->_request->getParam("dtfim"));
        $impacto = $this->_request->getParam("impacto");

        $tbAusencia = new Agente_Model_DbTable_TbAusencia();
        $tbDocumento = new tbDocumento();
        $tbArquivo = new tbArquivo();
        $tbArquivoImagem = new tbArquivoImagem();

        // pega as informacoes do arquivo
        $arquivoNome = $_FILES['arquivo']['name']; // nome
        $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome temporario
        $arquivoTipo = $_FILES['arquivo']['type']; // tipo
        $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

        if (!empty($arquivoNome)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
        }
        if (!empty($arquivoTemp)) {
            $arquivoBinario = Upload::setBinario($arquivoTemp); // binario
            $arquivoHash = Upload::setHash($arquivoTemp); // hash
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $dadosArquivo = array('nmArquivo' => $arquivoNome,
                'sgExtensao' => $arquivoExtensao,
                'stAtivo' => 'A',
                'dsHash' => $arquivoHash,
                'dtEnvio' => $dtAtual
            );

            $salvarArquivo = $tbArquivo->cadastrarDados($dadosArquivo);
            $idArquivo = $tbArquivo->buscarUltimo();

            $dadosArquivoImagem = array('idArquivo' => $idArquivo['idArquivo'],
                'biArquivo' => $arquivoBinario
            );

            $dadosAI = "Insert into BDCORPORATIVO.scCorp.tbArquivoImagem
				  (idArquivo, biArquivo) values (" . $idArquivo['idArquivo'] . ", " . $arquivoBinario . ") ";

            $salvarArquivoImagem = $tbArquivoImagem->salvarDados($dadosAI);

            $dadosDocumento = array('idTipoDocumento' => 0,
                'idArquivo' => $idArquivo['idArquivo']
            );

            $salvarDocumento = $tbDocumento->cadastrarDados($dadosDocumento);
            $ultimoDocumento = $tbDocumento->ultimodocumento();

            $dadosAusencia = array('idTipoAusencia' => 1,
                'idAgente' => $idAgente,
                'dtInicioAusencia' => $dtInicio,
                'dtFimAusencia' => $dtFim,
                'stImpacto' => $impacto,
                'idDocumento' => $ultimoDocumento['idDocumento'],
                'siAusencia' => 0,
                'dsJustificativaAusencia' => '',
                'dtCadastroAusencia' => $dtAtual
            );


            // salva o novo registro
            $salvar = $tbAusencia->inserir($dadosAusencia);

            // Busca o id do registro cadastrado
            $ultimoRegistro = $tbAusencia->UltimoRegistro();

            // Monta o array com o id do ultimo registro cadastrado
            $dados = array('idAlteracao' => $ultimoRegistro[0]->id);

            // Alterar o ultimo registro cadastrado colocando o seu próprio id no campo idalteracao
            $altera = $tbAusencia->alteraAusencia($dados, $ultimoRegistro[0]->id);


            /* ********************************************************************************************** */
            if ($impacto == 1) {
                // Tem que pegar todos os produtos que estao como Parecerista e devolver para o Coord.
                // Criar uma funcao para isso!


                $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
                $projetoDAO = new Projetos();
                $projetos = $projetoDAO->buscaProjetosProdutosAnaliseInicial(array('idAgenteParecerista = ?' => $idAgente, 'DtDistribuicao >= ?' => '' . $dtInicio . '', 'DtDistribuicao <= ?' => '' . $dtFim . ''));

                foreach ($projetos as $p) {
                    $dados = array('Observacao' => 'Devolvido por motivo de atestado m&eacute;dico.',
                            'idUsuario' => $this->getIdUsuario,
                            'DtDevolucao' => $dtAtual
                    );
                    $salvar = $tbDistribuirParecer->atualizarParecer($dados, $p->idDistribuirParecer);
                }
            }
            /* ********************************************************************************************** */

            $db->commit();
            parent::message("Cadastro realizado com sucesso!", "agente/agentes/atestados/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            $db->rollBack();
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agente/agentes/atestados/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo credenciamento()
     * @access public
     * @param void
     * @return void
     */
    public function credenciamentoAction()
    {
        $this->autenticacao();

        if (($this->GrupoAtivoSalic != 137) || ($this->getParecerista != 'sim')) {
            parent::message("Você n&atilde;o tem permiss&atilde;o para essa funcionalidade!", "agente/agentes/sempermissao", "ALERT");
        }

        $idAgente = $this->_request->getParam("id");

        $tbCredenciamentoParecerista = new Agente_Model_DbTable_TbCredenciamentoParecerista();
        $credenciados = $tbCredenciamentoParecerista->carregar($idAgente);

        $tbInformacaoProfissional = new Agente_Model_DbTable_TbInformacaoProfissional();
        $buscaAnos = $tbInformacaoProfissional->AnosExperiencia($idAgente);

        $anos = 0;

        foreach ($buscaAnos as $a) {
            $anos = $anos + $a->qtdAnos;
        }

        $Verificacao = new VerificacaoAGENTES();
        $buscaNivel = $Verificacao->buscar(array('idtipo=?' => 25), 'Descricao');

        $this->view->anosexperiencia = $anos;
        $this->view->credenciados = $credenciados;
        $this->view->Niveis = $buscaNivel;
    }

    /**
     * Metodo descredenciamento()
     * @access public
     * @param void
     * @return void
     */
    public function descredenciamentoAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $idCredenciamento = $this->_request->getParam("idcredenciamento");
        $siCredenciamento = $this->_request->getParam("sicredenciamento");
        $novoSiCredenciamento = 1;

        //Caso o credenciamento esteja ativo ele vai desativar
        //Caso contrario, ele ativa
        if ($siCredenciamento == 1) {
            $novoSiCredenciamento = 0;
        }

        $tbCredenciamentoParecerista = new Agente_Model_DbTable_TbCredenciamentoParecerista();

        try {
            $dados = array('siCredenciamento' => $novoSiCredenciamento);

            $exclui = $tbCredenciamentoParecerista->excluiCredenciamento($idCredenciamento);
            parent::message("Descredenciamento realizado com sucesso!", "agente/agentes/credenciamento/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao alterar o credenciamento! " . $e->getMessage(), "agente/agentes/credenciamento/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * Metodo salvacredenciamento()
     * Salva o credenciamento do Parecerista
     * @access public
     * @param void
     * @return void
     */
    public function salvacredenciamentoAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $areaCultural = $this->_request->getParam("areaCultural");
        $segmentoCultural = $this->_request->getParam("segmentoCultural");
        $nivel = $this->_request->getParam("nivel");

        $tbCredenciamentoParecerista = new Agente_Model_DbTable_TbCredenciamentoParecerista();

        $idArea = substr($areaCultural, 0, 1);
        $idSegmento = substr($segmentoCultural, 0, 1);

        $qtdSegmento = $tbCredenciamentoParecerista->QtdSegmento($idAgente, $idArea);

        $qtdArea = $tbCredenciamentoParecerista->QtdArea($idAgente);
        if ((($qtdArea[0]->qtd) >= 3) and (($qtdSegmento[0]->qtd) == 0)) {
            parent::message("Voc&ecirc; s&oacute; pode credenciar  3 (tr&ecirc;s) &aacute;reas culturais!", "agente/agentes/credenciamento/id/" . $idAgente, "ALERT");
        }

        $verificarCadastrado = $tbCredenciamentoParecerista->verificarCadastrado($idAgente, $segmentoCultural, $areaCultural);

        if (count($verificarCadastrado) > 0) {
            parent::message("&Aacute;rea e segmento j&aacute; credenciado!", "agente/agentes/credenciamento/id/" . $idAgente, "ALERT");
        }

        try {
            $dados = array('idAgente' => $idAgente,
                'idCodigoArea' => $areaCultural,
                'idCodigoSegmento' => $segmentoCultural,
                'idVerificacao' => $nivel,
                'siCredenciamento' => 1
            );
            $credenciados = $tbCredenciamentoParecerista->inserirCredenciamento($dados);

            parent::message("Credenciamento realizado com sucesso!", "agente/agentes/credenciamento/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao cadastrar! " . $e->getMessage(), "agente/agentes/credenciamento/id/" . $idAgente, "ERROR");
        }
    }

    /**
     * @author Alysson Vicuña de Oliveira
     * Metodo agentecadastrado()
     * @access public
     * @param void
     * @return void
     */
    public function agentecadastradoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);
        $cpf = preg_replace('/\.|-|\//', '', $_REQUEST['cpf']);

        $novos_valores = array();
        $dados = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);

        if ((strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) || (strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf))) {
            $novos_valores[0]['msgCPF'] = utf8_encode('invalido');
        } else {
            if (count($dados) != 0) {
                foreach ($dados as $dado) {
                    $dado = ((array) $dado);
                    array_walk($dado, function ($value, $key) use (&$dado) {
                        $dado[$key] = utf8_encode($value);
                    });
                    $novos_valores[0]['msgCPF'] = utf8_encode('cadastrado');
                    $novos_valores[0]['idAgente'] = utf8_encode($dado['idagente']);
                    $novos_valores[0]['Nome'] = utf8_encode($dado['nome']);
                    $novos_valores[0]['agente'] = $dado;
                }
            } else {
                #Instancia a Classe de Servico do WebService da Receita Federal
                $wsServico = new ServicosReceitaFederal();
                if (11 == strlen($cpf)) {
                    $arrResultado = $wsServico->consultarPessoaFisicaReceitaFederal($cpf);
                    if (count($arrResultado) > 0) {
                        $novos_valores[0]['msgCPF'] = utf8_encode('novo');
                        $novos_valores[0]['idAgente'] = $arrResultado['idPessoaFisica'];
                        $novos_valores[0]['Nome'] = utf8_encode($arrResultado['nmPessoaFisica']);
                        $novos_valores[0]['Cep'] = isset($arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep']) && $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] ? $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] : '';
                    }
                } elseif (14 == strlen($cpf)) {
                    $arrResultado = $wsServico->consultarPessoaJuridicaReceitaFederal($cpf);
                    if (count($arrResultado) > 0) {
                        $novos_valores[0]['msgCPF'] = utf8_encode('novo');
                        $novos_valores[0]['idAgente'] = $arrResultado['idPessoaJuridica'];
                        $novos_valores[0]['Nome'] = utf8_encode($arrResultado['nmRazaoSocial']);
                        $novos_valores[0]['Cep'] = isset($arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep']) && $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] ? $arrResultado['pessoa']['enderecos'][0]['logradouro']['nrCep'] : '';
                        ;
                    }
                }
            }
        }

        $this->_helper->json($novos_valores);
        die;
    }

    /**
     * salvaragente
     *
     * @access private
     * @return void
     * @author wouerner <wouerner@gmail.com>
     * @todo refatorar metodo para um generico que possa salvar todas as
     * possibilidades
     */
    private function salvaragente()
    {
        $arrAuth = (array)Zend_Auth::getInstance()->getIdentity();
        $usuario = isset($arrAuth['IdUsuario']) ? $arrAuth['IdUsuario'] : $arrAuth['usu_codigo'];
        $arrayAgente = array(
            'cnpjcpf' => $this->_request->getParam("cpf"),
            'tipopessoa' => $this->_request->getParam("Tipo"),
            'status' => 0,
            'usuario' => $usuario
        );

        try {
            $mprAgentes = new Agente_Model_AgentesMapper();
            $mprNomes = new Agente_Model_NomesMapper();
            $mdlAgente = new Agente_Model_Agentes($arrayAgente);
            $mprAgentes->save($mdlAgente);

            $agente = $mprAgentes->findBy(array('cnpjcpf' => $mdlAgente->getCnpjcpf()));
            $cpf = preg_replace('/\.|-|\//', '', $this->_request->getParam("cpf"));
            $idAgente = $agente['idAgente'];
            $nome = $this->_request->getParam("nome");
            $TipoNome = (strlen($mdlAgente->getCnpjcpf()) == 11 ? 18 : 19); // 18 = pessoa fisica e 19 = pessoa juridica

            if ($this->modal == "s") {
                $nome = Seguranca::tratarVarAjaxUFT8($nome);
            }
//            $nome = preg_replace('/[^A-Za-zZ0-9\ ]/', '', $nome);
            $nome = preg_replace('/[\'\"\n\`\´]/', '', $nome);

            try {
                $arrNome = array(
                    'idagente' => $idAgente,
                    'tiponome' => $TipoNome,
                    'descricao' => $nome,
                    'status' => 0,
                    'usuario' => $usuario
                );

                $mprNomes->save(new Agente_Model_Nomes($arrNome));
            } catch (Exception $e) {
                throw new Exception("Erro ao salvar o nome: " . $e->getMessage());
            }

            // ================================================ INICIO VISAO ======================================================
            $Visao = $this->_request->getParam("visao");
            $grupologado = $this->_request->getParam("grupologado");
            /*
             * Validacao - Se for componente da comissao ele nao salva a visao
             * Regra o componente da comissao nao pode alterar sua visao.
             */
            if ($grupologado != Autenticacao_Model_Grupos::COMPONENTE_COMISSAO) :

                $GravarVisao = array(
                    'idagente' => $idAgente,
                    'visao' => $Visao,
                    'usuario' => $usuario,
                    'stativo' => 'A');

            try {
                $visaoTable = new Agente_Model_DbTable_Visao();
                $busca = $visaoTable->buscarVisao($idAgente, $Visao);
                if (!$busca) {
                    $i = $visaoTable->cadastrarVisao($GravarVisao);
                }
            } catch (Exception $e) {
                throw new Exception("Erro ao salvar a vis&atilde;o: " . $e->getMessage());
            }
            // ================================================ FIM VISAO ======================================================

            // ===================== INICIO SALVAR TITULACAO (AREA/SEGMENTO DO COMPONENTE DA COMISSAO) ================================
            $titular = $this->_request->getParam("titular");
            $areaCultural = $this->_request->getParam("areaCultural");
            $segmentoCultural = $this->_request->getParam("segmentoCultural");

            // só salva area e segmento para a visao de Componente da Comissao e se os campos titular e areaCultural forem informados
            if ((int)$Visao == VisaoModel::COMPONENTE_DA_COMISSAO && ((int)$titular == 0 || (int)$titular == 1) && !empty($areaCultural)) {
                $GravarComponente = array(// insert
                        'idAgente' => $idAgente,
                        'cdArea' => $areaCultural,
                        'cdSegmento' => $segmentoCultural,
                        'stTitular' => $titular,
                        'stConselheiro' => 'A');

                $AtualizarComponente = array(// update
                        'cdArea' => $areaCultural,
                        'cdSegmento' => $segmentoCultural,
                        'stTitular' => $titular
                    );

                try {
                    // busca a titulacao do agente (titular/suplente de area cultural)
                    $busca = TitulacaoConselheiroDAO::buscarComponente($idAgente, $Visao);

                    if (!$busca) {
                        $i = TitulacaoConselheiroDAO::gravarComponente($GravarComponente);
                    } else {
                        $i = TitulacaoConselheiroDAO::atualizaComponente($idAgente, $AtualizarComponente);
                    }
                } catch (Exception $e) {
                    throw new Exception("Erro ao salvar a &aacute;rea e segmento: " . $e->getMessage());
                }
            }

            // ============================= FIM SALVAR TITULACAO (area/SEGMENTO DO COMPONENTE DA COMISSAO) ===========================

            endif;

            // =========================================== INICIO SALVAR ENDERECOS ====================================================
            $cepEndereco = $this->_request->getParam("cep");
            $tipoEndereco = $this->_request->getParam("tipoEndereco");
            $ufEndereco = $this->_request->getParam("uf");
            $CidadeEndereco = $this->_request->getParam("cidade");
            $Endereco = $this->_request->getParam("logradouro");
            $divulgarEndereco = $this->_request->getParam("divulgarEndereco");
            $tipoLogradouro = $this->_request->getParam("tipoLogradouro");
            $numero = $this->_request->getParam("numero");
            $complemento = $this->_request->getParam("complemento");
            $bairro = $this->_request->getParam("bairro");
            $enderecoCorrespodencia = 1;

            try {
                $arrayEnderecos = array(
                    'idagente' => $idAgente,
                    'cep' => str_replace(".", "", str_replace("-", "", $cepEndereco)),
                    'tipoendereco' => $tipoEndereco,
                    'uf' => $ufEndereco,
                    'cidade' => $CidadeEndereco,
                    'logradouro' => $Endereco,
                    'divulgar' => $divulgarEndereco,
                    'tipologradouro' => $tipoLogradouro,
                    'numero' => $numero,
                    'complemento' => $complemento,
                    'bairro' => $bairro,
                    'status' => $enderecoCorrespodencia,
                    'usuario' => $usuario
                );

                $enderecoDAO = new Agente_Model_EnderecoNacionalDAO();
                $insere = $enderecoDAO->inserir($arrayEnderecos);
            } catch (Exception $e) {
                throw new Exception("Erro ao salvar o endere&ccedil;o: " . $e->getMessage());
            }
            // ============================================= FIM SALVAR ENDERECOS ====================================================

            // =========================================== INICIO SALVAR TELEFONES ====================================================
            $exibirTelefone = $this->_request->getParam("exibirTelefone");
            if ($exibirTelefone == 's') {
                $tipoFone = $this->_request->getParam("tipoFone");
                $ufFone = $this->_request->getParam("ufFone");
                $dddFone = $this->_request->getParam("dddFone");
                $Fone = $this->_request->getParam("fone");
                $divulgarFone = $this->_request->getParam("divulgarFone");

                try {
                    $arrayTelefones = array(
                        'idagente' => $idAgente,
                        'tipotelefone' => $tipoFone,
                        'uf' => $ufFone,
                        'ddd' => $dddFone,
                        'numero' => $Fone,
                        'divulgar' => $divulgarFone,
                        'usuario' => $usuario
                    );

                    $insereTelefone = new Agente_Model_DbTable_Telefones();
                    $insere = $insereTelefone->insert($arrayTelefones);
                } catch (Exception $e) {
                    throw new Exception("Erro ao salvar o telefone: " . $e->getMessage());
                }
            }
            // =========================================== FIM SALVAR TELEFONES ====================================================

            // =========================================== INICIO SALVAR EMAILS ====================================================
            $exibirEmail = $this->_request->getParam("exibirEmail");
            if ($exibirEmail == 's') {
                $tipoEmail = $this->_request->getParam("tipoEmail");
                $Email = $this->_request->getParam("email");
                $divulgarEmail = $this->_request->getParam("divulgarEmail");
                $enviarEmail = 1;

                try {
                    $arrayEmail = array(
                        'idagente' => $idAgente,
                        'tipointernet' => $tipoEmail,
                        'descricao' => $Email,
                        'status' => $enviarEmail,
                        'divulgar' => $divulgarEmail,
                        'usuario' => $usuario
                    );

                    $insere = new Agente_Model_Email();
                    $insere = $insere->inserir($arrayEmail);
                } catch (Exception $e) {
                    throw new Exception("Erro ao salvar o e-mail: " . $e->getMessage());
                }
            }
            // =========================================== FIM SALVAR EMAILS ====================================================

            // ================ INICIO SALVAR VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE) ================
            $movimentacacaobancaria = $this->_request->getParam('movimentacaobancaria');
            $acao = null;
            if (empty($movimentacacaobancaria)) {
                try {
                    $this->vincular($cpf, $idAgente);
                } catch (Exception $e) {
                    throw new Exception("Erro ao vincular agente: " . $e->getMessage());
                }
                // ================ FIM SALVAR VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE) ================

                // Caso venha do UC 89 Solicitar Vinculo
                $acao = $this->_request->getParam('acao');
                #$idResponsavel = $this->idResponsavel;
                $idResponsavel = 0;

                // ============== VINCULA O RESPONSAVEL COM O PROPONENTE CADASTRADO =============================
                if ((!empty($acao)) && (!empty($idResponsavel))):
                    $tbVinculo = new Agente_Model_DbTable_TbVinculo();
                $dadosVinculo = array(
                        'idAgenteProponente' => $idAgente,
                        'dtVinculo' => new Zend_Db_Expr('GETDATE()'),
                        'siVinculo' => 0,
                        'idUsuarioResponsavel' => $idResponsavel
                    );
                $tbVinculo->inserir($dadosVinculo);
                endif;
            }

            //================ FIM VINCULA O RESPONSAVEL COM O PROPONENTE CADASTRADO ========================
            if (isset($acao) && $acao != '') {
                // Retorna para o listar propostas
                $tbVinculo = new Agente_Model_DbTable_TbVinculo();
                $dadosVinculo = array(
                    'idAgenteProponente' => $idAgente,
                    'dtVinculo' => new Zend_Db_Expr('GETDATE()'),
                    'siVinculo' => 0,
                    'idUsuarioResponsavel' => $arrAuth['IdUsuario']
                );
                $tbVinculo->inserir($dadosVinculo);
            }

            // Se vim do UC 10 - solicitar alteracao no Projeto
            // Chega aqui com o idpronac
            $idpronac = $this->_request->getParam('idpronac');
            // Se vim do UC38 - Movimentacao bancaria - Captacao
            $projetofnc = $this->_request->getParam('cadastrarprojeto');

            # tratamento para disparar "js custom event" no dispatch
            $agente = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf);
            $agente = $agente[0];
            $agente->id = $agente->idagente;
            $agente->cpfCnpj = $agente->cnpjcpf;

            $agenteArray = (array)$agente;
            array_walk($agenteArray, function ($value, $key) use ($agente) {
                $agente->$key = utf8_encode($value);
            });

            $this->salvarAgenteRedirect($agente, $idpronac, $projetofnc, $movimentacacaobancaria, $acao);
        } catch (Exception $e) {
            if ($this->modal == 's') {
                $this->_helper->json(['status' => 'error', 'msg' => $e->getMessage()]);
            } else {
                parent::message($e->getMessage(), "agente/agentes/incluiragente", "ERROR");
            }
        }
    }

    /**
     * Metodo salvaagentegeral()
     * Salva os dados do agente
     * @access public
     * @param void
     * @return void
     */
    public function salvaagentegeralAction()
    {
        $this->autenticacao();
        $this->salvaragente();
    }
    /**
     * @deprecated action nao utilizada (27/03/2018)
     */
//    public function salvaagentegeralexternoAction()
//    {
//        $this->salvaragente();
//    }

    /**
     * salvarAgenteRedirect Metodo para efetuar o redirecionamento apos o cadastro de agentes
     *
     * @param mixed $agente
     * @param bool $idpronac
     * @param bool $projetofnc
     * @param bool $movimentacacaobancaria
     * @param bool $acao
     * @access private
     * @return void
     * @todo retirar html da controller
     */
    private function salvarAgenteRedirect($agente, $idpronac = null, $projetofnc = null, $movimentacacaobancaria = null, $acao = null)
    {
        // Se vim do UC 10 - solicitar alteracao no Projeto
        $idpronac = $this->_request->getParam('idpronac');
        // Se vim do UC38 - Movimentacao bancaria - Captacao
        $projetofnc = $this->_request->getParam('cadastrarprojeto');

        if ($this->getRequest()->isXmlHttpRequest() ||
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            echo '<script>';
            echo 'var event = new CustomEvent("agenteCadastrar_POST", { "detail": ' . json_encode($agente) . ' });';
            echo 'document.dispatchEvent(event)';
            echo '</script>';
            echo '<div class="center-align">Cadastrado com sucesso!</div>';
            return;
        }

        if (!empty($idpronac)) {
            parent::message(
                'Cadastro realizado com sucesso!',
                "solicitaralteracao/acaoprojeto?idpronac={$idpronac}&cpf={$agente->cpfCnpj}",
                'CONFIRM'
            );
        } elseif (!empty($movimentacacaobancaria)) {
            parent::message("Cadastro realizado com sucesso.", "cadastrar-projeto", "CONFIRM");
        } elseif (($acao != '')) {
            parent::message(
                'Proponente cadastrado com sucesso. Uma solicita&ccedil;&atilde;o de v&iacute;nculo foi enviada a ele.',
                'proposta/manterpropostaincentivofiscal/listarproposta',
                'CONFIRM'
            );
        } else {
            // Caso nao seja ele retorna para a visualizacao dos dados cadastrados do agente
            # editado para atender
            parent::message('Cadastro realizado com sucesso!', "agente/agentes/agentes/id/{$agente->id}", 'CONFIRM');
        }
    }

    /**
     * Metodo incluirdirigente()
     * @access public
     * @param void
     * @return void
     */
    public function incluirdirigenteAction()
    {
        $this->autenticacao();
    }

    /**
     * Metodo salvadirigentegeral()
     * @access public
     * @param void
     * @return void
     */
    public function salvadirigentegeralAction()
    {
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usuario logado
        $idAgenteGeral = $this->_request->getParam("id"); // id da instituicao
        // =============================================== INICIO SALVAR CPF/CNPJ ==================================================

        $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($this->_request->getParam("cpf"))); // retira as mascaras
        $Tipo = $this->_request->getParam("Tipo");

        $arrayAgente = array(
            'cnpjcpf' => $cpf,
            'tipopessoa' => $Tipo,
            'status' => 0,
            'usuario' => $Usuario
        );

        $mprAgentes = new Agente_Model_AgentesMapper();
        $mdlAgente = new Agente_Model_Agentes($arrayAgente);
        $mprAgentes->save($mdlAgente);

        $agente = $mprAgentes->findBy(array('cnpjcpf' => $cpf));
        $idAgente = $agente['idAgente'];

        // ================================================ FIM SALVAR CPF/CNPJ =====================================================
        // ================================================ INICIO SALVAR NOME ======================================================

        $nome = $this->_request->getParam("nome");
        $TipoNome = (strlen($cpf) == 11 ? 18 : 19); // 18 = pessoa fisica e 19 = pessoa juridica

        try {
            $mprNomes = new Agente_Model_NomesMapper();
            $arrNome = array(
                'idagente' => $idAgente,
                'tiponome' => $TipoNome,
                'descricao' => $nome,
                'status' => 0,
                'usuario' => $Usuario
            );

            $mprNomes->save(new Agente_Model_Nomes($arrNome));
        } catch (Exception $e) {
            parent::message("Erro ao salvar o nome: " . $e->getMessage(), "agente/agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }

        // ================================================ FIM SALVAR NOME ======================================================
        // ================================================ INICIO SALVAR VISAO ======================================================
        $Visao = $this->_request->getParam("visao");

        $grupologado = $this->_request->getParam("grupologado");

        /*
         * Validacao - Se for componente da comissao ele nao salva a visao
         * Regra o componente da comissao nao pode alterar sua visao.
         */

        if ($grupologado != 118):

            $GravarVisao = array(// insert
                'idAgente' => $idAgente,
                'Visao' => $Visao,
                'Usuario' => $Usuario,
                'stAtivo' => 'A');

        try {
            $visaoTable = new Agente_Model_DbTable_Visao();
            $busca = $visaoTable->buscarVisao($idAgente, $Visao);

            if (!$busca) {
                $i = $visaoTable->cadastrarVisao($GravarVisao);
            }
        } catch (Exception $e) {
            parent::message("Erro ao salvar a vis&atilde;o: " . $e->getMessage(), "agente/agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }


        // ================================================ FIM SALVAR visao ======================================================
        // ===================== INICIO SALVAR TITULACAO (area/SEGMENTO DO COMPONENTE DA COMISSAO) ================================


        $titular = $this->_request->getParam("titular");
        $areaCultural = $this->_request->getParam("areaCultural");
        $segmentoCultural = $this->_request->getParam("segmentoCultural");

        // só salva area e segmento para a visao de Componente da Comissao e se os campos titular e areaCultural forem informados
        if ((int) $Visao == 210 && ((int) $titular == 0 || (int) $titular == 1) && !empty($areaCultural)) {
            $GravarComponente = array(// insert
                    'idAgente' => $idAgente,
                    'cdArea' => $areaCultural,
                    'cdSegmento' => $segmentoCultural,
                    'stTitular' => $titular,
                    'stConselheiro' => 'A');

            $AtualizarComponente = array(// update
                    'cdArea' => $areaCultural,
                    'cdSegmento' => $segmentoCultural,
                    'stTitular' => $titular
                );

            try {
                // busca a titulacao do agente (titular/suplente de area cultural)
                $busca = TitulacaoConselheiroDAO::buscarComponente($idAgente, $Visao);

                if (!$busca) {
                    $i = TitulacaoConselheiroDAO::gravarComponente($GravarComponente);
                } else {
                    $i = TitulacaoConselheiroDAO::atualizaComponente($idAgente, $AtualizarComponente);
                }
            } catch (Exception $e) {
                parent::message("Erro ao salvar a &aacute;rea e segmento: " . $e->getMessage(), $e->getMessage(), "agente/agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
            }
        }

        // ============================= FIM SALVAR TITULACAO (area/SEGMENTO DO COMPONENTE DA COMISSAO) ===========================

        endif; // Fecha o if da regra do componente da comissao
        // =========================================== INICIO SALVAR ENDERECOS ====================================================

        $cepEndereco = $this->_request->getParam("cep");
        $tipoEndereco = $this->_request->getParam("tipoEndereco");
        $ufEndereco = $this->_request->getParam("uf");
        $CidadeEndereco = $this->_request->getParam("cidade");
        $Endereco = $this->_request->getParam("logradouro");
        $divulgarEndereco = $this->_request->getParam("divulgarEndereco");
        $tipoLogradouro = $this->_request->getParam("tipoLogradouro");
        $numero = $this->_request->getParam("numero");
        $complemento = $this->_request->getParam("complemento");
        $bairro = $this->_request->getParam("bairro");
        $enderecoCorrespodencia = 1;

        try {
            $arrayEnderecos = array(
                'idAgente' => $idAgente,
                'Cep' => str_replace(".", "", str_replace("-", "", $cepEndereco)),
                'TipoEndereco' => $tipoEndereco,
                'UF' => $ufEndereco,
                'Cidade' => $CidadeEndereco,
                'Logradouro' => $Endereco,
                'Divulgar' => $divulgarEndereco,
                'TipoLogradouro' => $tipoLogradouro,
                'Numero' => $numero,
                'Complemento' => $complemento,
                'Bairro' => $bairro,
                'Status' => $enderecoCorrespodencia,
                'Usuario' => $Usuario
            );


            $insere = Agente_Model_EnderecoNacionalDAO::gravarEnderecoNacional($arrayEnderecos);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o endere&ccedil;o: " . $e->getMessage(), "agente/agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }


        // ============================================= FIM SALVAR ENDERECOS ====================================================
        // =========================================== INICIO SALVAR TELEFONES ====================================================

        $tipoFone = $this->_request->getParam("tipoFone");
        $ufFone = $this->_request->getParam("ufFone");
        $dddFone = $this->_request->getParam("dddFone");
        $Fone = $this->_request->getParam("fone");
        $divulgarFone = $this->_request->getParam("divulgarFone");


        try {
            $arrayTelefones = array(
                'idagente' => $idAgente,
                'tipotelefone' => $tipoFone,
                'uf' => $ufFone,
                'ddd' => $dddFone,
                'numero' => $Fone,
                'divulgar' => $divulgarFone,
                'usuario' => $Usuario
            );

            $insereTelefone = new Agente_Model_DbTable_Telefones();
            $insere = $insereTelefone->insert($arrayTelefones);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o telefone: " . $e->getMessage(), "agente/agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }


        // =========================================== FIM SALVAR TELEFONES ====================================================
        // =========================================== INICIO SALVAR EMAILS ====================================================

        $tipoEmail = $this->_request->getParam("tipoEmail");
        $Email = $this->_request->getParam("email");
        $divulgarEmail = $this->_request->getParam("divulgarEmail");
        $enviarEmail = 1;

        try {
            $arrayEmail = array(
                'idagente' => $idAgente,
                'tipointernet' => $tipoEmail,
                'descricao' => $Email,
                'status' => $enviarEmail,
                'divulgar' => $divulgarEmail,
                'usuario' => $Usuario
            );

            $insere = new Agente_Model_Email();
            $insere = $insere->inserir($arrayEmail);
        } catch (Exception $e) {
            parent::message("Erro ao salvar o e-mail: " . $e->getMessage(), "agente/agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }

        // =========================================== FIM SALVAR EMAILS ====================================================
        // =========================================== INICIO SALVAR VINCULO ====================================================
        try {
            // busca o dirigente vinculado ao cnpj/cpf
            $dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, $idAgente, $idAgenteGeral, $idAgenteGeral);

            // caso o agente nao esteja vinculado, realizara a vinculacao
            if (!$dadosDirigente) {
                // associa o dirigente ao cnpj/cpf
                $dadosVinculacao = array(
                    'idAgente' => $idAgente,
                    'idVinculado' => $idAgenteGeral,
                    'idVinculoPrincipal' => $idAgenteGeral,
                    'Usuario' => $Usuario
                );

                $vincular = Agente_Model_ManterAgentesDAO::cadastrarVinculados($dadosVinculacao);
            }
        } catch (Exception $e) {
            parent::message("Erro ao vincular o dirigente: " . $e->getMessage(), "agente/agentes/incluirdirigente/id/" . $idAgenteGeral, "ERROR");
        }


        parent::message("Cadastro realizado com sucesso!", "agente/agentes/dirigentes/id/" . $idAgenteGeral, "CONFIRM");
    }

    /**
     * Metodo vinculadirigente()
     * @access public
     * @param void
     * @return void
     */
    public function vinculadirigenteAction()
    {
        $this->autenticacao();
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario = isset($auth->getIdentity()->IdUsuario) ? $auth->getIdentity()->IdUsuario : $auth->getIdentity()->usu_codigo ; // id do usuario logado
        $idAgenteGeral = $this->_request->getParam("id");
        $idDirigente = $this->_request->getParam("idDirigente");

        try {
            // busca o dirigente vinculado ao cnpj/cpf
            $dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, $idDirigente, $idAgenteGeral, $idAgenteGeral);

            // caso o agente nao esteja vinculado, realizara a vinculacao
            if (count($dadosDirigente) == 0) {
                // associa o dirigente ao cnpj/cpf
                $dadosVinculacao = array(
                    'idAgente' => $idDirigente,
                    'idVinculado' => $idAgenteGeral,
                    'idVinculoPrincipal' => $idAgenteGeral,
                    'Usuario' => $Usuario
                );
                $vincular = Agente_Model_ManterAgentesDAO::cadastrarVinculados($dadosVinculacao);

                $Visao = 198; //Dirigente
                $GravarVisao = array(// insert
                    'idAgente' => $idDirigente,
                    'Visao' => $Visao,
                    'Usuario' => $Usuario,
                    'stAtivo' => 'A');
                $visaoTable = new Agente_Model_DbTable_Visao();
                $busca = $visaoTable->buscarVisao($idDirigente, $Visao);
                if (!$busca) {
                    $i = $visaoTable->cadastrarVisao($GravarVisao);
                }
            }

            parent::message("Cadastrado realizado com sucesso! ", "agente/agentes/dirigentes/id/" . $idAgenteGeral, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao vincular o dirigente: " . $e->getMessage(), "agente/agentes/visualizadirigente/id/" . $idAgenteGeral . "/idDirigente/" . $idDirigente, "ERROR");
        }
    }

    /**
     * Metodo desvinculadirigente()
     * @access public
     * @param void
     * @return void
     */
    public function desvinculadirigenteAction()
    {
        $this->autenticacao();
        $Usuario = $this->getIdUsuario; // id do usuario logado
        $idAgenteGeral = $this->_request->getParam("id");
        $idDirigente = $this->_request->getParam("idDirigente");

        try {
            $vincular = new Agente_Model_DbTable_Vinculacao();

            #$where = "Idcaptacao = " . $where;

            $where = array('idAgente = ' . $idDirigente,
                'idVinculado = ' . $idAgenteGeral);

            $desvincula = $vincular->Desvincular($where);

            parent::message("Exclus&atilde;o realizada com sucesso! ", "agente/agentes/dirigentes/id/" . $idAgenteGeral, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao vincular o dirigente: " . $e->getMessage(), "agente/agentes/visualizadirigente/id/" . $idAgenteGeral . "/idDirigente/" . $idDirigente, "ERROR");
        }
    }

    /**
     * Metodo para realizar a buscar de agentes por cpf/cnpj ou por nome
     * @access public
     * @param void
     * @return void
     */
    public function buscaragenteAction()
    {
        $this->autenticacao();
        // caso o formulario seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formulario
            $post = Zend_Registry::get('post');
            $cpf = Mascara::delMaskCPF(Mascara::delMaskCNPJ($post->cpf)); // deleta a mascara
            $nome = $post->nome;

            try {
                // validacao dos campos
                if (empty($cpf) && empty($nome)) {
                    throw new Exception("Dados obrigat&oacute;rios n&atilde;o informados:<br /><br />&eacute; necess&aacute;rio informar o CPF/CNPJ ou o Nome!");
                } elseif (!empty($cpf) && strlen($cpf) != 11 && strlen($cpf) != 14) { // valida cnpj/cpf
                    throw new Exception("O CPF/CNPJ informado &eacute; inv&aacute;lido!");
                } elseif (!empty($cpf) && strlen($cpf) == 11 && !Validacao::validarCPF($cpf)) { // valida cpf
                    throw new Exception("O CPF informado &eacute; inv&aacute;lido!");
                } elseif (!empty($cpf) && strlen($cpf) == 14 && !Validacao::validarCNPJ($cpf)) { // valida cnpj
                    throw new Exception("O CNPJ informado &eacute; inv&aacute;lido!");
                } else {
                    // redireciona para a pagina com a busca dos dados com paginacao
                    $this->redirect("agente/agentes/listaragente?cpf=" . $cpf . "&nome=" . $nome);
                }
            } catch (Exception $e) {
                $this->view->message = $e->getMessage();
                $this->view->message_type = "ERROR";
                $this->view->cpf = !empty($cpf) ? Validacao::mascaraCPFCNPJ($cpf) : ''; // caso exista, adiciona a mascara
                $this->view->nome = $nome;
            }
        }
    }

    /**
     * Metodo listaragente()
     * @access public
     * @param void
     * @return List
     */
    public function listaragenteAction()
    {
        $this->autenticacao();
        // recebe os dados via get
        $get = Zend_Registry::get('get');
        $cpf = $get->cpf;
        $nome = $get->nome;

        // realiza a busca por cpf e/ou nome
        $buscar = Agente_Model_ManterAgentesDAO::buscarAgentes($cpf, $nome);

        if (!$buscar) {
            // redireciona para a pagina de cadastro de agentes, e, exibe uma notificacao relativa ao cadastro
            parent::message("Agente n&atilde;o cadastrado!<br /><br />Por favor, cadastre o mesmo no formulário abaixo!", "/agente/manteragentes/agentes?acao=cc&cpf=" . $cpf . "&nome=" . $nome, "ALERT");
        } else {
            // ========== INICIO PAGINACAO ==========
            // criando a paginacao
            Zend_Paginator::setDefaultScrollingStyle('Sliding');
            $this->view->addScriptPath(APPLICATION_PATH.'/modules/default/views/scripts/paginacao');
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao.phtml');
            $paginator = Zend_Paginator::factory($buscar); // dados a serem paginados
            // pagina atual e quantidade de itens por pagina
            $currentPage = $this->_getParam('page', 1);
            $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(10); // 10 por pagina
            // ========== FIM PAGINACAO ==========

            $this->view->buscar = $paginator;
            $this->view->qtdAgentes = count($buscar); // quantidade de agentes
        }
    }

    /**
     * Metodo abrirarquivo()
     * Abrir arquivo em binario
     * @access public
     * @param void
     * @return void
     */
    public function abrirarquivoAction()
    {
        $this->autenticacao();
        $id = $this->_request->getParam("id");

        // Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        $tbArquivo = new tbArquivo();

        // busca o arquivo
        $resultado = $tbArquivo->buscarArquivo($id);

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // le os cabecalhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $this->getResponse()
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                        ->setHeader("Connection", "close")
                        ->setHeader("Content-transfer-encoding", "binary")
                        ->setHeader("Cache-control", "private");

                $this->getResponse()->setBody($r->biArquivo);
            }
        }
    }

    /**
     * Metodo painelcredenciamento()
     * Painel do Coordenador de Pronac
     * @access public
     * @param void
     * @return void
     */
    public function painelcredenciamentoAction()
    {
        $this->autenticacao();
        $agentes = new Agente_Model_DbTable_Agentes();

        $nome = $this->_request->getParam('nome');
        $cpf = Mascara::delMaskCPF($this->_request->getParam('cpf'));

        $buscar = $agentes->consultaPareceristasPainel($nome, $cpf);

        $this->view->dados = $buscar;
        $this->view->qtpareceristas = count($buscar);

        $orgaos = new Orgaos();
        $this->view->orgaos = $orgaos->pesquisarTodosOrgaos();
    }

    /**
     * Metodo painelferias()
     * Painel do Coordenador de Parecer
     * @access public
     * @param void
     * @return void
     */
    public function painelferiasAction()
    {
        $this->autenticacao();
        $ano = date('Y');

        $tbAusencia = new Agente_Model_DbTable_TbAusencia();

        $dados = $tbAusencia->BuscarAusenciaPainel($ano);

        $totalDias = 0;

        foreach ($dados as $d) {
            if (($d->siAusencia == 0) or ($d->siAusencia == 1)) {
                $totalDias = $totalDias + $d->qtdDias;
            }
        }

        $this->view->id = $this->_request->getParam("id");
        $this->view->totalDias = $totalDias;
        $this->view->dadosferias = $dados;
    }

    /**
     * Metodo sempermissao()
     * @access public
     * @param void
     * @return void
     */
    public function sempermissaoAction()
    {
        $this->autenticacao();
    }

    /**
     * Metodo com a pagina de alteracao de visao
     * @access public
     * @param void
     * @return void
     */
    public function alterarvisaoAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam('id');
        $GrupoAtivo = $this->GrupoAtivoSalic;

        // busca todas as visoes
        $visaoTable = new Agente_Model_DbTable_Visao();
        $visoes = $visaoTable->buscarVisao(null, null, true);
        $a = 0;
        $select = null;

        foreach ($visoes as $visaoGrupo) {
            if ($GrupoAtivo == 93 and ($visaoGrupo->idVerificacao == 209 or $visaoGrupo->idVerificacao == 216)) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 94 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 137 and $visaoGrupo->idVerificacao == 209) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 97) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 120 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 118 and $visaoGrupo->idVerificacao == 210) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 122 and ($visaoGrupo->idVerificacao == 210 or $visaoGrupo->idVerificacao == 216 or $GrupoAtivo == 123)) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            if ($GrupoAtivo == 121) {
                $select[$a]['idVerificacao'] = $visaoGrupo->idVerificacao;
                $select[$a]['Descricao'] = $visaoGrupo->Descricao;
            }
            $a++;
        }

        if ($GrupoAtivo == 1111) {
            $select[0]['idVerificacao'] = 144;
            $select[0]['Descricao'] = 'Proponente';
        }
        $this->view->visao = $select;

        // busca todas as visoes do agente
        $visaoTable = new Agente_Model_DbTable_Visao();
        $visoesAgente = $visaoTable->buscarVisao($idAgente);
        $b = 0;
        $selectAgente = null;
        foreach ($visoesAgente as $visaogrupo) {
            if ($GrupoAtivo == 93 and ($visaogrupo->idVerificacao == 209 or $visaogrupo->idVerificacao == 216)) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 94 and $visaogrupo->idVerificacao == 209) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 137 and $visaogrupo->idVerificacao == 209) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 97) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 120 and $visaogrupo->idVerificacao == 210) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 118 and $visaogrupo->idVerificacao == 210) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 122 and ($visaogrupo->idVerificacao == 210 or $visaogrupo->idVerificacao == 216 or $GrupoAtivo == 123)) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 121) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            if ($GrupoAtivo == 1111) {
                $selectAgente[$b]['idVerificacao'] = $visaogrupo->idVerificacao;
                $selectAgente[$b]['Descricao'] = $visaogrupo->Descricao;
            }
            $b++;
        }
        $this->view->visaoAgente = $selectAgente;
        //var_dump($selectAgente) ;die;

        // caso o formulario seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados do formulario
            $post = Zend_Registry::get('post');
            $visaoAgente = $post->visaoAgente;

            try {

                // exclui todas as visoes do agente
                $visaoTable = new Agente_Model_DbTable_Visao();
                $visaoTable->excluirVisao($idAgente);

                // cadastra todas as visoes do agente
                foreach ($visaoAgente as $visao) {
                    $dados = array(
                        'idagente' => $idAgente,
                        'visao' => $visao,
                        'usuario' => $this->getIdUsuario, // código do usuario logado
                        'stativo' => 'A');
                    $visaoTable->cadastrarVisao($dados);
                }

                parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "agente/agentes/alterarvisao/id/" . $idAgente, "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao efetuar altera&ccedil;&atilde;o das vis&otilde;es do agente! " . $e->getMessage(), "agente/agentes/alterarvisao/id/" . $idAgente, "ERROR");
            }
        }

        $this->view->id = $idAgente;
    }

    public function infoAdicionaisAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo; // Busca o perfil do usuario

        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $idAgente;

        $tbAgenteFisico = new Agente_Model_DbTable_TbAgenteFisico();
        $result = $tbAgenteFisico->buscar(array('idAgente = ?'=>$idAgente))->current();
        $this->view->dadosAdicionais = $result;
    }

    public function salvarInfoAdicionaisAction()
    {
        $post = Zend_Registry::get('post');

        $data = explode('/', $post->dtNascimento);
        $dtNascimento = $data[2].'-'.$data[1].'-'.$data[0];

        $processo = Mascara::delMaskProcesso($post->processo);

        $dados = array(
            'idAgente'                  => $post->agente,
            'stSexo'                    => $post->sexo,
            'stEstadoCivil'             => $post->estadoCivil,
            'stNecessidadeEspecial'     => $post->necEspecial,
            'nmMae'                     => $post->nomeMae,
            'nmPai'                     => $post->nomePai,
            'dtNascimento'              => $dtNascimento,
            'stCorRaca'                 => $post->raca,
            'nrIdentificadorProcessual' => $processo
        );

        $tbAgenteFisico = new Agente_Model_DbTable_TbAgenteFisico();

        $result = $tbAgenteFisico->buscar(array('idAgente = ?'=>$post->agente));

        try {
            if (count($result) > 0) {
                $msg = 'alterados';
                $tbAgenteFisico->alterarDados($dados, $post->agente);
            } else {
                $msg = 'cadastrados';
                $tbAgenteFisico->insert($dados);
            }

            parent::message("Dados $msg com sucesso!", "agente/agentes/info-adicionais/id/" . $post->agente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Ocorreu um erro durante a opera&ccedil;&atilde;o!", "agente/agentes/info-adicionais/id/" . $post->agente, "ERROR");
        }
    }

    /**
     * naturezaAction
     *
     * @access public
     * @return void
     */
    public function naturezaAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $idAgente;

        $tbVerificacao = new Agente_Model_DbTable_Verificacao();
        $direito = $tbVerificacao->combosNatureza(7);
        $this->view->direito = $direito;

        $esfera = $tbVerificacao->combosNatureza(8);
        $this->view->esfera = $esfera;

        $poder = $tbVerificacao->combosNatureza(9);
        $this->view->poder = $poder;

        $administracao = $tbVerificacao->combosNatureza(10);
        $this->view->administracao = $administracao;

        $Natureza = new Natureza();
        $result = $Natureza->buscar(array('idAgente = ?'=>$idAgente))->current();
        $this->view->dadosNatureza = $result;
    }

    /**
     * salvarNaturezaAction
     *
     * @access public
     * @return void
     */
    public function salvarNaturezaAction()
    {
        $post = Zend_Registry::get('post');
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idUsuario = isset($auth->getIdentity()->IdUsuario) ? $auth->getIdentity()->IdUsuario : $auth->getIdentity()->usu_codigo;

        $dados = array(
            'idAgente' => $post->agente,
            'Direito' => isset($post->direito) ? $post->direito : 0,
            'Esfera' => isset($post->esfera) ? $post->esfera : 0,
            'Poder' => isset($post->poder) ? $post->poder : 0,
            'Administracao' => isset($post->administracao) ? $post->administracao : 0,
            'Usuario' => $idUsuario
        );

        $Natureza = new Natureza();
        $result = $Natureza->buscar(array('idAgente = ?'=>$post->agente));
        try {
            if (count($result) > 0) {
                $result = $result->current();
                $msg = 'alterados';
                $Natureza->alterarDados($dados, $result->idNatureza);
            } else {
                $msg = 'cadastrados';
                $Natureza->inserir($dados);
            }
            parent::message("Dados $msg com sucesso!", "agente/agentes/natureza/id/" . $post->agente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Ocorreu um erro durante a opera&ccedil;&atilde;o!", "agente/agentes/natureza/id/" . $post->agente, "ERROR");
        }
    }

    /**
     * areaCulturalAction
     *
     * @access public
     * @return void
     */
    public function areaCulturalAction()
    {
        $this->autenticacao();
        $idAgente = $this->_request->getParam("id");
        $this->view->id = $idAgente;

        $Area = new Area();
        $areas = $Area->buscar(array(), array('Descricao'));
        $this->view->Areas = $areas;

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $areaCadastrada = $tbTitulacaoConselheiro->buscar(array('idAgente = ?'=>$idAgente));
        $this->view->AreaCadastrada = $areaCadastrada;
    }

    /**
     * salvarAreaCulturalAction
     *
     * @access public
     * @return void
     */
    public function salvarAreaCulturalAction()
    {
        $post = Zend_Registry::get('post');

        $dados = array(
            'cdArea' => $post->area,
            'cdSegmento' => !empty($post->segmento) ? $post->segmento : 0
        );

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $result = $tbTitulacaoConselheiro->buscar(array('idAgente = ?'=>$post->agente));

        try {
            if (count($result) > 0) {
                $result = $result->current();
                $msg = 'alterados';
                $tbTitulacaoConselheiro->alterarDados($dados, $result->idAgente);
            } else {
                $dados = array(
                    'cdArea' => $post->area,
                    'cdSegmento' => !empty($post->segmento) ? $post->segmento : 0,
                    'idAgente' =>  $post->agente,
                    'stTitular' => 0
               );

                $msg = 'cadastrados';
                $tbTitulacaoConselheiro->inserir($dados);
            }
            parent::message("Dados $msg com sucesso!", "agente/agentes/area-cultural/id/" . $post->agente, "CONFIRM");
        } catch (Exception $e) {
            parent::message("Ocorreu um erro durante a opera&ccedil;&atilde;o!", "agente/agentes/area-cultural/id/" . $post->agente, "ERROR");
        }
    }


    public function formUsuarioAgenteAction()
    {
        $idAgente = $this->_request->getParam("id");

        try {

            if (empty($idAgente)) {
                throw new Exception("Agente n&atilde;o encontrado");
            }

            if ($this->GrupoAtivoSalic == Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar esta funcionalidade");
            }

            $this->autenticacao();

            $dados = Agente_Model_ManterAgentesDAO::buscarAgentes(null, null, $idAgente);

            if (!$dados) {
                throw new Exception("Agente n&atilde;o encontrado!");
            }

            $visaoTable = new Agente_Model_DbTable_Visao();
            $visoes = $visaoTable->buscarVisao($idAgente);
            $this->view->visoes = $visoes;
            $this->view->dados = $dados;

            if ($dados[0]->tipopessoa == 1) {
                $dirigentes = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, null, null, $idAgente);
                $this->view->responsaveis = $dirigentes;
            } else {
                $sgcAcesso = new Autenticacao_Model_Sgcacesso();
                $objResponsavel = $sgcAcesso->buscarUsuario(['Cpf = ?' => $dados[0]->cnpjcpf])->current();

                $responsaveis = [];
                $responsavel = new stdClass();
                $responsavel->idResponsavel = $objResponsavel->IdUsuario;
                $responsavel->cpfResponsavel = $objResponsavel->cpf;
                $responsavel->nomeResponsavel = $objResponsavel->nome;
                $responsaveis[]  = $responsavel;
                $this->view->responsaveis = $responsaveis;
            }
            $this->view->dados = $dados;
        } catch (Exception $e) {
            parent::message($e->getMessage(), "/default/principal/", "ERROR");
        }
    }

    /**
     * Este metodo é para correcao de erro ao transformar projeto em proposta
     * o erro acontece quando existe agente com usuario invalido
     */
    public function salvarUsuarioAgenteAction()
    {
        $idAgente = (int)$this->_request->getParam("agente");
        $idResponsavel = (int)$this->_request->getParam("idResponsavel");

        try {

            if ($this->GrupoAtivoSalic == Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para esta a&ccedil;&atilde;o");
            }

            if (empty($idAgente) || empty($idResponsavel)) {
                throw new Exception("Dados obrigat&oacute;rios n&atilde;o informados");
            }

            $mprAgentes = new Agente_Model_AgentesMapper();
            $agente = $mprAgentes->findBy(['idAgente' => $idAgente]);

            if (empty($agente)) {
                throw new Exception("Agente n&atilde;o existe");
            }

            $where = [];
            $dados = [];
            $dbTableAgentes = new Agente_Model_DbTable_Agentes();
            $where[] = $dbTableAgentes->getAdapter()->quoteInto('idAgente = ?', $idAgente);
            $dados['Usuario'] = $idResponsavel;
            $dbTableAgentes->update($dados, $where);

            parent::message("Agente atualizado com sucesso!", "/agente/agentes/form-usuario-agente/id/" . $idAgente, "CONFIRM");
        } catch (Exception $e) {
            parent::message($e->getMessage(), "/agente/agentes/form-usuario-agente/id/" . $idAgente, "ERROR");
        }
    }
}
