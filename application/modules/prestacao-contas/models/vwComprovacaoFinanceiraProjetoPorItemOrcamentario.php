<?php

class PrestacaoContas_Model_vwComprovacaoFinanceiraProjetoPorItemOrcamentario extends MinC_Db_Table_Abstract
{
    protected $_name = 'vwComprovacaoFinanceiraProjetoPorItemOrcamentario';
    protected $_schema = 'sac';
    protected $_primary = 'IdPRONAC';
    /* --COMPROVAÇÃO CONSOLIDADA POR PRODUTO */
    public function consolidacaoPorProduto($idPronac)
    {
        $cols ="
            CASE WHEN cdProduto = 0
                THEN 'Administra&ccedil;&atilde;o do Projeto'
                ELSE b.Descricao END
            AS dsProduto,
                COUNT(*) AS qtComprovantes,
                SUM(vlComprovacao) AS vlComprovado,
                (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ";

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

        return $this->fetchAll($select);
    }

    /* --COMPROVAÇÃO CONSOLIDADA POR ETAPA */
    /*     SELECT IdPRONAC,b.Descricao,COUNT AS qtComprovantes,SUM(vlComprovacao) AS vlComprovado, */
    /*         (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado */
    /*         FROM       sac.dbo.vwComprovacaoFinanceiraProjetoPorItemOrcamentario a */
    /*         INNER JOIN sac.dbo.tbPlanilhaEtapa b on (a.cdEtapa = b.idPlanilhaEtapa) */
    /*         WHERE IdPRONAC = 168849 */
    /*         GROUP BY IdPRONAC,b.Descricao,b.nrOrdenacao */
    /*         ORDER BY b.nrOrdenacao */
    public function consolidadoPorEtapa($idPronac)
    {
        $cols ="
            IdPRONAC,
            b.Descricao,
            COUNT(*) AS qtComprovantes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ";

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

    /*         -- MAIORES ITENS ORCÇAMENTÁRIOS COMPROVADOS */
    /*         SELECT TOP 30 IdPRONAC,b.Descricao,COUNT AS qtComprovantes ,SUM(vlComprovacao) AS vlComprovado, */
    /*             (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado */
    /*             FROM       sac.dbo.vwComprovacaoFinanceiraProjetoPorItemOrcamentario a */
    /*             INNER JOIN sac.dbo.tbPlanilhaItens b on (a.idPlanilhaItem = b.idPlanilhaItens) */
    /*             WHERE IdPRONAC = 185898 */
    /*             GROUP BY a.IdPRONAC,b.Descricao */
    /*             ORDER BY SUM(vlComprovacao) DESC */
    public function maioresItensComprovados($idPronac)
    {
        $cols ="
            TOP 30 IdPRONAC,
            b.Descricao,
            COUNT(*) AS qtComprovantes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        /*             INNER JOIN sac.dbo.tbPlanilhaItens b on (a.idPlanilhaItem = b.idPlanilhaItens) */
        $select->joinLeft(
            array('b'=>'tbPlanilhaItens'),
            '(a.idPlanilhaItem = b.idPlanilhaItens)',
            null,
            $this->schema
        );

        $select->where('IdPRONAC = ?', $idPronac);
        /*             GROUP BY a.IdPRONAC,b.Descricao */
        /*             ORDER BY SUM(vlComprovacao) DESC */

        $select->group('a.IdPRONAC');
        $select->group('b.Descricao');

        $select->order('SUM(vlComprovacao) DESC');

        return $this->fetchAll($select);
    }

    /*             --COMPROVAÇÃO CONSOLIDADA POR UF E MUNICIPIO */
    /*             SELECT IdPRONAC,b.UF,b.Municipio,COUNT AS qtComprovantes ,SUM(vlComprovacao) AS vlComprovado, */
    /*                 (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado */
    /*                 FROM vwComprovacaoFinanceiraProjetoPorItemOrcamentario a */
    /*                 INNER JOIN Agentes.dbo.vUFMunicipio b on (a.cdUF = b.idUF AND a.cdCidade = b.idMunicipio) */
    /*                 WHERE IdPRONAC = 185898 */
    /*                 GROUP BY IdPRONAC,b.UF,b.Municipio */
    public function comprovacaoConsolidadaUfMunicipio($idPronac)
    {
        $cols ="
            IdPRONAC,
            b.UF,
            b.Municipio,
            COUNT(*) AS qtComprovantes ,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        /*                 INNER JOIN Agentes.dbo.vUFMunicipio b on (a.cdUF = b.idUF AND a.cdCidade = b.idMunicipio) */
        $select->join(
            array('b'=>'vUFMunicipio'),
            '(a.cdUF = b.idUF AND a.cdCidade = b.idMunicipio)',
            null,
            'Agentes'
        );

        $select->where('IdPRONAC = ?', $idPronac);

        /*                 GROUP BY IdPRONAC,b.UF,b.Municipio */
        $select->group('IdPRONAC');
        $select->group('b.UF');
        $select->group('b.Municipio');

        return $this->fetchAll($select);
    }

    /*                 --MAIORES COMPROVAÇÕES POR TIPO DE DOCUMENTOS COMPROBATÓRIO */
    /*                 SELECT TOP 10 IdPRONAC,tpDocumento,nrComprovante,c.Descricao AS nmFornecedor,COUNT AS qtComprovacoes,SUM(vlComprovacao) AS vlComprovado, */
    /*                     (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado */
    /*                     FROM vwComprovacaoFinanceiraProjetoPorItemOrcamentario a */
    /*                     INNER JOIN Agentes.dbo.Agentes b on (a.nrCNPJCPF = b.CNPJCPF) */
    /*                     INNER JOIN Agentes.dbo.Nomes   c on (b.idAgente = c.idAgente) */
    /*                     WHERE IdPRONAC = 185898 */
    /*                     GROUP BY IdPRONAC,tpDocumento,nrComprovante,c.Descricao */
    /*                     ORDER BY SUM(vlComprovacao) DESC */
    public function maioresComprovacaoTipoDocumento($idPronac)
    {
        $cols ="
            TOP 10 IdPRONAC,
            tpDocumento,
            nrComprovante,
            c.Descricao AS nmFornecedor,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ";

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
        /*                     INNER JOIN Agentes.dbo.Nomes   c on (b.idAgente = c.idAgente) */
        $select->join(
            array('c'=>'Nomes'),
            '(b.idAgente = c.idAgente)',
            null,
            'Agentes'
        );

        $select->where('IdPRONAC = ?', $idPronac);
        /*                     GROUP BY IdPRONAC,tpDocumento,nrComprovante,c.Descricao */
        /*                     ORDER BY SUM(vlComprovacao) DESC */

        $select->group('IdPRONAC');
        $select->group('tpDocumento');
        $select->group('nrComprovante');
        $select->group('c.Descricao');

        $select->order('SUM(vlComprovacao) DESC');

        return $this->fetchAll($select);
    }

    /*                     --MAIORES COMPROVAÇÕES POR TIPO DE DOCUMENTOS DE PAGAMENTO */
    /*                     SELECT TOP 10 IdPRONAC,tpFormaDePagamento,nrDocumentoDePagamento, */
    /*                     c.Descricao AS nmFornecedor,COUNT AS qtComprovacoes,SUM(vlComprovacao) AS vlComprovado, */
    /*                     (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado */
    /*                     FROM vwComprovacaoFinanceiraProjetoPorItemOrcamentario a */

    /*                     INNER JOIN Agentes.dbo.Agentes b on (a.nrCNPJCPF = b.CNPJCPF) */
    /*                     INNER JOIN Agentes.dbo.Nomes   c on (b.idAgente = c.idAgente) */
    /*                     WHERE IdPRONAC = 185898 */
    /*                     GROUP BY IdPRONAC,tpFormaDePagamento,nrDocumentoDePagamento,c.Descricao */
    /*                     ORDER BY SUM(vlComprovacao) DESC */
    public function comprovacaoTipoDocumentoPagamento($idPronac)
    {
        $cols ="
            TOP 10 IdPRONAC,
            tpFormaDePagamento, 
            nrDocumentoDePagamento,
            c.Descricao AS nmFornecedor,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );

        /*                     INNER JOIN Agentes.dbo.Agentes b on (a.nrCNPJCPF = b.CNPJCPF) */

        $select->join(
            array('b'=>'Agentes'),
            '(a.nrCNPJCPF = b.CNPJCPF)',
            null,
            'Agentes'
        );
        /*                     INNER JOIN Agentes.dbo.Nomes   c on (b.idAgente = c.idAgente) */
        $select->join(
            array('c'=>'Nomes'),
            '(b.idAgente = c.idAgente)',
            null,
            'Agentes'
        );

        $select->where('IdPRONAC = ?', $idPronac);

        /*                     GROUP BY IdPRONAC,tpFormaDePagamento,nrDocumentoDePagamento,c.Descricao */
        /*                     ORDER BY SUM(vlComprovacao) DESC */
        $select->group('IdPRONAC');
        $select->group('tpFormaDePagamento');
        $select->group('nrDocumentoDePagamento');
        $select->group('c.Descricao');

        $select->order('SUM(vlComprovacao) DESC');

        return $this->fetchAll($select);
    }

    /*                     -- MAIORES POR FORNECEDORES DO PROJETO CULTURAL */
    /*                     SELECT TOP 20 IdPRONAC,nrCNPJCPF,c.Descricao AS nmFornecedor,COUNT AS qtComprovacoes,SUM(vlComprovacao) AS vlComprovado, */
    /*                         (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado */
    /*                         FROM vwComprovacaoFinanceiraProjetoPorItemOrcamentario a */
    /*                         INNER JOIN Agentes.dbo.Agentes b on (a.nrCNPJCPF = b.CNPJCPF) */
    /*                         INNER JOIN Agentes.dbo.Nomes   c on (b.idAgente = c.idAgente) */
    /*                         WHERE IdPRONAC = 185898 */
    /*                         GROUP BY IdPRONAC,nrCNPJCPF,c.Descricao */
    /*                         ORDER BY SUM(vlComprovacao) DESC */
    public function maioresFornecedoresProjeto($idPronac)
    {
        $cols ="
            TOP 20 IdPRONAC,
            nrCNPJCPF,
            c.Descricao AS nmFornecedor,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(idPronac)) * 100 AS PercComprovado
        ";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );


        /*                         INNER JOIN Agentes.dbo.Agentes b on (a.nrCNPJCPF = b.CNPJCPF) */
        $select->join(
            array('b'=>'Agentes'),
            '(a.nrCNPJCPF = b.CNPJCPF)',
            null,
            'Agentes'
        );
        /*                         INNER JOIN Agentes.dbo.Nomes   c on (b.idAgente = c.idAgente) */
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

    /*                         -- PROPONENTE FORNECEDOR DE ITEM PARA O PROJETO CULTURAL */
    /*                         SELECT  a.IdPRONAC,nrCNPJCPF,c.Descricao AS nmFornecedor,e.Descricao as Etapa,Item,COUNT AS qtComprovacoes,SUM(vlComprovacao) AS vlComprovado, */
    /*                             (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(a.idPronac)) * 100 AS PercComprovado */

    /*                             FROM  sac.dbo.vwComprovacaoFinanceiraProjetoPorItemOrcamentario a */
    /*                             INNER JOIN Agentes.dbo.Agentes     b on (a.nrCNPJCPF = b.CNPJCPF) */
    /*                             INNER JOIN Agentes.dbo.Nomes       c on (b.idAgente  = c.idAgente) */
    /*                             INNER JOIN sac.dbo.Projetos        d on (a.IdPRONAC  = d.IdPRONAC) */
    /*                             INNER JOIN sac.dbo.tbPlanilhaEtapa e on (a.cdEtapa = e.idPlanilhaEtapa) */
    /*                             WHERE a.IdPRONAC = 168849 */
    /*                             AND b.CNPJCPF = d.CgcCpf */
    /*                             GROUP BY a.IdPRONAC,nrCNPJCPF,c.Descricao,e.Descricao ,Item */
    /*                             ORDER BY e.Descricao,Item */
    public function fornecedorItemProjeto($idPronac)
    {
        $cols ="
            a.IdPRONAC,
            nrCNPJCPF,
            c.Descricao AS nmFornecedor,
            e.Descricao as Etapa,
            Item,
            COUNT(*) AS qtComprovacoes,
            SUM(vlComprovacao) AS vlComprovado,
            (SUM(vlComprovacao) / sac.dbo.fnVlComprovadoProjeto(a.idPronac)) * 100 AS PercComprovado
        ";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a'=>$this->_name),
            $cols,
            $this->_schema
        );
        /*                             INNER JOIN Agentes.dbo.Agentes     b on (a.nrCNPJCPF = b.CNPJCPF) */

        $select->join(
            array('b'=>'Agentes'),
            '(a.nrCNPJCPF = b.CNPJCPF)',
            null,
            'Agentes'
        );
        /*                             INNER JOIN Agentes.dbo.Nomes       c on (b.idAgente  = c.idAgente) */
        $select->join(
            array('c'=>'Nomes'),
            '(b.idAgente  = c.idAgente)',
            null,
            'Agentes'
        );
        /*                             INNER JOIN sac.dbo.Projetos        d on (a.IdPRONAC  = d.IdPRONAC) */
        $select->join(
            array('d'=>'Projetos'),
            '(a.IdPRONAC  = d.IdPRONAC)',
            null,
            'sac'
        );
        /*                             INNER JOIN sac.dbo.tbPlanilhaEtapa e on (a.cdEtapa = e.idPlanilhaEtapa) */
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
