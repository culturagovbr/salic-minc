<?php

class PrestacaoContas_Model_vwComprovacaoFinanceiraProjetoPorItemOrcamentario extends MinC_Db_Table_Abstract
{
    protected $_name = 'vwComprovacaoFinanceiraProjetoPorItemOrcamentario';
    protected $_schema = 'sac';
    protected $_primary = 'IdPRONAC';
    /* --COMPROVAÇÃO CONSOLIDADA POR PRODUTO */
    public function consolidacaoPorProduto($idPronac)
    {
        $cols = new Zend_Db_Expr("
            CASE WHEN cdProduto = 0
                THEN 'Administra&ccedil;&atilde;o do Projeto'
                ELSE b.Descricao END
            AS dsProduto,
                COUNT(*) AS qtComprovantes,
                SUM(vlComprovacao) AS vlComprovado,
                (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->joinLeft(
            array('b'=>'Produto'),
            '(a.cdProduto = b.Codigo)',
            null,
            $this->schema
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->group('IdPRONAC');
        $select->group('b.Descricao');
        $select->group('cdProduto');
        /* echo $select;die; */

        return $this->fetchAll($select);
    }

    public function consolidadoPorEtapa($idPronac)
    {
        $cols = new Zend_Db_Expr("
            IdPRONAC,
            b.Descricao,
            COUNT(*) AS qtComprovantes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->joinLeft(
            array('b'=>'tbPlanilhaEtapa'),
            '(a.cdEtapa = b.idPlanilhaEtapa)',
            null,
            $this->schema
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->group('IdPRONAC');
        $select->group('b.Descricao');
        $select->group('b.nrOrdenacao');

        $select->order('b.nrOrdenacao');

        return $this->fetchAll($select);
    }

    /* MAIORES ITENS ORCÇAMENTÁRIOS COMPROVADOS */
    public function maioresItensComprovados($idPronac)
    {
        $cols = new Zend_Db_Expr("
            TOP 30 IdPRONAC,
            b.Descricao,
            COUNT(*) AS qtComprovantes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->joinLeft(
            array('b'=>'tbPlanilhaItens'),
            '(a.idPlanilhaItem = b.idPlanilhaItens)',
            null,
            $this->schema
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->group('a.IdPRONAC');
        $select->group('b.Descricao');

        $select->order('SUM(vlComprovacao) DESC');

        return $this->fetchAll($select);
    }

    /* COMPROVAÇÃO CONSOLIDADA POR UF E MUNICIPIO */
    public function comprovacaoConsolidadaUfMunicipio($idPronac)
    {
        $cols =new Zend_Db_Expr("
            IdPRONAC,
            b.UF,
            b.Municipio,
            COUNT(*) AS qtComprovantes ,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->join(
            array('b'=>'vUFMunicipio'),
            '(a.cdUF = b.idUF AND a.cdCidade = b.idMunicipio)',
            null,
            'Agentes'
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->group('IdPRONAC');
        $select->group('b.UF');
        $select->group('b.Municipio');

        return $this->fetchAll($select);
    }

    /* MAIORES COMPROVAÇÕES POR TIPO DE DOCUMENTOS COMPROBATÓRIO */
    public function maioresComprovacaoTipoDocumento($idPronac)
    {
        $cols =new Zend_Db_Expr("
            TOP 10 IdPRONAC,
            tpDocumento,
            nrComprovante,
            c.Descricao AS nmFornecedor,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->join(
            array('b'=>'Agentes'),
            '(a.nrCNPJCPF = b.CNPJCPF)',
            null,
            'Agentes'
        );

        $select->join(
            array('c'=>'Nomes'),
            '(b.idAgente = c.idAgente)',
            null,
            'Agentes'
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->group('IdPRONAC');
        $select->group('tpDocumento');
        $select->group('nrComprovante');
        $select->group('c.Descricao');

        $select->order('SUM(vlComprovacao) DESC');

        return $this->fetchAll($select);
    }

    /* MAIORES COMPROVAÇÕES POR TIPO DE DOCUMENTOS DE PAGAMENTO */
    public function comprovacaoTipoDocumentoPagamento($idPronac)
    {
        $cols =new Zend_Db_Expr("
            TOP 10 IdPRONAC,
            tpFormaDePagamento, 
            nrDocumentoDePagamento,
            c.Descricao AS nmFornecedor,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->join(
            array('b'=>'Agentes'),
            '(a.nrCNPJCPF = b.CNPJCPF)',
            null,
            'Agentes'
        );

        $select->join(
            array('c'=>'Nomes'),
            '(b.idAgente = c.idAgente)',
            null,
            'Agentes'
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->group('IdPRONAC');
        $select->group('tpFormaDePagamento');
        $select->group('nrDocumentoDePagamento');
        $select->group('c.Descricao');

        $select->order('SUM(vlComprovacao) DESC');

        return $this->fetchAll($select);
    }

    /* MAIORES POR FORNECEDORES DO PROJETO CULTURAL */
    public function maioresFornecedoresProjeto($idPronac)
    {
        $cols =new Zend_Db_Expr("
            TOP 20 IdPRONAC,
            nrCNPJCPF,
            c.Descricao AS nmFornecedor,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->join(
            array('b'=>'Agentes'),
            '(a.nrCNPJCPF = b.CNPJCPF)',
            null,
            'Agentes'
        );

        $select->join(
            array('c'=>'Nomes'),
            '(b.idAgente = c.idAgente)',
            null,
            'Agentes'
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->group('IdPRONAC');
        $select->group('nrCNPJCPF');
        $select->group('c.Descricao');

        $select->order('SUM(vlComprovacao) DESC');

        return $this->fetchAll($select);
    }

    /* PROPONENTE FORNECEDOR DE ITEM PARA O PROJETO CULTURAL */
    public function fornecedorItemProjeto($idPronac)
    {
        $cols =new Zend_Db_Expr("
            a.IdPRONAC,
            nrCNPJCPF,
            c.Descricao AS nmFornecedor,
            e.Descricao as Etapa,
            Item,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(a.idPronac)) * 100 AS PercComprovado
        ");

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        $select->join(
            array('b'=>'Agentes'),
            '(a.nrCNPJCPF = b.CNPJCPF)',
            null,
            'Agentes'
        );

        $select->join(
            array('c'=>'Nomes'),
            '(b.idAgente  = c.idAgente)',
            null,
            'Agentes'
        );

        $select->join(
            array('d'=>'Projetos'),
            '(a.IdPRONAC  = d.IdPRONAC)',
            null,
            'sac'
        );

        $select->join(
            array('e'=>'tbPlanilhaEtapa'),
            '(a.cdEtapa = e.idPlanilhaEtapa)',
            null,
            'sac'
        );

        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->where('b.CNPJCPF = d.CgcCpf');

        $select->group('a.IdPRONAC');
        $select->group('nrCNPJCPF');
        $select->group('c.Descricao');
        $select->group('e.Descricao');
        $select->group('Item');

        $select->order('e.Descricao');
        $select->order('Item');

        return $this->fetchAll($select);
    }

    public function itensOrcamentariosImpugnados($idPronac)
    {
        $cols =[
            'IdPRONAC',
            'Pronac',
            'NomeProjeto',
            'Produto',
            'Etapa',
            'Item',
            'stItemAvaliado',
            'Documento',
            'nrComprovante',
            'tpFormaDePagamento',
            'nrDocumentoDePagamento',
            'dsJustificativa',
            'vlComprovado'
        ];

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            'vwItemOrcamentarioImpugnado',
            $cols,
            $this->_schema
        );

        $select->where('IdPRONAC = ?', $idPronac);

        $select->order('Produto');
        $select->order('Etapa');
        $select->order('Item');

        return $this->fetchAll($select);
    }
}
