<?php
/**
 * Classe com a cor do registro de uma tabela ao passar o mouse em cima
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_CorLinhaOnMouseOver
{
    /**
     * Cor do registro de uma tabela ao passar o mouse em cima
     * @access public
     * @param string $bg
     * @return string
     */
    public function corLinhaOnMouseOver($bg = "#ffffcc")
    {
        $linha = "onmouseover=\"this.style.backgroundColor='". $bg ."'\" onfocus=\" \" ";
        $linha.= "onmouseout=\"this.style.backgroundColor=''\"   onblur=\" \"";
        return $linha;
    } // fecha método corLinhaOnMouserOver()
} // fecha class
