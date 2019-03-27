<?php

namespace Application\Modules\Fiscalizacao\Service\Assinatura;

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

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $this->dadosProjeto = $objTbProjetos->findBy(array('IdPRONAC' => $idPronac));

        if (!$this->dadosProjeto) {
            throw new \Exception("Projeto n&atilde;o encontrado.");
        }

        $this->idPronac = $idPronac;
        $this->idTipoDoAtoAdministrativo = $idTipoDoAtoAdministrativo;
        $this->idAtoDeGestao = $idAtoDeGestao;
    }

    public function iniciarFluxo(): int
    {
        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $isProjetoDisponivelParaAssinatura = $objDbTableDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
            $this->idPronac,
            $this->idTipoDoAtoAdministrativo
        );
        if (!$isProjetoDisponivelParaAssinatura) {
            $auth = \Zend_Auth::getInstance();
            $objTbProjetos = new \Projeto_Model_DbTable_Projetos();

            $objModelDocumentoAssinatura = new \Assinatura_Model_TbDocumentoAssinatura();
            $objModelDocumentoAssinatura->setIdPRONAC($this->idPronac);
            $objModelDocumentoAssinatura->setIdTipoDoAtoAdministrativo($this->idTipoDoAtoAdministrativo);
            $objModelDocumentoAssinatura->setIdAtoDeGestao($this->idAtoDeGestao);
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

     /*   $dados = [
            'siCumprimentoObjeto' => \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_EM_AVALIACAO_TECNICO
        ];*/

       /* $whereObjeto = 'idCumprimentoObjeto = ' . $this->idAtoDeGestao;
        $tbCumprimentoObjeto = new \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $tbCumprimentoObjeto->alterar($dados, $whereObjeto);*/

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

        $view->titulo = 'Parecer de Fiscaliza&ccedil;&atilde;o';
        $view->IdPRONAC = $this->idPronac;

        $objAgentes = new \Agente_Model_DbTable_Agentes();
        $dadosAgente = $objAgentes->buscarFornecedor([
            'a.CNPJCPF = ?' => $this->dadosProjeto['CgcCpf']
        ]);

        $arrayDadosAgente = $dadosAgente->current();
        $view->nomeAgente = (count($arrayDadosAgente) > 0) ? $arrayDadosAgente['nome'] : ' - ';

        $tbFiscalizacao = new \Fiscalizacao_Model_DbTable_TbFiscalizacao();
        $view->projeto = $tbFiscalizacao->projetosFiscalizacaoConsultar([
            'Projetos.IdPRONAC = ?' => $this->idPronac,
            'tbFiscalizacao.idFiscalizacao = ?' => $this->idAtoDeGestao
        ])->current();

        $tbArquivoFiscalizacao = new \Fiscalizacao_Model_DbTable_TbArquivoFiscalizacao();
        if ($view->projeto->idFiscalizacao) {
            $view->arquivos = $tbArquivoFiscalizacao->buscarArquivo([
                'arqfis.idFiscalizacao = ?' => $this->idAtoDeGestao
            ]);
        }

        $tbRelatorioFiscalizacao = new \Fiscalizacao_Model_DbTable_TbRelatorioFiscalizacao();
        $view->relatorio = $tbRelatorioFiscalizacao->buscaRelatorioFiscalizacao($this->idAtoDeGestao);

        $objOrgaos = new \Orgaos();
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($this->dadosProjeto['Orgao']);

        $view->orgaoSuperior = $dadosOrgaoSuperior['Codigo'];

        return $view->render('documento-assinatura.phtml');
    }

    public function obterProjetoDisponivelParaAssinatura()
    {
        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        return $objDbTableDocumentoAssinatura->obterProjetoDisponivelParaAssinatura(
            $this->idPronac,
            $this->idTipoDoAtoAdministrativo
        );
    }
}
