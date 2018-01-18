<?php

/**
 * Controller Anexar Documentos - Lado do Minc
 * @author Equipe RUP - Politec
 * @since 16/01/2013
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 */
class AnexardocumentosmincController extends MinC_Controller_Action_Abstract
{
    private $getIdAgente  = 0;
    private $getIdGrupo   = 0;
    private $getIdOrgao   = 0;
    private $intTamPag = 10;

    public function init()
    {

        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 92;
        $PermissoesGrupo[] = 93;
        $PermissoesGrupo[] = 97;
        $PermissoesGrupo[] = 100;
        $PermissoesGrupo[] = 103;
        $PermissoesGrupo[] = 104;
        $PermissoesGrupo[] = 106;
        $PermissoesGrupo[] = 110;
        $PermissoesGrupo[] = 113;
        $PermissoesGrupo[] = 115;
        $PermissoesGrupo[] = 121;
        $PermissoesGrupo[] = 122;
        $PermissoesGrupo[] = 123;
        $PermissoesGrupo[] = 125;
        $PermissoesGrupo[] = 126;
        $PermissoesGrupo[] = 127;
        $PermissoesGrupo[] = 131;
        $PermissoesGrupo[] = 132;
        $PermissoesGrupo[] = 134;
        $PermissoesGrupo[] = 135;
        $PermissoesGrupo[] = 136;
        $PermissoesGrupo[] = 137;
        $PermissoesGrupo[] = 138;
        $PermissoesGrupo[] = 139;
        $PermissoesGrupo[] = 148;
        $PermissoesGrupo[] = 151;
        parent::perfil(1, $PermissoesGrupo);

        $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
        $auth = Zend_Auth::getInstance(); // pega a autentica��o

        //SE CAIU A SECAO REDIRECIONA
        if (!$auth->hasIdentity()) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl();
            JS::redirecionarURL($url);
        }

        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idUsuarioLogado = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->idUsuarioLogado = $idUsuarioLogado;
        $this->getIdAgente = $idagente['idAgente'];
        $this->getIdGrupo  = $GrupoAtivo->codGrupo;
        $this->getIdOrgao  = $GrupoAtivo->codOrgao;
        parent::init();
    }

    public function indexAction()
    {
        $tbTipoDocumento = new tbTipoDocumentoBDCORPORATIVO();
        $result = $tbTipoDocumento->buscar(array(), array(2));
        $this->view->tpDocumentos = $result;
    }

    public function buscarProjetosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $pronac = Mascara::delMaskCPFCNPJ($_POST['pronac']);

        $projetos = new Projetos();
        $result = $projetos->buscarIdPronac($pronac);

        if (!empty($result)) {
            $dadosProjeto = $projetos->buscarTodosDadosProjeto($result->IdPRONAC);
            $dados = array();
            if ($dadosProjeto[0]->Orgao == $this->getIdOrgao) {
                $dados['NomeProjeto'] = utf8_encode($dadosProjeto[0]['NomeProjeto']);
                $jsonEncode = json_encode($dados);
                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dados));
            } else {
                $dados = array();
                $dados['msg'] = utf8_encode('<span style="color:red;">Usu&aacute;rio sem autoriza&ccedil;&atilde;o no org&atilde;o do projeto</span>');
                $jsonEncode = json_encode($dados);
                $this->_helper->json(array('resposta'=>false,'conteudo'=>$dados));
            }
        } else {
            $dados = array();
            $dados['msg'] = utf8_encode('<span style="color:red;">Projeto n&atilde;o encontrado.</span>');
            $jsonEncode = json_encode($dados);
            $this->_helper->json(array('resposta'=>false,'conteudo'=>$dados));
        }
        $this->_helper->viewRenderer->setNoRender(true);
        ;
    }

    public function buscarProjetosAnexosAction()
    {
        $this->_helper->layout->disableLayout();
        $pronac = Mascara::delMaskCPFCNPJ($_POST['pronac']);

        $projetos = new Projetos();
        $result = $projetos->buscarIdPronac($pronac);

        if (!empty($result)) {
            $dadosProjeto = $projetos->buscarTodosDadosProjeto($result->IdPRONAC);
            $dados = array();
            if ($dadosProjeto[0]->Orgao == $this->getIdOrgao) {
                $dados['NomeProjeto'] = utf8_encode($dadosProjeto[0]['NomeProjeto']);

                $tbDoc = new paDocumentos();
                $rs = $tbDoc->marcasAnexadas($dadosProjeto[0]->IdPRONAC);
                $dados['Anexos'] = array();
                $i = 0;
                foreach ($rs as $key => $value) {
                    $dados['Anexos'][$key]['idPronac'] = $value->idPronac;
                    $dados['Anexos'][$key]['Anexado'] = $value->Anexado;
                    $dados['Anexos'][$key]['Data'] = Data::tratarDataZend($value->Data, 'Brasileira', true);
                    $dados['Anexos'][$key]['Descricao'] = utf8_encode($value->Descricao);
                    $dados['Anexos'][$key]['idDocumentosAgentes'] = $value->idDocumentosAgentes;
                    $dados['Anexos'][$key]['NoArquivo'] = utf8_encode($value->NoArquivo);
                    $dados['Anexos'][$key]['AgenteDoc'] = $value->AgenteDoc;
                    $i++;
                }

                $jsonEncode = json_encode($dados);
                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dados));
            } else {
                $dados = array();
                $dados['msg'] = utf8_encode('<span style="color:red;">Usu&aacute;rio sem autoriza&ccedil;&atilde;o no org&atilde;o do projeto</span>');
                $jsonEncode = json_encode($dados);
                $this->_helper->json(array('resposta'=>false,'conteudo'=>$dados));
            }
        } else {
            $dados = array();
            $dados['msg'] = utf8_encode('<span style="color:red;">Projeto n�o encontrado.</span>');
            $jsonEncode = json_encode($dados);
            $this->_helper->json(array('resposta'=>false,'conteudo'=>$dados));
        }
        $this->_helper->viewRenderer->setNoRender(true);
        ;
    }

    public function cadastrarDocumentoAction()
    {
        $pronac = $this->_request->getParam("Pronac");
        $tpDocumento = $this->_request->getParam("tpDocumento");
        if (empty($pronac) || empty($tpDocumento)) {
            parent::message("Favor preencher os dados obrigat&oacute;rios!", "anexardocumentosminc", "ERROR");
        }

        try {
            $projetos = new Projetos();
            $dadosProjeto = $projetos->buscarIdPronac($pronac);

            if (!empty($_FILES['arquivo']['tmp_name'])) {
                $arquivoNome     = $_FILES['arquivo']['name']; // nome
                $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
                $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
                $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                }

                if (!isset($_FILES['arquivo'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "anexardocumentosminc", "ERROR");
                }

                if (empty($_FILES['arquivo']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo.", "anexardocumentosminc", "ERROR");
                }

                $tipos = array('pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo no formato PDF!", "anexardocumentosminc", "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'biArquivo'         => $data,
                        'dsDocumento'       => 'Anexar Documento - MINC',
                        'idPronac'          => $dadosProjeto->IdPRONAC,
                        'idTipoDocumento'   => $tpDocumento);

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);
            }
            parent::message("Anexo cadastrado com sucesso!", "anexardocumentosminc", "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "anexardocumentosminc", "ERROR");
        }
    }

    public function excluirAction()
    {
    }

    public function excluirArquivoAction()
    {
        $this->_helper->layout->disableLayout();
        $post = Zend_Registry::get('post');

        $vwAnexarComprovantes = new vwAnexarComprovantes();
        $resultado = $vwAnexarComprovantes->excluirArquivo($post->id);
        if ($resultado) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
        ;
    }
}
