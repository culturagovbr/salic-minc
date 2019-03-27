<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class FluxosControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/fluxos'
        )->seeJson([
            'teste Fluxos  INDEX'
        ]);
    }
}
