<?php
/**
 * Bot�o Pesquisar Acess�vel
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_Mes
{
    public function mes($valormes)
    {
        switch ($valormes) {
                case "01":    $mes = 'Janeiro';     break;
                case "02":    $mes = 'Fevereiro';   break;
                case "03":    $mes = 'Mar&ccedil;o';       break;
                case "04":    $mes = 'Abril';       break;
                case "05":    $mes = 'Maio';        break;
                case "06":    $mes = 'Junho';       break;
                case "07":    $mes = 'Julho';       break;
                case "08":    $mes = 'Agosto';      break;
                case "09":    $mes = 'Setembro';    break;
                case "10":    $mes = 'Outubro';     break;
                case "11":    $mes = 'Novembro';    break;
                case "12":    $mes = 'Dezembro';    break;
                }
        return $mes;
    }
} // fecha class
