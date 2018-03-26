<?php

class tbRelatorioTrimestral extends MinC_Db_Table_Abstract
{
    protected $_schema = 'SAC';
    protected $_name = 'tbRelatorioTrimestral';

    public function buscarDadosRelatorio($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('*')
        );
        if ($idRelatorio) {
            $select->where('a.idRelatorio = ?', $idRelatorio);
        }

        return $this->fetchAll($select);
    }

    public function buscarRelatorioTrimestral($idRelatorioTrimestral)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('idRelatorioTrimestral',
                'idRelatorio',
                new Zend_Db_Expr('CAST(dsParecer AS TEXT) AS dsParecer'),
                new Zend_Db_Expr('CAST(dsObjetivosMetas AS TEXT) AS dsObjetivosMetas'),
                'dtCadastro',
                'stRelatorioTrimestral',
                'nrRelatorioTrimestral')
        );
        $select->where('a.idRelatorioTrimestral = ?', $idRelatorioTrimestral);

        return $this->fetchAll($select);
    }

    public function buscarTodosRelatoriosTrimestrais($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            null,
            $this->_schema
        );
        $select->joinInner(
            array('b' => 'tbRelatorio'),
            'b.idRelatorio = a.idRelatorio',
            array('*'),
            $this->_schema
        );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->where('b.tpRelatorio = ?', 'T');
        $select->where('a.stRelatorioTrimestral != ?', 1);
        return $this->fetchAll($select);
    }

    public function buscarRelatorioPronac($idpronac, $status = 1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            null,
            $this->_schema
        );
        $select->joinInner(
            array('b' => 'tbRelatorio'),
            'b.idRelatorio = a.idRelatorio',
            array('*'),
            $this->_schema
        );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->where('a.stRelatorioTrimestral = ?', $status);
        $select->where('b.tpRelatorio = ?', 'T');

        return $this->fetchAll($select);
    }

    public function buscarUltimoNrRel($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name)
        );
        $select->joinInner(
            array('b' => 'tbRelatorio'),
            'b.idRelatorio = a.idRelatorio',
            array('*'),
            $this->_schema
        );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->order('b.idRelatorio desc');

        return $this->fetchAll($select);
    }

    public function buscarstRel($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name)
        );
        $select->joinInner(
            array('b' => 'tbRelatorio'),
            'b.idRelatorio = a.idRelatorio',
            array('*'),
            $this->_schema
        );
        $select->where('b.idRelatorio = ?', $idRelatorio);
        $select->where('a.stRelatorioTrimestral = ?', 1);

        return $this->fetchAll($select);
    }

    public function buscarUsandoCAST($idRelatorioTrimestral)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                'a.idRelatorioTrimestral',
                'a.idRelatorio',
                new Zend_Db_Expr('CAST(a.dsParecer AS TEXT) AS dsParecer'),
                new Zend_Db_Expr('CAST(a.dsObjetivosMetas AS TEXT) AS dsObjetivosMetas'),
                'a.dtCadastro',
                'a.stRelatorioTrimestral',
                'a.nrRelatorioTrimestral'
            )
        );
        $select->where('a.idRelatorioTrimestral = ?', $idRelatorioTrimestral);

        return $this->fetchAll($select);
    }

    public function buscarRelatorioMenu($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name)
        );
        $select->joinInner(
            array('b' => 'tbRelatorio'),
            'b.idRelatorio = a.idRelatorio',
            array('*'),
            $this->_schema
        );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->where('a.stRelatorioTrimestral = ?', 1);
        $select->where('b.tpRelatorio = ?', 'T');

        return $this->fetchAll($select);
    }
}
