-- =============================================================================================
-- Autor: Rômulo Menhô Barbosa
-- Data de Criacao: 14/11/2012
-- Descricao: Liberar Links
-- =============================================================================================
-- Data de Alteracao: 28/01/2013
-- Motivo: Considerar que o proponente podera apenas salvar a resposta da diligencia sem enviar.
--         Portanto enquanto nao enviar o link devera ficar disponivel ao proponente.
-- =============================================================================================
-- Data de Alteracao: 13/03/2013
-- Motivo: Retirar o link de readequacao temporariamente.
-- =============================================================================================
-- Data de Alteracao: 31/07/2013
-- Motivo: Incluir os links de Analise, Execucao e Prestacao de Contas.
-- =============================================================================================
-- Data de Alteracao: 30/10/2013
-- Motivo: Nova versao
-- =============================================================================================
-- Data de Alteracao: 22/11/2013
-- Motivo: Ajustar link de recurso
-- =============================================================================================
-- Data de Alteracao: 25/02/2014
-- Motivo: Ajustar link para remanejamento ate 20%
-- =============================================================================================
-- Data de Alteracao: 05/09/2014
-- Motivo: Ajustar link para relatório final de cumprimento do objeto.
-- =============================================================================================
-- Data de Alteracao: 28/04/2015
-- Motivo: Incluir restricao na fase 2.
--         Nao liberar link após a analise do componente e sim apenas o fechamento da reuniao
--         da CNIC.
-- =============================================================================================
-- Data de Alteracao: 12/06/2015
-- Motivo: Ajustar links para readequeacao.
-- =============================================================================================
-- Data de Alteracao: 29/06/2015
-- Motivo: Ajustar links para remanejamento ate DE 20 na fase 5.
-- =============================================================================================
-- Data de Alteracao: 24/09/2015
-- Motivo: Ajustar links para o proponente alterar comprovacao financeira após ser diligenciado.
-- =============================================================================================
-- Data de Alteracao: 30/09/2015
-- Motivo: Fase 4- periodo de execucao >= getdate()
-- =============================================================================================
-- Data de Alteracao: 15/04/2016.
-- Motivo: Incluir opcoes para inibir os links Marcas e Solicitacao de Prorrogacao na fase 5
--         Permitir comprovacoes para projetos nas situacoes E74 e E75.
-- =============================================================================================
-- Data de Alteracao: 20/04/2016.
-- Motivo: Retirar a restricao de limite de 20% de captacao da fase 4 e 5.
-- =============================================================================================
-- Data de Alteracao: 28/04/2016.
-- Motivo: Permitir readequar planilha na fase 5 nas seguintes situacoes: E13,E15,E23,E74,E75
-- =============================================================================================
-- Data de Alteracao: 29/04/2016.
-- Motivo: Só liberar o link do Relatório de Cumprimento de Objeto na fase 4 após todos os 
--         Relatórios Trimestrais forem enviados.
-- =============================================================================================
-- Data de Alteracao: 05/05/2016.
-- Motivo: Só liberar o link de Solicitar Prorrogacao e Marcas nas fase 3 e 4.
-- =============================================================================================

IF OBJECT_ID(N'dbo.fnLiberarLinks') IS NOT NULL
    DROP FUNCTION dbo.fnLiberarLinks;
GO

SET QUOTED_IDENTIFIER ON 
GO
SET ANSI_NULLS OFF 
GO

CREATE FUNCTION dbo.fnLiberarLinks(@Acao tinyint, @CNPJCPF_Proponente varchar(14),@idUsuario_Logado int,@idPronac int)
RETURNS varchar(100) 
AS  
BEGIN 
DECLARE @Rows                  int
DECLARE @Fase                  char(1)
DECLARE @Pronac                varchar(7)
DECLARE @AnoProjeto            char(2)
DECLARE @Sequencial            varchar(5)
DECLARE @NrReuniao             int
DECLARE @idNrReuniao           int
DECLARE @qtAEnviar             int
DECLARE @qtEnviados            int
DECLARE @ContaLiberada         char(1)
DECLARE @NrPortaria            varchar(10)
DECLARE @DtFinalExecucao       datetime
DECLARE @DtSituacao            datetime
DECLARE @DtReuniao             datetime
DECLARE @Situacao              char(3)
DECLARE @idDiligencia          int
DECLARE @idRecurso             int
DECLARE @Permissao             char(1) -- 0.Sem permissao; 1.Acesso liberado
DECLARE @Diligencia            char(1) --  0.Respondida; 1. A responder
DECLARE @Recursos              char(1) --  0.Sem Recurso; 1. Solicitar Recursos
DECLARE @Readequacao           char(1) --  0.Sem Pedido de Readequacao; 1. Permitir Solicitar Readequacao
DECLARE @PercentualCaptado     money
DECLARE @ComprovacaoFinanceira char(1) --  0.Sem comprovacao Financeira; 1. Permitir Realizar a Comprovacao Financeira
DECLARE @RelatorioTrimestral   char(1) --  0.Sem Relatorio Trimestral; 1. Permitir cadastrar Relatorio Trimestral
DECLARE @RelatorioFinal        char(1) --  0.Sem Relatorio Final; 1. Permitir cadastrar Relatorio de cumprimento do objeto
DECLARE @Analise               char(1) --  0.Sem visualizacao; 1. Com visualizacao
DECLARE @Execucao              char(1) --  0.Sem visualizacao; 1. Com visualizacao
DECLARE @PrestacaoDeContas     char(1) --  0.Sem visualizacao; 1. Com visualizacao
DECLARE @Readequacao_20        char(1) --  0.Sem Pedido de Readequacao; 1. Permitir Solicitar Readequacao
DECLARE @SolicitarProrrogacao  char(1) --  0.Sem visualizacao; 1. Com visualizacao
DECLARE @Marcas                char(1) --  0.Sem visualizacao; 1. Com visualizacao

--=====================================================================================================
-- SETAR VARIAVEIS
--=====================================================================================================
SET @Diligencia            = 0
SET @Recursos              = 0
SET @Readequacao           = 0
SET @PercentualCaptado     = 0
SET @ComprovacaoFinanceira = 0
SET @RelatorioTrimestral   = 0
SET @RelatorioFinal        = 0
SET @Analise               = 0
SET @Execucao              = 0
SET @PrestacaoDeContas     = 0
SET @Readequacao_20        = 0
SET @SolicitarProrrogacao  = 0
SET @Marcas                = 0

--=====================================================================================================
-- VERIFICAR PERMISSAO
--=====================================================================================================
SELECT  @Permissao = SAC.dbo.fnVerificarPermissao(@Acao,@CNPJCPF_Proponente,@idUsuario_Logado,@idPronac)
--=====================================================================================================
-- PEGAR O PRONAC
--=====================================================================================================
SELECT @AnoProjeto = AnoProjeto, @Sequencial = Sequencial, @Situacao = Situacao, @DtSituacao = DtSituacao,
       @DtFinalExecucao = DtFimExecucao 
       FROM Projetos WHERE idPronac = @idPronac
--=====================================================================================================
-- PEGAR O NUMERO DA REUNIAO DA CNIC QUE O PROJETO PARTICIPOU
--=====================================================================================================
SELECT @DtReuniao = dtFinal FROM BdCorporativo.scSAC.tbPauta a
       INNER JOIN tbReuniao b on (a.idNrReuniao = b.idNrReuniao)
       WHERE idPronac=@idPronac
--=====================================================================================================
-- VERIFICAR SE A CONTA FOI LIBERADA
--=====================================================================================================
SELECT @ContaLiberada = 'S' FROM LIBERACAO WHERE AnoProjeto = @AnoProjeto AND Sequencial = @Sequencial
SELECT @Rows = @@ROWCOUNT
IF @Rows = 0
   SET @ContaLiberada = 'N'
--=====================================================================================================
-- VERIFICAR ENVIO DE RELATÓRIO TRIMESTRAL
--=====================================================================================================
SELECT @qtAEnviar = dbo.fnQtdeRelatorioTrimestral(@idPronac)
SELECT @qtEnviados = COUNT(*) FROM SAC.DBO.tbComprovanteTrimestral WHERE siComprovanteTrimestral <> 1 and idPronac = @idPronac
--=====================================================================================================
-- VERIFICAR EXISTENCIA DE PORTARIA
--=====================================================================================================
SELECT @NrPortaria = SAC.dbo.fnNrPortariaAprovacao(@AnoProjeto,@Sequencial)
--=====================================================================================================
-- VERIFICAR PERCENTUAL DE CAPTACAO
--=====================================================================================================
SELECT  @PercentualCaptado = ISNULL(SAC.dbo.fnPercentualCaptado(@AnoProjeto,@Sequencial),0)
--=====================================================================================================
-- VERIFICAR SE HA DILIGENCIA PARA RESPONDER.
--=====================================================================================================
SELECT  @idDiligencia = idDiligencia 
        FROM tbDiligencia
        WHERE stEstado = 0 and ((DtResposta IS NULL AND stEnviado = 'S') or 
                                (DtResposta IS not NULL AND stEnviado = 'N')) AND idPronac = @idPronac
SELECT @Rows = @@ROWCOUNT
IF @Rows <> 0
   SET @Diligencia = 1
--=====================================================================================================
-- VERIFICAR SE HA RECURSO
--=====================================================================================================

IF (DATEDIFF(DAY,@DtReuniao,GETDATE()) < = 11 
       AND @Situacao in ('A14','A16','A17','A20','A23','A24','A41','A42','D02','D03','D14')
       AND NOT EXISTS(SELECT TOP 1 * FROM tbRecurso WHERE stEstado = 1 and siFaseProjeto = 2 and siRecurso = 0 AND idPronac = @idPronac)
       AND NOT EXISTS(SELECT  TOP 1 * FROM tbRecurso WHERE stEstado = 0 AND siRecurso <> 0 AND idPronac = @idPronac))
   OR (NOT EXISTS(SELECT  TOP 1 * FROM tbRecurso WHERE stEstado = 0 AND tpRecurso = 2 AND idPronac = @idPronac) 
       AND @Situacao in ('A14','A16','A17','A20','A23','A24','A41','A42','D02','D03','D14')
       AND (ISNULL(DATEDIFF(DAY,(SELECT dtFinal FROM sac.dbo.TBRecurso a
                                       INNER JOIN tbReuniao b on (a.idNrReuniao = b.idNrReuniao)
                                       WHERE a.tpRecurso = 1 AND a.siRecurso <> 0 AND a.stEstado = 1 AND a.idPronac = @idPronac),GETDATE()),90)) <= 10)
      BEGIN
     SELECT @Rows = @@ROWCOUNT
     IF @Rows = 0
        SET @Recursos = 1
   END 
 
--=====================================================================================================
-- IDENTIFICAR A FASE DO PROJETO
--=====================================================================================================
-- FASE 2: DA TRANSFORMACAO DA PROPOSTA EM PROJETO ATE O ENCERRAMENTO DA CNIC.
IF (@NrPortaria IS NULL  OR @NrPortaria = '')
    AND @Situacao NOT IN ('B11','B14','C10','C20','C30','D20')  
    AND NOT EXISTS(SELECT * FROM BDCorporativo.scsac.tbPauta a
                            INNER JOIN sac.dbo.tbReuniao b on (a.idNrReuniao = b.idNrReuniao)
                            WHERE b.stEstado = 0 and a.idPronac = @idPronac)  
   BEGIN 
     SET @Fase = 2
     SET @Analise = 1
   END
ELSE
IF @NrPortaria IS NULL
  BEGIN
     SET @Fase = 2
     SET @Analise = 0
  END
ELSE  
--=====================================================================================================
-- FASE 3 : DA PUBLICACAO DA PORTARIA DE APROVACAO ATE A LIBERACAO DA CONTA
--=====================================================================================================
IF (@NrPortaria IS NOT NULL OR @NrPortaria <> '') AND @ContaLiberada = 'N'
   BEGIN
	 SET @Fase = 3
	 SET @Analise = 1
	 SET @Execucao = 1
	 SET @PrestacaoDeContas = 0
	 SET @SolicitarProrrogacao  = 0
     SET @Marcas                = 0
   END
ELSE
--=====================================================================================================
-- FASE 4 : DA LIBERACAO DA CONTA ATE O DATA FINAL DO PERIODO DE EXECUCAO
--=====================================================================================================
IF @ContaLiberada = 'S' AND CONVERT(CHAR(8),@DtFinalExecucao,112) > = CONVERT(CHAR(8),GETDATE(),112)
   BEGIN 
     SET @Analise = 1
     SET @Execucao = 1
     SET @PrestacaoDeContas = 1
     SET @ComprovacaoFinanceira = 1
     SET @RelatorioTrimestral = 1
     SET @Readequacao = 1
 	 SET @SolicitarProrrogacao  = 1
     SET @Marcas                = 1
    --===============================================================================================
     -- CHECAR SE EXISTE READEQUACAO DE 20%
     --===============================================================================================
     IF NOT EXISTS(SELECT TOP 1 * FROM SAC.DBO.tbReadequacao a 
                                  INNER JOIN SAC.DBO.tbTipoReadequacao b on (a.idTipoReadequacao = b.idTipoReadequacao)
                                  WHERE a.idPronac = @idPronac AND b.idTipoReadequacao = 1)
        BEGIN
          SET @Readequacao_20 = 1
        END
     ELSE     
         BEGIN
           SET @Readequacao_20 = 0
         END

     --===============================================================================================
     -- CHECAR SE EXISTE RELATORIO DE CUMPRIMENTO DO OBJETO PARA SER ENVIADO
     --===============================================================================================
     IF EXISTS(SELECT TOP 1 * FROM tbCumprimentoObjeto WHERE siCumprimentoObjeto <> 1 AND idPronac = @idPronac) 
        BEGIN
          SET @Readequacao_20 = 0
          SET @Readequacao = 0
          SET @ComprovacaoFinanceira = 0
          SET @RelatorioTrimestral = 0
          SET @RelatorioFinal = 0
        END   
     ELSE
        BEGIN         
          --===============================================================================================
          -- CHECAR SE EXISTE RELATORIO TRIMESTRAL PARA SER ENVIADO
          --===============================================================================================
	      IF (@qtAEnviar - @qtEnviados) = 0
             BEGIN
               SET @RelatorioTrimestral = 0
		       SET @RelatorioFinal = 1
             END
          ELSE
	         BEGIN
               SET @RelatorioTrimestral = 1
	           SET @RelatorioFinal = 0
             END
        END
        
	 SET @Fase = 4
   END
ELSE
--=====================================================================================================
-- FASE 5 : PRESTACAO DE CONTAS DO PROPONENTE - RELATÓRIO DE CUMPRIMENTO DO OBJETO
--=====================================================================================================
IF @ContaLiberada = 'S' AND CONVERT(CHAR(8),GETDATE(),112) > CONVERT(CHAR(8),@DtFinalExecucao,112) 
    BEGIN 
      --===============================================================================================
      -- SETAR VARIAVEIS
      --===============================================================================================
      SET @Analise = 1
      SET @Execucao = 1
      SET @PrestacaoDeContas = 1
      SET @Marcas = 0
      SET @SolicitarProrrogacao  = 0
      SET @Readequacao = 0
	  SET @Readequacao_20 = 0
      SET @ComprovacaoFinanceira = 1
      SET @RelatorioTrimestral = 0
      SET @RelatorioFinal = 1 
  
      --===============================================================================================
      -- EXCESSAO PARA AJUSTAR PLANILHA PARA PRESTAR CONTAS
      --===============================================================================================  
	  IF @Situacao IN ('E13','E15','E23','E74','E75')
         BEGIN
	       SET @Readequacao_20 = 1
		   SET @Readequacao = 1
         END
     --===============================================================================================
     -- CHECAR SE EXISTE RELATORIO DE CUMPRIMENTO DO OBJETO PARA SER ENVIADO
     --===============================================================================================
      IF EXISTS(SELECT TOP 1 * FROM tbCumprimentoObjeto WHERE siCumprimentoObjeto <> 1 AND idPronac = @idPronac) 
         BEGIN
           SET @ComprovacaoFinanceira = 0
           SET @RelatorioFinal = 0
		   IF @Diligencia = 1
	          BEGIN
                SET @ComprovacaoFinanceira = 1
		      END
         END
	  ELSE
	     BEGIN
           --===============================================================================================
           -- CHECAR SE EXISTE READEQUACAO DE 20%
           --===============================================================================================
           IF NOT EXISTS(SELECT TOP 1 * 
		                        FROM SAC.DBO.tbReadequacao a 
                                INNER JOIN SAC.DBO.tbTipoReadequacao b on (a.idTipoReadequacao = b.idTipoReadequacao)
                                WHERE a.idPronac = @idPronac AND b.idTipoReadequacao = 1)
              BEGIN
                SET @Readequacao_20 = 1
              END
           ELSE     
              BEGIN
                SET @Readequacao_20 = 0
              END
	     END 
      SET @Fase = 5
	END
--=====================================================================================================
-- RESULTADO
--=====================================================================================================
RETURN   (@Permissao + ' - ' + @Fase + ' - ' + @Diligencia + ' - ' + @Recursos       + ' - ' + @Readequacao          + ' - ' + @ComprovacaoFinanceira + ' - '+ 
          @RelatorioTrimestral + ' - '+ @RelatorioFinal + ' - '+ @Analise + ' - '+ @Execucao + ' - '+ @PrestacaoDeContas + ' - '+ @Readequacao_20+
		  ' - '+ @Marcas + ' - ' + @SolicitarProrrogacao)

END


GO
SET QUOTED_IDENTIFIER OFF 
GO
SET ANSI_NULLS ON 
GO

GRANT  EXECUTE  ON [dbo].fnLiberarLinks  TO usuarios_internet
GO