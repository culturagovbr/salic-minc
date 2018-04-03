Módulo de Solicita&ccedil;&atilde;o
=========================

#### Sobre

Este M&oacute;dulo disponibiliza a opção para que os proponentes façam solicitações ao MinC, realizando envio de mensagens na proposta ou projeto.

#### Workflow

#### PERFIL DO PROPONENTE
Essa funcionalidade permite ao proponente enviar solicitação ao MinC referente a proposta ou projeto cultural.

O caso de uso poderá iniciar: 
1. pelo botão na linha da proposta;
2. pelo botão na linha do projeto.

Quando a opção escolhida for: 

1. se o opção for na linha da proposta o sistema deverá montar a tela 2 para a solicitação do proponente.

2. se o opção for na linha do projeto o sistema deverá montar a tela 2 para a solicitação do proponente.

O sistema deverá disponibilizar ao proponente os seguintes botão de ação para o proponente:

1. Salvar -- permitirá ao proponente digitar e salvar para posterior envio ao MinC. Nesse caso o sistema deverá gravar na tabela tbSolicitado o valor no atributo siEncaminhamento o valor 12 (Solicitação Cadastrada pelo proponente);

2. Enviar - opção que encaminhará ao MinC a solicitação do proponente. Nesse caso o sistema deverá gravar na tabela tbSolicitado o valor no atributo siEncaminhamento o valor 1(Solicitação encaminhada ao MinC pelo Proponente);

3. Cancelar - opção que cancela o envio da solicitação ao MinC.

O sistema deverá permitir ao proponente anexar documento, opção que não é obrigatória.

O sistema só permitirá ao proponente fazer uma nova solicitação se a anterior estiver respondida, ou seja se o valor do atributo stEstado estiver setado para 0 na tabela tbSolicitacao.

Quando o proponente clicar no botão salvar o sistema deverá gravar as seguintes informações na tabela tbSolicitacao:

Segue a query para inserir o registro na tabela tbSolicitacao
```
INSERT INTO sac.dbo.tbSolicitacao
(idProposta, idOrgao, idSolicitante, dtSolicitacao, dsSolicitacao, idTecnico, dtResposta, dsResposta, idDocumento, siEncaminhamento, stEstado)
SELECT ?,?,?,GETDATE(),'TEXTO DIGITADO,
sac.dbo.fnPegarTecnico(?,?,2),NULL,NULL,NULL,1,1
```
 - idProposta = (idPreprojeto para proposta ou idProjeto para projeto); 
 - idOrgao = (prospota - se abragencia for igual a zero codigo 171 senão 262. No caso de projeto é só pegar o Orgao da tabela Projetos;
 - idSolicitante = [Usuario Logado];
 - dtSolicitacao = getdate();
 - dsSolicitacao = solicitação digitada pelo proponente. Máximo de 8000 caracteres;
 - idTecnico = null;
 - dtResposta = null;
 - dsResposta = null;
 - idDocumento = id do documento anexado ou null;
 - siEncaminhamento = 12 (Salvar) ou 1 (Enviar)
 - stEstado = 1


###PERFIL MINC
O sistema separará as solicitações pelo atributo idOrgao da tabela tbSolicitacao.

O sistema deverá apresentar o ícone com um sino quando existir solicitação para a unidade.

Quando o usuário clicar no ícone o sistema devera apresentar a tela 3 anexada.

Query para montar a tela 3.
SELECT idSolicitacao,idProposta,idOrgao,Sigla,idSolicitante,Solicitante,dtSolicitacao, dsSolicitacao, idTecnico,Tecnico,dtResposta,dsResposta, idDocumento,siEncaminhamento,dsEncaminhamento,stEstado
FROM sac.dbo.vwPainelDeSolicitacaoProponente

O usuário terá as seguintes opções:

1.VISUALIZAR: o sistema mostrará ao usuário a solicitação e permitirá se quiser respondê-la;

2.RESPONDER: funcionalidade para responder a solicitação;

3.ENCAMINHAR: enviar a solicitação para outra unidade responder a solicitação.

Quando o usuário clicar no botão RESPONDER o sistema deverá apresentar a tela 4. Ato de responder significa ATUALIZAR (UPDATE) as seguintes informações na tabela tbSolicitacao:

```
idTecnico = [USUÁRIO LOGADO];
dtResposta = GETDATE();
dsResposta = RESPOSTA DIGITADA PELO USUÁRIO LOGADO;
siEncaminhamento = 15(Solicitação finalizada pelo MinC)
stEstado = 0
```

Quando o usuário clicar no botão ENCAMINHAR o sistema deverá ATUALIZAR (UPDATE) as seguintes informações na tabela tbSolicitacao:
```
idOrgao = UNIDADE SELECIONADA
idTecnico = NULLL
dtResposta = NULL;
dsResposta = NULL;
siEncaminhamento = 1 (Solicitação encaminhada ao MinC pelo Proponente)
stEstado = 1
```