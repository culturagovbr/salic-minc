<?php
class Foo_Model_Foo extends Zend_Db_Table_Abstract
{
    protected $_name = "Foo";
    protected $_primary = "idFoo";

    public function __construct()
    {
    }

    public function listar()
    {
        return array('teste01', 'teste02');
    }
}
