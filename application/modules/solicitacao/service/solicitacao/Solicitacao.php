<?php

namespace Application\Modules\Solicitacao\Service\Solicitacao;

class Solicitacao
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

    private function obterParametrosDaSolicitacao()
    {
        $idPronac = $this->request->getParam('idPronac', null);
        $idPreProjeto = $this->request->getParam('idPreProjeto', null);

        if (strlen($idPronac) > 7) {
            $idPronac = \Seguranca::dencrypt($idPronac);
        }

        $authInstance = \Zend_Auth::getInstance();
        $authObject = $authInstance->getIdentity();

        $where = [];

        if ($authInstance->hasIdentity() && !empty($authObject->usu_codigo)) {
            $where['a.siEncaminhamento = ?'] = \Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC;
            $where['a.idTecnico = ?'] = $authObject->usu_codigo;
            $where['a.idOrgao = ?'] = $authObject->usu_orgao;
            $where['a.dsResposta IS NULL'] = '';

        } else if ($authInstance->hasIdentity() && !empty($authObject->IdUsuario)) {
            $where['a.siEncaminhamento = ?'] = \Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_FINALIZADA_MINC;
            $where["a.idSolicitante = ?"] = $authObject->IdUsuario;
            $where['a.stLeitura = ?'] = 0;
        }

        if ($idPronac) {
            $where['a.idPronac = ?'] = (int)$idPronac;
        }

        if ($idPreProjeto) {
            $where['a.idProjeto = ?'] = (int)$idPreProjeto;
        }

        return $where;
    }

    public function obterSolicitacoes($limit = 10)
    {
        $where = $this->obterParametrosDaSolicitacao();

        if (empty($where)) {
            return [];
        }

        $tbSolicitacoes = new \Solicitacao_Model_DbTable_TbSolicitacao();
        $solicitacoes = $tbSolicitacoes->obterSolicitacoes($where, ['dtSolicitacao DESC', 'idSolicitacao DESC'], $limit)->toArray();

        foreach ($solicitacoes as $key => $solicitacao) {
            $objDateTimeSolicitacao = new \DateTime($solicitacao['dtSolicitacao']);
            $objDateTimeResposta = new \DateTime($solicitacao['dtResposta']);

            $solicitacoes[$key]['dtSolicitacao'] = $objDateTimeSolicitacao->format('d/m/Y');
            $solicitacoes[$key]['dtResposta'] = $objDateTimeResposta->format('d/m/Y');
            $solicitacoes[$key]['dsSolicitacao'] = $this->removerHtmlTags($solicitacao['dsSolicitacao']);
            $solicitacoes[$key]['dsResposta'] = $this->removerHtmlTags($solicitacao['dsResposta']);
        }

        array_walk($solicitacoes, function (&$value) {
            $value = array_map('utf8_encode', $value);
            $value = array_map('html_entity_decode', $value);
        });

        return $solicitacoes;
    }

    public function contarSolicitacoes()
    {
        $where = $this->obterParametrosDaSolicitacao();

        if (empty($where)) {
            return [];
        }

        $tbSolicitacoes = new \Solicitacao_Model_DbTable_TbSolicitacao();
        $quantidade = $tbSolicitacoes->contarSolicitacoes($where);

        return $quantidade;
    }

    private function removerHtmlTags($string)
    {
        $result = strip_tags($string);
        return $this->stringReplace($result);
    }

    private function stringReplace($string)
    {
        return str_replace('&nbsp;', '', $string);
    }
}
