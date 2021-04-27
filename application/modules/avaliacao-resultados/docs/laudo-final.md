# Documentação Avaliação de Resultados - Laudo Final

## Como acessar o Laudo Final no perfil Coordenador Geral

* Primeiro passo precisa esta logado como Coordenador Geral de Prestação de Contas. 
* Segundo passo vá em Avaliação de Resultados->Analisar laudo final.

### Avaliação de Resultados: Laudo Final 
* Na página do Laudo Final tem um Painel com as abas 'Em Analise', 'Assinar', 'Em Assinatura' e 'Finalizados'.  
* Cada aba vai ter uma tabela com a listagem dos projetos de acordo com o estado atual e um campo para pesquisar por um projeto expecífico.

    ![Painel do Laudo](./imagens/painel_laudo.png)
### Tabela dos projetos
* A tabela é montada com os itens 'PRONAC', 'Nome do projeto', 'Manifestação', 'Devolver' e 'Ação'.
* Nos itens 'PRONAC' e 'Manifestação', são executadas ações comum a todas as abas do painel.
* No item 'Devolver', a funcionalidade se difere apenas na aba Em Análise.
* A 'Ação' no projeto muda conforme o estado do projeto.

#### Consultar Dados do Projeto
* Ao clicar no número do Pronac o usuário é redirecionado para o módulo de `Projetos` podendo visualizar os dados do projeto.

    ![Clique item pronac](./imagens/dados_projeto.png)

#### Visulizar o Parecer técnico
* Clique na manifestação do projeto.
    ![Clique item manifestação](./imagens/manifestacao.png)
* Ao clicar vai abrir uma modal para visualizar o parecer emitido pelo técnico.
    ![Tela Visualizar Parecer](./imagens/visualizar_parecer.png)


#### Devolver 
* Clique no botão devolver.

    ![Clique item devolver parecer](./imagens/acao_devolver.png)
* Em seguida vai abrir uma modal para que confirme a devolução com o Pronac e o nome do projeto selecionado.
    ![modal confirmar devolver](./imagens/modal_devolver.png)
##### - Parecer Técnico
* Estará sendo feita a devolução do parecer tecnico quando clicado no botão Devolver da aba Em Análise.
* Se confirmada a devolução, o parecer do técnico é invalidado o e projeto volta pra _Avaliação de Resultados: Analisar Parecer_, finalizando o fluxo do projeto no laudo final.
##### - Laudo final
* Estará sendo feita a devolução do laudo final quando clicado no botão Devolver das demais abas.
* Confirmada a devolução, o Laudo Final é invalidado, o estado do projeto muda e ele volta para a aba Em Análise para ser criado um novo laudo, reiniciando o fluxo do projeto no laudo final.

### Em Análise 
* Na primeira aba do painel, Em Análise, são listados os projetos que ainda não possui um documento de laudo final gerado.
    ![Aba em análise](./imagens/aba_em_analise.png)
* Na ação é possível emitir o laudo final.

#### Emitir Laudo Final
* Clique na ação da aba Em Análise.

    ![Clique ação emitir laudo](./imagens/acao_em_analise.png)

* Uma modal vai abrir para que o usuário crie o laudo final.
    ![tela form laudo final](./imagens/tela_emitir_laudo.png)
* É necessário o preenchimento de todos os campos para que o laudo seja gerado.
* O usuário tem duas opções no canto superior direito: 1) Salvar, criando assim apenas um rascunho do laudo; 2) Finalizar, que estará salvando, gerando o documento do laudo final e mudando o estado do projeto para iniciar o fluxo de assinaturas.

    ![finalizar laudo final](./imagens/opcoes_laudo.png)


### Assinar
* Segunda aba do painel onde são listados os projetos que já possuem um laudo final gerado porém não teve nenhuma assinatura ainda.
    ![Aba assinar](./imagens/aba_assinar.png)
* A ação disponível nessa aba é para assinar o documento.

#### Assinar Laudo
* Ao clicar na ação da aba Assinar o sistema redireciona para o módulo de `Assinatura` onde será assinado o documento do laudo final.
* Quem deve assinar é o próprio coordenador geral, criador do documento.

    ![Clique ação assinar](./imagens/acao_assinar.png)

### Em Assinatura
* Terceira aba do painel, a listagem apresenta os projetos que já possuem um laudo final emitido e este já foi assinado pelo menos pelo Coordenador Geral, mas ainda não tem todas as assinaturas.
    ![Aba em assinatura](./imagens/aba_em_assinatura.png)
* A ação é para visualizar o laudo final.

#### Visualizar Laudo
* Clique na ação da aba Em Assinatura.

    ![Clique ação visualizar laudo](./imagens/acao_visualizar.png)
* Uma modal vai abrir apresentado um visualização do laudo final emitido.
    ![Tela visualizar laudo](./imagens/visualizar_laudo.png)

### Finalizados
* Ultima aba do painel com a listagem dos projetos finalizados, ou seja, projetos que já possuem um laudo final emitido e este foi assinado por todos os responsáveis.
    ![Aba finalizados](./imagens/aba_finalizados.png)
* A ação nessa aba é para [visualizar o laudo final](#visualizar-laudo) da mesma forma que na aba Em Assinatura.

## Como acessar o Laudo Final no perfil Secretário

* Primeiro passo precisa esta logado como Secretário. 
* Segundo passo vá em Avaliação de Resultados->Analisar laudo final.

### Avaliação de Resultados: Laudo Final 
* Na página do Laudo Final tem um Painel com a aba 'Em Assinatura'.  
* Nessa aba vai ter um campo para pesquisar por um projeto expecífico e uma tabela com a listagem dos projetos que ja possuem um laudo final emitido e este ja foi assinado pelo _Coordenador Geral de Prestação de Contas_.

    ![Painel do Laudo secundário](./imagens/painel_laudo_Sec_Dir.png)
### Tabela de listagem
* A [tabela](#tabela-dos-projetos) é a mesma do perfil do Coordenador geral.
* No campo _Devolver_, o Secretário vai poder estar devolvendo o [Laudo final](#laudo-final).
* Na 'Ação', é possível [Visualizar o laudo](#visualizar-laudo) emitido.

## Como acessar o Laudo Final no perfil Diretor

* Primeiro passo precisa esta logado como Diretor de Departamento. 
* Segundo passo vá em Avaliação de Resultados->Analisar laudo final.

### Avaliação de Resultados: Laudo Final 
 [Segue da mesma forma que o perfil do secretário](#avaliação-de-resultados-laudo-final-1)