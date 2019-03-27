# Group Agente

## Agente [/agente/visualizar/obter-dados-proponente/idAgente/{idAgente}]

### Dados agente [GET]

+ Parameters
    + idAgente: 299275

+ Response 200 (application/json; charset=utf-8)

    + Body
        {
          "data": {
            "identificacao": {
              "idagente": "299275",
              "cnpjcpf": "01234567891011",
              "cnpjcpfsuperior": "00000000000000",
              "tipopessoa": "1",
              "dtcadastro": "2017-05-11 18:24:26",
              "dtatualizacao": "2017-05-11 18:24:26",
              "dtvalidade": "2018-05-11 18:24:26",
              "status": "0",
              "usuario": "4787",
              "idnome": "299267",
              "tiponome": "19",
              "descricao": "CENTRO DE EXTENSAO UNIVERSITARIA"
            },
            "natureza": {
              "idnatureza": "41851",
              "idagente": "299275",
              "direito": "35",
              "esfera": "0",
              "poder": "0",
              "administracao": "0",
              "usuario": "4787"
            },
            "enderecos": [
              {
                "idendereco": "284830",
                "idagente": "299275",
                "tipoendereco": "Comercial",
                "tipologradouro": "45",
                "logradouro": "bla bla bla o",
                "numero": "573",
                "bairro": "Bela Vista",
                "complemento": " ",
                "cidade": "355030",
                "uf": "SP",
                "cep": "01234567",
                "municipio": "São Paulo",
                "ufdescricao": "",
                "status": "1",
                "divulgar": "0",
                "usuario": "4787",
                "codtipoendereco": "23",
                "codmun": "355030",
                "coduf": "35",
                "dstipologradouro": "Rua"
              }
            ],
            "emails": [
              {
                "idinternet": "153021",
                "idagente": "299275",
                "tipointernet": "29",
                "descricao": "salicweb@gmail.com",
                "status": "1",
                "divulgar": "0",
                "tipo": "E-mail Institucional"
              },
              {
                "idinternet": "156835",
                "idagente": "299275",
                "tipointernet": "29",
                "descricao": "salicweb@gmail.com",
                "status": "1",
                "divulgar": "1",
                "tipo": "E-mail Institucional"
              },
              {
                "idinternet": "165520",
                "idagente": "299275",
                "tipointernet": "29",
                "descricao": "salicweb@gmail.com",
                "status": "1",
                "divulgar": "0",
                "tipo": "E-mail Institucional"
              },
              {
                "idinternet": "167365",
                "idagente": "299275",
                "tipointernet": "29",
                "descricao": "salicweb@gmail.com",
                "status": "1",
                "divulgar": "0",
                "tipo": "E-mail Institucional"
              }
            ],
            "telefones": [
              {
                "idtelefone": "156345",
                "tipotelefone": "25",
                "numero": "3177-8209",
                "divulgar": "0",
                "dstelefone": "Comercial",
                "ufsigla": "SP",
                "idagente": "299275",
                "ddd": "11",
                "codigo": "11"
              },
              {
                "idtelefone": "160052",
                "tipotelefone": "25",
                "numero": "3177-8320",
                "divulgar": "0",
                "dstelefone": "Comercial",
                "ufsigla": "SP",
                "idagente": "299275",
                "ddd": "11",
                "codigo": "11"
              },
              {
                "idtelefone": "160053",
                "tipotelefone": "25",
                "numero": "3177-8200",
                "divulgar": "0",
                "dstelefone": "Comercial",
                "ufsigla": "SP",
                "idagente": "299275",
                "ddd": "11",
                "codigo": "11"
              }
            ],
            "dirigentes": [
              {
                "cnpjcpfdirigente": "12345678991",
                "idagente": "299447",
                "nomedirigente": "Itamar Franco"
              },
              {
                "cnpjcpfdirigente": "19876543212",
                "idagente": "299442",
                "nomedirigente": "Paulo Oliveira Silva"
              }
            ]
          },
          "success": "true"
        }
