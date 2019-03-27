<?php

namespace Application\Modules\PrestacaoContas\Service\RelatorioCumprimentoObjeto;


class RelatorioCumprimentoObjeto implements \MinC\Servico\IServicoRestZend
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    private $isUsuarioInterno = false;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $auth = \Zend_Auth::getInstance();
        $this->isUsuarioInterno = isset($auth->getIdentity()->usu_codigo);
    }

    public function listarRelatorioCumprimentoObjeto()
    {
        $idPronac = $this->request->idPronac;
        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        $arrayDados = [];

        $projetos = new \Projetos();
        $dadosProjeto = $projetos->dadosProjeto(array('idPronac = ?' => $idPronac))->current();

        $tbCumprimentoObjeto = new \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $dadosRelatorio = $tbCumprimentoObjeto->buscarCumprimentoObjeto(array('idPronac = ?' => $idPronac));
        $dadosRelatorio = $dadosRelatorio ? $dadosRelatorio->toArray() : [];

        if (!empty($dadosRelatorio)) {
            $arrayDados['locaisRealizacao'] = $projetos->buscarLocaisDeRealizacao($idPronac)->toArray();
            $arrayDados['planoDeDivulgacao'] = $projetos->buscarPlanoDeDivulgacao($idPronac)->toArray();
            $arrayDados['dadosCompMetas'] = $projetos->buscarMetasComprovadas($idPronac)->toArray();
            $arrayDados['dadosItensOrcamentarios'] = $projetos->buscarItensComprovados($idPronac)->toArray();

            $planoDistribuicaoProduto = new \Proposta_Model_DbTable_PlanoDistribuicaoProduto();
            $arrayDados['planoDistribuicao'] = $planoDistribuicaoProduto->buscarPlanoDeDistribuicao($idPronac)->toArray();

            $tbBeneficiarioProdutoCultural = new \tbBeneficiarioProdutoCultural();
            $arrayDados['planosCadastrados'] = $tbBeneficiarioProdutoCultural->buscarPlanosCadastrados($idPronac)->toArray();

            $arquivo = new \Arquivo();
            $arrayDados['dadosComprovantes'] = $arquivo->buscarComprovantesExecucao($idPronac, \Zend_DB::FETCH_ASSOC);

            $tbTermoAceiteObra = new \ComprovacaoObjeto_Model_DbTable_TbTermoAceiteObra();
            $arrayDados['aceiteObras'] = $tbTermoAceiteObra->buscarTermoAceiteObraArquivos(array('idPronac=?'=>$idPronac), true)->toArray();

            $bensCadastrados= new \ComprovacaoObjeto_Model_DbTable_TbBensDoados();
            $arrayDados['bensCadastrados'] = $bensCadastrados->buscarBensCadastrados(array('a.idPronac=?'=>$idPronac), array('b.Descricao'))->toArray();

            if ($dadosRelatorio['siCumprimentoObjeto'] == 6) {
                $tbUsuario = new \Autenticacao_Model_DbTable_Usuario();
                $arrayDados['tecnicoAvaliador'] = $tbUsuario->nomeUsuario($dadosRelatorio['idTecnicoAvaliador'])->toArray();

                if ($dadosRelatorio['idChefiaImediata']) {
                    $arrayDados['chefiaImediata'] = $tbUsuario->nomeUsuario($dadosRelatorio['idChefiaImediata'])->toArray();
                }
            }

            $arrayDados['dadosRelatorio'] = $dadosRelatorio;

            $isPermitidoVisualizarRelatorio = in_array(
                    $dadosProjeto->situacao,
                    \Projeto_Model_Situacao::obterSituacoesPermitidoVisualizarPrestacaoContas()
            );

            $arrayDados['isPermitidoVisualizarRelatorio'] = $this->isUsuarioInterno || $isPermitidoVisualizarRelatorio;
        }

        return $arrayDados;
    }
}
