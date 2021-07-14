M&oacute;dulo de Admissibilidade
=========================

M&oacute;dulo respons&aacute;vel por realizar a admissibilidade da proposta.

```
    @todo : Complementar esse arquivo com mais informações do m&oacute;dulo.
```

## Avaliação de propostas

Esse &eacute; um dos recursos do m&oacute;dulo de admissibilidade no qual permite fazer as seguintes ações na proposta:
* Distribuir
* Avaliar
* Sugerir Enquadramento
* Arquivar

#### Configurações

Esse m&oacute;dulo exige o uso de CronJobs. CronJobs são rotinas que são executadas automaticamente de acordo com um tempo pr&eacute;-configurado.
Uma das CronJobs executar&aacute; uma rotina acessada atrav&eacute;s da url abaixo:

```
    {ambiente}/admissibilidade/enquadramento-proposta/tratar-avaliacoes-vencidas-componentes-comissao?hash=XPTO
```

Comandos importantes sobre as crontabs:

```
    crontab -l # lista as entradas para crontabs

    crontab -e # Edita as crontabs para o usu&aacute;rio atual
```

Sugestão de definição para crontab em /etc/crontab:

```
    5  0    * * *   root    wget -o /var/www/cron/salic-minc/propostas-avaliacoes -q http://localhost/admissibilidade/enquadramento-proposta/tratar-avaliacoes-vencidas-componentes-comissao?hash=XPTO
```

Onde o ```hash=XPTO``` deve ser substitu&iacute;do pelo hash definido na propriedade ```cronJobs.proponente.avaliacaoProposta.hash``` do arquivo de configurações da aplicação ```application.ini```.
