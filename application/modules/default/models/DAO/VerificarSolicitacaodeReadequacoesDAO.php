<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VerificarSolicitacaodeReadequacoesDAO
 *
 * @author 01373930160
 */
class VerificarSolicitacaodeReadequacoesDAO extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_name = "Projetos";
    

    public function buscarProjetos($idPronac)
    {
        $sql = "select projetos.idProjeto,

                    projetos.IdPRONAC,
                    projetos.CgcCpf,
                    projetos.AnoProjeto+projetos.Sequencial as nrpronac,
                    projetos.NomeProjeto,
                    agentes.Descricao,
                    agentes.idAgente,
                    areaCultura.Codigo as 'codigoArea',
                    areaCultura.Descricao as 'areaCultura',
                    segmentoCultura.Codigo as 'codigoDescricao',
                    segmentoCultura.Descricao as 'segmentoCultura' from
                    sac.dbo.Projetos as projetos
                    inner join SAC.dbo.Area as areaCultura
                    on projetos.Area = areaCultura.Codigo
                    left join SAC.dbo.Segmento as segmentoCultura
                    on projetos.Segmento = segmentoCultura.Codigo
                    inner join SAC.dbo.PreProjeto as pre
                    on projetos.idProjeto = pre.idPreProjeto
                    inner join AGENTES.dbo.Nomes as agentes
                    on pre.idAgente = agentes.idAgente

                where projetos.IdPRONAC = $idPronac";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function buscarProdutos($idPronac)
    {
//        $sql = "SELECT   DISTINCT  SAC.dbo.Projetos.IdPRONAC, SAC.dbo.Produto.Descricao,
//                      SAC.dbo.Produto.Codigo AS idProduto
        //FROM         SAC.dbo.Produto INNER JOIN
//                      SAC.dbo.PlanoDistribuicaoProduto ON SAC.dbo.Produto.Codigo = SAC.dbo.PlanoDistribuicaoProduto.idProduto CROSS JOIN
//                      SAC.dbo.Projetos
        //WHERE     (SAC.dbo.Projetos.IdPRONAC = $idPronac) AND SAC.dbo.PlanoDistribuicaoProduto.stPlanoDistribuicaoProduto = 1 ORDER BY idProduto ASC";
//
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pr' => 'Projetos'),
                        array('IdPRONAC'),
                        "SAC.dbo"
                )
                ->joinInner(
                        array('prep' => 'PreProjeto'),
                        'prep.idPreProjeto = pr.idProjeto',
                        array('idPreprojeto'),
                        'SAC.dbo'
                )
                ->joinInner(
                        array('ag' => 'Agentes'),
                        'ag.idAgente = prep.idAgente',
                        array(
                            'TipoPessoa',
                            'idAgente'
                        ),
                        "Agentes.dbo"
                )
                ->joinLeft(
                        array('tpap' => 'tbPedidoAlteracaoProjeto'),
                        'tpap.IdPRONAC = pr.IdPRONAC',
                        array(),
                        'BDCORPORATIVO.scSAC'
                )
                ->joinLeft(
                        array('tpd' => 'tbPlanoDistribuicao'),
                        'tpd.idPedidoAlteracao = tpap.idPedidoAlteracao',
                        array('tpd.idProduto'),
                        'SAC.dbo'
                )
                ->joinLeft(
                        array('pd' => 'Produto'),
                        'tpd.idProduto = pd.Codigo',
                        array(
                            'pd.Descricao as produto'
                        ),
                        'SAC.dbo'
                )
                        
                ->where('pr.IdPRONAC= ?', $idPronac);
                
        return $this->fetchAll($slct);

//        $db= Zend_Db_Table::getDefaultAdapter();
//        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//
//        return $db->fetchAll($sql);
    }

    public function buscaItem($idPronac, $idPlanilhaAprovacao, $idPlanilhaItem)
    {
        $sql = "SELECT tpa.idPlanilhaAprovacao,
                        tpa.idProduto,
                        tpa.IdPRONAC,
                        SAC.dbo.tbPlanilhaEtapa.idPlanilhaEtapa,
                        SAC.dbo.tbPlanilhaItens.idPlanilhaItens,
                        SAC.dbo.tbPlanilhaUnidade.idUnidade,
                        AGENTES.dbo.Verificacao.idVerificacao,
                        tpa.qtItem,
                        tpa.nrOcorrencia,
                        tpa.vlUnitario,
                        tpa.qtDias,
                        CAST(tpa.dsJustificativa AS TEXT) AS dsJustificativa,
                        tpa.idPedidoAlteracao,
                        tpa.idAgente,
                        tpa.tpAcao,
                        AGENTES.dbo.Municipios.Descricao AS DescricaoMunicipio,
                        SAC.dbo.tbPlanilhaItens.Descricao AS DescricaoItem,
                        AGENTES.dbo.UF.Descricao AS DescricaoUF,
                        AGENTES.dbo.Verificacao.Descricao AS DescricaoFonteRecurso,
                        SAC.dbo.tbPlanilhaEtapa.Descricao AS DescricaoEtapa,
                        SAC.dbo.tbPlanilhaUnidade.Descricao AS DescricaoUnidade,
                        prod.Descricao AS dsProduto
                        FROM SAC.dbo.tbPlanilhaAprovacao tpa
                        INNER JOIN SAC.dbo.tbPlanilhaEtapa ON tpa.idEtapa = SAC.dbo.tbPlanilhaEtapa.idPlanilhaEtapa
                        INNER JOIN SAC.dbo.tbPlanilhaItens ON tpa.idPlanilhaItem = SAC.dbo.tbPlanilhaItens.idPlanilhaItens
                        INNER JOIN SAC.dbo.tbPlanilhaUnidade ON tpa.idUnidade = SAC.dbo.tbPlanilhaUnidade.idUnidade
                        INNER JOIN AGENTES.dbo.Verificacao ON tpa.nrFonteRecurso = AGENTES.dbo.Verificacao.idVerificacao
                        INNER JOIN AGENTES.dbo.UF ON tpa.idUFDespesa = AGENTES.dbo.UF.idUF
                        INNER JOIN AGENTES.dbo.Municipios ON tpa.idMunicipioDespesa = AGENTES.dbo.Municipios.idMunicipioIBGE
                        LEFT JOIN SAC.dbo.Produto prod ON tpa.idProduto = prod.Codigo
                        WHERE (tpa.IdPRONAC = $idPronac) AND (tpa.idPlanilhaAprovacao = $idPlanilhaAprovacao) AND (tpa.idPlanilhaItem = $idPlanilhaItem)
                        and tpa.dtPlanilha in (select max(dtPlanilha) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC=tpa.IdPRONAC
                        and idPlanilhaAprovacao=tpa.idPlanilhaAprovacao and idPlanilhaItem=tpa.idPlanilhaItem)";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarProdutosItens($idPronac = null, $idEtapa = null, $idPlanilhaAprovacao=null, $situacao=null, $idProduto=null)
    {
        $sql = "SELECT a.IdPRONAC,
                    a.idPlanilhaAprovacao,
                    b.Descricao,
                    c.Descricao AS DescricaoEtapa,
                    d.Descricao AS DescricaoItem,
                    e.Descricao AS DescricaoProduto,
                    a.idUFDespesa,
                    a.idMunicipioDespesa,
                    g.Descricao AS DescricaoMunicipio,
                    f.Descricao AS DescricaoUF,
                    h.idAgente,
                    a.tpPlanilha,
                    b.idUnidade AS Unidade,
                    c.idPlanilhaEtapa,
                    d.idPlanilhaItens AS idPlanilhaItem,
                    f.idUF AS UF,
                    e.Codigo,
                    g.idMunicipioIBGE,
                    a.nrOcorrencia,
                    a.vlUnitario,
                    a.qtDias,
                    a.qtItem,
                    a.idUnidade,
                    a.tpAcao ,
                    CAST(a.dsJustificativa as TEXT) AS dsjustificativa,
                    a.stAtivo,
                    (a.nrOcorrencia * a.vlUnitario * a.qtDias) AS Total,
                    a.idProduto
            FROM SAC.dbo.tbPlanilhaAprovacao        AS a
            INNER JOIN SAC.dbo.tbPlanilhaUnidade    AS b ON a.idUnidade = b.idUnidade
            INNER JOIN SAC.dbo.tbPlanilhaEtapa      AS c ON a.idEtapa = c.idPlanilhaEtapa
            INNER JOIN SAC.dbo.tbPlanilhaItens      AS d ON a.idPlanilhaItem = d.idPlanilhaItens
            INNER JOIN SAC.dbo.Produto              AS e ON a.idProduto = e.Codigo
            INNER JOIN AGENTES.dbo.UF               AS f ON a.idUFDespesa = f.idUF
            INNER JOIN AGENTES.dbo.Municipios       AS g ON a.idMunicipioDespesa = g.idMunicipioIBGE
            INNER JOIN AGENTES.dbo.Agentes          AS h ON a.idAgente = h.idAgente

            WHERE a.stAtivo = '$situacao'
            AND a.tpAcao is not null
            AND a.idPedidoAlteracao is not null
            AND a.tpPlanilha = 'PA'";
        if (!empty($idPronac) and !empty($idEtapa)) {
            $sql .= " AND  a.idEtapa = $idEtapa AND a.IdPRONAC = $idPronac ";
        }
        if (!empty($idPlanilhaAprovacao)) {
            $sql .=" AND a.idPlanilhaAprovacao=$idPlanilhaAprovacao";
        }
        if (!empty($idProduto)) {
            $sql .=" AND idProduto = $idProduto";
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarProdutosItensParecerista($idPronac = null, $idEtapa = null, $idPlanilhaAprovacao=null, $situacao=null, $idProduto=null)
    {
        $sql = "SELECT a.IdPRONAC,
                    a.idPlanilhaAprovacao,
                    b.Descricao,
                    c.Descricao AS DescricaoEtapa,
                    d.Descricao AS DescricaoItem,
                    e.Descricao AS DescricaoProduto,
                    a.idUFDespesa,
                    a.idMunicipioDespesa,
                    g.Descricao AS DescricaoMunicipio,
                    f.Descricao AS DescricaoUF,
                    h.idAgente,
                    a.tpPlanilha,
                    b.idUnidade AS Unidade,
                    c.idPlanilhaEtapa,
                    d.idPlanilhaItens AS idPlanilhaItem,
                    f.idUF AS UF,
                    e.Codigo,
                    g.idMunicipioIBGE,
                    a.nrOcorrencia,
                    a.vlUnitario,
                    a.qtDias,
                    a.qtItem,
                    a.idUnidade,
                    a.tpAcao ,
                    CAST(a.dsJustificativa as TEXT) AS dsjustificativa,
                    a.stAtivo,
                    (a.nrOcorrencia * a.vlUnitario * a.qtDias) AS Total,
                    a.idProduto
            FROM SAC.dbo.tbPlanilhaAprovacao        AS a
            INNER JOIN SAC.dbo.tbPlanilhaUnidade    AS b ON a.idUnidade = b.idUnidade
            INNER JOIN SAC.dbo.tbPlanilhaEtapa      AS c ON a.idEtapa = c.idPlanilhaEtapa
            INNER JOIN SAC.dbo.tbPlanilhaItens      AS d ON a.idPlanilhaItem = d.idPlanilhaItens
            INNER JOIN SAC.dbo.Produto              AS e ON a.idProduto = e.Codigo
            INNER JOIN AGENTES.dbo.UF               AS f ON a.idUFDespesa = f.idUF
            INNER JOIN AGENTES.dbo.Municipios       AS g ON a.idMunicipioDespesa = g.idMunicipioIBGE
            INNER JOIN AGENTES.dbo.Agentes          AS h ON a.idAgente = h.idAgente 

            WHERE a.stAtivo = '$situacao'
            AND a.tpAcao is not null
            AND a.idPedidoAlteracao is not null
            AND a.tpPlanilha = 'SR'";
        if (!empty($idPronac) and !empty($idEtapa)) {
            $sql .= " AND  a.idEtapa = $idEtapa AND a.IdPRONAC = $idPronac ";
        }
        if (!empty($idPlanilhaAprovacao)) {
            $sql .=" AND a.idPlanilhaAprovacao=$idPlanilhaAprovacao";
        }
        if (!empty($idProduto)) {
            $sql .=" AND idProduto = $idProduto";
        }
//

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function inserirCopiaPlanilha($idPronac, $idPedidoAlteracao)
    {
        $sql = "insert into SAC.dbo.tbPlanilhaAprovacao

                    SELECT
                    'tpPlanilha' = 'PA',
                    GETDATE(),
                    idPlanilhaProjeto,
                    idPlanilhaProposta,
                    IdPRONAC,
                    idProduto,
                    idEtapa,
                    idPlanilhaItem,
                    dsItem,
                    idUnidade,
                    qtItem,
                    nrOcorrencia,
                    vlUnitario,
                    qtDias,
                    tpDespesa,
                    tpPessoa,
                    nrContraPartida,
                    nrFonteRecurso,
                    idUFDespesa,
                    idMunicipioDespesa,
                    'dsJustificativa' = '',
                    idAgente,
                    idPlanilhaAprovacao,
                    $idPedidoAlteracao,
                    tpAcao,
                    idRecursoDecisao,
                    'stAtivo' = 'N'

                    FROM         SAC.dbo.tbPlanilhaAprovacao
                    WHERE     (IdPRONAC = $idPronac) AND (stAtivo = 'N') AND (tpPlanilha = 'SR')

                    ";
        //die($sql);

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function copiaAprovada($idPronac, $idProduto, $idEtapa, $idPlanilhaItem)
    {
        $sql = "insert into SAC.dbo.tbPlanilhaAprovacao

                    SELECT
                    'tpPlanilha' = 'PA',
                    dtPlanilha,
                    idPlanilhaProjeto,
                    idPlanilhaProposta,
                    IdPRONAC,
                    idProduto,
                    idEtapa,
                    idPlanilhaItem,
                    dsItem,
                    idUnidade,
                    qtItem,
                    nrOcorrencia,
                    vlUnitario,
                    qtDias,
                    tpDespesa,
                    tpPessoa,
                    nrContraPartida,
                    nrFonteRecurso,
                    idUFDespesa,
                    idMunicipioDespesa,
                    dsJustificativa,
                    idAgente,
                    idPlanilhaAprovacaoPai,
                    idPedidoAlteracao,
                    'tpAcao' = 'N',
                    idRecursoDecisao,
                    'stAtivo' = 'S'

                    FROM         SAC.dbo.tbPlanilhaAprovacao
                    WHERE     (IdPRONAC = $idPronac) AND (idProduto = $idProduto) AND (idEtapa = $idEtapa) AND (idPlanilhaItem = $idPlanilhaItem) AND (stAtivo = 'S') AND (tpPlanilha <> 'PA') AND (tpPlanilha <> 'SR')

                    ";
        

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaSubItem($idAvaliacaoSubItem, $where=null)
    {
        $sql = " SELECT * FROM BDCORPORATIVO.scSac.tbAvaliacaoSubItemPedidoAlteracao WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacaoSubItem ";
        if ($where) {
            $sql .=  $where;
        }
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaSubItemPedidoAlteracao($idAvaliacaoSubItem, $idPedidoAlteracao)
    {
        $sql = " SELECT stAvaliacaoSubItemPedidoAlteracao as stAvaliacao FROM BDCORPORATIVO.scSac.tbAvaliacaoSubItemPedidoAlteracao
                WHERE idAvaliacaoItemPedidoAlteracao = $idPedidoAlteracao AND idAvaliacaoItemPedidoAlteracao = $idAvaliacaoSubItem";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
  
    public static function verificaPedidoAlteracao($idPRONAC)
    {
        $sql = " SELECT idPedidoAlteracao FROM BDCORPORATIVO.scSac.tbPedidoAlteracaoProjeto WHERE idPRONAC = $idPRONAC ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaMudancaOrcamentaria($idPronac)
    {
        $sql = "select (select SUM (qtItem * nrOcorrencia * vlUnitario)
                            from SAC.dbo.tbPlanilhaAprovacao
                            WHERE IdPRONAC = $idPronac and stAtivo= 'S') as totalS,
                            (select SUM (qtItem * nrOcorrencia * vlUnitario)
                            from SAC.dbo.tbPlanilhaAprovacao
                            WHERE IdPRONAC = $idPronac and stAtivo= 'N' and tpAcao <> 'E') as totalN
                            ";

        //die($sql);
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaIdAvaliacaoItemPedidoAlteracao($idPedidoAlteracao, $tpAlteracaoProjeto = null)
    {
        $sql = "select idAvaliacaoItemPedidoAlteracao from BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao WHERE idPedidoAlteracao = $idPedidoAlteracao";
        if (!empty($tpAlteracaoProjeto)) :
            $sql.= " AND tpAlteracaoProjeto = " . $tpAlteracaoProjeto;
        endif;
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaIdAvaliacaoSubItemPedidoAlteracao($idItemAvaliacaoItemPedidoAlteracao)
    {
        $sql = "select TOP 1 idAvaliacaoSubItemPedidoAlteracao from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao WHERE idAvaliacaoItemPedidoAlteracao = $idItemAvaliacaoItemPedidoAlteracao ORDER BY idAvaliacaoSubItemPedidoAlteracao DESC ";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaAvaliacoesSubItemPedidoAlteracao($idPedidoAlteracao, $idPlanilhaAprovacao, $idAvaliacaoItemPedidoAlteracao)
    {
        $sql = "SELECT b.*, c.* from BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao AS a
                INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemCusto AS b ON a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracao
                INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao AS c ON c.idAvaliacaoSubItemPedidoAlteracao = b.idAvaliacaoSubItemPedidoAlteracao
                WHERE a.idPedidoAlteracao = $idPedidoAlteracao
                AND a.tpAlteracaoProjeto = 7
                AND b.idPlanilhaAprovacao = $idPlanilhaAprovacao
                AND a.idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao
                AND b.idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchRow($sql);
    }

    public static function buscarProdutosItensSemProduto($idPronac = null, $idEtapa = null, $idPlanilhaAprovacao=null, $situacao=null, $idProduto=null)
    {
        $sql = "SELECT  SAC.dbo.tbPlanilhaAprovacao.IdPRONAC, SAC.dbo.tbPlanilhaAprovacao.idPlanilhaAprovacao, SAC.dbo.tbPlanilhaUnidade.Descricao,
                        SAC.dbo.tbPlanilhaEtapa.Descricao AS DescricaoEtapa,
                      SAC.dbo.tbPlanilhaItens.Descricao AS DescricaoItem,  SAC.dbo.tbPlanilhaAprovacao.idUFDespesa,
                      SAC.dbo.tbPlanilhaAprovacao.idMunicipioDespesa, AGENTES.dbo.Municipios.Descricao AS DescricaoMunicipio,
                      AGENTES.dbo.UF.Descricao AS DescricaoUF, AGENTES.dbo.Agentes.idAgente, SAC.dbo.tbPlanilhaAprovacao.tpPlanilha,
                      SAC.dbo.tbPlanilhaUnidade.idUnidade AS Unidade, SAC.dbo.tbPlanilhaEtapa.idPlanilhaEtapa, SAC.dbo.tbPlanilhaItens.idPlanilhaItens AS idPlanilhaItem, AGENTES.dbo.UF.idUF AS UF,
                      AGENTES.dbo.Municipios.idMunicipioIBGE, SAC.dbo.tbPlanilhaAprovacao.nrOcorrencia,
                      SAC.dbo.tbPlanilhaAprovacao.vlUnitario, SAC.dbo.tbPlanilhaAprovacao.qtDias, SAC.dbo.tbPlanilhaAprovacao.qtItem,
                      SAC.dbo.tbPlanilhaAprovacao.idUnidade,SAC.dbo.tbPlanilhaAprovacao.tpAcao , CAST(SAC.dbo.tbPlanilhaAprovacao.dsJustificativa AS TEXT) AS dsjustificativa,
                      SAC.dbo.tbPlanilhaAprovacao.stAtivo,
                      (SAC.dbo.tbPlanilhaAprovacao.nrOcorrencia * SAC.dbo.tbPlanilhaAprovacao.vlUnitario * SAC.dbo.tbPlanilhaAprovacao.qtDias) AS Total
                        FROM         SAC.dbo.tbPlanilhaAprovacao INNER JOIN
                      SAC.dbo.tbPlanilhaUnidade ON SAC.dbo.tbPlanilhaAprovacao.idUnidade = SAC.dbo.tbPlanilhaUnidade.idUnidade INNER JOIN
                      SAC.dbo.tbPlanilhaEtapa ON SAC.dbo.tbPlanilhaAprovacao.idEtapa = SAC.dbo.tbPlanilhaEtapa.idPlanilhaEtapa INNER JOIN
                      SAC.dbo.tbPlanilhaItens ON SAC.dbo.tbPlanilhaAprovacao.idPlanilhaItem = SAC.dbo.tbPlanilhaItens.idPlanilhaItens INNER JOIN
                      AGENTES.dbo.UF ON SAC.dbo.tbPlanilhaAprovacao.idUFDespesa = AGENTES.dbo.UF.idUF INNER JOIN
                      AGENTES.dbo.Municipios ON SAC.dbo.tbPlanilhaAprovacao.idMunicipioDespesa = AGENTES.dbo.Municipios.idMunicipioIBGE INNER JOIN
                      AGENTES.dbo.Agentes ON SAC.dbo.tbPlanilhaAprovacao.idAgente = AGENTES.dbo.Agentes.idAgente WHERE SAC.dbo.tbPlanilhaAprovacao.stAtivo = '$situacao' and SAC.dbo.tbPlanilhaAprovacao.tpAcao is not null and SAC.dbo.tbPlanilhaAprovacao.idPedidoAlteracao is not null AND SAC.dbo.tbPlanilhaAprovacao.tpPlanilha = 'PA'";
        if (!empty($idPronac) and !empty($idEtapa)) {
            $sql .= " AND  SAC.dbo.tbPlanilhaAprovacao.idEtapa = $idEtapa AND SAC.dbo.tbPlanilhaAprovacao.IdPRONAC = $idPronac ";
        }
        if (!empty($idPlanilhaAprovacao)) {
            $sql .=" AND SAC.dbo.tbPlanilhaAprovacao.idPlanilhaAprovacao=$idPlanilhaAprovacao";
        }


        //die( "<pre>" . $sql) ;


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    public static function atualizaPlanilhaAprovacao($idPlanilhaAprovacao, $tpAcao)
    {
        $sql = "UPDATE    SAC.dbo.tbPlanilhaAprovacao
                SET   tpAcao = '$tpAcao'
        WHERE     (idPlanilhaAprovacao = $idPlanilhaAprovacao) AND tpPlanilha = 'PA' AND stAtivo = 'N'";

        //die();
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    public static function atualizaAvaliacaoSubItemPedidoAlteracao($idItemAvaliacaoItemPedidoAlteracao, $stAvaliacaoSubItemPedidoAlteracao, $dsAvaliacaoSubItemPedidoAlteracao)
    {
        $sql = "UPDATE BDCORPORATIVO.SCsAC.tbAvaliacaoSubItemPedidoAlteracao
        set stAvaliacaoSubItemPedidoAlteracao = '$stAvaliacaoSubItemPedidoAlteracao' where idAvaliacaoItemPedidoAlteracao = $idItemAvaliacaoItemPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function atualizaAvaliacaoItemPedidoAlteracao($dsJustificativaAvaliador, $stDeferimento, $idPedidoAlteracao)
    {
        $sql = "UPDATE BDCORPORATIVO.scSac.tbAvaliacaoItemPedidoAlteracao
        SET stAvaliacaoSubItemPedidoAlteracao = $stDeferimento , dsAvaliacaoSubItemPedidoAlteracao = '$dsJustificativaAvaliador', dtInicioAvaliacao = getdate()
        WHERE idPedidoAlteracao = $idPedidoAlteracao";
    }

    

    public static function inserirAvaliacaoSubItemPedidoAlteracao($dsJustificativaAvaliador, $stDeferimento, $idPedidoAlteracao, $idAvaliacaoSubItemPedidoAlteracao)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSac.tbAvaliacaoSubItemPedidoAlteracao
        (idAvaliacaoItemPedidoAlteracao, stAvaliacaoSubItemPedidoAlteracao, dsAvaliacaoSubItemPedidoAlteracao)
            VALUES ($idAvaliacaoSubItemPedidoAlteracao, '$stDeferimento', '$dsJustificativaAvaliador')";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function inserirAvaliacaoSubItemCusto($idItemAvaliacaoItemPedidoAlteracao, $idAvaliacaoSubItemPedidoAlteracao, $idPlanilhaAprovacao)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSac.tbAvaliacaoSubItemCusto
                (idAvaliacaoItemPedidoAlteracao, idAvaliacaoSubItemPedidoAlteracao , idPlanilhaAprovacao)
                VALUES ($idItemAvaliacaoItemPedidoAlteracao, $idAvaliacaoSubItemPedidoAlteracao,  $idPlanilhaAprovacao)";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function deletaPlanilhaAprovacaoExcluida($idPlanilhaAprovacao, $idProduto = null, $idEtapa = null, $idPronac = null, $idItem = null)
    {
        $sql = "DELETE FROM SAC.dbo.tbPlanilhaAprovacao WHERE tpPlanilha = 'PA'";

        if (!empty($idPlanilhaAprovacao)) {
            $sql .= " AND idPlanilhaAprovacao = $idPlanilhaAprovacao";
        }
        if (!empty($idPronac)) {
            $sql .= " AND idPRONAC = $idPronac";
        }
        if (!empty($idEtapa)) {
            $sql .= " AND idEtapa = $idEtapa";
        }
        if (!empty($idProduto)) {
            $sql .= " AND idProduto = $idProduto";
        }
        if (!empty($idItem)) {
            $sql .= " AND idPlanilhaItem = $idItem";
        }

        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function atualizaPedidoAlteracao($idPronac, $idAgente, $idPedido, $dsAvaliacao, $tipoAlteracao = null)
    {
        $sql = "UPDATE    BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                        SET stAvaliacaoItemPedidoAlteracao = '$tipoAlteracao', dsAvaliacao = '$dsAvaliacao' WHERE idPedidoAlteracao = $idPedido";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaPlanilhaAprovacao($idPronac)
    {
        $sql = "select * from SAC.dbo.tbPlanilhaAprovacao WHERE idPRONAC = $idPronac AND tpPlanilha = 'PA'";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaStatus($idPedidoAlteracao, $tpAlteracaoProjeto = null)
    {
        $sql = "SELECT stAvaliacaoItemPedidoAlteracao FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao WHERE idPedidoAlteracao = $idPedidoAlteracao ";

        if (!empty($tpAlteracaoProjeto)) :
            $sql.= "AND tpAlteracaoProjeto = " . $tpAlteracaoProjeto;
        endif;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaStatusItemDeCusto($idPedidoAlteracao, $tpAlteracaoProjeto = null)
    {
        $sql = "SELECT a.*
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao AS a
                INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao AS b on a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracao
                WHERE a.idPedidoAlteracao = $idPedidoAlteracao AND a.tpAlteracaoProjeto = 10 AND b.stAtivo = 0";

        if (!empty($tpAlteracaoProjeto)) :
            $sql.= "AND tpAlteracaoProjeto = " . $tpAlteracaoProjeto;
        endif;

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchRow($sql);
    }

    public static function verificaStatusFinal($idPedidoAlteracao)
    {
        $sql = "SELECT stAvaliacaoItemPedidoAlteracao as stAvaliacao FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao WHERE idPedidoAlteracao = $idPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaAnalise($idPlanilhaAprovacao, $idAvaliacaoItemPedidoAlteracao)
    {
        $sql = " SELECT a.stAvaliacaoSubItemPedidoAlteracao as stAvaliacao, CAST (dsAvaliacaoSubItemPedidoAlteracao as TEXT) as dsAvaliacaoSubItemPedidoAlteracao
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao AS a
                INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemCusto AS b ON a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracao
                        AND a.idAvaliacaoSubItemPedidoAlteracao = b.idAvaliacaoSubItemPedidoAlteracao
                WHERE b.idPlanilhaAprovacao = $idPlanilhaAprovacao AND a.idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao AND b.idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaAvaliacaoAnalise()
    {
        $sql = "SELECT * FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto tpa
    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao tai ON tpa.idPedidoAlteracao = tai.idPedidoAlteracao
    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemCusto tsi ON tsi.idAvaliacaoItemPedidoAlteracao = tai.idAvaliacaoItemPedidoAlteracao
    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao tsu ON tsu.idAvaliacaoSubItemPedidoAlteracao = tsi.idAvaliacaoSubItemPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function buscarUltimoRemetente($idacao)
    {
        $sql = "SELECT TOP 1 idAgenteRemetente AS idAgenteRemetente
                                    FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                                    WHERE idAvaliacaoItemPedidoAlteracao = $idacao
                                            AND idPerfilRemetente = 93
                                    ORDER BY dtEncaminhamento DESC";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    }

    public function buscarUltimoRemetenteCoordParecerista($idacao)
    {
        $sql = "SELECT TOP 1 idAgenteRemetente AS idAgenteRemetente
                                    FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                                    WHERE idAvaliacaoItemPedidoAlteracao = $idacao
                                    ORDER BY dtEncaminhamento DESC";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    }

    public function buscarUltimoRemetenteCoordPareceristaSemBD($idacao)
    {
        $sql = "SELECT TOP 1 idAgenteRemetente AS idAgenteRemetente
                                    FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                                    WHERE idAvaliacaoItemPedidoAlteracao = $idacao
                                    ORDER BY dtEncaminhamento DESC";
        return $sql;
    }

    public function buscarOrgao($idacao)
    {
        $sql = "select idorgao from BDCORPORATIVO.scSac.tbAcaoAvaliacaoItemPedidoAlteracao where idAvaliacaoItemPedidoAlteracao=$idacao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);

        return $db->fetchRow($sql);
    }

    public function buscarOrgaoSemDB($idacao)
    {
        $sql = "select idorgao from BDCORPORATIVO.scSac.tbAcaoAvaliacaoItemPedidoAlteracao where idAvaliacaoItemPedidoAlteracao=$idacao";
        return $sql;
    }

    public function buscarEtapa()
    {
        $sql = "select idPlanilhaEtapa, Descricao, tpCusto from SAC.dbo.tbPlanilhaEtapa";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function atualizarStatus($dados, $where)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $alterar = $db->update("bdcorporativo.scsac.tbavaliacaoitempedidoalteracao", $dados, $where);
        return $alterar;
    }
    
    public function atualizarPedido($dados, $where)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->update("BDCORPORATIVO.scSac.tbPedidoAlteracaoProjeto", $dados, $where);
    }

    public function atualizarTipoAlteracao($dados, $where)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $alterar = $db->update("BDCORPORATIVO.scSac.tbPedidoAlteracaoXTipoAlteracao", $dados, $where);
    }
    
    public function atualizarAvaliacaopedido($dados, $where)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $alterar = $db->update("BDCORPORATIVO.scSac.tbAvaliacaoItemPedidoAlteracao", $dados, $where);
    }
    
    public function atualizarAvaliacaoAcao($dados, $where)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $alterar = $db->update("BDCORPORATIVO.scSac.tbAcaoAvaliacaoItemPedidoAlteracao", $dados, $where);
    }
    
    public function insertAvaliacaoAcao($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $alterar = $db->insert("BDCORPORATIVO.scSac.tbAcaoAvaliacaoItemPedidoAlteracao", $dados);
    }
}
