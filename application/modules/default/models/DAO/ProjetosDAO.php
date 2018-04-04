<?php

/* ProjetosDAO
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class ProjetosDAO extends Zend_Db_Table
{
    protected $_name = 'dbo.Agentes';

    /*     * ************************************************************************************************************************
     * Fun��o que retorna o sql desejado
     * *********************************************************************************************************************** */

    public static function retornaSQL($sqlDesejado)
    {
        $sql = '';

        if ($sqlDesejado == "sqlProjetos") {
            $sql = "SELECT idPronac, Pronac, NomeProjeto, CodSituacao,Situacao,
						   idParecer, DtConsolidacao,ValorProposta,OutrasFontes,
						   ValorSolicitado,ValorSugerido,Elaboracao,ValorParecer,PERC,Acima
								FROM SAC.dbo.vwDesconsolidarParecer
									WHERE idSecretaria = 251
										ORDER BY DtConsolidacao, Pronac";
        }

        return $sql;
    }

    /*     * ************************************************************************************************************************
     * Fun��o que copia as tabelas SAC.dbo.tbPlanilhaProjeto e tbAnaliseDeConteudo
     * e cola nas tabelas tbPlanilhaProjetoConselheiro e tbAnaliseConteudoConselheiro
     * *********************************************************************************************************************** */

    public static function tbPlanilhaProjeto($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = "	INSERT INTO SAC.dbo.tbPlanilhaAprovacao
                                                (tpPlanilha,
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
                                                 tpAcao,
                                                 idRecursoDecisao,
                                                 stAtivo)
                                                 SELECT
                                                 'CO',
                                                 GETDATE(),
                                                 idPlanilhaProjeto,
                                                 idPlanilhaProposta,
                                                 idPRONAC,
                                                 idProduto,
                                                 idEtapa,
                                                 idPlanilhaItem,
                                                 Descricao,
                                                 idUnidade,
                                                 Quantidade,
                                                 Ocorrencia,
                                                 ValorUnitario,
                                                 QtdeDias,
                                                 TipoDespesa,
                                                 TipoPessoa,
                                                Contrapartida,
                                                FonteRecurso,
                                                UfDespesa,
                                                MunicipioDespesa,
                                                Justificativa,
                                                NULL,NULL,NULL,NULL,NULL,
                                                'S'
                                                FROM SAC.dbo.tbPlanilhaProjeto  WHERE idPRONAC=$idPronac;";
        $db->fetchAll($sql);
    }

    public static function tbAnaliseDeConteudo($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sqlAnaliseOriginal = "	INSERT INTO sac.dbo.tbAnaliseAprovacao
                                            (tpAnalise,
                                             dtAnalise,
                                             idAnaliseConteudo,
                                             IdPRONAC,
                                             idProduto,
                                             stLei8313,
                                             stArtigo3,
                                             nrIncisoArtigo3,
                                             dsAlineaArt3,
                                             stArtigo18,
                                             dsAlineaArtigo18,
                                             stArtigo26,
                                             stLei5761,
                                             stArtigo27,
                                             stIncisoArtigo27_I,
                                             stIncisoArtigo27_II,
                                             stIncisoArtigo27_III,
                                             stIncisoArtigo27_IV,
                                             stAvaliacao,
                                             dsAvaliacao)
                                             SELECT  'CO',
                                             GETDATE(),
                                             idAnaliseDeConteudo,
                                             idPronac,
                                             idProduto,
                                             Lei8313,
                                             Artigo3,
                                             IncisoArtigo3,
                                             AlineaArtigo3,
                                             Artigo18,
                                             AlineaArtigo18,
                                             Artigo26,
                                             Lei5761,
                                             Artigo27,
                                             IncisoArtigo27_I,
                                             IncisoArtigo27_II,
                                             IncisoArtigo27_III,
                                             IncisoArtigo27_IV,
                                             ParecerFavoravel,
                                             ParecerDeConteudo
                                             FROM sac.dbo.tbAnaliseDeConteudo WHERE idPRONAC=$idPronac  ";

        $db->fetchAll($sqlAnaliseOriginal);
    }

    /*     * ************************************************************************************************************************
     * Fun��o que faz o balanceamento
     * Pega o Componente que tem menos projeto da �rea do projeto
     * ou manda para o componente que � da �rea e seguimento do projeto
     * *********************************************************************************************************************** */

    public static function balancear($idPronac)
    {
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);

            $sqlProjetoAreaSegmento = "SELECT Pr.idPRONAC, 
        ar.Codigo as area,
        sg.Codigo as segmento 
        FROM SAC.dbo.Projetos Pr
        left JOIN SAC.dbo.Area ar on ar.Codigo = pr.Area
        left JOIN SAC.dbo.Segmento sg on sg.Codigo = pr.Segmento
        WHERE Pr.idPRONAC = $idPronac";

            // Busca a �rea e seguimento do projeto
            $PAS = $db->fetchAll($sqlProjetoAreaSegmento);
            foreach ($PAS as $dados) {
                $areaP = $dados->area;
                $segmentoP = $dados->segmento;
            }

            // Busca para verificar se existe algum componente para a area e segmento do projeto
            $sqlComponenteAreaSegmento = "
        SELECT C.idAgente,
               C.cdArea,
               C.cdSegmento,
               C.stTitular
               FROM AGENTES.dbo.tbTitulacaoConselheiro C 
               WHERE C.stConselheiro = 'A' AND C.cdArea = " . $areaP;
        
            $AAS = $db->fetchAll($sqlComponenteAreaSegmento);

            // Se n�o tiver componente com a Area e Segmento do projeto ele faz...
            if (count($ASS)==0) {

            //aqui j� est� buscando o id do agente que tem a menor quantidade de projetos
                $sqlMenor = "SELECT TOP 1 TC.idAgente as agente,
					       PXC.Qtd
					FROM AGENTES.dbo.tbTitulacaoConselheiro TC
					INNER JOIN (SELECT ATC.idAgente, COUNT(DPC.idPronac) Qtd
					            FROM  AGENTES.dbo.tbTitulacaoConselheiro ATC
					            LEFT JOIN BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao DPC ON ATC.idAgente = DPC.idAgente
					            WHERE ATC.stConselheiro = 'A'
					            AND DPC.stDistribuicao = 'A'
					            OR DPC.stDistribuicao IS NULL
					            GROUP BY ATC.idAgente
					            UNION
					            SELECT ATC.idAgente, COUNT(DPC.idPronac) - COUNT(DPCI.idPronac) Qtd
					            FROM  AGENTES.dbo.tbTitulacaoConselheiro ATC
					            LEFT JOIN BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao DPC ON ATC.idAgente = DPC.idAgente
					            LEFT JOIN BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao DPCI ON ATC.idAgente = DPCI.idAgente
					            WHERE ATC.stConselheiro = 'A'
					            AND DPCI.stDistribuicao = 'I'
					            AND ATC.idAgente NOT IN (SELECT DISTINCT ATC.idAgente
					                                     FROM  AGENTES.dbo.tbTitulacaoConselheiro ATC
					                                     LEFT JOIN BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao DPC ON ATC.idAgente = DPC.idAgente
					                                     WHERE ATC.stConselheiro = 'A'
					                                     AND DPC.stDistribuicao = 'A'
					                                     OR DPC.stDistribuicao IS NULL)
					            GROUP BY ATC.idAgente) PXC ON PXC.idAgente = TC.idAgente
					WHERE TC.cdArea = " . $areaP . "
					ORDER BY PXC.Qtd, TC.idAgente ";

                $projetos = $db->fetchAll($sqlMenor);

                foreach ($projetos as $dados) {
                    $menor = $dados->agente;
                }

                $dados = "Insert into BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao " .
                    "(idPRONAC, idAgente, dtDistribuicao, idResponsavel)" .
                    "values" .
                    "($idPronac, $menor, GETDATE(), 7522);
                    UPDATE SAC.dbo.Projetos SET dtSituacao=GETDATE(), Situacao = 'C10' WHERE IdPRONAC = $idPronac;";

                $insere = $db->query($dados);
                // Se tiver componente com a Area e Segmento do projeto ele faz...
            } else {
                $dados = "Insert into BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao " .
                    "(idPRONAC, idAgente, dtDistribuicao, idResponsavel)" .
                    "values" .
                    "($idPronac, ".$AAS[0]->idAgente.", GETDATE(), 7522);
                    UPDATE SAC.dbo.Projetos SET dtSituacao=GETDATE(), Situacao = 'C10',  WHERE IdPRONAC = $idPronac;";
                $insere = $db->query($dados);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            echo $sqlComponenteAreaSegmento;
            die;
        }
        // atualiza a situa��o do projeto
//        $atualizarProjeto = "UPDATE SAC.dbo.Projetos SET Situacao = 'C10' WHERE IdPRONAC = $idPronac";
//        $db->fetchAll($atualizarProjeto);
    }

    /*     * ************************************************************************************************************************
     * Altera a situa��o do projeto para C10 para n�o aparecer na tela
     * Situa��o de enviado para o componente
     * *********************************************************************************************************************** */

    public static function alteraProjeto($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $dados = array('Situacao' => 'C10');
        $where = "IdPRONAC =" . $idPronac;
        $n = $db->update('SAC.dbo.Projetos', $dados, $where);
        $db->closeConnection();
    }

    public static function alterarDadosProjeto($dados, $idpronac)
    {
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $where = "idpronac = $idpronac";
            $alterar = $db->update("SAC.dbo.Projetos", $dados, $where);
        } catch (Exception $e) {
            die("ERRO: AlterarDadosProjeto-ProjetoDAO. ".$e->getMessage());
        }
    }
}
