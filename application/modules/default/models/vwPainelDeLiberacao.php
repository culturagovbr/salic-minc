<?php

class vwPainelDeLiberacao extends MinC_Db_Table_Abstract
{

    protected $_schema = 'SAC';
    protected $_name   = 'vwPainelDeLiberacao';
    protected $_primary = 'IdPRONAC';


    public function listaRelatorios($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('*')
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($count) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        
        return $this->fetchAll($select);
    }
}
