CREATE PROCEDURE dbo.spAtivarPlanilhaOrcamentaria (@idPronac int)
AS 

-- =========================================================================================
-- Autor: Rômulo Menhô Barbosa
-- Data de Criação: 18/01/0016
-- Descrição: Ativar planilha orçamentária
-- =========================================================================================

SET NOCOUNT ON

DECLARE @tpPlanillhaAtiva  CHAR(2)
DECLARE @idTipoReadequacao INT
DECLARE @idReadequacao     INT
DECLARE @stAtivo           CHAR(1)
DECLARE @Erro              INT
-- =========================================================================================
-- PEGAR DADOS DA READEQUAÇÃO
-- =========================================================================================

SELECT @idReadequacao = idReadequacao 
  FROM sac.dbo.tbReadequacao 
  WHERE idPronac = @idPronac
        AND idTipoReadequacao = 2 
        AND siEncaminhamento = 15
		AND stEstado = 1

-- =========================================================================================
-- PEGAR TIPO DE PLANILHA ATIVA
-- =========================================================================================
SELECT TOP 1 @tpPlanillhaAtiva = tpPlanilha 
  FROM sac.dbo.tbPlanilhaAprovacao
  WHERE idPronac = @idPronac
        AND stAtivo = 'S'

-- =========================================================================================
-- DESABILITAR / HABILITAR PLANILHA
-- =========================================================================================
BEGIN TRAN
    -- DESABILITAR PLANILHA ATIVA CORRENTE
	UPDATE sac.dbo.tbPlanilhaAprovacao
	   SET stAtivo = 'N'
     WHERE idPronac = @idPronac
	       AND tpPlanilha = @tpPlanillhaAtiva
		   AND stAtivo = 'S'

     SELECT @Erro = @@ERROR
             
     IF @Erro <> 0
         BEGIN
		   ROLLBACK TRAN
		   SELECT 'FALSE'
		   RETURN
     END

    -- ATIVAR NOVA PLANILHA
	UPDATE 	sac.dbo.tbPlanilhaAprovacao
	   SET stAtivo = 'S'
     WHERE idPronac = @idPronac
		   AND idReadequacao = @idReadequacao

     SELECT @Erro = @@ERROR
             
     IF @Erro <> 0
         BEGIN
		   ROLLBACK TRAN
		   SELECT 'FALSE'
		   RETURN
     END

COMMIT TRAN

SELECT 'TRUE'

