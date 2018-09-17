<?php
abstract class MinC_Controller_Action_Abstract extends Zend_Controller_Action
{
    protected $_msg;
    protected $_url;
    protected $_type;
    protected $_urlPadrao;
    protected $idResponsavel = 0;
    protected $idAgente = 0;
    protected $idUsuario = 0;
    protected $moduleName;

    /**
     * Reescreve o metodo init() para aceitar
     * as mensagens e redirecionamentos.
     * Teremos que chama-lo dentro do
     * metodo init() da classe filha assim: parent::init();
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        //SE CAIU A SECAO REDIRECIONA
        //$auth = Zend_Auth::getInstance();

        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $arrAuth = array_change_key_case((array)$auth->getIdentity());

        $this->_msg = $this->_helper->getHelper('FlashMessenger');
        $this->_url = $this->_helper->getHelper('Redirector');
        $this->_type = $this->_helper->getHelper('FlashMessengerType');

        # Forcando o charset conforme o application.ini
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $strCharset = $config->resources->db->params->charset;
        $this->view->charset = $strCharset;

        //@todo verificar motivo desse header/ verificar ajax com erro.
        if (PHP_SAPI != 'cli') {
            header('Content-type: text/html; charset=' . $strCharset);
        }

        $this->_urlPadrao = Zend_Controller_Front::getInstance()->getBaseUrl();
        if (isset($arrAuth['usu_codigo'])) {
            $Usuario = new Autenticacao_Model_DbTable_Usuario();
            $Agente = $Usuario->getIdUsuario($arrAuth['usu_codigo']);
            $idAgente = $Agente['idagente'];
            // manda os dados para a visao
            $this->view->idAgente = $idAgente;
        }
//        @$cpf = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_identificacao : $auth->getIdentity()->Cpf;
        //
        $cpf = isset($arrAuth['usu_codigo']) ? $arrAuth['usu_identificacao'] : $arrAuth['cpf'];

        if ($cpf) {

            # Busca na SGCAcesso
            $sgcAcesso = new Autenticacao_Model_Sgcacesso();
            $acessos = $sgcAcesso->findBy(array('cpf' => $cpf));

            # Busca na Usuarios
            $mdlusuario = new Autenticacao_Model_DbTable_Usuario();
            $usuario = $mdlusuario->findBy(array('usu_identificacao' => $cpf));

            # Busca na Agentes
            $tblAgentes = new Agente_Model_DbTable_Agentes();
            $agente = $tblAgentes->findBy(array('cnpjcpf' => $cpf));

            if ($acessos) {
                $this->idResponsavel = $acessos['IdUsuario'];
            }
            if ($agente) {
                $this->idAgente = $agente['idAgente'];
            }
            if ($usuario) {
                $this->idUsuario = $usuario['usu_codigo'];
            }
            $this->view->idAgenteKeyLog = $this->idAgente;
            $this->view->idResponsavelKeyLog = $this->idResponsavel;
            $this->view->idUsuarioKeyLog = $this->idUsuario;
        }

        $this->moduleName = $this->getRequest()->getModuleName();

        $this->view->bodyClass = $this->getBodyClass();
    }

    /**
     * Metodo para chamar as mensagens e fazer o redirecionamento
     * @access protected
     * @param string $msg
     * @param string $url
     * @param string $type
     * @return void
     */
    protected function message($msg, $url, $type = null, $options = array())
    {
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->flashMessenger->addMessage($msg);
        $this->_helper->flashMessengerType->addMessage($type);
        $this->redirect($url, $options);
    }

    /**
     * Reescreve o metodo postDispatch() que e responsavel
     * por executar uma acao apos a execucao de um metodo
     * @access public
     * @param void
     * @return void
     */
    public function postDispatch()
    {
        if ($this->_msg->hasMessages()) {
            $this->view->message = implode("<br />", $this->_msg->getMessages());
        }
        if ($this->_type->hasMessages()) {
            $this->view->message_type = implode("<br />", $this->_type->getMessages());
        }
        parent::postDispatch(); // chama o metodo pai
    }

    /**
     * Metodo responsavel pela autenticacao e perfis
     * @access protected
     * @param integer $tipo
     *        0 => somente autenticacao zend
     *        1 => autenticacao e permissoes zend (AMBIENTE MINC)
     *        2 => autenticacao scriptcase (AMBIENTE PROPONENTE)
     *        3 => autenticacao scriptcase e autenticacao/permissao zend (AMBIENTE PROPONENTE E MINC)
     * @param array $permissoes (array com as permissoes para acesso)
     * @return void
     *
     * @todo algumas linhas comentadas para verificar em producao se essas linhas sao mesmo necessarias e o motivo  delas.
     */
    protected function perfil($tipo = 0, $permissoes = null)
    {
        # Convertendo os objetos da sessao em array, transformando as chaves em minusculas.
        $auth = Zend_Auth::getInstance();
        $objIdentity = $auth->getIdentity();
        $arrAuth = array_change_key_case((array)$objIdentity);

        $objModelUsuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usuario
        $UsuarioAtivo = new Zend_Session_Namespace('UsuarioAtivo'); // cria a sessao com o usuario ativo
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        // somente autenticacao zend

        $from = base64_encode($this->getRequest()->getRequestUri());
        if ($tipo == 0 || empty($tipo)) {
            if ($auth->hasIdentity()) // caso o usuario esteja autenticado
            {

                // pega as unidades autorizadas, orgaos e grupos do usuario (pega todos os grupos)
                if (isset($objIdentity->usu_codigo) && !empty($arrAuth['usu_codigo'])) {
                    $grupos = $objModelUsuario->buscarUnidades($arrAuth['usu_codigo'], 21);
                    $objAgente = $objModelUsuario->getIdUsuario($arrAuth['usu_codigo']);
                    $idAgente = $objAgente['idagente'];
                    $cpfLogado = $objAgente['usu_identificacao'];
                } elseif (isset($objIdentity->auth) && isset($objIdentity->auth['uid'])) {

                    $this->tratarPerfilOAuth($objIdentity);
                } else {
                    return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout', 'module' => 'autenticacao', 'from' => $from), null, true);
                }

                // manda os dados para a visao
                $this->view->idAgente = $idAgente;
                $this->view->usuario = $objIdentity; // manda os dados do usuario para a visao
                $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
                $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a vis?o
                $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o orgao ativo do usuario para a vis?o
            } else {

                return $this->_helper->redirector->goToRoute(
                    array(
                        'controller' => 'index',
                        'action' => 'logout',
                        'module' => 'autenticacao',
                        'from' => $from
                    )
                    , null
                    , true
                );

            }
            # autenticacao e permissoes zend (AMBIENTE MINC)
        } else if ($tipo === 1) {
            # Caso o usuario esteja autenticado
            if ($auth->hasIdentity()) {
                if (empty($permissoes)) {
                    return $this->_helper->redirector->goToRoute(
                        array(
                            'controller' => 'index',
                            'action' => 'logout',
                            'module' => 'autenticacao'
                        ),
                        null,
                        true);
                }

                # Verifica se o grupo ativo esta no array de permissoes
                if (!in_array($GrupoAtivo->codGrupo, $permissoes)) {
                    $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
                }

                // pega as unidades autorizadas, org?os e grupos do usu?rio (pega todos os grupos)
                $grupos = $objModelUsuario->buscarUnidades($arrAuth['usu_codigo'], 21);

                // manda os dados para a vis?o
                $objAgente = $objModelUsuario->getIdUsuario($arrAuth['usu_codigo']);
                $idAgente = $objAgente['idagente'];
                $this->view->usuario = $objIdentity; // manda os dados do usu?rio para a vis?o
                $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu?rio para a vis?o
                $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu?rio para a vis?o
                $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o ?rg?o ativo do usu?rio para a vis?o
            } else {
                return $this->_helper->redirector->goToRoute(
                    array(
                        'controller' => 'index',
                        'action' => 'logout',
                        'module' => 'autenticacao',
                        'from' => $from
                    )
                    , null
                    , true
                );
            }

            # autenticacao scriptcase (AMBIENTE PROPONENTE)
        } else if ($tipo == 2) {
            // configuracoes do layout padr?o para o scriptcase
            Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));

            // pega o id do usuario logado pelo scriptcase (sess?o)
            //$codUsuario = isset($_SESSION['gusuario']['id']) ? $_SESSION['gusuario']['id'] : $UsuarioAtivo->codUsuario;
            $codUsuario = isset($_GET['idusuario']) ? (int)$_GET['idusuario'] : $UsuarioAtivo->codUsuario;
            //$codUsuario = 366;
            if (isset($codUsuario) && !empty($codUsuario)) {
                $UsuarioAtivo->codUsuario = $codUsuario;
            } else // caso o usuario n?o esteja autenticado
            {
                $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "index", "ALERT");
            }

            // tenta fazer a autenticacao do usuario logado no scriptcase para o zend
            $autenticar = UsuarioDAO::loginScriptcase($codUsuario);

            if ($autenticar && $auth->hasIdentity()) // caso o usuario seja passado pelo scriptcase e esteja autenticado
            {
                // manda os dados para a visao
                $this->view->usuario = $objIdentity; // manda os dados do usuario para a visao
            } // fecha if
            else // caso o usuario n?o esteja autenticado
            {
                $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "index", "ALERT");
            }

            # autenticacao scriptcase e autenticacao/permissao zend (AMBIENTE PROPONENTE E MINC)
        } else if ($tipo == 3) {

            // ========== INICIO AUTENTICACAO SCRIPTCASE ==========
            // pega o id do usuario logado pelo scriptcase
            //$codUsuario = isset($_SESSION['gusuario']['id']) ? $_SESSION['gusuario']['id'] : $UsuarioAtivo->codUsuario;
            $codUsuario = isset($_GET['idusuario']) ? (int)$_GET['idusuario'] : $UsuarioAtivo->codUsuario;
            //$codUsuario = 366;
            if (isset($codUsuario) && !empty($codUsuario)) {
                // configura??es do layout padr?o para o scriptcase
                Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));

                $UsuarioAtivo->codUsuario = $codUsuario;

                // tenta fazer a autenticacao do usuario logado no scriptcase para o zend
                $autenticar = UsuarioDAO::loginScriptcase($codUsuario);

                if ($autenticar && $auth->hasIdentity()) // caso o usuario seja passado pelo scriptcase e esteja autenticado
                {
                    // manda os dados para a visao
                    $this->view->usuario = $objIdentity; // manda os dados do usuario para a visao
                } // fecha if
                else // caso o usuario nao esteja autenticado
                {
                    $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "index", "ALERT");
                }
                // ========== FIM AUTENTICACAO SCRIPTCASE ==========
            } else {
                // ========== INICIO AUTENTICACAO ZEND ==========
                # Caso o usuario nao esteja autenticado pelo scriptcase

                # verifica se o grupo ativo esta no array de permissoes
                if (!in_array($GrupoAtivo->codGrupo, $permissoes)) {
                    $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
                }

                # pega as unidades autorizadas, orgaos e grupos do usuario (pega todos os grupos)
                if (isset($arrAuth['usu_codigo']) && !empty($arrAuth['usu_codigo'])) {
                    $grupos = $objModelUsuario->buscarUnidades($arrAuth['usu_codigo'], 21);
                } else {
                    $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
                }

                # manda os dados para a visao
                $this->view->usuario = $arrAuth; // manda os dados do usuario para a visao
                $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
                $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao
                $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o orgao ativo do usuario para a visao
            }

        } else if ($tipo == 4) {
            # autenticacao migracao e autenticacao/permissao zend (AMBIENTE DE MIGRACAO E MINC)
            $codUsuario = isset($arrAuth['idusuario']) ? (int)$arrAuth['idusuario'] : $UsuarioAtivo->codusuario;

            if (isset($codUsuario) && !empty($codUsuario)) {

                # ====== NICIO AUTENTICACAO MIGRACAO ==========
                # configuracoes do layout padrao para o proponente
//                Zend_Layout::startMvc(array('layout' => 'layout_proponente'));
                $UsuarioAtivo->codUsuario = $codUsuario;

                # tenta fazer a autenticacao do usuario logado no scriptcase para o zend
                # Comentado para verificar se faz sentido autenticar duas vezes no sistema.
                $autenticar = UsuarioDAO::loginScriptcase($codUsuario);

                # caso o usuario seja passado pelo scriptcase e esteja autenticado
                if ($autenticar || $auth->hasIdentity()) {
                    $this->view->usuario = $objIdentity;
                } else {
                    $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "index", "ALERT");
                }
                # ========== FIM AUTENTICACAO MIGRACAO ==========
            } else {
                # ========== INICIO AUTENTICACAO ZEND ==========
                # caso o usuario nao esteja autenticado pelo scriptcase
                # verifica se o grupo ativo esta no array de permissoes

                if (!$objIdentity) {
                    return self::perfil(0, $permissoes);
                } else {
                    if (!in_array($GrupoAtivo->codGrupo, $permissoes)) {
                        $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
                    }

                    // pega as unidades autorizadas, org?os e grupos do usuario (pega todos os grupos)
                    if (isset($objIdentity->usu_codigo) && !empty($objIdentity->usu_codigo)) {
                        $grupos = $objModelUsuario->buscarUnidades($objIdentity->usu_codigo, 21);
                    } else {
                        $this->message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
                    }

                    $this->view->usuario = $objIdentity; // manda os dados do usu?rio para a vis?o
                    $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu?rio para a vis?o
                    $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu?rio para a vis?o
                    $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o ?rg?o ativo do usu?rio para a vis?o

                }
            }
        }

        if (!empty($grupos)) {
            $tblSGCacesso = new Autenticacao_Model_Sgcacesso();
            $rsSGCacesso = $tblSGCacesso->buscar(array("Cpf = ? " => $objIdentity->usu_identificacao));
            if ($rsSGCacesso->count() > 0) {
                $this->view->arrayGrupoProponente = array("gru_codigo" => 1111, "uog_orgao" => 2222, "gru_nome" => "Proponente");
            }
        }

    }

    /**
     * Monta a tela de retorno ao usuario
     * @param string $corpo - arquivo tpl do corpo
     * @param array $dados - array com os dados a serem inseridos na tela, no seguinte formato "nome"=>"valor"
     * @param boolean $exibeHeader - true ou false para exibir header, menu e rodape
     * @return void
     */
    public function montaTela($view, $dados = array())
    {
        // percorrendo array de dados e inserindo no template
        foreach ($dados as $dado => $valor) {
            $this->view->assign($dado, $valor);
        }

        // retorna o tempalte master, com corpo e variaveis setadas
        $this->renderScript($view);
    }

    /**
     * Recebe codigo em HTML e gera um PDF
     * @return void
     */
    public function gerarPdfAction()
    {
        @ini_set("memory_limit", "5000M");
        @ini_set('max_execution_time', 3000);
        @set_time_limit(9500);
        @error_reporting(0);

        $this->_helper->layout->disableLayout();
        @$this->_helper->viewRenderer->setNoRender();

        @$output = '
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
                            .destacar{
                                    background:#DFEFC2;
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


        @$output .= $_POST['html'];

        $patterns = array();
        $patterns[] = '/<table.*?>/is';
        $patterns[] = '/size="3px"/is';
        $patterns[] = '/size="4px"/is';
        $patterns[] = '/size="2px"/is';
        $patterns[] = '/<thead>/is';
        $patterns[] = '/<\/thead>/is';
        $patterns[] = '/<tbody>/is';
        $patterns[] = '/<\/tbody>/is';
        $patterns[] = '/<col.*?>/is';
        $patterns[] = '/<a.*?>/is';
        $patterns[] = '/<img.*?>/is';

        $replaces = array();
        $replaces[] = '<table cellpadding="0" cellspacing="1" border="1" width="90%" align="center">';
        $replaces[] = 'size="14px"';
        $replaces[] = 'size="14px"';
        $replaces[] = 'size="11px"';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';

        //METODO QUE GERA PDF UTILIZANDO A BIBLIOTECA MPDF
        @$output = preg_replace($patterns, $replaces, utf8_encode($output));
        @$pdf = new mPDF('pt', 'A4', 12, '', 8, 8, 5, 14, 9, 9, 'P');
        @$pdf->allow_charset_conversion = true;
        @$pdf->charset_in = 'UTF-8';
        @$pdf->WriteHTML($output);
        @$pdf->Output();
    }

    public function gerarXlsAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $html = $_POST['html'];
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: inline; filename=file.ods;");
        echo $html;
    }

    public function html2PdfAction()
    {
        $orientacao = false;

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($this->_getParam('orientacao') == 'L') {
            $orientacao = true;
        }

        $pdf = new PDFCreator($_POST['html'], $orientacao);

        $pdf->gerarPdf();
    }

    public static function montaBuscaData(Zend_Filter_Input $post, $tpBuscaData, $cmpData, $cmpBD, $cmpDataFinal = null, Array $arrayJoin = null)
    {
        $arrBusca = array();
        $aux1 = $post->__get($cmpData);
        $aux2 = $post->__get($tpBuscaData);
        if (!empty ($aux1) || $aux2 != '') {
            if ($post->__get($tpBuscaData) == "igual") {
                $arrBusca["{$cmpBD} >= ?"] = ConverteData($post->__get($cmpData), 13) . " 00:00:00";
                $arrBusca["{$cmpBD} <= ?"] = ConverteData($post->__get($cmpData), 13) . " 23:59:59";

            } elseif ($post->__get($tpBuscaData) == "maior") {
                $arrBusca["{$cmpBD} >= ?"] = ConverteData($post->__get($cmpData), 13) . " 00:00:00";

            } elseif ($post->__get($tpBuscaData) == "menor") {
                $arrBusca["{$cmpBD} <= ?"] = ConverteData($post->__get($cmpData), 13) . " 00:00:00";

            } elseif ($post->__get($tpBuscaData) == "entre") {

                $arrBusca["{$cmpBD} >= ?"] = ConverteData($post->__get($cmpData), 13) . " 00:00:00";
                $arrBusca["{$cmpBD} <= ?"] = ConverteData($post->__get($cmpDataFinal), 13) . " 23:59:59";

            } elseif ($post->__get($tpBuscaData) == "OT") {
                $arrBusca["{$cmpBD} = ?"] = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";

            } elseif ($post->__get($tpBuscaData) == "U7") {
                $arrBusca["{$cmpBD} > ?"] = date("Y-m-d", strtotime("-7 day")) . " 00:00:00";
                $arrBusca["{$cmpBD} < ?"] = date("Y-m-d") . " 23:59:59";

            } elseif ($post->__get($tpBuscaData) == "SP") {
                /*$arrBusca["{$cmpBD} > ?"] = date("Y-m-").(date("d")-7)." 00:00:00";
                $arrBusca["{$cmpBD} < ?"] = date("Y-m-d")." 23:59:59";*/

                $dia_semana = date('w');
                $primeiro_dia = date('Y-m-d', strtotime("-" . $dia_semana . "day"));
                $domingo = date('Y-m-d', strtotime($primeiro_dia . "-1 week"));
                $sabado = date('Y-m-d', strtotime($domingo . "6 day"));

                $arrBusca["{$cmpBD} >= ?"] = $domingo . " 00:00:00";
                $arrBusca["{$cmpBD} <= ?"] = $sabado . " 23:59:59";


            } elseif ($post->__get($tpBuscaData) == "MM") {
                $arrBusca["{$cmpBD} > ?"] = date("Y-m-01") . " 00:00:00";
                $arrBusca["{$cmpBD} < ?"] = date("Y-m-d") . " 23:59:59";

            } elseif ($post->__get($tpBuscaData) == "UM") {
                $arrBusca["{$cmpBD} >= ?"] = date("Y-m", strtotime("-1 month")) . "-01 00:00:00";
                $arrBusca["{$cmpBD} <= ?"] = date("Y-m-d", mktime(0, 0, 0, date("m", strtotime("-1 month")) + 1, 0, date("Y")));

            } elseif ($post->__get($tpBuscaData) == "") {

            } else {
                $arrBusca["{$cmpBD} > ?"] = ConverteData($post->__get($cmpData), 13) . " 00:00:00";

                if ($post->__get($cmpDataFinal) != "") {
                    $arrBusca["{$cmpBD} < ?"] = ConverteData($post->__get($cmpDataFinal), 13) . " 23:59:59";
                }
            }
        }

        if (!empty($arrayJoin)) {
            $arrBusca = array_merge($arrayJoin, $arrBusca);
        }

        return $arrBusca;
    }


    public function prepararXlsPdfAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        ini_set('max_execution_time', 900);
        $this->_response->clearHeaders();
        $dados = $this->_getAllParams();

        $this->view->dados = $dados;
        $this->view->tipo = $dados['tipo'];

        if ($dados['view']) {
            $this->montaTela($dados['view'], $dados);
        }

    }


    /**
     * Metodo para verificar se o usuario logado tem permissao para acessar o projeto
     * OBS: SERVE APENAS PARA RESPONSAVEL E AGENTE (PROPONENTE E PROCURADOR)
     * @access public
     * @param int
     * @param int
     * @return mixed
     */
    public function verificarPermissaoAcesso($idPreProjeto = null, $idProjeto = null, $administrativo = false, $callback = false)
    {
        $msgERRO = "Usu&aacute;rio com permiss&atilde;o";
        $permissao = 0;
        $auth = Zend_Auth::getInstance()->getIdentity();

        $arrAuth = array_change_key_case((array)$auth);
        if (!isset($arrAuth['usu_codigo'])) {
            $idUsuarioLogado = $arrAuth['idusuario'];

            #Verifica Permissao de Projeto
            if ($idProjeto) {

                $msgERRO = 'Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar esse Projeto!';
                $idPronac = $this->_request->getParam('idpronac') ? $this->_request->getParam('idpronac') : $this->_request->getParam('idPronac');
                $idPronac = !empty($idPronac) ? $idPronac : $idProjeto;

                if (strlen($idPronac) > 7) {
                    $idPronac = Seguranca::dencrypt($idPronac);
                }
                
                $fnVerificarPermissao = new Autenticacao_Model_FnVerificarPermissao();
                $consulta = $fnVerificarPermissao->verificarPermissaoProjeto($idPronac, $idUsuarioLogado);
                //print $idPronac . '/' . $idUsuarioLogado;
                //print_r($consulta);
                $permissao = $consulta->Permissao;
            }
            # Verifica Permissao de Proposta
            if ($idPreProjeto) {

                $msgERRO = 'Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa Proposta!';
                $idPreProjeto = !empty($this->_request->getParam('idPreProjeto')) ? $this->_request->getParam('idPreProjeto') : $idPreProjeto;

                $fnVerificarPermissao = new Autenticacao_Model_FnVerificarPermissao();
                $permissao = $fnVerificarPermissao->verificarPermissaoProposta($idPreProjeto, $idUsuarioLogado);
            }

            if ($administrativo) {
            }

            //Se o usuario nao tiver permissao pra acessar o Projeto / Proposta / Administrativo,
            // exibe a msg de alerta ou retorna um array
            if (!$permissao) {

                if ($callback) {
                    return ['status' => false, 'msg' => $msgERRO];
                }

                $this->message($msgERRO, 'principalproponente', 'ALERT');
            }

            if ($callback) {
                return ['status' => true, 'msg' => $msgERRO];
            }
        }

        if ($callback) {
            return ['status' => true];
        }

    } // fecha metodo verificarPermissaoAcesso()

    public static function validarSenhaInicial()
    {
        return 'c367b67ab2a9d8c3e447735a79a78544';
    }

    /**
     * Metodo para montar as Planilhas Orcamentarias
     * OBS: A planilha deve vir no padrao da spPlanilhaOrcamentaria
     * @access public
     * @param array
     */
    public function montarPlanilhaOrcamentaria($planilhaOrcamentaria, $tipoPlanilha = null)
    {
        $planilha = array();
        $count = 0;
        $seq = 1;
        if ($tipoPlanilha == 0) {
            foreach ($planilhaOrcamentaria as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Seq'] = $seq;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Unidade'] = $resuplanilha->Unidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaProposta'] = $resuplanilha->idPlanilhaProposta;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Item'] = $resuplanilha->Item;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSolicitado'] = $resuplanilha->vlSolicitado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustProponente'] = $resuplanilha->JustProponente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['QtdeDias'] = $resuplanilha->QtdeDias;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Quantidade'] = $resuplanilha->Quantidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Ocorrencia'] = $resuplanilha->Ocorrencia;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlUnitario'] = $resuplanilha->vlUnitario;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idFonte'] = $resuplanilha->idFonte;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['stCustoPraticado'] = $resuplanilha->stCustoPraticado;
                $count++;
                $seq++;
            }
        } else if ($tipoPlanilha == 1) {
            foreach ($planilhaOrcamentaria as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Seq'] = $seq;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaProposta'] = $resuplanilha->idPlanilhaProposta;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Item'] = $resuplanilha->Item;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Unidade'] = $resuplanilha->Unidade;                
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSolicitado'] = $resuplanilha->vlSolicitado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustProponente'] = $resuplanilha->JustProponente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['QtdeDias'] = $resuplanilha->QtdeDias;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Quantidade'] = $resuplanilha->Quantidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Ocorrencia'] = $resuplanilha->Ocorrencia;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlUnitario'] = $resuplanilha->vlUnitario;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idFonte'] = $resuplanilha->idFonte;
                $count++;
                $seq++;
            }
        } else if ($tipoPlanilha == 2) {
            foreach ($planilhaOrcamentaria as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Seq'] = $seq;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Unidade'] = $resuplanilha->Unidade;                
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaProjeto'] = $resuplanilha->idPlanilhaProjeto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Item'] = $resuplanilha->Item;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSolicitado'] = $resuplanilha->vlSolicitado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustProponente'] = TratarString::converterParaUTF8($resuplanilha->JustProponente);
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['QtdeDias'] = $resuplanilha->QtdeDias;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Quantidade'] = $resuplanilha->Quantidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Ocorrencia'] = $resuplanilha->Ocorrencia;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlUnitario'] = $resuplanilha->vlUnitario;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSugerido'] = $resuplanilha->vlSugerido;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustParecerista'] = $resuplanilha->JustParecerista;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idFonte'] = $resuplanilha->idFonte;
                $count++;
                $seq++;
            }
        } else if ($tipoPlanilha == 3) {
            foreach ($planilhaOrcamentaria as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Seq'] = $seq;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Unidade'] = $resuplanilha->Unidade;                
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Item'] = $resuplanilha->Item;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSolicitado'] = $resuplanilha->vlSolicitado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSugerido'] = $resuplanilha->vlSugerido;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['QtdeDias'] = $resuplanilha->QtdeDias;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Quantidade'] = $resuplanilha->Quantidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Ocorrencia'] = $resuplanilha->Ocorrencia;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlUnitario'] = $resuplanilha->vlUnitario;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlAprovado'] = $resuplanilha->vlAprovado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlComprovado'] = $resuplanilha->vlComprovado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustProponente'] = $resuplanilha->JustProponente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustParecerista'] = $resuplanilha->JustParecerista;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustComponente'] = $resuplanilha->JustComponente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idFonte'] = $resuplanilha->idFonte;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['tpPlanilha'] = $resuplanilha->tpPlanilha;
                $count++;
                $seq++;
            }
        } else if ($tipoPlanilha == 4) {
            foreach ($planilhaOrcamentaria as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Seq'] = $seq;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Unidade'] = $resuplanilha->Unidade;                
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaProjeto'] = $resuplanilha->idPlanilhaProjeto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Item'] = $resuplanilha->Item;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSolicitado'] = $resuplanilha->vlSolicitado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlSugerido'] = $resuplanilha->vlSugerido;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['QtdeDias'] = $resuplanilha->QtdeDias;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Quantidade'] = $resuplanilha->Quantidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Ocorrencia'] = $resuplanilha->Ocorrencia;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlUnitario'] = $resuplanilha->vlUnitario;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlAprovado'] = $resuplanilha->vlAprovado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustProponente'] = $resuplanilha->JustProponente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustParecerista'] = $resuplanilha->JustParecerista;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['JustComponente'] = $resuplanilha->JustComponente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idFonte'] = $resuplanilha->idFonte;
                $count++;
                $seq++;
            }
        } else if ($tipoPlanilha == 5) {
            foreach ($planilhaOrcamentaria as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Seq'] = $seq;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaAprovacaoPai'] = $resuplanilha->idPlanilhaAprovacaoPai;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idEtapa'] = $resuplanilha->idEtapa;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Item'] = $resuplanilha->Item;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idFonte'] = $resuplanilha->idFonte;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['FonteRecurso'] = $resuplanilha->FonteRecurso;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Unidade'] = $resuplanilha->Unidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Quantidade'] = $resuplanilha->Quantidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Ocorrencia'] = $resuplanilha->Ocorrencia;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlUnitario'] = $resuplanilha->vlUnitario;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlAprovado'] = $resuplanilha->vlAprovado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlComprovado'] = $resuplanilha->vlComprovado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['QtdeDias'] = $resuplanilha->QtdeDias;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['dsJustificativa'] = $resuplanilha->dsJustificativa;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idAgente'] = $resuplanilha->idAgente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['tpAcao'] = $resuplanilha->tpAcao;
                $count++;
                $seq++;
            }
        } else if ($tipoPlanilha == 6 || $tipoPlanilha == 7) {
            foreach ($planilhaOrcamentaria as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Seq'] = $seq;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idPlanilhaAprovacaoPai'] = $resuplanilha->idPlanilhaAprovacaoPai;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idEtapa'] = $resuplanilha->idEtapa;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Item'] = $resuplanilha->Item;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idFonte'] = $resuplanilha->idFonte;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['FonteRecurso'] = $resuplanilha->FonteRecurso;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Unidade'] = $resuplanilha->Unidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Quantidade'] = $resuplanilha->Quantidade;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['Ocorrencia'] = $resuplanilha->Ocorrencia;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlUnitario'] = $resuplanilha->vlUnitario;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlAprovado'] = $resuplanilha->vlAprovado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['vlComprovado'] = $resuplanilha->vlComprovado;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['QtdeDias'] = $resuplanilha->QtdeDias;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['dsJustificativa'] = $resuplanilha->dsJustificativa;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idAgente'] = $resuplanilha->idAgente;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['tpAcao'] = $resuplanilha->tpAcao;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idUF'] = $resuplanilha->idUF;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idMunicipio'] = $resuplanilha->idMunicipio;
                $planilha[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Municipio][$count]['idProduto'] = $resuplanilha->idProduto;

                $count++;
                $seq++;
            }
        }
        return $planilha;
    }

    private function tratarPerfilOAuth($objIdentity)
    {
        try {
            $arrayOAuth = $objIdentity->auth['raw'];
            $objSGCacesso = new Autenticacao_Model_Sgcacesso();
            $arraySGCacesso = $objSGCacesso->findBy(array("cpf" => $arrayOAuth['cpf']));
            if ($arraySGCacesso) {
                if (!$arraySGCacesso['id_login_cidadao']) {
                    $arraySGCacesso['id_login_cidadao'] = $arrayOAuth['id'];
                    $objSGCacesso->salvar($arraySGCacesso);
                }
                return true;
            } else {
                $this->redirecionarParaCadastroOauth($arrayOAuth);
                return false;
            }
        } catch (Exception $objException) {
            throw $objException;
        }
    }

    /**
     * @param $objAuth
     * @return void
     */
    protected function redirecionarParaCadastroOauth($objAuth)
    {
        $this->postFoward("autenticacao", "Logincidadao", "cadastrarusuario", $objAuth);
    }

    /**
     * @param $module
     * @param $controllerName
     * @param $actionName
     * @param $parameters
     * @return void
     */
    protected function postFoward($module, $controllerName, $actionName, $parameters)
    {
        $request = clone $this->getRequest();
        $request->setModuleName($module)
            ->setControllerName($controllerName)
            ->setActionName($actionName)
            ->setPost($parameters);
        $this->_helper->actionStack($request);
    }

    /**
     * @param $url
     * @param array $data
     * @param array|null $headers
     * @throws Exception
     * @return void
     */
    public function postRedirector($url, array $data, array $headers = null)
    {
        $params = array(
            'http' => array(
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        if (!is_null($headers)) {
            $params['http']['header'] = '';
            foreach ($headers as $k => $v) {
                $params['http']['header'] .= "$k: $v\n";
            }
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if ($fp) {
            echo @stream_get_contents($fp);
            die();
        } else {
            throw new Exception("Error loading '$url', $php_errormsg");
        }
    }

    /**
     * @return array
     * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
     * @return mixed
     */
    protected function getOPAuthConfiguration()
    {
        $oauthConfigArray = Zend_Registry::get("config")->toArray();
        if ($oauthConfigArray && $oauthConfigArray['OAuth']['servicoHabilitado'] == true) {
            return $oauthConfigArray['OAuth'];
        }
    }

    protected function abrirDocumento($idDocumento)
    {
        // Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        try {
            $tbDocumento = new Arquivo_Model_DbTable_TbDocumento();
            $resultado = $tbDocumento->abrir($idDocumento);

            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
            $this->_response->clearBody();                  // Limpa o corpo html
            $this->_response->clearHeaders();               // Limpa os headers do Zend

            if (!$resultado) {
                throw new Exception("O documento n&atilde;o existe!");
            } else {
                foreach ($resultado as $r) {
                    $this->getResponse()
                        ->setHeader('Content-Type', $r->dsTipoPadronizado)
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                        ->setBody($r->biArquivo);
                }
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    function getBodyClass($class = '')
    {
        return join(' ', $this->getArrayBodyClass($class));
    }

    function getArrayBodyClass($class = '')
    {
        $classes = array();

        $classes[] = $this->getRequest()->getCookie('menu');

        $classes[] = $this->getRequest()->getModuleName();

        $classes[] = $this->getRequest()->getControllerName();

        $classes[] = $this->getRequest()->getActionName();

        if (!empty($class)) {

            if (!is_array($class)) {
                $class = preg_split('#\s+#', $class);
            }

            $classes = array_merge($classes, $class);
        }
        return array_unique($classes);

    }
}
