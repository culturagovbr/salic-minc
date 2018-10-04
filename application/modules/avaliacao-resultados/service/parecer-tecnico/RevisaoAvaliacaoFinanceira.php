<?php

namespace Application\Modules\AvaliacaoResultados\Service\ParecerTecnico;


class RevisaoAvaliacaoFinanceira
{

    public function buscarRevisoes($data)
    {
        $tbAvaliacaoFinanceira = new \AvaliacaoResultados_Model_DbTable_tbAvaliacaoFinanceiraRevisao();
        $where = [
            'idAvaliacaoFinanceira' => $data
        ];
        $dadosRevisao = $tbAvaliacaoFinanceira->findByAvaliacaoFinanceira($where)->toArray();
        return $dadosRevisao;
    }

    public function salvar($data)
    {
        $authInstance = \Zend_Auth::getInstance();
        $arrAuth = array_change_key_case((array)$authInstance->getIdentity());
        $tbAvaliacaoFinanceiraRevisao = new \AvaliacaoResultados_Model_tbAvaliacaoFinanceiraRevisao($data);

        if(!isset($data["idAvaliacaoFinanceiraRevisao"])){
            $tbAvaliacaoFinanceiraRevisao->setDtAtualizacao(date('Y-m-d h:i:s'));
        }else{
            $tbAvaliacaoFinanceiraRevisao->setDtRevisao(date('Y-m-d h:i:s'));
        }

        $tbAvaliacaoFinanceiraRevisao->setIdAgente($arrAuth['usu_codigo']);
        $mapper = new \AvaliacaoResultados_Model_tbAvaliacaoFinanceiraRevisaoMapper();
        $codigo = $mapper->save($tbAvaliacaoFinanceiraRevisao);

        //$this->request->setParam('idAvaliacaoFinanceiraRevisao', $codigo);
        if (!$codigo) {
            return $mapper->getMessages();
        }
        return $this->buscarRevisao($codigo);
    }

    public function buscarRevisao($data)
    {
        $tbAvaliacaoFinanceira = new \AvaliacaoResultados_Model_DbTable_tbAvaliacaoFinanceiraRevisao();
        $where = [
            'idAvaliacaoFinanceiraRevisao' => $data
        ];
        $dadosRevisao = $tbAvaliacaoFinanceira->findOneRevisao($where)->toArray();
        return $dadosRevisao;
    }
}
