<?php

class Projeto_AssinaturaController extends Assinatura_GenericController
{
    private $idTipoDoAtoAdministrativo;

    protected $idResponsavel;
    protected $idUsuario;
    protected $idAgente;

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

        // isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    public function init()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

        $arrIdentity = array_change_key_case((array) Zend_Auth::getInstance()->getIdentity());
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo');

        /*********************************************************************************************************/

        $cpf = isset($arrIdentity['usu_codigo']) ? $arrIdentity['usu_identificacao'] : $arrIdentity['cpf'];

        if (is_null($cpf)) {
            $this->redirect('/');
        }

        // Busca na SGCAcesso
        $modelSgcAcesso 	 = new Autenticacao_Model_Sgcacesso();
        $arrAcesso = $modelSgcAcesso->findBy(array('cpf' => $cpf));

        // Busca na Usuarios
        //Excluir ProposteExcluir Proposto
        $usuarioDAO   = new Autenticacao_Model_DbTable_Usuario();
        $arrUsuario = $usuarioDAO->findBy(array('usu_identificacao' => $cpf));

        // Busca na Agentes
        $tableAgentes  = new Agente_Model_DbTable_Agentes();
        $arrAgente = $tableAgentes->findBy(array('cnpjcpf' => trim($cpf)));

        if ($arrAcesso)  $this->idResponsavel = $arrAcesso['idusuario'];
        if ($arrAgente)  $this->idAgente 	  = $arrAgente['idagente'];
        if ($arrUsuario) $this->idUsuario     = $arrUsuario['usu_codigo'];
        if ($this->idAgente != 0) $this->usuarioProponente = "S";
        $this->cpfLogado = $cpf;


        $this->arrBreadCrumb[] = array('url' => '/principal', 'title' => 'In&iacute;cio', 'description' => 'Ir para in&iacute;cio');


        parent::init();

        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_HOMOLOGAR_PROJETO;
    }

    public function indexAction()
    {
        $this->view->idUsuarioLogado = $this->auth->getIdentity()->usu_codigo;
        $documentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();

        $this->view->dados = $documentoAssinatura->obterProjetosComAssinaturasAbertas(
            $this->grupoAtivo->codOrgao,
            $this->grupoAtivo->codGrupo,
            $this->auth->getIdentity()->usu_org_max_superior,
            $this->idTipoDoAtoAdministrativo
        );

        d($this->view->dados);

        $this->view->codGrupo = $this->grupoAtivo->codGrupo;

        $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $this->view->quantidade_minima_assinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
            $this->idTipoDoAtoAdministrativo,
            $this->auth->getIdentity()->usu_org_max_superior
        );
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;
    }

    public function devolverProjetoAction()
    {
        $this->validarPerfis();
        $get = Zend_Registry::get('get');
        try {
            if (!filter_input(INPUT_GET, 'IdPRONAC')) {
                throw new Exception("Identificador do projeto é necessário para acessar essa funcionalidade.");
            }

            $objTbProjetos = new Projeto_Model_DbTable_Projetos();

            $this->view->projeto = $objTbProjetos->findBy(array(
                'IdPRONAC' => $get->IdPRONAC
            ));

            $post = $this->getRequest()->getPost();
            if ($post) {
                if (!$post['motivoDevolucao']) {
                    throw new Exception("Campo 'Motivação da Devolução para nova avaliação' não informado.");
                }
                $objTbDepacho = new Proposta_Model_DbTable_TbDespacho();
                $objTbDepacho->devolverProjetoEncaminhadoParaAssinatura($get->IdPRONAC, $post['motivoDevolucao']);

                $objOrgaos = new Orgaos();
                $orgaoSuperior = $objOrgaos->obterOrgaoSuperior($this->view->projeto['Orgao']);

                $orgaoDestino = Orgaos::ORGAO_SAV_DAP;
                if ($orgaoSuperior['Codigo'] == Orgaos::ORGAO_SUPERIOR_SEFIC) {
                    $orgaoDestino = Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
                }

                $objTbProjetos->alterarOrgao($orgaoDestino, $get->IdPRONAC);
                $objProjetos = new Projetos();
                $objProjetos->alterarSituacao(
                    $get->IdPRONAC,
                    null,
                    Projeto_Model_Situacao::PROJETO_DEVOLVIDO_PARA_ENQUADRAMENTO,
                    'Projeto encaminhado para nova avalia&ccedil;&atilde;o do enquadramento'
                );

                $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
                $data = array(
                    'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA,
                    'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO
                );
                $where = array(
                    'IdPRONAC = ?' => $get->IdPRONAC,
                    'idTipoDoAtoAdministrativo = ?' => $this->idTipoDoAtoAdministrativo,
                    'cdSituacao = ?' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                    'stEstado = ?' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
                );

                $objModelDocumentoAssinatura->update($data, $where);

                parent::message('Projeto devolvido com sucesso.', "/{$this->moduleName}/enquadramento-assinatura/gerenciar-assinaturas", 'CONFIRM');
            }

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $this->view->abertoParaDevolucao = $objModelDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
                $get->IdPRONAC,
                $this->idTipoDoAtoAdministrativo
            );

            $this->view->IdPRONAC = $get->IdPRONAC;

            $mapperArea = new Agente_Model_AreaMapper();
            $this->view->areaCultural = $mapperArea->findBy(array(
                'Codigo' => $this->view->projeto['Area']
            ));

            $objSegmentocultural = new Segmentocultural();
            $this->view->segmentoCultural = $objSegmentocultural->findBy(array(
                'Codigo' => $this->view->projeto['Segmento']
            ));

            $objEnquadramento = new Admissibilidade_Model_Enquadramento();
            $arrayPesquisa = array(
                'AnoProjeto' => $this->view->projeto['AnoProjeto'],
                'Sequencial' => $this->view->projeto['Sequencial'],
                'IdPRONAC' => $this->view->projeto['IdPRONAC']
            );
            $this->view->dadosEnquadramento = $objEnquadramento->findBy($arrayPesquisa);

            $this->view->titulo = "Devolver";
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/enquadramento-assinatura/devolver-projeto?IdPRONAC={$get->IdPRONAC}");
        }
    }

    public function finalizarAssinaturaAction()
    {
        $this->validarPerfis();
        $get = Zend_Registry::get('get');
        try {
            if (!filter_input(INPUT_GET, 'IdPRONAC')) {
                throw new Exception("Identificador do projeto é necessário para acessar essa funcionalidade.");
            }

            $objProjetos = new Projetos();
            $objProjetos->alterarSituacao(
                $get->IdPRONAC,
                null,
                Projeto_Model_Situacao::PROJETO_APROVADO_AGUARDANDO_ANALISE_DOCUMENTAL,
                'Projeto aprovado - aguardando an&aacute;lise documental'
            );

            $objTbProjetos = new Projeto_Model_DbTable_Projetos();
            $dadosProjeto = $objTbProjetos->findBy(array(
                'IdPRONAC' => $get->IdPRONAC
            ));

            $orgaoDestino = Orgaos::ORGAO_SAV_DAP;
            $objOrgaos = new Orgaos();
            $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

            if ($dadosOrgaoSuperior['Codigo'] == Orgaos::ORGAO_SUPERIOR_SEFIC) {
                $orgaoDestino = Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
            }
            $objTbProjetos->alterarOrgao($orgaoDestino, $get->IdPRONAC);

            $enquadramento = new Admissibilidade_Model_Enquadramento();
            $dadosEnquadramento = $enquadramento->obterEnquadramentoPorProjeto($get->IdPRONAC, $dadosProjeto['AnoProjeto'], $dadosProjeto['Sequencial']);

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $data = array(
                'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA
            );
            $where = array(
                'IdPRONAC = ?' => $get->IdPRONAC,
                'idTipoDoAtoAdministrativo = ?' => $this->idTipoDoAtoAdministrativo,
                'idAtoDeGestao = ?' => $dadosEnquadramento['IdEnquadramento'],
                'cdSituacao = ?' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                'stEstado = ?' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
            );
            $objModelDocumentoAssinatura->update($data, $where);

            $auth = Zend_Auth::getInstance();

            $valoresProjeto = $objTbProjetos->obterValoresProjeto($get->IdPRONAC);

            $dadosInclusaoAprovacao = array(
                'IdPRONAC' => $get->IdPRONAC,
                'AnoProjeto' => $dadosProjeto['AnoProjeto'],
                'Sequencial' => $dadosProjeto['Sequencial'],
                'TipoAprovacao' => 1,
                'dtAprovacao' => $objTbProjetos->getExpressionDate(),
                'ResumoAprovacao' => $dadosEnquadramento['Observacao'],
                'AprovadoReal' => $valoresProjeto['ValorProposta'],
                'Logon' => $auth->getIdentity()->usu_codigo,
            );
            $objAprovacao = new Aprovacao();
            $idAprovacao = $objAprovacao->inserir($dadosInclusaoAprovacao);

            $idTecnico = new Zend_Db_Expr("sac.dbo.fnPegarTecnico(110, {$orgaoDestino}, 3)");

            $tblVerificaProjeto = new tbVerificaProjeto();
            $dadosVP['idPronac'] = $get->IdPRONAC;
            $dadosVP['idOrgao'] = $orgaoDestino;
            $dadosVP['idAprovacao'] = $idAprovacao;
            $dadosVP['idUsuario'] = $idTecnico;
            $dadosVP['stAnaliseProjeto'] = 1;
            $dadosVP['dtRecebido'] = $tblVerificaProjeto->getExpressionDate();
            $dadosVP['stAtivo'] = 1;
            $tblVerificaProjeto->inserir($dadosVP);

            parent::message('Projeto finalizado com sucesso!', "/{$this->moduleName}/enquadramento-assinatura/gerenciar-assinaturas", 'CONFIRM');
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/enquadramento-assinatura/finalizar-assinatura?IdPRONAC={$get->IdPRONAC}");
        }
    }
}
