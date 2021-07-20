M&oacute;dulo de Agentes
=========================

Modulo responsavel por manter agentes e os cadastros de contato.

```
@todo : Complementar esse arquivo com mais informaç&otilde;es do m&oacute;dulo.
```

## Tipos de Agentes (Vis&otilde;es)

No sistema um Agente pode ter uma ou v&aacute;rias vis&otilde;es, em um projeto um agente pode desempenhar diversos papeis.
Um exemplo, pela mecanismo de Incentivo Fiscal, obrigatoriamente vai existir Proponente e Incentivador,
pode ter também um Benefici&aacute;rio e Fornecedor. Abaixo segue uma lista das principais vis&otilde;es de agentes.

#### Proponente
Para cadastrar um proponente:
 - Perfil Proponente
 - Acessar: Menu Principal -> Administrativo -> Cadastrar Proponente

#### Incentivador
Para cadastrar um Incentivador:
 - Perfil de Coordenador ou T&eacute;cnico de Acompanhamento
 - Caminho:
  - Menu Principal -> Acompanhamento -> Movimenta&ccedil;&atilde;o Banc&aacute;ria
  - Menu lateral -> Relat&oacute;rios -> Inconsist&ecirc;ncias de conta capta&ccedil;&atilde;o
  - Na listagem verificar um projeto com inconsist&ecirc;ncia do tipo `Sem incentivador`
  - Clicar na op&ccedil;&atilde;o da coluna `Aç&otilde;es`

#### Fornecedor
Para cadastrar Fornecedor:
 - Perfil de Proponente
 - Caminho: Menu Lateral -> Realizar Comprova&ccedil;&atilde;o Financeira
 - Acessar um link da coluna `Item de Custo`
 - Na pr&oacute;xima tela preencher o campo CNPJ/CPF

#### Beneficiário
Para cadastrar um benefici&aacute;rio:
 - Perfil de Proponente
 - Caminho:
    - Menu Lateral -> Realizar Comprovação Física -> Relat&oacute;rio Trimestral
    - Na tela clicar no link da coluna `Status`
    - Menu Lateral -> Plano de Distribui&ccedil;&atilde;o
    - Preencher o campo CNPJ/CPF

#### Outras vis&otilde;es
O sistema permite que o Gestor do sistema cadastre um agente com outras vis&otilde;es
 - Perfil Gestor do SALIC
 - Caminho: Menu Principal -> Administrativo -> Manter Agentes
 - Vis&otilde;es disponíveis para cadastro pelo Gestor:
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
