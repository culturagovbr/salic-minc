<?php

class TitulacaoConselheiro extends MinC_Db_Table_Abstract
{
    protected $_banco = 'agentes';
    protected $_schema = 'agentes';
    protected $_name = 'tbTitulacaoConselheiro';

    public function buscarTitulacaoConselheiro($where = null, $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('TC' => $this->_name),
            array(
                'TC.stTitular',
                'TC.idAgente'
            )
        );
        $select->joinInner(
            array('ar' => 'Area'),
            'ar.Codigo = TC.cdArea',
            array('ar.Descricao as Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('nm' => 'Nomes'),
            'nm.idagente = tc.idAgente',
            array('nm.Descricao as nome'),
            'Agentes.dbo'
        );
        if (isset($where)) {
            $keys = array_keys($where);
            $num = 0;
            foreach ($where as $formar) {
                $select->where($keys[$num] . ' = ?', $formar);
                $num++;
            }
        }

        $select->order($order);
        return $this->fetchAll($select);
    }

    public function buscarAreaConselheiro($idAgente = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('C' => $this->_name),
            array('C.idAgente',
                'C.cdArea',
                'C.stTitular',
                'C.stConselheiro'
            )
        );
        $select->joinInner(
            array('Ar' => 'Area'),
            'Ar.Codigo = C.cdArea',
            array(
                'Ar.Descricao as Area'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            ['Agente' => 'Agentes'],
            'Agente.idAgente = C.idAgente',
            ['Agente.CNPJCPF'],
            $this->getSchema('Agentes')
        );
        $select->joinInner(
            array('Nm' => 'Nomes'),
            'Nm.idAgente = C.idAgente',
            array('Nm.Descricao as Nome')
        );
        $select->where('C.stConselheiro = ?', 'A');
        if (isset($idAgente)) {
            $select->where('C.idAgente = ?', $idAgente);
            return $this->fetchRow($select);
        }
        $select->order('Ar.Descricao asc');

        return $this->fetchAll($select);
    }

    public function BuscarComponentes()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('T' => $this->_name),
            array("T.idAgente",
                new Zend_Db_Expr("(SELECT COUNT(SDPC.idPronac) as QTD
                              FROM BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao SDPC
                              INNER JOIN SAC.dbo.projetos pr on pr.IdPRONAC = SDPC.idPronac
                              WHERE pr.Situacao = 'C10' and SDPC.idAgente = T.idAgente
                              and SDPC.idPronac not in (select idpronac from BDCORPORATIVO.scSAC.tbPauta)
                      )  as QTD
                     "),
                "T.cdArea"
            )
        );
        $select->joinInner(
            array('N' => 'Nomes'),
            "N.idAgente =   T.idAgente",
            array('N.Descricao as Nome')
        );
        $select->joinInner(
            array('A' => 'Area'),
            'A.Codigo =  T.cdArea',
            array('A.Descricao as Area'),
            'SAC.dbo'
        );
        $select->where('T.stConselheiro = ?', 'A');
        $select->order('T.cdArea asc');

        return $this->fetchAll($select);
    }

    public function BuscarComponenteDesabilidados()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('C' => 'tbTitulacaoConselheiro'),
            array('C.idAgente')
        );
        $select->joinInner(
            ['Agente' => 'Agentes'],
            'Agente.idAgente = C.idAgente',
            ['Agente.CNPJCPF'],
            $this->getSchema('Agentes')
        );
        $select->joinInner(
            array('N' => 'Nomes'),
            'C.idAgente = N.idAgente',
            array('N.Descricao as Nome'),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('A' => 'Area'),
            'C.cdArea = A.Codigo',
            array('A.Descricao as Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('H' => 'tbHistoricoConselheiro'),
            'H.idConselheiro = N.idAgente',
            array(
                'H.idConselheiro',
                'H.dsJustificativa as Just',
                new Zend_Db_Expr('CONVERT(CHAR(10), H.dtHistorico,103) as Data')
            ),
            "BDCORPORATIVO.scAGENTES"
        );
        $select->where('H.stConselheiro = ?', 'I');
        $select->where('C.stConselheiro = ?', 'I');
        $select->where(new Zend_Db_Expr('H.dtHistorico in (select top 1 dtHistorico from BDCORPORATIVO.scAGENTES.tbHistoricoConselheiro where idConselheiro = C.idAgente order by dtHistorico desc )'));
        return $this->fetchAll($select);
    }

    public function buscarComponenteBalanceamento($area)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('T' => $this->_name),
            array("T.idAgente",
                new Zend_Db_Expr("(SELECT COUNT(SDPC.idPronac) as QTD
                              FROM BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao SDPC
                              INNER JOIN SAC.dbo.projetos pr on pr.IdPRONAC = SDPC.idPronac
                              WHERE pr.Situacao = 'C10' and SDPC.idAgente = T.idAgente
                              and SDPC.idPronac not in (select idpronac from BDCORPORATIVO.scSAC.tbPauta)
                      )  as QTD
                     "),
                "T.cdArea"
            )
        );
        $select->joinInner(
            array('N' => 'Nomes'),
            "N.idAgente =   T.idAgente",
            array('N.Descricao as Nome')
        );
        $select->joinInner(
            array('A' => 'Area'),
            'A.Codigo =  T.cdArea',
            array('A.Descricao as Area'),
            'SAC.dbo'
        );
        $select->where('T.stConselheiro = ?', 'A');
        $select->where('T.cdArea = ?', $area);
        $select->order('T.cdArea asc');
        $select->order('QTD asc');

        return $this->fetchAll($select);
    }

    public function alterarTitulacaoConselheiro($dados, $where)
    {
        $update = $this->update($dados, $where);
    }
}
