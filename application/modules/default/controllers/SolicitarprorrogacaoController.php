<?php 

/**
 * @author Equipe RUP - Politec
 * @since 09/01/2013
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright 2010 - Ministério da Cultura - Todos os direitos reservados.
 */
class SolicitarprorrogacaoController extends MinC_Controller_Action_Abstract
{
    private $idUsuario = 0;
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::perfil(4);
        
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autenticação
        $this->idUsuario = $auth->getIdentity()->IdUsuario;
        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }
        $this->idPronac = $idpronac;
        $this->verificarPermissaoAcesso(false, true, false);
        parent::init();
    }

    public function indexAction()
    {
        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $this->idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
        $this->view->idPronac = $this->idPronac;

        $DadosDatasProjeto = $projetos->buscarDatasPrazos($this->idPronac);
        $this->view->DatasProjeto = $DadosDatasProjeto;
    }

    public function cadastrarProrrogacaoAction()
    {
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscar(array('idPronac = ?' => $_POST['pronac']))->current();

        $dataI = explode('/', $_POST['dtInicio']);
        $dtI = checkdate($dataI[1], $dataI[0], $dataI[2]);
        if (!$dtI) {
            parent::message("Data Início inválida.", "solicitarprorrogacao", "ERROR");
        }

        $dataF = explode('/', $_POST['dtFinal']);
        $dtF = checkdate($dataF[1], $dataF[0], $dataF[2]);
        if (!$dtF) {
            parent::message("Data Final inválida.", "solicitarprorrogacao", "ERROR");
        }

        $pa = new paChecklistSolicitacaoProrrogacaoPrazo();
        $resutadoPA = $pa->checkSolicitacao($_POST['pronac'], Data::dataAmericana($_POST['dtInicio']), Data::dataAmericana($_POST['dtFinal']), 'I');

        if (count($resutadoPA)>0) {
            $Projetos = new Projetos();
            $this->view->dadosProjeto = $Projetos->buscar(array('IdPRONAC = ?'=>$_POST['pronac']));
            $this->view->Erros = $resutadoPA;
        } else {
//            $idDocumento = null;
            $idDocumento = '';

            if (!empty($_FILES['arquivo']['tmp_name'])) {
                $arquivoNome     = $_FILES['arquivo']['name']; // nome
                $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome temporário
                $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
                $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensão
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // binário
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                }

                if (!isset($_FILES['arquivo'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "solicitarprorrogacao", "ERROR");
                }

                if (empty($_FILES['arquivo']['tmp_name'])) {
                    parent::message("Falha ao anexar o arquivo.", "solicitarprorrogacao", "ERROR");
                }

                $tipos = array('pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo no formato PDF!", "solicitarprorrogacao", "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                    'nmArquivo'         => $arquivoNome,
                    'sgExtensao'        => $arquivoExtensao,
                    'biArquivo'         => $data,
                    'dsDocumento'       => 'Cadastro de Prorrogação de Prazo de Captação',
                    'idPronac'          => $_POST['pronac'],
                    'idTipoDocumento'   => 27
                );

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);

                $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                $dadosDocumento = $DocumentoProjeto->buscar(array('idPronac =?'=>$_POST['pronac'],'idTipoDocumento =?'=>27), array('idDocumento DESC'));
                $idDocumento = $dadosDocumento[0]->idDocumento;
            }

            $dados = array(
                'idPronac' => $DadosProjeto->IdPRONAC,
                'AnoProjeto' => $DadosProjeto->AnoProjeto,
                'Sequencial' => $DadosProjeto->Sequencial,
                'DtInicioExecucao' => $DadosProjeto->DtInicioExecucao,
                'DtFimExecucao' => $DadosProjeto->DtFimExecucao,
                'Justificativa' => $_POST['justificativa'],
                'idUsuario' => $this->idUsuario,
                'idDocumento' => $idDocumento,
                'DtInicio' => Data::dataAmericana($_POST['dtInicio']),
                'DtFinal' => Data::dataAmericana($_POST['dtFinal'])
            );
            $vw = new vwSolicitarProrrogacaoPrazoCaptacao();
            $vw->inserir($dados);

            parent::message("Pedido de prorrogação enviado ao Ministério da Cultura com sucesso!", "consultardadosprojeto/index?idPronac=". Seguranca::encrypt($DadosProjeto->IdPRONAC), "CONFIRM");
        }
    }
}
