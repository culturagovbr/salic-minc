# Validações para versionamento

Utilizamos como ferramenta para tratamento de versionamento o (Husky)[https://github.com/typicode/husky].

De acordo com o repositório oficial:

    Husky can prevent bad `git commit`, `git push` and more :dog: _woof!_

Com o Husky, podemos utilizar regras para ações como pre-push, pre-commit, etc. Temos a flexibilidade de criar e versionar validações para os arquivos alterados, como por exemplo:
- Verificação de versionamento de scripts de debug
- Validação de padrão mínimo de qualidade utilizando PHPCS e PHPMD
- Entre outras coisas...

Esses definições estão localizadas no arquivo ```package.json```

## Como utilizar?

Execute o comando abaixo na raiz da aplicação:
```
npm install
```

Dessa maneira a aplicação utilizará o arquivo ```package.json``` e instalará dependências.

## Git Hooks
Os hooks (ou gatilhos) utilizados pelo pela aplicação para versionamento utilizando o git estão versionados na aplicação dentro do diretório ```git_hooks``` .
