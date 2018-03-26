<?php
/**
 * Helper para verificar o status da diligência
 * @author Equipe RUP - Politec
 * @since 16/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_VerificarStatusDiligencia
{
    /**
     * Método para verificar o status da diligencia
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function verificarStatusDiligencia($idPronac = null, $idDiligencia = null)
    {
        if (isset($idPronac) && !empty($idPronac)) :

            $Diligencia = new Diligencia();

        // busca as diligências respondidas
        $buscarDiligenciaResp = $Diligencia->buscar(array('idPronac = ?' => $idPronac, 'DtResposta ?' => array(new Zend_Db_Expr('IS NULL'))));
        $buscarDiligenciaRespNaoEnviada = $Diligencia->buscar(array('idDiligencia = ?' => $idDiligencia, 'DtResposta ?' => array(new Zend_Db_Expr('IS NOT NULL')), 'stEnviado = ?' => 'N'));

        if (count($buscarDiligenciaResp->toArray()) > 0 || count($buscarDiligenciaRespNaoEnviada->toArray()) > 0) :
                return 'D'; else :
                return 'A';
        endif; elseif (isset($idDiligencia) && !empty($idDiligencia)) :

            $Diligencia = new Diligencia();
                        
        // busca as diligências respondidas
        $buscarDiligenciaResp = $Diligencia->buscar(array('idDiligencia = ?' => $idDiligencia, 'DtResposta ?' => array(new Zend_Db_Expr('IS NULL'))));
        $buscarDiligenciaRespNaoEnviada = $Diligencia->buscar(array('idDiligencia = ?' => $idDiligencia, 'DtResposta ?' => array(new Zend_Db_Expr('IS NOT NULL')), 'stEnviado = ?' => 'N'));

        if (count($buscarDiligenciaResp->toArray()) > 0 || count($buscarDiligenciaRespNaoEnviada->toArray()) > 0) :
                return 'D'; else :
                return 'A';
        endif; else :
            return 'D';
        endif;
    } // fecha método verificarStatusDiligencia()
} // fecha class
