<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Orgaos
 *
 * @author augusto
 */
class TabelasOrgaos extends MinC_Db_Table_Abstract
{
    protected $_banco = 'TABELAS';
    protected $_name  = 'Orgaos';

    public function pesquisarUsuariosExterno($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('o'=>$this->_name),
                array('o.org_codigo',
                new Zend_Db_Expr('Tabelas.dbo.fnEstruturaOrgao(o.org_codigo, 0) + '."':'".' + SUBSTRING(pid_identificacao, 1, 60) orgao_nome'),)
        );
        $select->joinInner(
             array('p'=>'Pessoa_Identificacoes'),
                'p.pid_pessoa = o.org_pessoa',
                 array('o.org_pessoa'),
                 'Tabelas.dbo'

              );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
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
