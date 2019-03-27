<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class TecnicosControllerTest extends TestCase
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
            '/avaliacao-resultados/tecnicos/{idpronac}'
        )->seeJson([
            'teste Tecnicos GET'
        ]);
    }

    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/tecnicos'
        )->seeJson([
            'teste Tecnicos INDEX'
        ]);
    }
}
