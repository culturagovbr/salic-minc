<?php
/**
 * Helper para verificar se projeto já foi assinado
 */

class Zend_View_Helper_IsProjetoJaAssinado
{
    /**
     * Método para verificar se projeto já foi assinado na fase atual
     * @access public
     * @param integer $idPronac
     * @param integer $idTipoDoAtoAdministrativo
     * @param integer $quantidadeMinimaAssinaturas
     * @return string
     */
    public function IsProjetoJaAssinado($idPronac, $idTipoDoAtoAdministrativo, $idPerfilDoAssinante)
    {
        $objAssinatura = new Assinatura_Model_DbTable_TbAssinatura();
        $assinaturas = $objAssinatura->obterAssinaturas($idPronac, $idTipoDoAtoAdministrativo);

        foreach ($assinaturas as $assinatura) {
            if ($assinatura['idPerfilDoAssinante'] == $idPerfilDoAssinante) {
                return true;
            }
        }
        return false;
    }
}
