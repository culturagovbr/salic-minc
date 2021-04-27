<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class DetalhamentoItensRestControllerTest extends TestCase
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
            '/avaliacao-resultados/detalhamento-itens/{idpronac}'
        )->seeJson([
            'teste detalhamento GET'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'POST',
            '/avaliacao-resultados/detalhamento-itens'
        )->seeJson([
            'teste detalhamento Post'
        ]);
    }
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/detalhamento-itens'
        )->seeJson([
            'teste detalhamento INDEX'
        ]);
    }
}
