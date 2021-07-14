Infra t&eacute;cnica e de desenvolvimento SALIC


# C&oacute;digo

    Hist&oacute;rias de uso - backlog

    A1) criacao de branch dev-backlog a partir do MASTER; criacao dos branches dev-bl-fernao e dev-bl-pedro a partir da branch dev-backlog
    A2) ap&oacute;s realizar alteracoes em sua branch, fernao manda suas alteracoes para a dev-backlog
    A3) correcoes de uma funcionalidade sao feitas diretamente na dev-backlog, que e tageada com v1.5.1 e jogada para a master
    A4) ap&oacute;s realizar correcoes em sua branch, pedro faz um merge para pegar as atualizacoes da master (ou da dev-backlog, que estara sincada com a master nesse momento)
    A5) pedro envia suas alteracoes para a dev-backlog
    A6) alteracoes na backlog sao publicadas na producao com a tag v1.5.2

    A                       1          2      3        4      5     6

    ----dev-bl-pedro--------o---o---o---o---o----o-----o---o
                           /                          /    \
    ----dev-bl-fernao-----o---o---o---o------------------o--\--------o
                         /             \            /   /    \      /
    A---dev-backlog-----o---------------o-----o----/---/------o----o
                       /                       \  /   /             \
                      /                         \/                   \
    ----MASTER-------o-v1.5----------------------o-v1.5.1-------------o-v1.5.2--------------------------------------------------------------o-v2.0.0
                      \                          \                     \                                                                   /
                       \                          \                     \                                                                 /
    B---dev-novaIN------o----------------o-a2.0.0--\-------o-a2.0.1------\-------o---------------------o-a2.0.2--------------o-a2.0.3----o
                         \              /\          \     / \             \     / \           \       / \     \             /
    ----dev-IN-rafa-------o---o---o----o--\----------\---/---o---o---o---o-\---/---\---o---o---o-----o   \     \           /
                           \               \          \ /     \             \ /     \                     \     \         /
    ----dev-INsync----------o---------------o----------o-------\-------------o-------\---------------------o     \       /
                                                                \                     \                           \     /
    ----dev-INfernao---------------------------------------------o---o---o---o---o-----o---------------------------o---o


    B                       1            2  3          4    5  6   7         8   9     10     11      12   13     14   15                16


    Hist&oacute;rias de uso - nova IN:

    B1) criacao de branch dev-novaIN a partir do MASTER; criacao dos branches dev-IN-RAFA e dev-INsyn a partir da branch dev-novaIN
    B2) ap&oacute;s realizar alteracoes em sua branch, Rafa manda suas alteracoes para a dev-novaIN, que sao tageadas como versao alpha2.0.0
    B3) responsavel pela branch de sincronizacao atualiza branch com a dev-novaIN
    B4) responsavel pela branch de sincronizacao puxa atualizacoes da branch dev-backlog que foram lancadas para a master
    B5) alteracoes de sync sao enviadas para branch dev-novaIN
    B6) rafa atualiza sua branch recebendo ultimas alteracoes de sync a partir da dev-novaIN
    B7) fernao se junta ao time e faz um branch a partir da ultima versao da dev-novaIN
    B8) novo sync feito com alteracoes jogadas para a master
    B9) alteracoes de sync sao enviadas para dev-novaIN
    B10) fernao realiza alteracoes na sua branch e faz merge com ultimas alteracoes vindas do backlog/sync
    B11) rafa realiza alteracoes na sua branch e faz merge com ultimas alteracoes vindas do backlog/sync
    B12) rafa publica suas alteracoes no dev-novaIN
    B13) sync atualiza c&oacute;digo com base no dev-novaIN
    B14) fernao atualiza c&oacute;digo com base no dev-novaIN
    B15) fernao publica suas alteracoes no dev-novaIN
    B16) alteracoes da dev-novaIN sao publicadas na master

# BANCOS

    ----------
    | dev X  |                             ---------------->  * ambientes
    ----------

    ---------------
    | dev-novaIN  |                        ---------------->  * ambientes
    ---------------

    --------------
    | homolog  X |                         ---------------->    homolog.novosalic.cultura.gov.br
    --------------

    ---------------------
    | homolog-novaIN  X |                  ---------------->    homologn.novosalic-novain.cultura.gov.br
    ---------------------


    ---------------
    | treinamento |                        ---------------->    treinamento.novosalic.cultura.gov.br
    ---------------

    -------------
    | producao  |                          ---------------->    novosalic.cultura.gov.br
    -------------


# Deploy das alterações do banco / versionamento


## Fase 1: criar pasta sql e criar pastas para cada versão em que são adicionados os scripts da versão

    /sql/v1.5.2
    /sql/v1.5.2/tblAgentes.sql
    /sql/v1.5.2/spValidaAgentes.sql
    /sql/v1.5.2/triggerAgentes.sql
    /sql/v1.5.3/tabelaProposta.sql
    /sql/v2.0.0/tblProposta.sql


## Fase 2: versionar sql estrutural (tabelas / procedures / triggers / etc)
## Fase 3: disponibilizar carga inicial

    /db
    /db/dbname
    /db/dbname/table/tabela1.sql
    /db/dbname/table/tabela2.sql
    /db/dbname/table/tabela3.sql
    /db/dbname/trigger/trigger1.sql
    /db/dbname/trigger/trigger2.sql
    /db/dbname/trigger/trigger3.sql
    /db/procedure/sp1.sql
    /db/procedure/sp2.sql
    /db/procedure/sp3.sql
    /db/etc...

    ...
    /db/carga_inicial.sql


