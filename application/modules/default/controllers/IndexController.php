<?php

class IndexController extends MinC_Controller_Action_Abstract
{
    protected $autenticacao;
    protected $cpfLogado;

    const COD_USUARIO_INTERNO = 1;
    const COD_USUARIO_EXTERNO = 4;
    const SISTEMA_SALIC = 21;

    public function init()
    {
        parent::init();

        $this->autenticacao = array_change_key_case((array)Zend_Auth::getInstance()->getIdentity());
        $this->cpfLogado = isset($this->autenticacao['usu_codigo']) ? $this->autenticacao['usu_identificacao'] : $this->autenticacao['cpf'];

        $this->verificarPermissoes();
    }

    private function verificarPermissoes()
    {
        $permissoesGrupo = [];
        if ($this->autenticacao) {
            if ($this->autenticacao['usu_codigo']) {
                $dbTableUsuario = new Autenticacao_Model_DbTable_Usuario();
                $grupos = $dbTableUsuario->buscarUnidades($this->autenticacao['usu_codigo'], self::SISTEMA_SALIC);

                foreach ($grupos as $grupo) {
                    $permissoesGrupo[] = $grupo->gru_codigo;
                }
                parent::perfil(self::COD_USUARIO_INTERNO, $permissoesGrupo);
            } else {
                parent::perfil(self::COD_USUARIO_EXTERNO, $permissoesGrupo);
            }
        }
    }

    public function indexAction()
    {
        if (empty($this->autenticacao)) {
            $this->redirect("/autenticacao/index/index");
        }

        if (!$this->autenticacao['usu_codigo']) {
            $this->redirect("/default/principalproponente");
        }

        $validator = new Zend_Validate_File_Exists();
        $validator->addDirectory('application/layouts/scripts');
        if (!$validator->isValid('vue.phtml')) {
            echo "Aguarde, atualizando o sistema...";
            die;
        }

        header('Content-type: text/html; charset=UTF-8');
        Zend_Layout::startMvc(array('layout' => 'vue'));
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indisponivelAction()
    {
    }

    /*
     * Pega o IP do usuario
     *
     * @return string
     */
    protected function buscarIp()
    {
        $ip = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = 'DESCONHECIDO';
        }

        return $ip;
    }

    /**
     * Envia notifica��o para o usu�rio atrav�s do aplicativo mobile.
     *
     * @param stdClass $projeto
     */
    protected function enviarNotificacao(stdClass $projeto)
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
            ->setText('Recebeu nova mensagem!')
            ->setListParameters(array('projeto' => $projeto->idPronac))
            ->send()
        ;
        echo var_dump($notification->getResponse());
        die;
    }

    /**
     * M�todo para o envio de notifica��es
     *
     * @access public
     * @param void
     * @return void
     */
    public function notificationAction()
    {
        $idPronac = $this->_request->getParam('idPronac');

        # Envia notifica��o para o usu�rio atrav�s do aplicativo mobile.
        $modelProjeto = new Projetos();
        $projeto = $modelProjeto->buscarPorPronac($idPronac?$idPronac:119079);
        $this->enviarNotificacao((object)array(
            'cpf' => $projeto->CNPJCPF,
            'pronac' => $projeto->Pronac,
            'idPronac' => $projeto->IdPRONAC
        ));
    }

    public function loginUsuarioAction()
    {
        $this->_helper->layout->disableLayout();
        $this->renderScript("/index/index.phtml");
    }


    /**
     * Efetua o login no sistema
     * @access public
     * @throws Exception
     * @throws Zend_Exception
     * @internal param $void
     */
    public function loginAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita Zend_Layout

        // recebe os dados do formulrio via post
        $post     = Zend_Registry::get('post');
        $username = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->Login)); // recebe o login sem mscaras
        $password = $post->Senha; // recebe a senha

        //Pega o IP do usuario

        $ip = $this->buscarIp();

        $tbLoginTentativasAcesso = new tbLoginTentativasAcesso();
        $LoginAttempt = $tbLoginTentativasAcesso->consultarAcessoCpf($username, $ip);


        //Pega timestamp atual
        $data = new Zend_Date();
        $timestamp = $data->getTimestamp();

        $maxTentativas = 4;
        $tempoBan = 300; // segundos

        // VERIFICA SE USUARIO ESTA BANIDO
        if (isset($LoginAttempt)) {
            $tempoLogin = $timestamp - strtotime($LoginAttempt->dtTentativa);

            if ($tempoLogin <= $tempoBan && $LoginAttempt->nrTentativa >= $maxTentativas) {
                parent::message('Acesso bloqueado, aguarde '.gmdate("i", ($tempoBan + 5 - $tempoLogin)).' minuto(s) e tente novamente!', "/", "ERROR");
            } else {
                try {
                    // valida os dados
                    if (empty($username) || empty($password)) { // verifica se os campos foram preenchidos
                        throw new Exception("Login ou Senha invalidos!");
                    } elseif (strlen($username) == 11 && !Validacao::validarCPF($username)) { // verifica se o CPF ? v?lido
                        throw new Exception("O CPF informado  invalido!");
                    } elseif (strlen($username) == 14 && !Validacao::validarCNPJ($username)) { // verifica se o CNPJ ? v?lido
                        throw new Exception("O CNPJ informado  invalido!");
                    } else {
                        // realiza a busca do usurio no banco, fazendo a autenticao do mesmo
                        $Usuario = new Usuario();
                        $buscar = $Usuario->login($username, $password);



                        if ($buscar) { // acesso permitido
                            $tbLoginTentativasAcesso->removeTentativa($username, $ip);

                            $auth = Zend_Auth::getInstance(); // instancia da autenti��o

                            // registra o primeiro grupo do usurio (pega unidade autorizada, org�o e grupo do usu�rio)
                            $Grupo   = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21); // busca todos os grupos do usu�rio

                            $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
                            $GrupoAtivo->codGrupo = $Grupo[0]->gru_codigo; // armazena o grupo na sess�o
                            $GrupoAtivo->codOrgao = $Grupo[0]->uog_orgao; // armazena o org�o na sess�o
                            $this->orgaoAtivo = $GrupoAtivo->codOrgao;

                            // redireciona para o Controller protegido
                            return $this->_helper->redirector->goToRoute(array('controller' => 'principal'), null, true);
                        } // fecha if
                        else {
                            if ($tempoLogin > $tempoBan) {
                                $tbLoginTentativasAcesso->removeTentativa($username, $ip);
                            }
                            $LoginAttempt = $tbLoginTentativasAcesso->consultarAcessoCpf($username, $ip);

                            //se nenhum registro foi encontrado na tabela Usuario, ele passa a tentar se logar como proponente.
                            //neste ponto o _forward encaminha o processamento para o metodo login do controller login, que recebe
                            //o post igualmente e tenta encontrar usuario cadastrado em SGCAcesso

                            //INSERE OU ATUALIZA O ATUAL ATTEMPT
                            if (!$LoginAttempt) {
                                $tbLoginTentativasAcesso->insereTentativa($username, $ip, $data->get('YYYY-MM-dd HH:mm:ss'));
                            } else {
                                $tbLoginTentativasAcesso->atualizaTentativa($username, $ip, $LoginAttempt->nrTentativa, $data->get('YYYY-MM-dd HH:mm:ss'));
                            }

                            $this->forward("login", "login");
                            //throw new Exception("Usurio inexistente!");
                        }
                    } // fecha else
                } // fecha try
                catch (Exception $e) {
                    parent::message($e->getMessage(), "index", "ERROR");
                }
            }
        }
    } // fecha loginAction



    /**
     * Efetua o logout do sistema
     * @access public
     * @param void
     * @return void
     */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity(); // limpa a autentica��o
        Zend_Session::destroy();
        unset($_SESSION);
        $this->redirect('index');
    } // fecha logoutAction



    /**
     * Altera o pefil do usu�rio
     * @access public
     * @param void
     * @return void
     */
    public function alterarperfilAction()
    {
        $get      = Zend_Registry::get('get');
        $codGrupo = $get->codGrupo; // grupo do usu�rio logado
        $codOrgao = $get->codOrgao; // �rg�o do usu�rio logado

        $auth       = Zend_Auth::getInstance(); // pega a autentica��o
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $GrupoAtivo->codGrupo = $codGrupo; // armazena o grupo ativo na sess�o
        $GrupoAtivo->codOrgao = $codOrgao; // armazena o �rg�o ativo na sess�o

        if ($GrupoAtivo->codGrupo == "1111" && $GrupoAtivo->codOrgao == "2222") {
            $auth   = Zend_Auth::getInstance();
            $tblSGCacesso = new Sgcacesso();
            $rsSGCacesso = $tblSGCacesso->buscar(array("Cpf = ? "=>$auth->getIdentity()->usu_identificacao))->current()->toArray();
            $objAuth = $auth->getStorage()->write((object)$rsSGCacesso);

            $_SESSION["GrupoAtivo"]["codGrupo"] = $GrupoAtivo->codGrupo;
            $_SESSION["GrupoAtivo"]["codOrgao"] = $GrupoAtivo->codOrgao;
            parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principalproponente", "INFO");
        }

        //Reescreve a sessao com o novo orgao superior
        $tblUsuario = new Usuario();
        $codOrgaoMaxSuperior = $tblUsuario->recuperarOrgaoMaxSuperior($codOrgao);
        $_SESSION['Zend_Auth']['storage']->usu_org_max_superior = $codOrgaoMaxSuperior;

        // redireciona para a p�gina inicial do sistema
        parent::message("Seu perfil foi alterado no sistema. Voc&ecirc; ter&aacute; acesso a outras funcionalidades!", "principal", "INFO");
    } // fecha alterarPerfilAction()


    public function verificamensagemusuarioAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $usuario = new Usuario();
        $pr = new Projetos();
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Agente = $usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idAgente = $Agente['idAgente'];
        $camMensagem = getcwd().'/public/mensagem/mensagem-destinatario-'.$idAgente.'.txt';
        $verificarmensagem = array();
        if (file_exists($camMensagem)) {
            $read = fopen($camMensagem, 'r');
            if ($read) {
                while (($buffer = fgets($read, 4096)) !== false) {
                    $verificarmensagem[] = json_decode($buffer, true);
                }
                fclose($read);
            }
        }
        $qtdmensagem = count($verificarmensagem);

        if ($qtdmensagem > 0) {
            $a = 0;
            $idpronac = 0;
            $mensagem = array();
            foreach ($verificarmensagem as $resu) {
                if ($resu['status']== 'N' and $resu['idpronac'] != $idpronac and $GrupoAtivo->codGrupo == $resu['perfilDestinatario']) {
                    $mensagem[$a]['idpronac'] = $resu['idpronac'];
                    $buscarpronac = $pr->buscar(array('IdPRONAC = ?'=>$resu['idpronac']))->current();
                    $mensagem[$a]['pronac'] = $buscarpronac->AnoProjeto.$buscarpronac->Sequencial;
                    $a++;
                    $idpronac = $resu['idpronac'];
                }
            }
            echo count($mensagem) > 0 ? json_encode($mensagem) : json_encode(array('error'=>true));
        } else {
            $this->_helper->json(array('error'=>true));
        }
        exit();
    }

    /**
     * montarPlanilhaOrcamentariaAction
     *
     * @access public
     * @return void
     * @author wouerner <wouerner@gmail.com>
     * @todo Verificar melhor local para esse metodo
     */
    public function montarPlanilhaOrcamentariaAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $this->view->idPerfil = $GrupoAtivo->codGrupo;

        $idPronac = $this->_request->getParam('idPronac');
        $tipoPlanilha = $this->_request->getParam('tipoPlanilha');
        $link = ($this->_request->getParam('link')) ? true : false;
        $view_edicao = ($this->_request->getParam('view_edicao')) ? true : false;
        $this->view->idPronac = $idPronac;

        $params = [];
        $params['link'] = $link;
        $params['view_edicao'] = $view_edicao;

        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
        $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPronac, $tipoPlanilha, $params);

        $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoPlanilha);
        // tipoPlanilha = 0 : Planilha Orcamentaria da Proposta
        // tipoPlanilha = 1 : Planilha Orcamentaria do Proponente
        // tipoPlanilha = 2 : Planilha Orcamentaria do Parecerista
        // tipoPlanilha = 3 : Planilha Orcamentaria Aprovada Ativa
        // tipoPlanilha = 4 : Cortes Orcamentarios Aprovados
        // tipoPlanilha = 5 : Remanejamento menor que 20%
        // tipoPlanilha = 6 : Readequacao
        // tipoPlanilha = 7 : Saldo

        $this->montaTela(
            'index/montar-planilha-orcamentaria.phtml',
            array(
            'tipoPlanilha' => $tipoPlanilha,
            'tpPlanilha' => (count($planilhaOrcamentaria)>0) ? isset($planilhaOrcamentaria[0]->tpPlanilha) ? $planilhaOrcamentaria[0]->tpPlanilha : '' : '',
            'planilha' => $planilha,
            'link' => $link
            )
        );
    }
}
