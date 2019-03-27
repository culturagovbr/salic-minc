
## Pode onde começar

### Instale as dependências
`npm install`
### Para desenvolvimento

`npm run watch` 

Possui reload automático da página(BrowserSync).

### Preparando um pacote para produção (minificado)

`npm run build`

## Utilizando Docker para construir a aplica&ccedil;&atilde;o

```
  docker run -t --rm --name salic-front-build -v "$PWD":/home/node/app -w /home/node/app node:9 sh -c "npm install --silent && npm run build
```

## Guia de estilo

Antes de começar a desenvolver é essencial que você leia o [guia de estilo oficial](https://vuejs.org/v2/style-guide) do Vue JS.

## Estrutura de diret&oacute;rios atual (aguardando contribuição)
A estrutura inicial definida é a seguinte:

```
app
├── package.json
├── public 
│   └── dist (gerado no build)
│        ├── js
│        └── css
├── front 
│   ├── README.md
│   ├── src
│   │   ├── store.js (arquivo principal do vuex)
│   │   ├── assets
│   │   │   ├── img
│   │   │   │   └── logo.png
│   │   │   └── scss
│   │   │       ├── _migrar_estilos_css_pra_ca.scss
│   │   │       └── main.scss
│   │   ├── components (neste nível apenas componentes globais, ou seja, que  podem ser usados por toda aplicação)
│   │   │   ├── planilha
│   │   │   │   ├── Planilha.vue
│   │   │   │   ├── PlanilhaItensAprovados.vue
│   │   │   │   ├── PlanilhaItensPadrao.vue
│   │   │   │   └── PlanilhaItensReadequados.vue
│   │   │   ├── agente
│   │   │   │   ├── AgenteDirigente.vue
│   │   │   │   ├── AgenteEmail.vue
│   │   │   │   ├── AgenteEndereco.vue
│   │   │   │   ├── AgenteIdentificacao.vue
│   │   │   │   ├── AgenteNatureza.vue
│   │   │   │   ├── AgenteProcurador.vue
│   │   │   │   ├── AgenteTelefone.vue
│   │   │   │   └── AgenteIdentificacao.vue
│   │   │   ├── todo
│   │   │   │   ├── TodoList.vue
│   │   │   │   ├── TodoListItem.vue
│   │   │   │   └── TodoListItemButton.vue
│   │   │   ├── SalicCarregando.vue
│   │   │   ├── SalicFormatarValor.vue
│   │   │   ├── sidebar
│   │   │   │   ├── SideBar.vue
│   │   │   │   ├── SidebarLink.vue
│   │   │   │   └── SidebarSearch.vue
│   │   │   └── index.js
│   │   ├── mixins
│   │   │   ├── utils.js
│   │   │   └── planilhas.js
│   │   ├── modules
│   │   │   ├── projeto
│   │   │   │   ├── components (neste nível apenas componentes a nível do módulo)
│   │   │   │   │   └── MenuSuspenso.vue
│   │   │   │   ├── visualizar
│   │   │   │   │   ├── components (nível da pagina)
│   │   │   │   │   │     ├── DadosProjeto.vue
│   │   │   │   │   │     ├── incentivo
│   │   │   │   │   │     │     ├── Index.vue
│   │   │   │   │   │     │     ├── ValoresDoProjeto.vue
│   │   │   │   │   │     │     └── TransferenciaRecurso.vue
│   │   │   │   │   │     ├── Convenio.vue
│   │   │   │   │   │     ├── Proponente.vue (chama os componentes de @/componentes/agentes)
│   │   │   │   │   │     └── PlanilhaProposta.vue (chama os componentes de @/componentes/planilha)
│   │   │   │   │   └── Index.vue (possui a barra lateral e route view para visualizacao)
│   │   │   │   ├── store (actions e getters) - apenas se usar o vuex
│   │   │   │   │   ├── actions.js 
│   │   │   │   │   ├── getters.js 
│   │   │   │   │   ├── index.js 
│   │   │   │   │   ├── mutations.js 
│   │   │   │   │   └── types.js
│   │   │   │   ├── config.js (sync da rota com a store vuex)
│   │   │   │   ├── index.js
│   │   │   │   ├── router.js
│   │   │   │   └── Index.vue (para inicializacao do modulo. obs: futuramente nao sera necessario)
│   │   │   └── agente
│   │   │       ├── components (nível da página)
│   │   │       │   ├── Cadastrar.vue
│   │   │       │   ├── Listar.vue
│   │   │       │   └── Editar.vue
│   │   │       ├── index.js
│   │   │       ├── router.js
│   │   │       └── Index.vue
│   │   ├── plugins
│   │   └── helpers
│   │       └── api
│   │            ├── base
│   │            │      ├── index.js
│   │            │      └── instance.js
│   │            └── Projeto.js
│   ├── build
│   ├── config
│   └── static
└── application (backend)
```

## Criando novo módulo

Ao criar um novo módulo você deve:
 
1 - criar a estrutura de pastas no diretório `src/modules`;

2 - atualizar o arquivo `webpack.base.conf.js` no diretório `build` com informações do novo módulo, conforme exemplo abaixo;
```
 entry: {
        projeto: './src/modules/projeto/index.js',
        agente: './src/modules/agente/index.js'
    }
 ```
3 - criar um método para carregar os scripts na controller desejada. Recomendamos utilizar a `indexController` do seu módulo com o exemplo abaixo.
```
    private function carregarScripts()
    {
        $gitTag = '?v=' . $this->view->gitTag();
        $this->view->headScript()->offsetSetFile(99, '/public/dist/js/manifest.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(100, '/public/dist/js/vendor.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(101, '/public/dist/js/projeto.js'. $gitTag, 'text/javascript', array('charset' => 'utf-8'));
    }
 ```

Se você ainda estiver com dúvidas, pergunte ao coleguinha e atualize este documento com a resposta. ;D

## Browser Support

Atualmente nós suportamos, oficialmente, apenas as duas últimas versões dos seguintes navegadores:

<img src="https://s3.amazonaws.com/creativetim_bucket/github/browser/chrome.png" width="64" height="64"> <img src="https://s3.amazonaws.com/creativetim_bucket/github/browser/firefox.png" width="64" height="64"> <img src="https://s3.amazonaws.com/creativetim_bucket/github/browser/edge.png" width="64" height="64"> <img src="https://s3.amazonaws.com/creativetim_bucket/github/browser/safari.png" width="64" height="64"> <img src="https://s3.amazonaws.com/creativetim_bucket/github/browser/opera.png" width="64" height="64">
