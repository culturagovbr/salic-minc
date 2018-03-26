<?php
/**
 * Botão Pesquisar Acessível
 * @author Equipe RUP - Politec
 * @since 28/04/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_BotaoPesquisar
{
    /**
     * Método botão pesquisar
     * @access public
     * @param string $form (nome do formulário)
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
    } // fecha método botaoPesquisar()
} // fecha class
