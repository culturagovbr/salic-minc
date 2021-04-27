Manual de testes Salic
==================================================

## Versões:
* [Zend_Test_PHPUnit 1.12.20](https://framework.zend.com/manual/1.12/en/zend.test.phpunit.html)
* [PHPUnit 4.8.36](https://phpunit.de/manual/4.8/en/index.html)
* PHP >= 5.6
* Xdebug instalação opcional, apenas necessario se for gerar relatorios.

É **importante** observar as versões da bibliotecas de documentação do PHPUnit, pois estamos usando a ultima versão compativel sem modificar o core do Zend Framework.

## Estrutura de pastas
```
./tests
├── application
│   ├── Bootstrap.php (bootstrap dos testes, configuração de hook, init e helpers)
│   └── modules (testes seguindo os modulos)
│       ├── admissibilidade
│       │   ├── MensagemControllerTest.php
│       │   ├── RecursoCoordenadorControllerTest.php
│       │   └── RecursoProponenteControllerTest.php
│       ├── agente
│       │   └── controllers
│       │       └── AgentesControllerTest.php
│       └── autenticacao
│          └── controllers
│              ├── IndexControllerTest.php
│              └── LogincidadaoControllerTest.php
├── bin
│   └── test.sh (script para executar todos os teste do sistema)
├── log (pasta com codecoverage gerado pelo PHPUnit)
└── phpunit.xml (configuraçãos dos testes)
```
## Passo a passo

1°passo - Entrar na pasta dos testes.
``` sh
$ cd ./caminho/projeto/tests/bin
```
2°passo - Em sistemas *nix da permissão de execução
``` sh
$ chmod +x test.sh
```

3°passo - Configurar login dos testes, que geralmente fica no ambiente de **[testing : production]** do arquivo application.ini 
``` sh
$ cd ./application/configs/
$ vim application.ini 
```

Exemplo de configuração da sessão de _test_ do arquivo application.ini:

test.params.login = 239XXXXXX  - usuario que será usado para executar os testes    
test.params.password = m2XXXX - Senha do usuario de testes

resources.db.adapter = "PREENCHER"
resources.db.params.host = "PREENCHER"
resources.db.params.dbname = "PREENCHER"
resources.db.params.username = "PREENCHER"
resources.db.params.password = "PREENCHER"
resources.db.params.port = "PREENCHER"
resources.db.params.charset = "PREENCHER"

**OBS:** O usuario precisa ter os perfis de acesso ao sistema necessarios para executar os testes, caso contrario recebermos falhas.

4°passo - Executar os tests
``` sh
$ cd ./caminho/projeto/tests/bin
$ ./test.sh # executa todos os testes do projeto.
```
**OBS:** Ao executar esse _script_ todos os _tests_ dentro do _application/modules_ serão executados, o que pode levar algum tempo. Recomendamos a execução dos _tests_ separadamente atraves do comando:
``` sh
cd ./tests/application
../../vendor/bin/phpunit --debug --colors --verbose -c ../phpunit.xml modules/NomeDoModulo/controllers/NomeDoTesteTest.php
```

5°passo - Devemos receber uma resposta parecida com essa: 

![exemplo de teste](https://github.com/culturagovbr/salic-minc/raw/develop/doc/img/teste_exemplo.png "Teste com sucesso")

## Referências

Nós criamos algumas funçes auxiliáres para ajudar nos testes que se encontram nesse arquivo:
[ControllerActionTestCase.php](../library/MinC/Test/ControllerActionTestCase.php)


Abaixo, referẽncia de implementação de testes possíveis:
``` php
<?php

class PlanoDistribuicaoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    
    public function testIndexAction()
    {
        $this->autenticar();
        $this->perfilParaProponente();
       
        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();
	      $idPreProjeto = 240102;
        // Acessando local de realizacao
        $url = '/proposta/plano-distribuicao?idPreProjeto=' . $idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('plano-distribuicao');
        $this->assertAction('index');
    }

    public function testDetalharPlanoDistribuicaoAction()
	{
        $this->autenticar();
        $this->perfilParaProponente();
        $url = '/proposta/plano-distribuicao/detalhar-plano-distribuicao/idPreProjeto/240102/idPlanoDistribuicao/192467';
        $this->request->setMethod('GET');
        $this->dispatch($url);
        $this->assertModule('proposta');
        $this->assertController('plano-distribuicao');
        $this->assertAction('detalhar-plano-distribuicao');
    }

    public function testSalvarAction()
	{
        $this->autenticar();
        $this->perfilParaProponente();

        $this->resetRequest()
            ->resetResponse();

        $url = '/proposta/plano-distribuicao/salvar?idPreProjeto=240105';
        $this->request->setMethod('POST')
            ->setPost([
            'areaCultural' => 1,
            'idPlanoDistribuicao' => '',
            'idProjeto' => '',
            'prodprincipal' => 0,
            'produto' => 81,
            'segmentoCultural' => 17
        ]);

        $this->dispatch($url);
        $this->assertRedirectTo('/proposta/plano-distribuicao/index?idPreProjeto=240105');
    }
}
```
