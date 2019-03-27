<?php

class ComprovacaoObjeto_AvaliaracompanhamentoprojetoController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 10;
    private $getIdOrgao = 0;
    const URL_ASSINATURA = '/assinatura/index/visualizar-projeto';

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura";
        $auth = Zend_Auth::getInstance();
        $Usuario = new UsuarioDAO();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if ($auth->hasIdentity()) { // caso o usuario esteja autenticado
            // verifica as permissoes
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 122;
            $PermissoesGrupo[] = 121;
            $PermissoesGrupo[] = 129;
            $PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 135; // tecnico
            $PermissoesGrupo[] = 134; // coordenador
            $PermissoesGrupo[] = 138; // tecnico de avaliacao
            $PermissoesGrupo[] = 139; // coordenador de avaliacao
            $PermissoesGrupo[] = 124; //Tecnico de Prestacao de Contas
            $PermissoesGrupo[] = 132; // Chefe de Divisao
            $PermissoesGrupo[] = 151;
            $PermissoesGrupo[] = 148;
            $PermissoesGrupo[] = Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS;
            $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo esta no array de permissoes
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            $grupos = $Usuario->buscarUnidades(isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario, 21);

            $this->view->usuario = $auth->getIdentity();
            $this->view->arrayGrupos = $grupos;
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo;
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao;
            $this->orgSup = $GrupoAtivo->codOrgao;
            $this->usu_orgao = $GrupoAtivo->codOrgao;

            $this->getIdOrgao = $GrupoAtivo->codOrgao;
        } else { // caso o usuario n&atilde;o esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }


        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        parent::init();
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $idusuario = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;
        $codPerfil = $GrupoAtivo->codGrupo;
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

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
            $order = array(2); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['b.Orgao = ?'] = $codOrgao;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['AnoProjeto+Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }
        $analisados = false;
        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $tipoFiltro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            switch ($tipoFiltro) {
                case 'emanalise':
                    $tipoFiltro = 'emanalise';
                    $filtro = 'Em an&aacute;lise';
                    $where['a.siCumprimentoObjeto in (?)'] = [3, 4];
                    $order = ['3 DESC'];
                    break;
                case 'analisados':
                    $tipoFiltro = 'analisados';
                    $filtro = 'Analisados';
                    $analisados = true;
                    $where['a.siCumprimentoObjeto = ?'] = 5;
                    break;
                default:
                    $tipoFiltro = 'aguardando';
                    $filtro = 'Aguardando An&aacute;lise';
                    $where['a.siCumprimentoObjeto = ?'] = 2;
                    break;
            }
        } else {
            $tipoFiltro = 'aguardando';
            $filtro = 'Aguardando An&aacute;lise';
            $where['a.siCumprimentoObjeto = ?'] = 2;
        }


        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $total = $tbCumprimentoObjeto->listaRelatorios($where, $order, null, null, true, 'analisados');
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        $busca = $tbCumprimentoObjeto->listaRelatorios($where, $order, $tamanho, $inicio, false, 'analisados');

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

        $this->view->urlAssinatura = self::URL_ASSINATURA;
        $this->view->originAssinatura = 'comprovacao-objeto/avaliaracompanhamentoprojeto/index?tipoFiltro=analisados';
        $this->view->paginacao = $paginacao;
        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;
        $this->view->filtro = $filtro;
        $this->view->tipoFiltro = $tipoFiltro;

        $vw = new vwUsuariosOrgaosGrupos();
        $usuarios = $vw->buscarUsuarios($codPerfil, $codOrgao);
        $this->view->Usuarios = $usuarios;
    }

    public function imprimirPainelAction()
    {
        $this->_helper->layout->disableLayout();

        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idusuario = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  orgao ativo na sessao
        $codPerfil = $GrupoAtivo->codGrupo; //  orgao ativo na sessao
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

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
            $order = array(2); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $post = Zend_Registry::get('post');
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['b.Orgao = ?'] = $codOrgao;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['AnoProjeto+Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }
        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $tipoFiltro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            switch ($tipoFiltro) {
                case 'emanalise': //Em an&aacute;lise
                    $tipoFiltro = 'emanalise';
                    $filtro = 'Em an&aacute;lise';
                    $where['a.siCumprimentoObjeto = ?'] = 3;
                    break;
                case 'analisados': //Analisados
                    $tipoFiltro = 'analisados';
                    $filtro = 'Analisados';
                    $where['a.siCumprimentoObjeto = ?'] = 5;
                    break;
                default: //Aguardando An&aacute;lise
                    $tipoFiltro = 'aguardando';
                    $filtro = 'Aguardando An&aacute;lise';
                    $where['a.siCumprimentoObjeto = ?'] = 2;
                    break;
            }
        } else { //Aguardando An&aacute;lise
            $tipoFiltro = 'aguardando';
            $filtro = 'Aguardando An&aacute;lise';
            $where['a.siCumprimentoObjeto = ?'] = 2;
        }

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $total = $tbCumprimentoObjeto->listaRelatorios($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        $busca = $tbCumprimentoObjeto->listaRelatorios($where, $order, $tamanho, $inicio);

        if (isset($post->xls) && $post->xls) {
            $colspan = 7;
            if (isset($tipoFiltro) && $tipoFiltro != 'aguardando') {
                $colspan = 8;
            }

            $html = '';
            $html .= '<table style="border: 1px">';
            $html .= '<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 16px; font-weight: bold;" colspan="' . $colspan . '">Analisar Comprova&ccedil;&atilde;o do Objeto - ' . $filtro . '</td></tr>';
            $html .= '<tr><td style="border: 1px dotted black; background-color: #EAF1DD; font-size: 10px" colspan="' . $colspan . '">Data do Arquivo: ' . Data::mostraData() . '</td></tr>';
            $html .= '<tr><td colspan="' . $colspan . '"></td></tr>';

            $html .= '<tr>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">#</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">PRONAC</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Nome do Projeto</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">UF</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Mecanismo</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Situa&ccedil;&atilde;o</th>';
            $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">Dt. Relat&oacute;rio</th>';
            if (isset($tipoFiltro) && $tipoFiltro != 'aguardando') {
                $html .= '<th style="border: 1px dotted black; background-color: #9BBB59;">T&eacute;cnico</th>';
            }
            $html .= '</tr>';

//            $pa = new paUsuariosDoPerfil();
            $vw = new vwUsuariosOrgaosGrupos();
            $usuarios = $vw->buscarUsuarios($codPerfil, $codOrgao);

            $i = 1;
            foreach ($busca as $dp) {
                if ($dp->Mecanismo == 1) {
                    $mecanismo = 'Incentivo Fiscal Federal';
                } elseif ($dp->Mecanismo != 2) {
                    $mecanismo = 'FNC';
                } elseif ($dp->Mecanismo != 6) {
                    $mecanismo = 'Recursos do Tesouro';
                }

                if (isset($tipoFiltro) && $tipoFiltro != 'aguardando') {
                    foreach ($usuarios as $user) {
                        if ($user->idUsuario == $dp->idTecnicoAvaliador) {
                            $nomeTec = $user->Nome;
                        }
                    }
                }

                $html .= '<tr>';
                $html .= '<td style="border: 1px dotted black;">' . $i . '</td>';
                $html .= '<td style="border: 1px dotted black;">' . $dp->Pronac . '</td>';
                $html .= '<td style="border: 1px dotted black;">' . $dp->NomeProjeto . '</td>';
                $html .= '<td style="border: 1px dotted black;">' . $dp->UfProjeto . '</td>';
                $html .= '<td style="border: 1px dotted black;">' . $mecanismo . '</td>';
                $html .= '<td style="border: 1px dotted black;">' . $dp->Situacao . ' - ' . $dp->dsSituacao . '</td>';
                $html .= '<td style="border: 1px dotted black;">' . Data::tratarDataZend($dp->dtCadastro, 'Brasileiro') . '</td>';
                if (isset($tipoFiltro) && $tipoFiltro != 'aguardando') {
                    $html .= '<td style="border: 1px dotted black;">' . $nomeTec . '</td>';
                }
                $html .= '</tr>';
                $i++;
            }
            $html .= '</table>';

            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=Analisar_Comprovacao_do_Objeto.ods;");
            echo $html;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->view->dados = $busca;
            $this->view->filtro = $filtro;
            $this->view->tipoFiltro = $tipoFiltro;

            $vw = new vwUsuariosOrgaosGrupos();
            $usuarios = $vw->buscarUsuarios($codPerfil, $codOrgao);
            $this->view->Usuarios = $usuarios;
        }
    }

    public function visualizarRelatorioAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        $this->view->idPronac = $idPronac;
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $DadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto(array('idPronac = ?' => $idPronac, 'siCumprimentoObjeto in (?)' => array(2, 3, 4, 5)));
        $this->view->DadosRelatorio = $DadosRelatorio;
        $this->view->cumprimentoDoObjeto = $tbCumprimentoObjeto;
        if (count($DadosRelatorio) == 0) {
            parent::message("Projeto ainda não possui relat&oacute;rio!", "comprovacao-objeto/avaliaracompanhamentoprojeto/index", "ALERT");
        }

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idPronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idPronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idPronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idPronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idPronac);
        $this->view->DadosCompMetas = $DadosCompMetas;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idPronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idPronac);
        $this->view->DadosComprovantes = $dadosComprovantes;

        $tbTermoAceiteObra = new ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
        $AceiteObras = $tbTermoAceiteObra->buscarTermoAceiteObraArquivos(array('idPronac=?' => $idPronac));
        $this->view->AceiteObras = $AceiteObras;

        $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
        $BensCadastrados = $tbBensDoados->buscarBensCadastrados(array('a.idPronac=?' => $idPronac), array('b.Descricao'));
        $this->view->BensCadastrados = $BensCadastrados;

        $Usuario = new UsuarioDAO();
        $nmUsuarioCadastrador = $Usuario->buscarUsuario($DadosRelatorio->idTecnicoAvaliador);

        $this->view->TecnicoAvaliador = $nmUsuarioCadastrador;

        if (!empty($DadosRelatorio->idChefiaImediata)) {
            $nmChefiaImediata = $Usuario->buscarUsuario($DadosRelatorio->idChefiaImediata);
            $this->view->ChefiaImediata = $nmChefiaImediata;
        }

    }

    public function imprimirAction()
    {
        $idPronac = $this->_request->getParam("pronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $DadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto(array('idPronac = ?' => $idPronac, 'siCumprimentoObjeto!=?' => 1));
        $this->view->DadosRelatorio = $DadosRelatorio;
        if (count($DadosRelatorio) == 0) {
            parent::message("Relat&oacute;rio n&atilde;o encontrado!", "comprovacao-objeto/avaliaracompanhamentoprojeto/index", "ALERT");
        }

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idPronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idPronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idPronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $planosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idPronac);
        $this->view->PlanosCadastrados = $planosCadastrados;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idPronac);
        $this->view->DadosCompMetas = $DadosCompMetas;

        $dadosItensOrcam = $projetos->buscarItensComprovados($idPronac);
        $this->view->DadosItensOrcam = $dadosItensOrcam;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idPronac);
        $this->view->DadosComprovantes = $dadosComprovantes;

        $tbTermoAceiteObra = new ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
        $aceiteObras = $tbTermoAceiteObra->buscarTermoAceiteObraArquivos(array('idPronac=?' => $idPronac));
        $this->view->AceiteObras = $aceiteObras;

        $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
        $bensCadastrados = $tbBensDoados->buscarBensCadastrados(array('a.idPronac=?' => $idPronac), array('b.Descricao'));
        $this->view->BensCadastrados = $bensCadastrados;

        $Usuario = new UsuarioDAO();
        $nmUsuarioCadastrador = $Usuario->buscarUsuario($DadosRelatorio->idTecnicoAvaliador);
        $this->view->TecnicoAvaliador = $nmUsuarioCadastrador;

        if (!empty($DadosRelatorio->idChefiaImediata)) {
            $nmChefiaImediata = $Usuario->buscarUsuario($DadosRelatorio->idChefiaImediata);
            $this->view->ChefiaImediata = $nmChefiaImediata;

            $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
        }
    }

    public function encaminharRelatorioAction()
    {
        $post = Zend_Registry::get('post');
        $idPronac = (int)$post->pronac;

        $dados = array();
        $dados['idTecnicoAvaliador'] = (int)$post->tecnico;
        $dados['siCumprimentoObjeto'] = 3;
        $where = "idPronac = $idPronac";

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $return = $tbCumprimentoObjeto->update($dados, $where);

        if ($return) {
            parent::message("Relat&oacute;rio encaminhado com sucesso!", "comprovacao-objeto/avaliaracompanhamentoprojeto/index", "CONFIRM");
        } else {
            parent::message("Relat&oacute;rio n&atilde;o foi encaminhado. Contate o Administrador do sistema!", "comprovacao-objeto/avaliaracompanhamentoprojeto/index", "ERROR");
        }
    }

    public function indexTecnicoAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idusuario = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  orgao ativo na sessao
        $codPerfil = $GrupoAtivo->codGrupo; //  orgao ativo na sessao
        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;

        if ($codPerfil != Autenticacao_Model_Grupos::TECNICO_AVALIACAO
            && $codPerfil != Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS
            && $codPerfil != Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO
            && $codPerfil != Autenticacao_Model_Grupos::PRESIDENTE_VINCULADA_SUBSTITUTO) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa funcionalidade!", "principal", "ALERT");
        }

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
            $order = array('NomeProjeto');
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['b.Orgao = ?'] = $codOrgao;
        $where['a.siCumprimentoObjeto in (?)'] = array(3, 4);
        $where['a.idTecnicoAvaliador = ?'] = $idusuario;

        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['AnoProjeto+Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $total = $tbCumprimentoObjeto->listaRelatorios($where, $order, null, null, true, 'painel-tecnico');
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbCumprimentoObjeto->listaRelatorios($where, $order, $tamanho, $inicio, false , 'painel-tecnico');

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
        $this->view->qtdRelatorios = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;
    }

    public function devolverRelatorioAction()
    {
        $this->_helper->layout->disableLayout();

        $post = Zend_Registry::get('post');
        $idPronac = (int)$post->pronac;

        $dados = array();
        $dados['idTecnicoAvaliador'] = null;
        $dados['siCumprimentoObjeto'] = 2;
        $where = "idPronac = $idPronac";

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $return = $tbCumprimentoObjeto->update($dados, $where);

        if ($return) {
            $this->_helper->json(array('resposta' => true));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function parecerTecnicoAction()
    {
        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;
        $nmusuario = $auth->getIdentity()->usu_nome;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codPerfil = $GrupoAtivo->codGrupo; //  orgao ativo na sessao

        if ($codPerfil != Autenticacao_Model_Grupos::TECNICO_AVALIACAO
            && $codPerfil != Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS) {
            parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa funcionalidade!", "principal", "ALERT");
        }

        $idPronac = $this->_request->getParam("idPronac");
        $idRelatorio = $this->_request->getParam("relatorio");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();

        $projetos = new Projetos();
        $dadosProjeto = $projetos->buscarProjetoXProponente(['idPronac = ?' => $idPronac])->current();
        $dadosParecer = $tbCumprimentoObjeto->buscarCumprimentoObjeto([
            'idPronac = ?' => $idPronac,
            'idTecnicoAvaliador = ?' => $idusuario,
            'siCumprimentoObjeto in (?)' => [
                ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_PARA_AVALIACAO_TECNICO,
                ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_EM_AVALIACAO_TECNICO
            ]
        ]);

        if (empty($dadosParecer)) {
            parent::message("Objeto n&atilde;o encontrado!",
                "comprovacao-objeto/avaliaracompanhamentoprojeto/index-tecnico",
                "ALERT"
            );
        }

        if ($dadosParecer->siCumprimentoObjeto == ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_EM_AVALIACAO_TECNICO) {
            $servicoDocumentoAssinatura = new \Application\Modules\ComprovacaoObjeto\Service\Assinatura\DocumentoAssinatura(
                $idPronac,
                Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_AVALIACAO_OBJETO,
                $dadosParecer->idCumprimentoObjeto
            );

            $assinatura = $servicoDocumentoAssinatura->obterProjetoDisponivelParaAssinatura();

            if (!empty($assinatura)) {

                $url = '%1$s/?idDocumentoAssinatura=%2$s&origin=%3$s';
                $urlAssinatura = sprintf(
                    $url,
                    self::URL_ASSINATURA,
                    $assinatura['idDocumentoAssinatura'],
                    'comprovacao-objeto/avaliaracompanhamentoprojeto/index-tecnico'
                );

                parent::message("Parecer j&aacute; finalizado! Aguardando assinatura!",
                    $urlAssinatura,
                    "ALERT"
                );
            }
        }

        $this->view->DadosProjeto = $dadosProjeto;
        $this->view->DadosParecer = $dadosParecer;
        $this->view->idPronac = $idPronac;
        $this->view->idRelatorio = $idRelatorio;
        $this->view->idusuario = $idusuario;
        $this->view->nmusuario = $nmusuario;

    }

    public function etapasDeTrabalhoFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $DadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto(array('idPronac=?' => $idPronac));
        $this->view->DadosRelatorio = $DadosRelatorio;
        $this->view->cumprimentoDoObjeto = $tbCumprimentoObjeto;
        $this->view->idPronac = $idPronac;
    }

    public function localDeRealizacaoFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $LocaisDeRealizacao = $projetos->buscarLocaisDeRealizacao($idPronac);
        $this->view->LocaisDeRealizacao = $LocaisDeRealizacao;
        $this->view->idPronac = $idPronac;
    }

    public function planoDeDivulgacaoFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $dadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $dadosProjeto;

        $PlanoDeDivulgacao = $projetos->buscarPlanoDeDivulgacao($idPronac);
        $this->view->PlanoDeDivulgacao = $PlanoDeDivulgacao;

        $Verificacao = new Verificacao();
        $Peca = $Verificacao->buscar(array('idTipo =?' => 1, 'stEstado =?' => 1));
        $this->view->Peca = $Peca;

        $Veiculo = $Verificacao->buscar(array('idTipo =?' => 2, 'stEstado =?' => 1));
        $this->view->Veiculo = $Veiculo;
        $this->view->idPronac = $idPronac;

        $tbArquivoImagem = new tbArquivoImagem();
        $this->view->marcas = $tbArquivoImagem->marcasAnexadas($dadosProjeto->pronac);
    }

    public function planoDeDistribuicaoFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $PlanoDeDistribuicao = $PlanoDistribuicaoProduto->buscarPlanoDeDistribuicao($idPronac);
        $this->view->PlanoDeDistribuicao = $PlanoDeDistribuicao;

        $tbBeneficiarioProdutoCultural = new tbBeneficiarioProdutoCultural();
        $PlanosCadastrados = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idPronac);
        $this->view->PlanosCadastrados = $PlanosCadastrados;
        $this->view->idPronac = $idPronac;
    }

    public function metasComprovadasFinalAction()
    {

        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $DadosCompMetas = $projetos->buscarMetasComprovadas($idPronac);
        $this->view->DadosCompMetas = $DadosCompMetas;
        $this->view->idPronac = $idPronac;
    }

    public function itensComprovadosFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $DadosItensOrcam = $projetos->buscarItensComprovados($idPronac);
        $this->view->DadosItensOrcam = $DadosItensOrcam;
        $this->view->idPronac = $idPronac;
    }

    public function comprovantesDeExecucaoFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $Arquivo = new Arquivo();
        $dadosComprovantes = $Arquivo->buscarComprovantesExecucao($idPronac);
        $this->view->DadosComprovantes = $dadosComprovantes;
        $this->view->idPronac = $idPronac;
    }

    public function aceiteDeObraFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbTermoAceiteObra = new ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
        $DadosRelatorio = $tbTermoAceiteObra->buscarTermoAceiteObraArquivos(array('idPronac=?' => $idPronac));
        $this->view->DadosRelatorio = $DadosRelatorio;
        $this->view->idPronac = $idPronac;
    }

    public function bensFinalAction()
    {
        $this->verificarPermissaoAcesso(false, true, false);

        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $projetos = new Projetos();
        $DadosProjeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->DadosProjeto = $DadosProjeto;

        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $DadosItens = $tbPlanilhaAprovacao->buscarItensOrcamentarios(array('a.idPronac=?' => $idPronac), array('b.Descricao'));
        $this->view->DadosItens = $DadosItens;

        $tbBensDoados = new ComprovacaoObjeto_Model_DbTable_TbBensDoados();
        $BensCadastrados = $tbBensDoados->buscarBensCadastrados(array('a.idPronac=?' => $idPronac), array('b.Descricao'));
        $this->view->BensCadastrados = $BensCadastrados;
        $this->view->idPronac = $idPronac;
    }

    public function salvarAvaliacaoAction()
    {
        try {

            $auth = Zend_Auth::getInstance();
            $idusuario = $auth->getIdentity()->usu_codigo;

            $idPronac = $this->_request->getParam("idPronac");
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }

            $where = array();
            $where['idPronac = ?'] = $idPronac;
            $where['idTecnicoAvaliador = ?'] = $idusuario;
            $where['siCumprimentoObjeto in (?)'] = array(3, 4);

            $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
            $dadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto($where);

            if (empty($dadosRelatorio)) {
                parent::message('Relat&oacute;rio n&atilde;o encontrado!', "comprovacao-objeto/avaliaracompanhamentoprojeto/index-tecnico", "ALERT");
            }

            if ($this->getRequest()->isPost()) {
                $msg = 'Relat&oacute;rio salvo com sucesso!';
                $callback = "comprovacao-objeto/avaliaracompanhamentoprojeto/parecer-tecnico?idPronac=" . $idPronac;

                $post = $this->getRequest()->getPost();

                if (!empty($post['finalizar'])) {

                    $tbProjetos = new Projeto_Model_DbTable_Projetos();
                    $projeto = $tbProjetos->findBy(['idPronac = ?' => $idPronac]);

                    if ($projeto['Situacao'] == Projeto_Model_Situacao::DILIGENCIADO_NA_AVALIACAO_CUMPRIMENTO_DE_OBJETO) {
                        throw new Exception("N&atilde;o &eacute; poss&iacute;vel finalizar a avalia&ccedil;&atilde;o. O projeto est&aacute; em dilig&ecirc;ncia!");
                    }

                    $msg = 'Relat&oacute;rio finalizado com sucesso, agora voc&ecirc; deve assinar o documento para continuar!';
                    $callback = 'comprovacao-objeto/avaliaracompanhamentoprojeto/index-tecnico';

                    if (empty($post['informacaoAdicional']) || strlen($post['informacaoAdicional']) < 50) {
                        throw new Exception("Parecer de avalia&ccedil;&atilde;o t&eacute;cnica &eacute; obrigat&oacute;rio");
                    }

                    if (empty($post['conclusao']) || strlen($post['conclusao']) < 50) {
                        throw new Exception("Conclus&atilde;o &eacute; obrigat&oacute;rio");
                    }

                    if (empty($post['resultadoAvaliacao'])) {
                        throw new Exception("Avalia&ccedil;&atilde;o é obrigat&oacute;rio");
                    }
                }

                $dados = array(
                    'dsInformacaoAdicional' => $post['informacaoAdicional'],
                    'dsOrientacao' => $post['orientacao'],
                    'dsConclusao' => $post['conclusao'],
                    'stResultadoAvaliacao' => $post['resultadoAvaliacao'],
                    'idChefiaImediata' => $post['chefiaImediata'],
                    'siCumprimentoObjeto' => ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_PARA_AVALIACAO_TECNICO
                );

                $whereFinal = 'idCumprimentoObjeto = ' . $dadosRelatorio->idCumprimentoObjeto;
                $resultado = $tbCumprimentoObjeto->alterar($dados, $whereFinal);

                if ($post['finalizar']) {
                    $idDocumentoAssinatura = $this->iniciarFluxoAssinatura($idPronac);
                    if ($idDocumentoAssinatura) {
                        parent::message(
                            $msg, "/assinatura/index/visualizar-projeto?idDocumentoAssinatura={$idDocumentoAssinatura}&origin={$callback}",
                            "CONFIRM"
                        );
                    }
                }

                if ($resultado) {
                    parent::message($msg, $callback, "CONFIRM");
                }
            }

        } catch (Exception $e) {
            parent::message($e->getMessage(), "/comprovacao-objeto/avaliaracompanhamentoprojeto/parecer-tecnico?idPronac=" . $idPronac, "ERROR");
        }
    }

    final private function iniciarFluxoAssinatura($idPronac)
    {
        if (empty($idPronac)) {
            throw new Exception(
                "Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade."
            );
        }

        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;

        $where = array();
        $where['idPronac = ?'] = $idPronac;
        $where['idTecnicoAvaliador = ?'] = $idusuario;
        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $dadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto($where);

        if (empty($dadosRelatorio->idCumprimentoObjeto)) {
            throw new Exception(
                "&Eacute; necess&amp;aacute;rio ao menos um parecer para iniciar o fluxo de assinatura."
            );
        }

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $documentoAssinatura = $objDbTableDocumentoAssinatura->obterProjetoDisponivelParaAssinatura(
            $idPronac,
            Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_AVALIACAO_OBJETO
        );

        if (count($documentoAssinatura) < 1) {
            $servicoDocumentoAssinatura = new \Application\Modules\ComprovacaoObjeto\Service\Assinatura\DocumentoAssinatura(
                $idPronac,
                Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_AVALIACAO_OBJETO,
                $dadosRelatorio->idCumprimentoObjeto
            );
            $idDocumentoAssinatura = $servicoDocumentoAssinatura->iniciarFluxo();
        } else {
            $idDocumentoAssinatura = $documentoAssinatura['idDocumentoAssinatura'];
        }

        return $idDocumentoAssinatura;
    }

    /**
     * @deprecated não utilizado com o modelo de assinatura
     */
    public function finalizarRelatorioAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        die;
        $post = Zend_Registry::get('post');
        $idPronac = (int)$post->pronac;
        $dados = array();
        $dados['siCumprimentoObjeto'] = 6;

        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();

        $where = [];
        $where['idPronac = ?'] = $idPronac;
        $where['siCumprimentoObjeto in (?)'] = array(5);
        $dadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto($where);

        $where = "idPronac = $idPronac";


        $retorno = false;
        if ($dadosRelatorio->stResultadoAvaliacao == 'A') {
            $retorno = $tbCumprimentoObjeto->update($dados, $where);
            $tbProjetos = new Projetos();

            $situacao = Projeto_Model_Situacao::AGUARDA_ANALISE_FINANCEIRA;
            $providenciaTomada = 'Projeto encaminhado para an&aacute;lise financeira';
            $tbProjetos->alterarSituacao($idPronac, '', $situacao, $providenciaTomada);
        }

        if ($retorno) {
            $this->_helper->json(array('resposta' => true));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function recursosPorFonteAction()
    {
        $planilhaAprovacaoModel = new PlanilhaAprovacao();
        $this->view->recursosPorFonte = $planilhaAprovacaoModel->buscarRecursosDaFonte($this->getRequest()->getParam('idPronac'));
        $this->view->idPronac = $this->getRequest()->getParam('idPronac');
    }

    public function gerenciarAssinaturasAction()
    {
        $origin = $this->_request->getParam('origin');
        if ($origin != '') {
            $this->redirect($origin);
        }
        $this->redirect("/comprovacao-objeto/avaliaracompanhamentoprojeto/index-tecnico");
    }
}
