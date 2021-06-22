<?php
/**
 * Helper para verificar se projeto está disponível para encaminhamento
 */

class Zend_View_Helper_DisponivelEncaminharAnalise
{
    /**
     * Método para verificar se o projeto está disponível para assinatura
     * @access public
     * @param integer $idPerfil
     * @param integer $idVinculada
     * @return string
     */
    public function disponivelEncaminharAnalise()
    {
        $auth = Zend_Auth::getInstance();
        $idOrgaoSuperior = $auth->getIdentity()->usu_org_max_superior;
        
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $idOrgaoLogado = $GrupoAtivo->codOrgao;

        $orgaosEncaminhamento = [];
        $orgaosEncaminhamento[Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI] = [
            Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI,
            Orgaos::ORGAO_GEAR_SACAV,
            Orgaos::ORGAO_SUPERIOR_SEFIC,
        ];
        $orgaosEncaminhamento[Orgaos::ORGAO_SAV_CAP] = [
            Orgaos::ORGAO_SAV_CAP,
            Orgaos::ORGAO_SUPERIOR_SAV,
        ];

        $orgaosDisponiveis = [];
        
        foreach($orgaosEncaminhamento as $idOrgaoEncaminhar => $orgao) {
            if (in_array($idOrgaoLogado, $orgao)) {
                $orgaosDisponiveis[] = $idOrgaoEncaminhar;
            }
        }
        
        return implode(',', $orgaosDisponiveis);
    }
}
