<?php

/**
 * UploadController
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright 2010 - Ministerio da Cultura - Todos os direitos reservados.
 */
class UploadController extends MinC_Controller_Action_Abstract
{
    private $idPreProjeto = null;
    private $idPronac = null;
    private $limiteTamanhoArq = null;
    private $orgaoAutorizado = null;
    private $orgaoLogado = null;
    private $cod = null;
    private $blnProponente = false;
    private $intFaseProjeto = 0;
    protected $cpfLogado = 0;
    protected $idResponsavel = 0;
    protected $idAgente = 0;
    private $authIdentity;

    /**
     * Reescreve o m?todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->limiteTamanhoArq = 1024 * 1024 * 10;

        $auth = Zend_Auth::getInstance();
        $this->authIdentity = array_change_key_case((array)$auth->getIdentity());

        $PermissoesGrupo = array();

        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usuario
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }
        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : $this->blnProponente = true;
        parent::perfil(4, $PermissoesGrupo);


        // verifica as permiss?es
        /* $PermissoesGrupo = array();
          $PermissoesGrupo[] = 97;  // Gestor do SALIC
          $PermissoesGrupo[] = 103; // Coordenador de Analise
          $PermissoesGrupo[] = 124;
          $PermissoesGrupo[] = 125;
          $PermissoesGrupo[] = 126;
          $PermissoesGrupo[] = 125;
          $PermissoesGrupo[] = 94;
          $PermissoesGrupo[] = 93;
          $PermissoesGrupo[] = 82;
          $PermissoesGrupo[] = 132;
          $PermissoesGrupo[] = 100; */
        //$PermissoesGrupo[] = 1111; //Proponente
        //parent::perfil(3, $PermissoesGrupo);

        parent::init();

        //recupera ID do pre projeto (proposta)
        if (!empty($_REQUEST['idPreProjeto'])) {
            $this->idPreProjeto = $_REQUEST['idPreProjeto'];
            $this->cod = "?idPreProjeto=" . $this->idPreProjeto;
        }

        if (!empty($_REQUEST['idPronac'])) {
            $this->idPronac = $_REQUEST['idPronac'];

            if (strlen($this->idPronac) > 7) {
                $this->idPronac = Seguranca::dencrypt($this->idPronac);
            }

            $this->cod = "?idPronac=" . $this->idPronac;
            $idPronac = $_REQUEST['idPronac'];

            //DEFINE FASE DO PROJETO
            $this->faseDoProjeto($idPronac);
            $this->view->intFaseProjeto = $this->intFaseProjeto;

            /*             * * Validacao do Proponente Inabilitado *********************************** */
            $cpf = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_identificacao : $auth->getIdentity()->Cpf;
            $this->cpfLogado = $cpf;

            $geral = new ProponenteDAO();
            $tblProjetos = new Projetos();

            $proj = new Projetos();
            $resp = $proj->buscar(array('IdPRONAC = ?' => $idPronac))->current();
            $this->view->resp = $resp;

            $arrBusca['IdPronac = ?'] = $idPronac;
            $rsProjeto = $tblProjetos->buscar($arrBusca)->current();

            $idPreProjeto = null;

            if (!empty($rsProjeto->idProjeto)) {
                $idPreProjeto = $rsProjeto->idProjeto;
            }

            $tbdados = $geral->buscarDadosProponente($idPronac);
            $this->view->dados = $tbdados;

            // Busca na SGCAcesso
            $sgcAcesso = new Autenticacao_Model_Sgcacesso();
            $buscaAcesso = $sgcAcesso->buscar(array('Cpf = ?' => $cpf));

            // Busca na Agentes
            $agentesDAO = new Agente_Model_DbTable_Agentes();
            $buscaAgente = $agentesDAO->BuscaAgente($cpf);

            if (count($buscaAcesso) > 0) {
                $this->idResponsavel = $buscaAcesso[0]->IdUsuario;
            }
            if (count($buscaAgente) > 0) {
                $this->idAgente = $buscaAgente[0]->idAgente;
            }

            $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usuario
            $idagente = $Usuario->getIdUsuario('', $cpf);
            $this->idAgente = (isset($idagente['idAgente']) && !empty($idagente['idAgente'])) ? $idagente['idAgente'] : 0;
            $ag = new Agente_Model_DbTable_Agentes();
            $buscarvinculo = $ag->buscarAgenteVinculoProponente(array('vp.idAgenteProponente = ?' => $this->idAgente, 'pr.idPRONAC = ?' => $idPronac, 'vprp.siVinculoProposta = ?' => 2));
            $this->view->vinculo = $buscarvinculo->count() > 0 ? true : false;

            $cpfLogado = $this->cpfLogado;
            $cpfProponente = $tbdados[0]->CgcCpf;
            $respProponente = 'R';
            $inabilitado = 'N';

            // Indentificando o Proponente
            if ($cpfLogado == $cpfProponente) {
                $respProponente = 'P';
            }

            // Verificando se o Proponente esta inabilitado
            $inabilitadoDAO = new Inabilitado();
            $where['CgcCpf 		= ?'] = $cpfProponente;
            $where['Habilitado 	= ?'] = 'N';
            $busca = $inabilitadoDAO->Localizar($where)->count();

            if ($busca > 0) {
                $inabilitado = 'S';
            }

            if (!empty($idPreProjeto)) {

                // Se for Responsavel verificar se tem Procuracao
                $procuracaoDAO = new Procuracao();
                $procuracaoValida = 'N';

                $wherePro['vprp.idPreProjeto = ?'] = $idPreProjeto;
                $wherePro['v.idUsuarioResponsavel = ?'] = $this->idResponsavel;
                $wherePro['p.siProcuracao = ?'] = 1;
                $buscaProcuracao = $procuracaoDAO->buscarProcuracaoProjeto($wherePro)->count();

                if ($buscaProcuracao > 0) {
                    $procuracaoValida = 'S';
                }
            } else {
                $procuracaoValida = 'S';
            }

            $this->view->procuracaoValida = $procuracaoValida;
            $this->view->respProponente = $respProponente;
            $this->view->inabilitado = $inabilitado;

            /*             * ************************************************************************* */


            $this->view->urlMenu = [
                'module' => 'projeto',
                'controller' => 'menu',
                'action' => 'obter-menu-ajax',
                'idPronac' => $idPronac
            ];
        }
        $this->view->blnProponente = $this->blnProponente;

        //$this->orgaoAutorizado = "272"; //correto e 272
        $this->orgaoAutorizado = "251";
        $this->orgaoLogado = !isset($auth->getIdentity()->IdUsuario) ? $_SESSION['Zend_Auth']['storage']->usu_orgao : 0;



    }

    /**
     * Metodo para abrir um arquivo binario
     * @access public
     * @param void
     * @return void
     */
    public function abrirAction()
    {
        // recebe o id do arquivo via get
        $get = Zend_Registry::get('get');
        $id = (int)isset($get->id) ? $get->id : $this->_request->getParam('id');

        // Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = UploadDAO::abrir($id);

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // l� os cabe�alhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $this->getResponse()
                    ->setHeader('Content-Type', $r->dsTipoPadronizado)
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                    //->setHeader("Connection", "close")
                    //->setHeader("Content-transfer-encoding", "binary")
                    //->setHeader("Cache-control", "private")
                    ->setBody($r->biArquivo);
            } // fecha foreach
        } // fecha else
    }

    /**
     * Metodo para abrir um arquivo binario da tabela tbDocumentosPreProjeto
     *
     * @name abrirDocumentosPreProjetoAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since 02/10/2016
     */
    public function abrirDocumentosPreProjetoAction()
    {
        $get = Zend_Registry::get('get');
        $id = (int)isset($get->id) ? $get->id : $this->_request->getParam('id');

        # Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        # busca o arquivo
        $tbl = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
        $resultado = $tbl->abrir($id)->current();

        # erro ao abrir o arquivo
        $this->_helper->layout->disableLayout();        # Desabilita o Zend Layout
        $this->_helper->viewRenderer->setNoRender();    # Desabilita o Zend Render
        if (!$resultado) {
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            Zend_Layout::getMvcInstance()->disableLayout(); # Desabilita o Zend MVC
            $this->_response->clearBody();                  # Limpa o corpo html
            $this->_response->clearHeaders();               # Limpa os headers do Zend
            $up = new Upload();
            $tipoArquivo = method_exists($up, $up->getMimeType($resultado->noarquivo)) ? $up->getMimeType("jpg") : "application/pdf";
            if ($tbl->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
                $this->getResponse()
                    ->setHeader('Content-Type', $tipoArquivo)
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $resultado->noarquivo . '"')
                    ->setBody($resultado->imdocumento);
            } else {
                $this->getResponse()
                    ->setHeader('Content-Type', $tipoArquivo)
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $resultado->noarquivo . '"');
                readfile(APPLICATION_PATH . '/..' . $resultado->imdocumento);
            }
        }
    }

    /**
     * Metodo para abrir um arquivo binario da tabela tbDocumentosPreProjeto
     *
     * @name abrirDocumentosAgentesAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since 07/10/2016
     */
    public function abrirDocumentosAgentesAction()
    {
        // recebe o id do arquivo via get
        $get = Zend_Registry::get('get');
        $id = (int)isset($get->id) ? $get->id : $this->_request->getParam('id');

        // Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $tbl = new Proposta_Model_DbTable_TbDocumentosAgentes();
        $resultado = $tbl->abrir($id)->current();

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
            $this->_response->clearBody();                  // Limpa o corpo html
            $this->_response->clearHeaders();               // Limpa os headers do Zend

            $up = new Upload();
            $tipoArquivo = method_exists($up, $up->getMimeType($resultado->noarquivo)) ? $up->getMimeType("jpg") : "application/pdf";

            if ($tbl->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
                $this->getResponse()
                    ->setHeader('Content-Type', $tipoArquivo)
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $resultado->noarquivo . '"')
                    //->setHeader("Connection", "close")
                    //->setHeader("Content-transfer-encoding", "binary")
                    //->setHeader("Cache-control", "private")
                    ->setBody($resultado->imdocumento);
            } else {
                $this->getResponse()
                    ->setHeader('Content-Type', $tipoArquivo)
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $resultado->noarquivo . '"');
                readfile(APPLICATION_PATH . '/..' . $resultado->imdocumento);
            }
        } // fecha else
    }

    // fecha abrirAction()

    /**
     * Metodo para abrir documentos anexados
     * @access public
     * @param void
     * @return void
     */
    public function abrirdocumentosanexadosAction()
    {
        // recebe o id do arquivo via get
        $get = Zend_Registry::get('get');
        $id = (int)$get->id;
        $busca = $this->_request->getParam('busca'); //$get->busca;
        // Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = UploadDAO::abrirdocumentosanexados($id, $busca);
        if (!$resultado) {
            if ($busca == "documentosanexadosminc") {
                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexadosminc");
            } else {
                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexados");
            }
        }

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // os cabecalhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $hashArquivo = ($r->biArquivo) ? $r->biArquivo : $r->biArquivo2;

                $this->getResponse()
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                    ->setHeader("Connection", "close")
                    ->setHeader("Content-transfer-encoding", "binary")
                    ->setHeader("Cache-control", "private");

                if ($r->biArquivo2 == 1) {
                    if (strtolower(substr($r->biArquivo, 0, 4)) == '%pdf') {
                        $this->getResponse()->setBody($hashArquivo);
                    } else {
                        $this->getResponse()->setBody(base64_decode($hashArquivo));
                    }
                } else {
                    if ($r->biArquivo2 == null) {
                        $this->getResponse()->setBody(base64_decode($hashArquivo));
                    } else {
                        $this->getResponse()->setBody($hashArquivo);
                    }
                }
                //->setBody(base64_decode($hashArquivo));
            } // fecha foreach
        } // fecha else
    }

    // fecha abrirdocumentosanexadosAction()

    public function abrirdocumentosanexadosbinarioAction()
    {
        // recebe o id do arquivo via get
        $get = Zend_Registry::get('get');
        $id = (int)$get->id;
        $busca = $this->_request->getParam('busca'); //$get->busca;
        // Configuracao o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = UploadDAO::abrirdocumentosanexados($id, $busca);
        if (!$resultado) {
            if ($busca == "documentosanexadosminc") {
                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexadosminc");
            } else {
                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexados");
            }
        }

        // erro ao abrir o arquivo
        if (!$resultado) {
            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
            die("N&atilde;o existe o arquivo especificado");
            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // os cabecalhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend

                $hashArquivo = ($r->biArquivo) ? $r->biArquivo : $r->biArquivo2;
 
                $this->getResponse()
                    ->setHeader('Content-Type', 'application/pdf')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
                    ->setHeader("Connection", "close")
                    ->setHeader("Content-transfer-encoding", "binary")
                    ->setHeader("Cache-control", "private");

                if ($r->biArquivo2 == 1) {
                    if (strtolower(substr($r->biArquivo, 0, 4)) == '%pdf') {
                        $this->getResponse()->setBody($hashArquivo);
                    } else {
                        $this->getResponse()->setBody(base64_decode($hashArquivo));
                    }
                } else {
                    $this->getResponse()->setBody($hashArquivo);
                }
//                        $this->getResponse()->setBody($hashArquivo);
                //->setBody(base64_decode($hashArquivo));
            } // fecha foreach
        } // fecha else
    }

    // fecha abrirdocumentosanexadosAction()

    public function formEnviarArquivoMarcaAction()
    {
        $Projetos = new Projetos();
        $dadosProjeto = $Projetos->buscar(array('IdPRONAC=?' => $this->idPronac))->current();

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY DADOS
        $this->montaTela(
            "upload/formenviararquivomarca.phtml",
            array("idPronac" => $this->idPronac,
                "orgao" => $this->orgaoLogado,
                "orgaoAutorizado" => $this->orgaoAutorizado,
                "projeto" => $dadosProjeto)
        );
    }

    public function listarArquivoMarcaAction()
    {
        $this->_helper->layout->disableLayout();

        $rsArquivos = array();

        $get = Zend_Registry::get('get');
        $idProposta = $get->idPreProjeto;
        $idPronac = $get->idPronac;

        $arrBusca = array();
        (!empty($idProposta)) ? $arrBusca['dpp.idProposta =?'] = $idProposta : null;
        (!empty($idPronac)) ? $arrBusca['dp.idPronac =?'] = $idPronac : null;

        if (!empty($arrBusca)) {
            $tbArquivoImagem = new tbArquivoImagem();
            $rsArquivos = $tbArquivoImagem->buscarArquivoMarca($arrBusca, array('idArquivo'));
        }

        //METODO QUE MONTA TELA DO USUARIO ENVIANDO TODOS OS PARAMENTROS NECESSARIO DENTRO DO ARRAY DADOS
        $this->montaTela(
            "upload/listaarquivomarca.phtml",
            array("arquivos" => $rsArquivos,
                "orgao" => $this->orgaoLogado,
                "orgaoAutorizado" => $this->orgaoAutorizado)
        );
    }

    public function gravarArquivoMarcaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        // pega as informacoes do arquivo
        $post = Zend_Registry::get('post');

        $observacao = $post->observacao;
        $idProposta = $post->idPreProjeto;
        $idPronac = $post->idPronac;

        if (!empty($idPronac) && $idPronac != "0") {
            $tbProjeto = new Projetos();
            $rsProjeto = $tbProjeto->find($idPronac)->current();
            if (empty($rsProjeto) || count($rsProjeto) <= 0) {
                $mensagem = "Pronac inv&aacute;lido.";
                $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

                $this->montaTela(
                    "upload/mensagem.phtml",
                    array("mensagem" => $mensagem,
                        "tipoMensagem" => "ERROR",
                        "script" => $script)
                );
                return;
            }

            if ($_FILES['arquivo']['tmp_name']) {
                $arquivoNome = $_FILES['arquivo']['name']; // nome
                $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome temporario
                $arquivoTipo = $_FILES['arquivo']['type']; // tipo
                $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
                    $arquivoBinario = Upload::setBinario($arquivoTemp); // binario
                    $arquivoHash = Upload::setHash($arquivoTemp); // hash
                }

                //VALIDA TAMANHO ARQUIVO
                if ($arquivoTamanho > $this->limiteTamanhoArq) {
                    $mensagem = "O arquivo deve ser menor que 10 MB<br />";
                    $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

                    $this->montaTela(
                        "upload/mensagem.phtml",
                        array("mensagem" => $mensagem,
                            "tipoMensagem" => "ERROR",
                            "script" => $script)
                    );
                    return;
                }

                //VALIDA EXTENSAO ARQUIVO
                if (!in_array($arquivoExtensao, explode(',', 'jpeg,jpg,gif,bmp,png,tif,raw,pdf,JPG, JPEG,GIF,BMP,PNG,TIF,RAW,PDF'))) {
                    $mensagem = "Arquivo com extens&atilde;o Inv&aacute;lida<br />";
                    $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

                    $this->montaTela(
                        "upload/mensagem.phtml",
                        array("mensagem" => $mensagem,
                            "tipoMensagem" => "ERROR",
                            "script" => $script)
                    );
                    return;
                }

                try {
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB :: FETCH_OBJ);
                    $db->beginTransaction();


                    // ==================== PERSISTE DADOS DO ARQUIVO =================//
                    $dadosArquivo = array(
                        'nmArquivo' => $arquivoNome,
                        'sgExtensao' => $arquivoExtensao,
                        'dsTipoPadronizado' => $arquivoTipo,
                        'nrTamanho' => $arquivoTamanho,
                        'dtEnvio' => new Zend_Db_Expr('GETDATE()'),
                        'dsHash' => $arquivoHash,
                        'stAtivo' => 'I');

                    $tbArquivo = new tbArquivo();
                    $idArquivo = $tbArquivo->inserir($dadosArquivo);


                    // ================== PERSISTE DADOS ARQUIVO - BINARIO ============//
                    $dadosBinario = array(
                        'idArquivo' => $idArquivo,
                        'biArquivo' => new Zend_Db_Expr("CONVERT(varbinary(MAX), {$arquivoBinario})"));

                    $tbArquivoImagem = new tbArquivoImagem();
                    $idArquivoImagem = $tbArquivoImagem->inserir($dadosBinario);

                    // ================= PERSISTE DADOS DO DOCUMENTO ==================//
                    $dadosDoc = array(
                        'idArquivo' => $idArquivo,
                        'idTipoDocumento' => '1',
                        'dsDocumento' => $observacao);

                    $tbDocumento = new tbDocumento();
                    $idDocumento = $tbDocumento->inserir($dadosDoc);
                    $idDocumento = $idDocumento['idDocumento'];


                    // ================= PERSISTE DOCUMENTO PROPOSTA ==================//
                    if (!empty($idProposta)) {
                        $dadosDocProposta = array('idProposta' => $idProposta,
                            'idTipoDocumento' => '1',
                            'idDocumento' => $idDocumento,
                            'stAtivoDocumentoProposta' => '0');

                        $tbDocProposta = new tbDocumentoProposta();
                        $tbDocProposta->inserir($dadosDocProposta);
                    }

                    // ================= PERSISTE DOCUMENTO PROJETO ===================//
                    if (!empty($idPronac)) {
                        $dadosDocProjeto = array('idPronac' => $idPronac,
                            'idTipoDocumento' => '1',
                            'idDocumento' => $idDocumento,
                            'stAtivoDocumentoProjeto' => '0');

                        $tbDocProjeto = new tbDocumentoProjeto();
                        $tbDocProjeto->inserir($dadosDocProjeto);
                    }

                    //$script = "window.parent.document.getElementById('divMensagem').innerHTML = 'Erro ao enviar arquivo.<br />';\n";
                    $db->commit();


                    $mensagem = "Arquivo enviado com sucesso.";
                    $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";
                    $script .= "\$('#observacao', parent.document.body).val(''); \n \$('#arquivo', parent.document.body).val('');";

                    $this->montaTela(
                        "upload/mensagem.phtml",
                        array("mensagem" => $mensagem,
                            "tipoMensagem" => "CONFIRM",
                            "script" => $script)
                    );
                    return;
                    //echo $script;
                } catch (Exception $e) {
                    $db->rollBack();


                    $mensagem = "Erro ao enviar arquivo.";
                    $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

                    $this->montaTela(
                        "upload/mensagem.phtml",
                        array("mensagem" => $mensagem,
                            "tipoMensagem" => "ERROR",
                            "script" => $script
                        )
                    );
                    return;
                }
            } else {
                $mensagem = "Nenhum arquivo enviado.";
                $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

                $this->montaTela(
                    "upload/mensagem.phtml",
                    array("mensagem" => $mensagem,
                        "tipoMensagem" => "ERROR",
                        "script" => $script)
                );
                return;
            }
        } else {
            $mensagem = "Pronac inv&aacute;lido.";
            $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

            $this->montaTela(
                "upload/mensagem.phtml",
                array("mensagem" => $mensagem,
                    "tipoMensagem" => "ERROR",
                    "script" => $script)
            );
            return;
        }
    }

    public function aprovarArquivoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');
        $idArquivo = $get->idArquivo;

        try {
            $tbArquivo = new tbArquivo();
            $rsArquivo = $tbArquivo->find($idArquivo)->current();
            $rsArquivo->stAtivo = "A";
            $rsArquivo->save();

            $mensagem = "Marca aprovada com sucesso!";
            $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

            $this->montaTela(
                "upload/mensagem.phtml",
                array("mensagem" => $mensagem,
                    "tipoMensagem" => "CONFIRM",
                    "script" => $script)
            );
            return;
        } catch (Exception $e) {
            $mensagem = "N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o.";
            $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

            $this->montaTela(
                "upload/mensagem.phtml",
                array("mensagem" => $mensagem,
                    "script" => $script,
                    "tipoMensagem" => "ERROR")
            );
            return;
        }
    }

    public function excluirArquivoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');
        $idArquivo = $get->idArquivo;
        $idDocumento = $get->idDocumento;

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $db->beginTransaction();

            // ================= APAGA DOCUMENTO PROPOSTA ==================//
            if (!empty($this->idPreProjeto)) {
                $tbDocProposta = new tbDocumentoProposta();
                $tbDocProposta->excluir("idProposta = {$this->idPreProjeto} and idDocumento= {$idDocumento} ");
            }

            // ================= APAGA DOCUMENTO PROJETO ===================//
            if (!empty($this->idPronac)) {
                $tbDocProjeto = new tbDocumentoProjeto();
                $tbDocProjeto->excluir("idPronac = {$this->idPronac} and idDocumento= {$idDocumento} ");
            }

            $tbDocumento = new tbDocumento();
            $tbDocumento->excluir("idArquivo = {$idArquivo} and idDocumento= {$idDocumento} ");

            $tbArquivoImagem = new tbArquivoImagem();
            $tbArquivoImagem->excluir("idArquivo =  {$idArquivo} ");

            $tbArquivo = new tbArquivo();
            $tbArquivo->excluir("idArquivo = {$idArquivo} ");

            $db->commit();

            $mensagem = "Arquivo exclu&iacute;do com sucesso!";
            $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

            $this->montaTela(
                "upload/mensagem.phtml",
                array("mensagem" => $mensagem,
                    "tipoMensagem" => "CONFIRM",
                    "script" => $script)
            );
            return;
        } catch (Exception $e) {
            $db->rollBack();

            $mensagem = "N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o.";
            $script = "window.parent.jqAjaxLinkSemLoading('" . $this->view->baseUrl() . "/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";
            $this->montaTela(
                "upload/mensagem.phtml",
                array("mensagem" => $mensagem,
                    "script" => $script,
                    "tipoMensagem" => "ERROR")
            );
            return;
        }
    }

    /**
     * @todo mover este metodo para a classe generica, pode ser usado em outros lugares
     */
    public function faseDoProjeto($idPronac)
    {
        if (!empty($idPronac)) {
            $tblProjeto = new Projetos();
            $rsProjeto = $tblProjeto->buscar(array("IdPronac=?" => $idPronac))->current();
            $pronac = $rsProjeto->AnoProjeto . $rsProjeto->Sequencial;
            $dtFimPerExecucao = date('Ymd', strtotime($rsProjeto->DtFimExecucao));
            $dtAtual = date("Ymd");
            $diffDias = Data::CompararDatas($dtFimPerExecucao, $dtAtual);

            $tblAprovacao = new Aprovacao();
            $arrBuscaF1 = array();
            $arrBuscaF1['AnoProjeto+Sequencial = ?'] = $pronac;
            $arrBuscaF1['TipoAprovacao = ?'] = 1;
            $rsF1 = $tblAprovacao->buscar($arrBuscaF1);

            $arrBuscaF2 = array();
            $arrBuscaF2['AnoProjeto+Sequencial = ?'] = $pronac;
            $arrBuscaF2['TipoAprovacao = ?'] = 1;
            $arrBuscaF2['PortariaAprovacao IS NOT NULL'] = '?';
            $rsF2 = $tblAprovacao->buscar($arrBuscaF2);

            $tbRelatorio = new tbRelatorio();
            $tbRelConsolidado = new tbRelatorioConsolidado();

            $arrBuscaRel = array();
            $rsF3 = array();
            $arrBuscaRel['idPronac = ?'] = $idPronac;
            $arrBuscaRel['tpRelatorio = ?'] = 'C';
            $arrBuscaRel['idDistribuicaoProduto is NOT NULL'] = '?';
            $rsRelatorio = $tbRelatorio->buscar($arrBuscaRel)->current();
            if (is_object($rsRelatorio) && count($rsRelatorio) > 0) {
                $arrBuscaF3 = array();
                $arrBuscaF3['idRelatorio = ?'] = $rsRelatorio->idRelatorio;
                $rsF3 = $tbRelConsolidado->buscar($arrBuscaF3);
            }

            //situacoes fase Proj. Encerrado
            $arrSituacoes = array('E19', 'E22', 'L03');

            $tbRelatorioTec = new tbRelatorioTecnico();
            $arrBuscaF4 = array();
            $arrBuscaF4['idPronac = ?'] = $idPronac;
            $arrBuscaF4['cdGrupo IN (?)'] = array('125', '126');
            $rsF4 = $tbRelatorioTec->buscar($arrBuscaF4);

            //FASE INICIAL
            if ($rsF1->count() == 0 && $rsF2->count() == 0) {
                $this->intFaseProjeto = 1;

                //FASE EXECUCAO
            } elseif ($rsF1->count() >= 1 && $rsF2->count() >= 1 && (!is_object($rsF3) || $rsF3->count() == 0)) {
                $this->intFaseProjeto = 2;

                //FASE FINAL
            } elseif ($rsF1->count() >= 1 && $rsF2->count() >= 1 && (is_object($rsF3) && $rsF3->count() >= 1) /* && $diffDias > 30 */ && $rsF4->count() == 0) { //retirei a comparacao com os trinta dias para que entrem nessa fase projetoa que atendam a todas as condicoes mas ainda nao tiveram 30 dias passados da data fim de execucao
                $this->intFaseProjeto = 3;

                //FASE PROJETO ENCERRADO
            } elseif ($rsF1->count() >= 1 && $rsF2->count() >= 1 && (is_object($rsF3) && $rsF3->count() >= 1) && $diffDias > 30 && (in_array($rsProjeto->Situacao, $arrSituacoes) && $rsF4->count() >= 1)) {
                $this->intFaseProjeto = 4;
            }


            //FASE INICIAL
            /* nunca esteve na situacao E10 e nao ha registros na tabela captacao, os projetos por edital nao podem ser inclusos nessa condicao
             * para diferenciar pre-projetos de edital e fiscal quando o projeto nao tiver idProjeto deve-se utilizar o Mecanismo = 1
             * situacoes dessa fase = B11,B14,C10,C20,C30,D03,D11,D27
             * ENTENDIMENTO ATUAL - Nao ha registro na tabela aprovacao
             */

            //FASE DE EXECUCAO
            /* ja esteve na situacao E10 os projetos por edital nao podem ser inclusos nessa condicao
             * para diferenciar pre-projetos de edital e fiscal quando o projeto nao tiver idProjeto deve-se utilizar o Mecanismo = 1
             * ENTENDIMENTO ATUAL - Tem que haver um registro na tabela Aprovacao  com TipoAprovacao = 1 e com PortariaAprovacao.
             * pode-se utilizar a funcao (fnNrPortariaAprovacao) para checar essa informacao
             */

            //FASE FINAL
            /* 30 dias apos a data fim do periodo de execucao
             *
             */

            //FASE PROJETO ENCERRADO
            /* se tiver dados na tabela prestacao de contas com a analise de prestacao de contas ja finalizada
             * PROJETO nas situacoes E19, E22 e L03
             * Deve haver registro na tabela (tbRelatorioTecnico) com o codigo do grupo de COORD. DE PREST. DE CONTAS e COORD. GERAL DE PREST. DE CONTAS
             * e o Orgao onde o projeto esta e o 290(Arquivo)
             */
        }
    }

    public function arquivoMarcaProjetoAction()
    {
        $post = Zend_Registry::get('post');

        $observacao = $post->observacao;
        $idProposta = $post->idPreProjeto;
        $idPronac = $post->idPronac;

        $arquivoNome = $_FILES['arquivo']['name']; // nome
        $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
        $arquivoTipo = $_FILES['arquivo']['type']; // tipo
        $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho

        if (!empty($arquivoNome) && !empty($arquivoTemp)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
            $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
            $arquivoHash = Upload::setHash($arquivoTemp); // hash
        }

        if (!isset($_FILES['arquivo'])) {
            parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "upload/form-enviar-arquivo-marca?idPronac=$idPronac", "ERROR");
        }

        if (empty($_FILES['arquivo']['tmp_name'])) {
            parent::message("Favor selecionar um arquivo.", "upload/form-enviar-arquivo-marca?idPronac=$idPronac", "ERROR");
        }

        $tipos = array('bmp', 'gif', 'jpeg', 'jpg', 'png', 'raw', 'tif', 'pdf');
        if (!in_array(strtolower($arquivoExtensao), $tipos)) {
            parent::message("Favor selecionar o arquivo de Marca no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "upload/form-enviar-arquivo-marca?idPronac=$idPronac", "ERROR");
        }

        if (!empty($idPronac) && $idPronac != "0") {
            $dataString = file_get_contents($arquivoTemp);
            $arrData = unpack("H*hex", $dataString);
            $data = "0x" . $arrData['hex'];

            try {
                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                    'nmArquivo' => $arquivoNome,
                    'sgExtensao' => $arquivoExtensao,
                    'biArquivo' => $data,
                    'dsDocumento' => $observacao,
                    'idPronac' => $idPronac);

                $Arquivo = new Arquivo();
                $idArquivo = $Arquivo->inserirMarca($dadosArquivo);

                parent::message("Arquivo enviado com sucesso!", "upload/form-enviar-arquivo-marca?idPronac=$idPronac", "CONFIRM");
            } catch (Exception $e) {
                parent::message("$e", "upload/form-enviar-arquivo-marca?idPronac=$idPronac", "CONFIRM");
            }
        } else {
            parent::message("Pronac inv&aacute;lido.", "upload/form-enviar-arquivo-marca?idPronac=$idPronac", "ERROR");
        }
    }

    public function downloadAnexoRecursoPropostaAction()
    {
        // $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(true);

        $get = $this->getRequest()->getParams();

        $id_preprojeto = trim($get['id_preprojeto']);
        if (empty($id_preprojeto) || is_null($id_preprojeto)) {
            throw new Exception("Identificador da Proposta n&atilde;o foi localizado.");
        }

        $idArquivo = trim($get['idArquivo']);
        if (empty($idArquivo) || is_null($idArquivo)) {
            throw new Exception("Identificador do arquivo foi localizado.");
        }

        $recursoPropostaDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
        $recursoProposta = $recursoPropostaDbTable->obterRecursoAtual($id_preprojeto);

        if (count($recursoProposta) < 1) {
            throw new Exception("Informa&ccedil;&otilde;es do Arquivo e Proposta Cultural n&atilde;o coincidem.");
        }

        $idUsuarioSGCAcesso = $this->authIdentity['idusuario'];
        if ((!is_null($idUsuarioSGCAcesso) && !empty($idUsuarioSGCAcesso))) {
            $fnVerificarPermissao = new Autenticacao_Model_FnVerificarPermissao();
            $possuiPermissaoDeEdicao = (bool)$fnVerificarPermissao->verificarPermissaoProposta(
                $id_preprojeto,
                $idUsuarioSGCAcesso,
                false
            );
            if (!$possuiPermissaoDeEdicao) {
                throw new Exception("Usu&aacute;rio sem permiss&atilde;o para visualizar o arquivo.");
            }
        }

        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $tbArquivo = new tbArquivo();
        $dadosArquivo = $tbArquivo->buscarArquivoComBinario($idArquivo);

        if (!$dadosArquivo) {
            throw new Exception("Arquivo especificado inexistente.");
        }
 
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
        Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
        $this->_response->clearBody();                  // Limpa o corpo html
        $this->_response->clearHeaders();               // Limpa os headers do Zend

        if ($tbArquivo->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $dadosArquivo->nmArquivo . '"')
            ->setBody($dadosArquivo->biArquivo);
        } else {
            $this->getResponse()
                ->setHeader('Content-Type', $dadosArquivo->dsTipoPadronizado)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $dadosArquivo->nmArquivo . '"');
            readfile(APPLICATION_PATH . '/..' . $dadosArquivo->imdocumento);
        }
    }
}
