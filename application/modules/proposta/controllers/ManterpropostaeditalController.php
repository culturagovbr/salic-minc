<?php
class Proposta_ManterpropostaeditalController extends Proposta_GenericController
{

    /**
     * Reescreve o metodo init()
     */
    public function init()
    {
        parent::init();

        //VALIDA ITENS DO MENU (Documento pendentes)
        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;

            $tableDocumentosExigidos = new Proposta_Model_DbTable_DocumentosExigidos();
            $this->view->documentosPendentes = $tableDocumentosExigidos->buscarDocumentoPendente($this->idPreProjeto);

            if (!empty($this->view->documentosPendentes)) {
                $verificarmenu = 1;
                $this->view->verificarmenu = $verificarmenu;
            } else {
                $verificarmenu = 0;
                $this->view->verificarmenu = $verificarmenu;
            }

            //(Enviar Proposta ao MinC , Excluir Proposta)
            $mov = new Proposta_Model_DbTable_TbMovimentacao();
            $movBuscar = $mov->buscar(array('idProjeto = ?' => $this->idPreProjeto), array('idMovimentacao desc'), 1, 0)->current();

            if (isset($movBuscar->Movimentacao) && $movBuscar->Movimentacao != 95) {
                $enviado = 'true';
                $this->view->enviado = $enviado;
            } else {
                $enviado = 'false';
                $this->view->enviado = $enviado;
            }
        }

        $this->verificarPermissaoAcesso(true, false, false);
        $this->validarEdicaoProposta();
    }

    /**
     * indexAction
     *
     * @access public
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * editalAction
     *
     * @access public
     * @return void
     */
    public function editalAction()
    {
        $get = Zend_Registry::get('get');
        $idpreprojeto = $get->idpreprojeto;
        $array = array();
        $array['idUsuario'] = $this->idUsuario;
        $tbedital = ManterpropostaeditalDAO::buscaredital($array);
        $array = array();
        $i = 0;
        /**
         * @todo: escolher melhor forma para carregamento de dados.
         */
        foreach ($tbedital as $valores) {
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF]['idPreProjeto'] = $valores->idPreProjeto;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF]['CNPJCPF'] = $valores->CNPJCPF;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF]['idAgente'] = $valores->idAgente;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['idPreProjeto'] = $valores->idPreProjeto;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['NomeProjeto'] = $valores->NomeProjeto;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['Mecanismo'] = $valores->Mecanismo;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['MecanismoDesc'] = $valores->MecanismoDesc;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['stTipoDemanda'] = $valores->stTipoDemanda;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['idEdital'] = $valores->idEdital;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['Edital'] = $valores->Edital;
            $array[$valores->Proponente . ' : ' . $valores->CNPJCPF][$i]['CNPJCPF'] = $valores->CNPJCPF;
            $i++;
        }
        $this->view->edital = $array;
    }

    /**
     * dadospropostaeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function dadospropostaeditalAction()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['idPreProjeto']) && !empty($params['idPreProjeto'])) {

            /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
            $this->verificarPermissaoAcesso(true, false, false);

            $_SESSION['idPreProjeto'] = $params['idPreProjeto'];

            $where = array();
            $where['p.stTipoDemanda NOT LIKE ?'] = 'NA';
            $where['NOT EXISTS (?)'] = new Zend_Db_Expr('select * from SAC.dbo.projetos pr where p.idPreProjeto = pr.idProjeto');
            $where['p.stestado = ?'] = 1;
            $where['fd.idClassificaDocumento not in (?)'] = array(23, 24, 25);
            $where['p.idPreProjeto = ?'] = $params['idPreProjeto'];

            $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $dados = $tblPreProjeto->buscarPropostaEditalCompleto($where);

            $get = Zend_Registry::get("get");
            $tableDocumentosExigidos = new Proposta_Model_DbTable_DocumentosExigidos();
            $this->view->documentosPendentes = $tableDocumentosExigidos->buscarDocumentoPendente($get->idPreProjeto);

            if (!empty($this->view->documentosPendentes)) {
                $verificarmenu = 1;
                $this->view->verificarmenu = $verificarmenu;
            } else {
                $verificarmenu = 0;
                $this->view->verificarmenu = $verificarmenu;
            }

            $mov = new Proposta_Model_DbTable_TbMovimentacao();
            $movBuscar = $mov->buscar(array('idProjeto = ?' => $get->idPreProjeto), array('idMovimentacao desc'), 1, 0)->current();

            if (isset($movBuscar->Movimentacao) && $movBuscar->Movimentacao != 95) {
                $enviado = 'true';
                $this->view->enviado = $enviado;
            } else {
                $enviado = 'false';
                $this->view->enviado = $enviado;
            }

            if ($dados) {
                $this->view->idPreProjeto = $dados[0]->idPreProjeto;
                $this->view->nomeAgente = $dados[0]->nomeAgente;
                $this->view->nomeProjeto = $dados[0]->NomeProjeto;
                $this->view->resumoProjeto = $dados[0]->ResumoDoProjeto;
                $this->view->idAgente = $dados[0]->idAgente;
                $this->view->AgenciaBancaria = $dados[0]->AgenciaBancaria;
                $this->view->DtInicioDeExecucao = $dados[0]->DtInicioDeExecucao;
                $this->view->DtFinalDeExecucao = $dados[0]->DtFinalDeExecucao;
                $this->view->idEdital = $dados[0]->idEdital;
            } else {
                $this->view->mensagem = 'Pré Projeto não encontrado.';
                $this->view->tpmensagem = "msgERROR";
            }

            $ag = new Agente_Model_DbTable_Agentes();
            $verificarvinculo = $ag->buscarAgenteVinculoProponente(array('vprp.idPreProjeto = ?' => $dados[0]->idPreProjeto,
                                                                         'vprp.siVinculoProposta = ?' => 2));
            if (count($verificarvinculo) > 0) {
                if ($verificarvinculo[0]->siVinculo != 2) {
                    $this->view->siVinculoProponente = true;
                } else {
                    $this->view->siVinculoProponente = false;
                }
            }

            $tblVinculo = new Agente_Model_DbTable_TbVinculo();

            $arrBuscaP['VP.idPreProjeto = ?'] 			= $dados[0]->idPreProjeto;
            $arrBuscaP['VI.idUsuarioResponsavel = ?'] 	= $this->idResponsavel;
            $rsVinculoP = $tblVinculo->buscarVinculoProponenteResponsavel($arrBuscaP);

            $arrBuscaN['VI.siVinculo IN (0,2)'] 		= '';
            $arrBuscaN['VI.idUsuarioResponsavel = ?'] 	= $this->idResponsavel;
            $rsVinculoN = $tblVinculo->buscarVinculoProponenteResponsavel($arrBuscaN);

            $this->view->listaProponentes = $rsVinculoN;
            $this->view->dadosVinculo = $rsVinculoP;
        } else {
            $dados = array();
            if (isset($params['idAgente']) && !empty($params['idAgente'])) {
                $dados = ManterpropostaeditalDAO::buscarNomeAgente($_REQUEST);
            }
            if ($dados) {
                $this->view->nomeAgente = $dados[0]->Descricao;
                $this->view->idAgente = $_REQUEST['idAgente'];
                $this->view->idEdital = $_REQUEST['idEdital'];
            } else {
                $this->view->mensagem = "Agente n&atilde;o encontrado";
                $this->view->tpmensagem = "msgERROR";
            }
        }
        if (isset($_REQUEST['mensagem'])) {
            $this->view->mensagem = $_REQUEST['mensagem'];
            $this->view->tpmensagem = $_REQUEST['tpmensagem'];
        }
        $tbeditalpreprojeto = ManterpropostaeditalDAO::buscarpreprojeto();
        $this->view->editalpreprojeto = $tbeditalpreprojeto;
    }

    /**
     * inserirdadospropostaeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function inserirdadospropostaeditalAction()
    {
        $array = array('mensagem' => '');
        $params = $this->getRequest()->getParams();

        if ($params['idAgente'] && $params['idEdital'] && $params['nomeProjeto'] && $params['resumoProjeto']) {
            try {
                if (strlen(trim($_REQUEST['resumoProjeto'])) > 1000) {
                    $array['mensagem'] = 'Quantidade de caracteres maior que o permitido. Limite: 1000 caracteres.';
                    $array['tpmensagem'] = 'msgERROR';
                    $this->redirect('/manterpropostaedital/dadospropostaedital?idPreProjeto=' . $params['idPreProjeto'] . '&idAgente=' . $_REQUEST['idAgente'] . '&idEdital=' . $_REQUEST['idEdital'] . '&mensagem=' . $array['mensagem'] . '&tpmensagem=' . $array['tpmensagem']);
                }

                $array = array();
                $array['Mecanismo'] = 2; // Adicionado, Edital deve ser Mecanismo 2
                $array['idAgente'] = $params['idAgente'];
                $array['idEdital'] = $params['idEdital'];
                $array['AgenciaBancaria'] = $params['agencia'];
                $datainicio = explode('/',$params['dtIniExec']);
                $array['DtInicioDeExecucao'] = $datainicio['2'].'-'.$datainicio['1'].'-'.$datainicio['0'];
                $datafim = explode('/',$params['dtFimExec']);
                $array['DtFinalDeExecucao'] = $datafim['2'].'-'.$datafim['1'].'-'.$datafim['0'];
                $array['NomeProjeto'] = TratarString::escapeString($params['nomeProjeto']);
                $array['stTipoDemanda'] = 'ED';
                $array['ResumoDoProjeto'] = trim(TratarString::escapeString($params['resumoProjeto']));

                // Salvar o responsavel
                $array['idUsuario'] = $this->idResponsavel;

                if (isset($params['idPreProjeto']) ) {
                    $array['idPreProjeto'] 	= $params['idPreProjeto'];
                    $dados = ManterpropostaeditalDAO::alterarDadosProposta($array);
                    $array['mensagem'] 		= 'Alteração realizada com sucesso!';
                    $array['tpmensagem'] 	= 'msgCONFIRM';
                    $array['mensagem'] 		= htmlspecialchars($array['mensagem']);
                    parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/manterpropostaedital/dadospropostaedital?idPreProjeto=" . $array['idPreProjeto'], "CONFIRM");
                } else {
                    $dados = ManterpropostaeditalDAO::inserirProposta($array);
                    $array['idPreProjeto'] = $dados;
                    $tblMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
                    $dados = array(	"idProjeto" 		=> $array['idPreProjeto'],
                                    "Movimentacao" 		=> "95", //Status = Proposta com Proponente
                                    "DtMovimentacao" 	=> date("Y/m/d H:i:s"),
                                    "stEstado" 			=> "0",
                                    "Usuario" 			=> $this->idUsuario); //$this->view->usuario->usu_codigo;

                    $tblMovimentacao->salvar($dados);


                    /*******************************************************************************************/
                    // Salvando os dados na TbVinculoProposta
                    $tbVinculoDAO 		  = new Agente_Model_DbTable_TbVinculo();
                    $tbVinculoPropostaDAO = new Agente_Model_DbTable_TbVinculoProposta();

	                $whereVinculo['idUsuarioResponsavel = ?'] = $this->idResponsavel;
	                $whereVinculo['idAgenteProponente   = ?'] = $params['idAgente'];
	                $vinculo = $tbVinculoDAO->buscar($whereVinculo);

	                if(count($vinculo) == 0)
	                {
						$dadosV = array( 'idAgenteProponente'		=> $params['idAgente'],
	    				   				'dtVinculo' 				=> MinC_Db_Expr::date(),
	    				   				'siVinculo' 				=> 2,
	    				   				'idUsuarioResponsavel' 		=> $this->idResponsavel
	    				);

                        $insere = $tbVinculoDAO->inserir($dadosV);
                    }


                    $vinculo2 = $tbVinculoDAO->buscar($whereVinculo);
                    if (count($vinculo2) > 0) {
                        $novosDadosV = array('idVinculo' 			=> $idVinculo = $vinculo2[0]->idVinculo,
                                             'idPreProjeto' 		=> $array['idPreProjeto'],
                                             'siVinculoProposta' 	=> 2
                        );

                        $insere = $tbVinculoPropostaDAO->inserir($novosDadosV, false);
                    }

                    $array['mensagem'] = 'Cadastro realizado com sucesso!';
                    $array['tpmensagem'] = 'msgCONFIRM';
                }
            } catch (Zend_Exception $ex) {
                parent::message("Não foi possível realizar a operação!", "/proposta/manterpropostaincentivofiscal/listar-propostas", "ERROR");
            }
        } else {
            $array['mensagem'] 	 = 'Dados incorretos.';
            $array['tpmensagem'] = 'Erro!';
        }
        $this->redirect('/proposta/manterpropostaedital/dadospropostaedital?idPreProjeto=' . $array['idPreProjeto'] . '&mensagem=' . $array['mensagem'] . '&tpmensagem=' . $array['tpmensagem']);
    }

    /**
     * localderealizacaoeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function localderealizacaoeditalAction()
    {
    }

    /**
     * responderquestionarioeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function responderquestionarioeditalAction()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['idPreProjeto'])) {

            /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
            $this->verificarPermissaoAcesso(true, false, false);

            $where = array();
            $where['p.stTipoDemanda NOT LIKE ?'] = 'NA';
            $where['NOT EXISTS (?)'] = new Zend_Db_Expr('select * from SAC.dbo.projetos pr where p.idPreProjeto = pr.idProjeto');
            $where['p.stestado = ?'] = 1;
            $where['fd.idClassificaDocumento not in (?)'] = array(23, 24, 25);
            $where['p.idPreProjeto = ?'] = $params['idPreProjeto'];

            $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $dados = $tblPreProjeto->buscarPropostaEditalCompleto($where);
        } else {
            parent::message("Projeto não informado!", "/proposta/manterpropostaincentivofiscal/listar-propostas", "ERROR");
        }
        if ($dados) {
            $this->view->idPreProjeto = $dados[0]->idPreProjeto;
            $this->view->nomeAgente = $dados[0]->nomeAgente;
            $this->view->nomeProjeto = $dados[0]->NomeProjeto;
            $this->view->resumoProjeto = $dados[0]->ResumoDoProjeto;
            $this->view->idAgente = $dados[0]->idAgente;
            $this->view->idEdital = $dados[0]->idEdital;
            $this->view->dados = $dados[0];
        } else {
            parent::message("Pré Projeto não encontrado!", "/proposta/manterpropostaincentivofiscal/listar-propostas", "ERROR");
        }
        $this->view->idUsuario = $this->idUsuario;
    }

    /**
     * @name enviararquivoeditalAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  02/10/2016
     */
    public function enviararquivoeditalAction()
    {
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        $this->verificarPermissaoAcesso(true, false, false);

        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

        $tbl = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
        $rs = $tbl->buscarDocumentos(array("idprojeto = ?" => $idPreProjeto));
        $this->view->arquivosProposta = $rs;

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $dadosProjeto = $tblPreProjeto->findBy(array('idPreProjeto' => $idPreProjeto));

        $tbA = new Proposta_Model_DbTable_TbDocumentosAgentes();
        $rsA = $tbA->buscarDadosDocumentos(array("idagente = ?" => $dadosProjeto['idAgente']));
        $this->view->arquivosProponente = $rsA;

        $this->view->idPreProjeto = $idPreProjeto;

//        $tblDocumentos = new DocumentosExigidos();
//
//        $where = array(
//            'stUpload = ?' => true,
//            'Area = ?' => '4'
//        );
//
//        $listaDeArquivos = $tblDocumentos->buscar($where, 'Descricao desc');
//
//        foreach ($listaDeArquivos  as $item) {
//            $novoItem = $item->toArray();
//
//            if(in_array($item->Codigo, array_column($rs, 'codigodocumento')))
//                $novoItem['Anexado'] = true;
//            else
//                $novoItem['Anexado'] = false;
//            $novaLista[] = $novoItem;
//        }
//        $this->view->documentosObrigatorios = $novaLista;
    }

    /**
     * listararquivosAction
     *
     * @access public
     * @return void
     */
    public function listararquivosAction()
    {
        $params = $this->getRequest()->getParams();

        $opcao = !empty($params['classificao']) ? $params['classificao'] : -1;
        $where = array(
            'Opcao = ?' => $opcao
        );

        $tblDocumentos = new DocumentosExigidos();
        $Documento = $tblDocumentos->buscar($where, 'Descricao desc');
        foreach ($Documento as $doc) {
            echo utf8_encode('<option value="' . $doc->Codigo . '" >' . $doc->Descricao . '</option>');
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     *
     * @name incluirAnexoAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  29/09/2016
     */
    public function incluirAnexoAction()
    {
        if ($this->getRequest()->isPost()) {
            $arrPost = ($this->getRequest()->getPost());
            $mapperTbDocumentoAgentes = new Proposta_Model_TbDocumentosAgentesMapper();
            $file = new Zend_File_Transfer();
            if ($mapperTbDocumentoAgentes->saveCustom($arrPost, $file)) {
                parent::message("Arquivo anexado com sucesso!", "proposta/manterpropostaedital/enviararquivoedital?idPreProjeto=" . $arrPost['idPreProjeto'] . "&edital=" . $arrPost['edital'], "CONFIRM");
            } else {
                parent::message($mapperTbDocumentoAgentes->getMessage(), "proposta/manterpropostaedital/enviararquivoedital?idPreProjeto=" . $arrPost['idPreProjeto'] . "&edital=" . $arrPost['edital'], "ALERT");
            }
        } else {
            parent::message('Dados incorretos', "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
        }
    }

    /**
     * incluirarquivoAction
     *
     * @access public
     * @return void
     */
    public function incluirarquivoAction()
    {
        $post = Zend_Registry::get('post');

        if ($_FILES['arquivo']['tmp_name']) {
            $idArquivo = $this->anexararquivo();
            $dados = array(
                'idArquivo' => $idArquivo,
                'idTipoDocumento' => 0, //$post->tipodocumento,
                'dsDocumento' => $post->observacao
            );
            $tabela = new tbDocumento();
            $idDocumento = $tabela->inserir($dados);

            if ($idDocumento) {
                $idDocumento = $tabela->ultimodocumento(array('idArquivo = ? ' => $idArquivo));
                $idDocumento = $idDocumento->idDocumento;

                $dados = array(
                    'idTipoDocumento' => 0, //$post->tipodocumento,
                    'idDocumento' => $idDocumento,
                    'idProposta' => $post->idPreProjeto,
                    'stAtivoDocumentoProposta' => 0
                );
                $DocumentoProposta = new tbDocumentoProposta();
                $DocumentoProposta->inserir($dados);
            } else {
                parent::message("Falha ao anexar", "proposta/manterpropostaedital/enviararquivoedital?idPreProjeto=" . $post->idPreProjeto . "&edital=" . $post->edital, "ERROR");
            }
            parent::message("Documento anexado com sucesso!", "proposta/manterpropostaedital/enviararquivoedital?idPreProjeto=" . $post->idPreProjeto . "&edital=" . $post->edital, "CONFIRM");
        } else {
            parent::message("Documento n&atilde;o informado", "proposta/manterpropostaedital/enviararquivoedital?idPreProjeto=" . $post->idPreProjeto . "&edital=" . $post->edital, "ERROR");
        }
    }

    /**
     * anexararquivo
     *
     * @access private
     * @return void
     */
    private function anexararquivo()
    {
        // pega as informacoes do arquivo
        $idUltimoArquivo = 'null';
        $post = Zend_Registry::get('post');

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
            if ($arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                parent::message("O arquivo não pode ser maior do que 10MB!", "proposta/manterpropostaedital/enviararquivoedital?idPreProjeto=" . $post->idPreProjeto . "&edital=" . $post->edital, "ALERT");
            }
            // cadastra dados do arquivo
            $dadosArquivo = array(
                'nmArquivo' => $arquivoNome,
                'sgExtensao' => $arquivoExtensao,
                'dsTipoPadronizado' => $arquivoTipo,
                'nrTamanho' => $arquivoTamanho,
                'dtEnvio' => new Zend_Db_Expr('GETDATE()'),
                'dsHash' => $arquivoHash,
                'stAtivo' => 'I');
            $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

            // pega o id do ultimo arquivo cadastrado
            $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
            $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

            // cadastra o binario do arquivo
            $dadosBinario = array(
                'idArquivo' => $idUltimoArquivo,
                'biArquivo' => $arquivoBinario);
            $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);
        }
        return $idUltimoArquivo;
    }

    /**
     * enviarpropostaaominceditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function enviarpropostaaominceditalAction()
    {
    }

    /**
     * manteragentesAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function manteragentesAction()
    {
    }

    /**
     * dadosproponenteenderecoeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function dadosproponenteenderecoeditalAction()
    {
        $get = Zend_Registry::get('get');
        $idpreprojeto = $get->idpreprojeto;
        $cpf = $get->cpf;
        $tbedital = ManterpropostaeditalDAO::buscaredital();
        $this->view->edital = $tbedital;
        $tbeditalpreprojeto = ManterpropostaeditalDAO::buscarpreprojeto();
        $this->view->editalpreprojeto = $tbeditalpreprojeto;
        $tbeendereco = ManterpropostaeditalDAO::buscaendereco($cpf);
        $this->view->endereco = $tbeendereco;
    }

    /**
     * documentospendenteseditalAction
     *
     * @access public
     * @return void
     * @deprecated  nao utilizado no salic atual
     */
    public function documentospendenteseditalAction()
    {
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        $this->verificarPermissaoAcesso(true, false, false);

        $get = Zend_Registry::get("get");
        //$get = Zend_Registry::get("get");
        //$this->view->documentosPendentes = AnalisarPropostaDAO::buscarDocumentoPendente($get->idPreProjeto);

        $model = new Proposta_Model_DbTable_DocumentosExigidos();
        $this->view->documentosPendentes = $model->buscarDocumentoPendente($get->idPreProjeto);
    }

    /**
     * msnenviadasaominceditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function msnenviadasaominceditalAction()
    {
    }

    /**
     * acompanhesuapropostaeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function acompanhesuapropostaeditalAction()
    {
    }

    /**
     * alterardtnascimentoeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function alterardtnascimentoeditalAction()
    {
    }

    /**
     * dadosenderecoeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function dadosenderecoeditalAction()
    {
    }

    /**
     * novodadosenderecoeditalAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function novodadosenderecoeditalAction()
    {
    }

    /**
     * exluirpropostaAction
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto, inicialmente seria em manterpropostaincentivofiscal
     */
    public function exluirpropostaAction()
    {

        /* =============================================================================== */
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        /* =============================================================================== */
        $this->verificarPermissaoAcesso(true, false, false);

        $get = Zend_Registry::get("get");
        $idPreProjeto = $get->idPreProjeto;

        //BUSCANDO REGISTRO A SER ALTERADO
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $rsPreProjeto = $tblPreProjeto->find($idPreProjeto)->current();
        //altera Estado da proposta
        $rsPreProjeto->stEstado = 0;

        if ($rsPreProjeto->save()) {
            parent::message("Exclus&atilde;o realizada com sucesso!", "/proposta/manterpropostaincentivofiscal/listarproposta", "CONFIRM");
        } else {
            parent::message("N&atilde;o foi possível realizar a opera&ccedil;&atilde;o!", "/proposta/manterpropostaincentivofiscal/listarproposta", "ERROR");
        }
    }

    /**
     * editallocalizarAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function editallocalizarAction()
    {
        $this->view->idAgente = $_REQUEST['idAgente'];
    }

    /**
     * editalconfirmarAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function editalconfirmarAction()
    {
        if ($_REQUEST['idAgente']) {
            if (isset($_POST['dtEditalInicial']) || isset($_POST['nrEdital']) || isset($_POST['dtEditalFinal']) || isset($_POST['dtInicoInscricaoInicial']) || isset($_POST['dtInicoInscricaoFinal']) || isset($_POST['dtFinalInscricaoInicial']) || isset($_POST['dtFinalInscricaoFinal']) || isset($_POST['Classificacao']) || isset($_POST['nmEdital'])) {
                $dados = ManterpropostaeditalDAO::buscaEditalConfirmarAvancada($_POST);
            } else {
                $dados = ManterpropostaeditalDAO::buscaEditalConfirmar();
            }
            $array = array();
            $i = 0;
            foreach ($dados as $dado) {
                $array[$dado->TipoFundo][$i]['idEdital'] = $dado->idEditalTb;
                $array[$dado->TipoFundo][$i]['NrEditalTb'] = $dado->NrEditalTb;
                $array[$dado->TipoFundo][$i]['Ano'] = $dado->Ano;
                $array[$dado->TipoFundo][$i]['Unidade'] = $dado->Unidade;
                $array[$dado->TipoFundo][$i]['nmDocumento'] = $dado->nmDocumento;
                $array[$dado->TipoFundo][$i]['DtEditalTb'] = $dado->DtEditalTb;
                $array[$dado->TipoFundo][$i]['dtIniFase'] = $dado->dtIniFase;
                $array[$dado->TipoFundo][$i]['Classificacao'] = $dado->Classificacao;
                $array[$dado->TipoFundo][$i]['dtFimFase'] = $dado->dtFimFase;
                $array[$dado->TipoFundo][$i]['Objeto'] = $dado->Objeto;
                $array[$dado->TipoFundo][$i]['idAgente'] = $_REQUEST['idAgente'];
                $i++;
            }
            $this->view->dados = $array;
            $this->view->idAgente = $_REQUEST['idAgente'];
        } else {
            $this->view->mensagem = "CPF não encontrado.";
        }
    }

    /**
     * editalconfirmarlocalizarAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function editalconfirmarlocalizarAction()
    {
        if ($_REQUEST['idEdital']) {
            $this->view->dado = ManterpropostaeditalDAO::buscaEditalConfirmarLocalizar(array('idEdital' => $_REQUEST['idEdital']));
        }
        if (!$this->view->dado || !$_REQUEST['idEdital']) {
            $this->view->message = 'Edital não encontrado.';
        } else {
            $this->view->dado = $this->view->dado[0];
        }
    }

    /**
     * editalnovoAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function editalnovoAction()
    {
        $post = Zend_Registry::get('post');
        $idPreProjeto = $post->idPreProjeto;
        $this->view->idAgente = $post->idAgente;
        $arrDados = array();
        //$arrDados['idUsuario'] = 31041;
        $arrDados['idUsuario'] = $this->idUsuario;
        $tbedital = ManterpropostaeditalDAO::buscaredital($arrDados);
        $this->view->edital = $tbedital;
        $tbeditalpreprojeto = ManterpropostaeditalDAO::buscarpreprojeto();
        $this->view->editalpreprojeto = $tbeditalpreprojeto;
    }

    /**
     * editalresumoAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function editalresumoAction()
    {
        $array = array();
        $this->view->dados = ManterpropostaeditalDAO::listarEditalResumo($array);
    }

    /**
     * gerarpdfAction
     *
     * @access public
     * @return void
     * @todo retirar html da controller
     */
    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();


        $output = '
			<style>
				th{
                                background:#ABDA5D;
                                color:#3A7300;
                                text-transform:uppercase;
                                font-size:14px;
        			font-weight: bold;
        			font-family: sans-serif;
        			height: 16px;
        			line-height: 16px;
        		}
        		td{
        			color:#000;
        			font-size:14px;
        			font-family: sans-serif;
        			height: 14px;
        			line-height: 14px;
        		}
        		.blue{
        			color: blue;
        		}
        		.red{
        			color: red;
        		}
        		.orange{
        			color: orange;
        		}
        		.green{
        			color: green;
        		}

        		.direita{
        			text-align: right;
        		}

        		.centro{
        			text-align: center;
        		}

			</style>';



        $output .= $_POST['html'];

        $patterns = array();
        $patterns[] = '/<table.*?>/is';
        $patterns[] = '/<thead>/is';
        $patterns[] = '/<\/thead>/is';
        $patterns[] = '/<tbody>/is';
        $patterns[] = '/<\/tbody>/is';
        $patterns[] = '/<col.*?>/is';
        $patterns[] = '/<a.*?>/is';

        $replaces = array();
        $replaces[] = '<table cellpadding="0" cellspacing="1" border="1" width="97%" align="center">';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';

        $output = preg_replace($patterns, $replaces, $output);

        $pdf = new PDF($output, 'pdf');
        $pdf->gerarRelatorio('h');
    }

    /**
     * enviarPropostaAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function enviarPropostaAction()
    {

        /* =============================================================================== */
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        /* =============================================================================== */
        $this->verificarPermissaoAcesso(true, false, false);

        //recupera parametros
        $get = Zend_Registry::get('get');
        $idPreProjeto = $get->idPreProjeto;

        $erro = "";
        $msg = "";

        if (!empty($idPreProjeto)) {
            $arrResultado = $this->validarEnvioPropostaAoMinc($idPreProjeto);
            if ($arrResultado['erro'] !== false) {
                $arrResultado['erro'] = 1;
            } elseif ($arrResultado['erro'] !== true) {
                $arrResultado['erro'] = 0;
            }
            //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY
            $this->montaTela("proposta/manterpropostaedital/enviarproposta.phtml", array("acao" => $this->_urlPadrao . "/proposta/manterpropostaedital/salvar",
                "erro" => $arrResultado['erro'],
                "resultado" => $arrResultado));
        } else {
            parent::message("Necessário informar o número da proposta.", "/proposta/manterpropostaedital/index", "ERROR");
        }
    }

    /**
     * validarEnvioPropostaAoMinc
     *
     * @param mixed $idPreProjeto
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function validarEnvioPropostaAoMinc($idPreProjeto)
    {

        //BUSCA DADOS DO PROJETO
        $arrBusca = array();
        $arrBusca['idPreProjeto = ?'] = $idPreProjeto;
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $rsPreProjeto = $tblPreProjeto->buscar($arrBusca)->current();

        /* ======== VERIFICA TODAS AS INFORMACOES NECESSARIAS AO ENVIO DA PROPOSTA ======= */

        $arrResultado = array();

        $arrResultado['erro'] = false;

        /*         * ******* MOVIMENTACAO ******** */
        $tblMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
        $rsMovimentacao = $tblMovimentacao->buscar(array("idProjeto = ?" => $idPreProjeto), array("idMovimentacao DESC"))->current();

        if (count($rsMovimentacao) > 0) {
            if ($rsMovimentacao->Movimentacao != 95) {
                $arrResultado['erro'] = true;
                $arrResultado['movimentacao']['erro'] = false;
                $arrResultado['movimentacao']['msg'] = "A Proposta Cultural encontra-se no Minist&eacute;rio da Cultura";
            }
        }

        /*         * ******* DADOS DO PROPONENTE ******** */
        $tblProponente = new Proponente();

        $tblAgente = new Agente_Model_DbTable_Agentes();
        $rsProponente = $tblAgente->buscarAgenteENome(array("a.idAgente = ?" => $rsPreProjeto->idAgente))->current();

        $regularidade = Regularidade::buscarSalic($rsProponente->CNPJCPF);

        $dadosEndereco = Agente_Model_EnderecoNacionalDAO::buscarEnderecoNacional($rsPreProjeto->idAgente);

        $objEmail = new Agente_Model_Email();
        $dadosEmail = $objEmail->buscar($rsPreProjeto->idAgente);

        $dadosDirigente = Agente_Model_ManterAgentesDAO::buscarVinculados(null, null, null, null, $rsPreProjeto->idAgente);

        $tblLocaisRealizacao = new Proposta_Model_DbTable_Abrangencia();
        $dadosLocais = $tblLocaisRealizacao->buscar(array("a.idProjeto" => $idPreProjeto, "a.stAbrangencia" => 1));

        if (count($rsProponente) > 0) {

        //VERIFICA SE O PROPONENTE ESTA VINCULADO
            $vinculoProponente = new Agente_Model_DbTable_TbVinculoProposta();
            $whereProp['VP.idPreProjeto = ?'] 		= $idPreProjeto;
            $whereProp['VP.siVinculoProposta = ?'] 	= 2;
            $rsVinculo = $vinculoProponente->buscarResponsaveisProponentes($whereProp);

            if ($rsVinculo[0]->siVinculo == 2) {
                $arrResultado['erro'] = false;
                $arrResultado['vinculoproponente']['erro'] = false;
                $arrResultado['vinculoproponente']['msg'] = "Vinculo do Proponente REGULAR";
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['vinculoproponente']['erro'] = true;
                $arrResultado['vinculoproponente']['msg'] = "Vinculo do Proponente IRREGULAR";
            }

            //REGULARIDADE DO PROPONENTE
            if (count($regularidade) > 0) {
                if ($regularidade[0]->Habilitado == "S") {
                    $arrResultado['regularidadeproponente']['erro'] = false;
                    $arrResultado['regularidadeproponente']['msg'] = "Proponente em situa&ccedil;&atilde;o REGULAR no Minist&eacute;rio da Cultura - <font color='green'>OK</font>";
                } else {
                    $arrResultado['erro'] = true;
                    $arrResultado['regularidadeproponente']['erro'] = true;
                    $arrResultado['regularidadeproponente']['msg'] = "Proponente em situa&ccedil;&atilde;o IRREGULAR no Minist&eacute;rio da Cultura - <font color='red'>PENDENTE</font>";
                }
            } else {
                $arrResultado['regularidadeproponente']['erro'] = false;
                $arrResultado['regularidadeproponente']['msg'] = "Proponente em situa&ccedil;&atilde;o REGULAR no Minist&eacute;rio da Cultura - <font color='green'>OK</font>";
            }

            //E-MAIL
            $blnEmail = false;
            if (count($dadosEmail) > 0) {
                foreach ($dadosEmail as $email) {
                    if ($email->Status == 1) {
                        $blnEmail = true;
                    }
                }
                if ($blnEmail === false) {
                    $arrResultado['erro'] = true;
                    $arrResultado['email']['erro'] = true;
                    $arrResultado['email']['msg'] = "E-mail do proponente inexistente - <font color='red'>PENDENTE</font>";
                } else {
                    $arrResultado['email']['erro'] = false;
                    $arrResultado['email']['msg'] = "E-mail do proponente - <font color='green'>OK</font>";
                }
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['email']['erro'] = true;
                $arrResultado['email']['msg'] = "E-mail do proponente inexistente - <font color='red'>PENDENTE</font>";
            }

            //ENDERECO
            $blnEndCorrespondencia = false;
            if (count($dadosEndereco) > 0) {
                foreach ($dadosEndereco as $endereco) {
                    if ($endereco->Status == 1) {
                        $blnEndCorrespondencia = true;
                    }
                }
                if ($blnEndCorrespondencia === false) {
                    $arrResultado['erro'] = true;
                    $arrResultado['endereco']['erro'] = true;
                    $arrResultado['endereco']['msg'] = "Dados cadastrais do proponente inexistente ou n&atilde;o h&aacute; endere&ccedil;o para correspond&ecirc;ncia selecionado";
                } else {
                    $arrResultado['endereco']['erro'] = false;
                    $arrResultado['endereco']['msg'] = "Dados cadastrais do proponente - <font color='green'>OK</font>";
                }
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['endereco']['erro'] = true;
                $arrResultado['endereco']['msg'] = "Dados cadastrais do proponente inexistente ou n&atilde;o h&aacute; endere&ccedil;o para correspond&ecirc;ncia selecionado";
            }

            //NATUREZA
            if ($rsProponente->TipoPessoa == 1) {
                $tblNatureza = new Natureza();
                $dadosNatureza = $tblNatureza->buscar(array("idAgente = ?" => $rsPreProjeto->idAgente));

                if (count($dadosNatureza) > 0) {
                    $arrResultado['dirigente']['erro'] = false;
                    $arrResultado['dirigente']['msg'] = "Natureza do proponente - <font color='green'>OK</font>";
                } else {
                    $arrResultado['erro'] = true;
                    $arrResultado['dirigente']['erro'] = true;
                    $arrResultado['dirigente']['msg'] = "Natureza do proponente - <font color='red'>PENDENTE</font>";
                }
            }

            //DIRIGENTE
            if ($rsProponente->TipoPessoa == 1) {
                if (count($dadosDirigente) > 0) {
                    $arrResultado['dirigente']['erro'] = false;
                    $arrResultado['dirigente']['msg'] = "Cadastro de Dirigente - <font color='green'>OK</font>";
                } else {
                    $arrResultado['erro'] = true;
                    $arrResultado['dirigente']['erro'] = true;
                    $arrResultado['dirigente']['msg'] = "Cadastro de Dirigente - <font color='red'>PENDENTE</font>";
                }
            }

            //LOCAIS DE RALIZACAO
            if (count($dadosLocais) > 0) {
                $arrResultado['locaisrealizacao']['erro'] = false;
                $arrResultado['locaisrealizacao']['msg'] = "Local de realiza&ccedil;&atilde;o da proposta - <font color='green'>OK</font>";
            } else {
                $arrResultado['erro'] = true;
                $arrResultado['locaisrealizacao']['erro'] = true;
                $arrResultado['locaisrealizacao']['msg'] = "O Local de realiza&ccedil;&atilde;o da proposta n&atilde;o foi preenchido - <font color='red'>PENDENTE</font>";
            }
        } else {
            $arrResultado['erro'] = true;
            $arrResultado['proponente']['erro'] = true;
            $arrResultado['proponente']['msg'] = "Dados cadastrais do proponente inexistente ou n&atilde;o h&aacute; endere&ccedil;o para correspond&ecirc;ncia selecionado";
        }

        return $arrResultado;
    }

    /**
     * confirmarEnvioPropostaAoMincAction
     *
     * @access public
     * @return void
     * @todo nao utilizado no salic atual
     */
    public function confirmarEnvioPropostaAoMincAction()
    {

        /* =============================================================================== */
        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        /* =============================================================================== */
        $this->verificarPermissaoAcesso(true, false, false);

        //recupera parametros
        $get = Zend_Registry::get('get');
        $idPreProjeto = $get->idPreProjeto;
        $valida = $get->valida;
        $idTecnico = null;
        $rsTecnicos = array();
        $idOrgaoSuperior = null;

        if (isset($_REQUEST['edital'])) {
            $edital = "&edital=s";
        } else {
            $edital = "";
        }

        if (!empty($idPreProjeto) && $valida == "s") {
            $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $tblAvaliacao = new Proposta_Model_AnalisarPropostaDAO();

            //recupera dados do projeto
            $rsPreProjeto = $tblPreProjeto->find($idPreProjeto)->current();

            // Recuperando edital
            $tblEdital = new Edital();
            $rsEdital = $tblEdital->buscar(array("idEdital = ?" => $rsPreProjeto->idEdital))->current();
            $idOrgaoSuperior = $rsEdital->idOrgao;
            //verifica se a proposta ja foi recebida por um tecnico
            $avaliacao = $tblAvaliacao->verificarAvaliacao($idPreProjeto);

            //SE A PROPOSTA JA FOI AVALIADA POR UM TECNICO E O MESMO ESTIVER ATIVO, ATRIBUI A AVALIACAO A ELE
            if (count($avaliacao) > 0) {
                if ($avaliacao[0]->ConformidadeOK == 0 || $avaliacao[0]->ConformidadeOK == 1) {
                    //verifica se o tecnico esta habilitado
                    $arrBusca = array();
                    $arrBusca['sis_codigo = '] = 21;
                    $arrBusca['gru_codigo = '] = 92;
                    $arrBusca['usu_codigo = '] = $avaliacao[0]->idTecnico;
                    $analista = AdmissibilidadeDAO::buscarAnalistas($arrBusca);

                    if (count($analista) > 0) {
                        if ($analista[0]->uog_status == 1) {
                            $idTecnico = $avaliacao[0]->idTecnico;
                        } else {
                            $idTecnico = null;
                            //recupera todos os tecnicos do orgao para fazer o balanceamento
                            $rsTecnicos = $tblPreProjeto->recuperarTecnicosOrgao($idOrgaoSuperior);
                        }
                    } else {
                        $idTecnico = null;
                        //recupera todos os tecnicos do orgao para fazer o balanceamento
                        $rsTecnicos = $tblPreProjeto->recuperarTecnicosOrgao($idOrgaoSuperior);
                    }
                } else {
                    $idTecnico = $avaliacao[0]->idTecnico;
                }
            } else {
                //recupera todos os tecnicos do orgao para fazer o balanceamento
                $rsTecnicos = $tblPreProjeto->recuperarTecnicosOrgao($idOrgaoSuperior);
            }


            //SE A PROPOSTA NUNCA FOI AVALIADA OU SE O TECNICO Q A AVALIOU ESTA DESABILITADO FAZ O BALANCEAMENTO
            if (count($rsTecnicos) > 0 && $idTecnico == null) {
                $arrTecnicosPropostas = array();

                foreach ($rsTecnicos as $tecnico) {
                    $rsAvaliacaoPorTecnico = $tblAvaliacao->recuperarQtdePropostaTecnicoOrgao($tecnico->uog_orgao, $tecnico->usu_codigo);
                    $arrTecnicosPropostas[$tecnico->usu_codigo] = $rsAvaliacaoPorTecnico[0]->qtdePropostas;
                }
                asort($arrTecnicosPropostas);

                //PEGA O ID DO TECNICO Q TEM MENOS PROPOSTAS
                $ct = 1;
                foreach ($arrTecnicosPropostas as $chave => $valor) {
                    if ($ct == 1) {
                        $idTecnico = $chave;
                        $ct++;
                    } else {
                        break;
                    }
                }
            }

            //INICIA PERSISTENCIA DOS DADOS
            if ($idTecnico) {
                $db = Zend_Db_Table::getDefaultAdapter();
                //$db->beginTransaction();

                try {

                    //======== PERSXISTE DADOS DA MOVIMENTACAO ==========/
                    //atualiza status da ultima movimentacao
                    //$tblAvaliacao->updateEstadoMovimentacao($idPreProjeto);
                    $tblMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
                    //Mudando as movimentacoes anteriores para o stEstado = 1
                    $rsRetorno = $tblMovimentacao->update(array("stEstado" => 1), "idProjeto = {$idPreProjeto}");
                    //Pegando ultima movimentacao
                    $rsMov = $tblMovimentacao->buscar(array("idProjeto = ?" => $idPreProjeto), array("idMovimentacao DESC"), 1, 0)->current();

                    if (count($rsMov) > 0) {
                        $ultimaMovimentacao = $rsMov->Movimentacao;
                        //Pegando penultima movimentacao
                        $rsMov = $tblMovimentacao->buscar(array("idProjeto = ?" => $idPreProjeto, "Movimentacao <> ?" => $ultimaMovimentacao), array("idMovimentacao DESC"), 1, 0)->current();

                        $movimentacaoDestino = 96;
                        if (count($rsMov) > 0) {
                            $movimentacaoDestino = $rsMov->Movimentacao;
                        }
                    } else {
                        $movimentacaoDestino = 96;
                    }

                    //PERSISTE DADOS DA MOVIMENTACAO

                    $tblMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
                    $dados = array("idProjeto" => $idPreProjeto,
                        "Movimentacao" => $movimentacaoDestino, //satus
                        //"DtMovimentacao" => date("Y/m/d H:i:s"),
                        "DtMovimentacao" => new Zend_Db_Expr('GETDATE()'),
                        "stEstado" => "0", //esta informacao estava fixa trigger
                        "Usuario" => $this->idUsuario);

                    $tblMovimentacao->inserir($dados);

                    //======== PERSXISTE DADOS DA AVALIACAO ==========/
                    //atualiza status da ultima avaliacao
                    $tblAvaliacao->updateEstadoAvaliacao($idPreProjeto);

                    $dados = array();
                    $dados['idPreProjeto'] = $idPreProjeto;
                    $dados['idTecnico'] = $idTecnico; //$this->idUsuario;
                    $dados['dtEnvio'] = "'" . date("Y/m/d H:i:s") . "'";
                    $dados['dtAvaliacao'] = "'" . date("Y/m/d H:i:s") . "'";
                    $dados['avaliacao'] = "";
                    $dados['conformidade'] = 9;
                    $dados['estado'] = 0;

                    //PERSISTE DADOS DA AVALIACAO PROPOSTA
                    $tblAvaliacao->inserirAvaliacao($dados);

                    parent::message("A Proposta foi enviado com sucesso ao Minist&eacute;rio da Cultura!", "/proposta/manterpropostaedital/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "CONFIRM");
                    die();
                } catch (Exception $e) {
                    parent::message("O Projeto n&atilde;o foi enviado ao Minist&eacute;rio da Cultura!", "/proposta/manterpropostaedital/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "ERROR");
                    die();
                }
            } else { //fecha IF se encontrou tecnicos para enviar a proposta
                parent::message("A Proposta n&atilde;o foi enviado ao Minist&eacute;rio da Cultura. T&eacute;cnico n&atilde;o localizado", "/proposta/manterpropostaedital/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "ERROR");
                die();
            }
        } else {
            parent::message("A Proposta n&atilde;o foi enviado ao Minist&eacute;rio da Cultura.", "/proposta/manterpropostaedital/enviar-proposta?idPreProjeto=" . $idPreProjeto . $edital, "ERROR");
        }
    }

    /**
     * validarAgenciaBancariaAction
     *
     * @access public
     * @return void
     */
    public function validarAgenciaBancariaAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $ba = new BancoAgencia;
        $validaragencia = $ba->buscar(array('Agencia = ?'=>$_POST['agencia']))->count();
        if ($validaragencia > 0) {
            $this->_helper->json(array('error'=>false));
        } else {
            $this->_helper->json(array('error'=>true));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * excluiranexoAction
     * Metodo para efetuar a exclusao do arquivo
     *
     * @access public
     * @return void
     */
    public function excluiranexoAction()
    {
        if (isset($_GET['idArquivo']) && !empty($_GET['idArquivo']) && isset($_GET['idPreProjeto']) && !empty($_GET['idPreProjeto']) && isset($_GET['tipoDocumento']) && !empty($_GET['tipoDocumento'])) :
            if ($_GET['tipoDocumento'] == 'proposta') {
                $tbDocumentosPreProjeto = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
                $file = $tbDocumentosPreProjeto->findBy(array('iddocumentospreprojetos' => $_GET['idArquivo']));
                $tbDocumentosPreProjeto->apagar(array('iddocumentospreprojetos = ?' => $_GET['idArquivo']));
            } else {
                $tbDocumentosAgentes = new Proposta_Model_DbTable_TbDocumentosAgentes();
                $file = $tbDocumentosAgentes->findBy(array('iddocumentosagentes' => $_GET['idArquivo']));
                $tbDocumentosAgentes->apagar(array('iddocumentosagentes = ?' => $_GET['idArquivo']));
            }
        $filePath = APPLICATION_PATH . '/..' . $file['imdocumento'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if (isset($_GET['request'])) {
            $request = $_GET['request'];
        } else {
            $request = 'proposta/manterpropostaedital/enviararquivoedital?idPreProjeto=' . $_GET['idPreProjeto'];
        }

        parent::message('Exclus&atilde;o efetuada com sucesso!', $request, 'CONFIRM');
        endif;
    }
}
