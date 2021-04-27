// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This is will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

Cypress.Commands.add("Login", () => {
  cy.request('POST', 
          'http://localhost/autenticacao/index/login', 
          [{ from: '', Login: '239.691.561-49', Senha: '123456' }])


})

Cypress.Commands.add("mudarPerfil", (codGrupo, codOrgao) => {
  cy.request(`http://localhost/autenticacao/perfil/alterarperfil?codGrupo=${codGrupo}&codOrgao=${codOrgao}`);
})


Cypress.Commands.add('projetosInicio', () => {
  // Mock Server
  cy.request('http://localhost:4000/avaliacao-resultados/fluxo-projeto?estadoid=5&idAgente=236').then((response) => {
    var mock = response.body
  })

  cy.wait('@projetosInicio').then((response) => {
    response.body = {} // mock 
  })

});

Cypress.Commands.add('mockDadosSALIC', () => {
  cy.server()
  cy.route({
    method: 'GET',      // Route all GET requests
    url: '/navegacao/dados-rest/index',
    response: cy.request('http://localhost:4000/navegacao/dados-rest/index')
  }).as('mockDadosSALIC')

});

Cypress.Commands.add('mockProjetosInicio', () => {

  cy.request('http://localhost:4000/avaliacao-resultados/projeto-inicio').then((response) => {
    cy.server()
    cy.route({
      method: 'GET',      // Route all GET requests
      url: '/avaliacao-resultados/fluxo-projeto?estadoid=5&idAgente=236',
      response: response
    }).as('mockProjetosInicio')

  })
  cy.wait(5000)
});


// var data = {};
// function saveResponse(response) {
//   data = response;

//   return true;
// }

