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

    public function index()
    {
        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $filtro = $this->request->getParam('filtro');
        $produtos = $tbDistribuirParecer->obterPainelGerenciarParecer($filtro);
        return \TratarArray::utf8EncodeArray($produtos);
    }

    public function get()
    {
        $id = $this->request->getParam('id');
        $idPronac = $this->request->getParam('idPronac');

        $projeto = new \Projetos();
        $produto = $projeto->buscaProjetosProdutosParaAnalise(
            [
                'distribuirParecer.idProduto = ?' => $id,
                'distribuirParecer.siEncaminhamento = ?' => \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
                'projeto.IdPRONAC = ?' => $idPronac,
            ]
        )->current();

        if ($produto) {
            $produto = $produto->toArray();
            $produto['stDiligencia'] = $this->definirStatusDiligencia($produto);
            $produto['diasEmDiligencia'] = $this->obterTempoDiligencia($produto);
            $produto['diasEmAvaliacao'] = $this->obterTempoRestanteDeAvaliacao($produto);

            if ($produto['stPrincipal']
                && $produto['siAnalise'] == \Parecer_Model_TbDistribuirParecer::SI_ANALISE_ANALISADO) {
                $produto['idDocumentoAssinatura'] = $this->getIdDocumentoAssinatura($idPronac);
            }
        }
        return \TratarArray::utf8EncodeArray($produto);

    }

    public function obteDistribuicaoProduto()
    {
        $idProduto = $this->request->getParam('idProduto');
        $idPronac = $this->request->getParam('idPronac');

        $resposta = [];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $produto = current($tbDistribuirParecer->obterPainelGerenciarParecer(
            '',
            [
                'idProduto = ?' => $idProduto,
                'IdPRONAC = ?' => $idPronac,
            ]
        ));

        if (empty($produto)) {
            throw new \Exception("Produto indispon&iacute;vel para distribui&ccedil;&atilde;o!");
        }

        $resposta['pareceristas'] = $this->obterPareceristas(
            $produto['idOrgao'],
            $produto['idArea'],
            $produto['idSegmento'],
            $produto['valor']
        );

        $resposta['vinculadas'] = $this->obterUnidadesVinculadas($produto['idOrgao']);

        return $resposta;
    }

    public function distribuirProduto()
    {
        $params = $this->request->getParams();

        if (empty($dados['idDistribuirParecer'])
            || empty($dados['idPronac'])
            || empty($dados['idProduto'])
        ) {
            throw new \Exception("Dados obrigatórios não informados");
        }

        xd( 'produtoooo');

        $whereDistribuicaoAtual = [];
        $whereDistribuicaoAtual["idDistribuirParecer = ?"] = $params['idDistribuirParecer'];
        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicaoAtual = $tbDistribuirParecer->findBy($whereDistribuicaoAtual);

        $resposta = $this->distribuir($params, $distribuicaoAtual);

        if ($resposta) {
            $this->desabilitarDocumentoAssinatura($params['idPronac']);
            $this->alterarSituacaoDoProjeto($distribuicaoAtual, $params['tipoAcao']);
        }
    }

    public function distribuirTodosProdutosDoProjeto()
    {
        $params = $this->request->getParams();

        $whereDistribuicaoAtual = [
            "idPronac = ?" => $params['idPronac'],
            "idOrgao = ?" => $this->idOrgao,
            "stEstado = ?" => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO
        ];

        xd('projeto');
        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicoes = $tbDistribuirParecer->findAll($whereDistribuicaoAtual);

        $resposta = false;
        foreach($distribuicoes as $distribuicao) {
            $resposta = $this->distribuir($params, $distribuicao);
        }

        if ($resposta) {
            $this->desabilitarDocumentoAssinatura($params['idPronac']);
            $this->alterarSituacaoDoProjeto($params, $params['tipoAcao']);
        }

    }

    public function distribuir($dados, $distribuicaoAtual)
    {
        if ($dados['tipoAcao'] === 'distribuir'
            && !$this->isPareceristaCredenciado(
                $dados['idParecerista'],
                $dados['idAreaProduto'],
                $dados['idSegmentoProduto'])
        ) {
            throw new \Exception("Parecerista n&atilde;o credenciado na &aacute;rea e segmento do Produto");
        }

        if ($dados['tipoAcao'] === 'encaminhar' && empty($dados['idOrgaoDestino'])) {
            throw new \Exception("Selecione o org&atilde;o destino");
        }

        $observacao = utf8_decode(trim(strip_tags($dados['observacao'])));

        if (strlen($observacao) < 11) {
            throw new \Exception("O campo observa&ccedil;&atilde;o deve ter no m&iacute;nimo 11 caracteres");
        }

        $distribuicao = array_merge($distribuicaoAtual, [
            'Observacao' => $observacao,
            'idUsuario' => $this->idUsuario,
        ]);

        if (!empty($dados['idParecerista'])) {
            $distribuicao['idAgenteParecerista'] = $dados['idParecerista'];
        }

        if (!empty($dados['idOrgaoDestino'])) {
            $distribuicao['idOrgao'] = $dados['idOrgaoDestino'];
        }

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        if ($dados['tipoAcao'] === 'encaminhar') {
            return $tbDistribuirParecerMapper->encaminharProdutoParaVinculada($distribuicao);
        }

        return $tbDistribuirParecerMapper->distribuirProdutoParaParecerista($distribuicao);
    }

    private function isPareceristaCredenciado($idParecerista, $idAreaProduto, $idSegmentoProduto)
    {
        $whereCredenciamento = [
            'idAgente = ?' => $idParecerista,
            'idCodigoArea = ?' => $idAreaProduto,
            'idCodigoSegmento = ?' => $idSegmentoProduto
        ];

        $tbCredenciamentoParecerista = new \Agente_Model_DbTable_TbCredenciamentoParecerista();
        $credenciamentos = $tbCredenciamentoParecerista->buscar($whereCredenciamento)->toArray();

        return (count($credenciamentos) > 0);
    }

    private function alterarSituacaoDoProjeto($dados, $tipoAcao) : void
    {
        $providenciaTomada = 'Encaminhado ao perito para an&aacute;lise t&eacute;cnica e emiss&atilde;o de parecer.';

        if ($tipoAcao === 'encaminhar') {
            $orgaos = new \Orgaos();
            $orgao = $orgaos->pesquisarNomeOrgao($dados['idOrgaoDestino']);

            $providenciaTomada = sprintf(
                'Encaminhado para %s para an&aacute;lise e emiss&atilde;o de parecer t&eacute;cnico.',
                $orgao[0]->NomeOrgao
            );
        }

        $projetos = new \Projetos();
        $projetos->alterarSituacao(
            $dados['idPronac'],
            null,
            \Projeto_Model_Situacao::ENCAMINHADO_PARA_ANALISE_TECNICA,
            $providenciaTomada
        );
    }

    private function obterPareceristas($idOrgao, $idArea, $idSegmento, $valor)
    {
        $spSelecionarParecerista = new \Parecer_Model_DbTable_SpSelecionarParecerista();
        $pareceristas = $spSelecionarParecerista->exec(
            $idOrgao,
            $idArea,
            $idSegmento,
            $valor,
            \Zend_DB::FETCH_ASSOC
        );

        foreach ($pareceristas as &$parecerista) {
            $parecerista['emAvaliacao'] = $this->obterQuantidadeEmAvaliacao($parecerista['idParecerista']);
        }

        return $pareceristas;
    }

    private function obterQuantidadeEmAvaliacao($idAgente)
    {
        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        return $tbDistribuirParecer->buscaProjetosProdutosParaAnalise(['idAgenteParecerista = ?' => $idAgente])->count();
    }

    private function obterUnidadesVinculadas($idOrgao)
    {
        $tbOrgaos = new \Orgaos();
        $unidadesVinculadas = $tbOrgaos->buscar(
            array(
                "Codigo <> ?" => $idOrgao,
                "Status = ?" => 0,
                "Vinculo = ?" => 1,
                "stvinculada = ?" => 1,
                "idSecretaria <> ?" => \Orgaos::ORGAO_SUPERIOR_SEFIC,
            )
        )->toArray();

        /**
         * Apenas o IPHAN principal pode ter acesso as unidades vinculadas
         */
        if ($idOrgao == \Orgaos::ORGAO_IPHAN_PRONAC) {
            $vinculadasIphan = $tbOrgaos->buscar(
                array(
                    "Codigo <> ?" => $idOrgao,
                    "Status = ?" => 0,
                    "idSecretaria = ?" => \Orgaos::ORGAO_IPHAN_PRONAC,
                )
            )->toArray();

            $unidadesVinculadas = array_merge($unidadesVinculadas, $vinculadasIphan);
        }

        return $unidadesVinculadas;
    }

    private function desabilitarDocumentoAssinatura($idPronac)
    {
        $idTipoDoAtoAdministrativo = \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ANALISE_INICIAL;

        $objAssinatura = new \Assinatura_Model_DbTable_TbAssinatura();
        $assinaturas = $objAssinatura->obterAssinaturas($idPronac, $idTipoDoAtoAdministrativo);
        if (count($assinaturas) > 0) {
            $idDocumentoAssinatura = current($assinaturas)['idDocumentoAssinatura'];

            $objDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $dadosDocumentoAssinatura = ["stEstado" => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO];
            $whereDocumentoAssinatura = "idDocumentoAssinatura = $idDocumentoAssinatura";

            $objDocumentoAssinatura->update($dadosDocumentoAssinatura, $whereDocumentoAssinatura);
        }
    }
}
