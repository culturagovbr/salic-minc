<?php

namespace Application\Modules\Execucao\Service\DadosReadequacoes;

class DadosReadequacoes
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function buscarReadequacoes()
    {
        $idPronac = $this->request->idPronac;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {

            $Readequacao_Model_DbTable_TbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $dadosReadequacoes = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('a.idPronac = ?' => $idPronac, 'a.siEncaminhamento <> ?' => 12), array('tipoReadequacao','dtSolicitacao'))->toArray();

            $dadosReadequacoesDevolvidas = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('a.idPronac = ?'=>$idPronac, 'a.siEncaminhamento = ?' => 12, 'a.stAtendimento = ?' => 'E', 'a.stEstado = ?' => 0))->toArray();

            $tbReadequacaoXParecer = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
            foreach ($dadosReadequacoes as &$dr) {
                $dr['pareceres'] = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?'=>$dr['idReadequacao']))->toArray();
            }

            $resultArray = [];
            $resultArray['dadosReadequacoes'] = $dadosReadequacoes;
            $resultArray['dadosReadequacoesDevolvidas'] = $dadosReadequacoesDevolvidas;

            return $resultArray;
        }
    }

    public function visualizarReadequacao()
    {
        $idPronac = $this->request->idPronac;
        $idReadequacao = $this->request->idReadequacao;

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {

            $Readequacao_Model_DbTable_TbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $dadosReadequacoes = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('a.idPronac = ?' => $idPronac, 'a.siEncaminhamento <> ?' => 12, 'a.idReadequacao = ?' => $idReadequacao), array('tipoReadequacao','dtSolicitacao'))->toArray();

            $dadosReadequacoesDevolvidas = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('a.idPronac = ?'=>$idPronac, 'a.siEncaminhamento = ?' => 12, 'a.stAtendimento = ?' => 'E', 'a.stEstado = ?' => 0))->toArray();

            $tbReadequacaoXParecer = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
            foreach ($dadosReadequacoes as &$dr) {
                $dr['pareceres'] = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao = ?'=>$dr['idReadequacao']))->toArray();
            }

            $resultArray = [];
            $resultArray['dadosReadequacoes'] = $dadosReadequacoes;
            $resultArray['dadosReadequacoesDevolvidas'] = $dadosReadequacoesDevolvidas;

            return $resultArray;
        }
    }
}

