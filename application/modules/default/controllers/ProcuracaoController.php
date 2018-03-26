<?php

class ProcuracaoController extends MinC_Controller_Action_Abstract
{
    private $idResponsavel = 0;
    private $idAgente      = 0;
    private $idUsuario     = 0;

    private $idPreProjeto  = null;
    private $orgaoSuperior = 0;

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */

    public function init()
    {
        // verifica as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 103;  // Coordenador de An�lise
        $PermissoesGrupo[] = 122;  // Coordenador de Acompanhamento

        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo');

        if (isset($auth->getIdentity()->usu_codigo)) {
            parent::perfil(1, $PermissoesGrupo);

            $orgaoSuperiorLogado = $GrupoAtivo->codOrgao;
            $orgao = new Orgaos();
            $orgaoSuperior = $orgao->codigoOrgaoSuperior($orgaoSuperiorLogado);
            @$this->orgaoSuperior = $orgaoSuperior[0]->Superior;
        } else {
            parent::perfil(4, $PermissoesGrupo);
        }

        /*********************************************************************************************************/
        $cpf = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_identificacao : $auth->getIdentity()->Cpf;

        // Busca na SGCAcesso
        $sgcAcesso = new Autenticacao_Model_Sgcacesso();
        $buscaAcesso = $sgcAcesso->buscar(array('Cpf = ?' => $cpf));

        // Busca na Usuarios
        $usuarioDAO = new Autenticacao_Model_Usuario();
        $buscaUsuario = $usuarioDAO->buscar(array('usu_identificacao = ?' => $cpf));

        // Busca na Agentes
        $agentesDAO = new Agente_Model_DbTable_Agentes();
        $buscaAgente = $agentesDAO->BuscaAgente($cpf);

        if (count($buscaAcesso) > 0) {
            $this->idResponsavel = $buscaAcesso[0]->IdUsuario;
            $this->view->nomeproponente = $buscaAcesso[0]->Nome;
        }
        if (count($buscaAgente) > 0) {
            $this->idAgente        = $buscaAgente[0]->idAgente;
        }
        if (count($buscaUsuario) > 0) {
            $this->idUsuario   = $buscaUsuario[0]->usu_codigo;
        }

        if ($this->idAgente != 0) {
            $this->usuarioProponente = "S";
        }

//        $this->view->nomeproponente = $buscaAcessos[0]->Nome;
        //x($this->idResponsavel);
        //x($this->idAgente);
        

        parent::init();
    }

    public function indexAction()
    {
        $p = new Projetos();
        $buscarprocuracao = $p->listarProjetosProcuracoes($this->idResponsavel);
        $this->view->buscarprocuracao = $buscarprocuracao;
    }

    /* M�dodo para cadastrar uma procura��o
     *
     *
     */
    public function cadastramentoAction()
    {
        $tbVinculo = new Agente_Model_DbTable_TbVinculo();
        $dadosCombo = array();
        $cpfCnpj = '';

        $whereResponsavel['a.idAgente = ?'] = $this->idAgente;
        $buscaResponsavel = $tbVinculo->buscarProponenteResponsavel($whereResponsavel, $this->idResponsavel);

        $whereProponente['a.idAgente != ?'] = $this->idAgente;
        $buscaProponente = $tbVinculo->buscarProponenteResponsavel($whereProponente, $this->idResponsavel);

        $this->view->responsaveis 	= $buscaResponsavel;
        $this->view->proponentes 	= $buscaProponente;
        $this->view->proponente 	= $this->idAgente;
    }

    public function listarprojetosAction()
    {
        $this->_helper->layout->disableLayout();

        $propostas = new Proposta_Model_DbTable_PreProjeto();
        $whereProjetos['pp.idAgente = ?'] 			=  $this->_request->getParam("idProponente");//$this->idAgente;
        $whereProjetos['pr.idProjeto IS NOT NULL'] 	=  '';
        $listaProjetos = $propostas->buscarPropostaProjetos($whereProjetos);

        $procuracaoDAO = new Procuracao();

        $listacerta = array();
        $i = 0;

        foreach ($listaProjetos as $pj) {
            $where['p.siProcuracao IN (0,1)'] 	= '';
            $where['vprp.idPreProjeto = ?'] 	= $pj->idPreProjeto ;
            $buscaProcuracao = $procuracaoDAO->buscarProcuracaoAceita($where);

            if (count($buscaProcuracao) > 0) {
                $listacerta[$i]['visualiza'] 	=  'N';
            } else {
                $listacerta[$i]['visualiza'] 	=  'S';
            }

            $listacerta[$i]['PRONAC'] 		= $pj->PRONAC;
            $listacerta[$i]['NomeProjeto'] 	= $pj->NomeProjeto;
            $listacerta[$i]['idProjeto'] 	= $pj->idProjeto;
            $listacerta[$i]['idPreProjeto'] = $pj->idPreProjeto;

            $i++;
        }

        $this->view->projetos 		= $listacerta;
        //$this->view->projetos 		= $listaProjetos;
    }


    public function uploadAction()
    {
        //======================= INSTANCIA AS DAO ===========================
        $tbArquivoDAO 			= new tbArquivo();
        $tbArquivoImagemDAO 	= new tbArquivoImagem();
        $tbDocumentoDAO 		= new tbDocumento();
        $ProcuracaoDAO 			= new Procuracao();
        $tbVinculoPropostaDAO 	= new Agente_Model_DbTable_TbVinculoProposta();
        $tbVinculoDAO 			= new Agente_Model_DbTable_TbVinculo();
        $Sgcacesso              = new Autenticacao_Model_Sgcacesso();
        $Agentes                = new Agente_Model_DbTable_Agentes();
        $Nomes                  = new Nomes();
        $Visao                  = new Visao();
        $Internet               = new Internet();

        //================== VARI�VEIS PASSADAS VIA POST =====================
        $responsavel 	= $this->_request->getParam("responsavel");
        $proponente 	= $this->_request->getParam("proponente");
        $dsObservacao 	= $this->_request->getParam("dsObservacao");
        $arrayProjetos 	= $this->_request->getParam("projetos");

        // ==================== Dados do arquivo de upload ===============================
        $arquivoNome 	= $_FILES['divulgacao']['name']; // nome
        $arquivoTemp 	= $_FILES['divulgacao']['tmp_name']; // nome tempor�rio
        $arquivoTipo 	= $_FILES['divulgacao']['type']; // tipo
        $arquivoTamanho = $_FILES['divulgacao']['size']; // tamanho

        $arquivoExtensao 	= Upload::getExtensao($arquivoNome); // extens�o
        $arquivoBinario 	= Upload::setBinario($arquivoTemp); // bin�rio
        $arquivoHash 		= Upload::setHash($arquivoTemp); // hash


        //================= VALIDA O RESPONSAVEL E PROPONENTE ================
        if ($responsavel == 0) {
            $responsavel = $this->idResponsavel;
        }

        if ($proponente == 0) {
            $proponente = $this->idAgente;
        }

        //========= BUSCA O IDVINCULO COM AS INFORMA��ES PASSADAS =============
        $whereVinculo['idUsuarioResponsavel = ?'] = $responsavel;
        $whereVinculo['idAgenteProponente = ?']   = $proponente;
        $buscarVinculo = $tbVinculoDAO->buscar($whereVinculo);

        try {

            // ==================== Insere na Tabela tbArquivo ===============================
            $dadosArquivo = array(
                                   'nmArquivo' 			=> $arquivoNome,
                                   'sgExtensao' 		=> $arquivoExtensao,
                                   'dsTipoPadronizado' 	=> $arquivoTipo,
                                   'nrTamanho' 			=> $arquivoTamanho,
                                   'dtEnvio' 			=> new Zend_Db_Expr('GETDATE()'),
                                   'dsHash' 			=> $arquivoHash,
                                   'stAtivo' 			=> 'A'
            );

            $idArquivo = $tbArquivoDAO->inserir($dadosArquivo);

            // ==================== Insere na Tabela tbArquivoImagem ===============================
            $dadosBinario = array(
                                  'idArquivo' => $idArquivo,
                                  'biArquivo' => new Zend_Db_Expr("CONVERT(varbinary(MAX), {$arquivoBinario})")
            );

            $idArquivo = $tbArquivoImagemDAO->inserir($dadosBinario);

            // ==================== Insere na Tabela tbDocumento ===============================
            $dados = array(
                           'idTipoDocumento' 		=> 17,
                           'idArquivo' 				=> $idArquivo,
                           'dsDocumento' 			=> $_POST['dsObservacao'],
                           'dtEmissaoDocumento' 	=> null,
                           'dtValidadeDocumento' 	=> null,
                           'idTipoEventoOrigem' 	=> null,
                           'nmTitulo' 				=> 'Procuracao'
            );

            $idDocumento = $tbDocumentoDAO->inserir($dados);
            $idDocumento = $idDocumento['idDocumento'];


            //======== MONTA UM ARRAY COM AS INFORMA��ES DO VINCULO PROPOSTA========
            for ($i = 0; $i < sizeof($arrayProjetos); $i++) {
                $arrayVinculoProposta = array('idVinculo' 	  	  => $buscarVinculo[0]->idVinculo,
                                                  'idPreProjeto' 	  => $arrayProjetos[$i],
                                                  'siVinculoProposta' => 0
                    );

                // Salva as informa��es retornando o idVinculo Proposta
                $idVinculoProposta = $tbVinculoPropostaDAO->inserir($arrayVinculoProposta);

                // ==================== Insere na Tabela Procuracao ===============================
                $dadosVinculoProjeto = array(
                                                 'idVinculoProposta' 	=> $idVinculoProposta,
                                                 'idDocumento' 			=> $idDocumento,
                                                 'siProcuracao' 		=> 0,
                                                 'dsObservacao' 		=> $dsObservacao,
                                                 'dsJustificativa'      => ''
                    );

                $inserirproposta = $ProcuracaoDAO->inserir($dadosVinculoProjeto);
            }


            // ======== CADASTRA A VIS�O DE PROCURADOR PARA O RESPONS�VEL CASO A MESMA N�O EXISTA ========
            $buscarDadosResponsavel = $Sgcacesso->buscar(array('IdUSuario = ?' => $responsavel))->current(); // busca os dados do respons�vel
            $buscarDadosAgente      = $Agentes->buscar(array('CNPJCPF = ?' => $buscarDadosResponsavel['Cpf']))->current(); // verifica se o respons�vel � um agente

            if ($buscarDadosAgente) :
                // verifica se tem vis�o de procurador
                $buscarVisao = $Visao->buscar(array('idAgente = ?' => $buscarDadosAgente['idAgente'], 'Visao = ?' => 247))->current();
            if (!$buscarVisao) :
                    $dadosVisao = array(
                        'idAgente' => $buscarDadosAgente['idAgente']
                        ,'Visao'   => 247
                        ,'Usuario' => empty($this->idUsuario) ? $this->idAgente : $this->idUsuario
                        ,'stAtivo' => 'A'
                    );
            $Visao->inserir($dadosVisao); // cadastra a vis�o de procurador
                endif; else : // cadastra como agente

                $dadosNovoAgente = array(
                    'CNPJCPF'     => $buscarDadosResponsavel['Cpf']
                    ,'TipoPessoa' => 0
                    ,'Status'     => 0
                    ,'Usuario'    => empty($this->idUsuario) ? $this->idAgente : $this->idUsuario
                );
            $Agentes->inserir($dadosNovoAgente); // cadastra o agente

            $idAgenteNovo = $Agentes->BuscaAgente($buscarDadosResponsavel['Cpf']);
            $idAgenteNovo = $idAgenteNovo[0]->idAgente; // pega o id do agente cadastrado

            $dadosNome = array(
                    'idAgente'   => $idAgenteNovo
                    ,'TipoNome'  => 18
                    ,'Descricao' => $buscarDadosResponsavel['Nome']
                    ,'Status'    => 0
                    ,'Usuario'   => empty($this->idUsuario) ? $this->idAgente : $this->idUsuario
                );
            $Nomes->inserir($dadosNome); // cadastra o nome do agente

            $dadosVisao = array(
                    'idAgente' => $idAgenteNovo
                    ,'Visao'   => 247
                    ,'Usuario' => empty($this->idUsuario) ? $this->idAgente : $this->idUsuario
                    ,'stAtivo' => 'A'
                );
            $Visao->inserir($dadosVisao); // cadastra a vis�o de procurador

            $dadosInternet = array(
                    'idAgente'      => $idAgenteNovo
                    ,'TipoInternet' => 28 // particular
                    ,'Descricao'    => $buscarDadosResponsavel['Email']
                    ,'Status'       => 1 // sim para correspond�ncia
                    ,'Divulgar'     => 1 // sim para divulgar
                    ,'Usuario'      => empty($this->idUsuario) ? $this->idAgente : $this->idUsuario
                );
            $Internet->inserir($dadosInternet); // cadastra o email do procurador
            endif;


            parent::message("Procura&ccedil;&atilde;o cadastrada com sucesso!", "procuracao/cadastramento", "CONFIRM");
        } catch (Zend_Exception $e) {
            parent::message("Error".$e->getMessage(), "procuracao/cadastramento", "ERROR");
            //	            parent::message("&Eacute; necess&aacute;rio um v&iacute;nculo para enviar o cadastramento da procura&ccedil;&atilde;o", "procuracao/index?idPreProjeto=" . $idpreprojeto, "ERROR");
        }

        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function analisarAction()
    {
        $tbProcuracao = new tbProcuracao();
        $resultado = $tbProcuracao->listarProcuracoesPendentes();
        $this->view->procuracoes = $resultado;

//        if (isset($_POST['siProcuracao']) ) {
//            $p = new Procuracao();
//            $pronac = $_POST['Pronac'];
//
//            $dados = array('siProcuracao'=>$_POST['siProcuracao'],'dsJustificativa' => $_POST['dsJustificativa']);
//            $where = array('idProcuracao = ?' => $_POST['idProcuracao']);
//            $idProcuracao = $p->alterar($dados, $where);
//
//            if(!empty($idProcuracao)){
//                $aceito  = $_POST['siProcuracao'] == 1 ? 'aprovada' : 'rejeitada';
//                    parent::message("Procura&ccedil;&atilde;o {$aceito} com sucesso para o Pronac: $pronac", "procuracao/verificarprocuracao", "CONFIRM");
//            }
//        }
//        $o = new Orgaos();
//        $buscarorgaos = $o->pesquisarTodosOrgaos();
//        $this->view->orgaos = $buscarorgaos;
    }

    public function verificarprocuracaoAction()
    {
        $p = new Procuracao();
        $where = array();

        if (isset($_POST)) {
            if (!empty($_POST['nprojeto'])) {
                $where['(vprp.idPreProjeto = ? or pr.AnoProjeto+pr.Sequencial = ?)'] = $_POST['nprojeto'];
            }
            if (!empty($_POST['nomeprojeto'])) {
                $where['pr.NomeProjeto = ?'] = $_POST['nomeprojeto'];
            }
            if (!empty($_POST['cpfcnpjproponente'])) {
                $where['pr.CgcCpf = ?'] = Mascara::delMaskCPFCNPJ($_POST['cpfcnpjproponente']);
            }
            if (!empty($_POST['nomeresponsavel'])) {
                $where['nmr.Descricao like "?"'] = "%" . $_POST['nomeresponsavel'] . "%";
            }
            if (!empty($_POST['dataenvio'])) {
                $where['a.dtEnvio = ?'] = $_POST['dataenvio'];
            }
            if (($_POST['situacao'] != "") && isset($_POST['situacao'])) {
                $where['p.siProcuracao = ?'] = $_POST['situacao'];
            }

            $where['org.idSecretaria = ?'] = $this->orgaoSuperior;

            $buscarprocuracao = $p->buscarProcuracaoProjeto($where);
            $this->view->procuracao = $buscarprocuracao;
        }
    }

    public function avaliarAction()
    {
        $procuracaoDAO = new Procuracao();
        $idDocumento = $this->_request->getParam("idDocumento");
        $where['p.idDocumento = ?'] 	= $idDocumento;
        $where['org.idSecretaria = ?']  = $this->orgaoSuperior;
        $buscar = $procuracaoDAO->buscarProcuracaoProjeto($where);
        $this->view->procuracao = $buscar;
    }

    public function visualizarAction()
    {
        $projetos = new Projetos();
        $idDocumento = $this->_request->getParam("idDocumento");
        $buscar = $projetos->visualizarProcuracoes($idDocumento);
        $this->view->procuracao = $buscar;
    }

    public function aprovacaoAction()
    {
        $vinculoPropostaDAO     = new Agente_Model_DbTable_TbVinculoProposta();
        $tbvinculoDAO 		= new Agente_Model_DbTable_TbVinculo();
        $procuracaoDAO 		= new Procuracao();
        $preProjetoDAO 		= new Proposta_Model_DbTable_PreProjeto();

        $responsavel   		= $this->_request->getParam("responsavel");
        $idDocumento   		= $this->_request->getParam("idDocumento");
        $justificativa 		= $this->_request->getParam("justificativa");
        $situacao 	   	= $this->_request->getParam("situacao");
        $idProcuracao  		= $this->_request->getParam("idProcuracao");
        $idVinculoProposta      = $this->_request->getParam("idVinculoProposta");

        $situacaoVI = 0;
        $situacaoPR = 0;
        $situacaoMSG = "";

        if ($situacao == 0) {
            $situacaoVI = 1;
            $situacaoPR = 2;
            $situacaoMSG = "Rejeitada";
        } else {
            $situacaoVI = 2;
            $situacaoPR = 1;
            $situacaoMSG = "Aprovada";
        }

        try {
            for ($i = 0; $i < sizeof($idProcuracao); $i++) {
                $dadosPR = array('siProcuracao' => $situacaoPR, 'dsJustificativa' => $justificativa);
                $wherePR['idProcuracao = ?'] = $idProcuracao[$i];
                $alteraPR = $procuracaoDAO->alterar($dadosPR, $wherePR);

                $dadosVI = array('siVinculoProposta' => $situacaoVI);
                $whereVP['idVinculoProposta = ?'] = $idVinculoProposta[$i];
                $alteraVP = $vinculoPropostaDAO->alterar($dadosVI, $whereVP);

                //$wherePROP['idVinculoProposta = ?'] = $idVinculoProposta[$i];
                //$buscarProjeto = $vinculoPropostaDAO->buscar($wherePROP);
                $alteraPRO = $preProjetoDAO->alteraresponsavel($idVinculoProposta[$i], $responsavel) ;
            }
            parent::message("Procura&ccedil;&atilde;o ".$situacaoMSG." com sucesso!", "procuracao/analisar", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Error".$e->getMessage(), "procuracao/avaliar/idDocumento/".$idDocumento, "ERROR");
        }

        $where['p.idDocumento = ?'] = $idDocumento;
        $buscar = $procuracaoDAO->buscarProcuracaoProjeto($where);
        $this->view->procuracao = $buscar;
    }

    public function buscarProjetosProcuracaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $cpf = Mascara::delMaskCPFCNPJ($_POST['cpf']);

        $dados = array();
        if (!empty($cpf)) {
            $dados['p.CgcCpf = ?'] = $cpf;
        }

        $projetos = new Projetos();
        $result = $projetos->buscarProjProcuracao($dados);

        $a = 0;
        if (count($result) > 0) {
            foreach ($result as $registro) {
                $dadosAgente[$a]['Pronac'] = $registro['Pronac'];
                $dadosAgente[$a]['CgcCpf'] = strlen($cpf) == 11 ? Mascara::addMaskCPF($registro['CgcCpf']) : Mascara::addMaskCNPJ($registro['CgcCpf']);
                $dadosAgente[$a]['NomeProjeto'] = utf8_encode($registro['NomeProjeto']);
                $dadosAgente[$a]['idPronac'] = $registro['IdPRONAC'];
                $dadosAgente[$a]['idAgente'] = $registro['idAgente'];
                $dadosAgente[$a]['nmAgente'] = utf8_encode($registro['nmAgente']);
                $a++;
            }
            $jsonEncode = json_encode($dadosAgente);
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosAgente));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function buscarProcuradorAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $cpf = Mascara::delMaskCPF($_POST['cpf']);

        $dados = array();
        $dados['a.CNPJCPF = ?'] = $cpf;

        $agentes = new Agente_Model_DbTable_Agentes();
        $result = $agentes->buscarAgenteENome($dados);

        $a = 0;
        if (count($result) > 0) {
            foreach ($result as $registro) {
                $dadosAgente[$a]['idAgente'] = $registro['idAgente'];
                $dadosAgente[$a]['CNPJCPF'] = $registro['CNPJCPF'];
                $dadosAgente[$a]['nmAgente'] = utf8_encode($registro['Descricao']);
                $a++;
            }
            $jsonEncode = json_encode($dadosAgente);
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosAgente));
        } else {
            $this->_helper->json(array('resposta'=>false,'CNPJCPF'=>$cpf));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function cadastrarProcuracaoAction()
    {
        $auth = Zend_Auth::getInstance();
        $idUsuarioAtivo = $auth->getIdentity()->IdUsuario;

        // ==================== Dados do arquivo de upload ===============================
        $arquivoNome 	= $_FILES['arquivoProcuracao']['name']; // nome
        $arquivoTemp 	= $_FILES['arquivoProcuracao']['tmp_name']; // nome temporario
        $arquivoTipo 	= $_FILES['arquivoProcuracao']['type']; // tipo
        $arquivoTamanho = $_FILES['arquivoProcuracao']['size']; // tamanho

        $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
        $arquivoBinario  = Upload::setBinario($arquivoTemp); // binario
        $arquivoHash     = Upload::setHash($arquivoTemp); // hash

        if (!isset($_FILES['arquivoProcuracao'])) {
            parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "procuracao/cadastramento", "ERROR");
        }

        $tipos = array("pdf");
        if (!in_array(strtolower($arquivoExtensao), $tipos)) {
            parent::message("Favor selecionar o arquivo da Procura&ccedil;&atilde;o em formato PDF!", "procuracao/cadastramento", "ERROR");
        }

//
//        if ($arquivoExtensao > 1024) {
//            parent::message("Favor selecionar o arquivo da Procura&ccedil;&atilde;o em formato PDF!", "procuracao/cadastramento", "ERROR");
//        }

        if (!isset($_POST['idPronac'])) {
            parent::message("Nenhum projeto foi selecionado!", "procuracao/cadastramento", "ERROR");
        }

        try {
            $tbArquivoDAO       = new tbArquivo();
            $tbArquivoImagemDAO = new tbArquivoImagem();
            $tbDocumentoDAO     = new tbDocumento();
            $tbProcuracao       = new tbProcuracao();
            $tbProcuradorProjeto= new tbProcuradorProjeto();

            // ==================== Insere na Tabela tbArquivo ===============================
            $dadosArquivo = array(
                'nmArquivo'         => $arquivoNome,
                'sgExtensao'        => $arquivoExtensao,
                'dsTipoPadronizado' => $arquivoTipo,
                'nrTamanho'         => $arquivoTamanho,
                'dtEnvio'           => new Zend_Db_Expr('GETDATE()'),
                'dsHash'            => $arquivoHash,
                'stAtivo'           => 'A'
            );
            $idArquivo = $tbArquivoDAO->inserir($dadosArquivo);

            // ==================== Insere na Tabela tbArquivoImagem ===============================
            $dadosBinario = array(
                'idArquivo' => $idArquivo,
                'biArquivo' => new Zend_Db_Expr("CONVERT(varbinary(MAX), {$arquivoBinario})")
            );
            $idArquivo = $tbArquivoImagemDAO->inserir($dadosBinario);

            // ==================== Insere na Tabela tbDocumento ===============================
            $dados = array(
               'idTipoDocumento'     => 17,
               'idArquivo'           => $idArquivo,
               'dsDocumento'         => null,
               'dtEmissaoDocumento'  => null,
               'dtValidadeDocumento' => null,
               'idTipoEventoOrigem'  => null,
               'nmTitulo'            => 'Procuracao'
            );
            $idDocumento = $tbDocumentoDAO->inserir($dados);
            $idDocumento = $idDocumento['idDocumento'];

            //Cadastra a procuracao
            $dadosProcuracao = array(
                'idAgente' => $_POST['idAgenteProcurador'],
                'idDocumento' => $idDocumento,
                'dtProcuracao' => new Zend_Db_Expr('GETDATE()'),
                'siProcuracao' => 0,
                'dsJustificativa' => $_POST['dsJustificativa'],
                'idSolicitante' => $idUsuarioAtivo
            );
            $idProcuracao = $tbProcuracao->inserir($dadosProcuracao);

            //Cadastra os projetos relacionados a procuracao cadastrada
            foreach ($_POST['idPronac'] as $idPronac) {
                $dadosProcuradorProjeto = array(
                    'idProcuracao' => $idProcuracao,
                    'idPronac' => $idPronac,
                    'siEstado' => 0
                );
                $tbProcuradorProjeto->inserir($dadosProcuradorProjeto);
            }
            parent::message("Procura&ccedil;&atilde;o cadastrada com sucesso!", "procuracao/index", "CONFIRM");
        } catch (Zend_Exception $e) {
            parent::message("Error".$e->getMessage(), "procuracao/index", "ERROR");
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function desvincluarProjetoProcuracaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idProcProj = $_POST['idProcProj'];
        $idProc = $_POST['idProc'];

        $dados = array(
            'dtDesvinculacao' => new Zend_Db_Expr('GETDATE()'),
            'siEstado' => 3
        );
        $where = array('idProcuradorProjeto = ?' => $idProcProj);

        $tbProcuradorProjeto = new tbProcuradorProjeto();
        $result = $tbProcuradorProjeto->update($dados, $where);

        $desvincularProcuracao = false;
        $todosProjetos = $tbProcuradorProjeto->buscar(array('idProcuracao = ?' => $idProc));
        if (count($todosProjetos) > 0) {
            $desvincularProcuracao = true;
            foreach ($todosProjetos as $proj) {
                //Se algum projeto nao estiver desvinculado, nao rejeita a Procuracao
                if ($proj->siEstado != 3) {
                    $desvincularProcuracao = false;
                }
            }
            //Se todos os projetos foram desvinculados, rejeita a Procuracao tambem
            if ($desvincularProcuracao) {
                $dados = array('siProcuracao' => 2);
                $where = array('idProcuracao = ?' => $idProc);
                $tbProcuracao = new tbProcuracao();
                $tbProcuracao->update($dados, $where);
            }
        }

        if ($result) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function avaliarProcuracaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idProcuracao = $_POST['id'];

        /*** siProcuracao = Situacao da procuracao, informando qual fase de homologacao que esta se encontra, com as opcoes a seguir:
        0-Pendente de Validacao; 1-Aceita; 2-Rejeitada.
        ***/
        $dadostbProcuracao = array(
            'siProcuracao' => $_POST['siProcuracao']
        );
        if ($_POST['siProcuracao'] == 2) {
            $dadostbProcuracao['dsObservacao'] = utf8_decode($_POST['justificativa']);
        }
        $tbProcuracao = new tbProcuracao();
        $result1 = $tbProcuracao->update($dadostbProcuracao, array("idProcuracao = ?" => $idProcuracao));


        if ($_POST['siProcuracao'] == 1) {
            $dadostbProcuradorProjeto = array(
                'siEstado' => 2, //Vinculado
                'dtVinculacao' => new Zend_Db_Expr('GETDATE()') //Vinculado
            );
        } else {
            $dadostbProcuradorProjeto = array(
                'siEstado' => 1 //Rejeitar V�nculo
            );
        }
        $tbProcuradorProjeto = new tbProcuradorProjeto();
        $result2 = $tbProcuradorProjeto->update($dadostbProcuradorProjeto, array("idProcuracao = ?" => $idProcuracao));

        if ($result1 && $result2) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function listarProjetosProcuracaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idProcuracao = $_POST['id'];

        $projetos = new Projetos();
        $result = $projetos->detalharProjetosProcuracao($idProcuracao);

        $i = 0;
        if (count($result) > 0) {
            foreach ($result as $registro) {
                $dadosProjeto[$i]['Pronac'] = $registro['Pronac'];
                $dadosProjeto[$i]['NomeProjeto'] = utf8_encode($registro['NomeProjeto']);
                $i++;
            }
            $jsonEncode = json_encode($dadosProjeto);
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosProjeto));
        } else {
            $this->_helper->json(array('resposta'=>false,'CNPJCPF'=>$cpf));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }


    public function verificarDirigenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $cpfPropoenente = Mascara::delMaskCPFCNPJ($_POST['cpfProponente']);
        $cpfProcurador = Mascara::delMaskCPFCNPJ($_POST['cpfProcurador']);

        $Vinculacao = new Agente_Model_DbTable_Vinculacao();
        $result = $Vinculacao->verificarDirigente($cpfPropoenente, $cpfProcurador);

        if (count($result) > 0) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
