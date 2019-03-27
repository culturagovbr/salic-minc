<?php

class Analise_AnaliseController extends Analise_GenericController
{
    protected $idUsuario = null;
    private $idPreProjeto = null;
    private $idProjeto = null;

    private $codGrupo = null;
    private $codOrgao = null;

    const PERCENTUAL_MINIMO_CAPTACAO_PARA_ANALISE = 10;
    const PARECER_NAO_FAVORAVEL = 0;
    const PARECER_FAVORAVEL = 1;

    public function init()
    {
        parent::init();

        # define as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_ANALISE;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::TECNICO_ANALISE;

        if (!empty($this->getRequest()->getParam('idPreProjeto'))) {
            $this->idPreProjeto = $this->getRequest()->getParam('idPreProjeto');
        }
        $auth = Zend_Auth::getInstance();

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        $this->idUsuario = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_codigo : $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }
    }

    public function listarprojetosAction()
    {
        $this->view->usuarioEhCoordenador = true;

        if (Autenticacao_Model_Grupos::TECNICO_ANALISE == $this->codGrupo) {
            $this->view->usuarioEhCoordenador = false;
        }
    }

    public function listarProjetosAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');

        $order = ($order[0]['dir'] != 1 && isset($order)) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : array("DtSituacao DESC");

        $vwPainelAvaliar = new Analise_Model_DbTable_vwProjetosAdequadosRealidadeExecucao();

        if (Autenticacao_Model_Grupos::TECNICO_ANALISE == $this->codGrupo) {
            $where['idTecnico = ?'] = $this->idUsuario;
        }

        $orgao = new Orgaos();
        $orgao = $orgao->codigoOrgaoSuperior($this->codOrgao);
        $orgaoSuperior = $orgao[0]['Codigo'];
        $where['Orgao = ?'] = $orgaoSuperior;

        $projetos = $vwPainelAvaliar->projetos($where, $order, $start, $length, $search);
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $aux = array();
        if (count($projetos) > 0) {
            foreach ($projetos as $key => $projeto) {
                $projeto->NomeProjeto = utf8_encode($projeto->NomeProjeto);
                $projeto->Tecnico = utf8_encode($projeto->Tecnico);
                $projeto->Segmento = utf8_encode($projeto->Segmento);
                $projeto->Proponente = utf8_encode($projeto->Proponente);
                $projeto->Enquadramento = utf8_encode($projeto->Enquadramento);
                $projeto->Area = utf8_encode($projeto->Area);
                $projeto->VlSolicitado = number_format(($projeto->VlSolicitado), 2, ",", ".");
                $aux[$key] = $projeto;
            }
            $recordsTotal = $vwPainelAvaliar->projetosTotal($where);
            $recordsFiltered = $vwPainelAvaliar->projetosTotal($where, null, null, null, $search);
        }

        $this->_helper->json(array(
            "data" => !empty($aux) ? $aux : 0,
            'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0));
    }

    public function visualizarprojetoAction()
    {
        $this->carregarScriptsVue();

        $idPronac = $this->getRequest()->getParam('idpronac');

        try {
            if (empty($idPronac)) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            if (empty($idPreProjeto)) {
                $objTbProjetos = new Projeto_Model_DbTable_Projetos();
                $projeto = $objTbProjetos->findBy(array(
                    'IdPRONAC' => $idPronac
                ));

                $this->view->projeto = $projeto;
                $this->view->idPreProjeto = $projeto['idProjeto'];
                $this->view->tipo = 'alterarprojeto';
            }

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/analise/listarprojetos", "ERROR");
        }
    }

    public function formavaliaradequacaoAction()
    {
        $this->_helper->layout->disableLayout();

        $idPronac = $this->getRequest()->getParam('idpronac');

        $objTbProjetos = new Projeto_Model_DbTable_Projetos();
        $projeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $idPronac
        ));

        $this->view->projeto = $projeto;
    }

    public function salvaravaliacaadequacaoAction()
    {
        $params = $this->getRequest()->getParams();
        $idPronac = (int) $params['idPronac'];

        try {

            if (empty($idPronac)) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            $tbProjetos = new Projetos();

            $tbAvaliacao = new Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
            $avaliacao = $tbAvaliacao->buscarUltimaAvaliacao($idPronac);

            if ($params['conformidade'] == self::PARECER_NAO_FAVORAVEL) {

                $situacao = Projeto_Model_Situacao::PROJETO_LIBERADO_PARA_AJUSTES;
                $providenciaTomada = 'Projeto liberado para o proponente adequar &agrave; realidade de execu&ccedil;&atilde;o por 30 dias, conforme o Art. 26 da IN 5/2017.';

                $tbProjetos->alterarSituacao($idPronac, '', $situacao, $providenciaTomada);

                if (!empty($avaliacao)) {
                    $tbAvaliacao->atualizarAvaliacaoNegativa($idPronac, $avaliacao['idTecnico'], $params['observacao']);
                }

                # emcaminha e-mail para o proponente com o despacho do avaliador.
                $this->enviarEmail($idPronac, $params['observacao']);

                parent::message($providenciaTomada, "{$this->moduleName}/analise/listarprojetos", "CONFIRM");

            } elseif ($params['conformidade'] == self::PARECER_FAVORAVEL) {

                $dadosProjeto = $tbProjetos->buscar(array('idPronac = ?' => $idPronac))->current();
                $idPreProjeto = $dadosProjeto['idProjeto'];

                if (empty($idPreProjeto)) {
                    throw new Exception("Proposta do projeto n&atilde;o foi encontrada!");
                }

                /**
                 * Adequacao já deve chegar aqui preenchida
                 * 1 - Quando o proponente envia o projeto readequado para o MinC o sistema registra a primeira avaliacao
                 * 2 - Existe uma rotina que pega os projetos na situacao E90 com prazo de adequacao expirado,
                 * a rotina escolhe o tecnico e registra uma nova avaliacao.
                 */
                if (empty($avaliacao)) {
                    throw new Exception("Projeto n&atilde;o possui avalia&ccedil;&atilde;o!");
                }

                $tbPlanoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
                $unidadeVinculada = $tbPlanoDistribuicao->buscarIdVinculada($idPreProjeto);

                if (empty($unidadeVinculada)) {
                    throw new Exception("Unidade vinculada n&atilde;o foi encontrada! Entre em contato com o administrador do sistema!");
                }

                $tbDistribuirParecer = new tbDistribuirParecer();
                $jaExisteParecer = $tbDistribuirParecer->buscar(array('idPronac = ?' => $idPronac))->current();

                if (empty($jaExisteParecer)) {
                    $tbDistribuirParecer->inserirDistribuicaoParaParecer($idPreProjeto, $idPronac, $unidadeVinculada->idVinculada);
                }

                $tbAnaliseDeConteudo = new tbAnaliseDeConteudo();
                $jaExisteAnaliseConteudo = $tbAnaliseDeConteudo->buscar(array('idPronac = ?' => $idPronac))->current();
                if (empty($jaExisteAnaliseConteudo)) {
                    $tbAnaliseDeConteudo->inserirAnaliseConteudoParaParecerista($idPreProjeto, $idPronac);
                }

                $PlanilhaProjeto = new PlanilhaProjeto();
                $jaExistePlanilhaDoParecerista = $PlanilhaProjeto->buscar(array('idPronac = ?' => $idPronac))->current();
                if (empty($jaExistePlanilhaDoParecerista)) {
                    $PlanilhaProjeto->inserirPlanilhaParaParecerista($idPreProjeto, $idPronac);
                }

                $percentualJaCaptado = $this->percentualCaptadoProjeto($idPronac);

                $providenciaTomada = "O Projeto aguardar&aacute; o percentual m&iacute;nimo de capta&ccedil;&atilde;o
                 e depois ser&aacute; encaminhado para unidade vinculada({$unidadeVinculada->Vinculada})!";

                $preProjeto = new Proposta_Model_DbTable_PreProjeto();
                $dadosPreProjeto = $preProjeto->findBy(array('idPreProjeto' => $idPreProjeto));

                if (($percentualJaCaptado >= self::PERCENTUAL_MINIMO_CAPTACAO_PARA_ANALISE)
                    or (!empty($dadosPreProjeto['stProposta']) && $dadosPreProjeto['stProposta'] != Verificacao::PROJETO_NORMAL)
                ) {

                    $situacao = Projeto_Model_Situacao::ENCAMINHADO_PARA_ANALISE_TECNICA;
                    $providenciaTomada = "Projeto encaminhado &agrave; unidade vinculada para an&aacute;lise
                    e emiss&atilde;o de parecer t&eacute;cnico";

                    $tbProjetos->alterarSituacao($idPronac, '', $situacao, $providenciaTomada);
                }

                $tbAvaliacao->atualizarAvaliacaoPositiva($idPronac, $avaliacao['idTecnico'], $params['observacao']);

                $dados = array(
                    'NomeProjeto' => $dadosPreProjeto['NomeProjeto'],
                    'ResumoProjeto' => $dadosPreProjeto['ResumoDoProjeto'],
                    'DtInicioExecucao' => $dadosPreProjeto['DtInicioDeExecucao'],
                    'DtFimExecucao' => $dadosPreProjeto['DtFinalDeExecucao'],
                    'SolicitadoReal' => $preProjeto->valorTotalSolicitadoNaProposta($idPreProjeto),
                    'Logon' => $this->idUsuario
                );

                $where = array("IdPRONAC = ?" => $idPronac);
                $tbProjetos->update($dados, $where);

                parent::message($providenciaTomada, "{$this->moduleName}/analise/listarprojetos", "CONFIRM");
            }
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "{$this->moduleName}/analise/listarprojetos", "ERROR");
        }
    }

    private function enviarEmail($idProjeto, $Mensagem, $pronac = null)
    {
        $auth = Zend_Auth::getInstance();
        $tbTextoEmailDAO = new tbTextoEmail();

        $tbProjetos = new Projetos();
        $dadosProjeto = $tbProjetos->dadosProjetoDiligencia($idProjeto);

        $tbHistoricoEmailDAO = new tbHistoricoEmail();

        foreach ($dadosProjeto as $d) :
            # para Producao comentar linha abaixo e para teste descomente ela
            # $d->Email =   'salicweb@gmail.com';
            $email = trim(strtolower($d->Email));
            $mens = '<b>Pronac: ' . $d->pronac . ' - ' . $d->NomeProjeto . '<br>Proponente: ' . $d->Destinatario . '<br> </b>' . $Mensagem;
            $assunto = 'Pronac:  ' . $d->pronac . ' - Avaliação adequação do projeto';
            $assunto = utf8_decode($assunto);

            $enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens);

            $dados = array(
                'idProjeto' => $idProjeto,
                'idTextoemail' => new Zend_Db_Expr('NULL'),
                'iDAvaliacaoProposta' => new Zend_Db_Expr('NULL'),
                'DtEmail' => new Zend_Db_Expr('getdate()'),
                'stEstado' => 1,
                'idUsuario' => $auth->getIdentity()->usu_codigo,
            );

            $tbHistoricoEmailDAO->inserir($dados);
        endforeach;
    }

    public function redistribuiranaliseitemAction()
    {
        $params = $this->getRequest()->getParams();
        try {
            if (empty($params['idpronac'])) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            $vwPainelAvaliar = new Analise_Model_DbTable_vwProjetosAdequadosRealidadeExecucao();
            $where['idpronac = ?'] = $params['idpronac'];
            $projetos = $vwPainelAvaliar->projetos($where, array(), 0, 1);

            if (empty($projetos)) {
                throw new Exception("Projeto n&atilde;o dispon&iacute;vel para redistribui&ccedil;&atilde;o!");
            }

            $this->view->projeto = $projetos[0];

            if ($this->getRequest()->isPost()) {
                if (empty($params['idNovoTecnico']) || empty($params['tecnicoAtual'])) {
                    throw new Exception("Id do t&eacute;cnico &eacute; necess&aacute;rio para acessar essa funcionalidade.");
                }

                $dados = array(
                    'idTecnico' => $params['idNovoTecnico'],
                    'dtEncaminhamento' => new Zend_Db_Expr('GETDATE()'),
                );

                $where = array('idPronac = ?' => $params['idpronac'], 'idTecnico = ?' => $params['tecnicoAtual'], 'stEstado = ?' => true);

                $tbAvaliacao = new Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
                $tbAvaliacao->update($dados, $where);

                parent::message("An&aacute;lise redistribu&iacute;da com sucesso.", "/{$this->moduleName}/analise/listarprojetos", "CONFIRM");
            } else {

                $vw = new vwUsuariosOrgaosGrupos();
                $this->view->novosAnalistas = $vw->carregarTecnicosPorUnidadeEGrupo($this->codOrgao, 110);
            }
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/analise/listarprojetos", "ERROR");
        }
    }

    public function percentualCaptadoProjeto($idPronac)
    {
        if (empty($idPronac)) {
            return false;
        }

        $dbTableProjetos = new Projeto_Model_DbTable_Projetos();
        $valoresProjeto = $dbTableProjetos->obterValoresProjeto($idPronac);

        return $valoresProjeto['PercentualCaptado'];
    }

    private function carregarScriptsVue()
    {
        $gitTag = '?v=' . $this->view->gitTag();
        $this->view->headScript()->offsetSetFile(99, '/public/dist/js/manifest.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(100, '/public/dist/js/vendor.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(101, '/public/dist/js/proposta.js'. $gitTag, 'text/javascript', array('charset' => 'utf-8'));
    }
}
