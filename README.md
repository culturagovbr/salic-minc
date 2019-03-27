# SALIC

[![Junte-se ao nosso chat no https://gitter.im/salic-minc/Lobby](https://badges.gitter.im/salic-minc/Lobby.svg)](https://gitter.im/salic-minc/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
<a href="https://app.zenhub.com/workspace/o/culturagovbr/salic-minc/boards" target="_blank">
    <img src="https://img.shields.io/badge/Managed_with-ZenHub-5e60ba.svg" alt="zenhub">
  </a>
[![GitHub Issues Abertas](https://img.shields.io/github/issues/culturagovbr/salic-minc.svg?maxAge=2592000)]() 
[![GitHub Issues Fechas](https://img.shields.io/github/issues-closed-raw/culturagovbr/salic-minc.svg?maxAge=2592000)]()
[![Build Status](https://travis-ci.org/culturagovbr/salic-minc.svg?branch=master)](https://travis-ci.org/culturagovbr/salic-minc)

Bem vindo/a à documentação do SALIC! Aqui você vai encontrar diversas documentações sobre o processo de desenvolvimento do SALIC, versionameno e publicação.

## Passos iniciais:

- ```composer update```
- ```npm install```
- ```npm run build```

## Docker
Utilizamos o Docker como plataforma de desenvolvimento com o intuito de garantir o mesmo ambiente de desenvolvimento 
independentemente do Sistema Operacional(SO) utilizado. Informaçoes mais detalhadas sobre a utilização do docker clique
[aqui](doc/Guia_utilizacao_docker.md).

Para criar um ambiente para trabalhar com o SALIC basta executar o comando abaixo:
```
  docker-compose up -d
```

Para parar o container basta digitar:
```
  docker-compose down
```

## Front-end
* Estamos desacoplando o front-end. [Clique aqui para ver mais](front/README.md).

## Submodulos
* Esse projeto contem os manuais e implementações de layout do salic [Layout](https://github.com/culturagovbr/salic-minc-layout)

## Tecnologias
* [Docker](https://www.docker.com/)
* [PHP 7.x](http://php.net/)
* [Zend Framework 1](https://framework.zend.com/manual/1.12/en/learning.quickstart.html) 
* [Composer](https://getcomposer.org/)
* [jQuery](https://jquery.com/)
* [Vuejs](https://vuejs.org/)
* [Webpack](https://webpack.js.org/)
* [Materialize](http://materializecss.com/)
* [SqlServer](https://www.microsoft.com/en-us/sql-server/sql-server-2017)
* [Cypress](https://www.cypress.io/)

## Documenta&ccedil;&atilde;o sobre a aplica&ccedil;&atilde;o
* [Valida&ccedil;&otilde;es para versionamento](doc/Validacoes_para_versionamento.md)
* [Teste e PHPUnit](doc/Teste_Manual.md)
* [Esquema de desenvolvimento e banco](doc/Esquema_de_desenvolvimento_e_banco.md)
* [Guia de operação e desenvolvimento](doc/Guia_de_operacao-desenvolvimento.md)
* [Regras de versionamento](doc/Regras_versionamento.md)
* [Roteiro de publicação de releases](doc/Roteiro_de_publicacao_de_releases.md) - contém o git workflow
* [SQLs](https://github.com/culturagovbr/salic-minc-sql) de apoio ao desenvolvimento do Salic


## Autores
Várias pessoas colaboraram com o desenvimento do projeto SALIC e decidimos centralizar em um único local todos os que participaram com o desenvolvimento do projeto.
  
Clique [aqui](https://github.com/culturagovbr/salic-minc/graphs/contributors) para visualizar.
