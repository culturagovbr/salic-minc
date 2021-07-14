describe('Testes da Análise de Resultados', function () {
  before(function () { // Inicia todos os testes na Tela de Análise de Resultados - Perfil Técnico
    cy.mudarPerfil(124, 203)
    cy.visit('http://localhost/avaliacao-resultados/#/')
    cy.wait(3000)
  });

  it('Renderização da tabela de análise de resultados - Perfil Técnico', function () {
    cy.get('div.v-tabs');
  });

  it('[EM ANÁLISE] Verifica se há pelo menos um item na tabela', function () {

    cy.get('[analisar="true"] > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(1)').contains('1');
  });

  it('[EM ANÁLISE] Click para abrir/fechar modal de Diligências do projeto', function () {
    cy.get('div.v-tabs__div a').contains('Em Analise').click();
    cy.get('.v-icon.material-icons.theme--light.blue--text').contains('assignment_late').click();
    cy.wait(1000);
    cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-toolbar > .v-toolbar__content > .v-btn > .v-btn__content > .v-icon').click();

  });

  it('[EM ANÁLISE] Click para abrir/fechar modal Histórico de Encaminhamentos ', function () {
    cy.get('.v-icon.material-icons.theme--light').contains('history').click();
    cy.wait(1000);
    cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-card__actions > .v-btn > .v-btn__content').click();
  });

  it('[EM ANÁLISE] Click para abrir/fechar modal de Analisar Projeto', function () {
    cy.get(':nth-child(7) > .v-btn--router > .v-btn__content > .v-tooltip > span > .v-icon').click();
    cy.wait(1000);
    cy.get('.v-icon.material-icons.theme--light').contains('arrow_back').click();
  });

  it('[EM ANÁLISE] Click para abrir/fechar modal de Visualizar Objeto', function () {
    cy.get('div.v-tabs__div a').contains('Em Analise').click();
    cy.get(':nth-child(5) > .v-dialog__activator > .v-tooltip > span > .v-btn > .v-btn__content > .v-icon').click();
    cy.wait(3000);
    cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-toolbar > .v-toolbar__content > .v-btn > .v-btn__content > .v-icon').click();
  });

  it('[ASSINAR] Verifica se há pelo menos um item na tabela', function () {
    cy.get('div.v-tabs__div a').contains('Assinar').click();
    cy.get(':nth-child(3) > .v-card > .v-card__text > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(1)').contains('1');
  });

  it('[ASSINAR] Click para abrir/fechar modal de Diligências do projeto', function () {
    cy.get('div.v-tabs__div a').contains('Assinar').click();
    cy.get('[id-pronac="136867"] > .v-dialog__activator').click();
    cy.wait(1000);
    cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-toolbar > .v-toolbar__content > .v-btn > .v-btn__content > .v-icon').click();

  });

  it('[ASSINAR] Click para abrir/fechar modal Histórico de Encaminhamentos ', function () {
    cy.get('[obj="[object Object]"][atual="15"][style="display: inline-block;"] > .v-dialog__activator > .v-tooltip > span > .v-btn > .v-btn__content > .v-icon').click();
    cy.wait(1000);
    cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-card__actions > .v-btn > .v-btn__content').click();
  });

  it('[ASSINAR] Botão de ação para Assinar', function () {
    cy.get('.v-btn--small > .v-btn__content > .v-tooltip > span > .v-icon');
  });

  it('[ASSINAR] Click para abrir/fechar modal de Devolver Projeto', function () {
    cy.get('.v-icon.material-icons.theme--light.error--text').contains('undo').click();
    cy.wait(1000);
    cy.get('.v-card__actions > .error--text').click();
  });

  it('[ASSINAR] Click para abrir/fechar modal de Visualizar Projeto', function () {
    cy.get('div.v-tabs__div a').contains('Assinar').click();
    cy.get(':nth-child(4) > .v-card > .v-card__text > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(6) > .v-btn--router > .v-btn__content > .v-tooltip > span > .v-icon').click();
    cy.wait(3000);
    cy.get('.hidden-xs-only > .v-btn__content > .v-icon').click();
    cy.wait(1000);
  });

  it('[ASSINAR] Click para abrir/fechar modal de Visualizar Objeto', function () {
    cy.get('div.v-tabs__div a').contains('Assinar').click();
    cy.get(':nth-child(6) > .v-dialog__activator > .v-tooltip > span > .v-btn > .v-btn__content > .v-icon').click();
    cy.wait(3000);
    cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-toolbar > .v-toolbar__content > .v-btn').click();
    cy.wait(1000);
  });

  it('[HISTÓRICO] Verifica se há pelo menos um item na tabela', function () {
    cy.get('div.v-tabs__div a').contains('Historico').click();
    cy.get(':nth-child(4) > .v-card > .v-card__text > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(1)').contains('1');
  });

  it('[HISTÓRICO] Click para abrir/fechar modal de Histórico de Encaminhamentos', function () {
    cy.get('div.v-tabs__div a').contains('Historico').click();
    cy.get(':nth-child(5) > .v-card > .v-card__text > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(6) > .v-dialog__container > .v-dialog__activator > .v-tooltip > span > .v-btn > .v-btn__content > .v-icon').click();
    cy.wait(1000);
    cy.get('.v-dialog__content--active > .v-dialog > .v-card > .v-card__actions > .v-btn > .v-btn__content').click();
  });

  it('[HISTÓRICO] Click para abrir/fechar modal de Visualizar Projeto', function () {
    cy.get('div.v-tabs__div a').contains('Historico').click();
    cy.get(':nth-child(5) > .v-card > .v-card__text > :nth-child(1) > :nth-child(2) > .v-table__overflow > .v-datatable > tbody > tr > :nth-child(6) > .v-btn--router > .v-btn__content > .v-tooltip > span > .v-icon').click();
    cy.wait(3000);
    cy.get('.hidden-xs-only > .v-btn__content > .v-icon').click();
    cy.wait(1000);
  });


});