<?php

/* ProjetosGerenciarDAO
 * @author Equipe RUP - Politec
 * @since 17/05/2010
 * @version 1.0
 * @package application
 * @subpackage application.controllers
 * @link http://www.politec.com.br
 * @copyright � 2010 - Politec - Todos os direitos reservados.
 */

class ProjetosGerenciarDAO extends Zend_Db_Table
{
    protected $_name = 'dbo.Agentes';

    /**************************************************************************************************************************
    * Fun��o que ret orna o sql desejado
    * ************************************************************************************************************************/
    public static function retornaSQL($sqlDesejado)
    {
        $sql = '';
        if ($sqlDesejado == "sqlAreaCmp") {
            $sql = "Select C.idAgente,
                                Ar.Descricao Area,
                                Nm.Descricao Nome
                                From AGENTES.dbo.tbTitulacaoConselheiro C
                                INNER JOIN SAC.dbo.Area Ar on ar.Codigo = C.cdArea
                                INNER JOIN AGENTES.dbo.Nomes Nm on Nm.idAgente = C.idAgente
                                Order by Ar.Descricao, Nm.Descricao";
        } elseif ($sqlDesejado == "sqlComponentes") {
            $sql = "SELECT T.idAgente,
                                        N.Descricao Nome,
                                        A.Descricao Area,
                                        (SELECT COUNT(SDPC.idPronac) as QTD
                                        FROM BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao SDPC
                                        WHERE SDPC.idAgente = T.idAgente AND SDPC.stDistribuicao = 'A')
                                        as QTD FROM AGENTES.dbo.tbTitulacaoConselheiro T
                                        INNER JOIN AGENTES.dbo.Nomes N on N.idAgente =   T.idAgente
                                        INNER JOIN SAC.dbo.Area A on A.Codigo =  T.cdArea
                                        WHERE T.stConselheiro = 'A' ORDER BY Nome";
        } elseif ($sqlDesejado == "sqlDesabilitados") {
            $sql = "Select C.idAgente,
					                                N.Descricao Nome,
					                                A.Descricao Area,
					                                H.dsJustificativa Just,
					                                CONVERT(CHAR(10), HT.dtHistorico,103) Data
					                                    From AGENTES.dbo.Nomes N,
					                                    SAC.dbo.Area A,
					                                    AGENTES.dbo.tbTitulacaoConselheiro C,
					                                    BDCORPORATIVO.scAGENTES.tbHistoricoConselheiro H,
					                                        (SELECT idConselheiro, MAX(dtHistorico) dtHistorico
					                                            FROM BDCORPORATIVO.scAGENTES.tbHistoricoConselheiro
					                                                WHERE stConselheiro = 'I'
					                                                    GROUP BY idConselheiro) HT
					                                                        WHERE C.cdArea = A.Codigo
					                                                            AND C.idAgente = N.idAgente
					                                                            AND H.idConselheiro = N.idAgente
					                                                            AND HT.dtHistorico = H.dtHistorico
					                                                            AND C.stConselheiro = 'I'";
        }
        return $sql;
    }

    /**************************************************************************************************************************
    * Fun��o para buscar os projetos do componente da comiss�o
    * ************************************************************************************************************************/

    public static function buscaProjetos($idAgente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sqlProjetosDoComponente = "SELECT
                    P.idPRONAC,
                    D.idAgente,
                    P.AnoProjeto + P.Sequencial AS PRONAC,
                    P.NomeProjeto,
                    DATEDIFF(DAY,D.dtDistribuicao,GETDATE()) as Dias,
                    CONVERT(CHAR(10), D.dtDistribuicao,103) AS dtDistribuicao,
                    CONVERT(CHAR(10), D.dtDistribuicao,103) AS dtCompleta
                    FROM SAC.dbo.Projetos P
                    Inner Join BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao D on D.idPRONAC = P.idPRONAC
                    WHERE D.stDistribuicao = 'A'
                    AND P.Situacao = 'C10'
                    AND D.idAgente = $idAgente
                    and not exists (select idpronac from BDCORPORATIVO.scSAC.tbPauta where idpronac = P.idPRONAC)";
        $projetos = $db->fetchAll($sqlProjetosDoComponente);
        return $projetos;
    }

    /**************************************************************************************************************************
    * Fun��o para encaminhar o projeto para outro componente da comiss�o
    * ************************************************************************************************************************/

    public static function encaminharProjeto($idPronac, $data, $justificativa, $agenteAtual, $agenteNovo)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $dados = "Update BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao " .
        " Set idAgente = $agenteNovo, dtDistribuicao = GETDATE(),  dsJustificativa='" . $justificativa . "'" .
        " Where idAgente =" . $agenteAtual . " AND idPronac=" . $idPronac;
//                die($dados);

        $atualiza = $db->query($dados);
        return true;
    }

    /**************************************************************************************************************************
    * Fun��o para desativar o componente da comiss�o
    * ************************************************************************************************************************/

    public function desativarComponente($idAgente, $justificativa)
    {

        // Busca os projetos que estavam alocados para o componente atual
        $sqlBuscaProjetosdoComponente = "SELECT D.idPRONAC, D.idAgente, CONVERT(CHAR(10), D.dtDistribuicao,103) AS Data
		                                        From BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao D
		                                        where D.stDistribuicao = 'A' AND idAgente=" . $idAgente;
        $result = $db->fetchAll($sqlBuscaProjetosdoComponente);

        foreach ($result as $dado) {
            // Deletar todos os dados da duas tabelas abaixo
            $whereDelete = "idPRONAC=" . $dado->idPRONAC;
            $deletaDasTabelas = $db->delete('SAC.dbo.Aprovacao', $whereDelete);
            $deletaDasTabelas2 = $db->delete('SAC.dbo.Enquadramento', $whereDelete);

            // Atualiza a situa��o do projeto para Inativo
            $dados = "Update BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao " .
            "Set stDistribuicao = 'I',  dsJustificativa='" . $justificativa . "'" .
            "Where idAgente =" . $idAgente . " AND idPronac=" . $dado->idPRONAC . " AND dtDistribuicao='" . $dado->Data . "'";

            $atualiza = $db->query($dados);

            // Faz o rebalanceamento de todos os projetos
            $up = ProjetosGerenciarDAO :: balancear($dado->idPRONAC);
        }
        return true;
    }

    /**************************************************************************************************************************
    * Fun��o para habilitar o componente da comiss�o para o balanceamento
    * ************************************************************************************************************************/

    public static function ativarComponente($idAgente, $justificativa, $usucodigo)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // Mudar a situa��o do componente da comiss�o para inativo 'I'
        $dadosUpdateSituacao = array(
            'stConselheiro' => 'A'
        );
        $whereUpdateSituacao = "idAgente =" . $idAgente;
        $UpdateSituacao = $db->update('AGENTES.dbo.tbTitulacaoConselheiro', $dadosUpdateSituacao, $whereUpdateSituacao);

        // Grava na tabela de historico
        $dadosInsereHistorico = "Insert into BDCORPORATIVO.scAGENTES.tbHistoricoConselheiro " .
        "(idConselheiro, dtHistorico, dsJustificativa, stConselheiro, idResponsavel)" .
        "values " .
        "($idAgente, GETDATE(), '$justificativa', 'A', $usucodigo)";

        $InsereHistorico = $db->query($dadosInsereHistorico);
        return true;
    }

    /**************************************************************************************************************************
    * Fun��o para fazer o rebalanceamento dos projetos
    * ************************************************************************************************************************/

    public static function balancear($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sqlProjetoAreaSegmento = "SELECT P.idPRONAC, A.Codigo as area, S.Codigo as segmento
		                                      FROM SAC.dbo.Projetos P, SAC.dbo.Area A, SAC.dbo.Segmento S
		                                      WHERE P.idPRONAC = " . $idPronac . "  AND  P.Area = A.Codigo  AND  P.Segmento = S.Codigo";

        // Busca a �rea e seguimento do projeto
        $PAS = $db->fetchAll($sqlProjetoAreaSegmento);
        foreach ($PAS as $dados) {
            $areaP = $dados->area;
            $segmentoP = $dados->segmento;
        }

        // Busca para verificar se existe algum componente para a area e segmento do projeto
        $sqlComponenteAreaSegmento = "SELECT C.idAgente, C.cdArea, C.cdSegmento, C.stTitular " .
        "FROM AGENTES.dbo.tbTitulacaoConselheiro C " .
        "WHERE C.stConselheiro = 'A' AND C.cdArea = " . $areaP . " AND C.cdSegmento = " . $segmentoP;

        $AAS = $db->fetchAll($sqlComponenteAreaSegmento);
        foreach ($AAS as $dados) {
            $agenteP = $dados->idAgente;
        }

        // Se n�o tiver componente com a Area e Segmento do projeto ele faz...
        if (!isset($agenteP) && $agenteP == '') {

            //aqui j� est� buscando o id do agente que tem a menor quantidade de projetos
            $sqlMenor = "SELECT TC.idAgente as agente,
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

            if (!empty($projetos)) {
                foreach ($projetos as $dados) {
                    $menor = $dados->agente;
                }
                if (!empty($agenteP)) {
                    $dados = "Insert into BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao " .
                "(idPRONAC, idAgente, dtDistribuicao, idResponsavel)" .
                "values" .
                "($idPronac, $agenteP, GETDATE(), 7522)";

                    $insere = $db->query($dados);
                    $db->closeConnection();
                }

                // Se tiver componente com a Area e Segmento do projeto ele faz...
            } else {
                if (!empty($menor)) {
                    $dados = "Insert into BDCORPORATIVO.scSAC.tbDistribuicaoProjetoComissao " .
                    "(idPRONAC, idAgente, dtDistribuicao, idResponsavel)" .
                    "values" .
                    "($idPronac, $menor, GETDATE(), 7522)";

                    $insere = $db->query($dados);
                    $db->closeConnection();
                }
            }
        }
    }
}
