<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Interessado
 *
 * @author augusto
 */
class Interessado extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'Interessado';



    public function Busca($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(array('tbr' => $this->_name));



        // adicionando clausulas where
        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchRow($slct);
    }
}
