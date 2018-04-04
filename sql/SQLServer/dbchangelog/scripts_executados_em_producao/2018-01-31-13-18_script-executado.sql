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