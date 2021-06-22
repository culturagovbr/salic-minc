<?php

/**
 * Created by PhpStorm.
 * User: vinnyfs89
 * Date: 03/02/17
 * Time: 14:49
 */
class Qrcode_QrcodeController extends Qrcode_GenericController
{
    public function init()
    {
        parent::init();
    }

    public function exibirAction()
    {
        $this->_helper->layout->disableLayout();

        $get = Zend_Registry::get('get');
        if (!$get->texto) {
            throw new Exception("Parâmetro 'texto' não informado.");
        }
        \PHPQRCode\QRcode::png($get->texto, false, 'L', 4, 2);
        die;
    }
}
