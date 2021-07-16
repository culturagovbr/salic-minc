M&oacute;dulo de Solicita&ccedil;&atilde;o
=========================

#### Sobre

Este M&oacute;dulo disponibiliza a op&ccedil;&atilde;o para que os proponentes fa&ccedil;am solicita&ccedil;&otilde;es ao MinC, realizando envio de mensagens na proposta ou projeto.

#### Workflow

#### PERFIL DO PROPONENTE
Essa funcionalidade permite ao proponente enviar solicita&ccedil;&atilde;o ao MinC referente a proposta ou projeto cultural.

O caso de uso poder&aacute; iniciar:
1. pelo bot&atilde;o na linha da proposta;
2. pelo bot&atilde;o na linha do projeto.

Quando a op&ccedil;&atilde;o escolhida for:

1. se o op&ccedil;&atilde;o for na linha da proposta o sistema dever&aacute; montar a tela 2 para a solicita&ccedil;&atilde;o do proponente.

2. se o op&ccedil;&atilde;o for na linha do projeto o sistema dever&aacute; montar a tela 2 para a solicitaç&atilde;o do proponente.

O sistema dever&aacute; disponibilizar ao proponente os seguintes bot&atilde;o de aç&atilde;o para o proponente:

1. Salvar -- permitir&aacute; ao proponente digitar e salvar para posterior envio ao MinC. Nesse caso o sistema dever&aacute; gravar na tabela tbSolicitado o valor no atributo siEncaminhamento o valor 12 (Solicitaç&atilde;o Cadastrada pelo proponente);

2. Enviar - opç&atilde;o que encaminhar&aacute; ao MinC a solicitaç&atilde;o do proponente. Nesse caso o sistema dever&aacute; gravar na tabela tbSolicitado o valor no atributo siEncaminhamento o valor 1(Solicitaç&atilde;o encaminhada ao MinC pelo Proponente);

3. Cancelar - opç&atilde;o que cancela o envio da solicitaç&atilde;o ao MinC.

O sistema dever&aacute; permitir ao proponente anexar documento, opç&atilde;o que n&atilde;o é obrigat&oacute;ria.

O sistema s&oacute; permitir&aacute; ao proponente fazer uma nova solicitaç&atilde;o se a anterior estiver respondida, ou seja se o valor do atributo stEstado estiver setado para 0 na tabela tbSolicitacao.

Quando o proponente clicar no bot&atilde;o salvar o sistema dever&aacute; gravar as seguintes informaç&otilde;es na tabela tbSolicitacao:

Segue a query para inserir o registro na tabela tbSolicitacao
```
INSERT INTO sac.dbo.tbSolicitacao
(idProposta, idOrgao, idSolicitante, dtSolicitacao, dsSolicitacao, idTecnico, dtResposta, dsResposta, idDocumento, siEncaminhamento, stEstado)
SELECT ?,?,?,GETDATE(),'TEXTO DIGITADO,
sac.dbo.fnPegarTecnico(?,?,2),NULL,NULL,NULL,1,1
```
 - idProposta = (idPreprojeto para proposta ou idProjeto para projeto);
 - idOrgao = (prospota - se abragencia for igual a zero codigo 171 sen&atilde;o 262. No caso de projeto é s&oacute; pegar o Orgao da tabela Projetos;
 - idSolicitante = [Usuario Logado];
 - dtSolicitacao = getdate();
 - dsSolicitacao = solicitaç&atilde;o digitada pelo proponente. M&aacute;ximo de 8000 caracteres;
 - idTecnico = null;
 - dtResposta = null;
 - dsResposta = null;
 - idDocumento = id do documento anexado ou null;
 - siEncaminhamento = 12 (Salvar) ou 1 (Enviar)
 - stEstado = 1


###PERFIL MINC
O sistema separar&aacute; as solicitaç&otilde;es pelo atributo idOrgao da tabela tbSolicitacao.

O sistema dever&aacute; apresentar o ícone com um sino quando existir solicitaç&atilde;o para a unidade.

Quando o usu&aacute;rio clicar no ícone o sistema devera apresentar a tela 3 anexada.

Query para montar a tela 3.
SELECT idSolicitacao,idProposta,idOrgao,Sigla,idSolicitante,Solicitante,dtSolicitacao, dsSolicitacao, idTecnico,Tecnico,dtResposta,dsResposta, idDocumento,siEncaminhamento,dsEncaminhamento,stEstado
FROM sac.dbo.vwPainelDeSolicitacaoProponente

O usu&aacute;rio ter&aacute; as seguintes opç&otilde;es:

1.VISUALIZAR: o sistema mostrar&aacute; ao usu&aacute;rio a solicitaç&atilde;o e permitir&aacute; se quiser respond&ecirc;-la;

2.RESPONDER: funcionalidade para responder a solicitaç&atilde;o;

3.ENCAMINHAR: enviar a solicitaç&atilde;o para outra unidade responder a solicitaç&atilde;o.

Quando o usu&aacute;rio clicar no bot&atilde;o RESPONDER o sistema dever&aacute; apresentar a tela 4. Ato de responder significa ATUALIZAR (UPDATE) as seguintes informaç&otilde;es na tabela tbSolicitacao:

```
idTecnico = [USU&Aacute;RIO LOGADO];
dtResposta = GETDATE();
dsResposta = RESPOSTA DIGITADA PELO USU&Aacute;RIO LOGADO;
siEncaminhamento = 15(Solicitaç&atilde;o finalizada pelo MinC)
stEstado = 0
```

Quando o usu&aacute;rio clicar no bot&atilde;o ENCAMINHAR o sistema dever&aacute; ATUALIZAR (UPDATE) as seguintes informaç&otilde;es na tabela tbSolicitacao:
```
idOrgao = UNIDADE SELECIONADA
idTecnico = NULLL
dtResposta = NULL;
dsResposta = NULL;
siEncaminhamento = 1 (Solicitaç&atilde;o encaminhada ao MinC pelo Proponente)
stEstado = 1
```
