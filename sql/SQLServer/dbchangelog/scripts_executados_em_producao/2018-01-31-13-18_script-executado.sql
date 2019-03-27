CREATE TABLE sugestao_enquadramento
(
    id_sugestao_enquadramento INT PRIMARY KEY NOT NULL IDENTITY,
    id_orgao INT NOT NULL,
    id_perfil_usuario INT NOT NULL,
    id_usuario_avaliador INT NOT NULL,
    id_area VARCHAR(4),
    id_segmento VARCHAR(4),
    id_preprojeto INT NOT NULL,
    descricao_motivacao VARCHAR(8000),
    data_avaliacao DATETIME,
    CONSTRAINT sugestao_enquadramento_Area_Codigo_fk FOREIGN KEY (id_area) REFERENCES Area (Codigo),
    CONSTRAINT sugestao_enquadramento_Segmento_Codigo_fk FOREIGN KEY (id_segmento) REFERENCES Segmento (Codigo),
    CONSTRAINT sugestao_enquadramento_id_proposta_cultural_fk FOREIGN KEY (id_preprojeto) REFERENCES PreProjeto (idPreProjeto)
);

-- ===========================================================

CREATE TABLE distribuicao_avaliacao_proposta
(
    id_distribuicao_avaliacao_proposta INT PRIMARY KEY NOT NULL IDENTITY,
    id_preprojeto INT NOT NULL,
    id_orgao_superior INT NOT NULL,
    id_perfil INT NOT NULL,
    data_distribuicao DATETIME NOT NULL,
    avaliacao_atual BIT DEFAULT 0 NOT NULL,
    CONSTRAINT distribuicao_avaliacao_proposta_PreProjeto_idPreProjeto_fk FOREIGN KEY (id_preprojeto) REFERENCES PreProjeto (idPreProjeto)
);

/*
  avaliacao_atual
  0 - Inativa
  1 - Ativa
*/

-- ===========================================================

UPDATE sac.dbo.tbItensPlanilhaProduto
SET idPlanilhaEtapa=10
WHERE idProduto=0 AND idPlanilhaEtapa=8 AND idPlanilhaItens=5249;

-- ===========================================================

INSERT INTO sac.dbo.tbPlanilhaEtapa (Descricao,tpCusto,stEstado,tpGrupo,nrOrdenacao)
VALUES ('Remunera��o de Capta��o de Recursos','A',1,'D',7);

-- ===========================================================

CREATE TABLE sac.dbo.PreProjetoArquivado (
       idPreProjetoArquivado int NOT NULL IDENTITY(1,1)
       idPreProjeto int NOT NULL,
       MotivoArquivamento varchar(MAX),
       SolicitacaoDesarquivamento varchar(MAX),
       Avaliacao varchar(MAX),
       idAvaliador int NOT NULL,
       dtArquivamento datetime,
       dtSolicitacaoDesarquivamento datetime,
       dtAvaliacao datetime,
       stEstado int,
       stDecisao int
);

-- ===========================================================

ALTER TABLE sac.dbo.tbAvaliacaoProposta ADD idPerfil int DEFAULT NULL;

-- ===========================================================

ALTER TABLE SAC.dbo.PreProjeto ADD DescricaoAtividade varchar(max);

-- ===========================================================

DROP TRIGGER  trAvaliacaoProposta_Insert;

CREATE  TRIGGER trAvaliacaoProposta_Insert ON dbo.tbAvaliacaoProposta
	INSTEAD OF INSERT

AS
-- ====================================================================================
-- Autor: Rômulo Menhô Barbosa
-- Data de Criação: 02/10/2008
-- Descrição: Inserir a avaliação documental da proposta cultural.
-- Data de Alteração: 24/07/2009
-- Motivo : Setar o Estado da tabela tbAvaliacaoProposta para 0 na inclusão de registro.
-- Data de Alteração: 01/04/2011
-- Motivo : Não enviar email quando se tratar de proposta de edital.
-- Data de Alteração: 30/06/2015
-- Motivo : Enviar email com protocolo de recebimento da proposta nos casos de editais.
-- Data de Alteração: 10/08/2015
-- Motivo : Adptar a nova funcionalidade de edital (Habilitar e Inabilitar proposta)
-- ====================================================================================
SET NOCOUNT ON

DECLARE @Erro                INT
DECLARE @Rows                INT
DECLARE @idProjeto           INT
DECLARE @idTecnico           INT
DECLARE @DtEnvio             DATETIME
DECLARE @idAvaliacaoProposta INT
DECLARE @NomeProposta        VARCHAR(200)
DECLARE @Destinatario        VARCHAR(100)
DECLARE @Email               VARCHAR(50)
DECLARE @DtEmail             DATETIME
DECLARE @Mensagem            NVARCHAR(MAX)
DECLARE @ConformidadeOK      TINYINT
DECLARE @Conformidade9       BIT
DECLARE @tableHTML           NVARCHAR(MAX)
DECLARE @Retorno             INT
DECLARE @idAgente            INT
DECLARE @Movimentacao        INT
DECLARE @idEdital            INT
DECLARE @nmArquivo           VARCHAR(255)
DECLARE @sgExtensao          VARCHAR(5)
DECLARE @nrTamanho           VARCHAR(10)
DECLARE @dtCadastro          VARCHAR(10)
DECLARE @Documentos          NVARCHAR(MAX)
DECLARE @nmEdital            VARCHAR(255)
DECLARE @stEstadoAvaliacao   BIT

--================================================================================================================
-- INSERIR OS DADOS DA AVALIAÇÃO DA PROPOSTA
--================================================================================================================
SELECT @idProjeto = IdProjeto,@DtEnvio = DtEnvio,@ConformidadeOK = ConformidadeOK, @stEstadoAvaliacao = stEstado
       FROM Inserted

SELECT @idEdital = idEdital FROM PreProjeto WHERE idPreProjeto = @IdProjeto

--================================================================================================================
-- PEGAR O TÉCNICO QUE FEZ A ANÁLISE INICIAL AVALIAÇÃO DA PROPOSTA
--================================================================================================================
SELECT @idTecnico = idTecnico FROM tbAvaliacaoProposta WHERE idProjeto = @IdProjeto AND ConformidadeOK = 9
--================================================================================================================
-- PEGAR O CODIGO DA MOVIMENTAÇÃO ATUAL
--================================================================================================================
SELECT @Movimentacao = Movimentacao FROM tbMovimentacao WHERE idProjeto = @IdProjeto AND stEstado = 0
--================================================================================================================
-- GRAVAR HISTÓRICO DE AVALIAÇÃO
--================================================================================================================
IF EXISTS (SELECT * FROM tbAvaliacaoProposta WHERE idProjeto = @IdProjeto)
   BEGIN
     UPDATE tbAvaliacaoProposta
            SET stEstado = 1
            WHERE idProjeto = @IdProjeto AND stEstado = 0

     SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

     IF @Erro <> 0
        BEGIN
          RAISERROR('1. Erro ao ALTERAR registros %d na tabela tbAvaliacaoProposta, transação cancelada.',16,1,@Erro)
          ROLLBACK
          RETURN
        END

     IF @Rows > 1
        BEGIN
          RAISERROR('2. Não é permitido ALTERAR %d registros ao mesmo tempo na tabela tbAvaliacaoProposta, transação cancelada',16,1,@Rows)
          ROLLBACK
          RETURN
        END
     SET @Conformidade9 = 0 -- ENVIAR EMAIL PORQUE JÁ HOUVE AVALIAÇÃO INCENTIVO FISCAL FEDERAL.
     SET @DtEnvio = NULL
   END
ELSE
  BEGIN
    SET @Conformidade9 = 1 -- NÃO ENVIAR EMAIL PORQUE É O PRIMEIRO ENVIO AO MINC.
  END
--==========================================================================================================
-- INSERIR INFORMAÇÕES NO TABELA tbAvaliacaoProposta
--==========================================================================================================
INSERT INTO tbAvaliacaoProposta
           (idProjeto,idTecnico,DtEnvio ,DtAvaliacao,Avaliacao,ConformidadeOK,stEstado, idPerfil)
     SELECT idProjeto,idTecnico,@DtEnvio,getdate()  ,Avaliacao,ConformidadeOK,0, idPerfil        FROM Inserted

SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

IF @Erro <> 0
   BEGIN
     RAISERROR('3. Erro ao inserir registros %d na tabela tbAvaliacaoProposta, transação cancelada.',16,1,@Erro)
     ROLLBACK
     RETURN
   END

IF @Rows <> 1
   BEGIN
     RAISERROR('4. Não é permitido inserir %d registros ao mesmo tempo na tabela tbAvaliacaoProposta, transação cancelada',16,1,@Rows)
     ROLLBACK
     RETURN
   END

--================================================================================================================
-- PEGAR O ID DA AVALIAÇÃO DA PROPOSTA
--================================================================================================================
SELECT @idAvaliacaoProposta=@@IDENTITY
--===============================================================================================================
-- INSERIR DADOS NA TABELA tbMovimentacao
--===============================================================================================================
IF @Conformidade9 = 0
   BEGIN
     IF @idEdital IS NULL  -- INCENTIVO FISCAL FEDERAL
        BEGIN
          IF @ConformidadeOK = 1
	         BEGIN
               SET @Movimentacao = 97
		     END
		  ELSE
		  	 BEGIN
               SET @Movimentacao = 95
		     END
		END
     ELSE --EDITAL
	    BEGIN
          IF @Movimentacao = 96
	         BEGIN
               SET @Movimentacao = 127
    	     END
          ELSE
          IF @Movimentacao = 127 --EDITAL
	         BEGIN
               SET @Movimentacao = 128
    	     END
        END

    INSERT INTO tbMovimentacao
                (idProjeto,Movimentacao,DtMovimentacao,stEstado,Usuario)
         VALUES (@IdProjeto,@Movimentacao,getdate(),0,@idTecnico)

     SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

     IF @Erro <> 0
        BEGIN
          RAISERROR('5. Erro ao inserir registros %d na tabela tbMovimentacao, transação cancelada.',16,1,@Erro)
          ROLLBACK
          RETURN
        END

     IF @Rows <> 1
        BEGIN
          RAISERROR('6. Não é permitido inserir %d registros ao mesmo tempo na tabela tbMovimentacao, transação cancelada',16,1,@Rows)
          ROLLBACK
          RETURN
        END
   END
--================================================================================================================
-- PREPARAR INFORMAÇÕES PARA ENVIAR EMAIL - INCENTIVO FISCAL FEDERAL
--================================================================================================================
SELECT @NomeProposta = p.NomeProjeto,
       @Destinatario = agentes.dbo.fnNome(p.idAgente),
	   @DtEmail      = a.DtAvaliacao,
	   @Mensagem     = a.Avaliacao,
	   @idAgente     = p.idAgente
       FROM PreProjeto p
       INNER JOIN tbMovimentacao m on (p.idPreProjeto = m.idProjeto)
       INNER JOIN Inserted       a on (p.idPreProjeto = a.idProjeto)
       WHERE m.stEstado = 0 and p.idPreProjeto = a.idProjeto
       --SELECT @idAvaliacaoProposta,@idProjeto,@NomeProposta,@Destinatario,@Email,@DtEmail,@Mensagem,@ConformidadeOK,@idTecnico
--===============================================================================================================
-- ENVIAR EMAIL
--===============================================================================================================
IF @idEdital IS NULL AND @Conformidade9 = 0
   BEGIN
     --===============================================================================================================
     -- INCENTIVO FISCAL FEDERAL
     --===============================================================================================================
     DECLARE MyCursor CURSOR FOR
       SELECT Descricao FROM agentes.dbo.Internet i
                        INNER JOIN PreProjeto p on (i.idAgente = p.idAgente)
                        WHERE p.idPreProjeto = @idProjeto  and i.idAgente=p.idAgente and Status = 1

     OPEN MyCursor

     SET @Mensagem = '<b>Proposta: ' + CONVERT(VARCHAR(10),@idProjeto) + ' - ' + @NomeProposta + '<br> Proponente: ' + @Destinatario + '<br> </b>' + @Mensagem

     WHILE @@FETCH_STATUS = @@FETCH_STATUS
       BEGIN
         FETCH NEXT FROM MyCursor INTO @Email
         IF (@@FETCH_STATUS = -2)          CONTINUE
         IF (@@FETCH_STATUS = -1)          BREAK

         EXEC msdb.dbo.sp_send_dbmail   @profile_name = 'PerfilGrupoPRONAC',
                                           @recipients = @Email,
                                           @body = @Mensagem,
                                        @body_format = 'HTML',
                                           @subject = 'Avaliação da proposta',
                                        @exclude_query_output = 1;

       END
     CLOSE MyCursor
     DEALLOCATE MyCursor
	 --=====================================================================================================================
     -- GRAVAR INFORMAÇÕES DO EMAIL ENVIADO
     --=====================================================================================================================
     INSERT INTO tbHistoricoEmail
                (idProjeto,idTextoemail,iDAvaliacaoProposta,DtEmail,stEstado,idUsuario)
         VALUES( @idProjeto,NULL,@iDAvaliacaoProposta,getdate(),1,@idTecnico)
     SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

     IF @Erro <> 0
        BEGIN
          RAISERROR('7. Erro ao inserir registros %d na tabela tbHistoricoEmail, transação cancelada.',16,1,@Erro)
          ROLLBACK
          RETURN
        END

     IF @Rows <> 1
        BEGIN
          RAISERROR('8. Não é permitido inserir %d registros ao mesmo tempo na tabela tbHistoricoEmail, transação cancelada',16,1,@Rows)
          ROLLBACK
          RETURN
        END
   END
ELSE
IF @idEdital IS NOT NULL AND @ConformidadeOK = 9 AND @stEstadoAvaliacao = 0
   BEGIN
     --=====================================================================================================================
     -- EDITAL - ENVIAR EMAIL DE CONFIRMAÇÃO AO PROPONENTE
     --=====================================================================================================================
     IF NOT EXISTS (SELECT TOP 1 * FROM tbHistoricoEmail WHERE idProjeto = @idProjeto and idTextoEmail = 21 and
                                                              (CONVERT(char(10),(DtEmail),111) = CONVERT(char(10),getdate(),111)))
        BEGIN
          SELECT @Mensagem = dsTexto FROM tbTextoEmail WHERE idTextoEmail = 21
          --=====================================================================================================================
          -- EDITAL - CARREGAR INFORMAÇÕES DOS DOCUMENTOS ANEXADOS
          --=====================================================================================================================

		  DECLARE MyCursorDoc CURSOR FOR
 	         SELECT d.nmFormDocumento,c.nmArquivo,c.sgExtensao,c.nrTamanho, CONVERT(CHAR(10),c.dtEnvio,103)
                 FROM sac.dbo.PreProjeto                             a
	             INNER JOIN BDCORPORATIVO.scSAC.tbPreProjetoXArquivo b on (a.idPreProjeto = b.idProjeto)
	             INNER JOIN BDCORPORATIVO.scCorp.tbArquivo           c on (b.idArquivo    = c.idArquivo)
	             INNER JOIN BDCORPORATIVO.scQuiz.tbFormDocumento     d on (a.idEdital     = d.idEdital)
                 WHERE  a.idPreProjeto = @idProjeto
                        AND d.idClassificaDocumento not in (23,24,25)
          SET @Documentos =''
		  OPEN MyCursorDoc
          WHILE @@FETCH_STATUS = @@FETCH_STATUS
            BEGIN
              FETCH NEXT FROM MyCursorDoc INTO @nmEdital,@nmArquivo,@sgExtensao,@nrTamanho,@dtCadastro
              IF (@@FETCH_STATUS = -2)          CONTINUE
              IF (@@FETCH_STATUS = -1)          BREAK

			  SET @Documentos =  @Documentos + '<b>Nome do Arquivo: ' + @nmArquivo +  '</b><br>Extensão: ' + @sgExtensao + '<br>Tamanho: ' + @nrTamanho + '<br>Data de Envio: ' + @dtCadastro + '<br><br>'
            END
          CLOSE MyCursorDoc
          DEALLOCATE MyCursorDoc
          --=====================================================================================================================
          -- EDITAL - ENVIAR EMAILS
          --=====================================================================================================================
           DECLARE MyCursor CURSOR FOR
             SELECT Descricao FROM agentes.dbo.Internet i
                              INNER JOIN PreProjeto p on (i.idAgente = p.idAgente)
                              WHERE p.idPreProjeto = @idProjeto  and i.idAgente=p.idAgente and Status = 1

          OPEN MyCursor

          SET @Mensagem = '<b>Proposta: ' + CONVERT(VARCHAR(10),@idProjeto) + ' - ' + @NomeProposta + '<br><br> Proponente: ' + @Destinatario + '<br><br> </b>' + @Mensagem + '<p><b>Edital: ' + @nmEdital  + '</b><p>'

		  SET @Mensagem = @Mensagem + @Documentos
          WHILE @@FETCH_STATUS = @@FETCH_STATUS
            BEGIN
              FETCH NEXT FROM MyCursor INTO @Email
              IF (@@FETCH_STATUS = -2)          CONTINUE
              IF (@@FETCH_STATUS = -1)          BREAK

              EXEC msdb.dbo.sp_send_dbmail @profile_name = 'PerfilGrupoPRONAC',
                                           @recipients = @Email,
                                           @body = @Mensagem,
                                           @body_format = 'HTML',
                                           @subject = 'EDITAL - Confirmação do recebimento no Ministério da Cultura',
                                           @exclude_query_output = 1;

            END
          CLOSE MyCursor
          DEALLOCATE MyCursor
	      --=====================================================================================================================
          -- GRAVAR INFORMAÇÕES DO EMAIL ENVIADO
          --=====================================================================================================================
          INSERT INTO tbHistoricoEmail
                     (idProjeto,idTextoemail,iDAvaliacaoProposta,DtEmail,stEstado,idUsuario)
              VALUES (@idProjeto,21,NULL,getdate(),1,0)

	      SELECT @Rows = @@ROWCOUNT, @Erro = @@ERROR

          IF @Erro <> 0
             BEGIN
               RAISERROR('7. Erro ao inserir registros %d na tabela tbHistoricoEmail, transação cancelada.',16,1,@Erro)
               ROLLBACK
               RETURN
             END

          IF @Rows <> 1
             BEGIN
               RAISERROR('8. Não é permitido inserir %d registros ao mesmo tempo na tabela tbHistoricoEmail, transação cancelada',16,1,@Rows)
               ROLLBACK
               RETURN
           END
        END
	END

-- ===========================================================

ALTER TABLE sac.dbo.PlanoDistribuicaoProduto ADD canalAberto bit DEFAULT ((0));



-------------



-- 2018-02-06-13-12_spChecklistParaApresentacaoDeProposta.sql

-- =================================================================================================================
-- Autor: Rômulo Menhô Barbosa
-- Data de Criação: 29/01/2018
-- Descrição: INSTRUÇÃO NORMATIVA 05/2017
--            Verificar se a proposta cultural do mecanismo Incentivo Fiscal Federal está complementamente preenchida
--            e as obrigatoriedades exigidas na IN estão cumpridas.
--            @FLAG = O. Prosposta sem inconsistência;
--                    1. Inconsistência na proposta;
--                    2. Tentativa de envio de proposta oa MinC fora do período permitido.
-- Wouerner: Retirando validação 31- Media no plano de distribuição
--
-- =================================================================================================================

IF OBJECT_ID ( 'dbo.spChecklistParaApresentacaoDeProposta', 'P' ) IS NOT NULL
    DROP PROCEDURE dbo.spChecklistParaApresentacaoDeProposta;
GO

CREATE PROCEDURE dbo.spChecklistParaApresentacaoDeProposta (@idProjeto int)
AS

SET NOCOUNT ON

DECLARE @Rows                      INT
DECLARE @Flag                      INT
DECLARE @idAgente                  INT
DECLARE @idUsuario                 INT
DECLARE @idMuncipioDespesa         INT
DECLARE @idUFDespesa               INT
DECLARE @tpPessoa                  BIT
DECLARE @tpDireito                 INT
DECLARE @tpEsfera                  INT
DECLARE @stProposta                INT
DECLARE @CNPJCPF                   VARCHAR(14)
DECLARE @qtProjetos                INT
DECLARE @vlAprovado                DECIMAL(18,2)
DECLARE @vlCaptado                 DECIMAL(18,2)
DECLARE @PercCaptado               DECIMAL(18,2)
DECLARE @Secretaria                INT
DECLARE @DtInicioDeExecucao        DATETIME
DECLARE @qtProdutoPrincipal        INT
DECLARE @PrecoMedio                DECIMAL(18,2)
DECLARE @CustoBeneficio            DECIMAL(18,2)
DECLARE @ReceitaPrevista           DECIMAL(18,2)
DECLARE	@PercentualReceitaPrevista DECIMAL(18,2)
DECLARE @idProduto                 INT
DECLARE @CustoDoProjeto            DECIMAL(18,2)
DECLARE @CustoTotalDoProjeto       DECIMAL(18,2)
DECLARE @CustoDoProdutoPrincipal   DECIMAL(18,2)
DECLARE @CustoDoProdutoSecundario  DECIMAL(18,2)
DECLARE @QtdeProduzida             INT
DECLARE @QtdeVenda                 INT
DECLARE @PrecoVenda                INT
DECLARE @QtdeVendaProponente       INT
DECLARE @PrecoVendaProponente      DECIMAL(18,2)
DECLARE @Segmento                  CHAR(2)
DECLARE @Regiao                    BIT

SET @Flag = 0
--============================================================================================================
-- Tabela temporária
--============================================================================================================

CREATE TABLE #Verificacao
       (
        idProjeto        INT,
		dsChamada        VARCHAR(250),
        dsInconsistencia VARCHAR(250),
        Observacao       VARCHAR(100)
       )

--============================================================================================================
-- CARREGAR VARIÁVEIS
--============================================================================================================
----------------------------------------------------------------------------------------------------------
-- CARREGAR INFORMAÇÕES DA PROPOSTA
----------------------------------------------------------------------------------------------------------
SELECT @idAgente = idAgente, @DtInicioDeExecucao = DtInicioDeExecucao,@stProposta = ISNULL(stProposta,610),
       @Secretaria = AreaAbrangencia
  FROM sac.dbo.PreProjeto
  WHERE idPreProjeto = @idProjeto

----------------------------------------------------------------------------------------------------------
-- RECUPERAR O VALOR DO INCENTIVO FISCAL FEDERAL (FONTE - 109)
----------------------------------------------------------------------------------------------------------
SELECT @CustoDoProjeto = SUM(Quantidade * Ocorrencia * ValorUnitario)
  FROM sac.dbo.tbPlanilhaProposta
 WHERE FonteRecurso = 109
       AND idProjeto = @idProjeto
----------------------------------------------------------------------------------------------------------
-- RECUPERAR O CUSTO TOTAL DO PROJETO (TODAS AS FONTES)
----------------------------------------------------------------------------------------------------------
SELECT @CustoTotalDoProjeto = SUM(Quantidade * Ocorrencia * ValorUnitario)
  FROM sac.dbo.tbPlanilhaProposta
 WHERE idProjeto = @idProjeto
----------------------------------------------------------------------------------------------------------
-- RECUPERAR O CUSTO DO PRODUTO PRINCIPAL
----------------------------------------------------------------------------------------------------------
SELECT @CustoDoProdutoPrincipal = SUM(Quantidade * Ocorrencia * ValorUnitario)
  FROM sac.dbo.tbPlanilhaProposta a
  INNER JOIN sac.dbo.PlanoDistribuicaoProduto b on (a.idProjeto = b.idProjeto)
 WHERE a.idEtapa NOT IN (8,10)
       AND b.stPrincipal = 1
	   AND a.idProduto = b.idProduto
       AND a.idProjeto = @idProjeto

----------------------------------------------------------------------------------------------------------
-- RECUPERAR O SEGMENTO CULTURAL DO PRODUTO PRINCIPAL DA PROPOSTA
----------------------------------------------------------------------------------------------------------
SELECT @Segmento = Segmento FROM sac.dbo.PlanoDistribuicaoProduto  WHERE stPrincipal = 1 AND idProjeto = @idProjeto
----------------------------------------------------------------------------------------------------------
-- RECUPERAR O CUSTO DO PRODUTO SECUNDÁRIO
----------------------------------------------------------------------------------------------------------
SELECT @CustoDoProdutoSecundario = SUM(Quantidade * Ocorrencia * ValorUnitario)
  FROM sac.dbo.tbPlanilhaProposta a
  INNER JOIN sac.dbo.PlanoDistribuicaoProduto b on (a.idProjeto = b.idProjeto)
 WHERE a.idEtapa NOT IN (8,10)
       AND b.stPrincipal = 0
	   AND a.idProduto = b.idProduto
       AND a.idProjeto = @idProjeto
----------------------------------------------------------------------------------------------------------
-- RECUPERAR INFORMAÇÕES DO PLANO DE DISTRIBUIÇÃO DE PRODUTO
----------------------------------------------------------------------------------------------------------
SELECT @QtdeProduzida        = SUM(QtdeProduzida),
       @QtdeVenda            = SUM(QtdeVendaPopularNormal + QtdeVendaPopularPromocional + QtdeVendaNormal + QtdeVendaPromocional),
       @QtdeVendaProponente  = SUM(QtdeVendaNormal + QtdeVendaPromocional),
	   @PrecoVendaProponente = SUM(PrecoUnitarioNormal + PrecoUnitarioPromocional),
       @ReceitaPrevista      = SUM(vlReceitaTotalPrevista)
  FROM sac.dbo.PlanoDistribuicaoProduto
  WHERE idProjeto = @idProjeto
----------------------------------------------------------------------------------------------------------
-- RECUPERAR REGIÃO DA EXECUÇÃO DO PROJETO
----------------------------------------------------------------------------------------------------------
IF EXISTS(SELECT * FROM sac.dbo.Abrangencia a
                   INNER JOIN Agentes.dbo.UF b on (a.idUF = b.idUF)
                   WHERE b.Regiao IN ('Sudeste','Sul')
				         AND idProjeto = @idProjeto)

   SET @Regiao = 1
ELSE
   SET @Regiao = 0 -- EXECUÇÃO NAS DEMAIS REGIÕES

----------------------------------------------------------------------------------------------------------
-- CALCULAR MÉTRICAS
----------------------------------------------------------------------------------------------------------
IF @QtdeProduzida > 0 AND @ReceitaPrevista > 0
   SET @CustoBeneficio            = @CustoDoProjeto      / @QtdeProduzida

IF @QtdeProduzida > 0 AND @ReceitaPrevista > 0
   SET @PercentualReceitaPrevista = (@ReceitaPrevista  / @CustoTotalDoProjeto)  * 100

IF @QtdeVenda > 0 AND @ReceitaPrevista > 0
   SET @PrecoMedio = @PrecoVenda / @QtdeVenda

SET @CustoDoProjeto            = ISNULL(@CustoDoProjeto,0)
SET @CustoTotalDoProjeto       = ISNULL(@CustoTotalDoProjeto,0)
SET @PrecoMedio                = ISNULL(@PrecoMedio,0)
SET @CustoBeneficio            = ISNULL(@CustoBeneficio,0)
SET @PercentualReceitaPrevista = ISNULL(@PercentualReceitaPrevista,0)
SET @ReceitaPrevista           = ISNULL(@ReceitaPrevista,0)
SET @CustoDoProdutoPrincipal   = ISNULL(@CustoDoProdutoPrincipal,0)
SET @CustoDoProdutoSecundario  = ISNULL(@CustoDoProdutoSecundario,0)
----------------------------------------------------------------------------------------------------------
-- CARREGAR INFORMAÇÕES DO AGENTE CULTURAL
----------------------------------------------------------------------------------------------------------
SELECT @CNPJCPF = CNPJCPF, @tpPessoa = TipoPessoa FROM Agentes.dbo.Agentes WHERE idAgente = @idAgente

SELECT @tpDireito = Direito, @tpEsfera = Esfera FROM Agentes.dbo.Natureza WHERE idAgente = @idAgente

----------------------------------------------------------------------------------------------------------
--REGRA DE NEGÓCIO - VERIFICAR SÓCIOS EM COMUM EM PESSOAS JURÍDICAS OU GRUPO EMPRESARIAL
----------------------------------------------------------------------------------------------------------
--EXEC spCalcularQuantidadeMontanteProjetosProponenteSocios @CNPJCPF ,@qtProjetos OUTPUT,@vlAprovado OUTPUT,
--                                                                    @vlCaptado OUTPUT,@PercCaptado OUTPUT

 SET @qtProjetos  = ISNULL(@qtProjetos,0)
 SET @vlAprovado  = ISNULL(@vlAprovado,0)
 SET @vlCaptado   = ISNULL(@vlCaptado,0)
 SET @PercCaptado = ISNULL(@PercCaptado,0)

--============================================================================================================
-- 1 VERIFICAR ONDE SE ENCONTRA A PROPOSTA
--============================================================================================================
IF EXISTS(SELECT TOP 1 * FROM sac.dbo.tbMovimentacao
                         WHERE idProjeto = @idProjeto
						       AND Movimentacao <> 95
							   AND stEstado = 0)
  BEGIN
     INSERT INTO #Verificacao
            VALUES (@idProjeto,'','<font color=blue><b>A PROPOSTA CULTURAL ENCONTRA-SE NO MINISTÉRIO DA CULTURA.</b></font>','')
     SET @Flag = 2
   END
ELSE
--============================================================================================================
-- 2 REGRA DE NEGÓCIO - 2 : VERIFICAR O PERÍODO DE ENVIO DA PROPOSTA
--============================================================================================================
IF (MONTH(GETDATE()) = 1 OR MONTH(GETDATE()) = 12)
   AND NOT EXISTS(SELECT * FROM sac.dbo.tbAvaliacaoProposta WHERE idProjeto = @IdProjeto)
   BEGIN
     INSERT INTO #Verificacao
            VALUES (@idProjeto,'','Conforme Art 2º da Instrução Normativa nº 5/2017, o período para apresentação de propostas culturais é de 1º de fevereiro até 30 de novembro de cada ano.','PENDENTE')
            SET @Flag = 1
     END
ELSE
--============================================================================================================
--VERIFICAR INCONSISTÊNCIAS
--============================================================================================================
BEGIN
  ----------------------------------------------------------------------------------------------------------
  --REGRA DE NEGÓCIO - VERIFICAR CNAE CULTURAL EM PESSOAS JURÍDICAS
  ----------------------------------------------------------------------------------------------------------
/*
  IF @tpPessoa = 1
     AND @Segmento NOT IN('4D','5A','5D','5E','5S','6I')
     AND NOT EXISTS(SELECT TOP 1 e.*
                      FROM       sac.dbo.PreProjeto                   a
                      INNER JOIN agentes.dbo.Agentes                  b on (a.idAgente     = b.idAgente)
                      INNER JOIN sac.dbo.vwPessoaJuridica_CNAE        c on (b.CNPJCPF      = c.NR_CNPJ)
				      INNER JOIN sac.dbo.vwPlanoDeDistribuicaoProduto d on (a.idPreProjeto = d.idProjeto)
                      INNER JOIN sac.dbo.tbCnaeCultural               e on (d.Area         = e.cdArea AND
                                                                            d.Segmento     = e.cdSegmento AND
					    	       					                        c.CD_CNAE      = e.cdCnae)
                      WHERE b.TipoPessoa = '1'
				            AND d.stPrincipal = 1
					        AND b.CNPJCPF = @CNPJCPF
							AND a.idpreprojeto = @idProjeto)

     BEGIN
       INSERT INTO #Verificacao
           VALUES (@idProjeto,'','<font color=blue><b>Conforme Art 7º, §3º da Instrução Normativa nº 1/2017, o proponente deverá está classificado no CNAE cultural correspondente a área e segmento cultural do proposta apresentada.</b></font>','PENDENTE')
        SET @Flag = 1
     END
*/
  --============================================================================================================
  -- 3 VERIFICAR AS INFORMAÇÕES DO PROPONENTE
  --============================================================================================================
  IF NOT EXISTS(SELECT TOP 1 a.* FROM       sac.dbo.vCadastrarProponente a
                                 INNER JOIN sac.dbo.PreProjeto           b on (a.idAgente = b.idAgente)
                                 WHERE idpreprojeto = @idProjeto
					                   AND Correspondencia = 1)
     BEGIN
       INSERT INTO #Verificacao
           VALUES (@idProjeto,'Endereco','Dados cadastrais do proponente inexistente ou não há endereço selecionado para correspondência.','PENDENTE')
       SET @Flag = 1
     END
  --------------------------------------------------------------------------------------------------------------
  -- 4 VERIFICAR A REGULARIDADE DO PROPONENTE - INADIMPLENTE
  --------------------------------------------------------------------------------------------------------------
  IF EXISTS(SELECT TOP 1 b.* FROM  agentes.dbo.Agentes      a
                 INNER JOIN sac.dbo.Projetos    b on (a.CNPJCPF  = b.CgcCpf)
                  WHERE a.CNPJCPF = @CNPJCPF
						AND b.Situacao IN ('D38','E20','E23','E66','E69','E71','E80','E81','G20','G23','H04'))

    BEGIN
      INSERT INTO #Verificacao
          VALUES (@idProjeto,'','Proponente em situação INADIMPLENTE junto ao Ministério da Cultura, conforme disposto na alínea C, inciso I do Art. 58 da Instrução Normativa nº 5/2017.','PENDENTE')
          SET @Flag = 1
    END
   --------------------------------------------------------------------------------------------------------------
  -- 5 VERIFICAR A REGULARIDADE DO PROPONENTE - INABILITADO
  --------------------------------------------------------------------------------------------------------------
  IF EXISTS(SELECT TOP 1 a.* FROM  agentes.dbo.Agentes      a
                                INNER JOIN sac.dbo.PreProjeto  b on (a.idAgente = b.idAgente)
                                INNER JOIN sac.dbo.Inabilitado c on (a.CNPJCPF  = c.CgcCpf)
                                WHERE a.CNPJCPF = c.CgcCpf
								      AND  Habilitado  = 'N'
									  AND idpreprojeto = @idProjeto)
    BEGIN
      INSERT INTO #Verificacao
          VALUES (@idProjeto,'','Proponente em situação IRREGULAR no Ministério da Cultura.','PENDENTE')
          SET @Flag = 1
    END --------------------------------------------------------------------------------------------------------------
  -- 6 VERIFICAR SE HÁ OS EMAILS DO PROPONENTE CADASTRADOS
  --------------------------------------------------------------------------------------------------------------
  IF NOT EXISTS(SELECT TOP 1 a.* FROM  Agentes.dbo.Internet    a
                                    INNER JOIN sac.dbo.PreProjeto b on (a.idAgente = b.idAgente)
                                    WHERE b.idpreprojeto = @idProjeto
								      AND a.Status = 1)
     BEGIN
       INSERT INTO #Verificacao
           VALUES (@idProjeto,'Email','E-mail do proponente inexistente.','PENDENTE')
       SET @Flag = 1
    END
  --------------------------------------------------------------------------------------------------------------
  -- 7 NO CASO DE PESSOA FÍSICA, VERIFICAR O LANÇAMENTO DA DATA DE NASCIMENTO
  --------------------------------------------------------------------------------------------------------------
  IF @tpPessoa = 0
     BEGIN
       IF NOT EXISTS(SELECT DtNascimento FROM agentes.dbo.tbAgenteFisico WHERE idagente = @idAgente)
          BEGIN
            INSERT INTO #Verificacao
                VALUES (@idProjeto,'Nascimento','Data de Nascimento inexistente.','PENDENTE')
            SET @Flag = 1
          END
     END
  --------------------------------------------------------------------------------------------------------------
  -- 8 NO CASO DE PESSOA JURÍDICA, VERIFICAR O LANÇAMENTO DA NATUREZA DO PROPONENTE
  --------------------------------------------------------------------------------------------------------------
  IF @tpPessoa = 1
     BEGIN
       IF NOT EXISTS(SELECT TOP 1 * FROM       agentes.dbo.Natureza a
                                    INNER JOIN sac.dbo.PreProjeto   b on (a.idAgente = b.idAgente)
                                    WHERE b.idpreprojeto = @idProjeto)
          BEGIN
            INSERT INTO #Verificacao
                VALUES (@idProjeto,'Natureza','Natureza do proponente.','PENDENTE')
            SET @Flag = 1
          END
       ----------------------------------------------------------------------------------------------------------
       -- 9 VERIFICAR SE HÁ DIRIGENTE CADASTRADO
       ----------------------------------------------------------------------------------------------------------
       IF NOT EXISTS(SELECT TOP 1 a.*
                       FROM Agentes.dbo.Agentes as a
                       INNER JOIN Agentes.dbo.Vinculacao as b on (a.idAgente = b.idAgente)
                       INNER JOIN Agentes.dbo.Visao      as c on (a.idAgente = c.idAgente)
                       WHERE a.TipoPessoa = 0
	                         AND c.Visao = 198
		                     AND b.idVinculoPrincipal = @idAgente)
          BEGIN
            INSERT INTO #Verificacao
                VALUES (@idProjeto,'Dirigente','Cadastro de Dirigente.','PENDENTE')
            SET @Flag = 1
          END
     END
  -------------------------------------------------------------------------------------------------------------
  -- 10 REGRA DE NEGÓCIO - DOS PLANOS ANUAIS - VERIFICAR SE A ENTIDADE É SEM FINS LUCRATIVOS
  -------------------------------------------------------------------------------------------------------------
  IF @stProposta = 614 AND (@tpDireito = 2 OR (@tpDireito = 1 AND @tpEsfera = 5))
     BEGIN
       INSERT INTO #Verificacao
           VALUES (@idProjeto,'','Conforme Art 3 da Instrução Normativa nº 5/2017, somente entidades sem fins lucrativos poderão apresentar projetos de planos anuais e plurianuais de atividades','PENDENTE')
       SET @Flag = 1
     END
  -------------------------------------------------------------------------------------------------------------
  -- 11 REGRA DE NEGÓCIO - DO PRINCÍPIO DA NÃO CONCENTRAÇÃO
  -------------------------------------------------------------------------------------------------------------
  IF @tpPessoa = 1
     BEGIN
	   IF @tpDireito = 2 AND @stProposta IN (610,618,619)
          BEGIN
	        IF @qtProjetos > 10 OR (@Regiao = 0 AND @qtProjetos > 15)
	            ---------------------------------------------------------------------------------------------------
                --12 REGRA DE NEGÓCIO - LIMITE DE QUANTITATIVO DE PROJETOS ATIVOS NO SALIC
                ---------------------------------------------------------------------------------------------------
               BEGIN
                 INSERT INTO #Verificacao
                     VALUES (@idProjeto,'','Conforme Art 20 da Instrução Normativa nº 1, o proponente atingiu o limite máximo 10 (dez) de projetos ativos no SALIC','PENDENTE')
                 SET @Flag = 1
               END
	        IF @vlAprovado > 40000000 AND @Secretaria = 0 AND @stProposta IN (610,618,619)
		       ---------------------------------------------------------------------------------------------------
               --13 REGRA DE NEGÓCIO - 10.2.3 - LIMITE DO MONTANTE DE PROJETOS ATIVOS NO SALIC
		       ---                 - 10.5, 10.5.1, 10.5.2, 10.5.3, 10.5.4, 10.5.5, 10.5.6, 10.7
		       ---------------------------------------------------------------------------------------------------
               BEGIN
                 INSERT INTO #Verificacao
                     VALUES (@idProjeto,'','Conforme Art 20 da Instrução Normativa nº 1, o proponente atingiu o limite máximo de R$ 40 milhões em projetos ativos no SALIC','PENDENTE')
                 SET @Flag = 1
               END
	        IF ((@Regiao = 0 AND @CustoDoProjeto > 15000000) OR (@Regiao = 1 AND @CustoDoProjeto > 10000000))
	            AND (@Secretaria = 0 AND @stProposta IN (610,618,619))
	        -- IF (@CustoDoProjeto > 10000000 OR (@Regiao = 0 AND @CustoDoProjeto > 15000000))
	        --   AND @Secretaria = 0 AND @stProposta IN (610,618,619)
		       ---------------------------------------------------------------------------------------------------
               -- 14 REGRA DE NEGÓCIO - 10.2.3 - LIMITE DO MONTANTE DE PROJETOS ATIVOS NO SALIC
		       ---                 - 10.5, 10.5.1, 10.5.2, 10.5.3, 10.5.4, 10.5.5, 10.5.6, 10.7
		       ---------------------------------------------------------------------------------------------------
               BEGIN
                 INSERT INTO #Verificacao
                     VALUES (@idProjeto,'','Conforme Art 20 da Instrução Normativa nº 1, o proponente atingiu o limite máximo de R$ 10 milhões por projetos.','PENDENTE')
                 SET @Flag = 1
               END
          END
	 END
  ELSE
     BEGIN
	   IF @qtProjetos > 4 --OR (@Regiao = 0 AND @qtProjetos > 6)
	      ---------------------------------------------------------------------------------------------------
          --15 REGRA DE NEGÓCIO - LIMITE DE QUANTITATIVO DE PROJETOS ATIVOS NO SALIC
          ---------------------------------------------------------------------------------------------------
          BEGIN
            INSERT INTO #Verificacao
                 VALUES (@idProjeto,'','Conforme Art 4 da Instrução Normativa nº 5/2017, o proponente atingiu o limite máximo de 4 (quatro) projetos ativos no SALIC','PENDENTE')
            SET @Flag = 1
          END
       IF @vlAprovado > 1500000
          BEGIN
		    ---------------------------------------------------------------------------------------------------
            --16 REGRA DE NEGÓCIO - LIMITE DO MONTANTE DE PROJETOS ATIVOS NO SALIC
		    ---------------------------------------------------------------------------------------------------
            INSERT INTO #Verificacao
                VALUES (@idProjeto,'','Conforme Art 4º da Instrução Normativa nº 5/2017, o proponente atingiu o limite máximo deR$ 1.500.000,00 em projetos ativos no SALIC','PENDENTE')
            SET @Flag = 1
          END
       IF @CustoDoProjeto > 700000 OR (@Regiao = 0 AND @CustoDoProjeto > 1050000)
          BEGIN
		    ---------------------------------------------------------------------------------------------------
            -- 17 REGRA DE NEGÓCIO - 10.2.1 - LIMITE POR PROJETOS
		    ---------------------------------------------------------------------------------------------------
            INSERT INTO #Verificacao
                VALUES (@idProjeto,'','Conforme Art 20 da Instrução Normativa nº 1, o proponente atingiu o limite máximo de R$ 700.000,00 por projetos.','PENDENTE')
            SET @Flag = 1
          END
	 END
  --============================================================================================================
  -- 20 REGRA DE NEGÓCIO - VERIFICAR SE EXISTE NO MÍNIMO 90 DIAS ENTRE A DATA DE ENVIO E O INÍCIO DO PERÍODO
  --                    DE EXECUÇÃO DO PROJETO
  --============================================================================================================
  IF NOT EXISTS(SELECT TOP 1 * FROM sac.dbo.tbMovimentacao a WHERE a.Movimentacao <> 95)
	 BEGIN
	   IF DATEDIFF(DAY,GETDATE(),@DtInicioDeExecucao) < 90
          BEGIN
            INSERT INTO #Verificacao
               VALUES (@idProjeto,'PeriodoExecucao','Conforme Art 2º da Instrução Normativa nº 5/2017, não serão admitidas propostas culturais apresentadas em prazo inferior a 90 (noventa) dias da data prevista para o início de sua pré-produção.','PENDENTE')
            SET @Flag = 1
          END
     END
  --============================================================================================================
  -- 21 VERIFICAR SE O LOCAL DE REALIZAÇÃO ESTÁ CADASTRADO
  --============================================================================================================
  IF NOT EXISTS(SELECT TOP 1 * FROM Abrangencia WHERE idProjeto = @idProjeto)
     BEGIN
          INSERT INTO #Verificacao
                 VALUES (@idProjeto,'LocalRealizacao','O Local de realização da proposta não foi preenchido.','PENDENTE')
          SET @Flag = 1
     END
  --============================================================================================================
  -- 22 VERIFICAR SE O PLANO DE DISTRIBUIÇÃO DO PRODUTO ESTÁ PREENCHIDO
  --============================================================================================================
  IF NOT EXISTS(SELECT * FROM PlanoDistribuicaoProduto WHERE idProjeto = @idProjeto)
     BEGIN
       INSERT INTO #Verificacao
           VALUES (@idProjeto,'PlanoDistribuicao','O Plano Distribuição de Produto não foi preenchido.','PENDENTE')
       SET @Flag = 1
     END
  --============================================================================================================
  -- 23 VERIFICAR A EXISTÊNCIA DO PRODUTO PRINCIPAL NA PROPOSTA
  --============================================================================================================
  SELECT @qtProdutoPrincipal = ISNULL(COUNT(*),0) FROM PlanoDistribuicaoProduto
	                                              WHERE idProjeto = @idProjeto
	    										        AND stPrincipal = 1

  IF @qtProdutoPrincipal = 0
     BEGIN
       INSERT INTO #Verificacao
            VALUES (@idProjeto,'PlanoDistribuicao','Não há produto principal selecionado na proposta.','PENDENTE')
       SET @Flag = 1
     END

  IF @qtProdutoPrincipal > 1
     BEGIN
       INSERT INTO #Verificacao
            VALUES (@idProjeto,'PlanoDistribuicao','Só é permitido um produto principal por proposta, a sua está com mais de um produto principal cadastrado.','PENDENTE')
       SET @Flag = 1
     END
  --============================================================================================================
  -- 24 VERIFICAR SE EXISTE NA PLANILHA ORÇAMENTÁRIA ITENS DA FONTE INCENTIVO FISCAL FEDERAL.
  --============================================================================================================
  IF NOT EXISTS(SELECT TOP 1 * FROM sac.dbo.tbPlanilhaProposta WHERE FonteRecurso = 109 and idProjeto = @idProjeto)
     BEGIN
       INSERT INTO #Verificacao
            VALUES (@idProjeto,'Planilha','Não existe item orçamentário referente a fonte de recurso - Incentivo Fiscal Federal.','PENDENTE')
       SET @Flag = 1
     END
  --============================================================================================================
  -- 25 VERIFICAR SE EXISTE NA PLANILHA ORÇAMENTÁRIA ITENS DA ETAPA Assessoria Contábil e Juridica - CONTADOR
  --============================================================================================================
  IF NOT EXISTS(SELECT TOP 1 * FROM sac.dbo.tbPlanilhaProposta
                               WHERE FonteRecurso = 109
							         AND idPlanilhaItem = 191
							         AND idProjeto = @idProjeto)
     BEGIN
       INSERT INTO #Verificacao
            VALUES (@idProjeto,'Planilha','Não existe previsão orçamentária para contador.','PENDENTE')
       SET @Flag = 1
     END
   --============================================================================================================
  -- 26 VERIFICAR SE EXISTE NA PLANILHA ORÇAMENTÁRIA ITENS DA ETAPA Assessoria Contábil e Juridica - ADVOGADO
  --============================================================================================================
  IF NOT EXISTS(SELECT TOP 1 * FROM sac.dbo.tbPlanilhaProposta
                               WHERE FonteRecurso = 109
									 AND idPlanilhaItem = 3839
							         AND idProjeto = @idProjeto)
     BEGIN
       INSERT INTO #Verificacao
            VALUES (@idProjeto,'Planilha','Não existe previsão orçamentária para advogado.','PENDENTE')
       SET @Flag = 1
     END
  --============================================================================================================
  -- 27 VERIFICAR SE EXISTE ITEM ORÇAMENTÁRIO NA PLANILHA PARA CADA PRODUTO DESCRITO NO PLANO DE DISTRIBUIÇÃO DO PRODUTO
  --============================================================================================================
  IF EXISTS(SELECT * FROM sac.dbo.PlanoDistribuicaoProduto   a WHERE a.idProjeto = @idProjeto
     AND NOT EXISTS(SELECT * FROM sac.dbo.tbPlanilhaProposta b WHERE     b.idProjeto = @idProjeto
	                                                                 AND b.idProduto = a.idProduto
																	 AND a.idProduto <> 0))
     BEGIN
       INSERT INTO #Verificacao
           VALUES (@idProjeto,'Planilha','Existe produto cadastrado sem a respectiva planilha orcamentária cadastrada.','PENDENTE')
       SET @Flag = 1
     END
  --============================================================================================================
  --28 REGRA DE NEGÓCIO - CUSTO BENEFÍCIO  - VALOR DO PROJETO / QUANTIDADE PRODUZIDA)
  --============================================================================================================
  IF @CustoBeneficio > 375  AND @stProposta = 610
     BEGIN
       INSERT INTO #Verificacao
           VALUES (@idProjeto,'','Conforme Art 4º da Instrução Normativa nº 5/2017, o valor máximo do produto cultural, por beneficiário, será de até R$ 375,00 (trezentos e setenta e cinco reais)','PENDENTE')
       SET @Flag = 1
     END
  --============================================================================================================
  --REGRA DE NEGÓCIO - 22 - DAS VEDAÇÕES
  --============================================================================================================
  --------------------------------------------------------------------------------------------------------------
  -- 29 REGRA DE NEGÓCIO - VERIFICAR SE A RECEITA PREVISTA É MAIOR DO QUE O CUSTO TOTAL DO PROJETO - MÉTRICA
  --------------------------------------------------------------------------------------------------------------

  IF @ReceitaPrevista > @CustoTotalDoProjeto
      BEGIN
        INSERT INTO #Verificacao
            VALUES (@idProjeto,'','A receita prevista não poderá ser superior ao custo total do projeto','PENDENTE')
        SET @Flag = 1
      END
  --------------------------------------------------------------------------------------------------------------
  -- 30 REGRA DE NEGÓCIO - VERIFICAR SE CUSTO DO PROUDTO SECUNDÁRIO E MAIOR DO QUE O DO PRODUTO PRINCIPAL - MÉTRICA
  --------------------------------------------------------------------------------------------------------------
  IF @QtdeVendaProponente > @CustoDoProdutoPrincipal
      BEGIN
        INSERT INTO #Verificacao
            VALUES (@idProjeto,'','Previsão de custos relativos a um Produto Secundário superiores aos custos relativos ao Produto Principal','PENDENTE')
        SET @Flag = 1
      END
  --------------------------------------------------------------------------------------------------------------
  -- Regra Migrada para Aplicação - 31 REGRA DE NEGÓCIO - VERIFICAR PREÇO MÉDIO DO INGRESSO (R$ 225,00) MÉTRICA -
  --------------------------------------------------------------------------------------------------------------
  /** IF @QtdeVendaProponente > 0
     IF (@PrecoVendaProponente / @QtdeVendaProponente) > 150
     BEGIN
          INSERT INTO #Verificacao
              VALUES (@idProjeto,'','Conforme Art 20 da Instrução Normativa nº 5/2017, o preço médio do produto do ingresso ou produto está superior a R$ 225,00','PENDENTE')
          SET @Flag = 1
     END
  END **/
--============================================================================================================
-- MENSAGEM DE NÃO ENVIO DA PROPOSTA
--============================================================================================================
IF @Flag = 1
   BEGIN
     INSERT INTO #Verificacao
            VALUES (@idProjeto,'','<font color=red><b> A PROPOSTA CULTURAL NÃO FOI ENVIADA AO MINISTÉRIO DA CULTURA DEVIDO ÀS PENDÊNCIAS ASSINALADAS ACIMA.</b></font>','')
   END
ELSE
IF @Flag = 0
   BEGIN
      -- ----------------------------------------------------------------------------------------------------
      --  AJUSTAR A UF E O MUNICÍPIO DOS CUSTOS VINCULADOS
      -- ----------------------------------------------------------------------------------------------------
	   SELECT TOP 1 @idUFDespesa = b.UfDespesa,@idMuncipioDespesa = b.MunicipioDespesa,@idUsuario = idUsuario
        FROM sac.dbo.PlanoDistribuicaoProduto a
	   INNER JOIN sac.dbo.tbPlanilhaProposta b on (a.idProjeto = b.idProjeto)
	   WHERE a.stPrincipal = 1
	         AND idEtapa NOT IN(8,10)
	         AND a.idProjeto = @idProjeto

	   UPDATE sac.dbo.tbPlanilhaProposta
	      SET UfDespesa        = @idUFDespesa,
		      MunicipioDespesa = @idMuncipioDespesa,
			  idUsuario        = @idUsuario
		WHERE    UfDespesa        = 1
		     AND MunicipioDespesa = 1
			 AND idUsuario        = 462
	         AND idProjeto        = @idProjeto

   END
--============================================================================================================
-- Mostrar o resultado do check-list
--============================================================================================================
SELECT * FROM #Verificacao WHERE idProjeto = @idProjeto

GO

GRANT  EXECUTE  ON dbo.spChecklistParaApresentacaoDeProposta  TO Usuarios_Internet
GO

-- 08-02-2018_sugestao_enquadramento.sql

-- Adição da coluna 'ultima_sugestao' na tabela 'sac.dbo.sugestao_enquadramento'
ALTER TABLE sac.dbo.sugestao_enquadramento ADD ultima_sugestao BIT DEFAULT 0 NULL

-- Ajuste de dados antigos da tabela 'sac.dbo.sugestao_enquadramento'

update enquadramento set ultima_sugestao = 1
--select count(*)
from sac.dbo.sugestao_enquadramento enquadramento
  inner join (select max(data_avaliacao) as data_avaliacao, id_preprojeto
              from sac.dbo.sugestao_enquadramento
              GROUP BY id_preprojeto
             ) tabela_temporaria
    on enquadramento.id_preprojeto = tabela_temporaria.id_preprojeto
    and enquadramento.data_avaliacao = tabela_temporaria.data_avaliacao
where enquadramento.ultima_sugestao is null

update enquadramento set ultima_sugestao = 0
from sac.dbo.sugestao_enquadramento enquadramento
where enquadramento.ultima_sugestao is null
--COMMIT TRANSACTION ;
--ROLLBACK TRANSACTION ;

-- Definindo coluna id_orgao_superior como obrigatório
ALTER TABLE sac.dbo.sugestao_enquadramento ALTER COLUMN ultima_sugestao BIT NOT NULL;

----------------------------------

-- Adição da coluna 'id_distribuicao_avaliacao_proposta' na tabela 'sac.dbo.sugestao_enquadramento'
ALTER TABLE sac.dbo.sugestao_enquadramento ADD id_distribuicao_avaliacao_proposta INT NULL;
ALTER TABLE sac.dbo.sugestao_enquadramento ADD CONSTRAINT sugestao_enquadramento_distribuicao_avaliacao_proposta_id_distribuicao_avaliacao_proposta_fk FOREIGN KEY (id_distribuicao_avaliacao_proposta) REFERENCES distribuicao_avaliacao_proposta (id_distribuicao_avaliacao_proposta);

-----------------------------------------------------------------------------------------------------------------------
-- Atualização sugestões distribuídas à partir de perfis diferentes de Técnico de admissibilidade,
-- que ainda não possuem registro na tabela "distribuicao_avaliacao_proposta" quando fazem a primeira sugestão de
-- enquadramento.
-----------------------------------------------------------------------------------------------------------------------
update enquadramento set id_distribuicao_avaliacao_proposta = tabela_temporaria.id_distribuicao_avaliacao_proposta
  --select count(*)
from sac.dbo.sugestao_enquadramento enquadramento
  inner join sac.dbo.distribuicao_avaliacao_proposta tabela_temporaria
     on enquadramento.id_preprojeto = tabela_temporaria.id_preprojeto
    and enquadramento.id_perfil_usuario = tabela_temporaria.id_perfil
    and enquadramento.id_orgao_superior = tabela_temporaria.id_orgao_superior
where enquadramento.id_distribuicao_avaliacao_proposta is null;

-- 07-02-2018_sugestao_enquadramento.sql

-- Adição da coluna 'id_orgao_superior' na tabela 'sac.dbo.sugestao_enquadramento'
ALTER TABLE sac.dbo.sugestao_enquadramento ADD id_orgao_superior INT NULL;

-- Ajuste de dados antigos da tabela 'sac.dbo.sugestao_enquadramento'
update enquadramento set id_orgao_superior = tabela_temporaria.id_orgao_superior
  from sac.dbo.sugestao_enquadramento enquadramento
 inner join (select idPreProjeto as id_preprojeto,
                    case AreaAbrangencia when '0' then '251'
                    else '160' end as id_orgao_superior
             from sac.dbo.PreProjeto) tabela_temporaria on enquadramento.id_preprojeto = tabela_temporaria.id_preprojeto
 where enquadramento.id_orgao_superior is null

-- Definindo coluna id_orgao_superior como obrigatório
ALTER TABLE sac.dbo.sugestao_enquadramento ALTER COLUMN id_orgao_superior INT NOT NULL