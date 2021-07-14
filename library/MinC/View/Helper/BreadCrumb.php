<?php
/**
 * Pega o diret�rio principal da aplica��o
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class MinC_View_Helper_BreadCrumb extends Zend_View_Helper_Abstract
{
	/**
	 * Pega o diret�rio raiz do sistema
	 * @access public
	 * @param void
	 * @return string
	 */
	public function BreadCrumb($arrValues)
	{
	    $strHtml = <<<HTML
<nav class="breadcrumb">
    <div class="nav-wrapper" style="padding: 0 15px 0 15px">
        <div class="col s12">
HTML;
    foreach ($arrValues as $arrValue) {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        if ($arrValue['description']) {
            $strTooltip = "class=\"breadcrumb tooltipped\" data-position=\"top\" data-delay=\"50\" data-tooltip=\"{$arrValue['description']}\"";
        } else {
            $strTooltip = "class=\"breadcrumb\"";
        }
        $strHtml .= "<a href=\"{$baseUrl}{$arrValue['url']}\" $strTooltip>{$arrValue['title']}</a>";
    }
        $strHtml .= <<<HTML
        </div>
    </div>
</nav>
HTML;
		echo $strHtml;
	}
}
