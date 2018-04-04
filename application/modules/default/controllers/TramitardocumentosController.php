<?php

/**
 * ManterAgentesController
 * @author Equipe RUP - Politec
 * @since 09/08/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */
class TramitardocumentosController extends MinC_Controller_Action_Abstract
{

    /**
     * @var integer (vari�vel com o id do usu�rio logado)
     * @access private
     */
    private $getIdUsuario = 0;
    private $codOrgao = null;
    private $intTamPag = 10;

    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        // define as permis�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 90; // Protocolo - Documento
        $PermissoesGrupo[] = 91; // Protocolo - Recebimento
        $PermissoesGrupo[] = 93; // Coordenador de Parecer
        $PermissoesGrupo[] = 97; // Gestor Salic
        $PermissoesGrupo[] = 103; // Coordenador de An�lise
        $PermissoesGrupo[] = 104; // Protocolo - (Envio / Recebimento)
        $PermissoesGrupo[] = 109; // Arquivo
        $PermissoesGrupo[] = 110; // T�cnico de An�lise
        $PermissoesGrupo[] = 113; // Coordenador de Arquivo
        $PermissoesGrupo[] = 114; // Coordenador de Editais
        $PermissoesGrupo[] = 115; // Atendimento Representa��es
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        $PermissoesGrupo[] = 124; // T�cnico de Presta��o de Contas
        $PermissoesGrupo[] = 125; // Coordenador de Presta��o de Contas
        $PermissoesGrupo[] = 126; // Coordenador Geral de Presta��o de Contas
        $PermissoesGrupo[] = 127; // Coordenador Geral de An�lise
        $PermissoesGrupo[] = 128; // T�cnico de Portaria
        $PermissoesGrupo[] = 132; // Chefe de Divis�o
        $PermissoesGrupo[] = 134; // Coordenador de Fiscaliza��o
        $PermissoesGrupo[] = 135; // T�cnico de Fiscaliza��o
        $PermissoesGrupo[] = 136; // Coordenador de Entidade Vinculada
        $PermissoesGrupo[] = 137; // Coordenador de Pronac
        $PermissoesGrupo[] = 138; // Coordenador de Avalia��o
        $PermissoesGrupo[] = 139; // T�cnico de Avalia��o
        parent::perfil(1, $PermissoesGrupo);

        // pega o idAgente do usu�rio logado
        if (isset($auth->getIdentity()->usu_codigo)) {
            $idusuario = $auth->getIdentity()->usu_codigo;
            //$idorgao 	= $auth->getIdentity()->usu_orgao;

            $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
            //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
            $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
            $this->codOrgao = $GrupoAtivo->codOrgao;

            $this->view->codOrgao = $this->codOrgao;
            $this->view->idUsuarioLogado = $idusuario;

            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $auth->getIdentity()->usu_codigo;
            } else {
                $this->getIdUsuario = 0;
            }

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal/index", "ALERT");
            }
        } else {
            parent::perfil(4, $PermissoesGrupo);
        }
        parent::init();
    }

    public function indexAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        if ($GrupoAtivo->codGrupo == 90) { //Protocolo - Documento
            $this->_redirect("tramitardocumentos/despachar");
        } else {
            $this->_redirect("tramitardocumentos/receber");
        }
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
    }

    public function gerarxlsAction()
    {
        $this->_helper->layout->disableLayout();
    }

    public function imprimirguiaAction()
    {

        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $this->getIdUsuario;

        /*         * ************************************************************** */
        $this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender();

        $get = Zend_Registry::get('get');
        $idLote = $get->idLote;

        $docs = TramitarDocumentosDAO::buscarDocumentosEnviados(null, $idLote, 1);
        $this->view->docs = $docs;

        $buscaUsuario = UsuarioDAO::buscarUsuario($docs[0]->UsuarioEmissor);
        $nome = $buscaUsuario[0]->usu_nome;

        $this->view->Origem = $docs[0]->Origem;
        $this->view->Destino = $docs[0]->Destino;
        $this->view->Emissor = $nome;
        $this->view->idLote = $docs[0]->idLote;
    }

    public function despacharAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->codOrgao = $GrupoAtivo->codOrgao;

        $this->view->codOrgao = $this->codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $this->view->orgao = TramitarDocumentosDAO::buscarDocumentosCadastradosOrgao($this->codOrgao);
        $this->view->tipos = TramitarDocumentosDAO::listaTipoDocumentos();
    }

    public function despachardocAction()
    {
        $limiteTamanhoArq = 1024 * 1024 * 10;
        $arquivoNome = $_FILES['arquivo']['name']; // nome
        $arquivoTipo = $_FILES['arquivo']['type']; // tipo
        $arquivoTamanho = $_FILES['arquivo']['size']; // tamanho
        $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o

        $tipos = array('pdf');
        if (!in_array(strtolower($arquivoExtensao), $tipos)) {
            parent::message("O arquivo deve ser PDF!", "/tramitardocumentos/despachar", "ERROR");
        } elseif ($arquivoTamanho > $limiteTamanhoArq) {
            parent::message("O arquivo deve ser menor que 10 MB", "/tramitardocumentos/despachar", "ERROR");
        } else {

            /** Usuario Logado *********************************************** */
            $auth = Zend_Auth::getInstance(); // instancia da autentica��o
            $idusuario = $auth->getIdentity()->usu_codigo;
            //$idorgao 	= $auth->getIdentity()->usu_orgao;

            $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
            //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
            $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
            $this->view->codOrgao = $codOrgao;
            $this->view->idUsuarioLogado = $idusuario;
            /*             * *************************************************************** */

            $idpronac = $this->_request->getParam("idpronac");
            $pronac = $this->_request->getParam("pronac");
            $tipo_doc = $this->_request->getParam("tipo_doc");
            $cod_ect = $this->_request->getParam("cod_ect");
            $idDestino = $this->_request->getParam("idDestino");

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

            $dados = "Insert into SAC.dbo.tbDocumento
                          (idPronac, stEstado, imDocumento, idTipoDocumento, idUsuario, dtDocumento, NoArquivo, TaArquivo, idUsuarioJuntada, dtJuntada, idUnidadeCadastro, CodigoCorreio, biDocumento)
                          values
                          (" . $idpronac . ", 0, null, " . $tipo_doc . ", " . $idusuario . ", GETDATE(), '" . $arquivoNome . "', " . $arquivoTamanho . ", null, null, " . $this->codOrgao . ", '" . $cod_ect . "', " . $arquivoBinario . ")
                           ";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);

            try {
                //$db->beginTransaction();

                if (empty($pronac)) {
                    parent::message("Por favor, informe o PRONAC!", "tramitardocumentos/despachar", "ALERT");
                } elseif (empty($arquivoTemp)) { // nome do arquivo
                    parent::message("Por favor, informe o arquivo!!", "tramitardocumentos/despachar", "ALERT");
                } elseif (empty($arquivoTemp)) { // nome do arquivo
                    parent::message("Por favor, informe o arquivo!", "tramitardocumentos/despachar", "ALERT");
                } elseif (strtolower($arquivoExtensao) != 'pdf') { // extens�o do arquivo
                    parent::message("O arquivo n�o pode ser maior do que 10MB!", "tramitardocumentos/despachar", "ALERT");
                } elseif ($arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
                    parent::message("O arquivo n�o pode ser maior do que 10MB!", "tramitardocumentos/despachar", "ALERT");
                } else {
                    $resultado = TramitarDocumentosDAO::cadDocumento($dados);
                    $tbHistoricoDoc = array(
                        'idPronac' => $idpronac,
                        'idDocumento' => $resultado,
                        'idUnidade' => $idDestino,
                        'idOrigem' => $this->codOrgao,
                        'dtTramitacaoEnvio' => null,
                        'idUsuarioEmissor' => $idusuario,
                        'meDespacho' => null,
                        'idLote' => null,
                        'dtTramitacaoRecebida' => null,
                        'idUsuarioReceptor' => null,
                        'Acao' => 1,
                        'stEstado' => 1
                    );
                    $resultado2 = TramitarDocumentosDAO::cadHistorico('SAC.dbo.tbHistoricoDocumento', $tbHistoricoDoc);
                }
                //$db->commit();
                parent::message("Projeto enviado com Sucesso!", "tramitardocumentos/despachar", "CONFIRM");
            } catch (Zend_Exception $ex) {
                //$db->rollBack();
                parent::message("Erro ao realizar cadastro", "tramitardocumentos/despachar", "ERROR");
            }
        }
    }

    public function enviarAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->codOrgao = $GrupoAtivo->codOrgao;
        $this->view->codOrgao = $this->codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $this->view->orgao = TramitarDocumentosDAO::buscarDocumentosCadastradosOrgao($this->codOrgao);

        if (isset($_POST['idPro'])) {
            $idDestino = $_POST['idDes'];
            $idPronac = $_POST['idPro'];
            $justificativa = $_POST['justificativa'];
            $idLote = $_POST['idLote'];
            $idDoc = $_POST['idDoc'];
            

            $acaoAlterada = 1;
            $historicoDocumentos = new HistoricoDocumento();
            $dados = array('stEstado' => 0);
            $where = "idPronac =  $idPronac and stEstado = 1 and idDocumento <> 0 ";
            $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);

            $dadosInserir = array(
                'idPronac' => $idPronac,
                'idUnidade' => $idDestino,
                'dtTramitacaoEnvio' => date('Y-m-d H:i:s'),
                //                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                'idUsuarioEmissor' => $idusuario,
                //                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                //                        'idUsuarioReceptor' => $idusuario,
                'idLote' => $idLote,
                'Acao' => $acaoAlterada,
                'stEstado' => 1,
                'dsJustificativa' => $justificativa, //Criar campo justificativa
                'idDocumento' => $idDoc
            );

            
            $inserir = $historicoDocumentos->inserirHistoricoDocumento($dadosInserir);
            parent::message("Envio de documentos realizado com sucesso!", "tramitardocumentos/enviar", "CONFIRM");
        }
    }

    public function enviouAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */
        $data = date('Y/m/d H:i:s');
        $idDestino = $this->_request->getParam("Destino");

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();
            $docs = TramitarDocumentosDAO::buscarDocumentos($idusuario, $this->codOrgao, $idDestino);
            $NovoLote = TramitarDocumentosDAO::NovoLote();

            foreach ($docs as $d) {
                $idDocumento = $d->idDocumento;
                $idOrigem = $d->idOrigem;
                $Origem = $d->Origem;
                $idDestino = $d->idDestino;
                $idOrgao = $codOrgao;
                $Destino = $d->Destino;
                $Emissor = $d->UsuarioEmissor;
                $dtDocumentoBR = $d->dtDocumentoBR;
                $NomeProjeto = $d->NomeProjeto;
                $Pronac = $d->Pronac;
                $Processo = $d->Processo;
                $dsTipoDocumento = $d->dsTipoDocumento;
                $idPronac = $d->IdPRONAC;

                $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);

                $insere = array(
                    'idPronac' => $idPronac,
                    'idDocumento' => $idDocumento,
                    'idOrigem' => $idOrigem,
                    'idUnidade' => $idDestino,
                    'dtTramitacaoEnvio' => $data,
                    'idUsuarioEmissor' => $idusuario,
                    'meDespacho' => null,
                    'idLote' => $NovoLote,
                    'dtTramitacaoRecebida' => null,
                    'idUsuarioReceptor' => null,
                    'Acao' => 2,
                    'stEstado' => 1
                );
                $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($insere);
            }
            $db->commit();
            parent::message("Projeto enviado com sucesso!", "/tramitardocumentos/imprimirguia?idLote=" . $NovoLote, "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao enviar o documento!", "tramitardocumentos/enviar", "ERROR");
        }
    }

    public function receberAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * ************************************************************** */
        $where = array();
        if ($_GET) {
            $pronac = $this->getRequest()->getParam('pronac');
            $lote = $this->getRequest()->getParam('lote');

            if ($pronac) {
                $this->view->pronac = $pronac;
                $where[new Zend_Db_Expr('p.AnoProjeto+p.Sequencial') . ' =? '] = $pronac;
            }

            if ($lote) {
                $this->view->lote = $lote;
                $where['h.idLote = ?'] = $lote;
            }
        }

        $where['h.stEstado = ?'] = 1;
        $where['h.idDocumento > ?'] = 0;
        $where['h.Acao = ?'] = 2;
        $where['h.idUnidade = ?'] = $codOrgao;
        $order = array(8); //idLote

        $tbHistoricoDocumento = new tbHistoricoDocumento();
        $this->view->registros = $tbHistoricoDocumento->consultarTramitacoes($where, $order);
        $this->view->registros->setItemCountPerPage($this->getRequest()->getParam('nrows'));
        $this->view->registros->setCurrentPageNumber($this->getRequest()->getParam('page'));
    }

    public function anexarAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $orgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->lote = $this->getRequest()->getParam('lote', null);
        $this->view->pronac = $this->getRequest()->getParam('pronac', null);
        $this->view->registros = TramitarDocumentosDAO::buscarDocumentosRecebidosPaginator(3, 1, $orgao, $this->view->lote, $this->view->pronac);
        $this->view->registros->setItemCountPerPage($this->getRequest()->getParam('nrows'));
        $this->view->registros->setCurrentPageNumber($this->getRequest()->getParam('page'));
    }

    public function somentereceberAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $data = date('Y/m/d H:i:s');
        $get = Zend_Registry::get('get');
        $idLote = $get->idLote;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $docs = TramitarDocumentosDAO::buscarDocumentosEnviados(null, $idLote, null, 2);

            foreach ($docs as $d) {
                $idDocumento = $d->idDocumento;
                $idDestino = $d->idDestino;
                $idOrigem = $d->idOrigem;

                $histDoc = TramitarDocumentosDAO::buscarHistorico($idDocumento, 2, null, null);

                $MudarEstado = TramitarDocumentosDAO::MudaEstado($d->idDocumento);

                foreach ($histDoc as $h) {
                    $insere = array(
                        'idPronac' => $h->idPronac,
                        'idDocumento' => $h->idDocumento,
                        'idOrigem' => $idOrigem,
                        'idUnidade' => $h->idUnidade,
                        'dtTramitacaoEnvio' => $h->dtTramitacaoEnvioBR,
                        'idUsuarioEmissor' => $h->idUsuarioEmissor,
                        'meDespacho' => null,
                        'idLote' => $h->idLote,
                        'dtTramitacaoRecebida' => $data,
                        'idUsuarioReceptor' => $idusuario,
                        'Acao' => 3,
                        'stEstado' => 1
                    );

                    $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($insere);
                }
            }
            $db->commit();
            parent::message("Documento(s)recebido(s) com sucesso!", "tramitardocumentos/receber", "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao receber o documento!", "tramitardocumentos/receber", "ERROR");
        }
    }

    public function anexarloteAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $data = date('Y/m/d H:i:s');
        $get = Zend_Registry::get('get');
        $idLote = $get->idLote;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();
            $docs = TramitarDocumentosDAO::buscarDocumentosRecebidos($idusuario, $idLote);
            foreach ($docs as $d) {
                $MudarEstado = TramitarDocumentosDAO::MudaEstado($d->idDocumento);
                $insere = array(
                    'idPronac' => $d->IdPRONAC,
                    'idDocumento' => $d->idDocumento,
                    'idUnidade' => $this->codOrgao,
                    'idOrigem' => $d->idOrigem,
                    'dtTramitacaoEnvio' => $d->dtTramitacaoEnvioUS,
                    'idUsuarioEmissor' => $d->idUsuarioEmissor,
                    'meDespacho' => null,
                    'idLote' => $d->idLote,
                    'dtTramitacaoRecebida' => $data,
                    'idUsuarioReceptor' => $idusuario,
                    'Acao' => 6,
                    'stEstado' => 1
                );
                $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($insere);
            }
            $db->commit();
            parent::message("Documento(s)anexado(s) com sucesso!", "tramitardocumentos/anexar", "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao receber o documento.", "tramitardocumentos/anexar", "ERROR");
        }
    }

    public function anexardocumentoAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $data = date('Y/m/d H:i:s');
        $get = Zend_Registry::get('get');
        $idDocumento = $get->idDocumento;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $histDoc = TramitarDocumentosDAO::buscarHistorico($idDocumento, 3, null, null);

            $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);

            foreach ($histDoc as $h) {
                $insere = array(
                    'idPronac' => $h->idPronac,
                    'idDocumento' => $h->idDocumento,
                    'idUnidade' => $h->idUnidade,
                    'idOrigem' => $h->idOrigem,
                    'dtTramitacaoEnvio' => $h->dtTramitacaoEnvioBR,
                    'idUsuarioEmissor' => $h->idUsuarioEmissor,
                    'meDespacho' => null,
                    'idLote' => $h->idLote,
                    'dtTramitacaoRecebida' => $data,
                    'idUsuarioReceptor' => $idusuario,
                    'Acao' => 6,
                    'stEstado' => 1
                );

                $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($insere);
            }

            $db->commit();
            parent::message("Documento anexado com sucesso!", "tramitardocumentos/anexar", "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao anexar o documento!", "tramitardocumentos/anexar", "CONFIRM");
        }
    }

    public function desanexarloteAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $data = date('Y/m/d H:i:s');
        $get = Zend_Registry::get('get');
        $idLote = $get->idLote;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $histDoc = TramitarDocumentosDAO::buscarDocumentosAnexados($idusuario, $idLote, null);

            foreach ($histDoc as $d) {
                $idOrigem = $d->idOrigem;
                $MudarEstado = TramitarDocumentosDAO::MudaEstado($d->idDocumento);

                $insere = array(
                    'idPronac' => $d->IdPRONAC,
                    'idDocumento' => $d->idDocumento,
                    'idUnidade' => $d->idOrgao,
                    'idOrigem' => $idOrigem,
                    'dtTramitacaoEnvio' => $d->dtTramitacaoEnvioUS,
                    'idUsuarioEmissor' => $d->idUsuarioEmissor,
                    'meDespacho' => null,
                    'idLote' => $d->idLote,
                    'dtTramitacaoRecebida' => $data,
                    'idUsuarioReceptor' => $idusuario,
                    'Acao' => 3,
                    'stEstado' => 1
                );

                $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($insere);
            }

            $db->commit();
            parent::message("Documento(s)desanexado(s) com sucesso!", "tramitardocumentos/desanexar", "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao desanexar o documento.", "tramitardocumentos/desanexar", "ERROR");
        }
    }

    public function desanexardocumentoAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $data = date('Y-m-d H:i:s');
        $get = Zend_Registry::get('get');
        $idDocumento = $get->idDocumento;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $histDoc = TramitarDocumentosDAO::buscarDocumentosAnexados($idusuario, null, $idDocumento);

            $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);

            foreach ($histDoc as $h) {
                $insere = array(
                    'idPronac' => $h->IdPRONAC,
                    'idDocumento' => $h->idDocumento,
                    'idUnidade' => $h->idUnidade,
                    'idOrigem' => $h->idOrigem,
                    'dtTramitacaoEnvio' => $h->dtTramitacaoEnvio,
                    'idUsuarioEmissor' => $h->idUsuarioEmissor,
                    'meDespacho' => null,
                    'idLote' => $h->idLote,
                    'dtTramitacaoRecebida' => $data,
                    'idUsuarioReceptor' => $idusuario,
                    'Acao' => 3,
                    'stEstado' => 1
                );

                $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($insere);
            }
            $db->commit();
            parent::message("Documento desanexado com sucesso!", "tramitardocumentos/desanexar", "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao desanexar o documento!", "tramitardocumentos/desanexar", "ALERT");
        }
    }

    /**
     * RecebereanexarAction
     *
     * @access public
     * @return void
     * @todo remover chamadas diretas da model desse metodo
     */
    public function recebereanexarAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $data = date('Y/m/d H:i:s');
        $get = Zend_Registry::get('get');
        $idLote = $get->idLote;

        //$db = Zend_Db_Table::getDefaultAdapter();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $docs = TramitarDocumentosDAO::buscarDocumentosEnviados(null, $idLote, null);

            foreach ($docs as $d) {
                $idDocumento = $d->idDocumento;
                $idOrigem = $d->idOrigem;

                $histDoc = TramitarDocumentosDAO::buscarHistorico($idDocumento, 2, null, null);

                $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);

                foreach ($histDoc as $h) {
                    $recebe = array(
                        'idPronac' => $h->idPronac,
                        'idDocumento' => $h->idDocumento,
                        'idUnidade' => $h->idUnidade,
                        'idOrigem' => $idOrigem,
                        'dtTramitacaoEnvio' => $h->dtTramitacaoEnvioBR,
                        'idUsuarioEmissor' => $h->idUsuarioEmissor,
                        'meDespacho' => null,
                        'idLote' => $h->idLote,
                        'dtTramitacaoRecebida' => $data,
                        'idUsuarioReceptor' => $idusuario,
                        'Acao' => 3,
                        'stEstado' => 1
                    );

                    $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($recebe);

                    $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);

                    $anexa = array(
                        'idPronac' => $h->idPronac,
                        'idDocumento' => $h->idDocumento,
                        'idUnidade' => $h->idUnidade,
                        'idOrigem' => $idOrigem,
                        'dtTramitacaoEnvio' => $h->dtTramitacaoEnvioBR,
                        'idUsuarioEmissor' => $h->idUsuarioEmissor,
                        'meDespacho' => null,
                        'idLote' => $h->idLote,
                        'dtTramitacaoRecebida' => $data,
                        'idUsuarioReceptor' => $idusuario,
                        'Acao' => 6,
                        'stEstado' => 1
                    );

                    $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($anexa);
                }
            }

            $db->commit();
            parent::message("Documento(s)recebido(s) e anexado(s) com sucesso!", "tramitardocumentos/receber", "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao receber o documento!", "tramitardocumentos/receber", "ERROR");
        }
    }

    public function recusarAction()
    {

        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        if ($_POST) {
            $idDocumento = $_POST['idDoc'];
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $acao = '2,3';
            try {
                $db->beginTransaction();
                $docs = TramitarDocumentosDAO::buscarDocumentoUnico($idDocumento, $acao);

                foreach ($docs as $d) {
                    $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);
                    $insere = array(
                        'idPronac' => $d->IdPRONAC,
                        'idDocumento' => $d->idDocumento,
                        'idUnidade' => $d->idUnidade,
                        'idOrigem' => $d->idOrigem,
                        'dtTramitacaoEnvio' => $d->dtTramitacaoEnvioUS,
                        'idUsuarioEmissor' => $d->idUsuarioEmissor,
                        'meDespacho' => null,
                        'idLote' => $d->idLote,
                        'dtTramitacaoRecebida' => new Zend_Db_Expr('GETDATE()'),
                        'idUsuarioReceptor' => $idusuario,
                        'Acao' => 4,
                        'stEstado' => 1,
                        'dsJustificativa' => $_POST['justificativa']
                    );
                    $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($insere);
                }
                $db->commit();
                parent::message("Documento recusado com sucesso!", "tramitardocumentos/receber", "CONFIRM");
            } catch (Zend_Exception $ex) {
                $db->rollBack();
                parent::message("N&atilde;o foi poss&iacute;vel recusar o Documento!", "tramitardocumentos/receber", "ERROR");
            }
        }
    }

    public function consultarAction()
    {
        $orgaos = new Orgaos();
        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;
    }

    public function guiasAction()
    {

        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * ************************************************************** */

        /*         * ************************************************************** */
        $orgaos = new Orgaos();
        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;

        $where = array();
        $where['h.stEstado = ?'] = 1;
        $where['h.idDocumento > ?'] = 0;
        $where['h.Acao = ?'] = 2;
        $where['h.idOrigem = ?'] = $codOrgao;
        $where['h.idUsuarioEmissor = ?'] = $idusuario;
        $order = array(8); //idLote

        $tbHistoricoDocumento = new tbHistoricoDocumento();
        $this->view->registros = $tbHistoricoDocumento->consultarTramitacoes($where, $order);
    }

    public function buscaprojetoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $msgAjax = array();

        $pronac = $this->_request->getParam("pronac");

        $buscaProjeto = TramitarDocumentosDAO::buscaProjeto($pronac);


        if ($buscaProjeto) {
            $msgAjax[0]['msg'] = utf8_encode("ok");
            $msgAjax[0]['p'] = utf8_encode($pronac);

            foreach ($buscaProjeto as $p):

                // Fun��o para formatar o numero do Processo
                $Processo = FuncoesDoBanco::fnFormataProcesso($p->Processo);

            $msgAjax[0]['processonome'] = utf8_encode('Processo: ' . $Processo . ' - Nome do Projeto: ' . $p->nomeprojeto);
            $msgAjax[0]['localizacao'] = utf8_encode($p->localizacao);
            $msgAjax[0]['Orgao'] = utf8_encode($p->Orgao);
            $msgAjax[0]['idpronac'] = utf8_encode($p->IdPRONAC);

            endforeach;

            $this->_helper->json($msgAjax);
        } else {
            $msgAjax[0]['msg'] = utf8_encode("erro");
            $msgAjax[0]['p'] = utf8_encode($pronac);
            $this->_helper->json($msgAjax);
        }
    }

    public function abrirAction()
    {
        $id = $this->_request->getParam("id");
        ;


        // Configura��o o php.ini para 10MB
        @ini_set("mssql.textsize", 10485760);
        @ini_set("mssql.textlimit", 10485760);
        @ini_set("upload_max_filesize", "10M");

        $response = new Zend_Controller_Response_Http;

        // busca o arquivo
        $resultado = TramitarDocumentosDAO::buscarDoc($id);

        // erro ao abrir o arquivo
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

                $this->getResponse()
                        ->setHeader('Content-Type', 'application/pdf')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $r->NoArquivo . '"')
                        ->setHeader("Connection", "close")
                        ->setHeader("Content-transfer-encoding", "binary")
                        ->setHeader("Cache-control", "private");

                if ($r->biDocumento == null) {
                    $this->getResponse()->setBody(base64_decode($r->imDocumento));
                } else {
                    $this->getResponse()->setBody($r->biDocumento);
                }
            }
        }
    }

    public function consultaguiasAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        //		$this->view->lotes = TramitarDocumentosDAO::buscarLotes($this->codOrgao, 2);
        //		$docs = TramitarDocumentosDAO::buscarDocumentosEnviados($this->idUsuarioLogado,$lote->idLote, 1);
//
        function formatadata($data)
        {
            if ($data) {
                $dia = substr($data, 0, 2);
                $mes = substr($data, 3, 2);
                $ano = substr($data, 6, 4);
                $dataformatada = $ano . "/" . $mes . "/" . $dia;
            } else {
                $dataformatada = null;
            }

            return $dataformatada;
        }

        $post = Zend_Registry::get('post');
        $pronac = $post->pronac;
        $lote = (int) $post->lote;
        $destino = $post->destino;
        $dtDocI = formatadata($post->dtDocI);
        $dtDocF = formatadata($post->dtDocF);
        $dtEnvioI = formatadata($post->dtEnvioI);
        $dtEnvioF = formatadata($post->dtEnvioF);

        try {
            $busca = TramitarDocumentosDAO::buscaGuiasTramitacao($this->codOrgao, $pronac, $lote, $destino, $dtDocI, $dtDocF, $dtEnvioI, $dtEnvioF);

            if (!$busca) {
                parent::message("Nenhum Registro encontrado!", "tramitardocumentos/guias", "ALERT");
            } else {
                Zend_Paginator::setDefaultScrollingStyle('Sliding');
                Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                $paginator = Zend_Paginator::factory($busca); // dados a serem paginados
                $currentPage = $this->_getParam('page', 1);
                $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(5);


                foreach ($busca as $lote):
                    $docs = TramitarDocumentosDAO::buscarDocumentosEnviados($idusuario, $lote->idLote, null, 2);
                endforeach;


                $this->view->dados = $paginator;
                $this->view->qtdDocumentos = count($busca); // quantidade
            }
        } catch (Zend_Exception $ex) {
            parent::message($ex->getMessage(), "tramitardocumentos/guias", "ERROR");
        }
    }

    public function consultardocumentoAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $this->view->orgaoLogado = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $this->view->usuarioLogado = $auth->getIdentity()->usu_codigo;

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
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            $campo = null;
            $order = array(19);
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ====================== */
        $where = array();
        $where['h.stEstado = ?'] = 1;
        $where['h.idDocumento > ?'] = 0;

        if (isset($get->estado) && !empty($get->estado)) {
            $where['h.Acao = ?'] = $this->view->estado = $get->estado;
        }
        if (isset($get->pronac) && !empty($get->pronac)) {
            $where['p.AnoProjeto+p.Sequencial = ?'] = $this->view->pronac = $get->pronac;
        }
        if (isset($get->origem) && !empty($get->origem)) {
            $where['h.idOrigem = ?'] = $this->view->origem = $get->origem;
            $where['h.idUnidade = ?'] = $codOrgao;
        }
        if (isset($get->destino) && !empty($get->destino)) {
            $where['h.idOrigem = ?'] = $codOrgao;
            $where['h.idUnidade = ?'] = $this->view->destino = $get->destino;
        }
        if (isset($get->dtEnvioI) && !empty($get->dtEnvioI)) {
            $this->view->tipo_dtEnvio = $get->tipo_dtEnvio;
            $this->view->dtEnvioI = $get->dtEnvioI;
            $this->view->dtEnvioF = $get->dtEnvioF;
            $d1 = Data::dataAmericana($get->dtEnvioI);
            if ($get->tipo_dtEnvio == 1) {
                $where["h.dtTramitacaoEnvio BETWEEN '$d1' AND '$d1 23:59:59.999'"] = '';
            } elseif ($get->tipo_dtEnvio == 2) {
                $d2 = Data::dataAmericana($get->dtEnvioF);
                $where["h.dtTramitacaoEnvio BETWEEN '$d1' AND '$d2'"] = '';
            }
        }
        if (isset($get->dtRecebidoI) && !empty($get->dtRecebidoI)) {
            $this->view->tipo_dtRecebida = $get->tipo_dtRecebida;
            $this->view->dtRecebidoI = $get->dtRecebidoI;
            $this->view->dtRecebidoF = $get->dtRecebidoF;
            $d1 = Data::dataAmericana($get->dtRecebidoI);
            if ($get->tipo_dtRecebida == 1) {
                $where["h.dtTramitacaoRecebida BETWEEN '$d1' AND '$d1 23:59:59.999'"] = '';
            } elseif ($get->tipo_dtRecebida == 2) {
                $d2 = Data::dataAmericana($get->dtRecebidoF);
                $where["h.dtTramitacaoRecebida BETWEEN '$d1' AND '$d2'"] = '';
            }
        }
        if (isset($get->lote) && !empty($get->lote)) {
            $where['h.idLote = ?'] = $this->view->lote = $get->lote;
        }
        if (isset($get->cod_ect) && !empty($get->cod_ect)) {
            $where['doc.CodigoCorreio = ?'] = $this->view->cod_ect = $get->cod_ect;
        }

        $tbHistoricoDocumento = new tbHistoricoDocumento();
        $total = $tbHistoricoDocumento->consultarTramitacoes($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int) (($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbHistoricoDocumento->consultarTramitacoes($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag" => $pag,
            "qtde" => $this->intTamPag,
            "campo" => $campo,
            "ordem" => $ordem,
            "ordenacao" => $ordenacao,
            "novaOrdem" => $novaOrdem,
            "total" => $total,
            "inicio" => ($inicio + 1),
            "fim" => $fim,
            "totalPag" => $totalPag,
            "Itenspag" => $this->intTamPag,
            "tamanho" => $tamanho
        );

        $this->view->paginacao = $paginacao;
        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;

//            $correio = null;
//            if (isset ($post->correio)) {
//                if ( !empty ($_SESSION['correio']) ) {
//                    if ( $post->correio != $_SESSION['correio'] ) {
//                        $_SESSION['correio'] = $post->correio;
//                        $correio = $post->correio;
//                    }
//                    else {
//                        $correio = $_SESSION['correio'];
//                    }
//
//                }
//                else {
//                    $correio = $post->correio;
//                    $_SESSION['correio'] = $correio;
//
//                }
//            }elseif(!empty ($_SESSION['correio'])) {
//                $correio = $_SESSION['correio'];
//            }
    }

    public function cancelarTramitacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idHistorico = $_POST['idHistorico'];

        $HistoricoDocumento = new HistoricoDocumento();
        $rs = $HistoricoDocumento->buscar(array('idHistorico = ?' => $idHistorico))->current();

        $conclusao = false;
        switch ($rs->Acao) {
            case 1:
                $rs->Acao = 1;
                $rs->save();
                $conclusao = true;
                break;
            case 2:
                $rs->Acao = 1;
                $rs->save();
                $conclusao = true;
                break;
            default:
                break;
        }

        if ($conclusao) {
            $this->_helper->json(array('resposta' => true));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function excluirdocAction()
    {
        $get = Zend_Registry::get('get');
        $idDoc = $get->iddoc;
        $action = $get->action;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        try {
            $db->beginTransaction();

            $excluirH = TramitarDocumentosDAO::ExcluirDoc($idDoc, 'SAC.dbo.tbHistoricoDocumento');
            $excluirD = TramitarDocumentosDAO::ExcluirDoc($idDoc, 'SAC.dbo.tbDocumento');

            $db->commit();
            parent::message("Documento Excluido com sucesso!", "tramitardocumentos/" . $action, "CONFIRM");
        } catch (Zend_Exception $ex) {
            $db->rollBack();
            parent::message("Erro ao excluir o documento!", "tramitardocumentos/" . $action, "ERROR");
        }
    }

    public function solicitacoesAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;

        $cancelOrgaos = $cancelamento = TramitarDocumentosDAO::buscarCancelOrgao($codOrgao);
        $this->view->cancelOrgao = $cancelOrgaos;

        $cancelamento = TramitarDocumentosDAO::buscarCancelamento($codOrgao);
        $this->view->cancel = $cancelamento;

        if (isset($_POST['idDocumento']) && !empty($_POST['idDocumento'])) {
            $justificativa = $_POST['justificativa'];
            $idPronac = $_POST['idPronac'];
            $idUnidade = $_POST['idUnidade'];
            $idDocumento = $_POST['idDocumento'];
            $busca = TramitarDocumentosDAO::buscarHistorico($idDocumento, 2, null, null);

            foreach ($busca as $b) {
                $idLote = $b->idLote;
                $dataEnvio = $b->dtTramitacaoEnvio;
            }

            $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);
            $recebe = array(
                'idPronac' => $idPronac,
                'idOrigem' => $codOrgao,
                'idDocumento' => $idDocumento,
                'idUnidade' => $idUnidade,
                'dsJustificativa' => $justificativa,
                'idLote' => $idLote,
                'idUsuarioEmissor' => $idusuario,
                'dtTramitacaoEnvio' => $dataEnvio,
                'Acao' => 1,
                'stEstado' => 1
            );

            $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($recebe);
            parent::message("Envio de Documentos Cancelado!", "tramitardocumentos/solicitacoes", "CONFIRM");
        } else {
            if (isset($_POST['idHistorico'])) {
                $idHistorico = $_POST['idHistorico'];
                $justificativa = $_POST['justificativa'];
                $idDocumento = null;

                $busca = TramitarDocumentosDAO::buscarHistorico(null, 4, null, $idHistorico);
                foreach ($busca as $b) {
                    $idLote = $b->idLote;
                    $idDocumento = $b->idDocumento;
                    $idPronac = $b->idPronac;
                    $idUnidade = $b->idUnidade;
                    $dataEnvio = $b->dtTramitacaoEnvio;
                }

                $MudarEstado = TramitarDocumentosDAO::MudaEstado($idDocumento);
                $recebe = array(
                    'idPronac' => $idPronac,
                    'idDocumento' => $idDocumento,
                    'idUnidade' => $idUnidade,
                    'dsJustificativa' => $justificativa,
                    'idLote' => $idLote,
                    'idUsuarioEmissor' => $idusuario,
                    'dtTramitacaoEnvio' => $dataEnvio,
                    'Acao' => 2,
                    'stEstado' => 1
                );

                $gravarHistorico = TramitarDocumentosDAO::GravarHistorico($recebe);
                parent::message("Solicita��o n�o atendida!", "tramitardocumentos/solicitacoes", "CONFIRM");
            }
        }
    }

    public function desanexarAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $auth->getIdentity()->usu_codigo;
        //$idorgao 	= $auth->getIdentity()->usu_orgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->codOrgao = $GrupoAtivo->codOrgao;

        $this->view->codOrgao = $this->codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /*         * *************************************************************** */

        $this->view->lotes = TramitarDocumentosDAO::buscarLotes($this->codOrgao, 6, $idusuario);
    }
}
