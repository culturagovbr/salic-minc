<?php

namespace Application\Modules\AvaliacaoResultados\Service\LaudoFinal;

class Laudo 
{
    public function obterProjetos(){
        $model = new \AvaliacaoResultados_Model_DbTable_LaudoFinal();
        
        return $model->projetosLaudoFinal()->toArray();
    }

    public function obterLaudo($idPronac){
        $model = new \AvaliacaoResultados_Model_DbTable_LaudoFinal();
        return $model->laudoFinal($idPronac);
    }

    public function salvarLaudo($idLaudoFinal, $idPronac, $siManifestacao, $dsLaudoFinal){
        $auth = \Zend_Auth::getInstance();
        $tbTable = new \AvaliacaoResultados_Model_DbTable_LaudoFinal;
        if(empty($idLaudoFinal)) {
            $tbTable->insert(['idPronac'=>$idPronac,
                              'idUsuario'=>$auth->getIdentity()->usu_codigo,
                              'dtLaudoFinal'=>(new \DateTime())->format('Y-m-d'),
                              'siManifestacao'=>$siManifestacao,
                              'dsLaudoFinal'=>$dsLaudoFinal]);
        } else {
            $tbTable->alterar(['idUsuario'=>$auth->getIdentity()->usu_codigo,
                               'siManifestacao'=>$siManifestacao,
                               'dsLaudoFinal'=>$dsLaudoFinal],
                              ['idLaudoFinal = ?' => $idLaudoFinal]);
        }
        // $model = new \AvaliacaoResultados_Model_LaudoFinal;
        // $model->setIdPronac($idPronac);
        // $model->setDtLaudoFinal((new \DateTime())->format('Y-m-d'));
        // $model->setSiManifestacao($siManifestacao);
        // $model->setDsLaudoFinal($dsLaudoFinal);
        // $model->setIdUsuario($auth->getIdentity()->usu_codigo);
        
        // $mapper = new \AvaliacaoResultados_Model_LaudoFinalMapper;
        // return $mapper->save($model);
    }
}