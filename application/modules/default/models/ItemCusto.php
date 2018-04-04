<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ItemCusto
 *
 * @author 01610881125
 */
class ItemCusto extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbItemCusto';
    protected $_schema  = 'scSAC';

    public function inserirItemCusto($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarItemCusto($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarItemCusto($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }
}
