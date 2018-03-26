Módulo de Admissibilidade
=========================

Módulo responsável por realizar a admissibilidade da proposta.

```
    @todo : Complementar esse arquivo com mais informações do módulo.
```

## Avaliação de propostas

Esse é um dos recursos do módulo de admissibilidade no qual permite fazer as seguintes ações na proposta:
* Distribuir
* Avaliar
* Sugerir Enquadramento 
* Arquivar

#### Configurações

Esse módulo exige o uso de CronJobs. CronJobs são rotinas que são executadas automaticamente de acordo com um tempo pré-configurado.
Uma das CronJobs executará uma rotina acessada através da url abaixo:

```
    {ambiente}/admissibilidade/enquadramento-proposta/tratar-avaliacoes-vencidas-componentes-comissao?hash=XPTO
```

Comandos importantes sobre as crontabs:

```
    crontab -l # lista as entradas para crontabs 

    crontab -e # Edita as crontabs para o usuário atual
```

Sugestão de definição para crontab em /etc/crontab:

```
    5  0    * * *   root    wget -o /var/www/cron/salic-minc/propostas-avaliacoes -q http://localhost/admissibilidade/enquadramento-proposta/tratar-avaliacoes-vencidas-componentes-comissao?hash=XPTO
```

Onde o ```hash=XPTO``` deve ser substituído pelo hash definido na propriedade ```cronJobs.proponente.avaliacaoProposta.hash``` do arquivo de configurações da aplicação ```application.ini```.