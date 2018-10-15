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
        if(!$dadosRevisao && empty($dadosRevisao))
        {
            return ['dados'=>$dadosRevisao,'code'=> 400];
        }
        return ['dados'=>$dadosRevisao,'code'=> 200];
    }

    public function salvar($data)
    {
        $authInstance = \Zend_Auth::getInstance();
        $arrAuth = array_change_key_case((array)$authInstance->getIdentity());
        $tbAvaliacaoFinanceiraRevisao = new \AvaliacaoResultados_Model_tbAvaliacaoFinanceiraRevisao($data);

        if(isset($data['idAvaliacaoFinanceiraRevisao']) && !empty($data['idAvaliacaoFinanceiraRevisao'])){
            $tbAvaliacaoFinanceiraRevisao->setDtAtualizacao(date('Y-m-d h:i:s'));
        }else{
            $tbAvaliacaoFinanceiraRevisao->setDtRevisao(date('Y-m-d h:i:s'));
        }
        $tbAvaliacaoFinanceiraRevisao->setIdAgente($arrAuth['usu_codigo']);

        $mapper = new \AvaliacaoResultados_Model_tbAvaliacaoFinanceiraRevisaoMapper();
        $codigo = $mapper->save($tbAvaliacaoFinanceiraRevisao);

        if (!$codigo) {
            $error = ['dados'=>$mapper->getMessages(),'code'=> 400];
            return $error;
        }
        $retorno = ['dados'=>$this->buscarRevisao($codigo), 'code'=>200];

        return $retorno;
    }

    public function buscarRevisao($data)
    {
        $tbAvaliacaoFinanceira = new \AvaliacaoResultados_Model_DbTable_tbAvaliacaoFinanceiraRevisao();
        $where = [
            'idAvaliacaoFinanceiraRevisao' => $data
        ];
        $dadosRevisao = $tbAvaliacaoFinanceira->findOneRevisao($where)->toArray();

        if(!$dadosRevisao && empty($dadosRevisao))
        {
         return ['dados'=>$dadosRevisao,'code'=> 400];
        }
        return ['dados'=>$dadosRevisao, 'code'=> 200];
    }
}
