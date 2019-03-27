<?php

use Application\Modules\AvaliacaoResultados\Service\Encaminhar\AvaliacaoFinanceira as EncaminharAvaliacaoService;
use Application\Modules\AvaliacaoResultados\Service\Fluxo\Estado as EstadoService;

class AvaliacaoResultados_Events_Devolver
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
        $this->events->attach('run', $this->alterarEstado());
        $this->events->attach('run', $this->invalidarDocumento());
        $this->events->attach('run', $this->salvarEncaminhamento());
    }

    public function run($params) {
        $this->events->trigger(__FUNCTION__, $this, $params);
    }

    public function salvarEncaminhamento() {
        return function($t) {
            $params = $t->getParams();
            $EncaminharAvaliacaoService = new EncaminharAvaliacaoService();
            $EncaminharAvaliacaoService->salvar($params);
        };
    }

    public function alterarEstado() {
        return function($t) {
            $params = $t->getParams();

            $estadoService = new EstadoService();
            $estadoService->alterarEstado($params);
        };
    }

    public function invalidarDocumento() {
        return function($t) {
            $params = $t->getParams();

            $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $idDocumento = $objDbTableDocumentoAssinatura->getIdDocumentoAssinatura($params['idPronac'], $params['idTipoDoAtoAdministrativo']);
            $GrupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
            $codGrupo = $GrupoAtivo->codGrupo;
            $dados = [
                'dsMotivoDevolucao' => utf8_decode($params['dsJustificativa']),
                'idTipoDoAto' => $params['idTipoDoAtoAdministrativo'],
                'idPronac' => $params['idPronac'],
                'idPerfilDoAssinante' => $codGrupo,
                'idDocumentoAssinatura' => $idDocumento
            ];

            $assinaturaService = new \MinC\Assinatura\Servico\Assinatura($dados);

            $assinaturaService->devolver();
        };
    }
}
