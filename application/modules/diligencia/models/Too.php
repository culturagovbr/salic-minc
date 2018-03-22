<?php
/**
 * Foo_Model_Too
 *
 * @uses Zend
 * @uses _Db_Table_Abstract
 * @package Foo
 * @version 0.1
 * @author  wouerner <wouerner@gmail.com>
 */
class Foo_Model_Too extends Zend_Db_Table_Abstract
{
    protected $_name = "Foo";
    protected $_primary = "idFoo";

    public function __construct()
    {
    }

    public function listar()
    {
        return array('teste', 'teste');
    }
}
