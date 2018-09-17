<?php

namespace Application\Modules\Projeto\Service\Assinatura;

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
            'cdSituacao' => \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
            'idAtoDeGestao' => $this->idAtoDeGestao,
            'stEstado' => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO,
        ]);

        $objDocumentoAssinatura = new \MinC\Assinatura\Servico\DocumentoAssinatura();
        $objDocumentoAssinatura->registrarDocumentoAssinatura($objModelDocumentoAssinatura);

        $objProjetos = new \Projetos();
        $objProjetos->alterarSituacao(
            $this->idPronac,
            null,
            \Projeto_Model_Situacao::PROJETO_HOMOLOGADO,
            'Projeto aguardando an&aacute;lise para homologa&ccedil;&atilde;o de execu&ccedil;&atilde;o'
        );

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
        if(!isset($this->idAtoDeGestao) || is_null($this->idAtoDeGestao || empty($this->idAtoDeGestao))) {
            throw new Exception("Identificador do ato de gest&atilde;o n&atilde;o informado.");
        }

        $view = new \Zend_View();
        $view->setScriptPath(
            __DIR__
            . DIRECTORY_SEPARATOR
            . 'template'
        );

        $view->titulo = 'Parecer de Homologa&ccedil;&atilde;o para Execu&ccedil;&atilde;o do Projeto';
        $view->IdPRONAC = $this->idPronac;

        $objProjeto = new \Projeto_Model_DbTable_Projetos();
        $view->projeto = $objProjeto->findBy(array('IdPRONAC' => $this->idPronac));

        $objAgentes = new \Agente_Model_DbTable_Agentes();
        $dadosAgente = $objAgentes->buscarFornecedor(array('a.CNPJCPF = ?' => $view->projeto['CgcCpf']));
        $arrayDadosAgente = $dadosAgente->current();

        $view->nomeAgente = (count($arrayDadosAgente) > 0) ? $arrayDadosAgente['nome'] : ' - ';

        $auth = \Zend_Auth::getInstance();
        $dadosUsuarioLogado = $auth->getIdentity();
        $orgaoSuperior = $dadosUsuarioLogado->usu_org_max_superior;

        $view->secretaria = 'Secretaria do Audiovisual - SAv';
        if((int)$orgaoSuperior == (int)\Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $view->secretaria = 'Secretaria de Fomento e Incentivo &agrave; Cultura - SEFIC';
        }

        $tbHomologacao = new \Projeto_Model_DbTable_TbHomologacao();
        $parecer = $tbHomologacao->buscar([
            'idPronac = ?' => $this->idPronac
        ])->current();


        switch ((string)$parecer->stDecisao) {
            case '1':
                $view->posicionamentoTecnico = 'Desfavor&aacute;vel';
                break;
            case '2':
                $view->posicionamentoTecnico = 'Favor&aacute;vel';
                break;
            default:
                $view->posicionamentoTecnico = 'N&atilde;o definido';
                break;
        }
        $view->parecer = $parecer->dsHomologacao;

        return $view->render('documento-assinatura.phtml');
    }
}
