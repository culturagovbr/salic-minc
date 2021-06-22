<?php

namespace Application\Modules\Parecer\Service;

class AnaliseConteudo implements \MinC\Servico\IServicoRestZend
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
        $idProduto = (int) $this->request->getParam('id');
        $idPronac = (int) $this->request->getParam('idPronac');

        if (empty($idProduto) || empty($idPronac)) {
            throw new \Exception("Dados obrigat&oacute;rios n&atilde;o informados");
        }

        $analisedeConteudoDAO = new \Parecer_Model_DbTable_TbAnaliseDeConteudo();
        $analisedeConteudo = $analisedeConteudoDAO->dadosAnaliseconteudo(
            false,
            [
                'idProduto = ?' => $idProduto,
                'idPronac = ?' => $idPronac
            ]
        )->current();

        $resp = count($analisedeConteudo) > 0 ? $analisedeConteudo->toArray() : [];
        $resp['somenteLeitura'] = $this->isPermitidoAvaliar($idPronac, $idProduto)
            && count($analisedeConteudo) > 0;

        $resp = \TratarArray::utf8EncodeArray($resp);

        return $resp;

    }

    public function salvar()
    {
        $idAnaliseDeConteudo = $this->request->getParam('idAnaliseDeConteudo');
        $idDistribuirParecer = $this->request->getParam('idDistribuirParecer');
        $idPronac = $this->request->getParam('IdPRONAC');
        $idProduto = $this->request->getParam('idProduto');
        $parecerFavoravel= $this->request->getParam('ParecerFavoravel');
        $parecerDeConteudo = $this->request->getParam('ParecerDeConteudo');
        $parecerDeConteudo = \TratarString::tratarTextoRicoComCaracteresDoWord($parecerDeConteudo);
        $parecerDeConteudo = utf8_decode($parecerDeConteudo);

        if (!$idPronac) {
            throw new \Exception('Falta idPronac');
        }

        if (!$idProduto) {
            throw new \Exception('Falta id do Produto');
        }

        if (!$this->isPermitidoAvaliar($idPronac, $idProduto)) {
            throw new \Exception('Você não tem permissão para analisar');
        }

        if (strlen(trim($parecerDeConteudo)) == 0) {
            throw new \Exception('Falta parecer de conte&uacute;do');
        }

        $dados = [
            'idPronac' => $idPronac,
            'idProduto' => $idProduto,
            'Lei8313' => 0,
            'Artigo3' => 0,
            'IncisoArtigo3' => 0,
            'AlineaArtigo3' => " ",
            'Artigo18' => 0,
            'AlineaArtigo18' => " ",
            'Artigo26' => 0,
            'Lei5761' => 0,
            'Artigo27' => 0,
            'IncisoArtigo27_I' => 0,
            'IncisoArtigo27_II' => 0,
            'IncisoArtigo27_III' => 0,
            'IncisoArtigo27_IV' => 0,
            'TipoParecer' => 1,
            'ParecerFavoravel' => $parecerFavoravel,
            'ParecerDeConteudo' => $parecerDeConteudo,
            'idParecer' => null,
            'idUsuario' => $this->idUsuario,
        ];

        $analisedeConteudoDAO = new \Parecer_Model_DbTable_TbAnaliseDeConteudo();

        if (empty($idAnaliseDeConteudo)) {
            $analisedeConteudo = $analisedeConteudoDAO->dadosAnaliseconteudo(
                false,
                [
                    'idProduto = ?' => $idProduto,
                    'idPronac = ?' => $idPronac
                ]
            )->current();

            if ($analisedeConteudo) {
                $idAnaliseDeConteudo = $analisedeConteudo->idAnaliseDeConteudo;
            }
        }

        if (empty($idAnaliseDeConteudo)) {
            $idAnaliseDeConteudo = $analisedeConteudoDAO->insert($dados);
        } else {
            $where = [];
            $where['idAnaliseDeConteudo = ?'] = $idAnaliseDeConteudo;
            $where['idPRONAC = ?'] = $idPronac;
            $where['idProduto = ?'] = $idProduto;
            $analisedeConteudoDAO->update($dados, $where);
        }

        if ($idDistribuirParecer) {
            $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
            $tbDistribuirParecer->alterar(
                ['siAnalise' => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE],
                ['idDistribuirParecer = ?' => $idDistribuirParecer]
            );
        }

        $dados['idAnaliseDeConteudo'] = $idAnaliseDeConteudo;
        $dados = \TratarArray::utf8EncodeArray($dados);

        return $dados;
    }
}
