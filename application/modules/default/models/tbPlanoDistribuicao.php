<?php
/**
 * DAO tbPlanoDistribuicao
 * OBS:
 * 	-> A tabela SAC.dbo.PlanoDistribuicaoProduto armazena os produtos do projeto originais (aprovados)
 *  -> A tabela SAC.dbo.tbPlanoDistribuicao armazena os produtos do projeto que foram solicitados na readequa��o
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 20/04/2012
 * @version 1.0
 * @link http://salic.cultura.gov.br
 */

class tbPlanoDistribuicao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbPlanoDistribuicao";

    /**
     * Busca os produtos originais (aprovados)
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function buscarProdutosAprovados($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_schema . '.PlanoDistribuicaoProduto'),
            array('p.idPlanoDistribuicao'
                ,'p.idProduto'
                ,'p.Area AS cdArea'
                ,'p.Segmento AS cdSegmento'
                ,'p.idPosicaoDaLogo AS idPosicaoLogo'
                ,'p.QtdeProduzida AS qtdProduzida'
                ,'p.QtdePatrocinador AS qtdPatrocinador'
                ,'p.QtdeProponente AS qtdProponente'
                ,'p.QtdeOutros AS qtdOutros'
                ,'p.QtdeVendaNormal AS qtdVendaNormal'
                ,'p.QtdeVendaPromocional AS qtdVendaPromocional'
                ,'p.PrecoUnitarioNormal AS vlUnitarioNormal'
                ,'p.PrecoUnitarioPromocional AS vlUnitarioPromocional'
                ,'p.stPrincipal'
                ,'CAST(p.dsJustificativaPosicaoLogo AS TEXT) AS dsPosicaoLogo')
        );
        $select->joinInner(
            array('d' => 'Produto'),
            'p.idProduto = d.Codigo',
            array('d.Descricao AS dsProduto'),
            'SAC.dbo'
        );
        $select->where("p.stPlanoDistribuicaoProduto = ?", "1");

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    } // fecha m�todo buscarProdutosAprovados()


    /**
     * Busca os produtos solicitados (readequa��o)
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function buscarProdutosSolicitados($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array('p.idPlano'
                ,'p.idPlanoDistribuicao'
                ,'p.idProduto'
                ,'p.cdArea'
                ,'p.cdSegmento'
                ,'p.idPosicaoLogo'
                ,'p.qtProduzida AS qtdProduzida'
                ,'p.qtPatrocinador AS qtdPatrocinador'
                ,'p.qtOutros AS qtdOutros'
                ,'p.qtVendaNormal AS qtdVendaNormal'
                ,'p.qtVendaPromocional AS qtdVendaPromocional'
                ,'p.vlUnitarioNormal'
                ,'p.vlUnitarioPromocional'
                ,'p.stPrincipal'
                ,'p.tpAcao'
                ,'p.tpPlanoDistribuicao'
                ,'CONVERT(CHAR(10), p.dtPlanoDistribuicao, 103) AS dtPlanoDistribuicao'
                ,'CONVERT(CHAR(10), p.dtPlanoDistribuicao, 108) AS hrPlanoDistribuicao'
                ,'CAST(p.dsjustificativa AS TEXT) AS dsJustificativa')
        );
        $select->joinInner(
            array('d' => 'Produto'),
            'p.idProduto = d.Codigo',
            array('d.Descricao AS dsProduto'),
            'SAC.dbo'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    } // fecha m�todo buscarProdutosSolicitados()


    /**
     * Busca o hist�rico de readequa��o
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function historicoReadequacao($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('h' => $this->_name),
            array('h.idPlano'
                ,'h.idPlanoDistribuicao'
                ,'h.idProduto'
                ,'h.cdArea'
                ,'h.cdSegmento'
                ,'h.idPosicaoLogo'
                ,'h.qtProduzida AS qtdProduzida'
                ,'h.qtPatrocinador AS qtdPatrocinador'
                ,'h.qtOutros AS qtdOutros'
                ,'h.qtVendaNormal AS qtdVendaNormal'
                ,'h.qtVendaPromocional AS qtdVendaPromocional'
                ,'h.vlUnitarioNormal'
                ,'h.vlUnitarioPromocional'
                ,'h.stPrincipal'
                ,'h.tpAcao'
                ,'h.tpPlanoDistribuicao'
                ,'CONVERT(CHAR(10), h.dtPlanoDistribuicao, 103) AS dtPlanoDistribuicao'
                ,'CONVERT(CHAR(10), h.dtPlanoDistribuicao, 108) AS hrPlanoDistribuicao'
                ,'CAST(h.dsjustificativa AS TEXT) AS dsJustificativa')
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
            array('LTRIM(ver.Descricao) AS PosicaoLogo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('p' => 'tbPedidoAlteracaoProjeto'),
            'p.idPedidoAlteracao = h.idPedidoAlteracao',
            array(
                'p.idPedidoAlteracao'
                ,'p.idSolicitante'
                ,'CONVERT(CHAR(10), p.dtSolicitacao, 103) AS dtSolicitacao'
                ,'CONVERT(CHAR(10), p.dtSolicitacao, 108) AS hrSolicitacao'),
            'BDCORPORATIVO.scSAC'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);
                
        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()


    /**
     * Busca o produtos avaliados e deferidos pelo tecnico de acompanhamento na readequacao
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
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
    } // fecha m�todo historicoReadequacao()


    /*
     * Criada em 31/03/2014
     * @author: Jefferson Alessandro
     * Fun��o utilizada para buscar os planos de distribui��o do projeto para readequa��o.
     */
    public function buscarPlanosDistribuicaoReadequacao($idPronac, $tabela = 'PlanoDistribuicaoProduto')
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
                    b.idPlanoDistribuicao, b.idProjeto, b.idProduto, b.Area as idArea, b.Segmento as idSegmento, b.idPosicaoDaLogo,
                    (b.QtdeVendaNormal+b.QtdeVendaPromocional+b.QtdePatrocinador+b.QtdeOutros+b.QtdeProponente) as QtdeProduzida,
                    b.QtdePatrocinador,b.QtdeProponente,b.QtdeOutros,b.QtdeVendaNormal,b.QtdeVendaPromocional,b.PrecoUnitarioNormal,
                    b.PrecoUnitarioPromocional, b.stPrincipal,b.Usuario,'N' as tpSolicitacao")
                ),
                'SAC.dbo'
            );
        } else {
            $select->joinInner(
                array('b' => 'tbPlanoDistribuicao'),
                "a.IdPRONAC = b.idPronac AND stAtivo='S'",
                array(
                    new Zend_Db_Expr("
                        b.idPlanoDistribuicao,a.idProjeto,b.cdArea as idArea, b.cdSegmento as idSegmento,b.idPosicaoLogo as idPosicaoDaLogo,
                        (b.qtVendaNormal+b.qtVendaPromocional+b.qtPatrocinador+b.qtOutros+b.qtProponente) as QtdeProduzida,
                        b.qtPatrocinador as QtdePatrocinador, b.qtProponente as QtdeProponente, b.qtOutros as QtdeOutros, b.qtVendaNormal as QtdeVendaNormal,
                        b.qtVendaPromocional as QtdeVendaPromocional, b.vlUnitarioNormal as PrecoUnitarioNormal, b.vlUnitarioPromocional as PrecoUnitarioPromocional,
                        b.stPrincipal, '0' as Usuario, b.tpSolicitacao, b.idProduto
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
                array('d.Descricao as Area'),
                'SAC.dbo'
            );
            $select->joinInner(
                array('e' => 'Segmento'),
                'b.Segmento = e.Codigo',
                array('e.Descricao as Segmento'),
                'SAC.dbo'
            );
        } else {
            $select->joinInner(
                array('d' => 'Area'),
                'b.cdArea = d.Codigo',
                array('d.Descricao as Area'),
                'SAC.dbo'
            );
            $select->joinInner(
                array('e' => 'Segmento'),
                'b.cdSegmento = e.Codigo',
                array('e.Descricao as Segmento'),
                'SAC.dbo'
            );
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
                    b.idPlanoDistribuicao,a.idProjeto,b.cdArea as idArea, b.cdSegmento as idSegmento,b.idPosicaoLogo as idPosicaoDaLogo,
                    (b.qtVendaNormal+b.qtVendaPromocional+b.qtPatrocinador+b.qtOutros+b.qtProponente) as QtdeProduzida,
                    b.qtPatrocinador as QtdePatrocinador, b.qtProponente as QtdeProponente, b.qtOutros as QtdeOutros, b.qtVendaNormal as QtdeVendaNormal,
                    b.qtVendaPromocional as QtdeVendaPromocional, b.vlUnitarioNormal as PrecoUnitarioNormal, b.vlUnitarioPromocional as PrecoUnitarioPromocional,
                    b.stPrincipal, '0' as Usuario, b.tpSolicitacao,b.tpAnaliseTecnica,b.tpAnaliseComissao, c.Descricao as Produto
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
            array('d.Descricao as Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('e' => 'Segmento'),
            'b.cdSegmento = e.Codigo',
            array('e.Descricao as Segmento'),
            'SAC.dbo'
        );

        $select->where('b.idReadequacao = ?', $idReadequacao);

        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()


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

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        
        return $this->fetchAll($select);
    } // fecha m�todo historicoReadequacao()
}
