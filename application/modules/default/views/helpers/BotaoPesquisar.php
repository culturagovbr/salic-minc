<?php
/**
 * Bot�o Pesquisar Acess�vel
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2010 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_BotaoPesquisar
{
    /**
     * M�todo bot�o pesquisar
     * @access public
     * @param string $form (nome do formul�rio)
     * @return string $botao
     */
    public function botaoPesquisar($form)
    {
        $botao = "
			<script type=\"text/javascript\">
			<!--
				document.write('<div class=\"right_busca\"><a href=\"#\" onclick=\"document.$form.submit();\" title=\" Pesquisar \"></a></div>');
			//-->
			</script>
			<noscript>
				<div class=\"right\"></div>
				<input type=\"submit\" title=\" Pesquisar \" class=\"btn_pesquisar\" value=\" \" />
			</noscript>";

        return $botao;
    } // fecha m�todo botaoPesquisar()
} // fecha class
