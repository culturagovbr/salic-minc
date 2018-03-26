<?php
/**
 * DAO vwPainelTecnicoAvaliacaoTrimestral
 * @since 17/12/2012
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwPainelTecnicoAvaliacaoTrimestral extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco  = 'SAC';
    protected $_schema = 'SAC';
    protected $_name   = 'vwPainelTecnicoAvaliacaoTrimestral';
    protected $_primary = 'IdPRONAC';


    public function listaRelatorios($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('*')
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
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
} // fecha class
