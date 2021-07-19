<?php
/**
 * Classe para intera��o com a interface do usu�rio envolvento javascript
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package library
 * @subpackage library.MinC.JS
 * @copyright � 2010 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class JS
{
	/**
	 * Retorna um redirecionamento de url utilizando javascript
	 *
	 * @access public
	 * @static
	 * @param string $url
	 * @return string
	 */
	public static function redirecionarURL($url)
	{
		echo "<script type=\"text/javascript\">location.href=\"" . $url . "\"</script><noscript><a href=\"" . $url . "\" title=\"Voltar\">VOLTAR</a><br /></noscript>";
	}



	/**
	 * Retorna uma mensagem de alerta utilizando javascript
	 *
	 * @access public
	 * @static
	 * @param string $msg
	 * @return string
	 */
	public static function exibirMSG($msg)
	{
		echo "<script type=\"text/javascript\">alert(\"" . $msg . "\")</script><noscript>" . $msg . "<br /></noscript>";
	}
} // fecha class
