<?php

class ManterloginController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::perfil(4);

        parent::init();
        // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
    }

    public function loginAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita Zend_Layout
        $this->_helper->viewRenderer->setNoRender();


        // recebe os dados do formul�rio via post
        $post     = Zend_Registry::get('post');
        $username = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe o login sem m�scaras
        $password = $post->senha; // recebe a senha

                try {
                    // valida os dados
            if (empty($username) || empty($password)) { // verifica se os campos foram preenchidos
                parent::message("Senha ou login inv�lidos", "/manterlogin/index");
            } elseif (strlen($username) == 11 && !Validacao::validarCPF($username)) { // verifica se o CPF � v�lido
                parent::message("CPF inv�lido", "/manterlogin/index");
            } elseif (strlen($username) == 14 && !Validacao::validarCNPJ($username)) { // verifica se o CNPJ � v�lido
                parent::message("CNPJ inv�lido", "/manterlogin/index");
            } else {
                Zend_Layout::startMvc(array('layout' => 'layout_proponente'));
                // realiza a busca do usu�rio no banco, fazendo a autentica�?o do mesmo
                $Usuario = new Autenticacao_Model_Sgcacesso();
                $verificaStatus = $Usuario->buscar(array( 'Cpf = ?' => $username));
                $IdUsuario =  $verificaStatus[0]->IdUsuario;

                $sql = "SELECT tabelas.dbo.fnEncriptaSenha('" . $username . "', '" . $password . "') as senha";
                $db= Zend_Db_Table::getDefaultAdapter();
                $db->setFetchMode(Zend_DB::FETCH_OBJ);
                $senha =  $db->fetchAll($sql);
                $SenhaFinal = $senha[0]->senha;

                if ($verificaStatus[0]->Senha !=  trim($SenhaFinal)) {
                    parent::message("Login ou Senha inv�lidos!", "/manterlogin/index");
                }

                $verificaSituacao =  $verificaStatus[0]->Situacao;

                if ($verificaSituacao == 1) {
                    parent::message("Voc? logou com uma senha tempor�ria. Por favor, troque a senha.", "/manterlogin/alterarsenha?idUsuario=".$IdUsuario);
                }


                $buscar = $Usuario->login($username, $password);
                if ($buscar) { // acesso permitido
                    $usuarioLog = Autenticacao_Model_Usuario();
                    $buscarUsuLog = $usuarioLog->login(23969156149, 123456);

                    $auth = Zend_Auth::getInstance(); // instancia da autentica�?o

                    // registra o primeiro grupo do usu�rio (pega unidade autorizada, organiza e grupo do usua�io)
                                    $Grupo   = $usuarioLog->buscarUnidades($auth->getIdentity()->usu_codigo, 21); // busca todos os grupos do usu�rio

                                    $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
                                    $GrupoAtivo->codGrupo = $Grupo[0]->gru_codigo; // armazena o grupo na sess?o
                                    $GrupoAtivo->codOrgao = $Grupo[0]->uog_orgao; // armazena o org?o na sess?o

                            // redireciona para o Controller protegido
                    return $this->_helper->redirector->goToRoute(array('controller' => 'principal'), null, true);
                } // fecha if
                else {
                    parent::message("Senha ou usu�rio inv�lidos.", "/manterlogin/index");
                }
            } // fecha else
                } // fecha try
        catch (Exception $e) {
            xd($e);
            //parent::message($e->getMessage(), "index", "ERROR");
        }
    } // fecha loginAction

    public function cadastrarusuarioAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita Zend_Layout
        Zend_Layout::startMvc(array('layout' => 'layout_proponente'));


        if ($_POST) {
            $post     = Zend_Registry::get('post');
            $cpf = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe cpf
            $nome = $post->nome; // recebe o nome
            $dataNasc = $post->dataNasc; // recebe dataNasc
            $email = $post->email; // recebe email
            $emailConf = $post->emailConf; // recebe confirmacao senha

            if (trim($email) != trim($emailConf)) {
                parent::message("Digite o email certo!", "/manterlogin/cadastrarusuario");
            }

            $gerarSenha = Gerarsenha::gerasenha(15, true, true, true, true);

            $sql = "SELECT tabelas.dbo.fnEncriptaSenha('" . $cpf . "', '" . $gerarSenha . "') as senha";
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $senha =  $db->fetchAll($sql);
            $SenhaFinal = $senha[0]->senha;

            $dataFinal = data::dataAmericana($dataNasc);

            $dados = array(
                    "Cpf" => $cpf,
                    "Nome" => $nome,
                    "DtNascimento" => $dataFinal,
                    "Email" => $email,
                    "Senha" => $SenhaFinal,
                    "DtCadastro" => date("Y-m-d"),
                    "Situacao" => 1,
                    "DtSituacao" => date("Y-m-d")
                );


            $sgcAcesso = new Autenticacao_Model_Sgcacesso();
            $sgcAcessoBuscaCpf = $sgcAcesso->buscar(array("Cpf = ?" => $cpf));
            $sgcAcessoBuscaCpfArray = $sgcAcessoBuscaCpf->toArray();

            if (!empty($sgcAcessoBuscaCpfArray)) {
                parent::message("CPF j� cadastrado", "/manterlogin/cadastrarusuario");
            }

            $sgcAcessoBuscaEmail = $sgcAcesso->buscar(array("Email = ?" => $email));
            $sgcAcessoBuscaEmailArray = $sgcAcessoBuscaEmail->toArray();

            if (!empty($sgcAcessoBuscaEmailArray)) {
                parent::message("E-mail j� cadastrado", "/manterlogin/cadastrarusuario");
            }

            if (empty($sgcAcessoBuscaCpfArray) && empty($sgcAcessoBuscaEmailArray)) {
                $sgcAcessoSave = $sgcAcesso->salvar($dados);

                $mens = "<font face='Verdana' size='2'>";
                $mens .= "Ol� $nome,<br><br>";
                $mens .= "Senha....: <B>" . $SenhaFinal . "</B><br><br>";
                $mens .= "Esta � a sua senha de acesso ao Sistema de Apresenta�?o de Projetos via Web do ";
                $mens .= "Minist�rio da Cultura.<br><br>Lembramos que a mesma dever� ser ";
                $mens .= "trocada no seu primeiro acesso ao sistema.<br><br>";
                $mens .= "Esta � uma mensagem autom�tica. Por favor n?o responda.<br><br>";
                $mens .= "Para acessar o Sistema, clique no link abaixo:<br>";
                $mens .= "<a href='sistemas.cultura.gov.br/propostaweb/'>";
                $mens .= "Apresenta�?o de Projetos via Web</a><br><br>";
                $mens .= "Atenciosamente,<br><B>Minist�rio da Cultura</B></font>";


                $enviaEmail = EnviaemailController::enviaEmail($mens, "tiago.rodrigues@cultura.gov.br", $email);
                parent::message("Cadastro efetuado com sucesso.", "/manterlogin/index");
            }
        }
    }

    public function solicitarsenhaAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita Zend_Layout
        Zend_Layout::startMvc(array('layout' => 'layout_proponente'));

        if ($_POST) {
            //$enviaEmail = EnviaemailController::enviaEmail("ewrwr", "tiago.rodrigues@cultura.gov.br", "tisomar@gmail.com");
            $post     = Zend_Registry::get('post');
            $cpf = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe cpf
                    $dataNasc = data::dataAmericana($post->dataNasc); // recebe dataNasc
                    $email = $post->email; // recebe email

                    $sgcAcesso = new Autenticacao_Model_Sgcacesso();
            $sgcAcessoBuscaCpf = $sgcAcesso->buscar(array("Cpf = ?" => $cpf, "Email = ?" => $email, "DtNascimento = ?" => $dataNasc));
            $verificaUsuario = $sgcAcessoBuscaCpf->toArray();
            if (empty($verificaUsuario)) {
                parent::message("Usu�rio n?o cadastrado!", "/manterlogin/index");
            }

            $sgcAcessoBuscaCpfArray = $sgcAcessoBuscaCpf->toArray();
            $nome = $sgcAcessoBuscaCpfArray[0]['Nome'];
            $senha = Gerarsenha::gerasenha(15, true, true, true, true);

            $dados = array(
                        "IdUsuario" => $sgcAcessoBuscaCpfArray[0]['IdUsuario'],
                        "Senha" => $senha,
                        "Situacao" => 1,
                        "DtSituacao" => date("Y-m-d")
                    );
            $sgcAcessoSave = $sgcAcesso->salvar($dados);

            $endereco = "cadastro@cultura.gov.br";
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "From: cadastro@cultura.gov.br\r\n";

            $mens = "<font face='Verdana' size='2'>";
            $mens .= "Ol� " . $nome . ",<br><br>";
            $mens .= "Senha....: <B>" . $senha . "</B><br><br>";
            $mens .= "Esta � a sua nova senha de acesso ao Sistema de Apresenta�?o de Projetos via Web do ";
            $mens .= "Minist�rio da Cultura.<br><br>Lembramos que a mesma dever� ser ";
            $mens .= "trocada no seu pr�ximo acesso ao sistema.<br><br>";
            $mens .= "Esta � uma mensagem autom�tica. Por favor n?o responda.<br><br>";
            $mens .= "Para acessar o Sistema, clique no link abaixo:<br>";
            $mens .= "<a href='sistemas.cultura.gov.br/propostaweb/'>";
            $mens .= "Apresenta�?o de Projetos via Web</a><br><br>";
            $mens .= "Atenciosamente,<br><B>Minist�rio da Cultura</B></font>";

            $enviaEmail = EnviaemailController::enviaEmail($mens, "Solicita�?o de senha", "tiago.rodrigues@cultura.gov.br", $email);
            parent::message("Senha gerada com sucesso. Verifique seu email!", "/manterlogin/index");
        }
    }
    public function alterarsenhaAction()
    {

//            $this->_helper->layout->disableLayout(); // desabilita Zend_Layout


//            Zend_Layout::startMvc(array('layout' => 'layout_proponente'));



        $post     = Zend_Registry::get('post');
        $senhaAtual = $post->senhaAtual; // recebe senha atua
            $senhaNova = $post->senhaNova; // recebe senha nova
            $repeteSenha = $post->repeteSenha; // recebe repete senha


                if ($senhaNova == $repeteSenha && !empty($senhaNova) && !empty($repeteSenha)) {
                    $post     = Zend_Registry::get('post');
                    $idUsuario = $post->idUsuario;
                    $sgcAcesso = new Autenticacao_Model_Sgcacesso();
                    $sgcAcessoBuscaCpf = $sgcAcesso->buscar(array("IdUsuario = ?" => $idUsuario));
                    $cpf = $sgcAcessoBuscaCpf[0]['Cpf'];



                    $sql = "SELECT tabelas.dbo.fnEncriptaSenha('" . $cpf . "', '" . $senhaNova . "') as senha";
                    $db= Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $senha =  $db->fetchAll($sql);
                    $SenhaFinal = $senha[0]->senha;

                    $dados = array(
                            "IdUsuario" => $idUsuario,
                            "Senha" => $SenhaFinal,
                            "Situacao" => 3,
                            "DtSituacao" => date("Y-m-d")
                        );
                    $sgcAcessoSave = $sgcAcesso->salvar($dados);
                    parent::message("Senha alterada com sucesso. Verifique seu email!", "/manterlogin/index");
                }
    }

    public function logarcomoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita Zend_Layout
        Zend_Layout::startMvc(array('layout' => 'layout_proponente'));


        $buscaUsuario = new Usuariosorgaosgrupos();
        $buscaUsuarioRs = $buscaUsuario->buscarUsuariosOrgaosGrupos(
                    array('gru_status > ?' => 0, 'sis_codigo = ?' => 21),
                array( 'usu_nome asc' )
            );

        $this->view->buscaUsuario = $buscaUsuarioRs->toArray();


        if ($_POST) {


                // recebe os dados do formul�rio via post
            $post     = Zend_Registry::get('post');
            $username = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe o login sem m�scaras
        $password = $post->senha; // recebe a senha
                $idLogarComo =  $post->logarComo;


            $sql = "SELECT tabelas.dbo.fnEncriptaSenha('" . $username . "', '" . $password . "') as senha";
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $senha =  $db->fetchRow($sql);

            $SenhaFinal = $senha->senha;

            $usuario = Autenticacao_Model_Usuario();
            $usuarioRs = $usuario->buscar(
                        array('usu_identificacao = ?' => $username, 'usu_senha = ?'=> $SenhaFinal )
                );

            if (!empty($usuarioRs)) {
                $usuarioRs = $usuario->buscar(
                           array('usu_identificacao = ?' => $idLogarComo )
                   )->current();
                $senha = $usuarioRs->usu_senha;

                $Usuario = Autenticacao_Model_Usuario();
                $buscar = $Usuario->loginSemCript($idLogarComo, $senha);

                if ($buscar) { // acesso permitido
                            $auth = Zend_Auth::getInstance(); // instancia da autentica�?o

                            // registra o primeiro grupo do usu�rio (pega unidade autorizada, organiza e grupo do usua�io)
                            $Grupo   = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21); // busca todos os grupos do usu�rio

                            $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
                            $GrupoAtivo->codGrupo = $Grupo[0]->gru_codigo; // armazena o grupo na sess?o
                            $GrupoAtivo->codOrgao = $Grupo[0]->uog_orgao; // armazena o org?o na sess?o

                            // redireciona para o Controller protegido
                    return $this->_helper->redirector->goToRoute(array('controller' => 'principal'), null, true);
                } // fecha if
            }
        }
    }
}
