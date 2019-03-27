<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class DiligenciaControllerTest extends TestCase
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
            '/avaliacao-resultados/diligencia/{idpronac}'
        )->seeJson([
            'teste Diligencia GET'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'POST',
            '/avaliacao-resultados/diligencia'
        )->seeJson([
            'teste Diligencia POST'
        ]);
    }
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/diligencia'
        )->seeJson([
            'teste Diligencia INDEX'
        ]);
    }
}
