M&oacute;dulo de Projeto
=================

M&oacute;dulo respons&aacute;vel por ter funcionalidades vinculadas a Projeto.


# Planilha Orçament&aacute;ria
 Quando a proposta &eacute; transformada em projeto, enviada para parecer t&eacute;cnico, o sistema faz c&oacute;pia da tabela tbPlanilhaProposta
 para tbPlanilhaProjeto o parecerista faz os ajustes nesta tabela.

 Quando encaminha o projeto para CNIC o sistema faz c&oacute;pia da tabela tbPlanilhaProjeto para tbPlanilhaAprovação.


 1. Planilha Aprovada:

 1.1. Tipo CO - planilha do componente da comissão (CNIC) (aprovada monocraticamente);

 1.2. Tipo SE - planilha aprovada por decisão da plen&aacute;ria, a plen&aacute;ria discordou da 'CO' e aprovou a 'SE';

 1.3. Tipo SR - planilha de readequação (remanejamento, complementação ou redução) - proponente que solicita

     - a vinculada ou CNIC aprova as alterações do tipo 'SR', ativando com stAtivo = 'S'

 1.4  Tipo RP - planilha remanejada sem anuência do MinC - remanejamento de 50% feita pelo proponente.

    - não precisa ser aprovada, o proponente altera e automaticamente fica com stAtivo = 'S'

