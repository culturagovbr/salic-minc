<?php
class RealizarPrestacaoDeContasDAO extends Zend_Db_Table
{
    /* dados da tabela */
    protected $_schema  = "";
    protected $_name    = "";
    protected $_primary = "";

    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }
} 
