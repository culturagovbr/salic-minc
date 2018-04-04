<?php
/**
 * Helper para verificar o ícone da diligência do projeto
 * @author Equipe RUP - Politec
 * @since 23/11/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_IconeDiligencia
{
    /**
     * Método para exibir a imagem da diligencia do projeto
     * @access public
     * @param integer $diligencia
     * @return string
     */
    public function iconeDiligencia($diligencia, $idPronac, $tpDiligencia, $situacao)
    {
        switch ($diligencia) :
            case 0:
                $img   = "notice1";
        $title = "A Diligenciar";
        break;
        case 1:
                $img   = "notice";
        $title = "Diligenciado";
        break;
        case 2:
                $img   = "notice3";
        $title = "Dilig&ecirc;ncia respondida";
        break;
        case 3:
                $img   = "notice2";
        $title = "Dilig&ecirc;ncia n&atilde;o respondida";
        break;
        endswitch;

        $url = Zend_Controller_Front::getInstance()->getBaseUrl();

        return "<a href='$url/diligenciar/listardiligenciaanalista?idPronac=" . $idPronac . "&tpDiligencia=" . $tpDiligencia . "&situacao=" . $situacao . "' title='$title' target='_blank'><img src='$url/public/img/$img.png' alt='$title' title='$title' width='23px' /></a>";
    } // fecha método iconeDiligencia()
} // fecha class
