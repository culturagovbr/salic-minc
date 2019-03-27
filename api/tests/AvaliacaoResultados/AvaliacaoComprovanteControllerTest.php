<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AvaliacaoComprovanteControllerTest extends TestCase
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
            '/avaliacao-resultados/avaliacao-comprovante'
        )->seeJson([
            'teste Avaliacao Comprovante'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'post',
            '/avaliacao-resultados/avaliacao-comprovante'
        )->seeJson([
            'teste Post Avaliaçao Comprovante'
        ]);
    }
};

