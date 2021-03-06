# Salic API

## Passos iniciais

```
    composer update --ignore-platform-reqs
```

## Autenticação

Utilizamos o componente [jwt-auth](https://github.com/tymondesigns/jwt-auth) JSON Web Token (JWT) espec&iacute;fico para Laravel e Lumen, para fornecer nosso m&eacute;todo de autenticação.

Para gerar uma chave privada utilizar o comando abaixo:
```
    php artisan jwt:secret
```

Essa chave privada deve ser utilizada na propriedade ```JWT_SECRET={chave_aqui}``` do seu arquivo ```.env```.

Guia r&aacute;pido de utilização [aqui](http://jwt-auth.readthedocs.io/en/develop/quick-start).

### @todo
- Adicionar o comando ```composer update --ignore-platform-reqs``` no entry-point da imagem do php-fpm
- Adicionar Midware para JWT
- Criar estrutura b&aacute;sica para adicionar m&oacute;dulo inicial de "Autenticação"
- Atualizar documentação b&aacute;sica para utilização da API GraphQL

### @done
- Configurar Banco de dados
- Instalar o lumen
