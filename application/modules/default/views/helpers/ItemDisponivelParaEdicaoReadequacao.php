<?php
/**
 * Helper para verificar se o item está disponível para edição na readequacao
 */

class Zend_View_Helper_ItemDisponivelParaEdicaoReadequacao extends Zend_View_Helper_Abstract
{
    /**
     * Método para verificar se o item está disponível para edição no remanejamento
     * @access public
     * @param array $planilha
     * @param integer $fonte
     * @return string
     */
    public function itemDisponivelParaEdicaoReadequacao($planilha, $fonte, $link, $disponivelParaEdicaoReadequacaoPlanilha)
    {
        $etapasBloqueadas = [
            PlanilhaEtapa::ETAPA_CUSTOS_VINCULADOS,
            PlanilhaEtapa::ETAPA_CAPTACAO_RECURSOS
        ];

        return ($link &&
                $planilha['vlComprovado'] < $planilha['vlAprovado'] &&
                $disponivelParaEdicaoReadequacaoPlanilha &&
                (
                    $planilha['tpAcao'] != 'E' &&
                    $fonte == '0'
                ) &&
                !in_array($planilha['idEtapa'], $etapasBloqueadas)
        );
    }
}
