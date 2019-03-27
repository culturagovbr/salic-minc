# Avaliação de Resutlados

Modulo reponsavel pela avaliação de notas/comprovantes de despesas gastos nos projetos de incentivo fiscal.

## Requisitos:
- Modulo de Autenticação.
- Modulo de Assinatura.
- Modulo de Diligências.
- Modulo de Fluxos.

## Banco de dados:

Modulo de fluxo:
```
SELECT * FROM sac.dbo.Estados;
select * from sac.dbo.FluxosProjeto ;
select * from sac.dbo.Fluxos;
```

Modulo de Avaliação de Resultados:
```
SELECT * from BDCORPORATIVO.scSAC.tbComprovantePagamento;
SELECT * from BDCORPORATIVO.scSAC.tbComprovantePagamentoxPlanilhaAprovacao;
SELECT * from sac.dbo.tbPlanilhaAprovacao;
```

## Frontend:

- Vue.js
- Vuetify
- Webpack
```
npm install
npm run watch
```

## Documentos

## Colaboradores

- Rômulo Menhô (SEFIC)
- Douglas Vasconcelos (SEFIC)
- Janete Rodrigues (SEFIC)
- Fernanda
- Pedro
- Cleber
- Marcos
- Luiz F.
- Ruan
- Volthier
- Fernão Lopes
- Wouerner
