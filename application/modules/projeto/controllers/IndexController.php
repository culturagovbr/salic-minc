<?php

class Projeto_IndexController extends Projeto_GenericController
{
    private $getIdUsuario = 0;
    private $getCNPJCPF = 0;
    protected $idResponsavel = 0;
    protected $idAgente = 0;
    protected $idUsuario = 0;
    protected $cpfLogado = 0;


    public function init()
    {
        parent::init();
        $this->validarPerfis();
    }

    private function validarPerfis()
    {
        $auth = Zend_Auth::getInstance();

        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 147;
        $PermissoesGrupo[] = 148;
        $PermissoesGrupo[] = 149;
        $PermissoesGrupo[] = 150;
        $PermissoesGrupo[] = 151;
        $PermissoesGrupo[] = 152;

//         isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    private function carregarScripts()
    {
        $gitTag = '?v=' . $this->view->gitTag();
        $this->view->headScript()->offsetSetFile(99, '/public/dist/js/manifest.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(100, '/public/dist/js/vendor.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(101, '/public/dist/js/projeto.js'. $gitTag, 'text/javascript', array('charset' => 'utf-8'));
    }

    public function indexAction()
    {
        $this->carregarScripts();
    }

    public function listarAction()
    {
        $dbTableVinculo = new Agente_Model_DbTable_TbVinculo();
        $proponenteVinculados = $dbTableVinculo->buscarProponenteResponsavel($this->idUsuarioExterno);

        $proponentes = [];
        foreach ($proponenteVinculados as $key => $proponenteVinculado) {
            $proponentes[$key]['idAgenteProponente'] = $proponenteVinculado->idAgente;
            $proponentes[$key]['CPF'] = $proponenteVinculado->CNPJCPF;
            $proponentes[$key]['Nome'] = $proponenteVinculado->NomeProponente;
        }

        $this->view->buscaProponente = $proponentes;

        $tbMecanismo = new Mecanismo();
        $this->view->mecanismos = $tbMecanismo->buscar(['status = ?' => 1]);

        $this->view->proponentes = $proponentes;
        $this->view->idResponsavel = $this->idUsuarioExterno;
        $this->view->idUsuario = $this->idUsuarioExterno;
        $this->view->idAgente = $this->idAgente;

    }

    public function listarProjetosAjaxAction()
    {
        $idProponente = $this->getRequest()->getParam('id');
        $mecanismo = $this->getRequest()->getParam('mecanismo');
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');

        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["idPronac desc"];

        $idProponente = ((int)$idProponente == 0) ? $this->idAgente : (int)$idProponente;

        if (empty($idProponente)) {
            $this->_helper->json(array(
                "data" => 0,
                'recordsTotal' => 0,
                'draw' => 0,
                'recordsFiltered' => 0));
        }

        $tbProjetos = new Projeto_Model_DbTable_Projetos();
        $projetos = $tbProjetos->obterProjetosPorProponente(
            $this->idUsuarioExterno,
            $idProponente,
            $mecanismo,
            [],
            $order,
            $start,
            $length,
            $search
        );

        $recordsTotal = 0;
        $recordsFiltered = 0;
        $dados = array();
        $tbMecanismo = new Mecanismo();
        $parecer = new Parecer();
        if (!empty($projetos)) {
            foreach ($projetos as $key => $projeto) {
                $novoProjeto = new stdClass();
                $novoProjeto->pronac = $projeto->Pronac;
                $novoProjeto->idPronac = $projeto->IdPRONAC;
                $novoProjeto->idProjeto = $projeto->idProjeto;
                $novoProjeto->idPronacHash = Seguranca::encrypt($projeto->IdPRONAC);
                $novoProjeto->idMecanismo = $projeto->Mecanismo;
                $novoProjeto->mecanismo = utf8_encode($tbMecanismo->obterLabelMecanismo($projeto->Mecanismo));
                $novoProjeto->nomeprojeto = utf8_encode($projeto->NomeProjeto);
                $novoProjeto->periodo = Data::mostrarPeriodoDeDatas($projeto->DtInicioDeExecucao, $projeto->DtFinalDeExecucao);
                $novoProjeto->situacao = utf8_encode($projeto->Situacao) . ' - ' . utf8_encode($projeto->Descricao);
                $novoProjeto->podeClonarProjeto = !empty($projeto->idProjeto) ? true : false;
                $novoProjeto->podeAdequarProjeto = (boolean) $tbProjetos->fnChecarLiberacaoDaAdequacaoDoProjeto($projeto->IdPRONAC);
                $novoProjeto->emAnaliseCnic = count($parecer->verificaProjSituacaoCNIC($projeto->Pronac)) > 0 ? true : false;
                $dados[$key] = $novoProjeto;
            }

            $recordsTotal = $tbProjetos->obterProjetosPorProponente(
                $this->idUsuarioExterno,
                $idProponente,
                $mecanismo,
                [],
                $order,
                $start,
                $length,
                $search,
                true
            );

            $recordsFiltered = $recordsTotal;
        }

        $this->_helper->json(array(
            "data" => !empty($dados) ? $dados : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0
        ));
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();

        $cpf = $_GET['cpf'];
        $listar = ListarprojetosDAO::buscarDadosListarProjetos($cpf);
        $this->view->listarprojetos = $listar;
    }

    public function gerarxlsAction()
    {
        $cpf = $_GET['cpf'];
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $listar = ListarprojetosDAO::buscarDadosListarProjetos($cpf);
        $this->view->listarprojetos = $listar;
    }

    public function clonarProjetoAjaxAction()
    {
        $params = $this->getRequest()->getParams();


        $dados['status'] = false;
        try {

            $idPronac = isset($params['idPronac']) ? $params['idPronac'] : '';

            $permissaoProjeto = $this->verificarPermissaoAcesso(false, $idPronac, false, true);

            if (true !== $permissaoProjeto['status']) {
                throw new Exception($permissaoProjeto['msg']);
            }

            if (empty($this->idUsuarioExterno)) {
                throw new Exception("Usu&aacute;rio inv&aacute;lido!");
            }

            $tbProjetos = new Projeto_Model_DbTable_Projetos();
            $idPreProjeto = $tbProjetos->obterIdPreProjetoDoProjeto($idPronac);

            if (empty($idPreProjeto)) {
                throw new Exception("Erro ao Clonar! Este projeto n&atilde;o possui proposta!");
            }

            $retorno = (array)$tbProjetos->spClonarProjeto($idPronac, $this->idUsuarioExterno);

            $dados['msg'] = $retorno['Mensagem'];
            $dados['status'] = false;

            if ($retorno['stEstado'] == 'TRUE') {
                $dados['idPreProjeto'] = $retorno['Mensagem'];
                $dados['msg'] = 'Sucesso! Voc&ecirc; ser&aacute; redirecionado para a proposta!';
                $dados['status'] = true;
            }

            $this->_helper->json($dados);
            die;
        } catch(Exception $e) {
            $dados['msg'] =  utf8_encode($e->getMessage());
            $dados['status'] = false;
            $this->_helper->json($dados);
            die;
        }
    }
}
