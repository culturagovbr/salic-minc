<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AssinaturasControllerTest extends TestCase
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
            '/avaliacao-resultados/assinaturas'
        )->seeJson([
            'teste'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'POST',
            '/avaliacao-resultados/assinaturas'
        )->seeJson([
            'testePost'
        ]);
    }
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/assinaturas'
        )->seeJson([
            'teste'
        ]);
    }
}
