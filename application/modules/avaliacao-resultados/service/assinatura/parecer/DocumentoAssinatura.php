<?php

namespace Application\Modules\AvaliacaoResultados\Service\Assinatura\Parecer;

/* use Mockery\Exception; */

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

    public function iniciarFluxo() :int
    {
        $auth = \Zend_Auth::getInstance();
        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();

        $objModelDocumentoAssinatura = new \Assinatura_Model_TbDocumentoAssinatura([
            'IdPRONAC' => $this->idPronac,
            'idTipoDoAtoAdministrativo' => $this->idTipoDoAtoAdministrativo,
            'conteudo' => $this->criarDocumento(),
            'dt_criacao' => $objDbTableDocumentoAssinatura->getExpressionDate(),
            'idCriadorDocumento' => $auth->getIdentity()->usu_codigo,
            'cdSituacao' => (int)\Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
            'idAtoDeGestao' => $this->idAtoDeGestao,
            'stEstado' => (int)\Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO,
        ]);

        $objDocumentoAssinatura = new \MinC\Assinatura\Servico\DocumentoAssinatura();
        $objDocumentoAssinatura->registrarDocumentoAssinatura($objModelDocumentoAssinatura);

        return (int)$objDbTableDocumentoAssinatura->getIdDocumentoAssinatura(
            $this->idPronac,
            $this->idTipoDoAtoAdministrativo
        );
    }

    public function criarDocumento()
    {
        /* if(!isset($this->idAtoDeGestao) || is_null($this->idAtoDeGestao || empty($this->idAtoDeGestao))) { */
        /*     throw new \Exception("Identificador do ato de gest&atilde;o n&atilde;o informado."); */
        /* } */

        $view = new \Zend_View();
        $view->setScriptPath(
            __DIR__
            . DIRECTORY_SEPARATOR
            . 'template'
        );
        /** ============== Titulo do Documento ========================================== ===*/

        $view->titulo = 'Parecer T&eacute;cnico de Avalia&ccedil;&atilde;o de Resultado';

        /** =============== Consulta DB para aquisição das informações do parecer Tecnico ================= */

        $vwResultadoDaAvaliacaoFinanceira = new \AvaliacaoResultados_Model_DbTable_vwResultadoDaAvaliacaoFinanceira();
        $dadosAvaliacaoFinanceira = $vwResultadoDaAvaliacaoFinanceira->buscarConsolidacaoComprovantes($this->idPronac);
        $dadosAvaliacaoFinanceira = $dadosAvaliacaoFinanceira->toArray();

        $projeto = new \Projetos();
        $dadosProjeto = $projeto->buscar([
            'idPronac = ?' => $this->idPronac
        ]);
        $dadosProjeto = $dadosProjeto->toArray()[0];

        $proponente = new \ProponenteDAO();
        $dadosProponente = $proponente->buscarDadosProponente($this->idPronac);
        $dadosProponente = (array)$dadosProponente[0];

        $tbAvaliacaoFinanceira = new \AvaliacaoResultados_Model_DbTable_tbAvaliacaoFinanceira();
        $where = [
            'idPronac' => $this->idPronac
        ];
        $dadosParecer = $tbAvaliacaoFinanceira->findBy($where);
        $dadosParecer = ($dadosParecer) ?: new \stdClass();

        /** ============= Fim da consulta ================ */

        /** Carga na view */
        $view->parecer = $dadosParecer;
        $view->projeto = $dadosProjeto;
        $view->consolidacao = $dadosAvaliacaoFinanceira;
        $view->proponente = $dadosProponente;

        /** === Adequação do campo "select" da manifestação do tecnico === */

        switch ((string)$dadosParecer['siManifestacao']) {
            case 'R':
                $view->posicionamentoTecnico = 'Reprova&ccedil;&atilde;o';
                break;
            case 'A':
                $view->posicionamentoTecnico = 'Aprova&ccedil;&atilde;o';
                break;
            case 'P':
                $view->posicionamentoTecnico = 'Aprova&ccedil;&atilde;o com Ressalva';
                break;
        };

        return $view->render('documento-assinatura.phtml');
    }
}
