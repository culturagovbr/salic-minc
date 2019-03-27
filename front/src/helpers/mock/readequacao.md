HOST: http://localhost:4000

# SALIC API

# Group Readequacao

## Readequacao - Visualizar lista de readequações [/readequacao?idPronac={idPronac}]

+ Parameters
    + idPronac: 14100 (number, required)

### Visualizar lista de readequações [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body
        
            [
                {
                    "idPronac": 217336,
                    "idReadequacao": 217336,
                    "idTipoReadequacao": 6,
                    "dtSolicitacao": "2019-01-22",
                    "idSolicitante": 267,
                    "dsSolicitacao": "blabalbalbalablabalb",
                    "dsJustificativa": "É necessário",
                    "idDocumento": 19440,
                    "idAvaliador": 335,
                    "dsAvaliacao": "queuqeuqueuq",
                    "stAtendimento": "N",
                    "siEncaminhamento": 15,
                    "idNrReuniao": 45654,
                    "stEstado": 1,
                    "dtEnvio": "2019-01-23",
                    "stEstagioAtual": "proponente"
                },
                {
                    "idPronac": 217336,
                    "idReadequacao": 217336,
                    "idTipoReadequacao": 6,
                    "dtSolicitacao": "2019-01-22",
                    "idSolicitante": 267,
                    "dsSolicitacao": "blabalbalbalablabalb",
                    "dsJustificativa": "É nescessário",
                    "idDocumento": 19440,
                    "idAvaliador": 335,
                    "dsAvaliacao": "queuqeuqueuq",
                    "stAtendimento": "N",
                    "siEncaminhamento": 15,
                    "idNrReuniao": 45654,
                    "stEstado": 1,
                    "dtEnvio": "2019-01-23",
                    "stEstagioAtual": "proponente"
                }
            ]

### Criar nova readequação [POST]

+ Request (multipart/form-data; boundary=BOUNDARY)

        --BOUNDARY
        Content-Disposition: form-data; name="idPronac"
        
        215221
        --BOUNDARY
        Content-Disposition: form-data; name="idTipoReadequacao"
        
        5
        --BOUNDARY
        Content-Disposition: form-data; name="dsSolicitacao"
        
        Porque sim, eu quero!
        --BOUNDARY
        Content-Disposition: form-data; name="dsJustificativa"
        
        Porque é necessário.
        --BOUNDARY
        Content-Disposition: form-data; name="documento"; filename="file.pdf"
        Content-Type: application/pdf
        Content-Transfer-Encoding: base64
        
        [BINARY CODE]
        --BOUNDARY

+ Response 201 (application/json; charset=utf-8)

    + Body

            {
                    "idPronac": 217336,
                    "idReadequacao": 217336,
                    "idTipoReadequacao": 6,
                    "dtSolicitacao": "2019-01-22",
                    "idSolicitante": 267,
                    "dsSolicitacao": "blabalbalbalablabalb",
                    "dsJustificativa": "É necessário",
                    "idDocumento": 19440,
                    "idAvaliador": 335,
                    "dsAvaliacao": "queuqeuqueuq",
                    "stAtendimento": "N",
                    "siEncaminhamento": 15,
                    "idNrReuniao": 45654,
                    "stEstado": 1,
                    "dtEnvio": "2019-01-23",
                    "stEstagioAtual": "proponente"
            }


## Readequacao - Dados readequação [/readequacao/dados-readequacao/{idReadequacao}]

+ Parameters
    + idReadequacao: 14100 (number, required)

### Visualizar dados readequação [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body
        
            {
                "idPronac": 217336,
                "idReadequacao": 217336,
                "idTipoReadequacao": 6,
                "dtSolicitacao": "2019-01-22",
                "idSolicitante": 267,
                "dsSolicitacao": "blabalbalbalablabalb",
                "dsJustificativa": "É necessário",
                "idDocumento": 19440,
                "idAvaliador": 335,
                "dsAvaliacao": "queuqeuqueuq",
                "stAtendimento": "N",
                "siEncaminhamento": 15,
                "idNrReuniao": 45654,
                "stEstado": 1,
                "dtEnvio": "2019-01-23",
                "stEstagioAtual": "proponente"
            }


### Atualizar dados readequação [PUT]

+ Request (multipart/form-data; boundary=BOUNDARY)

        --BOUNDARY
        Content-Disposition: form-data; name="dsSolicitacao"
        
        Porque sim, eu quero!
        --BOUNDARY
        Content-Disposition: form-data; name="dsJustificativa"
        
        Porque é necessário.
        --BOUNDARY
        Content-Disposition: form-data; name="idAvaliador"
        
        236
        --BOUNDARY
        Content-Disposition: form-data; name="dsAvaliacao"
        
        --BOUNDARY
        Content-Disposition: form-data; name="stAcao"
        
        Aprovado!
        --BOUNDARY
        Content-Disposition: form-data; name="documento"; filename="file.pdf"
        Content-Type: application/pdf
        Content-Transfer-Encoding: base64
        
        [BINARY CODE]
        --BOUNDARY


+ Response 200 (application/json; charset=utf-8)

    + Body
        
            {
                "idPronac": 217336,
                "idReadequacao": 217336,
                "idTipoReadequacao": 6,
                "dtSolicitacao": "2019-01-22",
                "idSolicitante": 267,
                "dsSolicitacao": "blabalbalbalablabalb",
                "dsJustificativa": "É necessário",
                "idDocumento": 19440,
                "idAvaliador": 335,
                "dsAvaliacao": "queuqeuqueuq",
                "stAtendimento": "N",
                "siEncaminhamento": 15,
                "idNrReuniao": 45654,
                "stEstado": 1,
                "dtEnvio": "2019-01-23",
                "stEstagioAtual": "proponente"
            }

### Excluir readequação [DELETE]

+ Parameters
    + idReadequacao (string, required) - ID da Readequação  

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "mensagem": "Readequação removida com sucesso!"
            }

## Readequacao - Visualizar tipos de Readequação disponíveis para criação por Pronac [/readequacao/tipos-disponiveis?idPronac={idPronac}]

+ Parameters
    + idPronac (string, required)

### Visualizar tipos de Readequação disponíveis para criação por Pronac [GET]

+ Request
    + Attributes
        + idPronac: 141001 

+ Response 200 (application/json; charset=utf-8)

    + Body

            [
                {
                    "idTipoReadequacao": 3,
                    "tpCampo": "input",
                    "descricao": "Alteração de Razão Social"
                },
                {
                    "idTipoReadequacao": 4,
                    "tpCampo": "input"
                    "descricao": "Agência Bancária"
                },
                {
                    "idTipoReadequacao": 5,
                    "tpCampo": "textarea",
                    "descricao": "Sinópse da Obra"
                },
                {
                    "idTipoReadequacao": 6,
                    "tpCampo": "textarea",
                    "descricao": "Impacto Ambiental"
                },
                {
                    "idTipoReadequacao": 13,
                    "tpCampo": "data",
                    "descricao": "Período de Execução"
                },
                {
                    "idTipoReadequacao": 1,
                    "tpCampo": "",
                    "descricao": "Remanejamento até 50 %"
                },
                {
                    "idTipoReadequacao": 2,
                    "tpCampo": "",
                    "descricao": "Planilha Orçamentária"
                },
                {
                    "idTipoReadequacao": 22,
                    "tpCampo": "",
                    "descricao": "Saldo de Aplicação"
                },
                {
                    "idTipoReadequacao": 23,
                    "tpCampo": "",
                    "descricao": "Transferência de recursos entre projetos"
                }
            ]

## Readequacao - Buscar campos por tipo readequação [/readequacao/campo-atual-projeto?idPronac={idPronac}&idTipoReadequacao={idTipoReadequacao}]

### Buscar campos por tipo readequação [GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 5 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "tpoCampo": "date",
                "dscCampo": "2018-12-25"
            }

## Readequação - Documento [/readequacao/{idReadequacao}/documento]

+ Parameters
    + idReadequacao: 15122 (number, required)

### Remover documento de uma readequação [DELETE]

+ Response 200 (application/json; charset=utf-8)
  
    + Body
      
            {
                "mensagem": "Arquivo removido com sucesso!"
            }
