<?php

class EnviaemailController extends MinC_Controller_Action_Abstract
{
    public static function enviaEmail($body, $from, $to)
    {
        $config = array(
            'auth' => 'login',
            'username' => 'minc\01373930160',
            'password' => '13092435',
            'ssl' => 'tls',
            'port' => 25,
        );
        $transport = new Zend_Mail_Transport_Smtp('smtp.cultura.gov.br', $config);

        try {
            $email = new Zend_Mail();
            $email->setBodyHtml($body);
            $email->setFrom($from);
            $email->addTo($to);
            $email->send($transport);
        } catch (Zend_Exception $e) {
            xd($e);
        }
    }
}
