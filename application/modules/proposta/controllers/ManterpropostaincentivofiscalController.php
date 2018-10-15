<?php

class Proposta_ManterpropostaincentivofiscalController extends Proposta_GenericController
{

    /**
     * @var integer (variavel com o id do usuario logado)
     * @access private
     */
    private $blnPossuiDiligencias = 0;

    public function init()
    {
        parent::init();

        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;
            $this->view->addScriptPath(APPLICATION_PATH . '/modules/proposta/views/scripts/manterpropostaincentivofiscal');

            $this->verificarPermissaoAcesso(true, false, false);

            //VERIFICA SE A PROPOSTA TEM DILIGENCIAS
            $PreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $rsDiligencias = $PreProjeto->listarDiligenciasPreProjeto(array('pre.idpreprojeto = ?' => $this->idPreProjeto));
            $this->view->blnPossuiDiligencias = $rsDiligencias->count();

            $this->view->acao = $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/salvar";

        }

        // Busca na tabela apoio ExecucaoImediata
        $tableVerificacao = new Proposta_Model_DbTable_Verificacao();
        $listaExecucaoImediata = $tableVerificacao->fetchPairs('idVerificacao', 'Descricao', array('idTipo' => 23), array('idVerificacao'));
        $this->view->listaExecucaoImediata = $listaExecucaoImediata;
    }

    /**
     * verificaPermissaoAcessoProposta
     *
     * @param mixed $idPreProjeto
     * @access public
     * @return void
     * @deprecated existe o metodo $this->verificarPermissaoAcesso();
     */
    public function verificaPermissaoAcessoProposta($idPreProjeto)
    {
        $tblProposta = new Proposta_Model_DbTable_PreProjeto();
        $rs = $tblProposta->buscar(array("idPreProjeto = ? " => $idPreProjeto, "1=1 OR idEdital IS NULL OR idEdital > 0" => "?", "idUsuario =?" => $this->idResponsavel));
        return $rs->count();
    }

    /**
     * indexAction
     *
     * @access public
     * @return void
     */
    public function indexAction()
    {
        if ($this->idPreProjeto) {
            $this->redirect("/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/" . $this->idPreProjeto);
        } else {
            $this->redirect("/proposta/manterpropostaincentivofiscal/listarproposta");
        }

        $arrBusca = array();
        $arrBusca['stestado = ?'] = 1;
        $arrBusca['idusuario = ?'] = $this->idResponsavel;
        // Chama o SQL
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $rsPreProjeto = $tblPreProjeto->buscar($arrBusca, array("idagente ASC"));

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
        $this->montaTela(
            "manterpropostaincentivofiscal/index.phtml",
            array(
                "acaoAlterar" => $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/identificacaodaproposta",
                "acaoExcluir" => $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/excluir",
                "dados" => $rsPreProjeto
            )
        );
    }

    /**
     * declaracaonovapropostaAction
     *
     * @access public
     * @return void
     */
    public function declaracaonovapropostaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');

        if ($post->mecanismo == 1) { //mecanismo == 1 (proposta por incentivo fiscal)
            $url = $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/identificacaodaproposta";
        } else {
            $url = $this->_urlPadrao . "/manterpropostaedital/editalconfirmar";
        }

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
        $this->montaTela("manterpropostaincentivofiscal/declaracaonovaproposta.phtml", array("acao" => $url,
            "agente" => $post->proponente));
    }

    /**
     * buscaproponenteAction
     *
     * @access public
     * @return void
     * @deprecated Fiz as alteracoes e este metodo era desnecessario para iniciar uma nova proposta
     */
    public function buscaproponenteAction()
    {
        $post = Zend_Registry::get('post');

        if (empty($post->idAgente)) {
            $this->montaTela("manterpropostaincentivofiscal/declaracaonovaproposta.phtml");
            return;
        }

        //VERIFICA SE PROPONETE JA ESTA CADASTRADO
        $arrBusca = array();
        $arrBusca['a.idagente = ?'] = $post->idAgente;
        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsProponente = $tblAgente->buscarAgenteENome($arrBusca)->current();

        if ($rsProponente) {
            $rsProponente = array_change_key_case($rsProponente->toArray());
            //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
            $this->montaTela("manterpropostaincentivofiscal/identificacaodaproposta.phtml", array("proponente" => $rsProponente,
                "acao" => $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/salvar"));
        } else {
            $this->redirect("/agente/manteragentes/agentes");
        }
    }

    /**
     * validaagenciaAction
     *
     * @access public
     * @return void
     */
    public function validaagenciaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $agencia = $this->getParam('agencia');

        if ($agencia > 0) {
            $tblProposta = new Proposta_Model_DbTable_PreProjeto();
            $agencia = $tblProposta->buscaragencia($agencia);
            if (count($agencia) > 0) {
                echo "";
            } else {
                echo "Ag&ecirc;ncia inv&aacute;lida";
            }
        } else {
            echo "Ag&ecirc;ncia inv&aacute;lida";
        }
    }

    /**
     * Metodo responsavel por gravar a Proposta (INSERT e UPDATE)
     * @param void
     * @return objeto
     */
    public function salvarAction()
    {
        $this->validarEdicaoProposta();

        $post = array_change_key_case($this->getRequest()->getPost());

        if (empty($post['idagente'])) {
            throw new Zend_Exception("Informe o idagente");
        }

        $idPreProjeto = $post['idpreprojeto'];
        $acao = $post['acao'];
        $url = $post['url'];

        if ($acao == 'atualizacao_automatica') {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if ($post['dtiniciodeexecucao']) {
            $dtInicioTemp = explode("/", $post['dtiniciodeexecucao']);
            $post['dtiniciodeexecucao'] = $dtInicioTemp[2] . "/" . $dtInicioTemp[1] . "/" . $dtInicioTemp[0] . date(" H:i:s");
        }

        if ($post['dtfinaldeexecucao']) {
            $dtFimTemp = explode("/", $post['dtfinaldeexecucao']);
            $post['dtfinaldeexecucao'] = $dtFimTemp[2] . "/" . $dtFimTemp[1] . "/" . $dtFimTemp[0] . date(" H:i:s");
        }

        if ($post['dtatotombamento']) {
            $dtAtoTombamentoTemp = explode("/", $post['dtatotombamento']);
            $post['dtatotombamento'] = $dtAtoTombamentoTemp[2] . "/" . $dtAtoTombamentoTemp[1] . "/" . $dtAtoTombamentoTemp[0] . date(" H:i:s");
        }

        if ($post['nomeprojeto']) {
            $nomeProjeto = str_replace("'", "", $post['nomeprojeto']);
            $post['nomeprojeto'] = str_replace("\"", "", $nomeProjeto);
        }

        if ($post['resumodoprojeto']) {
            //***NAO TIRAR ESSA QUEBRA DE LINHA - FAZ PARTE DA PROGRAMACAO****
            $post['resumodoprojeto'] = str_replace('
', ' ', str_replace('	', '', str_replace('&nbsp;', '', strip_tags(trim($post['resumodoprojeto'])))));
            //***NAO TIRAR ESSA QUEBRA DE LINHA - FAZ PARTE DA PROGRAMACAO****
        }

        if (!empty($idPreProjeto)) {
            $arrBusca['idPreProjeto = ?'] = $idPreProjeto;

            $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $rsPreProjeto = $tblPreProjeto->buscar($arrBusca)->current();
            $proposta = array_change_key_case($rsPreProjeto->toArray());
            $post = array_intersect_key($post, $proposta) + $proposta;
        }

        $dados = array(
            "idagente" => isset($post['idagente']) ? $post['idagente'] : '',
            "nomeprojeto" => isset($post['nomeprojeto']) ? $post['nomeprojeto'] : '',
            "mecanismo" => 1, //seguindo sistema legado
            "agenciabancaria" => isset($post['agenciabancaria']) ? $post['agenciabancaria'] : '',
            "areaabrangencia" => isset($post['areaabrangencia']) ? $post['areaabrangencia'] : '',
            "dtiniciodeexecucao" => isset($post['dtiniciodeexecucao']) ? $post['dtiniciodeexecucao'] : '',
            "dtfinaldeexecucao" => isset($post['dtfinaldeexecucao']) ? $post['dtfinaldeexecucao'] : '',
            "dtatotombamento" => (isset($post['dtatotombamento']) && $post['dtatotombamento']) ? $post['dtatotombamento'] : null,
            "nratotombamento" => isset($post['nratotombamento']) ? $post['nratotombamento'] : '',
            "esferatombamento" => isset($post['esferatombamento']) ? $post['esferatombamento'] : '0',
            "resumodoprojeto" => isset($post['resumodoprojeto']) ? $post['resumodoprojeto'] : '',
            "objetivos" => isset($post['objetivos']) ? $post['objetivos'] : '',
            "justificativa" => isset($post['justificativa']) ? $post['justificativa'] : '',
            "acessibilidade" => isset($post['acessibilidade']) ? $post['acessibilidade'] : '',
            "democratizacaodeacesso" => isset($post['democratizacaodeacesso']) ? $post['democratizacaodeacesso'] : '',
            "etapadetrabalho" => isset($post['etapadetrabalho']) ? $post['etapadetrabalho'] : '',
            "fichatecnica" => isset($post['fichatecnica']) ? $post['fichatecnica'] : '',
            "sinopse" => isset($post['sinopse']) ? $post['sinopse'] : '',
            "impactoambiental" => isset($post['impactoambiental']) ? $post['impactoambiental'] : '',
            "descricaoatividade" => isset($post['descricaoatividade']) ? $post['descricaoatividade'] : '',
            "especificacaotecnica" => isset($post['especificacaotecnica']) ? $post['especificacaotecnica'] : '', //No legado o que esta sendo gravado aqui e OUTRAS INFORMACOES
            "estrategiadeexecucao" => isset($post['estrategiadeexecucao']) ? $post['estrategiadeexecucao'] : '', //No legado o que esta sendo gravado aqui e ESPECIFICAO TECNICA
            "dtaceite" => isset($post['dtaceite']) ? $post['dtaceite'] : date("Y/m/d H:i:s"), // verificar se realmente eh sempre que salva
            "stestado" => isset($post['stestado']) ? $post['stestado'] : 1,
            "stdatafixa" => isset($post['stdatafixa']) ? $post['stdatafixa'] : '',
            "stproposta" => isset($post['stproposta']) ? $post['stproposta'] : '',
            "idusuario" => isset($post['idusuario']) ? $post['idusuario'] : $this->idResponsavel,
            "sttipodemanda" => "NA", //seguindo sistema legado
            "tpprorrogacao" => isset($post['tpprorrogacao']) ? $post['tpprorrogacao'] : ''
        );

        $dados['idpreprojeto'] = $idPreProjeto;

        if (!empty($idPreProjeto)) {
            $mesagem = "Altera&ccedil;&atilde;o realizada com sucesso!";
        } else {
            $mesagem = "Cadastro realizado com sucesso!";
        }

        //instancia classe modelo
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        try {
            //persiste os dados do Pre Projeto
            $idPreProjeto = $tblPreProjeto->salvar($dados);
            $this->view->idPreProjeto = $idPreProjeto;

            if ($acao == "incluir") {
                // Salvando os dados na TbVinculoProposta
                $tbVinculoDAO = new Agente_Model_DbTable_TbVinculo();
                $tbVinculoPropostaDAO = new Agente_Model_DbTable_TbVinculoProposta();

                $whereVinculo['idUsuarioResponsavel = ?'] = $this->idResponsavel;
                $whereVinculo['idAgenteProponente   = ?'] = $post['idagente'];
                $vinculo = $tbVinculoDAO->buscar($whereVinculo);

                if (count($vinculo) == 0) {
                    $dadosV = array(
                        'idAgenteProponente' => $post['idagente'],
                        'dtVinculo' => MinC_Db_Expr::date(),
                        'siVinculo' => 2,
                        'idUsuarioResponsavel' => $this->idResponsavel
                    );

                    $insere = $tbVinculoDAO->insert($dadosV);
                }

                $vinculo2 = $tbVinculoDAO->buscar($whereVinculo);
                if (count($vinculo2) > 0) {
                    $vinculo2 = $vinculo2[0]->toArray();
                    $novosDadosV = array(
                        'idVinculo' => $idVinculo = $vinculo2['idVinculo'],
                        'idPreProjeto' => $idPreProjeto,
                        'siVinculoProposta' => 2
                    );
                    $insere = $tbVinculoPropostaDAO->insert($novosDadosV);
                }
                /* **************************************************************************************** */
            }
            // Plano de execução imediata #novain
            if ($post['stproposta'] == '618') { // proposta execucao imediata edital
                $idDocumento = 248;
            } elseif ($post['stproposta'] == '619') { // proposta execucao imediata contrato de patrocínio
                $idDocumento = 162;
            }

            if (!empty($idDocumento)) {
                $arrayFile = array(
                    'idPreProjeto' => $idPreProjeto,
                    'documento' => $idDocumento,
                    'tipoDocumento' => 2,
                    'observacao' => ''
                );

                $mapperTbDocumentoAgentes = new Proposta_Model_TbDocumentosAgentesMapper();
                $file = new Zend_File_Transfer();
                $mapperTbDocumentoAgentes->saveCustom($arrayFile, $file);
            }
            if ($acao != 'atualizacao_automatica') {
                if (empty($url)) {
                    $url = "/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/{$idPreProjeto}";
                }

                parent::message($mesagem, $url, "CONFIRM");
            }
            return;
        } catch (Zend_Exception $ex) {
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!" . $ex->getMessage(), "/proposta/manterpropostaincentivofiscal/index?idPreProjeto=" . $idPreProjeto, "ERROR");
        }
    }

    /**
     * Metodo responsavel por carregar a proposta na tela apos uma nova proposta ser inclusa
     * @param $idPreProjeto
     * @return objeto
     */
    public function carregaProposta($idPreProjeto)
    {
        $arrBusca = array();
        $arrBusca['idPreProjeto = ?'] = $idPreProjeto;
        $this->view->idPreProjeto = $idPreProjeto;
        // Chama o SQL
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $rsPreProjeto = $tblPreProjeto->buscar($arrBusca)->current();

        $arrBuscaProponete = array();
        $arrBuscaProponete['a.idAgente = ?'] = $rsPreProjeto->idAgente;

        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsProponente = $tblAgente->buscarAgenteENome($arrBuscaProponete)->current();

        $arrDados = array("proposta" => $rsPreProjeto,
            "proponente" => $rsProponente);
        return $arrDados;
    }

    /**
     * Metodo responsavel por carregar os dados da proposta para alteracao
     * @param void
     * @return objeto
     * @deprecated testar proposta e remover em novaIn
     */
    public function editarAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);

        $idPreProjeto = $this->idPreProjeto;

        $this->redirect("/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/" . $this->idPreProjeto);
    }

    public function identificacaodapropostaAction()
    {
        $this->validarEdicaoProposta();

        if (empty($this->_proposta["idpreprojeto"])) {
            $post = Zend_Registry::get('post');

            $arrBusca = array();
            if (empty($post->idAgente)) {
                parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!", "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
            }

            $arrBusca['a.idagente = ?'] = $post->idAgente;
            $tblAgente = new Agente_Model_DbTable_Agentes();
            $rsProponente = $tblAgente->buscarAgenteENome($arrBusca)->current();
            if ($rsProponente) {
                $rsProponente = array_change_key_case($rsProponente->toArray());

                $this->view->proponente = $rsProponente;

                $this->view->isEditavel = true;

                $this->montaTela("manterpropostaincentivofiscal/identificacaodaproposta.phtml", array("proponente" => $rsProponente,
                    "acao" => $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/salvar"));
            }
        } else {
            $tbl = new Proposta_Model_DbTable_TbDocumentosPreProjeto();

            // Plano de execução imediata #novain
            if ($this->_proposta["stproposta"] == '618') { // proposta execucao imediata edital
                $idDocumento = 248;
            } elseif ($this->_proposta["stproposta"] == '619') { // proposta execucao imediata contrato de patrocínio
                $idDocumento = 162;
            }
            if (!empty($idDocumento)) {
                $arquivoExecucaoImediata = $tbl->buscarDocumentos(array("idprojeto = ?" => $this->idPreProjeto, "CodigoDocumento = ?" => $idDocumento));
            }

            $this->view->arquivoExecucaoImediata = $arquivoExecucaoImediata;
        }


        if ($this->isEditarProjeto($this->idPreProjeto)) {
            $tblProjetos = new Projetos();
            $projeto = $tblProjetos->findBy(array('idprojeto = ?' => $this->idPreProjeto));

            if (!empty($projeto['IdPRONAC'])) {
                $projeto2 = ConsultarDadosProjetoDAO::obterDadosProjeto(array('idPronac' => (int)$projeto['IdPRONAC']));

                $this->view->projeto = array_change_key_case((array)$projeto2[0]);
            }

            $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($this->idPreProjeto, 109);
            $outrasfontes = $planilhaproposta->somarPlanilhaProposta($this->idPreProjeto, false, 109);

            $this->view->valorsolicitadoincentivo = !empty($fonteincentivo['soma']) ? $fonteincentivo['soma'] : 0;
            $this->view->valoroutrasfontes = !empty($outrasfontes['soma']) ? $outrasfontes['soma'] : 0;
        }
    }

    public function responsabilidadesocialAction()
    {
        $this->validarEdicaoProposta();

    }

    public function detalhestecnicosAction()
    {
        $this->validarEdicaoProposta();

    }

    public function outrasinformacoesAction()
    {
        $this->validarEdicaoProposta();
    }

    /**
     * Encaminhar projeto ao MinC
     *
     * Metódo para o proponente finalizar a situação do projeto, nem sempre este metódo será acionado,
     * tendo em vista que existe uma rotina no banco para alterar a situacao do projeto após o prazo de alteracao.
     *
     * Regras antes de encaminhar
     * 1. Validar o checklist da proposta
     *
     * Regras ao encaminhar
     * Quando o proponente clicar na opção Encaminhar projeto ao MinC, o sistema deverá a alterar situação do projeto para B20,
     * com a seguinte providencia tomada: Projeto ajustado pelo proponente e encaminhado ao MinC para avaliação.
     *
     *
     */
    public function encaminharprojetoaomincAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);
        $this->validarEdicaoProposta();

        $params = $this->getRequest()->getParams();

        $idPreProjeto = $params['idPreProjeto'];

        if (empty($idPreProjeto)) {
            parent::message("Necess&aacute;rio informar o n&uacute;mero da proposta.", "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
        }

        $validacao = new stdClass();
        $listaValidacao = array();

        if (!empty($idPreProjeto)) {
            $tbPreProjeto = new Proposta_Model_DbTable_PreProjeto();

            if (!$tbPreProjeto->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
                $arrResultado = $this->validarEnvioPropostaSemSp($idPreProjeto);
            } else {
                $arrResultado = $this->validarEnvioPropostaComSp($idPreProjeto);
            }

            $tblProjetos = new Projetos();
            $projeto = array_change_key_case($tblProjetos->findBy(array('idprojeto = ?' => $idPreProjeto)));
            $idPronac = $projeto['idpronac'];

            if ($arrResultado->Observacao === true) {
                    $this->view->acao = $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/encaminharprojetoaominc";
            }

            $this->view->resultado = $arrResultado;

            if ($params['confirmarenvioaominc'] == true && $arrResultado->Observacao === true) {
                if ($projeto['area'] == 2) {
                    $orgaoUsuario = Orgaos::ORGAO_SAV_DAP;
                } else {
                    $orgaoUsuario = Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
                }

                # verificar se o projeto já possui avaliador
                $tbAvaliacao = new Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
                $avaliacao = $tbAvaliacao->buscarUltimaAvaliacao($idPronac);

                $avaliador = isset($avaliacao['idTecnico']) ? $avaliacao['idTecnico'] : '';

                $tbAvaliacao->inserirAvaliacao($idPronac, $orgaoUsuario, $avaliador);

                # alterar a situacao do projeto
                $codigoSituacao = 'B20'; #B20 - Projeto adequado a realidade de execucao
                $providenciaTomada = "Projeto ajustado pelo proponente e encaminhado ao MinC para avalia&ccedil;&atilde;o";

                $tblProjetos->alterarSituacao($idPronac, '', $codigoSituacao, $providenciaTomada, $this->idUsuario);

                parent::message("Projeto encaminhado com sucesso para an&aacute;lise no Minist&eacute;rio da Cultura.", "/listarprojetos/listarprojetos", "CONFIRM");
            }
        }
    }


    /**
     * Metodo responsavel por inativar uma proposta gravada
     * @param void
     * @return objeto
     */
    public function excluirAction()
    {
        $this->validarEdicaoProposta();

        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

        //BUSCANDO REGISTRO A SER ALTERADO
        $preProjeto = new Proposta_Model_DbTable_PreProjeto();
        $preProjeto = $preProjeto->find($idPreProjeto)->current();
        //altera Estado da proposta
        $preProjeto->stEstado = 0;

        if ($preProjeto->save()) {
            parent::message("Exclus&atilde;o realizada com sucesso!", "/proposta/manterpropostaincentivofiscal/listarproposta", "CONFIRM");
        } else {
            parent::message("N&atilde;o foi possível realizar a opera&ccedil;&atilde;o!", "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
        }
    }

    /**
     * enviarPropostaAction
     *
     * @access public
     * @return void
     */
    public function enviarPropostaAction()
    {
        $this->validarEdicaoProposta();

        $params = $this->getRequest()->getParams();
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

        if (empty($idPreProjeto)) {
            parent::message("Necess&aacute;rio informar o n&uacute;mero da proposta.", "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
        }

        try {
            $tbPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            if (!$tbPreProjeto->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
                $arrResultado = $this->validarEnvioPropostaSemSp($idPreProjeto);
            } else {
                $arrResultado = $this->validarEnvioPropostaComSp($idPreProjeto);
            }

            if ($params['confirmarenvioaominc'] == true && $arrResultado->Observacao === true) {
                $proposta = $tbPreProjeto->findBy(array('idPreProjeto' => $idPreProjeto));

                $dados = array(
                    'idprojeto' => $idPreProjeto,
                    'movimentacao' => 96,
                    'dtmovimentacao' => MinC_Db_Expr::date(),
                    'stestado' => 0,
                    'usuario' => $proposta['idUsuario']
                );

                $tbMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
                $tbMovimentacao->insert($dados);

                parent::message("Proposta encaminhada com sucesso para an&aacute;lise no Minist&eacute;rio da Cultura.", "/proposta/visualizar/index/idPreProjeto/" . $idPreProjeto, "CONFIRM");
            }

            $this->view->resultado = $arrResultado;
            $this->view->acao = $this->_urlPadrao . "/proposta/manterpropostaincentivofiscal/enviar-proposta/idPreProjeto/" . $this->idPreProjeto;
        } catch(Exception $objException){
            parent::message("Envio de proposta: " . $objException->getMessage(), "/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/" . $idPreProjeto, "ERROR");
        }
    }

    /**
     * validarEnvioPropostaComSp
     *
     * @param mixed $idPreProjeto
     * @access public
     * @return void
     */
    private function validarEnvioPropostaComSp($idPreProjeto)
    {
        try {
            $validacao = new stdClass();
            $listaValidacao = [];

            $this->atualizarDadosPessoaJuridicaVerificandoCNAECultural($idPreProjeto);

            $tbPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $arrResultado = $tbPreProjeto->spChecklistParaApresentacaoDeProposta($idPreProjeto);

            $validado = true;

            // validar planodistribuicao
            $planoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
            $verificaPlanoDistribuicao = $planoDistribuicao->validatePlanoDistribuicao($idPreProjeto);

            if (!empty($verificaPlanoDistribuicao)) {
                $arrResultado = array_merge($arrResultado, $verificaPlanoDistribuicao);
            }

            foreach ($arrResultado as &$item) {
                if ($item->Observacao == 'PENDENTE') {
                    $validado = false;

                    $validacao->dsInconsistencia = $item->dsInconsistencia;
                    $validacao->Observacao = $item->Observacao;
                    $validacao->Url = $this->obterUrlDaPendencia($item->dsChamada);
                    $listaValidacao[] = clone($validacao);
                }
            }

            if ($validado) {
                $validacao->dsInconsistencia = 'A proposta cultural n&atilde;o possui pend&ecirc;ncias';
                $validacao->Observacao = true;
                $validacao->Url = '';
                return $validacao;
            }

            return $listaValidacao;

        } catch (Exception $objExcetion) {
            throw $objExcetion;
        }
    }

    private function obterUrlDaPendencia($nome)
    {

        $url = '';
        switch ($nome) {
            case 'local_realizacao':
                $url = array('module' => 'proposta', 'controller' => 'localderealizacao', 'idPreProjeto' => $this->idPreProjeto);
                break;
            case 'orcamento':
                $url = array('module' => 'proposta', 'controller' => 'manterorcamento', 'action' => 'produtoscadastrados', 'idPreProjeto' => $this->idPreProjeto);
                break;
            case 'endereco':
                $url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'enderecos', 'id' => $this->_proposta['idagente']);
                break;
            case 'email':
                $url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'emails', 'id' => $this->_proposta['idagente']);
                break;
            case 'perfil':
                $url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'alterarvisao', 'id' => $this->_proposta['idagente']);
                break;
            case 'data_nascimento':
                $url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'info-adicionais', 'id' => $this->_proposta['idagente']);
                break;
            case 'natureza':
                $url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'natureza', 'id' => $this->_proposta['idagente']);
                break;
            case 'dirigente':
                $url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'dirigentes', 'id' => $this->_proposta['idagente']);
                break;
            case 'periodo_execucao':
                $url = array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'identificacaodaproposta', 'idPreProjeto' => $this->idPreProjeto);
                break;
            case 'plano_distribuicao':
                $url = array('module' => 'proposta', 'controller' => 'plano-distribuicao', 'action' => 'index', 'idPreProjeto' => $this->idPreProjeto);;
                break;
        }

        return $url;
    }

    /**
     * validarEnvioPropostaSemSp
     *
     * @param mixed $idPreProjeto
     * @access public
     * @return void
     */
    private function validarEnvioPropostaSemSp($idPreProjeto)
    {
        try {
            $tbPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $arrResultado = $tbPreProjeto->checklistEnvioPropostaSemSp($idPreProjeto);

            return $arrResultado;
        } catch (Zend_Exception $ex) {
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o! (semsp)" . $ex->getMessage(), "/proposta/manterpropostaincentivofiscal/index?idPreProjeto=" . $idPreProjeto, "ERROR");
        }
    }

    public function confirmarEnvioPropostaAoMincAction()
    {
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        $this->verificarPermissaoAcesso(true, false, false);
    }

    /**
     * Metodo responsavel por validar as datas do formulario
     * @param void
     * @return objeto
     */
    public function validaDatasAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        //recupera parametros
        $get = Zend_Registry::get('get');
        $dtInicio = $get->dtInicio;
        $dtFim = $get->dtFim;

        $bln = "true";
        $script = "";
        $mensagem = "";

        $objData = new Data();

        //VERIFICA SE DATA INICIO E MAIOR QUE DATA FINAL
        if (!empty($get->dtInicio) && !empty($get->dtFim) && strlen($get->dtInicio) == 10 && strlen($get->dtFim) == 10) {
            $dtTemp = explode("/", $get->dtInicio);
            $dtInicio = $dtTemp[2] . $dtTemp[1] . $dtTemp[0];

            $dtTemp = null;
            $dtTemp = explode("/", $get->dtFim);
            $dtFim = $dtTemp[2] . $dtTemp[1] . $dtTemp[0];

            if ($dtInicio > $dtFim) {
                $mensagem = "<br><font color='red'>Data de in&iacute;cio n&atilde;o pode ser maior que a data final</font>";
                $bln = "false";
            }
            if (!$objData->validarData($get->dtInicio)) {
                $mensagem = "<br><font color='red'>Data de in&iacute;cio inv&aacute;lida</font>";
                $bln = "false";
            }
            if (!$objData->validarData($get->dtFim)) {
                $mensagem = "<br><font color='red'>Data final inv&aacute;lida</font>";
                $bln = "false";
            }
            if (!$objData->validarData($get->dtInicio)) {
                $mensagem = "<br><font color='red'>O per&iacute;odo de execu&ccedil;&atilde;o de projetos de plano anual dever ser posterior ao ano vigente</font>";
                $bln = "false";
            }
        }

        //VERIFICA SE DATA INICIO E MAIOR QUE 90 DIAS DA DATA ATUAL
        if (!empty($get->dtInicio) && strlen($get->dtInicio) == 10 && !$this->view->isEditarProjeto ) {
            $dtTemp = explode("/", $get->dtInicio);
            $dtInicio = $dtTemp[2] . $dtTemp[1] . $dtTemp[0];

            $diffEmDias = $objData->CompararDatas(date("Ymd"), $dtInicio);
            if ($diffEmDias < 0 || $diffEmDias < 90) {
                $mensagem = "<br><font color='red'>A data inicial de realiza&ccedil;&atilde;o dever&aacute; ser no m&iacute;nimo 90 dias ap&oacute;s a data atual.</font>";
                $bln = "false";
            }

            if (!$objData->validarData($get->dtInicio)) {
                $mensagem = "<br><font color='red'>Data de in&iacute;cio inv&aacute;lida</font>";
                $bln = "false";
            }
            //verifica se a data inicio esta entre 01 de Fevereiro e 30 de Novembro
            //if($dtInicio >= date("Y")."0201" && $dtInicio <= date("Y")."1130"){
        }

        //VERIFICA SE DATA DO ATO E VALIDA, CASO ELA TENHA SIDO INFORMADA
        if (!empty($get->dtAto) && strlen($get->dtAto) == 10) {
            if (!$objData->validarData(trim($get->dtAto))) {
                $mensagem = "<br><font color='red'>Data tombamento inv&aacute;lida</font>";
                $bln = "false";
            }
        }


        $script = "\$('#blnDatasValidas').val(" . $bln . ");\n";
        $this->montaTela("manterpropostaincentivofiscal/mensagem.phtml", array("mensagem" => $mensagem,
            "script" => $script));
    }

    /**
     * listarpropostaAction
     *
     * @access public
     * @return void
     */
    public function listarpropostaAction()
    {
        $proposta = new Proposta_Model_DbTable_PreProjeto();
        $dadosCombo = array();
        $cpfCnpj = '';

        $rsVinculo = ($this->idResponsavel) ? $proposta->listarPropostasCombo($this->idResponsavel) : array();

        $agente = array();

        $i = 0;
        foreach ($rsVinculo as $rs) {
            $cpfCnpj = Mascara::addMaskCPF($rs->cnpjcpf);
            if (strlen(trim($rs->cnpjcpf)) > 11) {
                $cpfCnpj = Mascara::addMaskCNPJ($rs->cnpjcpf);
            }

            $dadosCombo[$i]['idAgenteProponente'] = $rs->idagente;
            $dadosCombo[$i]['CPF'] = $cpfCnpj;
            $dadosCombo[$i]['Nome'] = $rs->nomeproponente;

            $i++;
        }

        $this->view->dadosCombo = $dadosCombo;
        $this->view->idResponsavel = $this->idResponsavel;
        $this->view->idUsuario = $this->idUsuario;
        $this->view->idAgente = $this->idAgente;
    }

    public function listarPropostasAjaxAction()
    {
        $idAgente = $this->getRequest()->getParam('idagente');
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');

        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["idpreprojeto desc"];

        $idAgente = ((int)$idAgente == 0) ? $this->idAgente : (int)$idAgente;

        if (empty($idAgente)) {
            $this->_helper->json(array(
                "data" => 0,
                'recordsTotal' => 0,
                'draw' => 0,
                'recordsFiltered' => 0));
        }

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();

        $rsPreProjeto = $tblPreProjeto->propostas($this->idAgente, $this->idResponsavel, $idAgente, array(), $order, $start, $length, $search);

        $Movimentacao = new Proposta_Model_DbTable_TbMovimentacao();

        $recordsTotal = 0;
        $recordsFiltered = 0;
        $aux = array();
        if (!empty($rsPreProjeto)) {
            foreach ($rsPreProjeto as $key => $proposta) {
                $proposta->nomeproponente = utf8_encode($proposta->nomeproponente);
                $proposta->nomeprojeto = utf8_encode($proposta->nomeprojeto);
                $proposta->situacao = utf8_encode($proposta->situacao);
                $rsStatusAtual = $Movimentacao->buscarMovimentacaoProposta($proposta->idpreprojeto);
                $proposta->situacao = isset($rsStatusAtual['MovimentacaoNome']) ? utf8_encode($rsStatusAtual['MovimentacaoNome']) : '';

                $aux[$key] = $proposta;
            }
            $recordsFiltered = $tblPreProjeto->propostasTotal($this->idAgente, $this->idResponsavel, $idAgente, array(), null, null, null, $search);
            $recordsTotal = $tblPreProjeto->propostasTotal($this->idAgente, $this->idResponsavel, $idAgente);
        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0,
            'teste' =>[$this->idAgente, $this->idResponsavel, $idAgente, array(), $order, $start, $length, $search]
        ));
    }

    /**
     * Metodo consultarresponsaveis()
     * UC 89 - Fluxo FA2 - Aceitar Vinculo
     * @access public
     * @param void
     * @return void
     */
    public function consultarresponsaveisAction()
    {
        $auth = Zend_Auth::getInstance();
        $arrAuth = array_change_key_case((array)$auth->getIdentity()); // pega a autenticação

        $idUsuario = $arrAuth['idusuario'];
        $cpf = $arrAuth['cpf'];

        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $buscarpendentes = $tblAgentes->gerenciarResponsaveisListas('0', $idUsuario);
        $buscarvinculados = $tblAgentes->gerenciarResponsaveisListas('2', $idUsuario);

        $this->view->pendentes = $buscarpendentes;
        $this->view->vinculados = $buscarvinculados;
        $this->view->cpfLogado = $cpf;
    }

    /**
     * Metodo vincularpropostas()
     * UC 89 - Fluxo FA6 - Vincular Propostas
     * @access public
     * @param void
     * @return void
     */
    public function vincularpropostasAction()
    {
        $tbVinculo = new Agente_Model_DbTable_TbVinculo();
        $propostas = new Proposta_Model_DbTable_PreProjeto();

        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $dadosCombo = array();
        $rsVinculo = $tblAgentes->listarVincularPropostaCombo($this->idResponsavel);

        $i = 0;

        foreach ($rsVinculo as $rs) {
            $dadosCombo[$i]['idResponsavel'] = $rs->idUsuarioResponsavel;
            $dadosCombo[$i]['idVinculo'] = $rs->idVinculo;
            $dadosCombo[$i]['NomeResponsavel'] = $rs->NomeResponsavel;
            $i++;
        }

        //ADICIONA AO ARRAY O IDAGENTE DO USUARIO LOGADO
        $dadosIdAgentes = array($this->idAgente);

        //VERIFICA SE O USUARIO LOGADO EH DIRIGENTE DE ALGUMA EMPRESA
        $Vinculacao = new Agente_Model_DbTable_Vinculacao();
        $rsVinculucao = $Vinculacao->verificarDirigenteIdAgentes($this->cpfLogado);

        //CASO RETORNE ALGUM RESULTADO, ADICIONA OS IDAGENTE'S DE CADA UM AO ARRAY
        if (count($rsVinculucao) > 0) {
            foreach ($rsVinculucao as $value) {
                $dadosIdAgentes[] = $value->idAgente;
            }
        }

        //PROCURA AS PROPOSTAS DE TODOS OS IDAGENTE'S
        $listaPropostas = $propostas->buscarVinculadosProponenteDirigentes($dadosIdAgentes);

        $wherePropostaD['pp.idagente = ?'] = $this->idAgente;
        $wherePropostaD['pr.idprojeto IS NULL'] = '';
        $wherePropostaD['pp.idusuario <> ?'] = $this->idResponsavel;
        $listaPropostasD = $propostas->buscarPropostaProjetos($wherePropostaD);

        $this->view->responsaveis = $dadosCombo;
        $this->view->propostas = $listaPropostas;
        $this->view->propostasD = $listaPropostasD;
    }

    /**
     * Metodo vincularpropostas()
     * UC 89 - Fluxo FA8 - Vincular Propostas
     * @access public
     * @param void
     * @return void
     */
    public function vincularprojetosAction()
    {
        $tbVinculo = new Agente_Model_DbTable_TbVinculo();
        $propostas = new Proposta_Model_DbTable_PreProjeto();

        $whereProjetos['pp.idAgente = ?'] = $this->idAgente;
        $whereProjetos['pp.idUsuario <> ?'] = $this->idResponsavel;
        $whereProjetos['pr.idProjeto IS NOT NULL'] = '';
        $listaProjetos = $propostas->buscarPropostaProjetos($whereProjetos);

        $this->view->projetos = $listaProjetos;
    }

    /**
     * Metodo novoresponsavel()
     * UC 89 - Fluxo FA4 - Vincular Responsï¿½vel
     * @access public
     * @param void
     * @return void
     */
    public function novoresponsavelAction()
    {
    }

    /**
     * Metodo resnovoresponsavel()
     * Retorno do novoresponsavel
     * UC 89 - Fluxo FA4 - Vincular Responsaveis
     * @access public
     * @param void
     * @return void
     * @todo retirar html
     */
    public function respnovoresponsavelAction()
    {
        $this->_helper->layout->disableLayout();

        $cnpjcpf = Mascara::delMaskCPF($this->_request->getParam("cnpjcpf"));
        $nome = $this->_request->getParam("nome");

        $tbVinculo = new Agente_Model_DbTable_TbVinculo();

        if ((empty($cnpjcpf)) && (empty($nome))) {
            echo "<table class='tabela'>
					<tr>
					    <td class='red' align='center'>Voc&ecirc; deve preencher pelo menos um campo!</td>
					</tr>
				</table>";
            $this->_helper->viewRenderer->setNoRender(true);
        } elseif (!empty($cnpjcpf)) {
            $where['SGA.Cpf = ?'] = $cnpjcpf;
        } elseif (!empty($nome)) {
            $where['SGA.Nome like (?)'] = "%" . $nome . "%";
        }

        $busca = $tbVinculo->buscarResponsaveis($where, $this->idAgente);

        $this->view->dados = $busca;
        $this->view->dadoscount = count($busca);
        $this->view->idAgenteProponente = $this->idAgente;
    }

    /**
     * Este metodo deve estar igual à regra de negocio 1.3 da spCheckListParaApresentacaoDeProposta
     * @param $idPreProjeto
     * @return ArrayObject|bool|mixed
     */
    private function atualizarDadosPessoaJuridicaVerificandoCNAECultural($idPreProjeto)
    {
        $TbPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $proponente = $TbPreProjeto->buscarProponenteProposta($idPreProjeto);

        $tbDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $produtoPrincipal = $tbDistribuicao->findBy(array('idProjeto' => $idPreProjeto, 'stPrincipal' => 1));

        $segmentosIsentos = array('4D', '5A', '5D', '5E', '5S', '6I');

        if (isCnpjValid($proponente->CNPJCPF) && isset($produtoPrincipal['Segmento'])) {
            if (!in_array($produtoPrincipal['Segmento'], $segmentosIsentos)) {
                $cnae = $TbPreProjeto->verificarCNAEProponenteComProdutoPrincipal($idPreProjeto);

                # Se o CNAE estiver vazio, força atualização do proponente com os dados do webservice da receita
                if (empty($cnae)) {
                    $servicoReceita = new ServicosReceitaFederal();
                    $dadosPessoaJuridica = $servicoReceita->consultarPessoaJuridicaReceitaFederal($proponente->CNPJCPF, true);
                    return $dadosPessoaJuridica;
                }
                return false;
            }
        }
        return true;
    }

    public function listarPropostasArquivadasAction()
    {
        $proposta = new Proposta_Model_DbTable_PreProjeto();
        $dadosCombo = array();
        $cpfCnpj = '';

        $rsVinculo = ($this->idResponsavel) ? $proposta->listarPropostasCombo($this->idResponsavel) : array();

        $i = 0;
        foreach ($rsVinculo as $rs) {
            $cpfCnpj = Mascara::addMaskCPF($rs->cnpjcpf);
            if (strlen(trim($rs->cnpjcpf)) > 11) {
                $cpfCnpj = Mascara::addMaskCNPJ($rs->cnpjcpf);
            }

            $dadosCombo[$i]['idAgenteProponente'] = $rs->idagente;
            $dadosCombo[$i]['CPF'] = $cpfCnpj;
            $dadosCombo[$i]['Nome'] = $rs->nomeproponente;

            $i++;
        }

        $this->view->dadosCombo = $dadosCombo;
        $this->view->idResponsavel = $this->idResponsavel;
        $this->view->idUsuario = $this->idUsuario;
        $this->view->idAgente = $this->idAgente;
    }
}
