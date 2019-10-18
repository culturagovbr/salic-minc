<?php

namespace Application\Modules\Solicitacao\Service\Solicitacao;

class Mensagem
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;


    function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function historicoSolicitacoes()
    {
        $idPronac = $this->request->getParam('idPronac', null);
        $idPreProjeto = $this->request->getParam('idPreProjeto', null);
        $listarTudo = $this->request->getParam('listarTudo', null);

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        $where = [];
        if ($idPronac) {
            $where['a.idPronac = ?'] = (int) $idPronac;
        }

        if ($idPreProjeto) {
            $where['a.idProjeto = ?'] = (int) $idPreProjeto;
        }

        $obterSolicitacoes = new \Solicitacao_Model_DbTable_TbSolicitacao();
        $solicitacoes = $obterSolicitacoes->obterSolicitacoes($where)->toArray();

        foreach ($solicitacoes as $key => $solicitacao) {
            $solicitacoes[$key]['dtSolicitacao'] = $solicitacao['dtSolicitacao'];
            $solicitacoes[$key]['dtResposta'] = $solicitacao['dtResposta'];
            $solicitacoes[$key]['dsSolicitacao'] = $this->stringReplace($solicitacao['dsSolicitacao']);
            $solicitacoes[$key]['dsResposta'] = $this->stringReplace($solicitacao['dsResposta']);
        }

        array_walk($solicitacoes, function (&$value) {
            $value = array_map('utf8_encode', $value);
            $value = array_map('html_entity_decode', $value);
        });

        return $solicitacoes;
    }

    private function stringReplace($string)
    {
        return str_replace('&nbsp;', '', $string);
    }
}
