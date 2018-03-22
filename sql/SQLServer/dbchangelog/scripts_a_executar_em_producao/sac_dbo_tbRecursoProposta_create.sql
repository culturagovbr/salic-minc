CREATE TABLE sac.dbo.tbRecursoProposta
(
  idRecursoProposta    INT IDENTITY PRIMARY KEY,
  idPreProjeto         INT NOT NULL CONSTRAINT tbRecursoProposta_PreProjeto_idPreProjeto_fk,
  dtRecursoProponente  DATETIME NOT NULL,
  dsRecursoProponente  VARCHAR(MAX) NOT NULL,
  -- dsRecursoProponente => Motivo da solicitação do Proponente
  idProponente INT NOT NULL,
  -- idProponente => Proponente que está solicitando o recurso (idAgente)
  idAvaliadorTecnico INT,
  -- idAvaliadorTecnico => Código do usuário que está avaliando o recurso (Tabelas.dbo.Usuarios.usu_codigo)
  dtAvaliacaoTecnica DATETIME,
  dsAvaliacaoTecnica VARCHAR(MAX),
  tpRecurso CHAR(1) DEFAULT '1' NULL,
  -- tpRecurso => 1 - Pedido de reconsideração
  -- tpRecurso => 2 - Recurso
  tpSolicitacao CHAR(2) NOT NULL,
  -- tpSolicitacao DR => Desistência do Prazo Recursal
  -- tpSolicitacao EN => Enquadramento
  stAtendimento CHAR(1) DEFAULT 'N' NOT NULL,
  -- stAtendimento - 'N' => Sem avaliação
  -- stAtendimento - 'E' => Quando devolve para o proponente
  -- stAtendimento - 'I' => Quando é indeferido
  -- stAtendimento - 'D' => Quando é deferido (movimenta para frente)
  idArquivo INT NULL,
  stEstado BIT DEFAULT 0 NOT NULL
  -- stEstado => 0 - Registro Atual
  -- stEstado => 1 - Registro Inativo
) GO

CREATE INDEX tbRecursoProposta_idPreProjeto_index
  ON tbRecursoProposta (idPreProjeto)
GO

CREATE INDEX tbRecursoProposta_idProponente_index
  ON tbRecursoProposta (idProponente)
GO
