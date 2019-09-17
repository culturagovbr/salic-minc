<?php

namespace Application\Modules\Parecer\Service;

class AnaliseInicial implements \MinC\Servico\IServicoRestZend
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
    private $distribuicao;

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

    private function obterDistribuicao($idPronac, $idProduto)
    {
        if (empty($idPronac) || empty($idProduto)) {
            return [];
        }

        $whereProduto = array();
        $whereProduto['idPRONAC = ?'] = $idPronac;
        $whereProduto['idProduto = ?'] = $idProduto;
        $whereProduto["stEstado = ?"] = 0;
        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $this->distribuicao = $tbDistribuirParecer->buscar($whereProduto)->current()->toArray();
    }

    private function isPermitidoAvaliar($idPronac, $idProduto)
    {
        if (empty($idPronac)
            || empty($idProduto)
            || $this->idGrupo != \Autenticacao_Model_Grupos::PARECERISTA) {
            return false;
        }

        $this->obterDistribuicao($idPronac, $idProduto);

        return ($this->idAgente == $this->distribuicao['idAgenteParecerista']);
    }

    public function index()
    {
        $projeto = new \Projetos();
        $resp = $projeto->buscaProjetosProdutosParaAnalise(
            [
                'distribuirParecer.idAgenteParecerista = ?' => $this->idAgente,
                'distribuirParecer.idOrgao = ?' => $this->idOrgao,
                'distribuirParecer.siAnalise in (?)' => [
                    \Parecer_Model_TbDistribuirParecer::SI_ANALISE_AGUARDANDO_ANALISE,
                    \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE,
                    \Parecer_Model_TbDistribuirParecer::SI_ANALISE_ANALISADO,
                    \Parecer_Model_TbDistribuirParecer::SI_ANALISE_FINALIZADA,
                ],
                'distribuirParecer.siEncaminhamento = ?' => \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
            ]
        )->toArray();

        foreach ($resp as &$produto) {
            $produto['stDiligencia'] = $this->definirStatusDiligencia($produto);
            $produto['diasEmDiligencia'] = $this->obterTempoDiligencia($produto);
            $produto['diasEmAvaliacao'] = $this->obterTempoRestanteDeAvaliacao($produto);
        }

        return \TratarArray::utf8EncodeArray($resp);
    }

    public function get()
    {
        $id = $this->request->getParam('id');
        $idPronac = $this->request->getParam('idPronac');

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $produto = $tbDistribuirParecer->buscaProjetosProdutosParaAnalise(
            [
                'distribuirParecer.idProduto = ?' => $id,
                'projeto.IdPRONAC = ?' => $idPronac,
            ]
        )->current();

        if ($produto) {
            $produto = $produto->toArray();
            $produto['stDiligencia'] = $this->definirStatusDiligencia($produto);
            $produto['diasEmDiligencia'] = $this->obterTempoDiligencia($produto);
            $produto['diasEmAvaliacao'] = $this->obterTempoRestanteDeAvaliacao($produto);

            if ($produto['stPrincipal']
                && (
                    $produto['siAnalise'] == \Parecer_Model_TbDistribuirParecer::SI_ANALISE_ANALISADO
                    || $produto['siAnalise'] == \Parecer_Model_TbDistribuirParecer::SI_ANALISE_VALIDADO)) {
                $produto['idDocumentoAssinatura'] = $this->getIdDocumentoAssinatura($idPronac);
            }
        }
        return \TratarArray::utf8EncodeArray($produto);

    }

    public function finalizarParecer()
    {
        try {
            $idDistribuirParecer = $this->request->getParam("idDistribuirParecer");
            $idPronac = $this->request->getParam("idPronac");
            $idProduto = $this->request->getParam("idProduto");

            if (empty($idDistribuirParecer)) {
                throw new \Exception("ID da distribui&ccedil;&atilde;o n&atilde;o informado!");
            }

            if (!$this->isPermitidoAvaliar($idPronac, $idProduto)) {
                throw new \Exception('Você não tem permissão para analisar');
            }

            $dadosWhere = [];
            $dadosWhere["t.idDistribuirParecer = ?"] = $idDistribuirParecer;
            $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
            $distribuicao = $tbDistribuirParecer->dadosParaDistribuir($dadosWhere)->current();
            $isProdutoPrincipal = $distribuicao->stPrincipal == 1;

            if ($distribuicao->idAgenteParecerista != $this->idAgente) {
                throw new \Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para finalizar esse parecer!");
            }

            if ($this->isConteudoNaoAnalisado($distribuicao->IdPRONAC, $distribuicao->idProduto)) {
                throw new \Exception("Conte&uacute;do ainda n&atilde;o analisado!");
            }

            if ($this->isProdutoComDiligenciaAberta($distribuicao->IdPRONAC, $distribuicao->idProduto)) {
                throw new \Exception("Existe dilig&ecirc;ncia aberta aguardando resposta!");
            }

            if ($this->isItemDeCustosNaoAvaliados($distribuicao->IdPRONAC, $distribuicao->idProduto)) {
                throw new \Exception("An&aacute;lise de custos: existem itens obrigat&oacute;rios da planilha que não foram avaliados!");
            }

            $dados = [
                'idAgenteParecerista' => $distribuicao->idAgenteParecerista,
                'idOrgaoOrigem' => $distribuicao->idOrgaoOrigem,
                'DtDistribuicao' => $distribuicao->DtDistribuicao,
                'FecharAnalise' => $distribuicao->FecharAnalise,
                'TipoAnalise' => $distribuicao->TipoAnalise,
                'stPrincipal' => $distribuicao->stPrincipal,
                'idProduto' => $distribuicao->idProduto,
                'idPRONAC' => $distribuicao->IdPRONAC,
                'idOrgao' => $distribuicao->idOrgao,
                'DtEnvio' => $distribuicao->DtEnvio,
                'idUsuario' => $this->idUsuario,
                'stDiligenciado' => null,
                'DtRetorno' => null,
                'Observacao' => 'Produto analisado',
                'DtDevolucao' => \MinC_Db_Expr::date(),
                'stEstado' => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
                'siAnalise' => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_VALIDACAO,
                'siEncaminhamento' => \TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA
            ];


            if ($this->isPareceristaComProdutoPrincipal($distribuicao->IdPRONAC, $distribuicao->idAgenteParecerista)
                && $distribuicao->TipoAnalise == \Parecer_Model_TbDistribuirParecer::TIPO_ANALISE_PRODUTO_COMPLETO) {
                $dados['siEncaminhamento'] = \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA;
                $dados['siAnalise'] = \Parecer_Model_TbDistribuirParecer::SI_ANALISE_ANALISADO;
                $dados['DtDevolucao'] = null;
            }

            if ($this->isParecerComAssinatura($distribuicao)) {

                if ($this->isProdutosSecundariosNaoAnalisados($distribuicao->IdPRONAC)) {
                    throw new \Exception("Existem produtos secund&aacute;rios aguardando an&aacute;lise!");
                }

                if (!$this->isProjetoSemParecer($distribuicao->IdPRONAC)) {
                    throw new \Exception("A consolida&ccedil;&atilde;o do projeto &eacute; obrigat&oacute;ria");
                }

                $this->iniciarAssinatura($distribuicao->IdPRONAC);
            }

            $this->finalizarProduto($idDistribuirParecer, $dados);

            return $distribuicao;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function isParecerComAssinatura($distribuicao)
    {
        return $distribuicao->stPrincipal == 1
            && $distribuicao->TipoAnalise == \Parecer_Model_TbDistribuirParecer::TIPO_ANALISE_PRODUTO_COMPLETO;
    }

    private function finalizarProduto($idDistribuirParecer, $dados)
    {
        try {
            $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
            $tbDistribuirParecer->getAdapter()->beginTransaction();

            $where = [];
            $where['idDistribuirParecer = ?'] = $idDistribuirParecer;
            $tbDistribuirParecer->alterar([
                'stEstado' => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_INATIVO
            ], $where);

            $tbDistribuirParecer->inserir($dados);
            $tbDistribuirParecer->getAdapter()->commit();

        } catch (\Zend_Db_Exception $e) {
            $tbDistribuirParecer->getAdapter()->rollBack();
            throw $e;
        }

    }

    public function obterOutrosProdutosDoProjeto()
    {
        $idProduto = (int)$this->request->getParam('id');
        $idPronac = (int)$this->request->getParam('idPronac');

        if (empty($idPronac) || empty($idProduto)) {
            throw new \Exception("Dados obrigat&oacute;rios n&atilde;o informados");
        }

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $projetos = $tbDistribuirParecer->buscaProjetosProdutosParaAnalise(
            [
                'distribuirParecer.idProduto <> ?' => $idProduto,
                'projeto.IdPRONAC = ?' => $idPronac,
            ]
        );

        return $projetos ? $projetos->toArray() : [];

    }

    private function isProdutoComDiligenciaAberta($idPronac, $idProduto)
    {
        $where = [
            'idPronac = ?' => $idPronac,
            'idProduto = ?' => $idProduto,
            'DtResposta IS NULL' => ''
        ];

        $diligenciaDbTable = new \Diligencia_Model_DbTable_TbDiligencia();
        $diligencia = $diligenciaDbTable->findBy($where);

        return !empty($diligencia);
    }

    private function isConteudoNaoAnalisado($idPronac, $idProduto)
    {
        $tbAnaliseDeConteudoDAO = new \Parecer_Model_DbTable_TbAnaliseDeConteudo();
        $where = [
            'IdPRONAC = ?' => $idPronac,
            'idProduto = ?' => $idProduto
        ];
        $parecerDeConteudo = $tbAnaliseDeConteudoDAO->dadosAnaliseconteudo(null, $where)
            ->current()['ParecerDeConteudo'];

        return (strlen(trim($parecerDeConteudo)) == 0);
    }

    private function getProdutosSecundariosNaoAnalisados($idPronac)
    {
        $where = [
            "IdPRONAC = ?" => $idPronac,
            "stEstado = ?" => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
            "stPrincipal = ?" => 0,
            "siEncaminhamento = ?" => \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
            "siAnalise in (?)" => [
                \Parecer_Model_TbDistribuirParecer::SI_ANALISE_AGUARDANDO_ANALISE,
                \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE
            ],
        ];

        $tbDistribuirParecerDAO = new \Parecer_Model_DbTable_TbDistribuirParecer();
        return $tbDistribuirParecerDAO->findAll($where);
    }

    private function isProdutosSecundariosNaoAnalisados($idPronac)
    {
        $produtosSecundariosAtivos = $this->getProdutosSecundariosNaoAnalisados($idPronac);

        return (count($produtosSecundariosAtivos) > 0);
    }

    private function isPareceristaComProdutoPrincipal($idPronac, $idAgenteParecerista)
    {
        $where = [
            "idPronac = ?" => $idPronac,
            "idAgenteParecerista = ?" => $idAgenteParecerista,
            "siEncaminhamento = ?" => \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
            "stEstado = ?" => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
            "stPrincipal = ?" => 1
        ];

        $tbDistribuirParecerDAO = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $produtoPrincipal = $tbDistribuirParecerDAO->findBy($where);

        return !empty($produtoPrincipal);
    }

    private function isItemDeCustosNaoAvaliados($idPronac, $idProduto)
    {
        $where = [
            'IdPRONAC = ?' => $idPronac,
            'idProduto = ?' => $idProduto,
            'stCustoPraticado = ?' => 1,
            'Justificativa = \'\'' => '',
        ];

        $planilhaProjeto = new \PlanilhaProjeto();
        $itensNaoAvaliados = $planilhaProjeto->findAll($where);
        return (count($itensNaoAvaliados) > 0);
    }

    private function isProjetoSemParecer($idPronac)
    {
        $where = [
            'IdPRONAC' => $idPronac,
            'idTipoAgente' => 1,
            'stAtivo' => 1,
            'ResumoParecer IS NOT NULL',
            'Logon' => $this->idUsuario
        ];

        $parecerDbTable = new \Parecer_Model_DbTable_Parecer();
        return empty($parecerDbTable->findBy($where));
    }

    private function iniciarAssinatura($idPronac): int
    {
        try {
            if (empty($idPronac)) {
                throw new \Exception("Id Pronac não informado");
            }

            $parecer = new \Parecer_Model_DbTable_Parecer();
            $parecerTecnico = $parecer->getIdAtoAdministrativoParecerTecnico(
                $idPronac,
                1
            )->current();

            $servicoDocumentoAssinatura = new \Application\Modules\Parecer\Service\Assinatura\AnaliseInicial\DocumentoAssinatura(
                $idPronac,
                \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL,
                $parecerTecnico['idParecer']
            );
            return $servicoDocumentoAssinatura->iniciarFluxo();

        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function definirStatusDiligencia($produto)
    {
        $diligencia = 0;
        if ($produto['DtSolicitacao'] && $produto['DtResposta'] == NULL) {
            $diligencia = 1;
        } else if ($produto['DtSolicitacao'] && $produto['DtResposta'] != NULL) {
            $diligencia = 2;
        } else if ($produto['DtSolicitacao']
            && round(\data::CompararDatas($produto['DtDistribuicao'])) > $produto['tempoFimDiligencia']) {
            $diligencia = 3;
        }

        return $diligencia;
    }

    private function obterTempoRestanteDeAvaliacao($produto)
    {
        switch ($produto['stDiligencia']) {
            case 1:
                $tempoRestante = round(\data::CompararDatas($produto['DtDistribuicao'], $produto['DtSolicitacao']));
                break;
            case 2:
            case 3:
                $tempoRestante = round(\data::CompararDatas($produto['DtResposta']));
                break;
            default:
                $tempoRestante = round(\data::CompararDatas($produto['DtDistribuicao']));
                break;
        }

        return $tempoRestante;
    }

    private function obterTempoDiligencia($produto)
    {
        $tempoDiligencia = 0;
        if ($produto['stDiligencia'] == 1) {
            $tempoDiligencia = round(\data::CompararDatas($produto['DtSolicitacao']));
        }

        return $tempoDiligencia;
    }


    private function getIdDocumentoAssinatura($idPronac)
    {
        $objDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        return $objDocumentoAssinatura->getIdDocumentoAssinatura($idPronac, self::ID_ATO_ADMINISTRATIVO);
    }

    public function devolverProdutoParaCordenador()
    {
        $params = $this->request->getParams();

        if (empty($params['idDistribuirParecer'])) {
            throw new \Exception("Dados obrigatórios não informado");
        }

        $whereDistribuicaoAtual = [];
        $whereDistribuicaoAtual["idDistribuirParecer = ?"] = $params['idDistribuirParecer'];
        $whereDistribuicaoAtual["idAgenteParecerista = ?"] = $this->idAgente;
        $whereDistribuicaoAtual["stEstado = ?"] = \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO;

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicao = $tbDistribuirParecer->findBy($whereDistribuicaoAtual);

        if (empty($distribuicao)) {
            throw new \Exception("Distribui&ccedil;&atilde;o n&atilde;o encontrada para o produto informado");
        }

        $modelDistribuicao = new \Parecer_Model_TbDistribuirParecer($distribuicao);
        $modelDistribuicao->setObservacao($params['Observacao']);
        $modelDistribuicao->setSiAnalise($params['siAnalise']);
        $modelDistribuicao->setSiEncaminhamento($params['siEncaminhamento']);
        $modelDistribuicao->tratarObservacaoTextoRico();
        $modelDistribuicao->setIdUsuario($this->idUsuario);

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        return $tbDistribuirParecerMapper->devolverProdutoParaCoordenador($modelDistribuicao);
    }

    public function devolverProdutoParaParecerista()
    {
        $params = $this->request->getParams();

        if (empty($params['idDistribuirParecer'])) {
            throw new \Exception("Dados obrigatórios não informado");
        }

        $whereDistribuicaoAtual = [];
        $whereDistribuicaoAtual["idDistribuirParecer = ?"] = $params['idDistribuirParecer'];
        $whereDistribuicaoAtual["stEstado = ?"] = \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO;

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicao = $tbDistribuirParecer->findBy($whereDistribuicaoAtual);

        if (empty($distribuicao)) {
            throw new \Exception("Distribui&ccedil;&atilde;o n&atilde;o encontrada para o produto informado");
        }

        $dados = array_merge($distribuicao, [
            'Observacao' => utf8_decode(trim(strip_tags($params['Observacao']))),
            'idUsuario' => $this->idUsuario,
        ]);

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        return $tbDistribuirParecerMapper->devolverProdutoParaParecerista($dados);
    }
}
