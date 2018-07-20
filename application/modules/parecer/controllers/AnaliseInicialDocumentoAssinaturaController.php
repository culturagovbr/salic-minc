<?php

class Parecer_AnaliseInicialDocumentoAssinaturaController implements \MinC\Assinatura\Servico\IDocumentoAssinatura
{
    public $idPronac;

    private $post;

    const ID_TIPO_AGENTE_PARCERISTA = 1;
    
    public function __construct($post)
    {
        $this->post = $post;
    }

    public function iniciarFluxo()
    {
        if (!$this->idPronac) {
            throw new Exception("Identificador do Projeto não informado.");
        }
        
        $objTbProjetos = new Projeto_Model_DbTable_Projetos();
        $dadosProjeto = $objTbProjetos->findBy(array('IdPRONAC' => $this->idPronac));

        if (!$dadosProjeto) {
            throw new Exception("Projeto n&atilde;o encontrado.");
        }
        
        $fnVerificarProjetoAprovadoIN2017 = new fnVerificarProjetoAprovadoIN2017();
        $IN2017 = $fnVerificarProjetoAprovadoIN2017->verificar($this->idPronac);
        
        if (!$IN2017) {
            $secundariosAnalisados = $this->verificaSecundariosAnalisados($this->idPronac);
            $principalConsolidacao = $this->verificaPrimarioConsolidacao($this->idPronac);
            $pareceresProjeto = $this->verificaParecer($this->idPronac);
            $diligenciasProjeto = $this->projetoPossuiDiligenciasDiligencias($this->idPronac);
            
            if ((!$secundariosAnalisados) &&
                (!$principalConsolidacao) &&
                (!$pareceresProjeto) &&
                ($diligenciasProjeto)) {
                throw new Exception("N&atilde;o &eacute; poss&iacute;vel assinar esse projeto!");
            }
        }

        $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $isProjetoDisponivelParaAssinatura = $objModelDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
            $this->idPronac,
            Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL
        );

        if (!$isProjetoDisponivelParaAssinatura) {
            $auth = Zend_Auth::getInstance();
            $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;

            $parecer = new Parecer();
            $parecerTecnico = $parecer->getIdAtoAdministrativoParecerTecnico(
                $this->idPronac,
                self::ID_TIPO_AGENTE_PARCERISTA
            )->current();
            $idAtoAdministrativo = $parecerTecnico['idParecer'];
            
            $objModelDocumentoAssinatura = new Assinatura_Model_TbDocumentoAssinatura();
            $objModelDocumentoAssinatura->setIdPRONAC($this->idPronac);
            $objModelDocumentoAssinatura->setIdTipoDoAtoAdministrativo($idTipoDoAtoAdministrativo);
            $objModelDocumentoAssinatura->setIdAtoDeGestao($idAtoAdministrativo);
            $objModelDocumentoAssinatura->setConteudo($this->criarDocumento());
            $objModelDocumentoAssinatura->setIdCriadorDocumento($auth->getIdentity()->usu_codigo);
            $objModelDocumentoAssinatura->setCdSituacao(
                Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA
            );
            $objModelDocumentoAssinatura->setDtCriacao($objTbProjetos->getExpressionDate());
            $objModelDocumentoAssinatura->setStEstado(
                Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
            );

            $objDocumentoAssinatura = new \MinC\Assinatura\Servico\DocumentoAssinatura();
            $objDocumentoAssinatura->registrarDocumentoAssinatura($objModelDocumentoAssinatura);
        }
    }

    /**
     * @return string
     */
    public function criarDocumento()
    {
        $view = new Zend_View();
        $view->setScriptPath(__DIR__ . DIRECTORY_SEPARATOR . '../views/scripts/analise-inicial-documento-assinatura');

        $view->titulo = 'Parecer T&eacute;cnico do Projeto';
        
        $view->IdPRONAC = $this->idPronac;

        $objPlanoDistribuicaoProduto = new Projeto_Model_vwPlanoDeDistribuicaoProduto();
        $view->dadosProducaoProjeto = $objPlanoDistribuicaoProduto->obterProducaoProjeto(array(
            'IdPRONAC = ?' => $this->idPronac
        ));

        $grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $grupoAtivo->codOrgao;
        $objOrgao = new Orgaos();
        $view->nomeOrgao =  $objOrgao->pesquisarNomeOrgao($codOrgao)[0]['NomeOrgao'];
        
        $objProjeto = new Projeto_Model_DbTable_Projetos();
        $view->projeto = $objProjeto->findBy(array('IdPRONAC' => $this->idPronac));
        
        $objAgentes = new Agente_Model_DbTable_Agentes();
        $dadosAgente = $objAgentes->buscarFornecedor(array('a.CNPJCPF = ?' => $view->projeto['CgcCpf']));
        $arrayDadosAgente = $dadosAgente->current();

        $view->nomeAgente = (count($arrayDadosAgente) > 0) ? $arrayDadosAgente['nome'] : ' - ';
        
        $mapperArea = new Agente_Model_AreaMapper();
        $view->areaCultural = $mapperArea->findBy(array(
            'Codigo' => $view->projeto['Area']
        ));
        $objSegmentocultural = new Segmentocultural();
        $view->segmentoCultural = $objSegmentocultural->findBy(
            array(
                'Codigo' => $view->projeto['Segmento']
            )
        );
        
        $view->totaldivulgacao = "true";
        
        $projetos = new Projetos();

        $dadosProjeto = $projetos->assinarParecerTecnico($this->idPronac);
        
        $view->dadosEnquadramento = $dadosProjeto['enquadramento'];
        $view->dadosProdutos = $dadosProjeto['produtos'];
        $view->dadosDiligencias = $dadosProjeto['diligencias'];

        $fnVerificarProjetoAprovadoIN2017 = new fnVerificarProjetoAprovadoIN2017();
        $view->IN2017 = $fnVerificarProjetoAprovadoIN2017->verificar($this->idPronac);
        
        if ($view->IN2017) {
            $view->dadosAlcance = $dadosProjeto['alcance'][0];
        }
        
        $view->dadosParecer = $dadosProjeto['parecer'];
        
        return $view->render('documento-assinatura.phtml');
    }

    private function validacao20($idPronac, $stPrincipal, $somaValorProjeto)
    {
        $totalDivulgacao = 0;
        
        if ($somaValorProjeto > 0 && $stPrincipal == "1") {
            $analisarprojetoparecerController = new AnalisarprojetoparecerController();
            $totalDivulgacao = $analisarprojetoparecerController->validaRegra20Porcento($idPronac);
        }
        
        return $totalDivulgacao;
    }

    public function validaRegra20Porcento($idPronac)
    {
        $planilhaProjeto = new PlanilhaProjeto();
        $valorProjeto = $planilhaProjeto->somarPlanilhaProjeto($idPronac, 109);

        $valorProjetoDivulgacao = $planilhaProjeto->somarPlanilhaProjetoDivulgacao($idPronac, 109, null, null);

        $somaProjetoDivulgacao = $valorProjetoDivulgacao->soma ? $valorProjetoDivulgacao->soma : 0;

        $totalDivulgacao = false;

        if ($somaProjetoDivulgacao != 0) {
            $this->view->totalsugerido = $valorProjeto['soma'] ? $valorProjeto['soma'] : 0;
            $porcentValorProjeto = ($valorProjeto['soma'] * 0.20);
            $totalValorProjetoDivulgacao = $valorProjetoDivulgacao->soma;

            $valorRetirar = $totalValorProjetoDivulgacao - $porcentValorProjeto;
            $this->view->valorRetirar = $valorRetirar;

            if ($totalValorProjetoDivulgacao > $porcentValorProjeto) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
    

    private function validacao15($stPrincipal, $idPronac, $somaValorProjeto)
    {
        $verifica15porcento = 0;
        
        if ($stPrincipal == "1") {
            $V1 = '';
            $V2 = '';
            $V3 = '';
            $V4 = '';
            $V5 = '';
            $V6 = '';

            $tpPlanilha = 'CO';
            $planilhaProjeto = new PlanilhaProjeto();

            $whereTotalV1['PAP.IdPRONAC = ?'] = $idPronac;
            $whereTotalV1['PAP.FonteRecurso = ?'] = 109;
            $whereTotalV1['PAP.idPlanilhaItem <> ? '] = 206;

            $valorProjeto15 = $planilhaProjeto->somaDadosPlanilha($whereTotalV1);
            $V1 = $valorProjeto15['soma'];

            $whereTotalV2['PAP.IdPRONAC = ?'] = $idPronac;
            $whereTotalV2['PAP.FonteRecurso = ?'] = 109;
            $whereTotalV2['PAP.idEtapa = ? '] = 4;
            $whereTotalV2['PAP.idProduto = ?'] = 0;
            $whereTotalV2['PAP.idPlanilhaItem not in (?)'] = array(5249, 206, 1238);

            $valoracustosadministrativos = $planilhaProjeto->somaDadosPlanilha($whereTotalV2);
            $V2 = $valoracustosadministrativos['soma'];

            if ($V1 > 0 and $valoracustosadministrativos['soma'] < $somaValorProjeto) {
                $quinzecentoprojetoV3 = $V1 * 0.15;
                
                $verificacaonegativo = $valoracustosadministrativos['soma'] - $quinzecentoprojetoV3;
                
                if ($verificacaonegativo < 0) {
                    $verifica15porcento = 0;
                } else {
                    $valorretirar = $V1 - $verificacaonegativo;
                    $quinzecentovalorretirar = $valorretirar * 0.15;
                    $valorretirarplanilha = $valoracustosadministrativos['soma'] - $quinzecentovalorretirar;
                    $verifica15porcento = $valorretirarplanilha;
                }
            } else {
                $verifica15porcento = $valoracustosadministrativos['soma'];
            }
        }
        return $verifica15porcento;
    }

    private function verificaSecundariosAnalisados($idPronac)
    {
        $tbDistribuirParecerDAO = new tbDistribuirParecer();
        $dadosWhere["t.stEstado = ?"] = 0;
        $dadosWhere["t.FecharAnalise = ?"] = 0;
        $dadosWhere["t.TipoAnalise = ?"] = 3;
        $dadosWhere["p.Situacao IN ('B11', 'B14')"] = '';
        $dadosWhere["p.IdPRONAC = ?"] = $idPronac;
        $dadosWhere["t.stPrincipal = ?"] = 0;
        $dadosWhere["t.DtDevolucao is null"] = '';
        
        $SecundariosAtivos = $tbDistribuirParecerDAO->dadosParaDistribuir($dadosWhere);
        $secundariosCount = count($SecundariosAtivos);
        
        if ($secundariosCount > 0) {
            return false;
        } else {
            return true;
        }
    }

    private function verificaPrimarioConsolidacao($idPronac)
    {
        $enquadramentoDAO = new Admissibilidade_Model_Enquadramento();
        $buscaEnquadramento = $enquadramentoDAO->buscarDados($idPronac, null, false);
        
        return count($buscaEnquadramento);
    }

    private function verificaParecer($idPronac)
    {
        $parecerDAO	= new Parecer();
        $buscaParecer = $parecerDAO->buscarParecer(null, $idPronac);
        
        return count($buscaParecer);
    }

    private function projetoPossuiDiligenciasDiligencias($idPronac)
    {
        $tbDiligencia = new tbDiligencia();
        $rsDilig = $tbDiligencia->buscarDados($idPronac);
        
        return count($rsDilig);
    }

    private function verificaProdutoParecer($idPronac, $idProduto)
    {
        $tbAnaliseDeConteudoDAO = new Analisedeconteudo();
        $where['IdPRONAC = ?'] = $idPronac;
        $where['idProduto = ?'] = $idProduto;
        $where['ParecerDeConteudo = ?'] = '';
        $naoAnalisados = $tbAnaliseDeConteudoDAO->dadosAnaliseconteudo(null, $where);
        
        return count($naoAnalisados);
    }
}
