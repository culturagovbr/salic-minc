<?php


class PesquisarprojetofiscalizacaoController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 10;
    private $codOrgao = null;
    private $grupoAtivo = null;
    private $codUsuario = 0;

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p�gina
        $auth = Zend_Auth::getInstance(); // pega a autentica�?o
        $Usuario = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss?es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 134; //Coordenador de Fiscaliza��o
            $PermissoesGrupo[] = 135; //T�cnico de Fiscaliza��o
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss?es
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org?os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis?o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis?o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis?o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis?o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg?o ativo do usu�rio para a vis?o
            $this->codOrgao = $GrupoAtivo->codOrgao; // manda o �rg?o ativo do usu�rio para a vis?o
            $this->codUsuario = $auth->getIdentity()->usu_codigo; // manda o codigo do usuario ativo
            $this->grupoAtivo = $GrupoAtivo->codGrupo; // manda o �rg?o ativo do usu�rio para a vis?o
        } // fecha if
        else { // caso o usu�rio n?o esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        //recupera ID do pre projeto (proposta)

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    // fecha m�todo init()*/

    public function indexAction()
    {
    }

    public function gridAction()
    {

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
            $order = array('NomeProjeto');
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
        if ((isset($_POST['pronac']) && !empty($_POST['pronac'])) || (isset($_GET['pronac']) && !empty($_GET['pronac']))) {
            $where['pr.AnoProjeto+pr.Sequencial = ?'] = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
            $this->view->pronacProjeto = isset($_POST['pronac']) ? $_POST['pronac'] : $_GET['pronac'];
        }

        if ($this->grupoAtivo == 134 && (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro']))) {
            //Coordenador
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    $where['b.stFiscalizacaoProjeto in (?)'] = array('0','1');
                    break;
                case 'analisados':
                    $where['b.stFiscalizacaoProjeto = ?'] = '2';
                    $this->view->nmPagina = 'Fiscaliza��o conclu�da pelo t�cnico';
                    break;
                case 'concluidos':
                    $where['b.stFiscalizacaoProjeto = ?'] = '3';
                    $this->view->nmPagina = 'Fiscaliza��o conclu�da pelo coordenador';
                    break;
            }
        } else {
            //T�cnico
            $where['b.stFiscalizacaoProjeto in (?)'] = array('0','1');
            //$where['b.stFiscalizacaoProjeto in (?)'] = array('1');
            //$where['b.idUsuarioInterno = ?'] = $this->codUsuario;
        }

        if ((isset($_POST['tecFiltro']) || isset($_GET['tecFiltro'])) && (!empty($_POST['tecFiltro']) || !empty($_GET['tecFiltro']))) {
            $tecnico = isset($_POST['tecFiltro']) ? $_POST['tecFiltro'] : $_GET['tecFiltro'];
            $this->view->tecnico = $tecnico;
            $where['b.idUsuarioInterno = ?'] = $tecnico;
        }

        $projetos = new Projetos();
        $total = $projetos->painelFiscalizacaoProjetos($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $projetos->painelFiscalizacaoProjetos($where, $order, $tamanho, $inicio);

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
        $this->view->qntdProjetos  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;

//        $pa = new paUsuariosDoPerfil();
        $vw = new vwUsuariosOrgaosGrupos();
        $usuarios = $vw->buscarUsuarios(134, $this->codOrgao);
        $this->view->Usuarios = $usuarios;
    }

    public function painelcontroleentidadeAction()
    {
        $auth = Zend_Auth::getInstance();

        $aprovacaoDao = new Aprovacao();
        $selectAp = $aprovacaoDao->totalAprovadoProjeto(true);
        /*$abrangenciaDao = new Abrangencia();
        $selectAb = $abrangenciaDao->abrangenciaProjeto(true);*/
        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $selectAb = $tblAgentes->buscarUfMunicioAgente(array(), null, null, null, true);
        $projetosDao = new Projetos();

        $where = array('ofisc.idOrgao = ?' => $this->view->orgaoAtivo, "tbFiscalizacao.stFiscalizacaoProjeto in (?,'1')" => '0', 'dtConfirmacaoFiscalizacao is ?' => new Zend_Db_Expr('null'));
        $resp = $projetosDao->projetosFiscalizacao($selectAb, $selectAp, $where, true);
        $this->view->projetosFiscalizacao = array(
            array('nome' => 'Projetos', 'qtd' => 0, 'projetos' => array())
        );
        foreach ($resp as $key => $val) {
            $num = 0;

            $this->view->projetosFiscalizacao[$num]['qtd']++;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&nbsp;'] = $this->view->projetosFiscalizacao[$num]['qtd'];
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['PRONAC'] = "<a target='_blank' href='" . $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')) . "?idPronac=" . $val->IdPRONAC . "' >" . $val->AnoProjeto . $val->Sequencial . "</a>";
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Nome do Projeto'] = $val->NomeProjeto;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Regi&atilde;o'] = $val->Regiao;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['UF'] = $val->uf;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Cidade'] = $val->cidade;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&Aacute;rea'] = $val->dsArea;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Segmento'] = $val->dsSegmento;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Valor'] = number_format($val->somatorio, 2, ',', '.');
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Mecanismo'] = $val->dsMecanismo;
            if ($val->stPlanoAnual == 0) {
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'N&atilde;o';
            } else {
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'Sim';
            }

            //$this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Qtde Dias']               = strtotime();

            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Data Inicio'] = date('d/m/Y', strtotime($val->dtInicioFiscalizacaoProjeto));
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Data Fim'] = date('d/m/Y', strtotime($val->dtFimFiscalizacaoProjeto));
            //$this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Situa&ccedil;&atilde;o']  = $val->;
            $consultarDadosHref = $this->url(array('controller' => 'Pesquisarprojetofiscalizacao', 'action' => 'consultadadosfiscalizacao'));
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Dados da Fiscaliza&ccedil;&atilde;o'] = '  <center><a href="' . $consultarDadosHref . '" class="dadosFiscalizacao"  idPronac="' . $val->IdPRONAC . '" idFiscalizacao="' . $val->idFiscalizacao . '">
                                                                                                                               <img src="../public/img/table_multiple.png" alt="Dados da Fiscaliza�?o"/>
                                                                                                                            </a></center>';
            $fiscalizarHref = $this->url(array('controller' => 'Pesquisarprojetofiscalizacao', 'action' => 'aceitacaofiscalizacao'));
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Aceitar Fiscaliza&ccedil;&atilde;o'] = '  <center><a href="' . $fiscalizarHref . '" class="fiscalizacao" idPronac="' . $val->IdPRONAC . '" idFiscalizacao="' . $val->idFiscalizacao . '">
                                                                                                       <img src="../public/img/btn_busca.gif" alt="Aceitar Fiscaliza&ccedil;&atilde;o" />
                                                                                                    </a></center>';
        }
    }

    public function aceitacaofiscalizacaoAction()
    {
        $post = Zend_Registry::get('post');
        $this->view->idPronac = $post->idPronac;
        $this->view->idFiscalizacao = $post->idFiscalizacao;

        $orgaoDao = new Orgaos();
        $this->view->orgaos = $orgaoDao->buscar(array('Vinculo = ?' => 1, 'Status = ?' => 0), array('Sigla'));

        $projetoDao = new Projetos();
        $this->view->infoProjeto = $projetoDao->projetosFiscalizacaoEntidade(array('Projetos.IdPRONAC = ?' => $this->view->idPronac, 'tbFiscalizacao.idFiscalizacao = ?' => $this->view->idFiscalizacao));



        $OrgaoFiscalizadorDao = new OrgaoFiscalizador();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $OrgaoFiscalizadorDao->update(array('dtRecebimentoResposta' => new Zend_Db_Expr('GETDATE()')), array('dtRecebimentoResposta is ?' => new Zend_Db_Expr('null'), 'idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao, 'idOrgao = ?' => $this->view->orgaoAtivo));
        }
        $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao));
        }
    }

    public function consultadadosfiscalizacaoAction()
    {
        $get = Zend_Registry::get('get');

        if ($get->email) {
            $this->view->email = true;
        } else {
            $this->view->email = false;
        }
        $this->view->idPronac = $get->idPronac;
        $this->view->idFiscalizacao = $get->idFiscalizacao;

        $orgaoDao = new Orgaos();
        $this->view->orgaos = $orgaoDao->buscar(array('Vinculo = ?' => 1, 'Status = ?' => 0), array('Sigla'));

        $projetoDao = new Projetos();
        $this->view->infoProjeto = $projetoDao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $this->view->idPronac, 'tbFiscalizacao.idFiscalizacao = ?' => $this->view->idFiscalizacao));

        $OrgaoFiscalizadorDao = new OrgaoFiscalizador();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $this->view->dadosOrgaos = $OrgaoFiscalizadorDao->dadosOrgaos(array('tbOF.idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao));
        }
        $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao));
        }
        $RelatorioFiscalizacaoDAO = new RelatorioFiscalizacao();
        $this->view->relatorioFiscalizacao = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($this->view->idFiscalizacao);
    }

    public function imprimirAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $get = Zend_Registry::get('get');

        if ($get->email) {
            $this->view->email = true;
        } else {
            $this->view->email = false;
        }
        $this->view->idPronac = $get->idPronac;
        $this->view->idFiscalizacao = $get->idFiscalizacao;

        $orgaoDao = new Orgaos();
        $this->view->orgaos = $orgaoDao->buscar(array('Vinculo = ?' => 1, 'Status = ?' => 0), array('Sigla'));

        $projetoDao = new Projetos();
        $this->view->infoProjeto = $projetoDao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $this->view->idPronac, 'tbFiscalizacao.idFiscalizacao = ?' => $this->view->idFiscalizacao));

        $OrgaoFiscalizadorDao = new OrgaoFiscalizador();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $this->view->dadosOrgaos = $OrgaoFiscalizadorDao->dadosOrgaos(array('tbOF.idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao));
        }
        $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao));
        }
        $RelatorioFiscalizacaoDAO = new RelatorioFiscalizacao();
        $this->view->relatorioFiscalizacao = $RelatorioFiscalizacaoDAO->buscaRelatorioFiscalizacao($this->view->idFiscalizacao);
    }

    public function painelcontrolecoordenadorAction()
    {
        $aprovacaoDao = new Aprovacao();
        $selectAp = $aprovacaoDao->totalAprovadoProjeto(true);

        //$abrangenciaDao = new Abrangencia();
        //$selectAb = $abrangenciaDao->abrangenciaProjeto(true);

        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $selectAb = $tblAgentes->buscarUfMunicioAgente(array(), null, null, null, true);

        $projetosDao = new Projetos();


        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao;

        $resp = $projetosDao->projetosFiscalizacao($selectAb, $selectAp, array('Projetos.Orgao =?' => $codOrgao));

        $this->view->projetosFiscalizacao = array(
            array('nome' => 'FISCALIZA&Ccedil;&Atilde;O EM ANDAMENTO', 'qtd' => 0, 'projetos' => array()),
            array('nome' => 'FISCALIZA&Ccedil;&Atilde;O EM ATRASO', 'qtd' => 0, 'projetos' => array()),
            array('nome' => 'FISCALIZA&Ccedil;&Atilde;O CONCLU&Iacute;DA POR PARECER', 'qtd' => 0, 'projetos' => array())
        );
        foreach ($resp as $key => $val) {
            if (($val->stFiscalizacaoProjeto == 0 and date('Y-m-d', strtotime($val['dtFimFiscalizacaoProjeto'])) >= date('Y-m-d'))) {
                $num = 0;
            } elseif ($val->stFiscalizacaoProjeto == 3) {
                $num = 3;
            } elseif (date('Y-m-d', strtotime($val['dtFimFiscalizacaoProjeto'])) < date('Y-m-d') || $val->stFiscalizacaoProjeto == 2) {
                $num = 2;
            }
            if (!isset($this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC])) {
                $this->view->projetosFiscalizacao[$num]['qtd']++;
            }
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&nbsp;'] = $this->view->projetosFiscalizacao[$num]['qtd'];
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['PRONAC'] = "<a target='_blank' href='" . $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')) . "?idPronac=" . $val->IdPRONAC . "' >" . $val->AnoProjeto . $val->Sequencial . "</a>";
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Nome do Projeto'] = $val->NomeProjeto;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Regi&atilde;o'] = $val->Regiao;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['UF'] = $val->uf;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Cidade'] = $val->cidade;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&Aacute;rea'] = $val->dsArea;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Segmento'] = $val->dsSegmento;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Valor'] = number_format($val->somatorio, 2, ',', '.');
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Mecanismo'] = $val->dsMecanismo;
            if ($val->stPlanoAnual == 0) {
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'N&atilde;o';
            } else {
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'Sim';
            }

            //$this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Qtde Dias']               = strtotime();

            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Data Inicio'] = date('d/m/Y', strtotime($val->dtInicioFiscalizacaoProjeto));
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Data Fim'] = date('d/m/Y', strtotime($val->dtFimFiscalizacaoProjeto));
            //$this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Situa&ccedil;&atilde;o']  = $val->;
            $consultarDadosHref = $this->url(array('controller' => 'Pesquisarprojetofiscalizacao', 'action' => 'consultadadosfiscalizacao', 'num' => $num));
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Dados da Fiscaliza&ccedil;&atilde;o'] = '  <center><a href="' . $consultarDadosHref . '" class="dadosFiscalizacao"  idPronac="' . $val->IdPRONAC . '" idFiscalizacao="' . $val->idFiscalizacao . '">
                                                                                                                               <img src="../public/img/table_multiple.png" alt="Dados da Fiscaliza�&atilde;o"/>
                                                                                                                            </a></center>';
            $emailHref = $this->url(array('controller' => 'Pesquisarprojetofiscalizacao', 'action' => 'visualizaremail'));
            $fiscalizarHref = $this->url(array('controller' => 'fiscalizarprojetocultural', 'action' => 'parecerdocoordenador')) . '?idProjeto=' . $val->idProjeto . '&idFiscalizacao=' . $val->idFiscalizacao;
            if (($val->stFiscalizacaoProjeto < 2 and date('Y-m-d', strtotime($val['dtFimFiscalizacaoProjeto'])) >= date('Y-m-d'))) {
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Comunicar Proponente da Fiscaliza&ccedil;&atilde;o'] = '  <center><a href="' . $emailHref . '" class="dadosFiscalizacao"  idPronac="' . $val->IdPRONAC . '" idFiscalizacao="' . $val->idFiscalizacao . '">
                                                                                                                               <img src="../public/img/table_multiple.png" alt="Dados da Fiscaliza�&atilde;o"/>
                                                                                                                            </a></center>';
                if ($val->stAvaliacao == 1) {
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '   <center><a href="' . $fiscalizarHref . '">
                                                                                                               <img src="../public/img/btn_busca.gif" alt="Fiscalizar"/>
                                                                                                            </a></center>';
                } else {
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '';
                }
            } elseif ($val->stFiscalizacaoProjeto != 3 and (date('Y-m-d', strtotime($val['dtFimFiscalizacaoProjeto'])) < date('Y-m-d') || $val->stFiscalizacaoProjeto == 2)) {
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Comunicar Proponente da Fiscaliza&ccedil;&atilde;o'] = '  <center><a href="' . $emailHref . '" class="dadosFiscalizacao"  idPronac="' . $val->IdPRONAC . '" idFiscalizacao="' . $val->idFiscalizacao . '">
                                                                                                                               <img src="../public/img/table_multiple.png" alt="Dados da Fiscaliza�&atilde;o"/>
                                                                                                                            </a></center>';
                if ($val->stAvaliacao == 1) {
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '   <center><a href="' . $fiscalizarHref . '">
                                                                                                               <img src="../public/img/btn_busca.gif" alt="Fiscalizar"/>
                                                                                                            </a><center>';
                } else {
                    $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '';
                }
            }
        }
    }

    public function parametropesquisaAction()
    {
        $ufDao = new Uf();
        $this->view->regiao = $ufDao->buscarRegiao();
        $mecanismoDao = new Mecanismo();
        $this->view->mecanismo = $mecanismoDao->buscar(array('Status = ?' => 1));
        $situacaoDao = new Situacao();
        $this->view->situacaoprojeto = $situacaoDao->buscar(array("StatusProjeto = ?" => 1), array('Codigo'));
        $areaDao = new Area();
        $this->view->area = $areaDao->buscar();
        $segmentoDao = new Segmento();
        $this->view->Segmento = $segmentoDao->buscar(array('stEstado = ?' => 1));
    }

    public function resultadopesquisaAction()
    {

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
            $order = array('AnoProjeto', 'Sequencial');
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        $this->view->filtros = $get;
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        if ($get->pronac != '') {
            $where['(p.AnoProjeto+p.Sequencial) = ?'] = $get->pronac;
        } else {
            if ($get->regiaoFake != '' || $get->regiao != '') {
                $where['u.Regiao = ?'] = isset($get->regiaoFake) ? $get->regiaoFake : $get->regiao;
            } else {
                $where['u.Regiao ?'] = new Zend_Db_Expr('IS NOT NULL');
            }
        }

        if ($get->uf != '') {
            $where['u.idUF = ?'] = $get->uf;
        }

        if ($get->cidade != '') {
            $where['mu.idMunicipioIBGE = ?'] = $get->cidade;
        }

        if ($get->mecanismo != '') {
            $where['p.Mecanismo = ?'] = $get->mecanismo;
        }

        if ($get->planoAnual == 'sim') {
            $where['pr.stPlanoAnual = ?'] = 1;
        }

        if ($get->situacaoprojeto != '') {
            $where['p.Situacao = ?'] = $get->situacaoprojeto;
        }

        if ($get->area != '') {
            $where['p.Area = ?'] = $get->area;
        }

        if ($get->Segmento != '') {
            $where['p.Segmento = ?'] = $get->Segmento;
        }

        if ($get->valorMenor != '') {
            $where['(sac.dbo.fnTotalAprovadoProjeto(p.AnoProjeto, p.Sequencial)) >= ?'] = $get->valorMenor;
        }

        if ($get->valorMaior != '') {
            $where['(sac.dbo.fnTotalAprovadoProjeto(p.AnoProjeto, p.Sequencial)) <= ?'] = $get->valorMaior;
        }

        $where["p.IdPRONAC NOT IN (SELECT IdPRONAC FROM SAC.dbo.tbFiscalizacao where stFiscalizacaoProjeto IN ('0','1') )"] = '';
        //$where["e.Status = ?"] = 1;

        $projetos = new Projetos();
        $total = $projetos->gridFiscalizacaoProjetoFiltro($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $projetos->gridFiscalizacaoProjetoFiltro($where, $order, $tamanho, $inicio);

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
        $this->view->qtdDocumentos = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;



        //Apagar daqui pra frente

        /*$post = Zend_Registry::get('post');
        $projetosDao = new Projetos();

        if ($post->PRONAC != '')
            $where['(p.AnoProjeto+p.Sequencial) = ?'] = $post->PRONAC;

        if ($post->regiaoFake != '' || $post->regiao != ''){
            $where['u.Regiao = ?'] = isset($post->regiaoFake) ? $post->regiaoFake : $post->regiao;
        } else {
            $where['u.Regiao ?'] = new Zend_Db_Expr('IS NOT NULL');
        }

        if ($post->uf != '')
            $where['u.idUF = ?'] = $post->uf;

        if ($post->cidade != '')
            $where['mu.idMunicipioIBGE = ?'] = $post->cidade;

        if ($post->mecanismo != '')
            $where['p.Mecanismo = ?'] = $post->mecanismo;

        if ($post->planoAnual == 'sim')
            $where['pr.stPlanoAnual = ?'] = 1;

        if ($post->situacaoprojeto != '')
            $where['p.Situacao = ?'] = $post->situacaoprojeto;

        if ($post->area != '')
            $where['p.Area = ?'] = $post->area;

        if ($post->Segmento != '')
            $where['p.Segmento = ?'] = $post->Segmento;

        if ($post->valorMenor != '')
            $where['(sac.dbo.fnTotalAprovadoProjeto(p.AnoProjeto, p.Sequencial)) >= ?'] = $post->valorMenor;

        if ($post->valorMaior != '')
            $where['(sac.dbo.fnTotalAprovadoProjeto(p.AnoProjeto, p.Sequencial)) <= ?'] = $post->valorMaior;

//        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
//        $codOrgao = $GrupoAtivo->codOrgao;
//        $where['p.Orgao =?'] = $codOrgao;
//        $where['sac.dbo.fnPercentualCaptado(p.AnoProjeto, p.Sequencial) >= ?'] = 20;
        $where["p.IdPRONAC NOT IN (SELECT IdPRONAC FROM SAC.dbo.tbFiscalizacao where stFiscalizacaoProjeto IN ('0','1') )"] = '';
        $resp = $projetosDao->projetosFiscalizacaoPesquisar($where);

        $this->view->projetosFiscalizacao = array(
            array('nome' => 'PROJETOS', 'qtd' => 0, 'projetos' => array())
        );

        foreach ($resp as $key => $val) {
            $num = 0;

            if (!isset($this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC])) {
                $this->view->projetosFiscalizacao[$num]['qtd']++;
            }
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&nbsp;']           = $this->view->projetosFiscalizacao[$num]['qtd'];
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['PRONAC']           = "<a target='_blank' href='" . $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')) . "?idPronac=" . $val->IdPRONAC . "' >" . $val->AnoProjeto . $val->Sequencial . "</a>";
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Nome do Projeto']  = $val->NomeProjeto;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Regi&atilde;o']    = $val->Regiao;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['UF']               = $val->uf;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Cidade']           = $val->cidade;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['&Aacute;rea']      = $val->dsArea;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Segmento']         = $val->dsSegmento;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Valor']            = number_format($val->somatorio, 2, ',', '.');
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Mecanismo']        = $val->dsMecanismo;
            if ($val->stPlanoAnual == 0)
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'N&atilde;o';
            else
                $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Plano Anual'] = 'Sim';
            $oficializarfiscalizacaoHref = $this->url(array('controller' => 'pesquisarprojetofiscalizacao', 'action' => 'oficializarfiscalizacao')).'?idPronac='.$val->IdPRONAC;
            $this->view->projetosFiscalizacao[$num]['projetos'][$val->IdPRONAC]['Fiscalizar'] = '<center><a href="' . $oficializarfiscalizacaoHref . '"><img src="../public/img/btn_busca.gif" alt="Dados da Fiscaliza��o"/></a></center>';
        }*/
    }

    public function oficializarfiscalizacaoAction()
    {
        $post = Zend_Registry::get('get');
        $this->view->idPronac = $post->idPronac;

        $orgaoDao = new Orgaos();
        $orgao = $orgaoDao->buscar(array('Codigo = ?' => $this->view->orgaoAtivo));
        $this->view->nomeOrgao = $orgao[0]->Sigla;
        $this->view->orgaos = $orgaoDao->buscar(array('Vinculo = ?' => 1, 'Status = ?' => 0), array('Sigla'));

        $projetoDao = new Projetos();
        $this->view->infoProjeto = $projetoDao->projetosFiscalizacaoPesquisar(array('p.IdPRONAC = ?' => $this->view->idPronac));
        if (count($this->view->infoProjeto)==0) {
            parent::message("Projeto n�o encontrado!", "pesquisarprojetofiscalizacao/parametropesquisa", "ALERT");
        }

        $OrgaoFiscalizadorDao = new OrgaoFiscalizador();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $this->view->orgaoFisca = $OrgaoFiscalizadorDao->buscarOrgao(array('idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao));
        }

        $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();
        if ($this->view->infoProjeto[0]->idFiscalizacao) {
            $this->view->arquivos = $ArquivoFiscalizacaoDao->buscarArquivo(array('arqfis.idFiscalizacao = ?' => $this->view->infoProjeto[0]->idFiscalizacao));
        }

//        $pa = new paUsuariosDoPerfil();
        $vw = new vwUsuariosOrgaosGrupos();
        $usuarios = $vw->buscarUsuarios(134, $this->view->orgaoAtivo);
        $this->view->Usuarios = $usuarios;
    }

    public function cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho)
    {
        // pega as informa�?es do arquivo
        /* $arquivoNome     = $_FILES['arquivo']['name']; // nome
          $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
          $arquivoTipo     = $_FILES['arquivo']['type']; // tipo
          $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho */

        if (!empty($arquivoNome) && !empty($arquivoTemp)) {
            $arquivoExtensao = Upload::getExtensao($arquivoNome); // extens?o
            $arquivoBinario = Upload::setBinario($arquivoTemp); // bin�rio
            $arquivoHash = Upload::setHash($arquivoTemp); // hash
            // cadastra dados do arquivo
            //if($arquivoExtensao != 'doc' and $arquivoExtensao != 'docx' and $arquivoExtensao != ''){
            // cadastra dados do arquivo

            $dadosArquivo = array(
                'nmArquivo' => $arquivoNome,
                'sgExtensao' => $arquivoExtensao,
                'dsTipoPadronizado' => $arquivoTipo,
                'nrTamanho' => $arquivoTamanho,
                'dtEnvio' => new Zend_Db_Expr('GETDATE()'),
                'dsHash' => $arquivoHash,
                'stAtivo' => 'A');
            $cadastrarArquivo = ArquivoDAO::cadastrar($dadosArquivo);

            // pega o id do �ltimo arquivo cadastrado
            $idUltimoArquivo = ArquivoDAO::buscarIdArquivo();
            $idUltimoArquivo = (int) $idUltimoArquivo[0]->id;

            // cadastra o bin�rio do arquivo
            $dadosBinario = array(
                'idArquivo' => $idUltimoArquivo,
                'biArquivo' => $arquivoBinario);
            $cadastrarBinario = ArquivoImagemDAO::cadastrar($dadosBinario);

            return $idUltimoArquivo;
        } else {
        }
        /*
          else{
          $this->view->erroextensao = true;
          } */
    }

    public function cadastraraceitacaofiscalizacaoAction()
    {
        $post = Zend_Registry::get('post');


        $OrgaoFiscalizadorDao = new OrgaoFiscalizador();
        $OrgaoFiscalizadorDao->update(array('dtConfirmacaoFiscalizacao' => new Zend_Db_Expr('GETDATE()'), 'dsObservacao' => $post->dsObservacao, 'idParecerista' => $post->idAgente), array('idFiscalizacao = ?' => $post->idFiscalizacao, 'idOrgao = ?' => $this->view->orgaoAtivo));
    }

    public function cadastrarfiscalizacaoAction()
    {
        $post = Zend_Registry::get('post');
        $this->view->idPronac = $post->idPronac;

        foreach ($_FILES['arquivo']['name'] as $key => $val) {
            $arquivoNome = $_FILES['arquivo']['name'][$key];
            $arquivoTemp = $_FILES['arquivo']['tmp_name'][$key];
            $arquivoTipo = $_FILES['arquivo']['type'][$key];
            $arquivoTamanho = $_FILES['arquivo']['size'][$key];
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $idArquivo[$key] = $this->cadastraranexo($arquivoNome, $arquivoTemp, $arquivoTipo, $arquivoTamanho);
            }
        }

        $dados = array();
        $fiscalizacaoDao = new Fiscalizacao();

        $auth = Zend_Auth::getInstance();
        $tpDemandante = 0;
        if ($auth->getIdentity()->usu_orgao == Orgaos::ORGAO_SUPERIOR_SAV) {
            $tpDemandante = 1;
        } //'SAV';
        if ($post->oficializar) {
            $dados['stFiscalizacaoProjeto'] = 1;
            $this->view->tela = 'grid';
        } else {
            $dados['stFiscalizacaoProjeto'] = 0;
            $this->view->tela = 'oficializarfiscalizacao';
        }

        if ($post->dtInicio != '') {
            $dados['dtInicioFiscalizacaoProjeto'] = data::dataAmericana($post->dtInicio);
        }
        if ($post->dtFim != '') {
            $dados['dtFimFiscalizacaoProjeto'] = data::dataAmericana($post->dtFim);
        }
        if ($post->dtResposta != '') {
            $dados['dtRespostaSolicitada'] = data::dataAmericana($post->dtResposta);
        }

        $dados['dsFiscalizacaoProjeto'] = $post->dsFiscalizacaoProjeto;
        $dados['tpDemandante'] = $tpDemandante;
        $dados['idSolicitante'] = $auth->getIdentity()->usu_codigo;
        $dados['idUsuarioInterno'] = $post->idUsuario;

        if ($post->idFiscalizacao) {
            $idFiscalizacao = $post->idFiscalizacao;
            $fiscalizacaoDao->alterar($dados, array('idFiscalizacao = ?' => $idFiscalizacao, 'IdPRONAC = ?' => $this->view->idPronac));
        } else {
            $dados['IdPRONAC'] = $this->view->idPronac;
            $idFiscalizacao = $fiscalizacaoDao->inserir($dados);
        }
        $ArquivoFiscalizacaoDao = new ArquivoFiscalizacao();
        foreach ($idArquivo as $idArq) {
            $ArquivoFiscalizacaoDao->inserir(array('idArquivo' => $idArq, 'idFiscalizacao' => $idFiscalizacao));
        }

        $OrgaoFiscalizadorDao = new OrgaoFiscalizador();
        foreach ($post->idOrgaoExcluido as $idOrgaoExcluido) {
            $OrgaoFiscalizadorDao->delete(array('idOrgaoFiscalizador = ?' => $idOrgaoExcluido));
        }

        foreach ($post->idOrgao as $idOrgao) {
            $OrgaoFiscalizadorDao->inserir(array('idOrgao' => $idOrgao, 'idFiscalizacao' => $idFiscalizacao));
        }

        if ($post->oficializar) {
            parent::message("Dados enviados com sucesso!", "pesquisarprojetofiscalizacao/grid", "CONFIRM");
        } else {
            parent::message("Dados cadastrados com sucesso!", "pesquisarprojetofiscalizacao/oficializarfiscalizacao?idPronac=".$this->view->idPronac, "CONFIRM");
        }
    }

    public function visualizaremailAction()
    {
        //comunicar proponente da fiscaliza�?o
        $post = Zend_Registry::get('get');

        $projetoDao = new Projetos();
        $infoProjeto = $projetoDao->projetosFiscalizacaoConsultar(array('Projetos.IdPRONAC = ?' => $post->idPronac, 'tbFiscalizacao.idFiscalizacao = ?' => $post->idFiscalizacao));
        $OrgaoFiscalizadorDao = new OrgaoFiscalizador();
        $dadosOrgaos = $OrgaoFiscalizadorDao->dadosOrgaos(array('tbOF.idFiscalizacao = ?' => $infoProjeto[0]->idFiscalizacao));

        $nomeProponente = $infoProjeto[0]->nmAgente;
        $enderecoInstituicao = '';
        $nomeProjeto = $infoProjeto[0]->NomeProjeto;
        $Pronac = $infoProjeto[0]->AnoProjeto . $infoProjeto[0]->Sequencial . " - ($nomeProjeto)";
        $nomeServidoresFiscalizadores = $infoProjeto[0]->nmTecnico;
        $periodo = date('d/m/Y', strtotime($infoProjeto[0]->dtInicioFiscalizacaoProjeto)) . ' a ' . date('d/m/Y', strtotime($infoProjeto[0]->dtFimFiscalizacaoProjeto));
        if (is_object($dadosOrgaos)) {
            foreach ($dadosOrgaos as $key => $value) {
                if ($value->Descricao != '') {
                    $nomeServidoresFiscalizadores .= ', ' . $value->Descricao;
                }
            }
        }
        $convenio = 'no Decreto n&deg; 6.170, de 25 de julho de 2007, e na PORTARIA INTERMINISTERIAL MP/MF/MCT N&deg; 127, DE 29 DE MAIO DE 2008';
        $incentivo = 'na Lei n. 8.313/91, e na INSTRU&Ccedil;&Atilde;O NORMATIVA n&deg; 1, DE 5 DE OUTUBRO DE 2010.';
        if ($infoProjeto[0]->Mecanismo == 109) {
            $texto = $incentivo;
        } else {
            $texto = $convenio;
        }
        $getBaseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $brasao = $getBaseUrl."/public/img/brasaoArmas.jpg";
        $textoenvio = " <center><img src=\"$brasao\"/></center>
                        <center>Minist&eacute;rio&nbsp;da&nbsp;Cultura</center>
                        <center>Secretaria&nbsp;de&nbsp;Incentivo&nbsp;e&nbsp;Fomento&nbsp;&agrave;&nbsp;Cultura</center>
                        <br/>
                        <br/>
                        A Sua Senhoria o Senhor<br/>
                            $nomeProponente<br/>
                            $enderecoInstituicao
                        <br/>
                        <br/>
                        Assunto: 	Fiscaliza&ccedil;&atilde;o in loco
                        <br/>
                        Pronac:	$Pronac
                        <br/>
                        <br/>
                                        Prezado Senhor (a),
                        <br/>
                        <br/>
                        1.		Apresentamos, $nomeServidoresFiscalizadores, servidores deste Minist&eacute;rio, que no per&iacute;odo de $periodo , verificar&atilde;o a execu&ccedil;&atilde;o f&iacute;sica do projeto $nomeProjeto, conforme disposto no $texto
                        <br/>
                        <br/>
                                        Atenciosamente,
                        <br/>
                        <br/>
                        <br/>
                        <center>HENILTON PARENTE MENEZES</center>
                        <center>Secret&aacute;rio de Fomento e Incentivo &agrave; Cultura</center>
                ";
        //*/

        $this->view->html = $textoenvio;
        $this->view->idPronac = $post->idPronac;
        $this->view->idFiscalizacao = $post->idFiscalizacao;
    }

    public function enviaremailAction()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $post = Zend_Registry::get('post');
        $infoProjeto = EmailDAO::buscarEmailsFiscalizacao($post->idPronac, $post->idFiscalizacao);
        $infoProjeto = $db->fetchAll($infoProjeto);

        $emails = array();
        foreach ($infoProjeto as $valor) {
            $emails[$valor->email] = $valor->email;
        }
        $textoenvio = $_POST['html'];

        //descomentar linha abaixo para produ�?o
        $emailEnvio = implode(';', $emails);
        EmailDAO::enviarEmail($emailEnvio, 'Fiscalizacao in loco', $textoenvio);

        parent::message("Mensagem enviada com sucesso!", "pesquisarprojetofiscalizacao/grid", "CONFIRM");
    }

    public function excluirAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);
        $post = Zend_Registry::get('post');
        $resposta = array('result' => false, 'mensagem' => utf8_encode('N?o foi possivel 1!'));
        if ($post->idOrgaoFiscalizador) {
            $orgaofiscalizadorDao = new OrgaoFiscalizador();
            if ($orgaofiscalizadorDao->delete(array('idOrgaoFiscalizador = ?' => $post->idOrgaoFiscalizador))) {
                $resposta = array('result' => true, 'mensagem' => 'Exclus&atilde;o realizada com sucesso!');
            } else {
                $resposta = array('result' => false, 'mensagem' => utf8_encode('N?o foi possivel2!'));
            }
        }
        if ($post->idArquivoFiscalizacao) {
            $arquivofiscalizacaoDao = new ArquivoFiscalizacao();
            if ($arquivofiscalizacaoDao->delete(array('idArquivoFiscalizacao = ?' => $post->idArquivoFiscalizacao))) {
                $resposta = array('result' => true, 'mensagem' => 'Exclus&atilde;o realizada com sucesso!');
            } else {
                $resposta = array('result' => false, 'mensagem' => utf8_encode('N?o foi possivel3!'));
            }
        }
        if ($post->idArquivo) {
            $ArquivoImagemDao = new ArquivoImagem();
            $rs2 = $ArquivoImagemDao->delete(array('idArquivo = ?' => $post->idArquivo));

            $arquivoDao = new Arquivo();
            $rs = $arquivoDao->delete(array('idArquivo = ?' => $post->idArquivo));

            if ($rs && $rs2) {
                $resposta = array('result' => true, 'mensagem' => 'Exclus&atilde;o realizada com sucesso!');
            } else {
                $resposta = array('result' => false, 'mensagem' => utf8_encode('N?o foi possivel4!'));
            }
        }
        $this->_helper->json($resposta);
    }

    public function buscartecnicoAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);

        $post = Zend_Registry::get('post');
        $perfil = $post->perfil;
        if (isset($post->cnpjcpf)) {
            $where = array('A.CNPJCPF = ?'=>preg_replace('/(\.|\-)/is', '', $post->cnpjcpf),'U.gru_codigo = ?' => $perfil, 'A.Status = ?' => 0, 'U.uog_orgao = ?' => $this->view->orgaoAtivo);
        } else {
            $where = array('U.gru_codigo = ?' => $perfil, 'A.Status = ?' => 0, 'U.uog_orgao = ?' => $this->view->orgaoAtivo);
        }

        $agentesDao = new Agente_Model_DbTable_Agentes();

        $tecnicos = $agentesDao->buscarFornecedorFiscalizacao($where)->toArray();
        foreach ($tecnicos as $key1 => $val1) {
            foreach ($tecnicos[$key1] as $key => $val) {
                $tecnicos[$key1][$key] = utf8_encode($val);
            }
        }
        if ($tecnicos) {
            $this->_helper->json($tecnicos);
        } else {
            $this->_helper->json(0);
        }
    }

    public function carregadadosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);
        $post = Zend_Registry::get('post');
        $retorno = array();
        switch ($post->option) {
            case 'uf':
                $ufDao = new Uf();
                $resp = $ufDao->buscar(array('Regiao = ?' => $post->regiao), array('Sigla'));
                foreach ($resp as $key => $resulte) {
                    $retorno[$key]['id'] = $resulte->idUF;
                    $retorno[$key]['nome'] = $resulte->Sigla;
                }
                break;

            case 'cidade':
                $municipioDao = new Municipios();

                $resp = $municipioDao->buscar(array('idUFIBGE = ?' => $post->idUF), array('Descricao'));
                foreach ($resp as $key => $resulte) {
                    $retorno[$key]['id'] = $resulte->idMunicipioIBGE;
                    $retorno[$key]['nome'] = utf8_encode($resulte->Descricao);
                }

                break;
            case 'regiao':
                $ufDao = new Uf();

                $resp = $ufDao->buscaRegiaoPorPRONAC($post->PRONAC);
                $retorno = $resp[0]['Regiao'];

                break;
            case 'segmento':
                $ufDao = new Segmento();
                $resp = $ufDao->buscar(array('Regiao = ?' => $post->regiao), array('Sigla'));
                foreach ($resp as $key => $resulte) {
                    $retorno[$key]['id'] = $resulte->idUF;
                    $retorno[$key]['nome'] = $resulte->Sigla;
                }
                break;
        }
        $this->_helper->json($retorno);
    }

    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        return $router->assemble($urlOptions, $name, $reset, $encode);
    }
}
