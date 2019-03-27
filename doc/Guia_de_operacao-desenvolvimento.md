Existem dois tipos de fluxos principais de trabalho: features e hotfixes. 

# 1 - Feature (Fluxo comum de trabalho)
 
 Trata-se de novas funcionalidades, melhorias e bugs de baixa prioridade.
 
 Além da master, existem duas branches principais de trabalho no SALIC:

               ---- develop ------
              /
    master   o-----------------------
              \
               ---- hmg -----

## Develop

Cada desenvolvedor criará uma branch a partir da `develop`.

### 1 - Criando uma branch a partir da branch develop
    
```sh
    $ git checkout develop
    $ git checkout -b feature/nome-da-funcionalidade
```

    master   o-------------------------------
              \
               o--- develop ----------------------
                \
                 o--- feature/nome-da-funcionalidade --        

### 2 - Faça commits na sua branch e envie para o github (origin)

```sh
    $ git commit -m '[FIX]: modulo: funcionalidade x #numero_da_issue'
    $ git commit -m '[UPDATE]: modulo: funcionalidade y #numero_da_issue'
    $ git push origin feature/nome-da-funcionalidade
```
 Para visualizar uma lista completa do padrão de versionamento de código [clique aqui](https://github.com/devbrotherhood/codeversioningpattern).
   
    master   o-----------------------------------------
               \
                o---------------- develop ----------
                 \
                  o---o----o----- feature/nome-da-funcionalidade ---

### 3 - Atualizando sua branch com as alterações mais recentes da develop

```sh
    $ git checkout feature/nome-da-funcionalidade
    $ git fetch
    $ git merge develop
    $ git push origin feature/nome-da-funcionalidade
```
    master   o----------------------------------------------
              \
               o-----o----o----o--- develop ------------
                \               \
                 o---o----o------o--- feature/nome-da-funcionalidade ---

### 4 - Fluxo de revisão de código(code review) e enviando suas alterações para a branch develop

  A branch `develop` é bloqueada para envio de commits e merges automáticos. 
  
  **No github:**  
 
  Para fazer o merge da sua branch você deverá criar um `pull request` no [github](https://github.com/culturagovbr/salic-minc/pulls).
  Após criar o `pull request`, solicite a um colega que revise e aprove o seu código.

  Importante: o merge para develop só pode acontecer se o seu trabalho já tiver sido homologado pelo cliente(próximo passo).

    master   o-----------------------------------------------------------
              \
               o-----------------------o ----------- develop --------------
                \                     /
                 o---o----o------o---o --- feature/nome-da-funcionalidade ---
    
### 5 - Excluir a branch criada
 
 - Após o merge para a branch `develop` delete sua branch no [github](https://github.com/culturagovbr/salic-minc/branches).
 - Para remover sua branch do conteúdo local execute o comando `git branch -D feature/minha-feature`
 
## Hmg - Homologando seu trabalho

  Para o cliente testar e homologar o seu trabalho, você deve fazer o merge da sua branch para a branch `hmg`.
  Atualmente utilizamos a biblioteca node chamada Husky para versionar nossos hooks do git e adicionar tratativas para versionamento de código. Recomendamos que na raiz do projeto você execute o comando ```npm install``` pelo ao menos uma vez, para que sejam instaladas todas as dependência necessárias.

```sh
    $ git checkout hmg
    $ git fetch
    $ git merge feature/nome-da-funcionalidade
    $ git push origin hmg
```
     master   o-----------------------------------------------------------
                \
                 o-----------------------o ----------- hmg-------------
                  \                     /
                   o---o----o------o---o --- feature/nome-da-funcionalidade ---
                 
  Após o `push`, se você executou `npm install`, um hook atualizará automaticamente o [ambiente de homologação](https://hmg.salic.cultura.gov.br/) utilizando o [jenkins](http://jenkins.cultura.gov.br/).

  Solicite ao cliente para validar o trabalho desenvolvido. Após a validação você deverá fazer um `pull request` da sua branch para a branch `develop`(passo 4 do item Develop).
  
  **OBS:** NUNCA FAÇA UM PULL REQUEST DA BRANCH **HMG** PARA QUALQUER OUTRA BRANCH.

# 2 - Hotfix

  Hotfix é uma alteração emergencial, que deve ser aplicada imediatamente em produção. Envolve poucos commits e dificilmente dura mais que um dia.

Para criar um hotfix, siga os passos abaixo:

```sh
    $ git checkout master
    $ git checkout -b hotfix/nome-da-correcao
    $ # Adicione os arquivos alterados
    $ git add pasta1/pasta2/arquivo2.php
    $ # Faça o commit de suas alterações suas correções
    $ git push hotfix/nome-da-correcao
```

**No github:**  

  1. crie dois pull requests da branch **hotfix/nome-da-correcao** para a branch **master** e para a branch **develop** no [github](https://github.com/culturagovbr/salic-minc/pulls).
  2. solicite a um colega que revise e aprove seu código.   
  
  Dessa maneira conseguimos garantir que a branch **develop** estará sempre mais atualizada.

# 3 - Publicando uma versão para a master

  Para publicar para a master siga o [roteiro para publicação de release](Roteiro_de_publicacao_de_releases.md)
