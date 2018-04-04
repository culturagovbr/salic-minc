<?php

/**
 * AnexarDocumentosController
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.cultura.gov.br
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 */

class AnexardocumentosController extends MinC_Controller_Action_Abstract
{

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p�gina
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            //$PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            //$PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 103; // Coordenador de An�lise
            $PermissoesGrupo[] = 118; // Componente da Comiss�o
            //$PermissoesGrupo[] = 119; // Presidente da Mesa
            //$PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o
        } // fecha if
        else { // caso o usu�rio n�o esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }
        parent::init(); // chama o init() do pai GenericControllerNew
    }

    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        // despacha para buscarpronac.phtml
        $pronac = $this->_request->getParam("idpronac");
        $this->_redirect("anexardocumentos/documentos-anexados/idpronac/$pronac");
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];
        //-------------------------------------------------------------------------------------------------------------
        $reuniao = new Reuniao();
        $ConsultaReuniaoAberta = $reuniao->buscar(array("stEstado = ?" => 0));
        if ($ConsultaReuniaoAberta->count() > 0) {
            $ConsultaReuniaoAberta = $ConsultaReuniaoAberta->current()->toArray();
            $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
            //---------------------------------------------------------------------------------------------------------------
            $votantes = new Votante();
            $exibirVotantes = $votantes->selecionarvotantes($ConsultaReuniaoAberta['idNrReuniao']);
            if (count($exibirVotantes) > 0) {
                foreach ($exibirVotantes as $votantes) {
                    $dadosVotante[] = $votantes->idAgente;
                }
                if (count($dadosVotante) > 0) {
                    if (in_array($idagente, $dadosVotante)) {
                        $this->view->votante = true;
                    } else {
                        $this->view->votante = false;
                    }
                }
            }
        } else {
            parent::message("N&atilde;o existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
        }
    }

    /**
     * M�todo com o formul�rio para buscar o PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function buscardocumentosAction()
    {
        // recebe o pronac via get
        $idpronac = $this->_request->getParam("idpronac");
        $this->view->idpronac = $idpronac;

        try {
            // verifica se o pronac veio vazio
            if (empty($idpronac)) {
                throw new Exception("Por favor, informe o PRONAC!");
            }
            // valida o n�mero do pronac
            elseif (!is_numeric($idpronac) || strlen($idpronac) > 20) {
                throw new Exception("O N&uacute;mero do PRONAC &eacute; inv&aacute;lido!");
            } else {
//                        die('fasdfasd');
                // integra��o MODELO e VIS�O

                $Projetos = new Projetos();
                $resultPronac = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac));

                // caso o PRONAC n�o esteja cadastrado
                if (!$resultPronac) {
                    throw new Exception("Registro n&atilde;o encontrado!");
                }
                // caso o PRONAC esteja cadastrado, vai para a p�gina de busca
                // dos seus documentos (comprovantes)
                else {
                    // pega o id do pronac
                    $idpronac = $resultPronac[0]->IdPRONAC;
                    $pronac = $resultPronac[0]->AnoProjeto.$resultPronac[0]->Sequencial;
                    $buscarpronac = $resultPronac;
                    // busca os documentos (comprovantes) do pronac

                    $Documentos = new Proposta_Model_DbTable_DocumentosProjeto();
                    $resultComprovantes = $Documentos->documentosAnexados($idpronac);

//                    $resultComprovantes = AnexardocumentosDAO::buscarArquivos($idpronac);


                    // caso n�o existam comprovantes cadastrados
                    if (count($resultComprovantes) == 0) {
                        $this->view->message = "Nenhum arquivo anexado ao PRONAC N� " . $pronac . "!";
                        $this->view->message_type = "ALERT";
                        $this->view->buscarpronac = $buscarpronac;
                    } else {
                        // manda os comprovantes para a vis�o
                        $this->view->buscarcomprovantes = $resultComprovantes;
                        $this->view->buscarpronac = $buscarpronac;
                        $auth = Zend_Auth::getInstance(); // pega a autentica��o
                        $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
                        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
                        $idagente = $idagente['idAgente'];
                        $reuniao = new Reuniao();
                        $ConsultaReuniaoAberta = $reuniao->buscar(array("stEstado = ?" => 0));
                        if ($ConsultaReuniaoAberta->count() > 0) {
                            $ConsultaReuniaoAberta = $ConsultaReuniaoAberta->current()->toArray();
                            $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
                            //---------------------------------------------------------------------------------------------------------------
                            $votantes = new Votante();
                            $exibirVotantes = $votantes->selecionarvotantes($ConsultaReuniaoAberta['idNrReuniao']);
                            if (count($exibirVotantes) > 0) {
                                foreach ($exibirVotantes as $votantes) {
                                    $dadosVotante[] = $votantes->idAgente;
                                }
                                if (count($dadosVotante) > 0) {
                                    if (in_array($idagente, $dadosVotante)) {
                                        $this->view->votante = true;
                                    } else {
                                        $this->view->votante = false;
                                    }
                                }
                            }
                        } else {
                            parent::message("N�o existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
                        }
                    }
                }
            } // fecha else
        } // fecha try
        catch (Zend_Exception $e) {
            die("ERRO:".$e->getMessage());
        }
    }
    
    public function documentosAnexadosAction()
    {
        $idPronac = $this->_request->getParam("idpronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $projeto = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $this->view->projeto = $projeto;

        $tbDoc = new paDocumentos();
        $rs = $tbDoc->marcasAnexadas($idPronac);
        $this->view->registros = $rs;
        
        $this->view->idPronac = $idPronac;
    }

    // fecha m�todo buscardocumentosAction()

    public function downdocAction()
    {
        $id = $this->_request->getParam("id");
        $tipo = $this->_request->getParam("tipo");
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        $resultado = AnexardocumentosDAO::uploadDocumento($id, $tipo);

        if (!$resultado) {
            $this->view->message = 'N�o foi poss�vel abrir o arquivo!';
            $this->view->message_type = 'ERROR';
        } else {
            // l� os cabe�alhos formatado
            foreach ($resultado as $r) {
                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
                $this->_response->clearBody();                  // Limpa o corpo html
                $this->_response->clearHeaders();               // Limpa os headers do Zend
                $ext = explode('.', $r->noarquivo);

                $nome = $ext['0'];
                $extencao = $ext[1];
                if ($tipo != 'docProp') {
                    $binario = base64_decode($r->imdocumento);
                } else {
                    $binario = $r->imdocumento;
                }
                $this->getResponse()
                        ->setHeader('Content-Type', $extencao)
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->noarquivo . '"')
                        ->setHeader("Connection", "close")
                        ->setHeader("Content-transfer-encoding", "binary")
                        ->setHeader("Cache-control", "private")
                        ->setBody($binario);
            } // fecha foreach
        }
    }

    /**
     * M�todo para buscar os documentos (comprovantes) do PRONAC
     * @access public
     * @param void
     * @return void
     */
    public function cadastrardocumentosAction()
    {
        $this->view->combotipodocumento = Tipodocumento::buscar();

        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post = Zend_Registry::get('post');
            $pronac = $post->pronac;
            $tipoDocumento = $post->tipoDocumento;
            $titulo = $post->titulo;
            $descricao = $post->descricao;

            // pega as informa��es do arquivo
            $arquivoNome = $_FILES['arquivo']['name']; // nome
            $arquivoTemp = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
            $arquivoTipo = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho
            if (!empty($arquivoNome)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
            }
            if (!empty($arquivoTemp)) {
                $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash = Upload::setHash($arquivoTemp); // hash
            }

            try {
                // integra��o MODELO e VIS�O
                // busca o PRONAC de acordo com o id no banco
                $resultado = Pronac::buscar($pronac);

                // caso o PRONAC n�o esteja cadastrado
                if (!$resultado) {
                    parent::message("Registro n�o encontrado!", "buscarpronac");
                }
                // caso o PRONAC esteja cadastrado, vai para a p�gina de busca
                else {
                    $this->view->buscarpronac = $resultado;
                }

                // valida os campos vazios
                if (empty($tipoDocumento)) {
                    throw new Exception("Por favor, informe o tipo de documento!");
                } elseif (empty($descricao)) {
                    throw new Exception("Por favor, informe a descri��o do documento!");
                } elseif (empty($arquivoTemp)) { // nome do arquivo
                    throw new Exception("Por favor, informe o arquivo!");
                } elseif ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                        $arquivoTipo == 'application/exe' || $arquivoTipo == 'application/x-exe' ||
                        $arquivoTipo == 'application/dos-exe') { // extens�o do arquivo
                    throw new Exception("A extens�o do arquivo � inv�lida!");
                } elseif ($arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    throw new Exception("O arquivo n�o pode ser maior do que 10MB!");
                }
                // faz o cadastro no banco de dados
                else {
                    // cadastra dados do arquivo
                    $sql = "INSERT INTO BDCORPORATIVO.scCorp.tbArquivo (nmArquivo, sgExtensao, dsTipo, nrTamanho, dtEnvio, dsHash, stAtivo) " .
                            "VALUES ('" . $arquivoNome . "', '" . $arquivoExtensao . "', '" . $arquivoTipo . "', '" . $arquivoTamanho . "', GETDATE(), '" . $arquivoHash . "', 'A')";
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB :: FETCH_OBJ);
                    $resultado = $db->query($sql);

                    // pega o id do arquivo
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB :: FETCH_OBJ);
                    $idGerado = $db->fetchOne("SELECT MAX(idArquivo) AS id FROM BDCORPORATIVO.scCorp.tbArquivo");

                    // insere o bin�rio do arquivo
                    $sql = "INSERT INTO BDCORPORATIVO.scCorp.tbArquivoImagem " .
                            "VALUES ($idGerado, $arquivoBinario)";
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB :: FETCH_OBJ);
                    $resultado = $db->query($sql);

                    // insere informa��es do documento
                    $sql = "INSERT INTO BDCORPORATIVO.scSac.tbComprovanteExecucao (idPRONAC, idTipoDocumento, nmComprovante, dsComprovante, idArquivo, idSolicitante, dtEnvioComprovante, stParecerComprovante, stComprovante) " .
                            "VALUES ($pronac, $tipoDocumento, '$titulo', '$descricao', $idGerado, 9997, GETDATE(), 'AG', 'A')";
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB :: FETCH_OBJ);
                    $resultado = $db->query($sql);

                    if ($resultado) {
                        parent::message("Cadastro realizado com sucesso!", "anexardocumentos/buscardocumentos?pronac=" . $pronac);
                    } else {
                        throw new Exception("Erro ao realizar cadastro");
                    }
                }
            } // fecha try
            catch (Exception $e) {
                $this->view->message = $e->getMessage();
                $this->view->message_type = "ERROR";
                $this->view->tipoDocumento = $tipoDocumento;
                $this->view->titulo = $titulo;
                $this->view->descricao = $descricao;
            }
        }
        // quando a p�gina � aberta
        else {
            // recebe o pronac via get
            $get = Zend_Registry::get('get');
            $pronac = $get->pronac;

            try {
                // verifica se o pronac veio vazio
                if (empty($pronac)) {
                    parent::message("Por favor, informe o PRONAC!", "buscarpronac");
                } else {
                    // integra��o MODELO e VIS�O
                    // busca o PRONAC de acordo com o id no banco
                    $resultado = Pronac::buscar($pronac);

                    // caso o PRONAC n�o esteja cadastrado
                    if (!$resultado) {
                        parent::message("Registro n�o encontrado!", "buscarpronac");
                    }
                    // caso o PRONAC esteja cadastrado, vai para a p�gina de busca
                    else {
                        $this->view->buscarpronac = $resultado;
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                $this->view->message = $e->getMessage();
            }
        }
    }
}
