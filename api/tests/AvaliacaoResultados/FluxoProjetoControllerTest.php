<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class FluxoProjetoControllerTest extends TestCase
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
            '/avaliacao-resultados/fluxo-projeto/{idpronac}'
        )->seeJson([
            'teste Fluxo Projeto Get'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'POST',
            '/avaliacao-resultados/fluxo-projeto'
        )->seeJson([
            'teste Fluxo Projeto Post'
        ]);
    }
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/fluxo-projeto'
        )->seeJson([
            'teste Fluxo Projeto INDEX'
        ]);
    }
}
