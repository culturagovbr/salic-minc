describe('Testes da Avaliação de Resultados: Laudo Final', () => {
    
    before(function () { // Inicia todos os testes na Tela de Análise de Resultados - Perfil Técnico
      cy.mudarPerfil(126, 203)
      cy.visit('http://localhost/avaliacao-resultados/#/laudo')
      cy.wait(3000)
    });

    it('Renderização do Painel do Laudo Final - Perfil Coordenador Geral', () => {
      cy.get('div.v-card .theme--light');
    });
    
    it('[EM ANÁLISE] Verifica se há pelo menos um item na tabela', () => {
      cy.get(':nth-child(1) > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(1)').contains('1');
    });

    it('[EM ANÁLISE] Click para abrir/fechar visualizar Manifestação', () => {
      cy.get(':nth-child(1) > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(4) > .v-dialog__container > .v-dialog__activator > .v-btn').click();
      cy.wait(1000);
      cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-toolbar > .v-toolbar__content > .v-btn > .v-btn__content > .v-icon').click();
    });

    it('[EM ANÁLISE] Click para abrir/cancelar modal de Devolver Projeto', () => {
      cy.get(':nth-child(1) > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(5) > .v-dialog__container > .v-dialog__activator > .v-tooltip > span > .v-btn').click();
      cy.wait(1000);
      cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-card__actions > .error--text').click();
    });

    it('[EM ANÁLISE] Click para Emitir Laudo', () => {
      cy.get('#emitirLaudo').click();
      cy.wait(1000);
      cy.get('.v-card > .v-toolbar > .v-toolbar__content > .v-btn--icon').click();
    });

    it('[ASSINAR] Verifica se há pelo menos um item na tabela', () => {
      cy.get('#assinar > .v-tabs__item').click();
      cy.wait(1000)
      cy.get(':nth-child(2) > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(1)').contains('1');
    });

    it('[ASSINAR] Click para abrir/fechar visualizar Manifestação', () => {
      cy.get(':nth-child(4) > .v-dialog__container > .v-dialog__activator > .v-tooltip > span > .v-btn > .v-btn__content > .v-icon').click();
      cy.wait(1000);
      cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-toolbar > .v-toolbar__content > .v-btn > .v-btn__content > .v-icon').click();
    });

    it('[ASSINAR] Verifica se há pelo menos um item na tabela', () => {
      cy.get('#assinar > .v-tabs__item').click();
      cy.get(':nth-child(2) > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(1)').contains('1');
      cy.wait(1000);
    });

    it('[FINALIZADOS] Verifica se há pelo menos um item na tabela - Aba "Finalizados" ', () => {
      cy.get('#finalizados > .v-tabs__item').click();
      cy.get(':nth-child(4) > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr').should('not.be.empty');

      cy.wait(1000);
    });

    it('[FINALIZADOS] Executar o click na ação da aba Finalizados', () => {
      cy.get('#visualizarLaudo').click();
    
      cy.wait(2000);

      cy.get('.v-card > .v-toolbar > .v-toolbar__content > .v-btn').click();
    });
  });