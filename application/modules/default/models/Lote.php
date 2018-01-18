<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Lote
 *
 * @author augusto
 */
class Lote extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name =  'tbLote';

    public function inserirLote($dados)
    {
        $insert = $this->insert($dados);
        return $insert;
    }
}
