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
    protected $_schema = 'SAC';
    protected $_name  = 'Interessado';
    protected $_primary = 'CgcCpf';

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

    public function obterContatosInteressado($where=array())
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from(
            ['i' => $this->_name],
            [
                'TelefoneResidencial',
                'TelefoneComercial',
                'TelefoneCelular',
                'CorreioEletronico as Email'
            ], 
            $this->_schema    
        );

        foreach ($where as $coluna=>$valor) {
            $query->where($coluna, $valor);
        }
        return $this->fetchRow($query);
    }
}
