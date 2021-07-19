Módulo de Análise
=====================

M&oacute;dulo respons&aacute;vel por realizar análise de projetos

##PRÉ-REQUISITO

RN - 37 (AJUSTAR PROJETO)
- Art. 72 Captado 10% (dez) do valor total aprovado (Custo do Projeto), será oportunizada a adequação do projeto à realidade de execução, não podendo representar aumento do Custo do Projeto e observando as vedações do Art. 42, visando o encaminhamento para análise técnica.

RN - 37.1
- §1º O prazo para adequação do projeto será de 10 (dez) dias corridos, improrrogável, a contar do dia seguinte do seu registro no Salic e envio desta informação pelo sistema.

RN - 37.2
- §2º O dispositivo do caput não se aplica para projetos de proteção do patrimônio material ou imaterial e de acervos, aos planos anuais, aos projetos museológicos, aos projetos de manutenção de corpos estáveis ou de equipamentos culturais, bem como projetos aprovados em editais públicos ou privados com termo de parceria, ou com contratos de patrocínios firmados, que garantam o alcance do índice ou projetos apresentados por instituições criadas pelo patrocinador na forma do §2º do Art. 27 da Lei 8.313/91.

##CASO DE USO

APÓS CAPTADO 10%  valor total aprovado (Custo do Projeto),

1. O sistema por meio de rotina automatizada(**dbo.spLiberarAdequacaoDoProjeto**) enviará e-mail ao proponente informando da oportunidade para adequação do projeto à realidade de execução e ao mesmo tempo alterará a situação do projeto para E90.

2. A alteração da situação do projeto para E90 é a condição para que o sistema liberar na lista de projetos do Escritório Virtual do Proponente a coluna com o botão que permitirá ao proponente abrir o projeto para a adequação do projeto à realidade de execução conforme a regra de negócio RN - 37.

3. O prazo para adequação do projeto será de 10 (dez) dias corridos, improrrogável, a contar do dia seguinte do seu registro no Salic e envio desta informação pelo sistema, conforme regra de negócio RN - 37.1.
A contagem de desse prazo é calculada entre a diferença a DtSituacao da tabela projetos e a data atual.

4. Findo o prazo de 10 dias, o sistema por meio de rotina automatizada alterará novamente a situação do projeto para uma das descritas abaixo:
    1. E12 se o projeto ainda não atingiu 20% ou
    2. B11 se o projeto já atingiu os 20% e encaminhará o projeto para a Unidade Vinculada para a avaliação técnica.

5. A vedação do Art 42 a que se refere a RN - 37 é para esclarecer  que os itens descritos abaixo não poderão sob nenhuma hipótese serem modificados.
    1. PRODUTO PRINCIPAL;
    2. ÁREA CULTURAL
    3. SEGMENTO CULTURAL

##TRIGGERS E PROCEDURES NO BANCO
PROCEDURE dbo.spLiberarAdequacaoDoProjeto
