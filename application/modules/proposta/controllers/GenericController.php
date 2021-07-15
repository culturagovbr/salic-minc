<?php

abstract class Proposta_GenericController extends MinC_Controller_Action_Abstract
{
    /**
     * idPreProjeto
     * Primary key da proposta
     * @var int
     */
    protected $idPreProjeto = null;

    /**
     * idUsuario
     * ID do usuario do sistema, nao eh proponente
     * @var int
     */
    protected $idUsuario = null;

    /**
     * ID do proponente, responsavel pela proposta cultural e projeto no sistema
     * @var int
     */
    protected $idAgente = null;


    /**
     * @todo verificar o sentido do idResponsavel, parece que eh o mesmo do idUsuario
     * @var int
     */
    protected $idResponsavel = null;


    /**
     * @var int
     * @todo verificar a diferenca deste id para os outros
     */
    protected $idAgenteProponente = null;


    /**
     * @var object
     */
    protected $usuario = null;


    /**
     * @var int
     */
    protected $cpfLogado = null;


    /**
     * @var object
     */
    protected $_proposta;


    /**
     * @var object
     */
    protected $_proponente;

    /**
     * @var array
     */
    protected $_agenteUsuarioLogado;


    private $_movimentacaoAlterarProposta = '95';
    private $_diasParaAlterarProjeto = 30;
    protected $isEditarProposta = false;
    protected $isEditarProjeto = false;
    protected $isEditavel = false;

    public function init()
    {
        parent::init();

        $auth = Zend_Auth::getInstance();
        $PermissoesGrupo = array();

        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_DbTable_Usuario();
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        $arrAuth = array_change_key_case((array)$auth->getIdentity());
        $this->usuario = $arrAuth;


        /**
         * Quando eh colabadordor do MinC (funcionarios e pareceristas)
         * O cpf eh o usu_identificacao
         *
         */
        $this->cpfLogado = isset($arrAuth['usu_identificacao']) ? $arrAuth['usu_identificacao'] : $arrAuth['cpf'];


        /**
         * Quando eh colabadordor do MinC (funcionarios e pareceristas)
         * O idUsuario eh o usu_codigo da Autenticacao_Model_Usuario
         */
        $this->idUsuario = !empty($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;


        /**
         * @todo verificar a diferenca entre idResponsavel e idUsuario
         */
        $this->idResponsavel = $auth->getIdentity()->IdUsuario;

        /**
         * Agentes sao proponentes da proposta ou do projeto
         */
        $tblAgentes = new Agente_Model_DbTable_Agentes();
        $agente = $tblAgentes->findBy(array('cnpjcpf' => $this->cpfLogado));
        $this->_agenteUsuarioLogado = $agente;
        if ($agente) {
            $this->idAgente = $agente['idAgente'];
            $this->view->idAgente = $agente['idAgente'];
        }

        $this->idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

        if (!empty($this->idPreProjeto)) {

            $this->_proposta = $this->buscarProposta($this->idPreProjeto);
            $this->_proponente = $this->buscarProponente($this->_proposta['idagente']);

            $this->view->idPreProjeto = $this->idPreProjeto;
            $this->view->proposta = $this->_proposta;
            $this->view->proponente = $this->_proponente;
            $this->view->url = $this->getRequest()->REQUEST_URI;

            $this->isEditarProposta = $this->isEditarProposta($this->idPreProjeto);
            $this->isEditarProjeto = $this->isEditarProjeto($this->idPreProjeto);
            $this->isEditavel = $this->isEditavel($this->idPreProjeto);
            $this->view->isEditarProposta = $this->isEditarProposta;
            $this->view->isEditarProjeto = $this->isEditarProjeto;
            $this->view->isEditavel = $this->isEditavel;

            $this->view->recursoEnquadramentoVisaoProponente = $this->obterRecursoEnquadramentoVisaoProponente($this->idPreProjeto);

            $layout = array(
                'titleShort' => 'Proposta',
                'titleFull' => 'Proposta Cultural',
                'projeto' => $this->idPreProjeto,
                'listagem' => ['Lista de propostas' => [
                    'module' => 'proposta',
                    'controller' => 'manterpropostaincentivofiscal',
                    'action' => 'listarproposta']
                ],
            );

            // Alterar projeto
            if (!$this->view->isEditarProposta) {
                $tblProjetos = new Projetos();
                $projeto = array_change_key_case($tblProjetos->findBy(array('idprojeto = ?' => $this->idPreProjeto)));

                if (!empty($projeto)) {

                    if (!isset($projeto['nrprojeto'])) {
                        $projeto['nrprojeto'] = $projeto['anoprojeto'] . $projeto['sequencial'];
                    }

                    $this->view->projeto = $projeto;

                    $layout = [
                        'titleShort' => 'Projeto',
                        'titleFull' => 'Alterar projeto',
                        'projeto' => $projeto['nrprojeto'],
                        'listagem' => [
                            'Lista de projetos' => [
                                'module' => 'default',
                                'controller' => 'Listarprojetos',
                                'action' => 'listarprojetos'
                            ]
                        ],
                        'prazoAlterarProjeto' => $this->contagemRegressivaSegundos($projeto['dtsituacao'], $this->_diasParaAlterarProjeto)
                    ];

                    if (!empty($this->view->isEditarProjeto)) {
                        $this->salvarDadosPropostaSerializada($this->idPreProjeto);
                    }
                }
            }

            $this->view->layout = $layout;

            # VERIFICA SE A PROPOSTA ESTA COM O MINC
            $Movimentacao = new Proposta_Model_DbTable_TbMovimentacao();
            $rsStatusAtual = $Movimentacao->buscarStatusAtualProposta($this->idPreProjeto);
            $this->view->movimentacaoAtual = isset($rsStatusAtual['Movimentacao']) ? $rsStatusAtual['Movimentacao'] : '';
        }
    }

    private function buscarProponente($idAgente)
    {
        $tblAgente = new Agente_Model_DbTable_Agentes();

        $proponente = $tblAgente->buscarAgenteENome(array('a.idagente = ?' => $idAgente))->current();

        if ($proponente) {
            $proponente = array_change_key_case($proponente->toArray());

            return $proponente;
        }

        return false;
    }

    private function buscarProposta($idPreProjeto)
    {
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $proposta = $tblPreProjeto->buscar(array('idPreProjeto = ?' => $idPreProjeto))->current();

        if ($proposta) {
            $proposta = array_change_key_case($proposta->toArray());
            return $proposta;
        }
        return false;
    }

    public function isEditarProposta($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        // Verifica se a proposta estah com o minc
        $tbMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
        $rsStatusAtual = $tbMovimentacao->findBy(array('idprojeto = ?' => $idPreProjeto, 'stestado = ?' => 0));

        if ($rsStatusAtual['Movimentacao'] == $this->_movimentacaoAlterarProposta) {
            return true;
        }

        return false;
    }

    public function isEditarProjeto($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        // Verifica se o projeto esta na situacao para editar
        $tblProjetos = new Projetos();
        $projeto = $tblProjetos->findBy(array('idprojeto = ?' => $idPreProjeto));

//        $tblProjetos->verificarLiberacaoParaAdequacao($projeto['IdPRONAC']);
        if (!$tblProjetos->verificarLiberacaoParaAdequacao($projeto['IdPRONAC'])) {
            return false;
        }

        if ($this->contagemRegressivaSegundos($projeto['DtSituacao'], $this->_diasParaAlterarProjeto) < 0) {
            return false;
        }

        if ($projeto['Situacao'] != Projeto_Model_Situacao::PROJETO_LIBERADO_PARA_AJUSTES) {
            return false;
        }

        return true;
    }

    public function isEditavel($idPreProjeto)
    {
        if (!$this->isEditarProjeto($idPreProjeto) && !$this->isEditarProposta($idPreProjeto)) {
            return false;
        }

        return true;
    }

    public function contagemRegressivaDias($datainicial = null, $prazo = null)
    {
        $datafinal = "NOW";

        $datainicial = strtotime($datainicial . "+ " . $prazo . " day");
        $datafinal = strtotime($datafinal);
        $datatirada = $datainicial - $datafinal;
        $dias = (($datatirada / 3600) / 24);

        return $dias;
    }

    public function contagemRegressivaSegundos($datainicial = null, $prazo = null)
    {
        $datafinal = "NOW";

        $datainicial = strtotime($datainicial . "+ " . $prazo . " day");
        $datafinal = strtotime($datafinal) + 24 * 3600;
        $segundos = $datainicial - $datafinal;

        return $segundos;
    }

    /**
     *
     * Metodo para salvar uma copia das informacoes da proposta antes do proponente alterar o projeto(proposta)
     * Salva a tbplanilhaproposta, abrangencia, planodistribuicaoproduto e tbdetalhaplanodistribuicao
     *
     * @param $idPreProjeto
     * @return bool
     */
    public function salvarDadosPropostaSerializada($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        if (!$this->isEditarProjeto($idPreProjeto)) {
            return false;
        }

        $this->salvarPropostaSerializadaAlterarProjeto($idPreProjeto);

        return true;
    }

    /**
     * Na adequação à realidade a versão do projeto será salva apenas uma vez
     */
    public function salvarPropostaSerializadaAlterarProjeto($idPreProjeto)
    {
        $tbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();
        $metaProposta = $tbPreProjetoMeta->buscarMeta($idPreProjeto, 'alterarprojeto_identificacaoproposta');

        if (!$metaProposta) {
            $tbPreProjetoMetaMapper = new Proposta_Model_TbPreProjetoMetaMapper();
            $tbPreProjetoMetaMapper->salvarPropostaCulturalSerializada($this->idPreProjeto, 'alterarprojeto');
        }
    }

    private function obterRecursoEnquadramentoVisaoProponente($idPreProjeto)
    {
        $tbRecursoProposta = new Recurso_Model_DbTable_TbRecursoProposta();
        return $tbRecursoProposta->obterRecursoAtualVisaoProponente($idPreProjeto);
    }

    public function validarEdicaoProposta()
    {
        if ($this->idPreProjeto && !$this->isEditavel) {
            $this->redirecionarParaVisualizacao();
        }
    }

    public function redirecionarParaVisualizacao()
    {
        $this->repassarMensagem();
        $this->redirect("/proposta/visualizar/index/idPreProjeto/" . $this->idPreProjeto);
    }

    public function repassarMensagem()
    {
        if ($this->_msg->hasMessages()) {
            $this->_helper->viewRenderer->setNoRender(false);
            $this->_helper->flashMessenger->addMessage(implode("<br />", $this->_msg->getMessages()));

            if ($this->_type->hasMessages()) {
                $this->_helper->flashMessengerType->addMessage(implode("<br />", $this->_type->getMessages()));
            }
        }
    }
}
