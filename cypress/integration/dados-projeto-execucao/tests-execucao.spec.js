describe('Testes dos Sub-menu Execucao', function () {
    it('Verifica se há pelo menos um item no sub-menu - "Marcas Anexadas" ', function () {
        cy.get('#pronac').type('171313{enter}');
        cy.get(':nth-child(6) > .collapsible > .bold > .collapsible-header > span').click();

        //Marcas Anexadas
        cy.get(':nth-child(6) > .collapsible > .bold > .collapsible-body > ul > :nth-child(3) > .waves-effect').click();
        cy.get(':nth-child(6) > .collapsible > .bold > .collapsible-body > ul > :nth-child(3) > .waves-effect').should('not.be.empty').contains('Marcas Anexadas');

        cy.wait(3500);

        //Pedido de Prorrogação
        cy.get(':nth-child(6) > .collapsible > .bold > .collapsible-body > ul > :nth-child(3) > .waves-effect').click();
        cy.get(':nth-child(6) > .collapsible > .bold > .collapsible-body > ul > :nth-child(4) > .waves-effect').should('not.be.empty').contains('Pedido de Prorrogação');

        cy.wait(3500);

        //Dados das Readequações
        cy.get(':nth-child(6) > .collapsible > .bold > .collapsible-body > ul > :nth-child(3) > .waves-effect').click(); //
        cy.get(':nth-child(6) > .collapsible > .bold > .collapsible-body > ul > :nth-child(2) > .waves-effect').should('not.be.empty').contains('Dados das readequações');
    });
});