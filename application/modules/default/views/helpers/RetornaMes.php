<?php
/**
 * Classe que retorna o Mês
 * @author Equipe RUP - Politec
 * @since 08/08/2011
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_RetornaMes
{
    /**
     * Mês
     * @access public
     * @param string $$mes
     * @return String
     */
    public function retornaMes($mes)
    {
        $mesString = 'Mês inválido';
        
        switch ($mes) {
        case 1:
            $mesString = 'Janeiro';
            break;
        case 2:
            $mesString = 'Fevereiro';
            break;
        case 3:
            $mesString = 'Março';
            break;
        case 4:
            $mesString = 'Abril';
            break;
        case 5:
            $mesString = 'Maio';
            break;
        case 6:
            $mesString = 'Junho';
            break;
        case 7:
            $mesString = 'Julho';
            break;
        case 8:
            $mesString = 'Agosto';
            break;
        case 9:
            $mesString = 'Setembro';
            break;
        case 10:
            $mesString = 'Outubro';
            break;
        case 11:
            $mesString = 'Novembro';
            break;
        case 12:
            $mesString = 'Dezembro';
            break;
    }
    
        return $mesString;
    } // fecha método formatarMilhar()
} // fecha class
