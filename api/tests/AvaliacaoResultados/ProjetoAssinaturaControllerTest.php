<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ProjetoAssinaturaControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testGet()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/projeto-assinatura/{idpronac}'
        )->seeJson([
            'teste Projeto Assinatura GET'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'POST',
            '/avaliacao-resultados/projeto-assinatura'
        )->seeJson([
            'teste Projeto Assinatura Post'
        ]);
    }
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/projeto-assinatura'
        )->seeJson([
            'teste Projeto Assinatura INDEX'
        ]);
    }
}
