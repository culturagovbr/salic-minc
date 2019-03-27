<?php
class SolicitarRecursoDecisaoDAO extends Zend_Db_Table
{
    /**
     * Metodo para buscar os Projetos Aprovados e Nao Aprovados
     * @access public
     * @static
     * @param integer $idPronac
     * @param string $cpf_cnpj
     * @return object
     * @todo tranformar em Zend_DB
     */
    public static function buscarProjetos($idPronac = null, $cpf_cnpj = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = "SELECT Pr.AnoProjeto+Pr.Sequencial AS pronac,Pr.IdPRONAC,Pr.NomeProjeto,St.descricao AS situacao,pr.Cgccpf,
                    CASE
                      WHEN (pr.Situacao = 'D02' OR pr.Situacao = 'D03')
                           THEN 'Projeto Aprovado'
                           ELSE 'Projeto Indeferido'
                      END AS StatusProjeto
             FROM SAC.dbo.Projetos Pr
             INNER JOIN SAC.dbo.Situacao St ON (St.Codigo = Pr.Situacao)
             WHERE pr.Situacao in ('A14','A16','A17','A20','A23','A24','A41','D02','D03','D14','B02', 'D51')";

        // caso o id do pronac seja informado
        if (!empty($idPronac)) {
            $sql.= "AND Pr.IdPRONAC = '". $idPronac ."' ";
        }
        // caso o cpf/cnpj seja informado
        if (!empty($cpf_cnpj)) {
            $sql.= "AND Pr.CgcCpf = '". $cpf_cnpj ."' ";
        }

        return  $db->fetchAll($sql);
    }

    /**
     * Metodo para buscar a planilha com o orcamento
     * @access public
     * @static
     * @param integer $idPronac
     * @param string $cpf_cnpj
     * @return object
     */
    public static function buscarPlanilhaOrcamento($idPronac = null, $idProduto = null, $idEtapa = null, $idUF = null, $idCidade = null, $buscarPorProduto = null)
    {
        $sql = "SELECT DISTINCT I.idPlanilhaItens AS idPlanilhaItens
                    ,RPA.dsJustificativa as justificativa
                    ,PRO.AnoProjeto+Sequencial as pronac
                    ,PAP.vlUnitario AS valorUnitario_con
                    ,PAP.idPlanilhaAprovacao
                    ,PAP.IdPRONAC
                    ,PAP.idProduto
                    ,PRO.NomeProjeto
                    ,PD.Descricao AS Produto
                    ,I.Descricao AS Item
                    ,(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS VlSolicitado
                    ,((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario)) AS VlReduzidoParecerista
                    ,(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) AS VlSugeridoParecerista
                    ,PPJ.Justificativa dsJustificativaParecerista
                    ,((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario)) AS VlReduzidoConselheiro
                    ,(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro
                    ,PAP.dsJustificativa dsJustificativaConselheiro

                FROM SAC.dbo.Projetos PRO
                    ,SAC.dbo.tbPlanilhaProjeto PPJ
                     INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
                     INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
                     INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
                     INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
                     left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)
                     LEFT JOIN SAC.dbo.tbRecursoXPlanilhaAprovacao RPA on RPA.idPlanilhaAprovacao = PAP.idPlanilhaAprovacao
                     RIGHT JOIN SAC.dbo.tbRecurso Rec on Rec.idRecurso = RPA.idRecurso

                WHERE PAP.IdPRONAC = PRO.IdPRONAC
                    AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
                    AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
                    AND PP.FonteRecurso = 109
                    AND PAP.stAtivo = 'S'";

        if (!empty($idRecurso)) {
            $sql.= " AND Rec.idRecurso = $idRecurso";
        }
        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }
        // busca de acordo com o produto
        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND PAP.idProduto = $idProduto";
        }
        // busca de acordo com a etapa
        if (!empty($idEtapa)) {
            $sql.= " AND PAP.idEtapa = $idEtapa";
        }
        // busca de acordo com a uf
        if (!empty($idUF)) {
            $sql.= " AND PAP.idUFDespesa = $idUF";
        }
        // busca de acordo com a cidade
        if (!empty($idCidade)) {
            $sql.= " AND PAP.idMunicipioDespesa = $idCidade";
        }

        $sql.= " ORDER BY I.Descricao ASC";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarproponentes($cpf)
    {
        $sql = "SELECT Pr.AnoProjeto+Pr.Sequencial as pronac ,
			Pr.idPRONAC,
			Pr.NomeProjeto,
			CASE WHEN N.Descricao IS NULL
			THEN I.Nome
			ELSE N.Descricao
			END AS nmProponente
			FROM SAC.dbo.Projetos Pr
			INNER JOIN AGENTES.dbo.Agentes A ON A.CNPJCPF = Pr.CgcCpf
			INNER JOIN AGENTES.dbo.Nomes N ON N.idAgente = A.idAgente
			INNER JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf
			where A.CNPJCPF = " . $cpf . "";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } // fecha m�todo buscarproponentes()

    public static function buscaprojetosaprovados($idpronac = null, $cpf = null)
    {
        $sql = "SELECT
				Pr.AnoProjeto+Pr.Sequencial as nrpronac, Pr.NomeProjeto as nmprojeto,
				Pr.IdPRONAC, a.CNPJCPF,
				St.descricao as situacao,
				CASE WHEN N.Descricao IS NULL
				THEN I.Nome
				ELSE N.Descricao
				END AS nmproponente
				FROM SAC.dbo.Projetos Pr
				INNER JOIN SAC.dbo.Situacao St ON (St.Codigo = Pr.Situacao) and (St.Codigo = 'E10' or St.Codigo = 'D09' or St.Codigo = 'D38' or St.Codigo = 'D11' or St.Codigo = 'D25' or St.Codigo = 'D36')
				INNER JOIN AGENTES.dbo.Agentes A ON A.CNPJCPF = Pr.CgcCpf
				INNER JOIN AGENTES.dbo.Visao Vi ON Vi.idAgente = A.idAgente
				INNER JOIN AGENTES.dbo.Verificacao as ver on ver.idVerificacao = '144'
				INNER JOIN SAC.dbo.PreProjeto PP ON PP.idPreProjeto = Pr.idProjeto
				INNER JOIN AGENTES.dbo.Nomes N ON N.idAgente = A.idAgente
				INNER JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf		";

        if (!empty($idpronac)) {
            $sql.= "where Pr.IdPRONAC = '". $idpronac ."'";
        }
        if (!empty($cpf)) {
            $sql.= "where Pr.CgcCpf = '". $cpf ."'";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } // fecha m�todo buscaprojetosaprovados()



    public static function buscaprojetosnaoaprovados($idpronac = null, $cpf = null)
    {
        $sql = "SELECT
				Pr.AnoProjeto+Pr.Sequencial as nrpronac, Pr.NomeProjeto as nmprojeto,
				Pr.IdPRONAC, a.CNPJCPF,
				St.descricao as situacao,
				CASE WHEN N.Descricao IS NULL
				THEN I.Nome
				ELSE N.Descricao
				END AS nmproponente
				FROM SAC.dbo.Projetos Pr
				INNER JOIN SAC.dbo.Situacao St ON (St.Codigo = Pr.Situacao) and (St.Codigo = 'A14' or St.Codigo = 'A16' or St.Codigo = 'A17' or St.Codigo = 'A41' or St.Codigo = 'D14')
				INNER JOIN AGENTES.dbo.Agentes A ON A.CNPJCPF = Pr.CgcCpf
				INNER JOIN AGENTES.dbo.Visao Vi ON Vi.idAgente = A.idAgente
				INNER JOIN AGENTES.dbo.Verificacao as ver on ver.idVerificacao = '144'
				INNER JOIN SAC.dbo.PreProjeto PP ON PP.idPreProjeto = Pr.idProjeto
				INNER JOIN AGENTES.dbo.Nomes N ON N.idAgente = A.idAgente
				INNER JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf ";

        if (!empty($idpronac)) {
            $sql.= "where Pr.IdPRONAC = '". $idpronac ."'";
        }
        if (!empty($cpf)) {
            $sql.= "where Pr.CgcCpf = '". $cpf ."'";
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } // fecha m�todo buscaprojetosnaoaprovados()



    public function reintegrarecursoorc($idrecurso, $idplanilhaaprovacao, $justificativa)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = "INSERT INTO SAC.dbo.tbRecursoXPlanilhaAprovacao (idRecurso, idPlanilhaAprovacao, stRecursoAprovacao, dsJustificativa)
     VALUES ('$idrecurso', '$idplanilhaaprovacao', 'N', '$justificativa')";

        $resultado = $db->query($sql);
        return $resultado;
    } // fecha m�todo reintegrarecursoorc()




    public function projetonaoaprovado($idpronac, $recurso, $idagente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = "INSERT INTO SAC.dbo.tbRecurso (IdPRONAC, dtSolicitacaoRecurso, dsSolicitacaoRecurso, idAgenteSolicitante, stAtendimento, tpSolicitacao)
				VALUES ('$idpronac', GETDATE(), '$recurso', '$idagente', 'E', 'NA')";

        $resultado = $db->query($sql);
        return $resultado;
    }




    public function projetoaprovado($idpronac, $recurso, $idagente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = "INSERT INTO SAC.dbo.tbRecurso (IdPRONAC, dtSolicitacaoRecurso, dsSolicitacaoRecurso, idAgenteSolicitante, stAtendimento, tpSolicitacao)
				VALUES ('$idpronac', GETDATE(), '$recurso', '$idagente', 'E', 'AE')";

        $resultado = $db->query($sql);
        return $resultado;
    }

    public function planilhaproposta($idplanilha)
    {
        $sql = "SELECT     (tbPlanilhaProposta.Quantidade * tbPlanilhaProposta.Ocorrencia * tbPlanilhaProposta.ValorUnitario) AS Total, tbPlanilhaItens.Descricao AS Itens,
                      Produto.Descricao AS Produto, tbPlanilhaEtapa.Descricao AS Etapa
FROM         tbPlanilhaProposta INNER JOIN
                      tbPlanilhaItens ON tbPlanilhaProposta.idPlanilhaItem = tbPlanilhaItens.idPlanilhaItens INNER JOIN
                      tbPlanilhaEtapa ON tbPlanilhaProposta.idEtapa = tbPlanilhaEtapa.idPlanilhaEtapa INNER JOIN
                      Produto ON tbPlanilhaProposta.idProduto = Produto.Codigo";


        if (!empty($idplanilha)) {
            $sql.= " AND tbPlanilhaProposta.idPlanilhaItem = $idplanilha";
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $resultado = $db->fetchAll($sql);
            return $resultado;
        }
    }

    public function planilhaorcamento()
    {
    }

    /**
     * M�todo para recuperar os projetos em an�lise. (CONSELHEIRO)
     * S� efetua a busca se as fontes de recursos estiverem de acordo com o C�digo 109 � Incentivo Fiscal Federal,
     * conforme Lei 8.313 de 1991.
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function analiseDeConta($idPronac = null, $idProduto = null, $idEtapa = null, $idUF = null, $idCidade = null, $buscarPorProduto = null)
    {
        $sql = "SELECT DISTINCT I.idPlanilhaItens AS idPlanilhaItens,
					RPA.dsJustificativa as justificativa
					,Rec.idRecurso
                    ,PRO.AnoProjeto+Sequencial as pronac
					,PAP.vlUnitario AS valorUnitario_con
					,PAP.idPlanilhaAprovacao
					,PAP.IdPRONAC
					,PAP.idProduto
					,PRO.NomeProjeto
					,PD.Descricao AS Produto
					,I.Descricao AS Item
					,(PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) AS VlSolicitado
					,((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario)) AS VlReduzidoParecerista
					,(PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) AS VlSugeridoParecerista
					,PPJ.Justificativa dsJustificativaParecerista
					,((PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario) - (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario)) AS VlReduzidoConselheiro
					,(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro
					,PAP.dsJustificativa dsJustificativaConselheiro

				FROM SAC.dbo.Projetos PRO
					,SAC.dbo.tbPlanilhaProjeto PPJ
					 INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					 INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
					 left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)
					 LEFT JOIN SAC.dbo.tbRecursoXPlanilhaAprovacao RPA on RPA.idPlanilhaAprovacao = PAP.idPlanilhaAprovacao
					 LEFT JOIN SAC.dbo.tbRecurso Rec on Rec.idRecurso = RPA.idRecurso

				WHERE PAP.IdPRONAC = PRO.IdPRONAC
					AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND PP.FonteRecurso = 109
					AND PAP.stAtivo = 'S'";

        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }

        if (!empty($idRecurso)) {
            $sql.= " AND Rec.idRecurso = $idRecurso";
        }
        // busca de acordo com o produto
        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND PAP.idProduto = $idProduto";
        }
        // busca de acordo com a etapa
        if (!empty($idEtapa)) {
            $sql.= " AND PAP.idEtapa = $idEtapa";
        }
        // busca de acordo com a uf
        if (!empty($idUF)) {
            $sql.= " AND PAP.idUFDespesa = $idUF";
        }
        // busca de acordo com a cidade
        if (!empty($idCidade)) {
            $sql.= " AND PAP.idMunicipioDespesa = $idCidade";
        }

        $sql.= " ORDER BY I.Descricao ASC";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } // fecha m�todo analiseDeConta()

    public static function analiseDeCustosBuscarProduto($idPronac)
    {
        $sql = "SELECT DISTINCT PD.Descricao,
					CASE
						WHEN PAP.idProduto = 0
							THEN 'Administra��o do Projeto'
							ELSE PD.Descricao
						END AS Produto
					,PAP.IdPRONAC
					,PAP.idProduto

				FROM SAC.dbo.Projetos PRO
					,SAC.dbo.tbPlanilhaProjeto PPJ
					 INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					 INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
					 left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)

				WHERE PAP.IdPRONAC = PRO.IdPRONAC
					AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND PP.FonteRecurso = 109
					AND PAP.tpPlanilha = 'CO' ";

        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }

        $sql.= " ORDER BY PD.Descricao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    } // fecha m�todo analiseDeCustosBuscarProduto()

    /**
     * M�todo que busca as etapas dos projetos da an�lise de custos
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function analiseDeCustosBuscarEtapa($idPronac, $idProduto = null, $buscarPorProduto = null)
    {
        $sql = "SELECT DISTINCT PP.idEtapa AS idEtapa
					,PAP.IdPRONAC
					,PAP.idProduto

				FROM SAC.dbo.Projetos PRO
					,SAC.dbo.tbPlanilhaProjeto PPJ
					 INNER JOIN SAC.dbo.tbPlanilhaProposta PP on (PPJ.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens I on (PPJ.idPlanilhaItem = I.idPlanilhaItens)
					 INNER JOIN SAC.dbo.tbPlanilhaAprovacao PAP on (PAP.idPlanilhaProposta = PP.idPlanilhaProposta)
					 INNER JOIN SAC.dbo.tbPlanilhaItens PIT on (PAP.idPlanilhaItem = PIT.idPlanilhaItens)
					 left join SAC.dbo.Produto PD on (PAP.idProduto = PD.Codigo)

				WHERE PAP.IdPRONAC = PRO.IdPRONAC
					AND (PPJ.Quantidade * PPJ.Ocorrencia * PPJ.ValorUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND (PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) <> (PP.Quantidade * PP.Ocorrencia * PP.ValorUnitario)
					AND PP.FonteRecurso = 109
					AND PAP.tpPlanilha = 'CO' ";

        // busca de acordo com o pronac
        if (!empty($idPronac)) {
            $sql.= " AND PAP.IdPRONAC = $idPronac";
        }
        if (!empty($idProduto) || $buscarPorProduto == true) {
            $sql.= " AND PAP.idProduto = $idProduto";
        }

        $sql.= " ORDER BY PP.idEtapa";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }
}
