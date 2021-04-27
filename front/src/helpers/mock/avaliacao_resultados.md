0FORMAT: 1A
HOST: http://localhost:4000

# SALIC API

# Group Autenticação

## Login [/autenticacao/index/login2] 

### Login [POST]

+ Request (multipart/form-data; charset=utf-8)

    + Attributes 
        + Login: 239.691.561-49 (string, required)
        + Senha: 123456 (number, required)

+ Response 200 (application/json; charset=utf-8)

    + Attributes (object)
        + status: 1 (number)
        + msg: Login realizado com sucesso! (string)
        + redirect: `/principal` (string)
        + token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NDg5NTMxMjgsImlzcyI6bnVsbCwiZXhwIjoxNTQ5OTUzMTI4LCJuYmYiOjE1NDg5NTMxMjcsImRhdGEiOnsiYXV0aCI6eyJ1c3VfY29kaWdvIjoyMzYsInVzdV9pZGVudGlmaWNhY2FvIjoiMjM5NjkxNTYxNDkgICAgICJ9LCJncnVwb0F0aXZvIjo5M319.6HPj8rB9OH0MPQNHrLyz_ZUzcwo3GS8FQEqRJsK_xC8 (string)

## Trocar Perfil [/autenticacao/perfil/alterarperfil]

### Trocar Perfil [POST]

+ Request (application/x-www-form-urlencoded)
        
    + Attributes 
        + codGrupo: 124 (string, required)
        + codOrgao: 203 (string, required)


+ Response 201 (application/json)

    + Headers

            Location: /principal

## Informações do Usuário Logado [/autenticacao/usuario/usuario/logado]

### Informações do Usuário Logado [GET]

+ Response 200 (application/json, charset=utf-8)

    + Body

            {"data":{"code":200,"items":{"usu_codigo":"236","usu_identificacao":"23969156149","usu_nome":"R\u00f4mulo Menh\u00f4 Barbosa","usu_pessoa":"536","usu_orgao":"251","usu_org_max_superior":"251","grupo_ativo":"126","orgao_ativo":"303"}}}


# Group Sistema

## Página Principal [/principal]

### Página Principal [GET]

+ Response 200 (text/html)

        Tela inicial!

# Group Projeto

## Dados do projeto 209561 [/projeto/dados-projeto/get?idPronac=209561]

### Dados do projeto 209561 [GET]

+ Response 200 (application/json)

    + Body

            {
                "data": {
                    "permissao": "1",
                    "idPronac": "209561",
                    "Pronac": "171313",
                    "NomeProjeto": "FOTOATIVIDADES",
                    "CgcCPf": "03667829000130",
                    "Proponente": "ASSOCIAÇÃO FOTOATIVA",
                    "UfProjeto": "PA",
                    "idMecanismo": "1",
                    "DtSituacao": "2019-01-21 16:15:00",
                    "Processo": "01400.009464/2017-98",
                    "DtInicioCaptacao": "2018-01-01 00:00:00",
                    "DtFimCaptacao": "2018-12-31 00:00:00",
                    "DtInicioExecucao": "2018-07-02 14:27:09",
                    "DtFimExecucao": "2018-11-05 14:27:09",
                    "TipoPortariaVigente": "Prorrogação",
                    "NrPortariaVigente": "0001/18",
                    "DtPublicacaoPortariaVigente": "03/01/2018",
                    "ResumoProjeto": "No &acirc;mbito das comemora&ccedil;&otilde;es do 34&ordm; anivers&aacute;rio da Associca&ccedil;&atilde;o Fotoativa, Fotoatividades integrar&aacute; a programa&ccedil;&atilde;o do X Col&oacute;quio Fotografia e Imagem, compreendendo a realiza&ccedil;&atilde;o de exposi&ccedil;&atilde;o, palestras, leituras de portf&oacute;lio, mostra de proje&ccedil;&atilde;o audiovisual e show em pra&ccedil;a p&uacute;blica. Toda a programa&ccedil;&atilde;o ser&aacute; gratuita e aberta ao grande p&uacute;blico, tecendo reflex&otilde;es acerca da imagem e suas aproxima&ccedil;&otilde;es com outras linguagens art&iacute;sticas.",
                    "ProvidenciaTomada": "Transferência de recursos entre conta captação e conta movimento no valor de R$2.000,00 em 29/08/2018.",
                    "LocalizacaoAtual": "SEFIC/GEAR/SACAV",
                    "vlSolicitadoOriginal": "53900",
                    "vlOutrasFontesPropostaOriginal": "0",
                    "vlTotalPropostaOriginal": "53900",
                    "vlAutorizado": "53900",
                    "vlAutorizadoOutrasFontes": "0",
                    "vlTotalAutorizado": "53900",
                    "vlAdequadoIncentivo": "53900",
                    "vlAdequadoOutrasFontes": "0",
                    "vlTotalAdequado": "53900",
                    "vlHomologadoIncentivo": "53900",
                    "vlHomologadoOutrasFontes": "0",
                    "vlTotalHomologado": "53900",
                    "vlReadequadoIncentivo": "0",
                    "vlReadequadoOutrasFontes": "0",
                    "vlTotalReadequado": "0",
                    "vlCaptado": "13500",
                    "vlTransferido": "0",
                    "vlRecebido": "0",
                    "vlSaldoACaptar": "40400",
                    "PercentualCaptado": "25.05",
                    "vlComprovado": "0",
                    "vlAComprovar": "13500",
                    "PercentualComprovado": "0",
                    "Enquadramento": "Artigo 26",
                    "idPreProjeto": "245047",
                    "idAgente": "7946",
                    "DataFixa": "N&atilde;o",
                    "ProrrogacaoAutomatica": "N&atilde;o",
                    "Area": "Artes Visuais",
                    "Segmento": "Fotografia",
                    "Mecanismo": "Mecenato",
                    "Situacao": "A10 - Pré Análise",
                    "PlanoExecucaoImediata": "Projeto normal",
                    "AgenciaBancaria": "18465",
                    "ContaCaptacao": "000000480835",
                    "ContaMovimentacao": "000000480843",
                    "ContaBancariaLiberada": "Sim",
                    "DtLiberacaoDaConta": "28/08/2018",
                    "DtArquivamento": "",
                    "CaixaInicio": "",
                    "CaixaFinal": "",
                    "dtInicioFase": "2018-11-06 05:00:39",
                    "dtFinalFase": "",
                    "idNormativo": "7",
                    "Normativo": "INSTRUÇÃO NORMATIVA MINC Nº 1/2017\r\n",
                    "dtPublicacaoNormativo": "2017-03-20 00:00:00",
                    "dtRevogacaoNormativo": "2017-11-30 00:00:00",
                    "idFase": "651",
                    "FaseProjeto": "Avaliação dos resultados da ação cultural",
                    "ProponenteInabilitado": "",
                    "EmAnaliseNaCNIC": "",
                    "idUsuarioExterno": "",
                    "isTipoIncentivo": "1",
                    "isProponente": ""
                },
                "success": "true"
            }

## Dados do projeto 217336 [/projeto/dados-projeto/get?idPronac=217336]

### Dados projeto 217336 [GET]

+ Response 200 (application/json)

    + Body

            {
                "data": {
                    "permissao": "1",
                    "idPronac": "217336",
                    "Pronac": "179303",
                    "NomeProjeto": "7º Festival do Japão do Rio Grande do Sul",
                    "CgcCPf": "19695098000177",
                    "Proponente": "Associação do Festival do Japão do Rio Grande do Sul",
                    "UfProjeto": "RS",
                    "idMecanismo": "1",
                    "DtSituacao": "2018-09-03 18:22:52",
                    "Processo": "01400.034615/2017-46",
                    "DtInicioCaptacao": "2018-01-01 00:00:00",
                    "DtFimCaptacao": "2018-12-31 00:00:00",
                    "DtInicioExecucao": "2018-06-01 09:36:29",
                    "DtFimExecucao": "2019-12-31 23:59:59",
                    "TipoPortariaVigente": "Redução",
                    "NrPortariaVigente": "0537/18",
                    "DtPublicacaoPortariaVigente": "15/08/2018",
                    "ResumoProjeto": "O Festival do Jap&atilde;o RS &eacute; um evento cultural realizado anualmente pela Associa&ccedil;&atilde;o do Festival do Jap&atilde;o do Rio Grande do Sul. Em sua 7&ordf; Edi&ccedil;&atilde;o, a ser realizada no ano de 2018, ser&aacute; produzido um document&aacute;rio resgatando as ra&iacute;zes nipo-brasileiras que formaram as col&ocirc;nias japonesas no RS, com a participa&ccedil;&atilde;o dos primeiros imigrantes japonesesque aportaram em 1956. Nestes dois dias o festival celebrar&aacute; os la&ccedil;os culturais entre os dois povos, preservando suas identidades e tradi&ccedil;&otilde;es, oportunizando ao p&uacute;blico conhecer, apreciar e vivenciar os h&aacute;bitos e costumes, a culin&aacute;ria e as mais variadas express&otilde;es art&iacute;sticas e pr&aacute;ticas relacionadas ao cotidiano do povo japon&ecirc;s. Em 2018 o p&uacute;blico estimado para o evento &eacute; de 50 mil pessoas.",
                    "ProvidenciaTomada": "Readequa&ccedil;&atilde;o em an&aacute;lise pela &aacute;rea t&eacute;cnica.",
                    "LocalizacaoAtual": "SEFIC/GEAR/SACAV",
                    "vlSolicitadoOriginal": "341251.2",
                    "vlOutrasFontesPropostaOriginal": "0",
                    "vlTotalPropostaOriginal": "341251.2",
                    "vlAutorizado": "341251.2",
                    "vlAutorizadoOutrasFontes": "0",
                    "vlTotalAutorizado": "341251.2",
                    "vlAdequadoIncentivo": "183500",
                    "vlAdequadoOutrasFontes": "0",
                    "vlTotalAdequado": "183500",
                    "vlHomologadoIncentivo": "161420",
                    "vlHomologadoOutrasFontes": "0",
                    "vlTotalHomologado": "161420",
                    "vlReadequadoIncentivo": "161420",
                    "vlReadequadoOutrasFontes": "0",
                    "vlTotalReadequado": "161420",
                    "vlCaptado": "45000",
                    "vlTransferido": "0",
                    "vlRecebido": "0",
                    "vlSaldoACaptar": "116420",
                    "PercentualCaptado": "27.88",
                    "vlComprovado": "37020",
                    "vlAComprovar": "7980",
                    "PercentualComprovado": "82.27",
                    "Enquadramento": "Artigo 18",
                    "idPreProjeto": "251702",
                    "idAgente": "221725",
                    "DataFixa": "N&atilde;o",
                    "ProrrogacaoAutomatica": "Sim",
                    "Area": "Artes Visuais",
                    "Segmento": "Exposição de Artes Visuais",
                    "Mecanismo": "Mecenato",
                    "Situacao": "E12 - Autorizada a captação residual dos recursos",
                    "PlanoExecucaoImediata": "Projeto  com contratos de patrocínios",
                    "AgenciaBancaria": "27944",
                    "ContaCaptacao": "000000462322",
                    "ContaMovimentacao": "000000462330",
                    "ContaBancariaLiberada": "Sim",
                    "DtLiberacaoDaConta": "31/07/2018",
                    "DtArquivamento": "",
                    "CaixaInicio": "",
                    "CaixaFinal": "",
                    "dtInicioFase": "2018-09-01 05:00:52",
                    "dtFinalFase": "",
                    "idNormativo": "8",
                    "Normativo": "INSTRUÇÃO NORMATIVA MINC Nº 4/2017\r\n",
                    "dtPublicacaoNormativo": "2017-11-30 00:00:00",
                    "dtRevogacaoNormativo": "2017-12-26 00:00:00",
                    "idFase": "650",
                    "FaseProjeto": "Execução do projeto e comprovação dos gastos efetuados",
                    "ProponenteInabilitado": "",
                    "EmAnaliseNaCNIC": "",
                    "idUsuarioExterno": "",
                    "isTipoIncentivo": "1",
                    "isProponente": ""
                },
                "success": "true"
            }


# Group Avaliação de Resultados

## PROJETO-INICIO - Listar Projetos [GET /avaliacao-resultados/projeto-inicio]

+ Response 200 (application/json; charset=utf-8)

    + Attributes (object)
        + data
            + code: 200 (string)
            + items (object)
                + 0
                    + Pronac: 1012121 (string)
                    + PRONAC: 1012121 (string)
                    + NomeProjeto: Criança é Vida - 15 anos (string)
                    + cdSituacao: E68 (string)
                    + Situacao: E68 (string)
                    + UfProjeto: SP (string)
                    + IdPRONAC: 132451 (string)
                    + Prioridade: 0 (string)
                    + idPronac: 132451 (string)
                + 1
                    + Pronac: 1210135 (string)
                    + PRONAC: 1210135 (string)
                    + NomeProjeto: FESTIVAL SERRANO DE DANÇAS TRADICIONAIS 2013 (string)
                    + cdSituacao: E68 (string)
                    + Situacao: E68 (string)
                    + UfProjeto: RS (string)
                    + IdPRONAC: 159131 (string)
                    + Prioridade: 0 (string)
                    + idPronac: 159131 (string)

## FLUXO-PROJETO - Projetos Análise [/avaliacao-resultados/fluxo-projeto?estadoid={estadoId}&idAgente={idAgente}]

### FLUXO-PROJETO - Projetos Análise [GET]

+ Parameters
    + estadoId: 5
    + idAgente: 236

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                    {
                        "id": "2",
                        "idPronac": "136867",
                        "estadoId": "5",
                        "orgao": "1",
                        "grupo": "1",
                        "idAgente": "236",
                        "IdPRONAC": "136867",
                        "AnoProjeto": "11",
                        "Sequencial": "3575",
                        "UfProjeto": "MG",
                        "Area": "3",
                        "Segmento": "32",
                        "Mecanismo": "1",
                        "NomeProjeto": "Série de Concertos Orquestra Pianíssimo",
                        "Processo": "01400008374201194",
                        "CgcCpf": "11861910000185",
                        "Situacao": "E17",
                        "DtProtocolo": "2011-05-04 15:21:35",
                        "DtAnalise": "2011-05-04 15:21:35",
                        "Modalidade": " ",
                        "OrgaoOrigem": "262",
                        "Orgao": "303",
                        "DtSaida": "",
                        "DtRetorno": "2013-10-29 00:00:00",
                        "UnidadeAnalise": " ",
                        "Analista": " ",
                        "DtSituacao": "2018-11-21 16:28:07",
                        "ResumoProjeto": "O projeto prevê a realização de 8 concertos da Orquestra Pianíssimo na cidade de Belo Horizonte/MG, com vistas à formação de público para música erudita/instrumental por meio do repertório clássico para orquestra de câmara.",
                        "ProvidenciaTomada": "Projeto diligenciado na análise de prestação de contas.",
                        "Localizacao": " ",
                        "DtInicioExecucao": "2014-01-07 10:09:09",
                        "DtFimExecucao": "2014-05-31 00:00:00",
                        "SolicitadoUfir": "0",
                        "SolicitadoReal": "366906.0795",
                        "SolicitadoCusteioUfir": "0",
                        "SolicitadoCusteioReal": "0",
                        "SolicitadoCapitalUfir": "0",
                        "SolicitadoCapitalReal": "0",
                        "Logon": "236",
                        "idProjeto": "44995",
                        "PRONAC": "113575",
                        "usu_nome": "Rômulo Menhô Barbosa",
                        "idDocumentoAssinatura": "",
                        "idTipoDoAtoAdministrativo": "",
                        "stEstado": "0",
                        "cdSituacao": "",
                        "idDiligencia": "78570",
                        "DtSolicitacao": "2018-11-21 16:28:07",
                        "DtResposta": "",
                        "stEnviado": "S"
                    }]
                }
            }

+ Request

    + Parameters
        + estadoId: 6
        + idAgente: 236

+ Response 200 (application/json; charset=utf-8)

    + Body 

            {
                "data": {
                    "code": 200,
                    "items": [
                    {
                        "id": "32",
                        "idPronac": "157779",
                        "estadoId": "6",
                        "orgao": "1",
                        "grupo": "1",
                        "idAgente": "236",
                        "IdPRONAC": "157779",
                        "AnoProjeto": "12",
                        "Sequencial": "8843",
                        "UfProjeto": "PR",
                        "Area": "1",
                        "Segmento": "12",
                        "Mecanismo": "1",
                        "NomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "Processo": "01400029889201217",
                        "CgcCpf": "02203539000173",
                        "Situacao": "E17",
                        "DtProtocolo": "2012-10-26 08:14:01",
                        "DtAnalise": "2012-10-26 08:14:01",
                        "Modalidade": " ",
                        "OrgaoOrigem": "262",
                        "Orgao": "303",
                        "DtSaida": "",
                        "DtRetorno": "",
                        "UnidadeAnalise": " ",
                        "Analista": " ",
                        "DtSituacao": "2018-12-13 18:12:57",
                        "ResumoProjeto": "A Fundação Assis Gurgacz promove um Festival Cultural de Artes Integradas para a 3ª idade, com oficinas de Música, Teatro, Dança e apresentações Artísticas, oportunizando a troca de experiências, o desenvolvimento das habilidades artísticas, estimulando a participação desta camada social em atividades de interação cultural, incluindo cursos e workshops para a 3ª idade e monitores culturais que trabalham com esta faixa etária.",
                        "ProvidenciaTomada": "Projeto diligenciado na análise de prestação de contas.",
                        "Localizacao": " ",
                        "DtInicioExecucao": "2014-01-07 10:09:09",
                        "DtFimExecucao": "2014-02-28 00:00:00",
                        "SolicitadoUfir": "0",
                        "SolicitadoReal": "187070.04",
                        "SolicitadoCusteioUfir": "0",
                        "SolicitadoCusteioReal": "0",
                        "SolicitadoCapitalUfir": "0",
                        "SolicitadoCapitalReal": "0",
                        "Logon": "236",
                        "idProjeto": "86273",
                        "PRONAC": "128843",
                        "usu_nome": "Rômulo Menhô Barbosa",
                        "idDocumentoAssinatura": "14526",
                        "idTipoDoAtoAdministrativo": "622",
                        "stEstado": "1",
                        "cdSituacao": "1",
                        "idDiligencia": "78576",
                        "DtSolicitacao": "2018-12-13 16:25:28",
                        "DtResposta": "2018-12-13 19:14:04",
                        "stEnviado": "S"
                    }]
                }
            }

## PROJETO-ASSINATURA - Projetos Histórico [/avaliacao-resultados/projeto-assinatura/estado/historico]

### PROJETO-ASSINATURA - Projetos Histórico [GET]

+ Response 200 (application/json; charset=utf-8)
    + Headers

            X-My-Message-Header: 42

    + Body 

            {
                "data": {
                    "code": 200,
                    "items": [
                    {
                        "IdPRONAC": 134261,
                        "AnoProjeto": "11",
                        "Sequencial": "1058",
                        "UfProjeto": "SC",
                        "Area": "6",
                        "Segmento": "61",
                        "Mecanismo": "1",
                        "NomeProjeto": "Livro:  Arte Catarinense para Crianças e Adolescentes - 2ª edição",
                        "Processo": "01400002178201114",
                        "CgcCpf": "06292251000173",
                        "Situacao": "E27",
                        "DtProtocolo": "2011-02-15 08:58:23",
                        "DtAnalise": "2011-02-15 08:58:23",
                        "Modalidade": " ",
                        "OrgaoOrigem": 262,
                        "Orgao": 303,
                        "DtSaida": null,
                        "DtRetorno": "2011-11-01 00:00:00",
                        "UnidadeAnalise": " ",
                        "Analista": " ",
                        "DtSituacao": "2018-12-21 17:35:22",
                        "ResumoProjeto": "Trata-se de  uma obra de fomento  das artes plásticas de Santa Catarina destinada ao público  escolar , com abordagem informativa, destacando a vida e obra de  20 artistas  plásticos desde a época colonial aos dias  atuais.O material  será apresentado  de forma colorida e dinâmica com brincadeiras, histórias em quadrinhos e animação. Sua primeira edição foi Lançado no Museu  de Arte de SC em 2005.e atualmente  está esgotada e necessita de atualização dos dados.",
                        "ProvidenciaTomada": "Comprova&ccedil;&atilde;o Financeira do Projeto em AnÃ¡lise",
                        "Localizacao": " ",
                        "DtInicioExecucao": "2011-05-06 00:00:00",
                        "DtFimExecucao": "2016-03-31 00:00:00",
                        "SolicitadoUfir": 0,
                        "SolicitadoReal": 104145,
                        "SolicitadoCusteioUfir": 0,
                        "SolicitadoCusteioReal": 0,
                        "SolicitadoCapitalUfir": 0,
                        "SolicitadoCapitalReal": 0,
                        "Logon": 236,
                        "idProjeto": 42333,
                        "PRONAC": "111058"
                    }]
                }
            }

## PLANILHA-APROVADA - Visualizar [/avaliacao-resultados/planilha-aprovada/idPronac/{idPronac}]

+ Parameters
    + idPronac: 134261

### PLANILHA-APROVADA - Visualizar [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body 

            {
                "0": {
                    "etapa": {
                        "4": {
                            "UF": {
                                "42": {
                                    "cidade": {
                                        "420240": {
                                            "itens": {
                                                "4": {
                                                    "3235": {
                                                        "item": "Assistente administrativo",
                                                        "valor": "1500",
                                                        "quantidade": 1,
                                                        "numeroOcorrencias": 1,
                                                        "varlorAprovado": 1500,
                                                        "varlorComprovado": 1500,
                                                        "comprovacaoValidada": 0,
                                                        "idPlanilhaAprovacao": 126856,
                                                        "idPlanilhaItens": 3235,
                                                        "ComprovacaoValidada": 0,
                                                        "stItemAvaliado": "4"
                                                    },
                                                    "3677": {
                                                        "item": "Coordenação geral",
                                                        "valor": "6500",
                                                        "quantidade": 1,
                                                        "numeroOcorrencias": 1,
                                                        "varlorAprovado": 6500,
                                                        "varlorComprovado": 4500,
                                                        "comprovacaoValidada": 0,
                                                        "idPlanilhaAprovacao": 126853,
                                                        "idPlanilhaItens": 3677,
                                                        "ComprovacaoValidada": 0,
                                                        "stItemAvaliado": "4"
                                                    }
                                                },
                                                "todos": {
                                                    "191": {
                                                        "item": "Contador",
                                                        "valor": "800",
                                                        "quantidade": 1,
                                                        "numeroOcorrencias": 1,
                                                        "varlorAprovado": 800,
                                                        "varlorComprovado": 0,
                                                        "comprovacaoValidada": 0,
                                                        "idPlanilhaAprovacao": 126854,
                                                        "idPlanilhaItens": 191,
                                                        "ComprovacaoValidada": 0,
                                                        "stItemAvaliado": null
                                                    },
                                                    "3235": {
                                                        "item": "Assistente administrativo",
                                                        "valor": "1500",
                                                        "quantidade": 1,
                                                        "numeroOcorrencias": 1,
                                                        "varlorAprovado": 1500,
                                                        "varlorComprovado": 1500,
                                                        "comprovacaoValidada": 0,
                                                        "idPlanilhaAprovacao": 126856,
                                                        "idPlanilhaItens": 3235,
                                                        "ComprovacaoValidada": 0,
                                                        "stItemAvaliado": "4"
                                                    },
                                                    "3677": {
                                                        "item": "Coordenação geral",
                                                        "valor": "6500",
                                                        "quantidade": 1,
                                                        "numeroOcorrencias": 1,
                                                        "varlorAprovado": 6500,
                                                        "varlorComprovado": 4500,
                                                        "comprovacaoValidada": 0,
                                                        "idPlanilhaAprovacao": 126853,
                                                        "idPlanilhaItens": 3677,
                                                        "ComprovacaoValidada": 0,
                                                        "stItemAvaliado": "4"
                                                    },
                                                    "5249": {
                                                        "item": "Remuneração para captação de recursos",
                                                        "valor": "4500",
                                                        "quantidade": 1,
                                                        "numeroOcorrencias": 1,
                                                        "varlorAprovado": 4500,
                                                        "varlorComprovado": 0,
                                                        "comprovacaoValidada": 0,
                                                        "idPlanilhaAprovacao": 126857,
                                                        "idPlanilhaItens": 5249,
                                                        "ComprovacaoValidada": 0,
                                                        "stItemAvaliado": null
                                                    },
                                                    "5250": {
                                                        "item": "Elaboração de prestação de contas",
                                                        "valor": "1200",
                                                        "quantidade": 1,
                                                        "numeroOcorrencias": 1,
                                                        "varlorAprovado": 1200,
                                                        "varlorComprovado": 0,
                                                        "comprovacaoValidada": 0,
                                                        "idPlanilhaAprovacao": 126855,
                                                        "idPlanilhaItens": 5250,
                                                        "ComprovacaoValidada": 0,
                                                        "stItemAvaliado": null
                                                    }
                                                }
                                            },
                                            "cidade": "Blumenau",
                                            "cdCidade": 420240
                                        }
                                    },
                                    "Uf": "SC",
                                    "cdUF": 42
                                }
                            },
                            "etapa": "Custos / Administrativos",
                            "cdEtapa": 4
                        }
                    },
                    "produto": "Administração do Projeto",
                    "cdProduto": 0
                }
            }

+ Request
    + Parameters
        + idPronac: 136867

+ Response 200 (application/json; charset=utf-8)

    + Body 

            {
                "0": {
                    "etapa": {
                    "5": {
                        "UF": {
                        "31": {
                            "cidade": {
                            "310620": {
                                "itens": {
                                "todos": {
                                    "200": {
                                    "item": "INSS",
                                    "valor": "2386",
                                    "quantidade": 1,
                                    "numeroOcorrencias": 6,
                                    "varlorAprovado": 14316,
                                    "varlorComprovado": 0,
                                    "comprovacaoValidada": 0,
                                    "idPlanilhaAprovacao": 216279,
                                    "idPlanilhaItens": 200,
                                    "ComprovacaoValidada": 0,
                                    "stItemAvaliado": null
                                    },
                                    "4380": {
                                    "item": "FGTS",
                                    "valor": "955",
                                    "quantidade": 1,
                                    "numeroOcorrencias": 6,
                                    "varlorAprovado": 5730,
                                    "varlorComprovado": 0,
                                    "comprovacaoValidada": 0,
                                    "idPlanilhaAprovacao": 216280,
                                    "idPlanilhaItens": 4380,
                                    "ComprovacaoValidada": 0,
                                    "stItemAvaliado": null
                                    }
                                }
                                },
                                "cidade": "Belo Horizonte",
                                "cdCidade": 310620
                            }
                            },
                            "Uf": "MG",
                            "cdUF": 31
                        }
                        },
                        "etapa": "Recolhimentos",
                        "cdEtapa": 5
                    }
                    },
                    "produto": "Administração do Projeto",
                    "cdProduto": 0
                },
                }

## PROJETO - Informações [/avaliacao-resultados/projeto/idPronac/{idPronac}]

+ Parameters
    + idPronac: 134261

### PROJETO - Informações [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body 

            {
                "data": {
                    "code": 200,
                    "items": {
                    "nomeProjeto": "Livro:  Arte Catarinense para Crianças e Adolescentes - 2ª edição",
                    "vlTotalComprovar": 43323.95,
                    "vlAprovado": 88245,
                    "vlComprovado": 44921.05,
                    "pronac": "111058",
                    "diligencia": false,
                    "estado": {
                        "id": 1,
                        "idPronac": 134261,
                        "estadoId": 14,
                        "orgao": 1,
                        "grupo": 1,
                        "idAgente": 236
                    },
                    "documento": []
                    }
                }
                }

+ Request
    + Parameters
        + idPronac: 136867

+ Response 200 (application/json; charset=utf-8)

    + Body 

            {
                "data": {
                    "code": 200,
                    "items": {
                    "nomeProjeto": "Série de Concertos Orquestra Pianíssimo",
                    "vlTotalComprovar": 278255.09,
                    "vlAprovado": 349399.78,
                    "vlComprovado": 71144.69,
                    "pronac": "113575",
                    "diligencia": false,
                    "estado": {
                        "id": 2,
                        "idPronac": 136867,
                        "estadoId": 12,
                        "orgao": 1,
                        "grupo": 1,
                        "idAgente": 236
                    },
                    "documento": []
                    }
                }
            }

## EMISSÃO-PARECER-REST - Visualizar - Aba Análise [/avaliacao-resultados/emissao-parecer-rest/idPronac/{idPronac}]

+ Parameters
    + idPronac: 136867

### EMISSÃO-PARECER-REST - Visualizar Parecer [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": {
                    "consolidacaoComprovantes": {
                        "qtTotalComprovante": 62,
                        "qtComprovantesValidadosProjeto": 0,
                        "qtComprovantesRecusadosProjeto": 0,
                        "qtComprovantesNaoAvaliados": 62,
                        "vlComprovadoProjeto": 71144.69,
                        "vlComprovadoValidado": 0,
                        "vlComprovadoRecusado": 0,
                        "vlNaoComprovado": -1044.69
                    },
                    "projeto": {
                        "IdPRONAC": 136867,
                        "AnoProjeto": "11",
                        "Sequencial": "3575",
                        "UfProjeto": "MG",
                        "Area": "3",
                        "Segmento": "32",
                        "Mecanismo": "1",
                        "NomeProjeto": "Série de Concertos Orquestra Pianíssimo",
                        "Processo": "01400008374201194",
                        "CgcCpf": "11861910000185",
                        "Situacao": "D53",
                        "DtProtocolo": "2011-05-04 15:21:35",
                        "DtAnalise": "2011-05-04 15:21:35",
                        "Modalidade": " ",
                        "OrgaoOrigem": 262,
                        "Orgao": 272,
                        "DtSaida": null,
                        "DtRetorno": "2013-10-29 00:00:00",
                        "UnidadeAnalise": " ",
                        "Analista": " ",
                        "DtSituacao": "2019-01-14 15:34:05",
                        "ResumoProjeto": "O projeto prevê a realização de 8 concertos da Orquestra Pianíssimo na cidade de Belo Horizonte/MG, com vistas à formação de público para música erudita/instrumental por meio do repertório clássico para orquestra de câmara.",
                        "ProvidenciaTomada": "Projeto encaminhado para o setor de elabora&ccedil;&atilde;o de portaria",
                        "Localizacao": " ",
                        "DtInicioExecucao": "2014-01-07 10:09:09",
                        "DtFimExecucao": "2014-05-31 00:00:00",
                        "SolicitadoUfir": 0,
                        "SolicitadoReal": 366906.0795,
                        "SolicitadoCusteioUfir": 0,
                        "SolicitadoCusteioReal": 0,
                        "SolicitadoCapitalUfir": 0,
                        "SolicitadoCapitalReal": 0,
                        "Logon": 236,
                        "idProjeto": 44995
                    },
                    "proponente": {
                        "tipoPessoa": "Pessoa Jur&iacute;dica",
                        "Nome": "PIANÍSSIMO PRODUÇOES MUSICAIS E ARTÍSTICAS",
                        "Endereco": "do Contorno - 4614 - sala 603 - Funcionários",
                        "CgcCpf": "11861910000185",
                        "Uf": "MG",
                        "Cidade": "Belo Horizonte",
                        "Esfera": null,
                        "Responsavel": "César Timóteo de Oliveira Santos",
                        "Cep": "30110028",
                        "Administracao": null,
                        "Utilidade": "1",
                        "Direito": 2
                    },
                    "parecer": {
                        "idAvaliacaoFinanceira": 35,
                        "idPronac": 136867,
                        "dtAvaliacaoFinanceira": "2019-01-14 03:24:08",
                        "tpAvaliacaoFinanceira": 1,
                        "siManifestacao": "A",
                        "dsParecer": "<p>Aprovação - Teste Assinatura Diretor e Secretário</p>",
                        "idUsuario": 236
                    },
                    "objetoParecer": {
                        "DtEnvioDaPrestacaoContas": null,
                        "stResultadoAvaliacao": "P",
                        "dsManifestacaoObjeto": "APROVADO COM RESSALVAS",
                        "dsParecerDeCumprimentoDoObjeto": "<b>PARECER DE AVALIA&Ccedil;&Atilde;O T&Eacute;CNICA DO CUMPRIMENTO DO OBJETO</B><br/><br/>A análise técnica referente ao cumprimento do objeto e objetivos do projeto foram consolidadas no parecer nº 276/2015 e encontra-se anexada ao Salic.<b>ORIENTA&Ccedil;&Otilde;ES</B><br/><br/> <b>CONCLUS&Atilde;O DO PARECER DE AVALIA&Ccedil;&Atilde;O T&Eacute;CNICA QUANTO &Agrave; EXECU&Ccedil;&Atilde;O DO OBJETO E DOS OBJETIVOS DO PROJETO</B><br/><br/>Conclui-se pelo cumprimento parcial do objeto. Registra-se que foram observados de forma satisfatória os requisitos expostos nos incisos de I a III e V a VIII do art. 80 da IN nº 01/2013, no entanto, o projeto não pôde ser executado integralmente em razão da não captação total dos recursos."
                    }
                    }
                }
            }

+ Request

    + Parameters
        + idPronac: 157779

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": {
                    "consolidacaoComprovantes": {
                        "qtTotalComprovante": 20,
                        "qtComprovantesValidadosProjeto": 19,
                        "qtComprovantesRecusadosProjeto": 1,
                        "qtComprovantesNaoAvaliados": 0,
                        "vlComprovadoProjeto": 64677.16,
                        "vlComprovadoValidado": 63632.16,
                        "vlComprovadoRecusado": 1045,
                        "vlNaoComprovado": 234.39
                    },
                    "projeto": {
                        "IdPRONAC": 157779,
                        "AnoProjeto": "12",
                        "Sequencial": "8843",
                        "UfProjeto": "PR",
                        "Area": "1",
                        "Segmento": "12",
                        "Mecanismo": "1",
                        "NomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "Processo": "01400029889201217",
                        "CgcCpf": "02203539000173",
                        "Situacao": "D53",
                        "DtProtocolo": "2012-10-26 08:14:01",
                        "DtAnalise": "2012-10-26 08:14:01",
                        "Modalidade": " ",
                        "OrgaoOrigem": 262,
                        "Orgao": 272,
                        "DtSaida": null,
                        "DtRetorno": null,
                        "UnidadeAnalise": " ",
                        "Analista": " ",
                        "DtSituacao": "2019-01-14 12:03:27",
                        "ResumoProjeto": "A Fundação Assis Gurgacz promove um Festival Cultural de Artes Integradas para a 3ª idade, com oficinas de Música, Teatro, Dança e apresentações Artísticas, oportunizando a troca de experiências, o desenvolvimento das habilidades artísticas, estimulando a participação desta camada social em atividades de interação cultural, incluindo cursos e workshops para a 3ª idade e monitores culturais que trabalham com esta faixa etária.",
                        "ProvidenciaTomada": "Projeto encaminhado para o setor de elabora&ccedil;&atilde;o de portaria",
                        "Localizacao": " ",
                        "DtInicioExecucao": "2014-01-07 10:09:09",
                        "DtFimExecucao": "2014-02-28 00:00:00",
                        "SolicitadoUfir": 0,
                        "SolicitadoReal": 187070.04,
                        "SolicitadoCusteioUfir": 0,
                        "SolicitadoCusteioReal": 0,
                        "SolicitadoCapitalUfir": 0,
                        "SolicitadoCapitalReal": 0,
                        "Logon": 236,
                        "idProjeto": 86273
                    },
                    "proponente": {
                        "tipoPessoa": "Pessoa Jur&iacute;dica",
                        "Nome": "FUNDAÇÃO ASSIS GURGACZ",
                        "Endereco": "das Torres - Santa Cruz",
                        "CgcCpf": "02203539000173",
                        "Uf": "PR",
                        "Cidade": "Cascavel",
                        "Esfera": null,
                        "Responsavel": "salete Gerardi de Lima Chrun",
                        "Cep": "85806095",
                        "Administracao": null,
                        "Utilidade": "2",
                        "Direito": 35
                    },
                    "parecer": {
                        "idAvaliacaoFinanceira": 30,
                        "idPronac": 157779,
                        "dtAvaliacaoFinanceira": "2018-12-27 03:07:43",
                        "tpAvaliacaoFinanceira": 1,
                        "siManifestacao": "A",
                        "dsParecer": "<p>Teste do parecer!</p>",
                        "idUsuario": 236
                    },
                    "objetoParecer": {
                        "DtEnvioDaPrestacaoContas": null,
                        "stResultadoAvaliacao": "A",
                        "dsManifestacaoObjeto": "APROVADO",
                        "dsParecerDeCumprimentoDoObjeto": "<b>PARECER DE AVALIA&Ccedil;&Atilde;O T&Eacute;CNICA DO CUMPRIMENTO DO OBJETO</B><br/><br/>Na concepção deste relatório, para averiguação do cumprimento do objeto e objetivos do projeto, bem como para aferição do retorno social e dos impactos socioculturais, econômicos e ambientais, tomou-se como base de análise o Plano Básico de Divulgação, o Plano de Distribuição e os documentos/informações apresentados pela entidade proponente visando à prestação de contas da execução. 1)Empregos e qualificações decorrentes do projeto: conforme o Relatório Físico - Anexo IV, foram contratados serviços de profissionais da área de execução do projeto, bem como de serviços de apoio. 2)Medidas preventivas quanto a impactos ambientais: não se aplica ao projeto. 3)Medidas de acessibilidade física: de acordo com o clipping de imprensa, o evento foi realizado no Auditório do Bloco 4 da Faculdade Assis Gurgacz, edificação acessível para portadores de necessidades especiais, conforme demonstrado por fotografias que retratam rampas, elevadores, banheiros adaptados, bebedouros acessíveis e estacionamento com vagas reservadas para portadores de necessidades especiais. Além disso, a própria temática do festival está relacionada à promoção da acessibilidade, ao desenvolver atividades voltadas para a formação cultural e a inclusão de idosos. 4)Medidas de estímulo à fruição e à democratização de acesso do público: conforme o clipping de imprensa, as inscrições para o evento foram gratuitas. 5)Plano de Divulgação: na análise dos documentos encaminhados pela entidade proponente, verifica-se que os itens executados do Plano Básico de Divulgação, com exceção do jornal/encarte de jornal, atenderam ao disposto no art. 47 do Decreto n. 5.761, de 27 de abril de 2006, que define a obrigatoriedade da inserção da logomarca do Ministério da Cultura nos produtos culturais provenientes de recursos incentivados. Observa-se, ainda, que o material atendeu aos requisitos do Manual de Uso das Marcas do Pronac, exibindo a marca da Lei de Incentivo à Cultura e a assinatura do Ministério da Cultura, acompanhada da marca do Governo Federal. 6)Plano de Distribuição: conforme relatório do proponente, o festival beneficiou cerca de 450 pessoas, entre alunos de terceira idade, cuidadores de idosos e representantes de programas sociais de entidades de 37 municípios da região oeste do estado de Paraná. Verificou-se, por meio da análise de registros fotográficos e videográficos, que o projeto alcançou um número representativo de beneficiários e obteve repercussão e alcance social significativos.<b>ORIENTA&Ccedil;&Otilde;ES</B><br/><br/> Não se aplica ao projeto.<b>CONCLUS&Atilde;O DO PARECER DE AVALIA&Ccedil;&Atilde;O T&Eacute;CNICA QUANTO &Agrave; EXECU&Ccedil;&Atilde;O DO OBJETO E DOS OBJETIVOS DO PROJETO</B><br/><br/>Diante do exposto, tendo em vista que o proponente apresentou a prestação de contas final em 04/04/2014, que as contas vinculadas ao projeto encontram-se zeradas e bloqueadas para novos aportes, o que evidencia a finalização da execução do projeto, CONCLUI-SE QUE O OBJETO E OBJETIVOS FORAM ALCANÇADOS, conforme demonstrado na análise dos documentos anexados ao Salic. Assim, sugere-se a remessa dos autos à Coordenação-Geral de Prestação de Contas/DIC/SEFIC/MinC para análise financeira da prestação de contas."
                    }
                    }
                }
            }

## HISTÓRICO - Encaminhamentos [/avaliacao-resultados/historico/idPronac/{idPronac}]

+ Parameters
    + idPronac: 136867

### HISTÓRICO - Encaminhamentos [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "PRONAC": "113575",
                            "NomeProjeto": "Série de Concertos Orquestra Pianíssimo",
                            "dtInicioEncaminhamento": "11/21/2018",
                            "dsJustificativa": "ContinuaÃ§Ã£o!",
                            "NomeOrigem": "Rômulo Menhô Barbosa",
                            "NomeDestino": "Rômulo Menhô Barbosa"
                        }
                    ]
                }
            }

+ Request

    + Parameters
        + idPronac: 157779

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                        "PRONAC": "128843",
                        "NomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "dtInicioEncaminhamento": "06/17/2015",
                        "dsJustificativa": ".",
                        "NomeOrigem": "Pablo.S. Santiago",
                        "NomeDestino": "Marina de Oliveira"
                        },
                        {
                        "PRONAC": "128843",
                        "NomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "dtInicioEncaminhamento": "06/19/2015",
                        "dsJustificativa": ".",
                        "NomeOrigem": "Pablo.S. Santiago",
                        "NomeDestino": "Marina de Oliveira"
                        },
                        {
                        "PRONAC": "128843",
                        "NomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "dtInicioEncaminhamento": "06/19/2015",
                        "dsJustificativa": ".",
                        "NomeOrigem": "Marina de Oliveira",
                        "NomeDestino": "Pablo.S. Santiago"
                        }
                    ]
                }
            }

+ Request

    + Parameters
        + idPronac: 134261

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                    {
                        "PRONAC": "111058",
                        "NomeProjeto": "Livro:  Arte Catarinense para Crianças e Adolescentes - 2ª edição",
                        "dtInicioEncaminhamento": "11/20/2018",
                        "dsJustificativa": "lkjlkj ljklkjj",
                        "NomeOrigem": "Rômulo Menhô Barbosa",
                        "NomeDestino": "Douglas V. C. Alves"
                    },
                    {
                        "PRONAC": "111058",
                        "NomeProjeto": "Livro:  Arte Catarinense para Crianças e Adolescentes - 2ª edição",
                        "dtInicioEncaminhamento": "12/21/2018",
                        "dsJustificativa": "bla",
                        "NomeOrigem": "Rômulo Menhô Barbosa",
                        "NomeDestino": "Rômulo Menhô Barbosa"
                    },
                    {
                        "PRONAC": "111058",
                        "NomeProjeto": "Livro:  Arte Catarinense para Crianças e Adolescentes - 2ª edição",
                        "dtInicioEncaminhamento": "12/21/2018",
                        "dsJustificativa": "ok",
                        "NomeOrigem": "Rômulo Menhô Barbosa",
                        "NomeDestino": "Rômulo Menhô Barbosa"
                    }
                    ]
                }
            }

## LAUDO FINAL - Análise [/avaliacao-resultados/laudo/index?estadoId=10]

### LAUDO - Aba em Análise [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "IdPronac": 149520,
                            "NomeProjeto": "Formação de Novos Escritores Infantis - Write in Canela",
                            "PRONAC": "120718",
                            "idAvaliacaoFinanceira": 34,
                            "idPronac": 149520,
                            "dtAvaliacaoFinanceira": "2019-01-31 05:45:15",
                            "tpAvaliacaoFinanceira": 1,
                            "siManifestacao": "A",
                            "dsParecer": "<p>dA  X XA S CSA CSAC </p>",
                            "idUsuario": 236,
                            "dsResutaldoAvaliacaoObjeto": "A",
                            "usu_nome": "Rômulo Menhô Barbosa",
                            "usu_codigo": 236
                        }]
                }
            }


## LAUDO FINAL - Assinar [/avaliacao-resultados/laudo/index?estadoId=12]

### LAUDO - Aba em Assinar [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                            "IdPronac": 153408,
                            "NomeProjeto": "São Marcos - espetáculos culturais",
                            "PRONAC": "124545",
                            "idAvaliacaoFinanceira": 3,
                            "idPronac": 153408,
                            "dtAvaliacaoFinanceira": "2018-11-29 11:16:11",
                            "tpAvaliacaoFinanceira": 1,
                            "siManifestacao": "R",
                            "dsParecer": "123 123 123 ",
                            "idUsuario": 236,
                            "dsResutaldoAvaliacaoObjeto": "R",
                            "usu_nome": "Rômulo Menhô Barbosa",
                            "usu_codigo": 236
                        },
                        {
                            "IdPronac": 154267,
                            "NomeProjeto": "1o. Edital de Teatro do CESCB",
                            "PRONAC": "125360",
                            "idAvaliacaoFinanceira": 4,
                            "idPronac": 154267,
                            "dtAvaliacaoFinanceira": "2018-11-29 11:16:25",
                            "tpAvaliacaoFinanceira": 1,
                            "siManifestacao": "A",
                            "dsParecer": "123 123 123 ",
                            "idUsuario": 236,
                            "dsResutaldoAvaliacaoObjeto": "A",
                            "usu_nome": "Rômulo Menhô Barbosa",
                            "usu_codigo": 236
                        }
                    ]
                }
            }

## LAUDO FINAL - Finalizados [/avaliacao-resultados/laudo/index?estadoId=14]

### LAUDO - Aba Finalizados [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                        }
                    ]
                }
            }


## LAUDO FINAL [/avaliacao-resultados/laudo/index?estadoId=null]

### LAUDO - Null [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                        {
                        }
                    ]
                }
            }

## LAUDO FINAL [/avaliacao-resultados/laudo/get?idPronac=149520]

### LAUDO - idPronac 149520 [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": {
                    "idLaudoFinal": 39,
                    "siManifestacao": "A",
                    "dsLaudoFinal": "<p>Teste assinatura </p>"
                    }
                }
            }           

## DILIGÊNCIAS [/avaliacao-resultados/diligencia?idPronac={idPronac}&situacao={situacao}&tpDiligencia={tpDiligencia}]

+ Parameters
    + idPronac: 136867
    + situacao: E17
    + tpDiligencia: 147

### DILIGÊNCIA - Aba Análise [GET]

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                    {
                        "nomeProjeto": "Série de Concertos Orquestra Pianíssimo",
                        "pronac": "113575",
                        "stProrrogacao": null,
                        "idDiligencia": 78570,
                        "dataSolicitacao": "2018-11-21 16:28:07",
                        "dataResposta": "2019-01-07 09:31:04",
                        "Solicitacao": "Coisa testando!",
                        "Resposta": "ok!<br />\r\n",
                        "idCodigoDocumentosExigidos": null,
                        "idTipoDiligencia": 174,
                        "stEnviado": "S",
                        "tipoDiligencia": "Diligência na prestação de contas",
                        "produto": null,
                        "Arquivos": []
                    },
                    {
                        "nomeProjeto": "Série de Concertos Orquestra Pianíssimo",
                        "pronac": "113575",
                        "stProrrogacao": null,
                        "idDiligencia": 78630,
                        "dataSolicitacao": "2019-01-07 09:32:40",
                        "dataResposta": "2019-01-14 15:22:41",
                        "Solicitacao": "<p>blablablablabla</p>",
                        "Resposta": "Resposta",
                        "idCodigoDocumentosExigidos": null,
                        "idTipoDiligencia": 174,
                        "stEnviado": "S",
                        "tipoDiligencia": "Diligência na prestação de contas",
                        "produto": null,
                        "Arquivos": []
                    },
                    {
                        "nomeProjeto": "Série de Concertos Orquestra Pianíssimo",
                        "pronac": "113575",
                        "stProrrogacao": null,
                        "idDiligencia": 8728,
                        "dataSolicitacao": "2011-05-05 19:50:00",
                        "dataResposta": "2011-05-07 15:40:49",
                        "Solicitacao": "<p>A Funarte foi designada para realizar a an&aacute;lise do <strong>Projeto 113575 - S&eacute;rie de Concertos Orquestra Pian&iacute;ssimo 2011</strong>, encaminhado por V.Sa. Para que possamos dar continuidade &agrave; an&aacute;lise t&eacute;cnica necessitamos das seguintes informa&ccedil;&otilde;es. O prazo de resposta &eacute; de 20 dias (art. 93, &sect; 1&ordm;, Instru&ccedil;&atilde;o Normativa n. 1, de 05/10/2010):</p>\r\n<p><strong>Descri&ccedil;&atilde;o da orquestra</strong><br />. Quantos m&uacute;sicos possui a orquestra no total e em cada naipe?<br />. Em todos os concertos a orquestra vai manter a mesma forma&ccedil;&atilde;o?</p>\r\n<p><strong>M&uacute;sicos</strong><br />. M&uacute;sicos, com or&ccedil;amento de R$ 43.200,00, confirmar: s&atilde;o 12 m&uacute;sicos de cordas atuando em 8 concertos?<br />. M&uacute;sicos, com or&ccedil;amento de R$ 43.200,00, esclarecer: este or&ccedil;amento inclui o cach&ecirc; do spalla?<br />. M&uacute;sicos, com or&ccedil;amento de R$ 12.000,00, confirmar: s&atilde;o 8 m&uacute;sicos de sopro atuando em 4 concertos (este n&uacute;mero est&aacute; na Justificativa do Proponente)?<br />. M&uacute;sicos, com or&ccedil;amento de R$ 12.000,00, esclarecer: este or&ccedil;amento inclui o cach&ecirc; dos chefes de naipe (violino, viola, violoncelo)?</p>\r\n<p><strong>Chefes de naipe</strong><br />. M&uacute;sicos, com or&ccedil;amento de R$ 19.200,00, confirmar: s&atilde;o 4 chefes de naipe em 8 concertos (este n&uacute;mero est&aacute; na Justificativa do Proponente)?<br />. M&uacute;sicos, com or&ccedil;amento de R$ 19.200,00, esclarecer: o \"servi&ccedil;o de chefe de naipe\" inclui o cach&ecirc; do concerto?<br />. Spalla, com or&ccedil;amento de R$ 9.000,00, esclarecer: o \"servi&ccedil;o de spalla da orquestra\" inclui o cach&ecirc; do concerto?</p>\r\n<p><strong>Divulga&ccedil;&atilde;o MinC</strong><br />. O proponente deve atender &agrave;s instru&ccedil;&otilde;es do Manual de Uso de Marcas publicado no Di&aacute;rio Oficial de 26/04/2011. <br />. No programa, como em todas as pe&ccedil;as de divulga&ccedil;&atilde;o, &eacute; preciso constar o cr&eacute;dito na forma estabelecida para projetos enquadrados no art. 18 da Lei 8.313/91: \"Minist&eacute;rio da Cultura apresenta...\" (art. 47, Decreto 5761/2006).</p>\r\n<p>OBSERVA&Ccedil;&Atilde;O IMPORTANTE: Solicitamos que a resposta &agrave; dilig&ecirc;ncia seja inserida no corpo desta mensagem, para que possamos ter acesso &agrave;s informa&ccedil;&otilde;es. Favor n&atilde;o enviar documentos \"anexados\", pois os mesmos n&atilde;o podem ser abertos e/ou visualizados pelo sistema SALIC na fase da an&aacute;lise t&eacute;cnica.</p>\r\n<p>&nbsp;</p>",
                        "Resposta": "<p>Seguem as informa&ccedil;&otilde;es solicitadas.</p>\r\n<p><strong>Descri&ccedil;&atilde;o da orquestra:</strong></p>\r\n<p>-\r\n A orquestra possui um total de 17 m&uacute;sicos, sendo 5 primeiros violinos, 4\r\n segundos violinos, 4 violas, 3 violoncelos e 1 contrabaixo. Deste \r\ntotal, 12 ser&atilde;o m&uacute;sicos de fila, 1 spalla, 1 chefe de naipe de segundos \r\nviolinos, 1 chefe de naipe de viola, 1 chefe de naipe de violoncelo e 1 \r\nchefe de naipe de contrabaixo. Essa &eacute; a \r\nforma&ccedil;&atilde;o b&aacute;sica que atuar&aacute; nos 8 concertos propostos.</p>\r\n<p>- Em 4 concertos propostos,a orquestra base de cordas ter&aacute; a adi&ccedil;&atilde;o \r\nde um naipe de sopros (madeiras), composto por 8 instrumentistas, de \r\nacordo com o repert&oacute;rio a ser executado.</p>\r\n<p><strong>M&uacute;sicos:</strong></p>\r\n<p>- O\r\n or&ccedil;amento de R$ 43.200,00 &eacute; referente a 12 m&uacute;sicos de cordas atuando em\r\n 8 concertos. Neste grupo n&atilde;o est&atilde;o inclu&iacute;dos os cach&ecirc;s do spalla e dos \r\nchefes de naipe, que constam em outro itens or&ccedil;ament&aacute;rios.</p>\r\n<p>- O \r\nitem or&ccedil;ament&aacute;rio de R$12.000,00 &eacute; referente a 8 m&uacute;sicos de sopro \r\natuando em 4 concertos, por&eacute;m nesse item n&atilde;o est&atilde;o inclusos os chefes de\r\n naipe (violino, viola, violoncelo e contrabaixo). O item or&ccedil;ament&aacute;rio \r\nque se refere &agrave; remunera&ccedil;&atilde;o dos chefes de naipe tem o valor de R$ \r\n19.200,00, referente a atua&ccedil;&atilde;o destes em 8 concertos.</p>\r\n<p><strong>Chefes de naipe:</strong></p>\r\n<p>-\r\n A remunera&ccedil;&atilde;o de m&uacute;sicos com valor de R$19.200,00 constante do \r\nor&ccedil;amento &eacute; referente ao pagamento de 4 chefes de naipe de cordas para a\r\n realiza&ccedil;&atilde;o dos 8 concertos propostos.</p>\r\n<p>- O servi&ccedil;o de chefe de \r\nnaipe inclui a realiza&ccedil;&atilde;o de todos os ensaios e dos 8 concertos \r\npropostos, estando, portanto, inclu&iacute;do o cach&ecirc; dos concertos no valor \r\ntotal proposto de R$ 19.200,00.</p>\r\n<p>- O servi&ccedil;o de spalla inclui a realiza&ccedil;&atilde;o de todos os ensaios e \r\ndos 8 concertos propostos, estando, portanto, inclu&iacute;do o cach&ecirc; dos \r\nconcertos no valor total proposto de R$ 9.000,00.</p>\r\n<p><strong>Divulga&ccedil;&atilde;o MinC:</strong></p>\r\n<p>-\r\n Atenderemos a todas as instru&ccedil;&otilde;es do Manual de Uso de Marcas de \r\n26/04/2011. Constar&aacute; no programa e em todas as pe&ccedil;as de divulga&ccedil;&atilde;o o \r\ncr&eacute;dito do Minist&eacute;rio da Cultura da forma proposta no Decreto 5761/2006 \r\n(\"Minist&eacute;rio da Cultura apresenta...\").</p>\r\n<p>&nbsp;</p>",
                        "idCodigoDocumentosExigidos": null,
                        "idTipoDiligencia": 124,
                        "stEnviado": "S",
                        "tipoDiligencia": "Diligência na Análise Técnica Inicial",
                        "produto": "Apresentação Musical",
                        "Arquivos": []
                    }
                    ]
                }
            }

+ Request
    + Parameters
        + idPronac: 157779
        + situacao: E17
        + tpDiligencia: 147

+ Response 200 (application/json; charset=utf-8)

    + Body

            {
                "data": {
                    "code": 200,
                    "items": [
                    {
                        "nomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "pronac": "128843",
                        "stProrrogacao": null,
                        "idDiligencia": 51955,
                        "dataSolicitacao": "2015-08-26 15:53:36",
                        "dataResposta": "2015-09-08 16:55:23",
                        "Solicitacao": "<p>\r\n\tPrezado proponente,</p>\r\n<p>\r\n\tEm an&aacute;lise da presta&ccedil;&atilde;o de contas, constataram-se ocorr&ecirc;ncias que podem ensejar sua reprova&ccedil;&atilde;o. Os documentos impugnados retornaram ao seu perfil e aqui elencamos as ocorr&ecirc;ncias encontradas em cada um, junto com as respectivas sugest&otilde;es de provid&ecirc;ncias a serem tomadas para sanar as impropriedades, de acordo com o n&uacute;mero do item da Rela&ccedil;&atilde;o de Pagamentos. Para responder a esta dilig&ecirc;ncia, no caso de substitui&ccedil;&atilde;o dos comprovantes fiscais, dirija-se ao item impugnado. No caso de envio de outros documentos, como o comprovante de pagamento da GRU, utilize o campo de resposta abaixo. Ressalta-se que, em caso de recolhimento ao Fundo Nacional da Cultura, o valor a ser devolvido por GRU, atualizado monetariamente conforme o art. 91 da Instru&ccedil;&atilde;o Normativa MinC 1/2013, constam nas sugest&otilde;es de provid&ecirc;ncias elencadas em cada item. As instru&ccedil;&otilde;es para gera&ccedil;&atilde;o da GRU constam ao final desta dilig&ecirc;ncia.</p>\r\n<p>\r\n\t1. Item 9 - INSS. O recolhimento foi de ISSQN e a rubrica &eacute; de INSS. Al&eacute;m disso, nota fiscal apresentada, que gerou a obriga&ccedil;&atilde;o do INSS, est&aacute; fora do prazo de execu&ccedil;&atilde;o. O valor desta nota tamb&eacute;m est&aacute; sendo motivo de glosa, vide item 4 desta dilig&ecirc;ncia. Prop&otilde;e-se a restitui&ccedil;&atilde;o do valor de R$ 60,73.</p>\r\n<p>\r\n\t2. Item 1 - Assistentes. No recibo de pagamento aut&ocirc;nomo n&atilde;o h&aacute; descri&ccedil;&atilde;o do servi&ccedil;o prestado, nem consta assinatura por parte do prestador de servi&ccedil;o. Prop&otilde;e-se que seja encaminhado novo recibo com a inser&ccedil;&atilde;o do servi&ccedil;o prestado e devidamente assinado pelo credor.</p>\r\n<p>\r\n\t3. Item 2 - Assistentes. No recibo de pagamento aut&ocirc;nomo n&atilde;o h&aacute; descri&ccedil;&atilde;o do servi&ccedil;o prestado, nem consta assinatura por parte do prestador de servi&ccedil;o. Prop&otilde;e-se que seja encaminhado novo recibo com a inser&ccedil;&atilde;o do servi&ccedil;o prestado e devidamente assinado pelo credor.</p>\r\n<p>\r\n\t4. Item 7 &ndash; Grupos folcl&oacute;ricos. A nota fiscal emitida em 24/03/2014, est&aacute; fora do prazo de execu&ccedil;&atilde;o do projeto, findo em 28/02/2014. Esta nota fiscal foi citada no item 1 desta dilig&ecirc;ncia. Prop&otilde;e-se a restitui&ccedil;&atilde;o do valor de R$ 1.153,86.</p>\r\n<p>\r\n\tCabe registrar que a documenta&ccedil;&atilde;o solicitada dever&aacute; ser encaminhada no prazo m&aacute;ximo de 20 dias, a contar do envio deste e-mail. Qualquer d&uacute;vida, estamos &agrave; disposi&ccedil;&atilde;o por meio do e-mail prestacaodecontas.incentivo@cultura.gov.br ou pelo telefone (61) 2024-2090.</p>\r\n<p>\r\n\tINSTRU&Ccedil;&Otilde;ES PARA EMISS&Atilde;O DA GRU:<br />\r\n\t- Acessar o link: http://consulta.tesouro.fazenda.gov.br/gru_novosite/gru_simples.asp<br />\r\n\t- Selecionar a op&ccedil;&atilde;o SIAFI /GRU - Guia de Recolhimento da Uni&atilde;o/GRU Simples;<br />\r\n\t- Preencher os seguintes campos da GRU:<br />\r\n\t. Unidade Favorecida: informar a Unidade Gestora (340001) e Gest&atilde;o (00001);<br />\r\n\t. N&uacute;mero de Refer&ecirc;ncia da GRU: informar o n&uacute;mero do PRONAC do projeto a que se refere &agrave; devolu&ccedil;&atilde;o;<br />\r\n\t. C&oacute;digo de recolhimento: informar o c&oacute;digo 20082-4;<br />\r\n\t. Dados do contribuinte/proponente: informar o CNPJ ou CPF do proponente e o respectivo nome;<br />\r\n\t- Imprimir e efetuar o pagamento da guia no Banco do Brasil.</p>\r\n<p>\r\n\tAtenciosamente,</p>\r\n<p>\r\n\tPABLO SILVA SANTIAGO<br />\r\n\tChefe da Divis&atilde;o de An&aacute;lise e Apoio T&eacute;cnico<br />\r\n\tCIFAT/CGEPC/DIC/SEFIC/MINIST&Eacute;RIO DA CULTURA<br />\r\n\tE-mail: pablo.santiago@cultura.gov.br<br />\r\n\t&nbsp;</p>\r\n",
                        "Resposta": "Prezado Sr. Pablo,<br />\r\nEm resposta a dilig&ecirc;ncia datada em 26/08/2015, segue nossas considera&ccedil;&otilde;es:<br />\r\n1. Item 9 - INSS. O recolhimento foi de ISSQN e a rubrica &eacute; de INSS. Al&eacute;m disso, nota fiscal apresentada, que gerou a obriga&ccedil;&atilde;o do INSS, est&aacute; fora do prazo de execu&ccedil;&atilde;o. O valor desta nota tamb&eacute;m est&aacute; sendo motivo de glosa, vide item 4 desta dilig&ecirc;ncia. Prop&otilde;e-se a restitui&ccedil;&atilde;o do valor de R$ 60,73.<br />\r\nResposta: Comprovante de restitui&ccedil;&atilde;o constante do anexo.<br />\r\n2. Item 1 - Assistentes. No recibo de pagamento aut&ocirc;nomo n&atilde;o h&aacute; descri&ccedil;&atilde;o do servi&ccedil;o prestado, nem consta assinatura por parte do prestador de servi&ccedil;o. Prop&otilde;e-se que seja encaminhado novo recibo com a inser&ccedil;&atilde;o do servi&ccedil;o prestado e devidamente assinado pelo credor.<br />\r\nResposta: Novo recibo com a inser&ccedil;&atilde;o do servi&ccedil;o prestado e devidamente assinado pelo credor, constante do anexo.<br />\r\n3. Item 2 - Assistentes. No recibo de pagamento aut&ocirc;nomo n&atilde;o h&aacute; descri&ccedil;&atilde;o do servi&ccedil;o prestado, nem consta assinatura por parte do prestador de servi&ccedil;o. Prop&otilde;e-se que seja encaminhado novo recibo com a inser&ccedil;&atilde;o do servi&ccedil;o prestado e devidamente assinado pelo credor.<br />\r\nResposta: Novo recibo com a inser&ccedil;&atilde;o do servi&ccedil;o prestado e devidamente assinado pelo credor, constante do anexo.<br />\r\n4. Item 7 &ndash; Grupos folcl&oacute;ricos. A nota fiscal emitida em 24/03/2014, est&aacute; fora do prazo de execu&ccedil;&atilde;o do projeto, findo em 28/02/2014. Esta nota fiscal foi citada no item 1 desta dilig&ecirc;ncia. Prop&otilde;e-se a restitui&ccedil;&atilde;o do valor de R$ 1.153,86.<br />\r\nResposta:Comprovante de restitui&ccedil;&atilde;o constante do anexo.<br />\r\nQualquer d&uacute;vida estamos a disposi&ccedil;&atilde;o.<br />\r\nAtenciosamente<br />\r\nAssessoria de Projetos<br />\r\nFunda&ccedil;&atilde;o Assis Gurgacz",
                        "idCodigoDocumentosExigidos": null,
                        "idTipoDiligencia": 174,
                        "stEnviado": "S",
                        "tipoDiligencia": "Diligência na prestação de contas",
                        "produto": null,
                        "Arquivos": [
                        {
                            "idArquivo": 744371,
                            "nmArquivo": "Diligência Pronac 128843.pdf",
                            "dtEnvio": "08/09/2015 16:55:03",
                            "idDiligencia": 51955
                        }
                        ]
                    },
                    {
                        "nomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "pronac": "128843",
                        "stProrrogacao": null,
                        "idDiligencia": 52585,
                        "dataSolicitacao": "2015-09-23 09:06:19",
                        "dataResposta": "2015-09-25 10:04:31",
                        "Solicitacao": "<p>\r\n\tPrezado proponente,</p>\r\n<p>\r\n\t<br />\r\n\tInformo que, para que seja validado o comprovante de despesa referente ao item 2 da Dilig&ecirc;ncia, encaminhado em sua resposta, &eacute; necess&aacute;rio que seja anexado em substitui&ccedil;&atilde;o ao comprovante de despesa impugnado, ou seja, &eacute; necess&aacute;rio inserir tal comprovante no item de custo a ele referente. Vale ainda informar que o comprovante referente ao item 3 n&atilde;o foi encaminhado, e deve seguir as mesmas instru&ccedil;&otilde;es relativas ao item 2.</p>\r\n<p>\r\n\t<br />\r\n\tEstou &agrave; disposi&ccedil;&atilde;o para sanar poss&iacute;veis d&uacute;vidas, por meio do e-mail leticia.goncalves@cultura.gov.br.</p>\r\n<p>\r\n\tAtenciosamente,</p>\r\n<p>\r\n\t<br />\r\n\tLET&Iacute;CIA MOREIRA DA S. GON&Ccedil;ALVES<br />\r\n\tT&eacute;cnica de N&iacute;vel Superior<br />\r\n\tCGEPC/DIC/SEFIC/MINIST&Eacute;RIO DA CULTURA<br />\r\n\tE-mail: leticia.goncalves@cultura.gov.br</p>\r\n",
                        "Resposta": "Prezada Let&iacute;cia,<br />\r\nInformamos que, anexamos os comprovantes de despesa do item 2 e 3 em substitui&ccedil;&atilde;o ao comprovante de despesa impugnado, no item de custo a ele referente.<br />\r\nQualquer d&uacute;vida estamos a disposi&ccedil;&atilde;o.<br />\r\n<br />\r\nAtenciosamente<br />\r\n<br />\r\nLucas Prates Chiarello<br />\r\nAssessoria de Projetos",
                        "idCodigoDocumentosExigidos": null,
                        "idTipoDiligencia": 174,
                        "stEnviado": "S",
                        "tipoDiligencia": "Diligência na prestação de contas",
                        "produto": null,
                        "Arquivos": []
                    },
                    {
                        "nomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "pronac": "128843",
                        "stProrrogacao": null,
                        "idDiligencia": 56676,
                        "dataSolicitacao": "2016-01-27 14:36:46",
                        "dataResposta": "2016-02-03 10:15:43",
                        "Solicitacao": "<p>\r\n\tPrezado Proponente,</p>\r\n<p>\r\n\tEm an&aacute;lise da presta&ccedil;&atilde;o de contas do seu projeto n&atilde;o encontramos comprovante de despesa anexado ao Salic que demonstre o gasto com o cheque 850020 no valor de R$ 306,64.</p>\r\n<p>\r\n\tRecomendamos que encaminhe em anexo a sua resposta &agrave; dilig&ecirc;ncia o comprovante devidamente identificado, no termos da reda&ccedil;&atilde;o contida no &sect; 1&ordm; do artigo 83 da Instru&ccedil;&atilde;o Normativa n&ordm; 1/2013.</p>\r\n<p>\r\n\tAtenciosamente,</p>\r\n<p>\r\n\t<br />\r\n\tLET&Iacute;CIA MOREIRA DA S. GON&Ccedil;ALVES<br />\r\n\tT&eacute;cnica de N&iacute;vel Superior<br />\r\n\tCGEPC/DIC/SEFIC/MINIST&Eacute;RIO DA CULTURA<br />\r\n\tE-mail: leticia.goncalves@cultura.gov.br</p>\r\n",
                        "Resposta": "Prezada, Let&iacute;ca Moreira da S. Gon&ccedil;alves!<br />\r\nConforme solicita&ccedil;&atilde;o, encaminhamos comprovante de despesa do gasto com o cheque 850020 no valor de R$ 306,64 referente a GRU de devolu&ccedil;&atilde;o de saldo remanescente. Informo que enviamos o mesmo na presta&ccedil;&atilde;o de contas por meio f&iacute;sico.<br />\r\nDeste modo, nos colocamos a disposi&ccedil;&atilde;o para maiores esclarecimentos caso seja necess&aacute;rio.<br />\r\n<br />\r\nAtenciosamente<br />\r\n<br />\r\nAssessoria de Projetos<br />\r\nFunda&ccedil;&atilde;o Assis Gurgacz",
                        "idCodigoDocumentosExigidos": null,
                        "idTipoDiligencia": 174,
                        "stEnviado": "S",
                        "tipoDiligencia": "Diligência na prestação de contas",
                        "produto": null,
                        "Arquivos": [
                        {
                            "idArquivo": 900214,
                            "nmArquivo": "Comprovante de despasa R$ 306,64.pdf",
                            "dtEnvio": "03/02/2016 10:08:05",
                            "idDiligencia": 56676
                        }
                        ]
                    },
                    {
                        "nomeProjeto": "FESTIVAL CULTURAL DE ARTES  INTEGRADAS - FESTFAG",
                        "pronac": "128843",
                        "stProrrogacao": null,
                        "idDiligencia": 78576,
                        "dataSolicitacao": "2018-12-13 16:25:28",
                        "dataResposta": "2018-12-13 19:14:04",
                        "Solicitacao": "<p>Ã k AÃ£ Ã¢ _ jiocsnaÃ§k Ã  Ã¡ { }</p>",
                        "Resposta": "coisa",
                        "idCodigoDocumentosExigidos": null,
                        "idTipoDiligencia": 174,
                        "stEnviado": "S",
                        "tipoDiligencia": "Diligência na prestação de contas",
                        "produto": null,
                        "Arquivos": []
                    }
                    ]
                }
                }

# Group Prestação de Contas

## Informações do Projeto [/prestacao-contas/visualizar-projeto/dados-projeto?idPronac={idPronac}]

+ Parameters
    + idPronac (string, required)

### Informações do Projeto [GET]

+ Request Informações Prestação de Contas (application/json)
    + Attributes
        + idPronac: 134261

+ Response 200 (application/json; charset=utf-8)

    + Body 

            {"consolidacaoPorProduto":{"lines":[{"dsProduto":"Administra&ccedil;&atilde;o do Projeto","qtComprovantes":"2","vlComprovado":"6.000,00","PercComprovado":"13,36"},
            {"dsProduto":"Livro","qtComprovantes":"14","vlComprovado":"38.921,05","PercComprovado":"86,64"}],"cols":{"dsProduto":{"name":"Produto","class":""},
            "qtComprovantes":{"name":"Qtde. Comprovantes","class":"right-align"},"vlComprovado":{"name":"Valor Comprovado","class":"right-align"},"PercComprovado":{"name":"% Comprovado","class":"right-align"}}
            ,"title":"COMPROVA\u00c7\u00c3O CONSOLIDADA POR PRODUTO","tfoot":{"qtComprovantes":16,"vlComprovado":"44.921,05","dsProduto":"Total"}},"consolidadoPorEtapa":{"lines":[{"Descricao":"Pr\u00e9-Produ\u00e7\u00e3o \/ Prepara\u00e7\u00e3o",
            "qtComprovantes":"1","vlComprovado":"3.500,00","PercComprovado":"7,79"},{"Descricao":"Produ\u00e7\u00e3o \/ Execu\u00e7\u00e3o","qtComprovantes":"11","vlComprovado":"33.817,50","PercComprovado":"75,28"},{"Descricao":"Divulga\u00e7\u00e3o \/ Comercializa\u00e7\u00e3o",
            "qtComprovantes":"2","vlComprovado":"1.603,55","PercComprovado":"3,57"},{"Descricao":"Custos \/ Administrativos","qtComprovantes":"2","vlComprovado":"6.000,00","PercComprovado":"13,36"}],"cols":{"Descricao":{"name":"Etapa","class":"left-align"},"qtComprovantes":{"name":"Qtde. Comprovantes",
            "class":"right-align"},"vlComprovado":{"name":"Valor Comprovado","class":"right-align"},"PercComprovado":{"name":"% Comprovado","class":"right-align"}},"title":"COMPROVA\u00c7\u00c3O CONSOLIDADA POR ETAPA","tfoot":{"qtComprovantes":16,"vlComprovado":"44.921,05","Descricao":"Total"}},
            "maioresItensComprovados":{"lines":[{"Descricao":"Impress\u00e3o","qtComprovantes":"3","vlComprovado":"18.021,50","PercComprovado":"40,12"},{"Descricao":"Arte-finalista","qtComprovantes":"1","vlComprovado":"5.200,00","PercComprovado":"11,58"},{"Descricao":"Coordena\u00e7\u00e3o geral",
            "qtComprovantes":"1","vlComprovado":"4.500,00","PercComprovado":"10,02"},{"Descricao":"Pesquisa","qtComprovantes":"1","vlComprovado":"3.500,00","PercComprovado":"7,79"},{"Descricao":"Coordena\u00e7\u00e3o gr\u00e1fica","qtComprovantes":"2","vlComprovado":"2.500,00","PercComprovado":"5,57"},
            {"Descricao":"Revis\u00e3o de texto","qtComprovantes":"2","vlComprovado":"2.500,00","PercComprovado":"5,57"},{"Descricao":"Redator","qtComprovantes":"1","vlComprovado":"1.996,00","PercComprovado":"4,44"},{"Descricao":"Fotografia art\u00edstica (fot\u00f3grafo, tratamento, revela\u00e7\u00e3o, etc.)",
            "qtComprovantes":"1","vlComprovado":"1.800,00","PercComprovado":"4,01"},{"Descricao":"Digitaliza\u00e7\u00e3o","qtComprovantes":"1","vlComprovado":"1.800,00","PercComprovado":"4,01"},{"Descricao":"Assistente administrativo","qtComprovantes":"1","vlComprovado":"1.500,00","PercComprovado":"3,34"},
            {"Descricao":"Cartaz","qtComprovantes":"1","vlComprovado":"1.020,00","PercComprovado":"2,27"},{"Descricao":"Convite\r\n","qtComprovantes":"1","vlComprovado":"583,55","PercComprovado":"1,30"}],"cols":{"Descricao":{"name":"Item Or\u00e7amentario"},"qtComprovantes":{"name":"Qtde. Comprovantes","class":"right-align"},
            "vlComprovado":{"name":"Valor Comprovado","class":"right-align"},"PercComprovado":{"name":"% Comprovado","class":"right-align"}},"title":"MAIORES ITENS OR\u00c7AMENTARIOS COMPROVADOS","tfoot":{"qtComprovantes":16,"vlComprovado":"44.921,05","Descricao":"Total"}},"comprovacaoConsolidadaUfMunicipio":{"lines":[{"UF":"SC",
            "qtComprovantes":"16","Municipio":"Blumenau","vlComprovado":"44.921,05","PercComprovado":"100,00"}],"cols":{"UF":{"name":"UF"},"qtComprovantes":{"name":"Qtde. Comprovantes","class":"right-align"},"Municipio":{"name":"Municipio","class":"center-align"},"vlComprovado":{"name":"Valor Comprovado","class":"right-align"},
            "PercComprovado":{"name":"% Comprovado","class":"right-align"}},"title":"COMPROVA\u00c7\u00c3O CONSOLIDADA POR UF E MUNICIPIO","tfoot":{"qtComprovantes":16,"vlComprovado":"44.921,05","UF":"Total"}},"maioresComprovacaoTipoDocumento":{"lines":[{"tpDocumento":"Recibo de Pagamento","nrComprovante":"000.009.357","nmFornecedor":"Jacy de Castro Higgie ME",
            "qtComprovacoes":"1","vlComprovado":"10.000,00","PercComprovado":"22,26"},{"tpDocumento":"Recibo de Pagamento","nrComprovante":"000.009.426","nmFornecedor":"Jacy de Castro Higgie ME","qtComprovacoes":"1","vlComprovado":"8.000,00","PercComprovado":"17,81"},{"tpDocumento":"Recibo de Pagamento","nrComprovante":"18","nmFornecedor":"PAULO ESCALEIRA DA SILVA",
            "qtComprovacoes":"1","vlComprovado":"5.200,00","PercComprovado":"11,58"},{"tpDocumento":"Recibo de Pagamento","nrComprovante":"23","nmFornecedor":"GILBERTO DA SILVA SANTOS","qtComprovacoes":"4","vlComprovado":"4.820,00","PercComprovado":"10,73"},{"tpDocumento":"Recibo de Pagamento","nrComprovante":"17","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"1",
            "vlComprovado":"4.500,00","PercComprovado":"10,02"},{"tpDocumento":"Recibo de Pagamento","nrComprovante":"19","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"1","vlComprovado":"3.500,00","PercComprovado":"7,79"}, {"tpDocumento":"Recibo de Pagamento","nrComprovante":"24","nmFornecedor":"GILBERTO DA SILVA SANTOS","qtComprovacoes":"2","vlComprovado":"2.383,55",
            "PercComprovado":"5,31"},{"tpDocumento":"RPA","nrComprovante":"Jus 002","nmFornecedor":"Instituto de Artes Integradas de Blumenau","qtComprovacoes":"1","vlComprovado":"2.000,00","PercComprovado":"4,45"},{"tpDocumento":"RPA","nrComprovante":"001","nmFornecedor":"Instituto de Artes Integradas de Blumenau","qtComprovacoes":"1","vlComprovado":"1.996,00","PercComprovado":"4,44"},
            {"tpDocumento":"Recibo de Pagamento","nrComprovante":"21","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"1","vlComprovado":"1.500,00","PercComprovado":"3,34"}],"cols":{"tpDocumento":{"name":"Tipo Documento"},"nrComprovante":{"name":"Nr. Comprovante"},"nmFornecedor":{"name":"Fornecedor"},"qtComprovacoes":{"name":"Qtde. Comprovantes","class":"right-align"},
            "vlComprovado":{"name":"Valor Comprovado","class":"right-align"},"PercComprovado":{"name":"% Comprovado","class":"right-align"}},"title":"MAIORES COMPROVA\u00c7\u00d5ES POR TIPO DE DOCUMENTOS COMPROBAT\u00d3RIOS","tfoot":{"qtComprovacoes":"14","vlComprovado":"43.899,55","tpDocumento":"Total"}},"comprovacaoTipoDocumentoPagamento":{"lines":[{"tpFormaDePagamento":"Cheque",
            "nrDocumentoDePagamento":"8550012","nmFornecedor":"Jacy de Castro Higgie ME","qtComprovacoes":"1","vlComprovado":"10.000,00","PercComprovado":"22,26"},{"tpFormaDePagamento":"Cheque","nrDocumentoDePagamento":"850014","nmFornecedor":"Jacy de Castro Higgie ME","qtComprovacoes":"1","vlComprovado":"8.000,00","PercComprovado":"17,81"},{"tpFormaDePagamento":"Cheque",
            "nrDocumentoDePagamento":"850005","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"1","vlComprovado":"5.200,00","PercComprovado":"11,58"},{"tpFormaDePagamento":"Cheque","nrDocumentoDePagamento":"850013","nmFornecedor":"GILBERTO DA SILVA SANTOS","qtComprovacoes":"4","vlComprovado":"4.820,00","PercComprovado":"10,73"},{"tpFormaDePagamento":"Cheque",
            "nrDocumentoDePagamento":"850002","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"1","vlComprovado":"4.500,00","PercComprovado":"10,02"},{"tpFormaDePagamento":"Cheque","nrDocumentoDePagamento":"850","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"1","vlComprovado":"3.500,00","PercComprovado":"7,79"},{"tpFormaDePagamento":"Cheque",
            "nrDocumentoDePagamento":"850015","nmFornecedor":"GILBERTO DA SILVA SANTOS","qtComprovacoes":"2","vlComprovado":"2.383,55","PercComprovado":"5,31"},{"tpFormaDePagamento":"Cheque","nrDocumentoDePagamento":"8500007","nmFornecedor":"Instituto de Artes Integradas de Blumenau","qtComprovacoes":"1","vlComprovado":"2.000,00","PercComprovado":"4,45"},
            {"tpFormaDePagamento":"Cheque","nrDocumentoDePagamento":"850006","nmFornecedor":"Instituto de Artes Integradas de Blumenau","qtComprovacoes":"1","vlComprovado":"1.996,00","PercComprovado":"4,44"},{"tpFormaDePagamento":"Cheque","nrDocumentoDePagamento":"850010","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"1","vlComprovado":"1.500,00","PercComprovado":"3,34"}],
            "cols":{"tpFormaDePagamento":{"name":"Tipo Documento"},"nrDocumentoDePagamento":{"name":"Nr. Comprovante"},"nmFornecedor":{"name":"Fonecedor"},"qtComprovacoes":{"name":"Qtde. Comprovantes","class":"right-align"},"vlComprovado":{"name":"Valor Comprovado","class":"right-align"},"PercComprovado":{"name":"% Comprovado","class":"right-align"}},"title":"MAIORES COMPROVA\u00c7\u00d5ES POR TIPO DE DOCUMENTOS DE PAGAMENTO",
            "tfoot":{"qtComprovacoes":14,"vlComprovado":"43.899,55","tpFormaDePagamento":"Total"}},"maioresFornecedoresProjeto":{"lines":[{"nrCNPJCPF":"83.061.234\/0001-76","nmFornecedor":"Jacy de Castro Higgie ME","qtComprovacoes":"2","vlComprovado":"18.000,00","PercComprovado":"40,07"},{"nrCNPJCPF":"15.244.838\/0001-06","nmFornecedor":"PAULO ESCALEIRA DA SILVA","qtComprovacoes":"5",
            "vlComprovado":"15.700,00","PercComprovado":"34,95"},{"nrCNPJCPF":"20.237.650\/0001-62","nmFornecedor":"GILBERTO DA SILVA SANTOS","qtComprovacoes":"6","vlComprovado":"7.203,55","PercComprovado":"16,04"},{"nrCNPJCPF":"06.292.251\/0001-73","nmFornecedor":"Instituto de Artes Integradas de Blumenau","qtComprovacoes":"2","vlComprovado":"3.996,00","PercComprovado":"8,90"},
            {"nrCNPJCPF":"00.000.000\/5934-00","nmFornecedor":"BANCO DO BRASIL SA","qtComprovacoes":"1","vlComprovado":"21,50","PercComprovado":"0,05"}],"cols":{"nrCNPJCPF":{"name":"CNPJ\/CPF"},"nmFornecedor":{"name":"Fornecedor"},"qtComprovacoes":{"name":"Qtde. Comprova\u00e7\u00f5es","class":"right-align"},"vlComprovado":{"name":"Valor Comprovado","class":"right-align"},
            "PercComprovado":{"name":"% Comprovado","class":"right-align"}},"title":"MAIORES FORNECEDORES DO PROJETO","tfoot":{"qtComprovacoes":"30","vlComprovado":"44.964,95","tpFormaDePagamento":"Total","nrCNPJCPF":"Total"}},"fornecedorItemProjeto":{"lines":[{"nrCNPJCPF":"06.292.251\/0001-73","nmFornecedor":"Instituto de Artes Integradas de Blumenau",
            "Etapa":"Produ\u00e7\u00e3o \/ Execu\u00e7\u00e3o","vlComprovado":"1.996,00","PercComprovado":"4,44"},{"nrCNPJCPF":"06.292.251\/0001-73","nmFornecedor":"Instituto de Artes Integradas de Blumenau","Etapa":"Produ\u00e7\u00e3o \/ Execu\u00e7\u00e3o","vlComprovado":"2.000,00","PercComprovado":"4,45"}],"cols":{"nrCNPJCPF":{"name":"CNPJ\/CPF"},
            "nmFornecedor":{"name":"Fornecedor"},"Etapa":{"name":"Etapa"},"vlComprovado":{"name":"Valor Comprovado","class":"right-align"},"PercComprovado":{"name":"% Comprovado","class":"right-align"}},"title":"PROPONENTE FORNECEDOR DE ITEM PARA O PROJETO","tfoot":{"qtComprovacoes":32,"vlComprovado":"4.040,96","tpFormaDePagamento":"Total","nrCNPJCPF":"Total"}},
            "itensOrcamentariosImpugnados":{"lines":[],"cols":{"NomeProjeto":{"name":"Projeto"},"Produto":{"name":"Produto"},"Etapa":{"name":"Etapa"},"Item":{"name":"Item"},"Documento":{"name":"Documento"},"nrComprovante":{"name":"Nr. Comprovante"},"tpFormaDePagamento":{"name":"Forma de Pagamento"},"nrDocumentoDePagamento":{"name":"Documento de Pagamento"},
            "dsJustificativa":{"name":"Justificativa"},"vlComprovado":{"name":"Valor Comprovado","class":"right-align"}},"title":"ITENS OR\u00c7AMENT\u00c1RIOS IMPUGNADOS NA AVALIA\u00c7\u00c3O FINANCEIRA","tfoot":{"NomeProjeto":"Total","vlComprovado":"0,00"}}}


