<?php
/**
 * Helper para verificar se o item está disponível para edição no remanejamento
 */

class Zend_View_Helper_ItemDisponivelParaEdicaoRemanejamento extends Zend_View_Helper_Abstract
{
    /**
     * Método para verificar se o item está disponível para edição no remanejamento
     * @access public
     * @param array $planilha
     * @param integer $fonte
     * @return string
     */
    public function itemDisponivelParaEdicaoRemanejamento($planilha, $fonte)
    {
        $etapasBloqueadas = [
            PlanilhaEtapa::ETAPA_CUSTOS_VINCULADOS,
            PlanilhaEtapa::ETAPA_CAPTACAO_RECURSOS
        ];
        
        if (($planilha['tpAcao'] != 'E') &&
            $fonte == '0' &&
            $planilha['vlAprovado'] > $planilha['vlComprovado'] &&
            !in_array($planilha['idEtapa'], $etapasBloqueadas)
        ) {
            return true;
        }
        return false;
    }
}
