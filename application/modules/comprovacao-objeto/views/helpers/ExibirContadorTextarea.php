<?php
/**
 * Exibe o contador de caracteres (quantidade de caracteres restantes)
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_ExibirContadorTextarea
{
    /**
     * Método com os parametros para exibição do contador
     * @access public
     * @param string $nome (nome do campo)
     * @param integer $tamanho (maxlenght do input)
     * @param integer $limite (quantidade máxima de caracteres - restantes)
     * @param integer $qtd (quantidade de caracteres preenchidos por default)
     * @return string $campo
     */
    public function exibirContadorTextarea($nome, $tamanho, $limite, $qtd = 0, $largura = '4%')
    {
        $limite -= $qtd; // atualiza a quantidade de caracteres restantes

        $campo = "<br />
				<p>
					<label for=\"" . $nome . "\" style=\"font-weight:normal;\">faltam</label> <input type=\"text\" readonly=\"readonly\" name=\"" . $nome . "\" id=\"" . $nome . "\"  
					maxlength=\"" . $tamanho . "\" value=\"" . $limite . "\" 
					style=\"width:$largura;0;background:none;\" /> 
					caracteres.
				</p>";

        return $campo;
    } // fecha método exibirContadorTextarea()
} // fecha class
