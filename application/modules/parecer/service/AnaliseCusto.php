<?php

namespace Application\Modules\Parecer\Service;

class AnaliseCusto implements \MinC\Servico\IServicoRestZend
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

    const ETAPAS_NAO_EDITAVEIS = [
        \Proposta_Model_TbPlanilhaEtapa::CUSTOS_ADMINISTRATIVOS,
        \Proposta_Model_TbPlanilhaEtapa::CUSTOS_VINCULADOS,
        \Proposta_Model_TbPlanilhaEtapa::CAPTACAO_DE_RECURSOS,
    ];

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

    public function obter()
    {
        $idProduto = $this->request->getParam('id');
        $idPronac = $this->request->getParam('idPronac');
        $stPrincipal = $this->request->getParam('stPrincipal');

        if (empty($idPronac) || empty($idProduto)) {
            throw new \Exception("Dados obrigat&oacute;rios n&atilde;o informados");
        }

        $PlanilhaDAO = new \PlanilhaProjeto();
        $planilhaCompleta = $PlanilhaDAO->buscarAnaliseCustos([
            'PPJ.IdPRONAC = ?' => $idPronac,
        ])->toArray();

        $analisedeConteudoDAO = new \Parecer_Model_DbTable_TbAnaliseDeConteudo();
        $analisedeConteudo = $analisedeConteudoDAO->dadosAnaliseconteudo(
            false,
            [
                'idPronac = ?' => $idPronac,
                'idProduto = ?' => $idProduto
            ]
        );
        $planilhaCompleta = \TratarArray::utf8EncodeArray($planilhaCompleta);
        $planilhaParaAnalise = $this->filtrarPlanilhaParaAnalise($planilhaCompleta, $analisedeConteudo, $stPrincipal);
        $resp['items'] = $planilhaParaAnalise;
        $resp['somenteLeitura'] = $this->isPermitidoAvaliar($idPronac, $idProduto)
            && $analisedeConteudo[0]->ParecerFavoravel == 1;

        return $resp;

    }

    private function filtrarPlanilhaParaAnalise($planilhaOrcamentaria, $analisedeConteudo, $stPrincipal)
    {
        $planilha = array();
        $i = 0;

        foreach ($planilhaOrcamentaria as $item) {
            $row = $item;
            $i++;

            if ($this->isRemoverItem($item, $analisedeConteudo, $stPrincipal)) {
                continue;
            }

            $row["Seq"] = $i;
            $row['isDisponivelParaAnalise'] = $this->idGrupo == \Autenticacao_Model_Grupos::PARECERISTA
                && $this->isItemDisponivelParaAnalise($item);
            $row['Produto'] = !empty($item['idProduto'])
                ? $item['Produto']
                : html_entity_decode('Administra&ccedil;&atilde;o do Projeto');
            $row["selecionado"] = false;
            $planilha[] = $row;
        }

        return $planilha;
    }

    private function isRemoverItem($item, $analisedeConteudo, $stPrincipal)
    {
        $idProdutoAnalise = $analisedeConteudo[0]->idProduto;

        if (empty($item['idProduto']) && $stPrincipal == 1) {
            return false;
        }

        if ($item['idProduto'] == $idProdutoAnalise) {
            return false;
        }

        return true;
    }

    private function isItemDisponivelParaAnalise($item)
    {
        if (in_array($item['idEtapa'], self::ETAPAS_NAO_EDITAVEIS)) {
            return false;
        }

        if ($item['NrFonteRecurso'] != \Mecanismo::INCENTIVO_FISCAL_FEDERAL) {
            return false;
        }

        return true;
    }

    public function salvar()
    {
        $params = $this->request->getParams();

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        if (!$params['idPlanilhaProjeto'] || !$params['IdPRONAC'] || !$params['idProduto']) {
            throw new \Exception('Dados obrigatórios não informado');
        }

        if (strlen(trim($params['dsJustificativaParecerista'])) < 10) {
            throw new \Exception('Parecer incompleto ou n&atilde;o informado');
        }

        if (round($params['VlSugeridoParecerista'], 2) > round($params['VlSolicitado'], 2)) {
            throw new \Exception('Valor sugerido não pode ser maior que o solicitado');
        }

        if (!$this->isPermitidoAvaliar($params['IdPRONAC'], $params['idProduto'])) {
            throw new \Exception('Você não tem permissão para alterar');
        }

        if ($params['stCustoPraticado'] == 1) {

            if (empty($params['idProduto'])
                || empty($params['idUnidade'])
                || empty($params['idPlanilhaItem'])
                || empty($params['idUfDespesa'])
                || empty($params['idMunicipioDespesa'])
            ) {
                throw new \Exception("Dado obrigatório não informado!");
            }

            $spCalcularMedianaItemOrcamentario = new \Planilha_Model_DbTable_SpCalcularMedianaItemOrcamentario();
            $mediana = $spCalcularMedianaItemOrcamentario->obterMedianaItemOrcamento(
                $params['idProduto'],
                $params['idUnidade'],
                $params['idPlanilhaItem'],
                $params['idUfDespesa'],
                $params['idMunicipioDespesa']
            );

            $params['stCustoPraticado'] = $params['valorUnitarioparc'] > $mediana['Mediana'] ? 1 : 0;
        }

        $dados = [
            'idUnidade' => $params['idUnidade'],
            'Quantidade' => $params['quantidadeparc'],
            'Ocorrencia' => $params['ocorrenciaparc'],
            'ValorUnitario' => $params['valorUnitarioparc'],
            'QtdeDias' => $params['diasparc'],
            'Justificativa' => utf8_decode($params['dsJustificativaParecerista']),
            'idUsuario' => $this->idUsuario,
            'stCustoPraticado' => $params['stCustoPraticado']
        ];
        $where = ['idPlanilhaProjeto = ?' => $params['idPlanilhaProjeto']];
        $planilhaProjeto = new \PlanilhaProjeto();
        $planilhaProjeto->alterar($dados, $where);

        $tbPlanilhaProjetoMapper = new \Planilha_Model_TbPlanilhaProjetoMapper();
        $tbPlanilhaProjetoMapper->atualizarCustosVinculadosDaTbPlanilhaProjeto($params['IdPRONAC']);

        $idPlanilhaProjeto = (int)$params['idPlanilhaProjeto'];
        $where = [
            'PPJ.IdPRONAC = ?' => $params['IdPRONAC'],
            "(PPJ.idPlanilhaProjeto = {$idPlanilhaProjeto} OR PPJ.idEtapa in (?))" => [
                \Proposta_Model_TbPlanilhaEtapa::CUSTOS_VINCULADOS,
                \Proposta_Model_TbPlanilhaEtapa::CAPTACAO_DE_RECURSOS,
            ]
        ];

        return [
            'items' => $planilhaProjeto->buscarAnaliseCustos($where)->toArray(),
        ];
    }

    public function restaurarPlanilha()
    {
        $params = $this->request->getParams();

        if (!$params['id']) {
            throw new \Exception('Falta id do Produto');
        }

        if (!$params['idPronac']) {
            throw new \Exception('Falta id do projeto');
        }

        if (!$this->isPermitidoAvaliar($params['idPronac'], $params['id'])) {
            throw new \Exception('Você não tem permissão para alterar!');
        }

        $idsProdutos = [$params['id']];
        if ($this->distribuicao['stPrincipal'] == 1) {
            $idsProdutos = [0, $params['id']];
        }

        $idPlanilhaProjeto  = $this->request->getParam('idPlanilhaProjeto', null);

        $tbPlanilhaProjeto = new \Planilha_Model_DbTable_TbPlanilhaProjeto();
        $response = $tbPlanilhaProjeto->restaurarPlanilha($params['idPronac'], $idsProdutos, $idPlanilhaProjeto);

        if ($response) {
            $tbPlanilhaProjetoMapper = new \Planilha_Model_TbPlanilhaProjetoMapper();
            $tbPlanilhaProjetoMapper->atualizarCustosVinculadosDaTbPlanilhaProjeto($params['idPronac']);
        }

        return $response;
    }

}
