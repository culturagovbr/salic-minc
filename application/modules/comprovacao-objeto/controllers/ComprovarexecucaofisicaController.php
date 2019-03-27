<?php

class ComprovacaoObjeto_ComprovarexecucaofisicaController extends MinC_Controller_Action_Abstract
{
    private $getCgcCpf = 0;

    public function init()
    {
        parent::perfil(4);

        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao
        $this->getCgcCpf = $auth->getIdentity()->Cpf;
        $this->IdUsuario = $auth->getIdentity()->IdUsuario;
        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }
        $dados['idPronac'] = $idpronac;

        //UC 13 - MANTER MENSAGENS (Habilitar o menu superior)
        $this->view->idPronac = $idpronac;
        $this->view->menumsg = 'true';

        if (!isset($_SESSION["Zend_Auth"]["storage"]->usu_codigo)) {
            $proj = new Projetos();
            $r = new tbRelatorio();
            $rt = new tbRelatorioTrimestral();
            $rc = new tbRelatorioConsolidado();
            $resp = $proj->buscar(array('IdPRONAC = ?' => $idpronac))->current();
            $this->view->resp = $resp;

            $fnDtInicioRelatorioTrimestral = new fnDtInicioRelatorioTrimestral();
            $DtLiberacao = $fnDtInicioRelatorioTrimestral->dtInicioRelatorioTrimestral($idpronac);

            $intervalo = round(Data::CompararDatas($DtLiberacao->dtLiberacao, $resp->DtFimExecucao));

            $qtdRelatorioEsperado = round($intervalo/90);
            $this->view->qtdRelatorioEsperado = $qtdRelatorioEsperado;
            $this->view->countRelTrimestral = count($r->buscar(array('idPRONAC = ? ' => $idpronac)));

            $buscarrelatorioTrimestral = count($rt->buscarRelatorioMenu($idpronac));
            $buscarrelatorioConsolidado = count($rc->buscarRelatorioConsolidado($idpronac));
            $this->view->buscarrelatorioTrimestral = $buscarrelatorioTrimestral;
            $this->view->buscarrelatorioConsolidado = $buscarrelatorioConsolidado;

            $totalReg = $r->buscar(array('idPronac = ?' => $idpronac, 'tpRelatorio = ?'=> 'T'));
            $diasExecutados = round(Data::CompararDatas($DtLiberacao->dtLiberacao));
            $qtdHabilitado = round($diasExecutados/90);
            $this->view->qtdHabilitado = $qtdHabilitado;
            $this->view->totalReg = $totalReg;
        }

        $rst = ConsultarDadosProjetoDAO::obterDadosProjeto($dados);
        if (count($rst) == '') {
            $this->view->projeto = $rst[0];
            $this->view->idpronac = $idpronac;
            if ($rst[0]->codSituacao == 'E12' || $rst[0]->codSituacao == 'E13' || $rst[0]->codSituacao == 'E15' || $rst[0]->codSituacao == 'E50' || $rst[0]->codSituacao == 'E59' || $rst[0]->codSituacao == 'E61' || $rst[0]->codSituacao == 'E62' || $rst[0]->codSituacao == 'E71') {
                $this->view->menuCompExec = 'true';
            }
        }

        $busca = new Projetos();
        $result = $busca->buscar(array('IdPRONAC = ?'=>$idpronac))->current();
        if (!empty($result)) {
            if (empty($result->idProjeto)) {
                parent::message("Somente ser&aacute; permitido comprovar execu&ccedil;&atilde;o do objeto de Projetos por meio do sistema para aqueles cadastrados a partir de Janeiro de 2009. Os outros casos dever&atilde;o ser solicitados por meio de of&iacute;cio.", "/consultardadosprojeto/?idPronac={$idpronac}", "ERROR");
                return;
            }
        }

        parent::init();
    }

    public function relatoriotrimestralAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $Projetos = new Projetos();
        $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
        $anoProjeto = $dadosProj->AnoProjeto;
        $sequencial = $dadosProj->Sequencial;

        $tblLib = new Liberacao();
        $rsLib = $tblLib->buscar(array('AnoProjeto =?'=>$anoProjeto,'Sequencial =?'=>$sequencial));
        $liberacao = $rsLib->count();
        $this->view->liberacao = $liberacao;
        if ($liberacao) {
            $intervalo = round(Data::CompararDatas($rsLib[0]['DtLiberacao'], $dadosProj->DtFimExecucao));
            $inicioPeriodo = $rsLib[0]['DtLiberacao'];
        } else {
            $intervalo = round(Data::CompararDatas($dadosProj->DtInicioExecucao, $dadosProj->DtFimExecucao));
            $inicioPeriodo = $dadosProj->DtInicioExecucao;
        }
        $this->view->inicioPeriodo = $inicioPeriodo;

        $qtdRelatorioEsperado = round($intervalo/90);
        $this->view->qtdRelatorioEsperado = $qtdRelatorioEsperado;

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $qtdRelatorioCadastrados = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac=?'=>$idpronac), true, array('nrComprovanteTrimestral')); //busca todos os relatorios
        $qtdRelCadastrados = !empty($qtdRelatorioCadastrados) ? $qtdRelatorioCadastrados->count() : 0;
        $this->view->qtdRelatorioCadastrados = $qtdRelCadastrados;
        $this->view->RelatorioCadastrados = $qtdRelatorioCadastrados;

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $liberaCadastro = false;
        if ($qtdRelatorioEsperado > $qtdRelCadastrados) {
            $liberaCadastro = true;
            foreach ($qtdRelatorioCadastrados as $rel) {
                // se algum relatorio cadastrado estiver apenas salvo, nao libera novo cadastro
                if ($rel->siComprovanteTrimestral == 1) {
                    $liberaCadastro = false;
                }
            }
        }
        $this->view->liberaCadastro = $liberaCadastro;
    }

    public function etapasDeTrabalhoAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac=?'=>$idpronac,'siComprovanteTrimestral=?'=>1));
        $this->view->DadosRelatorio = $DadosRelatorio;

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
    }

    public function metasComprovadasAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        //****** Dados da Comprovacao de Metas *****//
        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;
    }

    public function itensComprovadosAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;
    }

    public function localDeRealizacaoAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $pais = new Pais();
        $paises = $pais->buscar(array(), 'Descricao');
        $this->view->Paises = $paises;

        $uf = new Uf();
        $ufs = $uf->buscar(array(), 'Descricao');
        $this->view->UFs = $ufs;
    }

    public function cadastrarLocalRealizacaoAction()
    {
        $linkFinal = '';
        if (filter_input(INPUT_POST, 'relatorofinal')) {
            $linkFinal = '-final';
        }

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }
        $redirectUrl = "comprovacao-objeto/comprovarexecucaofisica/local-de-realizacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac);

        try {
            $Projetos = new Projetos();
            $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
            $idProjeto = $dadosProj->idProjeto;

            if (empty($idProjeto)) {
                parent::message('N&atilde;o existe idProjeto.', $redirectUrl, 'ERROR');
            }

            $AbrangenciaDAO = new Proposta_Model_DbTable_Abrangencia();

            if (filter_input(INPUT_POST, 'novoPais')) {
                if (31 == \filter_input(\INPUT_POST, 'novoPais')) { //31=Brasil
                    $idPais = filter_input(INPUT_POST, 'novoPais');
                    $idUF = filter_input(INPUT_POST, 'novoUf');
                    $idMunicipio = filter_input(INPUT_POST, 'novoMunicipio');
                    $dsJustificativa = filter_input(INPUT_POST, 'justificativaNovo');

                    //se for Brasil, o usuario deve informar a cidade e o municipio
                    if (empty($idPais) || empty($idUF) || empty($idMunicipio)) {
                        parent::message("N&atilde;o foi poss&iacute;vel cadastrar o novo local de realiza&ccedil;&atilde;o do Projeto!", "/comprovacao-objeto/comprovarexecucaofisica/local-de-realizacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }
                } else {
                    $idPais = filter_input(INPUT_POST, 'novoPais');
                    $idUF = 0;
                    $idMunicipio = 0;
                    $dsJustificativa = filter_input(INPUT_POST, 'justificativaNovo');
                }

                $Abrangencia = new Proposta_Model_DbTable_Abrangencia();
                $abrangencias = $Abrangencia->verificarIgual($idPais, $idUF, $idMunicipio, $idProjeto);

                if (0 == count($abrangencias)) {
                    $dtInicioNovo = null;
                    $dtFimNovo = null;

                    if (filter_input(INPUT_POST, 'novoDtInicioRealizacao')) {
                        $dtInicioNovo = Data::dataAmericana(filter_input(INPUT_POST, 'novoDtInicioRealizacao'));
                        $dtFimNovo = Data::dataAmericana(filter_input(INPUT_POST, 'novoDtFimRealizacao'));
                        $validacaoNovaInicio = Data::validarData(filter_input(INPUT_POST, 'novoDtInicioRealizacao'));
                        $validacaoNovaFim = Data::validarData(filter_input(INPUT_POST, 'novoDtFimRealizacao'));
                        if (!$validacaoNovaInicio || !$validacaoNovaFim) {
                            parent::message('Data inv&aacute;lida.', $redirectUrl, 'ERROR');
                        }
                    }

                    $dados = array(
                        'idProjeto' => $idProjeto,
                        'idPais' => $idPais,
                        'idUF' => $idUF,
                        'idMunicipioIBGE' => $idMunicipio,
                        'Usuario' => $this->IdUsuario,
                        'stAbrangencia' => 1,
                        'siAbrangencia' => filter_input(INPUT_POST, 'novoRealizado'),
                        'dsJustificativa' => $dsJustificativa,
                        'dtInicioRealizacao' => $dtInicioNovo,
                        'dtFimRealizacao' => $dtFimNovo
                    );
                    $success = $AbrangenciaDAO->cadastrar($dados);
                } else {
                    parent::message('Não &eacute; possível salvar o mesmo local mais de uma vez. '
                            . '(Pa&iacute;s, Uf, Munic&iacute;pio)', $redirectUrl, 'ERROR');
                }
            }

            if (!$success) {
                parent::message('Erro ao salvar os dados.', $redirectUrl, 'ERROR');
            }


            parent::message('Dados salvos com sucesso!', $redirectUrl, 'CONFIRM');
        } catch (Exception $e) {
            parent::message('Erro ao salvar os dados.', $redirectUrl, 'ERROR');
        }
    }

    /**
     * metodo para pegar o idpronac
     *
     * @return mixed|string
     */
    protected function buscarIdPronac()
    {
        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }
        return $idpronac;
    }

    public function salvarLocalRealizacaoAction()
    {
        $idpronac = $this->buscarIdPronac();

        $linkFinal = '';
        if (filter_input(INPUT_POST, 'relatoriofinal')) {
            $linkFinal = '-final';
        } elseif ($this->_request->getParam('relatoriofinal')) {
            $linkFinal = '-final';
        }
//

        try {

            $this->verificarPermissaoAcesso(false, true, false);


            $redirectUrl = "comprovacao-objeto/comprovarexecucaofisica/local-de-realizacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac);
            $redirectUrlErroData = "comprovacao-objeto/comprovarexecucaofisica/manter-local-de-realizacao-final/idpronac/".Seguranca::encrypt($idpronac);

            $AbrangenciaDAO = new Proposta_Model_DbTable_Abrangencia();

            $abrangenciaId = $this->_request->getParam('idAbrangencia');
            $abrangenciaSituacao = $this->_request->getParam('siAbrangencia');


            $dtInicio = null;
            $dtFim = null;
            if (filter_input(INPUT_POST, 'dtInicioRealizacao')) {
                $dtInicio = Data::dataAmericana(filter_input(INPUT_POST, 'dtInicioRealizacao'));
                $validacaoInicio = Data::validarData(filter_input(INPUT_POST, 'dtInicioRealizacao'));
                $dtFim = Data::dataAmericana(filter_input(INPUT_POST, 'dtFimRealizacao'));
                $validacaoFim = Data::validarData(filter_input(INPUT_POST, 'dtFimRealizacao'));

                if (!$validacaoInicio || !$validacaoFim) {
                    parent::message('Data inv&aacute;lida.', $redirectUrlErroData, 'ERROR');
                }
            }

            if (strlen(filter_input(INPUT_POST, 'dsJustificativa')) > 500) { //limite m�ximo de caracteres
                parent::message('N&uacute;mero de caracteres inv&aacute;lido. Limite de 500 caracteres!', $redirectUrlErroData, 'ERROR');
            }


            $abrangenciaRow = $AbrangenciaDAO->find($abrangenciaId)->current();

            if ($abrangenciaRow) {
                if ($abrangenciaRow->siAbrangencia != 2 && $abrangenciaSituacao == 2) {
                    $abrangenciaRow->dtInicioRealizacao = $dtInicio;
                    $abrangenciaRow->dtFimRealizacao = $dtFim;
                } elseif ($abrangenciaSituacao != 2) {
                    $abrangenciaRow->dtInicioRealizacao = null;
                    $abrangenciaRow->dtFimRealizacao = null;
                }
                if ($abrangenciaSituacao == 1) {
                    $justificativa = filter_input(INPUT_POST, 'dsJustificativa');

                    if (!empty($justificativa)) {
                        $abrangenciaRow->dsJustificativa = $justificativa;
                    }
                } else {
                    $abrangenciaRow->dsJustificativa = null;
                }

                if ($abrangenciaSituacao == 2) {
                    $abrangenciaRow->dtInicioRealizacao = $dtInicio;
                    $abrangenciaRow->dtFimRealizacao = $dtFim;
                }

                if (strtotime($abrangenciaRow->dtInicioRealizacao) > strtotime($abrangenciaRow->dtFimRealizacao)) {
                    parent::message('Data inv&aacute;lida.', $redirectUrlErroData, 'ERROR');
                }


                $abrangenciaRow->siAbrangencia = $abrangenciaSituacao;
                $abrangenciaRow->Usuario = $this->IdUsuario;
                $abrangenciaRow->save();
            }

            parent::message('Dados salvos com sucesso!', $redirectUrl, 'CONFIRM');
        } catch (Exception $e) {
            parent::message('Erro ao salvar os dados.', $redirectUrl, 'ERROR');
        }
    }

    public function excluirAbrangenciaAjaxAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idAbrangencia = (int) $post->abrg;

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        if ($tblAbrangencia->excluir($idAbrangencia)) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function planoDeDivulgacaoAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $Verificacao = new Verificacao();
        $Peca = $Verificacao->buscar(array('idTipo =?'=>1,'stEstado =?'=>1));
        $this->view->Peca = $Peca;

        $Veiculo = $Verificacao->buscar(array('idTipo =?'=>2,'stEstado =?'=>1));
        $this->view->Veiculo = $Veiculo;
    }

    public function cadastrarPlanoDivulgacaoAction()
    {
        $linkFinal = '';
        if (isset($_POST['relatorofinal']) && $_POST['relatorofinal']) {
            $linkFinal = '-final';
        }

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
            $Projetos = new Projetos();
            $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
            $idProjeto = $dadosProj->idProjeto;

            if (empty($idProjeto)) {
                parent::message("N&atilde;o existe idProjeto.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
            }

            $PlanoDeDivulgacao = new PlanoDeDivulgacao();
            foreach ($_POST['siPlanoDeDivulgacao'] as $valores) {
                $pldiv = array();
                $x = explode(':', $valores);
                $where = 'idPlanoDivulgacao = '.$x[1];
                $pldiv['siPlanoDeDivulgacao'] = $x[0];
                $pldiv['Usuario'] = $this->IdUsuario;

                if (!empty($_FILES['arquivo'.$x[1]]['tmp_name'])) {
                    $arquivoNome     = $_FILES['arquivo'.$x[1]]['name']; // nome
                    $arquivoTemp     = $_FILES['arquivo'.$x[1]]['tmp_name']; // nome tempor�rio
                    $arquivoTipo     = $_FILES['arquivo'.$x[1]]['type']; // tipo
                    $arquivoTamanho  = $_FILES['arquivo'.$x[1]]['size']; // tamanho

                    if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                        $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                        $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                        $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                    }

                    if (!isset($_FILES['arquivo'.$x[1]])) {
                        parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }

                    if (empty($_FILES['arquivo'.$x[1]]['tmp_name'])) {
                        parent::message("Favor selecionar um arquivo.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }

                    $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                    if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                        parent::message("Favor selecionar o arquivo de Marca no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }

                    $dataString = file_get_contents($arquivoTemp);
                    $arrData = unpack("H*hex", $dataString);
                    $data = "0x".$arrData['hex'];

                    // ==================== PERSISTE DADOS DO ARQUIVO =================//
                    $dadosArquivo = array(
                            'nmArquivo'         => $arquivoNome,
                            'sgExtensao'        => $arquivoExtensao,
                            'biArquivo'         => $data,
                            'dsDocumento'       => 'Comprova&ccedil;&atilde;o do Relat&oacute;rio Trimestral/Final - Plano de Divulga&ccedil;&atilde;o',
                            'idPronac'          => $idpronac,
                            'idTipoDocumento'   => 18);

                    $Arquivo = new Arquivo();
                    $Arquivo->inserirUploads($dadosArquivo);

                    $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                    $dadosDocumento = $DocumentoProjeto->buscar(array('idPronac =?'=>$idpronac,'idTipoDocumento =?'=>18), array('idDocumento DESC'));
                    $pldiv['idDocumento'] = $dadosDocumento[0]->idDocumento;
                }
                $PlanoDeDivulgacao->alterar($pldiv, $where);
            }

            if (!empty($_POST['novoPeca'])) {
                $idPeca = $_POST['novoPeca'];
                $idVeiculo = $_POST['novoVeiculo'];
                $siPlanoDeDivulgacao = $_POST['novoExecutado'];

                //se for Brasil, o usuario deve informar a cidade e o municipio
                if (empty($idPeca) || empty($idVeiculo)) {
                    parent::message("N&atilde;o foi poss&iacute;vel cadastrar o novo plano de divulga&ccedil;&atilde;o do Projeto!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $planos = $PlanoDeDivulgacao->buscar(array('idProjeto = ?'=>$idProjeto, 'idPeca = ?'=>$idPeca, 'idVeiculo = ?'=>$idVeiculo));
                if (count($planos)==0) {
                    if (!empty($_FILES['arquivoNovo']['tmp_name'])) {
                        $arquivoNome     = $_FILES['arquivoNovo']['name']; // nome
                        $arquivoTemp     = $_FILES['arquivoNovo']['tmp_name']; // nome temporario
                        $arquivoTipo     = $_FILES['arquivoNovo']['type']; // tipo
                        $arquivoTamanho  = $_FILES['arquivoNovo']['size']; // tamanho

                        if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
                            $arquivoBinario  = Upload::setBinario($arquivoTemp); // binario
                            $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                        }

                        if (!isset($_FILES['arquivoNovo'])) {
                            parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                        }

                        if (empty($_FILES['arquivoNovo']['tmp_name'])) {
                            parent::message("Favor selecionar um arquivo para o novo Plano de Divulgaç&atilde;o.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                        }

                        $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                        if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                            parent::message("Favor selecionar o arquivo de Marca no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                        }

                        $dataString = file_get_contents($arquivoTemp);
                        $arrData = unpack("H*hex", $dataString);
                        $data = "0x".$arrData['hex'];

                        // ==================== PERSISTE DADOS DO ARQUIVO =================//
                        $dadosArquivo = array(
                                'nmArquivo'         => $arquivoNome,
                                'sgExtensao'        => $arquivoExtensao,
                                'biArquivo'         => $data,
                                'dsDocumento'       => 'Comprova&ccedil;&atilde;o do Relat&oacute;rio Trimestral/Final - Plano de Divulga&ccedil;&atilde;o',
                                'idPronac'          => $idpronac,
                                'idTipoDocumento'   => 18);

                        $Arquivo = new Arquivo();
                        $Arquivo->inserirUploads($dadosArquivo);

                        $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                        $dadosDocumento = $DocumentoProjeto->buscar(array('idPronac =?'=>$idpronac,'idTipoDocumento =?'=>18), array('idDocumento DESC'));

                        $dados = array(
                            'idProjeto' => $idProjeto,
                            'idPeca' => $idPeca,
                            'idVeiculo' => $idVeiculo,
                            'Usuario' => $this->IdUsuario,
                            'siPlanoDeDivulgacao' => $siPlanoDeDivulgacao,
                            'idDocumento' => $dadosDocumento[0]->idDocumento
                        );
                        $PlanoDeDivulgacao->inserir($dados);
                    } else {
                        parent::message("Não foi poss&iacute;vel cadastrar o novo Plano de Divulga&ccedil;&atilde;o!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }
                }
            }

            parent::message("Dados salvos com sucesso!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function excluirPlanoDivulgacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idPlanoDeDivulgacao = (int) $post->plano;
        $idArquivo = (int) $post->arquivo;

        $PlanoDeDivulgacao = new PlanoDeDivulgacao();
        $dados = array(
            'siPlanoDeDivulgacao' => 0,
            'idDocumento' => new Zend_Db_Expr('null')
        );

        $divulgacao = $PlanoDeDivulgacao->buscar(array('idPlanoDivulgacao=?'=>$idPlanoDeDivulgacao))->current();
        if ($divulgacao->siPlanoDeDivulgacao == 3) {
            $return = $PlanoDeDivulgacao->delete('idPlanoDivulgacao='.$idPlanoDeDivulgacao); //($dados, $idPlanoDeDivulgacao);
        } else {
            $return = $PlanoDeDivulgacao->alterarDados($dados, $idPlanoDeDivulgacao);
        }

        if ($return) {
            $vw = new vwAnexarComprovantes();
            $resutado = $vw->excluirArquivo($idArquivo);
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function planoDeDistribuicaoAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;
    }

    public function cadastrarPlanoDistribuicaoAction()
    {
        $linkFinal = '';
        if (isset($_POST['relatorofinal']) && $_POST['relatorofinal']) {
            $linkFinal = '-final';
        }

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
            $Visao = new Visao();
            $visaoBeneficiario = $Visao->buscar(array('idAgente=?'=>$_POST['idAgente'],'Visao=?'=>199));

            if (count($visaoBeneficiario)==0) {
                $dadosAgente = array(
                    'idAgente' => $_POST['idAgente'],
                    'Visao' => 199,
                    'Usuario' => $this->IdUsuario,
                    'stAtivo' => 'A'
                );
                $Visao->inserir($dadosAgente);
            }

            $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
            $produtoBeneficiario = $tbBeneficiarioProdutoCultural->buscar(array('IdPRONAC=?'=>$idpronac,'idAgente=?'=>$_POST['idAgente'],'idPlanoDistribuicao=?'=>$_POST['produto'],'idTipoBeneficiario=?'=>$_POST['tipoDocumento']));

            if (count($produtoBeneficiario)>0) {
                parent::message("J&aacute; foi cadastrado o mesmo agente para este produto!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
            } else {
                if (!empty($_FILES['arquivo']['tmp_name'])) {
                    $arquivoNome     = $_FILES['arquivo']['name']; // nome
                    $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome temporario
                    $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
                    $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho

                    if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                        $arquivoExtensao = Upload::getExtensao($arquivoNome); // extensao
                        $arquivoBinario  = Upload::setBinario($arquivoTemp); // binario
                        $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                    }

                    if (!isset($_FILES['arquivo'])) {
                        parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }

                    if (empty($_FILES['arquivo']['tmp_name'])) {
                        parent::message("Favor selecionar um arquivo para o novo Plano de Distribui&ccedil;&atilde;o.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }

                    $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                    if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                        parent::message("Favor selecionar o arquivo de Marca no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                    }

                    $dataString = file_get_contents($arquivoTemp);
                    $arrData = unpack("H*hex", $dataString);
                    $data = "0x".$arrData['hex'];

                    // ==================== PERSISTE DADOS DO ARQUIVO =================//
                    $dadosArquivo = array(
                            'nmArquivo'         => $arquivoNome,
                            'sgExtensao'        => $arquivoExtensao,
                            'biArquivo'         => $data,
                            'dsDocumento'       => 'Comprova&ccedil;&atilde;o do Relat&oacute;rio Trimestral/Final - Plano de Distribui&ccedil;&atilde;o',
                            'idPronac'          => $idpronac,
                            'idTipoDocumento'   => $_POST['tipoDocumento']);

                    $Arquivo = new Arquivo();
                    $Arquivo->inserirUploads($dadosArquivo);

                    $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                    $dadosDocumento = $DocumentoProjeto->buscar(array('idPronac =?'=>$idpronac,'idTipoDocumento =?'=>$_POST['tipoDocumento']), array('idDocumento DESC'));

                    $dados = array(
                        'IdPRONAC' => $idpronac,
                        'idAgente' => $_POST['idAgente'],
                        'idPlanoDistribuicao' => $_POST['produto'],
                        'idDocumento' => $dadosDocumento[0]->idDocumento,
                        'qtRecebida' => $_POST['quantidade'],
                        'idTipoBeneficiario' => $_POST['tipoDocumento'],
                    );
                    $tbBeneficiarioProdutoCultural->inserir($dados);
                } else {
                    parent::message("N&atilde;o foi poss&iacute;vel cadastrar os dados do Plano de Distribui&ccedil;&atilde;o!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }
            }

            parent::message("Dados salvos com sucesso!", "comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function excluirPlanoDistribuicaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idBeneficiario = (int) $post->beneficiario;
        $idArquivo = (int) $post->arquivo;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $return = $tbBeneficiarioProdutoCultural->delete('idBeneficiarioProdutoCultural='.$idBeneficiario);

        if ($return) {
            $vw = new vwAnexarComprovantes();
            $resutado = $vw->excluirArquivo($idArquivo);
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function buscarAgenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $cnpjcpf = Mascara::delMaskCPF($_POST['cnpjcpf']);

        $dados = array();
        $dados['a.CNPJCPF = ?'] = $cnpjcpf;

        $agentes = new Agente_Model_DbTable_Agentes();
        $result = $agentes->buscarAgenteENome($dados);

        $a = 0;
        if (count($result) > 0) {
            foreach ($result as $registro) {
                $dadosAgente[$a]['idAgente'] = $registro['idAgente'];
                $dadosAgente[$a]['CNPJCPF'] = $registro['CNPJCPF'];
                $dadosAgente[$a]['nmAgente'] = utf8_encode($registro['Descricao']);
                $a++;
            }
            $jsonEncode = json_encode($dadosAgente);
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosAgente));
        } else {
            $this->_helper->json(array('resposta'=>false,'CNPJCPF'=>$cnpjcpf));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function comprovantesDeExecucaoAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
    }

    public function cadastrarComprovanteExecucaoAction()
    {
        $linkFinal = '';
        if (isset($_POST['relatorofinal']) && $_POST['relatorofinal']) {
            $linkFinal = '-final';
        }

        //** Verifica se o usu�rio logado tem permiss�o de acesso **//
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
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
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/comprovantes-de-execucao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                if (empty($_FILES['arquivo']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo.", "comprovacao-objeto/comprovarexecucaofisica/comprovantes-de-execucao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Marca no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/comprovantes-de-execucao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'biArquivo'         => $data,
                        'dsDocumento'       => $_POST['observacoes'],
                        'idPronac'          => $idpronac,
                        'idTipoDocumento'   => $_POST['tipoDocumento']);

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);
            } else {
                parent::message("N&atilde;o foi poss&iacute;vel cadastrar o Comprovante de Execu&ccedil;&atilde;o!", "comprovacao-objeto/comprovarexecucaofisica/comprovantes-de-execucao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
            }

            parent::message("Dados salvos com sucesso!", "comprovacao-objeto/comprovarexecucaofisica/comprovantes-de-execucao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "comprovacao-objeto/comprovarexecucaofisica/comprovantes-de-execucao$linkFinal/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function excluirComprovanteExecucaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idArquivo = (int) $post->arquivo;

        $vw = new vwAnexarComprovantes();
        $resutado = $vw->excluirArquivo($idArquivo);
        if ($resutado) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage('Comprovante exclu&iacute;do com sucesso!');
            $this->_helper->flashMessengerType->addMessage('CONFIRM');
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function enviarRelatorioAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscar(array('idPronac = ?' => $idpronac, 'siComprovanteTrimestral=?'=>1))->current();

        $erros = array();
        $dsEtapasExecutadas = trim($DadosRelatorio['dsEtapasExecutadas']);
        $dsAcessibilidade = trim($DadosRelatorio['dsAcessibilidade']);
        $dsDemocratizacaoAcesso = trim($DadosRelatorio['dsDemocratizacaoAcesso']);

        if (empty($dsEtapasExecutadas)) {
            $erros[] = 'Etapas Executadas n&atilde;o foi informado.';
        }
        if (empty($dsAcessibilidade)) {
            $erros[] = 'Acessibilidade n&atilde;o foi informado.';
        }
        if (empty($dsDemocratizacaoAcesso)) {
            $erros[] = 'Democratiza&ccedil;&atilde;o de Acesso n&atilde;o foi informado.';
        }

        if (count($erros)>0) {
            //****** Dados do Projeto - Cabecalho *****//
            $projetos = new Projetos();
            $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
            $this->view->DadosProjeto = $DadosProjeto;
            $this->view->Erros = $erros;
        } else {
            $DadosRelatorio->siComprovanteTrimestral = 2;
            $DadosRelatorio->idCadastrador = $this->IdUsuario;
            $DadosRelatorio->save();
            parent::message("Relat&oacute;rio enviado com sucesso.", "comprovacao-objeto/comprovarexecucaofisica/relatoriotrimestral/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        }
    }


    public function cadastrarTrimestralAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
            $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
            $rsTotal = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac=?'=>$idpronac), true);
            $nrRelatorio = count($rsTotal)+1;

            $Projetos = new Projetos();
            $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
            $anoProjeto = $dadosProj->AnoProjeto;
            $sequencial = $dadosProj->Sequencial;

            $tblLib = new Liberacao();
            $rsLib = $tblLib->buscar(array('AnoProjeto =?'=>$anoProjeto,'Sequencial =?'=>$sequencial));
            $liberacao = $rsLib->count();
            if ($liberacao) {
                $intervalo = round(Data::CompararDatas($rsLib[0]['DtLiberacao'], $dadosProj->DtFimExecucao));
                $inicioPeriodo = $rsLib[0]['DtLiberacao'];
            } else {
                $intervalo = round(Data::CompararDatas($resp->DtInicioExecucao, $dadosProj->DtFimExecucao));
                $inicioPeriodo = $resp->DtInicioExecucao;
            }

            if ($nrRelatorio == 1) {
                $dtInicioPeriodo = $inicioPeriodo;
                $inicioPeriodo = Data::tratarDataZend($inicioPeriodo, 'Brasileira');
                list($dia, $mes, $ano) = explode('/', $inicioPeriodo);
                $dias = 90*$nrRelatorio;
                $inicioPeriodo = mktime(24*$dias, 0, 0, $mes, $dia, $ano);
                $dataFormatada = date('Y-m-d', $inicioPeriodo);
                $dtFimPeriodo = $dataFormatada;
            } else {
                $inicioPeriodo = $rsTotal[count($rsTotal)-1]['dtFimPeriodo'];
                $inicioPeriodo = Data::tratarDataZend($inicioPeriodo, 'Brasileira');
                list($dia, $mes, $ano) = explode('/', $inicioPeriodo);
                $inicio = mktime(24*1, 0, 0, $mes, $dia, $ano); //comecao no dia seguinte
                $fim = mktime(24*91, 0, 0, $mes, $dia, $ano); //termina 90 dias a contar do dia seguinte, por isso 91.
                $dtInicioPeriodo = date('Y-m-d', $inicio);
                $dtFimPeriodo = date('Y-m-d', $fim);
            }

            $rs = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac=?'=>$idpronac, 'siComprovanteTrimestral=?'=>1));

            $arrayDados = array();
            if (empty($rs)) {
                $arrayDados['IdPRONAC'] = $idpronac;
                $arrayDados['dtComprovante'] = new Zend_Db_Expr('GETDATE()');
                $arrayDados['dtInicioPeriodo'] = $dtInicioPeriodo;
                $arrayDados['dtFimPeriodo'] = $dtFimPeriodo;
                $arrayDados['dsEtapasExecutadas'] = $_POST['etapasExecutadas'];
                $arrayDados['dsAcessibilidade'] = $_POST['acessibilidade'];
                $arrayDados['dsDemocratizacaoAcesso'] = $_POST['democratizacaoAcesso'];
                $arrayDados['dsImpactoAmbiental'] = $_POST['impactoAmbiental'];
                $arrayDados['siComprovanteTrimestral'] = 1; //1 = Salvo pelo proponente
                $arrayDados['nrComprovanteTrimestral'] = $nrRelatorio;
                $arrayDados['idCadastrador'] = $this->IdUsuario;
                $tbComprovanteTrimestral->inserir($arrayDados);
            } else {
                $rs->dsEtapasExecutadas = $_POST['etapasExecutadas'];
                $rs->dsAcessibilidade = $_POST['acessibilidade'];
                $rs->dsDemocratizacaoAcesso = $_POST['democratizacaoAcesso'];
                $rs->dsImpactoAmbiental = $_POST['impactoAmbiental'];
                $rs->idCadastrador = $this->IdUsuario;
                $rs->save();
            }

            parent::message("Dados salvos com sucesso!", "comprovacao-objeto/comprovarexecucaofisica/etapas-de-trabalho/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "comprovacao-objeto/comprovarexecucaofisica/etapas-de-trabalho/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function visualizarRelatorioTrimestralAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac = ?' => $idpronac, 'idComprovanteTrimestral=?'=>$idrelatorio, 'siComprovanteTrimestral!=?'=>1));
        $this->view->DadosRelatorio = $DadosRelatorio;
        if (count($DadosRelatorio)==0) {
            parent::message("Relat&oacute;rio n&atilde;o encontrado!", "comprovacao-objeto/comprovarexecucaofisica/relatoriotrimestral/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
    }

    public function buscarDadosItensAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idPlanilhaAprovacao = $_POST['idPlanilhaAprovacao'];
        $idPronac = $_POST['idpronac'];

        $tbComprovante = new tbComprovantePagamentoxPlanilhaAprovacao();
        $result = $tbComprovante->buscarDadosItens($idPronac, $idPlanilhaAprovacao);

        $a = 0;
        if (count($result) > 0) {
            foreach ($result as $registro) {
                $tipoDocumento = null;
                switch ($registro['tpDocumento']) {
                    case 1:
                        $tipoDocumento = 'Boleto Bancário';
                        break;
                    case 2:
                        $tipoDocumento = 'Cupom Fiscal';
                        break;
                    case 3:
                        $tipoDocumento = 'Nota Fiscal/Fatura';
                        break;
                    case 4:
                        $tipoDocumento = 'Recibo de Pagamento';
                        break;
                    case 5:
                        $tipoDocumento = 'Autônomo';
                        break;
                }

                $formaPagamento = '-';
                switch ($registro['tpFormaDePagamento']) {
                    case 1:
                        $formaPagamento = 'Cheque';
                        break;
                    case 2:
                        $formaPagamento = 'Transferência Bancária';
                        break;
                    case 3:
                        $formaPagamento = 'Saque/Dinheiro';
                        break;
                }

                $dadosItem[$a]['DtPagamento'] = Data::tratarDataZend($registro['DtPagamento'], 'Brasileira');
                $dadosItem[$a]['vlComprovacao'] = !empty($registro['vlComprovacao']) ? 'R$ '.number_format($registro['vlComprovacao'], 2, ",", ".") : '';
                $dadosItem[$a]['tpDocumento'] = !empty($tipoDocumento) ? utf8_encode($tipoDocumento) : '';
                $dadosItem[$a]['nrComprovante'] = $registro['nrComprovante'];
                $dadosItem[$a]['dtEmissao'] = Data::tratarDataZend($registro['dtEmissao'], 'Brasileira');
                $dadosItem[$a]['idArquivo'] = $registro['idArquivo'];
                $dadosItem[$a]['Item'] = utf8_encode($registro['Item']);
                $dadosItem[$a]['Fornecedor'] = utf8_encode($registro['Fornecedor']);
                $dadosItem[$a]['tpFormaDePagamento'] = utf8_encode($formaPagamento);
                $dadosItem[$a]['nmArquivo'] = !empty($registro['nmArquivo']) ? utf8_encode($registro['nmArquivo']) : '';
                $a++;
            }
            $jsonEncode = json_encode($dadosItem);
            $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosItem));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function imprimirAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("pronac");
        $idrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscarComprovantes(array('idPronac = ?' => $idpronac, 'idComprovanteTrimestral=?'=>$idrelatorio, 'siComprovanteTrimestral!=?'=>1));
        $this->view->DadosRelatorio = $DadosRelatorio;
        if (count($DadosRelatorio)==0) {
            parent::message("Relat&oacute;rio n&atilde;o encontrado!", "comprovacao-objeto/comprovarexecucaofisica/relatoriotrimestral/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
        $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
    }

    public function etapasDeTrabalhoFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $tbCumprimentoObjeto->buscarCumprimentoObjeto(
                array(
                    'idPronac=?' => $idpronac,
                    'siCumprimentoObjeto=?' => ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SITUACAO_PROPONENTE
                )
            );
        $this->view->cumprimentoDoObjeto = $tbCumprimentoObjeto;
    }

    public function localDeRealizacaoFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $pais = new Pais();
        $paises = $pais->buscar(array(), 'Descricao');
        $this->view->Paises = $paises;

        $uf = new Uf();
        $ufs = $uf->buscar(array(), 'Descricao');
        $this->view->UFs = $ufs;
    }

    public function manterLocalDeRealizacaoFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        $idLocal = $this->_request->getParam("idlocal");

        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
            $idLocal = Seguranca::dencrypt($idLocal);
        }

        $this->view->relatoriofinal = $this->_request->getParam("relatoriofinal");

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idpronac, $idLocal);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $pais = new Pais();
        $paises = $pais->buscar(array(), 'Descricao');
        $this->view->Paises = $paises;

        $uf = new Uf();
        $ufs = $uf->buscar(array(), 'Descricao');
        $this->view->UFs = $ufs;
    }


    public function planoDeDivulgacaoFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idpronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $Verificacao = new Verificacao();
        $Peca = $Verificacao->buscar(array('idTipo =?'=>1,'stEstado =?'=>1));
        $this->view->Peca = $Peca;

        $Veiculo = $Verificacao->buscar(array('idTipo =?'=>2,'stEstado =?'=>1));
        $this->view->Veiculo = $Veiculo;
    }

    public function planoDeDistribuicaoFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idpronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idpronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;
    }

    public function metasComprovadasFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        //****** Dados da Comprova��o de Metas *****//
        $DadosCompMetas = $projetos->buscarMetasComprovadas($idpronac);
        $this->view->DadosCompMetas = $DadosCompMetas;
    }

    public function itensComprovadosFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idpronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;
    }

    public function comprovantesDeExecucaoFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idpronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
    }

    public function aceiteDeObraFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbTermoAceiteObra = new ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
        $DadosRelatorio = $tbTermoAceiteObra->buscarTermoAceiteObraArquivos(array('idPronac=?'=>$idpronac));
        $this->view->DadosRelatorio = $DadosRelatorio;
    }

    public function cadastrarAceiteObraAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
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
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/aceite-de-obra-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                if (empty($_FILES['arquivo']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo para o novo Plano de Distribui&ccedil;&atilde;o.", "comprovacao-objeto/comprovarexecucaofisica/aceite-de-obra-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Marca no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/aceite-de-obra-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'biArquivo'         => $data,
                        'dsDocumento'       => $_POST['descricaoTermoAceite'],
                        'idPronac'          => $idpronac,
                        'idTipoDocumento'   => 25);

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);

                $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                $dadosDocumento = $DocumentoProjeto->buscar(array('idPronac =?'=>$idpronac,'idTipoDocumento =?'=>25), array('idDocumento DESC'));
            }

            $tbTermoAceiteObra = new ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
            $rs = $tbTermoAceiteObra->buscarTermoAceiteObra(array('idPronac=?'=>$idpronac));

            $arrayDados = array();
            if (empty($rs)) {
                $arrayDados['idPronac'] = $idpronac;
                $arrayDados['dtCadastroTermo'] = new Zend_Db_Expr('GETDATE()');
                $arrayDados['dsDescricaoTermoAceite'] = $_POST['descricaoTermoAceite'];
                $arrayDados['stConstrucaoCriacaoRestauro'] = $_POST['construcaoCriacaoRestauro'];
                $arrayDados['idDocumentoTermo'] = $dadosDocumento[0]->idDocumento;
                $arrayDados['idUsuarioCadastrador'] = $this->IdUsuario;
                $tbTermoAceiteObra->inserir($arrayDados);
            } else {
                if (isset($_POST['idArquivo']) && !empty($_POST['idArquivo'])) {
                    $vw = new vwAnexarComprovantes();
                    $resutado = $vw->excluirArquivo($_POST['idArquivo']);
                }
                $rs->dsDescricaoTermoAceite = $_POST['descricaoTermoAceite'];
                $rs->stConstrucaoCriacaoRestauro = $_POST['construcaoCriacaoRestauro'];
                $rs->idDocumentoTermo = $dadosDocumento[0]->idDocumento;
                $rs->idUsuarioCadastrador = $this->IdUsuario;
                $rs->save();
            }

            parent::message("Dados salvos com sucesso!", "comprovacao-objeto/comprovarexecucaofisica/aceite-de-obra-final/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "comprovacao-objeto/comprovarexecucaofisica/aceite-de-obra-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function bensFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $DadosItens = $tbPlanilhaAprovacao->buscarItensOrcamentarios(array('a.idPronac=?'=>$idpronac), array('b.Descricao'));
        $this->view->DadosItens = $DadosItens;

        $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
        $BensCadastrados = $tbBensDoados->buscarBensCadastrados(array('a.idPronac=?'=>$idpronac), array('b.Descricao'));
        $this->view->BensCadastrados = $BensCadastrados;
    }

    public function cadastrarBensMoveisAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
            if (!empty($_FILES['documentoDoacao']['tmp_name'])) {
                $arquivoNome     = $_FILES['documentoDoacao']['name']; // nome
                $arquivoTemp     = $_FILES['documentoDoacao']['tmp_name']; // nome tempor�rio
                $arquivoTipo     = $_FILES['documentoDoacao']['type']; // tipo
                $arquivoTamanho  = $_FILES['documentoDoacao']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                }

                if (!isset($_FILES['documentoDoacao'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                if (empty($_FILES['documentoDoacao']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo para a Doa&ccedil;&atilde;o do Bem M&oacute;vel.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Doa&ccedil;&atilde;o do Bem M&oacute;vel no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'biArquivo'         => $data,
                        'dsDocumento'       => 'Comprovação do Relatório Final - Bem Móvel',
                        'idPronac'          => $idpronac,
                        'idTipoDocumento'   => 26);

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);

                $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                $DocumentoDoacao = $DocumentoProjeto->buscar(array('idPronac=?'=>$idpronac,'idTipoDocumento=?'=>26), array('idDocumento DESC'));
            }

            if (!empty($_FILES['documentoAceite']['tmp_name'])) {
                $arquivoNome     = $_FILES['documentoAceite']['name']; // nome
                $arquivoTemp     = $_FILES['documentoAceite']['tmp_name']; // nome tempor�rio
                $arquivoTipo     = $_FILES['documentoAceite']['type']; // tipo
                $arquivoTamanho  = $_FILES['documentoAceite']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                }

                if (!isset($_FILES['documentoAceite'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                if (empty($_FILES['documentoAceite']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo para a Aceite do Bem M&oacute;vel.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Aceite do Bem M&oacute;vel no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'biArquivo'         => $data,
                        'dsDocumento'       => 'Comprova&ccedil;&atilde;o do Relat&oacute;rio Final - Bem M&oacute;vel',
                        'idPronac'          => $idpronac,
                        'idTipoDocumento'   => 25);

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);

                $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                $documentoAceite = $DocumentoProjeto->buscar(array('idPronac=?'=>$idpronac,'idTipoDocumento=?'=>25), array('idDocumento DESC'));
            }

            $dadosItem = array(
                'idPronac' => $idpronac,
                'dtCadastroDoacao' => new Zend_Db_Expr('GETDATE()'),
                'idItemOrcamentario' => $_POST['itemOrcamentario'],
                'tpBem' => 'M',
                'idAgente' => $_POST['agenteMovel'],
                'qtBensDoados' => $_POST['qtBensDoados'],
                'dsObservacao' => $_POST['observacao'],
                'idDocumentoDoacao' => $DocumentoDoacao[0]->idDocumento,
                'idDocumentoAceite' => $documentoAceite[0]->idDocumento,
                'idUsuarioCadastrador' => $this->IdUsuario
            );

            $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
            $insert = $tbBensDoados->inserir($dadosItem);

            parent::message("Dados salvos com sucesso!", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function cadastrarBensImoveisAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
            if (!empty($_FILES['documentoDoacao']['tmp_name'])) {
                $arquivoNome     = $_FILES['documentoDoacao']['name']; // nome
                $arquivoTemp     = $_FILES['documentoDoacao']['tmp_name']; // nome tempor�rio
                $arquivoTipo     = $_FILES['documentoDoacao']['type']; // tipo
                $arquivoTamanho  = $_FILES['documentoDoacao']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                }

                if (!isset($_FILES['documentoDoacao'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                if (empty($_FILES['documentoDoacao']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo para a Doa&ccedil;&atilde;o do Bem Im&oacute;vel.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Doa&ccedil;&atilde;o do Bem Im&oacute;vel no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'biArquivo'         => $data,
                        'dsDocumento'       => 'Comprovação do Relatório Final - Bem Imóvel',
                        'idPronac'          => $idpronac,
                        'idTipoDocumento'   => 26);

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);

                $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                $DocumentoDoacao = $DocumentoProjeto->buscar(array('idPronac=?'=>$idpronac,'idTipoDocumento=?'=>26), array('idDocumento DESC'));
            }

            if (!empty($_FILES['documentoAceite']['tmp_name'])) {
                $arquivoNome     = $_FILES['documentoAceite']['name']; // nome
                $arquivoTemp     = $_FILES['documentoAceite']['tmp_name']; // nome tempor�rio
                $arquivoTipo     = $_FILES['documentoAceite']['type']; // tipo
                $arquivoTamanho  = $_FILES['documentoAceite']['size']; // tamanho

                if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                    $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens�o
                    $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                    $arquivoHash     = Upload::setHash($arquivoTemp); // hash
                }

                if (!isset($_FILES['documentoAceite'])) {
                    parent::message("O arquivo n&atilde;o atende os requisitos informados no formul&aacute;rio.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                if (empty($_FILES['documentoAceite']['tmp_name'])) {
                    parent::message("Favor selecionar um arquivo para a Aceite do Bem Im&oacute;vel.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $tipos = array('bmp','gif','jpeg','jpg','png','raw','tif','pdf');
                if (!in_array(strtolower($arquivoExtensao), $tipos)) {
                    parent::message("Favor selecionar o arquivo de Aceite do Bem Im&oacute;vel no formato BMP, GIF, JPEG, JPG, PNG, RAW, TIF ou PDF!", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
                }

                $dataString = file_get_contents($arquivoTemp);
                $arrData = unpack("H*hex", $dataString);
                $data = "0x".$arrData['hex'];

                // ==================== PERSISTE DADOS DO ARQUIVO =================//
                $dadosArquivo = array(
                        'nmArquivo'         => $arquivoNome,
                        'sgExtensao'        => $arquivoExtensao,
                        'biArquivo'         => $data,
                        'dsDocumento'       => 'Comprovação do Relatório Final - Bem Imóvel',
                        'idPronac'          => $idpronac,
                        'idTipoDocumento'   => 25);

                $Arquivo = new Arquivo();
                $Arquivo->inserirUploads($dadosArquivo);

                $DocumentoProjeto = new tbDocumentoProjetoBDCORPORATIVO();
                $documentoAceite = $DocumentoProjeto->buscar(array('idPronac=?'=>$idpronac,'idTipoDocumento=?'=>25), array('idDocumento DESC'));
            }

            $dadosItem = array(
                'idPronac' => $idpronac,
                'dtCadastroDoacao' => new Zend_Db_Expr('GETDATE()'),
                'idItemOrcamentario' => $_POST['itemOrcamentario'],
                'tpBem' => 'I',
                'idAgente' => $_POST['agenteImovel'],
                'qtBensDoados' => $_POST['qtBensDoados'],
                'dsObservacao' => $_POST['observacao'],
                'idDocumentoDoacao' => $DocumentoDoacao[0]->idDocumento,
                'idDocumentoAceite' => $documentoAceite[0]->idDocumento,
                'idUsuarioCadastrador' => $this->IdUsuario
            );

            $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
            $insert = $tbBensDoados->inserir($dadosItem);

            parent::message("Dados salvos com sucesso!", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "CONFIRM");
        } catch (Exception $e) {
            parent::message("Erro ao salvar os dados.", "comprovacao-objeto/comprovarexecucaofisica/bens-final/idpronac/".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function excluirBemDoadoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idBensDoados = (int) $post->bensDoados;
        $doacao = (int) $post->doacao;
        $aceite = (int) $post->aceite;

        $where = 'idBensDoados = '.$idBensDoados;
        $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
        $exclusaoDoBem = $tbBensDoados->delete($where);

        $vw = new vwAnexarComprovantes();
        $exclusao1 = $vw->excluirArquivo($doacao);
        $exclusao2 = $vw->excluirArquivo($aceite);

        if ($exclusaoDoBem) {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage('O bem foi exclu&iacute;do com sucesso!');
            $this->_helper->flashMessengerType->addMessage('CONFIRM');
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function cadastrarFinalAction()
    {
        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }
        $url = 'comprovacao-objeto/comprovarexecucaofisica/etapas-de-trabalho-final/idpronac/' . Seguranca::encrypt($idpronac);
        try {
            $this->verificarPermissaoAcesso(false, true, false);

            $Projetos = new Projetos();
            $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();

            if (!$dadosProj) {
                parent::message('Projeto n�o foi encontrado!', $url, 'ERROR');
            }

            $post = filter_input_array(INPUT_POST);
            $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto(
                    $idpronac,
                    $this->IdUsuario,
                    ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SITUACAO_PROPONENTE,
                    $post['etapasConcluidas'],
                    $post['medidasAcessibilidade'],
                    $post['medidasFruicao'],
                    $post['medidasPreventivas'],
                    $post['totalEmpregosDiretos'],
                    $post['totalEmpregosIndiretos'],
                    $post['empregosGerados']
                    );
            $tbCumprimentoObjeto->saveOrUpdate();

            parent::message('Dados salvos com sucesso!', $url, 'CONFIRM');
        } catch (InvalidArgumentException $exeption) {
            parent::message('Erro ao salvar os dados.!', $url, 'ERROR');
        }
    }

    public function enviarRelatorioFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        $paChecklist = new paChecklistDeEnvioDeCumprimentoDeObjeto();
        $statusRelatorio = $paChecklist->verificarRelatorio($idpronac);
        $this->view->Resultado = $statusRelatorio;

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
    }

    public function finalizarCadastroRelatorioAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idpronac = $this->_request->getParam("idpronac");
        $confirmacao = $this->_request->getParam("envio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        try {
            if ($confirmacao) {
                $auth = Zend_Auth::getInstance(); // pega a autenticaca
                $idUsuario = $auth->getIdentity()->IdUsuario; // usuario logado

                //ATUALIZA A SITUA��O DO PROJETO
                $Projetos = new Projetos();
                $d = array();
                $d['situacao'] = 'E24';
                $d['ProvidenciaTomada'] = 'Prestação de Contas final apresentada, aguardando análise.';
                $d['dtSituacao'] = new Zend_Db_Expr('GETDATE()');
                $d['Logon'] = $idUsuario;
                $w = "IdPRONAC = $idpronac";
                $Projetos->update($d, $w);

                $dados = array();
                $dados['siCumprimentoObjeto'] = 2;

                /*
                  1 - Salvo pelo proponente;
                  2 - Enviado pelo proponente;
                  3 - Encaminhado para Tecnico de Acompanhamento;
                  4 - Em analise pelo Tecnico;
                  5 - Finalizado pelo Tecnico;
                  6 - Finalizado pelo Coordenador.
                */

                $dados['DtEnvioDaPrestacaoContas'] = new Zend_Db_Expr('GETDATE()');
                $where = "idPronac = $idpronac ";

                $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
                $return = $tbCumprimentoObjeto->update($dados, $where);

                if ($return) {
                    parent::message('Comprova&ccedil;&otilde;es enviadas com sucesso!', "default/consultardadosprojeto/index?idPronac=".Seguranca::encrypt($idpronac), "CONFIRM");
                } else {
                    throw new Exception("Erro ao enviar a comprova&ccedil;&atilde;o!");
                }
            } // fecha try
        } catch (Exception $e) {
            parent::message($e->getMessage(), "default/consultardadosprojeto/index?idPronac=".Seguranca::encrypt($idpronac), "ERROR");
        }
    }

    public function deletarImagemCumprimentoDoObjetoAction()
    {
        $url = '/comprovacao-objeto/comprovarexecucaofisica/etapas-de-trabalho-final/idpronac/' .
            $this->getRequest()->getParam('idpronac');
        try {
            Seguranca::encrypt($this->getRequest()->getParam('idpronac'));
            $cumprimentoObjetoArquivoModel = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjetoXArquivo(
                    null,
                    $this->getRequest()->getParam('idCumprimentoDoObjeto'),
                    $this->getRequest()->getParam('idArquivo')
                    );
            $cumprimentoObjetoArquivoModel->apagarArquivo();
            parent::message('Imagem deletada com sucesso', $url, 'CONFIRM');
        } catch (Exception $exception) {
            parent::message('N&atilde;o foi poss&iacute;vel deletar a imagem', $url, 'ERROR');
        }
    }
}
