<?php

namespace Application\Modules\Parecer\Service;

class ConsolidacaoParecer implements \MinC\Servico\IServicoRestZend
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
    private $idAgente;
    private $auth;
    private $idOrgao;
    private $idGrupo;
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

    public function obter()
    {
        $idPronac = $this->request->getParam('idPronac');

        $parecerDAO = new \Parecer_Model_DbTable_Parecer();
        $whereParecer = [];
        $whereParecer['idPRONAC = ?'] = $idPronac;
        $parecer = $parecerDAO->findBy($whereParecer);

        $planilhaprojeto = new \PlanilhaProjeto();
        $parecer['SugeridoReal'] = $planilhaprojeto->somarPlanilhaProjeto($idPronac, 109)['soma'];

        return \TratarArray::utf8EncodeArray($parecer);
    }

    public function salvar()
    {
        $idPronac = $this->request->getParam('IdPRONAC');
        $idProduto = $this->request->getParam('idProduto');
        $resumoParecer = $this->request->getParam("ResumoParecer");
        $parecerFavoravel = $this->request->getParam('ParecerFavoravel');

        try {

            if (empty($idPronac) || count($resumoParecer) > 10 || empty($parecerFavoravel) ) {
                throw new \Exception("Dados obrigatórios não informados");
            }

            if (!$this->isPermitidoAvaliar($idPronac, $idProduto)) {
                throw new \Exception('Você não tem permissão para analisar este produto');
            }

            $enquadramentoDAO = new \Admissibilidade_Model_Enquadramento();
            $enquadramento = $enquadramentoDAO->buscarDados($idPronac, null, false);

            $planilhaprojeto = new \PlanilhaProjeto();
            $total = $planilhaprojeto->somarPlanilhaProjeto(
                $idPronac,
                \Mecanismo::INCENTIVO_FISCAL_FEDERAL
            );
            $sugeridoReal = $total['soma'];

            $tbParecer = new \Parecer_Model_DbTable_Parecer();
            $dadosParecer = [
                'idPRONAC' => $idPronac,
                'AnoProjeto' => $enquadramento['AnoProjeto'],
                'Sequencial' => $enquadramento['Sequencial'],
                'TipoParecer' => 1,
                'ParecerFavoravel' => $parecerFavoravel,
                'DtParecer' => \MinC_Db_Expr::date(),
                'NumeroReuniao' => null,
                'ResumoParecer' => utf8_decode($resumoParecer),
                'SugeridoReal' => $sugeridoReal,
                'Atendimento' => 'S',
                'idEnquadramento' => $enquadramento['IdEnquadramento'],
                'stAtivo' => 1,
                'idTipoAgente' => 1,
                'Logon' => $this->idUsuario
            ];

            $buscarParecer = $tbParecer->buscar(['IdPRONAC = ?' => $idPronac]);
            if (count($buscarParecer) > 0) {
                $buscarParecer = $buscarParecer->current();
                $whereUpdateParecer = 'IdParecer = ' . $buscarParecer->IdParecer;
                $tbParecer->alterar($dadosParecer, $whereUpdateParecer);
            } else {
                $tbParecer->inserir($dadosParecer);
            }

        } catch (\Exception $e) {
            throw $e;
        }

    }

}
