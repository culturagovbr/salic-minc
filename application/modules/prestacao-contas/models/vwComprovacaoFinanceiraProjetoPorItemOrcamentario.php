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

    public function comprovacoes(
        $idpronac,
        $idPlanilhaItem = null ,
        $stItemAvaliado = null,
        $codigoProduto = null,
        $idComprovantePagamento = null
    ) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('vwComprovacaoFinanceiraProjetoPorItemOrcamentario'),
            [
                '*',
                'nmFornecedor as Descricao',
                'nrCNPJCPF as CNPJCPF',
                'dsJustificativaProponente as dsJustificativa',
                'dsOcorrenciaDoTecnico as ocorrencia'
            ],
            $this->_schema
        );

        if ($stItemAvaliado) {
            $select->where('stItemAvaliado = ?', $stItemAvaliado);
        }

        if ($codigoProduto || ($codigoProduto == 0&& !is_null($codigoProduto))) {
            $select->where('cdProduto = ?', $codigoProduto);
        }

        if ($idComprovantePagamento) {
            $select->where('idComprovantePagamento = ?', $idComprovantePagamento);
        }

        if ($idPlanilhaItem) {
            $select->where('idPlanilhaItem = ?', $idPlanilhaItem);
        }

        $select->where('IdPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }

    public function comprovacoesInternacionais(
        $idpronac,
        $idPlanilhaItem = null ,
        $stItemAvaliado = null,
        $codigoProduto = null,
        $idComprovantePagamento = null,
        $etapa = null,
        $idUf = null,
        $idMunicipio
    ) {
        $cols = [
            "a.idPlanilhaAprovacao",
            "c.idComprovantePagamento",
            "a.IdPRONAC",
            new Zend_Db_Expr("d.AnoProjeto+d.Sequencial AS Pronac"),
            "d.NomeProjeto",
            "a.nrFonteRecurso",
            "a.idProduto as cdProduto",
            "a.idEtapa as cdEtapa",
            "a.idUFDespesa AS cdUF",
            "a.idMunicipioDespesa as cdCidade",
            "a.idPlanilhaItem",
            "h.Descricao as Item",
            "c.nrComprovante",
            "c.nrSerie",
            "e.CNPJCPF as nrCNPJCPF",
            new Zend_Db_Expr("ISNULL(f.Descricao,i.dsNome) as nmFornecedor"),
            new Zend_Db_Expr("
                CASE c.tpDocumento
                 WHEN 1 THEN ('Cupom Fiscal')
                 WHEN 2 THEN ('Guia de Recolhimento')
                 WHEN 3 THEN ('Nota Fiscal/Fatura')
                 WHEN 4 THEN ('Recibo de Pagamento')
                 WHEN 5 THEN ('RPA') ELSE 'INVOICE'
               END as tpDocumento"),
            "c.dtPagamento",
            new Zend_Db_Expr("
            CASE
             WHEN c.tpFormaDePagamento = '1' THEN 'Cheque'
             WHEN c.tpFormaDePagamento = '2' THEN 'Transferencia Bancária'
             WHEN c.tpFormaDePagamento = '3' THEN 'Saque/Dinheiro' ELSE ''
            END as tpFormaDePagamento"),
            "c.nrDocumentoDePagamento",
            "c.idArquivo",
            "g.nmArquivo",
            "c.dsJustificativa as dsJustificativaProponente",
           "b.dsJustificativa as dsOcorrenciaDoTecnico",
           "b.stItemAvaliado",
           new Zend_Db_Expr("CASE
             WHEN stItemAvaliado = 1 THEN 'Validado'
             WHEN stItemAvaliado = 3 THEN 'Impugnado'
             WHEN stItemAvaliado = 4 THEN 'Aguardando análise'
           END AS stAvaliacao"),
           "c.vlComprovacao"
       ];

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            ['a' => 'tbplanilhaaprovacao'],
            $cols,
            'SAC.dbo'
        );

        $select->join(
            ['b' => 'tbComprovantePagamentoxPlanilhaAprovacao'],
            '(a.idPlanilhaAprovacao    = b.idPlanilhaAprovacao)',
            null,
            'BDCORPORATIVO.scSAC'
        );

        $select->join(
            ['c' => 'tbComprovantePagamento'],
            '(b.idComprovantePagamento = c.idComprovantePagamento)',
            [
                "c.nrComprovante as numero",
                "c.dtEmissao as dataEmissao",
                "c.dtPagamento as dataPagamento",
                "c.nrSerie as serie",
                'c.tpDocumento as tipo'
            ],
            'BDCORPORATIVO.scSAC'
        );

        $select->join(
            ['d' => 'Projetos'],
            '(a.IdPRONAC = d.IdPRONAC)',
            null,
           'sac.dbo'
        );

        $select->joinLeft(
            ['e' => 'Agentes'],
            '(c.idFornecedor = e.idAgente)',
            null,
            'Agentes.dbo'
        );

        $select->joinLeft(
            ['f' => 'Nomes'],
            '(c.idFornecedor = f.idAgente)',
            null,
            'Agentes.dbo'
        );

        $select->joinLeft(
            ['g' => 'tbArquivo'],
            '(c.idArquivo  = g.idArquivo)',
            null,
            'BDCORPORATIVO.scCorp'
        );

        $select->join(
            ['h' => 'tbPlanilhaItens'],
            '(a.idPlanilhaItem = h.idPlanilhaItens)',
            null,
            'sac.dbo'
        );

        $select->join(
            ['i' => 'tbFonecedorExterior'],
            '(c.idFornecedorExterior = i.idFornecedorExterior)',
            [
                'i.dsEndereco as endereco',
                'i.idFornecedorExterior as id',
                'i.dsPais as pais'
            ],
            'BDCORPORATIVO.scSAC'
        );

        $select->where('a.nrFonteRecurso = ?', 109);

        $select->where(new Zend_Db_Expr('
            (sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item
            (a.idPronac,a.nrFonteRecurso,a.idProduto,
            a.idEtapa,a.idUFDespesa,
            a.idMunicipioDespesa,
            a.idPlanilhaItem)) > 0'
        ));

        /* if ($stItemAvaliado) { */
        /*     $select->where('stItemAvaliado = ?', $stItemAvaliado); */
        /* } */

        if ($codigoProduto || ($codigoProduto == 0 && !is_null($codigoProduto))) {
            $select->where('a.idProduto = ?', $codigoProduto);
        }

        /* if ($idComprovantePagamento) { */
        /*     $select->where('idComprovantePagamento = ?', $idComprovantePagamento); */
        /* } */

        if ($idPlanilhaItem) {
            $select->where('a.idPlanilhaItem = ?', $idPlanilhaItem);
        }

        if ($etapa) {
            $select->where('a.idEtapa = ?', $etapa);
        }

        if ($idUf) {
            $select->where('a.idUFDespesa = ?', $idUf);
        }

        if ($idMunicipio) {
            $select->where('a.idMunicipioDespesa = ?', $idMunicipio);
        }

        $select->where('a.IdPRONAC = ?', $idpronac);
        return $this->fetchAll($select);
    }

    public function comprovacoesNacionais(
        $idpronac,
        $idPlanilhaItem = null ,
        $stItemAvaliado = null,
        $codigoProduto = null,
        $idComprovantePagamento = null,
        $etapa = null,
        $idUf = null,
        $idMunicipio = null
    ) {
        /* var_dump($etapa);die; */
        $cols = [
            "a.idPlanilhaAprovacao",
            "c.idComprovantePagamento",
            "a.IdPRONAC",
//            new Zend_Db_Expr("d.AnoProjeto+d.Sequencial AS Pronac"),
//            "d.NomeProjeto",
            "a.nrFonteRecurso",
            "a.idProduto as cdProduto",
            "a.idEtapa as cdEtapa",
            "a.idUFDespesa AS cdUF",
            "a.idMunicipioDespesa as cdCidade",
            "a.idPlanilhaItem",
            "h.Descricao as Item",
            new Zend_Db_Expr("
                CASE c.tpDocumento
                 WHEN 1 THEN ('Cupom Fiscal')
                 WHEN 2 THEN ('Guia de Recolhimento')
                 WHEN 3 THEN ('Nota Fiscal/Fatura')
                 WHEN 4 THEN ('Recibo de Pagamento')
                 WHEN 5 THEN ('RPA') ELSE 'INVOICE'
               END as tpDocumento"),
            "c.dtPagamento",
            new Zend_Db_Expr("
            CASE
             WHEN c.tpFormaDePagamento = '1' THEN 'Cheque'
             WHEN c.tpFormaDePagamento = '2' THEN 'Transfer&ecirc;ncia Banc&aacute;ria'
             WHEN c.tpFormaDePagamento = '3' THEN 'Saque/Dinheiro' ELSE ''
            END as tpFormaDePagamento"),
            "c.nrDocumentoDePagamento",
            "c.idArquivo",
            "g.nmArquivo",
            "c.dsJustificativa as dsJustificativaProponente",
           "b.dsJustificativa as dsOcorrenciaDoTecnico",
           "b.stItemAvaliado",
           new Zend_Db_Expr("CASE
             WHEN stItemAvaliado = 1 THEN 'Validado'
             WHEN stItemAvaliado = 3 THEN 'Impugnado'
             WHEN stItemAvaliado = 4 THEN 'Aguardando an&aacute;lise'
           END AS stAvaliacao"),
           "c.vlComprovacao"
       ];

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            ['a' => 'tbplanilhaaprovacao'],
            $cols,
            'SAC.dbo'
        );

        $select->join(
            ['b' => 'tbComprovantePagamentoxPlanilhaAprovacao'],
            '(a.idPlanilhaAprovacao    = b.idPlanilhaAprovacao)',
            null,
            'BDCORPORATIVO.scSAC'
        );

        $select->join(
            ['c' => 'tbComprovantePagamento'],
            '(b.idComprovantePagamento = c.idComprovantePagamento)',
            [
                "c.tpDocumento as tipo",
                "c.nrComprovante as numero",
                "c.nrSerie as serie",
                "c.tpFormaDePagamento as forma",
                "c.dtEmissao as dataEmissao",
                "c.nrDocumentoDePagamento as numeroDocumento",
                "c.dsJustificativa as justificativa",
            ],
            'BDCORPORATIVO.scSAC'
        );

//        $select->join(
//            ['d' => 'Projetos'],
//            '(a.IdPRONAC = d.IdPRONAC)',
//            null,
//           'sac.dbo'
//        );

        $select->joinLeft(
            ['e' => 'Agentes'],
            '(c.idFornecedor = e.idAgente)',
            ['e.CNPJCPF'],
            'Agentes.dbo'
        );

        $select->joinLeft(
            ['f' => 'Nomes'],
            '(c.idFornecedor = f.idAgente)',
            ['f.Descricao as nmFornecedor', 'f.Descricao as nome'],
            'Agentes.dbo'
        );

        $select->joinLeft(
            ['g' => 'tbArquivo'],
            '(c.idArquivo  = g.idArquivo)',
            null,
            'BDCORPORATIVO.scCorp'
        );

        $select->join(
            ['h' => 'tbPlanilhaItens'],
            '(a.idPlanilhaItem = h.idPlanilhaItens)',
            null,
            'sac.dbo'
        );

        $select->where('a.nrFonteRecurso = ?', 109);
        $select->where('c.idFornecedorExterior is null');

        $select->where(new Zend_Db_Expr('
            (sac.dbo.fnVlComprovado_Fonte_Produto_Etapa_Local_Item
            (a.idPronac,a.nrFonteRecurso,a.idProduto,
            a.idEtapa,a.idUFDespesa,
            a.idMunicipioDespesa,
            a.idPlanilhaItem)) > 0'
        ));

        if ($stItemAvaliado) {
            $select->where('b.stItemAvaliado = ?', $stItemAvaliado);
        }

        if ($codigoProduto || ($codigoProduto == 0 && !is_null($codigoProduto))) {
            $select->where('a.idProduto = ?', $codigoProduto);
        }

        /* if ($idComprovantePagamento) { */
        /*     $select->where('idComprovantePagamento = ?', $idComprovantePagamento); */
        /* } */

        if ($idPlanilhaItem) {
            $select->where('a.idPlanilhaItem = ?', $idPlanilhaItem);
        }

        if ($etapa) {
            $select->where('a.idEtapa = ?', $etapa);
        }

        if ($idUf) {
            $select->where('a.idUFDespesa = ?', $idUf);
        }

        if ($idMunicipio) {
            $select->where('a.idMunicipioDespesa = ?', $idMunicipio);
        }

        $select->where('a.IdPRONAC = ?', $idpronac);

        return $this->fetchAll($select);
    }
}
