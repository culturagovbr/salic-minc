<?php

class ManterreadequacaoController extends MinC_Controller_Action_Abstract
{
    private $getIdUsuario = 0;
    private $getIdOrgao = 0;
    private $intTamPag = 10;

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        $PermissoesGrupo[] = 128; // Tecnico de Portaria
        parent::perfil(1, $PermissoesGrupo);

        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        //SE CAIU A SECAO REDIRECIONA
        if (!$auth->hasIdentity()) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl();
            JS::redirecionarURL($url);
        }

        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $this->view->agente = $agente['idAgente'];
        $this->getIdUsuario = $agente['idAgente'];
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->getIdOrgao = $GrupoAtivo->codOrgao;
        $this->codGrupo = $GrupoAtivo->codGrupo;
        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()

    public function indexAction()
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
            $order = array(2); //Dt. Solicitacao
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
        $where['a.stPedidoAlteracao = ?'] = 'I';
        $where['a.siVerificacao in (?)'] = array(0,1);
        $where['e.tpAlteracaoProjeto = ?'] = 1; //Nome do Proponente

        if ($this->getIdOrgao == 166) {
            $where['b.Area = ?'] = 2;  // quando for SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
        } elseif ($this->getIdOrgao == 272) {
            $where['b.Area <> ?'] = 2; // quando for SEFIC/GEAR/SACAV pega somente os projetos das �reas que n�o sejam de Audiovisual
        } else {
            $where['b.Area = ?'] = 0;  // quando for diferente de SAV/CGAV/CAP e SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
        }

        $stCombo = 'A'; //Aguardando An�lise
        if (isset($_GET['tipoFiltro']) && !empty($_GET['tipoFiltro'])) {
            $comboView = explode(':', $_GET['tipoFiltro']);
            $this->view->filtro = $_GET['tipoFiltro'];
            $where['e.tpAlteracaoProjeto = ?'] = $comboView[0];
            if ($comboView[1] == 'd') {
                $stCombo = 'D'; //Devolvidos Ap�s An�lise
            }
        }

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['b.AnoProjeto+b.Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        $tbPedidoAlteracaoProjeto = new tbPedidoAlteracaoProjeto();
        if ($stCombo == 'A') {
            $where['e.stVerificacao = 0 or e.stVerificacao is null'] = '';

            $total = $tbPedidoAlteracaoProjeto->painelCoordAcomp($where, $order, null, null, true);
            $fim = $inicio + $this->intTamPag;
            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            $busca = $tbPedidoAlteracaoProjeto->painelCoordAcomp($where, $order, $tamanho, $inicio);
        } else {
            $where['e.stVerificacao in (?)'] = array(2,3);
            $where['f.stAvaliacaoItemPedidoAlteracao in (?)'] = array('AP','IN');
            $where['g.stAtivo = ?'] = 0;
            $where['g.idTipoAgente = ?'] = 3;

            $total = $tbPedidoAlteracaoProjeto->painelCoordAcompDev($where, $order, null, null, true);
            $fim = $inicio + $this->intTamPag;
            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            $busca = $tbPedidoAlteracaoProjeto->painelCoordAcompDev($where, $order, $tamanho, $inicio);
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

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Chama o SQL da lista de Entidades Vinculadas - T�cnico
        $sqllistasDeEntidadesVinculadas = ReadequacaoProjetos::retornaSQLlista("listasDeEntidadesVinculadas", $this->getIdOrgao);
        $listaEntidades = $db->fetchAll($sqllistasDeEntidadesVinculadas);

        // Chama o SQL da lista de Entidades Vinculadas - Parecerista
        $sqllistasDeEntidadesVinculadasPar = ReadequacaoProjetos::retornaSQLlista("listasDeEntidadesVinculadasPar", "NULL");
        $listaEntidadesPar = $db->fetchAll($sqllistasDeEntidadesVinculadasPar);

        $this->view->paginacao     = $paginacao;
        $this->view->qtdDocumentos = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
        $this->view->listaEntidades = $listaEntidades;
        $this->view->listaEntidadesPar = $listaEntidadesPar;
        $this->view->statusCombo = $stCombo;
    }

    public function encaminharPainelCoordAcompAction()
    {
        //retorna o id do agente logado
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idAgenteEncaminhar = $agente['idAgente'];

        $destinatario = explode(':', $_POST['destinatario']);
        $idAgenteReceber = $destinatario[0];
        $AgentePerfil = $destinatario[1];
        $Orgao = $_POST['entidade'];
        $ID_PRONAC = $_POST['idPronacModal'];
        $idPedidoAlteracao = $_POST['idPedidoAlteracaoModal'];
        $tpAlteracaoProjeto = $_POST['tpAlteracaoProjetoModal'];
        $justificativa = $_POST['dsJustificativa'];
        $tipoFiltro = $_POST['tipoFiltro'];

        $idAgenteRemetente = $this->getIdUsuario;
        $idPerfilRemetente = $this->codGrupo;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            //ALTERA O STATUS DE '0' PARA '1' NA TABELA tbPedidoAlteracaoProjeto
            $sqlAlteraVariavelAltProj = ReadequacaoProjetos::retornaSQLencaminhar("sqlAlteraVariavelAltProj", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $db->fetchAll($sqlAlteraVariavelAltProj);

            //ALTERA O STATUS DE '0' PARA '1' NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlAlteraVariavelTipoAlt = ReadequacaoProjetos::retornaSQLencaminhar("sqlAlteraVariavelTipoAlt", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $db->fetchAll($sqlAlteraVariavelTipoAlt);
            if ($tpAlteracaoProjeto == 7) {
                $sqlAlteraVariavelTipoAlt = ReadequacaoProjetos::retornaSQLencaminhar("sqlAlteraVariavelTipoAlt", $ID_PRONAC, $idPedidoAlteracao, 10, $justificativa, $Orgao, $idAgenteReceber);
                $db->fetchAll($sqlAlteraVariavelTipoAlt);
            }

            // INSERE OS VALORES NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlEncaminhar = ReadequacaoProjetos::retornaSQLencaminhar("sqlCoordAcompEncaminhar", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $db->fetchAll($sqlEncaminhar);

            //RETORNA EM VARI�VEIS OS DADOS DO LOG ANTERIOR PARA INSERIR NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlproposta = ReadequacaoProjetos::retornaSQLencaminhar("sqlRecuperarRegistro", $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber);
            $dados = $db->fetchAll($sqlproposta);
            $idAvaliacaoItemPedidoAlteracao = $dados[0]->idAvaliacaoItemPedidoAlteracao;


            //122 = Coord Acompanhamento
            //93 = Coord Parecerista
            //94 = Parecerista
            //129 = Tecnico

            if ($AgentePerfil == 122) {
                $tipoAg = '3';
            } elseif ($AgentePerfil == 93) {
                $tipoAg = '2';
            } elseif ($AgentePerfil == 94) {
                $tipoAg = '1';
            } elseif ($AgentePerfil == 121 or $AgentePerfil == 129) {
                $tipoAg = '5';
            }

            // INSERE OS VALORES NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlEncaminhar2 = ReadequacaoProjetos::retornaSQLtbAcao($idAvaliacaoItemPedidoAlteracao, $justificativa, $tipoAg, $Orgao, $idAgenteReceber, $idAgenteRemetente, $idPerfilRemetente);
            $db->fetchAll($sqlEncaminhar2);

            $db->commit();
            parent::message("Projeto encaminhado com sucesso!", "manterreadequacao?tipoFiltro=$tipoFiltro", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao encaminhar Projeto", "manterreadequacao?tipoFiltro=$tipoFiltro", "ERROR");
        }
    }

    public function reencaminharPainelCoordAcompAction()
    {
        $idAcaoAtual = $_POST['idAcao'];
        $idPedidoAlteracao = $_POST['idPedidoAlteracaoModal'];
        $tpAlteracaoProjeto = $_POST['tpAlteracaoProjetoModal'];
        $justificativa = $_POST['dsJustificativa'];
        $Orgao = $_POST['entidade'];
        $destinatario = explode(':', $_POST['destinatario']);
        $idAgente = $destinatario[0];
        $AgentePerfil = $destinatario[1];
        $tipoFiltro = $_POST['tipoFiltro'];

        if ($AgentePerfil == '121' || $AgentePerfil == '129') {
            $idPerfil = 5;
        } else {
            $idPerfil = 2;
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            //ALTERA OS DADOS DO REGISTRO NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlAlteraVar = ReadequacaoProjetos::retornaSQLReencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto);
            $db->fetchAll($sqlAlteraVar);

            //INSERE UM NOVO REGISTRO NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel = ReadequacaoProjetos::reencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto);
            $db->fetchAll($sqlAlteraVariavel);

            //ATUALIZA O CAMPO stAtivo ATUAL NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel1 = ReadequacaoProjetos::reencaminharPar1($idAcaoAtual);
            $db->fetchAll($sqlAlteraVariavel1);

            //RETORNA O idAvaliacaoItemPedidoAlteracao DO REGISTRO GERADO NA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlAlteraVariavel2 = ReadequacaoProjetos::reencaminharPar2($idPedidoAlteracao, $tpAlteracaoProjeto);
            $dados = $db->fetchAll($sqlAlteraVariavel2);
            $idAcao = $dados[0]->idAvaliacaoItemPedidoAlteracao;

            //INSERE NOVO REGISTRO
            $sqlAlteraVariavel3 = ReadequacaoProjetos::reencaminharPar5($idAcao, $this->getIdUsuario, $justificativa, $Orgao, $idPerfil, $idAgente, $AgentePerfil);
            $db->fetchAll($sqlAlteraVariavel3);

            $db->commit();
            parent::message("Projeto reencaminhado com sucesso!", "manterreadequacao?tipoFiltro=$tipoFiltro", "CONFIRM");
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro ao reencaminhar Projeto!", "manterreadequacao?tipoFiltro=$tipoFiltro", "ERROR");
        }
    }

    public function verificarExistenciaItensDeCustoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $where = array(
            'IdPRONAC = ?' => $_POST['idPronac'],
            'idPedidoAlteracao = ?' => $_POST['idPedidoAlteracao'],
            'tpPlanilha = ?' => 'SR',
            'tpAcao != ?' => 'N',
            'stAtivo = ?' => 'N'
        );
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $result = $tbPlanilhaAprovacao->buscar($where);

        if (count($result) > 0) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function comboEncaminhamentoTecnicoAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $idorgao = $_POST['idorgao'];
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $AgentesOrgao = ReadequacaoProjetos::dadosAgentesOrgaoA($idorgao);
        $AgentesOrgao = $db->fetchAll($AgentesOrgao);
        $a = 0;
        if (is_array($AgentesOrgao) and count($AgentesOrgao)>0) {
            foreach ($AgentesOrgao as $agentes) {
                $dadosAgente[$a]['usu_codigo'] = $agentes->usu_codigo;
                $dadosAgente[$a]['usu_nome'] = utf8_encode($agentes->usu_nome);
                $dadosAgente[$a]['Perfil'] = utf8_encode($agentes->Perfil);
                $dadosAgente[$a]['idperfil'] = $agentes->idVerificacao;
                $dadosAgente[$a]['idAgente'] = utf8_encode($agentes->idAgente);
                $a++;
            }
            $jsonEncode = json_encode($dadosAgente);

            //echo $jsonEncode;
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosAgente));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function comboEncaminhamentoPareceristaAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $idorgao = $_POST['idorgao'];
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $AgentesOrgao = ReadequacaoProjetos::dadosAgentesOrgaoB($idorgao);
        $AgentesOrgao = $db->fetchAll($AgentesOrgao);
        $a = 0;
        if (is_array($AgentesOrgao) and count($AgentesOrgao)>0) {
            foreach ($AgentesOrgao as $agentes) {
                $dadosAgente[$a]['usu_codigo'] = $agentes->usu_codigo;
                $dadosAgente[$a]['usu_nome'] = utf8_encode($agentes->usu_nome);
                $dadosAgente[$a]['Perfil'] = utf8_encode($agentes->Perfil);
                $dadosAgente[$a]['idperfil'] = $agentes->idVerificacao;
                $dadosAgente[$a]['idAgente'] = utf8_encode($agentes->idAgente);
                $a++;
            }
            $jsonEncode = json_encode($dadosAgente);

            //echo $jsonEncode;
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosAgente));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function historicoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $idavaliacao = $_POST['idavaliacao'];
        $ListaRegistros =  ReadequacaoProjetos::retornaSQLHistoricoLista($idavaliacao);
        $this->view->ListaRegistros = $db->fetchAll($ListaRegistros);
    }

    public function finalizageralAction()
    {
        $tbAbrangencia = new Proposta_Model_DbTable_Abrangencia();

        $idAcao = $_GET['id'];
        $tipoFiltro = $_GET['tipoFiltro'].':d'; // d = DEVOLVIDOS APOS ANALISE

        //retorna o id do agente logado
        $idAgenteRemetente = $this->getIdUsuario;
        $idPerfilRemetente = $this->codGrupo;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();
            //ATUALIZA OS CAMPOS stAtivo e stVerificacao NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfin = ReadequacaoProjetos::retornaSQLfinalizaGeral($idAcao);
            $dados = $db->fetchAll($sqlfin);

            //BUSCA OS REGISTROS DA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            $sqlfin2 = ReadequacaoProjetos::retornaSQLfinalizaGeral2($idAcao);
            $dados = $db->fetchAll($sqlfin2);
            $id = $dados[0]->idAvaliacaoItemPedidoAlteracao;
            $idOrgao = $dados[0]->idOrgao;

            //BUSCA OS REGISTROS DOS CAMPOS idPedidoAlteracao E tpAlteracaoProjeto DA TABELA tbAvaliacaoItemPedidoAlteracao
            $sqlfin3 = ReadequacaoProjetos::retornaSQLfinalizaGeral3($id);
            $dados = $db->fetchAll($sqlfin3);
            $idPedidoAlt = $dados[0]->idPedidoAlteracao;
            $tpAlt = $dados[0]->tpAlteracaoProjeto;
            $stAvaliacaoItem = $dados[0]->stAvaliacaoItemPedidoAlteracao;

            //ATUALIZA O CAMPO stVerificacao NA TABELA tbPedidoAlteracaoXTipoAlteracao
            $sqlfin4 = ReadequacaoProjetos::retornaSQLfinalizaGeral4($idPedidoAlt, $tpAlt);
            $dados = $db->fetchAll($sqlfin4);

            //CRIAR NOVO REGISTRO DE ENCAMINHAMENTO NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
            if (!isset($_GET['checklist'])) {
                $sqlfin5 = ReadequacaoProjetos::retornaSQLfinalizaGeral5($id, $idOrgao, $idAgenteRemetente, $idPerfilRemetente);
                $dados = $db->fetchAll($sqlfin5);
            }

            //BUSCA O IDPRONAC DA TABELA tbPedidoAlteracaoProjeto
            $sqlfin6 = ReadequacaoProjetos::retornaSQLfinalizaGeral6($idPedidoAlt);
            $dados = $db->fetchAll($sqlfin6);
            $idPronac = $dados[0]->IdPRONAC;

            //Verifica se possui item de custo NA TABELA tbPedidoAlteracaoXTipoAlteracao
            if ($tpAlt == 7) {
                $sqlfin7 = ReadequacaoProjetos::retornaSQLfinalizaGeral7($idPedidoAlt);
                $itens = $db->fetchAll($sqlfin7);
                if (count($itens) == 2) {
                    $tpAlt = 10;
                }
            }

            $auth = Zend_Auth::getInstance(); // pega a autentica��o
            $agente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
            $idagente = $agente['idAgente'];

            if ($stAvaliacaoItem == 'AP') {
                if ($tpAlt == 1 && isset($_GET['checklist'])) {
                    //NOME DO PROPONENTE
                    $NomeProponenteSolicitado = PedidoAlteracaoDAO::buscarAlteracaoNomeProponente($idPronac);

                    $proponente = new Interessado();
                    $dados = array(
                            'Nome' => mb_convert_case(strtolower($NomeProponenteSolicitado['proponente']), MB_CASE_TITLE, "ISO-8859-1")
                    );
                    $proponente->alterar($dados, array('CgcCpf = ?' => $NomeProponenteSolicitado['CgcCpf']));
                } elseif ($tpAlt == 2 && isset($_GET['checklist'])) {
                    //TROCA DE PROPONENTE
                    $trocaProponenteAtual = VerificarAlteracaoProjetoDAO::BuscarDadosGenericos($idPronac);
                    $NomeAtual = $trocaProponenteAtual['proponente'];
                    $CpfCnpjAtual = $trocaProponenteAtual['CgcCpf'];
                    $idNome = $trocaProponenteAtual['idNome'];
                    $trocaProponenteSolicitada = PedidoAlteracaoDAO::buscarAlteracaoRazaoSocial($idPronac);
                    $NomeSolicitado = $trocaProponenteSolicitada['nmRazaoSocial'];
                    $CpfCnpjSolicitado = $trocaProponenteSolicitada['CgcCpf'];

                    // altera o cpf do proponente
                    $_Projetos = new Projetos();
                    $_alterarProponente = $_Projetos->alterar(array('CgcCpf' => $CpfCnpjSolicitado), array('IdPRONAC = ?' => $idPronac));

                    // altera o nome do proponente
                    $_Nomes = new Nomes();
                    $_alterarNome = $_Nomes->alterar(array('Descricao' => $NomeSolicitado), array('idNome = ?' => $idNome));

                    $proponente = new Interessado();
                    $dados = array(
                            'Nome' => mb_convert_case(strtolower($NomeSolicitado), MB_CASE_TITLE, "ISO-8859-1")
                    );
                    $proponente->alterar($dados, array('CgcCpf = ?' => $CpfCnpjSolicitado));


                    /**
                     * ==============================================================
                     * INICIO DA ATUALIZACAO DO VINCULO DO PROPONENTE
                     * ==============================================================
                     */
                    $Projetos          = new Projetos();
                    $Agentes           = new Agente_Model_DbTable_Agentes();
                    $Visao             = new Visao();
                    $tbVinculo         = new Agente_Model_DbTable_TbVinculo();
                    $tbVinculoProposta = new tbVinculoProposta();

                    /* ========== BUSCA OS DADOS DO PROPONENTE ANTIGO ========== */
                    $buscarCpfProponenteAntigo = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac));
                    $cpfProponenteAntigo       = count($buscarCpfProponenteAntigo) > 0 ? $buscarCpfProponenteAntigo[0]->CgcCpf : 0;
                    $buscarIdProponenteAntigo  = $Agentes->buscar(array('CNPJCPF = ?' => $cpfProponenteAntigo));
                    $idProponenteAntigo        = count($buscarIdProponenteAntigo) > 0 ? $buscarIdProponenteAntigo[0]->idAgente : 0;
                    $idPreProjetoVinculo       = count($buscarCpfProponenteAntigo) > 0 ? $buscarCpfProponenteAntigo[0]->idProjeto : 0;

                    /* ========== BUSCA OS DADOS DO NOVO PROPONENTE ========== */
                    $buscarNovoProponente = $Agentes->buscar(array('CNPJCPF = ?' => $CpfCnpjSolicitado));
                    $idNovoProponente     = count($buscarNovoProponente) > 0 ? $buscarNovoProponente[0]->idAgente : 0;
                    $buscarVisao          = $Visao->buscar(array('Visao = ?' => 144, 'idAgente = ?' => $idNovoProponente));

                    /* ========== BUSCA OS DADOS DA PROPOSTA VINCULADA ========== */
                    $idVinculo = $tbVinculoProposta->buscar(array('idPreProjeto = ?' => $idPreProjetoVinculo));

                    /* ========== ATUALIZA O VINCULO DO PROPONENTE ========== */
                    if (count($buscarVisao) > 0 && count($idVinculo) > 0) :

                        $whereVinculo = array('idVinculo = ?' => $idVinculo[0]->idVinculo);

                    $dadosVinculo = array(
                                'idAgenteProponente' => $idNovoProponente
                                ,'dtVinculo'         => new Zend_Db_Expr('GETDATE()'));

                    $tbVinculo->alterar($dadosVinculo, $whereVinculo); else :
                        parent::message("O usu�rio informado n�o � Proponente ou o Projeto n�o est� vinculado a uma Proposta!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetocoordacompanhamento", "ERROR");
                    endif;

                    /**
                     * ==============================================================
                     * FIM DA ATUALIZACAO DO VINCULO DO PROPONENTE
                     * ==============================================================
                     */
                } elseif ($tpAlt == 3) {
                    //FICHA T�CNICA
                    $fichatecAtual = FichaTecnicaDAO::buscarFichaTecnicaFinal($idPronac, $idPedidoAlt);
                    $Atual = $fichatecAtual[0]->FichaTecnica;
                    $idPreProjeto = $fichatecAtual[0]->idPreProjeto;

                    $fichatecSolicitada = PedidoAlteracaoDAO::buscarAlteracaoFichaTecnicaFinal($idPronac, $idPedidoAlt);
                    $Solicitada = $fichatecSolicitada[0]['dsFichaTecnica'];

                    $avaliacao = ReadequacaoProjetos::finalizacaoCoordAcomp("SAC.dbo.PreProjeto", "FichaTecnica", $Solicitada, "idPreProjeto", $idPreProjeto);
                    $result = $db->fetchAll($avaliacao);
                } elseif ($tpAlt == 4) {
                    //LOCAL DE REALIZA��O
                    $local = ProjetoDAO::buscarPronac($idPronac);
                    $idProjeto = $local['idProjeto'];

                    $dadosTbAbran = tbAbrangenciaDAO::buscarDadosTbAbrangencia(null, $id);

                    foreach ($dadosTbAbran as $x):
                        if (trim($x->tpAcao) == 'I') {
                            $dados = array(
                                    'idProjeto'         => $idProjeto,
                                    'idPais'            => $x->idPais,
                                    'idUF'              => $x->idUF,
                                    'idMunicipioIBGE'   => $x->idMunicipioIBGE,
                                    'Usuario'           => $idagente,
                                    'stAbrangencia'     => '1'
                            );

                            //if (count(AbrangenciaDAO::verificarLocalRealizacao($idProjeto, $x->idMunicipioIBGE)) <= 0) :
                            $local = $tbAbrangencia->cadastrar($dados);
                            //endif;
                            //print_r($local);
                        } elseif (trim($x->tpAcao) == 'E') {
                            // altera o status dos locais exclu�dos
                            $Abrangencia = new Proposta_Model_DbTable_Abrangencia();
                            $Abrangencia->update(array('stAbrangencia' => 0), array('idAbrangencia = ?' => $x->idAbrangenciaAntiga));
                            //$_local = AbrangenciaDAO::buscarAbrangenciasAtuais($idProjeto, $x->idPais, $x->idUF, $x->idMunicipioIBGE);
                            //$__local = AbrangenciaDAO::excluir($_local[0]->idAbrangencia);
                        }
                    endforeach;
                } elseif ($tpAlt == 5 && isset($_GET['checklist'])) {
                    //NOME DO PROJETO
                    $Projetos = new Projetos();
                    $DadosAlteracaoNomeProjeto = PedidoAlteracaoDAO::buscarAlteracaoNomeProjeto($idPronac);
                    $dados = array(
                            'NomeProjeto' => $DadosAlteracaoNomeProjeto['nmProjeto']
                    );
                    $Projetos->alterar($dados, array('IdPRONAC = ?' => $idPronac));
                } elseif ($tpAlt == 6) {

                    //PROPOSTA PEDAG�GICA
                    $sqlproposta = ReadequacaoProjetos::retornaSQLproposta("sqlpropostafinalizar", $idPronac);
                    $dadosSolicitado = $db->fetchAll($sqlproposta);

                    $Projeto = new Projetos();
                    $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));

                    if (count($DadosProj) > 0 && !empty($DadosProj[0]->idProjeto)) {
                        $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
                        $dados = array(
                                'EstrategiadeExecucao' => $dadosSolicitado[0]['dsEstrategiaExecucao'],
                                'EspecificacaoTecnica' => $dadosSolicitado[0]['dsEspecificacaoSolicitacao']
                        );
                        PreProjeto::alterarDados($dados, array('idPreProjeto = ?' => $DadosProj[0]->idProjeto));
                    }
                } elseif ($tpAlt == 7) {
                    $tbPlanoDistribuicao = new tbPlanoDistribuicao();
                    $produtosAnalisadosDeferidos = $tbPlanoDistribuicao->produtosAvaliadosReadequacao($idPedidoAlt, $id);

                    foreach ($produtosAnalisadosDeferidos as $valores) {
                        $Projeto = new Projetos();
                        $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));

                        $dadosProduto = array(
                                'idPlanoDistribuicao'           => $valores->idPlanoDistribuicao
                                ,'idProjeto'                    => $DadosProj[0]->idProjeto
                                ,'idProduto'                    => $valores->idProduto
                                ,'Area'                         => $valores->cdArea
                                ,'Segmento'                     => $valores->cdSegmento
                                ,'idPosicaoDaLogo'              => $valores->idPosicaoLogo
                                ,'QtdeProduzida'                => $valores->qtProduzida
                                ,'QtdePatrocinador'             => $valores->qtPatrocinador
                                ,'QtdeProponente'               => null
                                ,'QtdeOutros'                   => $valores->qtOutros
                                ,'QtdeVendaNormal'              => $valores->qtVendaNormal
                                ,'QtdeVendaPromocional'         => $valores->qtVendaPromocional
                                ,'PrecoUnitarioNormal'          => $valores->vlUnitarioNormal
                                ,'PrecoUnitarioPromocional'     => $valores->vlUnitarioPromocional
                                ,'stPrincipal'                  => $valores->stPrincipal
                                ,'stPlanoDistribuicaoProduto'   => 1
                        );

                        //ALTERA OU INSERE O PLANO DE DISTRIBUICAO
                        $PlanoDistribuicao = new PlanoDistribuicao();
                        $x = $PlanoDistribuicao->salvar($dadosProduto);
                    }
                } elseif ($tpAlt == 8 && isset($_GET['checklist'])) {

                    //PRORROGACAO DE PRAZOS - CAPTACAO
                    $datas = PedidoAlteracaoDAO::buscarAlteracaoPrazoCaptacao($idPronac);
                    $Projeto = new Projetos();
                    $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));
                    $Aprovacao = new Aprovacao();
                    $registro = $Aprovacao->buscar(array('AnoProjeto = ?' => $DadosProj[0]->AnoProjeto, 'Sequencial = ?' => $DadosProj[0]->Sequencial ));
                    $dados = array(
                            'IdPRONAC' => $idPronac,
                            'AnoProjeto' => $DadosProj[0]->AnoProjeto,
                            'Sequencial' => $DadosProj[0]->Sequencial,
                            'TipoAprovacao' => 3,
                            'DtAprovacao' => new Zend_Db_Expr('GETDATE()'),
                            // 'ResumoAprovacao' => 'Solicita��o de Readequa��o',
                            'DtInicioCaptacao' => $datas['dtInicioNovoPrazo'],
                            'DtFimCaptacao' => $datas['dtFimNovoPrazo'],
                            'Logon' => $idagente
                    );
                    $Aprovacao->inserir($dados);
                } elseif ($tpAlt == 9 && isset($_GET['checklist'])) {
                    //PRORROGACAO DE PRAZOS - EXECUCAO
                    $datas = PedidoAlteracaoDAO::buscarAlteracaoPrazoExecucao($idPronac);
                    $projetos = new Projetos();
                    $dados = array(
                            'DtInicioExecucao' => $datas['dtInicioNovoPrazo'],
                            'DtFimExecucao' => $datas['dtFimNovoPrazo']
                    );
                    $projetos->alterar($dados, array('IdPRONAC = ?' => $idPronac));
                } elseif ($tpAlt == 10) {
                    $tbPlanoDistribuicao = new tbPlanoDistribuicao();
                    $produtosAnalisadosDeferidos = $tbPlanoDistribuicao->produtosAvaliadosReadequacao($idPedidoAlt, $id);

                    foreach ($produtosAnalisadosDeferidos as $valores) {
                        $Projeto = new Projetos();
                        $DadosProj = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac));
                        $dadosProduto = array(
                                'idPlanoDistribuicao'           => $valores->idPlanoDistribuicao
                                ,'idProjeto'                    => $DadosProj[0]->idProjeto
                                ,'idProduto'                    => $valores->idProduto
                                ,'Area'                         => $valores->cdArea
                                ,'Segmento'                     => $valores->cdSegmento
                                ,'idPosicaoDaLogo'              => $valores->idPosicaoLogo
                                ,'QtdeProduzida'                => $valores->qtProduzida
                                ,'QtdePatrocinador'             => $valores->qtPatrocinador
                                ,'QtdeProponente'               => null
                                ,'QtdeOutros'                   => $valores->qtOutros
                                ,'QtdeVendaNormal'              => $valores->qtVendaNormal
                                ,'QtdeVendaPromocional'         => $valores->qtVendaPromocional
                                ,'PrecoUnitarioNormal'          => $valores->vlUnitarioNormal
                                ,'PrecoUnitarioPromocional'     => $valores->vlUnitarioPromocional
                                ,'stPrincipal'                  => $valores->stPrincipal
                                ,'stPlanoDistribuicaoProduto'   => 1
                        );
                        //ALTERA OU INSERE O PLANO DE DISTRIBUICAO
                        $PlanoDistribuicao = new PlanoDistribuicao();
                        $x = $PlanoDistribuicao->salvar($dadosProduto);
                    }


                    // PRODUTO + ITEN DE CUSTO
                    $planilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
                    $planilhaProjeto  = new PlanilhaProjeto();
                    $DeParaPlanilhaAprovacao = new DeParaPlanilhaAprovacao();
                    $Projetos = new Projetos();
                    $planilha = new PlanilhaAprovacao();
                    $PlanilhasSolicitadas = $planilha->buscar(array('IdPRONAC = ?' => $idPronac, 'tpPlanilha = ?' => 'PA'));
                    $buscarProjeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac));

                    foreach ($PlanilhasSolicitadas as $dadosP) {
                        if (!empty($dadosP->idPedidoAlteracao)) {
                            $_dados = array('IdPRONAC = ?' => $idPronac
                                    , 'tpPlanilha = ?' => 'SR'
                                    , 'IdPRONAC = ?' => $idPronac
                                    , 'idPedidoAlteracao = ? ' => $dadosP->idPedidoAlteracao);

                            $buscarTpAcaoSR = $planilha->buscar($_dados);

                            if (count($buscarTpAcaoSR) > 0 && !empty($buscarProjeto[0]->idProjeto)) {
                                // EXCLUS�O
                                if ($buscarTpAcaoSR[0]->tpAcao == 'E') :
                                    // planilha antiga
                                    $idProjeto = $buscarProjeto[0]->idProjeto;
                                $dadosAprovados = $planilhaProposta->buscar(array('idProjeto = ?' => $idProjeto, 'idProduto = ?' => $dadosP->idProduto, 'idEtapa = ?' => $dadosP->idEtapa, 'idPlanilhaItem = ?' => $dadosP->idPlanilhaItem));
                                foreach ($dadosAprovados as $dadosExculsao) :
                                        $buscarDeParaPlanilhaAprovacao = $DeParaPlanilhaAprovacao->buscarPlanilhaProposta($dadosExculsao->idPlanilhaProposta);
                                foreach ($buscarDeParaPlanilhaAprovacao as $b) :
                                            $DeParaPlanilhaAprovacao->delete(array('idPlanilhaAprovacao = ?' => $b->idPlanilhaAprovacao));
                                endforeach;
                                $planilha->delete(array('idPlanilhaProposta = ?' => $dadosExculsao->idPlanilhaProposta));
                                $planilhaProjeto->delete(array('idPlanilhaProposta = ?' => $dadosExculsao->idPlanilhaProposta));
                                $planilhaProposta->delete(array('idPlanilhaProposta = ?' => $dadosExculsao->idPlanilhaProposta));
                                endforeach;

                                // ALTERA��O
                                elseif ($buscarTpAcaoSR[0]->tpAcao == 'A') :
                                    // planilha antiga
                                    $idProjeto = $buscarProjeto[0]->idProjeto;
                                $dadosAprovados = $planilhaProposta->buscar(array('idProjeto = ?' => $idProjeto, 'idProduto = ?' => $dadosP->idProduto, 'idEtapa = ?' => $dadosP->idEtapa, 'idPlanilhaItem = ?' => $dadosP->idPlanilhaItem));
                                foreach ($dadosAprovados as $dadosAlteracao) :
                                        $where = array('idPlanilhaProposta = ?' => $dadosAlteracao->idPlanilhaProposta);
                                $dados = array(
                                                'idProduto' => $dadosP->idProduto,
                                                'idEtapa' => $dadosP->idEtapa,
                                                'idPlanilhaItem' => $dadosP->idPlanilhaItem,
                                                'Descricao' => $dadosP->dsItem,
                                                'Unidade' => $dadosP->idUnidade,
                                                'Quantidade' => $dadosP->qtItem,
                                                'Ocorrencia' => $dadosP->nrOcorrencia,
                                                'ValorUnitario' => $dadosP->vlUnitario,
                                                'QtdeDias' => $dadosP->qtDias,
                                                'TipoDespesa' => $dadosP->tpDespesa,
                                                'TipoPessoa' => $dadosP->tpPessoa,
                                                'Contrapartida' => $dadosP->nrContraPartida,
                                                'FonteRecurso' => $dadosP->nrFonteRecurso,
                                                'UfDespesa' => $dadosP->idUFDespesa,
                                                'MunicipioDespesa' => $dadosP->idMunicipioDespesa,
                                                'idUsuario' => $dadosP->idAgente,
                                                'dsJustificativa' => $dadosP->dsJustificativa
                                        );
                                $planilhaProposta->alterar($dados, $where);
                                endforeach;

                                $planilha->update(array('tpPlanilha' => 'CO' , 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $dadosP->idPlanilhaAprovacao));
                                $planilha->update(array('tpPlanilha' => 'CO' , 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $buscarTpAcaoSR[0]->idPlanilhaAprovacao));
                                // INCLUS�O
                                elseif ($buscarTpAcaoSR[0]->tpAcao == 'I') :
                                    // planilha antiga
                                    $ReplicaDados = array(
                                            'idProjeto' => $buscarProjeto[0]->idProjeto,
                                            'idProduto' => $dadosP->idProduto,
                                            'idEtapa' => $dadosP->idEtapa,
                                            'idPlanilhaItem' => $dadosP->idPlanilhaItem,
                                            'Descricao' => $dadosP->dsItem,
                                            'Unidade' => $dadosP->idUnidade,
                                            'Quantidade' => $dadosP->qtItem,
                                            'Ocorrencia' => $dadosP->nrOcorrencia,
                                            'ValorUnitario' => $dadosP->vlUnitario,
                                            'QtdeDias' => $dadosP->qtDias,
                                            'TipoDespesa' => $dadosP->tpDespesa,
                                            'TipoPessoa' => $dadosP->tpPessoa,
                                            'Contrapartida' => $dadosP->nrContraPartida,
                                            'FonteRecurso' => $dadosP->nrFonteRecurso,
                                            'UfDespesa' => $dadosP->idUFDespesa,
                                            'MunicipioDespesa' => $dadosP->idMunicipioDespesa,
                                            'idUsuario' => $dadosP->idAgente,
                                            'dsJustificativa' => $dadosP->dsJustificativa
                                    );
                                $planilhaProposta->inserir($ReplicaDados);

                                $planilha->update(array('tpPlanilha' => 'CO', 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $dadosP->idPlanilhaAprovacao));
                                $planilha->update(array('tpPlanilha' => 'CO' , 'stAtivo' => 'N'), array('idPlanilhaAprovacao = ? ' => $buscarTpAcaoSR[0]->idPlanilhaAprovacao));
                                endif;
                            }
                        } // fecha if
                    }
                }
            }

            $db->commit();

            //CASO SEJA O �LTIMO ITEM DO PEDIDO DE ALTERA��O, FINALIZA O STATUS DA MESMA
            $tbPedidoAlteracaoXTipoAlteracao = new tbPedidoAlteracaoXTipoAlteracao();
            $verificarPedidosAtivos = $tbPedidoAlteracaoXTipoAlteracao->buscar(array('idPedidoAlteracao = ?' => $idPedidoAlt, 'stVerificacao <> ?' => 4));
            $arrBusca = array();
            $arrBusca['p.siVerificacao IN (?)'] = array('1');
            $arrBusca['p.IdPRONAC = ?'] = $idPronac;
            $arrBusca['x.tpAlteracaoProjeto IN (?)'] = array('1', '2', '5', '7', '8', '9', '10');
            $arrBusca['a.stAvaliacaoItemPedidoAlteracao IN (?)'] = array('AP');
            $arrBusca['c.stVerificacao NOT IN (?)'] = array('4');

            $buscaChecklist = $tbPedidoAlteracaoXTipoAlteracao->buscarPedidoChecklist($arrBusca);
            if (count($verificarPedidosAtivos) == 0 && count($buscaChecklist) == 0) :
                $tbPedidoAlteracaoProjeto = new tbPedidoAlteracaoProjeto();
            $tbPedidoAlteracaoProjeto->alterar(array('siVerificacao' => 2), array('idPedidoAlteracao = ?' => $idPedidoAlt));
            endif;

            if (isset($_GET['checklist'])) {
                parent::message("Portaria publicada com sucesso!", "publicacaodou/index", "CONFIRM");
            } else {
                parent::message("Projeto finalizado com sucesso!", "manterreadequacao?tipoFiltro=$tipoFiltro", "CONFIRM");
            }
        } catch (Zend_Exception $e) {
            $db->rollBack();
            parent::message("Erro na devolu&ccedil;&atilde;o da solicita&ccedil;&atilde;o", "manterreadequacao?tipoFiltro=$tipoFiltro", "ERROR");
        }
    }
}
