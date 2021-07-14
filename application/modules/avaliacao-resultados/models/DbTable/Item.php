<?php

class AvaliacaoResultados_Model_DbTable_Item extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbPlanilhaAprovacao';
    protected $_schema = 'SAC';
    protected $_primary = 'idPlanilhaAprovacao';

    public function buscarDadosDoItem(
        $idPronac = null,
        $uf = null,
        $produto = null,
        $idmunicipio = null,
        $idPlanilhaItem = null,
        $etapa = null
    )
    {
        if (!$idPronac) return;

            $select = $this->select();
        $select->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            [
                'idUFDespesa',
                'IdPRONAC',
                new Zend_Db_Expr('a.idMunicipioDespesa AS cdCidade'),
                new Zend_Db_Expr('
                    sac.dbo.fnVlAprovado_Fonte_Produto_Etapa_Local_Item ( a.idPronac,
                        a.nrFonteRecurso,
                        a.idProduto,
                        a.idEtapa,
                        a.idUFDespesa,
                        a.idMunicipioDespesa,
                        a.idPlanilhaItem
                    ) AS vlAprovado'),
                new Zend_Db_Expr('
                    sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item (
                        a.idPronac,
                        a.nrFonteRecurso,
                        a.idProduto,
                        a.idEtapa,
                        a.idUFDespesa,
                        a.idMunicipioDespesa,
                        a.idPlanilhaItem
                    ) AS vlComprovado'),
                new Zend_Db_Expr('
                    sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item_Validado (
                        a.idPronac,
                        a.nrFonteRecurso,
                        a.idProduto,
                        a.idEtapa,
                        a.idUFDespesa,
                        a.idMunicipioDespesa,
                        a.idPlanilhaItem
                    ) AS ComprovacaoValidada'),
            ],
            $this->_schema
        );

        $select->joinInner(
            ['b' => 'tbPlanilhaEtapa'],
            'a.idEtapa = b.idPlanilhaEtapa',
            [
                'tpCusto',
                new Zend_Db_Expr('b.idPlanilhaEtapa AS cdEtapa' ),
                new Zend_Db_Expr('b.Descricao AS Etapa'),
            ],
            $this->_schema
        );

        $select->joinInner(
            ['c' => 'tbPlanilhaItens'],
            'a.idPlanilhaItem = c.idPlanilhaItens',
            [
                'idPlanilhaItens',
                new Zend_Db_Expr('c.Descricao AS Item'),
            ],
            $this->_schema
        );

        $select->joinLeft(
            ['d' => 'produto'],
            'a.idproduto = d.Codigo',
            [
                new Zend_Db_Expr('ISNULL(d.Codigo,0) AS cdProduto'),
                new Zend_Db_Expr(utf8_decode('ISNULL(d.Descricao,\'Administração do Projeto\') AS Produto')),
            ],
            $this->_schema
        );

        $select->joinInner(
            ['e' => 'UF'],
            'a.idUFDespesa = e.idUF',
            [
                new Zend_Db_Expr('e.Sigla AS Uf'),
            ],
            'AGENTES.dbo'
        );

        $select->joinInner(
            ['f' => 'Municipios'],
            'a.idMunicipioDespesa = f.idMunicipioIBGE',
            [
                new Zend_Db_Expr('f.Descricao AS Cidade')
            ],
            'AGENTES.dbo'
        );

        $select->joinLeft(
            ['g' => 'tbComprovantePagamentoxPlanilhaAprovacao'],
            'a.idPlanilhaAprovacao    = g.idPlanilhaAprovacao',
            [],
            'BDCORPORATIVO.scSAC'
        );

        $select->joinLeft(
            ['h' => 'tbComprovantePagamento'],
            'g.idComprovantePagamento = h.idComprovantePagamento',
            [],
            'BDCORPORATIVO.scSAC'
        );

        $select->joinInner(
            ['i' => 'Projetos'],
            'a.IdPRONAC = i.IdPRONAC',
            [
                new Zend_Db_Expr('i.AnoProjeto+i.Sequencial AS Pronac'),
                'NomeProjeto',
            ],
            $this->_schema
        );


        $select->where('a.IdPRONAC = ?', $idPronac);


        if ($uf) {
            $select->where('e.Sigla = ?', $uf);
        }

        if ($idmunicipio) {
            $select->where('a.idMunicipioDespesa = ?', $idmunicipio);
        }

        if ($etapa) {
            $select->where('b.idPlanilhaEtapa = ?', $etapa);
        }

        if ($idPlanilhaItem) {
            $select->where('c.idPlanilhaItens = ?', $idPlanilhaItem);
        }

        if ($produto || ($produto == 0 && !is_null($produto) )) {
            $select->where('ISNULL( d.Codigo, 0 ) = ?', $produto);
        }


        return $this->fetchAll($select);
    }
}

