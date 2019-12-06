<?php

namespace Application\Modules\Parecer\Service;

class GerenciarParecer implements \MinC\Servico\IServicoRestZend
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
    }

    public function obterProdutos()
    {
        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $filtro = $this->request->getParam('filtro');

        $where = ['a.idOrgao = ?' => $this->idOrgao];
        if ($filtro == 'enviada_vinculada') {
            $where = ['a.idOrgaoOrigem = ?' => $this->idOrgao];
        }

        $produtos = $tbDistribuirParecer->obterPainelGerenciarParecer($filtro, $where);
        return \TratarArray::utf8EncodeArray($produtos);
    }

    public function inserirDistribuicaoProduto()
    {
        $params = $this->request->getParams();

        if (empty($params['idDistribuirParecer'])
            || empty($params['idPronac'])
            || empty($params['idProduto'])) {
            throw new \Exception("Dados obrigatórios não informados");
        }

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicao = $tbDistribuirParecer->findBy([
            "idDistribuirParecer = ?" => $params['idDistribuirParecer']
        ]);

        $resposta = $this->distribuirProduto($params, $distribuicao);
        if ($resposta
            && $distribuicao['stPrincipal'] == 1
            && $params['TipoAnalise'] == \Parecer_Model_TbDistribuirParecer::TIPO_ANALISE_PRODUTO_COMPLETO) {
            $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
            $tbDistribuirParecerMapper->prepararProjetoParaAnalise($params['idPronac']);
        }

        return $distribuicao;
    }

    public function inserirDistribuicaoTodosProdutosDoProjeto()
    {
        $params = $this->request->getParams();

        $whereDistribuicao = [
            "idPronac = ?" => $params['idPronac'],
            "idOrgao = ?" => $this->idOrgao,
            "stEstado = ?" => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO
        ];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicoes = $tbDistribuirParecer->findAll($whereDistribuicao);

        if (empty($distribuicoes)) {
            throw new \Exception("Nenhum produto encontrado!");
        }

        foreach ($distribuicoes as $distribuicao) {
            $this->distribuirProduto($params, $distribuicao);
        }

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        $tbDistribuirParecerMapper->prepararProjetoParaAnalise($params['idPronac']);
        return $distribuicoes;
    }

    private function distribuirProduto($params, $distribuicaoAtual)
    {
        $modelDistribuicao = new \Parecer_Model_TbDistribuirParecer($distribuicaoAtual);
        $modelDistribuicao->setIdUsuario($this->idUsuario);
        $modelDistribuicao->setObservacao($params['Observacao']);
        $modelDistribuicao->setSiAnalise($params['siAnalise']);
        $modelDistribuicao->setSiEncaminhamento($params['siEncaminhamento']);

        if (strlen($modelDistribuicao->getObservacao()) <= 11) {
            throw new \Exception("O campo observa&ccedil;&atilde;o deve ter no m&iacute;nimo 11 caracteres");
        }

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();

        if (!empty($params['idOrgao'])) {
            $modelDistribuicao->setIdOrgao($params['idOrgao']);
            $modelDistribuicao->setIdAgenteParecerista(null);
            return $tbDistribuirParecerMapper->encaminharProdutoParaVinculada($modelDistribuicao);
        }

        $modelDistribuicao->setIdAgenteParecerista($params['idAgenteParecerista']);
        return $tbDistribuirParecerMapper->distribuirProdutoParaParecerista($modelDistribuicao);
    }

    public function salvarValidacaoParecer()
    {
        $params = $this->request->getParams();

        if (empty($params['idDistribuirParecer']) || empty($params['siAnalise'])) {
            throw new \Exception("Dados obrigatórios não informados");
        }

        if ($params['tipoAnalise'] == \Parecer_Model_TbDistribuirParecer::TIPO_ANALISE_CUSTO_PRODUTO) {
            return $this->encaminharAposAnaliseComplementar($params);
        }

        $dados = [
            "siAnalise" => $params['siAnalise'],
            "FecharAnalise" => \Parecer_Model_TbDistribuirParecer::FECHAR_ANALISE_FECHADA,
            'DtRetorno' => \MinC_Db_Expr::date(),
        ];

        $whereUpdateDistribuirParecer = "idDistribuirParecer = " . $params['idDistribuirParecer'];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        return $tbDistribuirParecer->update($dados, $whereUpdateDistribuirParecer);
    }

    private function encaminharAposAnaliseComplementar($params)
    {
        $whereDistribuicaoAtual = ["idDistribuirParecer = ?" => $params['idDistribuirParecer']];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicaoAtual = $tbDistribuirParecer->findBy($whereDistribuicaoAtual);
        if (empty($distribuicaoAtual)) {
            throw new \Exception("Distribui&ccedil;&atilde;o n&atilde;o encontrada");
        }

        if ($distribuicaoAtual['stPrincipal'] == \Parecer_Model_TbDistribuirParecer::ST_PRODUTO_SECUNDARIO) {
            $this->devolverProdutoParaCoordenador($distribuicaoAtual);
        }

        $whereDistribuicaoPrincipal = [
            'IdPRONAC = ?' => $distribuicaoAtual['idPRONAC'],
            'stEstado = ?' => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
            'stPrincipal = ?' => \Parecer_Model_TbDistribuirParecer::ST_PRODUTO_PRINCIPAL,
        ];

        $distribuicaoPrincipal = $tbDistribuirParecer->findBy($whereDistribuicaoPrincipal);

        if ($this->isValorSugeridoAlterado($distribuicaoAtual['idPRONAC'])) {
            return $this->devolverProdutoPrincipalAoParecerista($distribuicaoPrincipal);
        }
        return $this->devolverProdutoParaCoordenador($distribuicaoPrincipal);
    }

    private function devolverProdutoPrincipalAoParecerista($distribuicao)
    {
        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        $agenteDistribuicaoOriginal = $tbDistribuirParecerMapper->obterAgenteAvaliacaoOriginal(
            $distribuicao['idPRONAC'],
            $distribuicao['idProduto'],
            $distribuicao['stPrincipal']
        );

        $distribuicao['idAgenteParecerista'] = $agenteDistribuicaoOriginal['idAgenteParecerista'];
        $distribuicao['idOrgaoOrigem'] = $agenteDistribuicaoOriginal['idOrgao'];

        $modelDistribuicao = new \Parecer_Model_TbDistribuirParecer($distribuicao);
        $modelDistribuicao->setIdOrgao($distribuicao['idOrgaoOrigem']);
        $modelDistribuicao->setSiAnalise(\Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE);
        $modelDistribuicao->setSiEncaminhamento(\TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA);
        $modelDistribuicao->setTipoAnalise(\Parecer_Model_TbDistribuirParecer::TIPO_ANALISE_PRODUTO_COMPLETO);
        $modelDistribuicao->setObservacao('Produto devolvido ao parecerista para nova an&aacute;lise ap&oacute;s an&aacute;lise complementar tendo em vista que houve altera&ccedil;&atilde;o de valor');

        $tbDistribuirParecerMapper->distribuirProdutoParaParecerista($modelDistribuicao);
        return $tbDistribuirParecerMapper->prepararProjetoParaAnalise($modelDistribuicao->getIdPRONAC());
    }

    private function devolverProdutoParaCoordenador($distribuicao)
    {
        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        $agenteDistribuicaoOriginal = $tbDistribuirParecerMapper->obterAgenteAvaliacaoOriginal(
            $distribuicao['idPRONAC'],
            $distribuicao['idProduto'],
            $distribuicao['stPrincipal']
        );

        $distribuicao['idAgenteParecerista'] = $agenteDistribuicaoOriginal['idAgenteParecerista'];
        $distribuicao['idOrgaoOrigem'] = $agenteDistribuicaoOriginal['idOrgao'];

        $modelDistribuicao = new \Parecer_Model_TbDistribuirParecer($distribuicao);
        $modelDistribuicao->setIdOrgao($distribuicao['idOrgaoOrigem']);
        $modelDistribuicao->setTipoAnalise(\Parecer_Model_TbDistribuirParecer::TIPO_ANALISE_PRODUTO_COMPLETO);
        $modelDistribuicao->setSiAnalise(\Parecer_Model_TbDistribuirParecer::SI_ANALISE_COMPLEMENTAR_DEVOLVIDO);
        $modelDistribuicao->setSiEncaminhamento(\TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA);
        $modelDistribuicao->setObservacao('Devolvido ao coordenador ap&oacute;s an&aacute;lise complementar');
        return $tbDistribuirParecerMapper->devolverProdutoParaCoordenador($modelDistribuicao);
    }

    private function isValorSugeridoAlterado($idPronac)
    {
        $parecerDbTable = new \Parecer_Model_DbTable_Parecer();
        $parecer = $parecerDbTable->findBy([
            'IdPRONAC' => $idPronac,
            'idTipoAgente' => 1,
            'stAtivo' => 1,
        ]);

        $planilhaprojeto = new \PlanilhaProjeto();
        $totalSugerido = $planilhaprojeto->somarPlanilhaProjeto($idPronac, 109)['soma'];

        return (round($parecer['SugeridoReal'], 2) != round($totalSugerido, 2));
    }
}
