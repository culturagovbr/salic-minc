<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class PlanilhaAprovadaControllerTest extends TestCase
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
            '/avaliacao-resultados/planilha-aprovada/{idpronac}'
        )->seeJson([
            'teste Planilha Aprovada GET'
        ]);
    }
}