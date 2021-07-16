# SALIC - Guia de utilização do Docker

Para facilitar o desenvolvimento e escabilidade da aplicação trabalhamos a plataforma Docker. Com essa plataforma temos a possibilidade de isolar serviços permitindo v&aacute;rias possibilidades de integrações, manutenções e melhorias.

Caso queira conhecer um pouco mais sobre a plataforma Docker &eacute; poss&iacute;vel acessar uma apresentação criada pela equipe da UFABC que &eacute; simples e objetiva fornecendo o que você precisa saber para iniciar no mundo Docker, clicando [aqui](http://pt.slideshare.net/vinnyfs89/docker-essa-baleia-vai-te-conquistar?qid=aed7b752-f313-4515-badd-f3bf811c8a35&v=&b=&from_search=1).

## Docker Images

O Docker fornece "Images" que são imagens de sistemas operacionais, o qual podemos costumizar para que esteja de acordo com um cen&aacute;rio mais pr&oacute;ximo poss&iacute;vel do ambiente de produção, criando nossas pr&oacute;prias imagens ou receitas para que sejam geradas dinâmicamente.

As "Images" são como "templates". Com elas tamb&eacute;m &eacute; poss&iacute;vel versionar estados do sistema operacional e gerar "Containers".

## Docker Containers

Um container &eacute; a implementação de uma Imagem, quando ele &eacute; criado, &eacute; poss&iacute;vel definir configurações para acesso e compartilhamento de conte&uacute;do.

## Docker Commands

Para executarmos ações na plataforma docker precisamos executar alguns comandos. Clicando [aqui](https://github.com/vinnyfs89/dockerCommands) você pode obter uma lista dos comandos mais utilizados do Docker.

## Imagens de Ambiente

As principais imagens utilizadas pelo SALIC podem ser encontradas [aqui](https://hub.docker.com/r/culturagovbr/) .

## Dockerfile

O Salic possui um Dockerfile contendo todas as informações necess&aacute;rias para que tenhamos um ambiente para o funcionamento
da aplicação. Para acessa-lo, clique [aqui](https://github.com/culturagovbr/docker-salic) .
