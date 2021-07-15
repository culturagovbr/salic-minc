Módulo de Agentes
=========================

Modulo responsavel por manter agentes e os cadastros de contato.

```
@todo : Complementar esse arquivo com mais informações do módulo.
```

## Tipos de Agentes (Visões)

No sistema um Agente pode ter uma ou várias visões, em um projeto um agente pode desempenhar diversos papeis.
Um exemplo, pela mecanismo de Incentivo Fiscal, obrigatoriamente vai existir Proponente e Incentivador,
pode ter também um Beneficiário e Fornecedor. Abaixo segue uma lista das principais visões de agentes.

#### Proponente
Para cadastrar um proponente:
 - Perfil Proponente
 - Acessar: Menu Principal -> Administrativo -> Cadastrar Proponente

#### Incentivador
Para cadastrar um Incentivador:
 - Perfil de Coordenador ou Técnico de Acompanhamento
 - Caminho:
  - Menu Principal -> Acompanhamento -> Movimentação Bancária
  - Menu lateral -> Relatórios -> Inconsistências de conta captação
  - Na listagem verificar um projeto com inconsistência do tipo `Sem incentivador`
  - Clicar na opção da coluna `Ações`

#### Fornecedor
Para cadastrar Fornecedor:
 - Perfil de Proponente
 - Caminho: Menu Lateral -> Realizar Comprovação Financeira
 - Acessar um link da coluna `Item de Custo`
 - Na próxima tela preencher o campo CNPJ/CPF

#### Beneficiário
Para cadastrar um beneficiário:
 - Perfil de Proponente
 - Caminho:
    - Menu Lateral -> Realizar Comprovação Física -> Relatório Trimestral
    - Na tela clicar no link da coluna `Status`
    - Menu Lateral -> Plano de Distribuição
    - Preencher o campo CNPJ/CPF

#### Outras visões
O sistema permite que o Gestor do sistema cadastre um agente com outras visões
 - Perfil Gestor do SALIC
 - Caminho: Menu Principal -> Administrativo -> Manter Agentes
 - Visões disponíveis para cadastro pelo Gestor:
     - Proponente
    - Incentivador
    - Servidor
    - Dirigente de Instituição
    - Parecerista de projeto cultural
    - Componente da Comissão
    - Votantes da Cnic
    - Procurador
    - Fornecedor
    - Servidor Comissionado
