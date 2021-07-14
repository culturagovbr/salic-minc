<?php
/**
 * Helper para verificar o status da dilig�ncia
 * @author Equipe RUP - Politec
 * @since 16/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_StatusDiligencia
{
    /**
     * M�todo para verificar o status da diligencia
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function statusDiligencia($idPronac = null, $idProduto = null)
    {
        if (isset($idPronac) && !empty($idPronac)) :
            $where['idPronac = ?'] = $idPronac;
        endif;

        if (isset($idProduto) && !empty($idProduto)) :
            $where['idProduto = ?'] = $idProduto;
        endif;

        $diligencias = array();

        $Diligencia = new Diligencia();
        $buscar 	= $Diligencia->buscar($where);

        foreach ($buscar as $d):

            if ($d[0]->DtSolicitacao && $d[0]->DtResposta == null) {
                $diligencias['img'] = "notice.png";
                $diligencias['msg'] = "Diligenciado";
            } elseif ($d[0]->DtSolicitacao && $d[0]->DtResposta != null) {
                $diligencias['img'] = "notice3.png";
                $diligencias['msg'] = "Diligencia respondida";
            } elseif ($d[0]->DtSolicitacao && round(data::CompararDatas($d[0]->DtDistribuicao)) > $d[0]->tempoFimDiligencia) {
                $diligencias['img'] = "notice2.png";
                $diligencias['msg'] = "Diligencia Não respondida";
            } else {
                $diligencias['img'] = "notice1.png";
                $diligencias['msg'] = "A Diligenciar";
            }

        endforeach;

        return 'Não terminei';
    } // fecha m�todo verificarStatusDiligencia()
} // fecha class
