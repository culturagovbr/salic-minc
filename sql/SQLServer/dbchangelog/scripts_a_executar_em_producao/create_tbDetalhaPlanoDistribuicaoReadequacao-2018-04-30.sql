CREATE TABLE SAC.dbo.tbDetalhaPlanoDistribuicaoReadequacao (
	idDetalhaPlanoDistribuicao int NOT NULL IDENTITY(1,1),
	idPlanoDistribuicao int,
	idReadequacao int,
	idUF int NOT NULL,
	idMunicipio int NOT NULL,
	stDistribuicao bit DEFAULT ((1)) NOT NULL,
	dsProduto varchar(100) NOT NULL,
	qtExemplares int DEFAULT ((0)) NOT NULL,
	qtGratuitaDivulgacao int DEFAULT ((0)) NOT NULL,
	qtGratuitaPatrocinador int DEFAULT ((0)) NOT NULL,
	qtGratuitaPopulacao int DEFAULT ((0)) NOT NULL,
	qtPopularIntegral int DEFAULT ((0)) NOT NULL,
	qtPopularParcial int DEFAULT ((0)) NOT NULL,
	vlUnitarioPopularIntegral decimal(18,2) DEFAULT ((0)) NOT NULL,
	vlReceitaPopularIntegral decimal(18,2) NOT NULL,
	vlReceitaPopularParcial decimal(18,2) DEFAULT ((0)) NOT NULL,
	qtProponenteIntegral int DEFAULT ((0)) NOT NULL,
	qtProponenteParcial int DEFAULT ((0)) NOT NULL,
	vlUnitarioProponenteIntegral decimal(18,2) DEFAULT ((0)) NOT NULL,
	vlReceitaProponenteIntegral decimal(18,2) NOT NULL,
	vlReceitaProponenteParcial decimal(18,2) DEFAULT ((0)) NOT NULL,
	vlReceitaPrevista decimal(18,2) DEFAULT ((0)) NOT NULL,
	tpLocal char(1),
	tpEspaco char(1),
	tpVenda char(1),
	tpSolicitacao char(1),
	stAtivo char(1) DEFAULT ('S') NOT NULL,
	idPronac int NOT NULL,
	idDetalhaOriginal int DEFAULT ((0)),
	CONSTRAINT PK_tbDetalhaPlanoDistribuicaoReadequacao PRIMARY KEY (idDetalhaPlanoDistribuicao),
	CONSTRAINT FK_Detalha_tbReadequacao FOREIGN KEY (idReadequacao) REFERENCES SAC.dbo.tbReadequacao(idReadequacao)
);

ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD canalAberto bit DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD qtdeVendaPopularNormal int DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD qtdeVendaPopularPromocional int DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD vlUnitarioPopularNormal money DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD precoUnitarioNormal money DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD receitaPopularPromocional money DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD receitaPopularNormal money DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD vlReceitaTotalPrevista money DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD idPlanoDistribuicaoOriginal int DEFAULT 0;
ALTER TABLE SAC.dbo.tbPlanoDistribuicao ADD CONSTRAINT tbPlanoDistribuicao_PlanoDistribuicaoProduto_FK FOREIGN KEY (idPlanoDistribuicaoOriginal) REFERENCES SAC.dbo.PlanoDistribuicaoProduto(idPlanoDistribuicao)
ALTER TABLE sac.dbo.tbPlanoDistribuicao DROP CONSTRAINT tbPlanoDistribuicao_Verificacao_FK;
