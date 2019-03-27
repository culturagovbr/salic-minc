<?php

use Application\Modules\AvaliacaoResultados\Service\Encaminhar\AvaliacaoFinanceira as EncaminharAvaliacaoService;
use Application\Modules\AvaliacaoResultados\Service\Assinatura\Laudo\DocumentoAssinatura as DocumentoAssinaturaService;
use Application\Modules\AvaliacaoResultados\Service\LaudoFinal\Laudo as LaudoService;

class AvaliacaoResultados_Events_FinalizarLaudo
{
    protected $events;

    public function __construct(Zend_EventManager_EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new Zend_EventManager_EventManager(__CLASS__);
        }

        $this->attach();
    }

    public function attach() {
        $this->events->attach('run', $this->iniciarAssinatura());
        $this->events->attach('run', $this->alterarEstado());
        $this->events->attach('run', $this->salvarLaudo());
    }

    public function run($params) {
        $this->events->trigger(__FUNCTION__, $this, $params);
    }

    public function alterarEstado() {
        return function($t) {
            $params = $t->getParams();

            $model = new AvaliacaoResultados_Model_FluxosProjeto();
            $mapper = new AvaliacaoResultados_Model_FluxosProjetoMapper();

            $row = $mapper->find(['idpronac = ?' => $params['idpronac']]);

            if (!empty($row)) {
                $model->setId($row['id']);
            }

            $model->setIdPronac($params['idpronac']);
            $model->setEstadoId($params['proximo']);

            $mapper->save($model);
        };
    }

    public function iniciarAssinatura() {
        return function($t) {
            $params = $t->getParams();

            $assinatura = new DocumentoAssinaturaService($params['idpronac'], 623);
            $idDocumentoAssinatura = $assinatura->iniciarFluxo();
        };
    }

    public function salvarLaudo() {
        return function($t){
            $params = $t->getParams();

            $service = new LaudoService();
            $data = $service->salvarLaudo($params['idLaudoFinal'],
                                          $params['idpronac'],
                                          $params['siManifestacao'],
                                          $params['dsLaudoFinal']);
        };
    }
}