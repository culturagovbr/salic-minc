<?php
class ManterusuarioController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 10;

    public function init()
    {
        // verifica as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Gestor Salic
        parent::perfil(1, $PermissoesGrupo);

        parent::init();
        // chama o init() do pai GenericControllerNew
    }

    public function gerarsenhaAction()
    {
        if (filter_input(INPUT_POST, 'alterar')) {
            $senha = Gerarsenha::gerasenha(15, true, true, true, true);
            $senhaFormatada = str_replace(">", "", str_replace("<", "", str_replace("'", "", $senha)));
            $cpf = Mascara::delMaskCPF(filter_input(INPUT_POST, 'cpf'));
            $sgcAcesso = new Autenticacao_Model_Sgcacesso();
            $sgcAcessoBuscar = $sgcAcesso->buscar(array('Cpf = ?' => $cpf))->current();

            if ($sgcAcessoBuscar) {
                $senhaCriptografada = EncriptaSenhaDAO::encriptaSenha($cpf, $senhaFormatada);

                $scgAcessoDados = $sgcAcessoBuscar->toArray();
                $dados = array(
                        "IdUsuario" 	=> $scgAcessoDados['IdUsuario'],
                        "Senha" 		=> $senhaCriptografada,
                        "Situacao" 		=> 1,
                        "DtSituacao" 	=> date("Y-m-d")
                );

                $sgcAcessoSave = $sgcAcesso->salvar($dados);

                $email 		 = $scgAcessoDados['Email'];
                $assunto 	 = "Alteração da senha de acesso";
                $perfil 	 = "SALICWEB";
                $mens  		 = "Ol&aacute; " . $nome . ",<br><br>";
                $mens 		.= "Senha....: " . $senhaFormatada . "<br><br>";
                $mens 		.= "Esta &eacute; a sua senha tempor&aacute;ria de acesso ao Sistema de Apresentaç?o de Projetos via Web do ";
                $mens 		.= "Minist&eacute;rio da Cultura.<br><br>Lembramos que a mesma dever&aacute; ser ";
                $mens 		.= "trocada no seu primeiro acesso ao sistema.<br><br>";
                $mens 		.= "Esta &eacute; uma mensagem autom&aacute;tica. Por favor n?o responda.<br><br>";
                $mens 		.= "Atenciosamente,<br>Minist&eacute;rio da Cultura";

                $enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens, $perfil);
                parent::message("A senha gerada &eacute; <b>".$senhaFormatada."</b> encaminhe ao proponente.", "/principal", "ALERT");
            }
        }

        if (filter_input(INPUT_POST, 'cpf')) {
            $cpf = filter_input(INPUT_POST, 'cpf');

            $scgAcesso = new Autenticacao_Model_Sgcacesso();
            $sgcAcessoBuscar = $scgAcesso->buscar(array('Cpf = ?' => $cpf))->current();

            $json = array('error' => false);
            if ($sgcAcessoBuscar) {
                $scgAcesso = $sgcAcessoBuscar->toArray();

                $aux = [];
                foreach ($scgAcesso as $k => $item) {
                    $aux[$k] = utf8_encode($item);
                }

                $this->_helper->json($aux);
            } else {
                $dados['semdados'] = 'semdados';
                $this->_helper->json($dados);
            }
        }
    }

    public function regerarsenhaAction()
    {
        if (isset($_POST['alterar'])) {
            $cpf = Mascara::delMaskCPF($_POST['cpf']);
            $nome = $_POST['nome'];
            $senha = Gerarsenha::gerasenha(15, true, true, true, true);
            $senhaFinal = EncriptaSenhaDAO::encriptaSenha($cpf, $senha);
            $usuarios = new Autenticacao_Model_Usuario();
            $usuariosBuscar = $usuarios->buscar(array('usu_identificacao = ?' => $cpf))->current();

            if ($usuariosBuscar) {
                $usuariosDados = $usuariosBuscar->toArray();
                $dados = array(
                    "usu_codigo" 			=> $usuariosDados['usu_codigo'],
                    "usu_identificacao" 	=> $usuariosDados['usu_identificacao'],
                    "usu_senha" 			=> $senhaFinal,
                    "usu_data_atualizacao" 	=> date("Y-m-d")
                );

                $usuariosSave = $usuarios->salvar($dados);

                $email 		 = $_POST['email'];
                $assunto 	 = "Alteração da senha de acesso";
                $perfil 	 = "SALICWEB";
                $mens 		.= "Ol&aacute; " . $nome . ",<br><br>";
                $mens 		.= "Senha....: " . $senha . "<br><br>";
                $mens 		.= "Esta &eacute; a sua senha tempor&aacute;ria de acesso ao Sistema de Apresentaç?o de Projetos via Web do ";
                $mens 		.= "Minist&eacute;rio da Cultura.<br><br>Lembramos que a mesma dever&aacute; ser ";
                $mens 		.= "trocada no seu primeiro acesso ao sistema.<br><br>";
                $mens 		.= "Esta &eacute; uma mensagem autom&aacute;tica. Por favor n&atilde;o responda.<br><br>";
                $mens 		.= "Atenciosamente,<br>Minist&eacute;rio da Cultura";

                parent::message("A senha gerada &eacute; <b>".$senha."</b> encaminhe ao usuario.", "/principal", "ALERT");
                $enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens, $perfil);
            }
        }

        if (isset($_POST['cpf'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $cpf = $_POST['cpf'];

            $usuario = new Autenticacao_Model_Usuario();

            $usuariosBuscar = $usuario->pesquisarUsuarioOrgao(array('usu_identificacao = ?' => $cpf))->current();

            if (empty($usuariosBuscar)) {
                $dados['semdados'] = 'semdados';
                $json = json_encode($dados);
                echo $json;
                $this->_helper->viewRenderer->setNoRender(true);
            }
//
//            $agentes = new Agente_Model_DbTable_Agentes();
//            $agentesBuscar = $agentes->buscar(array ('CNPJCPF = ?' => $cpf))->current();
//
//            if ( empty ( $agentesBuscar ) )
//            {
//                $dados['semdados'] = 'semdados';
//                $json = json_encode($dados);
//                echo $json;
//                $this->_helper->viewRenderer->setNoRender(TRUE);
//            }
//
//            $idAgente = $agentesBuscar['idAgente'];
//
//            $internet = new Internet();
//            $internetBuscar = $internet->buscar(array ('idAgente = ?' => $idAgente))->toArray();
//
//            if ( empty ( $internetBuscar ) )
//            {
//                  $dados['sememail'] = 'sememail';
//                  $json = json_encode($dados);
//                  echo $json;
//                  $this->_helper->viewRenderer->setNoRender(TRUE);
//            }

            $json = array('error' => false);
//            if($usuariosBuscar && $agentesBuscar && $internetBuscar)
            if ($usuariosBuscar) {
                $usuarioResultado = $usuariosBuscar->toArray();
//                $mesclagem = array_merge($usuarioResultado, $internetBuscar[0]);
                $utf8Array = (array_map('utf8_encode', $usuarioResultado));
                $json = json_encode($utf8Array);
            }
            echo $json;
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function cadastrarusuarioexternoAction()
    {
        $auth = Zend_Auth::getInstance();// instancia da autenticação
        $idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sessão
        $codOrgao = $GrupoAtivo->codOrgao; //  Órgão ativo na sessão
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        $usuariosExternos = new TabelasOrgaos();
        $minc = "MinC";
        $dadosUsuariosExternos = array(
            'Tabelas.dbo.fnSiglaOrgaoTopo(o.org_codigo) = ?' => $minc,
            'o.org_tipo >= ?' => 3,
            'o.org_status <> ? ' => 0,
            'p.pid_meta_dado = ?' => 1,
            'p.pid_sequencia =  ?' => 1,



        );



        if ($_POST) {
            $cpf = Mascara::delMaskCPF($_POST['cpf']);
            $identificacao = $_POST['unidade'];
            $nome = $_POST['nome'];
            $nomeUsuario = $_POST['nomeusuario'];
            $orgao = $_POST['unidade'];

            $pessoasIdentificacoes = new Pessoaidentificacoes();
            $pessoasIdentificacoesBuscar = $pessoasIdentificacoes->pesquisarPessoasDados(array('pdd_dado = ?' => $cpf))->current();

            $usuarios = new Usuario();
            $usuariosBuscar = $usuarios->buscar(array('usu_identificacao = ?' => $cpf))->current();

            if (!empty($usuariosBuscar)) {
                parent::message("CPF já cadastrado!", "/manterusuario/cadastrarusuarioexterno", "ALERT");
            }

            $pessoa = new Pessoas();
            $pessoaBuscar = $pessoa->buscar(array(), array('pes_codigo desc'), array(1))->current();
            $idPessoa = $pessoaBuscar->pes_codigo + 1;

            if (empty($pessoasBuscar)) {
                $dados = array(
                    "pes_codigo" => $idPessoa,
                    "pes_categoria" => 0,
                    "pes_tipo" => 1,
                    "pes_esfera" => 0,
                    "pes_administracao" => 0,
                    "pes_utilidade_publica" => 0,
                    "pes_validade" => 0,
                    "pes_orgao_cadastrador" => $idorgao,
                    "pes_usuario_cadastrador" => $idusuario,
                    "pes_data_cadastramento" => date("Y-m-d")
                );
                $pessoaSalvar = $pessoa->salvarDados($dados);

                $dadosPessoa = array(
                    "pdd_pessoa" => $pessoaSalvar,
                    "pdd_meta_dado" => 2,
                    "pdd_sequencia" => 1,
                    "pdd_dado" => $cpf
                );

                $pessoaDados = new PessoaDados();
                $pessoasDadosSalvar = $pessoaDados->salvarDados($dadosPessoa);

                $dadosIdentificacao = array(
                    "pid_pessoa" => $pessoasDadosSalvar['pdd_pessoa'],
                    "pid_meta_dado" => 1,
                    "pid_sequencia" => 1,
                    "pid_identificacao" => $identificacao
                );

                $pessoaIdentificacao = new PessoaIdentificacoes();
                $pessoasIdentificacaoSalvar = $pessoaIdentificacao->salvarDados($dadosIdentificacao);

                $dadosAtualizaPessoa = array(
                    "pes_codigo" => $pessoaSalvar,
                    "pes_validade" => 2
                );
                $idPessoa = $pessoa->salvar($dadosAtualizaPessoa);

                $formataCpf = substr($cpf, 0, 6);
                $idPessoa = $pessoa->salvar($dadosAtualizaPessoa);
                $formataCpf = substr($cpf, 0, 6);
                $senha = EncriptaSenhaDAO::encriptaSenha($cpf, $formataCpf);

                $senhaFinal = $senha[0]->senha;
                $pessoaBuscar = $usuarios->buscar(array(), array('usu_codigo desc'), array(1))->current();
                $idUsuario = $pessoaBuscar->usu_codigo + 1;
                $cadastraUsuario = CadastraUsuariosDAO::cadastraUsuario($idUsuario, $idPessoa, $cpf, $nome, $nomeUsuario, $orgao, $senha);
                parent::message("Cadastro realizado com sucesso", "/manterusuario/cadastrarusuarioexterno", "CONFIRM");
            }
        }
    }

    public function permissoessalicAction()
    {
        //x($_REQUEST);
        $dados['s.sis_codigo = ?'] = 21;

        if (isset($_GET['session']) &&  isset($_GET['pag'])) {
            if (isset($_SESSION['dados'])) {
                unset($_SESSION['dados']);
            }

            $pag = 1;
            $get = Zend_Registry::get('get');
            if (isset($get->pag)) {
                $pag = $get->pag;
            }
            if (isset($get->tamPag)) {
                $this->intTamPag = $get->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $pesquisaOrgaoUsuario = new Autenticacao_Model_Usuario();
            $total = $pesquisaOrgaoUsuario->pesquisarTotalUsuarioOrgao();
            $tamanho = (($inicio+$this->intTamPag)<=$total) ? $this->intTamPag : $total- ($inicio+$this->intTamPag) ;
            $fim = $inicio + $this->intTamPag;
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            $usuariosOrgaosGrupos = new Usuariosorgaosgrupos();

            $resultadoOrgaoUsuario = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array(), array( 'gru_nome ASC', 'usu_nome asc'), $tamanho, $inicio);

            $arrPerfis = array();
            foreach ($resultadoOrgaoUsuario as $orgaoUsuario) {
                $arrPerfis[$orgaoUsuario->gru_codigo][] = $orgaoUsuario;
                $arrPerfisNomes[$orgaoUsuario->gru_codigo] = $orgaoUsuario->gru_nome;
            }

            if ($fim>$total) {
                $fim = $total;
            }
            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));

            $paginacao = array(
                                "pag"		=> $pag,
                                "total"		=> $total,
                                "inicio"	=> ($inicio+1),
                                "fim"		=> $fim,
                                "totalPag"	=> $totalPag,
                                "Itenspag"	=> $this->intTamPag,
                                "tamanho"	=> $tamanho
            );

            $this->view->paginacao = $paginacao;
            $this->view->resultadoOrgaoUsuario = $resultadoOrgaoUsuario;
            $this->view->perfis = $arrPerfis;
            $this->view->perfisNomes = $arrPerfisNomes;
            $this->view->parametrosBusca = $_REQUEST;

            //Envia para tela que ira gerar todos os registro em PDF
            if (isset($_POST['imprimirResumo']) && !empty($_POST['imprimirResumo'])  && $_POST['imprimirResumo'] == 'html') {
                $resultadoOrgaoUsuario2 = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array(), array( 'gru_nome ASC', 'usu_nome asc'));

                $arrPerfis = array();
                foreach ($resultadoOrgaoUsuario2 as $orgaoUsuario) {
                    $arrPerfis2[$orgaoUsuario->gru_codigo][] = $orgaoUsuario;
                    $arrPerfisNomes2[$orgaoUsuario->gru_codigo] = $orgaoUsuario->gru_nome;
                }
                $this->_forward(
                    'gerar-pdf-permissao-salic',
                                null,
                                null,
                                array('resultadoOrgaoUsuario2'=>$resultadoOrgaoUsuario,
                                      'perfis2'=>$arrPerfis,
                                      'perfisNomes2'=>$arrPerfisNomes,
                                      'gerar'=>'html'
                                     )
                                );
            }
        } elseif ($_POST || isset($_SESSION['dados'])) {
            if ($_POST) {
                $get = Zend_Registry::get('post');

                if (!empty($_POST['cpf'])) {
                    if ($get->identificacao == "igual") {
                        $dados['usu_identificacao = ?'] = $cpf = Mascara::delMaskCPF($_POST['cpf']);
                    } else {
                        $dados['usu_identificacao <> ?'] = $cpf = Mascara::delMaskCPF($_POST['cpf']);
                    }
                }

                //nome
                if (!empty($_POST['nome'])) {
                    if ($get->nomePesquisa == "inicio") {
                        $dados['usu_nome like ?'] = $get->nome."%";
                    } elseif ($get->nomePesquisa == "igual") {
                        $dados['usu_nome = ? '] = $get->nome;
                    } elseif ($get->nomePesquisa == "contenha") {
                        $dados['usu_nome LIKE ? '] = "%".$get->nome."%";
                    } else {
                        $dados['usu_nome <> ?'] = $get->nome;
                    }
                }

                //lotacao
                if (!empty($_POST['unidade'])) {
                    if ($get->lotacao == "igual") {
                        $dados['usu_orgao = ?'] = $get->unidade;
                    } else {
                        $dados['usu_orgao <> ?'] = $get->unidade;
                    }
                }

                //telefone
                if (!empty($_POST['telefone'])) {
                    if ($get->tel == "inicio") {
                        $dados['usu_telefone = ?'] = $get->telefone;
                    } elseif ($get->tel == "igual") {
                        $dados['usu_telefone = ?'] = $get->telefone;
                    } elseif ($get->tel == "contenha") {
                        $dados['usu_telefone LIKE ?'] = "%".$get->telefone."%";
                    } else {
                        $dados['usu_telefone <> ?'] = $get->telefone;
                    }
                }

                //unidade autorizada
                if (!empty($_POST['unidadeAutorizada'])) {
                    if ($get->unidadeaut == "igual") {
                        $dados['tabelas.dbo.fnEstruturaOrgao(ug.uog_orgao, 0) = ?'] = $get->unidadeAutorizada;
                    } else {
                        $dados['tabelas.dbo.fnEstruturaOrgao(ug.uog_orgao, 0) <> ?'] = $get->unidadeAutorizada;
                    }
                }

                //perfil
                if (!empty($_POST['perfil'])) {
                    if ($get->perfilPesquisa == "igual") {
                        $dados['gru_codigo = ?'] = $get->perfil;
                    } else {
                        $dados['gru_codigo <> ?'] = $get->perfil;
                    }
                }

                //status
                if (@$_POST['status'] != '') {
                    if ($get->statusPesquisa == "igual") {
                        $dados['uog_status = ?'] = $get->status;
                    } else {
                        $dados['uog_status <> ?'] = $get->status;
                    }
                }
            } else {
                $dados = $_SESSION['dados'];
            }

            $usuariosOrgaosGrupos = new Usuariosorgaosgrupos();

            $pag = 1;
            $get = Zend_Registry::get('get');
            if (isset($get->pag)) {
                $pag = $get->pag;
            }
            if (isset($get->tamPag)) {
                $this->intTamPag = $get->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $pesquisaOrgaoUsuario = new Autenticacao_Model_Usuario();

            if (!empty($dados)) {
                $total = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla($dados, array( 'gru_nome ASC', 'usu_nome asc'), array(), array(), true);
            } else {
                $total = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array(), array( 'gru_nome ASC', 'usu_nome asc'), array(), array(), true);
            }

            $tamanho = (($inicio+$this->intTamPag)<=$total) ? $this->intTamPag : $total- ($inicio+$this->intTamPag) ;
            $fim = $inicio + $this->intTamPag;
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

            if (!empty($dados)) {
                $_SESSION['dados'] = $dados;
            }

            if (isset($_SESSION['dados']) || isset($dados)) {
                $resultadoOrgaoUsuario = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla($dados, array( 'gru_nome ASC', 'usu_nome asc'), $tamanho, $inicio);
            } else {
                $resultadoOrgaoUsuario = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array(), array( 'gru_nome ASC', 'usu_nome asc'), $tamanho, $inicio);
            }

            $arrPerfis = array();

            foreach ($resultadoOrgaoUsuario as $orgaoUsuario):
                    $arrPerfis[$orgaoUsuario->gru_codigo][] = $orgaoUsuario;
            $arrPerfisNomes[$orgaoUsuario->gru_codigo] = $orgaoUsuario->gru_nome;
            endforeach;

            if ($fim>$total) {
                $fim = $total;
            }
            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));

            $paginacao = array(
                                "pag"		=> $pag,
                                "total"		=> $total,
                                "inicio"	=> ($inicio+1),
                                "fim"		=> $fim,
                                "totalPag"	=> $totalPag,
                                "Itenspag"	=> $this->intTamPag,
                                "tamanho"	=> $tamanho
            );

            $this->view->paginacao = $paginacao;
            $this->view->resultadoOrgaoUsuario = $resultadoOrgaoUsuario;
            $this->view->perfis = $arrPerfis;
            $this->view->parametrosBusca = $_REQUEST;


            if (!empty($arrPerfisNomes)) {
                $this->view->perfisNomes = $arrPerfisNomes;
            } else {
                $this->view->perfisNomes = "";
            }

            //Envia para tela que ira gerar todos os registro em PDF
            if (isset($_POST['imprimirResumo']) && !empty($_POST['imprimirResumo'])  && $_POST['imprimirResumo'] == 'html') {
                if (isset($_SESSION['dados']) || isset($dados)) {
                    $resultadoOrgaoUsuario2 = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla($dados, array( 'gru_nome ASC', 'usu_nome asc'));
                } else {
                    $resultadoOrgaoUsuario2 = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array(), array( 'gru_nome ASC', 'usu_nome asc'));
                }


                foreach ($resultadoOrgaoUsuario2 as $orgaoUsuario):
                    $arrPerfis2[$orgaoUsuario->gru_codigo][] = $orgaoUsuario;
                $arrPerfisNomes2[$orgaoUsuario->gru_codigo] = $orgaoUsuario->gru_nome;
                endforeach;

                if (!empty($arrPerfisNomes)) {
                    $perfisNomes2 = $arrPerfisNomes2;
                } else {
                    $perfisNomes2 = "";
                }

                $this->_forward(
                    'gerar-pdf-permissao-salic',
                                null,
                                null,
                                array('resultadoOrgaoUsuario2'=>$resultadoOrgaoUsuario2,
                                      'perfis2'=>$arrPerfis2,
                                      'perfisNomes2'=>$arrPerfisNomes2,
                                      'gerar'=>'html'
                                     )
                                );
            }
        } else {
            $pag = 1;
            $get = Zend_Registry::get('get');
            if (isset($get->pag)) {
                $pag = $get->pag;
            }
            if (isset($get->tamPag)) {
                $this->intTamPag = $get->tamPag;
            }
            $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
            $pesquisaOrgaoUsuario = new Autenticacao_Model_Usuario();
            $total = $pesquisaOrgaoUsuario->pesquisarTotalUsuarioOrgao();
            $tamanho = (($inicio+$this->intTamPag)<=$total) ? $this->intTamPag : $total- ($inicio+$this->intTamPag) ;
            $fim = $inicio + $this->intTamPag;
            $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
            $usuariosOrgaosGrupos = new Usuariosorgaosgrupos();

            $resultadoOrgaoUsuario = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array(), array( 'gru_nome ASC', 'usu_nome asc'), $tamanho, $inicio);

            $arrPerfis = array();

            foreach ($resultadoOrgaoUsuario as $orgaoUsuario):
                $arrPerfis[$orgaoUsuario->gru_codigo][] = $orgaoUsuario;
            $arrPerfisNomes[$orgaoUsuario->gru_codigo] = $orgaoUsuario->gru_nome;
            endforeach;

            if ($fim>$total) {
                $fim = $total;
            }
            $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));

            $paginacao = array(
                                "pag"		=>$pag,
                                "total"		=>$total,
                                "inicio"	=>($inicio+1),
                                "fim"		=>$fim,
                                "totalPag"	=>$totalPag,
                                "Itenspag"	=>$this->intTamPag,
                                "tamanho"	=>$tamanho
            );

            $this->view->paginacao = $paginacao;
            $this->view->resultadoOrgaoUsuario = $resultadoOrgaoUsuario;
            $this->view->perfis = $arrPerfis;
            $this->view->perfisNomes = $arrPerfisNomes;
            $this->view->parametrosBusca = $_REQUEST;

            //Envia para tela que ira gerar todos os registro em PDF
            if (isset($_POST['imprimirResumo']) && !empty($_POST['imprimirResumo'])  && $_POST['imprimirResumo'] == 'html') {
                $resultadoOrgaoUsuario2 = $usuariosOrgaosGrupos->buscarUsuariosOrgaosGruposSigla(array(), array( 'gru_nome ASC', 'usu_nome asc'));

                foreach ($resultadoOrgaoUsuario2 as $orgaoUsuario):
                    $arrPerfis2[$orgaoUsuario->gru_codigo][] = $orgaoUsuario;
                $arrPerfisNomes2[$orgaoUsuario->gru_codigo] = $orgaoUsuario->gru_nome;
                endforeach;

                $this->_forward(
                    'gerar-pdf-permissao-salic',
                                null,
                                null,
                                array('resultadoOrgaoUsuario2'=>$resultadoOrgaoUsuario2,
                                      'perfis2'=>$arrPerfis2,
                                      'perfisNomes2'=>$arrPerfisNomes2,
                                      'gerar'=>'html'
                                     )
                                );
            }
        }
    }

    public function gerarPdfPermissaoSalicAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        $this->_response->clearHeaders();

        $dados = $this->_getAllParams();
        //x($dados['perfisNomes2']);

        $this->view->resultadoOrgaoUsuario2 = $dados['resultadoOrgaoUsuario2'];
        $this->view->perfis2                = $dados['perfis2'];
        $this->view->perfisNomes2           = $dados['perfisNomes2'];
    }

    public function gerarPdfNewAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        //$post = Zend_Registry::get('post');

        
        $pdf = new PDFCreator($_POST['html']);

        $pdf->gerarPdf();
    }

    public function gerarTelaPdfAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        //$post = Zend_Registry::get('post');


        $pdf = new PDFCreator($_POST['html']);

        $pdf->gerarPdf();
    }

    public function perfissalicwebAction()
    {
        $auth = Zend_Auth::getInstance();// instancia da autenticacao

        $idusuario         = $auth->getIdentity()->usu_codigo;
        $idorgao           = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
            $codGrupo   = $GrupoAtivo->codGrupo; //  Grupo ativo na sessao
            $codOrgao   = $GrupoAtivo->codOrgao; //  Órgao ativo na sessao

            $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        $usuario = new Usuariosorgaosgrupos();
        $listaUsuario = new Autenticacao_Model_Usuario();
        $resultadoUsuario = $listaUsuario->buscarUsuario();
        $resultadoUnidade = $usuario->buscarUsuariosOrgaosGruposUnidades(array('sis_codigo = ?' => 21), array('org_sigla ASC'));
        $resultadoGrupo   = $usuario->buscarUsuariosOrgaosGruposSistemas(array('sis_codigo = ?' => 21), array('gru_nome'));
        // $resultadoUnidade = $usuario->buscarUsuariosOrgaosGruposUnidades()->toArray(array('sis_codigo = ?' => 21));

        $verificaCoordenadorGeral = new Autenticacao_Model_Usuario();
        $buscaCoordenadorGeral = $verificaCoordenadorGeral->ECoordenadorGeral($idusuario);

        $buscaCoordenador = $verificaCoordenadorGeral->ECoordenador($idusuario);

        $this->view->resultadoUsuario = $resultadoUsuario;
        $this->view->resultadoUnidade = $resultadoUnidade;
        $this->view->resultadoGrupo   = $resultadoGrupo;


        if ($_POST) {
            $usuarioEnviado 	= $_POST['usuario'];
            $unidade 			= $_POST['unidade'];
            $grupo 				= $_POST['perfil'];
            $status 			= $_POST['status'];

            $usuarioEnviadooff 	= $_POST['usuariooff'];
            $unidadeoff 		= $_POST['unidadeoff'];
            $grupooff 			= $_POST['perfiloff'];
            $statusoff 			= $_POST['statusoff'];

            $usuario = new Usuariosorgaosgrupos();

            $where['uog_usuario = ?'] = $usuarioEnviado;
            $where['uog_orgao   = ?'] = $unidade;
            $where['uog_grupo   = ?'] = $grupo;

            $buscardados = $usuario->buscar($where);

            if ($_GET['editar'] ==  "sim") {
                $dadosAntigos = array(
                                    'uog_usuario = ?' 	=> $usuarioEnviadooff,
                                    'uog_orgao   = ?' 	=> $unidadeoff,
                                    'uog_grupo   = ?' 	=> $grupooff
                    );


                $delete = $usuario->delete($dadosAntigos);

                if (count($buscardados) > 0) {
                    $dadosAtual = array(
                                            'uog_usuario = ?' 	=> $usuarioEnviado,
                                            'uog_orgao   = ?' 	=> $unidade,
                                            'uog_grupo   = ?' 	=> $grupo
                        );

                    $delete = $usuario->delete($dadosAntigos);
                }

                $dados = array(
                                    'uog_usuario' 	=> $usuarioEnviado,
                                    'uog_orgao' 	=> $unidade,
                                    'uog_grupo' 	=> $grupo,
                                    'uog_status' 	=> $status
                    );

                $insere = $usuario->inserir($dados);

                parent::message("Altera&ccedil;&atilde;o realizada com sucesso!", "/manterusuario/permissoessalic", "CONFIRM");
            } else {
                if (count($buscardados) > 0) {
                    parent::message("Perfil j&aacute; cadastrado!", "/manterusuario/permissoessalic", "CONFIRM");
                }

                $dados = array(
                                'uog_usuario' 	=> $usuarioEnviado,
                                'uog_orgao' 	=> $unidade,
                                'uog_grupo' 	=> $grupo,
                                'uog_status' 	=> $status
                    );

                $insere = $usuario->inserir($dados);

                parent::message("Cadastro realizado com sucesso!", "/manterusuario/permissoessalic", "CONFIRM");
            }
        }

        if ($_GET) {
            $codigo  = $_GET['id'];
            $perfil  = $_GET['perfil'];
            $estado  = $_GET['estado'];
            $unidade = $_GET['unidade'];

            $usuario = new Usuariosorgaosgrupos();
            $buscarUsuario = $usuario->buscarUsuariosOrgaosGrupos(array('usu_codigo = ?' => $codigo))->toArray();
            $totalArray = count($buscarUsuario);

            $this->view->usu_nome = $buscarUsuario[0]['usu_nome'];
            $this->view->usu_codigo = $buscarUsuario[0]['usu_codigo'];
            $this->view->usu_identificacao = $buscarUsuario[0]['usu_identificacao'];

            foreach ($buscarUsuario as $tmpUsuario) {
                if ($unidade == $tmpUsuario['org_codigo']) {
                    $this->view->org_codigo = $tmpUsuario['org_codigo'];
                    $this->view->org_sigla = $tmpUsuario['org_sigla'];
                    break;
                }
            }

            $this->view->estado = $estado;
            $this->view->perfil = $perfil;
            $perfilUsuario = $usuario->buscarUsuariosOrgaosGrupos(array('usu_codigo = ?' => $codigo, "gru_codigo = ? "=>$perfil))->current();
            $this->view->perfil_nome = $perfilUsuario->gru_nome;
        }

        //============Trazer a Unidade para cadastrar o Perfil/Usuario externo, faz um tratamento para nao trazer órgao em branco=================
        $orgaos = new Orgaos();
        $this->view->orgaos  = $orgaos->buscar(array('Sigla != ?'=>''), array('Sigla'));
    }

    public function excluirPermissaoAction()
    {
        $get = Zend_Registry::get("get");
        $arrDados = array("uog_usuario = ? "=>$get->usuario, "uog_orgao = ? "=>$get->orgao, "uog_grupo = ? "=>$get->grupo);
        $tblUsuarioxOrgaoxGrupos = new Usuariosorgaosgrupos();
        $rs = $tblUsuarioxOrgaoxGrupos->delete($arrDados);
        if ($rs) {
            parent::message("Exclus&atilde;o realizada com sucesso!", "/manterusuario/permissoessalic?pag={$get->pag}", "CONFIRM");
        } else {
            parent::message("Falha ao deletar registro.", "/manterusuario/permissoessalic?pag={$get->pag}", "ERROR");
        }
    }

    public function localizarperfisAction()
    {
        $usuarios = new Usuariosorgaosgrupos();

        $unidades = $usuarios->buscarUnidades(array('s.sis_codigo = ?' => 21), array('1'));
        $this->view->lotacao = $unidades;

        $perfil = $usuarios->buscarPerfil(array('s.sis_codigo = ?' => 21), array('g.gru_nome asc','g.gru_codigo'));
        $this->view->perfil = $perfil;

        $unidadesAutorizadas = $usuarios->buscarUnidadesAutorizadas(array('s.sis_codigo = ?' => 21), array('org_siglaautorizado'));
        $this->view->unidadesAutorizadas = $unidadesAutorizadas;
    }

    public function gerarhtmlAction()
    {
        if (isset($_POST['html'])) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $pdf = new PDF($_POST['html'], 'pdf');
            $pdf->gerarRelatorio();
        }
    }
}
