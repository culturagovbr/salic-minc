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

        $where = array();
        $where['IdPRONAC = ?'] = $idPronac;
        $where['idTipoDoAtoAdministrativo = ?'] = $idTipoDoAtoAdministrativo;
        $where['stEstado = ?'] = 1;
        $result = $objDocumentoAssinatura->buscar($where);
        
        return $result[0]['idDocumentoAssinatura'];
    }
}
