Variaveis de ambiente para conexão com serviço de NF-e, dentro do arquivo application.ini

Habilita o serviço para uso da NFe:  

``` sh
vim application/configs/application.ini
```

Adicionar a linha:
``` ini
nfe=true  
```
Essa linha e que faz as configurações e habilita o serviço da NF-e em todo o sistema.


A autenticação é feito por ***TOKEN JWT*** para a comunicação com serviço.  
``` ini
jwt.token='xxxxxxxxxxxx'  
```
Essa configuração precisa ser feita no serviço NF-e

URL/Dominio do serviço da NFe:

``` ini
url.hostnfe='https://localhost:8080'
```
Por padrão o projeto sobe configurado com HTTPS, tenha ateno na configuração.

### Resumo das configurações: 
``` ini
nfe=true  
url.hostnfe='https://localhost:8080'
jwt.token='xxxxxxxxxxxx'  

```
