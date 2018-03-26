<?php
/**
 * Created by PhpStorm.
 * User: vinnyfs89
 * Date: 03/02/17
 * Time: 14:49
 */
class Qrcode_Bootstrap extends Zend_Application_Module_Bootstrap
{
    public function _initPath()
    {
        require_once APPLICATION_PATH . '/modules/qrcode/controllers/GenericController.php';
    }
}
