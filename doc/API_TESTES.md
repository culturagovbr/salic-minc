##Para executar os testes:

    1. Instalar bibliotecas registradas no projeto


```
npm install
```

    1. Rodar a API de testes (os endpoints serão montados de acordo com o que estiver descrito no arquivo de documentação da API - api.apib)


```
drakov -f api.apib -p 4000
```

    1. Rodar o front-end utlizando a API de testes


```
npm run watch:test
```

    1. Executar testes do Cypress


```
npm run cypress:open
```

## Para visualizar a documentação da API:

```
aglio -i api.apib --theme-full-width --no-theme-condense -s -p 4200
```
