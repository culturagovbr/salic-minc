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

    public function salvarLaudo($idLaudoFinal, $idPronac, $dtLaudoFinal, $siManifestacao, $dsLaudoFinal, $idUsuario){
        $auth = \Zend_Auth::getInstance();
        $tbTable = new \AvaliacaoResultados_Model_DbTable_LaudoFinal;
        $tbTable->insert(['idPronac'=>$idPronac, 
                          'idUsuario'=>$auth->getIdentity()->usu_codigo, 
                          'dtLaudoFinal'=>(new \DateTime())->format('Y-m-d'), 
                          'siManifestacao'=>$siManifestacao, 
                          'dsLaudoFinal'=>$dsLaudoFinal]);

        $model = new \AvaliacaoResultados_Model_LaudoFinal;
        $model->setIdPronac($idPronac);
        $model->setDtLaudoFinal($dtLaudoFinal);
        $model->setSiManifestacao($siManifestacao);
        $model->setDsLaudoFinal($dsLaudoFinal);
        $model->setIdUsuario($idUsuario);
        
        $mapper = new \AvaliacaoResultados_Model_LaudoFinalMapper;
        return $mapper->save($model);
    }
}