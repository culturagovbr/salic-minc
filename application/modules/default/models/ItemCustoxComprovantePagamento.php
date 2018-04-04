<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ItemCustoxComprovantePagamento
 *
 * @author 01610881125
 */
class ItemCustoxComprovantePagamento extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbItemCustoxComprovantePagamento';
    protected $_schema  = 'scSAC';

    public function inserirItemCustoxComprovantePagamento($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarItemCustoxComprovantePagamento($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarItemCustoxComprovantePagamento($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }
}
