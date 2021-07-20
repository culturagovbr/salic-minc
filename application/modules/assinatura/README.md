Módulo de Assinatura
=========================

#### Sobre

M&oacute;dulo respons&aacute;vel por realizar a admissibilidade da Projetos Culturais.
Este m&oacute;dulo fornece opera&ccedil;&otilde;es b&aacute;sicas para assinatura tais como :

 * Gerenciamento de Assinaturas
 * Visualiza&ccedil;&atilde;o de Projeto
 * Assinar Projeto Cultural
 * Gerar PDF
 * Movimentar Assinatura para pr&oacute;ximo respons&aacute;vel

#### Workflow

Neste m&amp;oacute;dulo &amp;eacute; poss&amp;iacute;vel realizar a Assinatura de Projetos por usu&amp;aacute;rios que est&amp;atilde;o vinculados a sub-secretarias com os perfis que podem ser localizados na tabela do schema &quot;sac&quot;, &quot;TbAtoAdministrativo&quot;.
Nesta tabela &eacute; poss&iacute;vel identificar informa&ccedil;&otilde;es tais como Unidade, Cargo do Assinante, Ordem de assinatura, Tipo do Ato administrativo e al&eacute;m de outras informa&ccedil;&otilde;es.

Um exemplo pr&aacute;tico podemos levantar a seguinte situal&ccedil;&atilde;o para o enquadramento, quando o usu&aacute;rio com perfil Coordenador Geral assinar, caso o projeto seja movimentado, a pr&oacute;xima assinatura deve ser do Secret&aacute;rio.

Na tabela "TbAssinatura" do schema "sac", quando o Projeto Cultural possui todas as assinaturas de acordo com a tabela "TbAtoAdministrativo" &eacute; possível finalizar a assinatura.

O processo de finalização da assinatura &eacute; particular para cada módulo e não fica dentro deste módulo, pois cada módulo apresenta necessidades diferentes tanto para alteração de situaç&otilde;es quanto para o acionamento de outras funcionalidades dentro do sistema.
Portanto cada módulo que necessitar desta funcionalidade precisa implementa-lo de acordo com a necessidade fora deste módulo.

#### Gestão de Atos Administrativos

O Workflow do módulo depente de atos admnistrativos que são armazenados na tabela tbAtoAdministrativo.

Cada Ato Administrativo possui ligação como :
 * Tipo do Ato Administrativo
 * Cargo
 * Perfil
 * Orgão
 * Ordem de assinatura

Para gerir os itens mencionados acima de um Ato Administrativo basta acessar com o perfil "Gestor Salic" (97) o menu:
```Assinatura > Gerir Atos Administrativos```

#### Depend&ecirc;ncias

Este módulo depende da library 'MinC_Assinatura_Assinatura' e utiliza serviços como:
  * MinC_Assinatura_Servico_Assinatura
  * MinC_Assinatura_Servico_Autenticacao
  * MinC_Assinatura_Servico_DocumentoAssinatura
Atualmente essa depend&ecirc;ncia foi criada dentro da
pasta library por limitaç&otilde;es do framework na geração de serviços.
