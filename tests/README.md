# Testes do SALIC

### Como estão organizados os testes?

Os testes estão localizados na pasta app/tests. Nessa pasta, podemos ver:
```
   application   -> as rotinas de teste ficam aqui
   bin           -> script para executar os testes
   log           -> logs e relat&oacute;rios de cobertura
```
Na pasta application, h&aacute; um arquivo Bootstrap.php, que inicializa o sistema para a suite de testes e uma pasta 'modules'. Dentro da pasta 'modules', cada m&oacute;dulo a ser testado possui uma pasta separada entre controllers e models. Por hora, estamos testando apenas backend.

### Padrões de estrutura
As classes de teste estão subdividas na seguinte estrutura de pastas:
```
    .
    ├── Bootstrap.php
    └── modules
        ├── admissibilidade
        │   └── controllers
        │       ├── AdmissibilidadeControllerTest.php
        │       └── ...
        ├── agente
        │   └── controllers
        │       └── AgentesControllerTest.php
        ├── analise
        │   └── controllers
        │       └── AnaliseControllerTest.php
        ├── assinatura
        │   └── controllers
        │       └── AssinaturaControllerTest.php
        ├── autenticacao
        │   └── controllers
        │       └── IndexControllerTest.php
        ├── default
        │   └── controllers
        │       ├── ConsultardadosprojetoControllerTest.php
        │       └── ...
        └── proposta
            └── controllers
                ├── DiligenciarControllerTest.php
                └── ...

```

### Teste de integração(rotas):

Teste de integração &eacute; a fase do teste de software em que m&oacute;dulos são combinados e testados em grupo.
Ela sucede o teste de unidade, em que os m&oacute;dulos são testados individualmente, e antecede o teste de sistema, em que o sistema completo (integrado) &eacute; testado num ambiente que simula o ambiente de produção.
```fonte: https://pt.wikipedia.org/wiki/Teste_de_integra%C3%A7%C3%A3o```

Requisitos B&aacute;sicos:
* Composer na versão 1.5+;
* Executar o comando ```composer update``` ;
* Definir os parametros abaixo no arquivo de configuração ```Application/configs/application.ini```:
```
    test.params.login = 'XXXXXXXXXXX';(CPF)
    test.params.password = 'xxxxx';(senha)
```

Exemplo de envio de uma requisição do tipo GET dentro de uma action:
```
    $url = '/proposta/diligenciar/listardiligenciaproponente?idPreProjeto='. $idPreProjeto;
    $this->request->setMethod('GET');
    $this->dispatch($url);
```

M&eacute;todos Essenciais:
```
    * dispatch() -> Carrega uma action
    * assertUrl() -> Verifica se a url foi carregada
    * assertResponseCode('200'); -> Assegura que o c&oacute;digo da p&aacute;gina carregada foi o informado
```

Padrões de nomenclatura:
* M&eacute;todos de teste que testam actions devem terminar seu nome com "Action";

### Testes unit&aacute;rios(funcionalidades):

Requisitos B&aacute;sicos:
* @todo: preencher esse item

Padrões de nomenclatura
* @todo: preencher esse item

Observando que a definição de classes e m&oacute;dulos, seguem a mesma estrutura definida na aplicação da pasta ```application/modules.```

### Metas - Curto Prazo
* Eliminar erros ou falhas dos testes atuais

### Como testar?
* Acessar diret&oacute;rio {root}/tests/bin
* Executar o script desejado de acordo com a necessidade. Exemplo: ```./test-admissibilidade.sh```
