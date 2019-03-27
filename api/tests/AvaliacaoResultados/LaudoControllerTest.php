<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LaudoControllerTest extends TestCase
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
            '/avaliacao-resultados/laudo/{idpronac}'
        )->seeJson([
            'teste Laudo Get'
        ]);
    }
    public function testPost()
    {
        $this->json(
            'POST',
            '/avaliacao-resultados/laudo'
        )->seeJson([
            'teste Laudo Post'
        ]);
    }
    public function testIndex()
    {
        $this->json(
            'GET',
            '/avaliacao-resultados/laudo'
        )->seeJson([
            'teste Laudo INDEX'
        ]);
    }
}
