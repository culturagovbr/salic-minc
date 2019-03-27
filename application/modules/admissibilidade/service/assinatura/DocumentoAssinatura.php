<?php

namespace Application\Modules\Admissibilidade\Service\Assinatura;

use Mockery\Exception;

class DocumentoAssinatura implements \MinC\Assinatura\Servico\IDocumentoAssinatura
{
    private $idPronac;
    private $idTipoDoAtoAdministrativo;
    private $idAtoDeGestao;

    private $dadosProjeto;

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

    public function iniciarFluxo() :int
    {
        if (!$this->idPronac) {
            throw new \Exception("Identificador do Projeto não informado.");
        }

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $this->dadosProjeto = $objTbProjetos->findBy(array('IdPRONAC' => $this->idPronac));

        if (!$this->dadosProjeto) {
            throw new \Exception("Projeto n&atilde;o encontrado.");
        }

        if ($this->dadosProjeto['Situacao'] != 'B02' && $this->dadosProjeto['Situacao'] != 'B03') {
            throw new \Exception("Situa&ccedil;&atilde;o do projeto inv&aacute;lida!");
        }

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $isProjetoDisponivelParaAssinatura = $objDbTableDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
            $this->idPronac,
            $this->idTipoDoAtoAdministrativo
        );

        if (!$isProjetoDisponivelParaAssinatura) {
            $auth = \Zend_Auth::getInstance();

            $enquadramento = new \Admissibilidade_Model_Enquadramento();
            $dadosEnquadramento = $enquadramento->obterEnquadramentoPorProjeto(
                $this->idPronac,
                $this->dadosProjeto['AnoProjeto'],
                $this->dadosProjeto['Sequencial']
            );

            $objModelDocumentoAssinatura = new \Assinatura_Model_TbDocumentoAssinatura();
            $objModelDocumentoAssinatura->setIdPRONAC($this->idPronac);
            $objModelDocumentoAssinatura->setIdTipoDoAtoAdministrativo($this->idTipoDoAtoAdministrativo);
            $objModelDocumentoAssinatura->setIdAtoDeGestao($dadosEnquadramento['IdEnquadramento']);
            $objModelDocumentoAssinatura->setConteudo($this->criarDocumento());
            $objModelDocumentoAssinatura->setIdCriadorDocumento($auth->getIdentity()->usu_codigo);
            $objModelDocumentoAssinatura->setCdSituacao(
                \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA
            );
            $objModelDocumentoAssinatura->setStEstado(
                \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
            );
            $objModelDocumentoAssinatura->setDtCriacao($objTbProjetos->getExpressionDate());

            $objDocumentoAssinatura = new \MinC\Assinatura\Servico\DocumentoAssinatura();
            $objDocumentoAssinatura->registrarDocumentoAssinatura($objModelDocumentoAssinatura);
        }

        $objProjeto = new \Projetos();
        $objProjeto->alterarSituacao(
            $this->idPronac,
            null,
            'B04',
            'Projeto em an&aacute;lise documental.'
        );

        $orgaoDestino = \Orgaos::ORGAO_SAV_DAP;
        $objOrgaos = new \Orgaos();
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($this->dadosProjeto['Orgao']);
        if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $orgaoDestino = \Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
        }
        $objTbProjetos->alterarOrgao($orgaoDestino, $this->idPronac);

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

        $view->titulo = 'Parecer T&eacute;cnico de Aprova&ccedil;&atilde;o Preliminar';

        $objPlanoDistribuicaoProduto = new \Projeto_Model_vwPlanoDeDistribuicaoProduto();
        $view->dadosProducaoProjeto = $objPlanoDistribuicaoProduto->obterProducaoProjeto(array(
            'IdPRONAC = ?' => $this->idPronac
        ));

        $view->IdPRONAC = $this->idPronac;

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
        $view->valoresProjeto = $objProjeto->obterValoresProjeto($this->idPronac);

        $objEnquadramento = new \Admissibilidade_Model_Enquadramento();
        $arrayPesquisa = array(
            'AnoProjeto' => $this->dadosProjeto['AnoProjeto'],
            'Sequencial' => $this->dadosProjeto['Sequencial'],
            'IdPRONAC' => $this->idPronac
        );

        $view->dadosEnquadramento = $objEnquadramento->findBy($arrayPesquisa);

        $objOrgaos = new \Orgaos();
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($this->dadosProjeto['Orgao']);

        $view->orgaoSuperior = $dadosOrgaoSuperior['Codigo'];

        return $view->render('documento-assinatura.phtml');
    }
}
