M&oacute;dulo de An&aacute;lise
=====================

M&oacute;dulo respons&aacute;vel por realizar an&aacute;lise de projetos

##PR&eacute;-REQUISITO

RN - 37 (AJUSTAR PROJETO)
- Art. 72 Captado 10% (dez) do valor total aprovado (Custo do Projeto), ser&aacute; oportunizada a adequação do projeto à realidade de execução, não podendo representar aumento do Custo do Projeto e observando as vedações do Art. 42, visando o encaminhamento para an&aacute;lise t&eacute;cnica.

RN - 37.1
- §1º O prazo para adequação do projeto ser&aacute; de 10 (dez) dias corridos, improrrog&aacute;vel, a contar do dia seguinte do seu registro no Salic e envio desta informação pelo sistema.

RN - 37.2
- §2º O dispositivo do caput não se aplica para projetos de proteção do patrimônio material ou imaterial e de acervos, aos planos anuais, aos projetos museol&oacute;gicos, aos projetos de manutenção de corpos est&aacute;veis ou de equipamentos culturais, bem como projetos aprovados em editais p&uacute;blicos ou privados com termo de parceria, ou com contratos de patroc&iacute;nios firmados, que garantam o alcance do &iacute;ndice ou projetos apresentados por instituições criadas pelo patrocinador na forma do §2º do Art. 27 da Lei 8.313/91.

##CASO DE USO

AP&oacute;S CAPTADO 10%  valor total aprovado (Custo do Projeto),

1. O sistema por meio de rotina automatizada(**dbo.spLiberarAdequacaoDoProjeto**) enviar&aacute; e-mail ao proponente informando da oportunidade para adequação do projeto à realidade de execução e ao mesmo tempo alterar&aacute; a situação do projeto para E90.

2. A alteração da situação do projeto para E90 &eacute; a condição para que o sistema liberar na lista de projetos do Escrit&oacute;rio Virtual do Proponente a coluna com o botão que permitir&aacute; ao proponente abrir o projeto para a adequação do projeto à realidade de execução conforme a regra de neg&oacute;cio RN - 37.

3. O prazo para adequação do projeto ser&aacute; de 10 (dez) dias corridos, improrrog&aacute;vel, a contar do dia seguinte do seu registro no Salic e envio desta informação pelo sistema, conforme regra de neg&oacute;cio RN - 37.1.
A contagem de desse prazo &eacute; calculada entre a diferença a DtSituacao da tabela projetos e a data atual.

4. Findo o prazo de 10 dias, o sistema por meio de rotina automatizada alterar&aacute; novamente a situação do projeto para uma das descritas abaixo:
    1. E12 se o projeto ainda não atingiu 20% ou
    2. B11 se o projeto j&aacute; atingiu os 20% e encaminhar&aacute; o projeto para a Unidade Vinculada para a avaliação t&eacute;cnica.

5. A vedação do Art 42 a que se refere a RN - 37 &eacute; para esclarecer  que os itens descritos abaixo não poderão sob nenhuma hip&oacute;tese serem modificados.
    1. PRODUTO PRINCIPAL;
    2. &aacute;REA CULTURAL
    3. SEGMENTO CULTURAL

##TRIGGERS E PROCEDURES NO BANCO
PROCEDURE dbo.spLiberarAdequacaoDoProjeto
