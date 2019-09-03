<?php

namespace Application\Modules\Parecer\Service\Assinatura\AnaliseInicial;

use Mockery\Exception;

class DocumentoAssinatura implements \MinC\Assinatura\Servico\IDocumentoAssinatura
{
    private $idPronac;
    private $idTipoDoAtoAdministrativo;
    private $idAtoDeGestao;

    public function __construct(
        $idPronac,
        $idTipoDoAtoAdministrativo,
        $idAtoDeGestao = null
    )
    {
        if (!isset($idPronac) || empty($idPronac)) {
            throw new \Exception("Identificador do projeto n&atilde;o informado");
        }

        if (!isset($idTipoDoAtoAdministrativo) || empty($idTipoDoAtoAdministrativo)) {
            throw new \Exception("Identificador do Tipo do Ato Administrativo n&atilde;o informado");
        }

        $this->idPronac = $idPronac;
        $this->idTipoDoAtoAdministrativo = $idTipoDoAtoAdministrativo;
        $this->idAtoDeGestao = $idAtoDeGestao;
    }

    public function iniciarFluxo(): int
    {
        if (!$this->idPronac) {
            throw new \Exception("Identificador do Projeto n&atilde;o informado.");
        }

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $dadosProjeto = $objTbProjetos->findBy(array('IdPRONAC' => $this->idPronac));

        if (!$dadosProjeto) {
            throw new \Exception("Projeto n&atilde;o encontrado.");
        }

        $objModelDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $isProjetoDisponivelParaAssinatura = $objModelDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
            $this->idPronac,
            \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL
        );

        if (!$isProjetoDisponivelParaAssinatura) {
            $auth = \Zend_Auth::getInstance();
            $objModelDocumentoAssinatura = new \Assinatura_Model_TbDocumentoAssinatura([
                'IdPRONAC' => $this->idPronac,
                'idTipoDoAtoAdministrativo' => $this->idTipoDoAtoAdministrativo,
                'conteudo' => $this->criarDocumento(),
                'dt_criacao' => $objTbProjetos->getExpressionDate(),
                'idCriadorDocumento' => $auth->getIdentity()->usu_codigo,
                'cdSituacao' => \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                'idAtoDeGestao' => $this->idAtoDeGestao,
                'stEstado' => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO,
            ]);

            $objDocumentoAssinatura = new \MinC\Assinatura\Servico\DocumentoAssinatura();
            $objDocumentoAssinatura->registrarDocumentoAssinatura($objModelDocumentoAssinatura);

        }

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        return (int)$objDbTableDocumentoAssinatura->getIdDocumentoAssinatura(
            $this->idPronac,
            $this->idTipoDoAtoAdministrativo
        );
    }

    /**
     * @return string
     */
    public function criarDocumento()
    {
        $view = new \Zend_View();
        $view->setScriptPath(
            __DIR__
            . DIRECTORY_SEPARATOR
            . 'template'
        );

        $view->titulo = 'Parecer T&eacute;cnico do Projeto';
        $view->IdPRONAC = $this->idPronac;
        $objPlanoDistribuicaoProduto = new \Projeto_Model_vwPlanoDeDistribuicaoProduto();
        $view->dadosProducaoProjeto = $objPlanoDistribuicaoProduto->obterProducaoProjeto(array(
            'IdPRONAC = ?' => $this->idPronac
        ));

        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $grupoAtivo->codOrgao;

        $objOrgao = new \Orgaos();
        $resultOrgao = $objOrgao->codigoOrgaoSuperior($codOrgao);
        $view->nomeOrgao = $resultOrgao[0]['Sigla'];
        $view->orgaoSuperior = $resultOrgao[0]['Superior'];

        $objProjeto = new \Projeto_Model_DbTable_Projetos();
        $view->projeto = $objProjeto->findBy(array('IdPRONAC' => $this->idPronac));

        $objAgentes = new \Agente_Model_DbTable_Agentes();
        $dadosAgente = $objAgentes->buscarFornecedor(array('a.CNPJCPF = ?' => $view->projeto['CgcCpf']));
        $arrayDadosAgente = $dadosAgente->current();

        $view->nomeAgente = (count($arrayDadosAgente) > 0) ? $arrayDadosAgente['nome'] : ' - ';

        $mapperArea = new \Agente_Model_AreaMapper();
        $view->areaCultural = $mapperArea->findBy(array(
            'Codigo' => $view->projeto['Area']
        ));
        $objSegmentocultural = new \Segmentocultural();
        $view->segmentoCultural = $objSegmentocultural->findBy(
            array(
                'Codigo' => $view->projeto['Segmento']
            )
        );

        $view->totaldivulgacao = "true";
        $projetos = new \Projetos();
        $dadosProjeto = $projetos->assinarParecerTecnico($this->idPronac);
        $view->dadosEnquadramento = $dadosProjeto['enquadramento'];

        $view->dadosParecer = $dadosProjeto['parecer'];

        return $view->render('documento-assinatura.phtml');
    }
}
