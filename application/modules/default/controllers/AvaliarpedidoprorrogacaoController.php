<?php 
class AvaliarpedidoprorrogacaoController extends MinC_Controller_Action_Abstract
{
    private $getIdAgente  = 0;
    private $getIdGrupo   = 0;
    private $getIdOrgao   = 0;
    private $getIdUsuario = 0;
    private $intTamPag = 10;
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        // verifica as permissões
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 121;  // Técnico de Acompanhamento
        $PermissoesGrupo[] = 122;  // Coordenador de Acompanhamento
        parent::perfil(1, $PermissoesGrupo);

        $Usuario = new Autenticacao_Model_Usuario(); // objeto usuário
        $auth = Zend_Auth::getInstance(); // pega a autenticação
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $this->getIdAgente = $idagente['idAgente'];
        $this->getIdGrupo = $GrupoAtivo->codGrupo;
        $this->getIdOrgao = $GrupoAtivo->codOrgao;
        $this->getIdUsuario = $auth->getIdentity()->usu_codigo;
        parent::init();
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
            $order = array(6);
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $Usuariosorgaosgrupos = new Usuariosorgaosgrupos();
        $dadosOrgaos = $Usuariosorgaosgrupos->buscarOrgaoSuperiorUnico($this->getIdOrgao);


        /* ================== PAGINACAO ======================*/
        $where = array();
        if (PerfilModel::TECNICO_DE_ACOMPANHAMENTO == $this->getIdGrupo) {
            $where['pr.Atendimento = ?'] = ProrrogacaoModel::EM_ANALISE;
        } elseif (PerfilModel::COORDENADOR_DE_ACOMPANHAMENTO == $this->getIdGrupo) {
            $where['pr.Atendimento = ?'] = ProrrogacaoModel::DEFERIDO;
        }
        $where['p.Situacao in (?)'] = array('E10','E11','E12','E15','E16','E23');
        $where['o.idSecretaria = ?'] = $dadosOrgaos->org_superior;

        if ($this->_request->getParam('pronac')) {
            $where['CONCAT(p.AnoProjeto, p.Sequencial) = ?'] = $this->_request->getParam('pronac');
            $this->view->pronac = $this->_request->getParam('pronac');
        }
        
        $Projetos = new Projetos();
        $total = $Projetos->pedidosDeProrrogacao($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Projetos->pedidosDeProrrogacao($where, $order, $tamanho, $inicio);
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
    }

    public function imprimirAction()
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
            $order = array(6);
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        $Usuariosorgaosgrupos = new Usuariosorgaosgrupos();
        $dadosOrgaos = $Usuariosorgaosgrupos->buscarOrgaoSuperiorUnico($this->getIdOrgao);


        /* ================== PAGINACAO ======================*/
        $where = array();
        if (PerfilModel::TECNICO_DE_ACOMPANHAMENTO == $this->getIdGrupo) {
            $where['pr.Atendimento = ?'] = ProrrogacaoModel::EM_ANALISE;
        } elseif (PerfilModel::COORDENADOR_DE_ACOMPANHAMENTO == $this->getIdGrupo) {
            $where['pr.Atendimento = ?'] = ProrrogacaoModel::DEFERIDO;
        }
        $where['p.Situacao in (?)'] = array('E10','E11','E12','E15','E16','E23');
        $where['o.idSecretaria = ?'] = $dadosOrgaos->org_superior;

        if ($this->_request->getParam('pronac')) {
            $where['CONCAT(p.AnoProjeto, p.Sequencial) = ?'] = $this->_request->getParam('pronac');
            $this->view->pronac = $this->_request->getParam('pronac');
        }
        
        $Projetos = new Projetos();
        $total = $Projetos->pedidosDeProrrogacao($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $Projetos->pedidosDeProrrogacao($where, $order, $tamanho, $inicio);
        
        $this->view->qtdRelatorios = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }
    
    
    public function detalharAction()
    {
        $prorrogacao = 0;
        if ($this->_request->getParam("prorrogacao")) {
            $prorrogacao = $this->_request->getParam("prorrogacao");
        } else {
            parent::message("Item n&atilde;o encontrado!", "analisarsituacaoitem", "ERROR");
        }

        $Usuariosorgaosgrupos = new Usuariosorgaosgrupos();
        $dadosOrgaos = $Usuariosorgaosgrupos->buscarOrgaoSuperiorUnico($this->getIdOrgao);
        
        $where = array();
        if (PerfilModel::TECNICO_DE_ACOMPANHAMENTO == $this->getIdGrupo) {
            $where['pr.Atendimento = ?'] = ProrrogacaoModel::EM_ANALISE;
        } elseif (PerfilModel::COORDENADOR_DE_ACOMPANHAMENTO == $this->getIdGrupo) {
            $where['pr.Atendimento = ?'] = ProrrogacaoModel::DEFERIDO;
        }
        $where['p.Situacao in (?)'] = array('E10','E11','E12','E15','E16','E23');
        $where['o.idSecretaria = ?'] = $dadosOrgaos->org_superior;
        $where['pr.idProrrogacao = ?'] = $prorrogacao;

        $Projetos = new Projetos();
        $busca = $Projetos->pedidosDeProrrogacao($where, array(), null, null);
        $this->view->dados = $busca;
        $this->view->justificativa = $busca[0]->Observacao;
        $this->view->analise = $busca[0]->Atendimento;
    }

    /**
     * @todo Definir a quem enviar quando deferir enquanto tecnico
     */
    public function avaliarProrrogacaoAction()
    {
        $idProrrogacao = $this->getRequest()->getParam('idProrrogacao');
        $opcaoDeferimento = $this->getRequest()->getParam('opcaoDeferimento');

        $this->view->dataInicio = $this->getRequest()->getParam('dtInicio');
        $this->view->analise = $this->getRequest()->getParam('analise');
        $this->view->justificativa = $this->getRequest()->getParam('justificativa');
        $prorrogacaoModel = new ProrrogacaoModel();
        try {
            if (ProrrogacaoModel::DEFERIDO == $this->getRequest()->getParam('analise')) {
                if (empty($opcaoDeferimento) && PerfilModel::TECNICO_DE_ACOMPANHAMENTO == $this->getIdGrupo) {
                    parent::message("Ao dererir, escolha uma das opções antes de salvar a sua avaliação!", "avaliarpedidoprorrogacao/detalhar/prorrogacao/{$idProrrogacao}", "ERROR");
                }
                $prorrogacaoModel->deferir(
                    $idProrrogacao,
                    $this->getRequest()->getParam('justificativa'),
                    $this->getRequest()->getParam('analise'),
                    $this->getIdUsuario,
                    $this->getRequest()->getParam('dtInicio'),
                    $this->getRequest()->getParam('dtFinal'),
                    $this->getRequest()->getParam('opcaoDeferimento', ProrrogacaoModel::ENCAMINHAR_COORDENADOR)
                );
            } elseif (ProrrogacaoModel::INDEFERIDO == $this->getRequest()->getParam('analise')) {
                $prorrogacaoModel->indeferir(
                    $idProrrogacao,
                    $this->getRequest()->getParam('justificativa'),
                    $this->getRequest()->getParam('analise'),
                    $this->getIdUsuario
                );
            } elseif (ProrrogacaoModel::PROCESSADO == $this->getRequest()->getParam('analise')) {
                $prorrogacaoModel->indeferir(
                    $idProrrogacao,
                    $this->getRequest()->getParam('justificativa'),
                    $this->getRequest()->getParam('analise'),
                    $this->getIdUsuario
                );
            } else {
                parent::message("Não foi encontrada nenhuma análise. Favor preencher o campo obrigatório!", "avaliarpedidoprorrogacao/detalhar/prorrogacao/{$idProrrogacao}", "ERROR");
            }
            parent::message('Prorrogação alterada com sucesso!', 'avaliarpedidoprorrogacao', 'CONFIRM');
        } catch (InvalidArgumentException $exception) {
            $this->view->camposObrigatoriosException = true;
            $this->_forward('detalhar', 'avaliarpedidoprorrogacao', null, array('prorrogacao' => $idProrrogacao));
        } catch (DateException $exception) {
            parent::message($exception->getMessage(), "avaliarpedidoprorrogacao/detalhar/prorrogacao/{$idProrrogacao}", 'ERROR');
        } catch (Exception $exception) {
            if ($prorrogacaoModel->hasErros()) {
                $this->view->Erros = $prorrogacaoModel->getErros();
                $this->view->dadosProjeto = $prorrogacaoModel->getProjeto($idProrrogacao);
            } else {
                parent::message('Não foi possível realizar seu pedido!', "avaliarpedidoprorrogacao/detalhar/prorrogacao/{$idProrrogacao}", "ERROR");
            }
        }
    }
    
    public function deletarProrrogacaoAction()
    {
        $prorrogacaoModel = new ProrrogacaoModel();
        $prorrogacaoModel->deletar($this->getRequest()->getParam('idProrrogacao'));
        parent::message("Prorrogação excluída com sucesso!", "avaliarpedidoprorrogacao", "CONFIRM");
    }
}
