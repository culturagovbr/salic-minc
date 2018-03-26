<?php

class tbRelatorioConsolidado extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbRelatorioConsolidado";


    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    public function alterarDados($dados, $where)
    {
        $where = "idRelatorioConsolidado = " . $where;
        return $this->update($dados, $where);
    }

    public function buscarRelatorioPronac($idpronac)
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
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }

    public function consultarDados($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>$this->_name),
                array('*','a.stDocumento as FNC')
        );
        $select->where("a.idRelatorio = ?", $idRelatorio);
        $select->joinInner(
                array('b' => 'tbDescricaoRelatorioConsolidado'),
                'b.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*', 'b.dsJustificativaAcompanhamento as JustificativaAcompanhamento')
        );
        $select->joinLeft(
                array('c' => 'tbDocumentoAceitacao'),
                'c.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*')
        );
        
        return $this->fetchAll($select);
    }

    public function consultarDados2($idRelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>$this->_name),
                array('*')
        );
        $select->where("a.idRelatorioConsolidado = ?", $idRelatorio);
        $select->joinInner(
                array('b' => 'tbDescricaoRelatorioConsolidado'),
                'b.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*', 'b.dsJustificativaAcompanhamento as JustificativaAcompanhamento')
        );
        $select->joinLeft(
                array('c' => 'tbDocumentoAceitacao'),
                'c.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*')
        );

        
        return $this->fetchAll($select);
    }

    public function consultarDadosPronac($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>$this->_name),
                array('*')
        );
        $select->joinInner(
                array('b' => 'tbRelatorio'),
                'b.idRelatorio = a.idRelatorio',
                array('')
        );
        $select->joinInner(
                array('c' => 'tbDocumentoAceitacao'),
                'c.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('')
        );
        $select->joinInner(
                array('d' => 'tbDocumento'),
                'd.idDocumento = c.idDocumento',
                array('idArquivo'),
                'BDCORPORATIVO.scCorp'
        );
        $select->joinInner(
                array('e' => 'tbArquivo'),
                'e.idArquivo = d.idArquivo',
                array('nmArquivo'),
                'BDCORPORATIVO.scCorp'
        );
        $select->joinLeft(
                array('f' => 'tbDescricaoRelatorioConsolidado'),
                'f.idRelatorioConsolidado = a.idRelatorioConsolidado',
                array('*')
        );
        $select->where("b.idPRONAC = ?", $idPronac);

        return $this->fetchAll($select);
    }

    public function buscardsrelatorioconsolidado($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        $slct->joinInner(
                array('d' => 'tb'),
                'd.idDocumento = c.idDocumento',
                array('idArquivo'),
                'BDCORPORATIVO.scCorp'
        );
        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($slct);
    }

    public function buscarstRelatorioPronac($idpronac, $status = 1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a'=>$this->_name)
            );
        $select->joinInner(
                    array('b' => 'tbRelatorio'),
                    'b.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->where('a.stRelatorioConsolidado = ?', $status);
        $select->where('b.tpRelatorio = ?', 'C');

        return $this->fetchAll($select);
    }


    public function buscarRelatorioConsolidado($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a'=>$this->_name)
            );
        $select->joinInner(
                    array('b' => 'tbRelatorio'),
                    'b.idRelatorio = a.idRelatorio',
                    array('*'),
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);
        $select->where('b.tpRelatorio = ?', 'C');

        return $this->fetchAll($select);
    }

    /**
     * Metodo para liberar a finalizacao do relatorio consolidado
     * Se estes 3 campos estiverem preenchidos eh sinal para que o toda a avaliacao do relatorio consolidado foi feita.
     */
    public function consultarAvaliacaoRelatorioConsolidado($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                    array('a'=>$this->_name),
                    array('a.stObjetivosMetas','a.stTermoProjeto','a.stProduto')
            );
        $select->joinInner(
                    array('b' => 'tbRelatorio'),
                    'b.idRelatorio = a.idRelatorio',
                    array(),
                    'SAC.dbo'
                    );
        $select->where('b.idPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }
}
