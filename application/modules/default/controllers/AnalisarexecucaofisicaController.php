<?php 
class AnalisarexecucaofisicaController extends MinC_Controller_Action_Abstract
{
    private $getIdAgente  = 0;
    private $getIdGrupo   = 0;
    private $getIdOrgao   = 0;
    private $getIdUsuario = 0;
    private $intTamPag = 10;
    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);

        $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usu�rio
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $this->getIdAgente = $idagente['idAgente'];
        $this->getIdGrupo  = $GrupoAtivo->codGrupo;
        $this->getIdOrgao  = $GrupoAtivo->codOrgao;

        parent::init();
    }

    /**
     * Metodo Analisar Relat�rios Trimestrais
     * @access public
     * @param void
     * @return void
     */
    public function filtroconsultaAction()
    {
        if (isset($_POST['periodo'])) {
            $this->_helper->layout->disableLayout();
            $anoatual = date('Y');
            $mesatual = date('m');

            $dataperiodo = $_POST['datacalculada'];
            $dataperiodo = explode('-', $dataperiodo);
            $anoperiodo = $dataperiodo[0];
            $mesperiodo = $dataperiodo[1];

            $periodo = $_POST['periodo'];
            $qtdperiodo = $periodo / 90;
            $periodo = array();
            $a = 0;
            for ($anoperiodo; $anoperiodo < $anoatual; $anoperiodo++) {
                if ($mesperiodo <= 3 and $anoperiodo < $anoatual) {
                    $periodo[$a]['valor'] = '01-01-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;
                    $periodo[$a]['valor'] = '01-04-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 2 - " . $anoperiodo;
                    $a++;
                    $periodo[$a]['valor'] = '01-07-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 3 - " . $anoperiodo;
                    $a++;
                    $periodo[$a]['valor'] = '01-10-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 4 - " . $anoperiodo;
                    $a++;
                } elseif ($mesperiodo >= 4 and $mesperiodo <= 6 and $anoperiodo) {
                    $periodo[$a]['valor'] = '01-04-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;
                    $periodo[$a]['valor'] = '01-07-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 2 - " . $anoperiodo;
                    $a++;
                    $periodo[$a]['valor'] = '01-10-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 3 - " . $anoperiodo;
                    $a++;
                } elseif ($mesperiodo >= 7 and $mesperiodo <= 9 and $anoperiodo) {
                    $periodo[$a]['valor'] = '01-07-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;
                    $periodo[$a]['valor'] = '01-10-' . $anoperiodo. '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 2 - " . $anoperiodo;
                    $a++;
                } elseif ($mesperiodo >= 10 and $anoperiodo < $anoatual) {
                    $periodo[$a]['valor'] = '01-10-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;
                }
                if ($mesatual <= 3 and $anoperiodo == $anoatual) {
                    $periodo[$a]['valor'] = '01-01-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;
                } elseif ($mesatual >= 4 and $mesatual <= 6 and $anoperiodo == $anoatual) {
                    $periodo[$a]['valor'] = '01-01-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;

                    $periodo[$a]['valor'] = '01-03-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 2 - " . $anoperiodo;
                    $a++;
                } elseif ($mesatual >= 7 and $mesatual <= 9 and $anoperiodo == $anoatual) {
                    $periodo[$a]['valor'] = '01-01-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;

                    $periodo[$a]['valor'] = '01-03-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 2 - " . $anoperiodo;
                    $a++;

                    $periodo[$a]['valor'] = '01-07-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 3 - " . $anoperiodo;
                    $a++;
                } elseif ($mesatual >= 10 and $anoperiodo == $anoatual) {
                    $periodo[$a]['valor'] = '01-01-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 1 - " . $anoperiodo;
                    $a++;

                    $periodo[$a]['valor'] = '01-03-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 2 - " . $anoperiodo;
                    $a++;

                    $periodo[$a]['valor'] = '01-07-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 3 - " . $anoperiodo;
                    $a++;

                    $periodo[$a]['valor'] = '01-10-' . $anoperiodo . '/' . date('d-m-Y');
                    $periodo[$a]['descricao'] = "Relatorio 4 - " . $anoperiodo;
                    $a++;
                }
                $anogravado = $anoperiodo;
            }
            $this->_helper->json($periodo);
            $this->_helper->viewRenderer->setNoRender(true);
        }


        $uf = new Uf();
        $buscaruf = $uf->buscar(array(), array('Descricao asc'));
        $this->view->uf = $buscaruf;

        $situacao = new Situacao();
        $buscarsituacao = $situacao->listasituacao(array('E12', 'E13', 'E15', 'E50', 'E59', 'E60', 'E61', 'E66', 'E67', 'E68'));
        $this->view->situacao = $buscarsituacao;

        $rsEstados = Estado::buscar();
        $mecanismo = new Mecanismo();
        $mecanismo2 = $mecanismo->buscar(array('Status = ?' => 1));
        $this->view->estados = $rsEstados;
        $this->view->mecanismo = $mecanismo2;
    }

    public function projetosAction()
    {
        //** Usuario Logado ************************************************/
        $auth               = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario          = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo         = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao           = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $codPerfil          = $GrupoAtivo->codGrupo; //  �rg�o ativo na sess�o
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /******************************************************************/

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
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array('NomeProjeto','nrComprovanteTrimestral');
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['a.Orgao = ?'] = $codOrgao;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['Pronac = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        $vw = new vwPainelCoordenadorAvaliacaoTrimestral();
        $total = $vw->listaRelatorios($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $vw->listaRelatorios($where, $order, $tamanho, $inicio);

        $paginacao = array(
                "pag"=>$pag,
                "qtde"=>$this->intTamPag,
                "campo"=>$campo,
                "ordem"=>$ordem,
                "ordenacao"=>$ordenacao,
                "novaOrdem"=>$novaOrdem,
                "total"=>$total,
                "inicio"=>($inicio+1),
                "fim"=>$fim,
                "totalPag"=>$totalPag,
                "Itenspag"=>$this->intTamPag,
                "tamanho"=>$tamanho
         );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRelatorios = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;

//        $pa = new paUsuariosDoPerfil();
        $vw = new vwUsuariosOrgaosGrupos();
        $usuarios = $vw->buscarUsuarios($codPerfil, $codOrgao);
        $this->view->Usuarios = $usuarios;
    }

    public function encaminharRelatorioAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idPronac = (int) $post->pronac;
        $nrRelatorio = (int) $post->nr;

        $dados = array();
        $dados['idTecnicoAvaliador'] = (int) $post->tecnico;
        $dados['siComprovanteTrimestral'] = 3;
        $where = "IdPRONAC = $idPronac AND nrComprovanteTrimestral = $nrRelatorio";

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $return = $tbComprovanteTrimestral->update($dados, $where);

        if ($return) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function visualizarRelatorioAction()
    {
        $idpronac = $this->_request->getParam("idPronac");
        $nrrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscarComprovantes(array('IdPRONAC = ?' => $idpronac, 'nrComprovanteTrimestral=?'=>$nrrelatorio, 'siComprovanteTrimestral in (?)'=>array(2,5)));
        $this->view->DadosRelatorio = $DadosRelatorio;
        if (count($DadosRelatorio)==0) {
            parent::message("Relat�rio n�o encontrado!", "analisarexecucaofisica/projetos", "ERROR");
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

    public function finalizarRelatorioAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $post = Zend_Registry::get('post');
        $idPronac = (int) $post->pronac;
        $nrRelatorio = (int) $post->nr;

        $dados = array();
        $dados['siComprovanteTrimestral'] = 6;
        $where = "IdPRONAC = $idPronac AND nrComprovanteTrimestral = $nrRelatorio";

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $return = $tbComprovanteTrimestral->update($dados, $where);

        if ($return) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function imprimirAction()
    {
        $idpronac = $this->_request->getParam("pronac"); //idPronac
        $nrrelatorio = $this->_request->getParam("relatorio");
        if (strlen($idpronac) > 7) {
            $idpronac = Seguranca::dencrypt($idpronac);
        }

        //****** Dados do Projeto - Cabecalho *****//
        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idpronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbComprovanteTrimestral = new ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral();
        $DadosRelatorio = $tbComprovanteTrimestral->buscarComprovantes(array('IdPRONAC = ?' => $idpronac, 'nrComprovanteTrimestral=?'=>$nrrelatorio, 'siComprovanteTrimestral in (?)'=>array(2,5)));
        $this->view->DadosRelatorio = $DadosRelatorio;
        if (count($DadosRelatorio)==0) {
            parent::message("Relat&oacute;rio n&atilde;o encontrado!", "analisarexecucaofisica/projetos", "ERROR");
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




    public function relatoriotrimestralAction()
    {
        $r = new tbRelatorio();
        $pr = new Projetos();
        $rt = new tbRelatorioTrimestral();
        $a = new Acesso();
        $dp = new tbDistribuicaoProduto();
        $b = new tbBeneficiario();
        $pd = new PlanoDeDivulgacao();
        $doc = new tbDocumento();
        $cb = new tbComprovanteBeneficiario();
        $ce = new tbComprovanteExecucao();
        $lm = new tbLogomarca();

        $idRelatorioTrimestral = $this->_request->getParam('idRelatorio');
        $idPronac = $this->_request->getParam('idPronac');
        $idRelatorio = $rt->buscar(array('idRelatorioTrimestral = ?' => $idRelatorioTrimestral));

        if (count($idRelatorio) > 0) {
            if ($idRelatorio[0]->stRelatorioTrimestral == 1) {
                parent::message('Relat&oacute;rio n&atilde;o finalizado pelo Proponente!', "analisarexecucaofisicatecnico", "ALERT");
            }
        }

        $buscarprojeto = $pr->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $this->view->dadosprojeto = $buscarprojeto;

        $buscarrelatorio = $r->buscar(array('IdPRONAC = ?' => $idPronac, 'idRelatorio = ?'=> $idRelatorio[0]->idRelatorio));
        $buscarcomprovanteexecucal = $ce->buscar(array('idRelatorio = ?' => $idRelatorio[0]->idRelatorio));

        $docexec = array();
        $count = 0;
        foreach ($buscarcomprovanteexecucal as $docexecucao) {
            $documento = $doc->buscardocumentosrelatorio($docexecucao->idDocumento)->current();
            $docexec[$count]['idArquivo'] = $documento['idArquivo'];
            $docexec[$count]['dtEnvio'] = $documento['dtEnvio'];
            $docexec[$count]['dsDocumento'] = $documento['dsDocumento'];
            $docexec[$count]['nmArquivo'] = $documento['nmArquivo'];
            $docexec[$count]['nmTitulo'] = $documento['nmTitulo'];
            $docexec[$count]['nrTamanho'] = $documento['nrTamanho'];
            $docexec[$count]['dsTipoDocumento'] = $documento['dsTipoDocumento'];
            $count++;
        }
        $this->view->documentoexecucao = $docexec;

        $buscarrelatoriotrimestral = $rt->buscarUsandoCAST($idRelatorioTrimestral)->current();
        $this->view->dadosrelatoriotrimestral = $buscarrelatoriotrimestral;

        $buscaracessibilidade = $a->buscarUsandoCAST($idRelatorio[0]->idRelatorio, 1)->current();
        $this->view->dadosacessibilidade = $buscaracessibilidade;

        $buscardemocratizacao = $a->buscarUsandoCAST($idRelatorio[0]->idRelatorio, 2)->current();
        $this->view->dadosdemocratizacao = $buscardemocratizacao;

        $buscarprodutos = $pr->buscarTodosDadosProjetoProdutos($idPronac);
        $this->view->distribuicaoproduto = $buscarprodutos;

        $planodivulgacao = array();
        if (count($buscarprodutos) > 0) {
            $buscarprodutos = $buscarprodutos->current();
            $buscarplanodivulgacao = $pd->buscarPlanoDivulgacao($buscarprodutos->idProjeto);

            $this->view->PosicaoLogo = $buscarprodutos->PosicaoLogo;
            $this->view->dsJustificativaPosicaoLogo = $buscarprodutos->dsJustificativaPosicaoLogo;

            $count = 0;
            foreach ($buscarplanodivulgacao as $plano) {
                $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['idPlanoDivulgacao'] = $plano->idPlanoDivulgacao;
                $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['Veiculo'] = $plano->Veiculo;
                $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['dsTamanhoDuracao'] = $plano->dsTamanhoDuracao;
                $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['dsPosicao'] = $plano->dsPosicao;
                $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['Peca'] = $plano->Peca;
                $buscarmarca = $lm->buscar(array('idPlanoDivulgacao = ?' => $plano->idPlanoDivulgacao, 'idDocumento is not null'=>''));
                $c = 0;
                $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'] = '';
                foreach ($buscarmarca as $docprod) {
                    $documento = $doc->buscardocumentosrelatorio($docprod->idDocumento)->current();
                    $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'][$c]['idArquivo'] = $documento->idArquivo;
                    $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'][$c]['dtEnvio'] = $documento->dtEnvio;
                    $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'][$c]['dsDocumento'] = $documento->dsDocumento;
                    $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'][$c]['nmArquivo'] = $documento->nmArquivo;
                    $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'][$c]['nrTamanho'] = $documento->nrTamanho;
                    $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'][$c]['dsPosicao'] = $docprod->dsPosicao;
                    $planodivulgacao[$buscarprodutos->idPlanoDistribuicao][$count]['arquivos'][$c]['dsTipoDocumento'] = $documento->dsTipoDocumento;
                    $c++;
                }
                $count++;
            }
        }
        $this->view->planodistribuicao = $planodivulgacao;
        
        $buscarBeneficiario = $b->buscarUsandoCAST($idRelatorio[0]->idRelatorio)->current();
        $this->view->dadosbeneficiario = $buscarBeneficiario;

        $buscarcomprovantes = $cb->buscar(array('idRelatorio = ?' => $idRelatorio[0]->idRelatorio));

        $docbeneficiario = array();
        $count = 0;
        foreach ($buscarcomprovantes as $docben) {
            $documento = $doc->buscardocumentosrelatorio($docben->idDocumento)->current();
            $docbeneficiario[$count]['idArquivo'] = $documento->idArquivo;
            $docbeneficiario[$count]['dtEnvio'] = $documento->dtEnvio;
            $docbeneficiario[$count]['dsDocumento'] = $documento->dsDocumento;
            $docbeneficiario[$count]['nmArquivo'] = $documento->nmArquivo;
            $docbeneficiario[$count]['nmTitulo'] = $documento->nmTitulo;
            $docbeneficiario[$count]['nrTamanho'] = $documento->nrTamanho;
            $docbeneficiario[$count]['dsTipoDocumento'] = $documento->dsTipoDocumento;
            $count++;
        }
        $this->view->documentosbeneficiarios = $docbeneficiario;
        $this->view->idrelatorio = $idRelatorio[0]->idRelatorio;
        $this->view->idrelatoriotrimestral = $this->_request->getParam('idRelatorio');
        $this->view->idAgenteLogado = $this->getIdAgente;
        $this->view->idAgenteAvaliador = $buscarrelatorio[0]->idAgenteAvaliador;
    }

    public function encaminharprojetoanaliseAction()
    {
        $justificativaenvio = $_POST['justificativaenvio'];
        $destinatario = $_POST['destinatario'];
        $idRelatorio = $_POST['idRelatorio'];
        $idRelatorioTrimestral = $_POST['idRelatorioTrimestral'];

        $rt = new tbRelatorioTrimestral();

        $dados = array('stRelatorioTrimestral' => 3);
        $where = array('idRelatorioTrimestral = ?' => $idRelatorioTrimestral);
        $rt->alterar($dados, $where);

        $idRelatorio = $rt->buscar(array('idRelatorioTrimestral = ?' => $idRelatorioTrimestral))->current();

        $r = new tbRelatorio();
        $dadosRel = array('idAgenteAvaliador' => $destinatario);
        $whereRel = array('idRelatorio = ?' => $idRelatorio->idRelatorio);
        $r->alterar($dadosRel, $whereRel);

        parent::message("Projeto encaminhado com sucesso!", "analisarexecucaofisica/projetos", "CONFIRM");
    }

    public function diligenciaAction()
    {
        $this->view->idpronac = $this->_request->getParam('idpronac');
        $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usu�rio
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $d = new tbDiligencia();
        $p = new Projetos();

        if (isset($_POST['idpronac'])) {
            $db = Zend_Db_Table::getDefaultAdapter();

            try {
                $db->beginTransaction();
                $dados = array(
                    'idPronac' => $_POST['idpronac'],
                    'idTipoDiligencia' => 174,
                    'DtSolicitacao' => date('Y-m-d H:i:s'),
                    'Solicitacao' => $_POST['justificativa'],
                    'idSolicitante' => $auth->getIdentity()->usu_codigo,
                    'stEnviado' => 'S'
                );
                $d->inserir($dados);
                $where = "IdPRONAC = " . $_POST['idpronac'];
                $dadosalterar = array('Situacao' => 'E61');
                $p->alterar($dadosalterar, $where);
                $db->commit();
                echo "<script>history.go(-1);</script>";
            } catch (Zend_Exception $e) {
                $db->rollBack();
                echo $e->getMessage();
            }
        }
    }

    public function detalharrelatoriosAction()
    {
        $idPronac = $this->_request->getParam('idPronac');
        $r = new tbRelatorio();
        $buscarRelatorios = $r->buscarRelatorioTrimestrais($idPronac);

        if (!$this->verificarOrgao($idPronac)) {
            parent::message('Voc&ecirc; n&atilde;o tem permiss&atilde;o para visualizar esse Relat&oacute;rio!', "analisarexecucaofisica/filtroconsulta", "ALERT");
        }

        $p = new Projetos();
        $DadosProjeto = $p->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;
        
        // busca os t�cnicos do �rg�o logado
        $Tecnicos = new Usuariosorgaosgrupos();
        $buscarTecnicos = $Tecnicos->buscardadosAgentesArray(array('uog.uog_orgao = ?' => $this->getIdOrgao, 'uog.gru_codigo IN (?)' => array('121', '129')));

        $this->view->tecnicos = $buscarTecnicos;
        $this->view->dadosRelatorios = $buscarRelatorios;
    }

    public function salvaranaliseAction()
    {
        if ($_POST) {
            $where = array('idRelatorio = ?' => $_POST['idRelatorio'], 'idRelatorioTrimestral = ?' => $_POST['idRelatorioTrimestral']);
            $dados = array('stRelatorioTrimestral' => $_POST['stRelatorio'], 'dsParecer' => $_POST['justificativa']);

            $idPronac = $this->_request->getParam('idPronac');

            $RT = new tbRelatorioTrimestral();
            $RT->alterar($dados, $where);

            $where = array('idRelatorio = ?' => $_POST['idRelatorio']);
            $dados = array('idAgenteAvaliador' => $this->getIdAgente);

            $R = new tbRelatorio();
            $R->alterar($dados, $where);

            $msg = ($_POST['stRelatorio'] == 7) ? 'finalizado' : 'salvo';
            $url[0] = ($_POST['stRelatorio'] == 7) ? 'detalharrelatorios' : 'relatoriotrimestral';
            $url[1] = ($_POST['stRelatorio'] == 7) ? '' : "/idRelatorio/" . $_POST['idRelatorioTrimestral'];

            parent::message("Relat�rio $msg com sucesso!", "analisarexecucaofisica/$url[0]/idPronac/" . $idPronac . $url[1], 'CONFIRM');
        }
    }

    public function verificarOrgao($idPronac)
    {
        $Projeto = new Projetos();
        $buscar = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac, 'Orgao = ?' => $this->getIdOrgao))->current();

        if (count($buscar) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
