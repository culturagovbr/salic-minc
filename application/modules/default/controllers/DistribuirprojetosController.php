<?php
class DistribuirprojetosController extends MinC_Controller_Action_Abstract
{
    private $idusuario = 0;
    private $usu_identificacao = 0;
    private $codGrupo = 0;
    private $codOrgao = 0;
    private $COD_SITUACAO_PROJETO              =   'G36';
    private $COD_SITUACAO_PROJETO_ATUALIZA     =   'G37';
    private $COD_SITUACAO_PROJETO_COMISSAO     =   'G51';
    private $COD_SITUACAO_PROJETO_SELECIONADOS =   'G52';
    private $COD_STTIPODEMANDA_PREPROJETO      =   'ED';
    private $TP_DISTRIBUICAO                   =   1;
    private $ST_DISTRIBUICAO_PENDENTE          =   1;
    private $ST_DISTRIBUICAO_REALIZADA         =   2;
    private $ST_APROVACAO_APROVADO             =   1;
    private $ST_APROVACAO_REPROVADO            =   0;



    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p�gina

        // 3 => autentica��o scriptcase e autentica��o/permiss�o zend (AMBIENTE PROPONENTE E MINC)
        // utilizar quando a Controller ou a Action for acessada via scriptcase e zend
        // define as permiss?es

        $auth = Zend_Auth::getInstance();// instancia da autentica��o
        $this->idusuario = $auth->getIdentity()->usu_codigo;
        $idorgao = $auth->getIdentity()->usu_orgao;
        $usu_identificacao = $auth->getIdentity()->usu_identificacao;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior))?$auth->getIdentity()->usu_org_max_superior:$auth->getIdentity()->usu_orgao;
        $this->codGrupo = $codGrupo; //  Grupo ativo na sess�o
        $this->codOrgao = $codOrgao;
        $this->view->codOrgao = $codOrgao;
        $this->view->codGrupo = $codGrupo;
        //$this->view->idUsuarioLogado = $idusuario;

        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 114; // Coordenador de Editais

        parent::perfil(1, $PermissoesGrupo);
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $this->getIdUsuario["idAgente"];
            } else {
                $this->getIdUsuario = 0;
            }
        } else {
            $this->getIdUsuario = $auth->getIdentity()->IdUsuario;
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()

    public function indexAction()
    {
        $this->_redirect("distribuirprojetos/distribuir");
    }

    public function distribuirAction()
    {

        //$IdOrgao = $this->codOrgao;
        $IdOrgao = $this->codOrgaoSuperior;
        //$IdOrgao = 362; //Comentar essa linha!!!!!!!!!!!!!!

        //Filtos de pesquisa que ser?o utilizados
        $where = array(
            'pro.Situacao = ?' => $this->COD_SITUACAO_PROJETO,
            'pp.stTipoDemanda = ?' =>  $this->COD_STTIPODEMANDA_PREPROJETO,
            'edi.idOrgao = ?' => $IdOrgao
        );

        // Lista projetos no org?o
        $tblProjetos = new Projetos();
        $editais = $tblProjetos->listaEditais($where);
        $this->view->listaEdital = $editais;

        //Lista UFs
        $tblUF = new Uf();
        $UFs = $tblUF->buscar(array(), "Sigla ASC");
        $this->view->UFs = $UFs;

        //Lista Projetos distribuidos
        if (!empty($_POST['UF'])) {
            $where['UfProjeto = ?'] = $_POST['UF'];
            $this->view->UF = $_POST['UF'];
        }
        if (!empty($_POST['idEdital'])) {
            $where['edi.idEdital = ?'] = $_POST['idEdital'];
            $this->view->idEdital = $_POST['idEdital'];
        }
        $projetosDistribuidos = $tblProjetos->listaProjetosDistribuidos($where)->toArray();

        //Listar Avaliadores
        $tbltbDistribuicao = new tbDistribuicao();

        for ($i = 0; $i < count($projetosDistribuidos);$i++) {
            $where = array(
                'dis.idItemDistribuicao = ?' => $projetosDistribuidos[$i]['idPreProjeto'],
                'dis.tpDistribuicao = ?' => $this->TP_DISTRIBUICAO,
                'dis.stDistribuicao = ?' => $this->ST_DISTRIBUICAO_PENDENTE
            );
            $Distribuicao = $tbltbDistribuicao->listaDistribuicao($where);

            $projetosDistribuidos[$i]['Selecionados'] = count($Distribuicao);
        }

        $this->view->projetosDistribuidos  = $projetosDistribuidos ;
    }


    public function redistribuirAction()
    {

        //$IdOrgao = $this->codOrgao;
        $IdOrgao = $this->codOrgaoSuperior;
        //$IdOrgao = 363; //Comentar essa linha!!!!!!!!!!!!!!


        //Filtos de pesquisa que ser?o utilizados
        $where = array(
            'pro.Situacao = ?' => $this->COD_SITUACAO_PROJETO_ATUALIZA,
            'pp.stTipoDemanda = ?' =>  $this->COD_STTIPODEMANDA_PREPROJETO,
            'edi.idOrgao = ?' => $IdOrgao
        );

        // Lista projetos no org?o
        $tblProjetos = new Projetos();
        $editais = $tblProjetos->listaEditais($where);
        $this->view->listaEdital = $editais;

        //Lista UFs
        $tblUF = new Uf();
        $UFs = $tblUF->buscar(array(), "Sigla desc");
        $this->view->UFs = $UFs;

        //Lista Projetos distribuidos
        if (!empty($_POST['UF'])) {
            $where['UfProjeto = ?'] = $_POST['UF'];
            $this->view->UF = $_POST['UF'];
        }


        if (!empty($_POST['idEdital']) and !empty($_POST['idAvaliador'])) {
            $where['dis.idDestinatario = ?'] = $_POST['idAvaliador'];
            $where['pp.idEdital = ?']        = $_POST['idEdital'];
            $where['nom.TipoNome = ?']       = 18;
            $this->view->idEdital            = $_POST['idEdital'];
            $this->view->idAvaliador         = $_POST['idAvaliador'];
            $tblDistribuicao = new tbDistribuicao();
            $projetosDistribuidos = $tblDistribuicao->listaRedistribuicaoPreprojetos($where)->toArray();
            $this->view->projos = $projetosDistribuidos;
        } else {
            $this->view->projos = array();
        }
    }


    public function projetosparaavaliadoresAction()
    {
        $this->_helper->layout->disableLayout();

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();

        $idPreProjeto = '';
        $PreProjetos = '';
        if (!empty($_POST['idPreProjeto'])) {
            foreach ($_POST['idPreProjeto'] as $key => $value) {
                if ($idPreProjeto == '' or empty($idPreProjeto)) {
                    $idPreProjeto = $value;
                }
                $PreProjetos .= $value.",";
            }
        }

        if ($idPreProjeto != '') {
            $Avaliadores = $tblPreProjeto->listaAvaliadores(array('pp.idPreProjeto = ?' => $_POST['idPreProjeto'][0], 'ave.stAtivo = ?' => 'A'));
            $this->view->Avaliadores = $Avaliadores;
            $this->view->PreProjetos = substr($PreProjetos, 0, strlen($PreProjetos)-1);
        } else {
            echo "<div class=\"msgALERT\"><div>Selecione pelo menos um projeto</div></div>";
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }


    public function redistribuirprojetosparaavaliadoresAction()
    {
        $this->_helper->layout->disableLayout();

        $Avaliador = !empty($_POST['Avaliador']) ? $_POST['Avaliador'] : 0;

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();

        $idPreProjeto = '';
        $PreProjetos = '';
        if (!empty($_POST['idPreProjeto'])) {
            foreach ($_POST['idPreProjeto'] as $key => $value) {
                if ($idPreProjeto == '' or empty($idPreProjeto)) {
                    $idPreProjeto = $value;
                }
                $PreProjetos .= $value.",";
            }
        }

        if ($idPreProjeto != '' and $Avaliador != 0) {
            $where = array(
                 'pp.idPreProjeto = ?' => $_POST['idPreProjeto'][0],
                 'ave.stAtivo = ?' => 'A',
                 '(ave.idAvaliador <> '.$Avaliador.') ' => ''

             );
            $Avaliadores = $tblPreProjeto->listaAvaliadores($where);
            $this->view->antigoAvaliador = $Avaliador;
            $this->view->Avaliadores = $Avaliadores;
            $this->view->PreProjetos = substr($PreProjetos, 0, strlen($PreProjetos)-1);
        } else {
            echo "<div class=\"msgALERT\"><div>Selecione pelo menos um projeto</div></div>";
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }


    public function enviaparaavaliadoresAction()
    {
        $this->_helper->layout->disableLayout();
        $retorno = "";
        $PreProjetos = array();
        $acao = !empty($_GET['acao']) ? $_GET['acao'] : null;

        if (isset($_REQUEST['idPreProjeto'])) {
            $PreProjetos = $_REQUEST['idPreProjeto'];
        }

        if (count($PreProjetos) < 1) {
            $error = "Nenhum projeto selecionado";
        }
        $tbltbdistribuir = new tbDistribuicao();
        $tblProjetos     = new Projetos();

        if (count($PreProjetos) > 0) {
            $projetosenviados = "";
            foreach ($PreProjetos as $key => $value) {
                $dadosprojeto    = $tblProjetos->listaProjetosDistribuidos(array('idPreProjeto = ?' => $value))->current();
                $Totalvinculados = $tbltbdistribuir->listaDistribuicao(array('idItemDistribuicao = ?' => $value,'tpDistribuicao = ?' => $this->TP_DISTRIBUICAO,'stDistribuicao = ?' => $this->ST_DISTRIBUICAO_PENDENTE));

                if ($dadosprojeto->qtAvaliador == count($Totalvinculados)) {
                    if ($tblProjetos->alterarSituacao($dadosprojeto->idPronac, $dadosprojeto->AnoProjeto.$dadosprojeto->Sequencial, $this->COD_SITUACAO_PROJETO_ATUALIZA, "Projeto encaminhado para avaliadores")) {
                        if ($tbltbdistribuir->alterar(array('stDistribuicao' => $this->ST_DISTRIBUICAO_REALIZADA), 'idItemDistribuicao = '.$value)) {
                            $this->view->confirme = "O projeto foi enviado com sucesso!";
                            $projetosenviados .= $value.",";
                        } else {
                            $this->view->error = "N&atilde;o foi possivel atualizar o Status da distribui&ccedil;&atilde;o!";
                            //$retorno = "N&atilde;o foi possivel atualizar o Status da distribui&ccedil;&atilde;o!";
                        }
                    } else {
                        $this->view->error = "N&atilde;o foi possivel atualizar a situa&ccedil;&atilde;o do projeto!";
                        //$retorno = "N&atilde;o foi possivel atualizar a situa&ccedil;&atilde;o do projeto!";
                    }
                } else {
                    $this->view->alerta = "Apenas projetos com quantide m�xima de avaliadores podem ser enviados";
                    //$retorno = "Apenas projetos com quantide m�xima de avaliadores podem ser enviados";
                }
            }
            $projetosenviados = substr($projetosenviados, 0, strlen($projetosenviados)-1);
            $this->view->PreProjetos = $PreProjetos = explode(",", $projetosenviados);
        } else {
            $this->view->alerta = "Nenhum projeto selecionado";
            //$retorno = "Nenhum projeto selecionado";
        }
    }


    public function distribuirprojetoAction()
    {
        $this->_helper->layout->disableLayout();
        $novosvinculos = 0;
        $javinculados  = 0;
        $error = "";
        $PreProjetos = array();
        $acao = !empty($_GET['acao']) ? $_GET['acao'] : null;
        if ($_POST or $_GET) {
            $PreProjetos = explode(",", $_REQUEST['PreProjetos']);
            if ((empty($_POST['idAvaliador']) or $_POST['idAvaliador'] == 0) and $acao == 'add') {
                $this->view->alerta = "Informe um avaliador";
            }
        }
        if (count($PreProjetos) < 1) {
            $this->view->alerta = "Nenhum projeto selecionado";
        }
        $tbltbdistribuir = new tbDistribuicao();
        $tblProjetos     = new Projetos();

        if (!empty($_POST['idAvaliador']) and $_POST['idAvaliador'] != 0) {
            foreach ($PreProjetos as $key => $value) {
                $dadosprojeto    = $tblProjetos->listaProjetosDistribuidos(array('idPreProjeto = ?' => $value))->current();
                $Totalvinculados = $tbltbdistribuir->listaDistribuicao(array('idItemDistribuicao = ?' => $value,'tpDistribuicao = ?' => $this->TP_DISTRIBUICAO,'stDistribuicao = ?' => $this->ST_DISTRIBUICAO_PENDENTE));
                $vinculado       = $tbltbdistribuir->buscar(array('idItemDistribuicao = ?' => $value,'idDestinatario = ?' => $_POST['idAvaliador'],'tpDistribuicao = ?' => $this->TP_DISTRIBUICAO,'stDistribuicao = ?' => $this->ST_DISTRIBUICAO_PENDENTE));
                if (count($vinculado) < 1 and $dadosprojeto->qtAvaliador > count($Totalvinculados)) {
                    $dados = array(
                                    'tpDistribuicao' => $this->TP_DISTRIBUICAO,
                                    'idRemetente' => $this->idusuario,
                                    'idDestinatario' => $_POST['idAvaliador'],
                                    'dtEnvio' => date("Y-m-d H:i:s"),
                                    'idItemDistribuicao' => $value,
                                    'stDistribuicao' => $this->ST_DISTRIBUICAO_PENDENTE
                                  );

                    if ($tbltbdistribuir->inserir($dados)) {
                        $novosvinculos++;
                    }
                }
            }
        }

        $listaprojetos = $this->listaprojetos($PreProjetos);

        if ($listaprojetos) {
            $this->view->listaprojetos = $listaprojetos;
        } else {
            $this->view->listaprojetos = array();
            $this->view->alerta = "Nenhum projeto selecionado";
        }

        if ($novosvinculos > 0) {
            $this->view->confirme = "Avaliador vinculado a ".$novosvinculos." projeto(s)";
        } elseif (strlen($this->view->alerta) < 2 and  $acao == 'add') {
            $this->view->alerta = "Avaliador j� vinculado ou quantidade m�xima de avaliadores atingida";
        }

        $Removidos = !empty($_GET['Del']) ? $_GET['Del'] : 0;

        if ($Removidos == 1) {
            $this->view->alerta = '';
            $this->view->alerta = '';
            $this->view->confirme = 'Avaliador removido com sucesso!';
        }
    }

    public function redistribuirprojetoAction()
    {
        $this->_helper->layout->disableLayout();
        $novosvinculos = 0;
        $javinculados  = 0;
        $atualizados = 0;
        $error = 0;
        $PreProjetos = array();
        $acao = !empty($_GET['acao']) ? $_GET['acao'] : null;
        if ($_POST or $_GET) {
            $PreProjetos = explode(",", $_REQUEST['PreProjetos']);
            if ((empty($_POST['idAvaliador']) or $_POST['idAvaliador'] == 0) and $acao == 'add') {
                $this->view->alerta = "Informe um avaliador";
            }
            if ((empty($_POST['idAvaliadorAnt']) or $_POST['idAvaliadorAnt'] == 0) and $acao == 'add') {
                $this->view->alerta = "N�o foi possivel recuperar o antigo avaliador";
            }
        }
        if (count($PreProjetos) < 1) {
//        	parent::message("Nenhum projeto selecionado!", "/distribuirprojetos/redistribuir", "ALERT");
            $this->view->alerta = "Nenhum projeto selecionado";
        }
        $tbltbdistribuir = new tbDistribuicao();
        $tblProjetos     = new Projetos();

        if (!empty($_POST['idAvaliador']) and $_POST['idAvaliador'] != 0) {
            foreach ($PreProjetos as $key => $value) {
                $dadosprojeto    = $tblProjetos->listaProjetosDistribuidos(array('idPreProjeto = ?' => $value))->current();
                //$Totalvinculados = $tbltbdistribuir->listaDistribuicao(array('idItemDistribuicao = ?' => $value,'tpDistribuicao = ?' => $this->TP_DISTRIBUICAO,'stDistribuicao = ?' => $this->ST_DISTRIBUICAO_REALIZADA));

                $vinculado       = $tbltbdistribuir->buscar(array('idItemDistribuicao = ?' => $value,'idDestinatario = ?' => $_POST['idAvaliador'],'tpDistribuicao = ?' => $this->TP_DISTRIBUICAO,'stDistribuicao = ?' => $this->ST_DISTRIBUICAO_REALIZADA ));

                if (count($vinculado) > 0 and strlen($error) < 2) {
                    if (!$tblProjetos->alterarSituacao($dadosprojeto->idPronac, null, $this->COD_SITUACAO_PROJETO_ATUALIZA, 'Redistribuindo o projeto para avalia��o')) {
                        $error = "N�o foi possivel mudar a situa��o do projeto";
                    }

                    $tbAvaliacao = new tbAvaliacaoPreProjeto();
                    $Avaliacao = $tbAvaliacao->buscar(array('idPreProjeto = ?' => $value, 'idAvaliador = ?' => $_POST['idAvaliadorAnt']));
                    if (count($Avaliacao) > 0 and strlen($error) < 2) {
                        if (!$tbAvaliacao->alterar(array('idAvaliador' => $_POST['idAvaliador'], 'stAvaliacao' => 'false'), array('idPreProjeto = ?' => $value, 'idAvaliador = ?' => $_POST['idAvaliadorAnt']))) {
                            $error = "N�o foi possivel distribuir a avalia��o";
                        }
                    }

                    if (strlen($error) < 2) {
                        $dados = array(
                                            'idDestinatario' => $_POST['idAvaliador'],
                                          );
                        $where = array(
                                 'idDestinatario = ?' => $_POST['idAvaliadorAnt'],
                                 'idItemDistribuicao = ?' => $value
                             );

                        if ($tbltbdistribuir->alterar($dados, $where)) {
                            $novosvinculos++;
                        } else {
                            $error = "N�o foi possivel distribuir a avalia��o";
                        }
                    }
                }
            }
        }

        $listaprojetos = $this->listaprojetos($PreProjetos);
        if (strlen($error)) {
//        	parent::message($error, "/distribuirprojetos/redistribuir", "ALERT");
            $this->view->alerta = $error;
        }

        if ($listaprojetos) {
            $this->view->listaprojetos = $listaprojetos;
        } else {
            $this->view->listaprojetos = array();
//            parent::message("Nenhum projeto selecionado!", "/distribuirprojetos/redistribuir", "ALERT");
            $this->view->alerta = "Nenhum projeto selecionado";
        }

        if ($novosvinculos > 0) {
//        	parent::message("Projeto(s) enviado(s) com sucesso!", "/distribuirprojetos/redistribuir", "CONFIRM");
            $this->view->confirme = "Projeto(s) enviado(s) com sucesso!";
        } elseif (strlen($this->view->alerta) < 2 and  $acao == 'add') {
            $this->view->alerta = "Avaliador j� vinculado ou quantidade m�xima de avaliadores atingida";
        }
    }

    public function reavaliarprojetoAction()
    {
        $this->_helper->layout->disableLayout();
        $atualizados = 0;
        $error = 0;
        $novosvinculos = 0;
        $PreProjetos = array();

        $acao = !empty($_GET['acao']) ? $_GET['acao'] : null;
        if ($_POST or $_GET) {
            $PreProjetos = isset($_REQUEST['idPreProjeto']) ? $_REQUEST['idPreProjeto'] : array();
            if (empty($_POST['Avaliador']) or $_POST['Avaliador'] == 0) {
//            	parent::message("Informe um avaliador!", "/distribuirprojetos/redistribuir", "ALERT");
                $this->view->alerta = "Informe um avaliador";
            }
            if ((empty($_POST['Avaliador']) or $_POST['Avaliador'] == 0) and $acao == 'add') {
                $this->view->alerta = "N�o foi possivel recuperar o antigo avaliador";
            }
        }
        if (count($PreProjetos) < 1) {
//        	parent::message("Nenhum projeto selecionado!", "/distribuirprojetos/redistribuir", "ALERT");
            $this->view->alerta = "Nenhum projeto selecionado";
        }
        $tbltbdistribuir = new tbDistribuicao();
        $tblProjetos     = new Projetos();

        if (!empty($_POST['Avaliador']) and $_POST['Avaliador'] != 0 and count($PreProjetos) > 0) {
            foreach ($PreProjetos as $key => $value) {
                $dadosprojeto    = $tblProjetos->listaProjetosDistribuidos(array('idPreProjeto = ?' => $value))->current();

                if (strlen($error) < 2) {
                    if (!$tblProjetos->alterarSituacao($dadosprojeto->idPronac, null, $this->COD_SITUACAO_PROJETO_ATUALIZA, 'Redistribuindo o projeto para avalia��o')) {
                        $error = "N�o foi possivel mudar a situa��o do projeto";
                    }

                    $tbAvaliacao = new tbAvaliacaoPreProjeto();
                    $Avaliacao = $tbAvaliacao->buscar(array('idPreProjeto = ?' => $value, 'idAvaliador = ?' => $_POST['Avaliador']));
                    if (count($Avaliacao) > 0 and strlen($error) < 2) {
                        if (!$tbAvaliacao->alterar(array('stAvaliacao' => 'false'), array('idPreProjeto = ?' => $value, 'idAvaliador = ?' => $_POST['Avaliador']))) {
                            $error = "N�o foi possivel distribuir a avalia��o";
                        }
                    }

                    if (strlen($error) < 2) {
                        $dados = array(
                                            'idDestinatario' => $_POST['Avaliador'],
                                          );
                        $where = array(
                                 'idDestinatario = ?' => $_POST['Avaliador'],
                                 'idItemDistribuicao = ?' => $value
                             );

                        if ($tbltbdistribuir->alterar($dados, $where)) {
                            $novosvinculos++;
                        } else {
                            $error = "N�o foi possivel distribuir a avalia��o";
                        }
                    }
                }
            }
        }

        $listaprojetos = $this->listaprojetos($PreProjetos);
        if (strlen($error)) {
            $this->view->alerta = $error;
        }

        if ($listaprojetos) {
            $this->view->listaprojetos = $listaprojetos;
        } else {
            $this->view->listaprojetos = array();
//            parent::message("Nenhum projeto selecionado!", "/distribuirprojetos/redistribuir", "ALERT");
            $this->view->alerta = "Nenhum projeto selecionado";
        }

        if ($novosvinculos > 0) {
//        	parent::message("Projeto(s) enviado(s) com sucesso!", "/distribuirprojetos/redistribuir", "CONFIRM");
            $this->view->confirme = "Projeto(s) enviado(s) com sucesso!";
        } elseif (strlen($this->view->alerta) < 2 and  $acao == 'add') {
            $this->view->alerta = "Avaliador j� vinculado ou quantidade m�xima de avaliadores atingida";
        }
    }

    public function addobsAction()
    {
        $this->_helper->layout->disableLayout();

        $idDistribuicao = !empty($_REQUEST['idDistribuicao']) ? (int)$_REQUEST['idDistribuicao'] : null;
        $dsObservacao   = !empty($_POST['dsObservacao']) ? $_POST['dsObservacao'] : null;
        if (!empty($idDistribuicao) and $idDistribuicao > 0) {
            $tbltbdistribuir = new tbDistribuicao();
            $where = "idDistribuicao = ".$idDistribuicao;
            $dados = array('dsObservacao' => $dsObservacao);

            if (strlen($dsObservacao) > 2) {
                if ($tbltbdistribuir->alterar($dados, $where)) {
//                    	parent::message("Cadastro realizado com sucesso!", "/distribuirprojetos/redistribuir", "CONFIRM");
                    $this->view->confirme = "Cadastro realizado com sucesso!";
                } else {
//                    	parent::message("Falha ao salvar dados!", "/distribuirprojetos/redistribuir", "ALERT");
                    $this->view->error = "Falha ao salvar dados";
                }
            }
            $dadosDistribuicao = array();
            $dadosDistribuicao = $tbltbdistribuir->buscar(array('idDistribuicao = ?' => $idDistribuicao))->current();

            if (count($dadosDistribuicao) < 1) {
                $this->view->dadosDistribuicao = array();
            } else {
                $this->view->dadosDistribuicao = $dadosDistribuicao;
            }
        } else {
            $this->view->alerta = "Informe um item de distribui��o";
        }
    }

    public function removedistribuicaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $idDistribuicao = !empty($_REQUEST['idDistribuicao']) ? (int)$_REQUEST['idDistribuicao'] : null;
        if (!empty($idDistribuicao) and $idDistribuicao > 0) {
            $where = "idDistribuicao = ".$idDistribuicao;
            $tbltbdistribuir = new tbDistribuicao();

            $tbltbdistribuir->delete($where);
        }

        $this->_redirect('distribuirprojetos/distribuirprojeto?PreProjetos='.$_POST['PreProjetos'].'&Del=1');
        return;
    }

    public function listaavaliadoresAction()
    {
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $Avaliadores = $tblPreProjeto->listaApenasAvaliadores(array('pp.idEdital = ?' => $_GET['idEdital']));
        $idAvaliador = !empty($_GET['idAvaliador']) ? $_GET['idAvaliador'] : 0;
        foreach ($Avaliadores as $A) {
            echo utf8_encode("<option value='".$A->idAvaliador."'");
            if ($idAvaliador == $A->idAvaliador) {
                echo utf8_encode(" selected ");
            };
            echo utf8_encode(" > ". $A->Descricao."</option>");
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function listaprojetos($idPreProjetos=array())
    {
        $filtrolistagem  = "( 1!=1 ";
        $tbltbdistribuir = new tbDistribuicao();
        $tblProjetos     = new Projetos();

        if (count($idPreProjetos) > 0) {
            foreach ($idPreProjetos as $key => $value) {
                $filtrolistagem .= 'or idPreProjeto='.$value;
            }
            $filtrolistagem .= ")";
            $listaprojetos = $tblProjetos->listaProjetosDistribuidos(array($filtrolistagem => ''))->toArray();

            for ($i = 0; $i < count($listaprojetos);$i++) {
                $where = array(
                    'dis.idItemDistribuicao = ?' => $listaprojetos[$i]['idPreProjeto'],
                    'dis.tpDistribuicao = ?' => $this->TP_DISTRIBUICAO,
                    'dis.stDistribuicao = ?' => $this->ST_DISTRIBUICAO_PENDENTE
                );
                $Distribuicao = $tbltbdistribuir->listaDistribuicao($where)->toArray();

                $listaprojetos[$i]['Avaliadores'] = $Distribuicao;
            }

            return $listaprojetos;
        } else {
            return false;
        }
    }
}
