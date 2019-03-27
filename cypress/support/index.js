// Import commands.js using ES2015 syntax:
import './commands'

before(() => {
    function visit(url) {
        cy.visit(url, {
            onBeforeLoad: (win) => {
            win.onerror = () => {}
            }
        });
    }

    visit('http://localhost/autenticacao/index/index')

    var login = cy.get('#Login'); // Login
    login.type('239.691.561-49')
    cy.get('#Senha').type('123456'); // Senha
    cy.get('#btConfirmar').click();
    
    cy.wait(2000);

});
// Alternatively you can use CommonJS syntax:
// require('./commands')
