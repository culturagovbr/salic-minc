<?php

namespace MinC\Assinatura\Servico;
use MinC\Assinatura\Acao\IListaAcoesModulo;

/**
 * @var \Assinatura_Model_DbTable_TbAssinatura $dbTableTbAssinatura
 * @var \MinC\Assinatura\Model\Assinatura $viewModelAssinatura
 * @var \MinC\Assinatura\Acao\IListaAcoesModulo[] $listaAcoes
 */
class Assinatura implements IServico
{
    public $isEncaminharParaProximoAssinanteAoAssinar = true;
    public $viewModelAssinatura;
    private static $listaAcoesGerais = [];
    /**
     * @var IListaAcoesModulo IListaAcoesModulo
     */
    private $listaAcoesModulo = null;
    private $idTipoDoAtoAdministrativo;

    function __construct(array $dadosViewModelAssinatura)
    {
        if (!$dadosViewModelAssinatura['idTipoDoAto']) {
            throw new \Exception ("O Tipo do Ato Administrativo &eacute; obrigat&oacute;rio.");
        }

        if (empty(trim($dadosViewModelAssinatura['idPronac']))) {
            throw new \Exception ("O Identificador do projeto &eacute; obrigat&oacute;rio.");
        }

        $this->idTipoDoAtoAdministrativo = $dadosViewModelAssinatura['idTipoDoAto'];
        $this->isolarAcoesPorTipoDeAto();
        $this->viewModelAssinatura = new \MinC\Assinatura\Model\Assinatura($dadosViewModelAssinatura);
    }

    private function isolarAcoesPorTipoDeAto()
    {
        if (count(self::$listaAcoesGerais) > 0 && isset(self::$listaAcoesGerais[$this->idTipoDoAtoAdministrativo])) {
            $this->listaAcoesModulo = self::$listaAcoesGerais[$this->idTipoDoAtoAdministrativo];
        }
    }

    private function executarAcoes(string $interfaceDeAcao)
    {
        if(!is_null($this->listaAcoesModulo)) {
            foreach ($this->listaAcoesModulo->obterLista() as $acaoModulo) {
                /**
                 * @var \MinC\Assinatura\Acao\IAcao $acaoModulo
                 */
                if ($acaoModulo instanceof $interfaceDeAcao) {
                    $acaoModulo->executar($this->viewModelAssinatura);
                }
            }
        }
    }

    public static function definirAcoesGerais(\MinC\Assinatura\Acao\IListaAcoesGerais $listaAcoes)
    {
        if (count(self::$listaAcoesGerais) < 1) {
            self::$listaAcoesGerais = $listaAcoes->obterLista();
        }
    }

    public function assinarProjeto($post, $identidadeUsuarioLogado)
    {
        $modeloTbAssinatura = $this->viewModelAssinatura->modeloTbAssinatura;
        $modeloTbAtoAdministrativo = $this->viewModelAssinatura->modeloTbAtoAdministrativo;
        if (empty(trim($modeloTbAssinatura->getDsManifestacao()))) {
            throw new \Exception ("Campo \"De acordo do Assinante\" &eacute; de preenchimento obrigat&oacute;rio.");
        }

        $servicoAutenticacao = new \MinC\Assinatura\Servico\Autenticacao(
            $post,
            $identidadeUsuarioLogado
        );
        $metodoAutenticacao = $servicoAutenticacao->obterMetodoAutenticacao();

        if (!$metodoAutenticacao->autenticar()) {
            throw new \Exception ("Os dados utilizados para autentica&ccedil;&atilde;o s&atilde;o inv&aacute;lidos.");
        }

        $usuario = $metodoAutenticacao->obterInformacoesAssinante();
        $objTbAtoAdministrativo = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $dadosAtoAdministrativoAtual = $objTbAtoAdministrativo->obterAtoAdministrativoAtual(
            $modeloTbAtoAdministrativo->getIdTipoDoAto(),
            $modeloTbAtoAdministrativo->getIdPerfilDoAssinante(),
            $modeloTbAtoAdministrativo->getIdOrgaoDoAssinante()
        );

        if (!$dadosAtoAdministrativoAtual) {
            throw new \Exception ("Usu&aacute;rio sem autoriza&ccedil;&atilde;o para assinar o documento.");
        }

        $modeloTbAssinatura->setIdAssinante($usuario['usu_codigo']);
        $modeloTbAtoAdministrativo->setIdOrdemDaAssinatura($dadosAtoAdministrativoAtual['idOrdemDaAssinatura']);
        $modeloTbAtoAdministrativo->setIdAtoAdministrativo($dadosAtoAdministrativoAtual['idAtoAdministrativo']);

        $dbTableTbAssinatura = $this->viewModelAssinatura->dbTableTbAssinatura;
        $dbTableTbAssinatura->preencherModeloAssinatura([
            'idPronac' => $modeloTbAssinatura->getIdPronac(),
            'idAtoAdministrativo' => $dadosAtoAdministrativoAtual['idAtoAdministrativo'],
            'idDocumentoAssinatura' => $modeloTbAssinatura->getIdDocumentoAssinatura()
        ]);

        if ($dbTableTbAssinatura->isProjetoAssinado()) {
            throw new \Exception ("O documento j&aacute; foi assinado pelo usu&aacute;rio logado nesta fase atual.");
        }

        $dadosInclusaoAssinatura = [
            'idPronac' => $modeloTbAssinatura->getIdPronac(),
            'idAtoAdministrativo' => $modeloTbAssinatura->getIdAtoAdministrativo(),
            'dtAssinatura' => $objTbAtoAdministrativo->getExpressionDate(),
            'idAssinante' => $modeloTbAssinatura->getIdAssinante(),
            'dsManifestacao' => $modeloTbAssinatura->getDsManifestacao(),
            'idDocumentoAssinatura' => $modeloTbAssinatura->getIdDocumentoAssinatura()
        ];

        $dbTableTbAssinatura->inserir($dadosInclusaoAssinatura);
        $codigoOrgaoDestino = $objTbAtoAdministrativo->obterProximoOrgaoDeDestino(
            $modeloTbAtoAdministrativo->getIdTipoDoAto(),
            $modeloTbAtoAdministrativo->getIdOrdemDaAssinatura(),
            $modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante()
        );

        if ($codigoOrgaoDestino) {
            $this->encaminhar();
        } else {
            $quantidadeAssinaturasRealizadas = $dbTableTbAssinatura->obterQuantidadeAssinaturasRealizadas();
            $quantidadeMinimaAssinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
                $modeloTbAtoAdministrativo->getIdTipoDoAto(),
                $modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante()
            );
            
            if ($quantidadeAssinaturasRealizadas == $quantidadeMinimaAssinaturas) {
                $this->finalizar();
            }
        }

        $this->executarAcoes('\MinC\Assinatura\Acao\IAcaoAssinar');
    }

    /**
     * @throws \Exception
     * @uses \MinC\Assinatura\Model\Assinatura
     */
    public function encaminhar()
    {
        $modeloTbAtoAdministrativo = $this->viewModelAssinatura->modeloTbAtoAdministrativo;
        if (!$modeloTbAtoAdministrativo->getIdOrdemDaAssinatura()) {
            throw new \Exception("A fase atual do projeto n&atilde;o permite movimentar o projeto.");
        }

        $modeloTbAssinatura = $this->viewModelAssinatura->modeloTbAssinatura;
        $dbTableTbAssinatura = $this->viewModelAssinatura->dbTableTbAssinatura;
        $dbTableTbAssinatura->modeloTbAssinatura = $modeloTbAssinatura;
        if (!$dbTableTbAssinatura->isProjetoAssinado()) {
            throw new \Exception ("O documento precisa ser assinado para que consiga ser movimentado.");
        }

        $objTbAtoAdministrativo = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $codigoOrgaoDestino = $objTbAtoAdministrativo->obterProximoOrgaoDeDestino(
            $modeloTbAtoAdministrativo->getIdTipoDoAto(),
            $modeloTbAtoAdministrativo->getIdOrdemDaAssinatura(),
            $modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante()
        );

        if (!$codigoOrgaoDestino) {
            throw new \Exception("A fase atual do projeto n&atilde;o permite movimentar o projeto.");
        }

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $objTbProjetos->alterarOrgao(
            $codigoOrgaoDestino,
            $modeloTbAssinatura->getIdPronac()
        );

        $this->executarAcoes('\MinC\Assinatura\Acao\IAcaoEncaminhar');
    }

    public function devolver()
    {
        $modeloTbAssinatura = $this->viewModelAssinatura->modeloTbAssinatura;
        $modeloTbDespacho = $this->viewModelAssinatura->modeloTbDespacho;

        $objTbDepacho = new \Proposta_Model_DbTable_TbDespacho();
        $objTbDepacho->devolverProjetoEncaminhadoParaAssinatura(
            $modeloTbAssinatura->getIdPronac(),
            $modeloTbDespacho->getDespacho()
        );

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $data = [
            'cdSituacao' => \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA,
            'stEstado' => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO
        ];
        $where = [
            'idDocumentoAssinatura = ?' => $this->viewModelAssinatura->modeloTbAssinatura->getIdDocumentoAssinatura(),
        ];

        $objDbTableDocumentoAssinatura->update(
            $data,
            $where);

        $this->executarAcoes('\MinC\Assinatura\Acao\IAcaoDevolver');
    }

    public function finalizar()
    {
        $data = [
            'cdSituacao' => \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA
        ];
        $where = [
            'idDocumentoAssinatura = ?' => $this->viewModelAssinatura->modeloTbDocumentoAssinatura->getIdDocumentoAssinatura(),
        ];
        $documentoAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $documentoAssinaturaDbTable->update(
            $data,
            $where
        );

        $this->executarAcoes('\MinC\Assinatura\Acao\IAcaoFinalizar');
    }

}
