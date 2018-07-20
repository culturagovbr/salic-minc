<?php

class Parecer_AnaliseCnicDocumentoAssinaturaController implements \MinC\Assinatura\Servico\IDocumentoAssinatura
{
    public $idPronac;

    private $post;

    const ID_TIPO_AGENTE_COMPONENTE_CNIC = 6;
    
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
        
        $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $isProjetoDisponivelParaAssinatura = $objModelDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
            $this->idPronac,
            Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_CNIC
        );

        if (!$isProjetoDisponivelParaAssinatura) {
            $auth = Zend_Auth::getInstance();
            $idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_CNIC;

            $parecer = new Parecer();
            $idAtoAdministrativo = $parecer->getIdAtoAdministrativoParecerTecnico($this->idPronac, self::ID_TIPO_AGENTE_COMPONENTE_CNIC)[0]['idParecer'];
            
            $objModelDocumentoAssinatura = new Assinatura_Model_TbDocumentoAssinatura();
            $objModelDocumentoAssinatura->setIdPRONAC($this->idPronac);
            $objModelDocumentoAssinatura->setIdTipoDoAtoAdministrativo($idTipoDoAtoAdministrativo);
            $objModelDocumentoAssinatura->setIdAtoDeGestao($idAtoAdministrativo);
            $objModelDocumentoAssinatura->setConteudo($this->criarDocumento());
            $objModelDocumentoAssinatura->setIdCriadorDocumento($auth->getIdentity()->usu_codigo);
            $objModelDocumentoAssinatura->setCdSituacao(Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA);
            $objModelDocumentoAssinatura->setDtCriacao($objTbProjetos->getExpressionDate());
            $objModelDocumentoAssinatura->setStEstado(Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);

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
        $view->setScriptPath(__DIR__ . DIRECTORY_SEPARATOR . '../views/scripts/analise-cnic-documento-assinatura');

        $view->titulo = 'Aprecia&ccedil;&atilde;o do Comiss&aacute;rio Relator';
        
        $view->IdPRONAC = $this->idPronac;

        $objPlanoDistribuicaoProduto = new Projeto_Model_vwPlanoDeDistribuicaoProduto();
        $view->dadosProducaoProjeto = $objPlanoDistribuicaoProduto->obterProducaoProjeto(array(
            'IdPRONAC = ?' => $this->idPronac
        ));

        $grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $grupoAtivo->codOrgao;
        $objOrgao = new Orgaos();
        $view->nomeOrgao =  'Comiss&atilde;o Nacionl de Incentivo &agrave Cultura';
        
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

        $dadosProjeto = $projetos->assinarApreciacaoCnic($this->idPronac);
        
        $view->dadosEnquadramento = $dadosProjeto['enquadramento'];
        $view->dadosProdutos = $dadosProjeto['produtos'];
        $view->dadosDiligencias = $dadosProjeto['diligencias'];
        $view->IN2017 = $projetos->verificarIN2017($this->idPronac);
        
        $view->dadosParecer = $dadosProjeto['parecer'];
        
        return $view->render('documento-assinatura.phtml');
    }
}
