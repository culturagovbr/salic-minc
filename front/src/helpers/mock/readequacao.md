HOST: http://localhost:4000

# SALIC API

# Group Readequacao

## Readequacao - Visualizar lista de readequações [/readequacao?idPronac=202779&idTipoReadequacao=22&stStatusAtual=proponente]

### Filtro por três parâmetros [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15123,
                            "idTipoReadequacao": 2,
                            "dsTipoReadequacao": "Planilha orçament&aacute;ria",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "Solicito alteração na planilha.",
                            "dsJustificativa": "&eacute; necess&aacute;rio",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avalio que est&aacute; bom.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15124,
                            "idTipoReadequacao": 6,
                            "dsTipoReadequacao": "Impacto ambiental",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "Descrição completa do impacto ambiental...",
                            "dsJustificativa": "&eacute; necess&aacute;rio alterar a parte que diz sobre o consumo de &aacute;gua elevado.",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avaliação positiva, de acordo.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15125,
                            "idTipoReadequacao": 10,
                            "dsTipoReadequacao": "Alteração de Proponente",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "Fulano de Tal",
                            "dsJustificativa": "Ciclano não est&aacute; mais no projeto, adicionando Fulano de Tal",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avaliação positiva, de acordo.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15126,
                            "idTipoReadequacao": 12,
                            "dsTipoReadequacao": "Nome do Projeto",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "19 Gramado Cine &aacute;udio Video",
                            "dsJustificativa": "Adicionamos '&aacute;udio' porque &eacute; preciso",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Ok, de acordo. Aprovado",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15127,
                            "idTipoReadequacao": 13,
                            "dsTipoReadequacao": "Per&iacute;odo de Execução",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "2019-06-01 00:00:00",
                            "dsJustificativa": "Ampliando per&iacute;odo de execução para dar tempo.",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Ok, de acordo. Aprovado",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        }
                    ]
                }
            }

## Readequacao - Obter Documento anexado [/readequacao/documento?idDocumento={idDocumento}&idReadequacao={idReadequacao}]

+ Parameters
    + idDocumento: 14100 (number, required)
    + idReadequacao: 14100 (number, required)

### Filtro por id do Documento [GET]

+ Response 200 (application/pdf; charset=utf-8)

    + Body

            {
                filename: "documento-readequacao.pdf",
                type: "application/pdf",
                content: "[BINARY CODE]"
            }

## Readequacao - Visualizar lista de readequações [/readequacao?idPronac={idPronac}]

+ Parameters
    + idPronac: 14100 (number, required)

### Visualizar lista de readequações [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15123,
                            "idTipoReadequacao": 2,
                            "dsTipoReadequacao": "Planilha orçament&aacute;ria",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "Solicito alteração na planilha.",
                            "dsJustificativa": "&eacute; necess&aacute;rio",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avalio que est&aacute; bom.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15124,
                            "idTipoReadequacao": 6,
                            "dsTipoReadequacao": "Impacto ambiental",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "<p><b>Descrição completa do impacto ambiental...</b></p>",
                            "dsJustificativa": "&eacute; necess&aacute;rio alterar a parte que diz sobre o consumo de &aacute;gua elevado.",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avaliação positiva, de acordo.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15125,
                            "idTipoReadequacao": 10,
                            "dsTipoReadequacao": "Alteração de Proponente",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "Fulano de Tal",
                            "dsJustificativa": "Ciclano não est&aacute; mais no projeto, adicionando Fulano de Tal",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avaliação positiva, de acordo.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15126,
                            "idTipoReadequacao": 12,
                            "dsTipoReadequacao": "Nome do Projeto",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "19 Gramado Cine &aacute;udio Video",
                            "dsJustificativa": "Adicionamos '&aacute;udio' porque &eacute; preciso",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Ok, de acordo. Aprovado",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        },
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15127,
                            "idTipoReadequacao": 13,
                            "dsTipoReadequacao": "Per&iacute;odo de Execução",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "2019-06-01 00:00:00",
                            "dsJustificativa": "Ampliando per&iacute;odo de execução para dar tempo.",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Ok, de acordo. Aprovado",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23",
                            "stStatusAtual": "proponente"
                        }
                    ]
                }
            }

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

        Porque &eacute; necess&aacute;rio.
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
                    "idReadequacao": 15128,
                    "idTipoReadequacao": 6,
                    "dsTipoReadequacao": "Impacto ambiental",
                    "dtSolicitacao": "2019-01-22",
                    "idSolicitante": 267,
                    "dsNomeSolicitante": "Leôncio das Neves",
                    "dsSolicitacao": "blabalbalbalablabalb",
                    "dsJustificativa": "&eacute; necess&aacute;rio",
                    "idDocumento": 19440,
                    "idAvaliador": 335,
                    "dtAvaliador": "2019-02-03",
                    "dsAvaliacao": "queuqeuqueuq",
                    "stAtendimento": "N",
                    "siEncaminhamento": 15,
                    "idNrReuniao": 45654,
                    "stEstado": 1,
                    "dtEnvio": "2019-01-23",
                    "stStatusAtual": "proponente"
            }


## Readequacao - Dados readequação [/readequacao/dados-readequacao/{idReadequacao}]

+ Parameters
    + idReadequacao: 15125 (number, required)

### Visualizar dados readequação [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "idPronac": 217336,
                "idReadequacao": 15125,
                "idTipoReadequacao": 6,
                "dtSolicitacao": "2019-01-22",
                "idSolicitante": 267,
                "dsNomeSolicitante": "Leôncio das Neves",
                "dsSolicitacao": "blabalbalbalablabalb",
                "dsJustificativa": "&eacute; necess&aacute;rio",
                "idDocumento": 19440,
                "idAvaliador": 335,
                "dtAvaliador": "2019-02-03",
                "dsAvaliacao": "queuqeuqueuq",
                "stAtendimento": "N",
                "siEncaminhamento": 15,
                "idNrReuniao": 45654,
                "stEstado": 1,
                "dtEnvio": "2019-01-23",
                "stStatusAtual": "proponente"
            }


### Atualizar dados readequação [POST]

+ Request (multipart/form-data; boundary=BOUNDARY)

        --BOUNDARY
        Content-Disposition: form-data; name="idReadequacao"

        15125
        --BOUNDARY
        Content-Disposition: form-data; name="dsSolicitacao"

        Porque sim, eu quero!
        --BOUNDARY
        Content-Disposition: form-data; name="dsJustificativa"

        Porque &eacute; necess&aacute;rio.
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
                "idReadequacao": 15128,
                "idTipoReadequacao": 6,
                "dtSolicitacao": "2019-01-22",
                "idSolicitante": 267,
                "dsNomeSolicitante": "Leôncio das Neves",
                "dsSolicitacao": "blabalbalbalablabalb",
                "dsJustificativa": "&eacute; necess&aacute;rio",
                "idDocumento": 19440,
                "idAvaliador": 335,
                "dtAvaliador": "2019-02-03",
                "dsAvaliacao": "queuqeuqueuq",
                "stAtendimento": "N",
                "siEncaminhamento": 15,
                "idNrReuniao": 45654,
                "stEstado": 1,
                "dtEnvio": "2019-01-23",
                "stStatusAtual": "proponente"
            }

### Excluir readequação [DELETE]

+ Parameters
    + idReadequacao (string, required) - ID da Readequação

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "mensagem": "Readequação removida com sucesso!"
            }



## Painel de readequações -  [/readequacao/painel]

### Filtro por pronac [GET]

+ Parameters
    + pronac (string, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idPronac": 217336,
                            "idReadequacao": 15123,
                            "PRONAC": "160338",
                            "NomeProjeto": "Baile perfumado",
                            "tpReadequacao": "Planilha orçament&aacute;ria",
                            "dtDistribuicao": "2019-01-22",
                            "qtDiasAvaliacao" "14",
                            "idTecnicoParecerista": 255,
                            "idOrgao": 262,
                            "idTipoReadequacao": 2,
                            "dsTipoReadequacao": "Planilha orçament&aacute;ria",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "Solicito alteração na planilha.",
                            "dsJustificativa": "&eacute; necess&aacute;rio",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avalio que est&aacute; bom.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23"
                        },
                        {
                            "idPronac": 217331,
                            "idReadequacao": 15523,
                            "PRONAC": "160338",
                            "NomeProjeto": "Miscelânia e tal",
                            "tpReadequacao": "Objetivos",
                            "dtDistribuicao": "2019-01-22",
                            "qtDiasAvaliacao" "14",
                            "idTecnicoParecerista": 255,
                            "idOrgao": 262,
                            "idTipoReadequacao": 2,
                            "dsTipoReadequacao": "Planilha orçament&aacute;ria",
                            "dtSolicitacao": "2019-01-22",
                            "idSolicitante": 267,
                            "dsNomeSolicitante": "Leôncio das Neves",
                            "dsSolicitacao": "Solicito alteração na planilha.",
                            "dsJustificativa": "&eacute; necess&aacute;rio",
                            "idDocumento": 19440,
                            "idAvaliador": 335,
                            "dsNomeAvaliador": "Ciclano avaliador",
                            "dtAvaliador": "2019-02-03",
                            "dsAvaliacao": "Avalio que est&aacute; bom.",
                            "stAtendimento": "N",
                            "siEncaminhamento": 15,
                            "idNrReuniao": 45654,
                            "stEstado": 1,
                            "dtEnvio": "2019-01-23"
                        },


## Readequacao - Visualizar tipos de Readequação dispon&iacute;veis para criação por Pronac [/readequacao/tipos-disponiveis?idPronac={idPronac}]

+ Parameters
    + idPronac (string, required)

### Visualizar tipos de Readequação dispon&iacute;veis para criação por Pronac [GET]

+ Request
    + Attributes
        + idPronac: 141001

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idTipoReadequacao": 3,
                            "descricao": "Razão Social"
                        },
                        {
                            "idTipoReadequacao": 7,
                            "descricao": "Especificação T&eacute;cnica"
                        },
                        {
                            "idTipoReadequacao": 16,
                            "descricao": "Objetivos"
                        }
                    ]
                }
            }


## Readequacao - Buscar campos por tipo readequação [/readequacao/campo-atual?idPronac=217336&idTipoReadequacao=12]

### Buscar campos por tipo readequação - nome do projeto [GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 12 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idTipoReadequacao": "12",
                            "descricao": "Nome do Projeto",
                            "tpCampo": "input",
                            "dsCampo": "19o. Gramado Cine Video"
                        }
                    ]
                }
            }

## Readequacao - Buscar campos por tipo readequação [/readequacao/campo-atual?idPronac=217336&idTipoReadequacao=2]

### Buscar campos por tipo readequação - planilha orçament&aacute;ria [GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 2 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idTipoReadequacao": "2",
                            "descricao": "Planilha Orçament&aacute;ria",
                            "tpCampo": "planilha_orcamentaria",
                            "dsCampo": ""
                        }
                    ]
                }
            }

## Readequacao - Buscar campos por tipo readequação [/readequacao/campo-atual?idPronac=217336&idTipoReadequacao=10]

### Buscar campos por tipo readequação - alteração de proponente[GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 10 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idTipoReadequacao": "10",
                            "descricao": "Alteração de Proponente",
                            "tpCampo": "input",
                            "dsCampo": "22344355678"
                        }
                    ]
                }
            }


## Readequacao - Buscar campos por tipo readequação [/readequacao/campo-atual?idPronac=217336&idTipoReadequacao=6]

### Buscar campos por tipo readequação - impacto ambiental [GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 6 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idTipoReadequacao": "6",
                            "descricao": "Impacto ambiental",
                            "tpCampo": "textarea",
                            "dsCampo": "A independência de adubação e correção anual do solo d&aacute;-se principalmente pelo manejo de podas e com a cobertura de solo, que al&eacute;m de produzir uma terra de qualidade qu&iacute;mica, f&iacute;sica e biologicamente falando, ret&eacute;m &aacute;gua, mat&eacute;ria orgânica, acaba com problemas de erosão e promove a infiltração de &aacute;gua (poupança de &aacute;gua) e a recarga dos lenç&oacute;is fre&aacute;ticos. <br/> A biodiversidade promove o equil&iacute;brio biol&oacute;gico e elimina a necessidade de aplicação de defensivos qu&iacute;micos (agrot&oacute;xicos). <br/>As condições clim&aacute;ticas do local de plantio (microclima) favorecem a sa&uacute;de das plantas, não as expondo a estresses por excesso de insolação, ventos e variações bruscas de temperatura. E ainda a evapotranspiração das plantas promove a chuva.<br/><br/>Aqui est&aacute; nossa vocação como um pa&iacute;s florestal. Nação da abundância de &aacute;gua e biodiversidade. A oportunidade de geração de renda e valor para todo o mundo."
                        }
                    ]
                }
            }

## Readequacao - Buscar campos por tipo readequação [/readequacao/campo-atual?idPronac=217336&idTipoReadequacao=13]

### Buscar campos por tipo readequação - per&iacute;odo de execução [GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 10 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body


            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "idTipoReadequacao": "13",
                            "descricao": "Per&iacute;odo de Execução",
                            "tpCampo": "date",
                            "dsCampo": "2019-06-01 00:00:00"
                        }
                    ]
                }
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

## Readequação - Saldo: dispon&iacute;vel para edição de item [/readequacao/saldo-disponivel-edicao-item/{idPronac}]

### Verifica se est&aacute; dispon&iacute;vel para editar item [GET]

+ Parameters
    + idPronac: 217336 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "disponivelEdicaoItem": "true"
            }

## Readequação - Finaliza an&aacute;lise de readequação [/readequacao/finalizar?idReadequacao={idReadequacao}]

### Finaliza readequação e envia para an&aacute;lise [POST]

+ Parameters
    + idReadequacao: 15213 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "mensagem": "Readequação enviada para an&aacute;lise."
            }

## Readequação - solicitar uso do saldo [/readequacao/solicitar-saldo/{idPronac}]

### Solicita uso do saldo [GET]

+ Parameters
    + idPronac: 217336 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                    "idPronac": 217336,
                    "idReadequacao": 15128,
                    "idTipoReadequacao": 22,
                    "dsTipoReadequacao": "Saldo de aplicação",
                    "dtSolicitacao": "2019-01-22",
                    "idSolicitante": 267,
                    "dsNomeSolicitante": "Leôncio das Neves",
                    "dsSolicitacao": "199533,00",
                    "dsJustificativa": "&eacute; necess&aacute;rio",
                    "idDocumento": 19440,
                    "idAvaliador": 335,
                    "dtAvaliador": "2019-02-03",
                    "dsAvaliacao": "queuqeuqueuq",
                    "stAtendimento": "N",
                    "siEncaminhamento": 15,
                    "idNrReuniao": 45654,
                    "stEstado": 1,
                    "dtEnvio": "2019-01-23",
                    "stStatusAtual": "proponente"
            }


## Readequação - Obtem parecer avaliação de readequação [/readequacao/avaliacao]

+ Parameters
    + idReadequacao: 15213 (number, required)

+ Response 200 (application/json; charset=utf-8)

            {
                "idParecer": "352233"
                "idPronac": "212553"
                "anoProjeto": "17"
                "sequencial" : "3233"
                "tipoParecer": "3"
                "parecerFavoravel": "2"
                "numeroReuniao": "31"
                "dtParecer": "2008-10-17 00:00:00"
                "parecerista": "James Lovelock"
                "resumoParecer": "Avalio readequação plaus&iacute;vel com indicação de alterações clim&aacute;ticas"
                "sugeridoReal": "331111"
                "atendimento": "S"
                "idEnquadramento": "33212"
                "stAtivo": "1"
                "idTipoAgente": "1",
                "logon": "321"
            }

## Readequação - Grava parecer de avaliação de solicitação [/readequacao/avaliacao]

+ Parameters
    + idReadequacao: 25221 (number, required)
    + ParecerAvaliacao: 2 (number, required)
    + dsParecer: "Avalio como plaus&iacute;vel a justificativa da solicitação de readequação." (string, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "idParecer": "352233"
                "idPronac": "212553"
                "anoProjeto": "17"
                "sequencial" : "3233"
                "tipoParecer": "3"
                "parecerFavoravel": "2"
                "numeroReuniao": "31"
                "dtParecer": "2008-10-17 00:00:00"
                "parecerista": "James Lovelock"
                "resumoParecer": "Avalio readequação plaus&iacute;vel com indicação de alterações clim&aacute;ticas"
                "sugeridoReal": "331111"
                "atendimento": "S"
                "idEnquadramento": "33212"
                "stAtivo": "1"
                "idTipoAgente": "1",
                "logon": "321"
            }


## Readequação - Obter planilha [/readequacao/obter-planilha=2116336&idTipoReadequacao=22]

### Solicita planilha [GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 22 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

        {
          "items": [
            {
                "tpPlanilha": "CO",
                "dtPlanilha": "2019-05-16 14:50:17",
                "idPronac": 216611,
                "idProduto": 1,
                "dsProduto": "Livro",
                "idReadequacao": 19221,
                "idEtapa": 2,
                "dsEtapa": "Produção / execução",
                "idPlanilhaItem": 101,
                "dsItem": "Ilustração",
                "idUnidade": 23,
                "dsUnidade": "Mês"
                "qtItem": 5,
                "nrOcorrencia": 4,
                "vlUnitario": 335151.33,
                "qtDias": 10,
                "tpDespesa": 0,
                "tpPessoa": 0,
                "nrContraPartida": 0,
                "nrFonteRecurso": 109,
                "idUFDespesa": 35,
                "idMunicipioDespesa": 355030,
                "dsJustificativa": "Remanejado por isso e aquilo",
                "idAgente": 3466,
                "StAtivo" : "S",
            }
          ]
        }


## Readequação - Finaliza readequação [/readequacao/finalizar]

### Finaliza an&aacute;lise [POST]

+ Parameters
    + idPronac: 205213 (number, required)
    + idParecer: 1205213 (number, required)
    + idTipoReadequacao: 12 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "mensagem": "An&aacute;lise de readequação finalizada."
            }


## Readequação - Obter unidades [/readequacao/planilha-obter-unidades?idPronac={idPronac}]

### Obter unidades - usadas na edição dos itens de planilha
+ Parameters
    + idPronac: 217336 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

        {
          "items": [
            {
                "idUnidade": 25,
                "Sigla": "CH",
                "Descricao": "Cach\u00ea"
            }, {
                "idUnidade": 5,
                "Sigla": "D",
                "Descricao": "Dia"
            }, {
                "idUnidade": 11,
                "Sigla": "filme",
                "Descricao": "Filme"
            }, {
                "idUnidade": 4,
                "Sigla": "FL",
                "Descricao": "Folha"
            }, {
                "idUnidade": 8,
                "Sigla": "H",
                "Descricao": "Hora"
            }, {
                "idUnidade": 19,
                "Sigla": "instal",
                "Descricao": "Instala\u00e7\u00e3o"
            }, {
                "idUnidade": 20,
                "Sigla": "Kg",
                "Descricao": "Kilograma"
            }, {
                "idUnidade": 22,
                "Sigla": "Kg\/m2",
                "Descricao": "Kilograma \/ metro 2"
            }, {
                "idUnidade": 23,
                "Sigla": "mes",
                "Descricao": "M\u00eas"
            }, {
                "idUnidade": 9,
                "Sigla": "M",
                "Descricao": "Metro"
            }, {
                "idUnidade": 16,
                "Sigla": "m2",
                "Descricao": "Metro 2"
            }, {
                "idUnidade": 17,
                "Sigla": "m3",
                "Descricao": "Metro 3"
            }, {
                "idUnidade": 18,
                "Sigla": "m3\/Km",
                "Descricao": "Metro 3 \/ Km"
            }, {
                "idUnidade": 12,
                "Sigla": "min",
                "Descricao": "Minuto"
            }, {
                "idUnidade": 1,
                "Sigla": "N\u00e3o",
                "Descricao": "N\u00e3o Informado"
            }, {
                "idUnidade": 2,
                "Sigla": "Obra",
                "Descricao": "Obra"
            }, {
                "idUnidade": 13,
                "Sigla": "parte",
                "Descricao": "Parte"
            }, {
                "idUnidade": 10,
                "Sigla": "per",
                "Descricao": "Per\u00edodo"
            }, {
                "idUnidade": 15,
                "Sigla": "projet",
                "Descricao": "Projeto"
            }, {
                "idUnidade": 6,
                "Sigla": "RL",
                "Descricao": "Rolo"
            }
          ]
        }

## Readequação - Atualizar item planilha [/readequacao/item-planilha]

### Altera item de planilha [POST]

+ Parameters
    + idReadequacao: 15213 (number, required)
    + idPlanilhaItem: 153 (number, required)
    + idPlanilhaAprovacao: 2315213 (number, required)
    + idPronac: 205213 (number, required)
    + dsJustificativa: "Alteração do item tal, alterando valor unit&aacute;rio e quantidade." (string, required)
    + idUnidade: 213 (number, required)
    + idFonte: 109 (number, required)
    + Ocorrencia: 5 (number, required)
    + Quantidade: 15 (number, required)
    + QtdeDias: 205213 (number, required)
    + ValorUnitario: 3613.50 (number, required)
    + idTipoReadequacao: 22 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "mensagem": "Item atualizado."
            }


## Readequação - calcular resumo da planilha [/readequacao/calcular-resumo-planilha]

### Executa c&aacute;lculos do resumo da planilha [GET]

+ Parameters
    + idPronac: 217336 (number, required)
    + idTipoReadequacao: 22 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "saldoDeclarado": 150636.50,
                "saldoValorUtilizado": 1220.00,
                "valorTotalDisponivelParaUso": -132.24,
                "statusPlanilha": "negativo",
                "PlanilhaReadequadaTotal": 63330.55,
                "PlanilhaAtivaTotal": 64444.55

            }

## Readequação - Reverter alteração de item [/readequacao/reverter-alteracao-item]

### Reverte alteração de item de planilha [POST]

+ Parameters
    + idReadequacao: 15213 (number, required)
    + idPlanilhaItem: 153 (number, required)
    + idPronac: 205213 (number, required)
    + idTipoReadequacao: 22 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "mensagem": "Dados do item revertidos!"
            }

## Readequação - Obt&eacute;m lista de t&eacute;cnicos dispon&iacute;veis para distribuir uma readequação [/readequacao/destinatarios-distribuicao]

### Obt&eacute;m lista de t&eacute;cnicos [GET]

+ Parameters
    + idPronac: 205213 (number, required)
    + vinculada: 262 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body


        {
          "items": [
              {
                  "id":6861,
                  "nome":"Juca Chavez Costa"
              },
              {
                  "id":6153,
                  "nome":"Ernesto Manuel"
              }


## Readequação - Distribui uma readequação [/readequacao/distribuir-readequacao]

### Obt&eacute;m lista de t&eacute;cnicos [POST]

+ Parameters
    + idPronac: 205213 (number, required)
    + idReadequacao: 12262 (number, required)
    + stAtendimento: 'D' (string, required)
    + dsAvaliacao: 'D' (string, required)
    + destinatario: 'Joaquim dos Anjos' (string, required)
    + vinculada: 262 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body


            {
                "mensagem": "Readequação encaminhada para t&eacute;cnico."
            }

## Readequação - Redistribui uma readequação [/readequacao/redistribuir-readequacao]

### Obt&eacute;m lista de t&eacute;cnicos [POST]

+ Parameters
    + idPronac: 205213 (number, required)
    + idReadequacao: 12262 (number, required)
    + stAtendimento: 'D' (string, required)
    + dsAvaliacao: 'D' (string, required)
    + destinatario: 'Joaquim dos Anjos' (string, required)
    + vinculada: 262 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body


            {
                "mensagem": "Readequação redistribu&iacute;da para outro t&eacute;cnico."
            }

## Readequação - Devolve e redistribui uma readequação [/readequacao/devolver-readequacao]

### Obt&eacute;m lista de t&eacute;cnicos [POST]

+ Parameters
    + idPronac: 205213 (number, required)
    + idReadequacao: 12262 (number, required)
    + stAtendimento: 'D' (string, required)
    + dsAvaliacao: 'D' (string, required)
    + destinatario: 'Joaquim dos Anjos' (string, required)
    + vinculada: 262 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body


            {
                "mensagem": "Readequação devolvida para o coordenador."
            }

## Readequação - Devolve uma readequação ao coordenador de acompanhamento [/readequacao/devolver-ao-coordenador]

### Coordenador de parecer de vinculada devolve ao coordenador de acompanhamento [POST]

+ Parameters
    + idPronac: 205213 (number, required)
    + idReadequacao: 12262 (number, required)
    + stAtendimento: 'D' (string, required)
    + dsAvaliacao: 'D' (string, required)
    + destinatario: 'Joaquim dos Anjos' (string, required)
    + vinculada: 262 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body


            {
                "mensagem": "Readequação devolvida para o coordenador de acompanhamento."
            }

## Readequação - T&eacute;cnico ou parecerista declara impedimento e devolve ao coordenador [/readequacao/declarar-impedimento]

### T&eacute;cnico ou parecerista declara impedimento e devolve ao coordenador [POST]

+ Parameters
    + idReadequacao: 12262 (number, required)
    + idTecnico: 12414 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body


            {
                "mensagem": "Readequação devolvida para o coordenador de acompanhamento."
            }

## Readequação - Coordenador de acompanhamento finaliza ciclo de an&aacute;lise [/readequacao/avaliar-ciclo-analise]

### Coordenador de acompanhamento finaliza ciclo de an&aacute;lise [POST]

+ Parameters
    + idReadequacao: 12262 (number, required)
    + idTecnico: 12414 (number, required)


+ Response 200 (application/json; charset=utf-8)

    + Body


            {
                "mensagem": "Ciclo de an&aacute;lise de readequação finalizado."
            }
