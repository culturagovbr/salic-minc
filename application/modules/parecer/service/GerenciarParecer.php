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

        $resposta['pareceristas'] = $this->obterPareceristasDistribuicao(
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

        if (empty($params['idDistribuirParecer']) || empty($params['idPronac']) || empty($params['idProduto'])) {
            throw new \Exception("Dados obrigatórios não informado");
        }

        $distribuicao = [
            'Observacao' => utf8_decode(trim(strip_tags($params['Observacao']))),
            'idUsuario' => $this->idUsuario,
            'idDistribuirParecer' => $params['idDistribuirParecer'],
            'idPRONAC' => $params['idPronac'],
            'idProduto' => $params['idProduto'],
            'idAgenteParecerista' => $this->idAgente
        ];

        $whereCredenciamento = [];
        $whereCredenciamento['idAgente = ?'] = $idAgente;
        $whereCredenciamento['idCodigoArea = ?'] = $idArea;
        $whereCredenciamento['idCodigoSegmento = ?'] = $idSegmento;

        $tbCredenciamentoParecerista = new \Agente_Model_DbTable_TbCredenciamentoParecerista();
        $credenciamentos = $tbCredenciamentoParecerista->buscar($whereCredenciamento);

        if (empty($credenciamentos)) {
            throw new \Exception("Parecerista n&atilde;o credenciado na &aacute;rea e segmento do Produto");
        }

        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        return $tbDistribuirParecerMapper->devolverProduto($distribuicao);
    }

    private function obterPareceristasDistribuicao($idOrgao, $idArea, $idSegmento, $valor)
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
}
