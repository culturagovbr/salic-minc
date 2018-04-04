<?php
/**
 * Description of ArquivoPagamentoParecerista
 *
 * @author Tarcisio Angelo
 */

class tbAssinantes extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'tbAssinantes';
    protected $_primary = 'idAssinantes';

    public function listarAssinantes($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
                        array('a.idAssinantes')
        );

        $select->joinInner(
            array('ag'=>'Agentes'),
            "a.idAgente = ag.idAgente",
                            array('ag.idAgente'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('nm'=>'Nomes'),
            "ag.idAgente = nm.idAgente",
                            array('nm.Descricao as Nome'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('v'=>'Verificacao'),
            "a.idCargo = v.idVerificacao",
                            array('v.Descricao as Cargo'),
            'AGENTES.dbo'
        );


        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('nm.Descricao');



        return $this->fetchAll($select);
    }


    public function listarNaoAssinantes()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('ag'=>'Agentes'),
                            array('ag.idAgente','ag.CNPJCPF'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('v'=>'Visao'),
            "ag.idAgente = v.idAgente",
                            array('v.Visao'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('nm'=>'Nomes'),
            "ag.idAgente = nm.idAgente",
                            array('nm.Descricao as Nome'),
            'AGENTES.dbo'
        );

        $select->where('v.Visao in (?)', array(146,266));

        $select->order('nm.Descricao');



        return $this->fetchAll($select);
    }

    public function listarCargos()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('v'=>'Verificacao'),
                            array('v.idVerificacao as idCargo','v.Descricao as Cargo'),
            'AGENTES.dbo'
        );

        $select->where('v.IdTipo = ?', 27);

        $select->order('v.Descricao');



        return $this->fetchAll($select);
    }
}
