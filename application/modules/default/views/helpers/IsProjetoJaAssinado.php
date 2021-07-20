<?php
/**
 * Helper para verificar se projeto j&aacute; foi assinado
 */

class Zend_View_Helper_IsProjetoJaAssinado
{
    /**
     * Método para verificar se projeto j&aacute; foi assinado na fase atual
     * @access public
     * @param integer $idPronac
     * @param integer $idTipoDoAtoAdministrativo
     * @param integer $quantidadeMinimaAssinaturas
     * @return string
     */
    public function IsProjetoJaAssinado($idPronac, $idTipoDoAtoAdministrativo, $idPerfilDoAssinante)
    {
        $tbDocumentoAssinatura = new Assinatura_Model_TbDocumentoAssinaturaMapper();

        return $tbDocumentoAssinatura->IsProjetoJaAssinado($idPronac, $idTipoDoAtoAdministrativo, $idPerfilDoAssinante);
    }
}
