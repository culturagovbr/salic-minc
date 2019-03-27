<?php

/**
 * tbPlanoDistribuicao
 * OBS:
 *  -> A tabela SAC.dbo.PlanoDistribuicaoProduto armazena os produtos do projeto originais (aprovados)
 *  -> A tabela SAC.dbo.tbPlanoDistribuicao armazena os produtos do projeto que foram solicitados na readequacao
 */
class Readequacao_Model_DbTable_TbPlanoDistribuicao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbPlanoDistribuicao";

    /**
     * Busca os produtos originais (aprovados)
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordenacao)
     * @return object
     */
    public function buscarProdutosAprovados($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_schema . '.PlanoDistribuicaoProduto'),
            array('p.idPlanoDistribuicao'
            , 'p.idProduto'
            , 'p.Area AS cdArea'
            , 'p.Segmento AS cdSegmento'
            , 'p.idPosicaoDaLogo AS idPosicaoLogo'
            , 'p.QtdeProduzida AS qtdProduzida'
            , 'p.QtdePatrocinador AS qtdPatrocinador'
            , 'p.QtdeProponente AS qtdProponente'
            , 'p.QtdeOutros AS qtdOutros'
            , 'p.QtdeVendaNormal AS qtdVendaNormal'
            , 'p.QtdeVendaPromocional AS qtdVendaPromocional'
            , 'p.PrecoUnitarioNormal AS vlUnitarioNormal'
            , 'p.PrecoUnitarioPromocional AS vlUnitarioPromocional'
            , 'p.stPrincipal'
            , new Zend_Db_Expr('CAST(p.dsJustificativaPosicaoLogo AS TEXT) AS dsPosicaoLogo'))
        );
        $select->joinInner(
            array('d' => 'Produto'),
            'p.idProduto = d.Codigo',
            array('d.Descricao AS dsProduto'),
            'SAC.dbo'
        );
        $select->where("p.stPlanoDistribuicaoProduto = ?", "1");

        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        $select->order($order);

        return $this->fetchAll($select);
    }

    public function buscarProdutosSolicitados($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array('p.idPlano'
            , 'p.idPlanoDistribuicao'
            , 'p.idProduto'
            , 'p.cdArea'
            , 'p.cdSegmento'
            , 'p.idPosicaoLogo'
            , 'p.qtProduzida AS qtdProduzida'
            , 'p.qtPatrocinador AS qtdPatrocinador'
            , 'p.qtOutros AS qtdOutros'
            , 'p.qtVendaNormal AS qtdVendaNormal'
            , 'p.qtVendaPromocional AS qtdVendaPromocional'
            , 'p.vlUnitarioNormal'
            , 'p.vlUnitarioPromocional'
            , 'p.stPrincipal'
            , 'p.tpAcao'
            , 'p.tpPlanoDistribuicao'
            , new Zend_Db_Expr('CONVERT(CHAR(10), p.dtPlanoDistribuicao, 103) AS dtPlanoDistribuicao')
            , new Zend_Db_Expr('CONVERT(CHAR(10), p.dtPlanoDistribuicao, 108) AS hrPlanoDistribuicao')
            , new Zend_Db_Expr('CAST(p.dsjustificativa AS TEXT) AS dsJustificativa'))
        );
        $select->joinInner(
            array('d' => 'Produto'),
            'p.idProduto = d.Codigo',
            array('d.Descricao AS dsProduto'),
            'SAC.dbo'
        );

        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        $select->order($order);

        return $this->fetchAll($select);
    }

    public function historicoReadequacao($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('h' => $this->_name),
            array('h.idPlano'
            , 'h.idPlanoDistribuicao'
            , 'h.idProduto'
            , 'h.cdArea'
            , 'h.cdSegmento'
            , 'h.idPosicaoLogo'
            , 'h.qtProduzida AS qtdProduzida'
            , 'h.qtPatrocinador AS qtdPatrocinador'
            , 'h.qtOutros AS qtdOutros'
            , 'h.qtVendaNormal AS qtdVendaNormal'
            , 'h.qtVendaPromocional AS qtdVendaPromocional'
            , 'h.vlUnitarioNormal'
            , 'h.vlUnitarioPromocional'
            , 'h.stPrincipal'
            , 'h.tpAcao'
            , 'h.tpPlanoDistribuicao'
            , new Zend_Db_Expr('CONVERT(CHAR(10), h.dtPlanoDistribuicao, 103) AS dtPlanoDistribuicao')
            , new Zend_Db_Expr('CONVERT(CHAR(10), h.dtPlanoDistribuicao, 108) AS hrPlanoDistribuicao')
            , new Zend_Db_Expr('CAST(h.dsjustificativa AS TEXT) AS dsJustificativa'))
        );
        $select->joinInner(
            array('pro' => 'Produto'),
            'pro.Codigo = h.idProduto',
            array('pro.Descricao AS Produto'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('area' => 'Area'),
            'area.Codigo = h.cdArea',
            array('area.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('seg' => 'Segmento'),
            'seg.Codigo = h.cdSegmento',
            array('seg.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('ver' => 'Verificacao'),
            'ver.idVerificacao = h.idPosicaoLogo AND ver.idTipo = 3',
            array(new Zend_Db_Expr('LTRIM(ver.Descricao) AS PosicaoLogo')),
            'SAC.dbo'
        );
        $select->joinInner(
            array('p' => 'tbPedidoAlteracaoProjeto'),
            'p.idPedidoAlteracao = h.idPedidoAlteracao',
            array(
                'p.idPedidoAlteracao'
            , 'p.idSolicitante'
            , new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 103) AS dtSolicitacao')
            , new Zend_Db_Expr('CONVERT(CHAR(10), p.dtSolicitacao, 108) AS hrSolicitacao')),
            'BDCORPORATIVO.scSAC'
        );

        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        $select->order($order);

        return $this->fetchAll($select);
    }


    /**
     * Busca o produtos avaliados e deferidos pelo tecnico de acompanhamento na readequacao
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordenacao)
     * @return object
     */
    public function produtosAvaliadosReadequacao($idPedidoAlteracao, $idAvaliacaoItem)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('*')
        );
        $select->joinInner(
            array('b' => 'tbAvaliacaoSubItemPlanoDistribuicao'),
            'a.idPlano = b.idPlano',
            array(''),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('c' => 'tbAvaliacaoSubItemPedidoAlteracao'),
            'c.idAvaliacaoSubItemPedidoAlteracao = b.idAvaliacaoSubItemPedidoAlteracao',
            array(''),
            'BDCORPORATIVO.scSAC'
        );

        $select->where('a.idPedidoAlteracao = ?', $idPedidoAlteracao);
        $select->where('b.idAvaliacaoItemPedidoAlteracao = ?', $idAvaliacaoItem);
        $select->where('c.idAvaliacaoItemPedidoAlteracao = ?', $idAvaliacaoItem);
        $select->where('a.tpPlanoDistribuicao = ?', 'AT');
        $select->where('a.tpAcao <> ?', 'N');
        $select->where('c.stAvaliacaoSubItemPedidoAlteracao = ?', 'D');

        return $this->fetchAll($select);
    }


    /*
     * Funcao utilizada para buscar os planos de distribuicao do projeto para readequacao.
     */
    public function buscarPlanosDistribuicaoReadequacao($idPronac, $tabela = 'PlanoDistribuicaoProduto', $where = [])
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'Projetos'),
            array()
        );
        if ($tabela == 'PlanoDistribuicaoProduto') {
            $select->joinInner(
                array('b' => 'PlanoDistribuicaoProduto'),
                'a.idProjeto = b.idProjeto AND b.stPlanoDistribuicaoProduto = 1',
                array(new Zend_Db_Expr("
                    b.idPlanoDistribuicao,
                    b.idProjeto,
                    b.idProduto,
                    b.Area as idArea,
                    b.Segmento as idSegmento,
                    b.idPosicaoDaLogo,
                    (b.QtdeVendaNormal+b.QtdeVendaPromocional+b.QtdePatrocinador+b.QtdeOutros+b.QtdeProponente) as QtdeProduzida,
                    b.QtdePatrocinador,
                    b.QtdeProponente,
                    b.QtdeOutros,
                    b.QtdeVendaNormal,
                    b.QtdeVendaPromocional,
                    b.PrecoUnitarioNormal,
                    b.vlUnitarioNormal,
                    b.PrecoUnitarioPromocional,
                    b.stPrincipal,
                    b.canalAberto,
                    b.Usuario,'N' as tpSolicitacao")
                ),
                'SAC.dbo'
            );
        } else {
            $select->joinInner(
                array('b' => 'tbPlanoDistribuicao'),
                "a.IdPRONAC = b.idPronac AND stAtivo='S'",
                array(
                    new Zend_Db_Expr("
                        b.idPlanoDistribuicao,
                        a.idProjeto,
                        b.cdArea as idArea,
                        b.cdSegmento as idSegmento,
                        b.idPosicaoLogo as idPosicaoDaLogo,
                        (b.qtVendaNormal+b.qtVendaPromocional+b.qtPatrocinador+b.qtOutros+b.qtProponente) as QtdeProduzida,
                        b.qtPatrocinador as QtdePatrocinador,
                        b.qtProponente as QtdeProponente,
                        b.qtOutros as QtdeOutros,
                        b.qtVendaNormal as QtdeVendaNormal,
                        b.qtVendaPromocional as QtdeVendaPromocional,
                        b.vlUnitarioNormal,
                        b.PrecoUnitarioNormal,
                        b.vlUnitarioPromocional as PrecoUnitarioPromocional,
                        b.stPrincipal,
                        '0' as Usuario,
                        b.tpSolicitacao,
                        b.canalAberto,
                        b.idProduto
                    ")
                ),
                'SAC.dbo'
            );
        }

        $select->joinInner(
            array('c' => 'Produto'),
            'c.Codigo = b.idProduto',
            array('c.Descricao as Produto'),
            'SAC.dbo'
        );

        if ($tabela == 'PlanoDistribuicaoProduto') {
            $select->joinInner(
                array('d' => 'Area'),
                'b.Area = d.Codigo',
                array('d.Descricao as DescricaoArea'),
                'SAC.dbo'
            );
            $select->joinInner(
                array('e' => 'Segmento'),
                'b.Segmento = e.Codigo',
                array('e.Descricao as DescricaoSegmento'),
                'SAC.dbo'
            );
        } else {
            $select->joinInner(
                array('d' => 'Area'),
                'b.cdArea = d.Codigo',
                array('d.Descricao as DescricaoArea'),
                'SAC.dbo'
            );
            $select->joinInner(
                array('e' => 'Segmento'),
                'b.cdSegmento = e.Codigo',
                array('e.Descricao as DescricaoSegmento'),
                'SAC.dbo'
            );
        }

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->where('a.IdPRONAC = ?', $idPronac);


        return $this->fetchAll($select);
    }

    public function buscarPlanosDistribuicaoConsolidadoReadequacao($idReadequacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'Projetos'),
            array()
        );
        $select->joinInner(
            array('b' => 'tbPlanoDistribuicao'),
            "a.IdPRONAC = b.idPronac",
            array(
                new Zend_Db_Expr("
                    b.idPlanoDistribuicao,
                    a.idProjeto,b.cdArea as idArea,
                    b.cdSegmento as idSegmento,
                    b.idProduto,
                    b.idPosicaoLogo as idPosicaoDaLogo,
                    (b.qtVendaNormal+b.qtVendaPromocional+b.qtPatrocinador+b.qtOutros+b.qtProponente) as QtdeProduzida,
                    b.qtPatrocinador as QtdePatrocinador,
                    b.qtProponente as QtdeProponente,
                    b.qtOutros as QtdeOutros,
                    b.qtVendaNormal as QtdeVendaNormal,
                    b.qtVendaPromocional as QtdeVendaPromocional,
                    b.PrecoUnitarioNormal,
                    b.vlUnitarioNormal,
                    b.vlUnitarioPromocional as PrecoUnitarioPromocional,
                    b.stPrincipal,
                    '0' as Usuario,
                    b.tpSolicitacao,
                    b.tpAnaliseTecnica,
                    b.tpAnaliseComissao,
                    c.Descricao as Produto
                ")
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('c' => 'Produto'),
            'c.Codigo = b.idProduto',
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('d' => 'Area'),
            'b.cdArea = d.Codigo',
            array('d.Descricao as DescricaoArea'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('e' => 'Segmento'),
            'b.cdSegmento = e.Codigo',
            array('e.Descricao as DescricaoSegmento'),
            'SAC.dbo'
        );

        $select->where('b.idReadequacao = ?', $idReadequacao);

        return $this->fetchAll($select);
    }


    public function buscarDadosPlanosDistribuicaoAtual($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'PlanoDistribuicaoProduto'),
            array(
                new Zend_Db_Expr('a.*')
            ),
            'SAC.dbo'
        );

        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        return $this->fetchAll($select);
    }

    /**
     * Faz medias e soma valores para salvar o resumo na tabela plano de distribuicao
     * Tem que salvar a media ponderada do preço popular(receitaPopularNormal) e do proponente(PrecoUnitarioNormal)
     * por isso, os campos receitaPopularParcial e precoUnitarioParcial devem ficar vazios.
     *
     * Existe o mesmo metodo na classe PlanoDistribuicao() da proposta
     *
     * @param $idPlanoDistribuicao
     */
    public function updateConsolidacaoPlanoDeDistribuicao($idPlanoDistribuicao)
    {
        $cols = array(
            new Zend_Db_Expr('COALESCE(sum(qtExemplares),0) as qtProduzida'),
            new Zend_Db_Expr('COALESCE(sum(qtGratuitaDivulgacao), 0) as qtProponente'),
            new Zend_Db_Expr('COALESCE(sum(qtGratuitaPatrocinador), 0) as qtPatrocinador'),
            new Zend_Db_Expr('COALESCE(sum(qtGratuitaPopulacao), 0) as qtOutros'),
            new Zend_Db_Expr('COALESCE(sum(qtPopularIntegral), 0) as qtdeVendaPopularNormal'),
            new Zend_Db_Expr('COALESCE(sum(qtPopularParcial), 0) as qtdeVendaPopularPromocional'),
            new Zend_Db_Expr('COALESCE(avg(vlUnitarioPopularIntegral), 0) as vlUnitarioPopularNormal'),
            new Zend_Db_Expr('COALESCE(sum(vlReceitaPopularIntegral + vlReceitaPopularParcial) / nullif((sum(qtPopularIntegral + qtPopularParcial)), 0), 0) AS receitaPopularNormal'), #valor médio ponderado do preco popular
            new Zend_Db_Expr('COALESCE(sum(vlReceitaProponenteIntegral + vlReceitaProponenteParcial) / nullif((sum(qtProponenteIntegral + qtProponenteParcial)), 0), 0) AS precoUnitarioNormal'), # valor médio ponderado do proponente
            new Zend_Db_Expr('COALESCE(sum(qtProponenteIntegral), 0) as qtVendaNormal'),
            new Zend_Db_Expr('COALESCE(sum(qtProponenteParcial), 0) as qtVendaPromocional'),
            new Zend_Db_Expr('COALESCE(avg(vlUnitarioProponenteIntegral),0) as vlUnitarioNormal'),
            new Zend_Db_Expr('COALESCE(sum(vlReceitaPrevista), 0) as  vlReceitaTotalPrevista'),
        );

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                array('tbDetalhaPlanoDistribuicaoReadequacao'),
                $cols,
                $this->_schema
            )
            ->where('idPlanoDistribuicao = ?', $idPlanoDistribuicao)
            ->where('tpSolicitacao <> ?', 'E')
        ;

        $dados = $this->fetchRow($sql)->toArray();

        $response = false;
        if($dados) {
            $dados['tpSolicitacao'] = 'A';
            $where = $this->getAdapter()->quoteInto('idPlanoDistribuicao = ?', $idPlanoDistribuicao);
            $response = $this->update($dados, $where);
        }

        return $response;
    }

    /*
    * busca os dados fazendo alias com a tabela original
    */
    public function obterPlanosDistribuicaoReadequacao($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'Projetos'),
            array('a.idProjeto')
        );

        $select->joinInner(
            array('b' => $this->_name),
            "a.IdPRONAC = b.idPronac",
            array(
                new Zend_Db_Expr("
                    b.idPlanoDistribuicao,
                    b.cdArea as idArea,
                    b.cdSegmento as idSegmento,
                    b.idPosicaoLogo as idPosicaoDaLogo,
                    b.stPrincipal,
                    '0' as Usuario,
                    b.tpSolicitacao,
                    b.canalAberto,
                    b.idProduto,
                    FORMAT(b.qtProponente,'0,0','pt-br') as QtdeProponente,
                    FORMAT(b.qtOutros,'0,0','pt-br') as QtdeOutros,
                    FORMAT(b.qtProduzida,'0,0','pt-br') as QtdeProduzida,
                    FORMAT(b.qtPatrocinador,'0,0','pt-br') as QtdePatrocinador,
                    FORMAT(b.qtVendaNormal,'0,0','pt-br') as QtdeVendaNormal,
                    FORMAT(b.qtVendaPromocional,'0,0','pt-br') as QtdeVendaPromocional,
                    FORMAT(b.vlUnitarioNormal,'0,0','pt-br') as vlUnitarioNormal,
                    FORMAT(b.PrecoUnitarioNormal,'0,0','pt-br') as PrecoUnitarioNormal,
                    FORMAT(b.qtdeVendaPopularNormal,'0,0','pt-br') as QtdeVendaPopularNormal,
                    FORMAT(b.qtdeVendaPopularPromocional,'0,0','pt-br') as QtdeVendaPopularPromocional,
                    FORMAT(b.vlUnitarioPopularNormal,'0,0','pt-br') as vlUnitarioPopularNormal,
                    FORMAT(b.receitaPopularNormal,'0,0','pt-br') as ReceitaPopularNormal,
                    FORMAT(b.vlReceitaTotalPrevista,'0,0','pt-br') as Receita 
                ")
            ),
            $this->_schema
        );

        $select->joinInner(
            array('c' => 'Produto'),
            'c.Codigo = b.idProduto',
            array('c.Descricao as Produto'),
            $this->_schema
        );

        $select->joinInner(
            array('d' => 'Area'),
            'b.cdArea = d.Codigo',
            array('d.Descricao as DescricaoArea'),
            $this->_schema
        );

        $select->joinInner(
            array('e' => 'Segmento'),
            'b.cdSegmento = e.Codigo',
            array('e.Descricao as DescricaoSegmento'),
            $this->_schema
        );

        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->where('b.stAtivo = ?', 'S');
        $select->order('b.stPrincipal DESC');

        return $this->fetchAll($select);
    }
}
