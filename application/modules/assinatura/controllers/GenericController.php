<?php

abstract class Assinatura_GenericController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();
    }

    public function gerarPdfAction()
    {
        ini_set("memory_limit", "5000M");
        set_time_limit(30);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $cssContents = file_get_contents(APPLICATION_PATH . '/../public/library/materialize/css/materialize.css');
        $cssContents .= file_get_contents(APPLICATION_PATH . '/../public/library/materialize/css/materialize-custom.css');
        $html = $_POST['html'];

        $pdf = new mPDF('pt', 'A4', 12, '', 8, 8, 5, 14, 9, 9, 'P');
        $pdf->allow_charset_conversion = true;
        $pdf->WriteHTML($cssContents, 1);
        $pdf->charset_in = 'ISO-8859-1';

        if (!mb_check_encoding($html, 'ISO-8859-1')) {
            $pdf->charset_in = 'UTF-8';
        }

        $pdf->WriteHTML($html, 2);
        $pdf->Output();
        die;
    }
}
