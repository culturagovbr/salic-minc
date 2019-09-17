<?php

namespace Application\Modules\Parecer\Service;

class DistribuicaoParecer implements \MinC\Servico\IServicoRestZend
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    private $idUsuario;
    private $idOrgao;
    private $idGrupo;
    private $idAgente;
    private $auth;

    const ID_ATO_ADMINISTRATIVO = \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;


    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->auth = \Zend_Auth::getInstance()->getIdentity();
        $this->idUsuario = $this->auth->usu_codigo;

        $grupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        $this->idOrgao = $grupoAtivo->codOrgao;
        $this->idGrupo = $grupoAtivo->codGrupo;

        $tbUsuario = new \Autenticacao_Model_DbTable_Usuario();
        $usuario = $tbUsuario->getIdUsuario($this->idUsuario);
        $this->idAgente = $usuario['idagente'];

        if (empty($this->idAgente)) {
            throw new \Exception("Agente n&atilde;o cadastrado");
        }
    }

    public function solicitarReanaliseParecerista()
    {
        $params = $this->request->getParams();

        $modelDistribuicao = $this->tratarDadosRequisicao($params);

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        $resposta = $tbDistribuirParecerMapper->solicitarReanaliseParecerista($modelDistribuicao);
        if ($resposta && $modelDistribuicao->getStPrincipal() == 1) {
            $tbDistribuirParecerMapper->distribuirProdutoParaParecerista($modelDistribuicao);
            $tbDistribuirParecerMapper->prepararProjetoParaAnalise($modelDistribuicao->getIdPRONAC());
        }

        return $resposta;
    }

    public function devolverProdutoParaSecult()
    {
        $params = $this->request->getParams();

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        $resposta = $tbDistribuirParecerMapper->devolverProdutoParaSecult($this->tratarDadosRequisicao($params));

        if ($resposta) {
            $providenciaTomada = 'An&aacute;lise t&eacute;cnica conclu&iacute;da';

            $projetos = new \Projetos();
            $projetos->alterarSituacao(
                $params['idPronac'],
                null,
                \Projeto_Model_Situacao::PARECER_TECNICO_EMITIDO,
                $providenciaTomada
            );
        }
        return $resposta;
    }

    private function tratarDadosRequisicao($params)
    {
        if (empty($params['idDistribuirParecer'])
            || empty($params['idPronac'])
            || empty($params['idProduto'])
        ) {
            throw new \Exception("Dados obrigatórios não informados");
        }

        $whereDistribuicaoAtual = ["idDistribuirParecer = ?" => $params['idDistribuirParecer']];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicao = $tbDistribuirParecer->findBy($whereDistribuicaoAtual);

        $modelDistribuicao = new \Parecer_Model_TbDistribuirParecer($distribuicao);
        $modelDistribuicao->setIdUsuario($this->idUsuario);
        $modelDistribuicao->setObservacao($params['Observacao']);
        $modelDistribuicao->tratarObservacaoTextoRico();
        $modelDistribuicao->setSiAnalise($params['siAnalise']);
        $modelDistribuicao->setSiEncaminhamento($params['siEncaminhamento']);
        $modelDistribuicao->setTipoAnalise($params['TipoAnalise']);

        if (strlen($modelDistribuicao->getObservacao()) < 10) {
            throw new \Exception("O campo observa&ccedil;&atilde;o deve ter no m&iacute;nimo 10 caracteres");
        }

        return $modelDistribuicao;
    }

    public function solicitarAnaliseComplementarVinculada()
    {
        $params = $this->request->getParams();

        if (empty($params['idOrgao'])) {
            throw new \Exception("Identificador da unidade destino &eacute; obrigat&oacute;rio");
        }

        $modelDistribuicao = $this->tratarDadosRequisicao($params);
        $modelDistribuicao->setIdOrgao($params['idOrgao']);
//        $modelDistribuicao->setTipoAnalise($modelDistribuicao::TIPO_ANALISE_CUSTO_PRODUTO);
//        $modelDistribuicao->setSiAnalise($modelDistribuicao::SI_ANALISE_COMPLEMENTAR_DEVOLVIDO);
        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        return $tbDistribuirParecerMapper->encaminharProdutoParaVinculada($modelDistribuicao);
    }
}
