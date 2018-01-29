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


    private $_movimentacaoAlterarProposta = '95';
    private $_situacaoAlterarProjeto = Projeto_Model_Situacao::PROJETO_LIBERADO_PARA_AJUSTES;
    private $_diasParaAlterarProjeto = 10;

    public function init()
    {
        parent::init();

        $auth = Zend_Auth::getInstance();
        $PermissoesGrupo = array();

        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario();
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
            $this->view->isEditarProposta = $this->isEditarProposta($this->idPreProjeto);
            $this->view->isEditarProjeto = $this->isEditarProjeto($this->idPreProjeto);
            $this->view->isEditavel = $this->isEditavel($this->idPreProjeto);

            $layout = array(
                'titleShort' => 'Proposta',
                'titleFull' => 'Proposta Cultural',
                'projeto' => $this->idPreProjeto,
                'listagem' => array('Lista de propostas' => array('controller' => 'manterpropostaincentivofiscal', 'action' => 'listarproposta')),
            );

            // Alterar projeto
            if (!empty($this->view->isEditarProjeto)) {
                $tblProjetos = new Projetos();
                $projeto = array_change_key_case($tblProjetos->findBy(array('idprojeto = ?' => $this->idPreProjeto)));

                if (!isset($projeto['nrprojeto'])) {
                    $projeto['nrprojeto'] = $projeto['anoprojeto'] . $projeto['sequencial'];
                }

                $this->view->projeto = $projeto;

                $layout = array(
                    'titleShort' => 'Projeto',
                    'titleFull' => 'Alterar projeto',
                    'projeto' => $projeto['nrprojeto'],
                    'listagem' => array('Lista de projetos' => array('module' => 'default', 'controller' => 'Listarprojetos', 'action' => 'listarprojetos')),
                    'prazoAlterarProjeto' => $this->contagemRegressivaSegundos($projeto['dtsituacao'], $this->_diasParaAlterarProjeto)
                );

                $this->salvarDadosPropostaSerializada($this->idPreProjeto);
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

        if ($projeto['Situacao'] == $this->_situacaoAlterarProjeto) {
            return true;
        }

        return false;
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
     * @todo utilizar o metodo generico para salvar a proposta cultural serializada na Proposta_Model_TbPreProjetoMetaMapper
     */
    public function salvarPropostaSerializadaAlterarProjeto($idPreProjeto) {

        # Recupera informações da proposta atual
        $proposta = $this->_proposta;

        $PPM = new Proposta_Model_DbTable_TbPreProjetoMeta();
        $tbPreProjetoMeta = new Proposta_Model_TbPreProjetoMetaMapper();

        # Planilha orcamentaria
        $metaPlanilha = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_tbplanilhaproposta');
        $this->view->PlanilhaSalvo = true;
        if (!$metaPlanilha) {
            $TPP = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $dadosPlanilhaCompleta = $TPP->buscarPlanilhaCompleta($idPreProjeto);

            $this->view->PlanilhaSalvo = $tbPreProjetoMeta->salvarArraySerializado($dadosPlanilhaCompleta, $idPreProjeto, 'alterarprojeto_tbplanilhaproposta');
        }

        # Local de realizacao (abrangencia)
        $metaAbrangencia = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_abrangencia');
        $this->view->AbrangenciaSalvo = true;
        if (!$metaAbrangencia) {
            $TPA = new Proposta_Model_DbTable_Abrangencia();
            $abrangenciaCompleta = $TPA->buscar(array('idProjeto' => $idPreProjeto));
            $this->view->AbrangenciaSalvo = $tbPreProjetoMeta->salvarArraySerializado($abrangenciaCompleta, $idPreProjeto, 'alterarprojeto_abrangencia');
        }

        # Deslocamento
        $metaDeslocamento = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_deslocamento');
        $this->view->DeslocamentoSalvo = true;
        if (!$metaDeslocamento) {
            $TPD = new Proposta_Model_DbTable_TbDeslocamento();
            $deslocamentoCompleto = $TPD->buscarDeslocamentosGeral(array('idProjeto' => $idPreProjeto));
            $this->view->DeslocamentoSalvo = $tbPreProjetoMeta->salvarArraySerializado($deslocamentoCompleto, $idPreProjeto, 'alterarprojeto_deslocamento');
        }

        # Plano distribuicao
        $metaPlanoDistribuicao = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_planodistribuicaoproduto');
        $this->view->PlanoDistribuicaoSalvo = true;
        if (!$metaPlanoDistribuicao) {
            $TPDC = new PlanoDistribuicao();
            $planoDistribuicaoCompleto = $TPDC->buscar(array('idProjeto = ?' => $idPreProjeto))->toArray();
            $this->view->PlanoDistribuicaoSalvo = $tbPreProjetoMeta->salvarArraySerializado($planoDistribuicaoCompleto, $idPreProjeto, 'alterarprojeto_planodistribuicaoproduto');
        }

        # Plano de distribuicao Detalhado
        $metaPlanoDistribuicaoDetalha = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_tbdetalhaplanodistribuicao');
        $this->view->PlanoDistribuicaoDetalhadoSalvo = true;
        if (!$metaPlanoDistribuicaoDetalha) {
            $TPD = new PlanoDistribuicao();
            $PlanoDetalhado = $TPD->buscarPlanoDistribuicaoDetalhadoByIdProjeto($idPreProjeto);

            $this->view->PlanoDistribuicaoDetalhadoSalvo = $tbPreProjetoMeta->salvarArraySerializado($PlanoDetalhado, $idPreProjeto, 'alterarprojeto_tbdetalhaplanodistribuicao');
        }

        # identificacao da proposta
        $metaIdentificacaoProposta = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_identificacaoproposta');
        $this->view->IdentificaoPropostaSalvo = true;
        if (!$metaIdentificacaoProposta) {
            $identificacaoProposta = array(
                'dtiniciodeexecucao' => $proposta['dtiniciodeexecucaoform'],
                'dtfinaldeexecucao' => $proposta['dtfinaldeexecucaoform']
            );

            $this->view->IdentificaoPropostaSalvo = $tbPreProjetoMeta->salvarArraySerializado($identificacaoProposta, $idPreProjeto, 'alterarprojeto_identificacaoproposta');
        }

        # responsabilidade social
        $metaResponsabilidadeSocial = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_responsabilidadesocial');
        $this->view->ResponsabilidadeSocialSalvo = true;
        if (!$metaResponsabilidadeSocial) {
            $responsabilidadeSocial = array(
                'Acessibilidade' => $proposta['acessibilidade'],
                'DemocratizacaoDeAcesso' => $proposta['democratizacaodeacesso'],
                'ImpactoAmbiental' => $proposta['impactoambiental']
            );

            $this->view->ResponsabilidadeSocialSalvo = $tbPreProjetoMeta->salvarArraySerializado($responsabilidadeSocial, $idPreProjeto, 'alterarprojeto_responsabilidadesocial');
        }

        # detalhes técnicos
        $metadetalhesTecnicos = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_detalhestecnicos');
        $this->view->DetalhesTecnicosSalvo = true;
        if (!$metadetalhesTecnicos) {
            $detalhesTecnicos = array(
                'EtapaDeTrabalho' => $proposta['etapadetrabalho'],
                'FichaTecnica' => $proposta['fichatecnica'],
                'Sinopse' => $proposta['sinopse'],
                'EspecificacaoTecnica' => $proposta['especificacaotecnica']
            );
            $this->view->DetalhesTecnicosSalvo = $tbPreProjetoMeta->salvarArraySerializado($detalhesTecnicos, $idPreProjeto, 'alterarprojeto_detalhestecnicos');
        }

        # outras informacoes
        $metaOutrasInformacoes = $PPM->buscarMeta($idPreProjeto, 'alterarprojeto_outrasinformacoes');
        $this->view->OutrasInformacoesSalvo = true;
        if (!$metaOutrasInformacoes) {
            $outrasInformacoes = array(
                'EstrategiadeExecucao' => $proposta['estrategiadeexecucao']
            );

            $this->view->OutrasInformacoesSalvo = $tbPreProjetoMeta->salvarArraySerializado($outrasInformacoes, $idPreProjeto, 'alterarprojeto_outrasinformacoes');
        }
    }
}
