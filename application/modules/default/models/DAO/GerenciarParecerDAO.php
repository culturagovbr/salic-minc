<?php
/**
 * DAO GerenciarParecerDAO
 * @author Equipe RUP - Politec
 * @since 20/01/2011
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class GerenciarParecerDAO extends MinC_Db_Table_Abstract
{
    /* dados da tabela */
    protected $_banco   = "SAC";
    protected $_schema  = "SAC";
    protected $_name    = "tbDistribuirParecer";



    /* EXCLUIR ESSA CLASSE J� EST� NO PADR�O ZEND_DB
     * ALTERADO POR TARCISIO.
     * UC 103
     * GERENCIAR PARECER
     */



/*
    public static function listarProjetos($org_codigo)
    {

        $sql = "SELECT  t.idDistribuirParecer, t.idOrgao, p.IdPRONAC, p.AnoProjeto + p.Sequencial AS NrProjeto, p.NomeProjeto, t.idProduto, r.Descricao AS Produto,
                        t.DtDevolucao,
                        CASE WHEN TipoAnalise = 0 THEN 'Cont�udo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo' END
                        AS DescricaoAnalise, t.TipoAnalise, SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs, a.Descricao AS Area,
                        s.Descricao AS Segmento, t.DtEnvio,  CONVERT(CHAR(10),t.DtEnvio,103) AS DtEnvioPT
                FROM    SAC.dbo.tbDistribuirParecer AS t
                        INNER JOIN SAC.dbo.Projetos AS p ON t.idPRONAC = p.IdPRONAC
                        INNER JOIN SAC.dbo.Produto AS r ON t.idProduto = r.Codigo
                        INNER JOIN SAC.dbo.Area AS a ON p.Area = a.Codigo
                        INNER JOIN SAC.dbo.Segmento AS s ON p.Segmento = s.Codigo
                WHERE   t.stEstado = 0
                          AND t.FecharAnalise = 0
                        AND t.TipoAnalise <> 2
                        AND p.Situacao IN ('B11', 'B14')
                        AND t.idOrgao = " . $org_codigo . "
                             ORDER BY t.DtEnvio, r.Descricao, p.AnoProjeto + p.Sequencial, TipoAnalise";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;


    }

    public static function listarProjetosSub($idPronac)
    {

        $sql = "SELECT * FROM SAC.dbo.tbDiligencia
                      WHERE idPronac = ".$idPronac."
                          AND stEstado = 0
                          AND DtResposta IS NULL";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;

    }

    public static function vwAnaliseConteudo($idPronac, $idProduto)
    {

        $sql = "SELECT    p.IdPRONAC, p.AnoProjeto + p.Sequencial AS PRONAC, p.NomeProjeto, i.Nome AS Proponente, pr.Descricao AS Produto, a.idProduto,
                          CASE WHEN Lei8313 = 1 THEN 'Sim' ELSE 'N�o' END AS Lei8313, CASE WHEN Artigo3 = 1 THEN 'Sim' ELSE 'N�o' END AS Artigo3,
                          CASE WHEN IncisoArtigo3 = 1 THEN 'I' WHEN IncisoArtigo3 = 2 THEN 'II' WHEN IncisoArtigo3 = 3 THEN 'III' WHEN IncisoArtigo3 = 4 THEN 'IV' WHEN IncisoArtigo3
                          = 5 THEN 'V' END AS IncisoArtigo3, a.AlineaArtigo3, CASE WHEN Artigo18 = 1 THEN 'Sim' ELSE 'N�o' END AS Artigo18, a.AlineaArtigo18,
                          CASE WHEN Artigo26 = 1 THEN 'Sim' ELSE 'N�o' END AS Artigo26, CASE WHEN Lei5761 = 1 THEN 'Sim' ELSE 'N�o' END AS Lei5761,
                          CASE WHEN Artigo27 = 1 THEN 'Sim' ELSE 'N�o' END AS Artigo27, CASE WHEN IncisoArtigo27_I = 1 THEN 'Sim' ELSE 'N�o' END AS IncisoArtigo27_I,
                          CASE WHEN IncisoArtigo27_II = 1 THEN 'Sim' ELSE 'N�o' END AS IncisoArtigo27_II,
                          CASE WHEN IncisoArtigo27_III = 1 THEN 'Sim' ELSE 'N�o' END AS IncisoArtigo27_III,
                          CASE WHEN IncisoArtigo27_IV = 1 THEN 'Sim' ELSE 'N�o' END AS IncisoArtigo27_IV,
                          CASE WHEN TipoParecer = 1 THEN 'Aprova��o' WHEN TipoParecer = 2 THEN 'Complementa��o' WHEN TipoParecer = 4 THEN 'Redu��o' END AS TipoParecer,
                          CASE WHEN ParecerFavoravel = 1 THEN 'Sim' ELSE 'N�o' END AS ParecerFavoravel, a.ParecerDeConteudo, SAC.dbo.fnNomeParecerista(a.idUsuario)
                          AS Parecerista
                FROM      SAC.dbo.Projetos AS p
                          INNER JOIN SAC.dbo.Interessado AS i ON p.CgcCpf = i.CgcCpf
                          INNER JOIN SAC.dbo.tbAnaliseDeConteudo AS a ON p.IdPRONAC = a.idPronac
                          INNER JOIN SAC.dbo.Produto AS pr ON a.idProduto = pr.Codigo
                WHERE     (a.idUsuario IS NOT NULL) AND p.IdPRONAC = ".$idPronac." AND idProduto = ".$idProduto;


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;

    }


    public static function historico($idPronac , $idProduto, $TipoAnalise)
    {

        $sql = "select idPronac,idProduto, Descricao as Produto,
                       case TipoAnalise
                          when 0 then 'Cont�udo'
                          when 1 then 'Custo do Produto'
                          else 'Custo Administrativo'
                          end as TipoAnalise,
                          d.idOrgao,tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) as Unidade,
                          DtEnvio,
                          CONVERT(CHAR(10),DtEnvio,103) AS DtEnvioPT,
                          Observacao,
                SAC.dbo.fnNomeUsuario(idUsuario) as Usuario
                from SAC.dbo.tbdistribuirParecer d
                inner join SAC.dbo.Produto p on (d.idProduto = p.Codigo)
                where idPronac=".$idPronac." and idProduto = ".$idProduto." and TipoAnalise = ".$TipoAnalise."
                order by idDistribuirParecer DESC";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;


    }

    public static function dadosParaDistribuir($idPronac , $idProduto, $TipoAnalise)
    {

        $sql = "SELECT     t.idDistribuirParecer, t.idOrgao, p.IdPRONAC, p.AnoProjeto + p.Sequencial AS NrProjeto, p.NomeProjeto, t.idProduto, r.Descricao AS Produto,
                      t.DtDevolucao,
                      CASE WHEN TipoAnalise = 0 THEN 'Cont�udo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo' END
                      AS DescricaoAnalise, t.TipoAnalise, SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs, a.Descricao AS Area,
                      s.Descricao AS Segmento,
                      CONVERT(CHAR(10), t.DtDistribuicao, 103) AS DtDistribuicaoPT,
                      CONVERT(CHAR(10), t.DtDevolucao, 103) AS DtDevolucaoPT,
                      t.DtEnvio
                FROM  SAC.dbo.tbDistribuirParecer AS t INNER JOIN
                      SAC.dbo.Projetos AS p ON t.idPRONAC = p.IdPRONAC INNER JOIN
                      SAC.dbo.Produto AS r ON t.idProduto = r.Codigo INNER JOIN
                      SAC.dbo.Area AS a ON p.Area = a.Codigo INNER JOIN
                      SAC.dbo.Segmento AS s ON p.Segmento = s.Codigo
                WHERE (t.stEstado = 0) AND (t.FecharAnalise = 0) AND (t.TipoAnalise <> 2) AND (p.Situacao IN ('B11', 'B14'))
                AND p.IdPRONAC=".$idPronac." and t.idProduto=".$idProduto." and TipoAnalise = ".$TipoAnalise;


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;


    }

    public static function pareceristasDoOrgao($idOrgao)
    {

        $sql = "SELECT    n.idAgente AS idParecerista, n.Descricao AS Nome, u.org_superior AS idOrgao
                FROM      AGENTES.dbo.Agentes AS a
                        INNER JOIN AGENTES.dbo.Nomes AS n ON a.idAgente = n.idAgente
                        INNER JOIN TABELAS.dbo.vwUsuariosOrgaosGrupos AS u ON a.CNPJCPF = u.usu_identificacao
                            AND u.sis_codigo = 21
                            AND (u.gru_codigo = 94 OR u.gru_codigo = 105)
                        INNER JOIN AGENTES.dbo.Visao AS v ON n.idAgente = v.idAgente
                WHERE     (v.Visao = 209) AND (n.TipoNome = 18)
                AND u.org_superior=".$idOrgao." order by nome";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }


    public static function orgaosEncaminhar($idorgao)
    {

        $sql = "SELECT Codigo, Sigla
                FROM SAC.dbo.orgaos
                WHERE Vinculo = 1
                        AND Status = 0
                        AND Codigo <> ".$idorgao."
                ORDER BY Sigla";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;

    }




    public static function distribuirParecer($idpronac, $idproduto, $observacao, $tipoanalise, $idusuario, $idAgenteParecerista)
    {
        $sql = "UPDATE SAC.dbo.tbDistribuirParecer SET  FecharAnalise=0,
                                                        Observacao = '".$observacao."',
                                                        idUsuario = ".$idusuario.",
                                                        idAgenteParecerista = ".$idAgenteParecerista."

                    WHERE idpronac    = ".$idpronac."
                    AND   idproduto   = ".$idproduto."
                    AND   tipoanalise = ".$tipoanalise."
                    AND   stestado    = 0";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }

    public static function encaminharParecer($idpronac, $idproduto, $observacao, $tipoanalise, $idusuario, $idorgao)
    {
        $sql = "UPDATE SAC.dbo.tbDistribuirParecer SET DtEnvio=GETDATE(),
                                                       FecharAnalise=0,
                                                       Observacao = '".$observacao."',
                                                       idUsuario = ".$idusuario.",
                                                       idOrgao = ".$idorgao." ,
                                                       idAgenteParecerista = null,
                                                       DtDistribuicao = null,
                                                       DtDevolucao = null,
                                                       DtRetorno = null
                    WHERE idpronac    = ".$idpronac."
                    AND   idproduto   = ".$idproduto."
                    AND   tipoanalise = ".$tipoanalise."
                    AND   stestado    = 0";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }


    public static function concluirParecer($idpronac, $idproduto, $observacao, $tipoanalise, $idusuario)
    {
        $sql = "UPDATE SAC.dbo.tbDistribuirParecer SET FecharAnalise=1, Observacao = '".$observacao."', idUsuario = ".$idusuario."
                    WHERE idpronac    = ".$idpronac."
                    AND   idproduto   = ".$idproduto."
                    AND   tipoanalise = ".$tipoanalise."
                    AND   stestado    = 0";




        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }

*/
}
