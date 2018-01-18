<?php

class DadosprojetoController extends MinC_Controller_Action_Abstract
{

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // titulo da pagina
        $auth              = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario           = new UsuarioDAO(); // objeto usuario
        $GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $PermissoesGrupo = array();
        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario(); // objeto usu�rio
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        /*if ($auth->hasIdentity()) // caso o usuario esteja autenticado
        {
            // verifica as permissoes
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            $PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 103; // Coordenador de Analise
            $PermissoesGrupo[] = 118; // Componente da Comissao
            $PermissoesGrupo[] = 119; // Presidente da Mesa
            $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) // verifica se o grupo ativo esta no array de permissoes
            {
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usuario (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visao
            $this->view->usuario     = $auth->getIdentity(); // manda os dados do usuario para a visao
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
            $this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao
            $this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o orgao ativo do usuario para a visao
        } // fecha if
        else // caso o usuario n�o esteja autenticado
        {
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }*/

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()



        public function ultimatramitacaoAction()
        {
            $auth = Zend_Auth::getInstance(); // pega a autenticacao
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
                parent::message("N�o existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
            }
            $pronac = $this->_request->getParam("idpronac");
            $tbdadosprojeto = DadosprojetoDAO::buscar($pronac);
            $this->view->dadosprojeto = $tbdadosprojeto;
        }

    public function despachoAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];
        //-------------------------------------------------------------------------------------------------------------
        $ConsultaReuniaoAberta = ReuniaoDAO::buscarReuniaoAberta();
        $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
        //---------------------------------------------------------------------------------------------------------------
        $exibirVotantes = AtualizaReuniaoDAO::selecionarvotantes($ConsultaReuniaoAberta['idnrreuniao']);
        if (count($exibirVotantes) > 0) {
            foreach ($exibirVotantes as $votantes) {
                $dadosVotante[] = $votantes->idagente;
            }
            if (count($dadosVotante) > 0) {
                if (in_array($idagente, $dadosVotante)) {
                    $this->view->votante = 'ok';
                } else {
                    $this->view->votante = 'nao';
                }
            }
        }
        $get = Zend_Registry::get('get');
        $pronac = $get->idPronac;
        $tbdadosprojeto = DadosprojetoDAO::buscar($pronac);
        $this->view->dadosprojeto = $tbdadosprojeto;
    }

    public function localprojetoAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];
        //-------------------------------------------------------------------------------------------------------------
        $ConsultaReuniaoAberta = ReuniaoDAO::buscarReuniaoAberta();
        $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
        //---------------------------------------------------------------------------------------------------------------
        $exibirVotantes = AtualizaReuniaoDAO::selecionarvotantes($ConsultaReuniaoAberta['idnrreuniao']);
        if (count($exibirVotantes) > 0) {
            foreach ($exibirVotantes as $votantes) {
                $dadosVotante[] = $votantes->idagente;
            }
            if (count($dadosVotante) > 0) {
                if (in_array($idagente, $dadosVotante)) {
                    $this->view->votante = 'ok';
                } else {
                    $this->view->votante = 'nao';
                }
            }
        }
        $get = Zend_Registry::get('get');
        $pronac = $get->idPronac;
        $tbdadosprojeto = DadosprojetoDAO::buscar($pronac);
        $this->view->dadosprojeto = $tbdadosprojeto;
    }


    public function indexAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
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
            parent::message("Nao existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
        }

        $idpronac = $this->_request->getParam("idpronac");
        $projetos = new Projetos();
        $aprovacao = new Aprovacao();
        $PlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $interessado      = new Interessado();
        $agente           = new Agente_Model_DbTable_Agentes();

        $dadosprojeto = $projetos->buscarTodosDadosProjeto($idpronac)->current()->toArray();
        $buscarInteressado = $interessado->buscar(array('CgcCpf = ?'=> $dadosprojeto['CgcCpf']));

        if ($buscarInteressado->count() > 0) {
            $proponente = $buscarInteressado->current()->toArray();
            $this->view->proponente = $proponente['Nome'];
        } else {
            $nome = new Nomes();
            $buscarNome = $nome->buscarNomePorCPFCNPJ($dadosprojeto['CgcCpf']);
            $this->view->proponente = $buscarNome['Nome'];
        }
        $enquadramento = $dadosprojeto['Enquadramento'] == 1 ? 'Artigo 26' : 'Artigo 18';
        $this->view->Enquadramento = $enquadramento;
        $buscarAprovacao = $aprovacao->buscar(array("IdPRONAC = ?"=>$idpronac));

        if (!empty($dadosprojeto['idProjeto'])) {
            $outrasfontes = $PlanilhaProposta->somarPlanilhaProposta($dadosprojeto['idProjeto'], false, 109);
            $incentivo    = $PlanilhaProposta->somarPlanilhaProposta($dadosprojeto['idProjeto'], 109);
            $this->view->outrasfontes   = $outrasfontes['soma'] ? $outrasfontes['soma'] : 0 ;
            $this->view->valorproposta  = $incentivo['soma'] + $outrasfontes['soma'];
        } else {
            $this->view->outrasfontes   = '';
            $this->view->valorproposta  = '';
        }
        $this->view->dadosprojeto = $dadosprojeto;
        $this->view->ValorAprovado = '0';
        $this->view->idpronac = $idpronac;
    }

    public function ajaxAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        if (isset($_POST['tipo']) and $_POST['tipo'] == 'objetivos') {
            $idpronac = $_POST['idpronac'];
            $buscarObj = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela' >";
            $htmlGerado .= "<th colspan='6' >Objetivos</th>";
            if (count($buscarObj['Objetivos']) == 0) {
                $htmlGerado .= "<tr style='font:4em'><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $buscarObj['Objetivos'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'justificativa') {
            $idpronac = $_POST['idpronac'];
            $buscarJust = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Justificativa</th>";
            if (count($buscarJust['Justificativa']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $buscarJust['Justificativa'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'acessibilidade') {
            $idpronac = $_POST['idpronac'];
            $aces = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Acessibilidade</th>";
            if (count($aces['Acessibilidade']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $aces['Acessibilidade'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'democratizacao') {
            $idpronac = $_POST['idpronac'];
            $democ = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Democratiza&ccedil;&atilde;o de Acesso</th>";
            if (count($democ['DemocratizacaoDeAcesso']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $democ['DemocratizacaoDeAcesso'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }
        if (isset($_POST['tipo']) and $_POST['tipo'] == 'etapa') {
            $idpronac = $_POST['idpronac'];
            $etapa = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Etapa de trabalho</th>";
            if (count($etapa['EtapaDeTrabalho']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $etapa['EtapaDeTrabalho'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'ficha') {
            $idpronac = $_POST['idpronac'];
            $ficha = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Ficha T&eacute;cnica</th>";
            if (count($ficha['FichaTecnica']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $ficha['FichaTecnica'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'sinopse') {
            $idpronac = $_POST['idpronac'];
            $sinopse = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Sinopse da Obra</th>";
            if (count($sinopse['Sinopse']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>'Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $sinopse['Sinopse'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'impacto') {
            $idpronac = $_POST['idpronac'];
            $impacto = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Impacto Ambiental</th>";
            if (count($impacto['ImpactoAmbiental']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $impacto['ImpactoAmbiental'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'estrategia') {
            $idpronac = $_POST['idpronac'];
            $estrategia = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>".utf8_encode('Estrat&eacute;gia de Execu&ccedil;&atilde;o')."</th>";
            if (count($estrategia['EstrategiaDeExecucao']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>".utf8_encode('Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!')."</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $estrategia['EstrategiaDeExecucao'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'especificacao') {
            $idpronac = $_POST['idpronac'];
            $especificacao = RealizarAnaliseProjetoDAO::outrasinformacoes($idpronac);

            $htmlGerado  = "<table class='tabela'>";
            $htmlGerado .= "<th colspan='6'>Especifica&ccedil;&atilde;o T&eacute;cnica</th>";
            if (count($especificacao['EspecificacaoTecnica']) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                $htmlGerado .= "<tr><td colspan='6'>";
                $htmlGerado .= $especificacao['EspecificacaoTecnica'];
                $htmlGerado .= "</td></tr>";
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'localrealizacao') {
            $idpronac = $_POST['idpronac'];
            //Local de Realizacao
            $buscarLocalRealizacao = RealizarAnaliseProjetoDAO::localrealizacao($idpronac);

            $htmlGerado = "<table class='tabela'>";
            $htmlGerado .= "<tr class='titulo_tabela'><th colspan='6'>Local de Realiza&ccedil;&atilde;o</th></tr>";
            $htmlGerado .= "<tr>
                                    <th>Pa&iacute;s</th>
                                    <th>Unidade da Federa&ccedil;&atilde;o</th>
                                    <th>Cidade</th>
                                    <th>Dt.In&iacute;cio</th>
                                    <th >Dt.Final</th>
                                   </tr>";
            if (count($buscarLocalRealizacao) == 0) {
                $htmlGerado .= "<tr><td colspan='6' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                foreach ($buscarLocalRealizacao as $local) {
                    $htmlGerado .= "<tr>";
                    $htmlGerado .= "<td align= 'center'>".$local->Descricao."</td>";
                    $htmlGerado .= "<td align= 'center'>".$local->UF."</td>";
                    $htmlGerado .= "<td align= 'center'>".$local->Cidade."</td>";
                    $htmlGerado .= "<td align= 'center'>".$local->DtInicioDeExecucao."</td>";
                    $htmlGerado .= "<td align= 'center'>".$local->DtFinalDeExecucao."</td>";
                    $htmlGerado .= "</tr>";
                }
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'deslocamento') {
            $idpronac = $_POST['idpronac'];
            //Deslocamento
            $buscarDeslocamento = RealizarAnaliseProjetoDAO::deslocamento($idpronac);
            $htmlGerado = "<table class='tabela'>";
            $htmlGerado .= "<tr class='titulo_tabela'><th colspan='8'>Deslocamento</th></tr>";
            $htmlGerado .= "<tr>
                                        <th>Pa&iacute;s de Origem</th>
                                        <th>UF de Origem</th>
                                        <th>Cidade de Origem</th>
                                        <th>Pa&iacute;s de Destino</th>
                                        <th >UF de Destino</th>
                                        <th >Cidade de Destino</th>
                                        <th >Quantidade</th>
                                    </tr>";
            if (count($buscarDeslocamento) == 0) {
                $htmlGerado .= "<tr><td colspan='8' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto</td></tr>";
            } else {
                foreach ($buscarDeslocamento as $deslocamento) {
                    $htmlGerado .="<tr>";
                    $htmlGerado .="<td align='center'>".$deslocamento->PaisOrigem."</td>";
                    $htmlGerado .="<td align='center'>".$deslocamento->UFOrigem."</td>";
                    $htmlGerado .="<td align='center'>".$deslocamento->MunicipioOrigem."</td>";
                    $htmlGerado .="<td align='center'>".$deslocamento->PaisDestino."</td>";
                    $htmlGerado .="<td align='center'>".$deslocamento->UFDestino."</td>";
                    $htmlGerado .="<td align='center'>".$deslocamento->MunicipioDestino."</td>";
                    $htmlGerado .="<td align='center'>".$deslocamento->Qtde."</td>";
                    $htmlGerado ."</tr>";
                }
            }
            $htmlGerado .= "</table>";

            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'divulgacao') {
            $idpronac = $_POST['idpronac'];
            //Divulgacao
            $buscarDivulgacao = RealizarAnaliseProjetoDAO::divulgacao($idpronac);
            $htmlGerado = "<table class='tabela'>";
            $htmlGerado .= "<tr class='titulo_tabela'><th colspan='4'>Divulga&ccedil;&atilde;o</th></tr>";
            $htmlGerado .= "<tr>
                                    <th>Pe&ccedila</th>
                                    <th>Ve&iacute;culo</th>
                                </tr>";
            if (count($buscarDivulgacao) == 0) {
                $htmlGerado .= "<tr><td colspan='8' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto</td></tr>";
            } else {
                foreach ($buscarDivulgacao as $divulgacao) {
                    $htmlGerado .= "<tr>";
                    $htmlGerado .="<td align='center'>".$divulgacao->Peca."</td>";
                    $htmlGerado .="<td align='center'>".$divulgacao->Veiculo."</td>";
                    $htmlGerado .= "</tr>";
                }
            }
            $htmlGerado .= "</table>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'plano') {
            $idpronac = $_POST['idpronac'];
            //Plano de Distribuicao
            $buscarDistribuicao = RealizarAnaliseProjetoDAO::planodedistribuicao($idpronac);
            $htmlGerado = "<table class='tabela'>";
            $htmlGerado .= "<tr class='titulo_tabela'><th colspan='3'>Plano de Distribui&ccedil;&atilde;o de Produtos de Projeto Cultural</th></tr>";
            $htmlGerado .= "<tr >
                                    <th>Produto</th>
                                    <th colspan='2'>Logomarca</th>
                                </tr>";
            if (count($buscarDistribuicao) == 0) {
                $htmlGerado .= "<tr><td colspan='8' align= 'center'>Informa&ccedil;&atilde;o n&atilde;o cadastrada no projeto!</td></tr>";
            } else {
                foreach ($buscarDistribuicao as $distribuicao) {
                    $htmlGerado .= "<tr>";
                    $htmlGerado .= "<td align='center' style='font-size:12pt; font-weight: 600;'>$distribuicao->Produto</td>";
                    $htmlGerado .= "<td align='center' style='font-size:12pt; font-weight: 600;'>$distribuicao->PosicaoDaLogo</td>";
                    $htmlGerado .= "</tr>";


                    $htmlGerado .= "<tr>";
                    $htmlGerado .= "<td	colspan='8'  align= 'center'>";
                    $htmlGerado .= "<table class='tabela' style='margin:0'>";
                    $htmlGerado .= "<tr class='titulo_tabela'>";
                    $htmlGerado .= "<th>Distribui&ccedil;&atilde;o Gratuita (Qtde)</th>";
                    $htmlGerado .= "<th>Total para Venda (Qtde)</th>";
                    $htmlGerado .= "<th>Pre&ccedil;o Unit&atilde;rio (R$)</th>";
                    $htmlGerado .= "</tr>";
                    $buscarDistribuicaoproduto = RealizarAnaliseProjetoDAO::planodedistribuicao($idpronac, $distribuicao->idProduto);
                    foreach ($buscarDistribuicaoproduto as $distribuicao) {
                        $htmlGerado .= "<tr>";
                        $htmlGerado .= "<td>";
                        $htmlGerado .= "<table class='tabela'>";
                        $htmlGerado .= "<tr>
                                                <th>Divulga&ccedil;&atilde;o</th>
                                                <th>Patrocinador</th>
                                                <th>Benefici&atilde;rios</th>
                                                <th>Produzida</th>
                                            </tr>";
                        $htmlGerado .= "<tr>
                                                <td align='center'>".$distribuicao->QtdeProponente."</td>
                                                <td align='center'>".$distribuicao->QtdePatrocinador."</td>
                                                <td align='center'>".$distribuicao->QtdeOutros."</td>
                                                <td align='center'>".$distribuicao->QtdeProduzida."</td>
                                           </tr>";
                        $htmlGerado .= "</table>";
                        $htmlGerado .= "</td>";
                        $htmlGerado .= "<td>";
                        $htmlGerado .= "<table class='tabela'>";
                        $htmlGerado .= "<tr>";
                        $htmlGerado .= "<th>Normal</th>";
                        $htmlGerado .= "<th>Promocional</th>";
                        $htmlGerado .= "</tr>";
                        $htmlGerado .= "<tr>";
                        $htmlGerado .= "<td align='center'>".$distribuicao->QtdeVendaNormal."</td>";
                        $htmlGerado .= "<td align='center'>".$distribuicao->QtdeVendaPromocional."</td>";
                        $htmlGerado .= "</tr>";
                        $htmlGerado .= "</table>";
                        $htmlGerado .= "</td>";
                        $htmlGerado .= "<td>";
                        $htmlGerado .= "<table class='tabela'>";
                        $htmlGerado .= "<tr>";
                        $htmlGerado .= "<th>Normal</th>";
                        $htmlGerado .= "<th>Promocional</th>";
                        $htmlGerado .= "</tr>";
                        $htmlGerado .= "<tr>";
                        $htmlGerado .= "<td align='center'>".number_format($distribuicao->PrecoUnitarioNormal, 2, ',', '.')."</td>";
                        $htmlGerado .= "<td align='center'>".number_format($distribuicao->PrecoUnitarioPromocional, 2, ',', '.')."</td>";
                        $htmlGerado .= "</tr>";
                        $htmlGerado .= "</table>";
                        $htmlGerado .= "</td>";
                        $htmlGerado .= "</tr>";
                        $htmlGerado .="<tr><th>Receita Prevista (R$)</th><th colspan='2'>Total Receita Prevista (R$)</th></tr>";
                        $htmlGerado .="<tr>";
                        $htmlGerado .="<td>";
                        $htmlGerado .="<table class='tabela'>";
                        $htmlGerado .="<tr>
                                               <th>Normal</th>
                                               <th>Promocional</th>
                                               <th>Prevista</th>
                                           </tr>";
                        $htmlGerado .= "<tr>
                                               <td align='center'>".number_format($distribuicao->ReceitaNormal, 2, ',', '.')."</td>
                                               <td align='center'>".number_format($distribuicao->ReceitaPro, 2, ',', '.')."</td>
                                               <td align='center'>".number_format($distribuicao->ReceitaPrevista, 2, ',', '.')."</td>
                                          </tr>";
                        $htmlGerado .= "</table>";
                        $htmlGerado .= "</td>";
                        $htmlGerado .= "<td colspan='2'>";
                        $htmlGerado .= "<table class='tabela' >";
                        $htmlGerado .= "<tr>
                                             <th>Total Receita Prevista(R$)</th>
                                           </tr>";
                        $htmlGerado .= "<tr>";
                        $htmlGerado .= "<td align='center'>".number_format($distribuicao->ReceitaPrevista, 2, ',', '.')."</td>";
                        $htmlGerado .= "</tr>";
                        $htmlGerado .= "</table>
                                                        </td>";
                        $htmlGerado .="</table>";
                    }
                    $htmlGerado .="</td>";
                }
            }
            $htmlGerado .= "</table>";
            $htmlGerado .= "</td>";
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['tipo']) and $_POST['tipo'] == 'orcamento') {
            $idpronac = $_POST['idpronac'];
            $buscarProdutos = RealizarAnaliseProjetoDAO::planilhaOrcamentoBuscarProduto($idpronac);
            $soma = RealizarAnaliseProjetoDAO::somarOrcamentoSolicitado($idpronac);
            $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
            $buscarPlanilhaEtapa = PlanilhaEtapaDAO::buscar();

            $buscarpronac = ProjetoDAO::buscarPronac($idpronac);
            $buscarPronac = ProjetoDAO::buscar($buscarpronac['pronac']);

            $htmlGerado ="<table class=\"tabela\">";
            $htmlGerado .= "<tr>";
            $htmlGerado .= "<th colspan=\"12\" class=\"center\">Planilha de Or&ccedil;amento Sugerido</th>";
            $htmlGerado .= "</tr>";
            //-- ========== INCENTIVO FISCAL FEDERAL ==========
            $htmlGerado . "<tr>";
            $htmlGerado .="<td colspan=\"12\">&nbsp;</td>";
            $htmlGerado .="</tr>";
            $htmlGerado .="<tr>";
            $htmlGerado .="<td colspan=\"12\">";
            $htmlGerado .="<strong>";
            $htmlGerado .="<div id=\"icn_maisIFF\" class=\"sumir\"><a href=\"#icn_maisIFF\" onclick=\"closeIFF('IFF');\" onkeypress=\" \"><div class=\"icn_mais\" style=\"width:90%\"><span class=\"red del_link\">FONTE DE RECURSO: INCENTIVO FISCAL FEDERAL</span></div></a></div>";
            $htmlGerado .="<div id=\"icn_menosIFF\"><a href=\"#icn_menosIFF\" onclick=\"openIFF('IFF');\" onkeypress=\" \"><div class=\"icn_menos\" style=\"width:90%\"><span class=\"red del_link\">FONTE DE RECURSO: INCENTIVO FISCAL FEDERAL</span></div></a></div>";
            $htmlGerado .="</strong>";
            $htmlGerado .= "</td>";
            $htmlGerado .= "</tr>";
            $htmlGerado .= "<tr class=\"IFF\">";
            $htmlGerado .= "<td colspan=\"12\">&nbsp;</td>";
            $htmlGerado .= "</tr>";
            // ========== INICIO BUSCA POR PRODUTO ==========
                    $item = 1; // contador para os itens
                    $contadorProd = 0;
            $contadorEtapa = 0;
            $contadorUF = 0;
            // inicializa valor total por custo administrativo ou produto
            $totalVal_01_Produto = (float) 0;
            foreach ($buscarProdutos as $buscarProd) {
                $htmlGerado .= "<tr class=\"IFF linha\">";
                $htmlGerado .= "<td colspan=\"12\">";
                $htmlGerado .= "<strong>";
                $htmlGerado .= "<div id=\"icn_maisIFF_PRODUTO$contadorProd\" class=\"sumir\"><a href=\"#icn_menosIFF_PRODUTO$contadorProd\" onclick=\"$('#icn_menosIFF_PRODUTO$contadorProd, .IFF_PRODUTO$contadorProd').show(); $('#icn_maisIFF_PRODUTO$contadorProd').hide();\" onkeypress=\" \"><div class=\"icn_mais\" style=\"width:98%; margin-left:2%;\"><span class=\"green del_link\">$buscarProd->Produto</span></div></a></div>";
                $htmlGerado .= "<div id=\"icn_menosIFF_PRODUTO$contadorProd\"><a href=\"#icn_maisIFF_PRODUTO$contadorProd\" onclick=\"$('#icn_maisIFF_PRODUTO$contadorProd').show(); $('#icn_menosIFF_PRODUTO$contadorProd, .IFF_PRODUTO$contadorProd').hide();\" onkeypress=\" \"><div class=\"icn_menos\" style=\"width:98%; margin-left:2%;\"><span class=\"green del_link\">$buscarProd->Produto</span></div></a></div>";
                $htmlGerado .= "</strong>";
                $htmlGerado .= "</td>";
                $htmlGerado .= "</tr>";
                // ========== INICIO BUSCA POR ETAPA ========== -->
                // inicializa valor total por etapa
                $totalVal_01_Etapa = (float) 0;

                foreach (RealizarAnaliseProjetoDAO::planilhaOrcamentoBuscarEtapa($buscarProd->idPronac, $buscarProd->idProduto, true) as $buscarEtapa) {
                    $htmlGerado .= "<tr class=\"IFF IFF_PRODUTO$contadorProd\">";
                    $htmlGerado .= "<td colspan=\"12\">";
                    $htmlGerado .= "<strong style=\"margin-left:2%;\">";
                    $htmlGerado .= "<div id=\"icn_maisIFF_ETAPA$contadorEtapa\" class=\"sumir\"><a href=\"#icn_menosIFF_ETAPA$contadorEtapa\" onclick=\"$('#icn_menosIFF_ETAPA$contadorEtapa, .IFF_ETAPA$contadorEtapa').show(); $('#icn_maisIFF_ETAPA$contadorEtapa').hide();\" onkeypress=\" \"><div class=\"icn_mais\" style=\"width:90%; margin-left:4%;\"><span class=\"orange del_link\">$buscarEtapa->Etapa</span></div></a></div>";
                    $htmlGerado .= "<div id=\"icn_menosIFF_ETAPA$contadorEtapa\"><a href=\"#icn_maisIFF_ETAPA$contadorEtapa\" onclick=\"$('#icn_maisIFF_ETAPA$contadorEtapa').show(); $('#icn_menosIFF_ETAPA$contadorEtapa, .IFF_ETAPA$contadorEtapa').hide();\" onkeypress=\" \"><div class=\"icn_menos\" style=\"width:90%; margin-left:4%;\"><span class=\"orange del_link\">$buscarEtapa->Etapa</span></div></a></div>";
                    $htmlGerado .= "</strong>";
                    $htmlGerado .= "</td>";
                    $htmlGerado .= "</tr>";
                    //-- ========== INICIO BUSCA POR UF ========== -->
                    $totalVal_01_UF = (float) 0;
                    foreach (RealizarAnaliseProjetoDAO::planilhaOrcamentoBuscarUF($buscarEtapa->idPronac, $buscarEtapa->idProduto, $buscarEtapa->Etapa, true) as $buscarUF) {
                        $htmlGerado .="<tr class=\"IFF IFF_PRODUTO$contadorProd IFF_ETAPA$contadorEtapa\">";
                        $htmlGerado .="<td colspan=\"12\">";
                        $htmlGerado .="<strong style=\"margin-left:3%;\">";
                        $htmlGerado .="<div id=\"icn_maisIFF_UF$contadorUF\" class=\"sumir\"><a href=\"#icn_menosIFF_UF$contadorUF\" onclick=\"$('#icn_menosIFF_UF$contadorUF, .IFF_UF$contadorUF').show(); $('#icn_maisIFF_UF$contadorUF').hide();\" onkeypress=\" \"><div class=\"icn_mais\" style=\"width:90%; margin-left:6%\"><span class=\"black del_link\">$buscarUF->UF -  $buscarUF->Municipio</span></div></a></div>";
                        $htmlGerado .="<div id=\"icn_menosIFF_UF$contadorUF\"><a href=\"#icn_maisIFF_UF$contadorUF\" onclick=\"$('#icn_maisIFF_UF$contadorUF').show(); $('#icn_menosIFF_UF$contadorUF, .IFF_UF$contadorUF').hide();\" onkeypress=\" \"><div class=\"icn_menos\" style=\"width:90%; margin-left:6%\"><span class=\"black del_link\">$buscarUF->UF - $buscarUF->Municipio</span></div></a></div>";
                        $htmlGerado .="</strong>";
                        $htmlGerado .="</td>";
                        $htmlGerado .="</tr>";
                        // ========== INICIO itens ==========
                        // caso tenha projetos
                        $totalItens = 0;
                        if (count(RealizarAnaliseProjetoDAO::planilhaOrcamento($buscarUF->idPronac, $buscarUF->idProduto, $buscarUF->Etapa, $buscarUF->UF, $buscarUF->Municipio, true)) > 0) {
                            // inicializa valor total de itens
                            $totalVal_01 = (float) 0;
                            foreach (RealizarAnaliseProjetoDAO::planilhaOrcamento($buscarUF->idPronac, $buscarUF->idProduto, $buscarUF->Etapa, $buscarUF->UF, $buscarUF->Municipio, true) as $resposta) {
                                $i = $resposta->idPlanilhaProposta; // criar� id �nico
                                // ===== CALCULA TOTAL ITENS =====
                                $totalVal_01 += (float) $resposta->VlSolicitado;
                                $htmlGerado .="<tr onmouseover=\"over_tr(this);\" onfocus=\"over_tr(this);\" onmouseout=\"out_tr(this);\" onblur=\"out_tr(this);\" onclick=\"click_tr(this);\" onkeypress=\"click_tr(this);\"";
                                $htmlGerado .="class=\"IFF IFF_PRODUTO$contadorProd IFF_ETAPA$contadorEtapa IFF_UF$contadorUF";
                                $htmlGerado .=" fundo_linha1\">";

                                $totalItens--;

                                $htmlGerado .= "<td>&nbsp;</td>";
                                $htmlGerado .= "<td>";
                                $htmlGerado .= $resposta->Item;
                                $htmlGerado .= "</td>";
                                $htmlGerado .= "<td class=\"direita\">".$resposta->QtdeDias."</td>";
                                $htmlGerado .= "<td class=\"centro\">";
                                foreach ($buscarPlanilhaUnidade as $unidade) {
                                    if ($unidade->Descricao == $resposta->Unidade) {
                                        $htmlGerado .= $unidade->Descricao;
                                    }
                                }
                                $htmlGerado .= "</td>";
                                $htmlGerado .= "<td class=\"direita\">$resposta->Quantidade</td>";
                                $htmlGerado .= "<td class=\"direita\">$resposta->Ocorrencia</td>";
                                $htmlGerado .= "<td class=\"direita\">".number_format($resposta->ValorUnitario, '2', ',', '.')."</td>";
                                $htmlGerado .= "<td class=\"direita\">".number_format($resposta->VlSolicitado, '2', ',', '.')."</td>";
                                $htmlGerado .= "</tr>";
                                $item++; // incrementa o contador de itens
                            } // fecha foreach itens
                        } // fecha if (caso tenha projetos)
           // ===== CALCULA TOTAL UF =====
           $totalVal_01_UF += (float) $totalVal_01;
                        // ========== FIM itens ==========
                        // EXIBE TOTAL UF -->
                        $htmlGerado .= "<tr class=\"IFF IFF_PRODUTO$contadorProd IFF_ETAPA$contadorEtapa IFF_UF$contadorUF\">";
                        $htmlGerado .= "<td colspan=\"7\"><strong>Total de UF</strong></td>";
                        $htmlGerado .= "<td class=\"direita\"><strong>".number_format($totalVal_01, '2', ',', '.')."</strong></td>";
                        $htmlGerado .= "</tr>";
                        $contadorUF++;
                    }
                    // ========== FIM BUSCA POR UF ========== -->
                    // ===== CALCULA TOTAL ETAPA =====
                    $totalVal_01_Etapa += (float) $totalVal_01_UF;
                    //<!-- EXIBE TOTAL ETAPA -->
                    $contadorEtapaMenos = $contadorEtapa - 1;
                    $htmlGerado .= " <tr class=\"IFF IFF_PRODUTO$contadorProd IFF_ETAPA$contadorEtapaMenos\">";
                    $htmlGerado .=  "<td colspan=\"7\" class=\"orange\"><strong>Total da Etapa</strong></td>";
                    $htmlGerado .=  "<td class=\"direita orange\"><strong>".number_format($totalVal_01_Etapa, '2', ',', '.')."</strong></td>";
                    $htmlGerado .="</tr>";
                    $contadorEtapa++;
                }
                //<!-- ========== FIM BUSCA POR ETAPA ========== -->
                // ===== CALCULA TOTAL PRODUTO =====
                $totalVal_01_Produto += (float) $totalVal_01_Etapa;
                //<!-- EXIBE TOTAL PRODUTO -->
                $htmlGerado .= "<tr class=\"IFF IFF_PRODUTO$contadorProd\">";
                $htmlGerado .= "<td colspan=\"7\" class=\"green\"><strong>Total dos custos administrativos ou do produto</strong></td>";
                $htmlGerado .= "<td class=\"direita green\"><strong>".number_format($totalVal_01_Produto, '2', ',', '.')."</strong></td>";
                $htmlGerado .= "</tr>";
                $htmlGerado .= "<tr class=\"IFF\">";
                $htmlGerado .= "<td colspan=\"12\">&nbsp;</td>";
                $htmlGerado .= "</tr>";
                $contadorProd++;
            }
            //<!-- ========== FIM BUSCA POR PRODUTO ========== -->
            //<!-- ========== INICIO TOTAL GERAL ========== -->
            $htmlGerado .= "<tr class=\"IFF\">";
            $htmlGerado .= "<td colspan=\"7\" class=\"red\"><strong>Total da Fonte de Recurso</strong></td>";
            $htmlGerado .= "<td class=\"direita red\"><strong>".number_format($soma['somatudo'], '2', ',', '.')."</strong></td>";
            $htmlGerado .= "</tr>";
            $htmlGerado .= "<tr>";
            $htmlGerado .= "<th class=\"left\" colspan=\"7\"><strong>Total Geral</strong></th>";
            $htmlGerado .= "<td class=\"direita red\"><strong>".number_format($soma['somatudo'], '2', ',', '.')."</strong></td>";
            $htmlGerado .= "</tr>";

            //<!-- ========== FIM TOTAL GERAL ========== -->
            $htmlGerado .= "</table>";
            // <!-- ========== FIM PLANILHA ========== -->
            echo $htmlGerado;
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }
}// fecha class
