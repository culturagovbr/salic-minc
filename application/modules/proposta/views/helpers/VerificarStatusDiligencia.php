<?php
/**
 * Helper para verificar o status da dilig�ncia
 * @author Equipe RUP - Politec
 * @since 16/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2011 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_VerificarStatusDiligencia
{
    /**
     * M�todo para verificar o status da diligencia
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function verificarStatusDiligencia($idPronac = null, $idDiligencia = null)
    {
        if (isset($idPronac) && !empty($idPronac)) :

            $Diligencia = new Diligencia();

        // busca as dilig�ncias respondidas
        $buscarDiligenciaResp = $Diligencia->buscar(array('idPronac = ?' => $idPronac, 'DtResposta ?' => array(new Zend_Db_Expr('IS NULL'))));
        $buscarDiligenciaRespNaoEnviada = $Diligencia->buscar(array('idDiligencia = ?' => $idDiligencia, 'DtResposta ?' => array(new Zend_Db_Expr('IS NOT NULL')), 'stEnviado = ?' => 'N'));

        if (count($buscarDiligenciaResp->toArray()) > 0 || count($buscarDiligenciaRespNaoEnviada->toArray()) > 0) :
                return 'D'; else :
                return 'A';
        endif; elseif (isset($idDiligencia) && !empty($idDiligencia)) :

            $Diligencia = new Diligencia();

        // busca as dilig�ncias respondidas
        $buscarDiligenciaResp = $Diligencia->buscar(array('idDiligencia = ?' => $idDiligencia, 'DtResposta ?' => array(new Zend_Db_Expr('IS NULL'))));
        $buscarDiligenciaRespNaoEnviada = $Diligencia->buscar(array('idDiligencia = ?' => $idDiligencia, 'DtResposta ?' => array(new Zend_Db_Expr('IS NOT NULL')), 'stEnviado = ?' => 'N'));

        if (count($buscarDiligenciaResp->toArray()) > 0 || count($buscarDiligenciaRespNaoEnviada->toArray()) > 0) :
                return 'D'; else :
                return 'A';
        endif; else :
            return 'D';
        endif;
    } // fecha m�todo verificarStatusDiligencia()
} // fecha class
