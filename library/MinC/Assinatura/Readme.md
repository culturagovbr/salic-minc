Library Assinatura de Projetos Culturais
===============

### Sobre

Tem como responsabilidade fornecer recursos de assinatura de projetos culturais.

### Configura&ccedil;&otilde;es

Como futuramente al&eacute;m do usu&aacute;rio e senha ser&aacute; poss&iacute;vel assinar projetos utilizando outras 
formas de autentica&ccedil;&atilde;o, foram criados meios para possibilitar injetar a forma de 
autentica&ccedil;&atilde;o desejada para assinar o documento.

No arquivo ``.../application/configs/application.ini`` &eacute; poss&iacute;vel escolher as configura&ccedil;&otilde;es 
desejadas para a aplica&ccedil;&atilde;o definindo a propriedade abaixo:

* Assinatura.isServicoHabilitado = true
* Assinatura.Autenticacao.metodo = &quot;padrao&quot;

### Estrutura

O desenvolvimento deste m&amp;oacute;dulo foi focado na reutiliza&amp;ccedil;&amp;atilde;o de c&amp;oacute;digo, portanto foi dado um foco maior no conceito de tipifica&amp;ccedil;&amp;atilde;o com o foco em extensibilidade de funcionalidades.

Para foram utilizados alguns Design Patterns como ServicesLayer, Singleton, Inversion of Control e principalmente interfaces com o intuito de assumir contratos entre as classes.

### Depend&ecirc;ncias

O m&oacute;dulo apresenta depend&ecirc;ncias de acordo com a forma de autentica&ccedil;&atilde;o escolhida.
Caso a forma de autentica&ccedil;&atilde;o escolhida seja a ``padr&atilde;o`` as depend&ecirc;ncias s&atilde;o:

     * M&oacute;dulo: Autenticacao 
     * Classe: Autenticacao_Model_Usuario

---

     * M&oacute;dulo: Assinatura 
     * Classe: Assinatura_Model_DbTable_TbAssinatura

---

     * M&oacute;dulo: Assinatura 
     * Classe: Assinatura_Model_DbTable_TbAtoAdministrativo
          
---

     * M&oacute;dulo: Assinatura 
     * Classe: Assinatura_Model_DbTable_TbDocumentoAssinatura
          
---

     * M&oacute;dulo: Projeto
     * Classe: Projeto_Model_DbTable_Projetos
          
