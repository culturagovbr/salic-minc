<?php 
class AnalisarProjetoParecerDAO extends Zend_Db_Table
{
    protected $_name    = 'SAC.dbo.Projetos';

    /* EXCLUIR ESSA CLASSE
       	 * ATUALIZADA PARA ZEND_DB_TABLE
       	 * ALTERA��O TARCISIO
       	 * UC 101
       	 * IMPLEMENTADA POR YAN
       	 *
       	 */
           
           
           
           
           
           /*
           public static function dadosPainel($usu_Codigo, $idOrgao)
           {
               $sql = "SELECT     SAC.dbo.fnchecarDiligencia(p.IdPRONAC) AS Diligencia, d.idDistribuirParecer, p.IdPRONAC, p.AnoProjeto + p.Sequencial AS PRONAC, p.NomeProjeto,
                                          d.idProduto, pr.Descricao AS Produto, d.idAgenteParecerista, d.idOrgao, u.usu_codigo, CONVERT(CHAR(10),d.DtDistribuicao,103) AS DtDistribuicao2, DATEDIFF(day, d.DtDistribuicao, GETDATE())
                                          AS NrDias, d.Observacao,
                                          CASE WHEN TipoAnalise = 0 THEN 'Cont�udo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo' END
                                           AS DescricaoAnalise, d.TipoAnalise, AGENTES.dbo.fnNome(d.idAgenteParecerista) AS Parecerista
                    FROM         SAC.dbo.Projetos AS p INNER JOIN
                                          SAC.dbo.tbDistribuirParecer AS d ON p.IdPRONAC = d.idPRONAC INNER JOIN
                                          AGENTES.dbo.Agentes AS a ON d.idAgenteParecerista = a.idAgente INNER JOIN
                                          TABELAS.dbo.Usuarios AS u ON a.CNPJCPF = u.usu_identificacao LEFT OUTER JOIN
                                          SAC.dbo.Produto AS pr ON d.idProduto = pr.Codigo
                    WHERE     (d.stEstado = 0) AND (d.DtDistribuicao IS NOT NULL) AND (d.DtDevolucao IS NULL) AND (p.Situacao = 'B11' OR
                                          p.Situacao = 'B14')

                    AND u.usu_Codigo = ".$usu_Codigo." AND
                          d.idOrgao = ".$idOrgao." AND
                          TipoAnalise <> 2
                    ORDER BY d.DtDistribuicao";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $resultado = $db->fetchAll($sql);

            return $resultado;
           }

        public static function historico($g_idPronac, $g_idproduto, $g_tipoanalise)
           {
            $sql = "select idPronac,idProduto, Descricao as Produto,
                           case TipoAnalise
                              when 0 then 'Cont�udo'
                              when 1 then 'Custo do Produto'
                              else 'Custo Administrativo'
                              end as TipoAnalise
                             ,d.idOrgao,tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) as Unidade,DtEnvio,Observacao,
                    SAC.dbo.fnNomeUsuario(idUsuario) as Usuario
                    from SAC.dbo.tbdistribuirParecer d
                    inner join SAC.dbo.Produto p on (d.idProduto = p.Codigo)
                    where idPronac=".$g_idPronac." and
                            idProduto = ".$g_idproduto." and
                            TipoAnalise = ".$g_tipoanalise."
                    order by idDistribuirParecer DESC";

                    $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $resultado = $db->fetchAll($sql);

                    return $resultado;
        }


        public static function buscatipoanalise($idPronac, $idProduto)
        {
            $sql = "select * from SAC.dbo.tbAnaliseDeConteudo where idPronac = ".$idPronac." and idProduto = ".$idProduto;

            $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $resultado = $db->fetchAll($sql);

                    return $resultado;
        }

        public static function nrsequencialpronac($idPronac){
            $sql = "select anoprojeto+sequencial as pronac from SAC.dbo.projetos where idpronac=".$idPronac;

             $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $resultado = $db->fetchAll($sql);

                    return $resultado;
        }

        public static function pegavalorprojeto($idpronac, $idproduto){
            $sql = "SELECT p.nomeprojeto,pr.Descricao
                        FROM         SAC.dbo.Projetos AS p INNER JOIN
                                              SAC.dbo.tbDistribuirParecer AS d ON p.IdPRONAC = d.idPRONAC INNER JOIN
                                              AGENTES.dbo.Agentes AS a ON d.idAgenteParecerista = a.idAgente INNER JOIN
                                              TABELAS.dbo.Usuarios AS u ON a.CNPJCPF = u.usu_identificacao LEFT OUTER JOIN
                                              SAC.dbo.Produto AS pr ON d.idProduto = pr.Codigo
                        WHERE     (d.stEstado = 0) AND (d.DtDistribuicao IS NOT NULL) AND (d.DtDevolucao IS NULL) AND (p.Situacao = 'B11' OR
                                              p.Situacao = 'B14') and p.idpronac=".$idpronac." and d.idproduto=".$idproduto;

            $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $resultado = $db->fetchAll($sql);

                    return $resultado;
        }

        public static function  updateTipoAnalise(array $data, $where) {
            $db = Zend_Db_Table::getDefaultAdapter();
            try{
                $db->update('SAC.dbo.tbAnaliseDeConteudo',$data,$where);
                return true;
            }catch(Zend_Db_Exception $e){
                return false;
            }
        }

        public static function inserirDiligencia($data){
            $db = Zend_Db_Table::getDefaultAdapter();
            try{
                $db->insert('SAC.dbo.tbDiligencia',$data);
                return true;
            }catch(Zend_Db_Exception $e){
                return false;
            }
        }

        public static function buscaDiligencia($idPronac=null, $idProduto=null, $idSolicitante=null, $idDiligencia=null){
            $sql ="SELECT d.idDiligencia
                          ,p.AnoProjeto + p.Sequencial AS PRONAC
                          ,d.idPronac
                          ,d.idTipoDiligencia
                          ,d.DtSolicitacao
                          ,d.Solicitacao
                          ,d.idSolicitante
                          ,d.DtResposta
                          ,d.Resposta
                          ,d.idProponente
                          ,d.stEstado
                          ,d.idPlanoDistribuicao
                          ,d.idArquivo
                          ,d.idCodigoDocumentosExigidos
                          ,d.idProduto
                          ,d.stProrrogacao
                          ,d.stEnviado
                          ,p.NomeProjeto
                          ,v.Descricao
                          ,a.nmArquivo
                          ,a.sgExtensao
                          ,doc.Opcao
                          ,doc.Descricao as DocumentosExigidos
                      FROM SAC.dbo.tbDiligencia as d
                            INNER JOIN SAC.dbo.Projetos AS p on d.idPronac = p.IdPRONAC
                            LEFT JOIN Sac.dbo.DocumentosExigidos AS doc on d.idCodigoDocumentosExigidos = doc.Codigo
                            INNER JOIN SAC.dbo.Verificacao AS v on d.idTipoDiligencia = v.idVerificacao
                            LEFT JOIN BDCORPORATIVO.scCorp.tbArquivo AS a on d.idArquivo = a.idArquivo";

            if($idPronac !=null || $idProduto !=null || $idSolicitante !=null || $idDiligencia !=null){
                $sql.=" WHERE ";
                if($idPronac !=null){
                    $sql1[] ="d.idPronac = ".$idPronac;
                }
                if($idProduto !=null){
                    $sql1[] ="d.idProduto = ".$idProduto;
                }
                if($idSolicitante !=null){
                    $sql1[] ="d.idSolicitante = ".$idSolicitante;
                }
                if($idDiligencia !=null){
                    $sql1[] ="d.idDiligencia = ".$idDiligencia;
                }
            }

            $sql.=   implode( " AND ", $sql1 );
            //print_r($sql);die;

            $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $resultado = $db->fetchAll($sql);

                    return $resultado;
        }

        public static function produto($idProduto){
            $sql="SELECT Codigo
                      ,Descricao
                      ,Area
                      ,Idorgao
                      ,Sintese
                      ,stEstado
                  FROM SAC.dbo.Produto
                  WHERE Codigo = ".$idProduto;

            $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $resultado = $db->fetchAll($sql);

                    return $resultado;
        }

        public static function buscaTipo(){
             $sql="select idVerificacao, Descricao from SAC.dbo.Verificacao WHERE idtipo=8 and stEstado=1";

             $db = Zend_Db_Table::getDefaultAdapter();
                    $db->setFetchMode(Zend_DB::FETCH_OBJ);
                    $resultado = $db->fetchAll($sql);

                    return $resultado;
        }

        public static function analiseDeCustos($idpronac, $idItem=null)
    {
        $sql = "SELECT		a.IdPRONAC,
            a.AnoProjeto + a.Sequencial AS PRONAC,
            a.NomeProjeto,
            b.idProduto,
            b.idPlanilhaProjeto,
            PAP.dsJustificativa dsJustificativaConselheiro,
            (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro,
            CASE WHEN b.idProduto = 0 THEN 'Administra��o do Projeto' ELSE c.Descricao END AS Produto, CONVERT(varchar(8),
            d.idPlanilhaEtapa) + ' - ' + d.Descricao AS Etapa,
            d.idPlanilhaEtapa,
            i.Descricao AS Item,
                        i.idPlanilhaItens As idItem,
            z.Quantidade * z.Ocorrencia * z.ValorUnitario AS VlSolicitado,
            e.Descricao AS Unidade,
                        e.idUnidade,
            b.Quantidade,
            b.Ocorrencia,
            b.ValorUnitario,
            b.QtdeDias,
            b.TipoDespesa,
            b.TipoPessoa,
            b.Contrapartida,
            b.FonteRecurso AS idFonte,
            x.Descricao AS FonteRecurso,
            f.UF,
            f.idUF,
            f.Municipio,
            f.idMunicipio,
            ROUND(b.Quantidade * b.Ocorrencia * b.ValorUnitario, 2) AS Sugerido,
            CAST(b.Justificativa AS TEXT) AS Justificativa,
            b.idUsuario
FROM         SAC.dbo.Projetos AS a
                      INNER JOIN SAC.dbo.tbPlanilhaProjeto AS b ON a.IdPRONAC = b.idPRONAC
                      INNER JOIN SAC.dbo.tbPlanilhaProposta AS z ON b.idPlanilhaProposta = z.idPlanilhaProposta
                      left JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = z.idPlanilhaProposta)
                      LEFT OUTER JOIN SAC.dbo.Produto AS c ON b.idProduto = c.Codigo
                      INNER JOIN SAC.dbo.tbPlanilhaEtapa AS d ON b.idEtapa = d.idPlanilhaEtapa
                      INNER JOIN SAC.dbo.tbPlanilhaUnidade AS e ON b.idUnidade = e.idUnidade
                      INNER JOIN SAC.dbo.tbPlanilhaItens AS i ON b.idPlanilhaItem = i.idPlanilhaItens
                      INNER JOIN SAC.dbo.Verificacao AS x ON b.FonteRecurso = x.idVerificacao
                      INNER JOIN AGENTES.dbo.vUFMunicipio AS f ON b.UfDespesa = f.idUF AND b.MunicipioDespesa = f.idMunicipio
                      WHERE a.IdPRONAC = ".$idpronac;
                                                if($idItem != null){
                                                   $sql.="and i.idPlanilhaItens = ".$idItem;
                                                }
                                               $sql.= "ORDER BY x.Descricao, Produto, Etapa, UF, Item";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);

    }

        public static function buscaunidade()
    {
            $sql = "SELECT idUnidade, Descricao FROM SAC.dbo.tbPlanilhaUnidade ORDER BY Descricao";

            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        }

        public static function dadosFechar($usu_Codigo, $idPronac, $TipoAnalise)
           {
               $sql = "SELECT     SAC.dbo.fnchecarDiligencia(p.IdPRONAC) AS Diligencia, d.idDistribuirParecer, p.IdPRONAC, p.AnoProjeto + p.Sequencial AS PRONAC, p.NomeProjeto,
                                          d.idProduto, pr.Descricao AS Produto, d.idAgenteParecerista, d.idOrgao, u.usu_codigo, CONVERT(CHAR(10),d.DtDistribuicao,103) AS DtDistribuicao2, DATEDIFF(day, d.DtDistribuicao, GETDATE())
                                          AS NrDias, d.Observacao,
                                          CASE WHEN TipoAnalise = 0 THEN 'Cont�udo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo' END
                                           AS DescricaoAnalise, d.TipoAnalise, AGENTES.dbo.fnNome(d.idAgenteParecerista) AS Parecerista
                    FROM         SAC.dbo.Projetos AS p INNER JOIN
                                          SAC.dbo.tbDistribuirParecer AS d ON p.IdPRONAC = d.idPRONAC INNER JOIN
                                          AGENTES.dbo.Agentes AS a ON d.idAgenteParecerista = a.idAgente INNER JOIN
                                          TABELAS.dbo.Usuarios AS u ON a.CNPJCPF = u.usu_identificacao LEFT OUTER JOIN
                                          SAC.dbo.Produto AS pr ON d.idProduto = pr.Codigo
                    WHERE     (d.stEstado = 0) AND (d.DtDistribuicao IS NOT NULL) AND (d.DtDevolucao IS NULL) AND (p.Situacao = 'B11' OR
                                          p.Situacao = 'B14')

                    AND u.usu_Codigo = ".$usu_Codigo." AND
                                              p.IdPRONAC = ".$idPronac." AND
                                              d.TipoAnalise = ".$TipoAnalise." AND
                          TipoAnalise <> 2
                    ORDER BY d.DtDistribuicao";

                        $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $resultado = $db->fetchAll($sql);

            return $resultado;
           }

        public static function atualizarParecer(array $data, $where) {
            $db = Zend_Db_Table::getDefaultAdapter();
            try{
                $db->update('SAC.dbo.tbDistribuirParecer',$data,$where);
                return true;
            }catch(Zend_Db_Exception $e){
                return false;
            }
        }

        public static function updateItem(array $data, $where) {
            $db = Zend_Db_Table::getDefaultAdapter();
            try{
                $db->update('SAC.dbo.tbPlanilhaProjeto',$data,$where);
                return true;
            }catch(Zend_Db_Exception $e){
                print_r($e);
                die();
                return false;
            }
        }
*/
}
