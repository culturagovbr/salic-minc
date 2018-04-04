<?php
/**
 * Helper que retorna idDocumentoAssinatura para IdPronac
 */

class Zend_View_Helper_GetIdDocumentoAssinatura
{
    /**
     * Método para retornar idDocumentoAssinatura
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function GetIdDocumentoAssinatura($idPronac, $idTipoDoAtoAdministrativo)
    {
        $objDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        
        return $objDocumentoAssinatura->getIdDocumentoAssinatura($idPronac, $idTipoDoAtoAdministrativo);
    }
}
