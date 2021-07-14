HOST: http://localhost:4000

# SALIC API

# Group Projeto

## Projeto - Visualizar projeto [/projeto/projeto/get?idPronac={idPronac}]

+ Parameters
    + idPronac: 14100 (number, required)

### Visualizar projeto [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body
        
            {
                "data": {
                    "idPronac":"141000",
                    "NomeProjeto":"19\u00ba GRAMADO CINE VIDEO",
                    "CgcCPf":"05970767000167",
                    "idPreProjeto":"47356",
                    "Situacao":"K00",
                    "idMecanismo":"1",
                    "Pronac":"117551",
                    "idAgente":"11483",
                    "NomeProponente":"Associa\u00e7\u00e3o de Cultura e Turismo de Gramado",
                    "descricaoSituacao":"Projeto Arquivado",
                    "permissao":false
                },
                "success":"false",
                "msg":"Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar este projeto"
            }



## Projeto - Visualizar projeto completo [/projeto/dados-projeto/get?idPronac={idPronac}]

+ Parameters
    + idPronac: 14100 (number, required)

### Visualizar projeto [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body
        
            {
                "data": {
                    "idPronac":"141000",
                    "NomeProjeto":"19\u00ba GRAMADO CINE VIDEO",
                    "CgcCPf":"05970767000167",
                    "idPreProjeto":"47356",
                    "Situacao":"K00",
                    "idMecanismo":"1",
                    "Pronac":"117551",
                    "idAgente":"11483",
                    "NomeProponente":"Associa\u00e7\u00e3o de Cultura e Turismo de Gramado",
                    "descricaoSituacao":"Projeto Arquivado",
                    "permissao":false
                },
                "success":"false",
                "msg":"Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar este projeto"
            }

