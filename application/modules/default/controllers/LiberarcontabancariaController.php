<?php

class LiberarcontabancariaController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 10;

    public function init()
    {
        //recupera ID do pre projeto (proposta)
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // ttulo da pgina
        $auth = Zend_Auth::getInstance(); // pega a autenticao
        $Usuario = new UsuarioDAO(); // objeto usurio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sesso com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usurio esteja autenticado
            // verifica as permisses
            $PermissoesGrupo = array();

            $PermissoesGrupo[] = 123; // Coordenador - Geral de Acompanhamento
            $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
            $PermissoesGrupo[] = 121; // Coordenador de Acompanhamento

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est no array de permisses
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgos e grupos do usurio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a viso
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usurio para a viso
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usurio para a viso
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usurio para a viso
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o rgo ativo do usurio para a viso
            $this->cod_orgao = $GrupoAtivo->codOrgao;
        } // fecha if
        else {
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
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
            $order = array(3); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $Orgaos = new Orgaos();
        $idSecretaria = $Orgaos->buscar(array('codigo = ?'=>$this->cod_orgao))->current();

        $where = array();
        $where['idSecretaria = ?'] = $idSecretaria->idSecretaria;
        $where['PercentualCaptado > ?'] = 20; //Percentual maior do que 20%
        //$where['SAC.dbo.fnpercentualCaptado (AnoProjeto, Sequencial) >= ?'] = 20;

        if (isset($get->pronac) && !empty($get->pronac)) {
            $where['AnoProjeto+Sequencial = ?'] = $this->view->pronac = $get->pronac;
        }

        $vwPainelDeLiberacao = new vwPainelDeLiberacao();
        $total = $vwPainelDeLiberacao->listaRelatorios($where, $order, null, null, true);

        $fim = $inicio + $this->intTamPag;
        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $vwPainelDeLiberacao->listaRelatorios($where, $order, $tamanho, $inicio);
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
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function imprimirProjetosALiberarAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $post = Zend_Registry::get('post');

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
            $order = array(3); //Pronac
            $ordenacao = null;
        }

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['idOrgao = ?'] = $this->cod_orgao;

        if (isset($post->pronac) && !empty($post->pronac)) {
            $where['AnoProjeto+Sequencial = ?'] = $post->pronac;
        }

        $vwPainelDeLiberacao = new vwPainelDeLiberacao();
        $busca = $vwPainelDeLiberacao->listaRelatorios($where, $order);
        
        $this->view->dados = $busca;
    }
       
//    public function listarProjetosAction() {
//
//        $orgao = $this->cod_orgao;
//        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
//        $idPronac = $this->_request->getParam("idPronac");
//
//        $post   = Zend_Registry::get('post');
//        $this->intTamPag = 10;
//
//        $bln_encaminhamento = true;
//        $bln_dadosDiligencia = false;
//
//        $pag = 1;
//        if (isset($post->pag)) $pag = $post->pag;
//        if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;
//
//        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
//        $fim = $inicio + $this->intTamPag;
//
//        $arrBusca["idOrgao = ?"] = $orgao;
//
//        $vwPainelDeLiberacao = new vwPainelDeLiberacao();
//        $total = 0;
//        //$projetos = $tblLiberacao->buscaProjetoLiberacao($orgao, null, $inicio, $fim);
//        $total = $vwPainelDeLiberacao->listaRelatorios($arrBusca, array(), null, null, true);
//
//        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
//        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
//        if ($fim>$total) $fim = $total;
//
//        $ordem = array();
//        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('3 ASC');}
//        $rs = $vwPainelDeLiberacao->listaRelatorios($arrBusca, $ordem, $tamanho, $inicio);
//
//        $this->view->registros 	  = $rs;
//        $this->view->pag 	  = $pag;
//        $this->view->total 	  = $total;
//        $this->view->inicio 	  = ($inicio+1);
//        $this->view->fim 	  = $fim;
//        $this->view->totalPag     = $totalPag;
//        $this->view->parametrosBusca= $_POST;
//    }

    public function imprimirListaProjetosAction()
    {
        $orgao = $this->cod_orgao;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout

        $arrBusca["idOrgao = ?"] = $orgao;
        $vwPainelDeLiberacao = new vwPainelDeLiberacao();
        $rs = $vwPainelDeLiberacao->listaRelatorios($arrBusca);
        $this->view->registros = $rs;
    }

    public function liberacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $pronac = isset($_POST['pronac']) ? $_POST['pronac'] : 0;
        $vlCaptado = isset($_POST['vlCaptado']) ? $_POST['vlCaptado'] : 0.00;

        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;

        $buscaProjeto = new ProjetoDAO();
        $dadosProjetos = $buscaProjeto->buscar($pronac);

        foreach ($dadosProjetos as $dados) {
            $mecanismo = $dados->Mecanismo;
            $AnoProjeto = $dados->AnoProjeto;
            $Sequencial = $dados->Sequencial;
            $cgccpf = $dados->CgcCpf;
        }
        $dados = array(
            'AnoProjeto' => $AnoProjeto,
            'Sequencial' => $Sequencial,
            'Mecanismo' => $mecanismo,
            'DtLiberacao' => new Zend_Db_Expr('GETDATE()'),
            'DtDocumento' => new Zend_Db_Expr('GETDATE()'),
            'NumeroDocumento' => '00000',
            'VlOutrasFontes' => '0,00',
            'Observacao' => 'Conta Liberada',
            'CgcCpf' => '',
            'Permissao' => 'S',
            'Logon' => $idusuario,
            'VlLiberado' => $vlCaptado
        );
        
        $liberar = new Liberacao();
        $buscar = $liberar->buscar(array('AnoProjeto = ?' => $AnoProjeto, 'Sequencial = ?' => $Sequencial))->toArray();

        if (!$buscar) {
            $liberar->inserir($dados);
            $this->_helper->json(array('resposta'=>true, 'cgccpf'=>$cgccpf));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function inabilitadosAction()
    {
        $cpf = $_GET['Cpf'];
        $busca = new Liberacao();
        $projetos = $busca->buscaProjetosInabilitados(null, $cpf);
        $this->view->projetos = $projetos;
    }

    public function contasLiberadasAction()
    {
    }

    public function localizarprojetosAction()
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
            $order = array(4); //Pronac
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
        $where['n.TipoNome in (?)'] = array(18,19);

        if (isset($get->pronac) && !empty($get->pronac)) {
            $where['p.AnoProjeto+p.Sequencial = ?'] = $this->view->pronac = $get->pronac;
        }
        if (isset($get->cnpjcpf) && !empty($get->cnpjcpf)) {
            $where['a.CNPJCPF = ?'] = $this->view->cnpjcpf = Mascara::delMaskCPFCNPJ($get->cnpjcpf);
        }
        if (isset($get->dtI)  && !empty($get->dtI)) {
            $this->view->tipo_dt = $get->tipo_dt;
            $this->view->dtI = $get->dtI;
            $this->view->dtF = $get->dtF;
            $d1 = Data::dataAmericana($get->dtI);
            if ($get->tipo_dt == 1) {
                $where["l.DtLiberacao BETWEEN '$d1' AND '$d1 23:59:59.999'"] = '';
            } elseif ($get->tipo_dt == 2) {
                $d2 = Data::dataAmericana($get->dtF);
                $where["l.DtLiberacao BETWEEN '$d1' AND '$d2'"] = '';
            }
        }
        if (isset($get->secretaria) && !empty($get->secretaria)) {
            $this->view->secretaria = $get->secretaria;
            if ($get->secretaria == 1) {
                $where['p.Area <> ?'] = 2;
            } else {
                $where['p.Area = ?'] = 2;
            }
        }

        $Liberacao = new Liberacao();
        $total = $Liberacao->consultarLiberacoes($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Liberacao->consultarLiberacoes($where, $order, $tamanho, $inicio);
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
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;

        $this->view->vlrTotalGrid = $Liberacao->consultarLiberacoesTotalValorGrid($where);
    }

    public function imprimirProjetosLiberadosAction()
    {
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
        $post = Zend_Registry::get('post');

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
            $order = array(3); //Pronac
            $ordenacao = null;
        }

        $where = array();
        $where['n.TipoNome in (?)'] = array(18,19);

        if (isset($post->pronac) && !empty($post->pronac)) {
            $where['p.AnoProjeto+p.Sequencial = ?'] = $post->pronac;
        }
        if (isset($post->cnpjcpf) && !empty($post->cnpjcpf)) {
            $where['a.CNPJCPF = ?'] = Mascara::delMaskCPFCNPJ($post->cnpjcpf);
        }
        if (isset($post->dtI)  && !empty($post->dtI)) {
            $d1 = Data::dataAmericana($post->dtI);
            if ($post->tipo_dt == 1) {
                $where["l.DtLiberacao BETWEEN '$d1' AND '$d1 23:59:59:999'"] = '';
            } elseif ($post->tipo_dt == 2) {
                $d2 = Data::dataAmericana($post->dtF);
                $where["l.DtLiberacao BETWEEN '$d1' AND '$d2'"] = '';
            }
        }
        if (isset($post->secretaria) && !empty($post->secretaria)) {
            if ($post->secretaria == 1) {
                $where['p.Area <> ?'] = 2;
            } else {
                $where['p.Area = ?'] = 2;
            }
        }

        $Liberacao = new Liberacao();
        $busca = $Liberacao->consultarLiberacoes($where, $order);
        
        $this->view->dados = $busca;
        $this->view->vlrTotalGrid = $Liberacao->consultarLiberacoesTotalValorGrid($where);
    }
}
